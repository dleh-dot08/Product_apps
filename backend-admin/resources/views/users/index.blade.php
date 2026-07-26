<x-app-layout>
    <x-slot name="header">
        Manajemen Pengguna
    </x-slot>

    <div class="row g-3 mb-4">
        <!-- KPI Card 1: Total Pengguna -->
        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #4f46e5 0%, #6366f1 100%); color: white; border-radius: 12px;">
                <div class="card-body p-4 d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-uppercase fw-semibold mb-1" style="font-size: 0.75rem; letter-spacing: 0.5px; opacity: 0.85;">Total Pengguna</h6>
                        <h3 class="mb-0 fw-bold">{{ \App\Models\User::count() }}</h3>
                    </div>
                    <div class="fs-2 opacity-50">
                        <i class="fa-solid fa-users"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- KPI Card 2: Divisi Aktif -->
        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #8b5cf6 0%, #a855f7 100%); color: white; border-radius: 12px; cursor: pointer; transition: transform 0.2s;" data-bs-toggle="modal" data-bs-target="#divisionModal" onmouseover="this.style.transform='scale(1.02)'" onmouseout="this.style.transform='scale(1)'">
                <div class="card-body p-4 d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-uppercase fw-semibold mb-1" style="font-size: 0.75rem; letter-spacing: 0.5px; opacity: 0.85;">Divisi Aktif</h6>
                        <h3 class="mb-0 fw-bold">{{ \App\Models\Division::count() }}</h3>
                    </div>
                    <div class="fs-2 opacity-50">
                        <i class="fa-solid fa-layer-group"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- KPI Card 3: Roles -->
        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #ec4899 0%, #f472b6 100%); color: white; border-radius: 12px; cursor: pointer; transition: transform 0.2s;" data-bs-toggle="modal" data-bs-target="#roleModal" onmouseover="this.style.transform='scale(1.02)'" onmouseout="this.style.transform='scale(1)'">
                <div class="card-body p-4 d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-uppercase fw-semibold mb-1" style="font-size: 0.75rem; letter-spacing: 0.5px; opacity: 0.85;">Roles Sistem</h6>
                        <h3 class="mb-0 fw-bold">{{ \App\Models\Role::count() }}</h3>
                    </div>
                    <div class="fs-2 opacity-50">
                        <i class="fa-solid fa-shield-halved"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 data-card h-100 mb-4" style="background: var(--topbar-bg); color: var(--bs-body-color);">
        <div class="card-header bg-transparent border-bottom-0 p-4 pb-0 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <h5 class="fw-bold mb-0">Daftar Pengguna</h5>
            
            <div class="d-flex flex-column flex-md-row gap-3">
                <!-- Search Form -->
                <form action="{{ route('users.index') }}" method="GET" class="d-flex">
                    <div class="input-group">
                        <input type="text" name="search" class="form-control" placeholder="Cari nama atau email..." value="{{ $search ?? '' }}" style="border-radius: 8px 0 0 8px;">
                        <button class="btn btn-outline-secondary bg-white border-start-0" type="submit" style="border-radius: 0 8px 8px 0;">
                            <i class="fa-solid fa-search"></i>
                        </button>
                    </div>
                </form>

                <button type="button" class="btn btn-primary shadow-sm text-nowrap" style="border-radius: 10px;" onclick="openUserModal(false)">
                    <i class="fa-solid fa-plus me-2"></i> Tambah Pengguna
                </button>
            </div>
        </div>
        <div class="card-body p-4">
            
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fa-solid fa-triangle-exclamation me-2"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <style>
                .custom-table { border-collapse: separate; border-spacing: 0 12px; }
                .custom-table thead th { border: none; font-size: 0.75rem; font-weight: 600; letter-spacing: 0.5px; opacity: 0.7; padding-bottom: 0; }
                .custom-table tbody tr { background: var(--sidebar-bg); box-shadow: 0 2px 6px rgba(0,0,0,0.02); transition: all 0.3s ease; }
                .custom-table tbody tr:hover { transform: translateY(-2px); box-shadow: 0 6px 12px rgba(0,0,0,0.05); }
                .custom-table tbody td { border: none; padding: 1rem 1.5rem; }
                .custom-table tbody td:first-child { border-top-left-radius: 12px; border-bottom-left-radius: 12px; }
                .custom-table tbody td:last-child { border-top-right-radius: 12px; border-bottom-right-radius: 12px; }
            </style>
            
            <div class="table-responsive px-1 pb-3">
                <table class="table custom-table align-middle mb-0">
                    <thead class="text-uppercase">
                        <tr>
                            <th class="px-4" style="color: var(--sidebar-link);">Pengguna</th>
                            <th class="px-4" style="color: var(--sidebar-link);">Email</th>
                            <th class="px-4" style="color: var(--sidebar-link);">Divisi</th>
                            <th class="px-4" style="color: var(--sidebar-link);">Hak Akses</th>
                            <th class="px-4 text-end" style="color: var(--sidebar-link);">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=6366f1&color=fff&bold=true&size=128" class="rounded-circle me-3 shadow-sm border border-2 border-white" width="45" height="45" alt="{{ $user->name }}">
                                        <div>
                                            <div class="fw-bold mb-1" style="font-size: 0.95rem;">{{ $user->name }}</div>
                                            <div class="small opacity-50">Bergabung {{ $user->created_at->format('M Y') }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-muted" style="font-size: 0.9rem;">
                                    <i class="fa-regular fa-envelope me-2 opacity-50"></i>{{ $user->email }}
                                </td>
                                <td>
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25 px-3 py-2 rounded-pill fw-medium" style="font-size: 0.8rem;">
                                        <i class="fa-solid fa-layer-group me-1 opacity-50"></i> {{ $user->division->name ?? 'Belum Ditugaskan' }}
                                    </span>
                                </td>
                                <td>
                                    @php
                                        $roleName = $user->role->name ?? '-';
                                        $badgeClass = 'bg-success text-success border-success';
                                        $icon = 'fa-user-tag';
                                        if (stripos($roleName, 'Admin') !== false) {
                                            $badgeClass = 'bg-primary text-primary border-primary';
                                            $icon = 'fa-user-gear';
                                        }
                                        if (stripos($roleName, 'Super Admin') !== false) {
                                            $badgeClass = 'bg-danger text-danger border-danger';
                                            $icon = 'fa-shield-halved';
                                        }
                                    @endphp
                                    <span class="badge {{ $badgeClass }} bg-opacity-10 border border-opacity-25 px-3 py-2 rounded-pill fw-medium" style="font-size: 0.8rem;">
                                        <i class="fa-solid {{ $icon }} me-1 opacity-75"></i> {{ $roleName }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <div class="d-flex gap-2 justify-content-end">
                                        <button type="button" onclick="openUserModal(true, {{ json_encode(['id' => $user->id, 'name' => $user->name, 'email' => $user->email, 'division_id' => $user->division_id, 'role_id' => $user->role_id]) }})" class="btn btn-sm btn-light border rounded-circle d-flex align-items-center justify-content-center" style="width: 35px; height: 35px; transition: all 0.2s;" title="Edit Pengguna" onmouseover="this.classList.replace('btn-light', 'btn-primary'); this.classList.remove('border')" onmouseout="this.classList.replace('btn-primary', 'btn-light'); this.classList.add('border')">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </button>
                                        <form action="{{ route('users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pengguna ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-light border rounded-circle d-flex align-items-center justify-content-center" style="width: 35px; height: 35px; transition: all 0.2s;" title="Hapus Pengguna" onmouseover="this.classList.replace('btn-light', 'btn-danger'); this.classList.remove('border')" onmouseout="this.classList.replace('btn-danger', 'btn-light'); this.classList.add('border')">
                                                <i class="fa-solid fa-trash-can"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-5">
                                    <div class="text-muted mb-3"><i class="fa-solid fa-users-slash fa-3x opacity-25"></i></div>
                                    <h6 class="fw-semibold">Belum ada pengguna</h6>
                                    <p class="small opacity-75 mb-0">Tambahkan pengguna baru untuk mulai mengelola akses sistem.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-end mt-4">
                {{ $users->links() }}
            </div>
        
        <hr class="border-secondary border-opacity-25 my-5">

        <!-- Table Hak Akses -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h5 class="fw-bold mb-1">Matriks Hak Akses (Privileges)</h5>
                <p class="text-muted small mb-0">Atur izin akses modul dan fitur berdasarkan Role di bawah ini.</p>
            </div>
            <div>
                <button type="button" class="btn btn-sm btn-outline-primary" style="border-radius: 8px;">
                    <i class="fa-solid fa-floppy-disk me-1"></i> Simpan Perubahan
                </button>
            </div>
        </div>

        <div class="table-responsive px-1 pb-3">
            <table class="table custom-table align-middle mb-0">
                <thead class="text-uppercase">
                    <tr>
                        <th class="px-4" style="color: var(--sidebar-link); width: 250px;">Modul / Fitur</th>
                        @foreach($roles as $role)
                            <th class="px-4 text-center" style="color: var(--sidebar-link);">{{ $role->name }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @php
                        $dummyModules = [
                            ['name' => 'Dashboard & Analytics', 'icon' => 'fa-chart-pie', 'color' => 'text-primary'],
                            ['name' => 'Manajemen Pengguna', 'icon' => 'fa-users', 'color' => 'text-success'],
                            ['name' => 'Accounting & Keuangan', 'icon' => 'fa-wallet', 'color' => 'text-warning'],
                            ['name' => 'Inventory & Gudang', 'icon' => 'fa-boxes-stacked', 'color' => 'text-info'],
                            ['name' => 'Purchasing (Pembelian)', 'icon' => 'fa-cart-shopping', 'color' => 'text-danger'],
                        ];
                    @endphp
                    @foreach($dummyModules as $module)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center fw-medium">
                                <div class="bg-light rounded d-flex align-items-center justify-content-center me-3" style="width: 36px; height: 36px;">
                                    <i class="fa-solid {{ $module['icon'] }} {{ $module['color'] }}"></i>
                                </div>
                                {{ $module['name'] }}
                            </div>
                        </td>
                        @foreach($roles as $role)
                        <td class="text-center">
                            @php
                                // Logic sederhana untuk dummy data (Super Admin selalu on, sisanya random/tergantung)
                                $isChecked = ($role->name == 'Super Admin' || rand(0,1) == 1) ? 'checked' : '';
                                $switchId = 'switch_' . Str::slug($module['name']) . '_' . $role->id;
                            @endphp
                            <div class="form-check form-switch d-flex justify-content-center mb-0">
                                <input class="form-check-input shadow-sm" type="checkbox" role="switch" id="{{ $switchId }}" style="width: 2.5em; height: 1.25em; cursor: pointer;" {{ $isChecked }}>
                            </div>
                        </td>
                        @endforeach
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination Hak Akses (Dummy) -->
        <div class="d-flex justify-content-between align-items-center mt-4">
            <div class="text-muted small">
                Menampilkan 1 hingga 5 dari 12 modul
            </div>
            <ul class="pagination pagination-sm mb-0">
                <li class="page-item disabled">
                    <a class="page-link shadow-sm border-0" href="#" tabindex="-1" aria-disabled="true">Previous</a>
                </li>
                <li class="page-item active" aria-current="page">
                    <a class="page-link shadow-sm border-0" href="#">1</a>
                </li>
                <li class="page-item"><a class="page-link shadow-sm border-0 text-secondary" href="#">2</a></li>
                <li class="page-item"><a class="page-link shadow-sm border-0 text-secondary" href="#">3</a></li>
                <li class="page-item">
                    <a class="page-link shadow-sm border-0 text-secondary" href="#">Next</a>
                </li>
            </ul>
        </div>
    </div>

    @include('users.partials._user_modal')
    @include('users.partials._divisions_modal')
    @include('users.partials._roles_modal')

</x-app-layout>
