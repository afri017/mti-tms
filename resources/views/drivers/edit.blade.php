@extends('layout.main')
@section('content')
<section class="content">
<div class="card">
  <div class="card-header">
    <h3>Edit Driver: {{ $driver->iddriver }}</h3>
  </div>
<div class="card-body">
    <form method="POST" action="{{ route('drivers.update', $driver->iddriver) }}">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label>Nama</label>
            <input type="text" name="name" class="form-control" value="{{ $driver->name }}" required>
        </div>
        <div class="mb-3">
            <label>No SIM</label>
            <input type="text" name="no_sim" class="form-control" value="{{ $driver->no_sim }}" required>
        </div>
        <div class="mb-3">
            <label>Tipe SIM</label>
            <input type="text" name="typesim" class="form-control" value="{{ $driver->typesim }}" required>
        </div>
        <div class="mb-3">
            <label>No Telepon</label>
            <input type="text" name="notelp" class="form-control" value="{{ $driver->notelp }}" required>
        </div>
        <div class="mb-3">
            <label>Alamat</label>
            <textarea name="address" class="form-control" rows="3" required>{{ $driver->address }}</textarea>
        </div>

        <button type="submit" class="btn btn-success">Update</button>
        <a href="{{ route('drivers.index') }}" class="btn btn-secondary">Kembali</a>
    </form>
</div>
</div>
</section>
@endsection
