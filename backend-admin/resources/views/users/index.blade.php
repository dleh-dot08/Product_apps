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
            <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #0ea5e9 0%, #38bdf8 100%); color: white; border-radius: 12px;">
                <div class="card-body p-4 d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-uppercase fw-semibold mb-1" style="font-size: 0.75rem; letter-spacing: 0.5px; opacity: 0.85;">Divisi Aktif</h6>
                        <h3 class="mb-0 fw-bold">{{ \App\Models\User::whereNotNull('division')->distinct('division')->count('division') }}</h3>
                    </div>
                    <div class="fs-2 opacity-50">
                        <i class="fa-solid fa-layer-group"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- KPI Card 3: Super Admin -->
        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #ec4899 0%, #f472b6 100%); color: white; border-radius: 12px;">
                <div class="card-body p-4 d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-uppercase fw-semibold mb-1" style="font-size: 0.75rem; letter-spacing: 0.5px; opacity: 0.85;">Administrator</h6>
                        <h3 class="mb-0 fw-bold">{{ \App\Models\User::where('role', 'superadmin')->count() }}</h3>
                    </div>
                    <div class="fs-2 opacity-50">
                        <i class="fa-solid fa-shield-halved"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 data-card h-100" style="background: var(--topbar-bg); color: var(--bs-body-color);">
        <div class="card-header bg-transparent border-bottom-0 p-4 pb-0 d-flex justify-content-between align-items-center">
            <h5 class="fw-bold mb-0">Daftar Pengguna</h5>
            <a href="{{ route('users.create') }}" class="btn btn-primary shadow-sm" style="border-radius: 10px;">
                <i class="fa-solid fa-plus me-2"></i> Tambah Pengguna
            </a>
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

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead style="background: rgba(0,0,0,0.03);">
                        <tr>
                            <th class="py-3 px-4 border-bottom-0 rounded-start" style="color: var(--sidebar-link);">Nama</th>
                            <th class="py-3 px-4 border-bottom-0" style="color: var(--sidebar-link);">Email</th>
                            <th class="py-3 px-4 border-bottom-0" style="color: var(--sidebar-link);">Divisi</th>
                            <th class="py-3 px-4 border-bottom-0" style="color: var(--sidebar-link);">Jabatan</th>
                            <th class="py-3 px-4 border-bottom-0" style="color: var(--sidebar-link);">Role (Akses)</th>
                            <th class="py-3 px-4 border-bottom-0 rounded-end text-end" style="color: var(--sidebar-link);">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                            <tr>
                                <td class="py-3 px-4">
                                    <div class="d-flex align-items-center">
                                        <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=2563eb&color=fff&bold=true" class="rounded-circle me-3 shadow-sm" width="40" height="40" alt="{{ $user->name }}">
                                        <div class="fw-semibold">{{ $user->name }}</div>
                                    </div>
                                </td>
                                <td class="py-3 px-4 text-muted">{{ $user->email }}</td>
                                <td class="py-3 px-4">
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25 px-2 py-1 rounded-pill">
                                        {{ $user->division ?? '-' }}
                                    </span>
                                </td>
                                <td class="py-3 px-4">{{ $user->position ?? '-' }}</td>
                                <td class="py-3 px-4">
                                    @if($user->role === 'superadmin')
                                        <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-2 py-1 rounded-pill">Super Admin</span>
                                    @elseif($user->role === 'admin')
                                        <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-2 py-1 rounded-pill">Admin</span>
                                    @else
                                        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2 py-1 rounded-pill">Staff</span>
                                    @endif
                                </td>
                                <td class="py-3 px-4 text-end">
                                    <div class="d-flex gap-2 justify-content-end">
                                        <a href="{{ route('users.edit', $user->id) }}" class="btn btn-sm btn-light border" title="Edit">
                                            <i class="fa-solid fa-pen text-primary"></i>
                                        </a>
                                        <form action="{{ route('users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pengguna ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-light border" title="Hapus">
                                                <i class="fa-solid fa-trash text-danger"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">Belum ada data pengguna.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-end mt-4">
                {{ $users->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
