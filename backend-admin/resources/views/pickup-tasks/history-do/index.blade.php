<x-app-layout>
    @include('pickup-tasks.partials.header', ['activeTab' => 'history-do'])

<!-- Konten History Delivery Order -->
<div class="card card-premium table-panel">
    <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
        <div>
            <h5 class="table-panel-title"><i class="fa-solid fa-clock-rotate-left me-2 text-orange"></i>History Delivery Order</h5>
            <div class="secondary-line mt-1">Riwayat penugasan yang telah selesai atau dibatalkan.</div>
        </div>
        <div>
            <i class="fa-solid fa-clock-rotate-left table-header-art d-none d-md-inline" aria-hidden="true"></i>
        </div>
    </div>
    
    <!-- Filter Bar inside Card Body -->
    <div class="filter-panel">
        <form action="{{ route('pickup-tasks.history-do') }}" method="GET" class="row g-2 align-items-center mb-3">
            <div class="col-md-4">
                <div class="input-group input-group-sm filter-search overflow-hidden">
                    <span class="input-group-text bg-white border-0"><i class="fa-solid fa-magnifying-glass text-muted"></i></span>
                    <input type="text" name="search" class="form-control border-0 shadow-none" placeholder="Cari No DO..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-md-3">
                <input type="date" name="date" class="form-control form-control-sm filter-control px-3" value="{{ request('date') }}">
            </div>
            <div class="col-md-3">
                <select name="driver_id" class="form-select form-select-sm filter-control px-3">
                    <option value="">Semua Driver</option>
                    @foreach($drivers as $driver)
                        <option value="{{ $driver->id }}" {{ request('driver_id') == $driver->id ? 'selected' : '' }}>{{ $driver->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 d-flex gap-2 filter-actions">
                <button type="submit" class="btn btn-sm btn-orange w-100 fw-bold rounded-3"><i class="fa-solid fa-filter me-1"></i> Filter</button>
                <a href="{{ route('pickup-tasks.history-do') }}" class="btn btn-sm btn-light border w-100 rounded-3" title="Reset"><i class="fa-solid fa-rotate-right me-1"></i> Reset</a>
            </div>
        </form>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive" style="min-height: 250px;">
            <table class="table task-table align-middle mb-0">
                <thead>
                    <tr>
                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 px-4" style="width: 50px;">No</th>
                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">No Delivery Order</th>
                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Tanggal</th>
                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Driver Utama</th>
                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Co Driver</th>
                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Kendaraan</th>
                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Status</th>
                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Jumlah Tugas</th>
                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-end px-4">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($assignments as $index => $assignment)
                        <tr data-bs-toggle="collapse" data-bs-target="#collapse-{{ $assignment->id }}" style="cursor: pointer;" title="Klik untuk melihat detail tugas">
                            <td class="text-center px-4">
                                <i class="fa-solid fa-chevron-down me-2 text-muted" style="font-size: 10px;"></i>
                                <span class="secondary-line">{{ ($assignments->currentPage() - 1) * $assignments->perPage() + $loop->iteration }}</span>
                            </td>
                            <td><span class="primary-line fw-bold">{{ $assignment->no_do }}</span></td>
                            <td><span class="secondary-line">{{ \Carbon\Carbon::parse($assignment->date)->translatedFormat('d M Y') }}</span></td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 35px; height: 35px; font-size: 14px; font-weight: bold;">
                                        {{ strtoupper(substr($assignment->driver->name ?? '?', 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="primary-line fw-bold">{{ $assignment->driver->name ?? '-' }}</div>
                                        <div class="secondary-line">Utama</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if($assignment->coDriver)
                                    <div class="d-flex align-items-center">
                                        <div class="bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 35px; height: 35px; font-size: 14px; font-weight: bold;">
                                            {{ strtoupper(substr($assignment->coDriver->name ?? '?', 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="primary-line fw-bold">{{ $assignment->coDriver->name ?? '-' }}</div>
                                        </div>
                                    </div>
                                @else
                                    <span class="secondary-line">-</span>
                                @endif
                            </td>
                            <td>
                                @if($assignment->vehicle)
                                    <span class="qty-pill m-0">
                                        <i class="fa-solid fa-truck me-1 text-muted"></i> ([{{ $assignment->vehicle->plate_number }}]) {{ $assignment->vehicle->name }}
                                    </span>
                                @else
                                    <span class="secondary-line">-</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <span class="badge bg-secondary badge-sm">{{ strtoupper(str_replace('_', ' ', $assignment->status)) }}</span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-secondary badge-sm">{{ $assignment->task_count }} Tugas</span>
                            </td>
                            <td class="text-end px-4">
                                <button onclick="event.stopPropagation();" class="btn btn-sm btn-outline-info py-1 px-2" style="font-size: 12px;" title="Cetak DO">
                                    <i class="fa-solid fa-print me-1"></i> Cetak DO
                                </button>
                            </td>
                        </tr>
                        <tr id="collapse-{{ $assignment->id }}" class="collapse bg-light">
                            <td colspan="9" class="p-0 border-0">
                                <div class="p-3 border-bottom shadow-inner" style="background-color: #f8f9fa;">
                                    <h6 class="mb-3 fw-bold" style="font-size: 14px; color: #ea580c;"><i class="fa-solid fa-list-check me-2"></i>Daftar Tugas ({{ $assignment->no_do }})</h6>
                                    <div class="table-responsive bg-white border rounded">
                                        <table class="table table-sm table-hover align-middle mb-0">
                                            <thead class="table-light">
                                                <tr>
                                                    <th class="ps-3 text-secondary text-xs">Tipe</th>
                                                    <th class="text-secondary text-xs">No Ref / SO</th>
                                                    <th class="text-secondary text-xs">Tujuan / Lokasi</th>
                                                    <th class="text-secondary text-xs">Status</th>
                                                    <th class="text-end pe-3 text-secondary text-xs">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php
                                                    $tasks = collect([]);
                                                    if($assignment->manifest) {
                                                        $tasks = $assignment->manifest->pickupTasks->map(function($t) { $t->type = 'pickup'; return $t; })
                                                            ->concat($assignment->manifest->deliveryAssignments->map(function($t) { $t->type = 'delivery'; return $t; }));
                                                    }
                                                @endphp
                                                @forelse($tasks as $task)
                                                    <tr>
                                                        <td class="ps-3">
                                                            @if($task->type === 'pickup')
                                                                <span class="badge bg-light text-dark border"><i class="fa-solid fa-box-open text-orange"></i> PICKUP</span>
                                                            @else
                                                                <span class="badge bg-light text-dark border"><i class="fa-solid fa-truck-fast text-info"></i> DELIVERY</span>
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
                                                            <span class="badge bg-secondary" style="font-size: 10px;">{{ strtoupper(str_replace('_', ' ', $task->status)) }}</span>
                                                        </td>
                                                        <td class="text-end pe-3">
                                                            <a href="{{ route('pickup-tasks.show', $task->id) }}" class="btn btn-sm btn-outline-info py-1 px-2" style="font-size: 12px;" title="Lihat Detail Tugas">
                                                                <i class="fa-solid fa-eye me-1"></i> Detail
                                                            </a>
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="5" class="text-center py-3 text-muted" style="font-size: 13px;">Tidak ada detail tugas</td>
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
                            <td colspan="9" class="text-center empty-state">
                                <div class="empty-icon"><i class="fa-solid fa-clock-rotate-left"></i></div>
                                <h5 class="fw-bold mb-1" style="color:var(--app-text);">Belum Ada Riwayat</h5>
                                <p class="secondary-line mb-0">Riwayat pengiriman yang selesai akan muncul di sini.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
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
    <div class="table-footer">
        <div>Menampilkan <strong>{{ $firstItem }}</strong> - <strong>{{ $lastItem }}</strong> dari <strong>{{ $totalItem }}</strong> data</div>
        @if($isPaginator && $lastPage > 1)
            <div class="table-pagination">
                <a class="page-chip {{ $currentPage <= 1 ? 'disabled' : '' }}" href="{{ $currentPage > 1 ? $assignments->previousPageUrl() : '#' }}"><i class="fa-solid fa-chevron-left"></i></a>
                @for($page = max(1, $currentPage - 1); $page <= min($lastPage, $currentPage + 1); $page++)
                    <a class="page-chip {{ $page === $currentPage ? 'active' : '' }}" href="{{ $assignments->url($page) }}">{{ $page }}</a>
                @endfor
                <a class="page-chip {{ $currentPage >= $lastPage ? 'disabled' : '' }}" href="{{ $currentPage < $lastPage ? $assignments->nextPageUrl() : '#' }}"><i class="fa-solid fa-chevron-right"></i></a>
            </div>
        @endif
    </div>
</div>

</x-app-layout>
