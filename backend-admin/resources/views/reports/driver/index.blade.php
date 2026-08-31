@extends('layouts.app')
@section('title', 'Laporan Driver')

@section('content')
<div class="container-fluid">
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-header bg-white border-bottom-0 pt-4 pb-3 px-4">
            <h4 class="mb-0 fw-bold" style="color: var(--bs-primary);">Daftar Laporan Tugas Driver</h4>
        </div>
        <div class="card-body px-4 pb-4">
            <div class="table-responsive">
                <table class="table custom-table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>No Referensi</th>
                            <th>Driver</th>
                            <th>Kendaraan</th>
                            <th>Tanggal Penugasan</th>
                            <th>Tipe Tugas</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tasks as $key => $task)
                        <tr>
                            <td>{{ $tasks->firstItem() + $key }}</td>
                            <td class="fw-semibold">{{ $task->reference_number }}</td>
                            <td>{{ $task->driver_name ?? '-' }}</td>
                            <td>{{ $task->plate_number ?? '-' }}</td>
                            <td>{{ \Carbon\Carbon::parse($task->assigned_at)->format('d M Y H:i') }}</td>
                            <td>
                                @if($task->task_type == 'pickup')
                                    <span class="badge bg-info text-dark">Penjemputan</span>
                                @else
                                    <span class="badge bg-secondary">Pengiriman</span>
                                @endif
                            </td>
                            <td>
                                @php
                                    $bg = 'bg-secondary';
                                    if ($task->status == 'assigned') $bg = 'bg-warning text-dark';
                                    if ($task->status == 'on_route' || $task->status == 'arrived') $bg = 'bg-primary';
                                    if ($task->status == 'delivered') $bg = 'bg-success';
                                    if ($task->status == 'failed' || $task->status == 'cancelled') $bg = 'bg-danger';
                                @endphp
                                <span class="badge {{ $bg }}">{{ strtoupper($task->status) }}</span>
                            </td>
                            <td>
                                <a href="{{ route('driver-reports.show', $task->id) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                    <i class="fa-solid fa-file-lines me-1"></i> Lihat Laporan
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">Belum ada data tugas.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="d-flex justify-content-end mt-3">
                {{ $tasks->links() }}
            </div>
        </div>
    </div>
</div>

<style>
.custom-table th {
    font-weight: 600;
    color: #475569;
    border-bottom: 2px solid #e2e8f0;
    padding: 1rem;
}
.custom-table td {
    padding: 1rem;
    color: #1e293b;
    border-bottom: 1px solid #f1f5f9;
}
</style>
@endsection
