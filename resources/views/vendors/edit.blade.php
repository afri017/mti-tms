@extends('layout.main')

@section('content')
<section class="content">
<div class="container-fluid">
    <div class="card card-warning">
        <div class="card-header">
            <h3 class="card-title">Edit Transporter</h3>
        </div>

        <form action="{{ route('vendors.update', $vendor->idvendor) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="card-body">
                <div class="form-group">
                    <label>ID Vendor</label>
                    <input type="text" class="form-control" value="{{ $vendor->idvendor }}" disabled>
                </div>

                <div class="form-group">
                    <label>Nama Transporter</label>
                    <input type="text" name="transporter_name" class="form-control" required value="{{ old('transporter_name', $vendor->transporter_name) }}">
                </div>

                <div class="form-group">
                    <label>No. Telp</label>
                    <input type="text" name="notelp" class="form-control" required value="{{ old('notelp', $vendor->notelp) }}">
                </div>

                <div class="form-group">
                    <label>Alamat</label>
                    <textarea name="address" class="form-control" rows="3" required>{{ old('address', $vendor->address) }}</textarea>
                </div>

                <div class="form-group">
                    <label>NPWP</label>
                    <input type="text" name="npwp" class="form-control" required value="{{ old('npwp', $vendor->npwp) }}">
                </div>
            </div>

            <div class="card-footer text-right">
                <a href="{{ route('vendors.index') }}" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-warning">Update</button>
            </div>
        </form>
    </div>
</div>
</section>
@endsection
