<x-app-layout>
    <x-slot name="header">
        {{ $user->exists ? 'Edit Pengguna' : 'Tambah Pengguna' }}
    </x-slot>

    <div class="row justify-content-center">
        <div class="col-12 col-xl-8">
            <div class="card border-0 data-card" style="background: var(--topbar-bg); color: var(--bs-body-color);">
                <div class="card-header bg-transparent border-bottom-0 p-4 pb-0">
                    <h5 class="fw-bold mb-0 text-primary">
                        <i class="fa-solid {{ $user->exists ? 'fa-user-pen' : 'fa-user-plus' }} me-2"></i>
                        {{ $user->exists ? 'Form Edit Pengguna' : 'Form Tambah Pengguna' }}
                    </h5>
                    <p class="text-muted mt-2 mb-0 fs-6">Silakan lengkapi data profil, divisi, dan hak akses pengguna di bawah ini.</p>
                </div>
                <div class="card-body p-4">
                    
                    @if ($errors->any())
                        <div class="alert alert-danger rounded-3 border-0 bg-danger bg-opacity-10 text-danger mb-4">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ $user->exists ? route('users.update', $user->id) : route('users.store') }}" method="POST">
                        @csrf
                        @if($user->exists)
                            @method('PUT')
                        @endif

                        <!-- Bagian Informasi Dasar -->
                        <h6 class="fw-bold mb-3 mt-2" style="color: var(--sidebar-link); letter-spacing: 0.5px;">1. Informasi Profil Dasar</h6>
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-medium">Nama Lengkap <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required placeholder="Contoh: Budi Santoso" style="border-radius: 8px;">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-medium">Alamat Email <span class="text-danger">*</span></label>
                                <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required placeholder="Contoh: budi@aqpa.co.id" style="border-radius: 8px;">
                            </div>
                            <div class="col-md-12">
                                <label class="form-label fw-medium">Kata Sandi (Password) {!! !$user->exists ? '<span class="text-danger">*</span>' : '' !!}</label>
                                <input type="password" name="password" class="form-control" {{ !$user->exists ? 'required' : '' }} placeholder="{{ $user->exists ? 'Biarkan kosong jika tidak ingin mengubah password' : 'Minimal 8 karakter' }}" style="border-radius: 8px;">
                            </div>
                        </div>

                        <hr class="border-secondary border-opacity-25 my-4">

                        <!-- Bagian Penugasan Divisi -->
                        <h6 class="fw-bold mb-3" style="color: var(--sidebar-link); letter-spacing: 0.5px;">2. Penugasan & Jabatan</h6>
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-medium">Divisi (Unit Kerja)</label>
                                <select name="division" class="form-select" style="border-radius: 8px;">
                                    <option value="" disabled {{ old('division', $user->division) ? '' : 'selected' }}>-- Pilih Divisi --</option>
                                    <option value="Logistik" {{ old('division', $user->division) == 'Logistik' ? 'selected' : '' }}>Logistik</option>
                                    <option value="Driver" {{ old('division', $user->division) == 'Driver' ? 'selected' : '' }}>Driver (Pengiriman)</option>
                                    <option value="Accounting" {{ old('division', $user->division) == 'Accounting' ? 'selected' : '' }}>Accounting / Keuangan</option>
                                    <option value="Sales" {{ old('division', $user->division) == 'Sales' ? 'selected' : '' }}>Penjualan / Sales</option>
                                    <option value="Warehouse" {{ old('division', $user->division) == 'Warehouse' ? 'selected' : '' }}>Gudang / Inventory</option>
                                    <option value="Purchasing" {{ old('division', $user->division) == 'Purchasing' ? 'selected' : '' }}>Pembelian / Purchasing</option>
                                    <option value="HR & Admin" {{ old('division', $user->division) == 'HR & Admin' ? 'selected' : '' }}>HR & Admin</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-medium">Jabatan (Position)</label>
                                <select name="position" class="form-select" style="border-radius: 8px;">
                                    <option value="" disabled {{ old('position', $user->position) ? '' : 'selected' }}>-- Pilih Jabatan --</option>
                                    <option value="Manager" {{ old('position', $user->position) == 'Manager' ? 'selected' : '' }}>Manager</option>
                                    <option value="Supervisor" {{ old('position', $user->position) == 'Supervisor' ? 'selected' : '' }}>Supervisor</option>
                                    <option value="Leader" {{ old('position', $user->position) == 'Leader' ? 'selected' : '' }}>Leader</option>
                                    <option value="Admin" {{ old('position', $user->position) == 'Admin' ? 'selected' : '' }}>Admin (Clerk)</option>
                                    <option value="Staff" {{ old('position', $user->position) == 'Staff' ? 'selected' : '' }}>Staff / Operator</option>
                                </select>
                            </div>
                        </div>

                        <hr class="border-secondary border-opacity-25 my-4">

                        <!-- Bagian Hak Akses Sistem -->
                        <h6 class="fw-bold mb-3" style="color: var(--sidebar-link); letter-spacing: 0.5px;">3. Hak Akses (System Role)</h6>
                        <div class="mb-4">
                            <label class="form-label fw-medium">Level Akses <span class="text-danger">*</span></label>
                            
                            <div class="d-flex flex-column gap-3 mt-2">
                                <label class="p-3 border rounded-3 d-flex align-items-start gap-3 cursor-pointer" style="cursor: pointer; border-color: var(--sidebar-border) !important;">
                                    <input class="form-check-input mt-1" type="radio" name="role" value="superadmin" {{ old('role', $user->role) == 'superadmin' ? 'checked' : '' }} required>
                                    <div>
                                        <div class="fw-bold text-danger mb-1">Super Admin (Administrator Penuh)</div>
                                        <small class="text-muted">Memiliki akses penuh ke semua modul, pengaturan sistem, dan dapat mengelola hak akses pengguna lain.</small>
                                    </div>
                                </label>

                                <label class="p-3 border rounded-3 d-flex align-items-start gap-3 cursor-pointer" style="cursor: pointer; border-color: var(--sidebar-border) !important;">
                                    <input class="form-check-input mt-1" type="radio" name="role" value="admin" {{ old('role', $user->role) == 'admin' ? 'checked' : '' }} required>
                                    <div>
                                        <div class="fw-bold text-primary mb-1">Admin Modul (Manager / Supervisor)</div>
                                        <small class="text-muted">Dapat mengelola data master, membuat dan mengedit entri utama di dalam modul divisi mereka.</small>
                                    </div>
                                </label>

                                <label class="p-3 border rounded-3 d-flex align-items-start gap-3 cursor-pointer" style="cursor: pointer; border-color: var(--sidebar-border) !important;">
                                    <input class="form-check-input mt-1" type="radio" name="role" value="staff" {{ old('role', $user->role) == 'staff' ? 'checked' : (!$user->exists ? 'checked' : '') }} required>
                                    <div>
                                        <div class="fw-bold text-success mb-1">Staff / Operasional</div>
                                        <small class="text-muted">Akses terbatas untuk melihat data operasional atau menginput tugas harian sesuai divisi.</small>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-flex justify-content-end gap-3 pt-3 border-top" style="border-color: var(--sidebar-border) !important;">
                            <a href="{{ route('users.index') }}" class="btn btn-light border px-4" style="border-radius: 8px;">Batal</a>
                            <button type="submit" class="btn btn-primary px-4 shadow-sm" style="border-radius: 8px;">
                                <i class="fa-solid fa-save me-2"></i> Simpan Data Pengguna
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
