@extends('layout.main')
@section('content')
<section class="content">
<div class="container-fluid">
    <div class="card card-default">
        <div class="card-header">
            <h3 class="card-title">Build Truck Load</h3>
            <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                    <i class="fas fa-minus"></i>
                </button>
                <button type="button" class="btn btn-tool" data-card-widget="remove">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="p-4 bg-light rounded shadow-sm mb-4">
                <h2 class="h4 font-weight-bold text-primary-port mb-3 border-bottom pb-2">
                    1. Input Data Purchase Order (PO)
                </h2>
                <div class="row g-3 mb-4">
                    <div class="col-md-8">
                        <label for="po_numbers" class="form-label">Masukkan No. PO (Pisahkan dengan koma)</label>
                        <input type="text" id="po_numbers" name="po_numbers" class="form-control" placeholder="Contoh: PO-2025-00001, PO-2025-00002" required>
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <button type="button" id="load_po" class="btn btn-primary-port w-100 shadow-sm">
                            Muat Data PO
                        </button>
                    </div>
                </div>

                <!-- Hasil -->
                <div id="po_result" class="mt-4"></div>
                <!-- Draft Preview -->
                <div id="draft_preview" class="mt-4"></div>

                <!-- Tombol Build -->
                <div class="mt-3 text-end">
                    <button id="build_truck_btn" class="btn btn-success btn-sm">
                        🚚 Build Truck Load
                    </button>
                </div>

                <!-- Hasil Build -->
                <div id="truck_load_result" class="mt-4"></div>
            </div>
        </div>
    </div>
</div>
</section>
@endsection


@push('scripts')
<script>
let draftPlans = {};
let destinationOptions = [];
let originOptions = [];
let truckOptions = [];
let dropdownLoaded = false;

// 🔹 Ambil data dropdown hanya sekali
async function loadDropdownData() {
    if (dropdownLoaded) return;
    try {
        const res = await fetch('{{ route("optimization.getOptions") }}');
        const data = await res.json();

        if (data.status === 'success') {
            destinationOptions = data.destinations;
            originOptions = data.origins;
            truckOptions = data.truck_types;
            dropdownLoaded = true;
        } else {
            alert('Gagal memuat data referensi dropdown.');
        }
    } catch (err) {
        console.error(err);
        alert('Gagal konek ke server untuk load dropdown.');
    }
}


let truckData = [];
let routeData = [];

async function loadTruckAndRoute() {
    try {
        const res = await fetch('/getTruckRouteData');
        const data = await res.json();

        if (data.status !== 'success') throw new Error(data.message || 'Gagal memuat data.');

        truckData = data.truck_list || [];
        routeData = data.route_list || [];

        console.log('✅ Truck Loaded:', truckData);
        console.log('✅ Route Loaded:', routeData); // <--- lihat ini

        return true;
    } catch (err) {
        console.error('❌ Gagal load data truck/route:', err);
        alert('Gagal memuat data truck atau route.');
        return false;
    }
}

