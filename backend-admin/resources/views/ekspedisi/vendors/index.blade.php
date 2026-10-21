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
            background: linear-gradient(135deg, #4f46e5 0%, #3730a3 100%);
            border-bottom: 0;
            position: relative;
            padding: 1.5rem 1.75rem;
        }
        .modal-hero-header.warning-header {
            background: linear-gradient(135deg, #d97706 0%, #b45309 100%);
        }
        .modal-section-card {
            border-radius: 1rem;
            border: 1px solid #e2e8f0;
            background-color: #ffffff;
            transition: all 0.2s ease;
            box-shadow: 0 1px 3px rgba(0,0,0,0.03);
            margin-bottom: 1.25rem;
        }
        html[data-bs-theme="dark"] .modal-section-card {
            background-color: #131720 !important;
            border-color: rgba(255,255,255,0.08) !important;
        }
        .modal-section-header {
            padding: 0.75rem 1.25rem;
            border-bottom: 1px solid #f1f5f9;
            background: rgba(248, 250, 252, 0.8);
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
            padding: 1.25rem;
        }
        .form-floating-label {
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            color: #64748b;
            margin-bottom: 0.35rem;
            display: flex;
            align-items: center;
            gap: 0.35rem;
        }
        .form-control-custom {
            border-radius: 0.65rem;
            border: 1px solid #cbd5e1;
            padding: 0.55rem 0.85rem;
            font-size: 0.875rem;
            transition: all 0.2s;
        }
        .form-control-custom:focus {
            border-color: #6366f1;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.15);
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
                                            <a href="{{ $vendor->website_tracking_url }}" target="_blank" class="small text-decoration-none text-muted">
                                                <i class="fa-solid fa-arrow-up-right-from-square me-1"></i> Cek Resi Website
                                            </a>
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

    <!-- MODAL CREATE VENDOR (PREMIUM DESIGN) -->
    <div class="modal fade" id="createVendorModal" tabindex="-1" aria-labelledby="createVendorModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content rounded-4 border-0 shadow-lg overflow-hidden">
                <form method="POST" action="{{ route('ekspedisi.vendors.store') }}">
                    @csrf
                    
                    <div class="modal-hero-header text-white d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-3">
                            <div class="bg-white bg-opacity-20 p-2.5 rounded-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                                <i class="fa-solid fa-handshake fs-4 text-white"></i>
                            </div>
                            <div>
                                <h5 class="modal-title fw-bold text-white mb-0" id="createVendorModalLabel">Tambah Rekanan Vendor Ekspedisi</h5>
                                <p class="text-white text-opacity-75 small mb-0 mt-0.5">Daftarkan vendor kargo atau penyedia sewa armada ekspedisi baru.</p>
                            </div>
                        </div>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body p-4 bg-light">
                        <div class="modal-section-card">
                            <div class="modal-section-header">
                                <span class="fw-bold small text-uppercase text-primary d-flex align-items-center gap-2">
                                    <i class="fa-solid fa-id-card"></i> Identitas & Profil Rekanan
                                </span>
                            </div>
                            <div class="modal-section-body">
                                <div class="row g-3">
                                    <div class="col-12 col-md-4">
                                        <label class="form-floating-label"><i class="fa-solid fa-barcode text-muted"></i> Kode Vendor <span class="text-danger">*</span></label>
                                        <input type="text" name="vendor_code" required class="form-control form-control-custom font-monospace text-uppercase" placeholder="Cth: VND-DKT">
                                    </div>
                                    <div class="col-12 col-md-5">
                                        <label class="form-floating-label"><i class="fa-solid fa-building text-muted"></i> Nama Vendor <span class="text-danger">*</span></label>
                                        <input type="text" name="vendor_name" required class="form-control form-control-custom" placeholder="Cth: Dakota Cargo">
                                    </div>
                                    <div class="col-12 col-md-3">
                                        <label class="form-floating-label"><i class="fa-solid fa-cubes text-muted"></i> Jenis Layanan <span class="text-danger">*</span></label>
                                        <select name="vendor_type" class="form-select form-control-custom">
                                            <option value="cargo" selected>Kargo Ekspedisi</option>
                                            <option value="trucking_charter">Sewa Truk (Charter)</option>
                                            <option value="courier">Kurir Express</option>
                                            <option value="forwarder">Freight Forwarder</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="modal-section-card">
                            <div class="modal-section-header">
                                <span class="fw-bold small text-uppercase text-secondary d-flex align-items-center gap-2">
                                    <i class="fa-solid fa-address-book"></i> Kontak & Lokasi Drop Point
                                </span>
                            </div>
                            <div class="modal-section-body">
                                <div class="row g-3">
                                    <div class="col-12 col-md-4">
                                        <label class="form-floating-label"><i class="fa-solid fa-user-tie text-muted"></i> Contact Person (PIC)</label>
                                        <input type="text" name="contact_person" class="form-control form-control-custom" placeholder="Nama Marketing / PIC">
                                    </div>
                                    <div class="col-12 col-md-4">
                                        <label class="form-floating-label"><i class="fa-solid fa-phone text-muted"></i> No. HP / WhatsApp</label>
                                        <input type="text" name="phone_number" class="form-control form-control-custom" placeholder="0812...">
                                    </div>
                                    <div class="col-12 col-md-4">
                                        <label class="form-floating-label"><i class="fa-solid fa-phone-office text-muted"></i> Telp Kantor</label>
                                        <input type="text" name="telephone" class="form-control form-control-custom" placeholder="021-...">
                                    </div>

                                    <div class="col-12 col-md-6">
                                        <label class="form-floating-label"><i class="fa-solid fa-envelope text-muted"></i> Email</label>
                                        <input type="email" name="email" class="form-control form-control-custom" placeholder="vendor@example.com">
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <label class="form-floating-label"><i class="fa-solid fa-globe text-muted"></i> Website / Cek Resi</label>
                                        <input type="url" name="website_tracking_url" class="form-control form-control-custom" placeholder="https://...">
                                    </div>

                                    <div class="col-12">
                                        <label class="form-floating-label"><i class="fa-solid fa-map-pin text-muted"></i> Alamat Kantor / Drop Point Cabang</label>
                                        <textarea name="office_address" rows="2" class="form-control form-control-custom" placeholder="Alamat lengkap hub atau cabang drop point..."></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="modal-section-card mb-0">
                            <div class="modal-section-header">
                                <span class="fw-bold small text-uppercase text-muted d-flex align-items-center gap-2">
                                    <i class="fa-solid fa-credit-card"></i> Termin Pembayaran & Operasional
                                </span>
                            </div>
                            <div class="modal-section-body">
                                <div class="row g-3">
                                    <div class="col-12 col-md-6">
                                        <label class="form-floating-label">Layanan Yang Didukung (Pisahkan Koma)</label>
                                        <input type="text" name="supported_services" class="form-control form-control-custom" placeholder="Cargo Darat, Cargo Laut, Door to Door">
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <label class="form-floating-label">Termin Pembayaran <span class="text-danger">*</span></label>
                                        <select name="payment_terms" class="form-select form-control-custom">
                                            <option value="tempo_30" selected>Tempo 30 Hari</option>
                                            <option value="tempo_14">Tempo 14 Hari</option>
                                            <option value="monthly_invoice">Tagihan Bulanan (Invoice Rekap)</option>
                                            <option value="cash_cod">Cash / COD</option>
                                        </select>
                                    </div>

                                    <div class="col-12">
                                        <label class="form-floating-label">Catatan Tambahan</label>
                                        <input type="text" name="notes" class="form-control form-control-custom" placeholder="Spesialisasi rute, catatan armada...">
                                    </div>

                                    <div class="col-12">
                                        <div class="form-check form-switch mt-1">
                                            <input class="form-check-input" type="checkbox" name="is_active" id="create_is_active" checked>
                                            <label class="form-check-label small fw-bold" for="create_is_active">Aktifkan Vendor Ini</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer bg-white border-top py-3 px-4 d-flex justify-content-between align-items-center">
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
    </div>

    <!-- MODAL EDIT VENDOR (PREMIUM DESIGN) -->
    <div class="modal fade" id="editVendorModal" tabindex="-1" aria-labelledby="editVendorModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content rounded-4 border-0 shadow-lg overflow-hidden">
                <form id="editVendorForm" method="POST" action="">
                    @csrf
                    @method('PUT')
                    
                    <div class="modal-hero-header warning-header text-white d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-3">
                            <div class="bg-white bg-opacity-20 p-2.5 rounded-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                                <i class="fa-solid fa-pen-to-square fs-4 text-white"></i>
                            </div>
                            <div>
                                <h5 class="modal-title fw-bold text-white mb-0" id="editVendorModalLabel">Edit Master Vendor</h5>
                                <p class="text-white text-opacity-75 small mb-0 mt-0.5">Perbarui kontak agen, alamat drop point atau termin pembayaran.</p>
                            </div>
                        </div>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body p-4 bg-light">
                        <div class="modal-section-card">
                            <div class="modal-section-header">
                                <span class="fw-bold small text-uppercase text-warning d-flex align-items-center gap-2">
                                    <i class="fa-solid fa-id-card"></i> Identitas & Profil
                                </span>
                            </div>
                            <div class="modal-section-body">
                                <div class="row g-3">
                                    <div class="col-12 col-md-4">
                                        <label class="form-floating-label">Kode Vendor <span class="text-danger">*</span></label>
                                        <input type="text" name="vendor_code" id="edit_vendor_code" required class="form-control form-control-custom font-monospace text-uppercase">
                                    </div>
                                    <div class="col-12 col-md-5">
                                        <label class="form-floating-label">Nama Vendor <span class="text-danger">*</span></label>
                                        <input type="text" name="vendor_name" id="edit_vendor_name" required class="form-control form-control-custom">
                                    </div>
                                    <div class="col-12 col-md-3">
                                        <label class="form-floating-label">Jenis Layanan <span class="text-danger">*</span></label>
                                        <select name="vendor_type" id="edit_vendor_type" class="form-select form-control-custom">
                                            <option value="cargo">Kargo Ekspedisi</option>
                                            <option value="trucking_charter">Sewa Truk (Charter)</option>
                                            <option value="courier">Kurir Express</option>
                                            <option value="forwarder">Freight Forwarder</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="modal-section-card">
                            <div class="modal-section-header">
                                <span class="fw-bold small text-uppercase text-secondary d-flex align-items-center gap-2">
                                    <i class="fa-solid fa-address-book"></i> Kontak & Drop Point
                                </span>
                            </div>
                            <div class="modal-section-body">
                                <div class="row g-3">
                                    <div class="col-12 col-md-4">
                                        <label class="form-floating-label">Contact Person (PIC)</label>
                                        <input type="text" name="contact_person" id="edit_contact_person" class="form-control form-control-custom">
                                    </div>
                                    <div class="col-12 col-md-4">
                                        <label class="form-floating-label">No. HP / WA</label>
                                        <input type="text" name="phone_number" id="edit_phone_number" class="form-control form-control-custom">
                                    </div>
                                    <div class="col-12 col-md-4">
                                        <label class="form-floating-label">Telp Kantor</label>
                                        <input type="text" name="telephone" id="edit_telephone" class="form-control form-control-custom">
                                    </div>

                                    <div class="col-12 col-md-6">
                                        <label class="form-floating-label">Email</label>
                                        <input type="email" name="email" id="edit_email" class="form-control form-control-custom">
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <label class="form-floating-label">Website / Tracking</label>
                                        <input type="url" name="website_tracking_url" id="edit_website_tracking_url" class="form-control form-control-custom">
                                    </div>

                                    <div class="col-12">
                                        <label class="form-floating-label">Alamat Kantor / Drop Point</label>
                                        <textarea name="office_address" id="edit_office_address" rows="2" class="form-control form-control-custom"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="modal-section-card mb-0">
                            <div class="modal-section-header">
                                <span class="fw-bold small text-uppercase text-muted d-flex align-items-center gap-2">
                                    <i class="fa-solid fa-credit-card"></i> Termin Pembayaran
                                </span>
                            </div>
                            <div class="modal-section-body">
                                <div class="row g-3">
                                    <div class="col-12 col-md-6">
                                        <label class="form-floating-label">Layanan Yang Didukung</label>
                                        <input type="text" name="supported_services" id="edit_supported_services" class="form-control form-control-custom">
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <label class="form-floating-label">Termin Pembayaran <span class="text-danger">*</span></label>
                                        <select name="payment_terms" id="edit_payment_terms" class="form-select form-control-custom">
                                            <option value="tempo_30">Tempo 30 Hari</option>
                                            <option value="tempo_14">Tempo 14 Hari</option>
                                            <option value="monthly_invoice">Tagihan Bulanan (Invoice Rekap)</option>
                                            <option value="cash_cod">Cash / COD</option>
                                        </select>
                                    </div>

                                    <div class="col-12">
                                        <label class="form-floating-label">Catatan</label>
                                        <input type="text" name="notes" id="edit_notes" class="form-control form-control-custom">
                                    </div>

                                    <div class="col-12">
                                        <div class="form-check form-switch mt-1">
                                            <input class="form-check-input" type="checkbox" name="is_active" id="edit_is_active">
                                            <label class="form-check-label small fw-bold" for="edit_is_active">Aktifkan Vendor Ini</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer bg-white border-top py-3 px-4 d-flex justify-content-between align-items-center">
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
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
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
                    document.getElementById('edit_website_tracking_url').value = vendor.website_tracking_url || '';
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
        });
    </script>
    @endpush
</x-app-layout>
