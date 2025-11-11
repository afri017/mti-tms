@extends('layout.main')

@section('content')
<section class="content">
    <div class="container-fluid">
        {{-- ✅ ALERT --}}
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @elseif (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        {{-- 🔍 FORM PENCARIAN --}}
        <div class="card card-default">
            <div class="card-header">
                <h3 class="card-title">Shipment Realization</h3>
            </div>
            <div class="card-body">
                <form id="searchForm">
                    <div class="row g-2">
                        <div class="col-md-3">
                            <input type="text" id="no_shipment" name="no_shipment" class="form-control"
                                   placeholder="No Shipment">
                        </div>
                        <div class="col-md-3">
                            <input type="text" id="no_mobil" name="no_mobil" class="form-control"
                                   placeholder="No Mobil">
                        </div>
                        <div class="col-md-3">
                            <input type="text" id="nama_supir" name="nama_supir" class="form-control"
                                   placeholder="Nama Supir">
                        </div>
                        <div class="col-md-2">
                            <select id="status" name="status" class="form-control">
                                <option value="">Semua Status</option>
                                <option value="open">Open</option>
                                <option value="inprogress">In Progress</option>
                                <option value="completed">Complete</option>
                            </select>
                        </div>
                        <div class="col-md-1">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- 📋 HASIL PENCARIAN --}}
        <div id="resultSection" style="display:none;">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h3 class="card-title">Daftar Shipment</h3>
                </div>
                <div class="card-body">
                    <table class="table table-bordered table-striped" id="shipmentTable">
                        <thead class="table-light">
                            <tr>
                                <th>No</th>
                                <th>Delivery Date</th>
                                <th>No Shipment</th>
                                <th>Route</th>
                                <th>No Mobil</th>
                                <th>Nama Supir</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
document.getElementById('searchForm').addEventListener('submit', async function(e) {
    e.preventDefault();

    const no_shipment = document.getElementById('no_shipment').value.trim();
    const no_mobil = document.getElementById('no_mobil').value.trim();
    const nama_supir = document.getElementById('nama_supir').value.trim();
    const status = document.getElementById('status').value;

    // Tambah timestamp biar gak cache hasil lama
    const params = new URLSearchParams({ no_shipment, no_mobil, nama_supir, status, _: Date.now() });
    const response = await fetch(`/do/search-list?${params.toString()}`, { cache: 'no-store' });
    const data = await response.json();

    if (!data.success || !data.shipments.length) {
        alert(data.message || 'Shipment tidak ditemukan');
        document.getElementById('resultSection').style.display = 'none';
        return;
    }

    document.getElementById('resultSection').style.display = 'block';

    // Hancurkan DataTable lama sebelum isi ulang
    if ($.fn.DataTable.isDataTable('#shipmentTable')) {
        $('#shipmentTable').DataTable().clear().destroy();
    }

    const tbody = document.querySelector('#shipmentTable tbody');
    tbody.innerHTML = '';

    data.shipments.forEach((s, i) => {
        const doData = s.doship && s.doship.length > 0 ? s.doship[0] : null;
        const isCheckedIn = !!doData?.checkin;
        const isCheckedOut = !!doData?.checkout;
        const hasInProgress = data.inprogressTrucks.includes(s.truck?.nopol);

        const disableCheckIn = isCheckedOut || hasInProgress;
        const disableCheckOut = !isCheckedIn;

        // 🌈 Warna tombol dinamis berdasarkan status
        let checkinBtnClass = 'btn-success'; // default hijau
        let checkoutBtnClass = 'btn-warning'; // default kuning

        if (isCheckedIn && !isCheckedOut) {
            checkinBtnClass = 'btn-info'; // biru muda kalau sudah check-in tapi belum checkout
        } else if (isCheckedOut) {
            checkinBtnClass = 'btn-secondary'; // abu kalau sudah selesai
            checkoutBtnClass = 'btn-secondary';
        }

        const checkinBtn = `<button
            class="btn ${checkinBtnClass} btn-sm"
            ${disableCheckIn ? 'disabled' : ''}
            onclick="window.location.href='/do/checkin/${s.noshipment}'">
            <i class='fas fa-sign-in-alt'></i> ${isCheckedIn ? 'Sudah Check-In' : 'Check-In'}
        </button>`;

        const checkoutBtn = `<button
            class="btn ${checkoutBtnClass} btn-sm"
            ${disableCheckOut ? 'disabled' : ''}
            onclick="window.location.href='/do/checkout/${s.noshipment}'">
            <i class='fas fa-sign-out-alt'></i> ${isCheckedOut ? 'Sudah Check-Out' : 'Check-Out'}
        </button>`;

        // 🩵 Highlight warna baris tabel sesuai status DO
        let rowClass = '';
        if (isCheckedIn && !isCheckedOut) {
            rowClass = 'table-info'; // biru muda: sedang di lokasi
        } else if (isCheckedOut) {
            rowClass = 'table-success'; // hijau: sudah selesai
        }

        const row = `
            <tr>
                <td>${i + 1}</td>
                <td>${s.delivery_date ?? '-'}</td>
                <td>${s.noshipment ?? '-'}</td>
                <td>${s.route_data?.route_name ?? '-'}</td>
                <td>${s.truck?.nopol ?? '-'}</td>
                <td>${s.driver?.name ?? '-'}</td>
                <td>${s.status ?? '-'}</td>
                <td>${checkinBtn} ${checkoutBtn}</td>
            </tr>`;
        tbody.insertAdjacentHTML('beforeend', row);
    });

    // ✅ Reinit DataTable setelah isi ulang
    $('#shipmentTable').DataTable({
        pageLength: 10,
        order: [[1, 'desc']], // urut default by tanggal
        responsive: true,
        lengthChange: false,
        autoWidth: false,
        searching: false, // karena filter sudah manual via form
        language: {
            paginate: { previous: "<", next: ">" },
            info: "Menampilkan _START_ - _END_ dari _TOTAL_ shipment",
            infoEmpty: "Tidak ada shipment",
            emptyTable: "Belum ada data"
        }
    });
});
</script>
@endpush

