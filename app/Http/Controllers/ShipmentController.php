<?php

namespace App\Http\Controllers;

use App\Models\Shipment;
use App\Models\Route;
use App\Models\Truck;
use App\Models\Vendor;
use App\Models\Gate;
use App\Models\GateUsage;
use App\Models\ShipmentCost;
use App\Models\DeliveryOrder;
use App\Models\DeliveryOrderItem;
use App\Models\DeliveryOrderAttachment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ShipmentExport;
use Carbon\Carbon;

class ShipmentController extends Controller
{
    /**
     * Menampilkan daftar shipment
     */
    public function index(Request $request)
    {
        // Ambil filter dari request
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');
        $route = $request->input('route');
        $gate = $request->input('gate');
        $status = $request->input('status');

        // Query dasar
        $query = Shipment::query();

        // Filter tanggal (jika diisi)
        if ($dateFrom && $dateTo) {
            $query->whereBetween('delivery_date', [$dateFrom, $dateTo]);
        } elseif ($dateFrom) {
            $query->whereDate('delivery_date', '>=', $dateFrom);
        } elseif ($dateTo) {
            $query->whereDate('delivery_date', '<=', $dateTo);
        }

        // Filter route
        if ($route) {
            $query->where('route', $route);
        }

        // Filter gate
        if ($gate) {
            $query->where('gate', $gate);
        }

        // Filter status
        if ($status) {
            $query->where('status', $status);
        }

        // Eksekusi query
        $shipments = $query->orderBy('delivery_date', 'desc')->paginate(10);

        // Cegah error "Attempt to read property noshipment on null"
        foreach ($shipments as $shipment) {
            $shipment->noshipment = $shipment->noshipment ?? '-';
            $shipment->route_name = optional($shipment->route)->route_name ?? '-';
            $shipment->gate_name = optional($shipment->gate)->gate_name ?? '-';
        }

        // Ambil data untuk dropdown filter
        $routes = Route::select('route', 'route_name')->get();
        // ambil distinct gate untuk dropdown
        $gates = Gate::select('gate')->distinct()->orderBy('gate')->get();
        $statuses = [
            'open'        => 'OPEN',
            'inprogress'  => 'IN PROGRESS',
            'closed'      => 'CLOSED',
            'cancelled'   => 'CANCELLED',
        ];

        return view('shipment.index', compact(
            'shipments',
            'routes',
            'gates',
            'statuses',
            'dateFrom',
            'dateTo',
            'route',
            'gate',
            'status'
        ))->with('pageTitle', 'Shipment');
    }

    /**
     * Form create shipment baru
     */
    public function create()
    {
        $routes = Route::all();
        $vendors = Vendor::all();
        $trucks = Truck::all();
        $costs = ShipmentCost::all();

        return view('shipment.create', compact('routes', 'vendors', 'trucks', 'costs'));
    }

    /**
     * Simpan shipment baru
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'noshipment'    => 'required|unique:shipments,noshipment',
            'route'         => 'required|string',
            'shipcost'      => 'nullable|integer',
            'truck_id'      => 'required|integer',
            'driver'        => 'nullable|string',
            'transporter'   => 'nullable|string',
            'noseal'        => 'nullable|string',
            'delivery_date' => 'required|date',
            'gate'          => 'nullable|string',
            'timestart'     => 'nullable',
            'timeend'       => 'nullable',
            'status'        => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            Shipment::create($validated);
            DB::commit();
            return redirect()->route('shipment.index')->with('success', 'Shipment berhasil dibuat.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal membuat shipment: ' . $e->getMessage());
            return back()->with('error', 'Gagal membuat shipment: ' . $e->getMessage());
        }
    }

    /**
     * Form edit shipment
     */
    public function edit($id)
    {
        $shipment = Shipment::findOrFail($id);
        $routes = Route::all();
        $vendors = Vendor::with(['trucks.driver'])->get();
        $trucks = Truck::all();
        $costs = ShipmentCost::all();

        return view('shipment.edit', compact('shipment', 'routes', 'vendors', 'trucks', 'costs'))->with('pageTitle', 'Shipment');
    }

