@extends('layouts.app')
@section('title', 'Detail Laporan Driver')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0 fw-bold" style="color: var(--bs-primary);">Detail Laporan: {{ $task->reference_number }}</h4>
        <a href="{{ route('driver-reports.index') }}" class="btn btn-outline-secondary rounded-pill">
            <i class="fa-solid fa-arrow-left me-1"></i> Kembali
        </a>
    </div>

    <div class="row g-4">
        <!-- 1. Informasi Umum -->
        <div class="col-md-12">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white border-bottom-0 pt-4 pb-0 px-4">
                    <h5 class="fw-bold"><i class="fa-solid fa-circle-info text-primary me-2"></i>Informasi Umum</h5>
                </div>
                <div class="card-body px-4 pb-4">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <span class="text-muted d-block" style="font-size: 0.85rem;">Tipe Tugas</span>
                            <strong class="text-uppercase">{{ $task->task_type }}</strong>
                        </div>
                        <div class="col-md-3 mb-3">
                            <span class="text-muted d-block" style="font-size: 0.85rem;">Driver</span>
                            <strong>{{ $task->driver->name ?? '-' }}</strong>
                        </div>
                        <div class="col-md-3 mb-3">
                            <span class="text-muted d-block" style="font-size: 0.85rem;">Kendaraan</span>
                            <strong>{{ $task->vehicle->plate_number ?? '-' }} ({{ $task->vehicle->name ?? '' }})</strong>
                        </div>
                        <div class="col-md-3 mb-3">
                            <span class="text-muted d-block" style="font-size: 0.85rem;">Status</span>
                            <strong>{{ strtoupper($task->status) }}</strong>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <span class="text-muted d-block" style="font-size: 0.85rem;">Asal (Pickup)</span>
                            <strong>{{ $task->pickup_name ?? '-' }} - {{ $task->pickup_location ?? '-' }}</strong>
                        </div>
                        <div class="col-md-6 mb-3">
                            <span class="text-muted d-block" style="font-size: 0.85rem;">Tujuan</span>
                            <strong>{{ $task->destination ?? '-' }}</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. Laporan Keberangkatan -->
        <div class="col-md-12">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white border-bottom-0 pt-4 pb-0 px-4">
                    <h5 class="fw-bold"><i class="fa-solid fa-truck-fast text-warning me-2"></i>Laporan Keberangkatan</h5>
                </div>
                <div class="card-body px-4 pb-4">
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <span class="text-muted d-block" style="font-size: 0.85rem;">Odometer Awal</span>
                            <strong>{{ $task->start_odometer ?? '-' }} KM</strong>
                        </div>
                        <div class="col-md-4">
                            <span class="text-muted d-block" style="font-size: 0.85rem;">BBM Awal</span>
                            <strong>{{ $task->start_fuel ?? '-' }}</strong>
                        </div>
                        <div class="col-md-4">
                            <span class="text-muted d-block" style="font-size: 0.85rem;">Waktu Berangkat</span>
                            <strong>{{ $task->started_at ? \Carbon\Carbon::parse($task->started_at)->format('d M Y H:i') : '-' }}</strong>
                        </div>
                        <div class="col-md-12 mt-3">
                            <span class="text-muted d-block" style="font-size: 0.85rem;">Catatan</span>
                            <p class="mb-0">{{ $task->departure_notes ?? '-' }}</p>
                        </div>
                    </div>

                    {{-- Checklist Keberangkatan --}}
                    @php
                        $checklist = $task->departure_checklist;
                        if (is_string($checklist)) $checklist = json_decode($checklist, true);
                    @endphp
                    @if(!empty($checklist))
                    <h6 class="fw-bold mb-3"><i class="fa-solid fa-clipboard-check text-primary me-2"></i>Checklist Keberangkatan:</h6>
                    <div class="row g-2 mb-4">
                        @foreach($checklist as $label => $status)
                        <div class="col-md-4">
                            <div class="d-flex align-items-center gap-2 border rounded-3 p-2" style="background: #f8fafc;">
                                @if($status === 'check')
                                    <i class="fa-solid fa-circle-check" style="color:#10b981; font-size:18px;"></i>
                                    <span style="font-size:0.85rem; font-weight:600;">{{ $label }} — <span style="color:#10b981;">OK</span></span>
                                @elseif($status === 'cross')
                                    <i class="fa-solid fa-circle-xmark" style="color:#ef4444; font-size:18px;"></i>
                                    <span style="font-size:0.85rem; font-weight:600;">{{ $label }} — <span style="color:#ef4444;">Rusak</span></span>
                                @elseif($status === 'warning')
                                    <i class="fa-solid fa-triangle-exclamation" style="color:#f59e0b; font-size:18px;"></i>
                                    <span style="font-size:0.85rem; font-weight:600;">{{ $label }} — <span style="color:#f59e0b;">Perlu Perhatian</span></span>
                                @else
                                    <i class="fa-regular fa-square" style="color:#94a3b8; font-size:18px;"></i>
                                    <span style="font-size:0.85rem; font-weight:600; color:#94a3b8;">{{ $label }} — Belum Diisi</span>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @endif

                    @php
                        $allDeparturePhotos = [];
                        $depCats = [
                            'keberangkatan_depan' => 'Foto Depan', 
                            'keberangkatan_muatan' => 'Foto Muatan', 
                            'keberangkatan_surat' => 'Foto Surat'
                        ];
                        foreach($depCats as $key => $label) {
                            if(isset($attachments[$key]) && count($attachments[$key]) > 0) {
                                $att = $attachments[$key][0];
                                $att->display_label = $label;
                                $allDeparturePhotos[] = $att;
                            }
                        }
                        if(isset($attachments['bukti_keberangkatan'])) {
                            foreach($attachments['bukti_keberangkatan'] as $idx => $att) {
                                $att->display_label = 'Bukti ' . ($idx + 1);
                                $allDeparturePhotos[] = $att;
                            }
                        }
                        $totalPhotos = count($allDeparturePhotos);
                    @endphp

                    <h6 class="fw-bold mt-4 mb-3"><i class="fa-solid fa-images text-info me-2"></i>Bukti Keberangkatan ({{ $totalPhotos }} file):</h6>
                    
                    @if($totalPhotos > 0)
                        <div class="row g-3">
                            @foreach(array_slice($allDeparturePhotos, 0, 4) as $idx => $att)
                                @php
                                    $isLast = $idx === 3 && $totalPhotos > 4;
                                    $remaining = $totalPhotos - 4;
                                    $url = asset('storage/' . $att->file_path);
                                    $isPdf = str_ends_with(strtolower($att->file_path), '.pdf');
                                @endphp
                                <div class="col-md-3 col-6">
                                    <div class="border rounded p-2 text-center h-100 position-relative" style="overflow:hidden;">
                                        <span class="d-block mb-2 text-muted" style="font-size: 0.8rem;">{{ $att->display_label }}</span>
                                        <a href="#" data-bs-toggle="modal" data-bs-target="#modalBuktiKeberangkatan" class="d-block position-relative">
                                            @if($isPdf)
                                                <div class="d-flex align-items-center justify-content-center bg-light rounded" style="height: 150px;">
                                                    <i class="fa-solid fa-file-pdf" style="font-size:36px; color:#ef4444;"></i>
                                                </div>
                                            @else
                                                <img src="{{ $url }}" class="img-fluid rounded" alt="{{ $att->display_label }}" style="height: 150px; width: 100%; object-fit: cover;">
                                            @endif
                                            
                                            @if($isLast)
                                                <div class="position-absolute top-0 start-0 w-100 h-100 rounded d-flex align-items-center justify-content-center" style="background: rgba(0,0,0,0.6);">
                                                    <span class="text-white fw-bold" style="font-size: 28px;">+{{ $remaining }}</span>
                                                </div>
                                            @endif
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="bg-light rounded d-flex align-items-center justify-content-center" style="height: 100px;">
                            <span class="text-muted">Tidak ada foto bukti keberangkatan</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- 3. Laporan Tiba -->
        <div class="col-md-12">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white border-bottom-0 pt-4 pb-0 px-4">
                    <h5 class="fw-bold"><i class="fa-solid fa-location-dot text-primary me-2"></i>Laporan Tiba di Tujuan</h5>
                </div>
                <div class="card-body px-4 pb-4">
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <span class="text-muted d-block" style="font-size: 0.85rem;">Waktu Estimasi Kedatangan</span>
                            <strong>{{ $task->estimated_arrival ? \Carbon\Carbon::parse($task->estimated_arrival)->format('d M Y H:i') : '-' }}</strong>
                        </div>
                        <div class="col-md-4">
                            <span class="text-muted d-block" style="font-size: 0.85rem;">Waktu Tiba Aktual</span>
                            <strong class="{{ $task->arrived_at ? 'text-success' : '' }}">{{ $task->arrived_at ? \Carbon\Carbon::parse($task->arrived_at)->format('d M Y H:i') : '-' }}</strong>
                        </div>
                        <div class="col-md-12 mt-3">
                            <span class="text-muted d-block" style="font-size: 0.85rem;">Catatan Tiba</span>
                            <p class="mb-0">{{ $task->arrival_notes ?? '-' }}</p>
                        </div>
                    </div>

                    {{-- Checklist Tiba --}}
                    @php
                        $arrChecklist = $task->arrival_checklist;
                        if (is_string($arrChecklist)) $arrChecklist = json_decode($arrChecklist, true);
                    @endphp
                    @if(!empty($arrChecklist))
                    <h6 class="fw-bold mb-3"><i class="fa-solid fa-clipboard-check text-primary me-2"></i>Checklist Kedatangan:</h6>
                    <div class="row g-2 mb-4">
                        @foreach($arrChecklist as $label => $status)
                        <div class="col-md-4">
                            <div class="d-flex align-items-center gap-2 border rounded-3 p-2" style="background: #f8fafc;">
                                @if($status === 'check')
                                    <i class="fa-solid fa-circle-check" style="color:#10b981; font-size:18px;"></i>
                                    <span style="font-size:0.85rem; font-weight:600;">{{ $label }} — <span style="color:#10b981;">OK</span></span>
                                @elseif($status === 'cross')
                                    <i class="fa-solid fa-circle-xmark" style="color:#ef4444; font-size:18px;"></i>
                                    <span style="font-size:0.85rem; font-weight:600;">{{ $label }} — <span style="color:#ef4444;">Error/Masalah</span></span>
                                @elseif($status === 'warning')
                                    <i class="fa-solid fa-triangle-exclamation" style="color:#f59e0b; font-size:18px;"></i>
                                    <span style="font-size:0.85rem; font-weight:600;">{{ $label }} — <span style="color:#f59e0b;">Perlu Perhatian</span></span>
                                @else
                                    <i class="fa-regular fa-square" style="color:#94a3b8; font-size:18px;"></i>
                                    <span style="font-size:0.85rem; font-weight:600; color:#94a3b8;">{{ $label }} — Belum Diisi</span>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @endif

                    @php
                        $allArrivalPhotos = [];
                        $arrCats = [
                            'tiba_lokasi' => 'Foto Lokasi', 
                            'tiba_gudang' => 'Foto Gudang'
                        ];
                        foreach($arrCats as $key => $label) {
                            if(isset($attachments[$key]) && count($attachments[$key]) > 0) {
                                $att = $attachments[$key][0];
                                $att->display_label = $label;
                                $allArrivalPhotos[] = $att;
                            }
                        }
                        if(isset($attachments['bukti_kedatangan'])) {
                            foreach($attachments['bukti_kedatangan'] as $idx => $att) {
                                $att->display_label = 'Bukti Tiba ' . ($idx + 1);
                                $allArrivalPhotos[] = $att;
                            }
                        }
                        $totalArrPhotos = count($allArrivalPhotos);
                    @endphp

                    <h6 class="fw-bold mt-4 mb-3"><i class="fa-solid fa-images text-info me-2"></i>Bukti Kedatangan ({{ $totalArrPhotos }} file):</h6>
                    
                    @if($totalArrPhotos > 0)
                        <div class="row g-3">
                            @foreach(array_slice($allArrivalPhotos, 0, 4) as $idx => $att)
                                @php
                                    $isLast = $idx === 3 && $totalArrPhotos > 4;
                                    $remaining = $totalArrPhotos - 4;
                                    $url = asset('storage/' . $att->file_path);
                                    $isPdf = str_ends_with(strtolower($att->file_path), '.pdf');
                                @endphp
                                <div class="col-md-3 col-6">
                                    <div class="border rounded p-2 text-center h-100 position-relative" style="overflow:hidden;">
                                        <span class="d-block mb-2 text-muted" style="font-size: 0.8rem;">{{ $att->display_label }}</span>
                                        <a href="#" data-bs-toggle="modal" data-bs-target="#modalBuktiKedatangan" class="d-block position-relative">
                                            @if($isPdf)
                                                <div class="d-flex align-items-center justify-content-center bg-light rounded" style="height: 150px;">
                                                    <i class="fa-solid fa-file-pdf" style="font-size:36px; color:#ef4444;"></i>
                                                </div>
                                            @else
                                                <img src="{{ $url }}" class="img-fluid rounded" alt="{{ $att->display_label }}" style="height: 150px; width: 100%; object-fit: cover;">
                                            @endif
                                            
                                            @if($isLast)
                                                <div class="position-absolute top-0 start-0 w-100 h-100 rounded d-flex align-items-center justify-content-center" style="background: rgba(0,0,0,0.6);">
                                                    <span class="text-white fw-bold" style="font-size: 28px;">+{{ $remaining }}</span>
                                                </div>
                                            @endif
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="bg-light rounded d-flex align-items-center justify-content-center" style="height: 100px;">
                            <span class="text-muted">Tidak ada foto bukti kedatangan</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- 4. Laporan Serah Terima -->
        <div class="col-md-12">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white border-bottom-0 pt-4 pb-0 px-4">
                    <h5 class="fw-bold"><i class="fa-solid fa-handshake text-success me-2"></i>Laporan Serah Terima</h5>
                </div>
                <div class="card-body px-4 pb-4">
                    @if($task->status == 'failed')
                        <div class="alert alert-danger">
                            <strong>Pengiriman Gagal!</strong> Alasan: {{ $task->failure_reason }}
                        </div>
                    @endif

                    <div class="row mb-4">
                        <div class="col-md-4">
                            <span class="text-muted d-block" style="font-size: 0.85rem;">Nama Penerima</span>
                            <strong>{{ $task->receiver_name ?? '-' }}</strong>
                        </div>
                        <div class="col-md-4">
                            <span class="text-muted d-block" style="font-size: 0.85rem;">Jabatan / PIC</span>
                            <strong>{{ $task->receiver_role ?? '-' }}</strong>
                        </div>
                        <div class="col-md-4">
                            <span class="text-muted d-block" style="font-size: 0.85rem;">Kondisi Barang</span>
                            <strong>{{ $task->item_condition ?? '-' }}</strong>
                        </div>
                        <div class="col-md-4 mt-3">
                            <span class="text-muted d-block" style="font-size: 0.85rem;">Odometer Selesai</span>
                            <strong>{{ $task->completed_odometer ?? '-' }} KM</strong>
                        </div>
                        <div class="col-md-4 mt-3">
                            <span class="text-muted d-block" style="font-size: 0.85rem;">Waktu Selesai</span>
                            <strong>{{ $task->completed_at ? \Carbon\Carbon::parse($task->completed_at)->format('d M Y H:i') : '-' }}</strong>
                        </div>
                    </div>

                    @php
                        $allHandPhotos = [];
                        $handCats = [
                            'serah_terima_barang' => 'Foto Barang', 
                            'serah_terima_penerima' => 'Foto Penerima', 
                            'serah_terima_surat' => 'Foto Surat', 
                            'serah_terima_ttd' => 'Tanda Tangan'
                        ];
                        foreach($handCats as $key => $label) {
                            if(isset($attachments[$key]) && count($attachments[$key]) > 0) {
                                $att = $attachments[$key][0];
                                $att->display_label = $label;
                                $allHandPhotos[] = $att;
                            }
                        }
                        if(isset($attachments['bukti_serah_terima'])) {
                            foreach($attachments['bukti_serah_terima'] as $idx => $att) {
                                $att->display_label = 'Bukti ' . ($idx + 1);
                                $allHandPhotos[] = $att;
                            }
                        }
                        
                        // Fallback backward compatibility proof_photo
                        if($task->proof_photo && !isset($attachments['serah_terima_surat'])) {
                            $allHandPhotos[] = (object)[
                                'file_path' => $task->proof_photo,
                                'display_label' => 'Proof (Lama)'
                            ];
                        }
                        
                        $totalHandPhotos = count($allHandPhotos);
                    @endphp

                    <h6 class="fw-bold mt-4 mb-3"><i class="fa-solid fa-images text-info me-2"></i>Bukti Serah Terima ({{ $totalHandPhotos }} file):</h6>
                    
                    @if($totalHandPhotos > 0)
                        <div class="row g-3">
                            @foreach(array_slice($allHandPhotos, 0, 4) as $idx => $att)
                                @php
                                    $isLast = $idx === 3 && $totalHandPhotos > 4;
                                    $remaining = $totalHandPhotos - 4;
                                    $url = asset('storage/' . $att->file_path);
                                    $isPdf = str_ends_with(strtolower($att->file_path), '.pdf');
                                @endphp
                                <div class="col-md-3 col-6">
                                    <div class="border rounded p-2 text-center h-100 position-relative" style="overflow:hidden;">
                                        <span class="d-block mb-2 text-muted" style="font-size: 0.8rem;">{{ $att->display_label }}</span>
                                        <a href="#" data-bs-toggle="modal" data-bs-target="#modalBuktiSerahTerima" class="d-block position-relative">
                                            @if($isPdf)
                                                <div class="d-flex align-items-center justify-content-center bg-light rounded" style="height: 150px;">
                                                    <i class="fa-solid fa-file-pdf" style="font-size:36px; color:#ef4444;"></i>
                                                </div>
                                            @else
                                                <img src="{{ $url }}" class="img-fluid rounded" alt="{{ $att->display_label }}" style="height: 150px; width: 100%; object-fit: cover;">
                                            @endif
                                            
                                            @if($isLast)
                                                <div class="position-absolute top-0 start-0 w-100 h-100 rounded d-flex align-items-center justify-content-center" style="background: rgba(0,0,0,0.6);">
                                                    <span class="text-white fw-bold" style="font-size: 28px;">+{{ $remaining }}</span>
                                                </div>
                                            @endif
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="bg-light rounded d-flex align-items-center justify-content-center" style="height: 100px;">
                            <span class="text-muted">Tidak ada foto bukti serah terima</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- 5. Laporan Pengeluaran Operasional (Dari tabel Expenses) -->
        <div class="col-md-12">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white border-bottom-0 pt-4 pb-0 px-4">
                    <h5 class="fw-bold"><i class="fa-solid fa-money-bill-wave text-info me-2"></i>Laporan Pengeluaran Operasional</h5>
                </div>
                <div class="card-body px-4 pb-4">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>Kategori</th>
                                    <th>Nominal</th>
                                    <th>Keterangan</th>
                                    <th>Waktu</th>
                                    <th>Lampiran (Struk)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($expenses as $expense)
                                <tr>
                                    <td>{{ $expense->category }}</td>
                                    <td>Rp {{ number_format($expense->amount, 0, ',', '.') }}</td>
                                    <td>{{ $expense->description ?? '-' }}</td>
                                    <td>{{ \Carbon\Carbon::parse($expense->occurred_at)->format('d M Y H:i') }}</td>
                                    <td>
                                        @if($expense->attachment_path)
                                            <a href="{{ asset('storage/' . $expense->attachment_path) }}" target="_blank" class="btn btn-sm btn-outline-info">Lihat Struk</a>
                                        @else
                                            -
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted">Tidak ada catatan pengeluaran pada shift tugas ini.</td>
                                </tr>
                                @endforelse
                                @if($expenses->count() > 0)
                                <tr class="table-light">
                                    <th class="text-end">Total Pengeluaran:</th>
                                    <th colspan="4">Rp {{ number_format($expenses->sum('amount'), 0, ',', '.') }}</th>
                                </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

