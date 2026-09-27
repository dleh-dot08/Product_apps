<x-app-layout>
    @include('pickup-tasks.partials.header', ['activeTab' => 'list-tugas'])

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
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Nomor SO / PO</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Jumlah Barang</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Lokasi Awal</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Lokasi Tujuan</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Status</th>

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
                                    <td class="text-center">
                                        @php
                                            $itemsCount = 0;
                                            if ($task->task_type === 'pickup') {
                                                $itemsCount = $task->items ? $task->items->count() : 0;
                                                if ($itemsCount === 0 && !empty($task->item_description)) {
                                                    if (\Illuminate\Support\Str::startsWith($task->item_description, '{')) {
                                                        $json = json_decode($task->item_description, true);
                                                        $itemsCount = count($json['items'] ?? []);
                                                    }
                                                    if ($itemsCount === 0) $itemsCount = 1;
                                                }
                                            } else {
                                                if ($task->salesOrder) {
                                                    $itemsCount = $task->salesOrder->items ? $task->salesOrder->items->count() : 0;
                                                    if ($itemsCount === 0 && is_array($task->salesOrder->source_data)) {
                                                        $itemsCount = count($task->salesOrder->source_data['items'] ?? []);
                                                        if ($itemsCount === 0 && isset($task->salesOrder->source_data['deskripsi_barang'])) {
                                                            $itemsCount = 1;
                                                        }
                                                    }
                                                    if ($itemsCount === 0) $itemsCount = 1;
                                                }
                                            }
                                        @endphp
                                        <span class="badge bg-secondary badge-sm">{{ $itemsCount }} item</span>
                                    </td>
                                    <td>
                                        <div class="primary-line fw-bold">{{ $task->pickup_name ?? '-' }}</div>
                                    </td>
                                    <td>
                                        @if($task->task_type === 'pickup')
                                            <div class="primary-line fw-bold">{{ $task->destination_name ?? '-' }}</div>
                                        @else
                                            <div class="primary-line fw-bold">{{ $task->salesOrder->customer_name ?? '-' }}</div>
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
                                                @if(auth()->user()->hasPermission('Edit Tugas'))
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
                                                @endif

                                                @if(auth()->user()->hasPermission('Delete Tugas'))
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
                                                @endif
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


    <script>
        function openEditTaskModal(btn) {
            let task = JSON.parse(btn.getAttribute('data-task'));
            let items = JSON.parse(btn.getAttribute('data-items'));
            task.update_url = btn.getAttribute('data-url');
            window.openTaskModal('edit', task, items);
        }
    </script>
</x-app-layout>
