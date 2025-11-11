@extends('layout.main')

@section('content')
<section class="content" style="padding-bottom: 100px;">
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
                <h3 class="card-title">Form Receipt Delivery Order</h3>
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
                    <form id="editShipmentForm" method="POST" action="{{ route('do.receipt.store') }}" enctype="multipart/form-data">
                        @csrf

                        {{-- Detail Shipment --}}
                        <div class="row">
                            <div class="col-md-4">
                                <label>No Shipment</label>
                                <input type="text" id="noshipment_display" class="form-control" readonly>
                                <input type="hidden" id="noshipment_hidden" name="noshipment">
                            </div>
                            <div class="col-md-4">
                                <label>No Seal</label>
                                <input type="text" id="noseal" name="noseal" class="form-control" readonly>
                            </div>
                            <div class="col-md-4">
                                <label>No PO</label>
                                <input type="text" id="nopo" name="nopo" class="form-control" readonly>
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
                            <div class="col-md-4">
                                <label>Tara Weight (kg)</label>
                                <input type="number" id="tara_weight" name="tara_weight" class="form-control" readonly>
                            </div>
                            <div class="col-md-4">
                                <label>Gross Weight (kg)</label>
                                <input type="number" id="gross_weight" name="gross_weight" class="form-control" readonly>
                            </div>
                            <div class="col-md-3" hidden>
                                <label>Start Loading</label>
                                <input type="time" id="start_loading_display" class="form-control" readonly>
                                <input type="hidden" id="start_loading" name="start_loading">
                            </div>
                            <div class="col-md-3" hidden>
                                <label>Finish Loading</label>
                                <input type="time" id="finish_loading_display" class="form-control" readonly>
                                <input type="hidden" id="finish_loading" name="finish_loading">
                            </div>
                            <div class="col-md-4">
                                <label>Receipt Date</label>
                                <input type="date" id="receipt_date" name="receipt_date" class="form-control">
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
                                        <th>Qty</th>
                                        <th>Qty Receipt</th>
                                        <th>UOM</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>

                        {{-- 📎 Upload Attachment --}}
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <label>Upload Attachment (bisa lebih dari satu file)</label>
                                <input type="file" name="attachments[]" id="attachments" class="form-control" multiple accept=".jpg,.jpeg,.png,.pdf">
                                <small class="text-muted">Format: JPG, PNG, PDF (maks 5MB per file)</small>

                                <ul id="fileList" class="mt-2" style="list-style:none; padding-left:0;"></ul>
                            </div>
                        </div>

                        <div class="text-end mt-3">
                            <button type="submit" class="btn btn-primary">POST</button>
                        </div>
                    </form>
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

    // tampilkan file attachment yang sudah ada
    const fileList = document.getElementById('fileList');
    fileList.innerHTML = ''; // reset list lama

    if (firstDo.attachments && firstDo.attachments.length > 0) {
        firstDo.attachments.forEach((att, index) => {
            const li = document.createElement('li');
            li.classList.add('mb-3', 'border', 'p-2', 'rounded');
            li.style.background = '#f8f9fa';

            const name = document.createElement('div');
            name.innerHTML = `<strong>${index + 1}. ${att.filename}</strong>`;
            li.appendChild(name);

            // ✅ Dapatkan ekstensi file jika filetype tidak ada
            const filetype = att.filetype || (att.filename ? att.filename.split('.').pop().toLowerCase() : '');

            // ✅ Preview gambar
            if (filetype.startsWith('image') || ['jpg', 'jpeg', 'png'].includes(filetype)) {
                const img = document.createElement('img');
                img.src = att.url; // URL file di storage
                img.style.height = '100px';
                img.style.marginTop = '5px';
                img.style.borderRadius = '8px';
                img.style.objectFit = 'cover';
                li.appendChild(img);
            }

            // ✅ Preview PDF
            else if (filetype === 'pdf' || filetype === 'application/pdf') {
                const embed = document.createElement('embed');
                embed.src = att.url;
                embed.type = 'application/pdf';
                embed.width = '100%';
                embed.height = '120';
                embed.style.marginTop = '5px';
                li.appendChild(embed);
            }

            // ✅ Tombol download
            const downloadBtn = document.createElement('a');
            downloadBtn.href = att.url;
            downloadBtn.target = '_blank';
            downloadBtn.textContent = 'Lihat / Unduh';
            downloadBtn.classList.add('btn', 'btn-sm', 'btn-info', 'mt-2', 'me-2');
            li.appendChild(downloadBtn);

            fileList.appendChild(li);
        });
    } else {
        const noFile = document.createElement('li');
        noFile.textContent = 'Belum ada attachment';
        noFile.classList.add('text-muted');
        fileList.appendChild(noFile);
    }

    // tampilkan form detail
    // 🧩 Cek apakah semua qty_act null
    const allQtyNull = firstDo.items.every(i => !i.qty_act || parseFloat(i.qty_act) === 0);

    // Jika belum Good Issue
    if (allQtyNull) {
        // sembunyikan form shipment
        document.getElementById('shipmentDetail').style.display = 'none';

        // hapus card lama kalau ada
        const oldCard = document.getElementById('notGoodIssueCard');
        if (oldCard) oldCard.remove();

        // buat card baru
        const warningCard = document.createElement('div');
        warningCard.classList.add('card', 'border', 'border-warning', 'mt-3');
        warningCard.id = 'notGoodIssueCard';
        warningCard.innerHTML = `
            <div class="card-header bg-warning text-dark fw-bold d-flex justify-content-between align-items-center">
                <span>Shipment Belum di Good Issue</span>
                <button id="removeWarningCard" type="button" class="btn-close position-absolute" style="top: 8px; right: 8px;" aria-label="Close"></button>
            </div>
            <div class="card-body">
                <p class="mb-0">
                    Shipment <strong>${shipment.noshipment}</strong> belum dilakukan <em>Good Issue</em>.<br>
                    Silakan selesaikan proses <strong>Good Issue</strong> terlebih dahulu di sistem sebelum melakukan Receipt.
                </p>
            </div>
        `;

        // tampilkan di bawah form pencarian shipment
        document.querySelector('.container-fluid').appendChild(warningCard);

        // event listener tombol hapus card
        document.getElementById('removeWarningCard').addEventListener('click', () => {
            warningCard.remove();
        });

        return; // stop eksekusi agar form tidak muncul
    }

    // ✅ Kalau sudah ada qty_act → tampilkan form detail
    document.getElementById('shipmentDetail').style.display = 'block';


    document.getElementById('noshipment_display').value = shipment.noshipment;
    document.getElementById('receipt_date').value = firstDo.receipt_date ? firstDo.receipt_date.substring(0, 10) : '';
    document.getElementById('noshipment_hidden').value = shipment.noshipment;
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

    // isi tabel item
    const tbody = document.querySelector('#doTable tbody');
    tbody.innerHTML = '';
    firstDo.items.forEach(item => {
        const row = `
            <tr>
                <td>${item.nodo}<input type="hidden" name="nodo[]" value="${item.nodo}"></td>
                <td>${item.material_code}</td>
                <td>${item.material.material_desc}</td>
                <td>${item.qty_act}</td>
                <td>
                    <input type="number" name="qty_act[${item.material_code}]"
                        value="${item.qty_receipt ?? ''}" class="form-control form-control-sm qty-act" required min="0">
                    <input type="number" name="qty_reject[${item.material_code}]"
                        value="${item.qty_act - (item.qty_receipt ?? 0)}"
                        class="form-control form-control-sm qty-reject" hidden>
                </td>
                <td>${item.uom}</td>
            </tr>`;
        tbody.insertAdjacentHTML('beforeend', row);
    });

    // Hitung qty_reject otomatis saat user ubah qty_receipt
    tbody.querySelectorAll('.qty-act').forEach(input => {
        input.addEventListener('input', function() {
            const qtyAct = parseFloat(this.dataset.qtyAct) || 0;
            const qtyReceipt = parseFloat(this.value) || 0;
            const qtyReject = Math.max(qtyAct - qtyReceipt, 0);s

            // Temukan input qty_reject di baris yang sama
            const row = this.closest('tr');
            const rejectInput = row.querySelector('.qty-reject');
            rejectInput.value = qtyReject;
        });
    });
});