{{-- Modal to show all photos --}}
@if(isset($totalPhotos) && $totalPhotos > 0)
<div class="modal fade" id="modalBuktiKeberangkatan" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content border-0 shadow-lg rounded-4">
      <div class="modal-header border-bottom-0 pb-0 pt-4 px-4">
        <h5 class="modal-title fw-bold"><i class="fa-solid fa-images text-info me-2"></i>Semua Bukti Keberangkatan</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-4">
        <div class="row g-3">
            @foreach($allDeparturePhotos as $idx => $att)
            @php
                $url = asset('storage/' . $att->file_path);
                $isPdf = str_ends_with(strtolower($att->file_path), '.pdf');
            @endphp
            <div class="col-md-4 col-6">
                <div class="border rounded p-2 text-center h-100">
                    <span class="d-block mb-2 text-muted fw-bold" style="font-size: 0.8rem; text-transform:uppercase;">{{ $att->display_label }}</span>
                    @if($isPdf)
                        <a href="{{ $url }}" target="_blank" class="d-flex align-items-center justify-content-center bg-light rounded" style="height: 150px;">
                            <i class="fa-solid fa-file-pdf" style="font-size:36px; color:#ef4444;"></i>
                        </a>
                    @else
                        <a href="{{ $url }}" target="_blank">
                            <img src="{{ $url }}" class="img-fluid rounded" alt="{{ $att->display_label }}" style="height: 150px; width: 100%; object-fit: cover;">
                        </a>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
      </div>
    </div>
  </div>
