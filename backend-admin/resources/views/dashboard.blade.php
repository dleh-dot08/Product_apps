<x-app-layout>
    <style>
        :root {
            --dash-primary: #2563eb;
            --dash-primary-soft: #eff6ff;
            --dash-success: #10b981;
            --dash-success-soft: #ecfdf5;
            --dash-warning: #f59e0b;
            --dash-warning-soft: #fffbeb;
            --dash-danger: #ef4444;
            --dash-danger-soft: #fef2f2;
            --dash-orange: #f97316;
            --dash-orange-soft: #fff7ed;
            --dash-purple: #8b5cf6;
            --dash-purple-soft: #f5f3ff;
            --dash-text: #0f172a;
            --dash-muted: #64748b;
            --dash-border: #e2e8f0;
            --dash-surface: #ffffff;
            --dash-page: #f8fafc;
            --dash-radius: 18px;
        }

        html[data-bs-theme="dark"] {
            --dash-text: #f8fafc;
            --dash-muted: #94a3b8;
            --dash-border: rgba(255, 255, 255, .08);
            --dash-surface: #111827;
            --dash-page: #0b1120;
            --dash-primary-soft: rgba(37, 99, 235, .14);
            --dash-success-soft: rgba(16, 185, 129, .14);
            --dash-warning-soft: rgba(245, 158, 11, .14);
            --dash-danger-soft: rgba(239, 68, 68, .14);
            --dash-orange-soft: rgba(249, 115, 22, .14);
            --dash-purple-soft: rgba(139, 92, 246, .14);
        }

        .dashboard-page {
            color: var(--dash-text);
            padding-bottom: 1rem;
        }

        .dashboard-hero {
            position: relative;
            overflow: hidden;
            border: 1px solid var(--dash-border);
            border-radius: 22px;
            background:
                radial-gradient(circle at 92% 8%, rgba(249, 115, 22, .15), transparent 30%),
                radial-gradient(circle at 75% 110%, rgba(37, 99, 235, .10), transparent 34%),
                var(--dash-surface);
            box-shadow: 0 10px 35px rgba(15, 23, 42, .06);
        }

        html[data-bs-theme="dark"] .dashboard-hero {
            box-shadow: 0 18px 40px rgba(0, 0, 0, .20);
        }

        .hero-inner {
            min-height: 142px;
            padding: 1.35rem 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
        }

        .hero-kicker {
            display: inline-flex;
            align-items: center;
            gap: .5rem;
            padding: .4rem .7rem;
            border-radius: 999px;
            background: var(--dash-orange-soft);
            color: var(--dash-orange);
            font-size: .72rem;
            font-weight: 800;
            letter-spacing: .08em;
            text-transform: uppercase;
            margin-bottom: .8rem;
        }

        .hero-title {
            margin: 0;
            font-size: clamp(1.45rem, 2.6vw, 2.15rem);
            font-weight: 800;
            letter-spacing: -.04em;
            color: var(--dash-text);
        }

        .hero-subtitle {
            color: var(--dash-muted);
            margin: .45rem 0 0;
            font-size: .9rem;
            max-width: 660px;
        }

        .hero-date {
            min-width: 210px;
            padding: .85rem 1rem;
            border-radius: 16px;
            border: 1px solid var(--dash-border);
            background: rgba(255, 255, 255, .62);
            backdrop-filter: blur(10px);
        }

        html[data-bs-theme="dark"] .hero-date {
            background: rgba(15, 23, 42, .58);
        }

        .hero-date-label {
            color: var(--dash-muted);
            font-size: .7rem;
            font-weight: 800;
            letter-spacing: .07em;
            text-transform: uppercase;
        }

        .hero-date-value {
            margin-top: .25rem;
            font-weight: 700;
            font-size: .92rem;
        }

        .metric-card {
            height: 100%;
            border: 1px solid var(--dash-border);
            border-radius: var(--dash-radius);
            background: var(--dash-surface);
            box-shadow: 0 8px 26px rgba(15, 23, 42, .045);
            transition: transform .22s ease, box-shadow .22s ease, border-color .22s ease;
            position: relative;
            overflow: hidden;
        }

        .metric-card::before {
            content: "";
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 4px;
            background: var(--metric-color);
        }

        .metric-card:hover {
            transform: translateY(-3px);
            border-color: color-mix(in srgb, var(--metric-color) 35%, var(--dash-border));
            box-shadow: 0 14px 32px rgba(15, 23, 42, .08);
        }

        .metric-card .card-body {
            padding: 1.1rem 1.1rem 1rem 1.25rem;
        }

        .metric-head {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: .8rem;
        }

        .metric-label {
            color: var(--dash-muted);
            font-size: .74rem;
            font-weight: 800;
            letter-spacing: .055em;
            text-transform: uppercase;
            margin-bottom: .45rem;
        }

        .metric-value {
            color: var(--dash-text);
            font-weight: 800;
            font-size: clamp(1.4rem, 2vw, 1.8rem);
            letter-spacing: -.04em;
            line-height: 1.15;
            margin: 0;
        }

        .metric-value.metric-money {
            font-size: clamp(1.2rem, 1.7vw, 1.55rem);
        }

        .metric-icon {
            width: 46px;
            height: 46px;
            border-radius: 14px;
            display: grid;
            place-items: center;
            flex: 0 0 auto;
            background: var(--metric-soft);
            color: var(--metric-color);
            font-size: 1.05rem;
        }

        .metric-foot {
            margin-top: .9rem;
            padding-top: .75rem;
            border-top: 1px dashed var(--dash-border);
            color: var(--dash-muted);
            font-size: .75rem;
            display: flex;
            align-items: center;
            gap: .45rem;
        }

        .metric-progress {
            margin-top: .75rem;
            height: 5px;
            background: var(--dash-border);
            border-radius: 999px;
            overflow: hidden;
        }

        .metric-progress > span {
            display: block;
            height: 100%;
            border-radius: inherit;
            background: var(--metric-color);
        }

        .panel-card {
            height: 100%;
            border: 1px solid var(--dash-border);
            border-radius: var(--dash-radius);
            background: var(--dash-surface);
            box-shadow: 0 8px 28px rgba(15, 23, 42, .045);
            overflow: hidden;
        }

        .panel-header {
            padding: 1.05rem 1.15rem .75rem;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 1rem;
        }

        .panel-title {
            margin: 0;
            color: var(--dash-text);
            font-size: .98rem;
            font-weight: 800;
            letter-spacing: -.015em;
        }

        .panel-subtitle {
            margin: .22rem 0 0;
            color: var(--dash-muted);
            font-size: .76rem;
        }

        .panel-body {
            padding: .65rem 1.15rem 1.15rem;
        }

        .chart-wrap {
            position: relative;
            min-height: 315px;
        }

        .chart-wrap canvas {
            max-height: 315px;
        }

        .segment-control {
            display: inline-flex;
            gap: .25rem;
            padding: .25rem;
            border: 1px solid var(--dash-border);
            border-radius: 12px;
            background: var(--dash-page);
        }

        .segment-btn {
            border: 0;
            background: transparent;
            color: var(--dash-muted);
            border-radius: 9px;
            padding: .42rem .7rem;
            font-size: .74rem;
            font-weight: 700;
            line-height: 1;
            transition: .2s ease;
        }

        .segment-btn:hover {
            color: var(--dash-primary);
        }

        .segment-btn.active {
            color: #fff;
            background: var(--dash-primary);
            box-shadow: 0 4px 12px rgba(37, 99, 235, .22);
        }

        .hpp-summary {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: .55rem;
            margin-top: .55rem;
        }

        .hpp-item {
            display: flex;
            align-items: center;
            gap: .55rem;
            padding: .62rem .7rem;
            border: 1px solid var(--dash-border);
            border-radius: 12px;
            color: var(--dash-muted);
            font-size: .72rem;
        }

        .hpp-dot {
            width: 9px;
            height: 9px;
            border-radius: 50%;
            flex: 0 0 auto;
        }

        .dashboard-table {
            margin: 0;
            color: var(--dash-text);
        }

        .dashboard-table thead th {
            border-bottom: 1px solid var(--dash-border);
            color: var(--dash-muted);
            font-size: .7rem;
            font-weight: 800;
            letter-spacing: .055em;
            text-transform: uppercase;
            padding: .8rem .75rem;
            white-space: nowrap;
            background: transparent;
        }

        .dashboard-table tbody td {
            border-bottom: 1px solid var(--dash-border);
            padding: .85rem .75rem;
            vertical-align: middle;
            font-size: .82rem;
            background: transparent;
        }

        .dashboard-table tbody tr:last-child td {
            border-bottom: 0;
        }

        .dashboard-table tbody tr:hover td {
            background: var(--dash-page);
        }

        .task-code {
            display: inline-flex;
            align-items: center;
            gap: .4rem;
            font-weight: 800;
            color: var(--dash-text);
        }

        .task-code::before {
            content: "";
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: var(--dash-primary);
            box-shadow: 0 0 0 4px var(--dash-primary-soft);
        }

        .driver-cell {
            display: flex;
            align-items: center;
            gap: .65rem;
            min-width: 170px;
        }

        .driver-avatar {
            width: 34px;
            height: 34px;
            border-radius: 10px;
            display: grid;
            place-items: center;
            background: var(--dash-primary-soft);
            color: var(--dash-primary);
            font-weight: 800;
            font-size: .78rem;
            flex: 0 0 auto;
        }

        .driver-name {
            font-weight: 700;
            color: var(--dash-text);
        }

        .route-text {
            max-width: 420px;
            color: var(--dash-muted);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .date-main {
            font-weight: 700;
            color: var(--dash-text);
            white-space: nowrap;
        }

        .date-sub {
            color: var(--dash-muted);
            font-size: .72rem;
        }

        .btn-view-task {
            width: 34px;
            height: 34px;
            border: 1px solid var(--dash-border);
            border-radius: 10px;
            display: inline-grid;
            place-items: center;
            color: var(--dash-muted);
            background: var(--dash-surface);
            transition: .2s ease;
            text-decoration: none;
        }

        .btn-view-task:hover {
            color: #fff;
            background: var(--dash-primary);
            border-color: var(--dash-primary);
            transform: translateY(-1px);
        }

        .btn-all-task {
            border: 1px solid var(--dash-border);
            color: var(--dash-text);
            background: var(--dash-surface);
            border-radius: 10px;
            padding: .45rem .72rem;
            font-size: .75rem;
            font-weight: 700;
            text-decoration: none;
            transition: .2s ease;
            white-space: nowrap;
        }

        .btn-all-task:hover {
            background: var(--dash-primary-soft);
            color: var(--dash-primary);
            border-color: rgba(37, 99, 235, .22);
        }

        .empty-state {
            padding: 2.2rem 1rem;
            text-align: center;
            color: var(--dash-muted);
        }

        .empty-state-icon {
            width: 48px;
            height: 48px;
            border-radius: 14px;
            display: grid;
            place-items: center;
            margin: 0 auto .75rem;
            background: var(--dash-page);
            color: var(--dash-muted);
        }

        @media (max-width: 991.98px) {
            .hero-inner {
                align-items: flex-start;
                flex-direction: column;
            }

            .hero-date {
                min-width: 0;
                width: 100%;
            }
        }

        @media (max-width: 767.98px) {
            .hero-inner {
                padding: 1.1rem;
                min-height: 0;
            }

            .panel-header {
                flex-direction: column;
            }

            .segment-control {
                width: 100%;
            }

            .segment-btn {
                flex: 1 1 0;
            }

            .chart-wrap,
            .chart-wrap canvas {
                min-height: 260px;
                max-height: 260px;
            }

            .hpp-summary {
                grid-template-columns: 1fr 1fr;
            }
        }
    </style>

    <div class="dashboard-page">
        <!-- Header -->
        <section class="dashboard-hero mb-3">
            <div class="hero-inner">
                <div>
                    <div class="hero-kicker">
                        <i class="fa-solid fa-chart-line"></i>
                        Operational Dashboard
                    </div>
                    <h1 class="hero-title">Dashboard Overview</h1>
                    <p class="hero-subtitle">
                        Ringkasan aktivitas driver, progres packaging, pengguna aktif, dan biaya ritase dalam satu tampilan.
                    </p>
                </div>

                <div class="hero-date">
                    <div class="hero-date-label">
                        <i class="fa-regular fa-calendar me-1"></i> Periode Hari Ini
                    </div>
                    <div class="hero-date-value">{{ now()->translatedFormat('d F Y') }}</div>
                </div>
            </div>
        </section>

        <!-- KPI -->
        <div class="row g-3">
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="metric-card" style="--metric-color:#2563eb; --metric-soft:var(--dash-primary-soft);">
                    <div class="card-body">
                        <div class="metric-head">
                            <div>
                                <div class="metric-label">Total Tugas Driver</div>
                                <p class="metric-value">{{ number_format($totalTugasDriver, 0, ',', '.') }}</p>
                            </div>
                            <div class="metric-icon"><i class="fa-solid fa-truck-fast"></i></div>
                        </div>
                        <div class="metric-foot">
                            <i class="fa-solid fa-list-check"></i>
                            Total penugasan yang tercatat
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-xl-3">
                <div class="metric-card" style="--metric-color:#10b981; --metric-soft:var(--dash-success-soft);">
                    <div class="card-body">
                        <div class="metric-head">
                            <div>
                                <div class="metric-label">Packaging Selesai</div>
                                <p class="metric-value">{{ $packagingPercentage }}%</p>
                            </div>
                            <div class="metric-icon"><i class="fa-solid fa-box-open"></i></div>
                        </div>
                        <div class="metric-progress">
                            <span style="width: {{ min(100, max(0, (float) $packagingPercentage)) }}%;"></span>
                        </div>
                        <div class="metric-foot">
                            <i class="fa-solid fa-chart-simple"></i>
                            Persentase penyelesaian packaging
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-xl-3">
                <div class="metric-card" style="--metric-color:#f97316; --metric-soft:var(--dash-orange-soft);">
                    <div class="card-body">
                        <div class="metric-head">
                            <div>
                                <div class="metric-label">User Aktif</div>
                                <p class="metric-value">{{ number_format($totalUserAktif, 0, ',', '.') }}</p>
                            </div>
                            <div class="metric-icon"><i class="fa-solid fa-user-group"></i></div>
                        </div>
                        <div class="metric-foot">
                            <i class="fa-solid fa-circle-check"></i>
                            Akun dengan status aktif
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-xl-3">
                <div class="metric-card" style="--metric-color:#ef4444; --metric-soft:var(--dash-danger-soft);">
                    <div class="card-body">
                        <div class="metric-head">
                            <div>
                                <div class="metric-label">HPP Ritase Bulan Ini</div>
                                <p class="metric-value metric-money">Rp {{ number_format($totalHpp, 0, ',', '.') }}</p>
                            </div>
                            <div class="metric-icon"><i class="fa-solid fa-wallet"></i></div>
                        </div>
                        <div class="metric-foot">
                            <i class="fa-solid fa-coins"></i>
                            Akumulasi biaya bulan berjalan
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts -->
        <div class="row g-3 mt-0 pt-3">
            <div class="col-12 col-xl-8">
                <div class="panel-card">
                    <div class="panel-header">
                        <div>
                            <h2 class="panel-title" id="chartTitle">Aktivitas Driver & Packaging</h2>
                            <p class="panel-subtitle" id="chartSubtitle">Perbandingan jumlah tugas dan packaging selesai secara mingguan.</p>
                        </div>

                        <div class="segment-control" role="group" aria-label="Filter periode grafik">
                            <button type="button" class="segment-btn active" id="btnWeekly" onclick="updateChartData('weekly', 'Mingguan')">Mingguan</button>
                            <button type="button" class="segment-btn" id="btnMonthly" onclick="updateChartData('monthly', 'Bulanan')">Bulanan</button>
                            <button type="button" class="segment-btn" id="btnYearly" onclick="updateChartData('yearly', 'Tahunan')">Tahunan</button>
                        </div>
                    </div>
                    <div class="panel-body">
                        <div class="chart-wrap">
                            <canvas id="taskChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-xl-4">
                <div class="panel-card">
                    <div class="panel-header">
                        <div>
                            <h2 class="panel-title">Komposisi HPP Ritase</h2>
                            <p class="panel-subtitle">Distribusi biaya ritase pada bulan berjalan.</p>
                        </div>
                    </div>
                    <div class="panel-body">
                        <div class="chart-wrap" style="min-height: 245px;">
                            <canvas id="hppChart"></canvas>
                        </div>

                        <div class="hpp-summary">
                            <div class="hpp-item"><span class="hpp-dot" style="background:#ef4444;"></span>BBM</div>
                            <div class="hpp-item"><span class="hpp-dot" style="background:#f59e0b;"></span>Tol</div>
                            <div class="hpp-item"><span class="hpp-dot" style="background:#3b82f6;"></span>Parkir</div>
                            <div class="hpp-item"><span class="hpp-dot" style="background:#8b5cf6;"></span>Lain-lain</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Active Tasks -->
        <div class="row g-3 mt-0 pt-3 mb-4">
            <div class="col-12">
                <div class="panel-card">
                    <div class="panel-header">
                        <div>
                            <h2 class="panel-title">Tugas Aktif Terbaru</h2>
                            <p class="panel-subtitle">Daftar penugasan driver yang masih perlu dipantau.</p>
                        </div>
                        <a href="{{ route('pickup-tasks.index') }}" class="btn-all-task">
                            Lihat Semua <i class="fa-solid fa-arrow-right ms-1"></i>
                        </a>
                    </div>

                    <div class="table-responsive">
                        <table class="table dashboard-table align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>ID Tugas</th>
                                    <th>Driver</th>
                                    <th>Rute / Deskripsi</th>
                                    <th>Tanggal Dibuat</th>
                                    <th class="text-end">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($activeTasks as $task)
                                    @php
                                        $driverName = $task->driver ? $task->driver->full_name : 'Belum Ditugaskan';
                                        $driverInitial = strtoupper(mb_substr($driverName, 0, 1));
                                    @endphp
                                    <tr>
                                        <td>
                                            <span class="task-code">#{{ substr($task->id, 0, 8) }}</span>
                                        </td>
                                        <td>
                                            <div class="driver-cell">
                                                <div class="driver-avatar">{{ $driverInitial }}</div>
                                                <div class="driver-name">{{ $driverName }}</div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="route-text" title="{{ $task->pickup_address ?? 'Tidak ada deskripsi' }}">
                                                {{ $task->pickup_address ?? 'Tidak ada deskripsi' }}
                                            </div>
                                        </td>
                                        <td>
                                            <div class="date-main">{{ $task->created_at->format('d M Y') }}</div>
                                            <div class="date-sub">{{ $task->created_at->format('H:i') }} WIB</div>
                                        </td>
                                        <td class="text-end">
                                            <a href="{{ route('pickup-tasks.show', $task->id) }}" class="btn-view-task" title="Lihat detail">
                                                <i class="fa-solid fa-arrow-up-right-from-square"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5">
                                            <div class="empty-state">
                                                <div class="empty-state-icon"><i class="fa-solid fa-clipboard-check"></i></div>
                                                <div class="fw-bold mb-1">Tidak ada tugas aktif</div>
                                                <div class="small">Semua tugas saat ini sudah tertangani.</div>
                                            </div>
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

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const rawData = @json($chartData);
        const totalHpp = Number(@json($totalHpp));
        let taskChart;
        let hppChart;

        document.addEventListener('DOMContentLoaded', function () {
            const htmlElement = document.documentElement;
            let isDark = htmlElement.getAttribute('data-bs-theme') === 'dark';

            const getChartColors = () => ({
                text: isDark ? '#cbd5e1' : '#64748b',
                strong: isDark ? '#f8fafc' : '#0f172a',
                grid: isDark ? 'rgba(255,255,255,.07)' : 'rgba(15,23,42,.06)',
                tooltipBg: isDark ? '#0f172a' : '#ffffff',
                tooltipBorder: isDark ? 'rgba(255,255,255,.10)' : '#e2e8f0'
            });

            let chartColors = getChartColors();
            const ctxTask = document.getElementById('taskChart').getContext('2d');

            const gradientTugas = ctxTask.createLinearGradient(0, 0, 0, 340);
            gradientTugas.addColorStop(0, 'rgba(37, 99, 235, .92)');
            gradientTugas.addColorStop(1, 'rgba(37, 99, 235, .42)');

            const gradientPkg = ctxTask.createLinearGradient(0, 0, 0, 340);
            gradientPkg.addColorStop(0, 'rgba(16, 185, 129, .92)');
            gradientPkg.addColorStop(1, 'rgba(16, 185, 129, .42)');

            taskChart = new Chart(ctxTask, {
                type: 'bar',
                data: {
                    labels: rawData.weekly.labels,
                    datasets: [
                        {
                            label: 'Tugas Driver',
                            data: rawData.weekly.tugas,
                            backgroundColor: gradientTugas,
                            borderColor: '#2563eb',
                            borderWidth: 0,
                            borderRadius: 8,
                            borderSkipped: false,
                            barPercentage: .62,
                            categoryPercentage: .72
                        },
                        {
                            label: 'Packaging Selesai',
                            data: rawData.weekly.packaging,
                            backgroundColor: gradientPkg,
                            borderColor: '#10b981',
                            borderWidth: 0,
                            borderRadius: 8,
                            borderSkipped: false,
                            barPercentage: .62,
                            categoryPercentage: .72
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: { mode: 'index', intersect: false },
                    animation: { duration: 550, easing: 'easeOutQuart' },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { color: chartColors.grid, drawTicks: false },
                            ticks: { color: chartColors.text, stepSize: 1, padding: 10 },
                            border: { display: false }
                        },
                        x: {
                            grid: { display: false },
                            ticks: { color: chartColors.text, padding: 10, maxRotation: 0 },
                            border: { display: false }
                        }
                    },
                    plugins: {
                        legend: {
                            position: 'top',
                            align: 'start',
                            labels: {
                                color: chartColors.text,
                                usePointStyle: true,
                                pointStyle: 'circle',
                                boxWidth: 7,
                                boxHeight: 7,
                                padding: 18,
                                font: { size: 11, weight: '600' }
                            }
                        },
                        tooltip: {
                            backgroundColor: chartColors.tooltipBg,
                            titleColor: chartColors.strong,
                            bodyColor: chartColors.text,
                            borderColor: chartColors.tooltipBorder,
                            borderWidth: 1,
                            padding: 12,
                            cornerRadius: 10,
                            displayColors: true,
                            boxPadding: 5
                        }
                    }
                }
            });

            const centerTextPlugin = {
                id: 'centerText',
                afterDraw(chart) {
                    const { ctx, chartArea } = chart;
                    if (!chartArea) return;
                    const x = (chartArea.left + chartArea.right) / 2;
                    const y = (chartArea.top + chartArea.bottom) / 2;

                    ctx.save();
                    ctx.textAlign = 'center';
                    ctx.textBaseline = 'middle';
                    ctx.fillStyle = chartColors.text;
                    ctx.font = '600 11px sans-serif';
                    ctx.fillText('TOTAL HPP', x, y - 10);

                    ctx.fillStyle = chartColors.strong;
                    ctx.font = '800 15px sans-serif';
                    const shortValue = new Intl.NumberFormat('id-ID', {
                        notation: 'compact',
                        maximumFractionDigits: 1
                    }).format(totalHpp);
                    ctx.fillText('Rp ' + shortValue, x, y + 12);
                    ctx.restore();
                }
            };

            const ctxHpp = document.getElementById('hppChart').getContext('2d');
            hppChart = new Chart(ctxHpp, {
                type: 'doughnut',
                data: {
                    labels: ['Bensin/BBM', 'Tol', 'Parkir', 'Lain-lain'],
                    datasets: [{
                        data: [
                            {{ $hppBensin }},
                            {{ $hppTol }},
                            {{ $hppParkir }},
                            {{ $hppLainnya }}
                        ],
                        backgroundColor: ['#ef4444', '#f59e0b', '#3b82f6', '#8b5cf6'],
                        borderWidth: 4,
                        borderColor: isDark ? '#111827' : '#ffffff',
                        hoverOffset: 5
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '73%',
                    animation: { duration: 650, easing: 'easeOutQuart' },
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: chartColors.tooltipBg,
                            titleColor: chartColors.strong,
                            bodyColor: chartColors.text,
                            borderColor: chartColors.tooltipBorder,
                            borderWidth: 1,
                            padding: 11,
                            cornerRadius: 10,
                            callbacks: {
                                label(context) {
                                    const value = Number(context.raw || 0);
                                    return ' ' + context.label + ': Rp ' + new Intl.NumberFormat('id-ID').format(value);
                                }
                            }
                        }
                    }
                },
                plugins: [centerTextPlugin]
            });

            const observer = new MutationObserver(function (mutations) {
                mutations.forEach(function (mutation) {
                    if (mutation.attributeName !== 'data-bs-theme') return;

                    isDark = htmlElement.getAttribute('data-bs-theme') === 'dark';
                    chartColors = getChartColors();

                    taskChart.options.scales.y.grid.color = chartColors.grid;
                    taskChart.options.scales.y.ticks.color = chartColors.text;
                    taskChart.options.scales.x.ticks.color = chartColors.text;
                    taskChart.options.plugins.legend.labels.color = chartColors.text;
                    taskChart.options.plugins.tooltip.backgroundColor = chartColors.tooltipBg;
                    taskChart.options.plugins.tooltip.titleColor = chartColors.strong;
                    taskChart.options.plugins.tooltip.bodyColor = chartColors.text;
                    taskChart.options.plugins.tooltip.borderColor = chartColors.tooltipBorder;
                    taskChart.update();

                    hppChart.data.datasets[0].borderColor = isDark ? '#111827' : '#ffffff';
                    hppChart.options.plugins.tooltip.backgroundColor = chartColors.tooltipBg;
                    hppChart.options.plugins.tooltip.titleColor = chartColors.strong;
                    hppChart.options.plugins.tooltip.bodyColor = chartColors.text;
                    hppChart.options.plugins.tooltip.borderColor = chartColors.tooltipBorder;
                    hppChart.update();
                });
            });

            observer.observe(htmlElement, { attributes: true });
        });

        window.updateChartData = function (timeframe, title) {
            const titleEl = document.getElementById('chartTitle');
            const subtitleEl = document.getElementById('chartSubtitle');
            titleEl.innerText = 'Aktivitas Driver & Packaging';
            subtitleEl.innerText = 'Perbandingan jumlah tugas dan packaging selesai secara ' + title.toLowerCase() + '.';

            ['btnWeekly', 'btnMonthly', 'btnYearly'].forEach(id => {
                document.getElementById(id).classList.remove('active');
            });

            const activeMap = {
                weekly: 'btnWeekly',
                monthly: 'btnMonthly',
                yearly: 'btnYearly'
            };
            document.getElementById(activeMap[timeframe]).classList.add('active');

            if (!taskChart || !rawData[timeframe]) return;

            const newData = rawData[timeframe];
            taskChart.data.labels = newData.labels;
            taskChart.data.datasets[0].data = newData.tugas;
            taskChart.data.datasets[1].data = newData.packaging;

            const barSize = timeframe === 'monthly' ? .8 : .62;
            taskChart.data.datasets[0].barPercentage = barSize;
            taskChart.data.datasets[1].barPercentage = barSize;
            taskChart.update();
        };
    </script>
</x-app-layout>
