@extends('layout.main')
@section('content')
<section class="content">
<div class="container-fluid">
    <div class="card">
        <div class="card-header"><h3>Edit Shipment Cost</h3></div>
        <div class="card-body">
            <form method="POST" action="{{ route('shipment_cost.update', $shipmentCost->id) }}">
                @csrf @method('PUT')
                @include('shipment_cost.partials.form', ['shipmentCost' => $shipmentCost])
                <button class="btn btn-primary mt-3">Update</button>
                <a href="{{ route('shipment_cost.index') }}" class="btn btn-secondary mt-3">Kembali</a>
            </form>
        </div>
    </div>
</div>
</section>
@endsection
