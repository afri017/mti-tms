@extends('layout.main')

@section('content')
<section class="content">
<div class="container-fluid">
    <div class="card">
        @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
          {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
        </div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <div class="card-header">
            <h3 class="card-title">Permission Management</h3>
            <div class="card-tools">
                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#createPermissionModal">
                    <i class="bi bi-plus"></i> Tambah Permission
                </button>
            </div>
        </div>

        <div class="card-body">
            <table id="permissionTable" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nama Permission</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($permissions as $perm)
                    <tr>
                        <td>{{ $perm->id }}</td>
                        <td>{{ $perm->name }}</td>
                        <td>
                            <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editPermissionModal{{ $perm->id }}">
                                <i class="bi bi-pencil"></i> Edit
                            </button>

                            <form action="{{ route('permissions.destroy', $perm->id) }}" method="POST" class="d-inline"
                                  onsubmit="return confirm('Yakin ingin menghapus permission ini?')">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-danger btn-sm">
                                    <i class="bi bi-trash"></i> Delete
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

{{-- Modal Create --}}
<div class="modal fade" id="createPermissionModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('permissions.store') }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Permission Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <label>Nama Permission</label>
                        <input type="text" name="name" class="form-control" placeholder="misal: edit user" required>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Edit --}}
@foreach ($permissions as $perm)
<div class="modal fade" id="editPermissionModal{{ $perm->id }}" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('permissions.update', $perm->id) }}">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">Edit Permission</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <label>Nama Permission</label>
                        <input type="text" name="name" value="{{ $perm->name }}" class="form-control" required>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach
</section>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
$(function() {
    $('#permissionTable').DataTable({
        responsive: true,
        autoWidth: false,
    });
});
</script>
@endpush
