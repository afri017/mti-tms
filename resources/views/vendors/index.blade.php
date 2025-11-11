@extends('layout.main')

@section('content')
<section class="content">
<div class="container-fluid">
    <div class="card card-default">
        <div class="card-header">
            <h4 class="card-title">{{ $pageTitle }}</h4>
            <div class="card-tools">
                <a href="{{ route('vendors.create') }}" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Tambah Transporter</a>
            </div>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <table id="vendorTable" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>ID Vendor</th>
                        <th>Nama Transporter</th>
                        <th>No. Telp</th>
                        <th>Alamat</th>
                        <th>NPWP</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($vendors as $vendor)
                        <tr>
                            <td>{{ $vendor->idvendor }}</td>
                            <td>{{ $vendor->transporter_name }}</td>
                            <td>{{ $vendor->notelp }}</td>
                            <td>{{ $vendor->address }}</td>
                            <td>{{ $vendor->npwp }}</td>
                            <td class="text-center">
                                <a href="{{ route('vendors.edit', $vendor->idvendor) }}" class="btn btn-warning btn-sm">
                                    <i class="fas fa-edit"></i> Edit
                                </a>

                                <form action="{{ route('vendors.destroy', $vendor->idvendor) }}" method="POST" class="d-inline"
                                      onsubmit="return confirm('Yakin ingin menghapus data ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-danger btn-sm">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
</section>

{{-- Script DataTables --}}
@push('scripts')
<script>
$(document).ready(function() {
    $('#vendorTable').DataTable({
        responsive: true,
        order: [[0, 'asc']],
    });
});
</script>
@endpush
@endsection
