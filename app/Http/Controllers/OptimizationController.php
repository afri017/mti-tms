<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PurchaseOrder;
use App\Models\Source;
use App\Models\Tonnage;
use App\Models\DeliveryOrder;
use App\Models\DeliveryOrderItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class OptimizationController extends Controller
{
    public function index()
    {
        return view('optimization');
    }

    public function loadPOData(Request $request)
    {
        $poNumbers = array_map('trim', explode(',', $request->po_numbers));

        // Ambil data PO lengkap beserta item & customer
        $pos = \App\Models\PurchaseOrder::with(['poItems.material', 'customer'])
            ->whereIn('nopo', $poNumbers)
            ->get();

        // Ambil total qty DO per (nopo + material) dengan join ke header
        $doSummary = DeliveryOrderItem::select(
                'do.nopo',
                'delivery_order_items.material_code',
                DB::raw('SUM(delivery_order_items.qty_plan) as total_qty_do')
            )
            ->join('delivery_orders as do', 'do.nodo', '=', 'delivery_order_items.nodo')
            ->whereIn('do.nopo', $poNumbers)
            ->groupBy('do.nopo', 'delivery_order_items.material_code')
            ->get()
            ->keyBy(fn($r) => $r->nopo . '_' . $r->material_code);

        // Tambahkan remaining_qty ke setiap item PO
        foreach ($pos as $po) {
            foreach ($po->poItems as $item) {
                $key = $po->nopo . '_' . $item->material_code;
                $totalDO = ($doSummary[$key]->total_qty_do ?? 0) / 1000; // KG -> Ton
                $item->remaining_qty = max($item->qty - $totalDO, 0);
            }
        }
        \Log::info($doSummary);
        return response()->json([
            'status' => 'success',
            'data' => $pos
        ]);
    }

    public function getOptions()
    {
        $destinations = Source::where('type', 'Destination')
            ->select('id', 'location_name', 'capacity')
            ->get();
        $origins = Source::where('type', 'Source')
            ->select('id', 'location_name', 'capacity')
            ->get();

        $truckTypes = Tonnage::select('id', 'type_truck', 'desc')->get();

        return response()->json([
            'status' => 'success',
            'destinations' => $destinations,
            'truck_types' => $truckTypes,
            'origins' => $origins,
        ]);
    }

    public function dropdownData(): JsonResponse
    {
        try {
            // Ambil hanya data dengan type = 'Destination'
            $destinations = Source::where('type', 'Destination')
                ->select('id', 'location_name', 'capacity')
                ->get();

            $origins = Source::where('type', 'Source')
            ->select('id', 'location_name', 'capacity')
            ->get();

            // Ambil semua truck type dari tabel tonnage
            $truckTypes = Tonnage::select('id', 'type_truck', 'desc')->get();

            return response()->json([
                'status' => 'success',
                'destinations' => $destinations,
                'truck_types' => $truckTypes,
                'origins' => $origins
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }
    public function getTruckRouteData()
    {
        try {
            // Ambil data truck dan join dengan driver
            $truckList = \DB::table('trucks as t')
                ->leftJoin('drivers as d', 't.iddriver', '=', 'd.iddriver')
                ->select(
                    't.idtruck',
                    't.nopol',
                    't.merk',
                    't.type_truck',
                    'd.iddriver as driver_id',
                    'd.name as driver_name'
                )
                ->orderBy('t.nopol')
                ->get();

            // Ambil data route
            $routeList = \DB::table('routes')
                ->select('route', 'source', 'destination','route_name')
                ->orderBy('source')
                ->get();

            return response()->json([
                'status' => 'success',
                'truck_list' => $truckList,
                'route_list' => $routeList,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }
    public function saveSchedule(Request $request)
    {
        $trips = $request->input('trips', []);
        $poNumber = $request->input('po_number');

        if (empty($trips)) {
            return response()->json(['status' => 'error', 'message' => 'Tidak ada trip yang dipilih.'], 400);
        }

        // 🔹 Ambil semua PO item terkait PO ini
        $poItems = \App\Models\POItem::where('nopo', $poNumber)
            ->get()
            ->keyBy('material_code');

        // 🚫 Validasi qty DO tidak boleh melebihi remaining PO
        foreach ($trips as $trip) {
            $material = $trip['material'] ?? null;
            $qtyTon = floatval($trip['qty'] ?? 0); // qty di frontend dalam TON
            if (!$material) continue;

            $poItem = $poItems[$material] ?? null;
            if (!$poItem) {
                return response()->json([
                    'status' => 'error',
                    'message' => "Material $material tidak ditemukan di PO $poNumber."
                ], 422);
            }

            // Ambil total DO yang sudah ada untuk material ini (dalam KG)
            $existingDOKg = \DB::table('delivery_order_items as doi')
                ->join('delivery_orders as do', 'do.nodo', '=', 'doi.nodo')
                ->where('do.nopo', $poNumber)
                ->where('doi.material_code', $material)
                ->sum('doi.qty_plan');

            $remainingQtyTon = $poItem->qty - ($existingDOKg / 1000); // konversi KG -> TON

            if ($qtyTon > $remainingQtyTon) {
                return response()->json([
                    'status' => 'error',
                    'message' => "Qty trip ($qtyTon) melebihi sisa PO ($remainingQtyTon) untuk PO $poNumber / $material."
                ], 422);
            }
        }

        // 🔍 Ringkas total qty per destination per tanggal
        $summary = [];
        foreach ($trips as $trip) {
            $destination = $trip['destination'] ?? null;
            $tanggal = $trip['tanggal'] ?? date('Y-m-d');
            $qty = floatval($trip['qty'] ?? 0);
            if (!$destination) continue;

            $key = $destination . '_' . $tanggal;
            if (!isset($summary[$key])) {
                $summary[$key] = [
                    'destination' => $destination,
                    'tanggal' => $tanggal,
                    'total_qty' => 0,
                ];
            }
            $summary[$key]['total_qty'] += $qty;
        }

        // 🚫 Validasi kapasitas berdasarkan kapasitas destinasi per hari
        foreach ($summary as $row) {
            $destId = $row['destination'];
            $tanggal = $row['tanggal'];
            $qtyToInsert = $row['total_qty'];

            $existingQty = \DB::table('delivery_order_items as doi')
                ->join('delivery_orders as do', 'do.nodo', '=', 'doi.nodo')
                ->where('do.destination', $destId)
                ->whereDate('do.delivery_date', $tanggal)
                ->sum('doi.qty_plan');

            $capacityDest = \DB::table('sources')->where('id', $destId)->value('capacity') ?? 0;

            $existingQtyTon = $existingQty / 1000;
            $total = $existingQtyTon + $qtyToInsert;

            if ($total > $capacityDest) {
                $destinationName = \DB::table('sources')->where('id', $destId)->value('location_name') ?? $destId;
                return response()->json([
                    'status' => 'error',
                    'message' => "Kapasitas tujuan {$destinationName} pada tanggal {$tanggal} melebihi batas. (Total {$total} / Kapasitas {$capacityDest})"
                ], 422);
            }
        }

        // ✅ Insert ke Delivery Order dan Delivery Order Item
        foreach ($trips as $trip) {
            // 🔢 Generate nomor DO 10 digit (2 huruf + 8 angka)
            $prefix = 'DO';
            $lastDo = \App\Models\DeliveryOrder::where('nodo', 'LIKE', $prefix . '%')
                ->orderBy('nodo', 'desc')
                ->first();

            if ($lastDo && preg_match('/\d+$/', $lastDo->nodo, $matches)) {
                $nextNumber = intval($matches[0]) + 1;
            } else {
                $nextNumber = 1;
            }

            $nodo = sprintf('%s%08d', $prefix, $nextNumber);

            // 🚚 Insert ke delivery_orders
            $do = \App\Models\DeliveryOrder::create([
                'noshipment' => null, // dikosongkan dulu
                'nodo' => $nodo,
                'nopo' => $trip['po_number'] ?? $poNumber,
                'idtruck' => $trip['truck_id'] ?? null, // ✅ Tambahan
                'delivery_date' => $trip['tanggal'] ?? now(),
                'source' => $trip['source'] ?? null,
                'destination' => $trip['destination'] ?? null,
                'created_by' => auth()->id(),
                'last_update' => now(),
            ]);

            // 📦 Insert ke delivery_order_items
            \App\Models\DeliveryOrderItem::create([
                'nodo' => $do->nodo,
                'doitem' => 1,
                'material_code' => $trip['material'] ?? null,
                'qty_plan' => ($trip['qty'] * 1000) ?? 0,
                'uom' => $trip['uom'] ?? 'KG',
                'created_by' => auth()->id(),
                'last_update' => now(),
            ]);
        }

        return response()->json(['status' => 'success', 'message' => '✅ Jadwal berhasil disimpan ke Delivery Order.']);
    }

}
