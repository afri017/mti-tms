@extends('layout.main')
@section('content')
<section class="content">
<div class="container-fluid">
    <div class="card card-default">
        <div class="card-header">
            <h4 class="card-title">{{ $pageTitle }}</h4>
            <div class="card-tools">
                <a href="{{ route('trucks.create') }}" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Tambah Truck</a>
            </div>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            <table id="example1" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>ID Truck</th>
                        <th>Transporter Name</th>
                        <th>Driver</th>
                        <th>Tipe Truck</th>
                        <th>Merk</th>
                        <th>No Polisi</th>
                        <th>STNK</th>
                        <th>Expired KIR</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($trucks as $truck)
                    <tr>
                        <td>{{ $truck->idtruck }}</td>
                        <td>{{ $truck->vendor->transporter_name ?? '-' }}</td>
                        <td>{{ $truck->driver->name ?? '-' }}</td>
                        <td>{{ $truck->tonnage->desc ?? '-' }}</td>
                        <td>{{ $truck->merk }}</td>
                        <td>{{ $truck->nopol }}</td>
                        <td>{{ $truck->stnk }}</td>
                        <td>{{ $truck->expired_kir }}</td>
                        <td>
                            <a href="{{ route('trucks.edit', $truck->idtruck) }}" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i> Edit</a>
                            <form action="{{ route('trucks.destroy', $truck->idtruck) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Yakin hapus data ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i> Hapus</button>
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