document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('load_po').addEventListener('click', async function() {
        const po_numbers = document.getElementById('po_numbers').value.trim();
        const btn = this;
        const resultDiv = document.getElementById('po_result');

        if (!po_numbers) {
            alert('Silakan masukkan nomor PO terlebih dahulu.');
            return;
        }

        btn.disabled = true;
        const originalText = btn.innerHTML;
        btn.innerHTML = `<span class="spinner-border spinner-border-sm"></span> Memuat...`;

        try {
            const res = await fetch('{{ route("optimization.loadPOData") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ po_numbers })
            });

            const data = await res.json();

            if (data.status === 'success') {
                let html = `
                    <table class="table table-bordered table-striped align-middle">
                        <thead class="table-primary">
                            <tr>
                                <th>#</th>
                                <th>No</th>
                                <th>PO No.</th>
                                <th>PO Date</th>
                                <th>Customer</th>
                                <th>Kode Material</th>
                                <th>Desc Material</th>
                                <th>Qty</th>
                                <th>Unit</th>
                                <th>Origin</th>
                                <th>Destination</th>
                                <th>Truck Type</th>
                                <th>Plan Qty</th>
                            </tr>
                        </thead>
                        <tbody>
                `;

                let counter = 1;
                data.data.forEach(po => {
                    if (po.po_items && po.po_items.length > 0) {
                        po.po_items.forEach(item => {
                            const key = `${po.nopo}_${item.material_code}`;
                            html += `
                                <tr data-key="${key}">
                                    <td><input type="checkbox" class="select-item"></td>
                                    <td>${counter++}</td>
                                    <td>${po.nopo}</td>
                                    <td>${po.podate ?? '-'}</td>
                                    <td>${po.customer?.customer_name ?? '-'}</td>
                                    <td>${item.material_code}</td>
                                    <td>${item.material?.material_desc ?? '-'}</td>
                                    <td>${item.remaining_qty ?? item.qty}</td> <!-- ganti di sini -->
                                    <td>${item.uom}</td>
                                    <td class="src-cell"></td>
                                    <td class="dest-cell"></td>
                                    <td class="truck-cell"></td>
                                    <td><input type="number" class="form-control form-control-sm qty-input" placeholder="Qty" min="0" disabled></td>
                                </tr>
                            `;
                        });
                    }
                });

                html += `</tbody></table>`;
                resultDiv.innerHTML = html;

                attachTableEvents();
            } else {
                alert('Gagal memuat data: ' + (data.message ?? 'Tidak diketahui'));
            }
        } catch (err) {
            alert('Terjadi kesalahan memuat data PO: ' + err.message);
        } finally {
            btn.disabled = false;
            btn.innerHTML = originalText;
        }
    });

    document.getElementById('build_truck_btn').addEventListener('click', buildTruckLoad);
});

function attachTableEvents() {
    document.querySelectorAll('.select-item').forEach(chk => {
        chk.addEventListener('change', async function() {
            const row = this.closest('tr');
            const key = row.dataset.key;
            const srcCell = row.querySelector('.src-cell');
            const destCell = row.querySelector('.dest-cell');
            const truckCell = row.querySelector('.truck-cell');
            const qty = row.querySelector('.qty-input');

            if (!dropdownLoaded) await loadDropdownData();
            await loadTruckAndRoute();

            if (this.checked) {
                // Dropdown Origin
                srcCell.innerHTML = `
                    <select class="form-control form-control-sm origin-input">
                        <option value="">-- Pilih --</option>
                        ${originOptions.map(o =>
                            `<option value="${o.id}" data-capacity="${o.capacity}">${o.location_name}</option>`
                        ).join('')}
                    </select>
                    <input type="hidden" class="origin-id" value="">
                `;

                // Dropdown Destination
                destCell.innerHTML = `
                    <select class="form-control form-control-sm dest-input">
                        <option value="">-- Pilih --</option>
                        ${destinationOptions.map(d =>
                            `<option value="${d.id}" data-capacity="${d.capacity}">${d.location_name}</option>`
                        ).join('')}
                    </select>
                    <input type="hidden" class="destination-id" value="">
                `;

                // Dropdown Truck
                truckCell.innerHTML = `
                    <select class="form-control form-control-sm truck-input">
                        <option value="">-- Pilih --</option>
                        ${truckOptions.map(t =>
                            `<option value="${t.id}" data-capacity="${t.type_truck ?? 0}">${t.type_truck} (${t.desc})</option>`
                        ).join('')}
                    </select>
                `;

                qty.disabled = false;

                draftPlans[key] = {
                    po_number: row.children[2].innerText,
                    material_code: row.children[5].innerText,
                    qty: parseFloat(row.children[7].innerText || 0),
                    origin: '',
                    origin_name: '',
                    destination: '',
                    destination_name: '',
                    type_truck: '',
                    truck_capacity: 0,
                    plan_qty: 0
                };

                row.querySelector('.origin-input').addEventListener('change', handleInputChange);
                row.querySelector('.dest-input').addEventListener('change', handleInputChange);
                row.querySelector('.truck-input').addEventListener('change', handleInputChange);
                row.querySelector('.qty-input').addEventListener('input', handleInputChange);

            } else {
                destCell.innerHTML = '';
                truckCell.innerHTML = '';
                qty.disabled = true;
                qty.value = '';
                delete draftPlans[key];
            }

            updateDraftPreview();
        });
    });
}