document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('editShipmentForm');
    const attachmentInput = document.getElementById('attachments');
    const previewContainer = document.getElementById('attachmentPreview');

    // === 🖼️ Preview File Upload ===
    if (attachmentInput) {
        attachmentInput.addEventListener('change', function() {
            previewContainer.innerHTML = ''; // bersihkan preview sebelumnya

            Array.from(this.files).forEach((file, index) => {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const card = document.createElement('div');
                    card.className = 'card mb-2 position-relative';
                    card.style.width = '150px';

                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.className = 'card-img-top rounded';
                    img.style.height = '100px';
                    img.style.objectFit = 'cover';

                    const removeBtn = document.createElement('button');
                    removeBtn.className = 'btn btn-sm btn-danger position-absolute top-0 end-0';
                    removeBtn.innerHTML = '&times;';
                    removeBtn.type = 'button';
                    removeBtn.addEventListener('click', () => {
                        const dataTransfer = new DataTransfer();
                        Array.from(attachmentInput.files)
                            .forEach((f, i) => { if (i !== index) dataTransfer.items.add(f); });
                        attachmentInput.files = dataTransfer.files;
                        card.remove();
                    });

                    card.appendChild(img);
                    card.appendChild(removeBtn);
                    previewContainer.appendChild(card);
                };
                reader.readAsDataURL(file);
            });
        });
    }

    // === ✅ Validasi & Submit Form ===
    form.addEventListener('submit', function(e) {
        const tara = parseFloat(document.getElementById('tara_weight').value);
        const gross = parseFloat(document.getElementById('gross_weight').value);
        const start = document.getElementById('start_loading_display').value;
        const finish = document.getElementById('finish_loading_display').value;

        if (!start || !finish) {
            e.preventDefault();
            return alert('Start loading dan Finish loading wajib diisi.');
        }

        if (isNaN(tara) || tara <= 0) {
            e.preventDefault();
            return alert('Tara weight wajib diisi dan harus lebih dari 0.');
        }

        if (isNaN(gross) || gross <= tara) {
            e.preventDefault();
            return alert('Gross weight harus lebih besar dari tara weight.');
        }

        if (start > finish) {
            e.preventDefault();
            return alert('Start loading tidak boleh lebih besar dari finish loading.');
        }

        const qtyInputs = document.querySelectorAll('.qty-act');
        for (let input of qtyInputs) {
            if (input.value.trim() === '' || parseFloat(input.value) <= 0) {
                e.preventDefault();
                return alert('Qty actual wajib diisi dan lebih dari 0 untuk semua item.');
            }
        }

        // tambahkan tanggal hari ini untuk jam loading
        const today = new Date().toISOString().split('T')[0];
        document.getElementById('start_loading').value = `${today} ${start}:00`;
        document.getElementById('finish_loading').value = `${today} ${finish}:00`;

        // ✅ Tidak ada preventDefault di sini — biarkan submit normal
    });
});

