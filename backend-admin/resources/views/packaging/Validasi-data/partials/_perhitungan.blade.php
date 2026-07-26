<div class="p-4 bg-white m-4 rounded-3 border">
    <div class="d-flex align-items-center mb-4">
        <span class="material-symbols-rounded text-primary me-2 fs-4">functions</span>
        <h5 class="mb-0 fw-bold" style="color: #0b2a55;">Rumus Perhitungan Material</h5>
    </div>
    
    <p class="text-secondary mb-4" style="font-size: 13px;">Berikut adalah rincian rumus perhitungan Panjang dan QTY (Jumlah) untuk masing-masing bagian yang dikalkulasi pada boks berukuran <strong>{{ isset($calculation) ? $calculation->p : 0 }} x {{ isset($calculation) ? $calculation->l : 0 }} x {{ isset($calculation) ? $calculation->t : 0 }}</strong>.</p>
    
    @php
        $P = isset($calculation) ? ($calculation->p ?? 0) : 0;
        $L = isset($calculation) ? ($calculation->l ?? 0) : 0;
        $T = isset($calculation) ? ($calculation->t ?? 0) : 0;
        $jarak = isset($calculation) ? ($calculation->jarak_tiang ?? 0) : 0;
        $jenisPenutup = isset($calculation) ? ($calculation->jenis_penutup ?? '') : '';
        $celahAtas = isset($calculation) ? ($calculation->gap_atas ?? $calculation->celah_penutup ?? 0) : 0;
        $celahBawah = isset($calculation) ? ($calculation->gap_bawah ?? $calculation->celah_penutup ?? 0) : 0;
        
        $details = isset($calculation) ? ($calculation->details ?? collect()) : collect();
        $rangkaDetails = $details->where('section', 'Rangka')->values();
        $penyanggaDetails = $details->where('section', 'Penyangga')->values();
        $penutupDetails = $details->where('section', 'Penutup')->values();
        $bawahDetails = $details->where('section', 'Bawah')->values();
        
        // Dapatkan tebal rangka untuk rumus penutup
        $tebalRangka = 0;
        if($rangkaDetails->isNotEmpty()) {
            $tebalRangka = $rangkaDetails->first()->calculated_thickness ?? 0;
        }
    @endphp

    <div class="accordion" id="accordionPerhitungan">
        
        <!-- RANGKA -->
        <div class="accordion-item border-0 mb-3 rounded-3 shadow-sm">
            <h2 class="accordion-header" id="headingRangka">
                <button class="accordion-button fw-bold rounded-3 bg-light text-navy" type="button" data-bs-toggle="collapse" data-bs-target="#collapseRangka" aria-expanded="true" aria-controls="collapseRangka">
                    1. Rangka (Frame)
                </button>
            </h2>
            <div id="collapseRangka" class="accordion-collapse collapse show" aria-labelledby="headingRangka">
                <div class="accordion-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered align-middle" style="font-size: 12px;">
                            <thead class="table-light text-secondary">
                                <tr>
                                    <th>Bagian</th>
                                    <th>Rumus Panjang</th>
                                    <th>Rumus QTY (Per Sisi)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($rangkaDetails as $d)
                                <tr>
                                    <td class="fw-bold">{{ $d->part_name }}</td>
                                    <td>
                                        @if($d->part_name == 'Atas' || $d->part_name == 'Bawah')
                                            Panjang Boks (P) = <strong>{{ $P }} mm</strong>
                                        @elseif($d->part_name == 'Lebar Atas' || $d->part_name == 'Lebar Bawah')
                                            Lebar Boks (L) = <strong>{{ $L }} mm</strong>
                                        @else
                                            Tinggi Boks (T) = <strong>{{ $T }} mm</strong>
                                        @endif
                                    </td>
                                    <td>
                                        @if($d->part_name == 'Tinggi')
                                            8 Titik / 2 Sisi = <strong>4 Pcs</strong>
                                        @else
                                            4 Titik / 2 Sisi = <strong>2 Pcs</strong>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- PENYANGGA -->
        <div class="accordion-item border-0 mb-3 rounded-3 shadow-sm">
            <h2 class="accordion-header" id="headingPenyangga">
                <button class="accordion-button collapsed fw-bold rounded-3 bg-light text-navy" type="button" data-bs-toggle="collapse" data-bs-target="#collapsePenyangga" aria-expanded="false" aria-controls="collapsePenyangga">
                    2. Penyangga (Support) - Jarak: {{ $jarak }} mm
                </button>
            </h2>
            <div id="collapsePenyangga" class="accordion-collapse collapse" aria-labelledby="headingPenyangga">
                <div class="accordion-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered align-middle" style="font-size: 12px;">
                            <thead class="table-light text-secondary">
                                <tr>
                                    <th>Bagian</th>
                                    <th>Arah</th>
                                    <th>Rumus Panjang</th>
                                    <th>Rumus QTY (Per Sisi)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($penyanggaDetails as $d)
                                @php
                                    $partLength = 0; $spacingDimension = 0;
                                    $dimLengthLabel = ''; $dimSpacingLabel = '';
                                    if ($d->part_name == 'Atas' || $d->part_name == 'Bawah') {
                                        if ($d->direction == 'Horizontal') {
                                            $dimLengthLabel = 'Panjang (P)'; $partLength = $P;
                                            $dimSpacingLabel = 'Lebar (L)'; $spacingDimension = $L;
                                        } else {
                                            $dimLengthLabel = 'Lebar (L)'; $partLength = $L;
                                            $dimSpacingLabel = 'Panjang (P)'; $spacingDimension = $P;
                                        }
                                    } elseif ($d->part_name == 'Kanan' || $d->part_name == 'Kiri') {
                                        if ($d->direction == 'Horizontal') {
                                            $dimLengthLabel = 'Lebar (L)'; $partLength = $L;
                                            $dimSpacingLabel = 'Tinggi (T)'; $spacingDimension = $T;
                                        } else {
                                            $dimLengthLabel = 'Tinggi (T)'; $partLength = $T;
                                            $dimSpacingLabel = 'Lebar (L)'; $spacingDimension = $L;
                                        }
                                    } else { // Depan, Belakang
                                        if ($d->direction == 'Horizontal') {
                                            $dimLengthLabel = 'Panjang (P)'; $partLength = $P;
                                            $dimSpacingLabel = 'Tinggi (T)'; $spacingDimension = $T;
                                        } else {
                                            $dimLengthLabel = 'Tinggi (T)'; $partLength = $T;
                                            $dimSpacingLabel = 'Panjang (P)'; $spacingDimension = $P;
                                        }
                                    }
                                @endphp
                                <tr>
                                    <td class="fw-bold">{{ $d->part_name }}</td>
                                    <td><span class="badge bg-secondary">{{ $d->direction }}</span></td>
                                    <td>{{ $dimLengthLabel }} = <strong>{{ $partLength }} mm</strong></td>
                                    <td>
                                        Ceil( {{ $dimSpacingLabel }} / Jarak ) - 1 <br>
                                        <span class="text-muted">Ceil( {{ $spacingDimension }} / {{ $jarak }} ) - 1 = <strong>{{ max(0, ceil($spacingDimension / max(1, $jarak)) - 1) }} Pcs</strong></span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- PENUTUP -->
        <div class="accordion-item border-0 mb-3 rounded-3 shadow-sm">
            <h2 class="accordion-header" id="headingPenutup">
                <button class="accordion-button collapsed fw-bold rounded-3 bg-light text-navy" type="button" data-bs-toggle="collapse" data-bs-target="#collapsePenutup" aria-expanded="false" aria-controls="collapsePenutup">
                    3. Penutup (Cover) - Jenis: {{ $jenisPenutup ?: '-' }}
                </button>
            </h2>
            <div id="collapsePenutup" class="accordion-collapse collapse" aria-labelledby="headingPenutup">
                <div class="accordion-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered align-middle" style="font-size: 12px;">
                            <thead class="table-light text-secondary">
                                <tr>
                                    <th>Bagian</th>
                                    <th>Arah</th>
                                    <th>Lebar Papan</th>
                                    <th>Rumus Panjang</th>
                                    <th>Rumus QTY (Per Sisi)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(stripos($jenisPenutup, 'Tidak') !== false)
                                <tr><td colspan="5" class="text-center text-muted py-3">Tidak menggunakan penutup</td></tr>
                                @else
                                    @foreach($penutupDetails as $d)
                                    @php
                                        $partLength = 0; $qtyBasis = 0;
                                        $dimLengthLabel = ''; $dimSpacingLabel = '';
                                        if ($d->part_name == 'Atas' || $d->part_name == 'Bawah') {
                                            if ($d->direction == 'Horizontal') {
                                                $dimLengthLabel = 'Panjang (P)'; $partLength = $P + ($tebalRangka * 2);
                                                $dimSpacingLabel = 'Lebar (L)'; $qtyBasis = $L + ($tebalRangka * 2);
                                            } else {
                                                $dimLengthLabel = 'Lebar (L)'; $partLength = $L + ($tebalRangka * 2);
                                                $dimSpacingLabel = 'Panjang (P)'; $qtyBasis = $P + ($tebalRangka * 2);
                                            }
                                        } elseif ($d->part_name == 'Kanan' || $d->part_name == 'Kiri') {
                                            if ($d->direction == 'Horizontal') {
                                                $dimLengthLabel = 'Lebar (L)'; $partLength = $L + ($tebalRangka * 2);
                                                $dimSpacingLabel = 'Tinggi (T)'; $qtyBasis = $T + ($tebalRangka * 2);
                                            } else {
                                                $dimLengthLabel = 'Tinggi (T)'; $partLength = $T + ($tebalRangka * 2);
                                                $dimSpacingLabel = 'Lebar (L)'; $qtyBasis = $L + ($tebalRangka * 2);
                                            }
                                        } else { // Depan, Belakang
                                            if ($d->direction == 'Horizontal') {
                                                $dimLengthLabel = 'Panjang (P)'; $partLength = $P + ($tebalRangka * 2);
                                                $dimSpacingLabel = 'Tinggi (T)'; $qtyBasis = $T + ($tebalRangka * 2);
                                            } else {
                                                $dimLengthLabel = 'Tinggi (T)'; $partLength = $T + ($tebalRangka * 2);
                                                $dimSpacingLabel = 'Panjang (P)'; $qtyBasis = $P + ($tebalRangka * 2);
                                            }
                                        }
                                        
                                        $isTripleks = (stripos($jenisPenutup, 'Tripleks') !== false || stripos($jenisPenutup, 'Triplex') !== false);
                                        $lebarPapan = (float)$d->calculated_width;
                                        $activeCelah = ($d->part_name == 'Bawah') ? $celahBawah : $celahAtas;
                                        $divisor = (stripos($jenisPenutup, 'Setengah') !== false) ? ($lebarPapan + $activeCelah) : $lebarPapan;
                                        $divisorStr = (stripos($jenisPenutup, 'Setengah') !== false) ? "(Lebar Papan + Celah)" : "Lebar Papan";
                                    @endphp
                                    <tr>
                                        <td class="fw-bold">{{ $d->part_name }}</td>
                                        <td><span class="badge bg-secondary">{{ $d->direction }}</span></td>
                                        <td>{{ $lebarPapan }} mm</td>
                                        <td>
                                            {{ $dimLengthLabel }} + (2 x Tebal Rangka) <br>
                                            <span class="text-muted"> = <strong>{{ $partLength }} mm</strong></span>
                                        </td>
                                        <td>
                                            @if($isTripleks)
                                                Langsung = <strong>1 Lembar</strong>
                                            @else
                                                Ceil( ({{ $dimSpacingLabel }} + 2xTR) / {{ $divisorStr }} ) <br>
                                                <span class="text-muted">Ceil( {{ $qtyBasis }} / {{ $divisor }} ) = <strong>{{ $divisor > 0 ? ceil($qtyBasis / $divisor) : 0 }} Pcs</strong></span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- BAWAH -->
        <div class="accordion-item border-0 mb-3 rounded-3 shadow-sm">
            <h2 class="accordion-header" id="headingBawah">
                <button class="accordion-button collapsed fw-bold rounded-3 bg-light text-navy" type="button" data-bs-toggle="collapse" data-bs-target="#collapseBawah" aria-expanded="false" aria-controls="collapseBawah">
                    4. Bagian Bawah (Rangka, Penyangga, Penutup, & Palet)
                </button>
            </h2>
            <div id="collapseBawah" class="accordion-collapse collapse" aria-labelledby="headingBawah">
                <div class="accordion-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered align-middle" style="font-size: 12px;">
                            <thead class="table-light text-secondary">
                                <tr>
                                    <th>Bagian</th>
                                    <th>Arah</th>
                                    <th>Lebar Material</th>
                                    <th>Rumus Panjang</th>
                                    <th>Rumus QTY (Per Sisi)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if($bawahDetails->isEmpty())
                                    <tr>
                                        <td colspan="5" class="text-center text-muted">Tidak ada data untuk Bagian Bawah</td>
                                    </tr>
                                @else
                                    @foreach($bawahDetails as $d)
                                    <tr>
                                        <td class="fw-bold">{{ $d->part_name }}</td>
                                        <td><span class="badge bg-secondary">{{ $d->direction }}</span></td>
                                        <td>{{ (float)$d->calculated_width }} mm</td>
                                        <td>{{ (float)$d->calculated_length }} mm</td>
                                        <td>{{ $d->quantity }}</td>
                                    </tr>
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