function handleInputChange() {
    const row = this.closest('tr');
    const key = row.dataset.key;
    if (!draftPlans[key]) return;

    const srcInput = row.querySelector('.origin-input');
    const destInput = row.querySelector('.dest-input');
    const truckInput = row.querySelector('.truck-input');
    const qtyInput = row.querySelector('.qty-input');

    const selectedOrigin = srcInput?.selectedOptions?.[0];
    const selectedDest = destInput?.selectedOptions?.[0];
    const selectedTruck = truckInput?.selectedOptions?.[0];

    draftPlans[key].origin = selectedOrigin?.value || '';
    draftPlans[key].origin_name = selectedOrigin?.text || '-';
    draftPlans[key].origin_capacity = selectedOrigin?.dataset.capacity ? parseFloat(selectedOrigin.dataset.capacity) : 0;

    draftPlans[key].destination = selectedDest?.value || '';
    draftPlans[key].destination_name = selectedDest?.text || '-';
    draftPlans[key].destination_capacity = selectedDest?.dataset.capacity ? parseFloat(selectedDest.dataset.capacity) : 0;

    draftPlans[key].type_truck = selectedTruck?.value || '';
    draftPlans[key].truck_capacity = selectedTruck?.dataset.capacity ? parseFloat(selectedTruck.dataset.capacity) : 0;

    draftPlans[key].plan_qty = parseFloat(qtyInput?.value || 0);

    // ✅ Update input hidden agar selalu sinkron
    row.querySelector('.origin-id').value = draftPlans[key].origin;
    row.querySelector('.destination-id').value = draftPlans[key].destination;

    updateDraftPreview();

    // ✅ Debug console
    console.log('draftPlans[' + key + ']:', draftPlans[key]);
}


function updateDraftPreview() {
    const preview = document.getElementById('draft_preview');
    const values = Object.values(draftPlans);
    if (values.length === 0) {
        preview.innerHTML = '';
        return;
    }

    let html = `
        <h5 class="mt-4 mb-2">Rencana Sementara:</h5>
        <table class="table table-sm table-bordered">
            <thead class="table-secondary">
                <tr>
                    <th>No</th>
                    <th>PO No.</th>
                    <th>Material</th>
                    <th>Origin</th>
                    <th>Destination</th>
                    <th>Truck Type</th>
                    <th>Draft Qty</th>
                    <th>Capacity</th>
                </tr>
            </thead>
            <tbody>
    `;

    values.forEach((item, i) => {
        // ✅ fallback aman untuk origin
        const originName =
            (typeof originOptions !== 'undefined'
                ? originOptions.find(o => o.id == item.origin)?.location_name
                : destinationOptions.find(d => d.id == item.origin)?.location_name) || '-';

        const destName =
            destinationOptions.find(d => d.id == item.destination)?.location_name || '-';

        const truckName =
            truckOptions.find(t => t.id == item.type_truck)?.type_truck || '-';

        html += `
            <tr>
                <td>${i + 1}</td>
                <td>${item.po_number}</td>
                <td>${item.material_code}</td>
                <td>${originName}</td>
                <td>${destName}</td>
                <td>${truckName}</td>
                <td>${item.plan_qty}</td>
                <td>${item.truck_capacity || '-'}</td>
            </tr>
        `;
    });

    html += `</tbody></table>`;
    preview.innerHTML = html;
}


