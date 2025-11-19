@extends('layout.main')

@section('content')
<section class="content">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Report Outstanding PO Customer</h3>

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

            {{-- FILTER TANGGAL --}}
            <div class="row mb-3">
                <div class="col-md-3">
                    <label><b>Start Date</b></label>
                    <input type="date" id="start_date" class="form-control" value="{{ date('Y-m-01') }}">
                </div>

                <div class="col-md-3">
                    <label><b>End Date</b></label>
                    <input type="date" id="end_date" class="form-control" value="{{ date('Y-m-d') }}">
                </div>

                <div class="col-md-2 d-flex align-items-end">
                    <button id="btnView" class="btn btn-primary btn-block">
                        <i class="fas fa-filter"></i> View
                    </button>
                </div>
            </div>

            <hr>

            {{-- CHART --}}
            <div class="row">
                <div class="col-md-12">
                    <canvas id="poChart" width="100%" height="40"></canvas>
                    <div id="chartDetail"
                            class="mt-3 p-3 border rounded bg-light"
                            style="display:none;">
                    </div>
                </div>
            </div>

            <hr>
            <div class="mb-3">
                <a href="#" class="btn btn-success" id="btnExport">
                    <i class="fas fa-file-excel"></i> Export Excel
                </a>
            </div>

            {{-- TABLE OUTSTANDING (HEADER + DETAIL) --}}
            <div class="table-responsive mt-3">
                <table class="table table-bordered table-striped" id="tbl-po">
                    <thead class="bg-secondary text-white">
                        <tr>
                            <th></th> {{-- expand --}}
                            <th>No</th>
                            <th>PO Number</th>
                            <th>Customer</th>
                            <th>PO Date</th>
                            <th>Qty Order</th>
                            <th>Qty Delivery</th>
                            <th>Outstanding</th>
                        </tr>
                    </thead>
                    <tbody id="po-body">
                        <tr>
                            <td colspan="8" class="text-center">Loading...</td>
                        </tr>
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</section>



@endsection

@push('scripts')
{{-- Chart.js --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.getElementById('btnView').addEventListener('click', function () {
    loadOutstandingData();
});

// ======================= LOAD DATA ===========================
function updateExportUrl() {
    let start = $('#start_date').val();
    let end   = $('#end_date').val();

    let exportUrl = `/report/po-outstanding/export?start_date=${start}&end_date=${end}`;
    $('#btnExport').attr('href', exportUrl);
}

// update setiap kali tanggal berubah
$('#start_date, #end_date').on('change', updateExportUrl);

// update saat tombol filter ditekan (jika ada)
$('#btnFilter').on('click', function() {
    updateExportUrl();
});

function loadOutstandingData() {

    let start = $('#start_date').val();
    let end   = $('#end_date').val();

    $('#po-body').html(`<tr><td colspan="8" class="text-center">Loading...</td></tr>`);

    $.ajax({
        url: "{{ url('/report/po-outstanding/data') }}",
        method: "GET",
        data: {
            start_date: start,
            end_date: end
        },
        success: function(res) {

            let rows = '';

            if(res.data.length === 0) {
                rows = `<tr><td colspan="8" class="text-center">Tidak ada data</td></tr>`;
            }
            else
            {
                res.data.forEach((po, index) => {

                    let rowId = "items-" + po.po_number.replace(/\s+/g, '-');

                    // HEADER ROW
                    rows += `
                        <tr>
                            <td>
                                <button class="btn btn-sm btn-info toggle-items" data-target="#${rowId}">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </td>
                            <td>${index + 1}</td>
                            <td>${po.po_number}</td>
                            <td>${po.customer_name}</td>
                            <td>${po.po_date}</td>
                            <td>${po.qty_order}</td>
                            <td>${po.qty_received}</td>
                            <td><b>${po.outstanding}</b></td>
                        </tr>

                        <!-- DETAIL ROW -->
                        <tr id="${rowId}" class="collapse">
                            <td colspan="8">
                                <table class="table table-sm table-bordered mb-0">
                                    <thead class="bg-light">
                                        <tr>
                                            <th>Material</th>
                                            <th>Qty Order</th>
                                            <th>Qty Delivered</th>
                                            <th>Outstanding</th>
                                            <th>UOM</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                `;

                    // DETAIL ITEMS
                    po.items.forEach(item => {
                        rows += `
                            <tr>
                                <td>${item.material}</td>
                                <td>${item.qty_order}</td>
                                <td>${item.qty_delivered}</td>
                                <td>${item.qty_outstanding}</td>
                                <td>${item.uom}</td>
                            </tr>
                        `;
                    });

                    rows += `
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                    `;
                });
            }

            $('#po-body').html(rows);

            attachToggleEvent();
        }
    });
}

// ======================= EXPAND DETAIL ===========================
function attachToggleEvent() {
    document.querySelectorAll('.toggle-items').forEach(button => {
        button.addEventListener('click', function() {
            const target = document.querySelector(this.dataset.target);
            target.classList.toggle('show');

            const icon = this.querySelector('i');
            if(target.classList.contains('show')){
                icon.classList.remove('fa-plus');
                icon.classList.add('fa-minus');
            } else {
                icon.classList.remove('fa-minus');
                icon.classList.add('fa-plus');
            }
        });
    });
}

function getMonthLabelsThisYear() {
    const months = [
        "Jan","Feb","Mar","Apr","May","Jun",
        "Jul","Aug","Sep","Oct","Nov","Dec"
    ];

    return months;
}

loadOutstandingData(); // initial load

// =================== CHART ===================
const ctx = document.getElementById('poChart').getContext('2d');
let labels = getMonthLabelsThisYear();
// let labels = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];

// let poData = [12, 19, 3, 5, 2];
// let delData = [9, 2, 1, 5, 2];
// let outData = [3, 17, 2, 0, 0];
let poData = @json($monthlyPo);
let delData = @json($monthlyDelivered);
let outData = @json($monthlyOutstanding);

let poChart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: labels,
        datasets: [
            {
                label: "Purchase Order",
                data: poData,
                borderWidth: 2
            },
            {
                label: "Delivered",
                data: delData,
                borderWidth: 2
            },
            {
                label: "Outstanding",
                data: outData,
                borderWidth: 2
            }
        ]
    },
    options: {
        responsive: true,
        scales: {
            y: { beginAtZero: true }
        },
        plugins: {
            title: {
                display: true,
                text: 'Monitoring PO ' + new Date().getFullYear(),
                font: { size: 16, weight: 'bold' }
            }
        },
        onClick: function (evt) {
            const points = poChart.getElementsAtEventForMode(
                evt,
                'nearest',
                { intersect: true },
                false
            );
            const year = new Date().getFullYear();
            if (points.length > 0) {
                const i = points[0].index;

                let detailDiv = document.getElementById('chartDetail');
                detailDiv.style.display = 'block';

                detailDiv.innerHTML = `
                    <h5><b>Detail Bulan: ${labels[i]} ${year}</b></h5>
                    <div>Purchase Order: <b>${poData[i]} Ton</b></div>
                    <div>Delivered: <b>${delData[i]} Ton</b></div>
                    <div>Outstanding: <b>${outData[i]} Ton</b></div>
                `;
            }
        }
    }
});
</script>
@endpush
