@extends('layout.main')
@section('content')
<section class="content">
    <div class="card">
        <div class="card-header">
            <h3>Edit Delivery Order (DO) - {{ $deliveryOrder->nodo }}</h3>
        </div>
        <div class="card-body">

            <form action="{{ route('delivery_scheduling.update', $deliveryOrder->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row">
                    @php
                        $isReadonly = !empty($deliveryOrder->noshipment);
                    @endphp
                    <div class="col-md-4">
                        <label>No DO</label>
                        <input type="text" name="nodo" class="form-control"
                               value="{{ old('nodo', $deliveryOrder->nodo) }}" readonly>
                    </div>
                    <div class="col-md-4">
                        <label>No Shipment</label>
                        <input type="text" name="noshipment" class="form-control"
                               value="{{ old('noshipment', $deliveryOrder->noshipment) }}" readonly>
                    </div>
                    <div class="col-md-4">
                        <label>Tanggal DO</label>
                        <input type="date" name="delivery_date" class="form-control"
                               value="{{ old('delivery_date', \Carbon\Carbon::parse($deliveryOrder->delivery_date)->format('Y-m-d')) }}" {{ $isReadonly ? 'readonly' : '' }}>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-md-4">
                        <label>Source</label>
                        <select name="source" class="form-control" {{ $isReadonly ? 'readonly' : '' }}>
                            @foreach($sources as $source)
                                <option value="{{ $source->id }}"
                                    {{ $deliveryOrder->source == $source->id ? 'selected' : '' }}>
                                    {{ $source->location_name  }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label>Destination</label>
                        <select name="destination" class="form-control" {{ $isReadonly ? 'readonly' : '' }}>
                            @foreach($destinations as $destination)
                                <option value="{{ $destination->id }}"
                                    {{ $deliveryOrder->destination == $destination->id ? 'selected' : '' }}>
                                    {{ $destination->location_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label>Route</label>
                        <select name="route_id" class="form-control" {{ $isReadonly ? 'readonly' : '' }}>
                            @foreach($routes as $route)
                                <option value="{{ $route->id }}"
                                    {{ $deliveryOrder->route_id == $route->id ? 'selected' : '' }}>
                                    {{ $route->route }} - {{ $route->route_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-md-4">
                        <label>Transporter</label>
                        <select name="transporter_id" id="transporter_id" class="form-control" {{ $isReadonly ? 'readonly' : '' }}>
                            @foreach($transporters as $t)
                                <option value="{{ $t->idvendor }}"
                                    data-truck-id="{{ optional($t->truck)->id }}"
                                    data-truck-nopol="{{ optional($t->truck)->nopol }}" {{-- untuk display --}}
                                    {{ $deliveryOrder->truck?->vendor_id == $t->idvendor ? 'selected' : '' }}>
                                    {{ $t->transporter_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label>Truck ID</label>
                        <select name="truck_id" id="truck_id" class="form-control" {{ $isReadonly ? 'readonly' : '' }}>
                            <option value="">-- Pilih Truck --</option>
                            @foreach($transporters as $t)
                                @foreach($t->trucks as $truck)
                                    <option value="{{ $truck->idtruck }}"
                                        data-transporter="{{ $t->idvendor }}"
                                        {{ $deliveryOrder->truck?->idtruck == $truck->idtruck ? 'selected' : '' }}>
                                        {{ $truck->nopol }} (ID: {{ $truck->idtruck }} - {{ $truck->tonnage?->desc }})
                                    </option>
                                @endforeach
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label>Status</label>
                        <select name="status" class="form-control" readonly>
                            <option value="Open" {{ $deliveryOrder->status == 'Open' ? 'selected' : '' }}>Open</option>
                            <option value="Partial Delivery" {{ $deliveryOrder->status == 'Partial Delivery' ? 'selected' : '' }}>Partial Delivery</option>
                            <option value="Complete" {{ $deliveryOrder->status == 'Complete' ? 'selected' : '' }}>Complete</option>
                        </select>
                    </div>
                </div>

                <div class="row mt-4">
                    <div class="col-12">
                        <h5>DO Items</h5>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Material</th>
                                    <th>Material Description</th>
                                    <th>Qty Plan</th>
                                    <th>UOM</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($deliveryOrder->items as $item)
                                <tr>
                                    <td>{{ $item->material?->name ?? $item->material_code }}</td>
                                    <td>
                                        {{ $item->material?->material_desc ?? $item->material_code }}
                                    </td>
                                    <td>
                                        <input type="number" name="items[{{ $item->id }}][qty_plan]"
                                               class="form-control"
                                               value="{{ old("items.{$item->id}.qty_plan", $item->qty_plan) }}" {{ $isReadonly ? 'readonly' : '' }}>
                                    </td>
                                    <td>{{ $item->uom ?? $item->material_code }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">Update DO</button>
                    <a href="{{ route('delivery_scheduling.index') }}" class="btn btn-secondary">Kembali</a>
                </div>
            </form>

        </div>
    </div>
</section>
@endsection


@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const transporterSelect = document.getElementById('transporter_id');
    const truckSelect = document.getElementById('truck_id');

    function filterTrucks() {
        const selectedTransporter = transporterSelect.value;
        Array.from(truckSelect.options).forEach(option => {
            const transporterId = option.getAttribute('data-transporter');
            option.style.display = transporterId === selectedTransporter || option.value === "" ? '' : 'none';
        });

        // Reset selected jika tidak cocok
        if(truckSelect.selectedOptions[0].style.display === 'none') {
            truckSelect.value = '';
        }
    }

    transporterSelect.addEventListener('change', filterTrucks);

    // Jalankan filter saat halaman load
    filterTrucks();
});
</script>
@endpush