// 🚚 Build Truck Load + Expand Trip Breakdown
function buildTruckLoad() {

    const values = Object.values(draftPlans);
    if (values.length === 0) {
        alert('Tidak ada data yang dipilih!');
        return;
    }

    let errors = [];
    let html = `
        <h5 class="mt-4 mb-2">📦 Hasil Build Truck Load:</h5>
        <table class="table table-sm table-bordered align-middle" id="truckLoadTable">
            <thead class="table-success text-center">
                <tr>
                    <th>No</th>
                    <th>PO No.</th>
                    <th>Material</th>
                    <th>Route</th>
                    <th>Deskripsi</th>
                    <th>Truck Type</th>
                    <th>Capacity (Ton)</th>
                    <th>Draft Qty</th>
                    <th>Total Truck</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
    `;

    values.forEach((item, i) => {
        const originName = item.origin_name || '-';
        const destName = item.destination_name || '-';

        // 🔹 Debug: cek apa yang dicari
        console.log(`Mencari route untuk Origin: '${item.origin}', Destination: '${item.destination}'`);

        // 🔹 Cari route berdasarkan ID origin & destination, trim untuk aman
        const routeObj = routeData?.find(r =>
            String(r.source).trim() === String(item.origin).trim() &&
            String(r.destination).trim() === String(item.destination).trim()
        );

        console.log('draftPlans origin:', item.origin, 'destination:', item.destination);
        console.log('routeData sources:', routeData.map(r => r.source));
        console.log('routeData destinations:', routeData.map(r => r.destination));

        // 🔹 Debug: hasil pencarian route
        if (!routeObj) {
            console.warn(`Route NOT FOUND untuk PO ${item.po_number}, Material ${item.material_code}`);
        } else {
            console.log('Route found:', routeObj);
        }

        // 🔹 Route = kode route, Deskripsi = kombinasi origin → destination atau route_name
        const routeCode = routeObj?.route || 'NOT FOUND';
        const routeDesc = routeObj?.route_name || `${originName} → ${destName}`;

        // 🔹 Truck dan perhitungan kapasitas
        const truckObj = truckData?.find(t => String(t.id).trim() === String(item.type_truck).trim());
        const capacity = parseFloat(item.truck_capacity || item.destination_capacity || truckObj?.capacity || 0);
        const planQty = parseFloat(item.plan_qty || 0);
        const maxQty = parseFloat(item.qty || 0);

        // 🔹 Validasi
        if (planQty > maxQty) {
            errors.push(`PO ${item.po_number} (${item.material_code}): Qty rencana (${planQty}) > Qty PO (${maxQty}).`);
            return;
        }
        if (!item.type_truck) {
            errors.push(`PO ${item.po_number} (${item.material_code}): Truck type belum dipilih.`);
            return;
        }
        if (!capacity || isNaN(capacity) || capacity <= 0) {
            errors.push(`PO ${item.po_number} (${item.material_code}): Kapasitas truck tidak valid.`);
            return;
        }

        const totalTruck = Math.ceil(planQty / capacity);

        html += `
            <tr data-index="${i}">
                <td>${i + 1}</td>
                <td>${item.po_number}</td>
                <td>${item.material_code}</td>
                <td>${routeCode}</td>
                <td>${routeDesc}</td>
                <td>${truckObj ? (truckObj.type_truck ?? truckObj.truck_type) : item.type_truck}</td>
                <td>${capacity}</td>
                <td>${planQty}</td>
                <td>${totalTruck}</td>
                <td class="text-center">
                    <button class="btn btn-sm btn-primary"
                        onclick="toggleTripDetails(${i}, '${item.po_number}', '${item.material_code}', ${capacity}, ${totalTruck}, '${routeDesc}')">
                        🔽 Detail
                    </button>
                </td>
            </tr>
            <tr id="trip-details-${i}" class="trip-details-row" style="display: none;">
                <td colspan="10" class="p-2 bg-light" id="trip-details-container-${i}"></td>
            </tr>
        `;
    });

    html += `</tbody></table>`;

    if (errors.length > 0) {
        alert('⚠️ Validasi gagal:\n' + errors.join('\n'));
        return;
    }

    document.getElementById('truck_load_result').innerHTML = html;
}


