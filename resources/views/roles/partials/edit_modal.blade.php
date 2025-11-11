<div class="modal fade" id="editRoleModal{{ $role->id }}" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST" action="{{ route('roles.update', $role->id) }}">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">Edit Role</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <label>Nama Role</label>
                        <input type="text" name="name" class="form-control" value="{{ $role->name }}" required>
                    </div>

                    <div class="mb-3">
                        <label>Permissions</label>

                        <div class="form-check mb-2">
                            <input type="checkbox" class="form-check-input select-all" id="selectAll_{{ $role->id }}">
                            <label for="selectAll_{{ $role->id }}" class="form-check-label">Pilih Semua</label>
                        </div>

                        <div class="table-responsive" style="max-height: 300px; overflow-y: auto;">
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
                                                {{ $role->permissions->contains($perm) ? 'checked' : '' }}>
                                        </td>
                                        <td>{{ $perm->name }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
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
