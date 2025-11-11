@extends('layout.main')

@section('content')
<section class="content" style="padding-bottom: 100px;">
<div class="container-fluid">

    <div class="card card-default shadow">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h3 class="card-title mb-0">Shipment List</h3>

        </div>

        <div class="card-body">
            {{-- FILTER FORM --}}
            <form method="GET" action="{{ route('shipment.index') }}" class="mb-4">
                <div class="row">
                    {{-- Date From --}}
                    <div class="col-md-3 mb-2">
                        <label class="form-label">Date From</label>
                        <input type="date" name="date_from" class="form-control"
                               value="{{ request('date_from') }}">
                    </div>

                    {{-- Date To --}}
                    <div class="col-md-3 mb-2">
                        <label class="form-label">Date To</label>
                        <input type="date" name="date_to" class="form-control"
                               value="{{ request('date_to') }}">
                    </div>

                    {{-- Route --}}
                    <div class="col-md-3 mb-2">
                        <label class="form-label">Route</label>
                        <select name="route" class="form-control">
                            <option value="">-- All Routes --</option>
                            @foreach($routes as $r)
                                <option value="{{ $r->route }}" {{ request('route') == $r->route ? 'selected' : '' }}>
                                    {{ $r->route_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Gate --}}
                    <div class="col-md-3 mb-2">
                        <label class="form-label">Gate</label>
                        <select name="gate" class="form-control">
                            <option value="">-- All Gates --</option>
                            @foreach($gates as $g)
                                <option value="{{ $g->gate }}" {{ request('gate') == $g->gate ? 'selected' : '' }}>
                                    {{ $g->gate }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Status --}}
                    <div class="col-md-3 mb-2">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-control">
                            <option value="">-- All Statuses --</option>
                            @foreach($statuses as $value => $label)
                                <option value="{{ $value }}" {{ request('status') == $value ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Button --}}
                    <div class="col-md-12 mt-3">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i> Filter
                        </button>
                        <a href="{{ route('shipment.index') }}" class="btn btn-secondary">
                            <i class="fas fa-sync"></i> Reset
                        </a>
                        <a href="{{ route('shipment.export', request()->all()) }}" class="btn btn-success text-end">
                            <i class="fas fa-file-excel"></i> Download Excel
                        </a>
                    </div>
                </div>
            </form>

            {{-- SHIPMENT TABLE --}}
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover align-middle">
                    <thead class="thead-light">
                        <tr class="text-center">
                            <th>No</th>
                            <th>No Shipment</th>
                            <th>Transporter</th>
                            <th>Driver</th>
                            <th>NoPlat</th>
                            <th>Type Truck</th>
                            <th>Delivery Date</th>
                            <th>Route</th>
                            <th>Freight Cost</th>
                            <th>Driver Cost</th>
                            <th>Gate</th>
                            <th>Gate Open</th>
                            <th>Gate Close</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($shipments as $i => $shipment)
                            <tr>
                                <td class="text-center">{{ $shipments->firstItem() + $i }}</td>
                                <td>{{ $shipment->noshipment ?? '-' }}</td>
                                <td>{{ $shipment->vendor->transporter_name ?? '-' }}</td>
                                <td>{{ $shipment->truck->driver->name ?? '-' }}</td>
                                <td>{{ $shipment->truck->nopol ?? '-' }}</td>
                                <td>{{ $shipment->truck->tonnage->desc ?? '-' }}</td>
                                <td>{{ $shipment->delivery_date ?? '-' }}</td>
                                <td>{{ $shipment->routeData->route_name ?? '-' }}</td>
                                <td>{{ $shipment->shipmentCost ? 'Rp ' . number_format($shipment->shipmentCost->price_freight, 0, ',', '.') : '-' }}</td>
                                <td>{{ $shipment->shipmentCost ? 'Rp ' . number_format($shipment->shipmentCost->price_driver, 0, ',', '.') : '-' }}</td>
                                <td>{{ $shipment->gate ?? '-' }}</td>
                                <td>{{ $shipment->timestart ?? '-' }}</td>
                                <td>{{ $shipment->timeend ?? '-' }}</td>
                                <td class="text-center">
                                    <span class="badge
                                        @if($shipment->status == 'OPEN') bg-success
                                        @elseif($shipment->status == 'IN PROGRESS') bg-warning
                                        @elseif($shipment->status == 'CLOSED') bg-secondary
                                        @elseif($shipment->status == 'CANCELLED') bg-danger
                                        @endif">
                                        {{ $shipment->status ?? '-' }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('shipment.edit', $shipment->id) }}" class="btn btn-sm btn-warning">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('shipment.destroy', $shipment->id) }}"
                                          method="POST" style="display:inline-block"
                                          onsubmit="return confirm('Delete this shipment?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted">No shipment data found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- PAGINATION --}}
            <div class="d-flex justify-content-center mt-3">
                {{ $shipments->appends(request()->all())->links('pagination::bootstrap-5') }}
            </div>

        </div>
    </div>

</div>
</section>
@endsection
