<x-app-layout>
    <style>
        #packagingSummaryPage {
            --pkg-primary: #ff6b1a;
            --pkg-primary-dark: #e85a0c;
            --pkg-primary-soft: #fff2e9;
            --pkg-blue: #1769e8;
            --pkg-green: #159867;
            --pkg-yellow: #f5a800;
            --pkg-cyan: #0db4c8;
            --pkg-ink: #172033;
            --pkg-muted: #6c7688;
            --pkg-line: #e7ebf1;
            --pkg-surface: var(--bs-body-bg);
            --pkg-soft: rgba(248, 250, 252, .78);
            color: var(--pkg-ink);
        }

        [data-bs-theme="dark"] #packagingSummaryPage {
            --pkg-ink: #f3f5f8;
            --pkg-muted: #aeb7c6;
            --pkg-line: rgba(255, 255, 255, .10);
            --pkg-soft: rgba(255, 255, 255, .035);
        }

        #packagingSummaryPage .pkg-page-head {
            margin-bottom: 1.15rem;
        }

        #packagingSummaryPage .pkg-eyebrow {
            display: inline-flex;
            align-items: center;
            gap: .45rem;
            color: var(--pkg-primary);
            font-size: .72rem;
            font-weight: 800;
            letter-spacing: .09em;
            text-transform: uppercase;
            margin-bottom: .35rem;
        }

        #packagingSummaryPage .pkg-title {
            font-size: clamp(1.35rem, 2.2vw, 1.9rem);
            font-weight: 800;
            letter-spacing: -.03em;
            margin: 0;
        }

        #packagingSummaryPage .pkg-subtitle {
            margin: .35rem 0 0;
            color: var(--pkg-muted);
            font-size: .9rem;
        }

        #packagingSummaryPage .pkg-btn-main {
            min-height: 40px;
            border: 0;
            border-radius: 10px;
            background: linear-gradient(135deg, var(--pkg-primary), #ff8642);
            color: #fff;
            box-shadow: 0 8px 20px rgba(255, 107, 26, .22);
            font-size: .78rem;
            font-weight: 800;
            letter-spacing: .015em;
            padding: .62rem .95rem;
        }

        #packagingSummaryPage .pkg-btn-main:hover {
            background: linear-gradient(135deg, var(--pkg-primary-dark), var(--pkg-primary));
            color: #fff;
            transform: translateY(-1px);
        }

        #packagingSummaryPage .pkg-btn-secondary {
            min-height: 40px;
            border-radius: 10px;
            font-size: .78rem;
            font-weight: 700;
            padding: .62rem .9rem;
        }

        #packagingSummaryPage .pkg-card {
            background: var(--pkg-surface);
            border: 1px solid var(--pkg-line);
            border-radius: 14px;
            box-shadow: 0 4px 16px rgba(20, 32, 51, .045);
        }

        #packagingSummaryPage .pkg-kpi {
            position: relative;
            min-height: 112px;
            overflow: hidden;
            transition: transform .18s ease, box-shadow .18s ease;
        }

        #packagingSummaryPage .pkg-kpi:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 26px rgba(20, 32, 51, .08);
        }

        #packagingSummaryPage .pkg-kpi::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--accent);
        }

        #packagingSummaryPage .pkg-kpi::after {
            content: "";
            position: absolute;
            right: -28px;
            bottom: -42px;
            width: 110px;
            height: 110px;
            border-radius: 50%;
            background: var(--accent-soft);
        }

        #packagingSummaryPage .pkg-kpi .card-body {
            position: relative;
            z-index: 1;
            padding: 1.05rem 1rem;
        }

        #packagingSummaryPage .pkg-icon {
            width: 46px;
            height: 46px;
            border-radius: 13px;
            display: inline-flex;
            justify-content: center;
            align-items: center;
            flex: 0 0 46px;
            color: var(--accent);
            background: var(--accent-soft);
            font-size: 1.1rem;
        }

        #packagingSummaryPage .pkg-kpi-label {
            font-size: .68rem;
            font-weight: 800;
            letter-spacing: .08em;
            text-transform: uppercase;
            color: var(--pkg-muted);
        }

        #packagingSummaryPage .pkg-kpi-value {
            font-size: 1.65rem;
            font-weight: 800;
            line-height: 1.05;
            letter-spacing: -.03em;
        }

        #packagingSummaryPage .pkg-kpi-meta {
            color: var(--pkg-muted);
            font-size: .72rem;
        }

        #packagingSummaryPage .pkg-cover-card {
            min-height: 350px;
            overflow: hidden;
            position: relative;
        }

        #packagingSummaryPage .pkg-cover-card img {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        #packagingSummaryPage .pkg-cover-card::after {
            content: "";
            position: absolute;
            inset: 0;
            background: linear-gradient(180deg, rgba(12, 19, 31, .02) 35%, rgba(12, 19, 31, .78) 100%);
        }

        #packagingSummaryPage .pkg-cover-caption {
            position: absolute;
            z-index: 2;
            left: 1.1rem;
            right: 1.1rem;
            bottom: 1.05rem;
            color: #fff;
        }

        #packagingSummaryPage .pkg-cover-chip {
            display: inline-flex;
            align-items: center;
            gap: .4rem;
            padding: .38rem .6rem;
            border-radius: 999px;
            background: rgba(255,255,255,.14);
            border: 1px solid rgba(255,255,255,.22);
            backdrop-filter: blur(8px);
            font-size: .68rem;
            font-weight: 700;
        }

        #packagingSummaryPage .pkg-section-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: .75rem;
            margin-bottom: .8rem;
        }

        #packagingSummaryPage .pkg-section-title {
            font-size: .92rem;
            font-weight: 800;
            margin: 0;
        }

        #packagingSummaryPage .pkg-section-icon {
            width: 34px;
            height: 34px;
            border-radius: 10px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: var(--pkg-primary);
            background: var(--pkg-primary-soft);
        }

        #packagingSummaryPage .pkg-summary-list {
            margin: 0;
        }

        #packagingSummaryPage .pkg-summary-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: .85rem;
            padding: .75rem 0;
            border-bottom: 1px solid var(--pkg-line);
            font-size: .8rem;
        }

        #packagingSummaryPage .pkg-summary-row:last-child {
            border-bottom: 0;
        }

        #packagingSummaryPage .pkg-summary-name {
            display: flex;
            align-items: center;
            gap: .62rem;
            min-width: 0;
            color: var(--pkg-muted);
        }

        #packagingSummaryPage .pkg-summary-name i {
            width: 17px;
            text-align: center;
        }

        #packagingSummaryPage .pkg-summary-value {
            font-weight: 800;
            text-align: right;
            white-space: nowrap;
        }

        #packagingSummaryPage .pkg-chart-card .card-body {
            padding: 1rem 1.05rem;
        }

        #packagingSummaryPage .pkg-donut {
            width: 118px;
            height: 118px;
            position: relative;
            flex: 0 0 118px;
        }

        #packagingSummaryPage .pkg-donut svg {
            width: 100%;
            height: 100%;
            transform: rotate(-90deg);
        }

        #packagingSummaryPage .pkg-donut-center {
            position: absolute;
            inset: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        #packagingSummaryPage .pkg-donut-total {
            font-size: 1.45rem;
            line-height: 1;
            font-weight: 800;
        }

        #packagingSummaryPage .pkg-legend-row {
            display: flex;
            justify-content: space-between;
            gap: 1rem;
            margin-bottom: .58rem;
            font-size: .72rem;
        }

        #packagingSummaryPage .pkg-legend-row:last-child {
            margin-bottom: 0;
        }

        #packagingSummaryPage .pkg-dot {
            width: 9px;
            height: 9px;
            border-radius: 50%;
            display: inline-block;
            margin-right: .42rem;
        }

        #packagingSummaryPage .pkg-trend-item + .pkg-trend-item {
            margin-top: .9rem;
        }

        #packagingSummaryPage .pkg-trend-label {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: .38rem;
            font-size: .75rem;
            color: var(--pkg-muted);
        }

        #packagingSummaryPage .pkg-progress {
            height: 7px;
            border-radius: 999px;
            overflow: hidden;
            background: var(--pkg-line);
        }

        #packagingSummaryPage .pkg-progress > span {
            display: block;
            height: 100%;
            border-radius: inherit;
            background: linear-gradient(90deg, var(--pkg-primary), #ff9a60);
        }

        #packagingSummaryPage .pkg-table-card {
            overflow: hidden;
        }

        #packagingSummaryPage .pkg-table-toolbar {
            padding: .95rem 1rem;
            border-bottom: 1px solid var(--pkg-line);
            background: var(--pkg-surface);
        }

        #packagingSummaryPage .pkg-filter-control {
            min-height: 36px;
            border-radius: 9px;
            border-color: var(--pkg-line);
            font-size: .77rem;
            box-shadow: none;
        }

        #packagingSummaryPage .pkg-filter-control:focus {
            border-color: rgba(255, 107, 26, .55);
            box-shadow: 0 0 0 .2rem rgba(255, 107, 26, .10);
        }

        #packagingSummaryPage .pkg-search-group {
            min-width: 220px;
        }

        #packagingSummaryPage .pkg-search-group .input-group-text,
        #packagingSummaryPage .pkg-search-group .form-control {
            border-color: var(--pkg-line);
            background: var(--pkg-surface);
            font-size: .77rem;
            min-height: 36px;
        }

        #packagingSummaryPage .pkg-search-group .input-group-text {
            border-radius: 9px 0 0 9px;
        }

        #packagingSummaryPage .pkg-search-group .form-control {
            border-radius: 0 9px 9px 0;
        }

        #packagingSummaryPage .pkg-table thead th {
            background: #202b3c;
            color: #fff;
            border: 0;
            font-size: .68rem;
            font-weight: 800;
            letter-spacing: .035em;
            text-transform: uppercase;
            padding: .8rem .9rem;
            white-space: nowrap;
        }

        #packagingSummaryPage .pkg-table tbody td {
            padding: .82rem .9rem;
            border-color: var(--pkg-line);
            font-size: .78rem;
            vertical-align: middle;
        }

        #packagingSummaryPage .pkg-table tbody tr {
            transition: background-color .15s ease;
        }

        #packagingSummaryPage .pkg-table tbody tr:hover {
            background: var(--pkg-primary-soft);
        }

        #packagingSummaryPage .pkg-number {
            width: 32px;
            height: 32px;
            border-radius: 9px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: var(--pkg-soft);
            border: 1px solid var(--pkg-line);
            font-weight: 800;
            color: var(--pkg-muted);
        }

        #packagingSummaryPage .pkg-type-badge {
            display: inline-flex;
            align-items: center;
            gap: .38rem;
            padding: .4rem .58rem;
            border-radius: 8px;
            color: var(--pkg-blue);
            background: rgba(23, 105, 232, .09);
            border: 1px solid rgba(23, 105, 232, .20);
            font-size: .68rem;
            font-weight: 800;
        }

        #packagingSummaryPage .pkg-dimension {
            display: inline-flex;
            align-items: center;
            gap: .4rem;
            padding: .38rem .55rem;
            border-radius: 8px;
            background: var(--pkg-soft);
            border: 1px solid var(--pkg-line);
            color: var(--pkg-muted);
            font-family: var(--bs-font-monospace);
            font-size: .7rem;
        }

        #packagingSummaryPage .pkg-action-btn {
            width: 34px;
            height: 34px;
            padding: 0;
            border-radius: 9px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: 1px solid rgba(255, 107, 26, .25);
            color: var(--pkg-primary);
            background: var(--pkg-primary-soft);
        }

        #packagingSummaryPage .pkg-action-btn:hover {
            background: var(--pkg-primary);
            color: #fff;
            border-color: var(--pkg-primary);
        }

        #packagingSummaryPage .pkg-table-footer {
            padding: .8rem 1rem;
            border-top: 1px solid var(--pkg-line);
            font-size: .73rem;
            color: var(--pkg-muted);
        }

        @media (max-width: 991.98px) {
            #packagingSummaryPage .pkg-cover-card {
                min-height: 280px;
            }
        }

        @media (max-width: 767.98px) {
            #packagingSummaryPage .pkg-page-actions,
            #packagingSummaryPage .pkg-page-actions > * {
                width: 100%;
            }

            #packagingSummaryPage .pkg-search-group {
                min-width: 100%;
                width: 100%;
            }

            #packagingSummaryPage .pkg-filter-wrap > * {
                flex: 1 1 135px;
            }

            #packagingSummaryPage .pkg-summary-row {
                align-items: flex-start;
            }
        }
    </style>

    <div id="packagingSummaryPage">
        <div class="pkg-page-head d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 p-3 rounded-4 shadow-sm mb-3" style="background: linear-gradient(135deg, var(--pkg-surface) 0%, rgba(255, 107, 26, 0.08) 100%); border: 1px solid var(--pkg-line); position: sticky; top: 1rem; z-index: 1020; overflow: hidden; backdrop-filter: blur(10px);">
            <!-- Decorative Background Element -->
            <div style="position: absolute; top: -30px; right: -20px; width: 250px; height: 250px; background: radial-gradient(circle, rgba(255, 107, 26, 0.15) 0%, rgba(255,255,255,0) 70%); border-radius: 50%; pointer-events: none; z-index: 0;"></div>
            <div style="position: absolute; bottom: -50px; left: 10%; width: 150px; height: 150px; background: radial-gradient(circle, rgba(23, 105, 232, 0.1) 0%, rgba(255,255,255,0) 70%); border-radius: 50%; pointer-events: none; z-index: 0;"></div>
            
            <div style="position: relative; z-index: 1;">
                <div class="pkg-eyebrow mb-2" style="background: rgba(255, 107, 26, 0.15); padding: 4px 12px; border-radius: 20px; border: 1px solid rgba(255, 107, 26, 0.2);">
                    <i class="fa-solid fa-chart-pie"></i>
                    Packaging Dashboard
                </div>
                <h1 class="pkg-title fw-bolder mb-2" style="color: var(--pkg-ink);">Packaging Calculation Summary</h1>
            </div>

            <div class="pkg-page-actions d-flex flex-wrap gap-3" style="position: relative; z-index: 1;">
                @if(auth()->check() && auth()->user()->hasRole(['administrator', 'admin staff']))
                    <a href="{{ route('packaging.validasi_data.index') }}"
                       class="btn btn-light pkg-btn-secondary d-inline-flex align-items-center justify-content-center gap-2 shadow-sm border" style="border-radius: 12px; padding: 10px 20px;">
                        <i class="fa-solid fa-clipboard-check text-primary"></i>
                        <span class="fw-bold">DATA VALIDASI</span>
                    </a>
                @endif

                <a href="{{ route('packaging.calculations.create') }}"
                   class="btn pkg-btn-main d-inline-flex align-items-center justify-content-center gap-2 shadow" style="border-radius: 12px; padding: 10px 24px; font-size: 0.85rem;">
                    <i class="fa-solid fa-circle-plus fs-6"></i>
                    CRATE CALCULATION
                </a>
            </div>
        </div>

        <div class="row g-3 mb-3">
            <div class="col-xl-8">
                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="card pkg-card pkg-kpi h-100" style="--accent: var(--pkg-blue); --accent-soft: rgba(23,105,232,.10);">
                            <div class="card-body d-flex align-items-center gap-3">
                                <div class="pkg-icon"><i class="fa-solid fa-file-circle-plus"></i></div>
                                <div>
                                    <div class="pkg-kpi-label">Permintaan Baru</div>
                                    <div class="pkg-kpi-value mt-1">36</div>
                                    <div class="pkg-kpi-meta mt-1"><i class="fa-solid fa-arrow-trend-up me-1"></i>12.7% dari total</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card pkg-card pkg-kpi h-100" style="--accent: var(--pkg-yellow); --accent-soft: rgba(245,168,0,.12);">
                            <div class="card-body d-flex align-items-center gap-3">
                                <div class="pkg-icon"><i class="fa-solid fa-gears"></i></div>
                                <div>
                                    <div class="pkg-kpi-label">Dalam Proses</div>
                                    <div class="pkg-kpi-value mt-1">94</div>
                                    <div class="pkg-kpi-meta mt-1"><i class="fa-regular fa-clock me-1"></i>33.1% dari total</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card pkg-card pkg-kpi h-100" style="--accent: var(--pkg-green); --accent-soft: rgba(21,152,103,.11);">
                            <div class="card-body d-flex align-items-center gap-3">
                                <div class="pkg-icon"><i class="fa-solid fa-circle-check"></i></div>
                                <div>
                                    <div class="pkg-kpi-label">Siap Kirim</div>
                                    <div class="pkg-kpi-value mt-1">78</div>
                                    <div class="pkg-kpi-meta mt-1"><i class="fa-solid fa-truck-fast me-1"></i>27.5% dari total</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-3 mt-0">
                    <div class="col-md-5">
                        <div class="pkg-card pkg-cover-card h-100">
                            <img src="https://images.unsplash.com/photo-1587293852726-70cdb56c2866?auto=format&fit=crop&q=85&w=1000"
                                 alt="Warehouse packaging area">
                            <div class="pkg-cover-caption">
                                <span class="pkg-cover-chip"><i class="fa-solid fa-warehouse"></i> Packaging Overview</span>
                                <h5 class="fw-bold mb-1 mt-2">Aktivitas Packaging Terpantau</h5>
                                <p class="mb-0 small opacity-75">Pantau kebutuhan, proses, dan kesiapan pengiriman dari satu dashboard.</p>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-7">
                        <div class="card pkg-card h-100">
                            <div class="card-body p-3 p-xl-4">
                                <div class="pkg-section-head">
                                    <div>
                                        <h6 class="pkg-section-title">Ringkasan Permintaan</h6>
                                        <div class="small text-muted mt-1">Indikator aktivitas packaging terbaru</div>
                                    </div>
                                    <span class="pkg-section-icon"><i class="fa-solid fa-list-check"></i></span>
                                </div>

                                <div class="pkg-summary-list">
                                    <div class="pkg-summary-row">
                                        <div class="pkg-summary-name"><i class="fa-regular fa-calendar"></i><span>Total request bulan ini</span></div>
                                        <div class="pkg-summary-value">68 <span class="text-success ms-1" style="font-size:.67rem;"><i class="fa-solid fa-caret-up"></i> 15.3%</span></div>
                                    </div>
                                    <div class="pkg-summary-row">
                                        <div class="pkg-summary-name"><i class="fa-solid fa-users"></i><span>Customer aktif</span></div>
                                        <div class="pkg-summary-value">42</div>
                                    </div>
                                    <div class="pkg-summary-row">
                                        <div class="pkg-summary-name"><i class="fa-solid fa-location-dot"></i><span>Tujuan terbanyak</span></div>
                                        <div class="pkg-summary-value">Surabaya</div>
                                    </div>
                                    <div class="pkg-summary-row">
                                        <div class="pkg-summary-name"><i class="fa-solid fa-box"></i><span>Tipe dominan</span></div>
                                        <div class="pkg-summary-value">Wooden Crate + Pallet</div>
                                    </div>
                                    <div class="pkg-summary-row">
                                        <div class="pkg-summary-name"><i class="fa-solid fa-triangle-exclamation text-danger"></i><span>Permintaan urgent</span></div>
                                        <div class="pkg-summary-value">26 <span class="text-muted fw-normal" style="font-size:.67rem;">(9.2%)</span></div>
                                    </div>
                                    <div class="pkg-summary-row">
                                        <div class="pkg-summary-name"><i class="fa-regular fa-clock"></i><span>Rata-rata lead time</span></div>
                                        <div class="pkg-summary-value">4.6 hari</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-4">
                <div class="row g-3 h-100">
                    <div class="col-md-6 col-xl-12">
                        <div class="card pkg-card pkg-chart-card h-100">
                            <div class="card-body">
                                <div class="pkg-section-head mb-3">
                                    <div>
                                        <h6 class="pkg-section-title">Komposisi Tipe Packaging</h6>
                                        <div class="small text-muted mt-1">Distribusi dari total request</div>
                                    </div>
                                    <span class="pkg-section-icon" style="color:var(--pkg-cyan);background:rgba(13,180,200,.10);"><i class="fa-solid fa-chart-doughnut"></i></span>
                                </div>

                                <div class="d-flex align-items-center gap-3">
                                    <div class="pkg-donut">
                                        <svg viewBox="0 0 36 36" aria-label="Komposisi tipe packaging">
                                            <circle cx="18" cy="18" r="15.9155" fill="none" stroke="var(--pkg-line)" stroke-width="5"></circle>
                                            <circle cx="18" cy="18" r="15.9155" fill="none" stroke="#0ea5e9" stroke-width="5" stroke-dasharray="46 54" stroke-dashoffset="0"></circle>
                                            <circle cx="18" cy="18" r="15.9155" fill="none" stroke="#10b981" stroke-width="5" stroke-dasharray="28 72" stroke-dashoffset="-46"></circle>
                                            <circle cx="18" cy="18" r="15.9155" fill="none" stroke="#f59e0b" stroke-width="5" stroke-dasharray="14 86" stroke-dashoffset="-74"></circle>
                                            <circle cx="18" cy="18" r="15.9155" fill="none" stroke="#6366f1" stroke-width="5" stroke-dasharray="12 88" stroke-dashoffset="-88"></circle>
                                        </svg>
                                        <div class="pkg-donut-center">
                                            <span class="pkg-donut-total">284</span>
                                            <span class="text-muted" style="font-size:.62rem;">Total</span>
                                        </div>
                                    </div>

                                    <div class="flex-grow-1">
                                        <div class="pkg-legend-row"><span><span class="pkg-dot" style="background:#0ea5e9;"></span>W.Crate + Pallet</span><strong>46%</strong></div>
                                        <div class="pkg-legend-row"><span><span class="pkg-dot" style="background:#10b981;"></span>W.Crate</span><strong>28%</strong></div>
                                        <div class="pkg-legend-row"><span><span class="pkg-dot" style="background:#f59e0b;"></span>Steel Crate</span><strong>14%</strong></div>
                                        <div class="pkg-legend-row"><span><span class="pkg-dot" style="background:#6366f1;"></span>Skid / Frame</span><strong>12%</strong></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 col-xl-12">
                        <div class="card pkg-card pkg-chart-card h-100">
                            <div class="card-body">
                                <div class="pkg-section-head mb-3">
                                    <div>
                                        <h6 class="pkg-section-title">Tren Permintaan</h6>
                                        <div class="small text-muted mt-1">Perbandingan 4 bulan terakhir</div>
                                    </div>
                                    <span class="pkg-section-icon"><i class="fa-solid fa-arrow-trend-up"></i></span>
                                </div>

                                <div class="pkg-trend-item">
                                    <div class="pkg-trend-label"><span>Apr</span><strong>233</strong></div>
                                    <div class="pkg-progress"><span style="width:82%;"></span></div>
                                </div>
                                <div class="pkg-trend-item">
                                    <div class="pkg-trend-label"><span>Mei</span><strong>205</strong></div>
                                    <div class="pkg-progress"><span style="width:72%;"></span></div>
                                </div>
                                <div class="pkg-trend-item">
                                    <div class="pkg-trend-label"><span>Jun</span><strong>241</strong></div>
                                    <div class="pkg-progress"><span style="width:85%;"></span></div>
                                </div>
                                <div class="pkg-trend-item">
                                    <div class="pkg-trend-label"><span class="fw-bold text-body">Jul</span><strong style="color:var(--pkg-primary);">284</strong></div>
                                    <div class="pkg-progress"><span style="width:100%;"></span></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card pkg-card pkg-table-card mt-3">
            <div class="pkg-table-toolbar d-flex flex-column flex-xl-row justify-content-between align-items-xl-center gap-3">
                <div class="d-flex align-items-center gap-3">
                    <span class="pkg-section-icon"><i class="fa-solid fa-list-check"></i></span>
                    <div>
                        <h6 class="pkg-section-title mb-0">List Data Permintaan Packaging</h6>
                        <div class="small text-muted mt-1">Daftar request dan detail ukuran packaging</div>
                    </div>
                </div>

                <div class="pkg-filter-wrap d-flex flex-wrap justify-content-xl-end gap-2">
                    <div class="input-group pkg-search-group">
                        <span class="input-group-text border-end-0 text-muted"><i class="fa-solid fa-magnifying-glass"></i></span>
                        <input type="text" class="form-control border-start-0 text-body" placeholder="Cari project atau tipe...">
                    </div>
                    <input type="date" class="form-control pkg-filter-control" style="width:145px;">
                    <select class="form-select pkg-filter-control" style="width:125px;">
                        <option>10 / halaman</option>
                        <option>20 / halaman</option>
                        <option>50 / halaman</option>
                    </select>
                    <button type="button" class="btn btn-outline-danger pkg-filter-control d-inline-flex align-items-center justify-content-center gap-2 px-3">
                        <i class="fa-solid fa-rotate-right"></i> Reset
                    </button>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table pkg-table table-hover align-middle mb-0 text-nowrap">
                    <thead>
                        <tr>
                            <th class="text-center" style="width:64px;">No</th>
                            <th>No Packaging</th>
                            <th>No SO</th>
                            <th>Customer</th>
                            <th>Tgl Delivery</th>
                            <th>Dimension</th>
                            <th>Status</th>
                            <th class="text-center" style="width:80px;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($packagingJobs ?? [] as $index => $job)
                            @php
                                $firstDetail = $job->details->first();
                            @endphp
                            <tr>
                                <td class="text-center"><span class="pkg-number">{{ $index + 1 }}</span></td>
                                <td>
                                    <div class="fw-bold text-primary">{{ $firstDetail ? $firstDetail->packaging_number : '-' }}</div>
                                </td>
                                <td>
                                    <div class="fw-semibold">{{ $job->no_so }}</div>
                                    <div class="text-muted" style="font-size:.65rem;">{{ $job->created_at->format('d M Y H:i') }}</div>
                                </td>
                                <td>
                                    <div class="fw-semibold">{{ $job->customer ?? '-' }}</div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="pkg-section-icon" style="width:26px;height:26px;color:var(--pkg-muted);background:var(--pkg-soft);">
                                            <i class="fa-regular fa-calendar-days" style="font-size:0.7rem;"></i>
                                        </span>
                                        <span class="fw-semibold" style="font-size:0.75rem;">{{ $job->date_delivery ? $job->date_delivery->format('d M Y') : '-' }}</span>
                                    </div>
                                </td>
                                <td>
                                    <div class="text-muted" style="font-size:0.8rem;">
                                        {{ $firstDetail ? ($firstDetail->panjang ?? 0) . ' × ' . ($firstDetail->lebar ?? 0) . ' × ' . ($firstDetail->tinggi ?? 0) . ' mm' : '-' }}
                                    </div>
                                </td>
                                <td>
                                    <span class="badge {{ $job->status == 'draft' ? 'bg-secondary' : 'bg-success' }} bg-opacity-10 text-{{ $job->status == 'draft' ? 'secondary' : 'success' }} px-2 py-1 rounded-pill">
                                        {{ ucfirst($job->status) }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-light border" type="button" id="actionDropdown{{ $job->id }}" data-bs-toggle="dropdown" aria-expanded="false" data-bs-boundary="window" data-bs-popper-config='{"strategy":"fixed"}'>
                                            <i class="fa-solid fa-ellipsis-vertical"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0" aria-labelledby="actionDropdown{{ $job->id }}">
                                            <li>
                                                <a class="dropdown-item d-flex align-items-center gap-2" href="{{ route('packaging.calculations.show', $job->id) }}">
                                                    <i class="fa-solid fa-eye text-muted"></i> Show
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item d-flex align-items-center gap-2" href="{{ route('packaging.edit', $job->id) }}">
                                                    <i class="fa-solid fa-pen-to-square text-primary"></i> Edit
                                                </a>
                                            </li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li>
                                                <form action="{{ route('packaging.destroy', $job->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="dropdown-item d-flex align-items-center gap-2 text-danger">
                                                        <i class="fa-solid fa-trash"></i> Delete
                                                    </button>
                                                </form>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5">
                                    <div class="d-flex flex-column align-items-center justify-content-center text-muted">
                                        <span class="pkg-section-icon mb-3" style="width:54px;height:54px;font-size:1.25rem;">
                                            <i class="fa-solid fa-box-open"></i>
                                        </span>
                                        <h6 class="fw-bold text-body">Data Belum Tersedia</h6>
                                        <p class="small mb-3">Belum ada data permintaan packaging yang ditambahkan.</p>
                                        <a href="{{ route('packaging.calculations.create') }}" class="btn pkg-btn-main d-inline-flex align-items-center gap-2">
                                            <i class="fa-solid fa-circle-plus"></i> Tambah Calculation
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="pkg-table-footer d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2">
                <span>Menampilkan <strong class="text-body">{{ count($calculations ?? []) }}</strong> data permintaan</span>
                <div class="d-flex align-items-center gap-2">
                    <span>Halaman 1</span>
                    <div class="d-flex gap-1">
                        <button type="button" class="btn btn-sm btn-light border" disabled><i class="fa-solid fa-chevron-left"></i></button>
                        <button type="button" class="btn btn-sm btn-light border" disabled><i class="fa-solid fa-chevron-right"></i></button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>