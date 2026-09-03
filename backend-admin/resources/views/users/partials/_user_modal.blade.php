<!-- User Modal -->
<div class="modal fade" id="userModal" tabindex="-1" aria-labelledby="userModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content border-0 shadow-lg" style="border-radius: 12px; background: var(--sidebar-bg);">
      <div class="modal-header border-bottom border-secondary border-opacity-25 p-4">
        <h5 class="modal-title fw-bold" id="userModalLabel" style="color: var(--sidebar-link);">
            <i class="fa-solid fa-user-plus me-2 text-primary" id="userModalIcon"></i> 
            <span id="userModalTitle">Tambah Pengguna</span>
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-4" style="color: var(--bs-body-color);">
        
        <form id="userForm" action="{{ route('users.store') }}" method="POST">
            @csrf
            <div id="methodContainer"></div>

            <h6 class="fw-bold mb-3" style="color: var(--sidebar-link); letter-spacing: 0.5px;">1. Informasi Dasar</h6>
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label fw-medium">Nama Lengkap <span class="text-danger">*</span></label>
                    <input type="text" name="full_name" id="userFullName" class="form-control" style="border-radius: 8px;" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-medium">Alamat Email <span class="text-danger">*</span></label>
                    <input type="email" name="email" id="userEmail" class="form-control" style="border-radius: 8px;" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-medium">Username</label>
                    <input type="text" name="username" id="userUsername" class="form-control" style="border-radius: 8px;">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-medium">Password Dasar <span class="text-danger" id="passwordAsterisk">*</span></label>
                    <input type="password" name="password" id="userPassword" class="form-control" style="border-radius: 8px;">
                    <small class="text-muted" id="passwordHelpText">Gunakan minimal 8 karakter.</small>
                </div>
            </div>

            <hr class="border-secondary border-opacity-25 my-4">

            <h6 class="fw-bold mb-3" style="color: var(--sidebar-link); letter-spacing: 0.5px;">2. Penugasan & Hak Akses Sistem</h6>
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label fw-medium">Divisi (Unit Kerja)</label>
                    <select name="division_id" id="userDivision" class="form-select" style="border-radius: 8px;">
                        <option value="">-- Pilih Divisi --</option>
                        @foreach($divisions as $division)
                            <option value="{{ $division->id }}">{{ $division->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-medium">Hak Akses (System Role) <span class="text-danger">*</span></label>
                    <select name="role_id" id="userRole" class="form-select" style="border-radius: 8px;" required>
                        <option value="">-- Pilih Role Akses --</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->id }}">{{ $role->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            
            <hr class="border-secondary border-opacity-25 my-4">

            <h6 class="fw-bold mb-3" style="color: var(--sidebar-link); letter-spacing: 0.5px;">3. Status Akun</h6>
            <div class="row g-3 mb-4">
                <div class="col-md-12">
                    <div class="form-check form-switch d-flex align-items-center gap-2">
                        <input class="form-check-input shadow-sm" type="checkbox" name="active" role="switch" id="userActive" style="width: 2.5em; height: 1.25em; cursor: pointer;" checked>
                        <label class="form-check-label fw-medium ms-2" for="userActive">Akun Aktif (Dapat digunakan untuk login)</label>
                    </div>
                </div>
            </div>
            <div class="d-flex justify-content-end gap-3 pt-3 border-top border-secondary border-opacity-25">
                <button type="button" class="btn btn-light border px-4" data-bs-dismiss="modal" style="border-radius: 8px;">Batal</button>
                <button type="submit" class="btn btn-primary px-4 shadow-sm" style="border-radius: 8px;">
                    <i class="fa-solid fa-save me-2"></i> Simpan
                </button>
            </div>
        </form>

      </div>
    </div>
  </div>
</div>

<script>
function openUserModal(isEdit, user = null) {
    const form = document.getElementById('userForm');
    const title = document.getElementById('userModalTitle');
    const icon = document.getElementById('userModalIcon');
    const methodContainer = document.getElementById('methodContainer');
    const pwdHelp = document.getElementById('passwordHelpText');
    const pwdAsterisk = document.getElementById('passwordAsterisk');

    if (isEdit && user) {
        title.innerText = 'Edit Pengguna';
        icon.className = 'fa-solid fa-user-pen me-2 text-primary';
        form.action = `/users/${user.id}`;
        methodContainer.innerHTML = '<input type="hidden" name="_method" value="PUT">';
        
        document.getElementById('userFullName').value = user.full_name || '';
        document.getElementById('userUsername').value = user.username || '';
        document.getElementById('userEmail').value = user.email || '';
        document.getElementById('userPassword').required = false;
        document.getElementById('userDivision').value = user.division_id || '';
        document.getElementById('userRole').value = user.role_id || '';
        document.getElementById('userActive').checked = user.active ? true : false;
        
        pwdHelp.innerText = 'Kosongkan jika tidak ingin mengubah password.';
        pwdAsterisk.style.display = 'none';
    } else {
        title.innerText = 'Tambah Pengguna';
        icon.className = 'fa-solid fa-user-plus me-2 text-primary';
        form.action = `{{ route('users.store') }}`;
        methodContainer.innerHTML = '';
        form.reset();
        
        document.getElementById('userPassword').required = true;
        document.getElementById('userActive').checked = true;
        pwdHelp.innerText = 'Gunakan minimal 8 karakter.';
        pwdAsterisk.style.display = 'inline';
    }
    
    new bootstrap.Modal(document.getElementById('userModal')).show();
}
</script>
