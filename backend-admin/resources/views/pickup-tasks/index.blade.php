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

    <div class="container-fluid px-4 py-4">
        <!-- 1. Header Banner -->
        <div class="row mb-3" style="position: sticky; top: 1rem; z-index: 1020;">
            <div class="col-12">
                <div class="dashboard-banner d-flex justify-content-between align-items-center">
                    <div style="position: absolute; top: -30px; right: -20px; width: 250px; height: 250px; background: radial-gradient(circle, rgba(251, 146, 60, 0.18) 0%, rgba(255,255,255,0) 70%); border-radius: 50%; pointer-events: none; z-index: 0;"></div>
                    <div style="position: absolute; bottom: -50px; left: 10%; width: 150px; height: 150px; background: radial-gradient(circle, rgba(37, 99, 235, 0.1) 0%, rgba(255,255,255,0) 70%); border-radius: 50%; pointer-events: none; z-index: 0;"></div>
                    
                    <div style="position: relative; z-index: 1;">
                        <div class="banner-badge">
                            <i class="fa-solid fa-clipboard-list"></i>
                            AQPA DRIVER TASKS
                        </div>
                        <h1 class="banner-title mb-2">Tugas Driver & Pickup</h1>
                    </div>

                    <div style="position: relative; z-index: 1;">
                        <button class="btn border-0 shadow-sm rounded-pill fw-bold" style="background-color: #ea580c; color: #fff; padding: 0.6rem 1.2rem;" data-bs-toggle="modal" data-bs-target="#createTaskModal">
                            <i class="fa-solid fa-plus me-1"></i> Buat Tugas Baru
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
            <div class="col-12 col-md-6 col-xl-3">
                <div class="card border-0 premium-card h-100" style="background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%); color: white;">
                    <div class="card-body p-3 d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase fw-semibold mb-1" style="font-size: 0.725rem; letter-spacing: 0.5px; opacity: 0.85;">Total Tugas</h6>
                            <h4 class="mb-0 fw-bold" style="letter-spacing: -0.5px;">{{ $totalTasks }}</h4>
                        </div>
                        <div class="premium-icon-box fs-5">
                            <i class="fa-solid fa-layer-group"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-6 col-xl-3">
                <div class="card border-0 premium-card h-100" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); color: white;">
                    <div class="card-body p-3 d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase fw-semibold mb-1" style="font-size: 0.725rem; letter-spacing: 0.5px; opacity: 0.85;">Baru (Assigned)</h6>
                            <h4 class="mb-0 fw-bold" style="letter-spacing: -0.5px;">{{ $assignedTasks }}</h4>
                        </div>
                        <div class="premium-icon-box fs-5">
                            <i class="fa-solid fa-clock"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-6 col-xl-3">
                <div class="card border-0 premium-card h-100" style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); color: white;">
                    <div class="card-body p-3 d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase fw-semibold mb-1" style="font-size: 0.725rem; letter-spacing: 0.5px; opacity: 0.85;">Sedang Jalan</h6>
                            <h4 class="mb-0 fw-bold" style="letter-spacing: -0.5px;">{{ $onRouteTasks }}</h4>
                        </div>
                        <div class="premium-icon-box fs-5">
                            <i class="fa-solid fa-truck-fast"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-6 col-xl-3">
                <div class="card border-0 premium-card h-100" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white;">
                    <div class="card-body p-3 d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase fw-semibold mb-1" style="font-size: 0.725rem; letter-spacing: 0.5px; opacity: 0.85;">Selesai (Delivered)</h6>
                            <h4 class="mb-0 fw-bold" style="letter-spacing: -0.5px;">{{ $completedTasks }}</h4>
                        </div>
                        <div class="premium-icon-box fs-5">
                            <i class="fa-solid fa-check-double"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. Tabel Tugas -->
        <div class="row mt-3 g-3">
            <div class="col-12">
                <div class="card border-0 data-card h-100 shadow-sm">
                    <div class="card-header bg-transparent border-bottom-0 p-4 pb-0 d-flex justify-content-between align-items-center">
                        <h6 class="fw-bold mb-0">Daftar Tugas Pickup & Pengiriman</h6>
                    </div>
                    <div class="card-body p-0 mt-2">
                        <div class="table-responsive px-4 pb-4">
                            <table class="table table-hover table-premium mb-0">
                                <thead>
                                    <tr>
                                        <th width="15%">No Referensi</th>
                                        <th width="20%">Tujuan (Alamat)</th>
                                        <th width="20%">Driver & Kendaraan</th>
                                        <th width="15%">Barang</th>
                                        <th width="15%">Status</th>
                                        <th width="15%" class="text-end">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($tasks as $task)
                                    <tr>
                                        <td>
                                            <span class="badge bg-light text-dark border fw-bold fs-6 mb-1">{{ $task->reference_number }}</span>
                                            <div class="text-muted small fw-semibold"><i class="fa-solid fa-tag me-1"></i>SO: {{ $task->so_number ?? '-' }}</div>
                                        </td>
                                        <td>
                                            <div class="fw-bold text-dark">{{ $task->customer_name ?? '-' }}</div>
                                            <div class="text-muted small mt-1" style="line-height: 1.3;"><i class="fa-solid fa-location-dot me-1 text-danger"></i>{{ Str::limit($task->address, 50) }}</div>
                                        </td>
                                        <td>
                                            <div class="mb-1"><i class="fa-solid fa-user-circle text-primary me-2"></i><span class="fw-semibold">{{ $task->driver->full_name ?? 'N/A' }}</span></div>
                                            <div><i class="fa-solid fa-truck text-secondary me-2"></i><span class="small text-muted">{{ $task->vehicle->plate_number ?? 'N/A' }}</span></div>
                                        </td>
                                        <td>
                                            <div class="fw-semibold text-dark">{{ $task->item_name }}</div>
                                            <span class="badge rounded-pill bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25 mt-1 px-2 py-1">Qty: {{ $task->quantity ?? '-' }}</span>
                                        </td>
                                        <td>
                                            @php
                                                $badges = [
                                                    'assigned' => ['bg' => 'bg-warning', 'text' => 'text-dark', 'icon' => 'fa-clock'],
                                                    'on_route' => ['bg' => 'bg-info', 'text' => 'text-dark', 'icon' => 'fa-truck-fast'],
                                                    'arrived' => ['bg' => 'bg-primary', 'text' => 'text-white', 'icon' => 'fa-map-marker-alt'],
                                                    'delivered' => ['bg' => 'bg-success', 'text' => 'text-white', 'icon' => 'fa-check'],
                                                    'failed' => ['bg' => 'bg-danger', 'text' => 'text-white', 'icon' => 'fa-xmark'],
                                                    'cancelled' => ['bg' => 'bg-secondary', 'text' => 'text-white', 'icon' => 'fa-ban']
                                                ];
                                                $badgeStyle = $badges[$task->status] ?? ['bg' => 'bg-secondary', 'text' => 'text-white', 'icon' => 'fa-circle'];
                                            @endphp
                                            <span class="badge rounded-pill {{ $badgeStyle['bg'] }} {{ $badgeStyle['text'] }} px-3 py-2 text-uppercase shadow-sm border border-light" style="font-size: 0.725rem; letter-spacing: 0.5px;">
                                                <i class="fa-solid {{ $badgeStyle['icon'] }} me-1"></i> {{ str_replace('_', ' ', $task->status) }}
                                            </span>
                                        </td>
                                        <td class="text-end">
                                            <button class="btn btn-sm btn-light border shadow-sm text-primary rounded-circle" style="width: 32px; height: 32px;" data-bs-toggle="modal" data-bs-target="#editModal{{ $task->id }}" title="Update Status">
                                                <i class="fa-solid fa-pen-to-square"></i>
                                            </button>
                                            <form action="{{ route('pickup-tasks.destroy', $task->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus tugas ini?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-light border shadow-sm text-danger rounded-circle ms-1" style="width: 32px; height: 32px;" title="Hapus">
                                                    <i class="fa-solid fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>

                                    <!-- Modal Update Status -->
                                    <div class="modal fade" id="editModal{{ $task->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">
                                                <form action="{{ route('pickup-tasks.update', $task->id) }}" method="POST" class="w-100">
                                                    @csrf
                                                    @method('PUT')
                                                    <div class="modal-header bg-light border-bottom-0 p-4">
                                                        <h5 class="modal-title fw-bold text-dark">Update Status Tugas</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body p-4 text-start">
                                                        <div class="mb-3">
                                                            <label class="form-label fw-semibold small text-muted text-uppercase letter-spacing-1">Status Saat Ini</label>
                                                            <select name="status" class="form-select form-select-lg border-light-subtle shadow-sm bg-body-tertiary">
                                                                <option value="assigned" {{ $task->status == 'assigned' ? 'selected' : '' }}>Assigned (Baru)</option>
                                                                <option value="on_route" {{ $task->status == 'on_route' ? 'selected' : '' }}>On Route (Sedang Jalan)</option>
                                                                <option value="arrived" {{ $task->status == 'arrived' ? 'selected' : '' }}>Arrived (Telah Sampai)</option>
                                                                <option value="delivered" {{ $task->status == 'delivered' ? 'selected' : '' }}>Delivered (Selesai Dikirim)</option>
                                                                <option value="failed" {{ $task->status == 'failed' ? 'selected' : '' }}>Failed (Gagal)</option>
                                                                <option value="cancelled" {{ $task->status == 'cancelled' ? 'selected' : '' }}>Cancelled (Dibatalkan)</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer border-top-0 p-4 pt-0 bg-white">
                                                        <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                                                        <button type="submit" class="btn btn-primary rounded-pill px-4 shadow-sm" style="background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%); border: none;">Simpan Perubahan</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-5 text-muted">
                                            <i class="fa-solid fa-box-open fs-1 mb-3 text-light" style="opacity: 0.5;"></i>
                                            <p class="mb-0 fw-semibold">Belum ada data tugas driver.</p>
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

        <!-- Modal Create Task -->
        <div class="modal fade" id="createTaskModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <form action="{{ route('pickup-tasks.store') }}" method="POST" class="w-100">
                    @csrf
                    <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">
                        <div class="modal-header border-bottom-0 p-4" style="background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);">
                            <h5 class="modal-title fw-bold text-dark">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%) !important;">
                                        <i class="fa-solid fa-plus fs-6"></i>
                                    </div>
                                    Buat Tugas Driver Baru
                                </div>
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body p-4 bg-white">
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold small text-muted text-uppercase mb-2" style="letter-spacing: 0.5px;">Pilih Driver <span class="text-danger">*</span></label>
                                    <select name="driver_id" class="form-select form-select-lg border-light-subtle shadow-sm bg-body-tertiary" required>
                                        <option value="">-- Pilih Driver --</option>
                                        @foreach($drivers as $driver)
                                            <option value="{{ $driver->id }}">{{ $driver->full_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold small text-muted text-uppercase mb-2" style="letter-spacing: 0.5px;">Pilih Kendaraan <span class="text-danger">*</span></label>
                                    <select name="vehicle_id" class="form-select form-select-lg border-light-subtle shadow-sm bg-body-tertiary" required>
                                        <option value="">-- Pilih Kendaraan --</option>
                                        @foreach($vehicles as $vehicle)
                                            <option value="{{ $vehicle->id }}">{{ $vehicle->plate_number }} - {{ $vehicle->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold small text-muted text-uppercase mb-2" style="letter-spacing: 0.5px;">Nomor SO (Opsional)</label>
                                    <input type="text" name="so_number" class="form-control form-control-lg border-light-subtle shadow-sm bg-body-tertiary" placeholder="SO-2026...">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold small text-muted text-uppercase mb-2" style="letter-spacing: 0.5px;">Nama Customer</label>
                                    <input type="text" name="customer_name" class="form-control form-control-lg border-light-subtle shadow-sm bg-body-tertiary" placeholder="PT Contoh Maju">
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-bold small text-muted text-uppercase mb-2" style="letter-spacing: 0.5px;">Alamat Tujuan <span class="text-danger">*</span></label>
                                    <textarea name="address" class="form-control border-light-subtle shadow-sm bg-body-tertiary" rows="2" required placeholder="Alamat lengkap pengiriman..."></textarea>
                                </div>
                                <div class="col-md-8">
                                    <label class="form-label fw-bold small text-muted text-uppercase mb-2" style="letter-spacing: 0.5px;">Nama Barang <span class="text-danger">*</span></label>
                                    <input type="text" name="item_name" class="form-control form-control-lg border-light-subtle shadow-sm bg-body-tertiary" required placeholder="Deskripsi barang yang dibawa">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-bold small text-muted text-uppercase mb-2" style="letter-spacing: 0.5px;">Kuantitas</label>
                                    <input type="number" name="quantity" class="form-control form-control-lg border-light-subtle shadow-sm bg-body-tertiary" placeholder="10">
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-bold small text-muted text-uppercase mb-2" style="letter-spacing: 0.5px;">Catatan Tambahan</label>
                                    <textarea name="remarks" class="form-control border-light-subtle shadow-sm bg-body-tertiary" rows="2" placeholder="Catatan untuk driver (opsional)"></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer border-top-0 p-4 bg-white">
                            <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary rounded-pill px-4 shadow-sm" style="background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%); border: none;">Simpan Tugas</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
