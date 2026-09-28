<x-app-layout>
    @include('pickup-tasks.partials.header', ['activeTab' => 'list-penugasan'])

    <style>
        .table-responsive {
            overflow: visible !important;
        }
        .table-responsive-wrapper {
            overflow-x: auto;
            overflow-y: visible;
            -webkit-overflow-scrolling: touch;
            padding-bottom: 120px;
        }
        .action-dropdown .dropdown-menu {
            z-index: 1050;
            position: absolute !important;
        }
        .collapse-indicator {
            transition: transform 0.3s ease;
        }
        tr[aria-expanded="true"] .collapse-indicator {
            transform: rotate(180deg);
        }
    </style>

<!-- Konten List Penugasan -->
<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center rounded-top-4">
        <div>
            <h5 class="fw-bold mb-0" style="color: #1e40af;"><i class="fa-solid fa-clipboard-list me-2 text-primary"></i>Daftar Penugasan</h5>
            <div class="text-muted small mt-1">Pantau dan kelola penugasan driver.</div>
        </div>
    </div>
    
    <!-- Filter Bar inside Card Body -->
    <div class="card-body bg-light border-bottom py-3">
        <form action="{{ route('pickup-tasks.list-penugasan') }}" method="GET" class="row g-2 align-items-center">
            <div class="col-md-4">
                <div class="input-group bg-white rounded-3 border">
                    <span class="input-group-text bg-transparent border-0"><i class="fa-solid fa-magnifying-glass text-muted"></i></span>
                    <input type="text" name="search" class="form-control border-0 shadow-none bg-transparent" placeholder="Cari No DO..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-md-3">
                <input type="date" name="date" class="form-control rounded-3" value="{{ request('date') }}">
            </div>
            <div class="col-md-3">
                <select name="driver_id" class="form-select rounded-3">
                    <option value="">Semua Driver</option>
                    @foreach($drivers as $driver)
                        <option value="{{ $driver->id }}" {{ request('driver_id') == $driver->id ? 'selected' : '' }}>{{ $driver->full_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-primary w-100 fw-bold rounded-3"><i class="fa-solid fa-filter me-1"></i> Filter</button>
                <a href="{{ route('pickup-tasks.list-penugasan') }}" class="btn btn-light border rounded-3 px-3" title="Reset"><i class="fa-solid fa-rotate-right"></i></a>
            </div>
        </form>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive-wrapper">
            <div class="table-responsive" style="min-height: 300px;">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="text-secondary text-xs fw-bold px-4" style="width: 50px;">No</th>
                            <th class="text-secondary text-xs fw-bold">No Delivery Order</th>
                            <th class="text-secondary text-xs fw-bold">Tanggal</th>
                            <th class="text-secondary text-xs fw-bold">Dibuat Oleh</th>
                            <th class="text-secondary text-xs fw-bold">Driver Utama</th>
                            <th class="text-secondary text-xs fw-bold">Co Driver</th>
                            <th class="text-secondary text-xs fw-bold">Kendaraan</th>
                            <th class="text-secondary text-xs fw-bold text-center">Jumlah Tugas</th>
                            <th class="text-secondary text-xs fw-bold text-end px-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($assignments as $index => $assignment)
                            <tr data-bs-toggle="collapse" data-bs-target="#collapse-{{ $assignment->id }}" aria-expanded="false" style="cursor: pointer;" title="Klik untuk melihat detail tugas">
                                <td class="text-center px-4">
                                    <i class="fa-solid fa-chevron-down me-2 text-muted collapse-indicator" style="font-size: 10px;"></i>
                                    <span class="text-muted small">{{ ($assignments->currentPage() - 1) * $assignments->perPage() + $loop->iteration }}</span>
                                </td>
                                <td><span class="fw-bold text-dark">{{ $assignment->no_do }}</span></td>
                                <td><span class="text-muted small">{{ \Carbon\Carbon::parse($assignment->date)->translatedFormat('d M Y') }}</span></td>
                                <td><span class="badge bg-light text-dark border"><i class="fa-solid fa-user-gear me-1"></i> {{ $assignment->assigned_by_name ?? 'Sistem/Admin' }}</span></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2 shadow-sm" style="width: 32px; height: 32px; font-size: 13px; font-weight: bold;">
                                            {{ strtoupper(substr($assignment->driver->full_name ?? '?', 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="fw-bold" style="font-size: 14px;">{{ $assignment->driver->full_name ?? '-' }}</div>
                                            <div class="text-muted" style="font-size: 11px;">Utama</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @if($assignment->coDriver)
                                        <div class="d-flex align-items-center">
                                            <div class="bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center me-2 shadow-sm" style="width: 32px; height: 32px; font-size: 13px; font-weight: bold;">
                                                {{ strtoupper(substr($assignment->coDriver->full_name ?? '?', 0, 1)) }}
                                            </div>
                                            <div>
                                                <div class="fw-bold" style="font-size: 14px;">{{ $assignment->coDriver->full_name ?? '-' }}</div>
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($assignment->vehicle)
                                        <span class="badge bg-light text-dark border px-2 py-1">
                                            <i class="fa-solid fa-truck me-1 text-primary"></i> [{{ $assignment->vehicle->plate_number }}] {{ $assignment->vehicle->name }}
                                        </span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-info bg-opacity-10 text-info border border-info rounded-pill px-3">{{ $assignment->task_count }} Tugas</span>
                                </td>
                                <td class="text-end px-4">
                                    <div class="dropdown action-dropdown">
                                        <button class="btn btn-sm btn-light border rounded-3 dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false" onclick="event.stopPropagation();">
                                            <i class="fa-solid fa-ellipsis-vertical px-1"></i> Aksi
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 rounded-3">
                                            <li>
                                                <button onclick="event.stopPropagation(); window.openEditPenugasanModal('{{ $assignment->id }}', '{{ $assignment->no_do }}', '{{ $assignment->driver_id }}', '{{ $assignment->co_driver_id }}', '{{ $assignment->vehicle_id }}', '{{ $assignment->is_out_of_city }}', '{{ $assignment->estimated_arrival }}')" class="dropdown-item py-2">
                                                    <i class="fa-solid fa-list-check me-2 text-primary"></i> Kelola Tugas
                                                </button>
                                            </li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li>
                                                <button onclick="event.stopPropagation();" class="dropdown-item py-2">
                                                    <i class="fa-solid fa-print me-2 text-info"></i> Cetak DO
                                                </button>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                            <tr id="collapse-{{ $assignment->id }}" class="collapse bg-light">
                                <td colspan="9" class="p-0 border-0">
                                    <div class="p-4 border-bottom shadow-inner" style="background-color: #f8fafc;">
                                        <h6 class="mb-3 fw-bold" style="font-size: 14px; color: #ea580c;"><i class="fa-solid fa-list-check me-2"></i>Daftar Tugas ({{ $assignment->no_do }})</h6>
                                        <div class="table-responsive bg-white border rounded-3 shadow-sm">
                                            <table class="table table-sm table-hover align-middle mb-0">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th class="ps-4 text-secondary text-xs">Tipe</th>
                                                        <th class="text-secondary text-xs">No Ref / SO</th>
                                                        <th class="text-secondary text-xs">Tujuan / Lokasi</th>
                                                        <th class="text-secondary text-xs">Status</th>
                                                        <th class="text-end pe-4 text-secondary text-xs">History</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @php
                                                        $tasks = collect([]);
                                                        if($assignment->manifest) {
                                                            $tasks = $assignment->manifest->historicalPickupTasks->map(function($t) { $t->type = 'pickup'; return $t; })
                                                                ->concat($assignment->manifest->historicalDeliveryAssignments->map(function($t) { $t->type = 'delivery'; return $t; }));
                                                        }
                                                    @endphp
                                                    @forelse($tasks as $task)
                                                        <tr data-bs-toggle="collapse" data-bs-target="#history-{{ $task->id }}" style="cursor: pointer;" title="Klik untuk melihat history">
                                                            <td class="ps-4">
                                                                @if($task->type === 'pickup')
                                                                    <span class="badge bg-orange text-white bg-opacity-75"><i class="fa-solid fa-box-open me-1"></i> PICKUP</span>
                                                                @else
                                                                    <span class="badge bg-info text-white bg-opacity-75"><i class="fa-solid fa-truck-fast me-1"></i> DELIVERY</span>
                                                                @endif
                                                            </td>
                                                            <td>
                                                                <span class="fw-bold" style="font-size: 13px;">
                                                                    {{ $task->type === 'pickup' ? ($task->reference_number ?? 'N/A') : ($task->salesOrder->so_number ?? 'N/A') }}
                                                                </span>
                                                            </td>
                                                            <td style="font-size: 13px;">
                                                                {{ $task->type === 'pickup' ? ($task->pickup_name ?? '-') : ($task->salesOrder->customer_name ?? '-') }}
                                                            </td>
                                                            <td>
                                                                @php
                                                                    $displayStatus = $task->status;
                                                                    if (!in_array($task->status, ['completed', 'delivered']) && \Carbon\Carbon::parse($assignment->date)->startOfDay()->lt(\Carbon\Carbon::today())) {
                                                                        $displayStatus = 'tidak_terkirim';
                                                                    }
                                                                    if ($displayStatus === 'pending') {
                                                                        $displayStatus = 'tertunda';
                                                                    } elseif (in_array($displayStatus, ['completed', 'delivered'])) {
                                                                        $displayStatus = 'selesai';
                                                                    }
                                                                @endphp
                                                                <span class="badge bg-secondary" style="font-size: 10px;">{{ strtoupper(str_replace('_', ' ', $displayStatus)) }}</span>
                                                            </td>
                                                            <td class="text-end pe-4">
                                                                @if(!in_array($task->status, ['completed', 'delivered']))
                                                                <form action="{{ route('pickup-tasks.penugasan.remove-task', ['delivery_order' => $assignment->id, 'type' => $task->type, 'taskId' => $task->id]) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin mencabut tugas ini dari penugasan?');">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit" onclick="event.stopPropagation();" class="btn btn-sm btn-link text-danger p-0 me-3" title="Cabut Tugas">
                                                                        <i class="fa-solid fa-xmark" style="font-size: 14px;"></i>
                                                                    </button>
                                                                </form>
                                                                @endif
                                                                <i class="fa-solid fa-chevron-down text-muted" style="font-size: 12px;"></i>
                                                            </td>
                                                        </tr>
                                                        <tr id="history-{{ $task->id }}" class="collapse bg-white">
                                                            <td colspan="5" class="p-0 border-0">
                                                                <div class="px-4 py-3 shadow-inner border-bottom" style="background-color: #fafafa;">
                                                                    <h6 class="mb-3 text-secondary fw-bold" style="font-size: 13px;"><i class="fa-solid fa-clock-rotate-left me-2"></i>History Status Tugas ({{ \Carbon\Carbon::parse($assignment->date)->format('d M Y') }})</h6>
                                                                    @php
                                                                        // Hanya ambil history pada tanggal assignment ini saja
                                                                        $assignmentDate = \Carbon\Carbon::parse($assignment->date)->format('Y-m-d');
                                                                        $historyAsc = $task->history()
                                                                                           ->whereDate('created_at', $assignmentDate)
                                                                                           ->orderBy('created_at', 'asc')
                                                                                           ->get();
                                                                    @endphp

                                                                    <div class="ms-2 border-start border-2 ps-3 border-secondary" style="border-color: #dee2e6 !important;">
                                                                        @php
                                                                            $assignedEvent = $historyAsc->where('status', 'assigned')->first();
                                                                            $onRouteEvent = $historyAsc->where('status', 'on_route')->first();
                                                                            $arrivedEvent = $historyAsc->where('status', 'arrived')->first();
                                                                            $finalEvent = $historyAsc->whereIn('status', ['completed', 'delivered', 'failed', 'pending'])->last();
                                                                            
                                                                            $isFailed = $finalEvent && in_array($finalEvent->status, ['failed', 'pending']);
                                                                        @endphp

                                                                        <!-- Persiapan -->
                                                                        <div class="position-relative mb-3">
                                                                            <div class="position-absolute bg-primary rounded-circle shadow-sm" style="width: 12px; height: 12px; left: -23px; top: 3px;"></div>
                                                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                                                <span class="fw-bold text-dark" style="font-size: 12px;">PERSIAPAN</span>
                                                                                <span class="text-muted" style="font-size: 11px;">
                                                                                    {{ $assignedEvent ? 'Tanggal: '.$assignedEvent->created_at->format('d M Y').' | Jam: '.$assignedEvent->created_at->format('H:i') : '-' }}
                                                                                </span>
                                                                            </div>
                                                                            @if($assignedEvent)
                                                                                @php
                                                                                    $adminName = $assignedEvent->recorder->name ?? ($task->assignedBy->name ?? ($task->assigner->name ?? 'Admin'));
                                                                                @endphp
                                                                                <div class="text-muted" style="font-size: 12px;">Dibuat oleh: <strong>{{ $adminName }}</strong></div>
                                                                            @else
                                                                                <div class="text-muted" style="font-size: 12px;">Dibuat oleh: <strong>{{ $task->assignedBy->name ?? ($task->assigner->name ?? 'Admin') }}</strong></div>
                                                                            @endif
                                                                        </div>

                                                                        <!-- Perjalanan -->
                                                                        <div class="position-relative mb-3">
                                                                            <div class="position-absolute bg-info rounded-circle shadow-sm" style="width: 12px; height: 12px; left: -23px; top: 3px;"></div>
                                                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                                                <span class="fw-bold text-dark" style="font-size: 12px;">PERJALANAN</span>
                                                                                <span class="text-muted" style="font-size: 11px;">
                                                                                    @if($isFailed && !$onRouteEvent)
                                                                                        <span class="text-danger fw-bold">TIDAK TERKIRIM</span>
                                                                                    @else
                                                                                        {{ $onRouteEvent ? 'Tanggal: '.$onRouteEvent->created_at->format('d M Y').' | Jam: '.$onRouteEvent->created_at->format('H:i') : '-' }}
                                                                                    @endif
                                                                                </span>
                                                                            </div>
                                                                        </div>

                                                                        <!-- Sampai -->
                                                                        <div class="position-relative mb-3">
                                                                            <div class="position-absolute bg-warning rounded-circle shadow-sm" style="width: 12px; height: 12px; left: -23px; top: 3px;"></div>
                                                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                                                <span class="fw-bold text-dark" style="font-size: 12px;">SAMPAI</span>
                                                                                <span class="text-muted" style="font-size: 11px;">
                                                                                    {{ $arrivedEvent ? 'Tanggal: '.$arrivedEvent->created_at->format('d M Y').' | Jam: '.$arrivedEvent->created_at->format('H:i') : '-' }}
                                                                                </span>
                                                                            </div>
                                                                        </div>

                                                                        <!-- Serah Terima -->
                                                                        <div class="position-relative mb-3">
                                                                            <div class="position-absolute bg-{{ $isFailed ? 'danger' : 'success' }} rounded-circle shadow-sm" style="width: 12px; height: 12px; left: -23px; top: 3px;"></div>
                                                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                                                <span class="fw-bold text-dark" style="font-size: 12px;">SERAH TERIMA</span>
                                                                                <span class="text-muted" style="font-size: 11px;">
                                                                                    @if($isFailed && !$finalEvent && !$onRouteEvent)
                                                                                        <span class="text-danger fw-bold">TIDAK TERKIRIM</span>
                                                                                    @else
                                                                                        {{ $finalEvent ? 'Tanggal: '.$finalEvent->created_at->format('d M Y').' | Jam: '.$finalEvent->created_at->format('H:i') : '-' }}
                                                                                    @endif
                                                                                </span>
                                                                            </div>
                                                                            
                                                                            @if($finalEvent || in_array($task->status, ['completed', 'delivered', 'failed', 'pending']))
                                                                                <div class="mt-2 p-3 rounded" style="background-color: {{ $isFailed ? '#fff5f5' : '#f0fdf4' }}; border: 1px solid {{ $isFailed ? '#fed7d7' : '#bbf7d0' }};">
                                                                                    @if($isFailed)
                                                                                        <div class="text-danger mb-2 pb-2 border-bottom" style="font-size: 12px; border-color: #fed7d7 !important;">
                                                                                            <i class="fa-solid fa-circle-exclamation me-1"></i> <strong>KENDALA:</strong><br>
                                                                                            <span class="ms-3">{{ $task->failure_reason ?? ($finalEvent->notes ?? 'Tidak ada detail catatan') }}</span>
                                                                                        </div>
                                                                                    @else
                                                                                        <div class="text-success mb-2 pb-2 border-bottom" style="font-size: 12px; border-color: #bbf7d0 !important;">
                                                                                            <i class="fa-solid fa-circle-check me-1"></i> <strong>STATUS: BERHASIL</strong><br>
                                                                                            <span class="ms-3">{{ $finalEvent->notes ?? 'Selesai' }}</span>
                                                                                        </div>
                                                                                    @endif
                                                                                    
                                                                                    <div class="d-flex flex-wrap gap-3">
                                                                                        @if($task->receiver_name)
                                                                                            <div class="text-muted" style="font-size: 12px;">
                                                                                                <i class="fa-solid fa-user-check text-secondary me-1"></i> <strong>Penerima:</strong><br>
                                                                                                <span class="ms-3 text-dark">{{ $task->receiver_name }} {{ $task->receiver_role ? '('.$task->receiver_role.')' : '' }}</span>
                                                                                            </div>
                                                                                        @endif
                                                                                        @if($task->item_condition)
                                                                                            <div class="text-muted" style="font-size: 12px;">
                                                                                                <i class="fa-solid fa-box-open text-secondary me-1"></i> <strong>Kondisi:</strong><br>
                                                                                                <span class="ms-3 text-dark">{{ $task->item_condition }}</span>
                                                                                            </div>
                                                                                        @endif
                                                                                    </div>

                                                                                    @php
                                                                                        $proofImage = $task->proof_of_delivery_path ?? $task->photo_proof ?? null;
                                                                                    @endphp
                                                                                    @if($proofImage)
                                                                                        <div class="mt-3 pt-2 border-top" style="border-color: {{ $isFailed ? '#fed7d7' : '#bbf7d0' }} !important;">
                                                                                            <div class="mb-1" style="font-size: 11px; color: #666;"><i class="fa-solid fa-camera me-1"></i> Foto Bukti:</div>
                                                                                            <a href="{{ Storage::url($proofImage) }}" target="_blank" class="d-inline-block">
                                                                                                <img src="{{ Storage::url($proofImage) }}" class="rounded shadow-sm border" style="height: 70px; width: auto; object-fit: cover;" alt="Bukti Foto">
                                                                                            </a>
                                                                                        </div>
                                                                                    @endif
                                                                                </div>
                                                                            @endif
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    @empty
                                                        <tr>
                                                            <td colspan="5" class="text-center py-4 text-muted" style="font-size: 13px;">Tidak ada detail tugas</td>
                                                        </tr>
                                                    @endforelse
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-5">
                                    <div class="text-muted mb-2"><i class="fa-solid fa-clipboard-list fa-3x opacity-50"></i></div>
                                    <h5 class="fw-bold mb-1" style="color:var(--app-text);">Belum Ada Penugasan</h5>
                                    <p class="text-muted small mb-0">Tugas penugasan akan muncul di sini.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    @php
        $isPaginator = method_exists($assignments, 'currentPage') && method_exists($assignments, 'lastPage');
        $currentPage = $isPaginator ? $assignments->currentPage() : 1;
        $lastPage = $isPaginator ? $assignments->lastPage() : 1;
        $firstItem = $isPaginator ? ($assignments->firstItem() ?? 0) : ($assignments->count() ? 1 : 0);
        $lastItem = $isPaginator ? ($assignments->lastItem() ?? 0) : $assignments->count();
        $totalItem = $isPaginator ? $assignments->total() : $assignments->count();
    @endphp
    <div class="card-footer bg-white border-top py-3 rounded-bottom-4">
        <div class="d-flex justify-content-between align-items-center">
            <div class="text-muted small">Menampilkan <strong>{{ $firstItem }}</strong> - <strong>{{ $lastItem }}</strong> dari <strong>{{ $totalItem }}</strong> data</div>
            @if($isPaginator && $lastPage > 1)
                <nav>
                    <ul class="pagination pagination-sm mb-0">
                        <li class="page-item {{ $currentPage <= 1 ? 'disabled' : '' }}">
                            <a class="page-link" href="{{ $currentPage > 1 ? $assignments->previousPageUrl() : '#' }}"><i class="fa-solid fa-chevron-left"></i></a>
                        </li>
                        @for($page = max(1, $currentPage - 1); $page <= min($lastPage, $currentPage + 1); $page++)
                            <li class="page-item {{ $page === $currentPage ? 'active' : '' }}">
                                <a class="page-link" href="{{ $assignments->url($page) }}">{{ $page }}</a>
                            </li>
                        @endfor
                        <li class="page-item {{ $currentPage >= $lastPage ? 'disabled' : '' }}">
                            <a class="page-link" href="{{ $currentPage < $lastPage ? $assignments->nextPageUrl() : '#' }}"><i class="fa-solid fa-chevron-right"></i></a>
                        </li>
                    </ul>
                </nav>
            @endif
        </div>
    </div>
</div>

@include('pickup-tasks.penugasan.partials.edit-penugasan')

</x-app-layout>