async function toggleTripDetails(index, poNumber, material, capacity, totalTruck) {
    const row = document.getElementById(`trip-details-${index}`);
    const container = document.getElementById(`trip-details-container-${index}`);

    if (truckData.length === 0 || routeData.length === 0) {
        const loaded = await loadTruckAndRoute();
        if (!loaded) return;
    }

    if (row.style.display === 'none') {
        const poKey = `${poNumber}_${material}`;
        const poData = draftPlans[poKey];
        if (!poData) return;

        const draftQty = parseFloat(poData.plan_qty || 0);
        const availableTrucks = truckData.filter(t => String(t.type_truck).trim() === String(poData.type_truck).trim());
        const routeOptionsFiltered = routeData.filter(rt =>
            String(rt.source).trim() === String(poData.origin).trim() &&
            String(rt.destination).trim() === String(poData.destination).trim()
        );
        const defaultRoute = routeOptionsFiltered[0];

        let tripHTML = `
            <table class="table table-bordered align-middle table-sm mb-0">
                <thead class="table-primary text-center">
                    <tr>
                        <th>Pilih</th>
                        <th>Trip Ke-</th>
                        <th>Kapasitas Muatan (MT)</th>
                        <th>Muatan Kargo (PO Breakdown)</th>
                        <th>Nomor Mobil</th>
                        <th>Nama Supir</th>
                        <th>Rute Pengiriman</th>
                        <th>Outstanding Schedule</th>
                        <th>Tanggal Kirim</th>
                    </tr>
                </thead>
                <tbody>
        `;

        let totalLoaded = 0;

        for (let t = 0; t < totalTruck; t++) {
            const tripNo = t + 1;
            const truck = availableTrucks[t % availableTrucks.length];
            const selectedDriver = truck?.driver_name || '-';

            let tripCapacity = capacity;
            const remaining = draftQty - totalLoaded;
            if (remaining <= capacity) {
                tripCapacity = remaining;
            }
            totalLoaded += tripCapacity;

            const truckOptionsHTML = availableTrucks.map(tr =>
                `<option value="${tr.idtruck}" data-driver="${tr.driver_name || '-'}" ${tr.idtruck === truck.idtruck ? 'selected' : ''}>
                    ${tr.nopol} (${tr.type_truck})
                </option>`
            ).join('');

            const driverOptionsHTML = `<option selected>${selectedDriver}</option>`;

            const routeOptionsHTML = routeOptionsFiltered.map(rt =>
                `<option value="${rt.route}" data-source="${rt.source}" data-destination="${rt.destination}" ${rt.route === defaultRoute?.route ? 'selected' : ''}>
                    ${rt.route} (${rt.route_name})
                </option>`
            ).join('');

            const outstanding = Math.max(draftQty - totalLoaded, 0);

            tripHTML += `
                <tr>
                    <td class="text-center"><input type="checkbox" checked></td>
                    <td>Trip ${tripNo}</td>
                    <td>${tripCapacity.toFixed(2)} MT</td>
                    <td>${material} (PO ${poNumber})</td>
                    <td>
                        <select class="form-select form-select-sm truck-select">
                            ${truckOptionsHTML}
                        </select>
                    </td>
                    <td>
                        <select class="form-select form-select-sm driver-select">
                            ${driverOptionsHTML}
                        </select>
                    </td>
                    <td>
                        <select class="form-select form-select-sm route-select">
                            ${routeOptionsHTML}
                        </select>
                    </td>
                    <td>${outstanding.toFixed(2)} MT</td>
                    <td>
                        <input type="date" class="form-control form-control-sm schedule-date" />
                    </td>
                    <input type="hidden" class="trip-qty" value="${tripCapacity.toFixed(2)}" />
                </tr>
            `;
        }

        tripHTML += `</tbody></table>`;

        tripHTML += `
            <div class="mt-2 text-end">
                <button type="button" class="btn btn-success btn-sm save-trip-btn">
                    💾 Simpan Jadwal
                </button>
            </div>
        `;

        container.innerHTML = tripHTML;
        row.style.display = '';

        // 🟢 Update driver otomatis saat ganti truck
        container.querySelectorAll('.truck-select').forEach(select => {
            select.addEventListener('change', function() {
                const driverName = this.options[this.selectedIndex].dataset.driver || '-';
                const driverSelect = this.closest('tr').querySelector('.driver-select');
                driverSelect.innerHTML = `<option selected>${driverName}</option>`;
                updateTripSummary(container);
            });
        });

        // 🟢 Simpan jadwal ke backend
        const saveButton = container.querySelector('.save-trip-btn');
        console.log("Tombol simpan ditemukan:", saveButton);

        if (saveButton) {
            saveButton.addEventListener('click', async () => {
                console.log("🟢 Tombol Simpan diklik!");

                const selectedTrips = [];
                const poKey = `${poNumber}_${material}`;
                const poData = draftPlans[poKey];
                const draftQty = parseFloat(poData?.plan_qty || 0);

                container.querySelectorAll('table tbody tr').forEach(row => {
                    const checkbox = row.querySelector('input[type="checkbox"]');
                    if (!checkbox || !checkbox.checked) return;

                    const truckSelect = row.querySelector('.truck-select');
                    const routeSelect = row.querySelector('.route-select');
                    const dateInput = row.querySelector('.schedule-date');
                    const qtyInput = row.querySelector('.trip-qty');

                    const truckId = truckSelect?.value || null;
                    const truckText = truckSelect?.options[truckSelect.selectedIndex]?.text || '-';
                    const driverName = truckSelect?.options[truckSelect.selectedIndex]?.dataset.driver || '-';
                    const routeValue = routeSelect?.value || '-';
                    const source = routeSelect?.options[routeSelect.selectedIndex]?.dataset?.source || poData?.origin || null;
                    const destination = routeSelect?.options[routeSelect.selectedIndex]?.dataset?.destination || poData?.destination || null;
                    const tanggal = dateInput?.value || null;
                    const qty = parseFloat(qtyInput?.value || 0);

                    if (!tanggal) return console.warn("⚠️ Skip trip tanpa tanggal:", row);

                    selectedTrips.push({
                        po_number: poNumber,
                        material: material,
                        qty: qty,
                        truck_id: truckId,
                        truck_text: truckText,
                        driver_name: driverName,
                        route: routeValue,
                        source: source,
                        destination: destination,
                        tanggal: tanggal
                    });
                });

                if (selectedTrips.length === 0) {
                    alert("❗Tidak ada trip yang dipilih untuk disimpan.");
                    return;
                }

                const payload = {
                    trips: selectedTrips,
                    capacity: capacity,
                    po_number: poNumber
                };

                console.log("📦 Data yang dikirim ke controller:", payload);
                console.table(selectedTrips);

                try {
                    const response = await fetch('/optimization/save-schedule', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify(payload)
                    });

                    const result = await response.json();

                    if (response.ok) {
                        alert("✅ Jadwal pengiriman berhasil disimpan!");
                        console.log("🟢 Hasil simpan:", result);

                        // 🔥 Hapus hanya baris trip yang disimpan
                        container.querySelectorAll('table tbody tr').forEach(row => {
                            const checkbox = row.querySelector('input[type="checkbox"]');
                            if (checkbox && checkbox.checked) {
                                row.remove();
                            }
                        });

                        // Jika semua trip dihapus (artinya semua sudah disimpan), baru hilangkan PO-nya
                        const sisaTrip = container.querySelectorAll('table tbody tr').length;
                        if (sisaTrip === 0) {
                            const poRow = document.querySelector(`#trip-details-${index}`);
                            const mainRow = document.querySelector(`#main-po-row-${index}`);
                            if (poRow) poRow.remove();
                            if (mainRow) mainRow.remove();
                        }

                        console.log(`🧹 ${sisaTrip === 0 ? 'Semua trip disimpan, PO dihapus' : 'Sebagian trip disimpan, PO tetap tampil'}`);
                    } else {
                        alert(`❌ Gagal menyimpan jadwal:\n${result.message}`);
                        console.error("🔴 Respon error:", result);
                    }

                } catch (error) {
                    console.error("💥 Error simpan jadwal:", error);
                    alert("❌ Terjadi kesalahan saat menyimpan jadwal.");
                }
            });
        } else {
            console.warn("⚠️ Tombol simpan tidak ditemukan di container ini!");
        }

        updateTripSummary(container);

    } else {
        row.style.display = 'none';
        container.innerHTML = '';
    }
}


// Fungsi untuk update summary trips per nomor mobil
function updateTripSummary(container) {
    const truckTripCount = {};
    container.querySelectorAll('.truck-select').forEach(select => {
        const truckName = select.options[select.selectedIndex].text;
        if (!truckTripCount[truckName]) truckTripCount[truckName] = 0;
        truckTripCount[truckName]++;
    });

    let summaryHTML = `<h6 class="mt-3">📊 Summary Trips per Truck:</h6><ul>`;
    for (const [truckName, count] of Object.entries(truckTripCount)) {
        summaryHTML += `<li>${truckName}: ${count} trip(s)</li>`;
    }
    summaryHTML += `</ul>`;

    // Hapus summary lama jika ada
    const oldSummary = container.querySelector('.trip-summary');
    if (oldSummary) oldSummary.remove();

    // Tambahkan baru
    const div = document.createElement('div');
    div.classList.add('trip-summary');
    div.innerHTML = summaryHTML;
    container.appendChild(div);
}



</script>
@endpush