// 🗂 Preview file upload
let allFiles = []; // simpan semua file yang dipilih

document.getElementById('attachments').addEventListener('change', function(event) {
    const newFiles = Array.from(event.target.files);
    allFiles = allFiles.concat(newFiles); // gabungkan file lama + baru

    const fileList = document.getElementById('fileList');
    fileList.innerHTML = ''; // refresh tampilan

    allFiles.forEach((file, index) => {
        const li = document.createElement('li');
        li.classList.add('mb-3', 'border', 'p-2', 'rounded');
        li.style.background = '#f8f9fa';

        const name = document.createElement('div');
        name.innerHTML = `<strong>${index + 1}. ${file.name}</strong>`;
        li.appendChild(name);

        // Preview gambar
        if (file.type.startsWith('image/')) {
            const img = document.createElement('img');
            img.style.height = '100px';
            img.style.marginTop = '5px';
            img.style.borderRadius = '8px';
            img.style.objectFit = 'cover';
            const reader = new FileReader();
            reader.onload = e => img.src = e.target.result;
            reader.readAsDataURL(file);
            li.appendChild(img);
        }

        // Preview PDF
        else if (file.type === 'application/pdf') {
            const embed = document.createElement('embed');
            embed.src = URL.createObjectURL(file);
            embed.type = 'application/pdf';
            embed.width = '100%';
            embed.height = '120';
            embed.style.marginTop = '5px';
            li.appendChild(embed);
        }

        // Tombol hapus file dari daftar
        const removeBtn = document.createElement('button');
        removeBtn.textContent = 'Hapus';
        removeBtn.classList.add('btn', 'btn-sm', 'btn-danger', 'mt-2');
        removeBtn.onclick = function() {
            allFiles.splice(index, 1); // hapus dari array
            renderList(); // refresh ulang daftar
        };
        li.appendChild(removeBtn);

        fileList.appendChild(li);
    });

    // reset input supaya bisa pilih file yang sama lagi
});

function renderList() {
    const fileList = document.getElementById('fileList');
    fileList.innerHTML = '';
    allFiles.forEach((file, index) => {
        const li = document.createElement('li');
        li.textContent = `${index + 1}. ${file.name}`;
        fileList.appendChild(li);
    });
}
</script>
<!-- Bootstrap JS (versi 5 ke atas) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@endpush
