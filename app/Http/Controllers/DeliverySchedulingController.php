<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\DeliveryOrder;
use App\Models\Vendor;
use App\Models\Route;
use App\Models\Truck;
use App\Models\Source;
use App\Models\Shipment;
use App\Models\Gate;
use App\Models\GateUsage;
use Carbon\Carbon;


class DeliverySchedulingController extends Controller
{
    public function index(Request $request)
    {
        $pageTitle = 'Delivery Scheduling';
        $breadchumb = '';

        // Ambil semua vendor & route untuk dropdown filter
        $transporters = Vendor::select('idvendor', 'transporter_name')->get();
        $routes = Route::select('route', 'route_name')->get();

        $deliveryOrders = collect();

        // Kalau belum apply filter, jangan tampilkan data
        if (!$request->has('apply_filter')) {
            return view('delivery_scheduling.index', compact('pageTitle', 'breadchumb', 'transporters', 'routes'))
                ->with('deliveryOrders', collect());
        }

        // 🔍 Query dasar
        $query = DeliveryOrder::with(['items', 'truck.vendor']);

        // 📅 Filter tanggal
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('delivery_date', [$request->start_date, $request->end_date]);
        }

        // 🧾 Filter status (akan diproses manual setelah looping)
        $filterStatus = $request->status;

        // 🚚 Filter transporter (via relasi truck → vendor)
        if ($request->filled('transporter')) {
            $query->whereHas('truck.vendor', function ($q) use ($request) {
                $q->where('idvendor', $request->transporter);
            });
        }

        // 🛣️ Filter route berdasarkan kombinasi source + destination
        if ($request->filled('route')) {
            $selectedRoute = Route::find($request->route);

            if ($selectedRoute) {
                // Cocokkan gabungan source+destination antara route dan DO
                $concatRoute = $selectedRoute->source . $selectedRoute->destination;

                $query->whereRaw("CONCAT(source, destination) = ?", [$concatRoute]);
            }
        }

        // 🔢 Filter PO
        if ($request->filled('po_number')) {
            $query->where('nopo', 'like', '%' . $request->po_number . '%');
        }

        $deliveryOrders = $query->get();

        // Hitung total qty dan status
        $filtered = collect();
        foreach ($deliveryOrders as $do) {
            $totalQtyPlan = 0;
            $totalStillToDeliver = 0;

            foreach ($do->items as $item) {
                $item->still_to_be_delivered = max(0, $item->qty_plan - ($item->qty_act ?? 0));
                $totalQtyPlan += $item->qty_plan;
                $totalStillToDeliver += $item->still_to_be_delivered;
            }

            $do->total_qty_plan = $totalQtyPlan;
            $do->total_still_to_deliver = $totalStillToDeliver;

            $allItemsDelivered = $do->items->every(fn($i) => $i->still_to_be_delivered == 0);
            $anyItemDelivered = $do->items->some(fn($i) => $i->still_to_be_delivered < $i->qty_plan);

            if ($allItemsDelivered) {
                $do->status = 'Complete';
            } elseif ($anyItemDelivered) {
                $do->status = 'Partial Delivery';
            } else {
                $do->status = 'Open';
            }

            // Filter by status (setelah logic status jadi)
            if (!$filterStatus || $do->status == $filterStatus) {
                $filtered->push($do);
            }
        }

