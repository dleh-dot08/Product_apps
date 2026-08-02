            <style>
                .nav-pills.custom-tabs .nav-link {
                    color: #64748b;
                    font-weight: 600;
                    border: 1px solid transparent;
                    transition: all 0.2s ease;
                }
                .nav-pills.custom-tabs .nav-link:hover {
                    color: #0f172a;
                    background-color: #f1f5f9;
                }
                .nav-pills.custom-tabs .nav-link.active {
                    background-color: #0ea5e9;
                    color: white;
                    box-shadow: 0 4px 6px -1px rgba(14, 165, 233, 0.3), 0 2px 4px -2px rgba(14, 165, 233, 0.3);
                }
                .tab-content {
                    height: 65vh;
                }
                .tab-pane {
                    height: 100%;
                    flex-direction: column;
                }
                .tab-pane.active {
                    display: flex !important;
                }
                .table-responsive {
                    flex: 1;
                    overflow-y: auto;
                    min-height: 0; /* Ensures flex child can shrink */
                }
                .tab-footer {
                    flex-shrink: 0;
                    position: sticky;
                    bottom: 0;
                    z-index: 10;
                }
                .table-premium {
                    width: 100%;
                    border-collapse: separate;
                    border-spacing: 0;
                    min-width: 1200px;
                }
                .table-premium thead th {
                    position: sticky;
                    top: 0;
                    z-index: 10;
                    white-space: nowrap;
                    background-color: #f8fafc;
                    color: #475569;
                    font-size: 11px;
                    font-weight: 700;
                    text-transform: uppercase;
                    letter-spacing: 0.05em;
                    padding: 12px 16px;
                    border-bottom: 2px solid #e2e8f0;
                    border-top: none;
                }
                .table-premium tbody td {
                    white-space: nowrap;
                    padding: 14px 16px;
                    font-size: 13px;
                    color: #334155;
                    border-bottom: 1px solid #f1f5f9;
                    vertical-align: middle;
                }
                .table-premium tbody tr {
                    transition: all 0.2s ease;
                }
                .table-premium tbody tr:hover {
                    background-color: #f8fafc;
                }
                .badge-premium {
                    font-size: 11px;
                    font-weight: 600;
                    padding: 4px 10px;
                    border-radius: 6px;
                    background-color: #f1f5f9;
                    color: #475569;
                    border: 1px solid #e2e8f0;
                }
                .card-summary-premium {
                    background: linear-gradient(145deg, #ffffff, #f8fafc);
                    border: 1px solid #e2e8f0;
                    border-radius: 16px;
                    box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.02), 0 8px 10px -6px rgba(0, 0, 0, 0.01);
                }
            </style>

            <div class="col-12">
                <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
                    <div class="card-header bg-white px-4 py-3 border-bottom d-flex flex-wrap align-items-center justify-content-between gap-3">
                        <h5 class="fw-bold text-navy mb-0 d-flex align-items-center gap-2">
                            <span class="material-symbols-rounded text-primary fs-4" style="background: #eff6ff; padding: 6px; border-radius: 8px;">view_module</span>
                            Detail Material
                        </h5>
                        
                        <!-- Navigation Tabs -->
                        <ul class="nav nav-pills custom-tabs gap-2" id="materialTab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active rounded-pill px-4 py-1.5" style="font-size: 13px;" id="tab-resume" data-bs-toggle="tab" data-bs-target="#content-resume" type="button" role="tab">Resume</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link rounded-pill px-4 py-1.5" style="font-size: 13px;" id="tab-all" data-bs-toggle="tab" data-bs-target="#content-all" type="button" role="tab">All</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link rounded-pill px-4 py-1.5" style="font-size: 13px;" id="tab-penyangga" data-bs-toggle="tab" data-bs-target="#content-penyangga" type="button" role="tab">Penyangga</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link rounded-pill px-4 py-1.5" style="font-size: 13px;" id="tab-penutup" data-bs-toggle="tab" data-bs-target="#content-penutup" type="button" role="tab">Tutup</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link rounded-pill px-4 py-1.5" style="font-size: 13px;" id="tab-bawah" data-bs-toggle="tab" data-bs-target="#content-bawah" type="button" role="tab">Bawah</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link rounded-pill px-4 py-1.5" style="font-size: 13px;" id="tab-manpower" data-bs-toggle="tab" data-bs-target="#content-manpower" type="button" role="tab">Manpower</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link rounded-pill px-4 py-1.5" style="font-size: 13px;" id="tab-paku" data-bs-toggle="tab" data-bs-target="#content-paku" type="button" role="tab">Paku</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link rounded-pill px-4 py-1.5" style="font-size: 13px;" id="tab-waktu-proses" data-bs-toggle="tab" data-bs-target="#content-waktu-proses" type="button" role="tab">Waktu Proses</button>
                            </li>
                        </ul>
                    </div>

                    <div class="card-body p-0">
                        <div class="tab-content" id="materialTabContent">
                            
                            <!-- Tab: RESUME -->
                            <div class="tab-pane fade show active" id="content-resume" role="tabpanel">
                                <div class="table-responsive h-100">
                                    <table class="table table-hover align-middle mb-0 custom-table" style="font-size: 13px;">
                                        <thead class="bg-slate-50 sticky-top">
                                            <tr>
                                                <th class="fw-semibold text-slate-600 border-bottom border-slate-200 py-3">Kode Material</th>
                                                <th class="fw-semibold text-slate-600 border-bottom border-slate-200 py-3">Kebutuhan Potong</th>
                                                <th class="fw-semibold text-slate-600 border-bottom border-slate-200 py-3 text-end">Tebal (mm)</th>
                                                <th class="fw-semibold text-slate-600 border-bottom border-slate-200 py-3 text-end">Lebar (mm)</th>
                                                <th class="fw-semibold text-slate-600 border-bottom border-slate-200 py-3 text-end">Panjang (mm)</th>
                                                <th class="fw-semibold text-slate-600 border-bottom border-slate-200 py-3 text-center">Jumlah & UoM</th>
                                            </tr>
                                        </thead>
                                        <tbody id="resume-material-tbody">
                                            @php
                                                $groupedDetails = collect(isset($calculation) ? $calculation->details : [])
                                                    ->groupBy(function($item) {
                                                        return ($item->material?->kode ?? '-') . '|' . 
                                                               (float)$item->calculated_thickness . '|' . 
                                                               (float)$item->calculated_width . '|' . 
                                                               (float)$item->calculated_length;
                                                    })
                                                    ->map(function($group) {
                                                        $first = $group->first();
                                                        $totalQty = $group->sum('total_quantity');
                                                        $partNames = $group->pluck('part_name')->unique()->implode(', ');
                                                        return [
                                                            'material_kode' => $first->material?->kode ?? '-',
                                                            'part_name' => $partNames,
                                                            'calculated_thickness' => (float)$first->calculated_thickness,
                                                            'calculated_width' => (float)$first->calculated_width,
                                                            'calculated_length' => (float)$first->calculated_length,
                                                            'total_quantity' => $totalQty,
                                                            'satuan_harga' => $first->material?->satuan_harga ?? 'pcs'
                                                        ];
                                                    });
                                            @endphp
                                            @forelse($groupedDetails as $detail)
                                            <tr>
                                                <td class="fw-bold text-navy">{{ $detail['material_kode'] }}</td>
                                                <td>{{ $detail['part_name'] }}</td>
                                                <td class="text-end">{{ $detail['calculated_thickness'] }}</td>
                                                <td class="text-end">{{ $detail['calculated_width'] }}</td>
                                                <td class="text-end">{{ $detail['calculated_length'] }}</td>
                                                <td class="text-center fw-bold" style="color: #0ea5e9;">{{ $detail['total_quantity'] }} <span class="fw-normal text-muted">{{ strtolower($detail['satuan_harga']) === 'sqm' ? 'Sqm' : 'Pcs' }}</span></td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="6" class="text-center py-5">
                                                    <div class="d-flex flex-column align-items-center justify-content-center text-muted">
                                                        <span class="material-symbols-rounded mb-2 text-slate-300" style="font-size: 40px;">assignment_late</span>
                                                        <h6 class="fw-semibold text-slate-600 mb-1">Belum ada data</h6>
                                                    </div>
                                                </td>
                                            </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            
                            <!-- Tab: ALL -->
                            <div class="tab-pane fade" id="content-all" role="tabpanel">
                                <div class="table-responsive h-100">
                                    <table class="table-premium w-100 mb-0">
                                        <thead>
                                            <tr>
                                                <th>Kategori</th>
                                                <th>Bagian</th>
                                                <th>Arah</th>
                                                <th>Material</th>
                                                <th class="text-end">Tebal (mm)</th>
                                                <th class="text-end">Lebar (mm)</th>
                                                <th class="text-end">Panjang (mm)</th>
                                                <th class="text-center">Qty</th>
                                                <th class="text-center">Sisi</th>
                                                <th class="text-center">Total (pcs)</th>
                                                <th class="text-end">Total L (m)</th>
                                                <th class="text-end">Rate (Rp)</th>
                                                <th class="text-end">Total Harga (Rp)</th>
                                            </tr>
                                        </thead>
                                        <tbody id="tab-all-tbody">
                                            @forelse(isset($calculation) ? $calculation->details->values() : [] as $detail)
                                            <tr>
                                                <td><span class="badge-premium">{{ $detail->section }}</span></td>
                                                <td class="fw-medium">{{ $detail->part_name }}</td>
                                                <td>{{ $detail->direction }}</td>
                                                <td class="fw-bold text-navy">{{ $detail->material?->kode ?? '-' }}</td>
                                                <td class="text-end">{{ (float)$detail->calculated_thickness }}</td>
                                                <td class="text-end">{{ (float)$detail->calculated_width }}</td>
                                                <td class="text-end fw-medium">{{ (float)$detail->calculated_length }}</td>
                                                <td class="text-center">{{ $detail->quantity }}</td>
                                                <td class="text-center">{{ $detail->side_count }}</td>
                                                <td class="text-center fw-bold" style="color: #0ea5e9;">{{ $detail->total_quantity }}</td>
                                                <td class="text-end">{{ number_format($detail->total_length, 2, ',', '.') }}</td>
                                                <td class="text-end text-muted">{{ number_format($detail->price_per_unit, 0, ',', '.') }}</td>
                                                <td class="text-end fw-bold text-navy">Rp {{ number_format($detail->subtotal_price, 0, ',', '.') }}</td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="13" class="text-center py-5">
                                                    <div class="d-flex flex-column align-items-center justify-content-center text-muted">
                                                        <span class="material-symbols-rounded mb-2 text-slate-300" style="font-size: 40px;">assignment_late</span>
                                                        <h6 class="fw-semibold text-slate-600 mb-1">Belum ada data</h6>
                                                        <p class="small text-slate-400 mb-0">Detail kalkulasi akan muncul di sini</p>
                                                    </div>
                                                </td>
                                            </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Tab: PENYANGGA -->
                            <div class="tab-pane fade" id="content-penyangga" role="tabpanel">
                                <div class="table-responsive">
                                    <table class="table-premium w-100 mb-0">
                                        <thead>
                                            <tr>
                                                <th>Bagian</th>
                                                <th>Arah</th>
                                                <th>Material</th>
                                                <th class="text-end">Tebal (mm)</th>
                                                <th class="text-end">Lebar (mm)</th>
                                                <th class="text-end">Panjang (mm)</th>
                                                <th class="text-center">Qty</th>
                                                <th class="text-center">Sisi</th>
                                                <th class="text-center">Total (pcs)</th>
                                                <th class="text-end">Total L (m)</th>
                                                <th class="text-end">Rate (Rp)</th>
                                                <th class="text-end">Total Harga (Rp)</th>
                                            </tr>
                                        </thead>
                                        <tbody id="tab-penyangga-tbody">
                                            @forelse(isset($calculation) ? $calculation->details->where('section', 'Penyangga') : [] as $detail)
                                            <tr>
                                                <td class="fw-medium">{{ $detail->part_name }}</td>
                                                <td>{{ $detail->direction }}</td>
                                                <td class="fw-bold text-navy">{{ $detail->material?->kode ?? '-' }}</td>
                                                <td class="text-end">{{ (float)$detail->calculated_thickness }}</td>
                                                <td class="text-end">{{ (float)$detail->calculated_width }}</td>
                                                <td class="text-end fw-medium">{{ (float)$detail->calculated_length }}</td>
                                                <td class="text-center">{{ $detail->quantity }}</td>
                                                <td class="text-center">{{ $detail->side_count }}</td>
                                                <td class="text-center fw-bold" style="color: #0ea5e9;">{{ $detail->total_quantity }}</td>
                                                <td class="text-end">{{ number_format($detail->total_length, 2, ',', '.') }}</td>
                                                <td class="text-end text-muted">{{ number_format($detail->price_per_unit, 0, ',', '.') }}</td>
                                                <td class="text-end fw-bold text-navy">Rp {{ number_format($detail->subtotal_price, 0, ',', '.') }}</td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="12" class="text-center py-5 text-muted">Belum ada data kalkulasi penyangga</td>
                                            </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Tab: TUTUP -->
                            <div class="tab-pane fade" id="content-penutup" role="tabpanel">
                                <div class="table-responsive">
                                    <table class="table-premium w-100 mb-0">
                                        <thead>
                                            <tr>
                                                <th>Bagian</th>
                                                <th>Arah</th>
                                                <th>Material</th>
                                                <th class="text-end">Tebal (mm)</th>
                                                <th class="text-end">Lebar (mm)</th>
                                                <th class="text-end">Panjang (mm)</th>
                                                <th class="text-center">Qty</th>
                                                <th class="text-center">Sisi</th>
                                                <th class="text-center">Total (pcs)</th>
                                                <th class="text-end">Total L (m)</th>
                                                <th class="text-end">Rate (Rp)</th>
                                                <th class="text-end">Total Harga (Rp)</th>
                                            </tr>
                                        </thead>
                                        <tbody id="tab-penutup-tbody">
                                            @forelse(isset($calculation) ? $calculation->details->where('section', 'Penutup') : [] as $detail)
                                            <tr>
                                                <td class="fw-medium">{{ $detail->part_name }}</td>
                                                <td>{{ $detail->direction }}</td>
                                                <td class="fw-bold text-navy">{{ $detail->material?->kode ?? '-' }}</td>
                                                <td class="text-end">{{ (float)$detail->calculated_thickness }}</td>
                                                <td class="text-end">{{ (float)$detail->calculated_width }}</td>
                                                <td class="text-end fw-medium">{{ (float)$detail->calculated_length }}</td>
                                                <td class="text-center">{{ $detail->quantity }}</td>
                                                <td class="text-center">{{ $detail->side_count }}</td>
                                                <td class="text-center fw-bold" style="color: #0ea5e9;">{{ $detail->total_quantity }}</td>
                                                <td class="text-end">{{ number_format($detail->total_length, 2, ',', '.') }}</td>
                                                <td class="text-end text-muted">{{ number_format($detail->price_per_unit, 0, ',', '.') }}</td>
                                                <td class="text-end fw-bold text-navy">Rp {{ number_format($detail->subtotal_price, 0, ',', '.') }}</td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="12" class="text-center py-5 text-muted">Belum ada data kalkulasi penutup</td>
                                            </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            
                            <!-- Tab: BAWAH -->
                            <div class="tab-pane fade" id="content-bawah" role="tabpanel">
                                <div class="table-responsive">
                                    <table class="table-premium w-100 mb-0">
                                        <thead>
                                            <tr>
                                                <th>Bagian</th>
                                                <th>Arah</th>
                                                <th>Material</th>
                                                <th class="text-end">Tebal (mm)</th>
                                                <th class="text-end">Lebar (mm)</th>
                                                <th class="text-end">Panjang (mm)</th>
                                                <th class="text-center">Qty</th>
                                                <th class="text-center">Sisi</th>
                                                <th class="text-center">Total (pcs)</th>
                                                <th class="text-end">Total L (m)</th>
                                                <th class="text-end">Rate (Rp)</th>
                                                <th class="text-end">Total Harga (Rp)</th>
                                            </tr>
                                        </thead>
                                        <tbody id="tab-bawah-tbody">
                                            @forelse(isset($calculation) ? $calculation->details->where('section', 'Bawah')->values() : [] as $detail)
                                            <tr>
                                                <td class="fw-medium">{{ $detail->part_name }}</td>
                                                <td>{{ $detail->direction }}</td>
                                                <td class="fw-bold text-navy">{{ $detail->material?->kode ?? '-' }}</td>
                                                <td class="text-end">{{ (float)$detail->calculated_thickness }}</td>
                                                <td class="text-end">{{ (float)$detail->calculated_width }}</td>
                                                <td class="text-end fw-medium">{{ (float)$detail->calculated_length }}</td>
                                                <td class="text-center">{{ $detail->quantity }}</td>
                                                <td class="text-center">{{ $detail->side_count }}</td>
                                                <td class="text-center fw-bold" style="color: #0ea5e9;">{{ $detail->total_quantity }}</td>
                                                <td class="text-end">{{ number_format($detail->total_length, 2, ',', '.') }}</td>
                                                <td class="text-end text-muted">{{ number_format($detail->price_per_unit, 0, ',', '.') }}</td>
                                                <td class="text-end fw-bold text-navy">Rp {{ number_format($detail->subtotal_price, 0, ',', '.') }}</td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="12" class="text-center py-5 text-muted">Belum ada data kalkulasi bawah</td>
                                            </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Tab: MANPOWER -->
                            <div class="tab-pane fade" id="content-manpower" role="tabpanel">
                                <div class="table-responsive border-bottom">
                                    <table class="table-premium w-100 mb-0">
                                        <thead>
                                            <tr>
                                                <th>Bagian / Area</th>
                                                <th class="text-end">Panjang (mm)</th>
                                                <th class="text-end">Lebar (mm)</th>
                                                <th class="text-center">Jumlah Sisi</th>
                                                <th class="text-end">Luas per Sisi (m²)</th>
                                                <th class="text-end">Total Luas (m²)</th>
                                            </tr>
                                        </thead>
                                        <tbody id="manpower-tbody">
                                            <!-- Will be populated dynamically by JavaScript -->
                                        </tbody>
                                    </table>
                                </div>
                                
                                <!-- Manpower Summary Card -->
                                <div class="p-4 bg-light d-flex justify-content-end tab-footer border-top">
                                    <div class="card-summary-premium p-4" style="width: 380px;">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <div class="d-flex align-items-center gap-2 text-muted">
                                                <span class="material-symbols-rounded fs-5 text-sky-500" style="color: #0ea5e9;">square_foot</span>
                                                <span class="fw-bold small text-uppercase" style="letter-spacing: 0.5px; font-size: 11px;">Total Area (SQM)</span>
                                            </div>
                                            <span class="fs-6 fw-black text-dark" id="mp-total-sqm">0.00</span>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center mb-4">
                                            <div class="d-flex align-items-center gap-2 text-muted">
                                                <span class="material-symbols-rounded fs-5 text-sky-500" style="color: #0ea5e9;">payments</span>
                                                <span class="fw-bold small text-uppercase" style="letter-spacing: 0.5px; font-size: 11px;">Rate MP / m²</span>
                                            </div>
                                            <span class="fs-6 fw-bold text-dark">Rp {{ number_format($manpowerRate, 0, ',', '.') }}</span>
                                        </div>
                                        <div class="border-top pt-3 d-flex justify-content-between align-items-center">
                                            <span class="text-navy fw-black text-uppercase" style="font-size: 13px; letter-spacing: 0.5px;">Total Biaya MP</span>
                                            <span class="fs-4 fw-black text-primary" id="mp-total-cost">Rp 0</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Tab: PAKU -->
                            <div class="tab-pane fade" id="content-paku" role="tabpanel">
                                <div class="table-responsive border-bottom">
                                    <table class="table-premium w-100 mb-0">
                                        <thead>
                                            <tr>
                                                <th>Bagian / Area</th>
                                                <th class="text-center">Size (Cm)</th>
                                                <th class="text-end">Titik Paku</th>
                                                <th class="text-end">Jml Paku/Titik</th>
                                                <th class="text-end">Total Paku</th>
                                                <th class="text-end">Estimasi Berat (Kg)</th>
                                                <th class="text-end">Harga per Kg (Rp)</th>
                                                <th class="text-end">Total Harga (Rp)</th>
                                            </tr>
                                        </thead>
                                        <tbody id="paku-tbody">
                                            <!-- Data paku dimuat secara dinamis oleh JavaScript -->
                                        </tbody>
                                    </table>
                                </div>

                                <!-- Paku Summary Card -->
                                <div class="p-4 bg-light d-flex justify-content-end tab-footer border-top">
                                    <div class="card-summary-premium p-4" style="width: 380px;">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <div class="d-flex align-items-center gap-2 text-muted">
                                                <span class="material-symbols-rounded fs-5 text-amber-500" style="color: #f59e0b;">pin</span>
                                                <span class="fw-bold small text-uppercase" style="letter-spacing: 0.5px; font-size: 11px;">Total Paku</span>
                                            </div>
                                            <span class="fs-6 fw-black text-dark"><span id="paku-total-pcs">0</span> pcs</span>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <div class="d-flex align-items-center gap-2 text-muted">
                                                <span class="material-symbols-rounded fs-5 text-amber-500" style="color: #f59e0b;">scale</span>
                                                <span class="fw-bold small text-uppercase" style="letter-spacing: 0.5px; font-size: 11px;">Total Berat Paku</span>
                                            </div>
                                            <span class="fs-6 fw-black text-dark"><span id="paku-total-kg">0.00</span> Kg</span>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center mb-4">
                                            <div class="d-flex align-items-center gap-2 text-muted">
                                                <span class="material-symbols-rounded fs-5 text-amber-500" style="color: #f59e0b;">sell</span>
                                                <span class="fw-bold small text-uppercase" style="letter-spacing: 0.5px; font-size: 11px;">Harga Paku / Kg</span>
                                            </div>
                                            <span class="fs-6 fw-bold text-dark">Rp {{ number_format($nailsPricePerKg, 0, ',', '.') }}</span>
                                        </div>
                                        <div class="border-top pt-3 d-flex justify-content-between align-items-center">
                                            <span class="text-navy fw-black text-uppercase" style="font-size: 13px; letter-spacing: 0.5px;">Total Biaya Paku</span>
                                            <span class="fs-4 fw-black text-primary" id="paku-total-cost">Rp 0</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Tab: WAKTU PROSES -->
                            <div class="tab-pane fade" id="content-waktu-proses" role="tabpanel">
                                @php
                                    $volM3 = 0;
                                    if (isset($calculation)) {
                                        $p = $calculation->panjang ?? 0;
                                        $l = $calculation->lebar ?? 0;
                                        $t = $calculation->tinggi ?? 0;
                                        $volM3 = ($p * $l * $t) / 1000000000;
                                    }
                                @endphp
                                <div class="table-responsive border-bottom">
                                    <table class="table-premium w-100 mb-0">
                                        <thead class="bg-light">
                                            <tr>
                                                <th>Aktivitas</th>
                                                <th class="text-end">Volume (m³)</th>
                                                <th class="text-end">Waktu Proses</th>
                                                <th class="text-center">Satuan</th>
                                                <th class="text-end">Total Waktu Proses</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td><i class="fa-solid fa-cut me-2 text-secondary"></i>Potong Kayu</td>
                                                <td class="text-end fw-bold vol-m3-cell">{{ number_format($volM3, 3, ',', '.') }}</td>
                                                <td class="text-end">30</td>
                                                <td class="text-center">Menit/m³</td>
                                                <td class="text-end fw-bold" id="waktu-potong">{{ isset($calculation) ? number_format($calculation->manpower_potong ?? 0, 2, ',', '.') : '0,00' }}</td>
                                            </tr>
                                            <tr>
                                                <td><i class="fa-solid fa-layer-group me-2 text-secondary"></i>Serut Kayu</td>
                                                <td class="text-end fw-bold vol-m3-cell">{{ number_format($volM3, 3, ',', '.') }}</td>
                                                <td class="text-end">30</td>
                                                <td class="text-center">Menit/m³</td>
                                                <td class="text-end fw-bold" id="waktu-serut">{{ isset($calculation) ? number_format($calculation->manpower_serut ?? 0, 2, ',', '.') : '0,00' }}</td>
                                            </tr>
                                            <tr>
                                                <td><i class="fa-solid fa-hammer me-2 text-secondary"></i>Perakitan</td>
                                                <td class="text-end fw-bold vol-m3-cell">{{ number_format($volM3, 3, ',', '.') }}</td>
                                                <td class="text-end">105</td>
                                                <td class="text-center">Menit/m³</td>
                                                <td class="text-end fw-bold" id="waktu-perakitan">{{ isset($calculation) ? number_format($calculation->manpower_perakitan ?? 0, 2, ',', '.') : '0,00' }}</td>
                                            </tr>
                                            <tr>
                                                <td><i class="fa-solid fa-boxes-packing me-2 text-secondary"></i>Prepare</td>
                                                <td class="text-end fw-bold vol-m3-cell">{{ number_format($volM3, 3, ',', '.') }}</td>
                                                <td class="text-end">20</td>
                                                <td class="text-center">Menit/m³</td>
                                                <td class="text-end fw-bold" id="waktu-prepare">{{ isset($calculation) ? number_format($calculation->manpower_prepare ?? 0, 2, ',', '.') : '0,00' }}</td>
                                            </tr>
                                        </tbody>
                                        <tfoot class="bg-light border-top">
                                            <tr>
                                                <th colspan="4" class="text-end py-3">TOTAL WAKTU MANPOWER</th>
                                                <th class="text-end py-3 fs-5 text-primary fw-black"><span id="waktu-total">{{ isset($calculation) ? number_format(($calculation->total_waktu_manpower ?? 0) / 60, 2, ',', '.') : '0,00' }}</span> <small class="text-muted fs-6">Jam</small></th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>

                                <!-- Waktu Proses Summary Card -->
                                <div class="p-4 bg-light d-flex justify-content-end tab-footer border-top">
                                    <div class="card-summary-premium p-4" style="width: 380px;">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <div class="d-flex align-items-center gap-2 text-muted">
                                                <span class="material-symbols-rounded fs-5 text-blue-500" style="color: #3b82f6;">category</span>
                                                <span class="fw-bold small text-uppercase" style="letter-spacing: 0.5px; font-size: 11px;">Volume Packaging</span>
                                            </div>
                                            <span class="fs-6 fw-black text-dark"><span id="waktu-volume-m3">{{ number_format($volM3, 3, ',', '.') }}</span> m³</span>
                                        </div>
                                        <div class="border-top pt-3 d-flex justify-content-between align-items-center">
                                            <span class="text-navy fw-black text-uppercase" style="font-size: 13px; letter-spacing: 0.5px;">Total Waktu Manpower</span>
                                            <span class="fs-4 fw-black text-primary"><span id="waktu-total-card">{{ isset($calculation) ? number_format(($calculation->total_waktu_manpower ?? 0) / 60, 2, ',', '.') : '0,00' }}</span> <span class="fs-6 text-muted">Jam</span></span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
