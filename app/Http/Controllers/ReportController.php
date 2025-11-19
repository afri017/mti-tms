<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PurchaseOrder;
use App\Models\Gate;
use App\Models\POItem;
use App\Models\DeliveryOrder;
use App\Models\DeliveryOrderItem;
use App\Models\Customer;
use App\Models\Material;
use App\Models\GateUsage;
use Illuminate\Support\Facades\DB;
use App\Exports\OutstandingPOExport;
use Yajra\DataTables\Facades\DataTables;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    public function poOutstanding(Request $request)
    {
        $pageTitle = 'Purchase Order Report';

        $year = date('Y');

        // ==== INISIALISASI 12 BULAN ====
        $monthlyPo = array_fill(0, 12, 0);
        $monthlyDelivered = array_fill(0, 12, 0);
        $monthlyOutstanding = array_fill(0, 12, 0);

        // Ambil semua PO beserta item dan customer
        $pos = PurchaseOrder::with('poItems', 'customer')->get();

        // Ambil semua qty yang sudah digunakan per kombinasi nopo + material_code
        $usedQty = \DB::table('delivery_order_items as doi')
            ->join('delivery_orders as do', 'doi.nodo', '=', 'do.nodo')
            ->whereYear('do.delivery_date', $year)
            ->select('do.nopo', 'doi.material_code', 'doi.uom', \DB::raw('SUM(doi.qty_plan) as qty_used'))
            ->groupBy('do.nopo', 'doi.material_code', 'doi.uom')
            ->get()
            ->mapWithKeys(function($row){
                // Buat key gabungan nopo_material_code
                $key = $row->nopo . '_' . $row->material_code;

                // Konversi qty ke ton
                $qtyInTon = match(strtolower($row->uom)) {
                    'kg' => $row->qty_used / 1000,
                    'g'  => $row->qty_used / 1_000_000,
                    'ton' => $row->qty_used,
                    default => $row->qty_used, // jika UOM lain, pakai apa adanya
                };

                return [$key => $qtyInTon];
            });

            // Hitung status PO dan still to be delivered per item
            foreach ($pos as $po) {

                $monthIndex = (int)date('m', strtotime($po->podate)) - 1;

                $poTotal = 0;
                $delTotal = 0;

                foreach ($po->poItems as $item) {

                    $key = $po->nopo . '_' . $item->material_code;
                    $delivered = $usedQty[$key] ?? 0;

                    $poTotal += $item->qty;
                    $delTotal += $delivered;
                }

                $outstanding = max(0, $poTotal - $delTotal);

                // ==== SUM KE ARRAY 12 BULAN ====
                $monthlyPo[$monthIndex] += $poTotal;
                $monthlyDelivered[$monthIndex] += $delTotal;
                $monthlyOutstanding[$monthIndex] += $outstanding;
            }
            return view('report.pooutstanding', [
            'pos' => $pos,
            'monthlyPo' => $monthlyPo,
            'monthlyDelivered' => $monthlyDelivered,
            'monthlyOutstanding' => $monthlyOutstanding,
            'pageTitle' => $pageTitle,
        ]);
    }

    public function poOutstandingData(Request $request)
    {
        $start = $request->start_date;
        $end   = $request->end_date;

        // Ambil semua PO dengan relasi item & customer
        $pos = PurchaseOrder::with('poItems', 'customer')
                ->whereBetween('podate', [$start, $end])
                ->get();

        // Ambil total qty yang sudah digunakan (delivery order)
        $usedQty = DB::table('delivery_order_items as doi')
            ->join('delivery_orders as do', 'doi.nodo', '=', 'do.nodo')
            ->select(
                'do.nopo',
                'doi.material_code',
                'doi.uom',
                DB::raw('SUM(doi.qty_plan) as qty_used')
            )
            ->groupBy('do.nopo', 'doi.material_code', 'doi.uom')
            ->get()
            ->mapWithKeys(function($row){
                $key = $row->nopo . '_' . $row->material_code;

                $qtyInTon = match(strtolower($row->uom)) {
                    'kg'  => $row->qty_used / 1000,
                    'g'   => $row->qty_used / 1_000_000,
                    'ton' => $row->qty_used,
                    default => $row->qty_used,
                };

                return [$key => $qtyInTon];
            });

        $result = [];

        // Hitung outstanding & items
        foreach ($pos as $po) {

            $poTotalQty = 0;
            $poTotalStillToDeliver = 0;

            $items = [];

            foreach ($po->poItems as $item) {

                $key = $po->nopo . '_' . $item->material_code;
                $usedInTon = $usedQty[$key] ?? 0;

                $still = max(0, $item->qty - $usedInTon);

                $poTotalQty += $item->qty;
                $poTotalStillToDeliver += $still;

                $items[] = [
                    "material"        => $item->material_code,
                    "qty_order"       => $item->qty,
                    "qty_delivered"   => $usedInTon,
                    "qty_outstanding" => $still,
                    "uom"             => $item->uom
                ];
            }

            // Hanya ambil PO yang memang outstanding
            if ($poTotalStillToDeliver > 0) {
                $result[] = [
                    "po_number"       => $po->nopo,
                    "customer_name"   => optional($po->customer)->customer_name,
                    "po_date"         => $po->podate,
                    "qty_order"       => $poTotalQty,
                    "qty_received"    => $poTotalQty - $poTotalStillToDeliver,
                    "outstanding"     => $poTotalStillToDeliver,
                    "items"           => $items
                ];
            }
        }

        return response()->json([
            'status' => 'ok',
            'data'   => $result
        ]);
    }

    public function exportOutstanding(Request $request)
    {
        $start = $request->start_date ?? date('Y-01-01');
        $end   = $request->end_date ?? date('Y-m-d');

        $fileName = "Outstanding_PO_{$start}_to_{$end}.xlsx";

        return Excel::download(new OutstandingPOExport($start, $end), $fileName);
    }

    public function gates(Request $request)
    {
        // ambil gate operasional (type 35)
        $gateOperational = Gate::where('type', 35)
            ->select('gate', 'timestart', 'timeend')
            ->get()
            ->mapWithKeys(function ($g) {
                $gateId = 'G' . str_pad(
                    intval(filter_var($g->gate, FILTER_SANITIZE_NUMBER_INT)),
                    2,
                    '0',
                    STR_PAD_LEFT
                );
                return [
                    $gateId => [
                        'timestart' => $g->timestart,
                        'timeend'   => $g->timeend,
                    ]
                ];
            });

        $query = GateUsage::with(['gateud.source','shipment.truck']);

        // Filter tanggal (gate_usage)
        if ($request->date) {
            $query->whereDate('delivery_date', $request->date);
        }

        // Filter source
        if ($request->source && $request->source != 'all') {
            $gateCodes = Gate::where('point', $request->source)->pluck('gate');
            $query->whereIn('gate', $gateCodes);
        }

        return DataTables::eloquent($query)
            ->addColumn('gate_name', fn($row) => $row->gateud->gate ?? '-')
            ->addColumn('source_name', fn($row) => $row->gateud->source->location_name ?? '-')
            ->addColumn('timestart', fn($row) => $row->timestart)
            ->addColumn('timeend', fn($row) => $row->timeend)
            ->addColumn('type', fn($row) => $row->gateud->type ? 'Loading' : '-')
            ->addColumn('duration_minutes', function($row) {
                if (!$row->timestart || !$row->timeend) return 0;
                return round((strtotime($row->timeend) - strtotime($row->timestart)) / 60);
            })
            ->addColumn('truck', fn($row) => $row->shipment->truck->nopol ?? '-') // tampilkan truck number
            ->addColumn('noshipment', fn($row) => $row->noshipment)
            ->rawColumns(['noshipment']) // kalau mau HTML, bisa diubah
            ->with([
                'gateOperational' => $gateOperational  // ⬅⬅ TAMBAHKAN INI
            ])
            ->make(true);
    }


    public function gatesPage()
    {
        $sources = \App\Models\Source::where('type', 'source')
                ->orderBy('location_name')
                ->get();

        // Gate operasional berdasarkan type = 35
        $gateOperational = \App\Models\Gate::where('type', 35)
            ->select('gate', 'timestart', 'timeend')
            ->get()
            ->mapWithKeys(function ($g) {

                // normalisasi kode gate (misal G01, G02)
                $gateId = 'G' . str_pad(
                    intval(filter_var($g->gate, FILTER_SANITIZE_NUMBER_INT)),
                    2,
                    '0',
                    STR_PAD_LEFT
                );

                return [
                    $gateId => [
                        'timestart' => $g->timestart,
                        'timeend'   => $g->timeend,
                    ]
                ];
            })
            ->toArray();  // <--- tambahkan ini supaya pasti array


        return view('report.gates', [
            'pageTitle' => 'Report Gates',
            'sources'   => $sources,
            'gateOperational' => $gateOperational,
        ]);
    }
}
