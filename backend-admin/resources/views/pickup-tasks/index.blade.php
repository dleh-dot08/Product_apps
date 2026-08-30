<x-app-layout>
    <style>
        :root {
            --orange-50: #fff7ed;
            --orange-100: #ffedd5;
            --orange-200: #fed7aa;
            --orange-400: #fb923c;
            --orange-500: #f97316;
            --orange-600: #ea580c;
            --orange-700: #c2410c;
            --orange-800: #9a3412;
            --slate-50: #f8fafc;
            --slate-100: #f1f5f9;
            --slate-200: #e2e8f0;
            --slate-500: #64748b;
            --slate-700: #334155;
            --slate-800: #1e293b;
            --slate-900: #0f172a;
            --app-border: #e8edf3;
            --app-card: #ffffff;
            --app-muted: #64748b;
            --app-text: #172033;
        }

        html[data-bs-theme="dark"] {
            --app-border: #2b2f32;
            --app-card: #0d0f12;
            --app-muted: #94a3b8;
            --app-text: #f8fafc;
        }

        .pickup-page {
            min-height: 100vh;
            background:
                radial-gradient(circle at top right, rgba(249, 115, 22, .08), transparent 25%),
                linear-gradient(180deg, #fffaf6 0%, #f8fafc 260px, #f8fafc 100%);
        }

        html[data-bs-theme="dark"] .pickup-page {
            background: radial-gradient(circle at top right, rgba(249, 115, 22, .12), transparent 26%), #2b2f32;
        }

        .page-shell {
            max-width: 1600px;
            margin: 0 auto;
        }

        .bg-orange-gradient {
            background: linear-gradient(135deg, var(--orange-500) 0%, var(--orange-700) 100%);
        }

        .text-orange { color: var(--orange-600) !important; }
        .bg-orange { background-color: var(--orange-600) !important; }

        .btn-orange {
            background: linear-gradient(135deg, var(--orange-500), var(--orange-600));
            color: #fff;
            border: 0;
            box-shadow: 0 8px 20px rgba(234, 88, 12, .18);
            transition: .2s ease;
        }

        .btn-orange:hover,
        .btn-orange:focus {
            background: linear-gradient(135deg, var(--orange-600), var(--orange-700));
            color: #fff;
            transform: translateY(-1px);
            box-shadow: 0 10px 24px rgba(234, 88, 12, .26);
        }

        .btn-outline-orange {
            color: var(--orange-600);
            border-color: var(--orange-300, #fdba74);
            background: transparent;
        }

        .btn-outline-orange:hover {
            background: var(--orange-600);
            border-color: var(--orange-600);
            color: #fff;
        }

        .hero-panel {
            position: relative;
            overflow: hidden;
            border: 1px solid rgba(249, 115, 22, .12);
            border-radius: 24px;
            padding: 24px 26px;
            background: linear-gradient(135deg, rgba(255,255,255,.96), rgba(255,247,237,.94));
            box-shadow: 0 18px 50px rgba(15, 23, 42, .06);
        }

        .hero-panel::after {
            content: '';
            position: absolute;
            width: 170px;
            height: 170px;
            right: -55px;
            top: -70px;
            border-radius: 50%;
            background: rgba(249, 115, 22, .08);
        }

        html[data-bs-theme="dark"] .hero-panel {
            background: var(--app-card);
            border-color: rgba(251, 146, 60, .18);
            box-shadow: 0 18px 50px rgba(0,0,0,.20);
        }

        .hero-kicker {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 6px 10px;
            border-radius: 999px;
            background: rgba(249, 115, 22, .10);
            color: var(--orange-700);
            font-size: 11px;
            font-weight: 800;
            letter-spacing: .7px;
            text-transform: uppercase;
            margin-bottom: 10px;
        }

        html[data-bs-theme="dark"] .hero-kicker { color: #fdba74; }

        .hero-title {
            color: var(--app-text);
            font-weight: 800;
            font-size: clamp(22px, 2vw, 30px);
            line-height: 1.15;
            margin-bottom: 7px;
        }

        .hero-subtitle { color: var(--app-muted); margin: 0; font-size: 13px; }

        .card-premium {
            background: var(--app-card);
            border: 1px solid var(--app-border);
            border-radius: 18px;
            box-shadow: 0 10px 30px rgba(15, 23, 42, .055);
            transition: transform .2s ease, box-shadow .2s ease, border-color .2s ease;
        }

        .card-premium:hover {
            border-color: rgba(249, 115, 22, .18);
            box-shadow: 0 16px 36px rgba(15, 23, 42, .08);
        }

        html[data-bs-theme="dark"] .card-premium {
            background: radial-gradient(circle at 96% 10%, rgba(249, 115, 22, .14) 0 74px, transparent 75px), #0d0f12;
            box-shadow: 0 12px 34px rgba(0,0,0,.18);
        }

        .stat-card { position: relative; overflow: hidden; }
        .stat-card::after {
            content: '';
            position: absolute;
            inset: auto -30px -42px auto;
            width: 110px;
            height: 110px;
            border-radius: 50%;
            background: rgba(249, 115, 22, .055);
        }

        .stat-card .card-body { padding: 20px !important; }

        .stat-icon-wrapper {
            width: 46px;
            height: 46px;
            flex: 0 0 46px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
            box-shadow: inset 0 0 0 1px rgba(255,255,255,.12);
        }

        .stat-label {
            color: var(--app-muted);
            font-size: 10px;
            font-weight: 800;
            letter-spacing: .8px;
            text-transform: uppercase;
            margin-bottom: 4px;
        }

        .stat-value {
            color: var(--app-text);
            font-size: 25px;
            line-height: 1;
            font-weight: 800;
        }

        .stat-icon-bg {
            position: absolute;
            right: 12px;
            bottom: -10px;
            font-size: 4.5rem;
            opacity: .035;
            transform: rotate(-10deg);
        }

        .table-panel .card-header {
            border-bottom: 1px solid var(--app-border) !important;
            padding: 18px 20px !important;
        }

        .table-panel-title {
            color: var(--app-text);
            font-weight: 800;
            font-size: 16px;
            margin: 0;
        }

        .filter-panel {
            background: color-mix(in srgb, var(--app-card) 94%, var(--orange-50));
            border-bottom: 1px solid var(--app-border);
            padding: 14px 18px 16px;
        }

        html[data-bs-theme="dark"] .filter-panel { background: rgba(0,0,0,.15); }

        .filter-control,
        .filter-search {
            min-height: 38px;
            border: 1px solid var(--app-border) !important;
            border-radius: 11px !important;
            background: var(--app-card) !important;
            color: var(--app-text) !important;
            box-shadow: none !important;
            font-size: 12px;
        }

        .filter-search .input-group-text,
        .filter-search .form-control {
            background: transparent !important;
            color: var(--app-text) !important;
        }

        .filter-control:focus,
        .filter-search:focus-within {
            border-color: rgba(249, 115, 22, .55) !important;
            box-shadow: 0 0 0 3px rgba(249, 115, 22, .08) !important;
        }

        .task-table { color: var(--app-text); }
        .task-table thead th {
            background: var(--slate-50);
            color: var(--slate-500);
            border-bottom: 1px solid var(--app-border);
            padding-top: 13px;
            padding-bottom: 13px;
            font-size: 10px;
            font-weight: 800;
            letter-spacing: .7px;
            white-space: nowrap;
        }

        html[data-bs-theme="dark"] .task-table thead th {
            background: rgba(0,0,0,.15);
            color: #94a3b8;
        }

        .task-table tbody td {
            border-bottom: 1px solid var(--app-border);
            padding-top: 15px;
            padding-bottom: 15px;
            vertical-align: middle;
            font-size: 12px;
        }

        .task-table tbody tr:last-child td { border-bottom: 0; }
        .task-table tbody tr:hover { background: rgba(249, 115, 22, .025); }

        .task-kind {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 5px 8px;
            border-radius: 7px;
            font-size: 9px;
            font-weight: 900;
            letter-spacing: .6px;
            text-transform: uppercase;
            margin-bottom: 6px;
        }

        .task-kind.pickup { background: rgba(139, 92, 246, .11); color: #7c3aed; }
        .task-kind.delivery { background: rgba(249, 115, 22, .12); color: var(--orange-700); }
        html[data-bs-theme="dark"] .task-kind.pickup { color: #c4b5fd; }
        html[data-bs-theme="dark"] .task-kind.delivery { color: #fdba74; }

        .primary-line { color: var(--app-text); font-size: 12px; font-weight: 800; }
        .secondary-line { color: var(--app-muted); font-size: 11px; line-height: 1.45; }

        .qty-pill {
            display: inline-flex;
            align-items: center;
            border: 1px solid var(--app-border);
            background: var(--slate-50);
            color: var(--slate-700);
            border-radius: 999px;
            padding: 4px 8px;
            font-size: 10px;
            font-weight: 700;
            margin-top: 5px;
        }

        html[data-bs-theme="dark"] .qty-pill { background: #202b3d; color: #cbd5e1; }

        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 6px 9px;
            border-radius: 999px;
            font-size: 9px;
            font-weight: 900;
            letter-spacing: .45px;
            text-transform: uppercase;
            white-space: nowrap;
            box-shadow: none !important;
        }

        .action-btn {
            width: 34px;
            height: 34px;
            border-radius: 10px !important;
            padding: 0 !important;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: 1px solid var(--app-border) !important;
            background: var(--app-card) !important;
            box-shadow: none !important;
            transition: .2s ease;
        }

        .action-btn:hover { transform: translateY(-1px); border-color: rgba(249,115,22,.30) !important; }

        .modal-premium .modal-content {
            border: 1px solid rgba(249, 115, 22, .14) !important;
            border-radius: 20px !important;
            background: var(--app-card);
            color: var(--app-text);
            overflow: hidden;
            box-shadow: 0 24px 70px rgba(15,23,42,.18) !important;
        }

        .modal-premium .modal-header { padding: 17px 20px; }
        .modal-premium .modal-body { padding: 20px !important; background: var(--app-card) !important; }
        .modal-premium .modal-footer { padding: 15px 20px !important; background: var(--app-card) !important; border-top: 1px solid var(--app-border) !important; }

        .modal-premium .form-label {
            color: var(--app-muted) !important;
            font-size: 10px !important;
            font-weight: 800 !important;
            letter-spacing: .6px;
        }

        .modal-premium .form-select {
            min-height: 44px;
            border-radius: 11px;
            border: 1px solid var(--app-border) !important;
            background-color: var(--app-card) !important;
            color: var(--app-text) !important;
            font-size: 13px;
            box-shadow: none !important;
        }

        .modal-premium .form-select:focus {
            border-color: rgba(249,115,22,.55) !important;
            box-shadow: 0 0 0 3px rgba(249,115,22,.08) !important;
        }

        .empty-state { padding: 54px 20px !important; }
        .empty-icon {
            width: 58px;
            height: 58px;
            border-radius: 18px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 12px;
            background: rgba(249,115,22,.10);
            color: var(--orange-600);
            font-size: 22px;
        }

        @media (max-width: 767.98px) {
            .hero-panel { padding: 20px; border-radius: 18px; }
            .hero-panel .hero-actions { margin-top: 16px; width: 100%; }
            .hero-panel .hero-actions .btn { width: 100%; }
            .filter-panel { padding: 14px; }
            .stat-card .card-body { padding: 16px !important; }
            .task-table { min-width: 980px; }
        }


        /* =========================================================
        REVISION V2 - mengikuti visual dashboard yang disetujui
        ========================================================= */
        .pickup-page { padding-top: 12px !important; padding-bottom: 16px !important; }
        .page-shell { max-width: 1680px; padding-left: 0; padding-right: 0; }

        .hero-panel {
            min-height: 140px;
            padding: 18px 22px;
            display: flex;
            align-items: center;
            border-radius: 24px;
            background:
                radial-gradient(circle at 96% 10%, rgba(249,115,22,.13) 0 74px, transparent 75px),
                linear-gradient(115deg, #ffffff 0%, #fffdfa 60%, #fff1e6 100%);
            position: relative;
            z-index: 10;
        }
        html[data-bs-theme="dark"] .hero-panel {
            background: radial-gradient(circle at 96% 10%, rgba(249, 115, 22, .14) 0 74px, transparent 75px), #0d0f12;
        }
        .hero-panel::after { display:none; }
        .hero-copy { max-width: 700px; position:relative; z-index:3; }
        .hero-kicker { margin-bottom: 13px; padding: 7px 12px; }
        .hero-title { font-size: clamp(27px, 2.3vw, 36px); margin-bottom: 8px; }
        .hero-subtitle { font-size: 13px; }
        .hero-actions { position:relative; z-index:4; }
        .hero-actions .btn { min-height:44px; padding-left:24px !important; padding-right:24px !important; }

        .hero-visual {
            position:absolute;
            right: 220px;
            bottom: 0;
            width: 290px;
            height: 132px;
            pointer-events:none;
            opacity:.92;
            z-index:1;
        }
        .hero-city { position:absolute; right:0; bottom:0; width:210px; height:95px; opacity:.18; }
        .hero-city span { position:absolute; bottom:0; background:#94a3b8; border-radius:3px 3px 0 0; }
        .hero-city span:nth-child(1){left:4px;width:26px;height:48px}.hero-city span:nth-child(2){left:38px;width:35px;height:72px}
        .hero-city span:nth-child(3){left:82px;width:24px;height:58px}.hero-city span:nth-child(4){left:114px;width:42px;height:88px}
        .hero-city span:nth-child(5){left:164px;width:32px;height:64px}
        .hero-truck {
            position:absolute; left:2px; bottom:12px; width:154px; height:72px;
            color:rgba(249,115,22,.88); font-size:74px; display:flex; align-items:center;
            filter:drop-shadow(0 8px 12px rgba(234,88,12,.10));
        }
        .hero-boxes { position:absolute; right:4px; bottom:6px; display:flex; align-items:flex-end; gap:4px; opacity:.42; }
        .hero-boxes span { width:35px; height:26px; background:#fdba74; border:1px solid #fb923c; border-radius:3px; }
        .hero-boxes span:nth-child(2){height:38px}.hero-boxes span:nth-child(3){height:22px}

        .stats-row { --bs-gutter-x: 22px; }
        .stat-card { min-height: 132px; }
        .stat-card .card-body { padding: 19px 21px !important; display:flex; align-items:center; }
        .stat-content { width:100%; display:flex; align-items:center; gap:15px; position:relative; z-index:2; }
        .stat-text { min-width:0; }
        .stat-icon-wrapper { width:52px; height:52px; flex:0 0 52px; border-radius:14px; }
        .stat-value { font-size:27px; margin-top:1px; }
        .stat-caption { margin-top:10px; font-size:11px; color:var(--app-muted); white-space:nowrap; }
        .stat-spark { margin-left:auto; width:70px; height:30px; opacity:.9; }
        .stat-spark svg { width:100%; height:100%; overflow:visible; }
        .stat-spark path { fill:none; stroke:currentColor; stroke-width:2.2; stroke-linecap:round; stroke-linejoin:round; stroke-dasharray: 200; stroke-dashoffset: 200; animation: drawSpark 1.5s ease-out forwards; }
        @keyframes drawSpark { to { stroke-dashoffset: 0; } }
        .stat-spark.orange{color:#f97316}.stat-spark.amber{color:#f59e0b}.stat-spark.blue{color:#3b82f6}.stat-spark.green{color:#22c55e}
        .stat-icon-bg { display:none; }

        .table-panel { border-radius:20px; overflow:hidden; }
        .table-panel .card-header { min-height:70px; padding:16px 20px !important; position:relative; overflow:hidden; }
        .table-panel-title { font-size:17px; }
        .table-header-art { position:absolute; right:26px; bottom:-8px; font-size:76px; color:var(--orange-500); opacity:.055; transform:rotate(-4deg); }
        .filter-panel { padding:18px 22px; }
        .filter-panel form { margin-bottom:0 !important; }
        .filter-control, .filter-search { min-height:46px; border-radius:12px !important; font-size:12px; }
        .filter-search .form-control { min-height:44px; }
        .filter-actions .btn { min-height:46px; border-radius:11px !important; font-size:12px; display: flex; align-items: center; justify-content: center; gap: 6px; }

        .task-table thead th { padding-top:12px; padding-bottom:12px; font-size:10px; }
        .task-table tbody td { padding-top:12px; padding-bottom:12px; }
        .task-kind { padding:5px 9px; border-radius:7px; }
        .primary-line { font-size:12px; }
        .secondary-line { font-size:11px; }
        .action-btn { width:36px; height:36px; border-radius:10px !important; }

        .table-footer {
            min-height:76px; border-top:1px solid var(--app-border); padding:14px 22px;
            display:flex; align-items:center; justify-content:space-between; gap:16px;
            color:var(--app-muted); font-size:11px;
        }
        .table-pagination { display:flex; align-items:center; gap:7px; }
        .page-chip {
            min-width:38px; height:38px; border:1px solid var(--app-border); border-radius:10px;
            display:inline-flex; align-items:center; justify-content:center; color:var(--app-muted);
            background:var(--app-card); text-decoration:none; font-weight:700;
        }
        .page-chip.active { background:linear-gradient(135deg,var(--orange-500),var(--orange-600)); color:white; border-color:transparent; box-shadow:0 7px 16px rgba(234,88,12,.18); }
        .page-chip.disabled { opacity:.45; pointer-events:none; }

        @media (max-width: 1199.98px) { .hero-visual { right:190px; opacity:.65; } .stat-spark { display:none; } }
        @media (max-width: 991.98px) { .hero-visual { display:none; } .hero-panel { min-height:auto; } .stat-caption { white-space:normal; } }
        @media (max-width: 767.98px) { .page-shell{padding-left:0;padding-right:0}.hero-panel{padding:20px; border-radius:0; top: 0;}.table-footer{align-items:flex-start;flex-direction:column}.stats-row{--bs-gutter-x:12px}.stat-card{min-height:110px} }

    </style>

    <div class="pickup-page py-4">
        <div class="container-fluid page-shell">
            <!-- Header -->
            <div class="hero-panel mb-4">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center position-relative w-100" style="z-index:2;">
                    <div class="hero-copy">
                        <span class="hero-kicker"><i class="fa-solid fa-route"></i> Operasional Driver</span>
                        <h1 class="hero-title"><i class="fa-solid fa-truck-fast text-orange me-2"></i>Tugas Driver & Pickup</h1>
                        <p class="hero-subtitle">Kelola pickup, delivery, status perjalanan, driver, dan kendaraan dari satu halaman.</p>
                    </div>
                    <div class="hero-visual" aria-hidden="true">
                        <div class="hero-city"><span></span><span></span><span></span><span></span><span></span></div>
                        <div class="hero-truck"><i class="fa-solid fa-truck"></i></div>
                        <div class="hero-boxes"><span></span><span></span><span></span></div>
                    </div>
                    <div class="hero-actions">
                        <button type="button" class="btn btn-orange rounded-3 px-4 py-2 fw-bold" onclick="window.openTaskModal('create')">
                            <i class="fa-solid fa-plus me-2"></i>Buat Tugas Baru
                        </button>
                    </div>
                </div>
            </div>

            <!-- Alert Success -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-4 mb-4" role="alert">
                    <div class="d-flex align-items-center">
                        <i class="fa-solid fa-circle-check fs-4 me-2"></i>
                        <strong>Berhasil!</strong> {{ session('success') }}
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            @if($errors->any())
                <div class="alert alert-danger border-0 shadow-sm rounded-4 mb-4" role="alert">
                    <div class="d-flex align-items-center mb-2">
                        <i class="fa-solid fa-triangle-exclamation fs-4 me-2"></i>
                        <strong>Terdapat kesalahan input:</strong>
                    </div>
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Statistik Tugas -->
            <div class="row g-4 mb-4 stats-row">
                <div class="col-md-3">
                    <div class="card card-premium stat-card h-100">
                        <div class="card-body">
                            <div class="stat-content">
                                <div class="stat-icon-wrapper bg-orange-gradient text-white"><i class="fa-solid fa-layer-group"></i></div>
                                <div class="stat-text"><p class="stat-label">Total Tugas</p><div class="stat-value">{{ $totalTasks }}</div><div class="stat-caption">Semua tugas tercatat</div></div>
                                <div class="stat-spark orange"><svg viewBox="0 0 70 30"><path d="M2 26 L13 22 L20 14 L29 18 L38 5 L47 19 L56 12 L68 17"/></svg></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card card-premium stat-card h-100">
                        <div class="card-body">
                            <div class="stat-content">
                                <div class="stat-icon-wrapper bg-warning bg-opacity-10 text-warning"><i class="fa-solid fa-clock"></i></div>
                                <div class="stat-text"><p class="stat-label">Baru (Assigned)</p><div class="stat-value">{{ $assignedTasks }}</div><div class="stat-caption">Menunggu proses</div></div>
                                <div class="stat-spark amber"><svg viewBox="0 0 70 30"><path d="M2 27 L12 24 L19 11 L28 8 L36 3 L45 20 L54 16 L68 21"/></svg></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card card-premium stat-card h-100">
                        <div class="card-body">
                            <div class="stat-content">
                                <div class="stat-icon-wrapper bg-primary bg-opacity-10 text-primary"><i class="fa-solid fa-truck-fast"></i></div>
                                <div class="stat-text"><p class="stat-label">Sedang Jalan</p><div class="stat-value">{{ $onRouteTasks }}</div><div class="stat-caption">Dalam perjalanan</div></div>
                                <div class="stat-spark blue"><svg viewBox="0 0 70 30"><path d="M2 24 L12 23 L20 16 L28 21 L36 8 L44 17 L52 11 L60 20 L68 16"/></svg></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card card-premium stat-card h-100">
                        <div class="card-body">
                            <div class="stat-content">
                                <div class="stat-icon-wrapper bg-success bg-opacity-10 text-success"><i class="fa-solid fa-check-double"></i></div>
                                <div class="stat-text"><p class="stat-label">Selesai</p><div class="stat-value">{{ $completedTasks }}</div><div class="stat-caption">Tugas selesai</div></div>
                                <div class="stat-spark green"><svg viewBox="0 0 70 30"><path d="M2 27 L12 26 L20 23 L28 22 L36 17 L44 15 L52 10 L60 7 L68 2"/></svg></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabel Tugas -->
            <div class="card card-premium table-panel">
                <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="table-panel-title"><i class="fa-solid fa-list-check me-2 text-orange"></i>Daftar Tugas Pickup & Pengiriman</h5>
                        <div class="secondary-line mt-1">Pantau tugas aktif dan perbarui status operasional driver.</div>
                    </div>
                    <i class="fa-solid fa-clipboard-check table-header-art" aria-hidden="true"></i>
                </div>
                
                <!-- Filter Bar inside Card Body -->
                <div class="filter-panel">
                    <form action="{{ route('pickup-tasks.index') }}" method="GET" class="row g-2 align-items-center mb-3">
                        <div class="col-md-3">
                            <div class="input-group input-group-sm filter-search overflow-hidden">
                                <span class="input-group-text bg-white border-0"><i class="fa-solid fa-magnifying-glass text-muted"></i></span>
                                <input type="text" name="search" class="form-control border-0 shadow-none" placeholder="Cari No. Referensi..." value="{{ request('search') }}">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <select name="task_type" class="form-select form-select-sm filter-control px-3">
                                <option value="">Semua Jenis Tugas</option>
                                <option value="pickup" {{ request('task_type') == 'pickup' ? 'selected' : '' }}>Pickup</option>
                                <option value="delivery" {{ request('task_type') == 'delivery' ? 'selected' : '' }}>Delivery</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select name="status" class="form-select form-select-sm filter-control px-3">
                                <option value="">Semua Status</option>
                                <option value="assigned" {{ request('status') == 'assigned' ? 'selected' : '' }}>Assigned</option>
                                <option value="on_route" {{ request('status') == 'on_route' ? 'selected' : '' }}>On Route</option>
                                <option value="arrived" {{ request('status') == 'arrived' ? 'selected' : '' }}>Arrived</option>
                                <option value="delivered" {{ request('status') == 'delivered' ? 'selected' : '' }}>Delivered/Completed</option>
                                <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Failed</option>
                                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select name="driver_id" class="form-select form-select-sm filter-control px-3">
                                <option value="">Semua Driver</option>
                                @foreach($drivers as $driver)
                                    <option value="{{ $driver->id }}" {{ request('driver_id') == $driver->id ? 'selected' : '' }}>{{ $driver->full_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2 d-flex gap-2 filter-actions">
                            <button type="submit" class="btn btn-sm btn-orange w-100 fw-bold rounded-3"><i class="fa-solid fa-filter me-1"></i> Filter</button>
                            <a href="{{ route('pickup-tasks.index') }}" class="btn btn-sm btn-light border w-100 rounded-3" title="Reset"><i class="fa-solid fa-rotate-right me-1"></i> Reset</a>
                        </div>
                    </form>
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table task-table align-middle mb-0">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 px-4" style="width: 50px;">No</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Tipe</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Nomor SO / Ref</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Driver</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Kendaraan</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Lokasi Awal</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Lokasi Tujuan</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Status</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Tgl Pengiriman</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Estimasi Sampai</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-end px-4">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($tasks as $task)
                                <tr>
                                    <td class="text-center px-4">
                                        <span class="secondary-line">{{ ($tasks->currentPage() - 1) * $tasks->perPage() + $loop->iteration }}</span>
                                    </td>
                                    <td>
                                        @if($task->task_type === 'pickup')
                                            <span class="task-kind pickup m-0"><i class="fa-solid fa-box-open"></i> PICKUP</span>
                                        @else
                                            <span class="task-kind delivery m-0"><i class="fa-solid fa-truck-fast"></i> DELIVERY</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="primary-line fw-bold">{{ $task->task_type === 'pickup' ? $task->reference_number : ($task->salesOrder->so_number ?? '-') }}</span>
                                    </td>
                                    <td>
                                        <div class="primary-line">{{ $task->driver->full_name ?? 'N/A' }}</div>
                                    </td>
                                    <td>
                                        <div class="primary-line">{{ $task->vehicle->plate_number ?? 'N/A' }}</div>
                                    </td>
                                    <td>
                                        <div class="primary-line fw-bold">{{ $task->pickup_name ?? '-' }}</div>
                                        <div class="secondary-line" title="{{ $task->pickup_location }}">{{ $task->pickup_location ? Str::limit($task->pickup_location, 30) : '-' }}</div>
                                    </td>
                                    <td>
                                        @if($task->task_type === 'pickup')
                                            <div class="primary-line fw-bold">{{ $task->destination_name ?? '-' }}</div>
                                            <div class="secondary-line" title="{{ $task->destination }}">{{ $task->destination ? Str::limit($task->destination, 30) : '-' }}</div>
                                        @else
                                            <div class="primary-line fw-bold">{{ $task->salesOrder->customer_name ?? '-' }}</div>
                                            <div class="secondary-line" title="{{ $task->salesOrder->source_data['address'] ?? '' }}">{{ Str::limit($task->salesOrder->source_data['address'] ?? '-', 30) }}</div>
                                        @endif
                                    </td>
                                    <td>
                                        @php
                                            $badges = [
                                                'pending' => ['bg' => 'bg-secondary', 'text' => 'text-secondary', 'icon' => 'fa-hourglass'],
                                                'assigned' => ['bg' => 'bg-warning', 'text' => 'text-warning', 'icon' => 'fa-clock'],
                                                'on_route' => ['bg' => 'bg-info', 'text' => 'text-info', 'icon' => 'fa-truck-fast'],
                                                'arrived' => ['bg' => 'bg-primary', 'text' => 'text-primary', 'icon' => 'fa-map-marker-alt'],
                                                'delivered' => ['bg' => 'bg-success', 'text' => 'text-success', 'icon' => 'fa-check'],
                                                'failed' => ['bg' => 'bg-danger', 'text' => 'text-danger', 'icon' => 'fa-xmark'],
                                                'cancelled' => ['bg' => 'bg-secondary', 'text' => 'text-secondary', 'icon' => 'fa-ban']
                                            ];
                                            $badgeStyle = $badges[$task->status] ?? ['bg' => 'bg-secondary', 'text' => 'text-secondary', 'icon' => 'fa-circle'];
                                        @endphp
                                        <span class="status-badge {{ $badgeStyle['bg'] }} bg-opacity-10 {{ $badgeStyle['text'] }} border border-{{ str_replace('bg-', '', $badgeStyle['bg']) }} border-opacity-25">
                                            <i class="fa-solid {{ $badgeStyle['icon'] }} me-1"></i> {{ str_replace('_', ' ', $task->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="primary-line">{{ $task->dispatch_date ? \Carbon\Carbon::parse($task->dispatch_date)->format('d M Y H:i') : ($task->sort_date ? \Carbon\Carbon::parse($task->sort_date)->format('d M Y H:i') : '-') }}</div>
                                    </td>
                                    <td>
                                        <div class="primary-line" style="color: var(--app-orange);">{{ $task->estimated_arrival ? \Carbon\Carbon::parse($task->estimated_arrival)->format('d M Y H:i') : '-' }}</div>
                                    </td>
                                    <td class="text-end px-4">
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-light border shadow-none bg-transparent" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="width: 32px; height: 32px; padding: 0; display: inline-flex; align-items: center; justify-content: center;">
                                                <i class="fa-solid fa-ellipsis-vertical" style="color: var(--app-muted);"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end shadow border border-opacity-10" style="border-radius: 12px; min-width: 160px;">
                                                <li>
                                                    <a class="dropdown-item py-2 d-flex align-items-center" href="{{ route('pickup-tasks.show', ['pickup_task' => $task->id, 'task_type' => $task->task_type]) }}">
                                                        <i class="fa-solid fa-eye text-info me-3" style="width: 16px;"></i> Lihat Detail
                                                    </a>
                                                </li>
                                                @if($task->status === 'assigned')
                                                <li>
                                                    <button type="button" class="dropdown-item py-2 d-flex align-items-center text-warning"
                                                        onclick="openEditTaskModal(this)"
                                                        data-task="{{ json_encode($task) }}"
                                                        data-items="{{ json_encode($task->task_type === 'pickup' ? $task->items : ($task->salesOrder ? $task->salesOrder->items : [])) }}"
                                                        data-url="{{ route('pickup-tasks.update-detail', ['pickup_task' => $task->id, 'task_type' => $task->task_type]) }}">
                                                        <i class="fa-solid fa-edit me-3" style="width: 16px;"></i> Edit Tugas
                                                    </button>
                                                </li>
                                                @else
                                                <li>
                                                    <span class="dropdown-item py-2 d-flex align-items-center text-muted" title="Tidak dapat mengedit tugas yang sedang berjalan atau selesai" style="cursor: not-allowed; background-color: transparent;">
                                                        <i class="fa-solid fa-edit text-muted me-3" style="width: 16px;"></i> Edit Tugas
                                                    </span>
                                                </li>
                                                @endif
                                                <li>
                                                    <button class="dropdown-item py-2 d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#editModal{{ $task->id }}">
                                                        <i class="fa-solid fa-pen-to-square text-primary me-3" style="width: 16px;"></i> Update Status
                                                    </button>
                                                </li>
                                                <li><hr class="dropdown-divider opacity-10"></li>
                                                <li>
                                                    <form action="{{ route('pickup-tasks.destroy', ['pickup_task' => $task->id, 'task_type' => $task->task_type]) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus tugas ini?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="dropdown-item py-2 text-danger d-flex align-items-center">
                                                            <i class="fa-solid fa-trash me-3" style="width: 16px;"></i> Hapus
                                                        </button>
                                                    </form>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="11" class="text-center empty-state">
                                        <div class="empty-icon"><i class="fa-solid fa-inbox"></i></div>
                                        <h5 class="fw-bold mb-1" style="color:var(--app-text);">Belum Ada Tugas</h5>
                                        <p class="secondary-line mb-0">Tugas pickup dan delivery akan muncul di sini.</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                @php
                    $isPaginator = method_exists($tasks, 'currentPage') && method_exists($tasks, 'lastPage');
                    $currentPage = $isPaginator ? $tasks->currentPage() : 1;
                    $lastPage = $isPaginator ? $tasks->lastPage() : 1;
                    $firstItem = $isPaginator ? ($tasks->firstItem() ?? 0) : ($tasks->count() ? 1 : 0);
                    $lastItem = $isPaginator ? ($tasks->lastItem() ?? 0) : $tasks->count();
                    $totalItem = $isPaginator ? $tasks->total() : $tasks->count();
                @endphp
                <div class="table-footer">
                    <div>Menampilkan <strong>{{ $firstItem }}</strong> - <strong>{{ $lastItem }}</strong> dari <strong>{{ $totalItem }}</strong> data</div>
                    @if($isPaginator && $lastPage > 1)
                        <div class="table-pagination">
                            <a class="page-chip {{ $currentPage <= 1 ? 'disabled' : '' }}" href="{{ $currentPage > 1 ? $tasks->previousPageUrl() : '#' }}"><i class="fa-solid fa-chevron-left"></i></a>
                            @for($page = max(1, $currentPage - 1); $page <= min($lastPage, $currentPage + 1); $page++)
                                <a class="page-chip {{ $page === $currentPage ? 'active' : '' }}" href="{{ $tasks->url($page) }}">{{ $page }}</a>
                            @endfor
                            <a class="page-chip {{ $currentPage >= $lastPage ? 'disabled' : '' }}" href="{{ $currentPage < $lastPage ? $tasks->nextPageUrl() : '#' }}"><i class="fa-solid fa-chevron-right"></i></a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @foreach ($tasks as $task)
    <!-- Modal Update Status -->
    <div class="modal fade" id="editModal{{ $task->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-premium">
            <div class="modal-content">
                <form action="{{ route('pickup-tasks.update', ['pickup_task' => $task->id]) }}" method="POST" class="w-100">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="task_type" value="{{ $task->task_type }}">
                    
                    <div class="modal-header bg-orange-gradient text-white border-0 py-3">
                        <h5 class="modal-title fw-bold"><i class="fa-solid fa-pen-to-square me-2"></i>Update Status Tugas ({{ ucfirst($task->task_type) }})</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body text-start">
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted text-uppercase mb-2" style="letter-spacing: 0.5px;">Status Saat Ini</label>
                            <select name="status" class="form-select">
                                <option value="assigned" {{ $task->status == 'assigned' ? 'selected' : '' }}>Assigned (Baru)</option>
                                <option value="on_route" {{ $task->status == 'on_route' ? 'selected' : '' }}>On Route (Sedang Jalan)</option>
                                <option value="arrived" {{ $task->status == 'arrived' ? 'selected' : '' }}>Arrived (Telah Sampai)</option>
                                <option value="delivered" {{ $task->status == 'delivered' ? 'selected' : '' }}>Delivered (Selesai Dikirim)</option>
                                <option value="failed" {{ $task->status == 'failed' ? 'selected' : '' }}>Failed (Gagal)</option>
                                <option value="cancelled" {{ $task->status == 'cancelled' ? 'selected' : '' }}>Cancelled (Dibatalkan)</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light rounded-3 px-4 fw-semibold border" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-orange rounded-3 px-4 fw-semibold"><i class="fa-solid fa-save me-2"></i>Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endforeach
    @include('pickup-tasks.partials.create-modal')
    <script>
        function openEditTaskModal(btn) {
            let task = JSON.parse(btn.getAttribute('data-task'));
            let items = JSON.parse(btn.getAttribute('data-items'));
            task.update_url = btn.getAttribute('data-url');
            window.openTaskModal('edit', task, items);
        }
    </script>
</x-app-layout>