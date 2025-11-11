@extends('layout.main')
@section('content')
<section class="content">
<div class="container-fluid">
    <div class="card card-default">
        <div class="card-header">
            <h4 class="card-title">{{ $pageTitle }}</h4>
            <div class="card-tools">
                <a href="{{ route('tonnages.create') }}" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Tambah Tonnage</a>
            </div>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            <table id="example1" class="table table-bordered">
                <thead>
                    <tr>
                        <th>ID</th><th>Tipe Truck</th><th>Deskripsi</th><th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($tonnages as $t)
                    <tr>
                        <td>{{ $t->id }}</td>
                        <td>{{ $t->type_truck }}</td>
                        <td>{{ $t->desc }}</td>
                        <td>
                            <a href="{{ route('tonnages.edit', $t->id) }}" class="btn btn-sm btn-warning">Edit</a>
                            <form action="{{ route('tonnages.destroy', $t->id) }}" method="POST" style="display:inline">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Hapus tonnase ini?')">Hapus</button>
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
