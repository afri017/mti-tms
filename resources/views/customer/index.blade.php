    <!-- Main content -->
@extends('layout.main')
@section('content')
<section class="content">
<div class="card">
  <div class="card-header">
    <h3 class="card-title">Daftar Customer</h3>
    <div class="card-tools">
      <a href="{{ route('customer.create') }}" class="btn btn-primary btn-sm">+ Tambah Customer</a>
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
          <th>ID</th>
          <th>ID Customer</th>
          <th>Nama Customer</th>
          <th>Alamat</th>
          <th>No. Telp</th>
          <th>Status</th>
          <th>Dibuat Oleh</th>
          <th>Diperbarui Oleh</th>
          <th>Action</th> <!-- kolom baru -->
        </tr>
      </thead>
      <tbody>
        @foreach ($customers as $customer)
        <tr>
          <td>{{ $customer->id }}</td>
          <td>{{ $customer->idcustomer }}</td>
          <td>{{ $customer->customer_name }}</td>
          <td>{{ $customer->address }}</td>
          <td>{{ $customer->notelp }}</td>
          <td>{{ $customer->is_active }}</td>
          <td>{{ $customer->created_by }}</td>
          <td>{{ $customer->update_by }}</td>
          <td>
              <a href="{{ route('customer.edit', $customer->id) }}" class="btn btn-warning btn-sm">
                <i class="fas fa-edit"></i>
              </a>

              <form action="{{ route('customer.destroy', $customer->id) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Yakin hapus customer ini?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger btn-sm">
                  <i class="fas fa-trash"></i>
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
      "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
    }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
    $('#example2').DataTable({
      "paging": true,
      "lengthChange": false,
      "searching": false,
      "ordering": true,
      "info": true,
      "autoWidth": false,
      "responsive": true,
    });
  });
</script>
@endpush
