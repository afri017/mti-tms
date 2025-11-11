@extends('layout.main')

@section('content')
<section class="content" style="padding-bottom: 100px;">
    <div class="card">
      <div class="card-header">
        <h4>Edit Shipment</h4>
      </div>

      <div class="card-body">
          @if ($errors->any())
            <div class="alert alert-danger">
                <strong>Terjadi kesalahan!</strong> Periksa kembali isian berikut:
                <ul class="mb-0 mt-2">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    <form id="shipmentForm" action="{{ route('shipment.update', $shipment->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="row mb-3">
            <div class="col-md-4">
                <label>No Shipment</label>
                <input type="text" class="form-control" name="noshipment" value="{{ old('noshipment', $shipment->noshipment) }}" readonly>
            </div>
            <div class="col-md-4">
                <label>Transporter</label>
                <select name="transporter" id="transporter" class="form-control">
                    <option value="">-- Pilih Transporter --</option>
                    @foreach($vendors as $t)
                        <option value="{{ $t->idvendor }}"
                            {{ old('transporter', $shipment->transporter) == $t->idvendor ? 'selected' : '' }}>
                            {{ $t->transporter_name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label>Truck</label>
                <select name="truck" id="truck" class="form-control">
                    <option value="">-- Pilih Truck --</option>
                    @foreach($vendors as $t)
                        @foreach($t->trucks ?? [] as $tgo)
                            <option value="{{ $tgo->idtruck }}"
                                    data-transporter="{{ $t->idvendor }}"
                                    data-driver="{{ $tgo->driver->name ?? '' }}"
                                    data-driver-kode="{{ $tgo->driver->iddriver ?? '' }}"
                                    data-type-truck="{{ $tgo->type_truck }}"
                                    data-type-ton="{{ $tgo->tonnage?->type_truck }}"
                                    {{ old('truck', $shipment->truck_id) == $tgo->idtruck ? 'selected' : '' }}>
                                {{ $tgo->nopol }} ({{ $tgo->tonnage?->desc }})
                            </option>
                        @endforeach
                    @endforeach
                </select>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-4">
                <label>Driver</label>
                <input type="text" class="form-control" id="driver" name="driver"
                value="{{ old('driver', optional($shipment->truck->driver)->name) }}" readonly>
                <input type="text" class="form-control" id="driver1" name="driver1"
                value="{{ old('driver1', optional($shipment->truck->driver)->iddriver) }}" hidden>
            </div>
            <div class="col-md-4">
                <label>Freight Price</label>
                <input type="text" id="freightprice" class="form-control" name="freightprice"
                       value="{{ old('freightprice', $shipment->shipmentCost ? 'Rp ' . number_format($shipment->shipmentCost->price_freight, 0, ',', '.') : '-') }}"
                       readonly>
                <input type="text" id="shipcost" class="form-control" name="shipcost"
                       value="{{ old('shipcost', $shipment->shipcost ? $shipment->shipcost : '-') }}"
                       hidden>
            </div>
            <div class="col-md-4">
                <label>Driver Price</label>
                <input type="text" id="driverprice" class="form-control" name="driverprice"
                       value="{{ old('driverprice', $shipment->shipmentCost ? 'Rp ' . number_format($shipment->shipmentCost->price_driver, 0, ',', '.') : '-') }}"
                       readonly>
            </div>
        </div>


        <div class="row mb-3">
            <div class="col-md-4">
                <label>Route</label>
                <select name="route" id="route" class="form-control">
                    <option value="">-- Pilih Route --</option>
                    @foreach($routes as $route)
                        <option value="{{ $route->route }}"
                            data-source="{{ $route->sourceData->location_name }}"
                            data-destination="{{ $route->destinationData->location_name }}"
                            {{ old('route', $shipment->route) == $route->route ? 'selected' : '' }}>
                            {{ $route->route }} - {{ $route->route_name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label>Source</label>
                <input type="text" class="form-control" id="source" name="source"
                value="{{ optional($shipment->routeData->sourceData)->location_name }}"
                data-source="{{ optional($shipment->routeData)->source }}" readonly>
            </div>
            <div class="col-md-4">
                <label>Destination</label>
                <input type="text" class="form-control" id="destination" name="destination"
                value="{{ optional($shipment->routeData->destinationData)->location_name }}" readonly>
            </div>
        </div>


        <div class="row mb-3">
            <div class="col-md-3">
                <label>Gate</label>
                <select class="form-control" id="gate" name="gate_display" readonly>
                    <option value="">-- Pilih Gate --</option>
                    @if($shipment->gate)
                        <option value="{{ $shipment->gate }}" selected>{{ $shipment->gate }}</option>
                    @endif
                </select>
                <!-- Mirror input (hidden) yang dikirim ke controller -->
                <input hidden  name="gate" id="gateHidden" value="{{ $shipment->gate }}">
                {{-- Tempat menampilkan slot gate --}}
                <!-- <div id="available_gate_list" class="mt-2"></div> -->
            </div>
            <div class="col-md-3">
                <label>Time Start</label>
                <input type="time" id="timestart" class="form-control" name="timestart" value="{{ old('timestart', $shipment->timestart) }}" readonly>
            </div>
            <div class="col-md-3">
                <label>Time End</label>
                <input type="time" id="timeend" class="form-control" name="timeend" value="{{ old('timeend', $shipment->timeend) }}" readonly>
            </div>
            <div class="col-md-3">
                <label>Delivery Date</label>
                <input type="date" class="form-control" id="delivery_date" name="delivery_date" value="{{ old('delivery_date', $shipment->delivery_date) }}">
            </div>
        </div>

        <div class="row mb-12">
            <div id="available_gate_list" class="mt-2"></div>
        </div>

        <div class="row mb-3">
            <div class="col-md-3">
                <label>Status</label>
                <select class="form-control" name="status" readonly>
                    <option value="{{ $shipment->status }}">{{ $shipment->status }}</option>
                    <option value="pending" {{ $shipment->status == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="completed" {{ $shipment->status == 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="cancelled" {{ $shipment->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>
        </div>

        <button type="submit" class="btn btn-success" id="submitBtn">Simpan Perubahan</button>
        <a href="{{ route('shipment.index') }}" class="btn btn-secondary">Kembali</a>
    </form>
</div>
</div>
</section>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // === ELEMENTS ===
    const routeSelect = document.getElementById('route');
    const sourceInput = document.getElementById('source');
    const destinationInput = document.getElementById('destination');
    const transporterSelect = document.getElementById('transporter');
    const truckSelect = document.getElementById('truck');
    const driverInput = document.getElementById('driver');
    const driverInput1 = document.getElementById('driver1');
    const freightInput = document.getElementById('freightprice');
    const shipcostInput = document.getElementById('shipcost');
    const driverPriceInput = document.getElementById('driverprice');
    const deliveryDateInput = document.getElementById('delivery_date');
    const availableContainer = document.getElementById('available_gate_list');
    const gateSelect = document.getElementById('gate');
    const gateHidden = document.getElementById('gateHidden'); // mirror input hidden
    const timestartInput = document.getElementById('timestart');
    const timeendInput = document.getElementById('timeend');

    // === FUNGSI UTILITAS ===
    function formatRupiah(value) {
        return 'Rp ' + new Intl.NumberFormat('id-ID').format(value);
    }

    // === UPDATE SOURCE / DESTINATION ===
    routeSelect.addEventListener('change', function () {
        const selected = this.options[this.selectedIndex];
        const source = selected.getAttribute('data-source') || '';
        const destination = selected.getAttribute('data-destination') || '';
        sourceInput.value = source;
        destinationInput.value = destination;

        updateCost(); // update harga juga ketika route berubah
    });

    // === FILTER TRUCK BERDASARKAN TRANSPORTER ===
    function filterTruckByTransporter() {
        const transporterId = transporterSelect.value;
        for (let i = 0; i < truckSelect.options.length; i++) {
            const opt = truckSelect.options[i];
            const match = !transporterId || opt.getAttribute('data-transporter') === transporterId || opt.value === "";
            opt.style.display = match ? 'block' : 'none';
        }
            // Pastikan truck default tetap tampil
            if (truckSelect.value) {
                const selectedTruck = truckSelect.querySelector(`option[value="${truckSelect.value}"]`);
                if (selectedTruck) selectedTruck.style.display = 'block';
            }
    }

    // === UPDATE DRIVER ===
    function updateDriver() {
        const selected = truckSelect.options[truckSelect.selectedIndex];
        driverInput.value = selected?.getAttribute('data-driver') || '';
        driverInput1.value = selected?.getAttribute('data-driver-kode') || '';
    }

    // === FETCH COST ===
    async function updateCost() {
        const route = routeSelect.value;
        const vendor = transporterSelect.value;
        const selectedTruck = truckSelect.options[truckSelect.selectedIndex];
        const truckType = selectedTruck ? selectedTruck.getAttribute('data-type-truck') : '';

        console.log("Fetch cost =>", { route, vendor, truckType });

        if (!route || !vendor || !truckType) {
            freightInput.value = '-';
            driverPriceInput.value = '-';
            return;
        }

        try {
            const res = await fetch(`{{ route('shipment.cost') }}?route=${route}&vendor=${vendor}&truck_type=${truckType}`);
            const data = await res.json();
            console.log("Response:", data);

            if (data.success) {
                freightInput.value = formatRupiah(data.price_freight);
                driverPriceInput.value = formatRupiah(data.price_driver);
                shipcostInput.value = data.shipcost;
            } else {
                freightInput.value = '-';
                driverPriceInput.value = '-';
                shipcostInput.value = '-';
            }
        } catch (err) {
            console.error('Error fetching shipment cost:', err);
            freightInput.value = '-';
            driverPriceInput.value = '-';
        }
    }

    // === FETCH AVAILABLE GATES ===
    async function fetchAvailableGates() {
        const source = sourceInput.getAttribute('data-source');
        const selectedTruck = truckSelect.options[truckSelect.selectedIndex];
        const truckType = selectedTruck ? selectedTruck.getAttribute('data-type-ton') : '';
        const date = deliveryDateInput.value;


        if (!source || !truckType || !date) {
            availableContainer.innerHTML = '<small class="text-muted">Isi source, truck, dan tanggal terlebih dahulu.</small>';
            return;
        }

        try {
            const res = await fetch(`/shipment/available-gates?source=${source}&truck_type=${truckType}&date=${date}`);
            const data = await res.json();
            console.log("Available Gates:", data);

            availableContainer.innerHTML = '';

            if (data.success && data.available.length > 0) {
                data.available.forEach((g, index) => {
                    const cardDiv = document.createElement('div');
                    cardDiv.classList.add('card', 'mb-3');

                    cardDiv.innerHTML = `
                        <div class="card-header p-2 d-flex justify-content-between align-items-center"
                             style="cursor:pointer;"
                             data-bs-toggle="collapse"
                             data-bs-target="#collapse${index}"
                             aria-expanded="true"
                             aria-controls="collapse${index}">
                            <strong>Gate ${g.gate}</strong>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                    <i class="fas fa-minus"></i>
                                </button>
                                <button type="button" class="btn btn-tool" data-card-widget="remove">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                        <div id="collapse${index}" class="collapse show">
                            <div class="card-body p-2 d-flex flex-wrap">
                                ${g.slots.map(s => `
                                    <span
                                        class="badge ${s.status === 'available' ? 'bg-success' : 'bg-secondary'} m-1 slot-badge"
                                        data-gate="${g.gate}"
                                        data-start="${s.start}"
                                        data-end="${s.end}"
                                        style="cursor: ${s.status === 'available' ? 'pointer' : 'not-allowed'}">
                                        ${s.start.includes('T') ? s.start.split('T')[1].substring(0,5) : s.start.substring(0,5)} -
                                        ${s.end.includes('T') ? s.end.split('T')[1].substring(0,5) : s.end.substring(0,5)}
                                    </span>
                                `).join('')}
                            </div>
                        </div>
                    `;

                    availableContainer.appendChild(cardDiv);

                    // Inisialisasi collapse per card
                    const collapseEl = cardDiv.querySelector('.collapse');
                    const bsCollapse = new bootstrap.Collapse(collapseEl, { toggle: false });

                    // Slot klik per card
                    cardDiv.querySelectorAll('.slot-badge.bg-success').forEach(badge => {
                        badge.addEventListener('click', function () {
                            const gate = this.getAttribute('data-gate');
                            const startRaw = this.getAttribute('data-start') || '';
                            const endRaw = this.getAttribute('data-end') || '';

                            let start = startRaw.includes('T') ? startRaw.split('T')[1].substring(0,5) : startRaw.substring(0,5);
                            let end = endRaw.includes('T') ? endRaw.split('T')[1].substring(0,5) : endRaw.substring(0,5);

                            if (timestartInput) timestartInput.value = start;
                            if (timeendInput) timeendInput.value = end;

                            // Update select gate
                            if (gate) {
                                let optionExists = false;
                                for (let i = 0; i < gateSelect.options.length; i++) {
                                    if (gateSelect.options[i].value === gate) {
                                        gateSelect.selectedIndex = i;
                                        optionExists = true;
                                        break;
                                    }
                                }
                                if (!optionExists) {
                                    const opt = document.createElement('option');
                                    opt.value = gate;
                                    opt.text = gate;
                                    opt.selected = true;
                                    gateSelect.appendChild(opt);
                                }

                                if (gateHidden) gateHidden.value = gate;

                            }

                            // Tutup semua card lain kecuali yang ini
                            data.available.forEach((_, i) => {
                                if (i !== index) {
                                    const otherCollapse = document.getElementById(`collapse${i}`);
                                    bootstrap.Collapse.getInstance(otherCollapse)?.hide();
                                }
                            });

                            // Buka card saat ini jika tertutup
                            bsCollapse.show();
                        });
                    });
                });
            } else {
                availableContainer.innerHTML = '<p class="text-muted">Tidak ada gate tersedia untuk tanggal tersebut.</p>';
            }
        } catch (err) {
            console.error('Error fetching available gates:', err);
            availableContainer.innerHTML = '<p class="text-danger">Gagal memuat data gate.</p>';
        }
    }


    // === EVENT HANDLER ===
    let initialized = false;
    transporterSelect.addEventListener('change', function () {
        filterTruckByTransporter();

        // Reset truck & driver hanya jika bukan load awal
        if (initialized) {
            truckSelect.value = "";
            driverInput.value = "";
        }
        updateCost();
    });

    // === TRUCK CHANGE ===
    truckSelect.addEventListener('change', function () {
        updateDriver();
        updateCost();
        fetchAvailableGates();
    });

    deliveryDateInput.addEventListener('change', fetchAvailableGates);

    // === INIT ===
    filterTruckByTransporter();
    updateDriver();
    // tambahkan delay kecil agar data default sempat terload sebelum fetch
    setTimeout(() => {
        updateCost();
        fetchAvailableGates();
        initialized = true;
    }, 200);

    // Debug & safe submit handler:
    const form = document.getElementById('shipmentForm');
    const submitBtn = document.getElementById('submitBtn');

    if (form) {
        form.addEventListener('submit', function (e) {
            try {
                // Tampilkan dulu form data ke console supaya kita tahu apa yang dikirim
                const fd = new FormData(form);
                const obj = {};
                fd.forEach((v,k) => {
                    // kalau ada beberapa input dengan nama sama, build array
                    if (Object.prototype.hasOwnProperty.call(obj, k)) {
                        if (!Array.isArray(obj[k])) obj[k] = [obj[k]];
                        obj[k].push(v);
                    } else {
                        obj[k] = v;
                    }
                });
                console.log('Submitting Shipment Update:', obj);

                // disable tombol supaya tidak double submit
                if (submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.innerText = 'Menyimpan...';
                }

                // NOTE: jangan preventDefault di sini — kita ingin form submit normal ke server
            } catch (err) {
                console.error('Error saat prepare submit:', err);
                // jangan block submit agar server tetap menerima
            }
        });
    }

    // Tangkap error JS global supaya kita tahu kalau ada error yang menghentikan script
    window.addEventListener('error', function (event) {
        console.error('Global JS error caught:', event.error || event.message, event);
        // tampilkan pesan kecil pada user (opsional)
        // alert('Terjadi error javascript — cek console untuk detail.');
    });

    // Tangkap unhandledrejection (Promise errors)
    window.addEventListener('unhandledrejection', function (event) {
        console.error('Unhandled promise rejection:', event.reason);
    });
});
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
@endpush
