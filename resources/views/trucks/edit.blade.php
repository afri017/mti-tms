@extends('layout.main')
@section('content')
<section class="content">
<div class="card">
  <div class="card-header">
    <h4>{{ $pageTitle }}</h4>
  </div>

  <div class="card-body">
    <form action="{{ route('trucks.update', $truck->idtruck) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="form-group">
            <label for="idvendor">Vendor</label>
            <select name="idvendor" id="idvendor" class="form-control" required>
                <option value="">-- Pilih Vendor --</option>
                @foreach($vendors as $vendor)
                    <option value="{{ $vendor->idvendor }}"
                        {{ old('idvendor', $truck->idvendor ?? '') == $vendor->idvendor ? 'selected' : '' }}>
                        {{ $vendor->transporter_name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label>Driver</label>
            <select name="iddriver" class="form-control" required>
                @foreach($drivers as $driver)
                    <option value="{{ $driver->iddriver }}" {{ $driver->iddriver == $truck->iddriver ? 'selected' : '' }}>
                        {{ $driver->iddriver }} - {{ $driver->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label>Tipe Truck</label>
            <select name="type_truck" class="form-control" required>
                @foreach($tonnages as $t)
                    <option value="{{ $t->id }}" {{ $t->id == $truck->type_truck ? 'selected' : '' }}>
                        {{ $t->id }} - {{ $t->desc }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label>STNK</label>
            <input type="text" name="stnk" class="form-control" value="{{ $truck->stnk }}" required>
        </div>
        <div class="form-group">
            <label>Merk</label>
            <input type="text" name="merk" class="form-control" value="{{ $truck->merk }}" required>
        </div>
        <div class="form-group">
            <label>No Polisi</label>
            <input type="text" name="nopol" class="form-control" value="{{ $truck->nopol }}" required>
        </div>
        <div class="form-group">
            <label>Expired KIR</label>
            <input type="date" name="expired_kir" class="form-control" value="{{ $truck->expired_kir }}" required>
        </div>
        <button type="submit" class="btn btn-warning">Update</button>
        <a href="{{ route('trucks.index') }}" class="btn btn-secondary">Kembali</a>
    </form>
</div>
</div>
</section>
@endsection
