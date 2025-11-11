@extends('layout.main')
@section('content')
<section class="content" style="padding-bottom: 100px;">
    <div class="card">
        <div class="card-header">
            <h3>{{ $pageTitle }}</h3>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            {{-- 🧭 Filter Section --}}
            <form method="GET" action="{{ route('delivery_scheduling.index') }}" class="mb-4">
                <div class="row">
                    <div class="col-md-2">
                        <label>Start Date</label>
                        <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
                    </div>
                    <div class="col-md-2">
                        <label>End Date</label>
                        <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
                    </div>
                    <div class="col-md-2">
                        <label>Status</label>
                        <select name="status" class="form-control">
                            <option value="">-- Semua --</option>
                            <option value="Open" {{ request('status') == 'Open' ? 'selected' : '' }}>Open</option>
                            <option value="Partial Delivery" {{ request('status') == 'Partial Delivery' ? 'selected' : '' }}>Partial Delivery</option>
                            <option value="Complete" {{ request('status') == 'Complete' ? 'selected' : '' }}>Complete</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label>No PO</label>
                        <input type="text" name="po_number" class="form-control" placeholder="Cari No PO" value="{{ request('po_number') }}">
                    </div>
                    <div class="col-md-2">
                        <label>Transporter</label>
                        <select name="transporter" class="form-control">
                            <option value="">-- Semua --</option>
                            @foreach($transporters as $t)
                                <option value="{{ $t->idvendor }}" {{ request('transporter') == $t->idvendor ? 'selected' : '' }}>
                                    {{ $t->transporter_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label>Route</label>
                        <select name="route" class="form-control">
                            <option value="">-- Semua --</option>
                            @foreach($routes as $r)
                                <option value="{{ $r->route }}" {{ request('route') == $r->route ? 'selected' : '' }}>
                                  ({{ $r->route}})  {{ $r->route_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="mt-3 text-right">
                    <button type="submit" name="apply_filter" value="1" class="btn btn-primary">Apply Filter</button>
                </div>
            </form>

            {{-- 📦 Table Section --}}
            @if($deliveryOrders->isNotEmpty())
            <form id="bulkForm" method="POST" action="{{ route('delivery_scheduling.bulkAction') }}">
                @csrf
                <input type="hidden" name="selected_dos" id="selected_dos_input">
            </form>
                <table id="do-table" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th><input type="checkbox" id="check-all"></th> {{-- checkbox untuk select all --}}
                            <th>No DO</th>
                            <th>No Shipment</th>
                            <th>Tanggal DO</th>
                            <th>Route</th>
                            <th>Source</th>
                            <th>Destination</th>
                            <th>No PO</th>
                            <th>Transporter</th>
                            <th>Truck</th>
                            <th>Type Truck</th>
                            <th>Status</th>
                            <th>Qty Plan</th>
                            <th>Still to Deliver</th>
                            <th>Action</th> {{-- Kolom untuk edit/delete --}}
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($deliveryOrders as $do)
                        <tr>
                            <td><input type="checkbox" class="do-checkbox" name="selected_dos[]" value="{{ $do->nodo }}"></td>
                            <td>{{ $do->nodo }}</td>
                            <td>{{ $do->noshipment }}</td>
                            <td>{{ \Carbon\Carbon::parse($do->delivery_date)->format('Y-m-d') }}</td>
                            <td>{{ $do->route_data?->route ?? '-' }}</td>
                            <td>{{ $do->sourceLocation?->location_name ?? '-' }}</td>
                            <td>{{ $do->destinationLocation?->location_name ?? '-' }}</td>
                            <td>{{ $do->nopo }}</td>
                            <td>{{ $do->truck?->vendor?->transporter_name ?? '-' }}</td>
                            <td>{{ $do->truck?->nopol ?? '-' }}</td>
                            <td>{{ $do->truck?->tonnage?->desc ?? '-' }}</td>
                            <td>{{ $do->status }}</td>
                            <td>{{ $do->total_qty_plan }}</td>
                            <td>{{ $do->total_still_to_deliver }}</td>
                            <td>
                                <a href="{{ route('delivery_scheduling.edit', $do->id) }}" class="btn btn-sm btn-warning">Edit</a>
                                <form action="{{ route('delivery_scheduling.destroy', $do->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus DO ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-danger">Delete</button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="mt-3">
                    <button type="button" id="executeBtn" class="btn btn-success">Eksekusi DO Terpilih</button>
                </div>
            @else
            <div class="alert alert-info text-center mt-3">
                Silakan pilih filter dan klik <b>Apply Filter</b> untuk menampilkan data.
            </div>
            @endif

        </div>
    </div>
</section>
@endsection

@push('scripts')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap4.min.css">
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap4.min.js"></script>

<script>
$(document).ready(function() {
    const table = $('#do-table').DataTable({
        order: [[2, 'desc']],
        pageLength: 10,
    });

    // ✅ Check/Uncheck all
    $('#check-all').on('click', function() {
        const checked = this.checked;
        $('.do-checkbox').prop('checked', checked);
    });

    // ✅ Tombol eksekusi DO
    $('#executeBtn').on('click', function () {
        let selected = [];
        // Gunakan selector global DataTables agar tetap mendeteksi semua baris
        $('input.do-checkbox:checked', table.rows().nodes()).each(function() {
            selected.push($(this).val());
        });

        if (selected.length === 0) {
            alert('Pilih minimal satu DO untuk dieksekusi.');
            return;
        }

        $('#selected_dos_input').val(JSON.stringify(selected));
        $('#bulkForm').submit();
    });
});
</script>
@endpush
