<x-app-layout>
    <style>
        .dashboard-banner {
            border-radius: 1rem;
            padding: 1.25rem 1.5rem;
            border: 1px solid rgba(79, 70, 229, 0.2);
            transition: all 0.3s ease;
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            position: relative;
            overflow: hidden;
        }
        html:not([data-bs-theme="dark"]) .dashboard-banner {
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.95) 0%, rgba(238, 242, 255, 0.85) 60%, rgba(224, 231, 255, 0.85) 100%);
            box-shadow: 0 4px 20px -2px rgba(99, 102, 241, 0.15);
        }
        html[data-bs-theme="dark"] .dashboard-banner {
            background: linear-gradient(135deg, rgba(15, 23, 42, 0.85) 0%, rgba(30, 27, 75, 0.85) 100%);
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
            background-color: rgba(99, 102, 241, 0.15);
            color: #4f46e5;
            border: 1px solid rgba(79, 70, 229, 0.2);
        }
        html[data-bs-theme="dark"] .banner-badge {
            background-color: rgba(99, 102, 241, 0.25);
            color: #a5b4fc;
            border: 1px solid rgba(129, 140, 248, 0.3);
        }
        .banner-title {
            font-size: clamp(1.35rem, 2.2vw, 1.85rem);
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
            font-size: 0.72rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            font-weight: 700;
            color: #64748b;
            border-bottom-width: 2px;
            padding-top: 0.9rem;
            padding-bottom: 0.9rem;
        }

        /* Modal Styles */
        .modal-hero-header {
            background: linear-gradient(135deg, #4338ca 0%, #312e81 100%);
            border-bottom: 0;
            position: relative;
            padding: 1.25rem 1.75rem;
        }
        .modal-hero-header.warning-header {
            background: linear-gradient(135deg, #d97706 0%, #92400e 100%);
        }
        .modal-custom-body {
            background-color: #f8fafc;
        }
        html[data-bs-theme="dark"] .modal-custom-body {
            background-color: #0b0f19 !important;
        }
        .modal-custom-footer {
            background-color: #ffffff;
            border-top: 1px solid #e2e8f0;
        }
        html[data-bs-theme="dark"] .modal-custom-footer {
            background-color: #0f172a !important;
            border-top-color: rgba(255, 255, 255, 0.08) !important;
        }
        .modal-section-card {
            border-radius: 1rem;
            border: 1px solid #e2e8f0;
            background-color: #ffffff;
            transition: all 0.2s ease;
            box-shadow: 0 1px 3px rgba(0,0,0,0.03);
            margin-bottom: 0.85rem;
        }
        html[data-bs-theme="dark"] .modal-section-card {
            background-color: #131b2e !important;
            border-color: rgba(255,255,255,0.08) !important;
            box-shadow: 0 4px 12px rgba(0,0,0,0.2) !important;
        }
        .modal-section-card.highlight-card {
            border: 1.5px solid #a5b4fc;
            background: linear-gradient(180deg, #f5f7ff 0%, #ffffff 100%);
        }
        html[data-bs-theme="dark"] .modal-section-card.highlight-card {
            border-color: rgba(129, 140, 248, 0.4) !important;
            background: linear-gradient(180deg, rgba(30, 27, 75, 0.4) 0%, rgba(19, 27, 46, 1) 100%) !important;
        }
        .modal-section-header {
            padding: 0.65rem 1.15rem;
            border-bottom: 1px solid #f1f5f9;
            background: rgba(248, 250, 252, 0.9);
            border-top-left-radius: 1rem;
            border-top-right-radius: 1rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        html[data-bs-theme="dark"] .modal-section-header {
            background: rgba(255,255,255,0.03) !important;
            border-bottom-color: rgba(255,255,255,0.06) !important;
        }
        .modal-section-body {
            padding: 1.15rem;
        }
        .form-floating-label {
            font-size: 0.72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            color: #64748b;
            margin-bottom: 0.35rem;
            display: flex;
            align-items: center;
            gap: 0.35rem;
        }
        html[data-bs-theme="dark"] .form-floating-label {
            color: #94a3b8;
        }
        .form-control-custom {
            border-radius: 0.65rem;
            border: 1px solid #cbd5e1;
            padding: 0.52rem 0.85rem;
            font-size: 0.875rem;
            background-color: #ffffff;
            color: #0f172a;
            transition: all 0.2s;
        }
        html[data-bs-theme="dark"] .form-control-custom {
            background-color: #1e293b !important;
            border-color: #334155 !important;
            color: #f8fafc !important;
        }
        .form-control-custom:focus {
            border-color: #6366f1;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.18);
        }
        .tracking-preview-box {
            border-radius: 0.65rem;
            background: #f1f5f9;
            border: 1px dashed #cbd5e1;
            padding: 0.5rem 0.85rem;
            font-family: var(--bs-font-monospace);
            font-size: 0.75rem;
            word-break: break-all;
        }
        html[data-bs-theme="dark"] .tracking-preview-box {
            background: #0f172a !important;
            border-color: rgba(255,255,255,0.12) !important;
            color: #cbd5e1 !important;
        }
        .quick-preset-pill {
            cursor: pointer;
            transition: all 0.2s ease;
            user-select: none;
            font-size: 0.72rem;
            padding: 0.25rem 0.6rem;
            border-radius: 0.5rem;
            border: 1px solid #e2e8f0;
            background: #ffffff;
            color: #475569;
            font-weight: 600;
        }
        .quick-preset-pill:hover {
            border-color: #6366f1;
            color: #4f46e5;
            background: #eef2ff;
            transform: translateY(-1px);
        }
        html[data-bs-theme="dark"] .quick-preset-pill {
            background: #1e293b;
            border-color: rgba(255,255,255,0.1);
            color: #cbd5e1;
        }
        html[data-bs-theme="dark"] .quick-preset-pill:hover {
            background: rgba(99,102,241,0.2);
            color: #a5b4fc;
            border-color: #818cf8;
        }
    </style>

    <div class="container-fluid px-3 px-md-4 py-3">

        <!-- Flash Message -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show d-flex align-items-center mb-3 shadow-sm border-0 rounded-3" role="alert">
                <i class="fa-solid fa-circle-check me-2 fs-5"></i>
                <div class="fw-semibold">{{ session('success') }}</div>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center mb-3 shadow-sm border-0 rounded-3" role="alert">
                <i class="fa-solid fa-triangle-exclamation me-2 fs-5"></i>
                <div class="fw-semibold">{{ session('error') }}</div>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- BANNER HEADER -->
        <div class="dashboard-banner mb-4">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
                <div>
                    <span class="banner-badge">
                        <i class="fa-solid fa-handshake"></i> REKANAN 3PL & CARGO
                    </span>
                    <h1 class="banner-title">Master Vendor Ekspedisi</h1>
                    <p class="text-muted mb-0 small mt-1">
                        Daftar mitra kargo, kurir, dan sewa truk (charter) untuk pengiriman logistik eksternal.
                    </p>
                </div>
                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('ekspedisi.index') }}" class="btn btn-outline-secondary d-flex align-items-center gap-2 px-3 py-2 rounded-3 shadow-sm fw-bold">
                        <i class="fa-solid fa-arrow-left"></i>
                        <span>Ke Pengiriman</span>
                    </a>
                    @if(auth()->user()->hasPermission('Manage Vendor Ekspedisi'))
                    <button type="button" class="btn btn-primary d-flex align-items-center gap-2 px-3 py-2 rounded-3 shadow-sm fw-bold" data-bs-toggle="modal" data-bs-target="#createVendorModal">
                        <i class="fa-solid fa-plus"></i>
                        <span>Tambah Vendor Baru</span>
                    </button>
                    @endif
                </div>
            </div>
        </div>

        <!-- KPI SUMMARY CARDS -->
        <div class="row g-3 mb-4">
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card premium-card border-0 shadow-sm text-white" style="background: linear-gradient(135deg, #4f46e5 0%, #3730a3 100%);">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="text-white-50 small fw-bold text-uppercase tracking-wider">Total Rekanan</span>
                                <h3 class="fw-extrabold mb-0 mt-1 text-white">{{ number_format($stats['total_vendors']) }}</h3>
                                <div class="small text-white-50 mt-1">Vendor Terdaftar</div>
                            </div>
                            <div class="premium-icon-box">
                                <i class="fa-solid fa-building fs-4 text-white"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card premium-card border-0 shadow-sm text-white" style="background: linear-gradient(135deg, #10b981 0%, #047857 100%);">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="text-white-50 small fw-bold text-uppercase tracking-wider">Status Aktif</span>
                                <h3 class="fw-extrabold mb-0 mt-1 text-white">{{ number_format($stats['active_vendors']) }}</h3>
                                <div class="small text-white-50 mt-1">Siap Terima Kiriman</div>
                            </div>
                            <div class="premium-icon-box">
                                <i class="fa-solid fa-circle-check fs-4 text-white"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card premium-card border-0 shadow-sm text-white" style="background: linear-gradient(135deg, #0ea5e9 0%, #0369a1 100%);">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="text-white-50 small fw-bold text-uppercase tracking-wider">Layanan Kargo</span>
                                <h3 class="fw-extrabold mb-0 mt-1 text-white">{{ number_format($stats['total_cargo']) }}</h3>
                                <div class="small text-white-50 mt-1">Muatan Besar / Palet</div>
                            </div>
                            <div class="premium-icon-box">
                                <i class="fa-solid fa-boxes-packing fs-4 text-white"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card premium-card border-0 shadow-sm text-white" style="background: linear-gradient(135deg, #f59e0b 0%, #b45309 100%);">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="text-white-50 small fw-bold text-uppercase tracking-wider">Charter Truk</span>
                                <h3 class="fw-extrabold mb-0 mt-1 text-white">{{ number_format($stats['total_charter']) }}</h3>
                                <div class="small text-white-50 mt-1">Sewa Truk Khusus / FTL</div>
                            </div>
                            <div class="premium-icon-box">
                                <i class="fa-solid fa-truck-monster fs-4 text-white"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- TABLE CARD -->
        <div class="card data-card border-0 shadow-sm mb-4">
            <div class="card-header bg-transparent py-3 px-3 border-bottom">
                <form method="GET" action="{{ route('ekspedisi.vendors.index') }}" class="row g-2 align-items-center">
                    <div class="col-12 col-md-5">
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="fa-solid fa-magnifying-glass text-muted"></i></span>
                            <input type="text" name="search" value="{{ request('search') }}" class="form-control border-start-0 ps-0" placeholder="Cari Kode, Nama Vendor, PIC, Kontak...">
                        </div>
                    </div>

                    <div class="col-6 col-md-3">
                        <select name="vendor_type" class="form-select" onchange="this.form.submit()">
                            <option value="ALL">Semua Jenis Layanan</option>
                            <option value="cargo" {{ request('vendor_type') === 'cargo' ? 'selected' : '' }}>Kargo / Ekspedisi</option>
                            <option value="trucking_charter" {{ request('vendor_type') === 'trucking_charter' ? 'selected' : '' }}>Sewa Armada (Charter)</option>
                            <option value="courier" {{ request('vendor_type') === 'courier' ? 'selected' : '' }}>Kurir / Express</option>
                            <option value="forwarder" {{ request('vendor_type') === 'forwarder' ? 'selected' : '' }}>Freight Forwarder</option>
                        </select>
                    </div>

                    <div class="col-6 col-md-2">
                        <select name="status" class="form-select" onchange="this.form.submit()">
                            <option value="ALL">Semua Status</option>
                            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Aktif</option>
                            <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Nonaktif</option>
                        </select>
                    </div>

                    <div class="col-12 col-md-2 d-flex gap-2 justify-content-end">
                        <button type="submit" class="btn btn-secondary px-3 w-100">Cari</button>
                        @if(request()->anyFilled(['search', 'vendor_type', 'status']))
                            <a href="{{ route('ekspedisi.vendors.index') }}" class="btn btn-outline-danger" title="Reset Filter"><i class="fa-solid fa-xmark"></i></a>
                        @endif
                    </div>
                </form>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-premium align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3">Kode & Nama Vendor</th>
                                <th>Jenis</th>
                                <th>Kontak & Alamat Cabang</th>
                                <th>Termin Bayar</th>
                                <th>Layanan Support</th>
                                <th class="text-center">Total Kiriman</th>
                                <th class="text-center">Status</th>
                                <th class="text-center pe-3">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($vendors as $vendor)
                                <tr>
                                    <td class="ps-3">
                                        <span class="badge bg-primary-subtle text-primary font-monospace fw-bold mb-1">{{ $vendor->vendor_code }}</span>
                                        <div class="fw-bold text-dark fs-6">{{ $vendor->vendor_name }}</div>
                                        @if($vendor->website_tracking_url)
                                            <div class="mt-1">
                                                <a href="{{ $vendor->website_tracking_url }}" target="_blank" class="badge bg-light text-primary border text-decoration-none font-monospace" style="font-size: 0.68rem;" title="{{ $vendor->website_tracking_url }}">
                                                    <i class="fa-solid fa-satellite-dish me-1"></i> {{ Str::limit($vendor->website_tracking_url, 30) }}
                                                </a>
                                            </div>
                                        @else
                                            <span class="badge bg-light text-muted border fst-italic mt-1" style="font-size: 0.65rem;">URL Tracking: Belum diatur</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($vendor->vendor_type === 'cargo')
                                            <span class="badge bg-info-subtle text-info border border-info">Kargo Ekspedisi</span>
                                        @elseif($vendor->vendor_type === 'trucking_charter')
                                            <span class="badge bg-warning-subtle text-warning border border-warning">Sewa Truk / Charter</span>
                                        @elseif($vendor->vendor_type === 'courier')
                                            <span class="badge bg-secondary-subtle text-secondary border">Kurir Express</span>
                                        @else
                                            <span class="badge bg-light text-dark border">Forwarder</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="small fw-semibold text-dark">
                                            <i class="fa-solid fa-user-tie text-muted me-1"></i> {{ $vendor->contact_person ?: '-' }}
                                        </div>
                                        <div class="small text-muted font-monospace">
                                            <i class="fa-solid fa-phone text-muted me-1"></i> {{ $vendor->phone_number ?: ($vendor->telephone ?: '-') }}
                                        </div>
                                        @if($vendor->office_address)
                                            <div class="small text-muted text-truncate" style="max-width: 250px;" title="{{ $vendor->office_address }}">
                                                <i class="fa-solid fa-location-dot text-muted me-1"></i> {{ $vendor->office_address }}
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border font-monospace">
                                            {{ strtoupper(str_replace('_', ' ', $vendor->payment_terms)) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($vendor->supported_services && is_array($vendor->supported_services))
                                            <div class="d-flex flex-wrap gap-1">
                                                @foreach($vendor->supported_services as $svc)
                                                    <span class="badge bg-light text-secondary border" style="font-size: 0.65rem;">{{ $svc }}</span>
                                                @endforeach
                                            </div>
                                        @else
                                            <span class="text-muted small">-</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-primary rounded-pill px-2.5 py-1 font-monospace">
                                            {{ $vendor->expeditions_count }} Pengiriman
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        @if($vendor->is_active)
                                            <span class="badge bg-success-subtle text-success border border-success px-2 py-1">
                                                <i class="fa-solid fa-check me-1"></i> Aktif
                                            </span>
                                        @else
                                            <span class="badge bg-danger-subtle text-danger border border-danger px-2 py-1">
                                                Nonaktif
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-center pe-3">
                                        <div class="d-flex justify-content-center gap-1">
                                            @if(auth()->user()->hasPermission('Manage Vendor Ekspedisi'))
                                                <button type="button" class="btn btn-sm btn-outline-primary px-2 py-1 btn-edit-vendor" data-vendor='@json($vendor)' title="Edit Vendor">
                                                    <i class="fa-solid fa-pen-to-square"></i>
                                                </button>

                                                <form method="POST" action="{{ route('ekspedisi.vendors.destroy', $vendor->id) }}" class="d-inline" onsubmit="return confirm('Hapus vendor {{ $vendor->vendor_name }}?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger px-2 py-1" title="Hapus Vendor">
                                                        <i class="fa-solid fa-trash-can"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-5 text-muted">
                                        <i class="fa-solid fa-handshake-slash fa-3x mb-3 text-secondary opacity-50"></i>
                                        <p class="mb-0 fw-semibold">Belum ada vendor ekspedisi yang terdaftar.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @if($vendors->hasPages())
                <div class="card-footer bg-transparent py-2 border-top">
                    {{ $vendors->links() }}
                </div>
            @endif
        </div>

    </div>

    <!-- MODAL CREATE VENDOR (PREMIUM 2-COLUMN DESIGN) -->
    <div class="modal fade" id="createVendorModal" tabindex="-1" aria-labelledby="createVendorModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <form method="POST" action="{{ route('ekspedisi.vendors.store') }}" class="modal-content rounded-4 border-0 shadow-lg overflow-hidden d-flex flex-column" style="max-height: calc(100vh - 3rem);">
                @csrf
                
                <!-- MODAL HEADER -->
                <div class="modal-hero-header text-white d-flex align-items-center justify-content-between flex-shrink-0">
                    <div class="d-flex align-items-center gap-3">
                        <div class="bg-white bg-opacity-20 p-2.5 rounded-3 d-flex align-items-center justify-content-center shadow-sm" style="width: 48px; height: 48px;">
                            <i class="fa-solid fa-handshake fs-4 text-white"></i>
                        </div>
                        <div>
                            <div class="d-flex align-items-center gap-2">
                                <h5 class="modal-title fw-bold text-white mb-0" id="createVendorModalLabel">Tambah Rekanan Vendor Ekspedisi</h5>
                                <span class="badge bg-white bg-opacity-25 text-white font-monospace" style="font-size: 0.65rem;">NEW PARTNER</span>
                            </div>
                            <p class="text-white text-opacity-75 small mb-0 mt-0.5">Daftarkan mitra kargo, kurir, atau sewa truk baru dan konfigurasikan pelacakan resi otomatis.</p>
                        </div>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <!-- MODAL BODY -->
                <div class="modal-body p-3 p-md-4 modal-custom-body overflow-y-auto" style="flex: 1 1 auto;">
                    <div class="row g-3">
                        <!-- LEFT COLUMN: IDENTITAS & TRACKING & OPERASIONAL (Col 7) -->
                        <div class="col-12 col-lg-7 d-flex flex-column gap-3">
                            
                            <!-- 1. IDENTITAS & PROFIL -->
                            <div class="modal-section-card mb-0">
                                <div class="modal-section-header">
                                    <span class="fw-bold small text-uppercase text-primary d-flex align-items-center gap-2">
                                        <i class="fa-solid fa-id-card"></i> 1. Identitas & Profil Rekanan
                                    </span>
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill" style="font-size: 0.65rem;">Wajib Diisi</span>
                                </div>
                                <div class="modal-section-body">
                                    <div class="row g-3">
                                        <div class="col-12 col-sm-5">
                                            <label class="form-floating-label"><i class="fa-solid fa-barcode text-muted"></i> Kode Vendor <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <input type="text" name="vendor_code" id="create_vendor_code" required class="form-control form-control-custom font-monospace text-uppercase" placeholder="VND-XXX" value="{{ old('vendor_code') }}">
                                                <button type="button" class="btn btn-outline-primary btn-sm px-2.5 rounded-end-3" id="btnAutoCode" title="Generate Otomatis dari Nama Vendor">
                                                    <i class="fa-solid fa-wand-magic-sparkles"></i>
                                                </button>
                                            </div>
                                            @error('vendor_code') <div class="text-danger small mt-1" style="font-size: 0.72rem;">{{ $message }}</div> @enderror
                                        </div>

                                        <div class="col-12 col-sm-7">
                                            <label class="form-floating-label"><i class="fa-solid fa-building text-muted"></i> Nama Vendor <span class="text-danger">*</span></label>
                                            <input type="text" name="vendor_name" id="create_vendor_name" required class="form-control form-control-custom" placeholder="Cth: Ginta Cargo, Lion Parcel" value="{{ old('vendor_name') }}">
                                            @error('vendor_name') <div class="text-danger small mt-1" style="font-size: 0.72rem;">{{ $message }}</div> @enderror
                                        </div>

                                        <div class="col-12">
                                            <label class="form-floating-label"><i class="fa-solid fa-truck-moving text-muted"></i> Jenis Layanan Ekspedisi <span class="text-danger">*</span></label>
                                            <select name="vendor_type" class="form-select form-control-custom">
                                                <option value="cargo" {{ old('vendor_type') == 'cargo' ? 'selected' : '' }}>📦 Kargo Ekspedisi (Port-to-Port / Door-to-Door via Udara/Darat/Laut)</option>
                                                <option value="trucking_charter" {{ old('vendor_type') == 'trucking_charter' ? 'selected' : '' }}>🚛 Sewa Truk / Trucking Charter (CDD, Fuso, Tronton, Wingbox)</option>
                                                <option value="courier" {{ old('vendor_type') == 'courier' ? 'selected' : '' }}>⚡ Kurir Express (Paket Kilat / Sameday / Next Day)</option>
                                                <option value="forwarder" {{ old('vendor_type') == 'forwarder' ? 'selected' : '' }}>🌐 Freight Forwarder & Container Logistik</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- 2. INTEGRASI LIVE TRACKING RESI (HIGHLIGHT CARD) -->
                            <div class="modal-section-card highlight-card mb-0">
                                <div class="modal-section-header bg-transparent border-bottom-0 pb-1">
                                    <span class="fw-bold small text-uppercase text-indigo d-flex align-items-center gap-2" style="color: #4f46e5;">
                                        <i class="fa-solid fa-satellite-dish"></i> 2. Integrasi Pelacakan Resi Otomatis
                                    </span>
                                    <span class="badge bg-indigo text-white px-2 py-0.5 rounded-pill" style="font-size: 0.65rem; background-color: #4f46e5;">Auto-Tracking</span>
                                </div>
                                <div class="modal-section-body pt-2">
                                    <label class="form-floating-label mb-1">
                                        <i class="fa-solid fa-link text-primary"></i> Template URL Tracking Web Vendor
                                    </label>
                                    <div class="input-group mb-2 shadow-sm">
                                        <span class="input-group-text bg-white border-end-0 text-muted" style="border-radius: 0.65rem 0 0 0.65rem;">
                                            <i class="fa-solid fa-globe"></i>
                                        </span>
                                        <input type="text" name="website_tracking_url" id="create_tracking_url" class="form-control form-control-custom font-monospace border-start-0" placeholder="https://trace.gintacargo.com/?awb={RESI}" value="{{ old('website_tracking_url') }}" style="border-radius: 0 0.65rem 0.65rem 0;">
                                    </div>

                                    <!-- Quick Presets -->
                                    <div class="d-flex flex-wrap align-items-center gap-1.5 mb-2.5">
                                        <span class="small text-muted me-1" style="font-size: 0.7rem;"><i class="fa-solid fa-bolt text-warning me-1"></i>Pilih Cepat:</span>
                                        <button type="button" class="quick-preset-pill" data-target="create_tracking_url" data-url="https://trace.gintacargo.com/?awb={RESI}" data-name="Ginta Cargo">Ginta Cargo</button>
                                        <button type="button" class="quick-preset-pill" data-target="create_tracking_url" data-url="https://lionparcel.com/track/stt?q={RESI}" data-name="Lion Parcel">Lion Parcel</button>
                                        <button type="button" class="quick-preset-pill" data-target="create_tracking_url" data-url="https://www.jtcargo.id/tracking?awb={RESI}" data-name="J&T Cargo">J&T Cargo</button>
                                        <button type="button" class="quick-preset-pill" data-target="create_tracking_url" data-url="https://indahonline.com/tracking?resi={RESI}" data-name="Indah Logistik Cargo">Indah Logistik</button>
                                        <button type="button" class="quick-preset-pill" data-target="create_tracking_url" data-url="https://www.jne.co.id/tracking/{RESI}" data-name="JNE Express">JNE</button>
                                        <button type="button" class="quick-preset-pill" data-target="create_tracking_url" data-url="https://www.sicepat.com/checkAwb/{RESI}" data-name="SiCepat Express">SiCepat</button>
                                    </div>

                                    <!-- Live Preview box -->
                                    <div class="tracking-preview-box">
                                        <div class="d-flex align-items-center justify-content-between mb-1">
                                            <span class="text-muted fw-bold" style="font-size: 0.68rem;"><i class="fa-solid fa-eye me-1"></i>Pratinjau URL yang Dihasilkan:</span>
                                            <span class="badge bg-secondary-subtle text-secondary" style="font-size: 0.62rem;">Simulasi Resi: JKT261002167</span>
                                        </div>
                                        <div id="create_tracking_preview" class="text-truncate text-primary fw-medium font-monospace">
                                            (Ketik URL atau pilih template di atas dengan tag {RESI})
                                        </div>
                                    </div>
                                    <small class="text-muted d-block mt-1.5" style="font-size: 0.68rem; line-height: 1.35;">
                                        <i class="fa-solid fa-circle-info text-info me-1"></i>Sistem otomatis menyinkronkan status resi kurir & merubah SPK menjadi <strong>DELIVERED</strong> jika barang sudah tiba di penerima.
                                    </small>
                                </div>
                            </div>

                            <!-- 3. TERMIN & OPERASIONAL -->
                            <div class="modal-section-card mb-0">
                                <div class="modal-section-header">
                                    <span class="fw-bold small text-uppercase text-secondary d-flex align-items-center gap-2">
                                        <i class="fa-solid fa-file-invoice-dollar"></i> 3. Ketentuan Pembayaran & Operasional
                                    </span>
                                </div>
                                <div class="modal-section-body">
                                    <div class="row g-3">
                                        <div class="col-12 col-sm-6">
                                            <label class="form-floating-label"><i class="fa-solid fa-credit-card text-muted"></i> Termin Pembayaran <span class="text-danger">*</span></label>
                                            <select name="payment_terms" class="form-select form-control-custom">
                                                <option value="tempo_30" {{ old('payment_terms', 'tempo_30') == 'tempo_30' ? 'selected' : '' }}>Tempo 30 Hari (TOP 30)</option>
                                                <option value="tempo_14" {{ old('payment_terms') == 'tempo_14' ? 'selected' : '' }}>Tempo 14 Hari (TOP 14)</option>
                                                <option value="monthly_invoice" {{ old('payment_terms') == 'monthly_invoice' ? 'selected' : '' }}>Tagihan Bulanan (Invoice Rekap)</option>
                                                <option value="cash_cod" {{ old('payment_terms') == 'cash_cod' ? 'selected' : '' }}>Cash Sebelum Berangkat / COD</option>
                                            </select>
                                        </div>
                                        <div class="col-12 col-sm-6">
                                            <label class="form-floating-label"><i class="fa-solid fa-tags text-muted"></i> Layanan yang Didukung</label>
                                            <input type="text" name="supported_services" id="create_supported_services" class="form-control form-control-custom" placeholder="Cth: Cargo Darat, Cargo Laut, Door to Door" value="{{ old('supported_services', 'Cargo Darat, Cargo Laut') }}">
                                        </div>
                                        <div class="col-12">
                                            <label class="form-floating-label"><i class="fa-solid fa-note-sticky text-muted"></i> Catatan Khusus Vendor</label>
                                            <input type="text" name="notes" class="form-control form-control-custom" placeholder="Spesialisasi rute, kontak armada, batas waktu pick up harian..." value="{{ old('notes') }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- RIGHT COLUMN: KONTAK & ALAMAT & STATUS (Col 5) -->
                        <div class="col-12 col-lg-5 d-flex flex-column gap-3">
                            
                            <!-- 4. KONTAK & PIC -->
                            <div class="modal-section-card mb-0">
                                <div class="modal-section-header">
                                    <span class="fw-bold small text-uppercase text-secondary d-flex align-items-center gap-2">
                                        <i class="fa-solid fa-address-book"></i> 4. Kontak Narahubung (PIC)
                                    </span>
                                </div>
                                <div class="modal-section-body">
                                    <div class="row g-3">
                                        <div class="col-12">
                                            <label class="form-floating-label"><i class="fa-solid fa-user-tie text-muted"></i> Nama PIC / Marketing</label>
                                            <input type="text" name="contact_person" class="form-control form-control-custom" placeholder="Nama Marketing / Koordinator Agen" value="{{ old('contact_person') }}">
                                        </div>
                                        <div class="col-12 col-sm-6">
                                            <label class="form-floating-label"><i class="fa-brands fa-whatsapp text-success"></i> WhatsApp / HP</label>
                                            <input type="text" name="phone_number" class="form-control form-control-custom" placeholder="0812xxxxxxxx" value="{{ old('phone_number') }}">
                                        </div>
                                        <div class="col-12 col-sm-6">
                                            <label class="form-floating-label"><i class="fa-solid fa-phone text-muted"></i> Telp Kantor</label>
                                            <input type="text" name="telephone" class="form-control form-control-custom" placeholder="021-xxxxxxx" value="{{ old('telephone') }}">
                                        </div>
                                        <div class="col-12">
                                            <label class="form-floating-label"><i class="fa-solid fa-envelope text-muted"></i> Email Rekanan</label>
                                            <input type="email" name="email" class="form-control form-control-custom" placeholder="marketing@vendor.com" value="{{ old('email') }}">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- 5. HUB DROP POINT / GUDANG CABANG -->
                            <div class="modal-section-card mb-0">
                                <div class="modal-section-header">
                                    <span class="fw-bold small text-uppercase text-secondary d-flex align-items-center gap-2">
                                        <i class="fa-solid fa-map-location-dot"></i> 5. Hub Drop Point / Pool Armada
                                    </span>
                                </div>
                                <div class="modal-section-body">
                                    <label class="form-floating-label"><i class="fa-solid fa-map-pin text-muted"></i> Alamat Kantor Cabang / Drop Point Terdekat</label>
                                    <textarea name="office_address" rows="3" class="form-control form-control-custom" placeholder="Alamat lengkap drop point cabang, pergudangan, atau pool truk vendor...">{{ old('office_address') }}</textarea>
                                </div>
                            </div>

                            <!-- 6. STATUS AKTIVASI -->
                            <div class="modal-section-card mb-0">
                                <div class="modal-section-body p-3">
                                    <div class="d-flex align-items-center justify-content-between p-2.5 rounded-3 bg-light border">
                                        <div class="d-flex align-items-center gap-2.5">
                                            <div class="bg-success bg-opacity-10 text-success p-2 rounded-circle d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                                                <i class="fa-solid fa-circle-check fs-5"></i>
                                            </div>
                                            <div>
                                                <div class="fw-bold small">Status Aktif Rekanan</div>
                                                <div class="text-muted" style="font-size: 0.72rem;">Vendor dapat langsung dipilih saat membuat SPK Pengiriman</div>
                                            </div>
                                        </div>
                                        <div class="form-check form-switch m-0 fs-5">
                                            <input class="form-check-input" type="checkbox" name="is_active" id="create_is_active" checked>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

                <!-- MODAL FOOTER FIXED -->
                <div class="modal-footer modal-custom-footer py-3 px-4 d-flex justify-content-between align-items-center flex-shrink-0">
                    <button type="button" class="btn btn-light px-4 py-2 rounded-3 fw-bold text-secondary" data-bs-dismiss="modal">
                        <i class="fa-solid fa-xmark me-1"></i> Batal
                    </button>
                    <button type="submit" class="btn btn-primary px-4 py-2 rounded-3 fw-bold shadow-sm d-flex align-items-center gap-2">
                        <i class="fa-solid fa-circle-check"></i>
                        <span>Simpan Rekanan Vendor</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL EDIT VENDOR (PREMIUM 2-COLUMN DESIGN) -->
    <div class="modal fade" id="editVendorModal" tabindex="-1" aria-labelledby="editVendorModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <form id="editVendorForm" method="POST" action="" class="modal-content rounded-4 border-0 shadow-lg overflow-hidden d-flex flex-column" style="max-height: calc(100vh - 3rem);">
                @csrf
                @method('PUT')
                
                <!-- MODAL HEADER -->
                <div class="modal-hero-header warning-header text-white d-flex align-items-center justify-content-between flex-shrink-0">
                    <div class="d-flex align-items-center gap-3">
                        <div class="bg-white bg-opacity-20 p-2.5 rounded-3 d-flex align-items-center justify-content-center shadow-sm" style="width: 48px; height: 48px;">
                            <i class="fa-solid fa-pen-to-square fs-4 text-white"></i>
                        </div>
                        <div>
                            <div class="d-flex align-items-center gap-2">
                                <h5 class="modal-title fw-bold text-white mb-0" id="editVendorModalLabel">Edit Master Vendor</h5>
                                <span class="badge bg-white bg-opacity-25 text-white font-monospace" style="font-size: 0.65rem;">UPDATE PARTNER</span>
                            </div>
                            <p class="text-white text-opacity-75 small mb-0 mt-0.5">Perbarui informasi kontak, alamat drop point, atau URL pelacakan resi vendor.</p>
                        </div>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <!-- MODAL BODY -->
                <div class="modal-body p-3 p-md-4 modal-custom-body overflow-y-auto" style="flex: 1 1 auto;">
                    <div class="row g-3">
                        <!-- LEFT COLUMN: IDENTITAS & TRACKING & OPERASIONAL (Col 7) -->
                        <div class="col-12 col-lg-7 d-flex flex-column gap-3">
                            
                            <!-- 1. IDENTITAS & PROFIL -->
                            <div class="modal-section-card mb-0">
                                <div class="modal-section-header">
                                    <span class="fw-bold small text-uppercase text-warning d-flex align-items-center gap-2">
                                        <i class="fa-solid fa-id-card"></i> 1. Identitas & Profil
                                    </span>
                                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle rounded-pill" style="font-size: 0.65rem;">Master Data</span>
                                </div>
                                <div class="modal-section-body">
                                    <div class="row g-3">
                                        <div class="col-12 col-sm-5">
                                            <label class="form-floating-label"><i class="fa-solid fa-barcode text-muted"></i> Kode Vendor <span class="text-danger">*</span></label>
                                            <input type="text" name="vendor_code" id="edit_vendor_code" required class="form-control form-control-custom font-monospace text-uppercase">
                                        </div>
                                        <div class="col-12 col-sm-7">
                                            <label class="form-floating-label"><i class="fa-solid fa-building text-muted"></i> Nama Vendor <span class="text-danger">*</span></label>
                                            <input type="text" name="vendor_name" id="edit_vendor_name" required class="form-control form-control-custom">
                                        </div>
                                        <div class="col-12">
                                            <label class="form-floating-label"><i class="fa-solid fa-truck-moving text-muted"></i> Jenis Layanan Ekspedisi <span class="text-danger">*</span></label>
                                            <select name="vendor_type" id="edit_vendor_type" class="form-select form-control-custom">
                                                <option value="cargo">📦 Kargo Ekspedisi (Port-to-Port / Door-to-Door via Udara/Darat/Laut)</option>
                                                <option value="trucking_charter">🚛 Sewa Truk / Trucking Charter (CDD, Fuso, Tronton, Wingbox)</option>
                                                <option value="courier">⚡ Kurir Express (Paket Kilat / Sameday / Next Day)</option>
                                                <option value="forwarder">🌐 Freight Forwarder & Container Logistik</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- 2. INTEGRASI LIVE TRACKING RESI (HIGHLIGHT CARD) -->
                            <div class="modal-section-card highlight-card mb-0">
                                <div class="modal-section-header bg-transparent border-bottom-0 pb-1">
                                    <span class="fw-bold small text-uppercase text-indigo d-flex align-items-center gap-2" style="color: #4f46e5;">
                                        <i class="fa-solid fa-satellite-dish"></i> 2. Integrasi Pelacakan Resi Otomatis
                                    </span>
                                    <span class="badge bg-indigo text-white px-2 py-0.5 rounded-pill" style="font-size: 0.65rem; background-color: #4f46e5;">Auto-Tracking</span>
                                </div>
                                <div class="modal-section-body pt-2">
                                    <label class="form-floating-label mb-1">
                                        <i class="fa-solid fa-link text-primary"></i> Template URL Tracking Web Vendor
                                    </label>
                                    <div class="input-group mb-2 shadow-sm">
                                        <span class="input-group-text bg-white border-end-0 text-muted" style="border-radius: 0.65rem 0 0 0.65rem;">
                                            <i class="fa-solid fa-globe"></i>
                                        </span>
                                        <input type="text" name="website_tracking_url" id="edit_website_tracking_url" class="form-control form-control-custom font-monospace border-start-0" placeholder="https://trace.gintacargo.com/?awb={RESI}" style="border-radius: 0 0.65rem 0.65rem 0;">
                                    </div>

                                    <!-- Quick Presets -->
                                    <div class="d-flex flex-wrap align-items-center gap-1.5 mb-2.5">
                                        <span class="small text-muted me-1" style="font-size: 0.7rem;"><i class="fa-solid fa-bolt text-warning me-1"></i>Pilih Cepat:</span>
                                        <button type="button" class="quick-preset-pill" data-target="edit_website_tracking_url" data-url="https://trace.gintacargo.com/?awb={RESI}" data-name="Ginta Cargo">Ginta Cargo</button>
                                        <button type="button" class="quick-preset-pill" data-target="edit_website_tracking_url" data-url="https://lionparcel.com/track/stt?q={RESI}" data-name="Lion Parcel">Lion Parcel</button>
                                        <button type="button" class="quick-preset-pill" data-target="edit_website_tracking_url" data-url="https://www.jtcargo.id/tracking?awb={RESI}" data-name="J&T Cargo">J&T Cargo</button>
                                        <button type="button" class="quick-preset-pill" data-target="edit_website_tracking_url" data-url="https://indahonline.com/tracking?resi={RESI}" data-name="Indah Logistik Cargo">Indah Logistik</button>
                                        <button type="button" class="quick-preset-pill" data-target="edit_website_tracking_url" data-url="https://www.jne.co.id/tracking/{RESI}" data-name="JNE Express">JNE</button>
                                        <button type="button" class="quick-preset-pill" data-target="edit_website_tracking_url" data-url="https://www.sicepat.com/checkAwb/{RESI}" data-name="SiCepat Express">SiCepat</button>
                                    </div>

                                    <!-- Live Preview box -->
                                    <div class="tracking-preview-box">
                                        <div class="d-flex align-items-center justify-content-between mb-1">
                                            <span class="text-muted fw-bold" style="font-size: 0.68rem;"><i class="fa-solid fa-eye me-1"></i>Pratinjau URL yang Dihasilkan:</span>
                                            <span class="badge bg-secondary-subtle text-secondary" style="font-size: 0.62rem;">Simulasi Resi: JKT261002167</span>
                                        </div>
                                        <div id="edit_tracking_preview" class="text-truncate text-primary fw-medium font-monospace">
                                            (Ketik URL atau pilih template di atas dengan tag {RESI})
                                        </div>
                                    </div>
                                    <small class="text-muted d-block mt-1.5" style="font-size: 0.68rem; line-height: 1.35;">
                                        <i class="fa-solid fa-circle-info text-info me-1"></i>Gunakan tag <code>{RESI}</code>. Sistem otomatis menarik riwayat checkpoints kurir.
                                    </small>
                                </div>
                            </div>

                            <!-- 3. TERMIN & OPERASIONAL -->
                            <div class="modal-section-card mb-0">
                                <div class="modal-section-header">
                                    <span class="fw-bold small text-uppercase text-secondary d-flex align-items-center gap-2">
                                        <i class="fa-solid fa-file-invoice-dollar"></i> 3. Ketentuan Pembayaran & Operasional
                                    </span>
                                </div>
                                <div class="modal-section-body">
                                    <div class="row g-3">
                                        <div class="col-12 col-sm-6">
                                            <label class="form-floating-label"><i class="fa-solid fa-credit-card text-muted"></i> Termin Pembayaran <span class="text-danger">*</span></label>
                                            <select name="payment_terms" id="edit_payment_terms" class="form-select form-control-custom">
                                                <option value="tempo_30">Tempo 30 Hari (TOP 30)</option>
                                                <option value="tempo_14">Tempo 14 Hari (TOP 14)</option>
                                                <option value="monthly_invoice">Tagihan Bulanan (Invoice Rekap)</option>
                                                <option value="cash_cod">Cash Sebelum Berangkat / COD</option>
                                            </select>
                                        </div>
                                        <div class="col-12 col-sm-6">
                                            <label class="form-floating-label"><i class="fa-solid fa-tags text-muted"></i> Layanan yang Didukung</label>
                                            <input type="text" name="supported_services" id="edit_supported_services" class="form-control form-control-custom">
                                        </div>
                                        <div class="col-12">
                                            <label class="form-floating-label"><i class="fa-solid fa-note-sticky text-muted"></i> Catatan Khusus Vendor</label>
                                            <input type="text" name="notes" id="edit_notes" class="form-control form-control-custom">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- RIGHT COLUMN: KONTAK & ALAMAT & STATUS (Col 5) -->
                        <div class="col-12 col-lg-5 d-flex flex-column gap-3">
                            
                            <!-- 4. KONTAK & PIC -->
                            <div class="modal-section-card mb-0">
                                <div class="modal-section-header">
                                    <span class="fw-bold small text-uppercase text-secondary d-flex align-items-center gap-2">
                                        <i class="fa-solid fa-address-book"></i> 4. Kontak Narahubung (PIC)
                                    </span>
                                </div>
                                <div class="modal-section-body">
                                    <div class="row g-3">
                                        <div class="col-12">
                                            <label class="form-floating-label"><i class="fa-solid fa-user-tie text-muted"></i> Nama PIC / Marketing</label>
                                            <input type="text" name="contact_person" id="edit_contact_person" class="form-control form-control-custom">
                                        </div>
                                        <div class="col-12 col-sm-6">
                                            <label class="form-floating-label"><i class="fa-brands fa-whatsapp text-success"></i> WhatsApp / HP</label>
                                            <input type="text" name="phone_number" id="edit_phone_number" class="form-control form-control-custom">
                                        </div>
                                        <div class="col-12 col-sm-6">
                                            <label class="form-floating-label"><i class="fa-solid fa-phone text-muted"></i> Telp Kantor</label>
                                            <input type="text" name="telephone" id="edit_telephone" class="form-control form-control-custom">
                                        </div>
                                        <div class="col-12">
                                            <label class="form-floating-label"><i class="fa-solid fa-envelope text-muted"></i> Email Rekanan</label>
                                            <input type="email" name="email" id="edit_email" class="form-control form-control-custom">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- 5. HUB DROP POINT / GUDANG CABANG -->
                            <div class="modal-section-card mb-0">
                                <div class="modal-section-header">
                                    <span class="fw-bold small text-uppercase text-secondary d-flex align-items-center gap-2">
                                        <i class="fa-solid fa-map-location-dot"></i> 5. Hub Drop Point / Pool Armada
                                    </span>
                                </div>
                                <div class="modal-section-body">
                                    <label class="form-floating-label"><i class="fa-solid fa-map-pin text-muted"></i> Alamat Kantor Cabang / Drop Point Terdekat</label>
                                    <textarea name="office_address" id="edit_office_address" rows="3" class="form-control form-control-custom"></textarea>
                                </div>
                            </div>

                            <!-- 6. STATUS AKTIVASI -->
                            <div class="modal-section-card mb-0">
                                <div class="modal-section-body p-3">
                                    <div class="d-flex align-items-center justify-content-between p-2.5 rounded-3 bg-light border">
                                        <div class="d-flex align-items-center gap-2.5">
                                            <div class="bg-success bg-opacity-10 text-success p-2 rounded-circle d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                                                <i class="fa-solid fa-circle-check fs-5"></i>
                                            </div>
                                            <div>
                                                <div class="fw-bold small">Status Aktif Rekanan</div>
                                                <div class="text-muted" style="font-size: 0.72rem;">Vendor dapat langsung dipilih saat membuat SPK Pengiriman</div>
                                            </div>
                                        </div>
                                        <div class="form-check form-switch m-0 fs-5">
                                            <input class="form-check-input" type="checkbox" name="is_active" id="edit_is_active">
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

                <!-- MODAL FOOTER FIXED -->
                <div class="modal-footer modal-custom-footer py-3 px-4 d-flex justify-content-between align-items-center flex-shrink-0">
                    <button type="button" class="btn btn-light px-4 py-2 rounded-3 fw-bold text-secondary" data-bs-dismiss="modal">
                        <i class="fa-solid fa-xmark me-1"></i> Batal
                    </button>
                    <button type="submit" class="btn btn-primary px-4 py-2 rounded-3 fw-bold shadow-sm d-flex align-items-center gap-2">
                        <i class="fa-solid fa-circle-check"></i>
                        <span>Perbarui Data Vendor</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Helper function update tracking URL preview
            function updateTrackingPreview(inputEl, previewEl) {
                if (!inputEl || !previewEl) return;
                const val = (inputEl.value || '').trim();
                if (!val) {
                    previewEl.innerHTML = '<span class="text-muted fst-italic">(Ketik URL atau pilih template di atas dengan tag {RESI})</span>';
                    return;
                }

                if (val.includes('{RESI}')) {
                    const parts = val.split('{RESI}');
                    previewEl.innerHTML = `${escapeHtml(parts[0])}<span class="badge bg-primary text-white mx-1">JKT261002167</span>${escapeHtml(parts.slice(1).join('{RESI}'))}`;
                } else {
                    previewEl.innerHTML = `${escapeHtml(val)} <span class="badge bg-warning text-dark ms-1" style="font-size: 0.65rem;"><i class="fa-solid fa-triangle-exclamation me-1"></i>Belum ada tag {RESI}</span>`;
                }
            }

            function escapeHtml(string) {
                return String(string).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
            }

            // Bind Create Modal Preview
            const createTrackingInput = document.getElementById('create_tracking_url');
            const createTrackingPreview = document.getElementById('create_tracking_preview');
            if (createTrackingInput) {
                createTrackingInput.addEventListener('input', () => updateTrackingPreview(createTrackingInput, createTrackingPreview));
                updateTrackingPreview(createTrackingInput, createTrackingPreview);
            }

            // Bind Edit Modal Preview
            const editTrackingInput = document.getElementById('edit_website_tracking_url');
            const editTrackingPreview = document.getElementById('edit_tracking_preview');
            if (editTrackingInput) {
                editTrackingInput.addEventListener('input', () => updateTrackingPreview(editTrackingInput, editTrackingPreview));
            }

            // Quick Preset Pills Click Handler
            document.querySelectorAll('.quick-preset-pill').forEach(pill => {
                pill.addEventListener('click', function () {
                    const targetId = this.getAttribute('data-target');
                    const url = this.getAttribute('data-url');
                    const targetInput = document.getElementById(targetId);
                    if (targetInput) {
                        targetInput.value = url;
                        if (targetId === 'create_tracking_url') {
                            updateTrackingPreview(targetInput, createTrackingPreview);
                            // If create_vendor_name is empty, fill with preset vendor name
                            const nameInput = document.getElementById('create_vendor_name');
                            if (nameInput && !nameInput.value.trim()) {
                                nameInput.value = this.getAttribute('data-name');
                                generateAutoCode();
                            }
                        } else {
                            updateTrackingPreview(targetInput, editTrackingPreview);
                        }
                    }
                });
            });

            // Auto-Generate Vendor Code
            function generateAutoCode() {
                const nameInput = document.getElementById('create_vendor_name');
                const codeInput = document.getElementById('create_vendor_code');
                if (!nameInput || !codeInput) return;

                let clean = nameInput.value
                    .replace(/^(PT|CV|UD|PD)\.?\s+/i, '')
                    .trim()
                    .toUpperCase();

                if (!clean) return;

                const words = clean.split(/\s+/).filter(w => w.length > 0);
                let acronym = '';
                if (words.length === 1) {
                    acronym = words[0].substring(0, 5);
                } else if (words.length === 2) {
                    acronym = words[0].substring(0, 3) + words[1].substring(0, 3);
                } else {
                    acronym = words.map(w => w[0]).join('');
                }

                acronym = acronym.replace(/[^A-Z0-9]/g, '');
                codeInput.value = `VND-${acronym}`;
            }

            const btnAutoCode = document.getElementById('btnAutoCode');
            if (btnAutoCode) {
                btnAutoCode.addEventListener('click', generateAutoCode);
            }

            const createNameInput = document.getElementById('create_vendor_name');
            if (createNameInput) {
                createNameInput.addEventListener('blur', function() {
                    const codeInput = document.getElementById('create_vendor_code');
                    if (codeInput && !codeInput.value.trim()) {
                        generateAutoCode();
                    }
                });
            }

            // Edit Vendor Modal Population
            const editButtons = document.querySelectorAll('.btn-edit-vendor');
            const editModalEl = document.getElementById('editVendorModal');
            const editModal = editModalEl ? new bootstrap.Modal(editModalEl) : null;
            const editForm = document.getElementById('editVendorForm');

            editButtons.forEach(btn => {
                btn.addEventListener('click', function () {
                    const vendor = JSON.parse(this.getAttribute('data-vendor'));
                    if (!vendor) return;

                    editForm.action = `/ekspedisi/vendors/${vendor.id}`;
                    document.getElementById('edit_vendor_code').value = vendor.vendor_code || '';
                    document.getElementById('edit_vendor_name').value = vendor.vendor_name || '';
                    document.getElementById('edit_vendor_type').value = vendor.vendor_type || 'cargo';
                    document.getElementById('edit_contact_person').value = vendor.contact_person || '';
                    document.getElementById('edit_phone_number').value = vendor.phone_number || '';
                    document.getElementById('edit_telephone').value = vendor.telephone || '';
                    document.getElementById('edit_email').value = vendor.email || '';
                    
                    const urlInput = document.getElementById('edit_website_tracking_url');
                    urlInput.value = vendor.website_tracking_url || '';
                    updateTrackingPreview(urlInput, editTrackingPreview);

                    document.getElementById('edit_office_address').value = vendor.office_address || '';
                    document.getElementById('edit_payment_terms').value = vendor.payment_terms || 'tempo_30';
                    document.getElementById('edit_notes').value = vendor.notes || '';
                    document.getElementById('edit_is_active').checked = !!vendor.is_active;

                    if (Array.isArray(vendor.supported_services)) {
                        document.getElementById('edit_supported_services').value = vendor.supported_services.join(', ');
                    } else {
                        document.getElementById('edit_supported_services').value = '';
                    }

                    editModal.show();
                });
            });

            @if ($errors->any())
            const createModalEl = document.getElementById('createVendorModal');
            if (createModalEl) {
                const createModal = new bootstrap.Modal(createModalEl);
                createModal.show();
            }
            @endif
        });
    </script>
    @endpush
</x-app-layout>
