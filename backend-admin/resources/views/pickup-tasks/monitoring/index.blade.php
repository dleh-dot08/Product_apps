<x-app-layout>
    @include('pickup-tasks.partials.header', ['activeTab' => 'monitoring'])

<!-- Konten Monitoring Driver -->
<div class="card card-premium table-panel">
    <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
        <div>
            <h5 class="table-panel-title"><i class="fa-solid fa-map-location-dot me-2 text-orange"></i>Monitoring Driver</h5>
            <div class="secondary-line mt-1">Data aktivitas shift driver dan pelaporan tugas harian.</div>
        </div>
        <div>
            <i class="fa-solid fa-map-location-dot table-header-art d-none d-md-inline" aria-hidden="true"></i>
        </div>
    </div>

    <!-- Filter Bar inside Card Body -->
    <div class="filter-panel">
        <form action="{{ route('pickup-tasks.monitoring') }}" method="GET" class="row g-2 align-items-center mb-3">
            <div class="col-md-4">
                <div class="input-group input-group-sm filter-search overflow-hidden">
                    <span class="input-group-text bg-white border-0"><i class="fa-solid fa-magnifying-glass text-muted"></i></span>
                    <input type="text" name="search" class="form-control border-0 shadow-none" placeholder="Cari Driver / Plat Mobil..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-md-3">
                <input type="date" name="date" class="form-control form-control-sm filter-control px-3" value="{{ request('date', now()->toDateString()) }}">
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
                <button type="submit" class="btn btn-sm btn-orange w-100 fw-bold rounded-3"><i class="fa-solid fa-filter"></i></button>
                <a href="{{ route('pickup-tasks.monitoring') }}" class="btn btn-sm btn-light border w-100 rounded-3" title="Reset"><i class="fa-solid fa-rotate-right"></i></a>
                <button type="submit" formaction="{{ route('pickup-tasks.monitoring.export') }}" class="btn btn-sm btn-success w-100 fw-bold rounded-3" title="Export Excel"><i class="fa-solid fa-file-excel"></i></button>
            </div>
        </form>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive" style="min-height: 250px;">
            <table class="table task-table align-middle mb-0">
                <thead>
                    <tr>
                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 px-4" style="width: 50px;">No</th>
                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Driver</th>
                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Mobil</th>
                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Clock In</th>
                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Clock Out</th>
                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Total KM</th>
                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Lap. Tugas</th>
                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Lap. Pengeluaran</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($activeShifts as $index => $shift)
                        <tr>
                            <td class="text-center px-4">
                                <span class="secondary-line">{{ ($activeShifts->currentPage() - 1) * $activeShifts->perPage() + $loop->iteration }}</span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 35px; height: 35px; font-size: 14px; font-weight: bold;">
                                        {{ strtoupper(substr($shift->driver->full_name ?? ($shift->driver->name ?? '?'), 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="primary-line fw-bold">{{ $shift->driver->full_name ?? ($shift->driver->name ?? '-') }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if($shift->vehicle)
                                    <span class="qty-pill m-0">
                                        <i class="fa-solid fa-truck me-1 text-muted"></i> [{{ $shift->vehicle->plate_number }}] {{ $shift->vehicle->name }}
                                    </span>
                                @else
                                    <span class="secondary-line">-</span>
                                @endif
                            </td>
                            <td>
                                <span class="primary-line fw-bold">{{ \Carbon\Carbon::parse($shift->check_in_at)->format('H:i') }}</span>
                                <div class="secondary-line">{{ \Carbon\Carbon::parse($shift->check_in_at)->translatedFormat('d M Y') }}</div>
                            </td>
                            <td>
                                @if($shift->check_out_at)
                                    <span class="primary-line fw-bold">{{ \Carbon\Carbon::parse($shift->check_out_at)->format('H:i') }}</span>
                                    <div class="secondary-line">{{ \Carbon\Carbon::parse($shift->check_out_at)->translatedFormat('d M Y') }}</div>
                                @else
                                    <span class="badge bg-warning text-dark"><i class="fa-solid fa-spinner fa-spin me-1"></i> Berjalan</span>
                                @endif
                            </td>
                            <td>
                                <span class="primary-line fw-bold">{{ $shift->total_distance }} KM</span>
                            </td>
                            <td class="text-center">
                                @if($shift->has_tasks)
                                    <i class="fa-solid fa-circle-check text-success fs-5"></i>
                                @else
                                    <i class="fa-solid fa-circle-xmark text-danger fs-5"></i>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($shift->has_expenses)
                                    <i class="fa-solid fa-circle-check text-success fs-5"></i>
                                @else
                                    <i class="fa-solid fa-circle-xmark text-danger fs-5"></i>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center empty-state py-5">
                                <div class="empty-icon"><i class="fa-solid fa-map-location-dot"></i></div>
                                <h5 class="fw-bold mb-1" style="color:var(--app-text);">Belum Ada Driver Aktif</h5>
                                <p class="secondary-line mb-0">Shift driver yang berjalan hari ini akan muncul di sini.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    
    @php
        $isPaginator = method_exists($activeShifts, 'currentPage') && method_exists($activeShifts, 'lastPage');
        $currentPage = $isPaginator ? $activeShifts->currentPage() : 1;
        $lastPage = $isPaginator ? $activeShifts->lastPage() : 1;
        $firstItem = $isPaginator ? ($activeShifts->firstItem() ?? 0) : ($activeShifts->count() ? 1 : 0);
        $lastItem = $isPaginator ? ($activeShifts->lastItem() ?? 0) : $activeShifts->count();
        $totalItem = $isPaginator ? $activeShifts->total() : $activeShifts->count();
    @endphp
    <div class="table-footer">
        <div>Menampilkan <strong>{{ $firstItem }}</strong> - <strong>{{ $lastItem }}</strong> dari <strong>{{ $totalItem }}</strong> data</div>
        @if($isPaginator && $lastPage > 1)
            <div class="table-pagination">
                <a class="page-chip {{ $currentPage <= 1 ? 'disabled' : '' }}" href="{{ $currentPage > 1 ? $activeShifts->appends(request()->query())->previousPageUrl() : '#' }}"><i class="fa-solid fa-chevron-left"></i></a>
                @for($page = max(1, $currentPage - 1); $page <= min($lastPage, $currentPage + 1); $page++)
                    <a class="page-chip {{ $page === $currentPage ? 'active' : '' }}" href="{{ $activeShifts->appends(request()->query())->url($page) }}">{{ $page }}</a>
                @endfor
                <a class="page-chip {{ $currentPage >= $lastPage ? 'disabled' : '' }}" href="{{ $currentPage < $lastPage ? $activeShifts->appends(request()->query())->nextPageUrl() : '#' }}"><i class="fa-solid fa-chevron-right"></i></a>
            </div>
        @endif
    </div>
</div>
</x-app-layout>
