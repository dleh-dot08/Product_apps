<x-app-layout>
<style>
    .validasi-page {
        --val-primary: #3b82f6;
        --val-primary-dark: #2563eb;
        --val-success: #10b981;
        --val-success-dark: #059669;
        --val-bg: #f8fafc;
        --val-card: #ffffff;
        --val-text: #0f172a;
        --val-muted: #64748b;
        --val-border: #e2e8f0;
    }

    .validasi-page .card-container {
        background: var(--val-card);
        border-radius: 12px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.03);
        border: 1px solid var(--val-border);
        overflow: hidden;
    }

    .validasi-page .nav-tabs {
        border-bottom: 2px solid var(--val-border);
        padding: 0 20px;
        background: #fdfdfd;
    }

    .validasi-page .nav-tabs .nav-link {
        border: none;
        color: var(--val-muted);
        font-weight: 600;
        padding: 15px 20px;
        border-bottom: 3px solid transparent;
        transition: all 0.2s ease;
        font-size: 14px;
        letter-spacing: 0.3px;
    }

    .validasi-page .nav-tabs .nav-link:hover {
        color: var(--val-primary);
        background: transparent;
    }

    .validasi-page .nav-tabs .nav-link.active {
        color: var(--val-primary);
        border-bottom-color: var(--val-primary);
        background: transparent;
    }

    .validasi-page .table th {
        text-transform: uppercase;
        font-size: 11px;
        letter-spacing: 0.5px;
        font-weight: 700;
        color: var(--val-muted);
        background: #f8fafc;
        padding: 12px 16px;
        border-bottom: 2px solid var(--val-border);
    }

    .validasi-page .table td {
        vertical-align: middle;
        padding: 14px 16px;
        color: var(--val-text);
        font-size: 13px;
        font-weight: 500;
        border-bottom: 1px solid #f1f5f9;
    }

    .validasi-page .badge-valid {
        background: #ecfdf5;
        color: #059669;
        border: 1px solid #a7f3d0;
        padding: 5px 10px;
        border-radius: 6px;
        font-weight: 700;
        font-size: 10px;
    }

    .validasi-page .input-edit {
        border: 1px solid #cbd5e1;
        border-radius: 6px;
        padding: 6px 10px;
        font-size: 13px;
        width: 130px;
        font-weight: 600;
        color: var(--val-primary-dark);
        transition: border-color 0.2s, box-shadow 0.2s;
    }

    .validasi-page .input-edit:focus {
        border-color: var(--val-primary);
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15);
        outline: none;
    }

    .validasi-page .btn-save {
        background: linear-gradient(135deg, var(--val-success) 0%, var(--val-success-dark) 100%);
        color: white;
        border: none;
        border-radius: 6px;
        padding: 6px 14px;
        font-size: 12px;
        font-weight: 600;
        box-shadow: 0 3px 8px rgba(16, 185, 129, 0.2);
        transition: transform 0.2s, box-shadow 0.2s;
    }

    .validasi-page .btn-save:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 10px rgba(16, 185, 129, 0.3);
        color: white;
    }

    .validasi-page .btn-outline-primary {
        border-color: var(--val-primary);
        color: var(--val-primary);
        border-radius: 6px;
        padding: 6px 14px;
        font-size: 12px;
        font-weight: 600;
    }

    .validasi-page .input-group-text-custom {
        background: #f1f5f9;
        border: 1px solid #cbd5e1;
        border-right: none;
        font-size: 12px;
        font-weight: 700;
        color: #64748b;
        border-radius: 6px 0 0 6px;
    }

    /* Custom Pagination Styling */
    .validasi-page .custom-pagination .pagination {
        margin-bottom: 0;
        gap: 5px;
    }

    .validasi-page .custom-pagination .page-item .page-link {
        border-radius: 8px;
        border: 1px solid #e2e8f0;
        color: var(--val-muted);
        font-weight: 600;
        padding: 6px 12px;
        transition: all 0.2s ease;
        box-shadow: 0 1px 2px rgba(0,0,0,0.02);
    }

    .validasi-page .custom-pagination .page-item:not(.active) .page-link:hover {
        background: #f1f5f9;
        color: var(--val-primary);
        border-color: #cbd5e1;
    }

    .validasi-page .custom-pagination .page-item.active .page-link {
        background: var(--val-primary);
        border-color: var(--val-primary);
        color: white;
        box-shadow: 0 4px 10px rgba(59, 130, 246, 0.25);
    }

    .validasi-page .custom-pagination .page-item.disabled .page-link {
        background: #f8fafc;
        color: #cbd5e1;
        border-color: #e2e8f0;
        box-shadow: none;
    }
