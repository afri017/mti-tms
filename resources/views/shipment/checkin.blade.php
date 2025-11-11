@extends('layout.main')

@section('content')
<section class="content" style="padding-bottom: 100px;">
    <div class="card shadow-lg border-0">
        <div class="card-header bg-primary text-white">
            <div class="row">
                <div class="col-md-11">
                    <h5 class="mb-0">
                        <i class="fas fa-truck me-2"></i> Check In Shipment
                    </h5>
                </div>
                <div class="col-md-1">
                    <span class="badge bg-light text-primary px-3 py-2 fs-6">
                        {{ $shipment->noshipment }}
                    </span>
                </div>
            </div>
        </div>

        <div class="card-body">
            {{-- Informasi Umum --}}
            <div class="row mb-1">
                <div class="col-md-11">
                    <p class="mb-1 text-muted fw-semibold">Tanggal Kirim</p>
                    <p class="fs-5">{{ \Carbon\Carbon::parse($shipment->delivery_date)->format('d M Y') ?? '-' }}</p>
                </div>
                <div class="col-md-1">
                    <p class="mb-1 text-muted fw-semibold">Status</p>
                    <span class="badge bg-{{ $shipment->status == 'completed' ? 'success' : ($shipment->status == 'inprogress' ? 'warning' : 'secondary') }} px-3 py-2">
                        {{ ucfirst($shipment->status ?? '-') }}
                    </span>
                </div>
            </div>

            {{-- Detail Kendaraan & Rute --}}
            <div class="row mb-1">
                <div class="col-md-2">
                    <p class="mb-1 text-muted fw-semibold">Nomor Kendaraan</p>
                    <p class="fs-5">{{ $shipment->truck->nopol ?? $shipment->vehicle_no ?? '-' }}</p>
                </div>
                <div class="col-md-2">
                    <p class="mb-1 text-muted fw-semibold">Nama Supir</p>
                    <p class="fs-5">{{ $shipment->getRelation('driver')->name ?? $shipment->driver ?? '-' }}</p>
                </div>
                <div class="col-md-2">
                    <p class="mb-1 text-muted fw-semibold">Uang Jalan</p>
                    <p class="fs-5">Rp {{ number_format($shipment->shipmentCost->price_driver, 0, ',', '.') }}</p>
                </div>
                <div class="col-md-12 mt-2">
                    <p class="mb-1 text-muted fw-semibold">Rute Pengiriman</p>
                    <p class="fs-5">{{ $shipment->routeData->route_name ?? '-' }}</p>
                </div>
            </div>

            @if(isset($shipmentdetails) && $shipmentdetails->items->count())
            <div class="mb-4">
                <h6 class="fw-bold mb-2">
                    <i class="fas fa-boxes me-2 text-primary"></i> Detail Delivery Order
                </h6>
                <table class="table table-bordered align-middle">
                    <thead class="table-primary">
                        <tr>
                            <th class="text-center">No</th>
                            <th>Code Material</th>
                            <th>Material Description</th>
                            <th class="text-center">Qty</th>
                            <th class="text-center">Uom</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($shipmentdetails->items as $i => $item)
                            <tr>
                                <td class="text-center">{{ $i + 1 }}</td>
                                <td>{{ $item->material_code ?? '-' }}</td>
                                <td>{{ $item->material?->material_desc ?? '-' }}</td>
                                <td class="text-center">{{ $item->qty_plan ?? 0 }}</td>
                                <td class="text-center">{{ $item->uom ?? '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

            {{-- Catatan / keterangan tambahan --}}
            @if(!empty($shipmentdetails->checkin))
                <div class="alert alert-secondary">
                    <i class="fas fa-sticky-note me-2"></i>
                    <strong>Sudah Check-in : </strong> {{ $shipmentdetails->checkin }}
                </div>
            @else
            <div class="alert alert-info d-flex align-items-center mt-3">
                <i class="fas fa-info-circle me-2"></i>
                <div>Pastikan semua data sudah sesuai sebelum melakukan <strong>Check In</strong>.</div>
            </div>
            @endif


            {{-- Tombol Aksi --}}
            <form action="{{ route('do.checkin.store', $shipment->noshipment) }}" method="POST" class="mt-4">
                @csrf
                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('do.index.check') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Kembali
                    </a>

                    @if(!empty($shipmentdetails->checkin))
                        {{-- Sudah Check-in --}}
                        <button type="button" class="btn btn-secondary" disabled>
                            <i class="fas fa-check-circle me-1"></i> Sudah Check-In
                        </button>
                    @else
                        {{-- Belum Check-in --}}
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-check-circle me-1"></i> Konfirmasi Check-In
                        </button>
                    @endif
                </div>
            </form>
        </div>
    </div>
</section>
@endsection
