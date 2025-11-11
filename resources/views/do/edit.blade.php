@extends('layout.main')

@section('content')
<section class="content">
    <div class="container-fluid">
        {{-- ✅ ALERT SUCCESS / ERROR --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show position-relative" role="alert">
                <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close position-absolute top-0 end-0 mt-2 me-2"
                        data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @elseif (session('error'))
            <div class="alert alert-danger alert-dismissible fade show position-relative" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
                <button type="button" class="btn-close position-absolute top-0 end-0 mt-2 me-2"
                        data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- 🔍 Pencarian Shipment --}}
        <div class="card card-default">
            <div class="card-header">
                <h3 class="card-title">Form Removal Delivery Order</h3>
            </div>
            <div class="card-body">
                <form id="searchForm">
                    <div class="input-group">
                        <input type="text" id="noshipment" name="noshipment" class="form-control" placeholder="Masukkan No Shipment...">
                        <span class="input-group-append">
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </span>
                    </div>
                </form>
            </div>
        </div>

        {{-- 🚚 Detail Shipment dan DO --}}
        <div id="shipmentDetail" style="display:none;">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h3 class="card-title">Detail Shipment</h3>
                </div>
                <div class="card-body">
                    <form id="editShipmentForm" method="POST" action="{{ route('do.update') }}">
                        @csrf

                        {{-- Detail Shipment --}}
                        <div class="row">
                            <div class="col-md-3">
                                <label>No Shipment</label>
                                <input type="text" id="noshipment_display" class="form-control" readonly>
                                <input type="hidden" id="noshipment_hidden" name="noshipment">
                            </div>
                            <div class="col-md-3">
                                <label>No Seal</label>
                                <input type="text" id="noseal" name="noseal" class="form-control">
                            </div>
                            <div class="col-md-3">
                                <label>No PO</label>
                                <input type="text" id="nopo" name="nopo" class="form-control" readonly>
                            </div>
                            <div class="col-md-3">
                                <label>Delivery Date</label>
                                <input type="date" id="delivery_date" name="delivery_date" class="form-control" readonly>
                            </div>
                            <div class="col-md-4 " hidden>
                                <label>No DO</label>
                                <input type="text" id="nodo" name="nodo" class="form-control" readonly>
                            </div>
                        </div>

                        <div class="row mt-2">
                            <div class="col-md-4">
                                <label>No Polisi (Truck)</label>
                                <input type="text" id="nopol_display" class="form-control" readonly>
                            </div>
                            <div class="col-md-4">
                                <label>Nama Supir</label>
                                <input type="text" id="driver_display" class="form-control" readonly>
                            </div>
                            <div class="col-md-4">
                                <label>Customer</label>
                                <input type="text" id="customer_display" class="form-control" readonly>
                            </div>
                        </div>

                        <div class="row mt-2">
                            <div class="col-md-3">
                                <label>Tara Weight (kg)</label>
                                <input type="number" id="tara_weight" name="tara_weight" class="form-control">
                            </div>
                            <div class="col-md-3">
                                <label>Gross Weight (kg)</label>
                                <input type="number" id="gross_weight" name="gross_weight" class="form-control">
                            </div>
                            <div class="col-md-3">
                                <label>Start Loading</label>
                                <input type="time" id="start_loading_display" class="form-control">
                                <input type="hidden" id="start_loading" name="start_loading">
                            </div>
                            <div class="col-md-3">
                                <label>Finish Loading</label>
                                <input type="time" id="finish_loading_display" class="form-control">
                                <input type="hidden" id="finish_loading" name="finish_loading">
                            </div>
                        </div>

                        {{-- Detail DO --}}
                        <div class="card mt-4">
                            <div class="card-header bg-gray text-white">
                                <h5 class="card-title">Detail Delivery Order Items</h5>
                            </div>
                            <table class="table table-bordered" id="doTable">
                                <thead class="table-light">
                                    <tr>
                                        <th>No DO</th>
                                        <th>Material</th>
                                        <th>Desc</th>
                                        <th>Qty Plan</th>
                                        <th>Qty Actual</th>
                                        <th>UOM</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>

                        <div class="text-end mt-3">
                            <button type="submit" class="btn btn-primary">POST</button>
                            <button type="button" id="btnSuratJalan" class="btn btn-success" style="display:none;">
                                <i class="fas fa-print"></i> Surat Jalan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
