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
            <h3 class="card-title">Role Management</h3>
            <div class="card-tools">
                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#createRoleModal">
                    <i class="bi bi-plus"></i> Tambah Role
                </button>
            </div>
        </div>
        <div class="card-body">
            <table class="table table-bordered table-striped" id="roleTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nama Role</th>
                        <th>Permissions</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($roles as $role)
                    <tr>
                        <td>{{ $role->id }}</td>
                        <td>{{ $role->name }}</td>
                        <td>
                            @foreach($role->permissions as $perm)
                                <span class="badge bg-info text-dark">{{ $perm->name }}</span>
                            @endforeach
                        </td>
                        <td>
                            <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editRoleModal{{ $role->id }}">
                                <i class="bi bi-pencil"></i> Edit
                            </button>

                            <form action="{{ route('roles.destroy', $role->id) }}" method="POST" style="display:inline-block;"
                                onsubmit="return confirm('Yakin ingin menghapus role ini?')">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-danger btn-sm"><i class="bi bi-trash"></i> Delete</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            {{-- ✅ Pindahkan semua modal edit ke bawah sini --}}
            @foreach($roles as $role)
            @include('roles.partials.edit_modal', ['role' => $role, 'permissions' => $permissions])
            @endforeach
        </div>
    </div>
</div>

{{-- Modal Create --}}
<div class="modal fade" id="createRoleModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('roles.store') }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Role Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label>Nama Role</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Permissions</label>
                        <div class="table-responsive" style="max-height: 250px; overflow-y: auto; border: 1px solid #ddd;">
                            <table class="table table-sm table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 50px;">#</th>
                                        <th>Permission Name</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($permissions as $perm)
                                    <tr>
                                        <td>
                                            <input type="checkbox" name="permissions[]" value="{{ $perm->name }}"
                                                {{ isset($role) && $role->permissions->contains($perm) ? 'checked' : '' }}>
                                        </td>
                                        <td>{{ $perm->name }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <small class="text-muted">Centang permission yang ingin diberikan.</small>
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
</section>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.select-all').forEach(function(selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            const modal = this.closest('.modal');
            const checkboxes = modal.querySelectorAll('input[type="checkbox"][name="permissions[]"]');
            checkboxes.forEach(cb => cb.checked = this.checked);
        });
    });
});
</script>
<script>
$(document).ready(function() {
    $('#roleTable').DataTable({
        responsive: true,
        autoWidth: false
    });
});
</script>
@endpush
