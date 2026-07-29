<x-app-layout>

    <!-- Custom CSS untuk Header Banner & Card Dashboard (Premium Design) -->
    <style>
        .dashboard-banner {
            border-radius: 1rem;
            padding: 1rem;
            border: 1px solid rgba(251, 146, 60, 0.2);
            transition: all 0.3s ease;
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            position: relative;
            overflow: hidden;
        }
        html:not([data-bs-theme="dark"]) .dashboard-banner {
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.85) 0%, rgba(255, 247, 237, 0.85) 60%, rgba(255, 237, 213, 0.85) 100%);
            box-shadow: 0 4px 20px -2px rgba(251, 146, 60, 0.15);
        }
        html[data-bs-theme="dark"] .dashboard-banner {
            background: linear-gradient(135deg, rgba(13, 15, 18, 0.75) 0%, rgba(23, 25, 30, 0.75) 100%);
            border-color: rgba(255, 255, 255, 0.1);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
        }
        .banner-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.72rem;
            font-weight: 800;
            letter-spacing: 0.09em;
            text-transform: uppercase;
            margin-bottom: 0.35rem;
        }
        html:not([data-bs-theme="dark"]) .banner-badge {
            background-color: rgba(251, 146, 60, 0.15);
            color: #ea580c;
            border: 1px solid rgba(234, 88, 12, 0.2);
        }
        html[data-bs-theme="dark"] .banner-badge {
            background-color: rgba(249, 115, 22, 0.2);
            color: #fb923c;
            border: 1px solid rgba(249, 115, 22, 0.3);
        }
        .banner-title {
            font-size: clamp(1.35rem, 2.2vw, 1.9rem);
            font-weight: 800;
            letter-spacing: -0.03em;
            margin: 0;
        }
        html:not([data-bs-theme="dark"]) .banner-title { color: #0f172a; }
        html[data-bs-theme="dark"] .banner-title { color: #f8fafc; }

        .premium-card {
            border-radius: 14px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            overflow: hidden;
            position: relative;
            z-index: 1;
        }
        .premium-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 20px -8px rgba(0, 0, 0, 0.25);
        }
        .premium-icon-box {
            width: 44px;
            height: 44px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 10px;
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(4px);
        }

        .data-card {
            border-radius: 14px;
            transition: all 0.3s ease;
        }
        html[data-bs-theme="dark"] .data-card {
            background-color: #0d0f12 !important;
            border: 1px solid rgba(255, 255, 255, 0.08) !important;
            color: #f8fafc !important;
        }
        html:not([data-bs-theme="dark"]) .data-card {
            background-color: #ffffff !important;
            border: 1px solid #e2e8f0 !important;
        }

        .table-premium th {
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            font-weight: 700;
            color: #64748b;
            border-bottom-width: 2px;
            padding-top: 1rem;
            padding-bottom: 1rem;
        }
        .table-premium td {
            vertical-align: middle;
            padding-top: 1rem;
            padding-bottom: 1rem;
            color: #334155;
        }
        html[data-bs-theme="dark"] .table-premium th { color: #94a3b8; border-color: rgba(255,255,255,0.1); }
        html[data-bs-theme="dark"] .table-premium td { color: #cbd5e1; border-color: rgba(255,255,255,0.1); }
    </style>

    <!-- 1. Header Banner -->
    <div class="row mb-3" style="position: sticky; top: 1rem; z-index: 1020;">
        <div class="col-12">
            <div class="dashboard-banner d-flex justify-content-between align-items-center">
                <!-- Decorative Background Element -->
                <div style="position: absolute; top: -30px; right: -20px; width: 250px; height: 250px; background: radial-gradient(circle, rgba(251, 146, 60, 0.18) 0%, rgba(255,255,255,0) 70%); border-radius: 50%; pointer-events: none; z-index: 0;"></div>
                <div style="position: absolute; bottom: -50px; left: 10%; width: 150px; height: 150px; background: radial-gradient(circle, rgba(37, 99, 235, 0.1) 0%, rgba(255,255,255,0) 70%); border-radius: 50%; pointer-events: none; z-index: 0;"></div>
                
                <div style="position: relative; z-index: 1;">
                    <div class="banner-badge">
                        <i class="fa-solid fa-truck"></i>
                        AQPA VEHICLES
                    </div>
                    <h1 class="banner-title mb-2">Data Kendaraan Operasional</h1>
                </div>

                <div style="position: relative; z-index: 1;">
                    <button class="btn border-0 shadow-sm rounded-pill fw-bold" style="background-color: #ea580c; color: #fff; padding: 0.6rem 1.2rem;" data-bs-toggle="modal" data-bs-target="#createVehicleModal">
                        <i class="fa-solid fa-plus me-1"></i> Tambah Kendaraan
                    </button>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert" style="border-radius: 14px;">
        <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <!-- 2. Ringkasan Kartu Statistik -->
    <div class="row g-3">
        <!-- Card 1 -->
        <div class="col-12 col-md-6 col-xl-3">
            <div class="card border-0 premium-card h-100" style="background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%); color: white;">
                <div class="card-body p-3 d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-uppercase fw-semibold mb-1" style="font-size: 0.725rem; letter-spacing: 0.5px; opacity: 0.85;">Total Kendaraan</h6>
                        <h4 class="mb-0 fw-bold" style="letter-spacing: -0.5px;">{{ $totalVehicles }}</h4>
                    </div>
                    <div class="premium-icon-box fs-5">
                        <i class="fa-solid fa-truck-moving"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 2 -->
        <div class="col-12 col-md-6 col-xl-3">
            <div class="card border-0 premium-card h-100" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white;">
                <div class="card-body p-3 d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-uppercase fw-semibold mb-1" style="font-size: 0.725rem; letter-spacing: 0.5px; opacity: 0.85;">Kendaraan Aktif</h6>
                        <h4 class="mb-0 fw-bold" style="letter-spacing: -0.5px;">{{ $activeVehicles }}</h4>
                    </div>
                    <div class="premium-icon-box fs-5">
                        <i class="fa-solid fa-check-circle"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 3 -->
        <div class="col-12 col-md-6 col-xl-3">
            <div class="card border-0 premium-card h-100" style="background: linear-gradient(135deg, #ea580c 0%, #c2410c 100%); color: white;">
                <div class="card-body p-3 d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-uppercase fw-semibold mb-1" style="font-size: 0.725rem; letter-spacing: 0.5px; opacity: 0.85;">Non-Aktif</h6>
                        <h4 class="mb-0 fw-bold" style="letter-spacing: -0.5px;">{{ $inactiveVehicles }}</h4>
                    </div>
                    <div class="premium-icon-box fs-5">
                        <i class="fa-solid fa-ban"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 4 -->
        <div class="col-12 col-md-6 col-xl-3">
            <div class="card border-0 premium-card h-100" style="background: linear-gradient(135deg, #8b5cf6 0%, #6d28d9 100%); color: white;">
                <div class="card-body p-3 d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-uppercase fw-semibold mb-1" style="font-size: 0.725rem; letter-spacing: 0.5px; opacity: 0.85;">Rata-rata KM/L</h6>
                        <h4 class="mb-0 fw-bold" style="letter-spacing: -0.5px;">{{ number_format($avgKmPerLiter, 1) }}</h4>
                    </div>
                    <div class="premium-icon-box fs-5">
                        <i class="fa-solid fa-gas-pump"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 3. Tabel Kendaraan -->
    <div class="row mt-3 g-3">
        <div class="col-12">
            <div class="card border-0 data-card h-100 shadow-sm">
                <div class="card-header bg-transparent border-bottom-0 p-4 pb-0 d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold mb-0">Daftar Inventaris Kendaraan</h6>
                </div>
                <div class="card-body p-0 mt-2">
                    <div class="table-responsive px-4 pb-4">
                        <table class="table table-hover table-premium mb-0">
                            <thead>
                                <tr>
                                    <th width="5%">No</th>
                                    <th width="15%">Plat Nomor</th>
                                    <th width="25%">Nama Kendaraan</th>
                                    <th width="15%">Harga BBM/L</th>
                                    <th width="15%">Konsumsi BBM</th>
                                    <th width="10%">Status</th>
                                    <th width="15%" class="text-end">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($vehicles as $index => $v)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td><span class="badge bg-light text-dark border fw-bold fs-6">{{ $v->plate_number }}</span></td>
                                    <td class="fw-semibold">{{ $v->name }}</td>
                                    <td><span class="text-muted small">Rp</span> {{ number_format($v->fuel_price_per_liter, 0, ',', '.') }}</td>
                                    <td>{{ number_format($v->km_per_liter, 1, ',', '.') }} <span class="text-muted small">KM/L</span></td>
                                    <td>
                                        @if($v->active)
                                            <span class="badge rounded-pill bg-success bg-opacity-10 text-success px-3 border border-success border-opacity-25"><i class="fa-solid fa-check me-1"></i> Aktif</span>
                                        @else
                                            <span class="badge rounded-pill bg-danger bg-opacity-10 text-danger px-3 border border-danger border-opacity-25"><i class="fa-solid fa-xmark me-1"></i> Non-Aktif</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        <button class="btn btn-sm btn-light border shadow-sm text-primary rounded-circle" style="width: 32px; height: 32px;" data-bs-toggle="modal" data-bs-target="#editVehicleModal{{ $v->id }}" title="Edit">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </button>
                                        @if($v->active)
                                        <form action="{{ route('vehicles.destroy', $v->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menonaktifkan kendaraan ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-light border shadow-sm text-danger rounded-circle ms-1" style="width: 32px; height: 32px;" title="Non-aktifkan">
                                                <i class="fa-solid fa-power-off"></i>
                                            </button>
                                        </form>
                                        @endif
                                    </td>
                                </tr>
                                
                                <!-- Edit Modal -->
                                <div class="modal fade" id="editVehicleModal{{ $v->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <form action="{{ route('vehicles.update', $v->id) }}" method="POST" class="w-100">
                                            @csrf
                                            @method('PUT')
                                            <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">
                                                <div class="modal-header bg-light border-bottom-0 p-4">
                                                    <h5 class="modal-title fw-bold">Edit Kendaraan</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body p-4 text-start">
                                                    <div class="mb-3">
                                                        <label class="form-label fw-semibold small text-muted text-uppercase letter-spacing-1">Plat Nomor</label>
                                                        <input type="text" name="plate_number" class="form-control form-control-lg border-light-subtle shadow-sm bg-body-tertiary" value="{{ $v->plate_number }}" required style="text-transform: uppercase;">
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label fw-semibold small text-muted text-uppercase letter-spacing-1">Nama Kendaraan</label>
                                                        <input type="text" name="name" class="form-control form-control-lg border-light-subtle shadow-sm bg-body-tertiary" value="{{ $v->name }}" required>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-6 mb-3">
                                                            <label class="form-label fw-semibold small text-muted text-uppercase letter-spacing-1">Harga BBM (Rp)</label>
                                                            <input type="number" step="1" min="1" name="fuel_price_per_liter" class="form-control form-control-lg border-light-subtle shadow-sm bg-body-tertiary" value="{{ (int)$v->fuel_price_per_liter }}" required>
                                                        </div>
                                                        <div class="col-md-6 mb-3">
                                                            <label class="form-label fw-semibold small text-muted text-uppercase letter-spacing-1">Konsumsi (KM/L)</label>
                                                            <input type="number" step="0.1" min="0.1" name="km_per_liter" class="form-control form-control-lg border-light-subtle shadow-sm bg-body-tertiary" value="{{ $v->km_per_liter }}" required>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer border-top-0 p-4 pt-0 bg-white">
                                                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                                                    <button type="submit" class="btn btn-primary rounded-pill px-4 shadow-sm" style="background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%); border: none;">Simpan Perubahan</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5 text-muted">
                                        <i class="fa-solid fa-truck-ramp-box fs-1 mb-3 text-light"></i>
                                        <p class="mb-0 fw-semibold">Belum ada data kendaraan.</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Create Modal -->
    <div class="modal fade" id="createVehicleModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form action="{{ route('vehicles.store') }}" method="POST" class="w-100">
                @csrf
                <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">
                    <div class="modal-header border-bottom-0 p-4" style="background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);">
                        <h5 class="modal-title fw-bold text-dark">
                            <div class="d-flex align-items-center gap-2">
                                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%) !important;">
                                    <i class="fa-solid fa-plus fs-6"></i>
                                </div>
                                Tambah Kendaraan
                            </div>
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4 bg-white">
                        <div class="mb-4">
                            <label class="form-label fw-bold small text-muted text-uppercase mb-2" style="letter-spacing: 0.5px;">Plat Nomor</label>
                            <input type="text" name="plate_number" class="form-control form-control-lg border-light-subtle shadow-sm bg-body-tertiary" placeholder="Contoh: B 1234 ABC" required style="text-transform: uppercase;">
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-bold small text-muted text-uppercase mb-2" style="letter-spacing: 0.5px;">Nama / Tipe Kendaraan</label>
                            <input type="text" name="name" class="form-control form-control-lg border-light-subtle shadow-sm bg-body-tertiary" placeholder="Contoh: Mitsubishi Colt Diesel" required>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-muted text-uppercase mb-2" style="letter-spacing: 0.5px;">Harga BBM (Rp)</label>
                                <div class="input-group input-group-lg shadow-sm border-light-subtle">
                                    <span class="input-group-text bg-light text-muted border-light-subtle">Rp</span>
                                    <input type="number" step="1" min="1" name="fuel_price_per_liter" class="form-control border-light-subtle bg-body-tertiary" placeholder="10000" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-muted text-uppercase mb-2" style="letter-spacing: 0.5px;">Konsumsi (KM/L)</label>
                                <div class="input-group input-group-lg shadow-sm border-light-subtle">
                                    <input type="number" step="0.1" min="0.1" name="km_per_liter" class="form-control border-light-subtle bg-body-tertiary" placeholder="8.5" required>
                                    <span class="input-group-text bg-light text-muted border-light-subtle">KM</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-top-0 p-4 bg-white">
                        <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary rounded-pill px-4 shadow-sm" style="background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%); border: none;">Simpan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
