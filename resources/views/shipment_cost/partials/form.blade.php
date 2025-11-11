<div class="row">
    <div class="col-md-4">
        <label>Vendor</label>
        <select name="idvendor" class="form-control" required>
            <option value="">-- Pilih Vendor --</option>
            @foreach($vendors as $v)
                <option value="{{ $v->idvendor }}" {{ old('idvendor', $shipmentCost->idvendor ?? '') == $v->idvendor ? 'selected' : '' }}>
                    {{ $v->transporter_name }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="col-md-4">
        <label>Route</label>
        <select name="route" class="form-control" required>
            <option value="">-- Pilih Route --</option>
            @foreach($routes as $r)
                <option value="{{ $r->route }}" {{ old('route', $shipmentCost->route ?? '') == $r->route ? 'selected' : '' }}>
                    {{ $r->route_name }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="col-md-4">
        <label>Type Truck</label>
        <select name="type_truck" class="form-control" required>
            <option value="">-- Pilih Type Truck --</option>
            @foreach($tonnages as $t)
                <option value="{{ $t->id }}" {{ old('type_truck', $shipmentCost->type_truck ?? '') == $t->id ? 'selected' : '' }}>
                    {{ $t->desc }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="col-md-3 mt-3">
        <label>Price Freight</label>
        <input type="number" step="0.01" name="price_freight" class="form-control" value="{{ old('price_freight', $shipmentCost->price_freight) }}" required>
    </div>
    <div class="col-md-3 mt-3">
        <label>Price Driver</label>
        <input type="number" step="0.01" name="price_driver" class="form-control" value="{{ old('price_driver', $shipmentCost->price_driver) }}" required>
    </div>
    <div class="col-md-3 mt-3">
        <label>Validity Start</label>
        <input type="date" name="validity_start" class="form-control" value="{{ old('validity_start', $shipmentCost->validity_start?->format('Y-m-d')) }}" required>
    </div>
    <div class="col-md-3 mt-3">
        <label>Validity End</label>
        <input type="date" name="validity_end" class="form-control" value="{{ old('validity_end', $shipmentCost->validity_end?->format('Y-m-d')) }}" required>
    </div>

    <div class="col-md-3 mt-3">
        <label>Active</label><br>
        <input type="checkbox" name="active" value="Y" {{ old('active', $shipmentCost->active ?? false) ? 'checked' : '' }}>
    </div>
</div>