</section>
<!-- Modal Cetak Surat Jalan -->
<div class="modal fade" id="modalSuratJalan" tabindex="-1" aria-labelledby="modalSuratJalanLabel" aria-hidden="true">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header bg-success text-white">
        <h5 class="modal-title" id="modalSuratJalanLabel">Form Cetak Surat Jalan</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="formSuratJalan" target="_blank" method="GET" action="{{ route('do.suratjalan') }}">
          <!-- hidden inputs -->
          <input type="hidden" id="sj_noshipment" name="noshipment">
          <input type="hidden" id="sj_nodo" name="nodo">
          <input type="hidden" id="sj_nopol" name="nopol">
          <input type="hidden" id="sj_driver" name="driver">

            <div class="col-md-12">
                <div class="row mb-3">
                    <textarea name="remarks" class="form-control" rows="3" placeholder="Tambahkan keterangan tambahan..."></textarea>
                    <label>Keterangan Tambahan</label>
                </div>
            </div>
            <div class="text-end">
                <button type="button" class="btn btn-success" id="btnCetakSuratJalan">
                    <i class="fas fa-print"></i> Cetak Surat Jalan
                </button>
            </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection


@push('scripts')
<script>
document.getElementById('searchForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    const noshipment = document.getElementById('noshipment').value.trim();
    if (!noshipment) return alert('Masukkan No Shipment');

    const response = await fetch(`/do/search?noshipment=${noshipment}`);
    const data = await response.json();

    if (!data.success) {
        alert(data.message || 'Shipment tidak ditemukan');
        return;
    }

    const shipment = data.shipment;
    const deliveryOrders = data.deliveryOrders;

    if (!deliveryOrders.length) {
        alert('Tidak ada DO terkait shipment ini.');
        return;
    }

    const firstDo = deliveryOrders[0];

    // tampilkan form detail
    document.getElementById('shipmentDetail').style.display = 'block';

    document.getElementById('noshipment_display').value = shipment.noshipment;
    document.getElementById('noshipment_hidden').value = shipment.noshipment;
    document.getElementById('delivery_date').value = shipment.delivery_date;
    document.getElementById('noseal').value = shipment.noseal ?? '';
    document.getElementById('nodo').value = firstDo.nodo ?? '';
    document.getElementById('nopo').value = firstDo.poheader?.nopo ?? '-';
    document.getElementById('tara_weight').value = firstDo.tara_weight ?? '';
    document.getElementById('gross_weight').value = firstDo.gross_weight ?? '';
    document.getElementById('start_loading_display').value = firstDo.start_loading?.substring(11,16) ?? '';
    document.getElementById('finish_loading_display').value = firstDo.end_loading?.substring(11,16) ?? '';
    // simpan ke field hidden (untuk dikirim ke backend)
    document.getElementById('start_loading').value = firstDo.start_loading ?? '';
    document.getElementById('finish_loading').value = firstDo.end_loading ?? '';

    // tampilkan data truck & driver
    document.getElementById('nopol_display').value = shipment.truck?.nopol ?? '-';
    document.getElementById('driver_display').value = shipment.driver?.name ?? '-';
    document.getElementById('customer_display').value = firstDo.poheader.customer?.customer_name ?? '-';

    const allQtyFilled = firstDo.items.every(i => i.qty_act && parseFloat(i.qty_act) > 0);
    const taraOk = firstDo.tara_weight && parseFloat(firstDo.tara_weight) > 0;
    const grossOk = firstDo.gross_weight && parseFloat(firstDo.gross_weight) > 0 && parseFloat(firstDo.gross_weight) > parseFloat(firstDo.tara_weight || 0);
    const startOk = !!firstDo.start_loading;
    const finishOk = !!firstDo.end_loading;

    if (allQtyFilled && taraOk && grossOk && startOk && finishOk) {
        document.getElementById('btnSuratJalan').style.display = 'inline-block';
    } else {
        document.getElementById('btnSuratJalan').style.display = 'none';
    }


    // isi tabel item
    const tbody = document.querySelector('#doTable tbody');
    tbody.innerHTML = '';
    firstDo.items.forEach(item => {
        const row = `
            <tr>
                <td>${item.nodo}</td>
                <td>${item.material_code}</td>
                <td>${item.material.material_desc}</td>
                <td>${item.qty_plan}</td>
                <td>
                    <input type="number" name="qty_act[${item.material_code}]"
                        value="${item.qty_act ?? ''}" class="form-control form-control-sm qty-act" required min="0">
                </td>
                <td>${item.uom}</td>
            </tr>`;
        tbody.insertAdjacentHTML('beforeend', row);
    });
});

