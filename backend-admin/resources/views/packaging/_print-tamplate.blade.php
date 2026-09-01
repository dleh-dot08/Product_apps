<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Kalkulasi - {{ $calculation->project_name ?? 'Crate' }}</title>
    <style>
        /* Loading Overlay for Background Generation */
        #bgGenOverlay {
            position: fixed; top: 0; left: 0; width: 100vw; height: 100vh;
            background-color: rgba(255, 255, 255, 0.95);
            z-index: 99999;
            display: flex; flex-direction: column; justify-content: center; align-items: center;
        }
        
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');

        @page {
            size: A4 portrait;
            margin: 4mm 10mm 10mm 10mm; /* Top Right Bottom Left */
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background-color: #cbd5e1;
            color: #1e293b;
            margin: 0;
            padding: 20px 0;
            font-size: 10px;
            line-height: 1.4;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        /* Base Utilities */
        .w-100 { width: 100%; }
        .text-center { text-align: center; }
        .text-start { text-align: left; }
        .text-end { text-align: right; }
        .fw-bold { font-weight: 700; }
        .fw-bolder { font-weight: 800; }
        
        .print-container {
            width: 210mm;
            min-height: 297mm;
            background-color: #ffffff;
            padding: 10mm;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
            box-sizing: border-box;
        }

        /* Print Button Bar */
        .print-btn-bar {
            width: 210mm;
            display: flex;
            justify-content: flex-end;
            padding: 0;
            margin-bottom: 10px;
        }
        .btn-print {
            background-color: #0f3566;
            color: white;
            border: none;
            padding: 3px 6px;
            font-size: 12px;
            font-weight: bold;
            border-radius: 6px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }
        @media print {
            .print-btn-bar { display: none !important; }
            body { 
                background-color: #ffffff; 
                padding: 0; 
                margin: 0; 
                display: block;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            .print-container {
                width: 100%;
                min-height: auto;
                padding: 0;
                box-shadow: none;
            }
        }

        /* Document Header */
        .doc-header {
            border-bottom: 2px solid #0f3566;
            padding-top: 10px;
            padding-bottom: 4px;
            margin-bottom: 4px;
        }
        .doc-header-title h1 {
            color: #0f3566;
            font-size: 12px;
            font-weight: 800;
            margin: 0 0 2px 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .doc-header-title p {
            color: #475569;
            margin: 0;
            font-size: 8px;
            font-weight: 500;
        }
        .doc-header-right {
            text-align: right;
        }
        .doc-header-right .date-box {
            background-color: #f1f5f9;
            border: 1px solid #cbd5e1;
            padding: 2px 8px;
            border-radius: 4px;
            font-weight: 600;
            color: #0f3566;
            font-size: 9px;
            display: inline-block;
        }

        /* Section Layouts */
        .section-panel {
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            margin-bottom: 4px;
            overflow: visible;
            page-break-inside: avoid;
            break-inside: auto;
        }
        .section-header {
            background-color: #f1f5f9;
            color: #0f3566;
            padding: 4px 8px;
            font-size: 8px;
            font-weight: 800;
            border-bottom: 1px solid #cbd5e1;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .section-body {
            padding: 6px;
            background-color: #ffffff;
        }

        /* Konfigurasi Awal Grid */
        .config-cards {
            display: grid;
            grid-template-columns: 2fr 4fr 4fr 2fr;
            gap: 3px;
            margin-bottom: 5px;
        }
        .cfg-card {
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            background: #ffffff;
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }
        .cfg-header {
            display: flex;
            align-items: center;
            gap: 6px;
            padding: 4px 6px;
            font-size: 7px;
            font-weight: 800;
            text-transform: uppercase;
        }
        .cfg-header .badge {
            color: #ffffff;
            width: 14px;
            height: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 3px;
        }
        /* Card Colors */
        .cfg-card.blue { border-color: #bfdbfe; }
        .cfg-card.blue .cfg-header { background-color: #eff6ff; color: #1e3a8a; border-bottom: 1px solid #bfdbfe; }
        .cfg-card.blue .badge { background-color: #1d4ed8; }

        .cfg-card.green { border-color: #bbf7d0; }
        .cfg-card.green .cfg-header { background-color: #f0fdf4; color: #14532d; border-bottom: 1px solid #bbf7d0; }
        .cfg-card.green .badge { background-color: #15803d; }

        .cfg-card.orange { border-color: #fed7aa; }
        .cfg-card.orange .cfg-header { background-color: #fff7ed; color: #7c2d12; border-bottom: 1px solid #fed7aa; }
        .cfg-card.orange .badge { background-color: #ea580c; }

        .cfg-card.grey { border-color: #e2e8f0; }
        .cfg-card.grey .cfg-header { background-color: #f8fafc; color: #334155; border-bottom: 1px solid #e2e8f0; }
        .cfg-card.grey .badge { background-color: #64748b; }

        .cfg-body {
            padding: 4px;
            font-size: 7px;
            flex-grow: 1;
        }

        /* List layout for cards 1 and 4 */
        .cfg-list-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 6px;
        }
        .cfg-list-item:last-child { margin-bottom: 0; }
        .cfg-label { font-weight: 700; color: #475569; }
        .cfg-val-box {
            border: 1px solid #e2e8f0;
            background: #ffffff;
            border-radius: 4px;
            padding: 2px 6px;
            font-weight: 800;
            color: #0f172a;
            min-width: 40px;
            text-align: right;
            display: inline-flex;
            justify-content: flex-end;
            align-items: baseline;
            gap: 2px;
        }
        .cfg-unit { font-size: 6px; color: #94a3b8; font-weight: 600; }

        /* Table layout for cards 2 and 3 */
        .cfg-table {
            width: 100%;
            border-collapse: collapse;
        }
        .cfg-table th {
            text-align: center;
            font-weight: 800;
            color: #1e293b;
            padding-bottom: 6px;
            border-bottom: 1px solid #e2e8f0;
        }
        .cfg-table th:first-child, .cfg-table td:first-child { text-align: left; }
        .cfg-table td {
            text-align: center;
            padding: 4px 0;
            font-weight: 700;
            color: #475569;
        }
        .cfg-table td .val-box {
            border: 1px solid #e2e8f0;
            background: #ffffff;
            border-radius: 4px;
            padding: 2px 4px;
            display: inline-block;
            min-width: 30px;
            color: #0f172a;
        }

        /* 3D Visual Grid */
        .visual-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
        }
        .visual-card {
            text-align: center;
            padding: 5px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .visual-image-wrapper {
            height: 140px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 8px;
            overflow: hidden;
        }
        .visual-image-wrapper img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            /* Scale controlled individually to prevent cropping */
        }
        .visual-title {
            font-size: 7.5px;
            font-weight: 800;
            color: #334155;
            text-transform: uppercase;
        }
        .visual-card-small {
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            text-align: center;
            display: flex;
            flex-direction: column;
            background-color: #ffffff;
            overflow: hidden;
            padding: 4px;
            page-break-inside: avoid;
            break-inside: avoid;
        }
        .visual-card-small .visual-title {
            font-size: 8px;
            font-weight: 800;
            color: #0f3566;
            padding: 2px 0 4px 0;
            border-top: none;
        }
        .visual-card-small .visual-image-wrapper {
            background-color: #f8fafc;
            border-radius: 4px;
        }
        .not-included-box {
            width: 100%;
            height: 100%;
            border: 1.5px dashed #cbd5e1;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #64748b;
            font-size: 10px;
            font-weight: bold;
            font-style: italic;
        }
        .visual-placeholder {
            color: #94a3b8;
            font-size: 10px;
            font-style: italic;
        }

        /* Data Tables */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8px;
        }
        .data-table th {
            background-color: #f8fafc;
            color: #475569;
            font-weight: 800;
            text-transform: uppercase;
            padding: 6px 8px;
            border-bottom: 2px solid #cbd5e1;
            text-align: center;
        }
        .data-table th.text-start { text-align: left; }
        .data-table th.text-end { text-align: right; }
        .data-table td {
            padding: 6px 8px;
            border-bottom: 1px solid #e2e8f0;
            color: #334155;
            vertical-align: middle;
            font-size: 8px;
        }
        .data-table tr:nth-child(even) td {
            background-color: #fbfdff;
        }
        .data-table td.text-center { text-align: center; }
        .data-table td.text-start { text-align: left; }
        .data-table td.text-end { text-align: right; }
        
        .total-row td {
            background-color: #f1f5f9 !important;
            font-weight: 800;
            color: #0f3566;
            border-top: 2px solid #cbd5e1;
            border-bottom: none;
        }

        /* Split Layout */
        .split-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 5px;
        }
        
        /* Summary Grid */
        .summary-box {
            background-color: #f8fafc;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            padding: 12px;
            height: 100%;
            box-sizing: border-box;
        }
        .summary-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px dashed #cbd5e1;
            padding: 6px 0;
        }
        .summary-item:last-child {
            border-bottom: none;
        }
        .summary-box .summary-label {
            color: #475569;
            font-weight: 600;
            font-size: 8px;
        }
        .summary-box .summary-val {
            font-weight: 800;
            color: #0f172a;
            font-size: 8px;
        }
        .summary-grand-total {
            margin-top: 10px;
            padding-top: 10px;
            border-top: 2px solid #0f3566;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .summary-grand-total .label {
            font-size: 9px;
            font-weight: 800;
            color: #0f3566;
        }
        .summary-grand-total .val {
            font-size: 12px;
            font-weight: 900;
            color: #1d4ed8;
        }

        /* Signatures */
        .signature-area {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            height: 100%;
        }
        .sig-box {
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            display: flex;
            flex-direction: column;
        }
        .sig-title {
            background-color: #f1f5f9;
            text-align: center;
            padding: 4px;
            font-size: 9px;
            font-weight: 700;
            color: #475569;
            border-bottom: 1px solid #cbd5e1;
        }
        .sig-space {
            flex-grow: 1;
            min-height: 50px;
        }
        .sig-name {
            text-align: center;
            padding: 4px;
            font-size: 10px;
            font-weight: 800;
            color: #0f3566;
            border-top: 1px dotted #94a3b8;
            margin: 0 10px 10px 10px;
        }

        .clearfix::after {
            content: "";
            display: table;
            clear: both;
        }
    </style>
</head>
<body>

    <!-- Print Button -->
    <div class="print-btn-bar">
        <button class="btn-print" onclick="window.print()">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="6 9 6 2 18 2 18 9"></polyline>
                <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path>
                <rect x="6" y="14" width="12" height="8"></rect>
            </svg>
            Cetak Dokumen
        </button>
    </div>

    @php
        // Calculation Variables Setup
        $P = (float)($calculation->length ?? 0);
        $L = (float)($calculation->width ?? 0);
        $T = (float)($calculation->height ?? 0);

        // Wood Material totals
        $woodDetails = $calculation->details ?? collect();
        $totalWoodPcs = $woodDetails->sum('quantity');
        $totalWoodParts = $woodDetails->sum(function($item) { return $item->quantity * $item->side_count; });
        $totalWoodLength = $woodDetails->sum('total_length');
        $totalWoodVolume = $woodDetails->sum('volume');
        $totalWoodCost = $woodDetails->sum('subtotal_price');

        // Paku (Nails) totals
        $nailsDetails = $calculation->consumables ? $calculation->consumables->where('category', 'Paku') : collect();
        $totalNailsPcs = $nailsDetails->sum('total_paku');
        $totalNailsWeight = $nailsDetails->sum('estimasi_berat');
        $totalNailsCost = $nailsDetails->sum('total_harga');

        // Manpower totals
        $manpowerDetails = $calculation->manpower ?? collect();
        $totalManpowerArea = $manpowerDetails->sum('total_luas');
        $totalManpowerCost = $manpowerDetails->sum('total_biaya');

        // Grand Total Cost
        $grandTotal = $totalWoodCost + $totalNailsCost + $totalManpowerCost;
    @endphp

    <div class="print-container">
        <table style="width: 100%; border: none; border-spacing: 0; margin: 0; padding: 0;">
            <thead style="display: table-header-group;">
                <tr>
                    <td style="border: none; padding: 0;">
                        <!-- HEADER DOKUMEN -->
                        <div class="doc-header">
                            <!-- Baris Pertama: LOGO -->
                            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 4px;">
                                <img src="{{ asset('logo/aqpa-indonesia-logo.png') }}" style="max-height: 35px; object-fit: contain;" alt="Logo AQPA">
                                <img src="{{ asset('logo/tuv-logo-2025.jpg') }}" style="max-height: 35px; object-fit: contain;" alt="TUV">
                            </div>

                            <!-- Baris Kedua: JUDUL & TANGGAL -->
                            <div style="display: flex; justify-content: space-between; align-items: flex-end;">
                                <div class="doc-header-title">
                                    <h1>Spesifikasi & Biaya Packaging</h1>
                                    <p>Ringkasan Kebutuhan Material</p>
                                </div>
                                <div class="doc-header-right">
                                    <div class="date-box" style="margin-bottom: 2px;">
                                        Tanggal Cetak: {{ date('d M Y') }}
                                    </div>
                                    <div style="font-size: 8px; font-weight: 600; color: #475569;">
                                        Dokumen Cetak Internal
                                    </div>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="border: none; padding: 0;">
                        <!-- PACKING INFORMATION (Only on Page 1) -->
                        <div class="section-panel" style="margin-bottom: 0;">
                            <div style="display: flex; justify-content: space-between; align-items: center; padding: 6px 12px; border-bottom: 1px solid #cbd5e1; background-color: #f8fafc; border-radius: 8px 8px 0 0;">
                                <div style="font-size: 10px; font-weight: 800; color: #0f3566; display: flex; align-items: center; gap: 5px;">
                                    PACKING INFORMATION
                                </div>
                                <div style="font-size: 8px; font-weight: 800; color: #1d4ed8; background-color: #eff6ff; padding: 2px 8px; border-radius: 10px; border: 1px solid #bfdbfe;">
                                    &bull; DRAFT
                                </div>
                            </div>
                            <div style="padding: 10px 12px; background-color: #ffffff; border-radius: 0 0 8px 8px;">
                                <div>
                                    <div style="font-size: 8px; color: #64748b; margin-bottom: 2px;">Reference Number</div>
                                    <div style="font-size: 14px; font-weight: 900; color: #0f3566;">{{ $calculation->packaging_number ?? ($calculation->job->packaging_number ?? '-') }}</div>
                                </div>
                            </div>
                        </div>
                        <!-- REST OF PACKING INFORMATION (Only on Page 1) -->
                        <div class="section-panel" style="margin-top: -5px; margin-bottom: 10px; border-top: 1px dashed #cbd5e1; border-top-left-radius: 0; border-top-right-radius: 0;">
                            <div style="padding: 10px 12px; background-color: #ffffff; border-radius: 0 0 8px 8px;">
                                <div style="display: grid; grid-template-columns: repeat(5, 1fr); gap: 10px;">
                                    @php
                                        $packerData = $calculation->packer ?? ($calculation->job->packer ?? null);
                                        $packerName = '-';
                                        if (is_string($packerData)) {
                                            $decoded = json_decode($packerData, true);
                                            $packerName = $decoded['name'] ?? $packerData;
                                        } elseif (is_array($packerData)) {
                                            $packerName = $packerData['name'] ?? '-';
                                        } elseif (is_object($packerData)) {
                                            $packerName = $packerData->name ?? '-';
                                        } elseif ($packerData) {
                                            $packerName = $packerData;
                                        }

                                        $assignedData = $calculation->assigned_by ?? ($calculation->job->created_by ?? null);
                                        $assignedName = '-';
                                        if (is_string($assignedData)) {
                                            $decoded = json_decode($assignedData, true);
                                            $assignedName = $decoded['name'] ?? $assignedData;
                                        } elseif (is_array($assignedData)) {
                                            $assignedName = $assignedData['name'] ?? '-';
                                        } elseif (is_object($assignedData)) {
                                            $assignedName = $assignedData->name ?? '-';
                                        } elseif ($assignedData) {
                                            $assignedName = $assignedData;
                                        }
                                    @endphp
                                    <div style="border-right: 1px solid #e2e8f0;">
                                        <div style="font-size: 8px; color: #64748b; margin-bottom: 2px;">
                                            Packer By
                                        </div>
                                        <div style="font-size: 9px; font-weight: 800; color: #0f3566;">{{ $packerName }}</div>
                                    </div>
                                    <div style="border-right: 1px solid #e2e8f0;">
                                        <div style="font-size: 8px; color: #64748b; margin-bottom: 2px;">
                                            <span style="font-family: 'Material Symbols Rounded', sans-serif; font-size: 9px; vertical-align: middle;">person</span> Approved By
                                        </div>
                                        <div style="font-size: 9px; font-weight: 800; color: #0f3566;">{{ $assignedName }}</div>
                                    </div>
                                    <div style="border-right: 1px solid #e2e8f0;">
                                        <div style="font-size: 8px; color: #64748b; margin-bottom: 2px;">Packaging Type</div>
                                        <div style="font-size: 9px; font-weight: 800; color: #0f3566;">{{ $calculation->type_packaging ?? '-' }}</div>
                                    </div>
                                    <div style="border-right: 1px solid #e2e8f0;">
                                        <div style="font-size: 8px; color: #64748b; margin-bottom: 2px;">Status</div>
                                        <div style="font-size: 9px; font-weight: 800; color: #0f3566;">{{ $calculation->status ?? '-' }}</div>
                                    </div>
                                    <div>
                                        <div style="font-size: 8px; color: #64748b; margin-bottom: 2px;">
                                            <span style="font-family: 'Material Symbols Rounded', sans-serif; font-size: 9px; vertical-align: middle;"> Delivery Date
                                        </div>
                                        @php
                                            $deliveryDates = $calculation->items && $calculation->items->count() > 0 
                                                ? $calculation->items->pluck('date_delivery')->filter()->unique()->map(function($d) {
                                                    return \Carbon\Carbon::parse($d)->format('d-m-Y');
                                                })->implode(', ') 
                                                : null;
                                            $deliveryDates = $deliveryDates ?: ($calculation->deadline ? \Carbon\Carbon::parse($calculation->deadline)->format('d-m-Y') : '-');
                                        @endphp
                                        <div style="font-size: 9px; font-weight: 800; color: #0f3566;">{{ $deliveryDates }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>

        @php
            $b_kaki = [
                'status' => $calculation->bawah_kakibalok_status ?? '-',
                'arah' => $calculation->bawah_kakibalok_arahpemasangan ?? '-',
                'material' => $calculation->bawah_kakibalok_material ?? '-'
            ];
            $b_penyangga = [
                'status' => $calculation->bawah_penyanggah_status ?? '-',
                'arah' => $calculation->bawah_penyanggah_arahpemasangan ?? '-',
                'material' => $calculation->bawah_penyanggah_material ?? '-'
            ];
            $b_penutup = [
                'status' => $calculation->bawah_penutup_status ?? '-',
                'arah' => $calculation->bawah_penutup_arahpemasangan ?? '-',
                'material' => $calculation->bawah_penutup_material ?? '-'
            ];
            
            $a_penyangga = [
                'status' => $calculation->atas_penyanggah_status ?? '-',
                'arah' => $calculation->atas_penyanggah_arahpemasangan ?? '-',
                'material' => $calculation->atas_penyanggah_material ?? '-'
            ];
            $a_penutup = [
                'status' => $calculation->atas_penutup_status ?? '-',
                'arah' => $calculation->atas_penutup_arahpemasangan ?? '-',
                'material' => $calculation->atas_penutup_material ?? '-'
            ];

            // Safely get dimension properties (panjang/length, dll)
            $dim_P = $calculation->panjang ?? $calculation->length ?? 0;
            $dim_L = $calculation->lebar ?? $calculation->width ?? 0;
            $dim_T = $calculation->tinggi ?? $calculation->height ?? 0;

            $gap_atas = $ka['gap_atas'] ?? ($calculation->gap_atas ?? 0);
            $gap_bawah = $kb['gap_bawah'] ?? ($calculation->gap_bawah ?? 0);
            $jarak_penyanggah = $kb['jarak_penyanggah'] ?? ($calculation->distance_between_pillars ?? 300);
        @endphp

        <!-- SECTION 2.5: DAFTAR BARANG SO -->
        <div class="section-panel">
            <div class="section-header">Daftar Barang SO</div>
            <div class="section-body" style="padding: 0;">
                <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 10px;">
                    <thead>
                        <tr style="background-color: #f1f5f9;">
                            <th style="padding: 6px 10px; border-bottom: 1px solid #cbd5e1; width: 40px; font-weight: 700;">No</th>
                            <th style="padding: 6px 10px; border-bottom: 1px solid #cbd5e1; font-weight: 700;">No SO</th>
                            <th style="padding: 6px 10px; border-bottom: 1px solid #cbd5e1; font-weight: 700;">Customer</th>
                            <th style="padding: 6px 10px; border-bottom: 1px solid #cbd5e1; font-weight: 700;">No Product</th>
                            <th style="padding: 6px 10px; border-bottom: 1px solid #cbd5e1; font-weight: 700;">Deskripsi Barang</th>
                            <th style="padding: 6px 10px; border-bottom: 1px solid #cbd5e1; text-align: center; width: 80px; font-weight: 700;">Qty</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if($calculation->items && $calculation->items->count() > 0)
                            @foreach($calculation->items as $idx => $item)
                                <tr>
                                    <td style="padding: 6px 10px; border-bottom: 1px solid #e2e8f0; color: #475569;">{{ $idx + 1 }}</td>
                                    <td style="padding: 6px 10px; border-bottom: 1px solid #e2e8f0; color: #0f172a; font-weight: 600;">{{ $item->no_so ?? '-' }}</td>
                                    <td style="padding: 6px 10px; border-bottom: 1px solid #e2e8f0; color: #334155;">{{ $item->customer ?? '-' }}</td>
                                    <td style="padding: 6px 10px; border-bottom: 1px solid #e2e8f0; font-family: monospace; color: #0f172a; font-weight: 600;">{{ $item->no_product ?? '-' }}</td>
                                    <td style="padding: 6px 10px; border-bottom: 1px solid #e2e8f0; color: #334155;">{{ $item->desc_product ?? '-' }}</td>
                                    <td style="padding: 6px 10px; border-bottom: 1px solid #e2e8f0; text-align: center; font-weight: 600; color: #2563eb;">{{ $item->qty ?? 0 }}</td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="6" style="padding: 8px; border-bottom: 1px solid #e2e8f0; text-align: center; color: #64748b;">
                                    Data barang SO tidak ditemukan.
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- SECTION 1: KONFIGURASI AWAL -->
        <div class="config-cards">
            <!-- Card 1 -->
            <div class="cfg-card blue">
                <div class="cfg-header">
                    <span class="badge">1</span> DIMENSI
                </div>
                <div class="cfg-body">
                    <div class="cfg-list-item">
                        <span class="cfg-label">Panjang</span>
                        <span class="cfg-val-box">{{ number_format($dim_P, 0, ',', '.') }} <span class="cfg-unit">mm</span></span>
                    </div>
                    <div class="cfg-list-item">
                        <span class="cfg-label">Lebar</span>
                        <span class="cfg-val-box">{{ number_format($dim_L, 0, ',', '.') }} <span class="cfg-unit">mm</span></span>
                    </div>
                    <div class="cfg-list-item">
                        <span class="cfg-label">Tinggi</span>
                        <span class="cfg-val-box">{{ number_format($dim_T, 0, ',', '.') }} <span class="cfg-unit">mm</span></span>
                    </div>
                </div>
            </div>

            <!-- Card 2 -->
            <div class="cfg-card green">
                <div class="cfg-header">
                    <span class="badge">2</span> KONFIGURASI AREA BAWAH
                </div>
                <div class="cfg-body">
                    <table class="cfg-table">
                        <thead>
                            <tr>
                                <th>KOMPONEN</th>
                                <th>PENGGUNAAN</th>
                                <th>ARAH</th>
                                <th>MATERIAL</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><span style="color: {{ ($b_penyangga['status'] != '-' && $b_penyangga['status'] != 'Exclude' && $b_penyangga['status'] != '0' && $b_penyangga['status'] != 'Not Include') ? '#15803d' : '#94a3b8' }};">&#x2714;</span> Peyanggah</td>
                                <td><span class="val-box">{{ $b_penyangga['status'] == '1' ? 'Include' : ($b_penyangga['status'] == '0' ? 'Exclude' : $b_penyangga['status']) }}</span></td>
                                <td><span class="val-box">{{ $b_penyangga['arah'] ?? '-' }}</span></td>
                                <td><span class="val-box">{{ $b_penyangga['material'] ?? '-' }}</span></td>
                            </tr>
                            <tr>
                                <td><span style="color: {{ ($b_penutup['status'] != '-' && $b_penutup['status'] != 'Tanpa Penutup' && $b_penutup['status'] != '0' && $b_penutup['status'] != 'Not Include') ? '#15803d' : '#94a3b8' }};">&#x2714;</span> Penutup</td>
                                <td><span class="val-box">{{ $b_penutup['status'] ?? 'Tanpa Penutup' }}</span></td>
                                <td><span class="val-box">{{ $b_penutup['arah'] ?? '-' }}</span></td>
                                <td><span class="val-box">{{ $b_penutup['material'] ?? '-' }}</span></td>
                            </tr>
                            <tr>
                                <td><span style="color: {{ ($b_kaki['status'] != '-' && $b_kaki['status'] != 'Exclude' && $b_kaki['status'] != '0' && $b_kaki['status'] != 'Not Include') ? '#15803d' : '#94a3b8' }};">&#x2714;</span> Kaki Balok</td>
                                <td><span class="val-box">{{ $b_kaki['status'] == '1' ? 'Include' : ($b_kaki['status'] == '0' ? 'Exclude' : $b_kaki['status']) }}</span></td>
                                <td><span class="val-box">{{ $b_kaki['arah'] ?? '-' }}</span></td>
                                <td><span class="val-box">{{ $b_kaki['material'] ?? '-' }}</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Card 3 -->
            <div class="cfg-card orange">
                <div class="cfg-header">
                    <span class="badge">3</span> KONFIGURASI AREA ATAS
                </div>
                <div class="cfg-body">
                    <table class="cfg-table">
                        <thead>
                            <tr>
                                <th>KOMPONEN</th>
                                <th>PENGGUNAAN</th>
                                <th>ARAH</th>
                                <th>MATERIAL</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><span style="color: {{ ($a_penyangga['status'] != '-' && $a_penyangga['status'] != 'Exclude' && $a_penyangga['status'] != '0' && $a_penyangga['status'] != 'Not Include') ? '#ea580c' : '#94a3b8' }};">&#x2714;</span> Peyanggah</td>
                                <td><span class="val-box">{{ $a_penyangga['status'] == '1' ? 'Include' : ($a_penyangga['status'] == '0' ? 'Exclude' : $a_penyangga['status']) }}</span></td>
                                <td><span class="val-box">{{ $a_penyangga['arah'] ?? '-' }}</span></td>
                                <td><span class="val-box">{{ $a_penyangga['material'] ?? '-' }}</span></td>
                            </tr>
                            <tr>
                                <td><span style="color: {{ ($a_penutup['status'] != '-' && $a_penutup['status'] != 'Tanpa Penutup' && $a_penutup['status'] != '0' && $a_penutup['status'] != 'Not Include') ? '#ea580c' : '#94a3b8' }};">&#x2714;</span> Penutup</td>
                                <td><span class="val-box">{{ $a_penutup['status'] ?? 'Tanpa Penutup' }}</span></td>
                                <td><span class="val-box">{{ $a_penutup['arah'] ?? '-' }}</span></td>
                                <td><span class="val-box">{{ $a_penutup['material'] ?? '-' }}</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Card 4 -->
            <div class="cfg-card grey">
                <div class="cfg-header">
                    <span class="badge">4</span> KONFIGURASI
                </div>
                <div class="cfg-body">
                    <div class="cfg-list-item">
                        <span class="cfg-label">Jarak Penyanggah Atas</span>
                        <span class="cfg-val-box">{{ number_format($calculation->jarak_penyanggah_atas ?? 0, 0, ',', '.') }} <span class="cfg-unit">mm</span></span>
                    </div>
                    <div class="cfg-list-item">
                        <span class="cfg-label">Jarak Penyanggah Bawah</span>
                        <span class="cfg-val-box">{{ number_format($calculation->jarak_penyanggah_bawah ?? 0, 0, ',', '.') }} <span class="cfg-unit">mm</span></span>
                    </div>
                    <div class="cfg-list-item">
                        <span class="cfg-label">Celah Atas</span>
                        <span class="cfg-val-box">{{ number_format($calculation->gap_atas ?? 0, 0, ',', '.') }} <span class="cfg-unit">mm</span></span>
                    </div>
                    <div class="cfg-list-item">
                        <span class="cfg-label">Celah Bawah</span>
                        <span class="cfg-val-box">{{ number_format($calculation->gap_bawah ?? 0, 0, ',', '.') }} <span class="cfg-unit">mm</span></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- SECTION 3: RINCIAN MATERIAL KAYU -->
        <div class="section-panel">
            <div class="section-header">Rincian Kebutuhan Material Kayu</div>
            <div class="section-body">
                <div class="clearfix" style="display: block;">
                    <!-- COLS 7: List Kebutuhan -->
                    <div style="float: left; width: 58%; padding-right: 15px; box-sizing: border-box;">
                        @php
                            $groupedWood = [];
                            foreach($woodDetails as $d) {
                                $mat = $d->material->code ?? $d->material->kode ?? '-';
                                $t = (float)$d->calculated_thickness;
                                $l = (float)$d->calculated_width;
                                $p = (float)$d->calculated_length;
                                $qty = $d->quantity * $d->side_count;
                                $key = $mat . '|' . $t . '|' . $l . '|' . $p;
                                
                                if (!isset($groupedWood[$key])) {
                                    $groupedWood[$key] = [
                                        'material' => $mat,
                                        't' => $t,
                                        'l' => $l,
                                        'p' => $p,
                                        'qty' => 0
                                    ];
                                }
                                $groupedWood[$key]['qty'] += $qty;
                            }
                        @endphp
                        <table class="data-table" style="margin-top: 0; margin-bottom: 0;">
                            <thead>
                                <tr>
                                    <th class="text-start">Material</th>
                                    <th class="text-center">T (mm)</th>
                                    <th class="text-center">L (mm)</th>
                                    <th class="text-center">P (mm)</th>
                                    <th class="text-center">Qty (pcs)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($groupedWood as $item)
                                    <tr>
                                        <td class="text-start fw-bold" style="font-size: 8px;">{{ $item['material'] }}</td>
                                        <td class="text-center" style="font-size: 8px;">{{ $item['t'] }}</td>
                                        <td class="text-center" style="font-size: 8px;">{{ $item['l'] }}</td>
                                        <td class="text-center" style="font-size: 8px;">{{ $item['p'] }}</td>
                                        <td class="text-center fw-bold" style="font-size: 8px;">{{ $item['qty'] }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-2" style="color: #94a3b8; font-size: 8px;">Belum ada data material kayu</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- COLS 5: Summary Material -->
                    <div style="float: right; width: 42%; box-sizing: border-box;">
                        <div style="background: #f8fafc; border-radius: 8px; padding: 10px; border: 1px solid #cbd5e1; height: 100%;">
                            <div style="font-size: 10px; font-weight: bold; color: #0f3566; margin-bottom: 8px; border-bottom: 1px solid #cbd5e1; padding-bottom: 4px;">Total Kebutuhan per Material</div>
                            <div style="display: flex; flex-direction: column; gap: 6px;">
                                @php
                                    $materialSummary = [];
                                    foreach($woodDetails as $d) {
                                        $mat = $d->material->code ?? $d->material->kode ?? 'Unknown';
                                        if (!isset($materialSummary[$mat])) {
                                            $materialSummary[$mat] = 0;
                                        }
                                        $isTR = str_contains(strtoupper($mat), 'TR');
                                        if ($isTR) {
                                            $sqm = ((float)$d->calculated_length * (float)$d->calculated_width * (float)$d->total_quantity) / 1000000;
                                            $materialSummary[$mat] += $sqm;
                                        } else {
                                            $materialSummary[$mat] += $d->total_length;
                                        }
                                    }
                                    
                                    $colors = ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#ec4899', '#14b8a6', '#f43f5e'];
                                    $c_idx = 0;
                                @endphp

                                @forelse($materialSummary as $mat => $totalM)
                                    @php 
                                        $c = $colors[$c_idx % count($colors)]; 
                                        $c_idx++; 
                                        $unit = str_contains(strtoupper($mat), 'TR') ? 'm²' : 'm';
                                    @endphp
                                    <div style="display: flex; align-items: center; justify-content: space-between; background: #ffffff; padding: 6px 10px; border-radius: 6px; border-left: 4px solid {{ $c }}; box-shadow: 0 1px 2px rgba(0,0,0,0.05);">
                                        <span style="font-size: 8px; font-weight: bold; color: #334155;">{{ $mat }}</span>
                                        <span style="font-size: 9px; font-weight: 900; color: {{ $c }};">{{ number_format($totalM, 2, ',', '.') }} {{ $unit }}</span>
                                    </div>
                                @empty
                                    <div style="text-align: center; color: #94a3b8; padding: 10px 0; font-size: 9px;">Tidak ada rekap</div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <!-- SPLIT ROW: SUMMARY & SIGNATURE -->
        <div class="split-row">
        
            <!-- SECTION 7: TANDA TANGAN -->
            <div class="signature-area">
              
            </div>

        </div>
                    </td>
                </tr>
                <tr style="break-before: page; page-break-before: always;">
                    <td style="border: none; padding: 0;">
        <!-- SECTION 2.5: VISUALISASI POTONGAN MATERIAL -->
        <div class="section-panel">
            <div class="section-header">Visualisasi Potongan Material</div>
            <div class="section-body" style="padding: 0px;">
                @php
                    $materialGroups = [];
                    foreach($woodDetails as $d) {
                        $matName = $d->material->code ?? $d->material->kode ?? '-';
                        $t = (float)$d->calculated_thickness;
                        $w = (float)$d->calculated_width;
                        $l = (float)$d->calculated_length;
                        $qty = $d->quantity * $d->side_count;
                        
                        $key = $matName . '_' . $l . '_' . $w . '_' . $t;
                        if (!isset($materialGroups[$key])) {
                            $materialGroups[$key] = [
                                'mat' => $matName,
                                'l' => $l,
                                'w' => $w,
                                't' => $t,
                                'qty' => 0
                            ];
                        }
                        $materialGroups[$key]['qty'] += $qty;
                    }
                    
                    usort($materialGroups, function($a, $b) {
                        $getCategoryOrder = function($mat) {
                            $matLower = strtolower($mat);
                            if (str_contains($matLower, 'bk') || str_contains($matLower, 'balok')) return 1;
                            if (str_contains($matLower, 'pn') || str_contains($matLower, 'papan')) return 2;
                            if (str_contains($matLower, 'tr') || str_contains($matLower, 'triplek') || str_contains($matLower, 'panel')) return 3;
                            return 4;
                        };
                        
                        $orderA = $getCategoryOrder($a['mat']);
                        $orderB = $getCategoryOrder($b['mat']);
                        if ($orderA !== $orderB) {
                            return $orderA <=> $orderB;
                        }
                        return $a['l'] <=> $b['l'];
                    });
                    
                    $maxL = 0;
                    foreach($materialGroups as $group) {
                        if ($group['l'] > $maxL) {
                            $maxL = $group['l'];
                        }
                    }
                @endphp

                @if(count($materialGroups) > 0)
                    <div style="width: 100%; border: 1px solid #cbd5e1; border-radius: 8px; background: #f8fafc; padding: 6px 0px; display: flex; flex-wrap: wrap; gap: 25px 0; align-items: flex-end; justify-content: flex-start;">
                        @foreach($materialGroups as $group)
                            @php
                                $color = '#c19a6b'; // Default Kayu Papan (and Penyanggah)
                                $matLower = strtolower($group['mat']);
                                if (str_contains($matLower, 'bk') || str_contains($matLower, 'balok')) {
                                    $color = '#5c4033';
                                } else if (str_contains($matLower, 'tr') || str_contains($matLower, 'triplek') || str_contains($matLower, 'panel')) {
                                    $color = '#f3c583';
                                }
                                
                                $scale = $maxL > 0 ? 100 / $maxL : 1;
                                $h = max(20, $group['l'] * $scale);
                                $w = max(4, $group['w'] * $scale * 0.5);
                                
                                $woodType = 'Kayu Papan';
                                if (str_contains($matLower, 'bk') || str_contains($matLower, 'balok')) {
                                    $woodType = 'Balok';
                                } else if (str_contains($matLower, 'tr') || str_contains($matLower, 'triplek') || str_contains($matLower, 'panel')) {
                                    $woodType = 'Triplek / Panel';
                                }
                            @endphp
                            
                            <div style="display: flex; flex-direction: column; align-items: center; min-width: max-content;">
                                <div style="display: flex; gap: 1px; align-items: flex-end; position: relative; padding-left: 20px; padding-top: 15px; padding-bottom: 15px;">
                                    <!-- Dimensi P (Panjang / Height) -->
                                    <div style="position: absolute; left: 5px; top: 15px; bottom: 15px; width: 5px; border-left: 1px solid #3b82f6; border-top: 1px solid #3b82f6; border-bottom: 1px solid #3b82f6;">
                                        <div style="position: absolute; top: 50%; left: -5px; transform: translateY(-50%) rotate(-90deg); font-size: 8px; color: #2563eb; font-weight: bold; white-space: nowrap;">P:{{ $group['l'] }}</div>
                                    </div>
                                    
                                    <!-- Dimensi L (Lebar / Width of 1 item) -->
                                    <div style="position: absolute; left: 20px; top: 5px; width: {{ $w }}px; height: 5px; border-top: 1px solid #3b82f6; border-left: 1px solid #3b82f6; border-right: 1px solid #3b82f6;">
                                        <div style="position: absolute; top: -10px; left: 50%; transform: translateX(-50%); font-size: 8px; color: #2563eb; font-weight: bold;">L:{{ $group['w'] }}</div>
                                    </div>

                                    <!-- Dimensi T (Tebal / Depth) karena ini 2D, letakkan di bawah item pertama -->
                                    <div style="position: absolute; left: 20px; bottom: 0; width: {{ $w }}px; text-align: center; height: 10px;">
                                        <div style="position: absolute; bottom: -2px; left: 50%; transform: translateX(-50%); font-size: 8px; color: #2563eb; font-weight: bold;">T:{{ $group['t'] }}</div>
                                    </div>

                                    @for($i = 0; $i < min($group['qty'], 25); $i++)
                                        <div style="width: {{ $w }}px; height: {{ $h }}px; background-color: {{ $color }}; border: 1px solid rgba(0,0,0,0.15);"></div>
                                    @endfor
                                    @if($group['qty'] > 25)
                                        <div style="font-size: 9px; align-self: center; margin-left: 4px; font-weight: bold; color: #64748b;">+{{ $group['qty'] - 25 }}</div>
                                    @endif
                                </div>
                                
                                <div style="font-size: 9px; font-weight: bold; margin-top: 10px; text-align: center;">
                                    <span style="color: #334155;">{{ $woodType }} - {{ $group['t'] }} mm x {{ $group['w'] }} mm</span><br>
                                    <span style="color: #2563eb;">Qty: {{ $group['qty'] }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="not-included-box" style="height: 100px;">Tidak ada visualisasi material</div>
                @endif
            </div>
        </div>

        <br>
        <!-- SECTION 2: VISUAL ILUSTRASI 3D (6 STEPS) -->
        <div class="section-panel">
            <div class="section-header">Langkah Perakitan (Assembly Steps)</div>
            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px; padding: 10px;">
                
                <div class="visual-card-small">
                    <div class="visual-title">1. Kaki Balok & Pyg Bawah</div>
                    <div class="visual-image-wrapper" style="aspect-ratio: 4/3; height: auto; padding: 4px;">
                        @if(!empty($imgStep1)) <img id="img-step-1" src="{{ $imgStep1 }}" alt="Step 1" style="transform: scale(3.5);"> @else <img id="img-step-1" src="" style="transform: scale(3.5);"> @endif
                    </div>
                </div>

                <div class="visual-card-small">
                    <div class="visual-title">2. + Penutup Bawah</div>
                    <div class="visual-image-wrapper" style="aspect-ratio: 4/3; height: auto; padding: 4px;">
                        @if(!empty($imgStep2)) <img id="img-step-2" src="{{ $imgStep2 }}" alt="Step 2" style="transform: scale(3.5);"> @else <img id="img-step-2" src="" style="transform: scale(3.5);"> @endif
                    </div>
                </div>

                <div class="visual-card-small">
                    <div class="visual-title">3. + Pntp Depan Belakang</div>
                    <div class="visual-image-wrapper" style="aspect-ratio: 4/3; height: auto; padding: 4px;">
                        @if(!empty($imgStep3)) <img id="img-step-3" src="{{ $imgStep3 }}" alt="Step 3" style="transform: scale(3.5);"> @else <img id="img-step-3" src="" style="transform: scale(3.5);"> @endif
                    </div>
                </div>

                <div class="visual-card-small">
                    <div class="visual-title">4. + Pntp Kanan Kiri</div>
                    <div class="visual-image-wrapper" style="aspect-ratio: 4/3; height: auto; padding: 4px;">
                        @if(!empty($imgStep5)) <img id="img-step-4" src="{{ $imgStep5 }}" alt="Step 4" style="transform: scale(3.5);"> @else <img id="img-step-4" src="" style="transform: scale(3.5);"> @endif
                    </div>
                </div>

                <div class="visual-card-small">
                    <div class="visual-title">5. + Penyangga Atas</div>
                    <div class="visual-image-wrapper" style="aspect-ratio: 4/3; height: auto; padding: 4px;">
                        @if(!empty($imgStep7)) <img id="img-step-5" src="{{ $imgStep7 }}" alt="Step 5" style="transform: scale(3.5);"> @else <img id="img-step-5" src="" style="transform: scale(3.5);"> @endif
                    </div>
                </div>

                <div class="visual-card-small">
                    <div class="visual-title">6. + Penutup Atas</div>
                    <div class="visual-image-wrapper" style="aspect-ratio: 4/3; height: auto; padding: 4px;">
                        @if(!empty($imgStep8)) <img id="img-step-6" src="{{ $imgStep8 }}" alt="Step 6" style="transform: scale(3.5);"> @else <img id="img-step-6" src="" style="transform: scale(3.5);"> @endif
                    </div>
                </div>
            </div>

            <div style="display: flex; gap: 10px; margin-top: 10px; width: 100%;">
                <div class="visual-card-small" style="width: 50%;">
                    <div class="visual-title">Hasil Posisi Semua Terexpand</div>
                    <div class="visual-image-wrapper" style="height: 240px; padding: 4px;">
                        @if(!empty($imgFullExploded)) <img id="img-full-exploded" src="{{ $imgFullExploded }}" alt="Full Exploded" style="object-fit: contain; width: 100%; height: 100%; transform: scale(2.0);"> @else <img id="img-full-exploded" src="" style="object-fit: contain; width: 100%; height: 100%; transform: scale(2.0);"> @endif
                    </div>
                </div>
                <div class="visual-card-small" style="width: 50%;">
                    <div class="visual-title">Hasil Akhir Rakitan Utuh</div>
                    <div class="visual-image-wrapper" style="height: 240px; padding: 4px;">
                        @if(!empty($imgFull)) <img id="img-full" src="{{ $imgFull }}" alt="Full Assembled" style="object-fit: contain; width: 100%; height: 100%; transform: scale(2.0);"> @else <img id="img-full" src="" style="object-fit: contain; width: 100%; height: 100%; transform: scale(2.0);"> @endif
                    </div>
                </div>
            </div>
            
        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    @if(isset($isBackgroundGen) && $isBackgroundGen)
        <!-- Hidden iframe for 3D Generation (must be on top to avoid occlusion culling in Chrome) -->
        <iframe id="bgGenIframe" src="{{ route('packaging.calculations.show', ['id' => $calculation->id, 'auto_print' => 'iframe']) }}" style="position: absolute; left: 0px; top: 0px; width: 1024px; height: 768px; opacity: 0.001; pointer-events: none; border: none; z-index: 99999;"></iframe>
        <div id="bgGenOverlay" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: white; z-index: 9998; display: flex; flex-direction: column; align-items: center; justify-content: center;">
            <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem; border: 4px solid #0f3566; border-top: 4px solid transparent; border-radius: 50%; animation: spin 1s linear infinite;"></div>
            <style>@keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }</style>
            <h4 style="margin-top: 20px; color: #0f3566; font-family: sans-serif;">Generating 3D Document...</h4>
            <p style="color: #64748b; font-family: sans-serif;">Please wait, preparing images for print...</p>
        </div>
        <script>
            // Hide the actual print container while generating
            document.querySelector('.print-container').style.display = 'none';
        </script>
    @endif
    
    <!-- Auto Print Script -->
    <script>
        @if(isset($isBackgroundGen) && $isBackgroundGen)
            window.addEventListener('message', function(event) {
                if (event.data && event.data.type === '3d_images_generated') {
                    const images = event.data.images;
                    
                    // Replace all images
                    if (images.imgStep1) document.getElementById('img-step-1').src = images.imgStep1;
                    if (images.imgStep2) document.getElementById('img-step-2').src = images.imgStep2;
                    if (images.imgStep3) document.getElementById('img-step-3').src = images.imgStep3;
                    if (images.imgStep5) document.getElementById('img-step-4').src = images.imgStep5;
                    if (images.imgStep7) document.getElementById('img-step-5').src = images.imgStep7;
                    if (images.imgStep8) document.getElementById('img-step-6').src = images.imgStep8;
                    if (images.imgFullExploded) document.getElementById('img-full-exploded').src = images.imgFullExploded;
                    if (images.imgFull) document.getElementById('img-full').src = images.imgFull;

                    // Remove overlay and iframe
                    document.getElementById('bgGenOverlay').style.display = 'none';
                    let iframe = document.getElementById('bgGenIframe');
                    if (iframe) iframe.remove();
                    
                    document.querySelector('.print-container').style.display = 'block';

                    setTimeout(() => {
                        window.print();
                    }, 500);
                }
            });
        @else
            window.addEventListener('DOMContentLoaded', () => {
                setTimeout(() => {
                    window.print();
                }, 500);
            });
        @endif
    </script>
</body>
</html>
