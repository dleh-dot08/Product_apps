<x-app-layout>

    <style>
        /* HPP Page Variables & Styles */
        #hppDashboardPage {
            --hpp-primary: #ea580c;
            --hpp-primary-dark: #c2410c;
            --hpp-surface: var(--bs-body-bg);
            --hpp-line: #e7ebf1;
            --hpp-ink: #172033;
            --hpp-muted: #6c7688;
            color: var(--hpp-ink);
        }
        
        [data-bs-theme="dark"] #hppDashboardPage {
            --hpp-ink: #f3f5f8;
            --hpp-muted: #aeb7c6;
            --hpp-line: rgba(255, 255, 255, .10);
        }

        .hpp-page-head {
            background: linear-gradient(135deg, var(--hpp-surface) 0%, rgba(234, 88, 12, 0.08) 100%);
            border: 1px solid var(--hpp-line);
            position: sticky;
            top: 1rem;
            z-index: 1020;
            overflow: hidden;
            backdrop-filter: blur(10px);
            margin-bottom: 1.5rem;
        }

        .hpp-eyebrow {
            display: inline-flex;
            align-items: center;
            gap: .45rem;
            color: var(--hpp-primary);
            font-size: .72rem;
            font-weight: 800;
            letter-spacing: .09em;
            text-transform: uppercase;
            margin-bottom: .35rem;
            background: rgba(234, 88, 12, 0.12);
            padding: 4px 12px;
            border-radius: 20px;
            border: 1px solid rgba(234, 88, 12, 0.15);
        }

        .hpp-title {
            font-size: clamp(1.35rem, 2.2vw, 1.9rem);
            font-weight: 800;
            letter-spacing: -.03em;
            margin: 0;
            color: var(--hpp-ink);
        }
        .hpp-section-card {
            border: 0;
            border-radius: 14px;
            box-shadow: 0 4px 18px rgba(15, 23, 42, 0.06);
            overflow: hidden;
            background: #fff;
            height: 100%;
        }

        .hpp-section-card .card-header {
            background: #fff;
            border-bottom: 1px solid #eef2f7 !important;
            padding: 16px 20px;
        }

        .hpp-section-card .card-title {
            margin: 0;
            font-size: 15px;
            font-weight: 700;
            color: #1e293b;
            display: flex;
            align-items: center;
            gap: 9px;
        }

        .hpp-card-icon {
            width: 34px;
            height: 34px;
            border-radius: 9px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: #f1f5f9;
            color: #475569;
            font-size: 14px;
        }

        /* Chart */
        .cost-chart-wrapper {
            position: relative;
            min-height: 285px;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 10px 0;
        }

        .cost-chart-wrapper canvas {
            max-height: 260px !important;
        }

        /* Export Card */
        .export-panel {
            min-height: 285px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 28px 22px;
            text-align: center;
        }

        .export-icon {
            width: 68px;
            height: 68px;
            border-radius: 18px;
            background: rgba(234, 88, 12, 0.08);
            color: var(--hpp-primary);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 30px;
            margin-bottom: 18px;
        }

        .export-panel h4 {
            font-size: 17px;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 7px;
        }

        .export-panel p {
            font-size: 13px;
            color: #64748b;
            max-width: 340px;
            line-height: 1.6;
            margin-bottom: 20px;
        }

        .btn-export-hpp {
            border: 0;
            border-radius: 10px;
            padding: 11px 24px;
            font-size: 13px;
            font-weight: 700;
            background: var(--hpp-primary);
            color: #fff !important;
            box-shadow: 0 4px 12px rgba(234, 88, 12, 0.18);
            transition: all .2s ease;
        }

        .btn-export-hpp:hover {
            background: var(--hpp-primary-dark);
            transform: translateY(-1px);
            box-shadow: 0 6px 16px rgba(234, 88, 12, 0.22);
        }

        /* Table */
        .ritase-table {
            margin-bottom: 0;
        }

        .ritase-table thead th {
            border-top: 0;
            border-bottom: 1px solid #e9eef5;
            background: #f8fafc;
            color: #64748b;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .35px;
            padding: 13px 18px;
            white-space: nowrap;
        }

        .ritase-table tbody td {
            padding: 14px 18px;
            vertical-align: middle;
            border-top: 1px solid #f1f5f9;
            font-size: 13px;
            color: #334155;
        }

        .ritase-table tbody tr {
            transition: background-color .15s ease;
        }

        .ritase-table tbody tr:hover {
            background-color: #f8fafc;
        }

        .plate-number {
            display: inline-flex;
            align-items: center;
            padding: 5px 10px;
            border-radius: 7px;
            background: #f1f5f9;
            color: #334155;
            font-weight: 700;
            letter-spacing: .4px;
            font-size: 12px;
        }

        .driver-name {
            font-weight: 600;
            color: #334155;
        }

        .cost-value {
            font-weight: 700;
            color: #0f172a;
            white-space: nowrap;
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 700;
            white-space: nowrap;
        }

        .status-badge::before {
            content: "";
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: currentColor;
        }

        .status-completed {
            background: #ecfdf5;
            color: #16a34a;
        }

        .status-trip {
            background: #eff6ff;
            color: #2563eb;
        }

        .status-planned {
            background: #f1f5f9;
            color: #64748b;
        }

        .btn-detail-ritase {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 10px;
            border-radius: 7px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            color: #475569 !important;
            font-size: 11px;
            font-weight: 700;
            text-decoration: none !important;
            transition: all .15s ease;
        }

        .btn-detail-ritase:hover {
            background: #eef2ff;
            border-color: #c7d2fe;
            color: #4f46e5 !important;
        }

        .empty-ritase {
            padding: 45px 20px !important;
            color: #94a3b8 !important;
            font-size: 13px !important;
        }

        @media (max-width: 991.98px) {
            .hpp-dashboard-row > div {
                margin-bottom: 16px;
            }
        }
    </style>

    <div id="hppDashboardPage" class="content">
        <div class="container-fluid">

            <!-- Modern Header -->
            <div class="hpp-page-head d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 p-3 rounded-4 shadow-sm mb-4">
                <!-- Decorative Background Element -->
                <div style="position: absolute; top: -30px; right: -20px; width: 250px; height: 250px; background: radial-gradient(circle, rgba(234, 88, 12, 0.12) 0%, rgba(255,255,255,0) 70%); border-radius: 50%; pointer-events: none; z-index: 0;"></div>
                <div style="position: absolute; bottom: -50px; left: 10%; width: 150px; height: 150px; background: radial-gradient(circle, rgba(234, 88, 12, 0.08) 0%, rgba(255,255,255,0) 70%); border-radius: 50%; pointer-events: none; z-index: 0;"></div>
                
                <div style="position: relative; z-index: 1;">
                    <div class="hpp-eyebrow mb-2">
                        <i class="fa-solid fa-chart-line"></i>
                        HPP Ritase Dashboard
                    </div>
                    <h1 class="hpp-title fw-bolder mb-2">Monitor Harga Pokok Penjualan Trip</h1>
                    <p class="text-muted mb-0" style="font-size: 0.9rem;">Pantau seluruh log ritase, kalkulasi biaya operasional armada, dan ekspor laporan dengan mudah.</p>
                </div>

                <div class="hpp-page-actions d-flex flex-wrap gap-3" style="position: relative; z-index: 1;">
                    <div class="text-end">
                        <div class="text-muted" style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase;">Total Biaya Operasional</div>
                        <div class="fw-bolder" style="color: var(--hpp-primary); font-size: 1.4rem; letter-spacing: -0.02em;">Rp {{ number_format($totalCost, 0, ',', '.') }}</div>
                    </div>
                </div>
            </div>

            {{-- Chart + Export --}}
            <div class="row hpp-dashboard-row">

        {{-- Chart Komposisi Biaya --}}
        <div class="col-lg-6">
            <div class="card hpp-section-card">
                <div class="card-header">
                    <h3 class="card-title">
                        <span class="hpp-card-icon">
                            <i class="fas fa-chart-pie"></i>
                        </span>
                        Komposisi Biaya
                    </h3>
                </div>

                <div class="card-body">
                    <div class="cost-chart-wrapper">
                        <canvas id="costChart"></canvas>
                    </div>
                </div>
            </div>
        </div>


        {{-- Aksi Export --}}
        <div class="col-lg-6">
            <div class="card hpp-section-card">
                <div class="card-header">
                    <h3 class="card-title">
                        <span class="hpp-card-icon">
                            <i class="fas fa-file-export"></i>
                        </span>
                        Aksi & Export
                    </h3>
                </div>

                <div class="card-body p-0">
                    <div class="export-panel">
                        <div class="export-icon">
                            <i class="fas fa-file-excel"></i>
                        </div>

                        <h4>Export Laporan HPP</h4>

                        <p>
                            Unduh seluruh data dan perhitungan HPP dalam format
                            Microsoft Excel untuk kebutuhan laporan atau analisa lebih lanjut.
                        </p>

                        <a href="{{ route('hpp.export') }}"
                        class="btn btn-export-hpp">
                            <i class="fas fa-download mr-2"></i>
                            Download Excel
                        </a>
                    </div>
                </div>
            </div>
        </div>

    </div>


    {{-- Log Ritase --}}
    <div class="row mt-3">
        <div class="col-12">
            <div class="card hpp-section-card">

                <div class="card-header d-flex align-items-center justify-content-between">
                    <h3 class="card-title">
                        <span class="hpp-card-icon">
                            <i class="fas fa-truck"></i>
                        </span>
                        Log Ritase
                    </h3>

                    <span class="text-muted" style="font-size: 11px;">
                        {{ $trips->count() }} Data
                    </span>
                </div>


                <div class="card-body table-responsive p-0">
                    <table class="table ritase-table">

                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Plat Nomor</th>
                                <th>Driver</th>
                                <th>Total Biaya</th>
                                <th>Status</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse($trips as $trip)

                                <tr>

                                    {{-- Tanggal --}}
                                    <td>
                                        <i class="far fa-calendar-alt text-muted mr-2"></i>
                                        {{ $trip->date->format('d M Y') }}
                                    </td>


                                    {{-- Plat Nomor --}}
                                    <td>
                                        <span class="plate-number">
                                            {{ $trip->vehicle->plate_number ?? '-' }}
                                        </span>
                                    </td>


                                    {{-- Driver --}}
                                    <td>
                                        <span class="driver-name">
                                            {{ $trip->driver->full_name ?? '-' }}
                                        </span>
                                    </td>


                                    {{-- Total Biaya --}}
                                    <td>
                                        <span class="cost-value">
                                            Rp {{ number_format($trip->total_cost, 2, ',', '.') }}
                                        </span>
                                    </td>


                                    {{-- Status --}}
                                    <td>

                                        @if($trip->status == 'completed')

                                            <span class="status-badge status-completed">
                                                Completed
                                            </span>

                                        @elseif($trip->status == 'on_trip')

                                            <span class="status-badge status-trip">
                                                On Trip
                                            </span>

                                        @else

                                            <span class="status-badge status-planned">
                                                Planned
                                            </span>

                                        @endif

                                    </td>


                                    {{-- Aksi --}}
                                    <td class="text-center">
                                        <a href="{{ route('hpp.show', $trip->id) }}"
                                        class="btn-detail-ritase">
                                            <i class="fas fa-eye"></i>
                                            Detail
                                        </a>
                                    </td>

                                </tr>

                            @empty

                                <tr>
                                    <td colspan="6" class="text-center empty-ritase">
                                        <i class="fas fa-inbox d-block mb-2"
                                        style="font-size: 25px;"></i>
                                        Belum ada data ritase.
                                    </td>
                                </tr>

                            @endforelse
                        </tbody>

                    </table>
                </div>

            </div>
        </div>
    </div>


    <script>
        document.addEventListener('DOMContentLoaded', function () {

            const costCanvas = document.getElementById('costChart');

            if (!costCanvas) {
                return;
            }

            const costData = {
                labels: ['BBM', 'Manpower', 'Tol', 'Parkir', 'Lainnya'],
                datasets: [{
                    data: [
                        {{ $costComposition['BBM'] }},
                        {{ $costComposition['Manpower'] }},
                        {{ $costComposition['Tol'] }},
                        {{ $costComposition['Parkir'] }},
                        {{ $costComposition['Lainnya'] }}
                    ],
                    backgroundColor: ['#f56954', '#00a65a', '#f39c12', '#00c0ef', '#3c8dbc'],
                    borderWidth: 0
                }]
            };

            const costOptions = {
                responsive: true,
                maintainAspectRatio: false,

                layout: {
                    padding: {
                        top: 5,
                        right: 10,
                        bottom: 5,
                        left: 10
                    }
                },

                plugins: {
                    legend: {
                        position: 'bottom',

                        labels: {
                            usePointStyle: true,
                            pointStyle: 'circle',
                            padding: 18,
                            boxWidth: 8,
                            boxHeight: 8,

                            font: {
                                size: 11,
                                weight: '600'
                            }
                        }
                    },

                    tooltip: {
                        backgroundColor: '#0f172a',
                        titleColor: '#ffffff',
                        bodyColor: '#ffffff',
                        padding: 12,
                        cornerRadius: 8,
                        displayColors: true,

                        callbacks: {
                            label: function(context) {

                                let label = context.label || '';
                                let value = context.raw || 0;

                                if (label) {
                                    label += ': ';
                                }

                                label += 'Rp ' + Number(value).toLocaleString('id-ID');

                                return label;
                            }
                        }
                    }
                }
            };


            new Chart(costCanvas, {
                type: 'doughnut',

                data: costData,

                options: costOptions
            });

        });
    </script>
    </div>
</div>
</x-app-layout>