document.getElementById('editShipmentForm').addEventListener('submit', function(e) {
    const tara = parseFloat(document.getElementById('tara_weight').value);
    const gross = parseFloat(document.getElementById('gross_weight').value);
    const start = document.getElementById('start_loading_display').value;
    const finish = document.getElementById('finish_loading_display').value;


    // === Validasi wajib isi waktu ===
    if (!start || !finish) {
        e.preventDefault();
        return alert('Start loading dan Finish loading wajib diisi.');
    }

    // validasi berat
    if (isNaN(tara) || tara <= 0) {
        e.preventDefault();
        return alert('Tara weight wajib diisi dan harus lebih dari 0.');
    }

    if (isNaN(gross) || gross <= tara) {
        e.preventDefault();
        return alert('Gross weight harus lebih besar dari tara weight.');
    }

    // validasi waktu
    if (start > finish) {
        e.preventDefault();
        return alert('Start loading tidak boleh lebih besar dari finish loading.');
    }

    // validasi qty actual
    const qtyInputs = document.querySelectorAll('.qty-act');
    for (let input of qtyInputs) {
        if (input.value.trim() === '' || parseFloat(input.value) <= 0) {
            e.preventDefault();
            return alert('Qty actual wajib diisi dan lebih dari 0 untuk semua item.');
        }
    }

    // ubah waktu ke format full datetime (jika DB kolom DATETIME)
    const today = new Date().toISOString().split('T')[0];
    document.getElementById('start_loading').value = `${today} ${start}:00`;
    document.getElementById('finish_loading').value = `${today} ${finish}:00`;

    // ✅ jika semua valid, tampilkan tombol surat jalan
    document.getElementById('btnSuratJalan').style.display = 'inline-block';
});

document.getElementById('btnSuratJalan').addEventListener('click', function() {
    // isi data otomatis ke modal (hidden fields)
    document.getElementById('sj_noshipment').value = document.getElementById('noshipment_display').value;
    document.getElementById('sj_nodo').value = document.getElementById('nodo').value;
    document.getElementById('sj_nopol').value = document.getElementById('nopol_display').value;
    document.getElementById('sj_driver').value = document.getElementById('driver_display').value;

    // tampilkan modal
    const modal = new bootstrap.Modal(document.getElementById('modalSuratJalan'));
    modal.show();
});

document.getElementById('btnCetakSuratJalan').addEventListener('click', function() {
    const form = document.getElementById('formSuratJalan');
    const params = new URLSearchParams(new FormData(form)).toString();
    const url = form.action + '?' + params;

    // buka di tab baru
    window.open(url, '_blank');

    // tutup modal setelah membuka tab baru
    const modalEl = document.getElementById('modalSuratJalan');
    const modalInstance = bootstrap.Modal.getInstance(modalEl);
    modalInstance.hide();
});

</script>
<!-- Bootstrap JS (versi 5 ke atas) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@endpush
