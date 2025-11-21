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
                    <input type="date" name="start_date" class="form-control mr-2" value="{{ request('start_date', '2025-08-01') }}">
                    <input type="date" name="end_date" class="form-control mr-2"   value="{{ request('end_date', '2025-08-28') }}">
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
                        <h3>18</h3>
                        <p>Plan</p>
                    </div>
                </div>
                <div class="small-box bg-orange text-center p-2 flex-fill">
                    <div class="inner">
                        <h3>13</h3>
                        <p>Register</p>
                    </div>
                </div>
                <div class="small-box bg-warning text-center p-2 flex-fill">
                    <div class="inner">
                        <h3>0</h3>
                        <p>Hold</p>
                    </div>
                </div>
                <div class="small-box bg-danger text-center p-2 flex-fill">
                    <div class="inner">
                        <h3>0</h3>
                        <p>Waiting</p>
                    </div>
                </div>
                <div class="small-box bg-info text-center p-2 flex-fill">
                    <div class="inner">
                        <h3>1</h3>
                        <p>Gate-In</p>
                    </div>
                </div>
                <div class="small-box bg-success-light text-center p-2 flex-fill">
                    <div class="inner">
                        <h3>0</h3>
                        <p>Unloading</p>
                    </div>
                </div>
                <div class="small-box bg-success-dark text-center p-2 flex-fill">
                    <div class="inner">
                        <h3>0</h3>
                        <p>Receipt</p>
                    </div>
                </div>
                <div class="small-box bg-teal text-center p-2 flex-fill">
                    <div class="inner">
                        <h3>10</h3>
                        <p>Gate-Out</p>
                    </div>
                </div>
                <div class="small-box bg-pink text-center p-2 flex-fill">
                    <div class="inner">
                        <h3>76%</h3>
                        <p>Progress</p>
                    </div>
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
@endpush
