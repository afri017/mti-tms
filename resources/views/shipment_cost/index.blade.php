@extends('layout.main')

@section('content')
<section class="content">
<div class="container-fluid">

    {{-- 🔹 Card Container --}}
    <div class="card border-0 shadow-sm">

        <div class="card-header bg-secondary text-white">
            <h5 class="card-title"><i class="fas fa-truck-moving me-2"></i> Shipment Cost Management</h5>
            <div class="card-tools">
                <a href="{{ route('shipment_cost.create') }}" class="btn btn-light btn-sm fw-bold">
                <i class="fas fa-plus-circle me-1"></i> Tambah
            </a>
            </div>
        </div>

        <div class="card-body bg-light">
            {{-- 🔹 Alert --}}
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-1"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            {{-- 🔹 Filter & Search --}}
            <form method="GET" action="{{ route('shipment_cost.index') }}" class="row g-2 mb-3">
                <div class="col-md-3">
                    <input type="text" name="search" value="{{ request('search') }}"
                           class="form-control form-control-sm" placeholder="Cari vendor / route / truck...">
                </div>
                <div class="col-md-2">
                    <select name="active" class="form-control form-control-sm">
                        <option value="">-- Semua Status --</option>
                        <option value="1" {{ request('active')==='1' ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ request('active')==='0' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-sm btn-secondary w-100">
                        <i class="fas fa-search me-1"></i> Filter
                    </button>
                </div>
                <div class="col-md-2">
                    <a href="{{ route('shipment_cost.index') }}" class="btn btn-sm btn-outline-dark w-100">
                        <i class="fas fa-undo me-1"></i> Reset
                    </a>
                </div>
            </form>

            {{-- 🔹 Data Table --}}
            <div class="table-responsive">
                <table class="table table-hover align-middle table-bordered bg-white">
                    <thead class="bg-secondary text-white text-center">
                        <tr>
                            <th>ID</th>
                            <th>Vendor</th>
                            <th>Route</th>
                            <th>Truck Type</th>
                            <th>Freight</th>
                            <th>Driver</th>
                            <th>Validity</th>
                            <th>Status</th>
                            <th style="width: 100px;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($shipmentCosts as $sc)
                        <tr>
                            <td class="text-center fw-semibold text-dark">{{ $sc->id }}</td>
                            <td>{{ $sc->vendor->transporter_name ?? '-' }}</td>
                            <td>{{ $sc->routeData->route_name ?? $sc->route }}</td>
                            <td>{{ $sc->truckType->desc ?? $sc->type_truck }}</td>
                            <td class="text-end text-success fw-bold">Rp {{ number_format($sc->price_freight, 0, ',', '.') }}</td>
                            <td class="text-end text-muted">Rp {{ number_format($sc->price_driver, 0, ',', '.') }}</td>
                            <td class="text-center">
                                <small class="text-secondary">
                                    {{ $sc->validity_start ? \Carbon\Carbon::parse($sc->validity_start)->format('d M Y') : '-' }}
                                    →
                                    {{ $sc->validity_end ? \Carbon\Carbon::parse($sc->validity_end)->format('d M Y') : '-' }}
                                </small>
                            </td>
                            <td class="text-center">
                                @if($sc->active)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-danger">Inactive</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <a href="{{ route('shipment_cost.edit', $sc->id) }}" class="btn btn-outline-warning btn-sm">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('shipment_cost.destroy', $sc->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin hapus data ini?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-outline-danger btn-sm">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted py-3">
                                <i class="fas fa-info-circle me-1"></i> Tidak ada data ditemukan
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- 🔹 Pagination --}}
            <div class="d-flex justify-content-end mt-3">
                {{ $shipmentCosts->appends(request()->query())->links('pagination::bootstrap-5') }}
            </div>

        </div>
    </div>

</div>
</section>
@endsection
