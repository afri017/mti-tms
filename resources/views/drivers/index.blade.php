@extends('layout.main')
@section('content')
<section class="content">
<div class="container-fluid">
    <div class="card card-default">
        <div class="card-header">
            <h4 class="card-title">{{ $pageTitle }}</h4>
            <div class="card-tools">
                <a href="{{ route('drivers.create') }}" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Tambah Driver</a>
            </div>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

                <table id="example1" class="table table-bordered">
                    <thead>
                        <tr>
                            <th>ID</th><th>Nama</th><th>No SIM</th><th>Tipe SIM</th><th>Telepon</th><th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($drivers as $driver)
                        <tr>
                            <td>{{ $driver->iddriver }}</td>
                            <td>{{ $driver->name }}</td>
                            <td>{{ $driver->no_sim }}</td>
                            <td>{{ $driver->typesim }}</td>
                            <td>{{ $driver->notelp }}</td>
                            <td>
                                <a href="{{ route('drivers.edit', $driver->iddriver) }}" class="btn btn-sm btn-warning">Edit</a>
                                <form action="{{ route('drivers.destroy', $driver->iddriver) }}" method="POST" style="display:inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Hapus driver ini?')">Hapus</button>
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
@endsection


@push('scripts')
<script>
  $(function () {
    $("#example1").DataTable({
      "responsive": true, "lengthChange": false, "autoWidth": true,
       "language" : {
            search: "Cari:",
            lengthMenu: "Tampilkan _MENU_ data",
            info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
            paginate: {
                next: "Berikutnya",
                previous: "Sebelumnya"
            }
        },
      "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
    }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
  });
</script>
@endpush
