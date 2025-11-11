@extends('layout.main')

@section('content')
<section class="content">
<div class="container-fluid">
    <div class="card card-primary">
        <div class="card-header">
            <h3 class="card-title">Tambah Transporter</h3>
        </div>

        <form action="{{ route('vendors.store') }}" method="POST">
            @csrf
            <div class="card-body">
                <div class="form-group">
                    <label>Nama Transporter</label>
                    <input type="text" name="transporter_name" class="form-control" required value="{{ old('transporter_name') }}">
                </div>

                <div class="form-group">
                    <label>No. Telp</label>
                    <input type="text" name="notelp" class="form-control" required value="{{ old('notelp') }}">
                </div>

                <div class="form-group">
                    <label>Alamat</label>
                    <textarea name="address" class="form-control" rows="3" required>{{ old('address') }}</textarea>
                </div>

                <div class="form-group">
                    <label>NPWP</label>
                    <input type="text" name="npwp" class="form-control" required value="{{ old('npwp') }}">
                </div>
            </div>

            <div class="card-footer text-right">
                <a href="{{ route('vendors.index') }}" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</div>
</section>
@endsection