</style>

<div class="py-8 validasi-page">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-6 flex justify-between items-end">
            <div>
                <h2 class="text-2xl font-bold text-slate-800 leading-tight">
                    Data Validasi HPP
                </h2>
                <p class="text-sm text-slate-500 mt-1 font-medium">Lakukan pengecekan dan validasi parameter utama perhitungan HPP.</p>
            </div>
            <a href="{{ route('hpp.index') }}" class="btn btn-outline-secondary" style="border-radius: 8px; font-weight:600; font-size:13px;">
                <i class="fas fa-arrow-left mr-1"></i> Kembali ke HPP
            </a>
        </div>

        <div class="card-container">
            <!-- Tabs Header -->
            <ul class="nav nav-tabs" id="validasiTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="bbm-tab" data-bs-toggle="tab" data-bs-target="#bbm" type="button" role="tab" aria-controls="bbm" aria-selected="true">
                        <i class="fas fa-gas-pump mr-2"></i> Validasi Konsumsi BBM
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="manpower-tab" data-bs-toggle="tab" data-bs-target="#manpower" type="button" role="tab" aria-controls="manpower" aria-selected="false">
                        <i class="fas fa-users mr-2"></i> Validasi Rate Manpower
                    </button>
                </li>
            </ul>

            <!-- Tabs Content -->
            <div class="tab-content" id="validasiTabsContent">
                
                <!-- TAB 1: VALIDASI KONSUMSI BBM -->
                <div class="tab-pane fade show active" id="bbm" role="tabpanel" aria-labelledby="bbm-tab">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th class="text-center" width="5%">No</th>
                                    <th width="15%">Plat Nomor</th>
                                    <th width="20%">Mobil</th>
                                    <th width="20%">Harga BBM (Rp/Lt)</th>
                                    <th width="15%">Konsumsi (KM/Lt)</th>
                                    <th width="10%">Status</th>
                                    <th width="15%" class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($bbmValidations as $index => $vehicle)
                                    <tr>
                                        <form action="{{ route('hpp.validasi.update.bbm', $vehicle->id) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <td class="text-center">{{ $bbmValidations->firstItem() + $index }}</td>
                                            <td>
                                                <span class="badge bg-dark" style="font-size:11px; padding:6px 10px; border-radius:4px;">
                                                    {{ $vehicle->plate_number }}
                                                </span>
                                            </td>
                                            <td>{{ $vehicle->name }}</td>
                                            
                                            <!-- Editable Harga BBM -->
                                            <td>
                                                <div class="input-group input-group-sm" style="width: 150px;">
                                                    <span class="input-group-text input-group-text-custom">Rp</span>
                                                    <input type="number" class="form-control input-edit" style="border-radius: 0 6px 6px 0; width:100px;" value="{{ $vehicle->fuel_price_per_liter }}" step="1" name="fuel_price_per_liter" required>
                                                </div>
                                            </td>

                                            <!-- Editable KM per Liter -->
                                            <td>
                                                <div class="input-group input-group-sm" style="width: 120px;">
                                                    <input type="number" class="form-control input-edit" style="border-radius: 6px 0 0 6px; width:70px;" value="{{ $vehicle->km_per_liter }}" step="0.1" name="km_per_liter" required>
                                                    <span class="input-group-text" style="background:#f1f5f9; border-color:#cbd5e1; border-left:none; border-radius:0 6px 6px 0; font-size:11px; font-weight:700;">KM</span>
                                                </div>
                                            </td>
                                            
                                            <td>
                                                <span class="badge-valid"><i class="fas fa-check-circle mr-1"></i> Valid Sesuai</span>
                                            </td>
                                            <td class="text-center">
                                                <button type="submit" class="btn btn-save" title="Simpan Perubahan BBM">
                                                    <i class="fas fa-save mr-1"></i> Update
                                                </button>
                                            </td>
                                        </form>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-5">
                                            <i class="fas fa-inbox fa-3x mb-3 text-slate-300"></i>
                                            <p class="mb-0">Belum ada data kendaraan.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="p-4 border-t border-slate-100 d-flex justify-content-end custom-pagination">
                        {{ $bbmValidations->appends(request()->except('bbm_page'))->links('pagination::bootstrap-5') }}
                    </div>
                </div>

                <!-- TAB 2: VALIDASI RATE MANPOWER -->
                <div class="tab-pane fade" id="manpower" role="tabpanel" aria-labelledby="manpower-tab">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th class="text-center" width="5%">No</th>
                                    <th width="20%">Konfigurasi</th>
                                    <th width="20%">Keterangan</th>
                                    <th width="15%">Rate Per Jam</th>
                                    <th width="15%">Rate Per Menit</th>
                                    <th width="15%">Rate Per Detik</th>
                                    <th width="10%">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($manpowerValidations as $index => $validasi)
                                    @php
                                        $rateJam = $validasi->rate_per_hour ?? 0;
                                        $rateMenit = $validasi->rate_per_minute ?? 0;
                                        $rateDetik = $validasi->rate_per_second ?? 0;
                                        $statusVal = $validasi->status_validasi ?? 'Pending';
                                        $configName = $validasi->name ?? 'Rate MP Driver';
                                    @endphp
                                    <tr>
                                        <form action="{{ route('hpp.validasi.update.manpower', $validasi->id) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <td class="text-center">{{ $manpowerValidations->firstItem() + $index }}</td>
                                            <td>
                                                <div class="d-flex align-items-center gap-2">
                                                    <div style="width:28px; height:28px; border-radius:50%; background:#e2e8f0; display:flex; align-items:center; justify-content:center; color:#475569; font-weight:bold; font-size:12px;">
                                                        <i class="fas fa-users"></i>
                                                    </div>
                                                    <span class="font-bold text-slate-800">{{ $configName }}</span>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-light text-dark border">Global</span>
                                            </td>
                                            <td>
                                                <div class="input-group input-group-sm" style="width: 140px;">
                                                    <span class="input-group-text bg-light text-slate-500 border-end-0">Rp</span>
                                                    <input type="number" name="rate_per_hour" class="form-control border-start-0" value="{{ round($rateJam) }}" required>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="input-group input-group-sm" style="width: 140px;">
                                                    <span class="input-group-text bg-light text-slate-500 border-end-0">Rp</span>
                                                    <input type="number" name="rate_per_minute" class="form-control border-start-0" value="{{ round($rateMenit) }}" required>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="input-group input-group-sm" style="width: 140px;">
                                                    <span class="input-group-text bg-light text-slate-500 border-end-0">Rp</span>
                                                    <input type="number" name="rate_per_second" class="form-control border-start-0" value="{{ round($rateDetik) }}" required>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex flex-column gap-1">
                                                    <span class="badge-valid mb-1"><i class="fas fa-check-circle mr-1"></i> {{ $statusVal }}</span>
                                                    <button type="submit" class="btn btn-primary btn-sm rounded-pill" style="font-size: 11px;">
                                                        <i class="fas fa-save mr-1"></i> Update
                                                    </button>
                                                </div>
                                            </td>
                                        </form>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-5">
                                            <i class="fas fa-users-slash fa-3x mb-3 text-slate-300"></i>
                                            <p class="mb-0">Belum ada data pegawai yang memiliki rate manpower.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="p-4 border-t border-slate-100 d-flex justify-content-end custom-pagination">
                        {{ $manpowerValidations->appends(request()->except('manpower_page'))->links('pagination::bootstrap-5') }}
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
</x-app-layout>