        return view('delivery_scheduling.index', [
            'pageTitle' => $pageTitle,
            'breadchumb' => $breadchumb,
            'deliveryOrders' => $filtered,
            'transporters' => $transporters,
            'routes' => $routes,
        ]);
    }

    public function bulkAction(Request $request)
    {
        $selectedDOs = json_decode($request->input('selected_dos', '[]'), true);
        if (empty($selectedDOs)) {
            return back()->with('error', 'Tidak ada DO yang dipilih.');
        }

        $deliveryOrders = DeliveryOrder::with([
            'truck.vendor',
            'truck.tonnage',
            'items',
            'sourceLocation',
            'destinationLocation'
        ])->whereIn('nodo', $selectedDOs)->get();

        if ($deliveryOrders->isEmpty()) {
            return back()->with('error', 'Data DO tidak ditemukan di database.');
        }

        // Validasi DO
        foreach ($deliveryOrders as $do) {
            if (!$do->truck) {
                return back()->with('error', "DO {$do->nodo} belum memiliki truck.");
            }
            if ($do->noshipment) {
                return back()->with('error', "DO {$do->nodo} sudah memiliki shipment.");
            }
        }


        // Pakai DB Transaction
        DB::beginTransaction();
        try {
            // Ambil last shipment sebelum loop DO
            $lastShipment = Shipment::lockForUpdate()->orderBy('noshipment', 'desc')->first();
            if ($lastShipment) {
                \Log::info("Last shipment ditemukan: noshipment = {$lastShipment->noshipment}");
                $lastNumber = preg_match('/^11\d{7}$/', $lastShipment->noshipment)
                    ? intval(substr($lastShipment->noshipment, 2))
                    : 0;
            } else {
                \Log::info("Belum ada shipment sebelumnya, mulai dari 0");
                $lastNumber = 0;
            }
            \Log::info("Nomor terakhir: {$lastNumber}");
            $tempBookedSlots = [];

            foreach ($deliveryOrders as $do) {

                $truck = $do->truck;
                $routeCode = $do->route_data->route ?? null;
                $idVendor = $truck->vendor->idvendor ?? null;
                $typeTruck = $truck->tonnage->id ?? null;
                $typeTruckton = $truck->tonnage->type_truck ?? null;
                $deliveryDate = Carbon::parse($do->delivery_date);

                if (!$idVendor || !$routeCode || !$typeTruck) {
                    throw new \Exception("Gagal menentukan ShipCost untuk DO {$do->nodo}");
                }

                $shipmentCost = \App\Models\ShipmentCost::where('idvendor', $idVendor)
                    ->where('route', $routeCode)
                    ->where('type_truck', $typeTruck)
                    ->where('active', 'Y')
                    ->whereDate('validity_start', '<=', now())
                    ->whereDate('validity_end', '>=', now())
                    ->first();

                if (!$shipmentCost) {
                    throw new \Exception("ShipCost tidak ditemukan untuk DO {$do->nodo}");
                }

                // Generate nomor shipment
                $lastNumber++;
                $noshipment = '11' . str_pad($lastNumber, 7, '0', STR_PAD_LEFT); // 7 digit di belakang '11'

                // Buat Shipment
                $shipment = Shipment::create([
                    'noshipment'     => $noshipment,
                    'route'          => $routeCode,
                    'shipcost'       => $shipmentCost->id,
                    'truck_id'       => $truck->idtruck,
                    'driver'         => $truck->iddriver ?? '-',
                    'transporter'    => $idVendor,
                    'noseal'         => 'AUTO-' . rand(1000,9999),
                    'delivery_date'  => $deliveryDate,
                    'gate'           => null,
                    'timestart'      => null,
                    'timeend'        => null,
                    'status'         => 'Open',
                ]);

                // ✅ Assign gate otomatis
                $assignedGate = $this->assignGateAuto($do->source, $typeTruckton, $shipment, $deliveryDate, $tempBookedSlots);
                if (!$assignedGate) {
                    throw new \Exception("Semua gate penuh untuk DO {$do->nodo} pada {$deliveryDate->toDateString()}");
                }

                $do->update(['noshipment' => $shipment->noshipment]);
            }

            DB::commit();
            return back()->with('success', "Semua DO berhasil dibuat shipment masing-masing.");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }

    public function bulkAction_satu(Request $request)
    {
        $selectedDOs = json_decode($request->input('selected_dos', '[]'), true);

        if (empty($selectedDOs)) {
            return back()->with('error', 'Tidak ada DO yang dipilih.');
        }

        // ✅ Ambil hanya DO yang dipilih + relasi lengkap
        $deliveryOrders = DeliveryOrder::with([
            'truck.vendor',
            'truck.tonnage',
            'items', // pastikan relasi ini ada di model
            'sourceLocation',
            'destinationLocation'
        ])->whereIn('nodo', $selectedDOs)->get();

        if ($deliveryOrders->isEmpty()) {
            return back()->with('error', 'Data DO tidak ditemukan di database.');
        }

        // ✅ Validasi: jika ada DO yang sudah memiliki shipment
        $alreadyShipped = $deliveryOrders->filter(fn($do) => !empty($do->noshipment));

        if ($alreadyShipped->isNotEmpty()) {
            $nodos = $alreadyShipped->pluck('nodo')->join(', ');
            return back()->with('error', "DO berikut sudah memiliki shipment dan tidak bisa diproses: {$nodos}");
        }

        // ✅ Validasi semua DO menggunakan truk yang sama
        $firstTruck = $deliveryOrders->first()->truck;
        foreach ($deliveryOrders as $do) {
            if (!$do->truck) {
                return back()->with('error', "DO {$do->nodo} belum memiliki truck.");
            }
        }

        // foreach ($deliveryOrders as $do) {
        //     if ($do->idtruck !== $firstTruck->idtruck) {
        //         return back()->with('error', "Semua DO harus menggunakan truk yang sama.");
        //     }
        // }

        $ratios = [];

        foreach ($deliveryOrders as $do) {
            $tonnageValue = $do->truck?->tonnage?->type_truck ?? null; // contoh: "30 Ton"

            // 🔹 Total qty dari items
            $qtyPlan = $do->items->sum('qty_plan') ?? 0;

            $ratio = $tonnageValue ? $qtyPlan / $tonnageValue : null;

            $ratios[] = [
                'nodo' => $do->nodo,
                'truck_id' => $do->truck?->idtruck,
                'tonnage' => $tonnageValue,
                'qty_plan' => $qtyPlan,
                'ratio' => $ratio,
            ];
        }

        // 🔍 Debug hasil rasio (bisa dilihat di storage/logs/laravel.log)
        logger()->info('Rasio DOs', $ratios);

        // ✅ Cek apakah semua ratio sama (dengan toleransi 5%)
        $firstRatio = $ratios[0]['ratio'] ?? null;
        foreach ($ratios as $r) {
            if ($firstRatio && abs($r['ratio'] - $firstRatio) / $firstRatio > 0.05) {
                return back()->with('error', "Tonase Truck berbeda pada Muatan DO {$r['nodo']} (Qty: {$r['qty_plan_total']}, Tonase: {$r['tonnage']}).");
            }
        }

        // ✅ Ambil data dasar dari DO pertama
        $firstDO = $deliveryOrders->first();
        $truck = $firstDO->truck;
        $routeCode = $firstDO->route_data->route ?? null;
        $idVendor = $truck?->vendor?->idvendor ?? null;
        $typeTruck = $truck?->type_truck ?? null;

        // 🔍 Pastikan semua parameter ada
        if (!$idVendor || !$routeCode || !$typeTruck) {
            return back()->with('error', "Gagal menentukan ShipCost: idvendor, route, atau type_truck tidak lengkap.");
        }

        // 🔎 Cari ship cost yang aktif dan sesuai parameter
        $shipmentCost = \App\Models\ShipmentCost::where('idvendor', $idVendor)
            ->where('route', $routeCode)
            ->where('type_truck', $typeTruck)
            ->where('active', 'Y')
            ->whereDate('validity_start', '<=', now())
            ->whereDate('validity_end', '>=', now())
            ->first();

        if (!$shipmentCost) {
            return back()->with('error', "Tidak ditemukan ShipCost aktif untuk kombinasi vendor [$idVendor], route [$routeCode], dan type_truck [$typeTruck].");
        }

        // ✅ Simpan harga ke shipment (gabungkan freight + driver)
        $totalShipCost = ($shipmentCost->price_freight ?? 0) + ($shipmentCost->price_driver ?? 0);


        // ✅ Generate nomor shipment otomatis (format: 1100000001)
        $lastShipment = Shipment::orderBy('noshipment', 'desc')->first();

        if ($lastShipment && preg_match('/^11\d{7}$/', $lastShipment->noshipment)) {
            $lastNumber = intval(substr($lastShipment->noshipment, 2)); // ambil 7 digit terakhir
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1; // mulai dari 1 kalau belum ada data
        }

        $noshipment = '11' . str_pad($nextNumber, 7, '0', STR_PAD_LEFT);

        // ✅ Buat Shipment
        $shipment = Shipment::create([
            'noshipment'     => $noshipment,
            'route'          => $deliveryOrders->first()->route_data->route ?? null,
            'shipcost'       => $shipmentCost->id,
            'truck_id'       => $firstTruck->idtruck,
            'driver'         => $firstTruck->iddriver ?? '-',
            'transporter'    => $firstTruck->vendor->idvendor ?? null,
            'noseal'         => 'AUTO-' . rand(1000,9999),
            'delivery_date'  => $deliveryOrders->first()->delivery_date ?? now(),
            'gate'           => null,
            'timestart'      => now(),
            'timeend'        => null,
            'status'         => 'Open',
        ]);

        // ✅ Update semua DO yang dipilih → isi noshipment
        foreach ($deliveryOrders as $do) {
            $do->update(['noshipment' => $shipment->noshipment]);
        }

        // step 0: ambil data awal
        $deliveryOrder = $deliveryOrders->first();
        $truck = $deliveryOrder->truck;
        $sourceCode = $deliveryOrder->source;
        $deliveryDate = Carbon::parse($deliveryOrder->delivery_date);

        $gate = Gate::where('point', $sourceCode)
            ->where('type', $truck->tonnage->type_truck ?? null)
            ->orderBy('id')
            ->first();

        if (!$gate) {
            return back()->with('error', "Tidak ada gate yang cocok untuk source {$sourceCode} dan type {$truck->tonnage->type_truck}");
        }

        // Assign gate dan cari slot
        $slot = $this->assignGate($gate, $shipment, $deliveryDate);

        if (!$slot) {
            return back()->with('error', "Tidak ada slot tersedia di gate {$gate->id} pada tanggal {$deliveryDate->toDateString()}");
        }

        return back()->with('success', "Shipment {$shipment->noshipment} berhasil dibuat untuk DO terpilih.");
    }

    // Menampilkan form edit DO
    public function edit($id)
    {
        $deliveryOrder = DeliveryOrder::with('truck.vendor', 'items.material')->findOrFail($id);
        $routes = Route::all();
        $transporters = Vendor::all();
        // Ambil data source
        $sources = Source::where('type', 'Source')->get();

        // Ambil data destination
        $destinations = Source::where('type', 'Destination')->get();

        return view('delivery_scheduling.edit', compact('deliveryOrder', 'routes', 'transporters', 'sources', 'destinations'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'delivery_date' => 'required|date',
            'truck_id' => 'required|exists:trucks,idtruck',
            'source' => 'required|exists:sources,id',
            'destination' => 'required|exists:sources,id',
            'items' => 'required|array',
            'items.*.qty_plan' => 'required|numeric|min:0',
        ]);

        $deliveryOrder = DeliveryOrder::findOrFail($id);

        // Update DO
        $deliveryOrder->delivery_date = $request->delivery_date;
        $deliveryOrder->idtruck = $request->truck_id;
        $deliveryOrder->source = $request->source;
        $deliveryOrder->destination = $request->destination;

        $deliveryOrder->save();

        // Update DO Items
        foreach ($request->items as $itemId => $itemData) {
            $doItem = $deliveryOrder->items()->where('id', $itemId)->first();
            if ($doItem) {
                $doItem->qty_plan = $itemData['qty_plan'];
                $doItem->save();
            }
        }

        return redirect()->route('delivery_scheduling.index')
                         ->with('success', 'DO dan item berhasil diupdate.');
    }

    public function destroy($id)
    {
        $deliveryOrder = DeliveryOrder::findOrFail($id);

        // Jika ada relasi yang ingin dihapus juga, bisa handle di sini
        // Contoh: $deliveryOrder->items()->delete();

        $deliveryOrder->delete();

        return redirect()->route('delivery_scheduling.index')
                         ->with('success', 'DO berhasil dihapus.');
    }

    protected function assignGateAuto($source, $typeTruck, $shipment, $deliveryDate, &$tempBookedSlots = [])
    {
        // Ambil semua gate sesuai source & type
        $gates = Gate::where('point', $source)
            ->where('type', $typeTruck)
            ->orderBy('id')
            ->get();

        if ($gates->isEmpty()) {
            return false;
        }

        foreach ($gates as $gate) {
            $duration = $gate->duration_minutes ?? 30;
            $availableStart = Carbon::parse($deliveryDate->toDateString() . ' ' . ($gate->timestart ?? '07:00'));
            $availableEnd   = Carbon::parse($deliveryDate->toDateString() . ' ' . ($gate->timeend ?? '17:00'));

            $slots = [];
            $current = $availableStart->copy();
            while ($current->lt($availableEnd)) {
                $end = $current->copy()->addMinutes($duration);
                if ($end->lte($availableEnd)) {
                    $slots[] = ['start' => $current->copy(), 'end' => $end->copy()];
                }
                $current->addMinutes($duration);
            }

            // Ambil semua booked slots dari DB
            $booked = GateUsage::where('gate', $gate->gate)
                ->whereDate('delivery_date', $deliveryDate->toDateString())
                ->get(['timestart', 'timeend'])
                ->map(fn($u) => [
                    'start' => Carbon::parse($u->timestart),
                    'end'   => Carbon::parse($u->timeend),
                ])->toArray();

                // Ambil booked slots sementara dari tempBookedSlots per gate & tanggal
                $temp = $tempBookedSlots[$gate->gate][$deliveryDate->toDateString()] ?? [];
                $allBooked = array_merge($booked, $temp);

                \Log::info("Cek gate {$gate->gate} untuk tanggal {$deliveryDate->toDateString()}");
                \Log::info("Booked slot dari DB + temp: " . json_encode($allBooked));

                // Cari slot tersedia
                $available = null;
                foreach ($slots as $slot) {
                    if ($this->isSlotAvailable($slot['start'], $slot['end'], $allBooked)) {
                        $available = $slot;
                        break;
                    }
                }

                if ($available) {

                    try {
                        // Simpan GateUsage
                        GateUsage::create([
                            'gate'          => $gate->gate,
                            'noshipment'    => $shipment->noshipment,
                            'delivery_date' => $deliveryDate,
                            'timestart'     => $available['start'],
                            'timeend'       => $available['end'],
                        ]);
                    } catch (\Illuminate\Database\QueryException $e) {
                        // Jika bentrok karena constraint unik, lanjutkan ke gate atau slot berikutnya
                        if (str_contains($e->getMessage(), 'unique_gate_schedule')) {
                            \Log::warning("Slot bentrok di gate {$gate->gate}, untuk {$available['start']} - {$available['end']} skip ke slot berikutnya");
                            continue;
                        }
                        throw $e; // lempar error lain yang bukan duplikasi
                    }

                $shipment->update(
                    [
                        'gate' => $gate->gate,
                        'timestart'  => $available['start'],
                        'timeend'    => $available['end'],
                    ]);

                // Simpan slot ke tempBookedSlots agar tidak bentrok dengan shipment berikutnya
                $tempBookedSlots[$gate->gate][$deliveryDate->toDateString()][] = [
                    'start' => $available['start'],
                    'end'   => $available['end'],
                ];

                \Log::info("Slot berhasil diassign untuk shipment {$shipment->noshipment} di gate {$gate->gate}: "
                    . $available['start']->format('H:i') . " - " . $available['end']->format('H:i'));

                return $gate; // Return gate yang berhasil
            }
        }

        return false; // Semua gate penuh
    }

    /**
     * Cek apakah slot tersedia
     */
    protected function isSlotAvailable($slotStart, $slotEnd, $bookedSlots)
    {
        foreach ($bookedSlots as $booked) {
            if ($slotStart < $booked['end'] && $slotEnd > $booked['start']) {
                return false;
            }
        }
        return true;
    }

}
