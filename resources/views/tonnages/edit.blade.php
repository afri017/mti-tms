@extends('layout.main')
@section('content')
<section class="content">
<div class="card">
  <div class="card-header">
    <h3>Edit Tonnase: {{ $tonnage->id }}</h3>
  </div>

  <div class="card-body">

    <form method="POST" action="{{ route('tonnages.update', $tonnage->id) }}">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label>Tipe Truck (angka)</label>
            <input type="number" name="type_truck" class="form-control" value="{{ $tonnage->type_truck }}" required>
        </div>
        <div class="mb-3">
            <label>Deskripsi</label>
            <input type="text" name="desc" class="form-control" value="{{ $tonnage->desc }}" required>
        </div>

        <button type="submit" class="btn btn-success">Update</button>
        <a href="{{ route('tonnages.index') }}" class="btn btn-secondary">Kembali</a>
    </form>
</div>
</div>
</section>
@endsection