</div>
@endif

{{-- Modal to show all arrival photos --}}
@if(isset($totalArrPhotos) && $totalArrPhotos > 0)
<div class="modal fade" id="modalBuktiKedatangan" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content border-0 shadow-lg rounded-4">
      <div class="modal-header border-bottom-0 pb-0 pt-4 px-4">
        <h5 class="modal-title fw-bold"><i class="fa-solid fa-images text-info me-2"></i>Semua Bukti Kedatangan</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-4">
        <div class="row g-3">
            @foreach($allArrivalPhotos as $idx => $att)
            @php
                $url = asset('storage/' . $att->file_path);
                $isPdf = str_ends_with(strtolower($att->file_path), '.pdf');
            @endphp
            <div class="col-md-4 col-6">
                <div class="border rounded p-2 text-center h-100">
                    <span class="d-block mb-2 text-muted fw-bold" style="font-size: 0.8rem; text-transform:uppercase;">{{ $att->display_label }}</span>
                    @if($isPdf)
                        <a href="{{ $url }}" target="_blank" class="d-flex align-items-center justify-content-center bg-light rounded" style="height: 150px;">
                            <i class="fa-solid fa-file-pdf" style="font-size:36px; color:#ef4444;"></i>
                        </a>
                    @else
                        <a href="{{ $url }}" target="_blank">
                            <img src="{{ $url }}" class="img-fluid rounded" alt="{{ $att->display_label }}" style="height: 150px; width: 100%; object-fit: cover;">
                        </a>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
      </div>
    </div>
  </div>
