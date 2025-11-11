@extends('layout.main')
@section('content')
<section class="content">
<div class="container-fluid">
    <div class="card">
        <div class="card-header"><h3>Tambah Shipment Cost</h3></div>
        <div class="card-body">
            <form method="POST" action="{{ route('shipment_cost.store') }}">
                @csrf
                @include('shipment_cost.partials.form', ['shipmentCost' => new \App\Models\ShipmentCost])
                <button class="btn btn-success mt-3">Simpan</button>
                <a href="{{ route('shipment_cost.index') }}" class="btn btn-secondary mt-3">Kembali</a>
            </form>
        </div>
    </div>
</div>
</section>
@endsection
