<x-app-layout>
    @php
        $dbJobs = \App\Models\PackagingJob::latest()->get();
        $packagingTasks = $dbJobs->map(function($job) {
            return [
                'id' => $job->id,
                'type' => 'packaging',
                'number' => 'PKG-' . str_pad($job->id, 4, '0', STR_PAD_LEFT),
                'reference' => $job->no_so ?? '-',
                'title' => 'Packaging Order',
                'customer' => $job->customer ?? '-',
                'description' => $job->address ?? '-',
                'assignee' => 'Admin',
                'initial' => 'AD',
                'due_date' => $job->date_delivery ? \Carbon\Carbon::parse($job->date_delivery)->format('d M Y') : '-',
                'priority' => 'normal',
                'status' => $job->status ?: 'pending',
                'progress' => $job->status === 'completed' ? 100 : 0,
            ];
        })->toArray();

        $dbPickups = \App\Models\PickupTask::with(['driver'])->latest()->get();
        $deliveryTasks = $dbPickups->map(function($pt) {
            // Mapping status
            $mappedStatus = 'pending';
            $progress = 0;
            if (in_array($pt->status, ['on_route', 'arrived'])) {
                $mappedStatus = 'in_progress';
                $progress = 50;
            } elseif ($pt->status === 'delivered') {
                $mappedStatus = 'completed';
                $progress = 100;
            }

            return [
                'id' => $pt->id,
                'type' => 'delivery',
                'number' => $pt->reference_number ?? 'DLV-'.substr($pt->id, 0, 4),
                'reference' => $pt->so_number ?? '-',
                'title' => 'Pengiriman: ' . ($pt->item_name ?? 'Barang'),
                'customer' => $pt->customer_name ?? '-',
                'description' => $pt->address ?? '-',
                'assignee' => $pt->driver ? $pt->driver->full_name : 'Driver',
                'initial' => $pt->driver ? strtoupper(substr($pt->driver->full_name, 0, 2)) : 'DR',
                'due_date' => $pt->created_at ? \Carbon\Carbon::parse($pt->created_at)->format('d M Y') : '-',
                'priority' => 'normal',
                'status' => $mappedStatus,
                'progress' => $progress,
            ];
        })->toArray();

        $tasks = array_merge($packagingTasks, $deliveryTasks);


        $summary = [
            'total' => count($tasks),
            'packaging' => collect($tasks)->where('type', 'packaging')->count(),
            'delivery' => collect($tasks)->where('type', 'delivery')->count(),
            'urgent' => collect($tasks)->whereIn('priority', ['urgent', 'high'])->count(),
        ];

        $statusLabels = [
            'pending' => 'Pending',
            'in_progress' => 'In Progress',
            'waiting' => 'Menunggu',
            'review' => 'Review',
            'completed' => 'Completed',
        ];
    @endphp

    <style>
        .task-dashboard {
            --td-primary: #ea580c;
            --td-primary-dark: #c2410c;
            --td-primary-soft: #fff7ed;
            --td-primary-rgb: 234, 88, 12;
            --td-bg: #f6f8fb;
            --td-card: #ffffff;
            --td-soft: #f8fafc;
            --td-text: #0f172a;
            --td-muted: #64748b;
            --td-border: #e2e8f0;
            --td-border-strong: #cbd5e1;
            --td-success: #059669;
            --td-warning: #d97706;
            --td-danger: #dc2626;
            --td-blue: #2563eb;
            min-height: calc(100vh - 65px);
            padding: 18px;
            color: var(--td-text);
            background: var(--td-bg);
        }

        [data-bs-theme="dark"] .task-dashboard,
        body.dark-mode .task-dashboard,
        body.theme-dark .task-dashboard {
            --td-bg: #0b1220;
            --td-card: #111827;
            --td-soft: #172033;
            --td-text: #f8fafc;
            --td-muted: #94a3b8;
            --td-border: rgba(148, 163, 184, .18);
            --td-border-strong: rgba(148, 163, 184, .32);
            --td-primary-soft: rgba(234, 88, 12, .14);
        }

        .task-dashboard .td-header {
            position: relative;
            overflow: hidden;
            padding: 18px;
            border: 1px solid rgba(var(--td-primary-rgb), .20);
            border-radius: 18px;
            background:
                radial-gradient(circle at 92% 45%, rgba(var(--td-primary-rgb), .16), transparent 28%),
                linear-gradient(135deg, var(--td-card) 0%, rgba(var(--td-primary-rgb), .045) 100%);
        }

        .task-dashboard .td-header-content,
        .task-dashboard .td-toolbar,
        .task-dashboard .td-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 14px;
        }

        .task-dashboard .td-eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            margin-bottom: 9px;
            padding: 5px 10px;
            border: 1px solid rgba(var(--td-primary-rgb), .26);
            border-radius: 999px;
            color: var(--td-primary);
            background: rgba(var(--td-primary-rgb), .07);
            font-size: 10px;
            font-weight: 850;
            letter-spacing: .07em;
            text-transform: uppercase;
        }

        .task-dashboard .td-title {
            margin: 0;
            color: var(--td-text);
            font-size: clamp(22px, 2vw, 30px);
            font-weight: 900;
            letter-spacing: -.035em;
        }

        .task-dashboard .td-subtitle {
            max-width: 720px;
            margin: 6px 0 0;
            color: var(--td-muted);
            font-size: 12px;
            line-height: 1.6;
        }

        .task-dashboard .td-header-action {
            min-width: 148px;
            height: 42px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            border-radius: 11px;
            color: #fff;
            background: linear-gradient(135deg, var(--td-primary), #f97316);
            font-size: 11px;
            font-weight: 850;
            text-decoration: none;
            box-shadow: 0 9px 22px rgba(var(--td-primary-rgb), .22);
        }

        .task-dashboard .td-header-action:hover { color: #fff; transform: translateY(-1px); }

        .task-dashboard .td-summary-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 12px;
            margin-top: 14px;
        }

        .task-dashboard .td-summary-card {
            display: flex;
            align-items: center;
            gap: 12px;
            min-width: 0;
            padding: 14px;
            border: 1px solid var(--td-border);
            border-radius: 14px;
            background: var(--td-card);
        }

        .task-dashboard .td-summary-icon {
            width: 40px;
            height: 40px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex: 0 0 40px;
            border-radius: 11px;
            font-size: 14px;
        }

        .task-dashboard .td-summary-icon.orange { color: var(--td-primary); background: var(--td-primary-soft); }
        .task-dashboard .td-summary-icon.blue { color: var(--td-blue); background: rgba(37, 99, 235, .09); }
        .task-dashboard .td-summary-icon.green { color: var(--td-success); background: rgba(5, 150, 105, .09); }
        .task-dashboard .td-summary-icon.red { color: var(--td-danger); background: rgba(220, 38, 38, .09); }

        .task-dashboard .td-summary-label {
            margin-bottom: 2px;
            color: var(--td-muted);
            font-size: 9px;
            font-weight: 850;
            letter-spacing: .045em;
            text-transform: uppercase;
        }

        .task-dashboard .td-summary-value { color: var(--td-text); font-size: 20px; font-weight: 900; line-height: 1; }

        .task-dashboard .td-content {
            margin-top: 14px;
            overflow: hidden;
            border: 1px solid var(--td-border);
            border-radius: 16px;
            background: var(--td-card);
        }

        .task-dashboard .td-toolbar { padding: 13px; border-bottom: 1px solid var(--td-border); }

        .task-dashboard .td-tabs {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 4px;
            border: 1px solid var(--td-border);
            border-radius: 11px;
            background: var(--td-soft);
        }

        .task-dashboard .td-tab {
            min-height: 32px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 7px;
            padding: 6px 12px;
            border: 0;
            border-radius: 8px;
            color: var(--td-muted);
            background: transparent;
            font-size: 10px;
            font-weight: 800;
        }

        .task-dashboard .td-tab.is-active {
            color: var(--td-primary);
            background: var(--td-card);
            box-shadow: 0 3px 10px rgba(15, 23, 42, .07);
        }

        .task-dashboard .td-toolbar-right { display: flex; align-items: center; gap: 8px; min-width: 0; }
        .task-dashboard .td-search { position: relative; width: min(290px, 35vw); }
        .task-dashboard .td-search i { position: absolute; top: 50%; left: 12px; color: var(--td-muted); font-size: 12px; transform: translateY(-50%); }

        .task-dashboard .td-control {
            width: 100%;
            height: 36px;
            border: 1px solid var(--td-border-strong);
            border-radius: 9px;
            color: var(--td-text);
            background: var(--td-soft);
            font-size: 10px;
            font-weight: 650;
            outline: none;
        }

        .task-dashboard .td-search .td-control { padding: 7px 10px 7px 33px; }
        .task-dashboard select.td-control { width: 142px; padding: 7px 30px 7px 10px; }
        .task-dashboard .td-control:focus { border-color: rgba(var(--td-primary-rgb), .55); background: var(--td-card); box-shadow: 0 0 0 3px rgba(var(--td-primary-rgb), .09); }

        .task-dashboard .td-list-head,
        .task-dashboard .td-task-row {
            display: grid;
            grid-template-columns: minmax(245px, 1.45fr) minmax(150px, .75fr) minmax(135px, .60fr) minmax(120px, .55fr) minmax(112px, .50fr) 42px;
            gap: 12px;
            align-items: center;
        }

        .task-dashboard .td-list-head {
            padding: 10px 14px;
            color: var(--td-muted);
            background: var(--td-soft);
            border-bottom: 1px solid var(--td-border);
            font-size: 9px;
            font-weight: 850;
            letter-spacing: .045em;
            text-transform: uppercase;
        }

        .task-dashboard .td-task-row { padding: 13px 14px; border-bottom: 1px solid var(--td-border); transition: background-color .18s ease; }
        .task-dashboard .td-task-row:last-child { border-bottom: 0; }
        .task-dashboard .td-task-row:hover { background: rgba(var(--td-primary-rgb), .025); }

        .task-dashboard .td-task-main { display: flex; align-items: flex-start; gap: 11px; min-width: 0; }
        .task-dashboard .td-type-icon {
            width: 37px;
            height: 37px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex: 0 0 37px;
            border-radius: 10px;
            font-size: 13px;
        }
        .task-dashboard .td-type-icon.packaging { color: var(--td-primary); background: var(--td-primary-soft); }
        .task-dashboard .td-type-icon.delivery { color: var(--td-blue); background: rgba(37, 99, 235, .09); }

        .task-dashboard .td-task-number { display: flex; align-items: center; gap: 7px; margin-bottom: 4px; color: var(--td-muted); font-size: 9px; font-weight: 800; }
        .task-dashboard .td-type-label { padding: 2px 6px; border-radius: 999px; font-size: 8px; text-transform: uppercase; }
        .task-dashboard .td-type-label.packaging { color: var(--td-primary); background: var(--td-primary-soft); }
        .task-dashboard .td-type-label.delivery { color: var(--td-blue); background: rgba(37, 99, 235, .09); }
        .task-dashboard .td-task-title { margin: 0; color: var(--td-text); font-size: 11px; font-weight: 850; }
        .task-dashboard .td-task-description { max-width: 480px; margin: 4px 0 0; overflow: hidden; color: var(--td-muted); font-size: 9px; line-height: 1.4; text-overflow: ellipsis; white-space: nowrap; }

        .task-dashboard .td-customer-name { color: var(--td-text); font-size: 10px; font-weight: 750; }
        .task-dashboard .td-reference { margin-top: 3px; color: var(--td-muted); font-family: ui-monospace, SFMono-Regular, Menlo, monospace; font-size: 9px; }

        .task-dashboard .td-assignee { display: flex; align-items: center; gap: 8px; min-width: 0; }
        .task-dashboard .td-avatar {
            width: 29px;
            height: 29px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex: 0 0 29px;
            border: 1px solid rgba(var(--td-primary-rgb), .16);
            border-radius: 50%;
            color: var(--td-primary);
            background: var(--td-primary-soft);
            font-size: 8px;
            font-weight: 900;
        }
        .task-dashboard .td-assignee-name { overflow: hidden; color: var(--td-text); font-size: 9px; font-weight: 750; text-overflow: ellipsis; white-space: nowrap; }
        .task-dashboard .td-date { color: var(--td-text); font-size: 9px; font-weight: 750; }
        .task-dashboard .td-date-label { margin-top: 3px; color: var(--td-muted); font-size: 8px; }

        .task-dashboard .td-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 5px;
            min-height: 23px;
            padding: 4px 8px;
            border-radius: 999px;
            font-size: 8px;
            font-weight: 850;
            white-space: nowrap;
        }
        .task-dashboard .td-badge.pending { color: var(--td-muted); background: var(--td-soft); border: 1px solid var(--td-border); }
        .task-dashboard .td-badge.in_progress { color: var(--td-blue); background: rgba(37, 99, 235, .10); }
        .task-dashboard .td-badge.waiting, .task-dashboard .td-badge.review { color: var(--td-warning); background: rgba(217, 119, 6, .11); }
        .task-dashboard .td-badge.completed { color: var(--td-success); background: rgba(5, 150, 105, .11); }

        .task-dashboard .td-priority { display: inline-flex; align-items: center; gap: 5px; margin-top: 5px; color: var(--td-muted); font-size: 8px; font-weight: 800; text-transform: capitalize; }
        .task-dashboard .td-priority::before { width: 6px; height: 6px; content: ""; border-radius: 50%; background: var(--td-muted); }
        .task-dashboard .td-priority.urgent::before { background: var(--td-danger); }
        .task-dashboard .td-priority.high::before { background: var(--td-warning); }
        .task-dashboard .td-priority.normal::before { background: var(--td-success); }

        .task-dashboard .td-progress-track { width: 100%; height: 5px; margin-top: 6px; overflow: hidden; border-radius: 999px; background: var(--td-border); }
        .task-dashboard .td-progress-bar { height: 100%; border-radius: inherit; background: linear-gradient(90deg, var(--td-primary), #fb923c); }

        .task-dashboard .td-action {
            width: 34px;
            height: 34px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: 1px solid var(--td-border);
            border-radius: 9px;
            color: var(--td-muted);
            background: var(--td-card);
            text-decoration: none;
        }
        .task-dashboard .td-action:hover { border-color: rgba(var(--td-primary-rgb), .28); color: var(--td-primary); background: var(--td-primary-soft); }

        .task-dashboard .td-empty { display: none; padding: 54px 16px; color: var(--td-muted); text-align: center; }
        .task-dashboard .td-empty i { display: block; margin-bottom: 10px; color: var(--td-primary); font-size: 28px; }
        .task-dashboard .td-footer { padding: 11px 14px; border-top: 1px solid var(--td-border); color: var(--td-muted); background: var(--td-soft); font-size: 9px; }
        .task-dashboard .td-footer-count { color: var(--td-text); font-weight: 850; }

        @media (max-width: 1199.98px) {
            .task-dashboard .td-summary-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
            .task-dashboard .td-list-head,
            .task-dashboard .td-task-row { grid-template-columns: minmax(235px, 1.35fr) minmax(145px, .72fr) minmax(120px, .58fr) minmax(108px, .50fr) 42px; }
            .task-dashboard .td-list-head > :nth-child(4),
            .task-dashboard .td-task-row > :nth-child(4) { display: none; }
        }

        @media (max-width: 991.98px) {
            .task-dashboard { padding: 12px; }
            .task-dashboard .td-header-content,
            .task-dashboard .td-toolbar { align-items: stretch; flex-direction: column; }
            .task-dashboard .td-header-action { align-self: flex-start; }
            .task-dashboard .td-toolbar-right { width: 100%; }
            .task-dashboard .td-search { width: 100%; }
            .task-dashboard .td-list-head { display: none; }
            .task-dashboard .td-task-row { grid-template-columns: 1fr auto; gap: 12px; }
            .task-dashboard .td-task-row > :not(:first-child):not(:last-child) { display: none; }
        }

        @media (max-width: 575.98px) {
            .task-dashboard .td-summary-grid { grid-template-columns: 1fr 1fr; }
            .task-dashboard .td-tabs { width: 100%; overflow-x: auto; }
            .task-dashboard .td-tab { flex: 1 0 auto; }
            .task-dashboard .td-toolbar-right { flex-direction: column; }
            .task-dashboard select.td-control { width: 100%; }
            .task-dashboard .td-footer { align-items: flex-start; flex-direction: column; }
        }
    </style>

    <main class="task-dashboard">
        <section class="td-header">
            <div class="td-header-content">
                <div>
                    <span class="td-eyebrow">
                        <i class="fa-solid fa-list-check"></i>
                        AQPA Task Center
                    </span>
                    <h1 class="td-title">Daftar Tugas Operasional</h1>
                    <p class="td-subtitle">
                        Pantau pekerjaan Packaging dan Delivery dalam satu halaman,
                        mulai dari tugas baru, proses pengerjaan, review, hingga selesai.
                    </p>
                </div>
            </div>
        </section>

        <section class="td-summary-grid">
            <article class="td-summary-card">
                <span class="td-summary-icon orange"><i class="fa-solid fa-list-check"></i></span>
                <div><div class="td-summary-label">Total Tugas</div><div class="td-summary-value">{{ $summary['total'] }}</div></div>
            </article>
            <article class="td-summary-card">
                <span class="td-summary-icon blue"><i class="fa-solid fa-box"></i></span>
                <div><div class="td-summary-label">Packaging</div><div class="td-summary-value">{{ $summary['packaging'] }}</div></div>
            </article>
            <article class="td-summary-card">
                <span class="td-summary-icon green"><i class="fa-solid fa-truck-fast"></i></span>
                <div><div class="td-summary-label">Delivery</div><div class="td-summary-value">{{ $summary['delivery'] }}</div></div>
            </article>
            <article class="td-summary-card">
                <span class="td-summary-icon red"><i class="fa-solid fa-triangle-exclamation"></i></span>
                <div><div class="td-summary-label">Prioritas Tinggi</div><div class="td-summary-value">{{ $summary['urgent'] }}</div></div>
            </article>
        </section>

        <section class="td-content">
            <div class="td-toolbar">
                <div class="td-tabs" role="tablist">
                    <button type="button" class="td-tab is-active" data-task-filter="all"><i class="fa-solid fa-layer-group"></i>Semua</button>
                    <button type="button" class="td-tab" data-task-filter="packaging"><i class="fa-solid fa-box"></i>Packaging</button>
                    <button type="button" class="td-tab" data-task-filter="delivery"><i class="fa-solid fa-truck-fast"></i>Delivery</button>
                </div>

                <div class="td-toolbar-right">
                    <div class="td-search">
                        <i class="fa-solid fa-magnifying-glass"></i>
                        <input type="search" class="td-control" id="taskSearch" placeholder="Cari nomor, customer, tugas..." autocomplete="off">
                    </div>

                    <select class="td-control" id="taskStatusFilter">
                        <option value="all">Semua Status</option>
                        <option value="pending">Pending</option>
                        <option value="in_progress">In Progress</option>
                        <option value="waiting">Menunggu</option>
                        <option value="review">Review</option>
                        <option value="completed">Completed</option>
                    </select>
                </div>
            </div>

            <div class="td-list-head">
                <span>Tugas</span><span>Customer / Referensi</span><span>Penanggung Jawab</span><span>Jatuh Tempo</span><span>Status</span><span></span>
            </div>

            <div id="taskList">
                @foreach($tasks as $task)
                    <article
                        class="td-task-row"
                        data-task-row
                        data-type="{{ $task['type'] }}"
                        data-status="{{ $task['status'] }}"
                        data-search="{{ strtolower($task['number'].' '.$task['reference'].' '.$task['title'].' '.$task['customer'].' '.$task['assignee']) }}"
                    >
                        <div class="td-task-main">
                            <span class="td-type-icon {{ $task['type'] }}">
                                <i class="fa-solid {{ $task['type'] === 'packaging' ? 'fa-box' : 'fa-truck-fast' }}"></i>
                            </span>
                            <div class="min-w-0">
                                <div class="td-task-number">
                                    {{ $task['number'] }}
                                    <span class="td-type-label {{ $task['type'] }}">{{ ucfirst($task['type']) }}</span>
                                </div>
                                <h3 class="td-task-title">{{ $task['title'] }}</h3>
                                <p class="td-task-description">{{ $task['description'] }}</p>
                                <div class="td-progress-track"><div class="td-progress-bar" style="width: {{ $task['progress'] }}%"></div></div>
                            </div>
                        </div>

                        <div>
                            <div class="td-customer-name">{{ $task['customer'] }}</div>
                            <div class="td-reference">SO: {{ $task['reference'] }}</div>
                        </div>

                        <div class="td-assignee">
                            <span class="td-avatar">{{ $task['initial'] }}</span>
                            <span class="td-assignee-name">{{ $task['assignee'] }}</span>
                        </div>

                        <div>
                            <div class="td-date">{{ $task['due_date'] }}</div>
                            <div class="td-date-label">Due date</div>
                        </div>

                        <div>
                            <span class="td-badge {{ $task['status'] }}">
                                <i class="fa-solid {{ $task['status'] === 'completed' ? 'fa-circle-check' : ($task['status'] === 'in_progress' ? 'fa-spinner' : 'fa-clock') }}"></i>
                                {{ $statusLabels[$task['status']] ?? $task['status'] }}
                            </span>
                            <div class="td-priority {{ $task['priority'] }}">{{ $task['priority'] }}</div>
                        </div>

                        <a href="#" class="td-action" title="Lihat detail tugas" aria-label="Lihat detail tugas {{ $task['number'] }}">
                            <i class="fa-solid fa-chevron-right"></i>
                        </a>
                    </article>
                @endforeach
            </div>

            <div class="td-empty" id="taskEmptyState">
                <i class="fa-regular fa-folder-open"></i>
                Tidak ada tugas yang sesuai dengan filter.
            </div>

            <footer class="td-footer">
                <span>Menampilkan <strong class="td-footer-count" id="visibleTaskCount">{{ count($tasks) }}</strong> dari {{ count($tasks) }} tugas.</span>
                <span>Data demo dapat diganti dari controller atau database.</span>
            </footer>
        </section>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const tabs = [...document.querySelectorAll('[data-task-filter]')];
            const rows = [...document.querySelectorAll('[data-task-row]')];
            const searchInput = document.getElementById('taskSearch');
            const statusFilter = document.getElementById('taskStatusFilter');
            const visibleCount = document.getElementById('visibleTaskCount');
            const emptyState = document.getElementById('taskEmptyState');
            let activeType = 'all';

            const applyFilters = () => {
                const keyword = String(searchInput?.value || '').trim().toLowerCase();
                const selectedStatus = statusFilter?.value || 'all';
                let count = 0;

                rows.forEach((row) => {
                    const visible =
                        (activeType === 'all' || row.dataset.type === activeType) &&
                        (selectedStatus === 'all' || row.dataset.status === selectedStatus) &&
                        (!keyword || String(row.dataset.search || '').includes(keyword));

                    row.style.display = visible ? '' : 'none';
                    if (visible) count += 1;
                });

                if (visibleCount) visibleCount.textContent = count;
                if (emptyState) emptyState.style.display = count === 0 ? 'block' : 'none';
            };

            tabs.forEach((tab) => {
                tab.addEventListener('click', () => {
                    activeType = tab.dataset.taskFilter || 'all';
                    tabs.forEach((item) => item.classList.toggle('is-active', item === tab));
                    applyFilters();
                });
            });

            searchInput?.addEventListener('input', applyFilters);
            statusFilter?.addEventListener('change', applyFilters);
            applyFilters();
        });
    </script>
</x-app-layout>