    /**
     * Update shipment
     */
    public function update(Request $request, $id)
    {
        $shipment = Shipment::findOrFail($id);

        // ✅ validasi data
        $validated = $request->validate([
            'route'         => 'required|string',
            'transporter'   => 'required|string',
            'shipcost'      => 'required|string',
            'truck'         => 'required|string',
            'driver1'       => 'required|string',
            'gate'          => 'required|string',
            'timestart'     => 'required|string',
            'timeend'       => 'required|string',
            'delivery_date' => 'required|date',
            'status'        => 'nullable|string',
        ]);

        // ✅ log semua request
        Log::info('=== Shipment Update Request ===', [
            'shipment_id' => $id,
            'noshipment'  => $shipment->noshipment,
            'validated'   => $validated,
            'raw_request' => $request->all(),
        ]);

        try {
            // ✅ update shipment
            $shipment->update([
                'route'         => $validated['route'],
                'transporter'   => $validated['transporter'],
                'shipcost'      => $validated['shipcost'],
                'truck_id'      => $validated['truck'],
                'driver'        => $validated['driver1'],
                'gate'          => $validated['gate'],
                'timestart'     => $validated['timestart'],
                'timeend'       => $validated['timeend'],
                'delivery_date' => $validated['delivery_date'],
                'status'        => $validated['status'] ?? $shipment->status,
            ]);

            // ✅ update atau insert gate_usage berdasarkan noshipment (bukan id)
            $gateUsage = \App\Models\GateUsage::updateOrCreate(
                ['noshipment' => $shipment->noshipment],
                [
                    'gate'          => $validated['gate'],
                    'delivery_date' => $validated['delivery_date'],
                    'timestart'     => $validated['timestart'],
                    'timeend'       => $validated['timeend'],
                ]
            );

            Log::info('✅ Shipment dan GateUsage updated', [
                'shipment_id'  => $id,
                'noshipment'   => $shipment->noshipment,
                'gate_usage_id'=> $gateUsage->id ?? null,
            ]);

            return redirect()
                ->route('shipment.index')
                ->with('success', 'Shipment dan gate usage berhasil diperbarui.');
        } catch (\Exception $e) {
            Log::error('❌ Shipment update failed', [
                'shipment_id' => $id,
                'error'       => $e->getMessage(),
            ]);

            return back()->with('error', 'Gagal update shipment: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        // misalnya redirect balik ke index atau tampilkan detail shipment
        return redirect()->route('shipment.index');
    }

    /**
     * Hapus shipment
     */
    public function destroy($id)
    {
        $shipment = Shipment::findOrFail($id);

        try {
            $shipment->delete();
            return redirect()->route('shipment.index')->with('success', 'Shipment berhasil dihapus.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus shipment: ' . $e->getMessage());
        }
    }

    public function export(Request $request)
    {
        return Excel::download(new ShipmentExport($request->all()), 'shipments.xlsx');
    }

    public function getCost(Request $request)
    {
        $route = $request->input('route');
        $vendor = $request->input('vendor');
        $truckType = $request->input('truck_type');

        $cost = ShipmentCost::where('route', $route)
            ->where('idvendor', $vendor)
            ->where('type_truck', $truckType)
            ->first();

        if ($cost) {
            return response()->json([
                'success' => true,
                'price_freight' => $cost->price_freight,
                'price_driver' => $cost->price_driver,
                'shipcost' => $cost->id,
            ]);
        } else {
            return response()->json(['success' => false]);
        }
    }

    public function getAvailableGates(Request $request)
    {
        $date = $request->input('date');
        $type = $request->input('truck_type'); // pastikan sama dengan js: truckType
        $source = $request->input('source');

        \Log::info("AvailableGates Request", compact('date', 'type', 'source'));

        if (!$date || !$type || !$source) {
            return response()->json(['success' => false, 'message' => 'Missing parameters']);
        }

        $deliveryDate = Carbon::parse($date)->startOfDay()->tz(config('app.timezone'));

        // Ambil gates sesuai source & type
        $gates = Gate::where('point', $source)
            ->where('type', $type)
            ->orderBy('gate')
            ->get();

        \Log::info("AvailableGates: Found {$gates->count()} gates for source {$source} and type {$type}");

        $availableList = [];

        foreach ($gates as $gate) {

            // Ambil durasi slot dari gate
            $duration = $gate->duration_minutes ?? 30;

            $availableStart = Carbon::parse($deliveryDate->toDateString() . ' ' . ($gate->timestart ?? '07:00'))
                                ->tz(config('app.timezone'));
            $availableEnd   = Carbon::parse($deliveryDate->toDateString() . ' ' . ($gate->timeend ?? '17:00'))
                                ->tz(config('app.timezone'));

            // Ambil booked slot untuk gate ini
            $booked = GateUsage::where('gate', $gate->gate)
                ->whereDate('delivery_date', $deliveryDate)
                ->get(['timestart', 'timeend'])
                ->map(function($u) use ($deliveryDate) {
                    return [
                        'start' => Carbon::parse($deliveryDate->toDateString() . ' ' . $u->timestart)
                                        ->tz(config('app.timezone')),
                        'end'   => Carbon::parse($deliveryDate->toDateString() . ' ' . $u->timeend)
                                        ->tz(config('app.timezone')),
                    ];
                })
                ->sortBy('start')
                ->values()
                ->toArray();

            // \Log::info("Gate {$gate->gate} booked slots: " . json_encode($booked));

            // Generate semua slot
            $slots = [];
            $cursor = $availableStart->copy();

            while ($cursor->lt($availableEnd)) {
                $slotStart = $cursor->copy();
                $slotEnd   = $cursor->copy()->addMinutes($duration);

                if ($slotEnd->gt($availableEnd)) break;

                // cek bentrok dengan booked
                $isBooked = false;
                foreach ($booked as $b) {
                    // \Log::info("Checking slot {$slotStart->format('H:i')} - {$slotEnd->format('H:i')} against booked {$b['start']->format('H:i')} - {$b['end']->format('H:i')}");
                    if ($slotStart < $b['end'] && $slotEnd > $b['start']) {
                        $isBooked = true;
                        break;
                    }
                }

                $slots[] = [
                    'gate' => $gate->gate,
                    'start' => $slotStart->format('H:i'),
                    'end'   => $slotEnd->format('H:i'),
                    'status' => $isBooked ? 'booked' : 'available',
                ];

                $cursor->addMinutes($duration);
            }

            $availableList[] = [
                'gate' => $gate->gate,
                'slots' => $slots,
            ];
        }

        return response()->json([
            'success' => true,
            'available' => $availableList
        ]);
    }

    public function editWithDo()
    {
        return view('do.edit')->with('pageTitle', 'Removal Good Issue');
    }

    public function searchShipment(Request $request)
    {
        $noshipment = $request->get('noshipment');

        $shipment = \App\Models\Shipment::with(['truck', 'vendor', 'shipmentCost', 'driver'])
            ->where('noshipment', $noshipment)
            ->first();

        if (!$shipment) {
            return response()->json(['success' => false, 'message' => 'Shipment tidak ditemukan']);
        }

        $deliveryOrders = \App\Models\DeliveryOrder::with(['items.material', 'poheader.customer', 'attachments'])
            ->where('noshipment', $noshipment)
            ->get();

        return response()->json([
            'success' => true,
            'shipment' => $shipment,
            'deliveryOrders' => $deliveryOrders,
        ]);
    }

    public function updateDoDetail(Request $request)
    {
        $validated = $request->validate([
            'noshipment'    => 'required|string',
            'noseal'        => 'required|string',
            'nodo'          => 'required|string',
            'tara_weight'   => 'required|numeric|min:1',
            'gross_weight'  => 'required|numeric|gt:tara_weight',
            'start_loading' => 'required|date',
            'finish_loading'=> 'required|date|after:start_loading',
            'qty_act'       => 'required|array|min:1',
            'qty_act.*'     => 'required|numeric|min:1',
        ]);

        $do = \App\Models\DeliveryOrder::where('nodo', $validated['nodo'])->firstOrFail();
        $do->update([
            'tara_weight'   => $validated['tara_weight'],
            'gross_weight'  => $validated['gross_weight'],
            'start_loading' => $validated['start_loading'],
            'end_loading'   => $validated['finish_loading'],
        ]);

        // update no_seal di shipment
        $shipment = \App\Models\Shipment::where('noshipment', $do->noshipment)->first();
        if ($shipment) {
            $shipment->update([
            'noseal' => $validated['noseal'],
            'status' => 'InProgress', // ← tambahan di sini
        ]);
        }

        // update qty actual
        if ($request->has('qty_act')) {
            foreach ($request->qty_act as $materialCode => $qtyAct) {
                \App\Models\DeliveryOrderItem::where('nodo', $validated['nodo'])
                    ->where('material_code', $materialCode)
                    ->update(['qty_act' => $qtyAct]);
            }
        }

        return back()->with('success', 'Data DO & Shipment berhasil diperbarui.');
    }

    public function printSuratJalan(Request $request)
    {
        $validated = $request->validate([
            'noshipment' => 'required|string',
            'nodo'       => 'required|string',
            'nopol'      => 'required|string',
            'driver'     => 'required|string',
        ]);

        $shipment = Shipment::where('noshipment', $request->noshipment)->firstOrFail();
        $do = DeliveryOrder::with('items')->where('nodo', $request->nodo)->firstOrFail();

        $pdf = Pdf::loadView('do.suratjalan', [
            'shipment' => $shipment,
            'do'       => $do,
            'nopol'    => $request->nopol,
            'driver'   => $request->driver,
            'remarks'  => $request->remarks,
            'tanggal'  => now()->format('d-m-Y H:i'),
        ])->setPaper('A4', 'portrait');

        return $pdf->stream("SuratJalan_{$request->nodo}.pdf");
    }

    public function receipt(Request $request)
    {
        if ($request->ajax()) {
            $noshipment = $request->get('noshipment');

            // Ambil shipment beserta delivery order dan itemnya
            $shipment = \App\Models\Shipment::with(['deliveryOrders.items'])
                ->where('noshipment', $noshipment)
                ->first();

            if (!$shipment) {
                return response()->json(['success' => false, 'message' => 'Shipment tidak ditemukan']);
            }

            // Ambil semua DO terkait shipment
            $deliveryOrders = $shipment->deliveryOrders;

            if ($deliveryOrders->isEmpty()) {
                return response()->json(['success' => false, 'message' => 'Tidak ada Delivery Order untuk shipment ini.']);
            }

            return response()->json([
                'success' => true,
                'shipment' => $shipment,
                'deliveryOrders' => $deliveryOrders,
            ]);
        }

        // Kalau bukan AJAX, tampilkan view
        return view('do.receipt')->with('pageTitle', 'Goods Receipt');
    }

    public function storeReceipt(Request $request)
    {
        $node = is_array($request->nodo) ? $request->nodo[0] : $request->nodo;

        \Log::info('StoreReceipt called', ['nodo' => $node, 'request_all' => $request->all()]);
        $request->validate([
            'nodo' => 'required|exists:delivery_orders,nodo',
            'receipt_date' => 'required|date',
            'qty_act' => 'required|array',
            'qty_act.*' => 'numeric|min:0',
            'attachments.*' => 'nullable|file|max:5120', // 5 MB max
        ]);

        DB::beginTransaction();
        try {
            $now = Carbon::now();

            // === 1️⃣ Update tabel delivery_orders ===
            $do = DeliveryOrder::where('nodo', $request->nodo)->firstOrFail();
            $do->update([
                'receipt_date' => $request->receipt_date,
                'updated_at'   => $now,
                'last_update'  => $now,
                'update_by'    => auth()->user()->name ?? 'system',
            ]);

            // === 2️⃣ Update tabel delivery_order_items ===
            foreach ($request->qty_act as $materialCode => $qtyReceipt) {
                $item = DeliveryOrderItem::where('nodo', $request->nodo)
                    ->where('material_code', $materialCode)->first();

                    if ($item) {
                        $qtyReject = max(($item->qty_act ?? 0) - $qtyReceipt, 0); // jaga-jaga biar gak negatif

                        $item->update([
                        'qty_receipt' => $qtyReceipt,
                        'qty_reject'  => $qtyReject,
                        'updated_at'  => $now,
                        'last_update' => $now,
                    ]);
                }
            }

            \Log::info('Checking attachments', ['hasFile' => $request->hasFile('attachments')]);
            // === 3️⃣ Upload file ke storage/public/uploads/do_attachments ===
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    $filename = time() . '_' . $file->getClientOriginalName();
                    // $filepath = $file->storeAs('uploads/do_attachments', $filename, 'public');
                    // Simpan langsung ke public/
                    $destinationPath = public_path('uploads/do_attachments');
                    if (!file_exists($destinationPath)) {
                        mkdir($destinationPath, 0755, true);
                    }

                    $file->move($destinationPath, $filename);
                    $filepath = 'uploads/do_attachments/' . $filename;

                    DeliveryOrderAttachment::create([
                        'nodo'        => $node,
                        'filename'    => $filename,
                        'filepath'    => $filepath,
                        'uploaded_by' => auth()->user()->name ?? 'system',
                    ]);
                }
            } else {
                \Log::warning('No attachments found in request.');
            }

            DB::commit();
            return redirect()->route('do.receipt')->with('success', 'Receipt berhasil disimpan.');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('StoreReceipt gagal', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return back()->with('error', 'Gagal menyimpan receipt: ' . $e->getMessage());
        }
    }

    public function searchList(Request $request)
    {
        $query = Shipment::with(['driver', 'truck', 'routeData', 'doship'])
            ->when($request->no_shipment, function ($q) use ($request) {
                $q->where('noshipment', 'like', "%{$request->no_shipment}%");
            })
            ->when($request->no_mobil, function ($q) use ($request) {
                $q->whereHas('truck', fn($t) => $t->where('nopol', 'like', "%{$request->no_mobil}%"));
            })
            ->when($request->nama_supir, function ($q) use ($request) {
                $q->whereHas('driver', fn($d) => $d->where('name', 'like', "%{$request->nama_supir}%"));
            })
            ->when($request->status, function ($q) use ($request) {
                $q->where('status', $request->status);
            })
            ->orderByDesc('created_at');

        $shipments = $query->get();

        // cari mobil yang masih InProgress
        $inprogressTrucks = Shipment::where('status', 'inprogress')
            ->whereNotNull('truck_id')
            ->with('truck:idtruck,nopol')
            ->get()
            ->pluck('truck.nopol')
            ->filter()
            ->unique()
            ->values()
            ->toArray();

        return response()->json([
            'success' => true,
            'shipments' => $shipments,
            'inprogressTrucks' => $inprogressTrucks,
        ]);
    }


    public function indexCheck(Request $request)
    {
        $shipments = collect(); // default kosong

        if ($request->filled('keyword') || $request->filled('status')) {
            $query = Shipment::query();

            // Filter berdasarkan keyword (nama supir, no shipment, no mobil)
            if ($request->filled('keyword')) {
                $query->where(function ($q) use ($request) {
                    $q->where('driver_name', 'like', "%{$request->keyword}%")
                      ->orWhere('shipment_no', 'like', "%{$request->keyword}%")
                      ->orWhere('vehicle_no', 'like', "%{$request->keyword}%");
                });
            }

            // Filter berdasarkan status
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            $shipments = $query->orderBy('shipment_no', 'desc')->get();
        }

        return view('do.index_check', compact('shipments'))->with('pageTitle', 'Realization');
    }

    public function doCheckin($noshipment)
    {
        $shipment = Shipment::with(['routeData', 'driver', 'shipmentCost'])
            ->where('noshipment', $noshipment)
            ->first();

        if (!$shipment) {
            return redirect()->back()->with('error', 'Shipment tidak ditemukan');
        }

        // Ambil shipment beserta delivery order dan itemnya
        $shipmentdetails = DeliveryOrder::with(['items'])
            ->where('noshipment', $noshipment)
            ->first();

        return view('shipment.checkin', compact('shipment','shipmentdetails'))->with('pageTitle', 'Check-In');
    }

    public function doCheckout($noshipment)
    {
        $shipment = Shipment::with(['routeData', 'driver', 'shipmentCost'])
            ->where('noshipment', $noshipment)
            ->first();

        if (!$shipment) {
            return redirect()->back()->with('error', 'Shipment tidak ditemukan');
        }

        // Ambil shipment beserta delivery order dan itemnya
        $shipmentdetails = DeliveryOrder::with(['items'])
            ->where('noshipment', $noshipment)
            ->first();

        return view('shipment.checkout', compact('shipment','shipmentdetails'))->with('pageTitle', 'Check-Out');
    }

    public function storeCheckin(Request $request, $noshipment)
    {
        // Ambil DO berdasarkan nomor shipment
        $deliveryOrder = DeliveryOrder::where('noshipment', $noshipment)->first();

        if (!$deliveryOrder) {
            return redirect()->back()->with('error', 'Delivery Order tidak ditemukan.');
        }

        // Update status dan waktu check-in
        $deliveryOrder->checkin = now();
        $deliveryOrder->last_update = now();
        $deliveryOrder->update_by = auth()->user()->name ?? 'system';
        $deliveryOrder->save();

        return redirect()
        ->route('do.checkin', ['noshipment' => $noshipment])
        ->with('success', 'Check-in berhasil disimpan.');
    }

    public function storeCheckout(Request $request, $noshipment)
    {
        // Ambil DO berdasarkan nomor shipment
        $deliveryOrder = DeliveryOrder::where('noshipment', $noshipment)->first();

        if (!$deliveryOrder) {
            return redirect()->back()->with('error', 'Delivery Order tidak ditemukan.');
        }

        // Update status dan waktu check-in
        $deliveryOrder->checkout = now();
        $deliveryOrder->last_update = now();
        $deliveryOrder->update_by = auth()->user()->name ?? 'system';
        $deliveryOrder->save();

        return redirect()
        ->route('do.checkout', ['noshipment' => $noshipment])
        ->with('success', 'Check-out berhasil disimpan.');
    }

}