</div>
@endif

{{-- Modal to show all hand photos --}}
@if(isset($totalHandPhotos) && $totalHandPhotos > 0)
<div class="modal fade" id="modalBuktiSerahTerima" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content border-0 shadow-lg rounded-4">
      <div class="modal-header border-bottom-0 pb-0 pt-4 px-4">
        <h5 class="modal-title fw-bold"><i class="fa-solid fa-images text-info me-2"></i>Semua Bukti Serah Terima</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-4">
        <div class="row g-3">
            @foreach($allHandPhotos as $idx => $att)
            @php
                $url = asset('storage/' . $att->file_path);
                $isPdf = str_ends_with(strtolower($att->file_path), '.pdf');
            @endphp
            <div class="col-md-4 col-6">
                <div class="border rounded p-2 text-center h-100">
                    <span class="d-block mb-2 text-muted fw-bold" style="font-size: 0.8rem; text-transform:uppercase;">{{ $att->display_label }}</span>
                    @if($isPdf)
                        <a href="{{ $url }}" target="_blank" class="d-flex align-items-center justify-content-center bg-light rounded" style="height: 150px;">
                            <i class="fa-solid fa-file-pdf" style="font-size:36px; color:#ef4444;"></i>
                        </a>
                    @else
                        <a href="{{ $url }}" target="_blank">
                            <img src="{{ $url }}" class="img-fluid rounded" alt="{{ $att->display_label }}" style="height: 150px; width: 100%; object-fit: cover;">
                        </a>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
      </div>
    </div>
  </div>
</div>
@endif

@endsection
