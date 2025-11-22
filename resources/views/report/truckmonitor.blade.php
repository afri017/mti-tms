@extends('layout.main')

@section('content')
    <div class="content">
        <div class="container-fluid">
            <!-- Dashboard Header -->
            <div class="card card-dark">
                <div class="card-header text-center">
                    <h5>Informasi Jumlah Kendaraan (Plan dan Actual) yang Dikirim dari Pelabuhan</h5>
                    <p>Latest Sync Date: {!! json_encode($latest_sync) !!}</p>
                </div>
            </div>

            <!-- Filter Section -->
            <div class="mb-3">
                <form class="form-inline">
                    <input type="date" name="start_date" class="form-control mr-2" value="{{ request('start_date', now()->startOfMonth()->format('Y-m-d')) }}">
                    <input type="date" name="end_date" class="form-control mr-2"   value="{{ request('end_date', now()->format('Y-m-d')) }}">
                    <button type="submit" class="btn btn-warning">Filter</button>
                </form>
            </div>

            <!-- Chart Section -->
            <div class="card card-dark">
                <div class="card-body" style="height:400px;">
                    <canvas id="mixed-chart" style="background-color:#000;"></canvas>
                </div>
            </div>

            <!-- KPI Cards -->
            <div class="d-flex mt-3" style="gap: 0.5rem;">
                <div class="small-box bg-success text-center p-2 flex-fill">
                    <div class="inner">
                        <h3>{!! json_encode($kpi['plan']) !!}</h3>
                        <p>Plan</p>
                    </div>
                </div>
                <div class="small-box bg-info text-center p-2 flex-fill">
                    <div class="inner">
                        <h3>{!! json_encode($kpi['gate_in']) !!}</h3>
                        <p>Gate-In</p>
                    </div>
                </div>
                <div class="small-box bg-danger text-center p-2 flex-fill">
                    <div class="inner">
                        <h3>{!! json_encode($kpi['waiting']) !!}</h3>
                        <p>Waiting</p>
                    </div>
                </div>
                <div class="small-box bg-teal text-center p-2 flex-fill">
                    <div class="inner">
                        <h3>{!! json_encode($kpi['gate_out']) !!}</h3>
                        <p>Gate-Out</p>
                    </div>
                </div>
                <div class="small-box bg-lightblue text-center p-2 flex-fill">
                    <div class="inner">
                        <h3>{!! json_encode($kpi['unloading']) !!}</h3>
                        <p>Unloading Depo</p>
                    </div>
                </div>
                <div class="small-box bg-orange text-center p-2 flex-fill">
                    <div class="inner">
                        <h3>{!! json_encode($kpi['register']) !!}</h3>
                        <p>Delivery in (Ton)</p>
                    </div>
                </div>
                <div class="small-box bg-warning text-center p-2 flex-fill">
                    <div class="inner">
                        <h3>{!! json_encode($kpi['hold']) !!}</h3>
                        <p>Good In Transit</p>
                    </div>
                </div>
                <div class="small-box bg-navy text-center p-2 flex-fill">
                    <div class="inner">
                        <h3>{!! json_encode($kpi['receipt']) !!}</h3>
                        <p>Receipt in (Ton)</p>
                    </div>
                </div>
                <div class="small-box bg-pink text-center p-2 flex-fill">
                    <div class="inner">
                        <h3>{{ $kpi['progress'] }}%</h3>
                        <p>Progress</p>
                    </div>
                </div>
            </div>

            <!-- Data Table -->
            <div class="card card-dark mt-4">
                <div class="card-header">
                    <h5 class="mb-0">Daily Plan Truck Monitor</h5>
                </div>
                <div class="card-body">
                    <table id="truck-table" class="table table-bordered table-striped table-sm">
                        <thead class="thead-dark">
                            <tr>
                                <th></th> {{-- tombol expand --}}
                                <th>Tanggal</th>
                                <th>Plan</th>
                                <th>Gate-In</th>
                                <th>Gate-Out</th>
                                <th>Unloading</th>
                                <th>Waiting</th>
                                <th>Tonase Plan</th>
                                <th>Tonase Receipt</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($labels as $i => $tanggal)
                                <tr data-date="{{ $periodDates[$i] }}"">
                                    <td class="details-control text-center" style="cursor:pointer;">➕</td>
                                    <td>{{ $tanggal }}</td>
                                    <td>{{ $inbound[$i] ?? 0 }}</td>
                                    <td>{{ $outbound[$i] ?? 0 }}</td>
                                    <td>{{ $gate_out[$i] ?? 0 }}</td>
                                    <td>{{ $unloading[$i] ?? 0 }}</td>
                                    <td>{{ $waiting[$i] ?? 0 }}</td>
                                    <td>{{ number_format($kg[$i] ?? 0, 2) }}</td>
                                    <td>{{ number_format($receipt[$i] ?? 0, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
<script src="{{ asset('plugins/chart.umd.min.js') }}"></script>
<script src="{{ asset('plugins/chartjs-plugin-datalabels.min.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    Chart.register(ChartDataLabels);

    const ctx = document.getElementById('mixed-chart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($labels) !!},
            datasets: [
                {
                    label: 'Total Plan',
                    data: {!! json_encode($inbound) !!},
                    backgroundColor: '#007bff',
                    yAxisID: 'y',
                    stack: 'trucks',
                },
                {
                    label: 'Total Actual',
                    data: {!! json_encode($outbound) !!},
                    backgroundColor: '#28a745',
                    yAxisID: 'y',
                    stack: 'trucks',
                },
                {
                    label: 'Total Ton',
                    data: {!! json_encode($kg) !!},
                    borderColor: '#dc3545',
                    backgroundColor: '#dc3545',
                    type: 'line',
                    fill: false,
                    yAxisID: 'y1',
                    order: 1,
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: { mode: 'index', intersect: false },
            scales: {
                x: { grid: { color: '#444' }, ticks: { color: '#e0e0e0' } },
                y: { type:'linear', display:true, position:'left', stacked:true, grid:{color:'#444'}, ticks:{color:'#e0e0e0'} },
                y1:{ type:'linear', display:true, position:'right', grid:{color:'#444'}, ticks:{color:'#e0e0e0'} }
            },
            plugins: {
                legend:{ display:true, position:'top', labels:{color:'#e0e0e0'} },
                datalabels:{ anchor:'end', align:'top', color:'#e0e0e0' }
            }
        }
    });
});
</script>
<script>
let table;

$(document).ready(function () {

    table = $('#truck-table').DataTable({
        responsive: true,
        pageLength: 15,
        ordering: true
    });

    $('#truck-table tbody').on('click', 'td.details-control', function () {

        let tr  = $(this).closest('tr');
        let row = table.row(tr);

        // Jika sedang terbuka → tutup dan selesai
        if (row.child.isShown()) {
            row.child.hide();
            tr.removeClass('shown');
            $(this).text('➕');
            return; // <-- STOP di sini
        }

        // Jika belum terbuka → expand & load
        row.child('<div class="p-2">Loading...</div>').show();
        tr.addClass('shown');
        $(this).text('➖');

        let tanggal = tr.data('date');

        $.ajax({
            url: "/report/truckmonitor/detail",
            type: "GET",
            data: { date: tanggal },
            success: function (html) {
                row.child(html).show();
            }
        });
    });

});

</script>
@endpush
