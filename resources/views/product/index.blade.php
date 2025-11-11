    <!-- Main content -->
@extends('layout.main')
@section('content')
<section class="content">
<div class="card">
  <div class="card-header">
    <h3 class="card-title">Daftar Material</h3>
    <div class="card-tools">
      <a href="{{ route('product.create') }}" class="btn btn-primary btn-sm">+ Tambah Material</a>
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
    <table id="example1" class="table table-bordered table-striped">
      <thead>
        <tr>
            <th>#</th>
            <th>Kode Material</th>
            <th>Deskripsi</th>
            <th>UOM</th>
            <th>Konversi Ton</th>
            <th>Dibuat Oleh</th>
            <th>Diperbarui Oleh</th>
            <th>Terakhir Update</th>
            <th>Action</th> <!-- kolom baru -->
        </tr>
      </thead>
      <tbody>
          @foreach ($materials as $index => $m)
          <tr>
              <td>{{ $index + 1 }}</td>
              <td>{{ $m->material_code }}</td>
              <td>{{ $m->material_desc }}</td>
              <td>{{ $m->uom }}</td>
              <td>{{ number_format($m->konversi_ton, 2) }}</td>
              <td>{{ $m->created_by }}</td>
              <td>{{ $m->update_by }}</td>
              <td>{{ $m->last_update }}</td>
              <td class="text-center">
                  <a href="{{ route('product.edit', $m->id) }}" class="btn btn-sm btn-warning">
                      <i class="fas fa-edit"></i>
                  </a>
                  <form action="{{ route('product.destroy', $m->id) }}" method="POST" style="display:inline;">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Hapus material ini?')">
                          <i class="fas fa-trash-alt"></i>
                      </button>
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
