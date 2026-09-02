@extends('layouts.app')

@section('content')
<style>
    /* Styling Premium Oranye (seperti Pickup Tasks & HPP) */
    .bg-orange-gradient {
        background: linear-gradient(135deg, #ea580c 0%, #c2410c 100%);
    }
    .text-orange {
        color: #ea580c;
    }
    .btn-orange {
        background-color: #ea580c;
        color: white;
        border: none;
        transition: all 0.3s ease;
    }
    .btn-orange:hover {
        background-color: #c2410c;
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(234, 88, 12, 0.3);
    }
    .btn-outline-orange {
        color: #ea580c;
        border-color: #ea580c;
        transition: all 0.3s ease;
    }
    .btn-outline-orange:hover {
        background-color: #ea580c;
        color: white;
    }
    .card-premium {
        border: none;
        border-radius: 1rem;
        box-shadow: 0 4px 20px rgba(0,0,0,0.05);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    html[data-bs-theme="dark"] .card-premium {
        background-color: #1e293b;
        box-shadow: 0 4px 20px rgba(0,0,0,0.2);
    }
    
    /* Stat Cards */
    .stat-card {
        border-radius: 1rem;
        overflow: hidden;
        position: relative;
    }
    .stat-icon-wrapper {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
    }
    .stat-icon-bg {
        position: absolute;
        right: -10px;
        bottom: -10px;
        font-size: 5rem;
        opacity: 0.05;
        transform: rotate(-15deg);
    }
</style>

<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1 fw-bold text-dark"><i class="fa-solid fa-money-bill-wave text-orange me-2"></i>Pengeluaran Operasional (Expenses)</h4>
            <p class="text-muted small mb-0">Kelola dan pantau seluruh pengeluaran driver selama shift kerja berjalan.</p>
        </div>
        <div>
            <button type="button" class="btn btn-orange rounded-pill px-4 fw-semibold shadow-sm" data-bs-toggle="modal" data-bs-target="#createExpenseModal">
                <i class="fa-solid fa-plus me-2"></i>Catat Pengeluaran
            </button>
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
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Statistik Pengeluaran -->
    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="card card-premium stat-card h-100 bg-white">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-3">
                        <div class="stat-icon-wrapper bg-orange-gradient text-white shadow-sm me-3">
                            <i class="fa-solid fa-wallet"></i>
                        </div>
                        <div>
                            <p class="text-muted small fw-semibold text-uppercase mb-0" style="letter-spacing: 0.5px;">Total Pengeluaran</p>
                            <h4 class="fw-bold mb-0 text-dark">Rp {{ number_format($totalAmount, 0, ',', '.') }}</h4>
                        </div>
                    </div>
                    <i class="fa-solid fa-wallet stat-icon-bg"></i>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card card-premium stat-card h-100 bg-white">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-3">
                        <div class="stat-icon-wrapper bg-danger bg-opacity-10 text-danger shadow-sm me-3">
                            <i class="fa-solid fa-gas-pump"></i>
                        </div>
                        <div>
                            <p class="text-muted small fw-semibold text-uppercase mb-0" style="letter-spacing: 0.5px;">Bensin (Fuel)</p>
                            <h4 class="fw-bold mb-0 text-dark">Rp {{ number_format($fuelTotal, 0, ',', '.') }}</h4>
                        </div>
                    </div>
                    <i class="fa-solid fa-gas-pump text-danger stat-icon-bg"></i>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card card-premium stat-card h-100 bg-white">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-3">
                        <div class="stat-icon-wrapper bg-primary bg-opacity-10 text-primary shadow-sm me-3">
                            <i class="fa-solid fa-road-barrier"></i>
                        </div>
                        <div>
                            <p class="text-muted small fw-semibold text-uppercase mb-0" style="letter-spacing: 0.5px;">E-Toll</p>
                            <h4 class="fw-bold mb-0 text-dark">Rp {{ number_format($tollTotal, 0, ',', '.') }}</h4>
                        </div>
                    </div>
                    <i class="fa-solid fa-road-barrier text-primary stat-icon-bg"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabel Pengeluaran -->
    <div class="card card-premium">
        <div class="card-header bg-transparent border-bottom p-4 d-flex justify-content-between align-items-center">
            <h5 class="fw-bold mb-0"><i class="fa-solid fa-list-check me-2 text-orange"></i>Riwayat Pengeluaran</h5>
            
            <form action="{{ route('expenses.index') }}" method="GET" class="d-flex gap-2">
                <select name="category" class="form-select form-select-sm border-light-subtle shadow-sm rounded-pill px-3" onchange="this.form.submit()">
                    <option value="">Semua Kategori</option>
                    <option value="fuel" {{ request('category') == 'fuel' ? 'selected' : '' }}>Bensin</option>
                    <option value="toll" {{ request('category') == 'toll' ? 'selected' : '' }}>Tol</option>
                    <option value="parking" {{ request('category') == 'parking' ? 'selected' : '' }}>Parkir</option>
                    <option value="meal" {{ request('category') == 'meal' ? 'selected' : '' }}>Makan</option>
                    <option value="other" {{ request('category') == 'other' ? 'selected' : '' }}>Lainnya</option>
                </select>
                <select name="driver_id" class="form-select form-select-sm border-light-subtle shadow-sm rounded-pill px-3" onchange="this.form.submit()">
                    <option value="">Semua Driver</option>
                    @foreach($drivers as $driver)
                        <option value="{{ $driver->id }}" {{ request('driver_id') == $driver->id ? 'selected' : '' }}>{{ $driver->name }}</option>
                    @endforeach
                </select>
                <a href="{{ route('expenses.index') }}" class="btn btn-sm btn-light border shadow-sm rounded-pill px-3" title="Reset Filter"><i class="fa-solid fa-rotate-left"></i></a>
            </form>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 px-4">Tanggal / Waktu</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Driver & Shift</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Kategori</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Deskripsi</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Nominal</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Struk</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($expenses as $expense)
                            <tr>
                                <td class="px-4">
                                    <div class="fw-semibold text-dark">{{ $expense->occurred_at->format('d M Y') }}</div>
                                    <div class="text-muted small"><i class="fa-regular fa-clock me-1"></i>{{ $expense->occurred_at->format('H:i') }}</div>
                                </td>
                                <td>
                                    <div class="fw-bold text-dark"><i class="fa-solid fa-user-circle text-primary me-1"></i>{{ $expense->driver->name ?? 'N/A' }}</div>
                                    <div class="text-muted small">Shift: {{ $expense->shift->work_date ?? '-' }}</div>
                                </td>
                                <td>
                                    @php
                                        $catColor = match($expense->category) {
                                            'fuel' => 'danger',
                                            'toll' => 'primary',
                                            'parking' => 'warning',
                                            'meal' => 'success',
                                            default => 'secondary'
                                        };
                                        $catIcon = match($expense->category) {
                                            'fuel' => 'fa-gas-pump',
                                            'toll' => 'fa-road-barrier',
                                            'parking' => 'fa-square-parking',
                                            'meal' => 'fa-utensils',
                                            default => 'fa-money-bill'
                                        };
                                    @endphp
                                    <span class="badge bg-{{ $catColor }} bg-opacity-10 text-{{ $catColor }} border border-{{ $catColor }} border-opacity-25 px-2 py-1">
                                        <i class="fa-solid {{ $catIcon }} me-1"></i>{{ ucfirst($expense->category) }}
                                    </span>
                                </td>
                                <td>
                                    <div class="text-dark small" style="max-width: 200px; white-space: normal;">{{ $expense->description ?? '-' }}</div>
                                </td>
                                <td>
                                    <div class="fw-bold text-dark">Rp {{ number_format($expense->amount, 0, ',', '.') }}</div>
                                </td>
                                <td class="text-center">
                                    @if($expense->receipt_url)
                                        <a href="{{ $expense->receipt_url }}" target="_blank" class="btn btn-sm btn-light border shadow-sm rounded-circle p-2" title="Lihat Struk">
                                            <i class="fa-solid fa-image text-info"></i>
                                        </a>
                                    @else
                                        <span class="text-muted small">-</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <form action="{{ route('expenses.destroy', $expense->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pengeluaran ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-light text-danger border shadow-sm rounded-circle p-2" title="Hapus">
                                            <i class="fa-regular fa-trash-can"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <img src="https://cdn-icons-png.flaticon.com/512/7486/7486744.png" alt="No Data" width="100" class="mb-3 opacity-50">
                                    <h5 class="fw-bold text-muted">Belum Ada Pengeluaran</h5>
                                    <p class="text-muted">Data pengeluaran operasional akan muncul di sini.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@include('expenses.Partials.create-modal')
@endsection
