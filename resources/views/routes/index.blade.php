@extends('layout.main')
@section('content')
<section class="content">
<div class="card">
  <div class="card-header">
    <h3 class="card-title">List Route</h3>
    <div class="card-tools">
      <a href="{{ route('routes.create') }}" class="btn btn-primary btn-sm">+ Tambah Route</a>
    </div>
  </div>

  <div class="card-body">
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    @endif
    <table id="example1" class="table table-bordered">
        <thead>
            <tr>
                <th>Kode</th>
                <th>Source</th>
                <th>Destination</th>
                <th>Route Name</th>
                <th>Leadtime (hari)</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($routes as $r)
            <tr>
                <td>{{ $r->route }}</td>
                <td>{{ $r->sourceData->location_name ?? '-' }}</td>
                <td>{{ $r->destinationData->location_name ?? '-' }}</td>
                <td>{{ $r->route_name }}</td>
                <td>{{ $r->leadtime }}</td>
                <td>
                    <a href="{{ route('routes.edit', $r->route) }}" class="btn btn-sm btn-warning">Edit</a>
                    <form action="{{ route('routes.destroy', $r->route) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin hapus?')">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-danger">Hapus</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
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
