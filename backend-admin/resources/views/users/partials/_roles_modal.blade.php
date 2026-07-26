<!-- Role Modal -->
<div class="modal fade" id="roleModal" tabindex="-1" aria-labelledby="roleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow-lg" style="border-radius: 12px; background: var(--sidebar-bg);">
      <div class="modal-header border-bottom border-secondary border-opacity-25">
        <h5 class="modal-title fw-bold" id="roleModalLabel" style="color: var(--sidebar-link);">Daftar Role Sistem</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-4">
        
        <!-- Add New Role -->
        <form action="{{ route('roles.store') }}" method="POST" class="mb-4">
            @csrf
            <div class="input-group">
                <input type="text" name="name" class="form-control" placeholder="Nama Role Baru" required style="border-radius: 8px 0 0 8px;">
                <button class="btn btn-primary px-3" type="submit" style="border-radius: 0 8px 8px 0;">
                    <i class="fa-solid fa-plus me-1"></i> Tambah
                </button>
            </div>
            @error('name')
                <div class="text-danger small mt-1">{{ $message }}</div>
            @enderror
        </form>

        <!-- List Roles -->
        <div class="table-responsive">
            <table class="table table-borderless table-hover align-middle mb-0" style="color: var(--sidebar-link);">
                <thead class="border-bottom border-secondary border-opacity-25 text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px;">
                    <tr>
                        <th class="py-2 px-3 border-bottom-0">ID</th>
                        <th class="py-2 px-3 border-bottom-0">Nama Role</th>
                        <th class="py-2 px-3 border-bottom-0 text-end">Jumlah Pengguna</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($roles as $role)
                        <tr>
                            <td class="py-2 px-3 opacity-50">#{{ $role->id }}</td>
                            <td class="py-2 px-3 fw-semibold">{{ $role->name }}</td>
                            <td class="py-2 px-3 text-end"><span class="badge bg-secondary bg-opacity-25 text-light rounded-pill px-2">{{ $role->users_count ?? $role->users()->count() }}</span></td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="text-center py-3 opacity-50">Belum ada role</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

      </div>
    </div>
  </div>
</div>
