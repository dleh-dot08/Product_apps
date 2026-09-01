<x-app-layout>
@php
    $settingsPath = base_path('config/packaging_settings.json');
    $packagingSettings = file_exists($settingsPath) ? json_decode(file_get_contents($settingsPath), true) : [
        'manpower_rate' => 10000,
        'nails_price_per_kg' => 25000,
        'nails_weight_per_piece' => 0.025
    ];
    $manpowerRate = $packagingSettings['manpower_rate'] ?? 10000;
    $nailsPricePerKg = $packagingSettings['nails_price_per_kg'] ?? 25000;
    $nailsWeightPerPiece = $packagingSettings['nails_weight_per_piece'] ?? 0.025;

    $costRangka = isset($calculation) ? $calculation->details->whereIn('section', ['Rangka', 'Penyangga'])->sum('subtotal_price') : 0;
    $costPenutup = isset($calculation) ? $calculation->details->filter(function($d) {
        return $d->section === 'Penutup' || ($d->section === 'Bawah' && str_contains($d->part_name, 'Penutup'));
    })->sum('subtotal_price') : 0;
    $costBawah = isset($calculation) ? $calculation->details->filter(function($d) {
        return $d->section === 'Bawah' && !str_contains($d->part_name, 'Penutup');
    })->sum('subtotal_price') : 0;
    $totalCost = $costRangka + $costPenutup + $costBawah;

    $areaKerja = 0;
    if (isset($calculation)) {
        $calcPanjang = $calculation->length ?? $calculation->panjang ?? 0;
        $calcLebar = $calculation->width ?? $calculation->lebar ?? 0;
        $calcTinggi = $calculation->height ?? $calculation->tinggi ?? 0;
        if ($calcPanjang && $calcLebar && $calcTinggi) {
            $P_m = $calcPanjang / 1000;
            $L_m = $calcLebar / 1000;
            $T_m = $calcTinggi / 1000;
            $areaKerja = 2 * (($P_m * $L_m) + ($P_m * $T_m) + ($L_m * $T_m));
        }
    }
@endphp
    <!-- Include Material Symbols for icons -->
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet" />
    <style>
        .crate-page {
            --navy: #0b2a55;
            --navy-2: #123f78;
            --blue: #1769e8;
            --soft: #f4f7fb;
            --line: #e5eaf3;
            --text: #0f172a;
            --muted: #64748b;
        }

        .crate-page .page-header {
            background: #fff;
            border: 1px solid rgba(226, 232, 240, .85);
            border-radius: 16px;
            box-shadow: 0 8px 22px rgba(15, 23, 42, .07);
        }

        .crate-page .btn-soft {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            border-radius: 12px;
            border: 1px solid #d8e0ec;
            background: #fff;
            color: #0f172a;
            padding: 11px 18px;
            font-size: 12px;
            font-weight: 800;
            text-decoration: none;
            box-shadow: 0 6px 14px rgba(15, 23, 42, .06);
            transition: .2s ease;
        }

        .crate-page .btn-soft:hover {
            transform: translateY(-1px);
            background: #f8fafc;
            color: #0b2a55;
        }

        .crate-page .input-card,
        .crate-page .panel-card,
        .crate-page .stat-card {
            background: #fff;
            border: 1px solid rgba(226, 232, 240, .95);
            border-radius: 16px;
            box-shadow: 0 10px 24px rgba(15, 23, 42, .06);
        }

        .crate-page .input-card {
            padding: 12px;
            min-height: 72px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .crate-page .icon-box {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: #eef6ff;
            color: #1769e8;
            flex: 0 0 38px;
        }

        .crate-page .input-title {
            font-size: 11px;
            font-weight: 800;
            color: #64748b;
            margin-bottom: 2px;
        }

        .crate-page .input-value {
            font-size: 18px;
            font-weight: 900;
            color: #0b2a55;
            line-height: 1;
        }

        .crate-page .input-unit {
            font-size: 11px;
            font-weight: 700;
            color: #64748b;
            margin-left: 2px;
        }

        .crate-page .stat-card {
            padding: 16px;
            min-height: 100px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .crate-page .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #0b2a55;
            background: #eaf3ff;
            flex: 0 0 48px;
        }

        .crate-page .stat-icon.green { background: #eafaf1; color: #189a52; }
        .crate-page .stat-icon.orange { background: #fff4e5; color: #f59e0b; }
        .crate-page .stat-icon.purple { background: #f5ecff; color: #7c3aed; }

        .crate-page .stat-label {
            font-size: 12px;
            font-weight: 800;
            color: #64748b;
            margin-bottom: 3px;
        }

        .crate-page .stat-value {
            font-size: 22px;
            font-weight: 900;
            color: #0b2a55;
            line-height: 1.1;
        }

        .crate-page .section-title {
            font-size: 13px;
            font-weight: 900;
            color: #0b2a55;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .crate-page .visual-card {
            min-height: 380px;
            overflow: hidden;
            position: relative;
        }

        .crate-page .visual-stage {
            min-height: 300px;
            background:
                radial-gradient(circle at 50% 48%, rgba(23, 105, 232, .08), transparent 34%),
                linear-gradient(180deg, #ffffff 0%, #f7faff 100%);
            border-radius: 12px;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 16px;
        }

        .crate-page .crate-3d {
            width: min(580px, 100%);
            height: 260px;
        }

        .crate-page .dim-label {
            font-size: 12px;
            font-weight: 900;
            fill: #0b2a55;
        }

        .crate-page .dim-small {
            font-size: 10px;
            font-weight: 800;
            fill: #64748b;
        }

        .crate-page .panel-card {
            padding: 24px;
            background: rgba(255, 255, 255, 0.7);
            border-radius: 16px;
            border: 1px solid rgba(255, 255, 255, 0.9);
            box-shadow: 0 8px 32px rgba(11, 42, 85, 0.05);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .crate-page .panel-card:hover {
            box-shadow: 0 12px 40px rgba(11, 42, 85, 0.08);
        }

        .crate-page .table-modern {
            width: 100%;
            margin: 0;
            border-collapse: separate;
            border-spacing: 0;
            font-size: 13px;
            color: #1e293b;
        }

        .crate-page .table-modern thead th {
            background: rgba(11, 42, 85, 0.04);
            color: #0b2a55;
            font-size: 13px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 12px 10px;
            border-bottom: 2px solid rgba(11, 42, 85, 0.1);
            white-space: nowrap;
        }

        .crate-page .table-modern thead th:first-child { border-top-left-radius: 8px; }
        .crate-page .table-modern thead th:last-child { border-top-right-radius: 8px; border-right: none; }

        .crate-page .table-modern tbody td {
            padding: 12px 10px;
            border-bottom: 1px dashed rgba(11, 42, 85, 0.1);
            vertical-align: middle;
            white-space: nowrap;
            background: rgba(255, 255, 255, 0.4);
            transition: background 0.2s ease;
        }

        .crate-page .table-modern tbody tr:last-child td {
            border-bottom: none;
        }

        .crate-page .table-modern tbody tr:hover td { 
            background: rgba(255, 255, 255, 0.8); 
        }

        .crate-page .table-total td {
            background: rgba(11, 42, 85, 0.03) !important;
            font-weight: 900;
            color: #0b2a55;
            border-top: 2px solid rgba(11, 42, 85, 0.1);
            border-bottom: none;
            border-bottom-left-radius: 8px;
            border-bottom-right-radius: 8px;
        }

        .crate-page .badge-soft {
            display: inline-flex;
            align-items: center;
            border-radius: 999px;
            padding: 3px 8px;
            background: #eaf3ff;
            color: #1769e8;
            font-size: 10px;
            font-weight: 800;
        }

        .crate-page .cost-list {
            margin: 0;
            padding: 0;
            list-style: none;
        }

        .crate-page .cost-list li {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            padding: 12px 16px;
            background: rgba(255, 255, 255, 0.5);
            border-radius: 10px;
            margin-bottom: 8px;
            border: 1px solid rgba(255, 255, 255, 0.8);
            font-size: 12px;
            transition: all 0.2s ease;
        }

        .crate-page .cost-list li:hover {
            background: rgba(255, 255, 255, 0.9);
            box-shadow: 0 4px 12px rgba(11, 42, 85, 0.04);
            transform: translateY(-1px);
        }

        .crate-page .cost-list li:last-child { margin-bottom: 0; }

        .crate-page .info-note {
            font-size: 10px;
            color: #64748b;
            margin-top: 8px;
        }

        .crate-page .table-responsive {
            border-radius: 10px;
        }

        @media (max-width: 991.98px) {
            .crate-page .visual-card { min-height: auto; }
            .crate-page .visual-stage { min-height: 240px; }
        }
        .crate-page .bg-navy { background-color: var(--navy) !important; color: #fff; }
        .crate-page .text-navy { color: var(--navy) !important; }
        
        .crate-page .btn-navy {
            background-color: var(--navy);
            color: #fff;
            border: none;
            transition: all 0.3s ease;
        }
        
        .crate-page .btn-navy:hover {
            background-color: var(--navy-2);
            transform: translateY(-2px);
            box-shadow: 0 8px 16px rgba(11, 42, 85, 0.2) !important;
            color: #fff;
        }
        
        .crate-page .style-card-container {
            border-radius: 16px;
            border: 1px solid rgba(226, 232, 240, 0.8) !important;
            transition: all 0.3s ease;
            overflow: hidden;
        }
        
        .crate-page .style-card-container:hover {
            box-shadow: 0 12px 28px rgba(15, 23, 42, 0.08) !important;
        }
        
        .crate-page .container-input-group {
            border-color: #e2e8f0 !important;
            box-shadow: inset 0 2px 4px rgba(15, 23, 42, 0.01);
            background: #fafcff !important;
        }
        
        .crate-page .group-item {
            transition: all 0.2s ease;
            position: relative;
        }
        
        .crate-page .group-item:hover {
            background-color: #f1f5f9;
            border-radius: 8px;
        }
        
        .crate-page .border-end-md {
            border-right: 1px solid #e2e8f0;
        }
        
        @media (max-width: 767.98px) {
            .crate-page .border-end-md {
                border-right: none;
                border-bottom: 1px solid #e2e8f0;
            }
        }
        
        .crate-page .custom-input, .crate-page .custom-select {
            background: transparent !important;
            outline: none;
            height: auto;
            min-height: 24px;
        }
        
        .crate-page .custom-input:focus, .crate-page .custom-select:focus {
            box-shadow: none;
        }
        
        .crate-page .custom-input::-webkit-outer-spin-button,
        .crate-page .custom-input::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }
        
        .crate-page .custom-select {
            cursor: pointer;
            padding-right: 1.5rem !important;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%2364748b' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M2 5l6 6 6-6'/%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 0 center;
            background-size: 12px 12px;
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
        }

        .crate-page .custom-input[readonly], .crate-page .custom-select:disabled {
            color: var(--navy) !important;
            opacity: 1; /* Fix text opacity on iOS/disabled elements */
            cursor: default;
        }

        .crate-page .custom-select:disabled {
            background-image: none !important; /* Hide arrow when disabled */
        }

        .crate-page .editable-mode {
            background-color: #eef6ff !important;
            border-bottom: 2px solid var(--blue) !important;
            border-radius: 4px 4px 0 0;
            padding-left: 6px !important;
            padding-right: 6px !important;
            color: var(--navy) !important;
            transition: all 0.2s ease;
        }



        .crate-page .legend {
            position: absolute;
            z-index: 4;
            left: 16px;
            bottom: 16px;
            max-width: 350px;
            padding: 11px 13px;
            border: 1px solid rgba(151, 170, 194, .35);
            border-radius: 13px;
            color: #52657a;
            background: rgba(255,255,255,.90);
            box-shadow: 0 10px 25px rgba(34, 59, 90, .10);
            backdrop-filter: blur(10px);
            font-size: 10px;
            line-height: 1.55;
            pointer-events: none;
        }

        .crate-page .legend strong {
            color: #173d68;
        }

        .crate-page .status {
            position: absolute;
            z-index: 4;
            right: 16px;
            bottom: 16px;
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 9px 12px;
            border: 1px solid rgba(151, 170, 194, .35);
            border-radius: 999px;
            color: #51657a;
            background: rgba(255,255,255,.92);
            box-shadow: 0 10px 25px rgba(34, 59, 90, .10);
            font-size: 10px;
            font-weight: 800;
            pointer-events: none;
        }

        .crate-page .status-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #0e8a63;
            box-shadow: 0 0 0 4px rgba(14,138,99,.12);
        }

        .crate-page .table-select {
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%230b2a55'%3E%3Cpath d='M7 10l5 5 5-5H7z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 8px center;
            background-size: 16px;
            background-color: #ffffff;
            border: 1px solid rgba(11, 42, 85, 0.2);
            border-radius: 8px;
            color: #0b2a55;
            font-size: 11px;
            font-weight: 700;
            padding: 4px 28px 4px 10px;
            transition: border-color 0.25s ease, box-shadow 0.25s ease;
            cursor: pointer;
            box-shadow: 0 1px 2px rgba(11, 42, 85, 0.05);
            display: inline-block;
            width: auto;
            min-height: 28px;
        }

        .crate-page .table-select:focus {
            outline: none;
            border-color: #1769e8;
            box-shadow: 0 0 0 3px rgba(23, 105, 232, 0.15), 0 1px 2px rgba(11, 42, 85, 0.05);
        }

        .crate-page .table-select:hover {
            border-color: rgba(11, 42, 85, 0.4);
        }

        @media print {
            /* Hide non-printable components */
            nav, 
            header,
            footer,
            .btn,
            .btn-soft,
            .visual-stage,
            .legend,
            .status,
            .btn-group,
            .visual-card,
            #btn-edit-config,
            #btn-edit,
            #btn-cancel,
            #btn-save,
            .d-flex.justify-content-end.gap-2 {
                display: none !important;
            }
            
            body, .crate-page {
                background: #fff !important;
                color: #000 !important;
                padding: 0 !important;
                margin: 0 !important;
            }

            .container-fluid, .row, .col-12, .col-lg-7, .col-lg-5, .col-xl-2, .col-xl-7, .col-xl-3 {
                width: 100% !important;
                max-width: 100% !important;
                flex: 0 0 100% !important;
                margin: 0 !important;
                padding: 0 !important;
            }

            .panel-card, .stat-card, .page-header {
                box-shadow: none !important;
                border: 1px solid #cbd5e1 !important;
                border-radius: 8px !important;
                margin-bottom: 15px !important;
                page-break-inside: avoid;
            }

            .table-modern {
                border-collapse: collapse !important;
                width: 100% !important;
            }

            .table-modern th, .table-modern td {
                border: 1px solid #cbd5e1 !important;
                padding: 6px 10px !important;
                font-size: 14px !important;
            }
        }
    

        /* =========================================================
           CONFIGURATION CARD — COLOR & LAYOUT UPDATE
           Added without removing the existing styles above.
           ========================================================= */

        .crate-page .configuration-card {
            overflow: hidden;
            border: 1px solid rgba(226, 232, 240, 0.95) !important;
            border-radius: 18px;
            background: rgba(255, 255, 255, 0.98);
            box-shadow: 0 14px 32px rgba(15, 23, 42, 0.08) !important;
        }

        .crate-page .configuration-section {
            min-width: 0;
            padding: 0 18px 18px;
            background: #ffffff;
            border-right: 1px solid #e2e8f0;
        }

        .crate-page .configuration-section:last-child {
            border-right: 0;
        }

        .crate-page .configuration-heading {
            min-height: 70px;
            margin-left: -18px;
            margin-right: -18px;
            margin-bottom: 14px !important;
            padding: 18px;
            font-size: 13px;
            border-bottom: 1px solid rgba(226, 232, 240, 0.85);
        }

        .crate-page .configuration-section-dimension .configuration-heading {
            background: linear-gradient(135deg, #f4f8ff 0%, #eef5ff 100%);
        }

        .crate-page .configuration-section-general .configuration-heading {
            background: linear-gradient(135deg, #f2f7ff 0%, #eaf3ff 100%);
        }

        .crate-page .configuration-section-bottom .configuration-heading {
            background: linear-gradient(135deg, #f4fbf7 0%, #edf9f3 100%);
        }

        .crate-page .configuration-section-top .configuration-heading {
            background: linear-gradient(135deg, #f8f5ff 0%, #f2efff 100%);
        }

        .crate-page .configuration-section .badge {
            width: 30px !important;
            height: 30px !important;
            flex: 0 0 30px;
            font-size: 14px !important;
            box-shadow: 0 6px 14px rgba(11, 42, 85, 0.16);
        }

        .crate-page .configuration-section-bottom .badge {
            background: linear-gradient(135deg, #0b5ed7, #1689c9) !important;
        }

        .crate-page .configuration-section-top .badge {
            background: linear-gradient(135deg, #2439b8, #4f46e5) !important;
        }

        .crate-page .configuration-content {
            border: 0 !important;
            border-radius: 0 !important;
            background: transparent !important;
            box-shadow: none !important;
        }

        .crate-page .configuration-content > .group-item {
            min-height: 57px;
            padding-top: 11px !important;
            padding-bottom: 11px !important;
            border-color: #e6ebf2 !important;
            border-radius: 0 !important;
            background: transparent;
        }

        .crate-page .configuration-section-bottom .configuration-content > .group-item,
        .crate-page .configuration-section-top .configuration-content > .group-item {
            min-height: 83px;
        }

        .crate-page .configuration-content > .group-item:hover {
            background: rgba(244, 248, 255, 0.75);
        }

        .crate-page .configuration-section-bottom .configuration-content > .group-item:hover {
            background: rgba(237, 249, 243, 0.8);
        }

        .crate-page .configuration-section-top .configuration-content > .group-item:hover {
            background: rgba(245, 242, 255, 0.82);
        }

        .crate-page .configuration-content .material-symbols-rounded {
            width: 38px;
            height: 38px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex: 0 0 38px;
            border-radius: 10px;
            color: #0b2a55 !important;
            background: #f1f5f9;
        }

        .crate-page .configuration-section-dimension .configuration-content .material-symbols-rounded,
        .crate-page .configuration-section-general .configuration-content .material-symbols-rounded {
            background: #edf5ff;
            color: #1769e8 !important;
        }

        .crate-page .configuration-section-bottom .configuration-content .material-symbols-rounded {
            background: #edf9f3;
            color: #149466 !important;
        }

        .crate-page .configuration-section-top .configuration-content .material-symbols-rounded {
            background: #f2efff;
            color: #5b4bc4 !important;
        }

        .crate-page .configuration-content small {
            color: #475569 !important;
            font-weight: 700;
        }

        .crate-page .configuration-content .custom-input,
        .crate-page .configuration-content .custom-select,
        .crate-page .configuration-content .matrix-select {
            min-height: 38px;
            border: 1px solid #d8e2ef !important;
            border-radius: 8px !important;
            background-color: #ffffff !important;
            padding-left: 10px !important;
            padding-right: 28px !important;
            color: #0b2a55 !important;
            font-weight: 700 !important;
            box-shadow: 0 2px 5px rgba(15, 23, 42, 0.025) !important;
        }

        .crate-page .configuration-content .custom-input {
            padding-right: 10px !important;
        }

        .crate-page .configuration-content .custom-select:disabled,
        .crate-page .configuration-content .matrix-select:disabled {
            opacity: 1;
            background-color: #ffffff !important;
            background-image: var(--bs-form-select-bg-img) !important;
            background-repeat: no-repeat !important;
            background-position: right .65rem center !important;
            background-size: 12px 12px !important;
        }

        .crate-page .configuration-content .matrix-select {
            flex: 1 1 0;
            width: 0;
            min-width: 0;
            font-size: 11px !important;
        }

        .crate-page .configuration-content .d-flex.gap-1 {
            gap: 8px !important;
        }

        .crate-page .configuration-footer {
            padding: 0 18px 18px;
        }

        .crate-page .configuration-footer #btn-edit-config {
            width: 190px !important;
            border-radius: 10px !important;
            background: linear-gradient(135deg, #123f78, #1769e8);
            box-shadow: 0 8px 18px rgba(23, 105, 232, 0.22) !important;
        }

        .crate-page .configuration-footer #btn-edit-config:hover {
            background: linear-gradient(135deg, #0b2a55, #155bd0);
        }

        @media (max-width: 1399.98px) {
            .crate-page .configuration-grid {
                grid-template-columns: 1fr 1fr;
            }

            .crate-page .configuration-section:nth-child(2) {
                border-right: 0;
            }

            .crate-page .configuration-section:nth-child(-n+2) {
                border-bottom: 1px solid #e2e8f0;
            }
        }

        @media (max-width: 767.98px) {
            .crate-page .configuration-grid {
                grid-template-columns: 1fr;
            }

            .crate-page .configuration-section,
            .crate-page .configuration-section:nth-child(2) {
                border-right: 0;
                border-bottom: 1px solid #e2e8f0;
            }

            .crate-page .configuration-section:last-child {
                border-bottom: 0;
            }

            .crate-page .configuration-content .d-flex.gap-1 {
                flex-wrap: wrap;
            }

            .crate-page .configuration-content .matrix-select {
                width: 100%;
                flex: 1 1 100%;
            }
        }

    

        /* =========================================================
           INDUSTRIAL GREY THEME OVERRIDE
           Hanya mengubah tampilan konfigurasi. Kode lama tetap utuh.
           ========================================================= */

        .crate-page .configuration-card {
            border: 1px solid #cfd6de !important;
            border-radius: 10px !important;
            background: #f7f8fa !important;
            box-shadow: 0 14px 30px rgba(30, 41, 59, 0.12) !important;
        }

        .crate-page .configuration-section {
            background: #ffffff !important;
            border-right: 1px solid #d5dbe2 !important;
        }

        .crate-page .configuration-heading {
            min-height: 72px;
            color: #ffffff !important;
            background: linear-gradient(135deg, #1e3d61ff 0%, #13007eff 100%) !important;
            border-bottom: 1px solid #515963 !important;
            box-shadow: inset 0 4px 0 #9ea6ae;
        }

        .crate-page .configuration-section-dimension .configuration-heading {
            box-shadow: inset 0 4px 0 #f59e0b;
        }

        .crate-page .configuration-section-general .configuration-heading {
            box-shadow: inset 0 4px 0 #64748b;
        }

        .crate-page .configuration-section-bottom .configuration-heading {
            box-shadow: inset 0 4px 0 #4c9a3f;
        }

        .crate-page .configuration-section-top .configuration-heading {
            box-shadow: inset 0 4px 0 #f97316;
        }

        .crate-page .configuration-heading,
        .crate-page .configuration-heading * {
            color: #ffffff !important;
        }

        .crate-page .configuration-section .badge {
            border-radius: 5px !important;
            box-shadow: 0 4px 10px rgba(15, 23, 42, 0.22) !important;
        }

        .crate-page .configuration-section-dimension .badge {
            background: linear-gradient(135deg, #f59e0b, #d97706) !important;
        }

        .crate-page .configuration-section-general .badge {
            background: linear-gradient(135deg, #64748b, #475569) !important;
        }

        .crate-page .configuration-section-bottom .badge {
            background: linear-gradient(135deg, #55a946, #3f8535) !important;
        }

        .crate-page .configuration-section-top .badge {
            background: linear-gradient(135deg, #f97316, #dd5d0b) !important;
        }

        .crate-page .configuration-content {
            background:
                linear-gradient(180deg, rgba(248, 250, 252, 0.96), rgba(255, 255, 255, 0.98)) !important;
        }

        .crate-page .configuration-content > .group-item {
            border-color: #d8dde3 !important;
            background: transparent !important;
        }

        .crate-page .configuration-content > .group-item:hover {
            background: #f1f3f5 !important;
        }

        .crate-page .configuration-content .material-symbols-rounded {
            border: 1px solid #d7dce2;
            border-radius: 7px !important;
            background: linear-gradient(145deg, #f2f4f6, #e4e8ec) !important;
            color: #475569 !important;
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.75);
        }

        .crate-page .configuration-section-bottom .configuration-content .material-symbols-rounded {
            color: #3f8535 !important;
            border-color: #d5e5d2;
            background: linear-gradient(145deg, #f2f8f0, #e5f0e2) !important;
        }

        .crate-page .configuration-section-top .configuration-content .material-symbols-rounded {
            color: #dd5d0b !important;
            border-color: #f0ded2;
            background: linear-gradient(145deg, #fff7f1, #f4e8df) !important;
        }

        .crate-page .configuration-content small {
            color: #374151 !important;
            font-weight: 400 !important;
            font-size: 13px !important;
        }

        .crate-page .configuration-content .custom-input,
        .crate-page .configuration-content .custom-select,
        .crate-page .configuration-content .matrix-select {
            min-height: 40px;
            border: 1px solid #cbd2d9 !important;
            border-radius: 6px !important;
            background-color: #ffffff !important;
            color: #26313d !important;
            font-weight: 400 !important;
            font-size: 13px !important;
            box-shadow:
                inset 0 1px 2px rgba(15, 23, 42, 0.04),
                0 1px 2px rgba(15, 23, 42, 0.03) !important;
        }

        .crate-page .configuration-content .custom-input:focus,
        .crate-page .configuration-content .custom-select:focus,
        .crate-page .configuration-content .matrix-select:focus {
            border-color: #7d8791 !important;
            box-shadow: 0 0 0 3px rgba(100, 116, 139, 0.14) !important;
        }

        .crate-page .configuration-content .custom-select:disabled,
        .crate-page .configuration-content .matrix-select:disabled {
            opacity: 1;
            background-color: #ffffff !important;
            color: #26313d !important;
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
            background-image: none !important;
            padding-right: 0.5rem !important;
        }

        .crate-page .configuration-footer {
            padding-top: 14px !important;
            background: linear-gradient(180deg, #f3f5f7 0%, #e8ebef 100%) !important;
            border-top: 1px solid #d3d8de !important;
        }

        .crate-page .configuration-footer #btn-edit-config {
            border: 1px solid #606a74 !important;
            border-bottom: 3px solid #f59e0b !important;
            border-radius: 6px !important;
            background: linear-gradient(135deg, #1e3d61ff 0%, #13007eff 100%) !important;
            color: #ffffff !important;
            box-shadow: 0 7px 14px rgba(51, 65, 85, 0.18) !important;
        }

        .crate-page .configuration-footer #btn-edit-config:hover {
            background: linear-gradient(135deg, #1e3d61ff 0%, #13007eff 100%) !important;
            transform: translateY(-1px);
        }

        .crate-page .configuration-footer #btn-cancel-config {
            border-radius: 6px !important;
            border-color: #c7cdd4 !important;
            background: #ffffff !important;
        }

        .crate-page .configuration-footer #btn-save-config {
            border-radius: 6px !important;
            border-bottom: 3px solid #2f6f2a !important;
            background: linear-gradient(135deg, #58a94a, #3f8535) !important;
        }

        @media (max-width: 1399.98px) {
            .crate-page .configuration-section:nth-child(-n+2) {
                border-bottom-color: #d5dbe2 !important;
            }
        }

        @media (max-width: 767.98px) {
            .crate-page .configuration-section,
            .crate-page .configuration-section:nth-child(2) {
                border-bottom-color: #d5dbe2 !important;
            }
        }

    

        /* =========================================================
           CLEAN COLOR MOCKUP OVERRIDE
           Layout: Dimensi | Konfigurasi | Area Bawah | Area Atas
           Kode style lain di atas tetap dipertahankan.
           ========================================================= */

        .crate-page .configuration-card {
            overflow: hidden;
            border: 1px solid #e2e8f0 !important;
            border-radius: 12px !important;
            background: #ffffff !important;
            box-shadow: 0 8px 24px rgba(15, 23, 42, 0.08) !important;
        }

        .crate-page .configuration-grid {
            display: grid;
            grid-template-columns: minmax(210px, 1.5fr) minmax(430px, 3fr) minmax(380px, 3fr) minmax(210px, 1.2fr);
            align-items: stretch;
            gap: 0;
        }

        .crate-page .configuration-section {
            min-width: 0;
            padding: 0 14px 5px;
            background: #ffffff !important;
            border-top: 4px solid #cbd5e1;
            border-right: 1px solid #e2e8f0 !important;
        }

        .crate-page .configuration-section:last-child {
            border-right: 0 !important;
        }

        .crate-page .configuration-section-dimension {
            border-top-color: #1769e8;
        }

        .crate-page .configuration-section-general {
            border-top-color: #64748b;
        }

        .crate-page .configuration-section-bottom {
            border-top-color: #10b981;
        }

        .crate-page .configuration-section-top {
            border-top-color: #f59e0b;
        }

        .crate-page .configuration-heading,
        .crate-page .configuration-section-dimension .configuration-heading,
        .crate-page .configuration-section-general .configuration-heading,
        .crate-page .configuration-section-bottom .configuration-heading,
        .crate-page .configuration-section-top .configuration-heading {
            min-height: 44px;
            margin: 0 -14px 6px !important;
            padding: 10px 14px;
            border: 0 !important;
            border-bottom: 1px solid #f1f5f9 !important;
            background: #ffffff !important;
            box-shadow: none !important;
            color: #0f2748 !important;
            font-size: 13px;
            line-height: 1.2;
        }

        .crate-page .configuration-heading * {
            color: inherit !important;
        }

        .crate-page .configuration-section .badge {
            width: 22px !important;
            height: 22px !important;
            flex: 0 0 22px;
            border: 0 !important;
            border-radius: 5px !important;
            box-shadow: none !important;
            color: #ffffff !important;
            font-size: 13px !important;
            font-weight: 800;
        }

        .crate-page .configuration-section-dimension .badge {
            background: #1769e8 !important;
        }

        .crate-page .configuration-section-general .badge {
            background: #64748b !important;
        }

        .crate-page .configuration-section-bottom .badge {
            background: #10b981 !important;
        }

        .crate-page .configuration-section-top .badge {
            background: #f59e0b !important;
        }

        .crate-page .configuration-content,
        .crate-page .configuration-content.container-input-group {
            overflow: visible !important;
            border: 0 !important;
            border-radius: 0 !important;
            background: #ffffff !important;
            box-shadow: none !important;
        }

        .crate-page .configuration-content > .group-item,
        .crate-page .configuration-content-narrow > .group-item {
            min-height: 57px;
            padding: 0 !important;
            border: 0 !important;
            border-bottom: 0 !important;
            border-radius: 8px !important;
            background: transparent !important;
        }

        .crate-page .configuration-content > .group-item:hover,
        .crate-page .configuration-section-bottom .configuration-content > .group-item:hover,
        .crate-page .configuration-section-top .configuration-content > .group-item:hover {
            background: #f8fafc !important;
        }

        .crate-page .configuration-content .material-symbols-rounded {
            width: 30px;
            height: 30px;
            flex: 0 0 30px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: 0 !important;
            border-radius: 6px !important;
            box-shadow: none !important;
        }

        .crate-page .configuration-section-dimension .configuration-content .material-symbols-rounded {
            background: #eff6ff !important;
            color: #1769e8 !important;
        }

        .crate-page .configuration-section-general .configuration-content .material-symbols-rounded {
            background: #f1f5f9 !important;
            color: #64748b !important;
        }

        .crate-page .configuration-section-bottom .configuration-content .material-symbols-rounded {
            background: #ecfdf5 !important;
            color: #10b981 !important;
        }

        .crate-page .configuration-section-top .configuration-content .material-symbols-rounded {
            background: #fff7ed !important;
            color: #f59e0b !important;
        }

        .crate-page .configuration-content small {
            color: #334155 !important;
            font-size: 13px !important;
            font-weight: 700 !important;
            line-height: 1.2;
        }

        .crate-page .configuration-unit-field {
            display: grid;
            grid-template-columns: minmax(0, 1fr) auto;
            align-items: center;
            gap: 8px;
        }

        .crate-page .configuration-unit {
            min-width: 22px;
            margin: 0 !important;
            color: #64748b !important;
            font-size: 13px !important;
            font-weight: 700;
            text-align: left;
        }

        .crate-page .configuration-content .custom-input,
        .crate-page .configuration-content .custom-select,
        .crate-page .configuration-content .matrix-select {
            width: 100% !important;
            min-width: 0 !important;
            min-height: 34px;
            height: 34px;
            border: 1px solid #e2e8f0 !important;
            border-radius: 5px !important;
            background-color: #ffffff !important;
            color: #0f172a !important;
            font-size: 13px !important;
            font-weight: 500 !important;
            line-height: 1.2;
            padding: 6px 26px 6px 9px !important;
            box-shadow: 0 1px 2px rgba(15, 23, 42, 0.03) !important;
        }

        .crate-page .configuration-content .custom-input {
            padding-right: 9px !important;
        }

        .crate-page .configuration-content .custom-input:focus,
        .crate-page .configuration-content .custom-select:focus,
        .crate-page .configuration-content .matrix-select:focus {
            border-color: #94a3b8 !important;
            box-shadow: 0 0 0 3px rgba(148, 163, 184, 0.14) !important;
        }

        .crate-page .configuration-content .custom-input[readonly],
        .crate-page .configuration-content .custom-select:disabled,
        .crate-page .configuration-content .matrix-select:disabled {
            opacity: 1;
            color: #0f172a !important;
            background-color: #ffffff !important;
        }

        .crate-page .configuration-content .custom-select,
        .crate-page .configuration-content .matrix-select,
        .crate-page .configuration-content .custom-select:disabled,
        .crate-page .configuration-content .matrix-select:disabled {
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%2364748b' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M3 6l5 5 5-5'/%3e%3c/svg%3e") !important;
            background-repeat: no-repeat !important;
            background-position: right 8px center !important;
            background-size: 10px 10px !important;
        }

        .crate-page .configuration-content .fixed-status-select,
        .crate-page .configuration-content .fixed-status-select:disabled {
            color: #94a3b8 !important;
            background-color: #f8fafc !important;
            background-image: none !important;
            cursor: default;
        }

        .crate-page .configuration-matrix {
            display: block !important;
        }

        .crate-page .configuration-matrix-header,
        .crate-page .configuration-matrix-row {
            display: grid !important;
            grid-template-columns: 1.1fr 1.1fr 1.3fr 1.2fr;
            align-items: center;
            column-gap: 9px;
            width: 100%;
        }

        .crate-page .configuration-matrix-header {
            min-height: 28px;
            padding: 4px 0 5px;
            color: #334155;
            font-size: 12px;
            font-weight: 800;
            letter-spacing: .03em;
            text-transform: uppercase;
        }

        .crate-page .configuration-matrix-header span {
            min-width: 0;
            white-space: nowrap;
        }

        .crate-page .configuration-matrix-row,
        .crate-page .configuration-section-bottom .configuration-content > .configuration-matrix-row,
        .crate-page .configuration-section-top .configuration-content > .configuration-matrix-row {
            min-height: 57px;
            padding: 0 !important;
            border: 0 !important;
            border-bottom: 0 !important;
        }

        .crate-page .configuration-component {
            min-width: 0;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .crate-page .configuration-component small {
            overflow: hidden;
            color: #1e293b !important;
            font-size: 13px !important;
            font-weight: 700 !important;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .crate-page .configuration-cell {
            min-width: 0;
        }

        .crate-page .configuration-footer {
            min-height: 48px;
            padding: 6px 14px 12px !important;
            border: 0 !important;
            background: #ffffff !important;
        }

        .crate-page .configuration-footer #btn-edit-config {
            width: 138px !important;
            min-height: 32px;
            border: 0 !important;
            border-radius: 4px !important;
            background: #17375e !important;
            color: #ffffff !important;
            box-shadow: 0 4px 10px rgba(23, 55, 94, 0.16) !important;
        }

        .crate-page .configuration-footer #btn-edit-config:hover {
            background: #102d50 !important;
            transform: translateY(-1px);
        }

        .crate-page .configuration-footer #btn-cancel-config,
        .crate-page .configuration-footer #btn-save-config {
            min-height: 32px;
            border-radius: 4px !important;
        }

        @media (max-width: 1499.98px) {
            .crate-page .configuration-grid {
                grid-template-columns: minmax(200px, 1fr) minmax(200px, 1fr);
            }

            .crate-page .configuration-section:nth-child(2) {
                border-right: 0 !important;
            }

            .crate-page .configuration-section:nth-child(-n+2) {
                border-bottom: 1px solid #e2e8f0;
            }
        }

        @media (max-width: 767.98px) {
            .crate-page .configuration-grid {
                grid-template-columns: 1fr;
            }

            .crate-page .configuration-section,
            .crate-page .configuration-section:nth-child(2) {
                border-right: 0 !important;
                border-bottom: 1px solid #e2e8f0;
            }

            .crate-page .configuration-section:last-child {
                border-bottom: 0;
            }

            .crate-page .configuration-matrix-header {
                display: none !important;
            }

            .crate-page .configuration-matrix-row,
            .crate-page .configuration-section-bottom .configuration-content > .configuration-matrix-row,
            .crate-page .configuration-section-top .configuration-content > .configuration-matrix-row {
                grid-template-columns: 1fr;
                row-gap: 7px;
                padding: 10px 0 !important;
                border-bottom: 1px solid #f1f5f9 !important;
            }

            .crate-page .configuration-matrix-row:last-child {
                border-bottom: 0 !important;
            }

            .crate-page .configuration-component small {
                white-space: normal;
            }
        }

        /* 3D FLOATING TOOLBAR STYLES */
        .crate-page .floating-visual-toolbar {
            position: absolute;
            top: 12px;
            left: 12px;
            right: 12px;
            z-index: 10;
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(12px) saturate(190%);
            -webkit-backdrop-filter: blur(12px) saturate(190%);
            border: 1px solid rgba(226, 232, 240, 0.8);
            border-radius: 14px;
            box-shadow: 0 10px 30px -10px rgba(0, 0, 0, 0.08);
            padding: 8px 12px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        @media (max-width: 991px) {
            .crate-page .floating-visual-toolbar {
                position: relative;
                top: 0;
                left: 0;
                right: 0;
                margin: 12px;
                background: #ffffff;
                border-radius: 14px;
                box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            }
        }

        .crate-page .floating-visual-toolbar:hover {
            background: rgba(255, 255, 255, 0.95);
            border-color: rgba(59, 130, 246, 0.25);
            box-shadow: 0 12px 35px -8px rgba(0, 0, 0, 0.1);
        }

        .crate-page .toolbar-pill-group {
            display: flex;
            align-items: center;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            padding: 2px 4px;
            border-radius: 100px;
            height: 36px;
            box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.02);
        }

        .crate-page .toolbar-pill-label {
            font-size: 10px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            padding: 4px 10px;
            border-radius: 100px;
            margin-right: 4px;
            user-select: none;
        }

        .crate-page .toolbar-pill-label.atas {
            background-color: #eff6ff;
            color: #3b82f6;
        }

        .crate-page .toolbar-pill-label.bawah {
            background-color: #f0fdf4;
            color: #22c55e;
        }

        .crate-page .btn-check + .btn-outline-primary.btn-xs {
            padding: 3px 12px;
            font-size: 11px;
            font-weight: 600;
            border-radius: 100px;
            color: #475569;
            border: none;
            background: transparent;
            transition: all 0.2s ease;
            height: 28px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .crate-page .btn-check:checked + .btn-outline-primary.btn-xs {
            background-color: #ffffff !important;
            color: #0f172a !important;
            font-weight: 700;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.08), 0 0 1px rgba(0, 0, 0, 0.1);
        }

        .crate-page .btn-check + .btn-outline-primary.btn-xs:hover {
            color: #1e293b;
            background-color: rgba(0, 0, 0, 0.03);
        }

        /* Camera and option buttons */
        .crate-page .toolbar-btn-link {
            border: none;
            background: transparent;
            font-size: 11px;
            font-weight: 600;
            color: #64748b;
            padding: 3px 12px;
            border-radius: 100px;
            height: 28px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            transition: all 0.2s ease;
        }

        .crate-page .toolbar-btn-link:hover {
            color: #0f172a;
            background-color: rgba(0, 0, 0, 0.03);
        }

        .crate-page .toolbar-btn-link.active {
            background-color: #ffffff !important;
            color: #3b82f6 !important;
            font-weight: 700;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.08), 0 0 1px rgba(0, 0, 0, 0.1);
        }

        .crate-page .toolbar-btn-link#dimensionBtn.active,
        .crate-page .toolbar-btn-link#gridBtn.active {
            color: #8b5cf6 !important;
            background-color: #ffffff !important;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.08), 0 0 1px rgba(0, 0, 0, 0.1);
        }

        /* =========================================================
           DARK MODE OVERRIDES (All except Visual 2D/3D)
           ========================================================= */
        [data-bs-theme="dark"] .crate-page {
            --navy: #f8fafc;
            --navy-2: #cbd5e1;
            --blue: #3b82f6;
            --soft: #1e293b;
            --line: #334155;
            --text: #f8fafc;
            --muted: #94a3b8;
            color: var(--text);
        }

        [data-bs-theme="dark"] .crate-page .page-header,
        [data-bs-theme="dark"] .crate-page .input-card,
        [data-bs-theme="dark"] .crate-page .stat-card,
        [data-bs-theme="dark"] .crate-page .panel-card,
        [data-bs-theme="dark"] .crate-page .configuration-card,
        [data-bs-theme="dark"] .crate-page .configuration-section,
        [data-bs-theme="dark"] .crate-page .legend {
            background: #1e293b !important;
            border-color: #334155 !important;
            color: #f8fafc !important;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.15) !important;
        }

        [data-bs-theme="dark"] .crate-page .bg-white,
        [data-bs-theme="dark"] .crate-page .btn-soft {
            background: #1e293b !important;
            border-color: #334155 !important;
            color: #f8fafc !important;
        }

        [data-bs-theme="dark"] .crate-page .text-navy,
        [data-bs-theme="dark"] .crate-page .text-dark,
        [data-bs-theme="dark"] .crate-page .input-value,
        [data-bs-theme="dark"] .crate-page .stat-value,
        [data-bs-theme="dark"] .crate-page .section-title,
        [data-bs-theme="dark"] .crate-page .configuration-heading {
            color: #f8fafc !important;
        }

        [data-bs-theme="dark"] .crate-page .table-modern thead th {
            background: #0f172a !important;
            color: #f8fafc !important;
            border-color: #334155 !important;
        }
        
        [data-bs-theme="dark"] .crate-page .table-modern tbody td {
            background: #1e293b !important;
            border-color: #334155 !important;
            color: #cbd5e1 !important;
        }

        [data-bs-theme="dark"] .crate-page .table-modern tbody tr:hover td {
            background: #334155 !important;
        }

        [data-bs-theme="dark"] .crate-page .table-total td {
            background: #0f172a !important;
            color: #f8fafc !important;
            border-color: #334155 !important;
        }
        
        [data-bs-theme="dark"] .crate-page .cost-list li {
            background: #0f172a !important;
            border-color: #334155 !important;
            color: #cbd5e1 !important;
        }

        [data-bs-theme="dark"] .crate-page .cost-list li:hover {
            background: #1e293b !important;
            border-color: #475569 !important;
        }

        [data-bs-theme="dark"] .crate-page .custom-input,
        [data-bs-theme="dark"] .crate-page .custom-select,
        [data-bs-theme="dark"] .crate-page .matrix-select,
        [data-bs-theme="dark"] .crate-page .table-select {
            background-color: #0f172a !important;
            border-color: #334155 !important;
            color: #f8fafc !important;
        }

        [data-bs-theme="dark"] .crate-page .configuration-heading {
            background: #1e293b !important;
            border-bottom: 1px solid #334155 !important;
        }

        [data-bs-theme="dark"] .crate-page .border-end-md,
        [data-bs-theme="dark"] .crate-page .configuration-section {
            border-color: #334155 !important;
        }

        [data-bs-theme="dark"] .crate-page .group-item:hover,
        [data-bs-theme="dark"] .crate-page .configuration-content > .group-item:hover {
            background-color: #334155 !important;
        }

        [data-bs-theme="dark"] .crate-page .editable-mode {
            background-color: #0f172a !important;
            border-color: var(--blue) !important;
            color: #f8fafc !important;
        }

        [data-bs-theme="dark"] .crate-page .configuration-content .material-symbols-rounded,
        [data-bs-theme="dark"] .crate-page .icon-box {
            background: #334155 !important;
            color: #94a3b8 !important;
        }

        [data-bs-theme="dark"] .crate-page .stat-icon {
            background: #334155 !important;
        }
        [data-bs-theme="dark"] .crate-page .stat-icon.green { background: rgba(24, 154, 82, 0.2) !important; color: #4ade80 !important; }
        [data-bs-theme="dark"] .crate-page .stat-icon.orange { background: rgba(245, 158, 11, 0.2) !important; color: #fbbf24 !important; }
        [data-bs-theme="dark"] .crate-page .stat-icon.purple { background: rgba(124, 58, 237, 0.2) !important; color: #a78bfa !important; }

        /* Ensure visual 3D and 2D stages remain intact with their default colors */
        [data-bs-theme="dark"] .crate-page .visual-stage,
        [data-bs-theme="dark"] .crate-page .visual-stage .dim-label,
        [data-bs-theme="dark"] .crate-page .visual-stage .dim-small {
            /* No dark mode overrides for visual stage internals */
        }
        
        [data-bs-theme="dark"] .crate-page .visual-stage .badge,
        [data-bs-theme="dark"] .crate-page .visual-stage .text-dark {
            color: #0f172a !important;
        }
        
        [data-bs-theme="dark"] .crate-page .visual-stage .bg-white {
            background-color: #ffffff !important;
        }
        
        [data-bs-theme="dark"] .crate-page .visual-stage .border-light {
            border-color: #f8f9fa !important;
        }


        /* =========================================================
           COMPLETE DARK MODE — CALCULATION PAGE + MODALS + 2D/3D UI
           Supports Bootstrap, custom class themes, and JS auto mode.
           ========================================================= */
        .crate-page {
            --crate-page-bg: transparent;
            --crate-shell-bg: rgba(255, 255, 255, .42);
            --crate-surface: #ffffff;
            --crate-surface-2: #f8fafc;
            --crate-surface-3: #f1f5f9;
            --crate-text: #0f172a;
            --crate-text-soft: #334155;
            --crate-muted: #64748b;
            --crate-border: #e2e8f0;
            --crate-border-strong: #cbd5e1;
            --crate-shadow: 0 10px 28px rgba(15, 23, 42, .07);
            --crate-input-bg: #ffffff;
            --crate-hover: #f8fafc;
            --crate-modal-backdrop: rgba(15, 23, 42, .62);
        }

        .crate-page .crate-workspace-shell {
            background: var(--crate-shell-bg);
            border: 1px solid rgba(255, 255, 255, .52);
            box-shadow: 0 4px 6px rgba(0, 0, 0, .02);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
        }

        .crate-page .crate-page-title {
            color: var(--crate-text) !important;
        }

        :is(
            [data-bs-theme="dark"],
            [data-theme="dark"],
            html.dark,
            body.dark,
            body.dark-mode,
            body.theme-dark
        ) .crate-page,
        .crate-page.packaging-dark {
            --navy: #f8fafc;
            --navy-2: #cbd5e1;
            --blue: #60a5fa;
            --soft: #172033;
            --line: #334155;
            --text: #f8fafc;
            --muted: #94a3b8;

            --crate-page-bg: transparent;
            --crate-shell-bg: rgba(10, 17, 30, .78);
            --crate-surface: #111827;
            --crate-surface-2: #172033;
            --crate-surface-3: #1e293b;
            --crate-text: #f8fafc;
            --crate-text-soft: #cbd5e1;
            --crate-muted: #94a3b8;
            --crate-border: rgba(148, 163, 184, .20);
            --crate-border-strong: rgba(148, 163, 184, .34);
            --crate-shadow: 0 14px 38px rgba(0, 0, 0, .28);
            --crate-input-bg: #0f172a;
            --crate-hover: #1e293b;
            color: var(--crate-text);
        }

        :is(
            [data-bs-theme="dark"],
            [data-theme="dark"],
            html.dark,
            body.dark,
            body.dark-mode,
            body.theme-dark
        ) .crate-page .crate-workspace-shell,
        .crate-page.packaging-dark .crate-workspace-shell {
            background: var(--crate-shell-bg) !important;
            border-color: rgba(148, 163, 184, .17) !important;
            box-shadow: 0 18px 55px rgba(0, 0, 0, .22) !important;
        }

        :is(
            [data-bs-theme="dark"],
            [data-theme="dark"],
            html.dark,
            body.dark,
            body.dark-mode,
            body.theme-dark
        ) .crate-page :is(
            .page-header,
            .card,
            .card-header,
            .card-body,
            .panel-card,
            .stat-card,
            .input-card,
            .configuration-card,
            .configuration-section,
            .configuration-content,
            .configuration-footer,
            .style-card-container,
            .material-visual-card,
            .material-visual-header
        ),
        .crate-page.packaging-dark :is(
            .page-header,
            .card,
            .card-header,
            .card-body,
            .panel-card,
            .stat-card,
            .input-card,
            .configuration-card,
            .configuration-section,
            .configuration-content,
            .configuration-footer,
            .style-card-container,
            .material-visual-card,
            .material-visual-header
        ) {
            background-color: var(--crate-surface) !important;
            border-color: var(--crate-border) !important;
            color: var(--crate-text) !important;
        }

        :is(
            [data-bs-theme="dark"],
            [data-theme="dark"],
            html.dark,
            body.dark,
            body.dark-mode,
            body.theme-dark
        ) .crate-page :is(.bg-white, .bg-light),
        .crate-page.packaging-dark :is(.bg-white, .bg-light) {
            background-color: var(--crate-surface) !important;
        }

        :is(
            [data-bs-theme="dark"],
            [data-theme="dark"],
            html.dark,
            body.dark,
            body.dark-mode,
            body.theme-dark
        ) .crate-page :is(
            .text-dark,
            .text-navy,
            .section-title,
            .input-value,
            .stat-value,
            .configuration-heading,
            .configuration-content small,
            .configuration-component small,
            h1, h2, h3, h4, h5, h6,
            label,
            strong
        ),
        .crate-page.packaging-dark :is(
            .text-dark,
            .text-navy,
            .section-title,
            .input-value,
            .stat-value,
            .configuration-heading,
            .configuration-content small,
            .configuration-component small,
            h1, h2, h3, h4, h5, h6,
            label,
            strong
        ) {
            color: var(--crate-text) !important;
        }

        :is(
            [data-bs-theme="dark"],
            [data-theme="dark"],
            html.dark,
            body.dark,
            body.dark-mode,
            body.theme-dark
        ) .crate-page :is(.text-muted, .text-secondary, small, .info-note, .input-title, .input-unit),
        .crate-page.packaging-dark :is(.text-muted, .text-secondary, small, .info-note, .input-title, .input-unit) {
            color: var(--crate-muted) !important;
        }

        :is(
            [data-bs-theme="dark"],
            [data-theme="dark"],
            html.dark,
            body.dark,
            body.dark-mode,
            body.theme-dark
        ) .crate-page :is(.border, .border-top, .border-bottom, .border-end, .border-start),
        .crate-page.packaging-dark :is(.border, .border-top, .border-bottom, .border-end, .border-start) {
            border-color: var(--crate-border) !important;
        }

        :is(
            [data-bs-theme="dark"],
            [data-theme="dark"],
            html.dark,
            body.dark,
            body.dark-mode,
            body.theme-dark
        ) .crate-page :is(.btn-soft, .btn-light, .btn-outline-secondary),
        .crate-page.packaging-dark :is(.btn-soft, .btn-light, .btn-outline-secondary) {
            color: var(--crate-text) !important;
            background: var(--crate-surface-2) !important;
            border-color: var(--crate-border-strong) !important;
            box-shadow: 0 6px 16px rgba(0, 0, 0, .16) !important;
        }

        :is(
            [data-bs-theme="dark"],
            [data-theme="dark"],
            html.dark,
            body.dark,
            body.dark-mode,
            body.theme-dark
        ) .crate-page :is(.btn-soft, .btn-light, .btn-outline-secondary):hover,
        .crate-page.packaging-dark :is(.btn-soft, .btn-light, .btn-outline-secondary):hover {
            color: #ffffff !important;
            background: var(--crate-surface-3) !important;
            border-color: #475569 !important;
        }

        :is(
            [data-bs-theme="dark"],
            [data-theme="dark"],
            html.dark,
            body.dark,
            body.dark-mode,
            body.theme-dark
        ) .crate-page :is(
            .form-control,
            .form-select,
            .custom-input,
            .custom-select,
            .matrix-select,
            .table-select,
            textarea,
            input
        ),
        .crate-page.packaging-dark :is(
            .form-control,
            .form-select,
            .custom-input,
            .custom-select,
            .matrix-select,
            .table-select,
            textarea,
            input
        ) {
            color: var(--crate-text) !important;
            background-color: var(--crate-input-bg) !important;
            border-color: var(--crate-border-strong) !important;
            color-scheme: dark;
        }

        :is(
            [data-bs-theme="dark"],
            [data-theme="dark"],
            html.dark,
            body.dark,
            body.dark-mode,
            body.theme-dark
        ) .crate-page :is(input, textarea)::placeholder,
        .crate-page.packaging-dark :is(input, textarea)::placeholder {
            color: #64748b !important;
            opacity: 1;
        }

        :is(
            [data-bs-theme="dark"],
            [data-theme="dark"],
            html.dark,
            body.dark,
            body.dark-mode,
            body.theme-dark
        ) .crate-page :is(.form-control, .form-select, .custom-input, .custom-select, .matrix-select):focus,
        .crate-page.packaging-dark :is(.form-control, .form-select, .custom-input, .custom-select, .matrix-select):focus {
            border-color: #60a5fa !important;
            box-shadow: 0 0 0 3px rgba(96, 165, 250, .16) !important;
        }

        :is(
            [data-bs-theme="dark"],
            [data-theme="dark"],
            html.dark,
            body.dark,
            body.dark-mode,
            body.theme-dark
        ) .crate-page :is(input, select, textarea):disabled,
        :is(
            [data-bs-theme="dark"],
            [data-theme="dark"],
            html.dark,
            body.dark,
            body.dark-mode,
            body.theme-dark
        ) .crate-page :is(input, select, textarea)[readonly],
        .crate-page.packaging-dark :is(input, select, textarea):disabled,
        .crate-page.packaging-dark :is(input, select, textarea)[readonly] {
            color: #cbd5e1 !important;
            background-color: #172033 !important;
            opacity: 1 !important;
        }

        :is(
            [data-bs-theme="dark"],
            [data-theme="dark"],
            html.dark,
            body.dark,
            body.dark-mode,
            body.theme-dark
        ) .crate-page option,
        .crate-page.packaging-dark option {
            color: #f8fafc;
            background: #0f172a;
        }

        :is(
            [data-bs-theme="dark"],
            [data-theme="dark"],
            html.dark,
            body.dark,
            body.dark-mode,
            body.theme-dark
        ) .crate-page input:-webkit-autofill,
        :is(
            [data-bs-theme="dark"],
            [data-theme="dark"],
            html.dark,
            body.dark,
            body.dark-mode,
            body.theme-dark
        ) .crate-page input:-webkit-autofill:hover,
        :is(
            [data-bs-theme="dark"],
            [data-theme="dark"],
            html.dark,
            body.dark,
            body.dark-mode,
            body.theme-dark
        ) .crate-page input:-webkit-autofill:focus,
        .crate-page.packaging-dark input:-webkit-autofill,
        .crate-page.packaging-dark input:-webkit-autofill:hover,
        .crate-page.packaging-dark input:-webkit-autofill:focus {
            -webkit-text-fill-color: #f8fafc !important;
            -webkit-box-shadow: 0 0 0 1000px #0f172a inset !important;
            caret-color: #f8fafc;
        }

        :is(
            [data-bs-theme="dark"],
            [data-theme="dark"],
            html.dark,
            body.dark,
            body.dark-mode,
            body.theme-dark
        ) .crate-page .configuration-heading,
        .crate-page.packaging-dark .configuration-heading {
            background: var(--crate-surface) !important;
            border-bottom-color: var(--crate-border) !important;
        }

        :is(
            [data-bs-theme="dark"],
            [data-theme="dark"],
            html.dark,
            body.dark,
            body.dark-mode,
            body.theme-dark
        ) .crate-page :is(.configuration-content > .group-item:hover, .configuration-matrix-row:hover, .group-item:hover),
        .crate-page.packaging-dark :is(.configuration-content > .group-item:hover, .configuration-matrix-row:hover, .group-item:hover) {
            background: var(--crate-hover) !important;
        }

        :is(
            [data-bs-theme="dark"],
            [data-theme="dark"],
            html.dark,
            body.dark,
            body.dark-mode,
            body.theme-dark
        ) .crate-page .table-modern thead th,
        .crate-page.packaging-dark .table-modern thead th {
            color: #e2e8f0 !important;
            background: #0f172a !important;
            border-color: var(--crate-border) !important;
        }

        :is(
            [data-bs-theme="dark"],
            [data-theme="dark"],
            html.dark,
            body.dark,
            body.dark-mode,
            body.theme-dark
        ) .crate-page .table-modern tbody td,
        .crate-page.packaging-dark .table-modern tbody td {
            color: #cbd5e1 !important;
            background: var(--crate-surface) !important;
            border-color: var(--crate-border) !important;
        }

        :is(
            [data-bs-theme="dark"],
            [data-theme="dark"],
            html.dark,
            body.dark,
            body.dark-mode,
            body.theme-dark
        ) .crate-page .table-modern tbody tr:hover td,
        .crate-page.packaging-dark .table-modern tbody tr:hover td {
            background: var(--crate-hover) !important;
        }

        :is(
            [data-bs-theme="dark"],
            [data-theme="dark"],
            html.dark,
            body.dark,
            body.dark-mode,
            body.theme-dark
        ) .crate-page :is(.cost-list li, .table-total td),
        .crate-page.packaging-dark :is(.cost-list li, .table-total td) {
            color: #e2e8f0 !important;
            background: #0f172a !important;
            border-color: var(--crate-border) !important;
        }

        /* 3D main visual surface */
        :is(
            [data-bs-theme="dark"],
            [data-theme="dark"],
            html.dark,
            body.dark,
            body.dark-mode,
            body.theme-dark
        ) .crate-page #crate-canvas-container,
        .crate-page.packaging-dark #crate-canvas-container {
            background: #0b1220 !important;
            border-color: var(--crate-border) !important;
        }

        :is(
            [data-bs-theme="dark"],
            [data-theme="dark"],
            html.dark,
            body.dark,
            body.dark-mode,
            body.theme-dark
        ) .crate-page :is(.floating-visual-toolbar, .toolbar-pill-group),
        .crate-page.packaging-dark :is(.floating-visual-toolbar, .toolbar-pill-group) {
            color: #e2e8f0 !important;
            background: rgba(15, 23, 42, .88) !important;
            border-color: rgba(148, 163, 184, .24) !important;
            box-shadow: 0 12px 32px rgba(0, 0, 0, .28) !important;
        }

        :is(
            [data-bs-theme="dark"],
            [data-theme="dark"],
            html.dark,
            body.dark,
            body.dark-mode,
            body.theme-dark
        ) .crate-page :is(.toolbar-btn-link, .btn-check + .btn-outline-primary.btn-xs),
        .crate-page.packaging-dark :is(.toolbar-btn-link, .btn-check + .btn-outline-primary.btn-xs) {
            color: #94a3b8 !important;
        }

        :is(
            [data-bs-theme="dark"],
            [data-theme="dark"],
            html.dark,
            body.dark,
            body.dark-mode,
            body.theme-dark
        ) .crate-page :is(.toolbar-btn-link.active, .btn-check:checked + .btn-outline-primary.btn-xs),
        .crate-page.packaging-dark :is(.toolbar-btn-link.active, .btn-check:checked + .btn-outline-primary.btn-xs) {
            color: #ffffff !important;
            background: #334155 !important;
            box-shadow: 0 5px 13px rgba(0, 0, 0, .30) !important;
        }

        /* Potongan material 2D/orthographic visual */
        :is(
            [data-bs-theme="dark"],
            [data-theme="dark"],
            html.dark,
            body.dark,
            body.dark-mode,
            body.theme-dark
        ) .crate-page :is(.material-visual-stage, #material-sorting-container),
        .crate-page.packaging-dark :is(.material-visual-stage, #material-sorting-container) {
            background: #0b1220 !important;
            border-color: var(--crate-border) !important;
        }

        :is(
            [data-bs-theme="dark"],
            [data-theme="dark"],
            html.dark,
            body.dark,
            body.dark-mode,
            body.theme-dark
        ) .crate-page :is(.material-visual-legend, .material-visual-footer),
        .crate-page.packaging-dark :is(.material-visual-legend, .material-visual-footer) {
            color: #cbd5e1 !important;
            background: #111827 !important;
            border-color: var(--crate-border) !important;
        }

        :is(
            [data-bs-theme="dark"],
            [data-theme="dark"],
            html.dark,
            body.dark,
            body.dark-mode,
            body.theme-dark
        ) .crate-page #material-labels-container .bg-white,
        .crate-page.packaging-dark #material-labels-container .bg-white {
            color: #f8fafc !important;
            background: rgba(17, 24, 39, .96) !important;
            border-color: #475569 !important;
        }

        :is(
            [data-bs-theme="dark"],
            [data-theme="dark"],
            html.dark,
            body.dark,
            body.dark-mode,
            body.theme-dark
        ) .crate-page #material-labels-container .text-dark,
        .crate-page.packaging-dark #material-labels-container .text-dark {
            color: #f8fafc !important;
        }

        /* Included modals */
        :is(
            [data-bs-theme="dark"],
            [data-theme="dark"],
            html.dark,
            body.dark,
            body.dark-mode,
            body.theme-dark
        ) :is(#materialDetailModal, #validasiModal, #costResumeModal) .modal-content,
        body.packaging-auto-dark :is(#materialDetailModal, #validasiModal, #costResumeModal) .modal-content {
            color: #f8fafc !important;
            background: #111827 !important;
            border-color: rgba(148, 163, 184, .20) !important;
        }

        :is(
            [data-bs-theme="dark"],
            [data-theme="dark"],
            html.dark,
            body.dark,
            body.dark-mode,
            body.theme-dark
        ) :is(#materialDetailModal, #validasiModal, #costResumeModal) :is(.modal-header, .modal-body, .modal-footer, .card, .card-header, .card-body, .bg-white, .bg-light),
        body.packaging-auto-dark :is(#materialDetailModal, #validasiModal, #costResumeModal) :is(.modal-header, .modal-body, .modal-footer, .card, .card-header, .card-body, .bg-white, .bg-light) {
            color: #f8fafc !important;
            background: #111827 !important;
            border-color: rgba(148, 163, 184, .20) !important;
        }

        :is(
            [data-bs-theme="dark"],
            [data-theme="dark"],
            html.dark,
            body.dark,
            body.dark-mode,
            body.theme-dark
        ) :is(#materialDetailModal, #validasiModal, #costResumeModal) :is(.text-dark, .text-navy, h1, h2, h3, h4, h5, h6, strong),
        body.packaging-auto-dark :is(#materialDetailModal, #validasiModal, #costResumeModal) :is(.text-dark, .text-navy, h1, h2, h3, h4, h5, h6, strong) {
            color: #f8fafc !important;
        }

        :is(
            [data-bs-theme="dark"],
            [data-theme="dark"],
            html.dark,
            body.dark,
            body.dark-mode,
            body.theme-dark
        ) :is(#materialDetailModal, #validasiModal, #costResumeModal) :is(.text-muted, .text-secondary, small),
        body.packaging-auto-dark :is(#materialDetailModal, #validasiModal, #costResumeModal) :is(.text-muted, .text-secondary, small) {
            color: #94a3b8 !important;
        }

        :is(
            [data-bs-theme="dark"],
            [data-theme="dark"],
            html.dark,
            body.dark,
            body.dark-mode,
            body.theme-dark
        ) :is(#materialDetailModal, #validasiModal, #costResumeModal) :is(.form-control, .form-select, input, select, textarea),
        body.packaging-auto-dark :is(#materialDetailModal, #validasiModal, #costResumeModal) :is(.form-control, .form-select, input, select, textarea) {
            color: #f8fafc !important;
            background: #0f172a !important;
            border-color: rgba(148, 163, 184, .30) !important;
            color-scheme: dark;
        }

        :is(
            [data-bs-theme="dark"],
            [data-theme="dark"],
            html.dark,
            body.dark,
            body.dark-mode,
            body.theme-dark
        ) :is(#materialDetailModal, #validasiModal, #costResumeModal) .btn-close,
        body.packaging-auto-dark :is(#materialDetailModal, #validasiModal, #costResumeModal) .btn-close {
            filter: invert(1) grayscale(100%) brightness(200%);
        }

        :is(
            [data-bs-theme="dark"],
            [data-theme="dark"],
            html.dark,
            body.dark,
            body.dark-mode,
            body.theme-dark
        ) :is(#materialDetailModal, #validasiModal, #costResumeModal) .nav-tabs,
        body.packaging-auto-dark :is(#materialDetailModal, #validasiModal, #costResumeModal) .nav-tabs {
            border-color: rgba(148, 163, 184, .20) !important;
        }

        :is(
            [data-bs-theme="dark"],
            [data-theme="dark"],
            html.dark,
            body.dark,
            body.dark-mode,
            body.theme-dark
        ) :is(#materialDetailModal, #validasiModal, #costResumeModal) .nav-link,
        body.packaging-auto-dark :is(#materialDetailModal, #validasiModal, #costResumeModal) .nav-link {
            color: #94a3b8 !important;
        }

        :is(
            [data-bs-theme="dark"],
            [data-theme="dark"],
            html.dark,
            body.dark,
            body.dark-mode,
            body.theme-dark
        ) :is(#materialDetailModal, #validasiModal, #costResumeModal) .nav-link.active,
        body.packaging-auto-dark :is(#materialDetailModal, #validasiModal, #costResumeModal) .nav-link.active {
            color: #ffffff !important;
            background: #1e293b !important;
            border-color: rgba(148, 163, 184, .20) rgba(148, 163, 184, .20) #1e293b !important;
        }

        body.packaging-auto-dark .crate-page {
            --navy: #f8fafc;
            --navy-2: #cbd5e1;
            --blue: #60a5fa;
            --soft: #172033;
            --line: #334155;
            --text: #f8fafc;
            --muted: #94a3b8;
            --crate-shell-bg: rgba(10, 17, 30, .78);
            --crate-surface: #111827;
            --crate-surface-2: #172033;
            --crate-surface-3: #1e293b;
            --crate-text: #f8fafc;
            --crate-text-soft: #cbd5e1;
            --crate-muted: #94a3b8;
            --crate-border: rgba(148, 163, 184, .20);
            --crate-border-strong: rgba(148, 163, 184, .34);
            --crate-input-bg: #0f172a;
            --crate-hover: #1e293b;
            color: var(--crate-text);
        }

        @media print {
            .crate-page,
            .crate-page.packaging-dark,
            body.packaging-auto-dark .crate-page {
                --crate-surface: #ffffff;
                --crate-text: #000000;
                --crate-muted: #475569;
                color: #000000 !important;
            }
        }


        /* =========================================================
           PACKING INFORMATION — VISUAL CARD + TABLE COLLAPSE
           ========================================================= */
        .crate-page .packing-information-card {
            overflow: hidden;
            border: 1px solid #e2e8f0 !important;
            border-radius: 18px !important;
            background: #ffffff;
            box-shadow: 0 12px 30px rgba(15, 23, 42, .07) !important;
        }

        .crate-page .packing-header-icon,
        .crate-page .packing-reference-icon,
        .crate-page .packing-meta-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex: 0 0 auto;
        }

        .crate-page .packing-header-icon {
            width: 32px;
            height: 32px;
            border-radius: 9px;
            color: #1769e8;
            background: #eef6ff;
        }

        .crate-page .packing-header-icon .material-symbols-rounded {
            font-size: 18px;
        }

        .crate-page .packing-reference-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 18px;
            padding: 8px 6px 10px;
        }

        .crate-page .packing-reference-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            color: #1769e8;
            background: #eef6ff;
        }

        .crate-page .packing-table-toggle {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            min-height: 38px;
            padding: 8px 12px;
            border: 1px solid #cdd9e8;
            border-radius: 10px;
            color: #1769e8;
            background: #ffffff;
            font-size: 11px;
            font-weight: 800;
            white-space: nowrap;
            cursor: pointer;
            transition: .2s ease;
        }

        .crate-page .packing-table-toggle:hover {
            border-color: #1769e8;
            background: #f7fbff;
            box-shadow: 0 6px 14px rgba(23, 105, 232, .10);
            transform: translateY(-1px);
        }

        .crate-page .packing-table-toggle .material-symbols-rounded {
            font-size: 18px;
            transition: transform .2s ease;
        }

        .crate-page .packing-item-count {
            padding: 3px 7px;
            border-radius: 999px;
            color: #475569;
            background: #f1f5f9;
            font-size: 10px;
        }

        .crate-page .packing-table-shell {
            overflow: hidden;
            border: 1px solid #dbe3ee;
            border-radius: 12px;
            background: #ffffff;
        }

        .crate-page .packing-detail-table {
            width: 100%;
            color: #172033;
            font-size: 12px;
        }

        .crate-page .packing-detail-table thead th {
            padding: 13px 14px;
            border: 0;
            border-right: 1px solid #e2e8f0;
            border-bottom: 1px solid #dbe3ee;
            color: #102443;
            background: #f8fafc;
            font-size: 11px;
            font-weight: 900;
            text-transform: none;
            white-space: nowrap;
        }

        .crate-page .packing-detail-table thead th:last-child,
        .crate-page .packing-detail-table tbody td:last-child {
            border-right: 0;
        }

        .crate-page .packing-detail-table tbody td {
            padding: 14px;
            border: 0;
            border-right: 1px solid #edf1f6;
            border-bottom: 1px solid #edf1f6;
            background: #ffffff;
            vertical-align: middle;
        }

        .crate-page .packing-detail-table tbody tr:last-child td {
            border-bottom: 0;
        }

        .crate-page .packing-detail-table tbody tr:hover td {
            background: #fbfdff;
        }

        .crate-page .packing-number-column { width: 68px; }
        .crate-page .packing-qty-column { width: 90px; }
        .crate-page .packing-customer-cell { min-width: 280px; }

        .crate-page .packing-row-number {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 30px;
            height: 30px;
            border-radius: 8px;
            color: #172033;
            background: #f1f5f9;
            font-weight: 900;
        }

        .crate-page .packing-meta-grid {
            display: grid;
            grid-template-columns: repeat(5, minmax(0, 1fr));
            margin-top: 10px;
            padding: 18px 8px;
            border: 1px solid #e5eaf1;
            border-radius: 12px;
            background: #ffffff;
            box-shadow: 0 6px 18px rgba(15, 23, 42, .035);
        }

        .crate-page .packing-meta-item {
            min-width: 0;
            display: flex;
            align-items: center;
            gap: 11px;
            padding: 0 16px;
            border-right: 1px solid #e2e8f0;
        }

        .crate-page .packing-meta-item:last-child {
            border-right: 0;
        }

        .crate-page .packing-meta-icon {
            width: 42px;
            height: 42px;
            border-radius: 10px;
        }

        .crate-page .packing-meta-icon .material-symbols-rounded { font-size: 23px; }
        .crate-page .packing-meta-icon.is-purple { color: #8b5cf6; background: #f4edff; }
        .crate-page .packing-meta-icon.is-orange { color: #f59e0b; background: #fff6e5; }
        .crate-page .packing-meta-icon.is-blue { color: #1769e8; background: #eef6ff; }
        .crate-page .packing-meta-icon.is-green { color: #10b981; background: #eafaf4; }
        .crate-page .packing-meta-icon.is-blue-soft { color: #1769e8; background: #eff6ff; }

        .crate-page .packing-meta-item small {
            display: block;
            margin-bottom: 4px;
            color: #64748b;
            font-size: 10px;
            font-weight: 700;
            line-height: 1.1;
        }

        .crate-page .packing-meta-item strong {
            display: block;
            overflow: hidden;
            color: #102443;
            font-size: 12px;
            font-weight: 900;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .crate-page .packing-status-badge {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            width: fit-content;
            padding: 5px 10px;
            border: 1px solid transparent;
            border-radius: 999px;
            font-size: 9px;
            font-weight: 900;
            line-height: 1;
            white-space: nowrap;
        }

        .crate-page .packing-status-dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: currentColor;
        }

        .crate-page .packing-status-badge.is-ready {
            color: #059669;
            border-color: #bbf7d0;
            background: #ecfdf5;
        }

        .crate-page .packing-status-badge.is-progress {
            color: #d97706;
            border-color: #fde68a;
            background: #fffbeb;
        }

        .crate-page .packing-status-badge.is-danger {
            color: #dc2626;
            border-color: #fecaca;
            background: #fef2f2;
        }

        .crate-page .packing-status-badge.is-draft {
            color: #1769e8;
            border-color: #bfdbfe;
            background: #eff6ff;
        }

        .crate-page .min-w-0 { min-width: 0; }

        @media (max-width: 1199.98px) {
            .crate-page .packing-meta-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
                row-gap: 18px;
            }

            .crate-page .packing-meta-item {
                border-right: 0;
            }
        }

        @media (max-width: 767.98px) {
            .crate-page .packing-reference-row {
                align-items: flex-start;
                flex-direction: column;
            }

            .crate-page .packing-table-toggle {
                width: 100%;
            }

            .crate-page .packing-meta-grid {
                grid-template-columns: 1fr;
                row-gap: 0;
                padding: 4px 14px;
            }

            .crate-page .packing-meta-item {
                padding: 14px 0;
                border-bottom: 1px solid #edf1f6;
            }

            .crate-page .packing-meta-item:last-child {
                border-bottom: 0;
            }
        }

        /* Dark mode support for new packing card */
        :is([data-bs-theme="dark"], [data-theme="dark"], html.dark, body.dark, body.dark-mode, body.theme-dark)
        .crate-page :is(.packing-information-card, .packing-table-shell, .packing-meta-grid),
        .crate-page.packaging-dark :is(.packing-information-card, .packing-table-shell, .packing-meta-grid) {
            color: var(--crate-text) !important;
            background: var(--crate-surface) !important;
            border-color: var(--crate-border) !important;
        }

        :is([data-bs-theme="dark"], [data-theme="dark"], html.dark, body.dark, body.dark-mode, body.theme-dark)
        .crate-page .packing-detail-table :is(thead th, tbody td),
        .crate-page.packaging-dark .packing-detail-table :is(thead th, tbody td) {
            color: var(--crate-text-soft) !important;
            background: var(--crate-surface) !important;
            border-color: var(--crate-border) !important;
        }

        :is([data-bs-theme="dark"], [data-theme="dark"], html.dark, body.dark, body.dark-mode, body.theme-dark)
        .crate-page .packing-table-toggle,
        .crate-page.packaging-dark .packing-table-toggle {
            color: #60a5fa !important;
            background: var(--crate-surface-2) !important;
            border-color: var(--crate-border-strong) !important;
        }

        :is([data-bs-theme="dark"], [data-theme="dark"], html.dark, body.dark, body.dark-mode, body.theme-dark)
        .crate-page .packing-meta-item strong,
        .crate-page.packaging-dark .packing-meta-item strong {
            color: var(--crate-text) !important;
        }

</style>

    <div class="crate-page crate-theme-root container-fluid py-4">
        <div class="page-header px-3 py-3 d-flex flex-col gap-3 flex-md-row align-items-md-center justify-content-md-between mb-3">
            <div class="d-flex align-items-center gap-3">
                <a href="{{ route('packaging.index') }}" class="btn-soft px-3 py-2 rounded-3 shadow-none" style="border-color: #cbd5e1; height: 38px; width: 38px; display: inline-flex; align-items: center; justify-content: center;">
                    <span class="material-symbols-rounded text-lg" style="margin-left: 2px;">arrow_back</span>
                </a>
                <div>
                    <h1 class="h5 mb-0 crate-page-title" style="font-weight:900;letter-spacing:-.01em; line-height: 1.2;">Calculation</h1>
                    <p class="mb-0 small text-secondary d-none d-md-block" style="font-size: 11px;">Halaman kalkulasi packaging crate untuk pengiriman barang berat.</p>
                </div>
            </div>
            <div class="d-flex flex-wrap align-items-center gap-2 justify-content-md-end">
                <button type="button" data-bs-toggle="modal" data-bs-target="#validasiDataModal" class="btn-soft text-dark border-dark" style="color: #475569; border-color: rgba(71, 85, 105, 0.3); background-color: rgba(71, 85, 105, 0.03); cursor: pointer;">
                    <span class="material-symbols-rounded text-lg">database</span>
                    <span>DATA VALIDASI</span>
                </button>
                @if(!isset($calculation))
                <button type="button" data-bs-toggle="modal" data-bs-target="#productSetupModal" class="btn-soft text-primary border-primary" style="color: #1769e8; border-color: rgba(23, 105, 232, 0.3); background-color: rgba(23, 105, 232, 0.03); cursor: pointer;">
                    <span class="material-symbols-rounded text-lg">add_circle</span>
                    <span>ADD DATA</span>
                </button>
                @else
                    @if(auth()->check() && auth()->user()->hasRole('admin'))
                    <button type="button" data-bs-toggle="modal" data-bs-target="#validasiModal" class="btn-soft text-success border-success" style="color: #0e8a63; border-color: rgba(14, 138, 99, 0.3); background-color: rgba(14, 138, 99, 0.03); cursor: pointer; text-decoration: none;">
                        <span class="material-symbols-rounded text-lg">fact_check</span>
                        <span>VALIDASI</span>
                    </button>
                    @endif
                <button type="button" data-bs-toggle="modal" data-bs-target="#productSetupModal" class="btn-soft text-warning border-warning" style="color: #d97706; border-color: rgba(217, 119, 6, 0.3); background-color: rgba(217, 119, 6, 0.03); cursor: pointer;">
                    <span class="material-symbols-rounded text-lg">edit</span>
                    <span>ADD / EDIT DATA</span>
                </button>
                <button type="button" onclick="printWith3DImage()" class="btn-soft text-primary border-primary" style="color: #1769e8; border-color: rgba(23, 105, 232, 0.3); background-color: rgba(23, 105, 232, 0.03); cursor: pointer;">
                    <span class="material-symbols-rounded text-lg">print</span>
                    <span>PRINT</span>
                </button>
                @endif
            </div>
        </div>

        {{-- Main Row: Inputs, Visual, Stats --}}
        <div class="container-fluid pt-4 pb-4 rounded-4 crate-workspace-shell">
            <div class="row g-4">
                <!-- Full Width Column: Inputs (col-12) -->
                <div class="col-12 d-flex flex-column gap-3">
                    
                    <!-- PACKING INFORMATION -->
                    @php
                        $packingNumber = isset($calculation)
                            ? ($calculation->packaging_number ?? '-')
                            : '-';

                        $packingType = isset($calculation)
                            ? ($calculation->packaging_type
                                ?? $calculation->packing_type
                                ?? $calculation->package_type
                                ?? 'Wooden Crate')
                            : 'Wooden Crate';

                        $packingStatus = isset($calculation)
                            ? ($calculation->status ?? $calculation->packing_status ?? 'Draft')
                            : 'Draft';

                        $normalizedPackingStatus = strtolower((string) $packingStatus);
                        $statusClass = match (true) {
                            in_array($normalizedPackingStatus, ['ready', 'approved', 'complete', 'completed']) => 'is-ready',
                            in_array($normalizedPackingStatus, ['rejected', 'cancelled', 'canceled']) => 'is-danger',
                            in_array($normalizedPackingStatus, ['process', 'processing', 'in progress', 'in-progress']) => 'is-progress',
                            default => 'is-draft',
                        };

                        $deliveryDateRaw = isset($calculation)
                            ? ($calculation->delivery_date
                                ?? $calculation->shipping_date
                                ?? $calculation->tanggal_pengiriman
                                ?? null)
                            : null;

                        try {
                            $deliveryDate = $deliveryDateRaw
                                ? \Carbon\Carbon::parse($deliveryDateRaw)->format('d M Y')
                                : '-';
                        } catch (\Throwable $e) {
                            $deliveryDate = $deliveryDateRaw ?: '-';
                        }

                        $packerName = '-';
                        if (isset($calculation) && $calculation->packer_id) {
                            $packerName = optional(\App\Models\User::find($calculation->packer_id))->name ?? '-';
                        }

                        $approvedById = isset($calculation)
                            ? ($calculation->approved_by
                                ?? $calculation->approver_id
                                ?? $calculation->assigned_by
                                ?? $calculation->created_by
                                ?? null)
                            : null;

                        $approvedByName = $approvedById
                            ? (optional(\App\Models\User::find($approvedById))->name ?? '-')
                            : '-';

                        $packingItemCount = isset($job) && $job->items
                            ? $job->items->count()
                            : 0;
                    @endphp

                    <div class="card border-0 shadow-sm style-card-container packing-information-card">
                        <div class="card-header bg-white border-bottom-0 pt-3 pb-0 d-flex justify-content-between align-items-center">
                            <h6 class="fw-bold mb-0 text-navy d-flex align-items-center" style="font-size: 13px;">
                                <span class="packing-header-icon me-2">
                                    <span class="material-symbols-rounded">inventory_2</span>
                                </span>
                                PACKING INFORMATION
                            </h6>

                            <span class="packing-status-badge {{ $statusClass }}">
                                <span class="packing-status-dot"></span>
                                {{ strtoupper($packingStatus) }}
                            </span>
                        </div>

                        <div class="card-body pt-3">
                            <!-- Packaging Number + Table Toggle -->
                            <div class="packing-reference-row">
                                <div class="d-flex align-items-center min-w-0">
                                    <div class="packing-reference-icon me-3">
                                        <span class="material-symbols-rounded">description</span>
                                    </div>

                                    <div class="min-w-0">
                                        <small class="text-muted d-block lh-1 mb-1" style="font-size: 11px;">
                                            Packaging Number
                                        </small>
                                        <h5 class="fw-bold text-navy mb-0 text-truncate" style="font-size: 18px;">
                                            {{ $packingNumber }}
                                        </h5>
                                    </div>
                                </div>

                                <button
                                    id="packingTableToggle"
                                    class="packing-table-toggle"
                                    type="button"
                                    data-bs-toggle="collapse"
                                    data-bs-target="#packingDetailTable"
                                    aria-expanded="true"
                                    aria-controls="packingDetailTable"
                                >
                                    <span class="packing-item-count">{{ $packingItemCount }} Items</span>
                                    <span id="packingToggleLabel">Hide Table</span>
                                    <span id="packingToggleIcon" class="material-symbols-rounded">expand_less</span>
                                </button>
                            </div>

                            <!-- Only this table can be hidden -->
                            <div id="packingDetailTable" class="collapse show">
                                <div class="packing-table-shell">
                                    <div class="table-responsive">
                                        <table class="table packing-detail-table mb-0 align-middle">
                                            <thead>
                                                <tr>
                                                    <th class="packing-number-column">No</th>
                                                    <th>SO Number</th>
                                                    <th>Customer</th>
                                                    <th>Part Number</th>
                                                    <th class="text-center packing-qty-column">Qty</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @if(isset($job) && $job->items && $job->items->count() > 0)
                                                    @foreach($job->items as $item)
                                                        <tr>
                                                            <td class="text-center">
                                                                <span class="packing-row-number">{{ $loop->iteration }}</span>
                                                            </td>
                                                            <td>{{ $item->no_so ?? '-' }}</td>
                                                            <td class="packing-customer-cell">{{ $item->customer ?? '-' }}</td>
                                                            <td>{{ $item->no_product ?? '-' }}</td>
                                                            <td class="text-center fw-semibold">{{ $item->qty ?? 0 }}</td>
                                                        </tr>
                                                    @endforeach
                                                @else
                                                    <tr>
                                                        <td colspan="5" class="text-center text-muted py-4">
                                                            No items found
                                                        </td>
                                                    </tr>
                                                @endif
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <!-- This information always remains visible -->
                            <div class="packing-meta-grid">
                                <div class="packing-meta-item">
                                    <span class="packing-meta-icon is-purple">
                                        <span class="material-symbols-rounded">engineering</span>
                                    </span>
                                    <div class="min-w-0">
                                        <small>Packer By</small>
                                        <strong title="{{ $packerName }}">{{ $packerName }}</strong>
                                    </div>
                                </div>

                                <div class="packing-meta-item">
                                    <span class="packing-meta-icon is-orange">
                                        <span class="material-symbols-rounded">person</span>
                                    </span>
                                    <div class="min-w-0">
                                        <small>Approved By</small>
                                        <strong title="{{ $approvedByName }}">{{ $approvedByName }}</strong>
                                    </div>
                                </div>

                                <div class="packing-meta-item">
                                    <span class="packing-meta-icon is-blue">
                                        <span class="material-symbols-rounded">deployed_code</span>
                                    </span>
                                    <div class="min-w-0">
                                        <small>Packaging Type</small>
                                        <strong title="{{ $packingType }}">{{ $packingType }}</strong>
                                    </div>
                                </div>

                                <div class="packing-meta-item">
                                    <span class="packing-meta-icon is-green">
                                        <span class="material-symbols-rounded">check_circle</span>
                                    </span>
                                    <div class="min-w-0">
                                        <small>Status</small>
                                        <span class="packing-status-badge {{ $statusClass }} mt-1">
                                            <span class="packing-status-dot"></span>
                                            {{ strtoupper($packingStatus) }}
                                        </span>
                                    </div>
                                </div>

                                <div class="packing-meta-item">
                                    <span class="packing-meta-icon is-blue-soft">
                                        <span class="material-symbols-rounded">calendar_month</span>
                                    </span>
                                    <div class="min-w-0">
                                        <small>Delivery Date</small>
                                        <strong>{{ $deliveryDate }}</strong>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Single Combined Card -->
                    <div class="card border-0 shadow-sm style-card-container configuration-card">
                        <form
                            action="{{ isset($calculation) ? route('packaging.calculations.update', $calculation->id) : '#' }}"
                            method="POST"
                            id="form-edit-config"
                        >
                            @csrf
                    
                            @php
                                /* =========================================================
                                 * NILAI DIMENSI
                                 * Mendukung nama kolom baru dan nama kolom lama.
                                 * ========================================================= */
                                $cPanjang = isset($calculation)
                                    ? ($calculation->length ?? $calculation->panjang ?? 0)
                                    : 0;
                    
                                $cLebar = isset($calculation)
                                    ? ($calculation->width ?? $calculation->lebar ?? 0)
                                    : 0;
                    
                                $cTinggi = isset($calculation)
                                    ? ($calculation->height ?? $calculation->tinggi ?? 0)
                                    : 0;
                    
                                $cJarakAtas = isset($calculation)
                                    ? ($calculation->distance_between_pillars_top ?? $calculation->jarak_penyanggah_atas ?? 300)
                                    : 300;
                                    
                                $cJarakBawah = isset($calculation)
                                    ? ($calculation->distance_between_pillars_bottom ?? $calculation->jarak_penyanggah_bawah ?? 300)
                                    : 300;
                    
                                $cGapAtas = isset($calculation)
                                    ? ($calculation->gap_atas ?? 10)
                                    : 10;
                    
                                $cGapBawah = isset($calculation)
                                    ? ($calculation->gap_bawah ?? 10)
                                    : 10;
                    
                                $details = isset($calculation) && $calculation->relationLoaded('details')
                                    ? $calculation->details
                                    : (isset($calculation) ? $calculation->details : collect());
                    
                                /* =========================================================
                                 * HELPER DATA DETAIL
                                 * ========================================================= */
                                $getMaterialCode = function ($detail, $fallback = '-') {
                                    if (!$detail) {
                                        return filled($fallback) ? $fallback : '-';
                                    }
                    
                                    $kode = data_get($detail, 'material.kode')
                                        ?? data_get($detail, 'material_kode')
                                        ?? data_get($detail, 'material_code');
                                        
                                    // Prefer the flat column fallback if it's a full KAYU string
                                    // and the detail only has a short component code (like BK02).
                                    if (filled($fallback) && $fallback !== '-' && str_starts_with($fallback, 'KAYU-')) {
                                        return $fallback;
                                    }
                    
                                    return $kode ?? (filled($fallback) ? $fallback : '-');
                                };
                    
                                $getMaterialLabel = function ($detail, $fallback = '-') use ($getMaterialCode) {
                                    if (!$detail) {
                                        return filled($fallback) ? $fallback : '-';
                                    }
                    
                                    $code = $getMaterialCode($detail, $fallback);
                                    $name = data_get($detail, 'material.nama')
                                        ?? data_get($detail, 'material.name')
                                        ?? data_get($detail, 'material.deskripsi')
                                        ?? data_get($detail, 'material.description');
                    
                                    if (filled($name) && trim((string) $name) !== trim((string) $code)) {
                                        return trim($code . ' — ' . $name);
                                    }
                    
                                    return filled($code) ? $code : '-';
                                };
                    
                                $detailIsIncluded = function ($detail, $default = false) use ($getMaterialCode) {
                                    if (!$detail) {
                                        return $default;
                                    }
                    
                                    $include = data_get($detail, 'include');
                                    if ($include !== null) {
                                        return in_array(strtolower((string) $include), ['1', 'true', 'yes', 'ya', 'include'], true);
                                    }
                    
                                    $quantity = (float) (data_get($detail, 'total_quantity')
                                        ?? data_get($detail, 'quantity')
                                        ?? 0);
                    
                                    $material = trim((string) $getMaterialCode($detail));
                    
                                    return $quantity > 0 && $material !== '' && $material !== '-';
                                };
                    
                                $statusIsIncluded = function ($status, $default = true) {
                                    if ($status === null || $status === '') {
                                        return $default;
                                    }
                    
                                    return !in_array(
                                        strtolower(trim((string) $status)),
                                        ['exclude', 'not include', '0', 'false', 'tidak', 'tidak digunakan'],
                                        true
                                    );
                                };
                    
                                /* =========================================================
                                 * DETAIL AREA BAWAH
                                 * ========================================================= */
                                $detailBawahPenyangga = $details
                                    ->where('section', 'Bawah')
                                    ->first(fn ($detail) => in_array($detail->part_name, ['Penyangga', 'Penyanggah'], true));
                    
                                $detailBawahPenutup = $details
                                    ->where('section', 'Bawah')
                                    ->first(fn ($detail) => $detail->part_name === 'Penutup');
                    
                                $detailBawahKaki = $details
                                    ->where('section', 'Bawah')
                                    ->first(fn ($detail) => in_array($detail->part_name, ['Kaki Balok', 'Additional Balok'], true));
                    
                                $fallbackBawahPenyanggaStatus = isset($calculation)
                                    ? ($calculation->bawah_penyanggah_status ?? $calculation->bawah_penyangga_status ?? null)
                                    : null;
                    
                                $fallbackBawahKakiStatus = isset($calculation)
                                    ? ($calculation->bawah_kakibalok_status ?? $calculation->bawah_kaki_balok_status ?? null)
                                    : null;
                    
                                $bawahPenyanggaIncluded = $fallbackBawahPenyanggaStatus !== null
                                    ? $statusIsIncluded($fallbackBawahPenyanggaStatus, true)
                                    : ($detailBawahPenyangga ? $detailIsIncluded($detailBawahPenyangga, true) : true);
                    
                                $bawahPenyanggaArah = data_get($detailBawahPenyangga, 'direction')
                                    ?? (isset($calculation)
                                        ? ($calculation->bawah_penyanggah_arahpemasangan ?? $calculation->bawah_penyanggah_arah ?? $calculation->bawah_penyangga_arah ?? 'Horizontal')
                                        : 'Horizontal');
                    
                                $bawahPenyanggaMaterialFallback = isset($calculation)
                                    ? ($calculation->bawah_penyanggah_material ?? $calculation->bawah_penyangga_material ?? '-')
                                    : '-';
                    
                                $bawahPenyanggaMaterial = $getMaterialCode(
                                    $detailBawahPenyangga,
                                    $bawahPenyanggaMaterialFallback
                                );
                    
                                $bawahPenyanggaMaterialLabel = $getMaterialLabel(
                                    $detailBawahPenyangga,
                                    $bawahPenyanggaMaterialFallback
                                );
                    
                                $fallbackBawahPenutupTipe = isset($calculation)
                                    ? ($calculation->bawah_penutup_status ?? 'Tanpa Penutup')
                                    : 'Tanpa Penutup';
                    
                                $bawahPenutupIncluded = $fallbackBawahPenutupTipe !== 'Tanpa Penutup' && $fallbackBawahPenutupTipe !== null
                                    ? !in_array(strtolower(trim((string) $fallbackBawahPenutupTipe)), ['', '0', 'tanpa penutup', 'tidak makai penutup', 'tidak pakai papan', 'exclude'], true)
                                    : ($detailBawahPenutup ? $detailIsIncluded($detailBawahPenutup, false) : false);
                    
                                $bawahPenutupTipe = $bawahPenutupIncluded
                                    ? (data_get($detailBawahPenutup, 'tipe_penutup') ?: $fallbackBawahPenutupTipe)
                                    : 'Tanpa Penutup';
                    
                                $bawahPenutupArah = data_get($detailBawahPenutup, 'direction')
                                    ?? (isset($calculation) ? ($calculation->bawah_penutup_arahpemasangan ?? $calculation->bawah_penutup_arah ?? 'Horizontal') : 'Horizontal');
                    
                                $bawahPenutupMaterialFallback = isset($calculation)
                                    ? ($calculation->bawah_penutup_material ?? '-')
                                    : '-';
                    
                                $bawahPenutupMaterial = $getMaterialCode(
                                    $detailBawahPenutup,
                                    $bawahPenutupMaterialFallback
                                );
                    
                                $bawahPenutupMaterialLabel = $getMaterialLabel(
                                    $detailBawahPenutup,
                                    $bawahPenutupMaterialFallback
                                );
                    
                                $bawahKakiIncluded = $fallbackBawahKakiStatus !== null
                                    ? $statusIsIncluded($fallbackBawahKakiStatus, true)
                                    : ($detailBawahKaki ? $detailIsIncluded($detailBawahKaki, true) : true);
                    
                                $bawahKakiArah = data_get($detailBawahKaki, 'direction')
                                    ?? (isset($calculation) ? ($calculation->bawah_kakibalok_arahpemasangan ?? $calculation->bawah_kaki_balok_arah ?? 'Horizontal') : 'Horizontal');
                    
                                $bawahKakiMaterialFallback = isset($calculation)
                                    ? ($calculation->bawah_kakibalok_material ?? $calculation->bawah_kaki_balok_material ?? '-')
                                    : '-';
                    
                                $bawahKakiMaterial = $getMaterialCode(
                                    $detailBawahKaki,
                                    $bawahKakiMaterialFallback
                                );
                    
                                $bawahKakiMaterialLabel = $getMaterialLabel(
                                    $detailBawahKaki,
                                    $bawahKakiMaterialFallback
                                );
                    
                                /* =========================================================
                                 * DETAIL AREA ATAS
                                 * ========================================================= */
                                $detailAtasPenyangga = $details
                                    ->where('section', 'Penyangga')
                                    ->first(fn ($detail) => $detail->part_name === 'Atas')
                                    ?? $details->first(fn ($detail) => $detail->section === 'Penyangga');
                    
                                $detailAtasPenutup = $details
                                    ->where('section', 'Penutup')
                                    ->first(fn ($detail) => $detail->part_name === 'Atas')
                                    ?? $details->first(fn ($detail) => $detail->section === 'Penutup');
                    
                                $fallbackAtasPenyanggaStatus = isset($calculation)
                                    ? ($calculation->atas_penyanggah_status ?? $calculation->atas_penyangga_status ?? null)
                                    : null;
                    
                                $atasPenyanggaIncluded = $fallbackAtasPenyanggaStatus !== null
                                    ? $statusIsIncluded($fallbackAtasPenyanggaStatus, true)
                                    : ($detailAtasPenyangga ? $detailIsIncluded($detailAtasPenyangga, true) : true);
                    
                                $atasPenyanggaArah = data_get($detailAtasPenyangga, 'direction')
                                    ?? (isset($calculation)
                                        ? ($calculation->atas_penyanggah_arahpemasangan ?? $calculation->atas_penyanggah_arah ?? $calculation->atas_penyangga_arah ?? 'Vertikal')
                                        : 'Vertikal');
                    
                                $atasPenyanggaMaterialFallback = isset($calculation)
                                    ? ($calculation->atas_penyanggah_material ?? $calculation->atas_penyangga_material ?? '-')
                                    : '-';
                    
                                $atasPenyanggaMaterial = $getMaterialCode(
                                    $detailAtasPenyangga,
                                    $atasPenyanggaMaterialFallback
                                );
                    
                                $atasPenyanggaMaterialLabel = $getMaterialLabel(
                                    $detailAtasPenyangga,
                                    $atasPenyanggaMaterialFallback
                                );
                    
                                $fallbackAtasPenutupTipe = isset($calculation)
                                    ? ($calculation->atas_penutup_status ?? 'Tanpa Penutup')
                                    : 'Tanpa Penutup';
                    
                                $atasPenutupIncluded = $fallbackAtasPenutupTipe !== 'Tanpa Penutup' && $fallbackAtasPenutupTipe !== null
                                    ? !in_array(strtolower(trim((string) $fallbackAtasPenutupTipe)), ['', '0', 'tanpa penutup', 'tidak makai penutup', 'tidak pakai papan', 'exclude'], true)
                                    : ($detailAtasPenutup ? $detailIsIncluded($detailAtasPenutup, false) : false);
                    
                                $atasPenutupTipe = $atasPenutupIncluded
                                    ? (data_get($detailAtasPenutup, 'tipe_penutup') ?: $fallbackAtasPenutupTipe)
                                    : 'Tanpa Penutup';
                    
                                $atasPenutupArah = data_get($detailAtasPenutup, 'direction')
                                    ?? (isset($calculation) ? ($calculation->atas_penutup_arahpemasangan ?? $calculation->atas_penutup_arah ?? 'Horizontal') : 'Horizontal');
                    
                                $atasPenutupMaterialFallback = isset($calculation)
                                    ? ($calculation->atas_penutup_material ?? '-')
                                    : '-';
                    
                                $atasPenutupMaterial = $getMaterialCode(
                                    $detailAtasPenutup,
                                    $atasPenutupMaterialFallback
                                );
                    
                                $atasPenutupMaterialLabel = $getMaterialLabel(
                                    $detailAtasPenutup,
                                    $atasPenutupMaterialFallback
                                );
                    
                                /* =========================================================
                                 * JUMLAH KAKI BALOK
                                 * ========================================================= */
                                $jumlahKakiBalok = $detailBawahKaki
                                    ? (data_get($detailBawahKaki, 'total_quantity')
                                        ?? data_get($detailBawahKaki, 'quantity')
                                        ?? 0)
                                    : max(2, (int) floor(((float) $cPanjang) / 800) + 1);
                    
                                if (!$bawahKakiIncluded) {
                                    $jumlahKakiBalok = '-';
                                }
                    
                                $arahPemasanganGlobal = isset($calculation)
                                    ? ($calculation->arah_pemasangan ?? 'Horizontal')
                                    : 'Horizontal';
                            @endphp
                    
                            <style>
                                .crate-page .configuration-card .config-display-value {
                                    display: block;
                                    width: 100%;
                                    min-width: 0;
                                    color: #0f2748;
                                    font-size: 12px;
                                    font-weight: 600;
                                    line-height: 1.4;
                                    white-space: normal;
                                    overflow-wrap: anywhere;
                                    word-break: break-word;
                                }
                    
                                .crate-page .configuration-card .config-display-muted {
                                    color: #94a3b8;
                                }
                    
                                .crate-page .configuration-card .configuration-cell {
                                    min-width: 0;
                                    padding-right: 4px;
                                }
                    
                                .crate-page .configuration-card .configuration-matrix-row {
                                    align-items: center;
                                }
                    
                                .crate-page .configuration-card .configuration-readonly-input {
                                    cursor: default;
                                }
                    
                                .crate-page .configuration-card .configuration-hidden-source {
                                    display: none !important;
                                }
                            </style>
                    

                            <style>
                                /* Refined packing configuration layout */
                                .crate-page .packing-config-layout {
                                    display: grid;
                                    grid-template-columns: minmax(360px, 0.86fr) minmax(560px, 1.34fr);
                                    gap: 16px;
                                    align-items: stretch;
                                    padding: 16px;
                                }

                                .crate-page .packing-config-stack {
                                    display: grid;
                                    gap: 16px;
                                    align-content: start;
                                }

                                .crate-page .packing-config-panel {
                                    overflow: hidden;
                                    border: 1px solid #e2e8f0;
                                    border-radius: 14px;
                                    background: #ffffff;
                                    box-shadow: 0 5px 16px rgba(15, 23, 42, .045);
                                }

                                .crate-page .packing-config-panel-header {
                                    display: flex;
                                    align-items: flex-start;
                                    gap: 11px;
                                    padding: 16px 16px 13px;
                                    border-bottom: 1px solid #edf2f7;
                                }

                                .crate-page .packing-config-panel-icon {
                                    width: 34px;
                                    height: 34px;
                                    display: inline-flex;
                                    align-items: center;
                                    justify-content: center;
                                    flex: 0 0 34px;
                                    border-radius: 9px;
                                    background: #eff6ff;
                                    color: #1769e8;
                                }

                                .crate-page .packing-config-panel-icon.green {
                                    background: #ecfdf5;
                                    color: #059669;
                                }

                                .crate-page .packing-config-title {
                                    margin: 0;
                                    color: #0f2748;
                                    font-size: 13px;
                                    font-weight: 900;
                                    letter-spacing: .015em;
                                }

                                .crate-page .packing-config-subtitle {
                                    margin-top: 3px;
                                    color: #64748b;
                                    font-size: 11px;
                                    line-height: 1.45;
                                }

                                .crate-page .dimension-fields {
                                    display: grid;
                                    grid-template-columns: repeat(3, minmax(0, 1fr));
                                    gap: 12px;
                                    padding: 16px;
                                }

                                .crate-page .clearance-fields {
                                    display: grid;
                                    grid-template-columns: repeat(2, minmax(0, 1fr));
                                    gap: 12px;
                                    padding: 16px;
                                }

                                .crate-page .packing-field-label {
                                    display: flex;
                                    align-items: center;
                                    gap: 7px;
                                    margin-bottom: 7px;
                                    color: #334155;
                                    font-size: 11px;
                                    font-weight: 800;
                                }

                                .crate-page .packing-field-label .material-symbols-rounded {
                                    width: 25px;
                                    height: 25px;
                                    display: inline-flex;
                                    align-items: center;
                                    justify-content: center;
                                    border-radius: 7px;
                                    background: #eff6ff;
                                    color: #1769e8;
                                    font-size: 15px;
                                }

                                .crate-page .packing-field-label.green .material-symbols-rounded {
                                    background: #ecfdf5;
                                    color: #059669;
                                }

                                .crate-page .packing-value-box {
                                    display: grid;
                                    grid-template-columns: minmax(0, 1fr) auto;
                                    align-items: center;
                                    min-height: 44px;
                                    padding: 0 12px;
                                    border: 1px solid #dce5f0;
                                    border-radius: 9px;
                                    background: #ffffff;
                                    box-shadow: 0 2px 5px rgba(15, 23, 42, .025);
                                }

                                .crate-page .packing-value-box input {
                                    width: 100%;
                                    min-width: 0;
                                    border: 0 !important;
                                    outline: 0;
                                    background: transparent !important;
                                    color: #0f172a !important;
                                    font-size: 15px !important;
                                    font-weight: 800 !important;
                                    box-shadow: none !important;
                                }

                                .crate-page .packing-value-box .unit {
                                    color: #64748b;
                                    font-size: 11px;
                                    font-weight: 800;
                                }

                                .crate-page .clearance-note {
                                    display: flex;
                                    gap: 9px;
                                    margin: 0 16px 16px;
                                    padding: 11px 12px;
                                    border-radius: 9px;
                                    background: #f0f7ff;
                                    color: #52657a;
                                    font-size: 10px;
                                    line-height: 1.5;
                                }

                                .crate-page .clearance-note .material-symbols-rounded {
                                    color: #1769e8;
                                    font-size: 17px;
                                    flex: 0 0 auto;
                                }

                                .crate-page .material-tabs-wrap {
                                    padding: 13px 14px 0;
                                }

                                .crate-page .material-tabs {
                                    display: inline-flex;
                                    padding: 3px;
                                    border: 1px solid #e2e8f0;
                                    border-radius: 10px;
                                    background: #f8fafc;
                                }

                                .crate-page .material-tabs .nav-link {
                                    min-width: 118px;
                                    border: 0;
                                    border-radius: 7px;
                                    color: #64748b;
                                    font-size: 11px;
                                    font-weight: 800;
                                    padding: 8px 16px;
                                }

                                .crate-page .material-tabs .nav-link.active {
                                    background: #10b981;
                                    color: #ffffff;
                                    box-shadow: 0 4px 10px rgba(16, 185, 129, .18);
                                }

                                .crate-page .material-tab-content {
                                    padding: 14px;
                                    height: auto !important;
                                    min-height: 0 !important;
                                    flex: 0 0 auto;
                                }

                                /* Material Configuration berhenti sejajar dengan bagian bawah Internal Clearance */
                                .crate-page .packing-config-layout > .packing-config-panel {
                                    align-self: stretch;
                                    height: 100%;
                                    min-height: 0 !important;
                                    display: flex;
                                    flex-direction: column;
                                }

                                .crate-page .packing-config-layout > .packing-config-panel .material-tabs-wrap,
                                .crate-page .packing-config-layout > .packing-config-panel #materialAreaTabsContent {
                                    flex: 0 0 auto;
                                }

                                .crate-page #materialAreaTabsContent .tab-pane,
                                .crate-page #materialAreaTabsContent .material-list {
                                    height: auto !important;
                                    min-height: 0 !important;
                                }

                                .crate-page .material-list {
                                    display: grid;
                                    gap: 10px;
                                }

                                .crate-page .material-row-card {
                                    display: grid;
                                    grid-template-columns: minmax(150px, 1.15fr) minmax(88px, .7fr) minmax(105px, .75fr) minmax(180px, 1.4fr);
                                    gap: 12px;
                                    align-items: center;
                                    padding: 13px 14px;
                                    border: 1px solid #e8edf4;
                                    border-radius: 11px;
                                    background: #ffffff;
                                    transition: border-color .2s ease, box-shadow .2s ease;
                                }

                                .crate-page .material-row-card:hover {
                                    border-color: #cfd9e7;
                                    box-shadow: 0 5px 14px rgba(15, 23, 42, .05);
                                }

                                .crate-page .material-component {
                                    display: flex;
                                    align-items: center;
                                    gap: 10px;
                                    min-width: 0;
                                    color: #0f172a;
                                    font-size: 12px;
                                    font-weight: 900;
                                }

                                .crate-page .material-component-icon {
                                    width: 34px;
                                    height: 34px;
                                    display: inline-flex;
                                    align-items: center;
                                    justify-content: center;
                                    flex: 0 0 34px;
                                    border-radius: 9px;
                                    background: #ecfdf5;
                                    color: #059669;
                                }

                                .crate-page .material-component-icon.orange {
                                    background: #fff7ed;
                                    color: #f97316;
                                }

                                .crate-page .material-meta-label {
                                    display: block;
                                    margin-bottom: 4px;
                                    color: #94a3b8;
                                    font-size: 9px;
                                    font-weight: 800;
                                    letter-spacing: .04em;
                                    text-transform: uppercase;
                                }

                                .crate-page .material-meta-value {
                                    display: block;
                                    min-width: 0;
                                    color: #172033;
                                    font-size: 11px;
                                    font-weight: 700;
                                    line-height: 1.4;
                                    overflow-wrap: anywhere;
                                }

                                .crate-page .material-mode-badge {
                                    display: inline-flex;
                                    align-items: center;
                                    width: fit-content;
                                    border-radius: 999px;
                                    padding: 4px 9px;
                                    background: #eafaf1;
                                    color: #079455;
                                    font-size: 9px;
                                    font-weight: 900;
                                }

                                .crate-page .material-mode-badge.orange {
                                    background: #fff4e5;
                                    color: #e8790c;
                                }

                                .crate-page .material-empty {
                                    padding: 24px;
                                    border: 1px dashed #cbd5e1;
                                    border-radius: 11px;
                                    color: #64748b;
                                    text-align: center;
                                    font-size: 11px;
                                }

                                @media (max-width: 1199.98px) {
                                    .crate-page .packing-config-layout {
                                        grid-template-columns: 1fr;
                                    }
                                }

                                @media (max-width: 767.98px) {
                                    .crate-page .packing-config-layout {
                                        padding: 12px;
                                    }

                                    .crate-page .dimension-fields,
                                    .crate-page .clearance-fields {
                                        grid-template-columns: 1fr;
                                    }

                                    .crate-page .material-row-card {
                                        grid-template-columns: 1fr 1fr;
                                    }
                                }

                                @media (max-width: 479.98px) {
                                    .crate-page .material-row-card {
                                        grid-template-columns: 1fr;
                                    }

                                    .crate-page .material-tabs {
                                        display: flex;
                                        width: 100%;
                                    }

                                    .crate-page .material-tabs .nav-link {
                                        flex: 1 1 50%;
                                        min-width: 0;
                                    }
                                }

                                [data-bs-theme="dark"] .crate-page .packing-config-panel,
                                .crate-page.packaging-dark .packing-config-panel,
                                [data-bs-theme="dark"] .crate-page .material-row-card,
                                .crate-page.packaging-dark .material-row-card,
                                [data-bs-theme="dark"] .crate-page .packing-value-box,
                                .crate-page.packaging-dark .packing-value-box {
                                    background: #111827 !important;
                                    border-color: #334155 !important;
                                }

                                [data-bs-theme="dark"] .crate-page .packing-config-panel-header,
                                .crate-page.packaging-dark .packing-config-panel-header {
                                    border-color: #334155 !important;
                                }

                                [data-bs-theme="dark"] .crate-page .packing-config-title,
                                [data-bs-theme="dark"] .crate-page .material-component,
                                [data-bs-theme="dark"] .crate-page .material-meta-value,
                                .crate-page.packaging-dark .packing-config-title,
                                .crate-page.packaging-dark .material-component,
                                .crate-page.packaging-dark .material-meta-value {
                                    color: #f8fafc !important;
                                }
                            </style>

                            <div class="card-body p-0">
                                <div class="packing-config-layout">
                                    <div class="packing-config-stack">
                                        {{-- CRATE SIZE --}}
                                        <section class="packing-config-panel" aria-labelledby="crate-size-title">
                                            <div class="packing-config-panel-header">
                                                <span class="packing-config-panel-icon">
                                                    <span class="material-symbols-rounded" style="font-size: 19px;">deployed_code</span>
                                                </span>
                                                <div>
                                                    <h6 id="crate-size-title" class="packing-config-title">CRATE SIZE</h6>
                                                    <div class="packing-config-subtitle">Ukuran luar peti kemas / crate</div>
                                                </div>
                                            </div>

                                            <div class="dimension-fields">
                                                <label>
                                                    <span class="packing-field-label">
                                                        <span class="material-symbols-rounded">swap_horiz</span>
                                                        Panjang (P)
                                                    </span>
                                                    <span class="packing-value-box">
                                                        <input type="number" name="length" value="{{ $cPanjang }}" readonly class="configuration-readonly-input">
                                                        <span class="unit">mm</span>
                                                    </span>
                                                </label>

                                                <label>
                                                    <span class="packing-field-label">
                                                        <span class="material-symbols-rounded">straighten</span>
                                                        Lebar (L)
                                                    </span>
                                                    <span class="packing-value-box">
                                                        <input type="number" name="width" value="{{ $cLebar }}" readonly class="configuration-readonly-input">
                                                        <span class="unit">mm</span>
                                                    </span>
                                                </label>

                                                <label>
                                                    <span class="packing-field-label">
                                                        <span class="material-symbols-rounded">height</span>
                                                        Tinggi (T)
                                                    </span>
                                                    <span class="packing-value-box">
                                                        <input type="number" name="height" value="{{ $cTinggi }}" readonly class="configuration-readonly-input">
                                                        <span class="unit">mm</span>
                                                    </span>
                                                </label>
                                            </div>
                                        </section>

                                        {{-- INTERNAL CLEARANCE --}}
                                        <section class="packing-config-panel" aria-labelledby="clearance-title">
                                            <div class="packing-config-panel-header">
                                                <span class="packing-config-panel-icon green">
                                                    <span class="material-symbols-rounded" style="font-size: 19px;">height</span>
                                                </span>
                                                <div>
                                                    <h6 id="clearance-title" class="packing-config-title" style="color:#07895f;">INTERNAL CLEARANCE</h6>
                                                    <div class="packing-config-subtitle">Ruang tambahan untuk handling dan keamanan barang</div>
                                                </div>
                                            </div>

                                            <div class="clearance-fields">
                                                <label>
                                                    <span class="packing-field-label green">
                                                        <span class="material-symbols-rounded">vertical_align_top</span>
                                                        Jarak Atas
                                                    </span>
                                                    <span class="packing-value-box">
                                                        <input type="number" name="jarak_penyanggah_atas" value="{{ $cJarakAtas }}" readonly class="configuration-readonly-input">
                                                        <span class="unit">mm</span>
                                                    </span>
                                                </label>

                                                <label>
                                                    <span class="packing-field-label green">
                                                        <span class="material-symbols-rounded">vertical_align_bottom</span>
                                                        Jarak Bawah
                                                    </span>
                                                    <span class="packing-value-box">
                                                        <input type="number" name="jarak_penyanggah_bawah" value="{{ $cJarakBawah }}" readonly class="configuration-readonly-input">
                                                        <span class="unit">mm</span>
                                                    </span>
                                                </label>

                                                <label>
                                                    <span class="packing-field-label green">
                                                        <span class="material-symbols-rounded">keyboard_double_arrow_up</span>
                                                        Celah Atas
                                                    </span>
                                                    <span class="packing-value-box">
                                                        <input type="number" name="gap_atas" value="{{ $cGapAtas }}" readonly class="configuration-readonly-input">
                                                        <span class="unit">mm</span>
                                                    </span>
                                                </label>

                                                <label>
                                                    <span class="packing-field-label green">
                                                        <span class="material-symbols-rounded">keyboard_double_arrow_down</span>
                                                        Celah Bawah
                                                    </span>
                                                    <span class="packing-value-box">
                                                        <input type="number" name="gap_bawah" value="{{ $cGapBawah }}" readonly class="configuration-readonly-input">
                                                        <span class="unit">mm</span>
                                                    </span>
                                                </label>
                                            </div>

                                            <div class="clearance-note">
                                                <span class="material-symbols-rounded">info</span>
                                                <span>Nilai clearance digunakan sebagai ruang tambahan untuk memudahkan proses handling dan menjaga keamanan barang.</span>
                                            </div>
                                        </section>
                                    </div>

                                    {{-- MATERIAL CONFIGURATION --}}
                                    <section class="packing-config-panel" aria-labelledby="material-config-title">
                                        <div class="packing-config-panel-header">
                                            <span class="packing-config-panel-icon green">
                                                <span class="material-symbols-rounded" style="font-size: 19px;">deployed_code</span>
                                            </span>
                                            <div>
                                                <h6 id="material-config-title" class="packing-config-title" style="color:#07895f;">MATERIAL CONFIGURATION</h6>
                                                <div class="packing-config-subtitle">Atur material dan struktur untuk setiap area crate</div>
                                            </div>
                                        </div>

                                        <div class="material-tabs-wrap">
                                            @php
                                                $innerBoxesData = isset($calculation) ? json_decode($calculation->inner_carton_boxes, true) ?? [] : [];
                                                $hasInnerBoxes = is_array($innerBoxesData) && count($innerBoxesData) > 0;
                                            @endphp
                                            <ul class="nav material-tabs" id="materialAreaTabs" role="tablist">
                                                <li class="nav-item" role="presentation">
                                                    <button class="nav-link" id="area-bawah-tab" data-bs-toggle="tab" data-bs-target="#area-bawah-pane" type="button" role="tab" aria-controls="area-bawah-pane" aria-selected="false">Area Bawah</button>
                                                </li>
                                                <li class="nav-item" role="presentation">
                                                    <button class="nav-link active" id="area-atas-tab" data-bs-toggle="tab" data-bs-target="#area-atas-pane" type="button" role="tab" aria-controls="area-atas-pane" aria-selected="true">Area Atas</button>
                                                </li>
                                                <li class="nav-item" role="presentation">
                                                    <button class="nav-link" id="area-additional-tab" data-bs-toggle="tab" data-bs-target="#area-additional-pane" type="button" role="tab" aria-controls="area-additional-pane" aria-selected="false">Material Additional</button>
                                                </li>
                                                @if($hasInnerBoxes)
                                                <li class="nav-item" role="presentation">
                                                    <button class="nav-link" id="area-innerbox-tab" data-bs-toggle="tab" data-bs-target="#area-innerbox-pane" type="button" role="tab" aria-controls="area-innerbox-pane" aria-selected="false">Inner Box</button>
                                                </li>
                                                @endif
                                            </ul>
                                        </div>

                                        <div class="tab-content material-tab-content" id="materialAreaTabsContent">
                                            <div class="tab-pane fade show active" id="area-atas-pane" role="tabpanel" aria-labelledby="area-atas-tab" tabindex="0">
                                                <div class="material-list">
                                                    <div class="material-row-card">
                                                        <div class="material-component">
                                                            <span class="material-component-icon"><span class="material-symbols-rounded">change_history</span></span>
                                                            Penyangga
                                                        </div>
                                                        <div>
                                                            <span class="material-meta-label">Mode</span>
                                                            <span class="material-mode-badge">{{ $atasPenyanggaIncluded ? 'Include' : 'Not Include' }}</span>
                                                        </div>
                                                        <div>
                                                            <span class="material-meta-label">Arah</span>
                                                            <span class="material-meta-value">{{ $atasPenyanggaIncluded ? ($atasPenyanggaArah ?: '-') : '-' }}</span>
                                                        </div>
                                                        <div>
                                                            <span class="material-meta-label">Material</span>
                                                            <span class="material-meta-value" title="{{ $atasPenyanggaMaterialLabel }}">{{ $atasPenyanggaIncluded ? ($atasPenyanggaMaterialLabel ?: '-') : '-' }}</span>
                                                        </div>
                                                    </div>

                                                    <div class="material-row-card">
                                                        <div class="material-component">
                                                            <span class="material-component-icon orange"><span class="material-symbols-rounded">grid_view</span></span>
                                                            Penutup
                                                        </div>
                                                        <div>
                                                            <span class="material-meta-label">Mode</span>
                                                            <span class="material-mode-badge orange">{{ $atasPenutupTipe }}</span>
                                                        </div>
                                                        <div>
                                                            <span class="material-meta-label">Arah</span>
                                                            <span class="material-meta-value">{{ $atasPenutupIncluded ? ($atasPenutupArah ?: '-') : '-' }}</span>
                                                        </div>
                                                        <div>
                                                            <span class="material-meta-label">Material</span>
                                                            <span class="material-meta-value" title="{{ $atasPenutupMaterialLabel }}">{{ $atasPenutupIncluded ? ($atasPenutupMaterialLabel ?: '-') : '-' }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="tab-pane fade" id="area-bawah-pane" role="tabpanel" aria-labelledby="area-bawah-tab" tabindex="0">
                                                <div class="material-list">
                                                    <div class="material-row-card">
                                                        <div class="material-component">
                                                            <span class="material-component-icon"><span class="material-symbols-rounded">change_history</span></span>
                                                            Penyangga
                                                        </div>
                                                        <div>
                                                            <span class="material-meta-label">Mode</span>
                                                            <span class="material-mode-badge">{{ $bawahPenyanggaIncluded ? 'Include' : 'Not Include' }}</span>
                                                        </div>
                                                        <div>
                                                            <span class="material-meta-label">Arah</span>
                                                            <span class="material-meta-value">{{ $bawahPenyanggaIncluded ? ($bawahPenyanggaArah ?: '-') : '-' }}</span>
                                                        </div>
                                                        <div>
                                                            <span class="material-meta-label">Material</span>
                                                            <span class="material-meta-value" title="{{ $bawahPenyanggaMaterialLabel }}">{{ $bawahPenyanggaIncluded ? ($bawahPenyanggaMaterialLabel ?: '-') : '-' }}</span>
                                                        </div>
                                                    </div>

                                                    <div class="material-row-card">
                                                        <div class="material-component">
                                                            <span class="material-component-icon orange"><span class="material-symbols-rounded">grid_view</span></span>
                                                            Penutup
                                                        </div>
                                                        <div>
                                                            <span class="material-meta-label">Mode</span>
                                                            <span class="material-mode-badge orange">{{ $bawahPenutupTipe }}</span>
                                                        </div>
                                                        <div>
                                                            <span class="material-meta-label">Arah</span>
                                                            <span class="material-meta-value">{{ $bawahPenutupIncluded ? ($bawahPenutupArah ?: '-') : '-' }}</span>
                                                        </div>
                                                        <div>
                                                            <span class="material-meta-label">Material</span>
                                                            <span class="material-meta-value" title="{{ $bawahPenutupMaterialLabel }}">{{ $bawahPenutupIncluded ? ($bawahPenutupMaterialLabel ?: '-') : '-' }}</span>
                                                        </div>
                                                    </div>

                                                    <div class="material-row-card">
                                                        <div class="material-component">
                                                            <span class="material-component-icon"><span class="material-symbols-rounded">view_column_2</span></span>
                                                            Kaki Balok
                                                        </div>
                                                        <div>
                                                            <span class="material-meta-label">Mode</span>
                                                            <span class="material-mode-badge">{{ $bawahKakiIncluded ? 'Include' : 'Not Include' }}</span>
                                                        </div>
                                                        <div>
                                                            <span class="material-meta-label">Arah</span>
                                                            <span class="material-meta-value">{{ $bawahKakiIncluded ? ($bawahKakiArah ?: '-') : '-' }}</span>
                                                        </div>
                                                        <div>
                                                            <span class="material-meta-label">Material</span>
                                                            <span class="material-meta-value" title="{{ $bawahKakiMaterialLabel }}">{{ $bawahKakiIncluded ? ($bawahKakiMaterialLabel ?: '-') : '-' }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="tab-pane fade" id="area-additional-pane" role="tabpanel" aria-labelledby="area-additional-tab" tabindex="0">
                                                <div class="material-list">
                                                    @php
                                                        $terpalId = $calculation->terpal_material ?? '';
                                                        $cartonId = $calculation->carton_material ?? '';

                                                        $terpalMat = null;
                                                        if (!empty($terpalId)) {
                                                            $terpalMat = \Illuminate\Support\Facades\DB::table('packing_material_prices')->where('code', $terpalId)->first();
                                                            if (!$terpalMat && \Illuminate\Support\Str::isUuid($terpalId)) {
                                                                $terpalMat = \Illuminate\Support\Facades\DB::table('packing_material_prices')->where('id', $terpalId)->first();
                                                            }
                                                        }

                                                        $cartonMat = null;
                                                        if (!empty($cartonId)) {
                                                            $cartonMat = \Illuminate\Support\Facades\DB::table('packing_material_prices')->where('code', $cartonId)->first();
                                                            if (!$cartonMat && \Illuminate\Support\Str::isUuid($cartonId)) {
                                                                $cartonMat = \Illuminate\Support\Facades\DB::table('packing_material_prices')->where('id', $cartonId)->first();
                                                            }
                                                        }

                                                        $terpalComponent = $terpalMat ? $terpalMat->component : '-';
                                                        $cartonComponent = $cartonMat ? $cartonMat->component : '-';
                                                    @endphp

                                                    @if(!empty($terpalId))
                                                    <div class="material-row-card">
                                                        <div class="material-component">
                                                            <span class="material-component-icon"><span class="material-symbols-rounded">layers</span></span>
                                                            Terpal
                                                        </div>
                                                        <div>
                                                            <span class="material-meta-label">Mode</span>
                                                            <span class="material-mode-badge">Include</span>
                                                        </div>
                                                        <div>
                                                            <span class="material-meta-label">Material</span>
                                                            <span class="material-meta-value" title="{{ $terpalComponent }}">{{ $terpalComponent }}</span>
                                                        </div>
                                                    </div>
                                                    @endif

                                                    @if(!empty($cartonId))
                                                    <div class="material-row-card">
                                                        <div class="material-component">
                                                            <span class="material-component-icon"><span class="material-symbols-rounded">inventory_2</span></span>
                                                            Carton Box
                                                        </div>
                                                        <div>
                                                            <span class="material-meta-label">Mode</span>
                                                            <span class="material-mode-badge">Include</span>
                                                        </div>
                                                        <div>
                                                            <span class="material-meta-label">Material</span>
                                                            <span class="material-meta-value" title="{{ $cartonComponent }}">{{ $cartonComponent }}</span>
                                                        </div>
                                                        <div>
                                                            <span class="material-meta-label">Type Sablon</span>
                                                            <span class="material-meta-value">{{ $calculation->carton_type_sablon ?? 'Polos' }}</span>
                                                        </div>
                                                    </div>
                                                    @endif

                                                    @if(empty($terpalId) && empty($cartonId))
                                                    <div class="material-row-card text-center d-flex align-items-center justify-content-center" style="grid-template-columns: 1fr; border-left-color: #6c757d;">
                                                        <div class="py-3 text-muted">
                                                            <span class="material-symbols-rounded d-block mb-1" style="font-size: 24px; opacity: 0.5;">info</span>
                                                            <span style="font-size: 13px;">Tidak ada material tambahan</span>
                                                        </div>
                                                    </div>
                                                    @endif
                                                </div>
                                            </div>

                                            @if($hasInnerBoxes)
                                            <div class="tab-pane fade" id="area-innerbox-pane" role="tabpanel" aria-labelledby="area-innerbox-tab" tabindex="0">
                                                <div class="material-list" style="max-height: 280px; overflow-y: auto; overflow-x: hidden; padding-right: 5px;">
                                                    @foreach($innerBoxesData as $idx => $ib)
                                                    <div class="material-row-card" style="border-left-color: #07895f;">
                                                        <div class="material-component">
                                                            <span class="material-component-icon green"><span class="material-symbols-rounded">inventory_2</span></span>
                                                            Inner Box #{{ $idx + 1 }}
                                                        </div>
                                                        <div>
                                                            <span class="material-meta-label">Material</span>
                                                            <span class="material-meta-value">{{ $ib['materialName'] ?? '-' }}</span>
                                                        </div>
                                                        <div>
                                                            <span class="material-meta-label">Ukuran (PxLxT)</span>
                                                            <span class="material-meta-value">
                                                                @if(isset($ib['length']) && isset($ib['width']) && isset($ib['height']) && $ib['length'] && $ib['width'] && $ib['height'])
                                                                    {{ $ib['length'] }}mm x {{ $ib['width'] }}mm x {{ $ib['height'] }}mm
                                                                @else
                                                                    {{ $ib['size'] ?? '-' }}
                                                                @endif
                                                            </span>
                                                        </div>
                                                        <div>
                                                            <span class="material-meta-label">Qty</span>
                                                            <span class="material-meta-value">{{ $ib['qty'] ?? 1 }} Pcs</span>
                                                        </div>
                                                    </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                            @endif
                                        </div>
                                    </section>
                                </div>
                            {{--
                                Data ini tidak terlihat di halaman.
                                Fungsinya hanya sebagai sumber nilai bagi renderer 2D/3D dan proses JavaScript lama.
                                Tidak ada dropdown dan tidak ada elemen option.
                            --}}
                            <div class="configuration-hidden-source" aria-hidden="true">
                                <input type="hidden" name="arah_pemasangan" value="{{ $arahPemasanganGlobal }}">
                    
                                <input type="hidden" name="bawah_penyangga_include" value="{{ $bawahPenyanggaIncluded ? '1' : '0' }}">
                                <input type="hidden" name="bawah_penyangga_arah" value="{{ $bawahPenyanggaArah }}">
                                <input type="hidden" name="bawah_penyangga_material" value="{{ $bawahPenyanggaMaterial }}">
                    
                                <input type="hidden" name="bawah_penutup_tipe" value="{{ $bawahPenutupTipe }}">
                                <input type="hidden" name="bawah_penutup_arah" value="{{ $bawahPenutupArah }}">
                                <input type="hidden" name="bawah_penutup_material" value="{{ $bawahPenutupMaterial }}">
                    
                                <input type="hidden" name="include_pallet_base" value="{{ $bawahKakiIncluded ? '1' : '0' }}">
                                <input type="hidden" name="bawah_kakibalok_arah" value="{{ $bawahKakiArah }}">
                                <input type="hidden" name="bawah_kakibalok_material" value="{{ $bawahKakiMaterial }}">
                    
                                <input type="hidden" name="atas_penyangga_include" value="{{ $atasPenyanggaIncluded ? '1' : '0' }}">
                                <input type="hidden" name="atas_penyangga_arah" value="{{ $atasPenyanggaArah }}">
                                <input type="hidden" name="atas_penyangga_material" value="{{ $atasPenyanggaMaterial }}">
                    
                                <input type="hidden" name="atas_penutup_tipe" value="{{ $atasPenutupTipe }}">
                                <input type="hidden" name="atas_penutup_arah" value="{{ $atasPenutupArah }}">
                                <input type="hidden" name="atas_penutup_material" value="{{ $atasPenutupMaterial }}">
                            </div>
                        </form>
                    </div>
                    
                    {{-- Konfigurasi bersifat read-only. Perubahan dilakukan melalui modal ADD / EDIT DATA. --}}
                </div>
            </div>

            <style>
                @media (min-width: 992px) {
                    .col-lg-9-5 { flex: 0 0 auto; width: 79.166667%; }
                    .col-lg-2-5 { flex: 0 0 auto; width: 20.833333%; }
                }
            </style>
            {{-- Row Visual Ilustrasi --}}
            <div class="row g-3 mb-3 mt-1">
                <div class="col-12 col-lg-9-5">
                    <div class="panel-card visual-card h-100 d-flex flex-column border-0 shadow-sm">
                        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3 border-bottom pb-3">
                            <h2 class="section-title mb-0 d-flex align-items-center gap-2">
                                <span class="material-symbols-rounded text-primary">deployed_code</span>
                                Ilustrasi
                            </h2>
                        </div>
                        
                        <div class="visual-stage flex-grow-1 position-relative" id="crate-canvas-container" style="min-height: 700px; background-color: #f8fafc; border-radius: 12px; overflow: hidden;">
                            <!-- Floating CAD-Style Glassmorphic Toolbar (Line 1: Visibilitas) -->
                            <div class="floating-visual-toolbar d-flex flex-nowrap align-items-center justify-content-between gap-2 py-1 px-2 mb-2" style="position: absolute; top: 12px; left: 12px; right: 12px; z-index: 10; background: rgba(240, 245, 255, 0.85); border: 1px solid rgba(200, 220, 240, 0.8); border-radius: 12px; box-shadow: 0 4px 15px -5px rgba(0, 0, 0, 0.05); backdrop-filter: blur(12px);">
                                <div class="d-flex flex-nowrap align-items-center gap-2 flex-shrink-0">
                                    <!-- Grup Atas -->
                                    <div class="toolbar-pill-group flex-nowrap d-flex align-items-center rounded-pill px-2 py-1" id="group-atas-toggles" style="background: rgba(230, 245, 255, 0.8); border: 1px solid rgba(150, 200, 255, 0.5);">
                                        <span class="toolbar-pill-label atas text-nowrap fw-bold me-2" style="color: #0d6efd; font-size: 0.75rem;">GRUP ATAS</span>
                                        <div class="d-flex gap-1 flex-nowrap">
                                            
                                            <input type="checkbox" class="btn-check" id="vis-toggle-penyangga-atas" checked autocomplete="off">
                                            <label class="btn btn-outline-primary btn-sm text-nowrap py-0 px-2" style="font-size: 0.75rem; border-radius: 6px;" for="vis-toggle-penyangga-atas" id="vis-toggle-penyangga-atas-lbl">Penyangga</label>
                
                                            <input type="checkbox" class="btn-check" id="vis-toggle-penutup-atas" checked autocomplete="off">
                                            <label class="btn btn-outline-primary btn-sm text-nowrap py-0 px-2" style="font-size: 0.75rem; border-radius: 6px;" for="vis-toggle-penutup-atas" id="vis-toggle-penutup-atas-lbl">Penutup</label>
                                        </div>
                                    </div>
                                    
                                    <!-- Grup Bawah -->
                                    <div class="toolbar-pill-group flex-nowrap d-flex align-items-center rounded-pill px-2 py-1" id="group-bawah-toggles" style="background: rgba(235, 250, 235, 0.8); border: 1px solid rgba(180, 230, 180, 0.5);">
                                        <span class="toolbar-pill-label bawah text-nowrap fw-bold me-2" style="color: #198754; font-size: 0.75rem;">GRUP BAWAH</span>
                                        <div class="d-flex gap-1 flex-nowrap">

                                            <input type="checkbox" class="btn-check" id="vis-toggle-penyangga-bawah" checked autocomplete="off">
                                            <label class="btn btn-outline-success btn-sm text-nowrap py-0 px-2" style="font-size: 0.75rem; border-radius: 6px;" for="vis-toggle-penyangga-bawah" id="vis-toggle-penyangga-bawah-lbl">Penyangga</label>
                
                                            <input type="checkbox" class="btn-check" id="vis-toggle-penutup-bawah" checked autocomplete="off">
                                            <label class="btn btn-outline-success btn-sm text-nowrap py-0 px-2" style="font-size: 0.75rem; border-radius: 6px;" for="vis-toggle-penutup-bawah" id="vis-toggle-penutup-bawah-lbl">Penutup</label>
                                            
                                            <input type="checkbox" class="btn-check" id="vis-toggle-kakibalok" checked autocomplete="off">
                                            <label class="btn btn-outline-success btn-sm text-nowrap py-0 px-2" style="font-size: 0.75rem; border-radius: 6px;" for="vis-toggle-kakibalok" id="vis-toggle-kakibalok-lbl">Kaki Balok</label>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="d-flex flex-nowrap align-items-center gap-2 flex-shrink-0 pe-1">
                                    <div class="toolbar-pill-group px-2 py-1 flex-nowrap rounded-pill d-flex align-items-center" style="background: rgba(255, 255, 255, 0.5); border: 1px solid rgba(200, 220, 240, 0.5);">
                                        <div class="btn-group flex-nowrap gap-1" role="group">
                                            <button type="button" class="btn btn-outline-secondary btn-sm py-0 px-2 active text-nowrap" style="font-size: 0.75rem; border-radius: 6px;" data-view="iso">Iso</button>
                                            <button type="button" class="btn btn-outline-secondary btn-sm py-0 px-2 text-nowrap" style="font-size: 0.75rem; border-radius: 6px;" data-view="front">Depan</button>
                                            <button type="button" class="btn btn-outline-secondary btn-sm py-0 px-2 text-nowrap" style="font-size: 0.75rem; border-radius: 6px;" data-view="right">Kanan</button>
                                            <button type="button" class="btn btn-outline-secondary btn-sm py-0 px-2 text-nowrap" style="font-size: 0.75rem; border-radius: 6px;" data-view="top">Atas</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Floating CAD-Style Glassmorphic Toolbar (Line 2: Expand) -->
                            <div class="floating-visual-toolbar d-flex flex-nowrap align-items-center justify-content-between gap-2 py-1 px-2" style="position: absolute; top: 60px; left: 12px; right: 12px; z-index: 10; background: rgba(245, 245, 245, 0.85); border: 1px solid rgba(220, 220, 220, 0.8); border-radius: 12px; box-shadow: 0 4px 15px -5px rgba(0, 0, 0, 0.05); backdrop-filter: blur(12px);">
                                <div class="d-flex flex-nowrap align-items-center gap-2 flex-shrink-0">
                                    <!-- Expand Atas -->
                                    <div class="toolbar-pill-group flex-nowrap d-flex align-items-center rounded-pill px-2 py-1" id="group-atas-expand" style="background: rgba(255, 240, 230, 0.8); border: 1px solid rgba(255, 180, 150, 0.5);">
                                        <span class="toolbar-pill-label atas text-nowrap fw-bold me-2" style="color: #fd7e14; font-size: 0.75rem;">EXPAND ATAS</span>
                                        <div class="d-flex gap-1 flex-nowrap">
                                            
                                            <input type="checkbox" class="btn-check" id="vis-exp-penyangga-atas" autocomplete="off">
                                            <label class="btn btn-outline-warning btn-sm text-nowrap py-0 px-2" style="font-size: 0.75rem; border-radius: 6px; --bs-btn-color: #fd7e14; --bs-btn-border-color: #fd7e14; --bs-btn-hover-bg: #fd7e14; --bs-btn-hover-border-color: #fd7e14;" for="vis-exp-penyangga-atas" id="vis-exp-penyangga-atas-lbl">Penyangga</label>
                
                                            <input type="checkbox" class="btn-check" id="vis-exp-penutup-atas" autocomplete="off">
                                            <label class="btn btn-outline-warning btn-sm text-nowrap py-0 px-2" style="font-size: 0.75rem; border-radius: 6px; --bs-btn-color: #fd7e14; --bs-btn-border-color: #fd7e14; --bs-btn-hover-bg: #fd7e14; --bs-btn-hover-border-color: #fd7e14;" for="vis-exp-penutup-atas" id="vis-exp-penutup-atas-lbl">Penutup</label>
                                        </div>
                                    </div>
                                    
                                    <!-- Expand Bawah -->
                                    <div class="toolbar-pill-group flex-nowrap d-flex align-items-center rounded-pill px-2 py-1" id="group-bawah-expand" style="background: rgba(245, 235, 250, 0.8); border: 1px solid rgba(200, 150, 220, 0.5);">
                                        <span class="toolbar-pill-label bawah text-nowrap fw-bold me-2" style="color: #6f42c1; font-size: 0.75rem;">EXPAND BAWAH</span>
                                        <div class="d-flex gap-1 flex-nowrap">

                                            <input type="checkbox" class="btn-check" id="vis-exp-kakibalok" autocomplete="off">
                                            <label class="btn btn-outline-secondary btn-sm text-nowrap py-0 px-2" style="font-size: 0.75rem; border-radius: 6px; --bs-btn-color: #6f42c1; --bs-btn-border-color: #6f42c1; --bs-btn-hover-bg: #6f42c1; --bs-btn-hover-border-color: #6f42c1;" for="vis-exp-kakibalok" id="vis-exp-kakibalok-lbl">Kaki Balok</label>
                                            
                                            <input type="checkbox" class="btn-check" id="vis-exp-penyangga-bawah" autocomplete="off">
                                            <label class="btn btn-outline-secondary btn-sm text-nowrap py-0 px-2" style="font-size: 0.75rem; border-radius: 6px; --bs-btn-color: #6f42c1; --bs-btn-border-color: #6f42c1; --bs-btn-hover-bg: #6f42c1; --bs-btn-hover-border-color: #6f42c1;" for="vis-exp-penyangga-bawah" id="vis-exp-penyangga-bawah-lbl">Penyangga</label>
                
                                            <input type="checkbox" class="btn-check" id="vis-exp-penutup-bawah" autocomplete="off">
                                            <label class="btn btn-outline-secondary btn-sm text-nowrap py-0 px-2" style="font-size: 0.75rem; border-radius: 6px; --bs-btn-color: #6f42c1; --bs-btn-border-color: #6f42c1; --bs-btn-hover-bg: #6f42c1; --bs-btn-hover-border-color: #6f42c1;" for="vis-exp-penutup-bawah" id="vis-exp-penutup-bawah-lbl">Penutup</label>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="d-flex flex-nowrap align-items-center gap-2 flex-shrink-0 pe-1">
                                    <div class="toolbar-pill-group px-2 py-1 flex-nowrap rounded-pill d-flex align-items-center" style="background: rgba(255, 255, 255, 0.5); border: 1px solid rgba(220, 220, 220, 0.5);">
                                        <div class="btn-group flex-nowrap gap-1" role="group">
                                            <button type="button" class="btn btn-outline-secondary btn-sm py-0 px-2 active text-nowrap" style="font-size: 0.75rem; border-radius: 6px; --bs-btn-color: #6c757d;" id="dimensionBtn">Dimensi</button>
                                            <button type="button" class="btn btn-outline-secondary btn-sm py-0 px-2 active text-nowrap" style="font-size: 0.75rem; border-radius: 6px; --bs-btn-color: #6c757d;" id="gridBtn">Grid</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div id="canvas-loading" class="position-absolute top-50 start-50 translate-middle text-muted d-flex align-items-center gap-2" style="z-index: 10;">
                                <span class="spinner-border spinner-border-sm" role="status"></span>
                                Preparing 3D engine...
                            </div>
                            <div id="canvasWrap" class="w-100 h-100 position-absolute" style="top: 0; left: 0;">
                                <canvas id="crate-canvas" class="w-100 h-100" style="display: block;"></canvas>
                            </div>

                            <div class="legend"><strong>Kontrol 3D:</strong> klik kiri untuk memutar, scroll untuk zoom, klik kanan untuk menggeser.</div>
                            <div class="status"><span class="status-dot"></span><span id="statusText">Model siap dan interaktif</span></div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-2-5">
                    @php
                        $totalWoodLength = 0;
                        $materialResume = [];
                        
                        $areaKerja = 0;
                        if (isset($calculation)) {
                            $P_m = (float)($calculation->length ?? 0) / 1000;
                            $L_m = (float)($calculation->width ?? 0) / 1000;
                            $T_m = (float)($calculation->height ?? 0) / 1000;
                            $areaKerja = 2 * (($P_m * $L_m) + ($P_m * $T_m) + ($L_m * $T_m));
                        }

                        foreach (isset($calculation) && $calculation->details ? $calculation->details : [] as $detail) {
                            $matKode = $detail->material->kode ?? $detail->material->code ?? '-';
                            if ($matKode !== '-' && $matKode !== '') {
                                $len = (float)($detail->total_length ?? 0);
                                $totalWoodLength += $len;

                                $matWoodType = $detail->material->material_type ?? null;
                                if ($matWoodType) {
                                    $matNama = ucwords(strtolower($matWoodType));
                                } else {
                                    $matNama = $detail->material->nama ?? $matKode;
                                }

                                if (!isset($materialResume[$matKode])) {
                                    $materialResume[$matKode] = [
                                        'kode' => $matKode,
                                        'nama' => $matNama,
                                        'length' => 0,
                                        'quantity' => 0,
                                        'section' => $detail->section ?? '',
                                        'part_name' => $detail->part_name ?? ''
                                    ];
                                }
                                $materialResume[$matKode]['length'] += $len;
                                $materialResume[$matKode]['quantity'] += (float)($detail->total_quantity ?? 0);
                            }
                        }
                    @endphp
                    <div class="panel-card h-100 d-flex flex-column border-0 shadow-sm p-4">
                        <div class="d-flex align-items-center gap-2 mb-3 border-bottom pb-3">
                            <span class="material-symbols-rounded text-success" style="font-size: 26px;">analytics</span>
                            <h5 class="fw-bold mb-0 text-dark" style="font-size: 16px;">Resume Kebutuhan Kayu</h5>
                        </div>

                        <!-- KPI Widgets -->
                        <div class="row g-3 mb-4">
                            <div class="col-6">
                                <div class="p-3 rounded bg-light border d-flex flex-column h-100 justify-content-center">
                                    <span class="text-muted fw-bold text-uppercase" style="font-size: 11px; letter-spacing: 0.05em;">Total Panjang</span>
                                    <div class="mt-1 d-flex align-items-baseline gap-1">
                                        <span class="fw-extrabold text-dark" id="resume-total-panjang" style="font-size: 22px;">{{ number_format($totalWoodLength, 2, ',', '.') }}</span>
                                        <span class="fw-bold text-muted" style="font-size: 14px;">m</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="p-3 rounded bg-light border d-flex flex-column h-100 justify-content-center" title="Total luas seluruh area kerja / box luar">
                                    <span class="text-muted fw-bold text-uppercase" style="font-size: 11px; letter-spacing: 0.05em;">Total Luas</span>
                                    <div class="mt-1 d-flex align-items-baseline gap-1">
                                        <span class="fw-extrabold text-dark" id="resume-total-luas" style="font-size: 22px;">{{ number_format($areaKerja, 2, ',', '.') }}</span>
                                        <span class="fw-bold text-muted" style="font-size: 14px;">m²</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- List Per Material -->
                        <div class="d-flex flex-column flex-grow-1 overflow-hidden">
                            <span class="text-muted fw-bold text-uppercase d-block mb-3 flex-shrink-0" style="font-size: 12px; letter-spacing: 0.05em;">Kebutuhan Per Material</span>
                            
                            <div class="flex-grow-1 overflow-auto pe-2">
                                <div class="d-flex flex-column gap-3" id="resume-material-container">
                                    @forelse($materialResume as $mat)
                                    <div class="p-3 border rounded bg-white shadow-xs hover-shadow transition">
                                        <div class="d-flex flex-column gap-2 w-100">
                                            <div class="d-flex align-items-start">
                                                <span class="badge text-white fw-bold py-2 px-3 text-wrap text-start" style="font-size: 13px; background-color: var(--navy); line-height: 1.4;">{{ $mat['nama'] }}</span>
                                            </div>
                                            <div class="d-flex justify-content-between align-items-end w-100">
                                                <div class="text-dark fw-bold text-wrap pe-2" style="font-size: 14px; line-height: 1.4; word-break: break-word;" title="{{ $mat['kode'] }}">
                                                    {{ $mat['kode'] }}
                                                </div>
                                                <div class="text-end flex-shrink-0">
                                                    @if($mat['section'] === 'Inner Box')
                                                        <span class="fw-extrabold text-success" style="font-size: 18px;">{{ number_format($mat['quantity'], 0, ',', '.') }}</span>
                                                        <span class="text-muted fw-bold ms-1" style="font-size: 14px;">Pcs</span>
                                                    @elseif($mat['section'] === 'Terpal' || ($mat['section'] === 'Additional' && (stripos($mat['nama'], 'Terpal') !== false || stripos($mat['part_name'], 'Terpal') !== false)))
                                                        <span class="fw-extrabold text-success" style="font-size: 18px;">{{ number_format($mat['quantity'], 2, ',', '.') }}</span>
                                                        <span class="text-muted fw-bold ms-1" style="font-size: 14px;">m²</span>
                                                    @elseif($mat['section'] === 'Carton' || ($mat['section'] === 'Additional' && (stripos($mat['nama'], 'Carton') !== false || stripos($mat['part_name'], 'Carton') !== false)))
                                                        <span class="fw-extrabold text-success" style="font-size: 18px;">{{ number_format($mat['quantity'], 0, ',', '.') }}</span>
                                                        <span class="text-muted fw-bold ms-1" style="font-size: 14px;">Lembar</span>
                                                    @else
                                                        <span class="fw-extrabold text-success" style="font-size: 18px;">{{ number_format($mat['length'], 1, ',', '.') }}</span>
                                                        <span class="text-muted fw-bold ms-1" style="font-size: 14px;">m</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @empty
                                    <div class="text-center text-muted py-4" style="font-size: 14px;">Belum ada data kebutuhan material</div>
                                    @endforelse
                                </div>
                            </div>
                        </div>

                        <!-- Card Total Harga -->
                        <div class="mt-3 p-3 rounded-3 border bg-white shadow-sm">
                            <div class="d-flex align-items-center justify-content-between mb-1">
                                <span class="fw-bold text-dark text-uppercase" style="font-size: 13px; letter-spacing: 0.05em;">Total Packing</span>
                                <a href="javascript:void(0)" class="badge bg-primary bg-opacity-10 text-primary text-decoration-none transition px-2 py-1" style="font-size: 10px;" data-bs-toggle="modal" data-bs-target="#costResumeModal">Detail &rsaquo;</a>
                            </div>
                            <div>
                                <span class="fw-black text-success" id="cost-total-packing-resume" style="font-size: 20px;">Rp {{ number_format($totalCost, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        {{-- Row Material Sorting Visualizer --}}
        <div class="row g-3 mb-3">
            <div class="col-12">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-3 bg-white material-visual-card">
                    <!-- Header -->
                    <div class="card-header bg-white border-bottom-0 pt-4 pb-3 px-4 d-flex flex-wrap justify-content-between align-items-center gap-3 material-visual-header">
                        <div class="d-flex align-items-center gap-3">
                            <div class="bg-primary bg-opacity-10 text-primary rounded-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                                <span class="material-symbols-rounded fs-3">view_in_ar</span>
                            </div>
                            <div>
                                <h5 class="mb-0 fw-bold">Visualisasi Potongan Material</h5>
                                <small class="text-muted">Preview potongan material dalam skala 3D</small>
                            </div>
                        </div>
                        <div class="d-flex gap-2 align-items-center ms-auto">
                            <button type="button" class="btn btn-light bg-white border shadow-sm d-flex align-items-center me-2" data-bs-toggle="modal" data-bs-target="#materialDetailModal"><span class="material-symbols-rounded fs-6 me-2">list</span>Lihat Daftar</button>
                            <button type="button" class="btn btn-light bg-white border shadow-sm d-flex align-items-center justify-content-center" id="btn-mat-left" style="width: 38px; height: 38px; border-radius: 8px;">
                                <span class="material-symbols-rounded">chevron_left</span>
                            </button>
                            <button type="button" class="btn btn-light bg-white border border-primary text-primary shadow-sm d-flex align-items-center justify-content-center" id="btn-mat-right" style="width: 38px; height: 38px; border-radius: 8px;">
                                <span class="material-symbols-rounded">chevron_right</span>
                            </button>
                        </div>
                    </div>
                    
                    </div>
                    
                    <!-- 3D Canvas -->
                    <div class="position-relative border-top material-visual-stage">
                        <div id="material-sorting-container" style="width: 100%; height: 450px; position: relative; overflow: hidden;"></div>
                        <div id="material-labels-container" style="position: absolute; top: 0; left: 0; pointer-events: none; width: 100%; height: 100%; overflow: hidden;"></div>
                    </div>

                    <!-- Legend / Categories -->
                    <div class="px-4 py-2 bg-light border-top d-flex flex-wrap gap-4 align-items-center justify-content-center material-visual-legend">
                        <span class="text-muted fw-bold" style="font-size: 11px;">KATEGORI WARNA:</span>
                        <div class="d-flex align-items-center text-muted fw-semibold" style="font-size: 12px;">
                            <span class="d-inline-block rounded-circle me-2" style="background: #c19a6b; width: 10px; height: 10px;"></span> Kayu Papan
                        </div>
                        <div class="d-flex align-items-center text-muted fw-semibold" style="font-size: 12px;">
                            <span class="d-inline-block rounded-circle me-2" style="background: #5c4033; width: 10px; height: 10px;"></span> Balok
                        </div>
                        <div class="d-flex align-items-center text-muted fw-semibold" style="font-size: 12px;">
                            <span class="d-inline-block rounded-circle me-2" style="background: #f3c583; width: 10px; height: 10px;"></span> Panel / Triplek
                        </div>
                    </div>
                    
                    <!-- Footer -->
                    <div class="bg-light px-4 pb-3 pt-2 material-visual-footer">
                        <div class="d-flex align-items-center text-primary text-opacity-75" style="font-size: 12px;">
                            <span class="material-symbols-rounded fs-6 me-2">info</span>
                            <span>Dimensi dalam milimeter (mm). Visualisasi berskala untuk memperjelas perbandingan ukuran material.</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </div>
        
        </div> <!-- End of blur container -->
    </div>

    <!-- Modal Material Detail -->
    <div class="modal fade" id="materialDetailModal" tabindex="-1" aria-labelledby="materialDetailModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable" style="max-width: 1380px;">
            <div class="modal-content border-0 shadow-lg material-detail-modal-content">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold text-navy" id="materialDetailModalLabel">
                        <span class="material-symbols-rounded text-primary align-middle me-2" style="font-size: 20px;">inventory_2</span>Detail Semua Material
                    </h5>
                    <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body pt-3 pb-4">
                    @include('packaging.partials._table-material')
                </div>
            </div>
        </div>
    </div>

    @include('packaging.partials._cost_resume_modal')
    @include('packaging.partials.modals._product-setup')


    <!-- Modal Validasi Gabungan -->
    @if(auth()->check() && auth()->user()->hasRole('admin'))
    <div class="modal fade" id="validasiModal" tabindex="-1" aria-labelledby="validasiModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" style="max-width: 98vw; height: 98vh; margin: 1vh auto;">
            <div class="modal-content border-0 shadow-sm rounded-4 validation-modal-content" style="height: 100%;">
                
                <div class="modal-header border-bottom-0 pb-0 pt-4 px-4 bg-white rounded-top-4 flex-column align-items-start">
                    <div class="d-flex justify-content-between w-100 align-items-center mb-3">
                        <h5 class="modal-title fw-bold" id="validasiModalLabel" style="font-size: 16px; color: #0b2a55;">
                            <span class="material-symbols-rounded align-middle me-2" style="color: #0e8a63;">fact_check</span>
                            Validasi Data Packaging
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    
                    <!-- Nav Tabs -->
                    <ul class="nav nav-tabs border-0 w-100" id="validasiTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active fw-bold" id="material-tab" data-bs-toggle="tab" data-bs-target="#tab-material" type="button" role="tab" aria-controls="tab-material" aria-selected="true" style="font-size: 13px;">
                                <span class="material-symbols-rounded align-middle text-sm me-1">inventory</span> Master Material
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link fw-bold text-secondary" id="perhitungan-tab" data-bs-toggle="tab" data-bs-target="#tab-perhitungan" type="button" role="tab" aria-controls="tab-perhitungan" aria-selected="false" style="font-size: 13px;">
                                <span class="material-symbols-rounded align-middle text-sm me-1">functions</span> Rumus Perhitungan
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link fw-bold text-secondary" id="pendukung-tab" data-bs-toggle="tab" data-bs-target="#tab-pendukung" type="button" role="tab" aria-controls="tab-pendukung" aria-selected="false" style="font-size: 13px;">
                                <span class="material-symbols-rounded align-middle text-sm me-1">settings</span> Data Pendukung
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link fw-bold text-secondary" id="fastener-tab" data-bs-toggle="tab" data-bs-target="#tab-fastener" type="button" role="tab" aria-controls="tab-fastener" aria-selected="false" style="font-size: 13px;">
                                <span class="material-symbols-rounded align-middle text-sm me-1">hardware</span> Fastener
                            </button>
                        </li>
                    </ul>
                </div>
                
                <div class="modal-body p-0">
                    <div class="tab-content" id="validasiTabsContent">
                        
                        <!-- TAB: MASTER MATERIAL -->
                        <div class="tab-pane fade show active p-4" id="tab-material" role="tabpanel" aria-labelledby="material-tab">
                            @include('packaging.Validasi-data.partials._material')
                        </div>
                        
                        <!-- TAB: RUMUS PERHITUNGAN -->
                        <div class="tab-pane fade p-4" id="tab-perhitungan" role="tabpanel" aria-labelledby="perhitungan-tab">
                            @include('packaging.Validasi-data.partials._perhitungan')
                        </div>
                        
                        <!-- TAB: FASTENER -->
                        <div class="tab-pane fade p-4" id="tab-fastener" role="tabpanel" aria-labelledby="fastener-tab">
                            @include('packaging.Validasi-data.partials._fastener')
                        </div>
                        
                        <!-- TAB: DATA PENDUKUNG -->
                        <div class="tab-pane fade" id="tab-pendukung" role="tabpanel" aria-labelledby="pendukung-tab">
                            <form action="{{ route('packaging.validasi_data.settings.update') }}" method="POST">
                                @csrf
                                <div class="p-4 bg-white m-4 rounded-3 border">
                                    <p class="text-secondary mb-4" style="font-size: 12px;">Ubah konfigurasi tarif dasar untuk kalkulasi packaging. Perubahan akan langsung disimpan ke sistem.</p>

                                    <!-- Manpower Rate -->
                                    <div class="mb-3">
                                        <label class="form-label fw-bold" style="font-size: 12px; color: #0b2a55;">Rate Manpower (Rp/Jam)</label>
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text bg-light border-end-0 fw-bold text-secondary">Rp</span>
                                            <input type="number" step="any" name="manpower_rate" value="{{ $packagingSettings['manpower_rate'] ?? 10000 }}" class="form-control border-start-0 ps-0 fw-bold" required>
                                        </div>
                                    </div>

                                    <!-- Harga Paku -->
                                    <div class="mb-3">
                                        <label class="form-label fw-bold" style="font-size: 12px; color: #0b2a55;">Harga Paku (Rp/Kg)</label>
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text bg-light border-end-0 fw-bold text-secondary">Rp</span>
                                            <input type="number" step="any" name="nails_price_per_kg" value="{{ $packagingSettings['nails_price_per_kg'] ?? 25000 }}" class="form-control border-start-0 ps-0 fw-bold" required>
                                        </div>
                                    </div>

                                    <!-- Berat Paku -->
                                    <div class="mb-4">
                                        <label class="form-label fw-bold" style="font-size: 12px; color: #0b2a55;">Berat Paku (Kg/Pcs)</label>
                                        <div class="input-group input-group-sm">
                                            <input type="number" step="any" name="nails_weight_per_piece" value="{{ $packagingSettings['nails_weight_per_piece'] ?? 0.025 }}" class="form-control border-end-0 fw-bold" required>
                                            <span class="input-group-text bg-light border-start-0 fw-bold text-secondary">Kg</span>
                                        </div>
                                    </div>
                                    
                                    <div class="text-end">
                                        <button type="submit" class="btn rounded-3 px-4 fw-bold text-white shadow-sm" style="background-color: #1769e8; font-size: 12px;">Simpan Perubahan</button>
                                    </div>
                                </div>
                            </form>
                        </div>

                    </div>
                </div>
                
                <div class="modal-footer border-top-0 py-3 px-4 bg-white rounded-bottom-4">
                    <button type="button" class="btn btn-light rounded-3 px-4 fw-bold text-secondary shadow-sm" data-bs-dismiss="modal" style="font-size: 12px;">Tutup</button>
                </div>
            </div>
        </div>
    </div>
    @endif

    <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/three@0.128.0/examples/js/controls/OrbitControls.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/three@0.128.0/examples/js/loaders/GLTFLoader.js"></script>
    <script>
        // Global error listener to display runtime errors in the canvas loading overlay
        window.addEventListener('error', function(e) {
            console.error("Visualizer error caught globally:", e);
            showVisualizerError(e);
        });

        function showVisualizerError(err) {
            const loading = document.getElementById('canvas-loading');
            if (loading) {
                loading.classList.remove('d-none');
                loading.classList.add('d-flex');
                loading.innerHTML = `<div class="text-danger text-center">
                    <span class="material-symbols-rounded block mb-1" style="font-size: 24px;">error</span>
                    <div class="fw-bold" style="font-size: 13px;">Gagal Memuat 3D Engine</div>
                    <div style="font-size: 11px; opacity: 0.85; max-width: 280px; margin: 0 auto; line-height: 1.3;">
                        ${err.message || err}<br>
                        <span class="text-muted" style="font-size: 10px;">Pastikan koneksi internet aktif untuk memuat CDN Three.js.</span>
                    </div>
                </div>`;
            }
        }


        function packagingIsDarkMode() {
            const html = document.documentElement;
            const body = document.body;
            const explicitTheme = (
                html.getAttribute('data-bs-theme') ||
                html.getAttribute('data-theme') ||
                body?.getAttribute('data-bs-theme') ||
                body?.getAttribute('data-theme') ||
                ''
            ).toLowerCase();

            if (explicitTheme === 'dark') return true;
            if (explicitTheme === 'light') return false;

            if (
                html.classList.contains('dark') ||
                body?.classList.contains('dark') ||
                body?.classList.contains('dark-mode') ||
                body?.classList.contains('theme-dark')
            ) {
                return true;
            }

            return Boolean(window.matchMedia?.('(prefers-color-scheme: dark)').matches);
        }

        function packagingThemePalette() {
            const dark = packagingIsDarkMode();

            return dark
                ? {
                    dark: true,
                    sceneBackground: 0x0b1220,
                    gridCenter: 0x475569,
                    gridLine: 0x253247,
                    materialSceneBackground: 0x0b1220,
                    materialGridCenter: 0x475569,
                    materialGridLine: 0x1e293b,
                    dimensionLine: 0x60a5fa,
                    dimensionText: '#93c5fd',
                    labelBackground: 'rgba(17, 24, 39, .96)',
                    labelBorder: '#475569',
                    labelText: '#f8fafc',
                }
                : {
                    dark: false,
                    sceneBackground: 0xf3f7fc,
                    gridCenter: 0xaab7c8,
                    gridLine: 0xd5dde8,
                    materialSceneBackground: 0xf8fafc,
                    materialGridCenter: 0xe2e8f0,
                    materialGridLine: 0xf1f5f9,
                    dimensionLine: 0x3b82f6,
                    dimensionText: '#2563eb',
                    labelBackground: '#ffffff',
                    labelBorder: '#e2e8f0',
                    labelText: '#0f172a',
                };
        }

        function syncPackagingThemeClass() {
            const dark = packagingIsDarkMode();
            document.querySelectorAll('.crate-page').forEach((element) => {
                element.classList.toggle('packaging-dark', dark);
            });
            document.body?.classList.toggle('packaging-auto-dark', dark);
            return dark;
        }

        let cachedWebGLAvailability = null;

        function isWebGLAvailable() {
            if (cachedWebGLAvailability !== null) {
                return cachedWebGLAvailability;
            }

            try {
                const testCanvas = document.createElement('canvas');
                const gl = testCanvas.getContext('webgl2', {
                    antialias: false,
                    preserveDrawingBuffer: false
                }) || testCanvas.getContext('webgl', {
                    antialias: false,
                    preserveDrawingBuffer: false
                }) || testCanvas.getContext('experimental-webgl');

                if (!gl) {
                    cachedWebGLAvailability = false;
                    return false;
                }

                const loseContext = gl.getExtension('WEBGL_lose_context');
                loseContext?.loseContext();
                cachedWebGLAvailability = true;
                return true;
            } catch (error) {
                console.error('Pengecekan WebGL gagal:', error);
                cachedWebGLAvailability = false;
                return false;
            }
        }

        function disposeThreeRenderer(targetRenderer) {
            if (!targetRenderer) return;

            try {
                targetRenderer.setAnimationLoop?.(null);
                targetRenderer.dispose?.();
                targetRenderer.forceContextLoss?.();

                if (targetRenderer.domElement?.parentNode) {
                    targetRenderer.domElement.parentNode.removeChild(targetRenderer.domElement);
                }
            } catch (error) {
                console.warn('Renderer tidak dapat dibersihkan:', error);
            }
        }

        // Global variables for 3D Visualizer
        let scene, camera, renderer, controls, woodTexture, frameMaterial, supportMaterial, coverMaterial, plywoodMaterial;
        let modelGroup, dimensionGroup, ground, grid;
        let nailModel = null;
        let dimensionsVisible = true;
        let currentView = 'iso';
        let currentMaxDimension = 2;
        let activeDetails = [];
        let crate3DInitialized = false;
        let crate3DInitializing = false;
        let crateAnimationFrameId = null;
        let crateResizeObserver = null;
        const calcId = "{{ $calculation->id ?? 'new' }}";

        function init3D() {
            if (crate3DInitialized || crate3DInitializing) return true;
            crate3DInitializing = true;

            try {
                const container = document.getElementById('crate-canvas-container');
                const canvas = document.getElementById('crate-canvas');
                const loading = document.getElementById('canvas-loading');

                if (!container || !canvas) {
                    crate3DInitializing = false;
                    return false;
                }

                if (typeof THREE === 'undefined') {
                    throw new Error('Three.js belum berhasil dimuat.');
                }

                if (!isWebGLAvailable()) {
                    throw new Error('Browser tidak dapat membuat WebGL context. Periksa hardware acceleration atau context WebGL lain yang masih aktif.');
                }

                if (renderer) {
                    disposeThreeRenderer(renderer);
                    renderer = null;
                }

                // Create scene
                scene = new THREE.Scene();
                scene.background = new THREE.Color(packagingThemePalette().sceneBackground);

                // Groups
                modelGroup = new THREE.Group();
                dimensionGroup = new THREE.Group();
                scene.add(modelGroup);
                scene.add(dimensionGroup);

                // Camera setup
                camera = new THREE.PerspectiveCamera(42, container.clientWidth / container.clientHeight, 0.1, 1000);
                camera.position.set(3, 3, 4);

                // Renderer setup
                renderer = new THREE.WebGLRenderer({
                    canvas: canvas,
                    antialias: false,
                    alpha: false,
                    preserveDrawingBuffer: false,
                    powerPreference: 'default',
                    failIfMajorPerformanceCaveat: false
                });
                renderer.setSize(
                    Math.max(container.clientWidth, 320),
                    Math.max(container.clientHeight, 260),
                    false
                );
                renderer.setPixelRatio(Math.min(window.devicePixelRatio || 1, 1.25));
                renderer.shadowMap.enabled = false;

                // Load Nail GLB Model
                const gltfLoader = new THREE.GLTFLoader();
                gltfLoader.load('/glb-3D/cc0_-_nail.glb', function(gltf) {
                    nailModel = gltf.scene;
                    
                    // The GLB has two nails (Nail and NailRust) and they are tilted.
                    // Keep only 'Nail' (silver) and make it perfectly straight.
                    nailModel.traverse((child) => {
                        if (child.name.includes('NailRust')) {
                            child.visible = false;
                        } else if (child.name === 'Nail') {
                            child.position.set(0, 0, 0);
                            // Rotate the nail so its local Z axis (length) aligns with the Y axis (UP)
                            // Flipped so the head is on top
                            child.rotation.set(Math.PI / 2, 0, 0);
                        }
                        
                        if (child.isMesh) {
                            child.material = new THREE.MeshStandardMaterial({
                                color: 0xc0c0c0,
                                metalness: 0.8,
                                roughness: 0.2
                            });
                        }
                    });
                    
                    // Auto-scale to 5cm (0.05 units)
                    const box = new THREE.Box3().setFromObject(nailModel);
                    const size = new THREE.Vector3();
                    box.getSize(size);
                    const maxDim = Math.max(size.x, size.y, size.z);
                    if (maxDim > 0) {
                        const scaleFactor = 0.05 / maxDim;
                        nailModel.scale.set(scaleFactor, scaleFactor, scaleFactor);
                    }
                    
                    if (typeof drawCrate === 'function') drawCrate();
                }, undefined, function(error) {
                    console.error("Error loading nail model:", error);
                });

                // Controls setup
                controls = new THREE.OrbitControls(camera, canvas);
                controls.enableDamping = true;
                controls.dampingFactor = 0.08;
                controls.screenSpacePanning = true;
                controls.minDistance = 2;
                controls.maxDistance = 60;

                // Lights
                scene.add(new THREE.HemisphereLight(0xffffff, 0x637184, 1.25));

                const keyLight = new THREE.DirectionalLight(0xffffff, 1.35);
                keyLight.position.set(6, 9, 7);
                keyLight.castShadow = true;
                keyLight.shadow.mapSize.width = 2048;
                keyLight.shadow.mapSize.height = 2048;
                scene.add(keyLight);

                const fillLight = new THREE.DirectionalLight(0xbfd8ff, 0.55);
                fillLight.position.set(-7, 4, -5);
                scene.add(fillLight);

                // Ground plane (ShadowMaterial makes it transparent to match the sky color while capturing shadows)
                const groundMaterial = new THREE.ShadowMaterial({ opacity: 0.15 });
                ground = new THREE.Mesh(new THREE.PlaneGeometry(70, 70), groundMaterial);
                ground.rotation.x = -Math.PI / 2;
                ground.position.y = -0.02;
                ground.receiveShadow = true;
                scene.add(ground);

                // Grid helper
                const initialTheme = packagingThemePalette();
                grid = new THREE.GridHelper(40, 40, initialTheme.gridCenter, initialTheme.gridLine);
                grid.position.y = 0.002;
                grid.material.transparent = true;
                grid.material.opacity = 0.45;
                scene.add(grid);

                // Create procedural wood material
                woodTexture = makeWoodTexture();
                frameMaterial = new THREE.MeshStandardMaterial({
                    color: 0xffffff,
                    map: woodTexture,
                    roughness: 0.69,
                    metalness: 0.0
                });
                supportMaterial = frameMaterial.clone();
                supportMaterial.color = new THREE.Color('#94714f'); // Darker warm wood brown tint for supports
                coverMaterial = frameMaterial.clone();
                coverMaterial.color = new THREE.Color('#d59a59');
                coverMaterial.roughness = 0.76;
                plywoodMaterial = frameMaterial.clone();
                plywoodMaterial.color = new THREE.Color('#e4c394');
                plywoodMaterial.roughness = 0.84;

                // Hide loading message
                if (loading) {
                    loading.classList.remove('d-flex');
                    loading.classList.add('d-none');
                }

                // Resize observer
                if ('ResizeObserver' in window) {
                    crateResizeObserver?.disconnect();
                    crateResizeObserver = new ResizeObserver(onWindowResize);
                    crateResizeObserver.observe(container);
                }
                window.removeEventListener('resize', onWindowResize);
                window.addEventListener('resize', onWindowResize);

                crate3DInitialized = true;
                crate3DInitializing = false;

                if (crateAnimationFrameId === null) {
                    animateCrateScene();
                }

                return true;
            } catch (err) {
                crate3DInitialized = false;
                crate3DInitializing = false;

                if (renderer) {
                    disposeThreeRenderer(renderer);
                    renderer = null;
                }

                console.error("Error inside init3D:", err);
                showVisualizerError(err);
                return false;
            }
        }

        function animateCrateScene() {
            if (!renderer || !scene || !camera) {
                crateAnimationFrameId = null;
                return;
            }

            crateAnimationFrameId = window.requestAnimationFrame(animateCrateScene);
            controls?.update();

            if (camera && controls && dimensionGroup) {
                const dist = camera.position.distanceTo(controls.target);
                const maxD = typeof currentMaxDimension !== 'undefined' ? currentMaxDimension : 2;
                const baseDist = Math.max(1.5, maxD * 1.5);
                const scaleFactor = dist / baseDist;

                dimensionGroup.children.forEach((child) => {
                    if (child.isSprite && child.userData.baseScale) {
                        child.scale.copy(child.userData.baseScale).multiplyScalar(scaleFactor);
                    }
                });
            }

            const gl = renderer.getContext?.();
            if (!gl?.isContextLost?.()) {
                renderer.render(scene, camera);
            }
        }

        function makeWoodTexture() {
            const canvas = document.createElement('canvas');
            canvas.width = 512;
            canvas.height = 128;
            const ctx = canvas.getContext('2d');

            const gradient = ctx.createLinearGradient(0, 0, 0, canvas.height);
            gradient.addColorStop(0, '#8e6f50'); // Muted medium tan wood
            gradient.addColorStop(0.5, '#a48464'); // Softer medium wood brown
            gradient.addColorStop(1, '#6f5135'); // Deep cocoa wood brown
            ctx.fillStyle = gradient;
            ctx.fillRect(0, 0, canvas.width, canvas.height);

            let seed = 8128;
            function random() {
                seed = (seed * 9301 + 49297) % 233280;
                return seed / 233280;
            }

            for (let y = 8; y < canvas.height; y += 12) {
                ctx.beginPath();
                for (let x = 0; x <= canvas.width; x += 8) {
                    const wave = Math.sin((x + y * 3) * 0.035) * 2.2;
                    const noise = (random() - 0.5) * 2.1;
                    const py = y + wave + noise;
                    if (x === 0) ctx.moveTo(x, py); else ctx.lineTo(x, py);
                }
                ctx.strokeStyle = 'rgba(87,47,19,' + (0.12 + random() * 0.10) + ')';
                ctx.lineWidth = 1 + random();
                ctx.stroke();
            }

            for (let i = 0; i < 9; i += 1) {
                const x = random() * canvas.width;
                const y = random() * canvas.height;
                const r = 3 + random() * 8;
                ctx.beginPath();
                ctx.ellipse(x, y, r * 2, r, random() * Math.PI, 0, Math.PI * 2);
                ctx.strokeStyle = 'rgba(92,50,20,.24)';
                ctx.lineWidth = 1.4;
                ctx.stroke();
            }

            const texture = new THREE.CanvasTexture(canvas);
            texture.wrapS = THREE.RepeatWrapping;
            texture.wrapT = THREE.RepeatWrapping;
            return texture;
        }

        function clearGroup(group) {
            if (!group) return;
            while (group.children.length) {
                const child = group.children.pop();
                if (child.geometry) child.geometry.dispose();
                if (child.material && child.material.dispose && child.material !== frameMaterial && child.material !== supportMaterial) {
                    if (child.material.map && child.material.map.dispose) child.material.map.dispose();
                    child.material.dispose();
                }
                if (child.children && child.children.length) clearGroup(child);
            }
        }

        function addBeam(size, position, material, name) {
            const geometry = new THREE.BoxGeometry(size.x, size.y, size.z);
            const mesh = new THREE.Mesh(geometry, material);
            mesh.position.copy(position);

            if (window.printSequenceStep) {
                const lowerName = name ? name.toLowerCase() : '';
                const isPenyangga = (lowerName.includes('arah panjang') || lowerName.includes('arah lebar') || lowerName.includes('horizontal') || lowerName.includes('vertikal')) && !lowerName.includes('papan');
                const isPenutup = (lowerName.includes('papan') || lowerName.includes('triplex') || lowerName.includes('penutup'));
                const isKakiBalok = lowerName.includes('kaki balok');
                const isDepanBelakang = lowerName.includes('depan') || lowerName.includes('belakang');
                const isKananKiri = lowerName.includes('kanan') || lowerName.includes('kiri');
                const isAtas = lowerName.includes('atas');
                const isBawah = lowerName.includes('bawah');

                // Helper for expand offsets
                const expandDir = (distance) => {
                    if (lowerName.includes('depan')) mesh.position.z += distance;
                    else if (lowerName.includes('belakang')) mesh.position.z -= distance;
                    else if (lowerName.includes('kanan')) mesh.position.x += distance;
                    else if (lowerName.includes('kiri')) mesh.position.x -= distance;
                    else if (lowerName.includes('atas')) mesh.position.y += distance;
                    else if (lowerName.includes('bawah')) mesh.position.y -= distance;
                };

                // HIDE LOGIC based on Step
                let shouldHide = false;
                
                if (window.printSequenceStep < 2 && isBawah && isPenutup) shouldHide = true;
                if (window.printSequenceStep < 3 && isDepanBelakang && isPenutup) shouldHide = true;
                if (window.printSequenceStep < 3 && isDepanBelakang && isPenyangga) shouldHide = true; // Muncul bersamaan di langkah 3
                if (window.printSequenceStep < 5 && isKananKiri && isPenutup) shouldHide = true;
                if (window.printSequenceStep < 5 && isKananKiri && isPenyangga) shouldHide = true; // Muncul bersamaan di langkah 5
                if (window.printSequenceStep < 7 && isAtas && isPenyangga) shouldHide = true;
                if (window.printSequenceStep < 8 && isAtas && isPenutup) shouldHide = true;
                
                // Extra rule: in these steps, hide anything that isn't specifically handled above but is part of the upper body
                if (window.printSequenceStep < 3 && !isBawah && !isKakiBalok) shouldHide = true;
                if (window.printSequenceStep < 5 && isKananKiri) shouldHide = true;
                if (window.printSequenceStep < 7 && isAtas) shouldHide = true;
                
                if (shouldHide) {
                    mesh.visible = false;
                }

                // EXPAND LOGIC based on Step
                if (!shouldHide) {
                    if (window.printSequenceStep === 1) {
                        if (isKakiBalok) mesh.position.y -= 0.6;
                    }
                    else if (window.printSequenceStep === 2) {
                        if ((isBawah && isPenyangga) || isKakiBalok) mesh.position.y -= 0.6;
                    }
                    else if (window.printSequenceStep === 3) {
                        if (isDepanBelakang && (isPenutup || isPenyangga)) expandDir(0.6); // Terbang bersama
                    }
                    else if (window.printSequenceStep === 4) {
                        // Terpasang di rangka, tidak ada yang di-expand
                    }
                    else if (window.printSequenceStep === 5) {
                        if (isKananKiri && (isPenutup || isPenyangga)) expandDir(0.6); // Terbang bersama
                    }
                    else if (window.printSequenceStep === 6) {
                        // Terpasang di rangka, tidak ada yang di-expand
                    }
                    else if (window.printSequenceStep === 7) {
                        if (isAtas && isPenyangga) expandDir(0.6);
                    }
                    else if (window.printSequenceStep === 8) {
                        if (isAtas && isPenutup) expandDir(0.85); // Kurangi sedikit agar tidak keluar frame
                    }
                    else if (window.printSequenceStep === 9) {
                        // FULLY EXPLODED VIEW
                        // if (isKakiBalok) mesh.position.y -= 1.0;
                        // if (isBawah && isPenyangga) mesh.position.y -= 0.6;
                        // if (isBawah && isPenutup) mesh.position.y -= 0.3;
                        if (isDepanBelakang && (isPenutup || isPenyangga)) expandDir(0.4);
                        if (isKananKiri && (isPenutup || isPenyangga)) expandDir(0.8);
                        if (isAtas && isPenyangga) expandDir(0.4);
                        if (isAtas && isPenutup) expandDir(0.85);
                    }
                }
            }

            mesh.castShadow = true;
            mesh.receiveShadow = true;
            mesh.name = name || 'Balok';
            modelGroup.add(mesh);
            return mesh;
        }

        function roundRect(ctx, x, y, w, h, radius) {
            const r = Math.min(radius, w / 2, h / 2);
            ctx.beginPath();
            ctx.moveTo(x + r, y);
            ctx.arcTo(x + w, y, x + w, y + h, r);
            ctx.arcTo(x + w, y + h, x, y + h, r);
            ctx.arcTo(x, y + h, x, y, r);
            ctx.arcTo(x, y, x + w, y, r);
            ctx.closePath();
        }

        function makeTextSprite(text) {
            const canvas = document.createElement('canvas');
            const ctx = canvas.getContext('2d');
            const fontSize = 30;
            ctx.font = '700 ' + fontSize + 'px Arial';
            const width = Math.ceil(ctx.measureText(text).width + 30);
            canvas.width = width;
            canvas.height = 54;

            const context = canvas.getContext('2d');
            context.fillStyle = 'rgba(255,255,255,.94)';
            context.strokeStyle = 'rgba(31,103,199,.32)';
            context.lineWidth = 2;
            roundRect(context, 1, 1, canvas.width - 2, canvas.height - 2, 12);
            context.fill();
            context.stroke();
            context.font = '700 ' + fontSize + 'px Arial';
            context.fillStyle = '#163d69';
            context.textBaseline = 'middle';
            context.textAlign = 'center';
            context.fillText(text, canvas.width / 2, canvas.height / 2 + 1);

            const texture = new THREE.CanvasTexture(canvas);
            texture.minFilter = THREE.LinearFilter;
            const material = new THREE.SpriteMaterial({ map: texture, depthTest: false, transparent: true });
            const sprite = new THREE.Sprite(material);
            const maxDim = typeof currentMaxDimension !== 'undefined' ? currentMaxDimension : 2;
            // Diperkecil dari 0.00225 menjadi 0.0006 agar tidak kebesaran (direvisi kembali dari 0.001)
            const scale = Math.max(0.15, maxDim) * 0.0006;
            sprite.scale.set(canvas.width * scale, canvas.height * scale, 1);
            sprite.userData.baseScale = sprite.scale.clone();
            sprite.renderOrder = 999;
            return sprite;
        }

        function addDimensionLine(start, end, label, labelOffset) {
            const material = new THREE.LineBasicMaterial({ color: 0x1f67c7, transparent: true, opacity: 0.88, depthTest: false });
            const points = [start.clone(), end.clone()];
            const geometry = new THREE.BufferGeometry().setFromPoints(points);
            const line = new THREE.Line(geometry, material);
            line.renderOrder = 990;
            dimensionGroup.add(line);

            const direction = end.clone().sub(start).normalize();
            const length = start.distanceTo(end);
            const maxD = typeof currentMaxDimension !== 'undefined' ? currentMaxDimension : 2;
            const tickSize = Math.max(maxD * 0.02, length * 0.022);
            const upCandidate = Math.abs(direction.y) < 0.8 ? new THREE.Vector3(0, 1, 0) : new THREE.Vector3(1, 0, 0);
            const tickDir = new THREE.Vector3().crossVectors(direction, upCandidate).normalize().multiplyScalar(tickSize);

            [start, end].forEach(function (p) {
                const tickGeo = new THREE.BufferGeometry().setFromPoints([p.clone().sub(tickDir), p.clone().add(tickDir)]);
                const tick = new THREE.Line(tickGeo, material.clone());
                tick.renderOrder = 990;
                dimensionGroup.add(tick);
            });

            const sprite = makeTextSprite(label);
            sprite.position.copy(start.clone().add(end).multiplyScalar(0.5).add(labelOffset || new THREE.Vector3()));
            dimensionGroup.add(sprite);
        }

        function addDimensions(L, W, H, F) {
            const maxD = typeof currentMaxDimension !== 'undefined' ? currentMaxDimension : 2;
            const gap = maxD * 0.08;
            const offset = F * 1.65 + gap;
            const labelGap = maxD * 0.075;
            
            addDimensionLine(
                new THREE.Vector3(-L / 2, 0, W / 2 + offset),
                new THREE.Vector3(L / 2, 0, W / 2 + offset),
                'Panjang ' + Math.round(L * 1000) + ' mm',
                new THREE.Vector3(0, labelGap, 0)
            );
            addDimensionLine(
                new THREE.Vector3(L / 2 + offset, 0, -W / 2),
                new THREE.Vector3(L / 2 + offset, 0, W / 2),
                'Lebar ' + Math.round(W * 1000) + ' mm',
                new THREE.Vector3(0, labelGap, 0)
            );
            addDimensionLine(
                new THREE.Vector3(L / 2 + offset, 0, -W / 2),
                new THREE.Vector3(L / 2 + offset, H, -W / 2),
                'Tinggi ' + Math.round(H * 1000) + ' mm',
                new THREE.Vector3(labelGap, 0, 0)
            );
        }

        function evenlySpaced(count, min, max) {
            const positions = [];
            if (count === 1) {
                positions.push((min + max) / 2);
            } else {
                for (let i = 0; i < count; i += 1) {
                    positions.push(min + (max - min) * (i / (count - 1)));
                }
            }
            return positions;
        }

        function setCameraView(view) {
            if (!controls || !camera) return;
            currentView = view;
            const d = currentMaxDimension * 2.15 + 1.2;
            let targetPosition;
            
            const T = parseFloat(document.querySelector('input[name="height"]').value) || 0;
            const centerY = (T / 1000) / 2 || currentMaxDimension * 0.38;

            if (view === 'front') targetPosition = new THREE.Vector3(0, centerY, d);
            else if (view === 'right') targetPosition = new THREE.Vector3(d, centerY, 0);
            else if (view === 'top') targetPosition = new THREE.Vector3(0, d, 0.001);
            else if (view === 'bottom') targetPosition = new THREE.Vector3(0, -d, 0.001);
            else targetPosition = new THREE.Vector3(d * 0.86, d * 0.66, d * 0.86);

            controls.target.set(0, centerY, 0);
            camera.position.copy(targetPosition);
            camera.near = 0.01;
            camera.far = Math.max(100, d * 20);
            camera.updateProjectionMatrix();
            controls.update();
        }

        function downloadScreenshot() {
            if (!renderer || !scene || !camera) return;
            renderer.render(scene, camera);
            const link = document.createElement('a');
            link.download = 'packaging-kayu-' + Date.now() + '.png';
            link.href = renderer.domElement.toDataURL('image/png');
            link.click();
        }

        function onWindowResize() {
            const container = document.getElementById('crate-canvas-container');
            if (!container || !camera || !renderer) return;
            camera.aspect = container.clientWidth / container.clientHeight;
            camera.updateProjectionMatrix();
            renderer.setSize(container.clientWidth, container.clientHeight);
        }

        function addNailsForPenyangga(mesh, faceId) {
            if (!nailModel || !mesh) return;

            const p = mesh.geometry.parameters;
            const w = p.width, h = p.height, d = p.depth;

            let axis = '';
            if (w >= h && w >= d) axis = 'x';
            else if (h >= w && h >= d) axis = 'y';
            else axis = 'z';

            let inset = 0.05;
            const maxL = Math.max(w, h, d);
            if (inset > maxL / 2) inset = maxL / 4;

            let offsets = [];
            if (axis === 'x') offsets = [w/2 - inset, -w/2 + inset];
            else if (axis === 'y') offsets = [h/2 - inset, -h/2 + inset];
            else offsets = [d/2 - inset, -d/2 + inset];

            offsets.forEach(off => {
                let n = nailModel.clone();
                // Scale up nails specifically for bottom area (Penyangga) so they are more visible
                n.scale.multiplyScalar(2.5);
                
                let nx = mesh.position.x, ny = mesh.position.y, nz = mesh.position.z;
                
                if (axis === 'x') nx += off;
                else if (axis === 'y') ny += off;
                else nz += off;

                let thk = Math.min(w, h, d);
                
                if (faceId === 'top') {
                    ny += thk/2; 
                    n.rotation.x = Math.PI;
                } else if (faceId === 'bottom') {
                    ny -= thk/2;
                } else if (faceId === 'front') {
                    nz += thk/2;
                    n.rotation.x = -Math.PI/2;
                } else if (faceId === 'back') {
                    nz -= thk/2;
                    n.rotation.x = Math.PI/2;
                } else if (faceId === 'right') {
                    nx += thk/2;
                    n.rotation.z = Math.PI/2;
                } else if (faceId === 'left') {
                    nx -= thk/2;
                    n.rotation.z = -Math.PI/2;
                }
                
                if (mesh.visible === false) n.visible = false;
                
                n.position.set(nx, ny, nz);
                modelGroup.add(n);
            });
        }

        function addNailsForBoard(mesh, faceId) {
            if (!nailModel || !mesh) return;

            const p = mesh.geometry.parameters;
            const w = p.width, h = p.height, d = p.depth;

            let ax1, ax2, sz1, sz2;
            if (faceId === 'top' || faceId === 'bottom') {
                ax1 = 'x'; sz1 = w;
                ax2 = 'z'; sz2 = d;
            } else if (faceId === 'front' || faceId === 'back') {
                ax1 = 'x'; sz1 = w;
                ax2 = 'y'; sz2 = h;
            } else { 
                ax1 = 'y'; sz1 = h;
                ax2 = 'z'; sz2 = d;
            }

            let inset1 = 0.03;
            let inset2 = 0.03;
            if (inset1 > sz1 / 2) inset1 = sz1 / 4;
            if (inset2 > sz2 / 2) inset2 = sz2 / 4;

            let corners = [
                { [ax1]: sz1/2 - inset1, [ax2]: sz2/2 - inset2 },
                { [ax1]: -sz1/2 + inset1, [ax2]: sz2/2 - inset2 },
                { [ax1]: sz1/2 - inset1, [ax2]: -sz2/2 + inset2 },
                { [ax1]: -sz1/2 + inset1, [ax2]: -sz2/2 + inset2 }
            ];

            corners.forEach(corner => {
                let n = nailModel.clone();
                // Scale up nails to make them more visible in the 3D visual
                n.scale.multiplyScalar(2.5);
                
                let nx = mesh.position.x;
                let ny = mesh.position.y;
                let nz = mesh.position.z;
                
                if (ax1 === 'x') nx += corner.x;
                if (ax1 === 'y') ny += corner.y;
                if (ax1 === 'z') nz += corner.z;
                
                if (ax2 === 'x') nx += corner.x;
                if (ax2 === 'y') ny += corner.y;
                if (ax2 === 'z') nz += corner.z;

                let thk = Math.min(w, h, d);
                
                if (faceId === 'top') {
                    ny += thk/2; 
                    n.rotation.x = Math.PI;
                } else if (faceId === 'bottom') {
                    ny -= thk/2;
                } else if (faceId === 'front') {
                    nz += thk/2;
                    n.rotation.x = -Math.PI/2;
                } else if (faceId === 'back') {
                    nz -= thk/2;
                    n.rotation.x = Math.PI/2;
                } else if (faceId === 'right') {
                    nx += thk/2;
                    n.rotation.z = Math.PI/2;
                } else if (faceId === 'left') {
                    nx -= thk/2;
                    n.rotation.z = -Math.PI/2;
                }
                
                if (mesh.visible === false) n.visible = false;
                
                n.position.set(nx, ny, nz);
                modelGroup.add(n);
            });
        }

        function getConfigValue(name, fallback = '') {
            const element = document.querySelector(`[name="${name}"]`);
            if (!element) return fallback;

            const value = element.value;
            return value === undefined || value === null || value === '' ? fallback : value;
        }

        function drawCrate(resetCamera = false) {
            try {
                if (!modelGroup || !dimensionGroup) return;

                // Clear previous elements
                clearGroup(modelGroup);
                clearGroup(dimensionGroup);

                // Inputs
                const P = parseFloat(document.querySelector('input[name="length"]').value) || 0;
                const L = parseFloat(document.querySelector('input[name="width"]').value) || 0;
                const T = parseFloat(document.querySelector('input[name="height"]').value) || 0;
                const maxSpacingBawah = parseFloat(document.querySelector('input[name="jarak_penyanggah_bawah"]')?.value) || 500;
                const maxSpacingAtas = parseFloat(document.querySelector('input[name="jarak_penyanggah_atas"]')?.value) || 500;
                const maxSpacing = Math.max(maxSpacingBawah, maxSpacingAtas);
                const arahGlobal = getConfigValue('arah_pemasangan', 'Horizontal');
                const celahAtas = parseFloat(document.querySelector('input[name="gap_atas"]').value) || 0;
                const celahBawah = parseFloat(document.querySelector('input[name="gap_bawah"]').value) || 0;

                if (P <= 0 || L <= 0 || T <= 0) return;

                const bawahPenutupTipe = getConfigValue('bawah_penutup_tipe', 'Tanpa Penutup');
                const atasPenutupTipe = getConfigValue('atas_penutup_tipe', 'Tanpa Penutup');
                const hasBawahPenutup = (bawahPenutupTipe !== 'Tanpa Penutup' && bawahPenutupTipe !== 'Tidak makai penutup' && bawahPenutupTipe !== 'Tidak Pakai Papan' && bawahPenutupTipe !== '');
                const hasAtasPenutup = (atasPenutupTipe !== 'Tanpa Penutup' && atasPenutupTipe !== 'Tidak makai penutup' && atasPenutupTipe !== 'Tidak Pakai Papan' && atasPenutupTipe !== '');
                const hasCover = hasBawahPenutup || hasAtasPenutup;

                const includePallet = getConfigValue('include_pallet_base', '0');
                const isPalletEnabled = includePallet === '1' || String(includePallet).toLowerCase() === 'ya';

                // Visibility checkboxes
                // Mematikan Rangka Atas (dan Rangka Tinggi) secara paksa sesuai request pengguna
                const showRangkaAtas = false;
                const showPenyanggaAtas = document.getElementById('vis-toggle-penyangga-atas')?.checked !== false;
                const showPenutupAtas = document.getElementById('vis-toggle-penutup-atas')?.checked !== false;
                const showPenyanggaBawah = document.getElementById('vis-toggle-penyangga-bawah')?.checked !== false;
                const showPenutupBawah = document.getElementById('vis-toggle-penutup-bawah')?.checked !== false;
                const showKakiBalok = document.getElementById('vis-toggle-kakibalok')?.checked !== false;

                // Usage checks
                const hasRangkaAtas = typeof activeDetails !== 'undefined' && activeDetails.length > 0 ? activeDetails.some(d => d.section === 'Rangka' && d.material_kode !== '-' && parseFloat(d.total_quantity) > 0) : false;

                const hasPenyangga = typeof activeDetails !== 'undefined' && activeDetails.length > 0 ? activeDetails.some(d => (d.section === 'Penyangga' || (d.section === 'Bawah' && d.part_name === 'Penyangga')) && d.material_kode !== '-' && parseFloat(d.total_quantity) > 0) : false;                
                // Hide or show visualizer toggles
                const setToggleDisplay = (id, show) => {
                    const el = document.getElementById(id);
                    const lbl = document.querySelector(`label[for="${id}"]`);
                    if (el) el.style.display = show ? '' : 'none';
                    if (lbl) lbl.style.display = show ? '' : 'none';
                };

                const hasPenyanggaAtas = typeof activeDetails !== 'undefined' && activeDetails.length > 0 ? activeDetails.some(d => d.section === 'Penyangga' && d.material_kode !== '-' && parseFloat(d.total_quantity) > 0) : false;
                const hasPenyanggaBawah = typeof activeDetails !== 'undefined' && activeDetails.length > 0 ? activeDetails.some(d => d.section === 'Bawah' && d.part_name === 'Penyangga' && d.material_kode !== '-' && parseFloat(d.total_quantity) > 0) : false;

                setToggleDisplay('vis-toggle-penyangga-atas', hasPenyanggaAtas);
                setToggleDisplay('vis-toggle-penutup-atas', hasAtasPenutup);
                setToggleDisplay('vis-toggle-penyangga-bawah', hasPenyanggaBawah);
                setToggleDisplay('vis-toggle-penutup-bawah', hasBawahPenutup);
                setToggleDisplay('vis-toggle-kakibalok', isPalletEnabled);
                
                // Expand Checkboxes
                const expRangkaAtas = false;
                const expPenyanggaAtas = document.getElementById('vis-exp-penyangga-atas')?.checked;
                const expPenutupAtas = document.getElementById('vis-exp-penutup-atas')?.checked;

                const expPenyanggaBawah = document.getElementById('vis-exp-penyangga-bawah')?.checked;
                const expPenutupBawah = document.getElementById('vis-exp-penutup-bawah')?.checked;
                const expKakiBalok = document.getElementById('vis-exp-kakibalok')?.checked;
                const expRangkaBawah = false;

                // Determine offsets based on specific toggles
                const getOffRangka = (isBawah) => (isBawah ? expRangkaBawah : expRangkaAtas) ? 0.2 : 0;
                const getOffPenyangga = (isBawah, faceId = '') => {
                    if (isBawah) return (expPenutupBawah ? 0.2 : (expPenyanggaBawah ? 0.2 : 0));
                    if (faceId === 'top') return expPenyanggaAtas ? 0.2 : 0;
                    return expPenutupAtas ? 0.2 : (expPenyanggaAtas ? 0.2 : 0);
                };
                const getOffPenutup = (isBawah, faceId = '') => {
                    if (isBawah) return expPenutupBawah ? 0.2 : 0;
                    if (faceId === 'top') return expPenyanggaAtas ? 0.2 : (expPenutupAtas ? 0.2 : 0);
                    return expPenutupAtas ? 0.2 : 0;
                };
                const offsetBalokY = expPenutupBawah ? 0.2 : (expPenyanggaBawah ? 0.2 : (expKakiBalok ? 0.2 : 0));
                
                const maxD = typeof currentMaxDimension !== 'undefined' ? currentMaxDimension : 2;

                // Dimensions in Three.js units (meters)
                const l_m = P / 1000;
                const w_m = L / 1000;
                const h_m = T / 1000;

                // Helper to get dynamic dimension for a specific part
                const getDim = (section, partNames) => {
                    let t = section === 'Penyangga' ? 0.02 : 0.08;
                    let w = section === 'Penyangga' ? 0.10 : 0.08;
                    
                    for(let name of partNames) {
                        const match = activeDetails.find(d => d.section === section && d.part_name === name);
                        if(match && match.material_kode && match.material_kode !== '-') {
                            t = parseFloat(match.calculated_thickness) / 1000;
                            w = parseFloat(match.calculated_width) / 1000;
                            break;
                        }
                    }
                    return { t, w, ht: t/2, hw: w/2 };
                };

                // Helper to fall back to a global dimension if we need a reference for positioning
                const globalRangka = getDim('Bawah', ['Rangka Panjang']);
                const maxF_w = globalRangka.w; // Simplified reference for Z offsets
                const F_t = globalRangka.t;

                currentMaxDimension = Math.max(l_m, w_m, h_m);

                if (woodTexture) {
                    woodTexture.repeat.set(Math.max(1, l_m * 2.2), 1);
                    woodTexture.needsUpdate = true;
                }

                // 1. DRAW RANGKA (FRAME)
                // Top Rangka (includes top beams and height posts)
                if (showRangkaAtas && hasRangkaAtas) {
                    const off = getOffRangka(false);
                    // Top length beams (Rangka Atas Panjang ditiadakan)
                    // Top width beams (Rangka Atas Lebar ditiadakan)
                    // 4 vertical corner posts (Rangka Tinggi ditiadakan)
                }


                // 2. DRAW PENYANGGA (SUPPORTS)
                if (showPenyanggaAtas || showPenyanggaBawah) {
                    const getPenyanggaConfig = (sectionName, name, crossSpanMm) => {
                        const match = activeDetails.find(d => d.section === sectionName && d.part_name === name);
                        if (match) {
                            if (match.material_kode === '-' || parseFloat(match.total_quantity) <= 0) {
                                return { qty: 0, orientation: 'H' };
                            }
                            return {
                                qty: parseInt(match.quantity),
                                orientation: (match.direction || arahGlobal) === 'Horizontal' ? 'H' : 'V'
                            };
                        }
                        return {
                            qty: Math.max(0, Math.ceil(crossSpanMm / maxSpacing) - 1),
                            orientation: arahGlobal === 'Horizontal' ? 'H' : 'V'
                        };
                    };

                    const cDepan = getPenyanggaConfig('Penyangga', 'Depan', arahGlobal === 'Horizontal' ? T : P);
                    const cBelakang = getPenyanggaConfig('Penyangga', 'Belakang', arahGlobal === 'Horizontal' ? T : P);
                    const cKiri = getPenyanggaConfig('Penyangga', 'Kiri', arahGlobal === 'Horizontal' ? T : L);
                    const cKanan = getPenyanggaConfig('Penyangga', 'Kanan', arahGlobal === 'Horizontal' ? T : L);
                    const cAtas = getPenyanggaConfig('Penyangga', 'Atas', arahGlobal === 'Horizontal' ? L : P);
                    const cBawah = getPenyanggaConfig('Bawah', 'Penyangga', arahGlobal === 'Horizontal' ? L : P);

                    const faces = [
                        { id: 'front', name: 'Depan', qty: cDepan.qty, orientation: cDepan.orientation },
                        { id: 'back', name: 'Belakang', qty: cBelakang.qty, orientation: cBelakang.orientation },
                        { id: 'right', name: 'Kanan', qty: cKanan.qty, orientation: cKanan.orientation },
                        { id: 'left', name: 'Kiri', qty: cKiri.qty, orientation: cKiri.orientation },
                        { id: 'top', name: 'Atas', qty: cAtas.qty, orientation: cAtas.orientation },
                        { id: 'bottom', name: 'Bawah', qty: cBawah.qty, orientation: cBawah.orientation }
                    ];

                    faces.forEach(function (face) {
                        const isBottom = (face.id === 'bottom');
                        if (isBottom && (!showPenyanggaBawah || !hasPenyanggaBawah)) return;
                        if (!isBottom && (!showPenyanggaAtas || !hasPenyanggaAtas)) return;

                        const count = face.qty;
                        if (count <= 0) return;
                        const pDim = face.id === 'bottom' ? getDim('Bawah', ['Penyangga']) : getDim('Penyangga', [face.name]);

                        let positions;
                        
                        const rTopL = {w: 0, t: 0, hw: 0, ht: 0};
                        const rBotL = getDim('Bawah', ['Rangka Panjang']);
                        const rTopW = {w: 0, t: 0, hw: 0, ht: 0};
                        const rBotW = getDim('Bawah', ['Rangka Lebar']);
                        
                        const offP = getOffPenyangga(face.id === 'bottom', face.id);

                        // Hitung tinggi top Kaki Balok untuk pijakan Penyangga Depan/Belakang
                        let t_penutup_b_val = 0;
                        if (typeof hasBawahPenutup !== 'undefined' && hasBawahPenutup) {
                            const pBawah = typeof activeDetails !== 'undefined' ? activeDetails.find(d => d.section === 'Bawah' && d.part_name === 'Penutup') : null;
                            t_penutup_b_val = pBawah && pBawah.material_kode !== '-' ? parseFloat(pBawah.calculated_thickness) / 1000 : 0.02;
                        }
                        let t_penyangga_b_val = 0;
                        if (typeof showPenyanggaBawah !== 'undefined' && showPenyanggaBawah) {
                            const pPeny = typeof activeDetails !== 'undefined' ? activeDetails.find(d => d.section === 'Bawah' && d.part_name === 'Penyangga') : null;
                            t_penyangga_b_val = pPeny && pPeny.material_kode !== '-' ? parseFloat(pPeny.calculated_thickness) / 1000 : 0.02;
                        }
                        let t_penutup_s_val = 0;
                        if (typeof hasCover !== 'undefined' && hasCover) {
                            const pSamping = typeof activeDetails !== 'undefined' ? activeDetails.find(d => d.section === 'Penutup' && (d.part_name === 'Kanan' || d.part_name === 'Kiri')) : null;
                            t_penutup_s_val = pSamping && pSamping.material_kode !== '-' ? parseFloat(pSamping.calculated_thickness) / 1000 : 0.02;
                        }
                        const offsetBalokYGlobal = (typeof expKakiBalok !== 'undefined' && expKakiBalok) ? 0.2 : 0;
                        const topKakiBalokY = (typeof hasBawahPenutup !== 'undefined' && hasBawahPenutup ? -t_penutup_b_val : 0) - t_penyangga_b_val;

                        if (face.id === 'front' || face.id === 'back') {
                            let frontBackCoverThk = 0;
                            if (typeof hasCover !== 'undefined' && hasCover) {
                                const pDB = typeof activeDetails !== 'undefined' ? activeDetails.find(d => d.section === 'Penutup' && d.part_name === face.name) : null;
                                frontBackCoverThk = pDB && pDB.material_kode !== '-' ? parseFloat(pDB.calculated_thickness) / 1000 : 0.02;
                            }
                            const z = face.id === 'front' ? w_m / 2 + frontBackCoverThk + pDim.ht + offP : -w_m / 2 - frontBackCoverThk - pDim.ht - offP;
                            if (face.orientation === 'H') {
                                positions = evenlySpaced(count, rBotL.t + pDim.hw, h_m - rTopL.t - pDim.hw);
                                positions.forEach(function (y) {
                                    let bMesh = addBeam(new THREE.Vector3(l_m, pDim.w, pDim.t), new THREE.Vector3(0, y, z), supportMaterial, face.name + ' horizontal');
                                    addNailsForPenyangga(bMesh, face.id);
                                });
                            } else {
                                let b_w = 0; 
                                if (typeof activeDetails !== 'undefined') {
                                    const pKaki = activeDetails.find(d => d.section === 'Bawah' && (d.part_name === 'Kaki Balok' || d.part_name === 'Additional Balok'));
                                    if (pKaki && pKaki.material_kode !== '-') b_w = parseFloat(pKaki.calculated_width) / 1000;
                                }
                                const vLen = h_m - rTopL.t - (topKakiBalokY - b_w);
                                const yCenter = (topKakiBalokY - b_w) + (vLen / 2);
                                const outer_span_x = l_m;
                                let positions_x = [];
                                if (count === 1) {
                                    positions_x = [0];
                                } else {
                                    let b_t = 0.05; 
                                    if(typeof activeDetails !== 'undefined'){
                                        const pKaki = activeDetails.find(d => d.section === 'Bawah' && d.part_name === 'Kaki Balok');
                                        if(pKaki) b_t = parseFloat(pKaki.calculated_thickness) / 1000;
                                    }
                                    const start_x = -outer_span_x / 2 + b_t / 2;
                                    const end_x = outer_span_x / 2 - b_t / 2;
                                    for (let i = 0; i < count; i++) {
                                        positions_x.push(start_x + (end_x - start_x) * (i / (count - 1)));
                                    }
                                }
                                
                                positions_x.forEach(function (x) {
                                    let bMesh = addBeam(new THREE.Vector3(pDim.w, Math.max(pDim.w, vLen), pDim.t), new THREE.Vector3(x, yCenter, z), supportMaterial, face.name + ' vertikal');
                                    addNailsForPenyangga(bMesh, face.id);
                                });
                            }
                        } else if (face.id === 'right' || face.id === 'left') {
                            const sideCoverThk = (typeof hasCover !== 'undefined' && hasCover) ? t_penutup_s_val : 0;
                            const x = face.id === 'right' ? l_m / 2 + sideCoverThk + pDim.ht + offP : -l_m / 2 - sideCoverThk - pDim.ht - offP;
                            if (face.orientation === 'H') {
                                positions = evenlySpaced(count, rBotW.t + pDim.hw, h_m - rTopW.t - pDim.hw);
                                positions.forEach(function (y) {
                                    let bMesh = addBeam(new THREE.Vector3(pDim.t, pDim.w, Math.max(pDim.w, w_m - 2 * globalRangka.w)), new THREE.Vector3(x, y, 0), supportMaterial, face.name + ' horizontal');
                                    addNailsForPenyangga(bMesh, face.id);
                                });
                            } else {
                                let b_w = 0; 
                                if (typeof activeDetails !== 'undefined') {
                                    const pKaki = activeDetails.find(d => d.section === 'Bawah' && (d.part_name === 'Kaki Balok' || d.part_name === 'Additional Balok'));
                                    if (pKaki && pKaki.material_kode !== '-') b_w = parseFloat(pKaki.calculated_width) / 1000;
                                }
                                const vLen = h_m - rTopW.t - (topKakiBalokY - b_w);
                                const yCenter = (topKakiBalokY - b_w) + (vLen / 2);
                                let positions_z = [];
                                const interval = 0.8;
                                const start_z = -((count - 1) * interval) / 2;
                                for (let i = 0; i < count; i++) {
                                    positions_z.push(start_z + i * interval);
                                }
                                positions_z.forEach(function (z) {
                                    let bMesh = addBeam(new THREE.Vector3(pDim.t, Math.max(pDim.w, vLen), pDim.w), new THREE.Vector3(x, yCenter, z), supportMaterial, face.name + ' vertikal');
                                    addNailsForPenyangga(bMesh, face.id);
                                });
                            }
                        } else if (face.id === 'top') {
                            const y = h_m - pDim.ht + offP;
                            if (face.orientation === 'H') {
                                positions = evenlySpaced(count, -w_m / 2 + globalRangka.w + pDim.hw, w_m / 2 - globalRangka.w - pDim.hw);
                                positions.forEach(function (z) {
                                    let bMesh = addBeam(new THREE.Vector3(Math.max(pDim.w, l_m - 2 * globalRangka.w), pDim.t, pDim.w), new THREE.Vector3(0, y, z), supportMaterial, face.name + ' arah panjang');
                                    addNailsForPenyangga(bMesh, face.id);
                                });
                            } else {
                                const outer_span_x = l_m;
                                let positions_x = [];
                                if (count === 1) {
                                    positions_x = [0];
                                } else {
                                    let b_t = 0.05; 
                                    if(typeof activeDetails !== 'undefined'){
                                        const pKaki = activeDetails.find(d => d.section === 'Bawah' && d.part_name === 'Kaki Balok');
                                        if(pKaki) b_t = parseFloat(pKaki.calculated_thickness) / 1000;
                                    }
                                    const start_x = -outer_span_x / 2 + b_t / 2;
                                    const end_x = outer_span_x / 2 - b_t / 2;
                                    for (let i = 0; i < count; i++) {
                                        positions_x.push(start_x + (end_x - start_x) * (i / (count - 1)));
                                    }
                                }
                                positions_x.forEach(function (x) {
                                    let bMesh = addBeam(new THREE.Vector3(pDim.w, pDim.t, w_m), new THREE.Vector3(x, y, 0), supportMaterial, face.name + ' arah lebar');
                                    addNailsForPenyangga(bMesh, face.id);
                                });
                            }
                        } else if (face.id === 'bottom') {
                            let t_penutup_bawah = 0;
                            if (typeof hasBawahPenutup !== 'undefined' && hasBawahPenutup) {
                                const pBawah = typeof activeDetails !== 'undefined' ? activeDetails.find(d => d.section === 'Bawah' && d.part_name === 'Penutup') : null;
                                t_penutup_bawah = pBawah && pBawah.material_kode !== '-' ? parseFloat(pBawah.calculated_thickness) / 1000 : 0.02;
                            }
                            let maxBawahThk_c = 0;
                            let t_penutup_b_c = 0;
                            let t_penyangga_b_c = 0;
                            if (typeof hasBawahPenutup !== 'undefined' && hasBawahPenutup && typeof activeDetails !== 'undefined') {
                                const pBawah = activeDetails.find(d => d.section === 'Bawah' && d.part_name === 'Penutup');
                                if (pBawah && pBawah.material_kode !== '-') t_penutup_b_c = parseFloat(pBawah.calculated_thickness) / 1000;
                            }
                            if (typeof activeDetails !== 'undefined') {
                                const pPeny = activeDetails.find(d => d.section === 'Bawah' && d.part_name === 'Penyangga');
                                if (pPeny && pPeny.material_kode !== '-') t_penyangga_b_c = parseFloat(pPeny.calculated_thickness) / 1000;
                            }
                            maxBawahThk_c = Math.max(t_penutup_b_c, t_penyangga_b_c);
                            const offsetBalokY_c = (typeof expPenutupBawah !== 'undefined' && expPenutupBawah) ? 0.2 : ((typeof expPenyanggaBawah !== 'undefined' && expPenyanggaBawah) ? 0.2 : ((typeof expKakiBalok !== 'undefined' && expKakiBalok) ? 0.2 : 0));
                            const floorY = -maxBawahThk_c - offsetBalokY_c;
                            
                            // Bottom surface of Penyangga is at floorY (resting on Kaki Balok)
                            const y = floorY + (pDim.t / 2) - offP;
                            
                            let t_penutup_samping_p = 0;
                            if (typeof hasCover !== 'undefined' && hasCover) {
                                const pSamping = typeof activeDetails !== 'undefined' ? activeDetails.find(d => d.section === 'Penutup' && (d.part_name === 'Kanan' || d.part_name === 'Kiri')) : null;
                                t_penutup_samping_p = pSamping && pSamping.material_kode !== '-' ? parseFloat(pSamping.calculated_thickness) / 1000 : 0.02;
                            }
                            const outer_l = l_m + (typeof hasCover !== 'undefined' && hasCover ? t_penutup_samping_p * 2 : 0);
                            const outer_w = w_m + (typeof hasCover !== 'undefined' && hasCover ? t_penutup_samping_p * 2 : 0);

                            let celahPeny = (maxSpacingBawah) / 1000;
                            let langkahPeny = pDim.w + celahPeny;
                            let spanBound = (face.orientation === 'H') ? outer_w / 2 - pDim.hw : outer_l / 2 - pDim.hw;
                            
                            // Reserve space for Penutup if present
                            let w_penutup_reserved = 0;
                            if (typeof hasBawahPenutup !== 'undefined' && hasBawahPenutup && typeof activeDetails !== 'undefined') {
                                const pBawah = activeDetails.find(d => d.section === 'Bawah' && d.part_name === 'Penutup');
                                w_penutup_reserved = pBawah && pBawah.material_kode !== '-' ? parseFloat(pBawah.calculated_width) / 1000 : 0.1;
                            }
                            
                            let halfCount = Math.floor(count / 2);
                            
                            // Prevent exceeding bounds AND leave room for Penutup at the edges
                            let maxPenyCenter = spanBound - w_penutup_reserved;
                            if (maxPenyCenter < 0) maxPenyCenter = 0; // fallback if box is too small
                            
                            if (halfCount > 0 && halfCount * langkahPeny > maxPenyCenter) {
                                langkahPeny = maxPenyCenter / halfCount;
                            }
                            
                            positions = [];
                            if (count % 2 === 1) {
                                for(let i = -halfCount; i <= halfCount; i++) {
                                    positions.push(i * langkahPeny);
                                }
                            } else {
                                let halfCountEven = count / 2;
                                for(let i = -halfCountEven; i <= halfCountEven; i++) {
                                    if (i === 0) continue;
                                    let pos = i > 0 ? (i - 0.5) * langkahPeny : (i + 0.5) * langkahPeny;
                                    if (pos > spanBound) pos = spanBound;
                                    if (pos < -spanBound) pos = -spanBound;
                                    positions.push(pos);
                                }
                            }

                            if (face.orientation === 'H') {
                                positions.forEach(function (z) {
                                    let bMesh = addBeam(new THREE.Vector3(outer_l, pDim.t, pDim.w), new THREE.Vector3(0, y, z), supportMaterial, face.name + ' arah panjang');
                                    addNailsForPenyangga(bMesh, face.id);
                                });
                            } else {
                                positions.forEach(function (x) {
                                    let bMesh = addBeam(new THREE.Vector3(pDim.w, pDim.t, outer_w), new THREE.Vector3(x, y, 0), supportMaterial, face.name + ' arah lebar');
                                    addNailsForPenyangga(bMesh, face.id);
                                });
                            }
                        }
                    });
                }

                // 3. DRAW PENUTUP (COVERS)
                if ((showPenutupAtas || showPenutupBawah) && hasCover) {
                    const makeBoardLayout = (crossSpan, boardWidth, gap, isFullBoard, forcedCount = null) => {
                        const safeWidth = Math.max(0.01, Math.min(boardWidth, crossSpan));
                        let count = 0;
                        let pieceCross = safeWidth;
                        const positions = [];
                        const sizes = [];

                        if (isFullBoard) {
                            count = forcedCount !== null ? forcedCount : Math.max(1, Math.min(80, Math.ceil(crossSpan / safeWidth)));
                            pieceCross = crossSpan / count;
                            for (let i = 0; i < count; i += 1) {
                                positions.push(-crossSpan / 2 + pieceCross / 2 + i * pieceCross);
                                sizes.push(pieceCross);
                            }
                        } else {
                            if (forcedCount !== null) {
                                count = forcedCount;
                                if (count === 1) {
                                    positions.push(0);
                                    sizes.push(safeWidth);
                                } else if (count > 1) {
                                    // Use the user's explicit gap to center the group, rather than stretching it
                                    const totalGroupWidth = (count * safeWidth) + ((count - 1) * gap);
                                    const startOffset = -totalGroupWidth / 2 + safeWidth / 2;
                                    for (let i = 0; i < count; i++) {
                                        positions.push(startOffset + i * (safeWidth + gap));
                                        sizes.push(safeWidth);
                                    }
                                }
                            } else {
                                // Untuk Papan Setengah: mulai dari bawah, pasang papan + celah. Papan terakhir dipotong jika melebihi batas atas.
                                let currentY = -crossSpan / 2;
                                while (currentY < crossSpan / 2 - 0.001 && count < 80) {
                                    let boardSize = safeWidth;
                                    if (currentY + boardSize > crossSpan / 2) {
                                        boardSize = (crossSpan / 2) - currentY;
                                    }
                                    positions.push(currentY + boardSize / 2);
                                    sizes.push(boardSize);
                                    currentY += boardSize + gap;
                                    count++;
                                }
                            }
                        }
                        return { count: count, pieceCross: pieceCross, positions: positions, sizes: sizes };
                    };

                    const coverFaces = [
                        { id: 'front', name: 'Depan' },
                        { id: 'back', name: 'Belakang' },
                        { id: 'right', name: 'Kanan' },
                        { id: 'left', name: 'Kiri' },
                        { id: 'top', name: 'Atas' },
                        { id: 'bottom', name: 'Bawah' }
                    ];

                    coverFaces.forEach(function (face) {
                        const isBottom = (face.id === 'bottom');
                        if (isBottom && (!showPenutupBawah || !hasBawahPenutup)) return;
                        if (!isBottom && (!showPenutupAtas || !hasAtasPenutup)) return;

                        const currentCoverTipe = isBottom ? bawahPenutupTipe : atasPenutupTipe;
                        const isTripleks = currentCoverTipe.toLowerCase().includes('triplex') || currentCoverTipe.toLowerCase().includes('tripleks');
                        const isFull = currentCoverTipe.toLowerCase().includes('full');
                        const material = isTripleks ? plywoodMaterial : coverMaterial;

                        // Get thickness and width for this specific penutup part
                        let t_penutup = 0.02; // default 20mm
                        let w_penutup = 0.15; // default 150mm
                        const sec = isBottom ? 'Bawah' : 'Penutup';
                        const pName = isBottom ? 'Penutup' : face.name;
                        const penutupDetail = activeDetails.find(d => d.section === sec && d.part_name === pName);
                        if (penutupDetail) {
                            t_penutup = parseFloat(penutupDetail.calculated_thickness) / 1000;
                            w_penutup = parseFloat(penutupDetail.calculated_width) / 1000;
                        }
                        
                        // Cegah bug visual di mana w_penutup = 1000mm dari perhitungan Triplex (state DB), 
                        // lalu user pindah ke Papan di UI tanpa save sehingga tergambar 1 papan raksasa
                        if (!isTripleks && w_penutup > 0.3) {
                            w_penutup = 0.15;
                        }

                        let faceArah = (() => {
                            if (penutupDetail && penutupDetail.direction) return penutupDetail.direction === 'Horizontal' ? 'H' : 'V';
                            return arahGlobal === 'Horizontal' ? 'H' : 'V';
                        })();

                        let extraSpanPanjang = 0;
                        let extraSpanPanjangKK = 0;
                        if (typeof activeDetails !== 'undefined') {
                            const pPenyDepan = activeDetails.find(d => d.section === 'Penyangga' && d.part_name === 'Depan');
                            const pPenyBelakang = activeDetails.find(d => d.section === 'Penyangga' && d.part_name === 'Belakang');
                            const pPenutupDepan = activeDetails.find(d => d.section === 'Penutup' && d.part_name === 'Depan');
                            const pPenutupBelakang = activeDetails.find(d => d.section === 'Penutup' && d.part_name === 'Belakang');
                            
                            const thkDepan = pPenyDepan && pPenyDepan.material_kode !== '-' ? parseFloat(pPenyDepan.calculated_thickness) / 1000 : 0.02;
                            const thkBelakang = pPenyBelakang && pPenyBelakang.material_kode !== '-' ? parseFloat(pPenyBelakang.calculated_thickness) / 1000 : 0.02;
                            const thkPDepan = pPenutupDepan && pPenutupDepan.material_kode !== '-' ? parseFloat(pPenutupDepan.calculated_thickness) / 1000 : 0;
                            const thkPBelakang = pPenutupBelakang && pPenutupBelakang.material_kode !== '-' ? parseFloat(pPenutupBelakang.calculated_thickness) / 1000 : 0;
                            
                            extraSpanPanjang = thkPDepan + thkPBelakang;
                            extraSpanPanjangKK = thkPDepan + thkPBelakang + thkDepan + thkBelakang;

                            const pPKanan = activeDetails.find(d => d.section === 'Penutup' && d.part_name === 'Kanan');
                            const pPKiri = activeDetails.find(d => d.section === 'Penutup' && d.part_name === 'Kiri');
                            const pPenyKanan = activeDetails.find(d => d.section === 'Penyangga' && d.part_name === 'Kanan');
                            const pPenyKiri = activeDetails.find(d => d.section === 'Penyangga' && d.part_name === 'Kiri');
                            
                            const thkPKanan = pPKanan && pPKanan.material_kode !== '-' ? parseFloat(pPKanan.calculated_thickness) / 1000 : 0;
                            const thkPKiri = pPKiri && pPKiri.material_kode !== '-' ? parseFloat(pPKiri.calculated_thickness) / 1000 : 0;
                            const thkPenyKanan = pPenyKanan && pPenyKanan.material_kode !== '-' ? parseFloat(pPenyKanan.calculated_thickness) / 1000 : 0.02;
                            const thkPenyKiri = pPenyKiri && pPenyKiri.material_kode !== '-' ? parseFloat(pPenyKiri.calculated_thickness) / 1000 : 0.02;
                            
                            extraSpanLebar = thkPKanan + thkPKiri;
                        }

                        const offC = getOffPenutup(isBottom, face.id);

                        let t_penutup_b_val_c = 0;
                        if (typeof hasBawahPenutup !== 'undefined' && hasBawahPenutup) {
                            const pBawah = typeof activeDetails !== 'undefined' ? activeDetails.find(d => d.section === 'Bawah' && d.part_name === 'Penutup') : null;
                            t_penutup_b_val_c = pBawah && pBawah.material_kode !== '-' ? parseFloat(pBawah.calculated_thickness) / 1000 : 0.02;
                        }
                        let t_penyangga_b_val_c = 0;
                        if (typeof showPenyanggaBawah !== 'undefined' && showPenyanggaBawah) {
                            const pPeny = typeof activeDetails !== 'undefined' ? activeDetails.find(d => d.section === 'Bawah' && d.part_name === 'Penyangga') : null;
                            t_penyangga_b_val_c = pPeny && pPeny.material_kode !== '-' ? parseFloat(pPeny.calculated_thickness) / 1000 : 0.02;
                        }
                        const offsetBalokYGlobal_c = (typeof expKakiBalok !== 'undefined' && expKakiBalok) ? 0.2 : 0;
                        // Penutup samping (Kanan, Kiri, Depan, Belakang) diletakkan di atas papan penutup bawah (Y=0)
                        const topKakiBalokY_c = 0;

                        let t_penyangga_atas_c = 0; // Penyangga Atas kini berada di dalam (di bawah Penutup), sehingga tidak mengganjal Penutup Atas.
                        if (typeof showPenyanggaAtas !== 'undefined' && showPenyanggaAtas) {
                            const pPA = typeof activeDetails !== 'undefined' ? activeDetails.find(d => d.section === 'Penyangga' && d.part_name === 'Atas') : null;
                            if (pPA && parseFloat(pPA.total_quantity) > 0) {
                                // t_penyangga_atas_c = pPA.material_kode !== '-' ? parseFloat(pPA.calculated_thickness) / 1000 : 0.02;
                                // Di-comment karena Penyangga Atas diturunkan ke dalam rangka, tidak lagi menambah ketinggian Penutup Atas
                                t_penyangga_atas_c = 0;
                            }
                        }

                        let l_m_bottom = l_m;
                        let w_m_bottom = w_m;
                        if (face.id === 'bottom' && typeof activeDetails !== 'undefined') {
                            let t_ps_kk = 0, t_ps_db = 0, t_py_kk = 0, t_py_db = 0;
                            if (typeof hasCover !== 'undefined' && hasCover) {
                                const pSampingKK = activeDetails.find(d => d.section === 'Penutup' && (d.part_name === 'Kanan' || d.part_name === 'Kiri'));
                                if (pSampingKK && pSampingKK.material_kode !== '-') t_ps_kk = parseFloat(pSampingKK.calculated_thickness) / 1000;
                                const pSampingDB = activeDetails.find(d => d.section === 'Penutup' && (d.part_name === 'Depan' || d.part_name === 'Belakang'));
                                if (pSampingDB && pSampingDB.material_kode !== '-') t_ps_db = parseFloat(pSampingDB.calculated_thickness) / 1000;
                            }
                            const pPenyKK = activeDetails.find(d => d.section === 'Penyangga' && (d.part_name === 'Kanan' || d.part_name === 'Kiri'));
                            if (pPenyKK && pPenyKK.material_kode !== '-') t_py_kk = parseFloat(pPenyKK.calculated_thickness) / 1000;
                            const pPenyDB = activeDetails.find(d => d.section === 'Penyangga' && (d.part_name === 'Depan' || d.part_name === 'Belakang'));
                            if (pPenyDB && pPenyDB.material_kode !== '-') t_py_db = parseFloat(pPenyDB.calculated_thickness) / 1000;
                            l_m_bottom = l_m + (t_ps_kk * 2);
                            w_m_bottom = w_m + (t_ps_db * 2);
                        }

                        if (isTripleks) {
                            // Draw single solid sheet for plywood (triplex)
                            if (face.id === 'front' || face.id === 'back') {
                                const z = face.id === 'front' ? w_m / 2 + t_penutup / 2 + offC : -w_m / 2 - t_penutup / 2 - offC;
                                const frontLength = l_m;
                                const frontHeight = h_m - topKakiBalokY_c;
                                let bMesh = addBeam(new THREE.Vector3(frontLength, frontHeight, t_penutup), new THREE.Vector3(0, topKakiBalokY_c + frontHeight / 2, z), material, face.name + ' triplex');
                                addNailsForBoard(bMesh, face.id);
                            } else if (face.id === 'right' || face.id === 'left') {
                                const x = face.id === 'right' ? l_m / 2 + t_penutup / 2 + offC : -l_m / 2 - t_penutup / 2 - offC;
                                const sideHeight = h_m - topKakiBalokY_c;
                                let bMesh = addBeam(new THREE.Vector3(t_penutup, sideHeight, w_m + extraSpanPanjangKK), new THREE.Vector3(x, topKakiBalokY_c + sideHeight / 2, 0), material, face.name + ' triplex');
                                addNailsForBoard(bMesh, face.id);
                            } else {
                                const y = face.id === 'top' ? h_m + t_penyangga_atas_c + t_penutup / 2 + offC : -t_penutup / 2 - offC;
                                const l_m_top = face.id === 'top' ? l_m + (typeof extraSpanLebar !== 'undefined' ? extraSpanLebar : 0) : l_m_bottom;
                                const w_m_top = face.id === 'top' ? w_m + (typeof extraSpanPanjang !== 'undefined' ? extraSpanPanjang : 0) : w_m_bottom;
                                let bMesh = addBeam(new THREE.Vector3(l_m_top, t_penutup, w_m_top), new THREE.Vector3(0, y, 0), material, face.name + ' triplex');
                                addNailsForBoard(bMesh, face.id);
                            }
                        } else {
                            // Draw multiple boards (papan)
                            let gapM = celahAtas / 1000;
                            if (face.id === 'bottom') {
                                gapM = celahBawah / 1000;
                            }
                            
                            let crossSpan;
                            let longSpan;
                            if (face.id === 'front' || face.id === 'back') {
                                crossSpan = (faceArah === 'H') ? h_m - topKakiBalokY_c : l_m;
                                longSpan = (faceArah === 'H') ? l_m : h_m - topKakiBalokY_c;
                            } else if (face.id === 'right' || face.id === 'left') {
                                crossSpan = (faceArah === 'H') ? h_m - topKakiBalokY_c : w_m + extraSpanPanjangKK;
                                longSpan = (faceArah === 'H') ? w_m + extraSpanPanjangKK : h_m - topKakiBalokY_c;
                            } else {
                                const w_m_top = face.id === 'top' ? w_m + (typeof extraSpanPanjang !== 'undefined' ? extraSpanPanjang : 0) : w_m_bottom;
                                const l_m_top = face.id === 'top' ? l_m + (typeof extraSpanLebar !== 'undefined' ? extraSpanLebar : 0) : l_m_bottom;
                                crossSpan = (faceArah === 'H') ? w_m_top : l_m_top;
                                longSpan = (faceArah === 'H') ? l_m_top : w_m_top;
                            }

                            const forcedCount = penutupDetail && penutupDetail.quantity ? parseInt(penutupDetail.quantity) : null;
                            let layout = makeBoardLayout(crossSpan, w_penutup, gapM, isFull, forcedCount);

                            if (face.id === 'bottom' && forcedCount !== null && forcedCount > 0 && !isFull) {
                                let t_py_w = 0.08;
                                let pPeny = typeof activeDetails !== 'undefined' ? activeDetails.find(d => d.section === 'Bawah' && d.part_name === 'Penyangga') : null;
                                if (pPeny && pPeny.material_kode !== '-') t_py_w = parseFloat(pPeny.calculated_width) / 1000;
                                let celahPeny = maxSpacingBawah / 1000;
                                let langkahPeny = t_py_w + celahPeny;
                                let countPeny = pPeny && pPeny.quantity ? parseInt(pPeny.quantity) : 0;
                                
                                let customPositions = [];
                                let customSizes = [];
                                // 2 Edge pieces at the absolute outer bounds
                                customPositions.push(-crossSpan / 2 + w_penutup / 2);
                                customSizes.push(w_penutup);
                                
                                if (forcedCount > 1) {
                                    customPositions.push(crossSpan / 2 - w_penutup / 2);
                                    customSizes.push(w_penutup);
                                }
                                
                                // Pieces between Penyangga
                                if (countPeny > 1 && forcedCount > 2) {
                                    let maxPenyCenter = crossSpan / 2 - w_penutup - (t_py_w / 2);
                                    if (maxPenyCenter < 0) maxPenyCenter = 0;
                                    
                                    let halfPeny = Math.floor(countPeny / 2);
                                    if (halfPeny > 0 && halfPeny * langkahPeny > maxPenyCenter) {
                                        langkahPeny = maxPenyCenter / halfPeny;
                                        celahPeny = langkahPeny - t_py_w;
                                    }
                                    
                                    let penyPositions = [];
                                    if (countPeny % 2 === 1) {
                                        for(let i = -halfPeny; i <= halfPeny; i++) {
                                            penyPositions.push(i * langkahPeny);
                                        }
                                    } else {
                                        let halfCountEven = countPeny / 2;
                                        for(let i = -halfCountEven; i <= halfCountEven; i++) {
                                            if (i === 0) continue;
                                            let pos = i > 0 ? (i - 0.5) * langkahPeny : (i + 0.5) * langkahPeny;
                                            penyPositions.push(pos);
                                        }
                                    }
                                    
                                    let spaces = countPeny - 1;
                                    let langkahPenutup = w_penutup + gapM;
                                    let coversPerSpace = (langkahPenutup > 0) ? Math.floor((celahPeny + gapM) / langkahPenutup) : 0;
                                    
                                    for(let i = 0; i < spaces; i++) {
                                        let leftPenyCenter = penyPositions[i];
                                        let rightPenyCenter = penyPositions[i+1];
                                        
                                        let spaceWidth = rightPenyCenter - leftPenyCenter - t_py_w; // actual space between inner edges
                                        
                                        // Use the user's inputted gap instead of stretching evenly
                                        let totalCoversWidth = (coversPerSpace * w_penutup) + ((coversPerSpace - 1) * gapM);
                                        let sideMargin = (spaceWidth - totalCoversWidth) / 2;
                                        
                                        let currentPos = leftPenyCenter + (t_py_w / 2) + sideMargin + (w_penutup / 2);
                                        for(let j=0; j<coversPerSpace; j++) {
                                            customPositions.push(currentPos);
                                            customSizes.push(w_penutup);
                                            currentPos += w_penutup + gapM;
                                        }
                                    }
                                    
                                    // Also fill the outer spaces (left and right) if they are large enough!
                                    if (penyPositions.length > 0) {
                                        let leftmostPenyCenter = penyPositions[0];
                                        let rightmostPenyCenter = penyPositions[penyPositions.length - 1];
                                        
                                        // Left outer space (between left edge board inner face and leftmost penyangga inner face)
                                        let leftOuterSpace = (leftmostPenyCenter - t_py_w/2) - (-crossSpan/2 + w_penutup);
                                        if (leftOuterSpace > 0) {
                                            let coversOuter = Math.floor((leftOuterSpace + gapM) / (w_penutup + gapM));
                                            if (coversOuter > 0) {
                                                let totalOuterCoversWidth = (coversOuter * w_penutup) + ((coversOuter - 1) * gapM);
                                                let sideMarginOuter = (leftOuterSpace - totalOuterCoversWidth) / 2;
                                                let currentPosOuter = (-crossSpan/2 + w_penutup) + sideMarginOuter + (w_penutup / 2);
                                                for(let j=0; j<coversOuter; j++) {
                                                    customPositions.push(currentPosOuter);
                                                    customSizes.push(w_penutup);
                                                    currentPosOuter += w_penutup + gapM;
                                                }
                                            }
                                        }
                                        
                                        // Right outer space (between rightmost penyangga inner face and right edge board inner face)
                                        let rightOuterSpace = (crossSpan/2 - w_penutup) - (rightmostPenyCenter + t_py_w/2);
                                        if (rightOuterSpace > 0) {
                                            let coversOuter = Math.floor((rightOuterSpace + gapM) / (w_penutup + gapM));
                                            if (coversOuter > 0) {
                                                let totalOuterCoversWidth = (coversOuter * w_penutup) + ((coversOuter - 1) * gapM);
                                                let sideMarginOuter = (rightOuterSpace - totalOuterCoversWidth) / 2;
                                                let currentPosOuter = (rightmostPenyCenter + t_py_w/2) + sideMarginOuter + (w_penutup / 2);
                                                for(let j=0; j<coversOuter; j++) {
                                                    customPositions.push(currentPosOuter);
                                                    customSizes.push(w_penutup);
                                                    currentPosOuter += w_penutup + gapM;
                                                }
                                            }
                                        }
                                    }
                                }
                                layout = { count: customPositions.length, pieceCross: w_penutup, positions: customPositions, sizes: customSizes };
                            }

                            layout.positions.forEach(function (pos, index) {
                                const pSize = layout.sizes ? layout.sizes[index] : layout.pieceCross;
                                if (face.id === 'front' || face.id === 'back') {
                                    const z = face.id === 'front' ? w_m / 2 + t_penutup / 2 + offC : -w_m / 2 - t_penutup / 2 - offC;
                                    if (faceArah === 'H') {
                                        const frontLength = l_m;
                                        let bMesh = addBeam(new THREE.Vector3(frontLength, pSize, t_penutup), new THREE.Vector3(0, topKakiBalokY_c + (h_m - topKakiBalokY_c) / 2 + pos, z), material, face.name + ' papan horizontal');
                                        addNailsForBoard(bMesh, face.id);
                                    } else {
                                        const sideHeight = h_m - topKakiBalokY_c;
                                        let bMesh = addBeam(new THREE.Vector3(pSize, sideHeight, t_penutup), new THREE.Vector3(pos, topKakiBalokY_c + sideHeight / 2, z), material, face.name + ' papan vertikal');
                                        addNailsForBoard(bMesh, face.id);
                                    }
                                } else if (face.id === 'right' || face.id === 'left') {
                                    const x = face.id === 'right' ? l_m / 2 + t_penutup / 2 + offC : -l_m / 2 - t_penutup / 2 - offC;
                                    if (faceArah === 'H') {
                                        let bMesh = addBeam(new THREE.Vector3(t_penutup, pSize, w_m + extraSpanPanjangKK), new THREE.Vector3(x, topKakiBalokY_c + (h_m - topKakiBalokY_c) / 2 + pos, 0), material, face.name + ' papan horizontal');
                                        addNailsForBoard(bMesh, face.id);
                                    } else {
                                        const sideHeight = h_m - topKakiBalokY_c;
                                        let bMesh = addBeam(new THREE.Vector3(t_penutup, sideHeight, pSize), new THREE.Vector3(x, topKakiBalokY_c + sideHeight / 2, pos), material, face.name + ' papan vertikal');
                                        addNailsForBoard(bMesh, face.id);
                                    }
                                } else {
                                    let floorY_c = 0;
                                    if (face.id === 'bottom') {
                                        let maxBawahThk_c = 0;
                                        let t_penutup_b_c = 0;
                                        let t_penyangga_b_c = 0;
                                        if (typeof hasBawahPenutup !== 'undefined' && hasBawahPenutup && typeof activeDetails !== 'undefined') {
                                            const pBawah = activeDetails.find(d => d.section === 'Bawah' && d.part_name === 'Penutup');
                                            if (pBawah && pBawah.material_kode !== '-') t_penutup_b_c = parseFloat(pBawah.calculated_thickness) / 1000;
                                        }
                                        if (typeof showPenyanggaBawah !== 'undefined' && showPenyanggaBawah && typeof activeDetails !== 'undefined') {
                                            const pPeny = activeDetails.find(d => d.section === 'Bawah' && d.part_name === 'Penyangga');
                                            if (pPeny && pPeny.material_kode !== '-') t_penyangga_b_c = parseFloat(pPeny.calculated_thickness) / 1000;
                                        }
                                        maxBawahThk_c = Math.max(t_penutup_b_c, t_penyangga_b_c);
                                        const offsetBalokY_c = (typeof expPenutupBawah !== 'undefined' && expPenutupBawah) ? 0.2 : ((typeof expPenyanggaBawah !== 'undefined' && expPenyanggaBawah) ? 0.2 : ((typeof expKakiBalok !== 'undefined' && expKakiBalok) ? 0.2 : 0));
                                        floorY_c = -maxBawahThk_c - offsetBalokY_c;
                                    }
                                    const y = face.id === 'top' ? h_m + t_penyangga_atas_c + t_penutup / 2 + offC : floorY_c + (t_penutup / 2) - offC;
                                    const w_m_top = face.id === 'top' ? w_m + (typeof extraSpanPanjang !== 'undefined' ? extraSpanPanjang : 0) : w_m_bottom;
                                    const l_m_top = face.id === 'top' ? l_m + (typeof extraSpanLebar !== 'undefined' ? extraSpanLebar : 0) : l_m_bottom;
                                    if (faceArah === 'H') {
                                        let bMesh = addBeam(new THREE.Vector3(l_m_top, t_penutup, pSize), new THREE.Vector3(0, y, pos), material, face.name + ' papan arah panjang');
                                        addNailsForBoard(bMesh, face.id);
                                    } else {
                                        let bMesh = addBeam(new THREE.Vector3(pSize, t_penutup, w_m_top), new THREE.Vector3(pos, y, 0), material, face.name + ' papan arah lebar');
                                        addNailsForBoard(bMesh, face.id);
                                    }
                                }
                            });
                        }
                    });
                }

                // 4. DRAW ADDITIONAL BALOK
                if (isPalletEnabled && showKakiBalok) {
                    const addBalokMatch = typeof activeDetails !== 'undefined' ? activeDetails.find(d => d.section === 'Bawah' && (d.part_name === 'Kaki Balok' || d.part_name === 'Additional Balok')) : null;
                    if (addBalokMatch && parseFloat(addBalokMatch.total_quantity) > 0) {
                        const qty = parseInt(addBalokMatch.quantity) || 1;
                        const direction = addBalokMatch.direction || 'Horizontal';
                        
                        let balok_t = 0.05; 
                        let balok_w = 0.05; 
                        if (addBalokMatch.material_kode && addBalokMatch.material_kode !== '-') {
                            balok_t = parseFloat(addBalokMatch.calculated_thickness) / 1000;
                            balok_w = parseFloat(addBalokMatch.calculated_width) / 1000;
                        }

                        // Determine Y position (underneath bottom board or frame)
                        let t_penutup_bawah = 0;
                        let t_penutup_samping = 0;
                        if (hasBawahPenutup) {
                            const pBawah = typeof activeDetails !== 'undefined' ? activeDetails.find(d => d.section === 'Bawah' && d.part_name === 'Penutup') : null;
                            t_penutup_bawah = pBawah && pBawah.material_kode !== '-' ? parseFloat(pBawah.calculated_thickness) / 1000 : 0.02;
                        }
                        if (hasAtasPenutup) {
                            const pSamping = typeof activeDetails !== 'undefined' ? activeDetails.find(d => d.section === 'Penutup' && (d.part_name === 'Kanan' || d.part_name === 'Kiri')) : null;
                            t_penutup_samping = pSamping && pSamping.material_kode !== '-' ? parseFloat(pSamping.calculated_thickness) / 1000 : 0.02;
                        }
                        let t_penyangga_bawah = 0;
                        if (typeof showPenyanggaBawah !== 'undefined' && showPenyanggaBawah) {
                            const pPenyangga = typeof activeDetails !== 'undefined' ? activeDetails.find(d => d.section === 'Bawah' && d.part_name === 'Penyangga') : null;
                            t_penyangga_bawah = pPenyangga && pPenyangga.material_kode !== '-' ? parseFloat(pPenyangga.calculated_thickness) / 1000 : 0.02;
                        }

                        const offsetBalokY = expPenutupBawah ? 0.2 : (expPenyanggaBawah ? 0.2 : (expKakiBalok ? 0.2 : 0));
                        // Kaki Balok sits directly under the thickest bottom layer (since Penyangga and Penutup are side-by-side)
                        const maxBawahThk = Math.max(hasBawahPenutup ? t_penutup_bawah : 0, typeof showPenyanggaBawah !== 'undefined' && showPenyanggaBawah ? t_penyangga_bawah : 0);
                        const permukaanBawahPenyangga = -maxBawahThk - offsetBalokY;
                        const y = permukaanBawahPenyangga - (balok_w / 2);
                        
                        // Kaki Balok disesuaikan agar sejajar presisi dengan panjang/lebar Rangka Bawah (termasuk penutup)
                        const isVert = direction === 'Vertikal';
                        // Hitung tebal penutup depan/belakang dan kanan/kiri terpisah
                        let t_cover_depan = 0, t_cover_kanan = 0;
                        if (typeof activeDetails !== 'undefined') {
                            const pDepan = activeDetails.find(d => d.section === 'Penutup' && d.part_name === 'Depan');
                            const pKanan = activeDetails.find(d => d.section === 'Penutup' && (d.part_name === 'Kanan' || d.part_name === 'Kiri'));
                            if (pDepan && pDepan.material_kode !== '-') t_cover_depan = parseFloat(pDepan.calculated_thickness) / 1000 || 0;
                            if (pKanan && pKanan.material_kode !== '-') t_cover_kanan = parseFloat(pKanan.calculated_thickness) / 1000 || 0;
                        }
                        const outer_l = l_m + t_cover_kanan * 2;  // panjang + penutup kanan/kiri
                        const outer_w = w_m + t_cover_depan * 2;  // lebar + penutup depan/belakang
                        
                        const length_m = isVert ? outer_w : outer_l;
                        const span = isVert ? outer_l : outer_w;
                        
                        let positions = [];
                        if (qty === 1) {
                            positions = [0];
                        } else {
                            // Karena posisi didirikan, ketebalan arah horizontal adalah balok_t
                            const start = -span / 2 + balok_t / 2;
                            const end = span / 2 - balok_t / 2;
                            for (let i = 0; i < qty; i++) {
                                positions.push(start + (end - start) * (i / (qty - 1)));
                            }
                        }

                        positions.forEach(pos => {
                            if (isVert) {
                                // Spaced along X (Length), span along Z (Width). Didirikan: X=tebal, Y=lebar, Z=panjang
                                addBeam(new THREE.Vector3(balok_t, balok_w, length_m), new THREE.Vector3(pos, y, 0), frameMaterial, 'Kaki Balok');
                            } else {
                                // Spaced along Z (Width), span along X (Length). Didirikan: X=panjang, Y=lebar, Z=tebal
                                addBeam(new THREE.Vector3(length_m, balok_w, balok_t), new THREE.Vector3(0, y, pos), frameMaterial, 'Kaki Balok');
                            }
                        });
                    }
                }



                // Add Dimensions if visible
                addDimensions(l_m, w_m, h_m, F_t);
                dimensionGroup.visible = dimensionsVisible;

                // Adjust Ground and Grid position
                if (ground) ground.position.y = -0.025;
                if (grid) grid.position.y = 0.003;

                // Update Status text
                const statusTextEl = document.getElementById('statusText');
                if (statusTextEl) {
                    statusTextEl.textContent = 'Model: ' + Math.round(P) + ' × ' + Math.round(L) + ' × ' + Math.round(T) + ' mm';
                }

                if (resetCamera) {
                    setCameraView(currentView);
                }
            } catch (err) {
                console.error("Error drawing crate:", err);
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            const btnEdit = document.getElementById('btn-edit-config');
            const btnCancel = document.getElementById('btn-cancel-config');
            const btnSave = document.getElementById('btn-save-config');
            
            let bawahPenutupTipe = document.querySelector('[name="bawah_penutup_tipe"]');
            let atasPenutupTipe = document.querySelector('[name="atas_penutup_tipe"]');
            let bawahPenutupSelect = document.querySelector('select[name="bawah_penutup_material"]');
            let atasPenutupSelect = document.querySelector('select[name="atas_penutup_material"]');
            
            const inputs = document.querySelectorAll('.custom-input');
            const selects = document.querySelectorAll('.custom-select, .matrix-select');

            // Menyimpan value asli saat masuk mode edit (untuk fungsi batal)
            let originalValues = {};
            let originalHtmls = {};
            let isEditModeActive = false;

            // Load materials list from server
            const availableMaterials = {!! json_encode(isset($materials) ? $materials : []) !!};

            // Load initial calculation details from server
            activeDetails = {!! json_encode(isset($calculation) ? $calculation->details->map(function($d) {
                return [
                    'section' => $d->section,
                    'part_name' => $d->part_name,
                    'direction' => $d->direction,
                    'tipe_penutup' => $d->tipe_penutup,
                    'material_kode' => $d->material ? $d->material->kode : '-',
                    'material_wood_type' => $d->material ? $d->material->material_type : null,
                    'material_satuan_harga' => $d->material ? $d->material->satuan_harga : 'pcs',
                    'calculated_thickness' => (float)$d->calculated_thickness,
                    'calculated_width' => (float)$d->calculated_width,
                    'calculated_length' => (float)$d->calculated_length,
                    'quantity' => $d->quantity,
                    'side_count' => $d->side_count,
                    'total_quantity' => $d->total_quantity,
                    'total_length' => (float)$d->total_length,
                    'price_per_unit' => (float)$d->price_per_unit,
                    'subtotal_price' => (float)$d->subtotal_price,
                ];
            }) : []) !!};

            // Load initial calculation manpower from server
            let activeManpower = {!! json_encode(isset($calculation) && $calculation->manpower->isNotEmpty() ? $calculation->manpower->map(function($m) {
                return [
                    'bagian' => $m->bagian,
                    'panjang' => (float)$m->panjang,
                    'lebar' => (float)$m->lebar,
                    'sisi' => (int)$m->sisi,
                    'luas' => (float)$m->luas,
                    'total_luas' => (float)$m->total_luas,
                    'rate' => (float)$m->rate,
                    'total_biaya' => (float)$m->total_biaya,
                ];
            }) : []) !!};

            function getDefaultNails() {
                return [
                    { bagian: 'Penyangga Atas', titik: 8, per_titik: 1, kode: '-' },
                    { bagian: 'Penyangga Bawah', titik: 8, per_titik: 1, kode: '-' },
                    { bagian: 'Penyangga Kanan', titik: 8, per_titik: 1, kode: '-' },
                    { bagian: 'Penyangga Kiri', titik: 8, per_titik: 1, kode: '-' },
                    { bagian: 'Penyangga Depan', titik: 8, per_titik: 1, kode: '-' },
                    { bagian: 'Penyangga Belakang', titik: 8, per_titik: 1, kode: '-' },
                    { bagian: 'Penutup Atas', titik: 8, per_titik: 1, kode: '-' },
                    { bagian: 'Penutup Bawah', titik: 8, per_titik: 1, kode: '-' },
                    { bagian: 'Penutup Kanan', titik: 8, per_titik: 1, kode: '-' },
                    { bagian: 'Penutup Kiri', titik: 8, per_titik: 1, kode: '-' },
                    { bagian: 'Penutup Depan', titik: 8, per_titik: 1, kode: '-' },
                    { bagian: 'Penutup Belakang', titik: 8, per_titik: 1, kode: '-' },
                    { bagian: 'Kaki Balok', titik: 0, per_titik: 1, kode: '-' },
                ];
            }

            window.pakuDetails = {!! json_encode(isset($calculation) ? \Illuminate\Support\Facades\DB::table('packing_job_nails')->where('job_id', $calculation->id)->get()->map(function($c) {
                return [
                    'bagian' => $c->bagian,
                    'titik' => (int)$c->titik_paku,
                    'per_titik' => (int)$c->jumlah_paku_per_titik,
                    'kode' => $c->kode_material ?? '-',
                ];
            })->values() : []) !!};

            // Remove any old 'Rangka' defaults from existing data as they are no longer used
            window.pakuDetails = window.pakuDetails.filter(p => !p.bagian.startsWith('Rangka'));

            if (!window.pakuDetails || window.pakuDetails.length === 0) {
                window.pakuDetails = getDefaultNails();
            } else {
                const defaults = getDefaultNails();
                defaults.forEach(def => {
                    if (!window.pakuDetails.some(p => p.bagian === def.bagian)) {
                        window.pakuDetails.push(def);
                    }
                });
            }

            // Start 3D Visualizer
            init3D();
            drawCrate(true);

            // Bind visualizer toggle modes and expand toggles
            [
                'vis-toggle-penyangga-atas', 'vis-toggle-penutup-atas',
                'vis-toggle-penyangga-bawah', 'vis-toggle-penutup-bawah', 'vis-toggle-kakibalok',
                'vis-exp-penyangga-atas', 'vis-exp-penutup-atas',
                'vis-exp-penyangga-bawah', 'vis-exp-penutup-bawah', 'vis-exp-kakibalok'
            ].forEach(id => {
                const el = document.getElementById(id);
                if (el) {
                    el.addEventListener('change', () => {
                        drawCrate(false);
                    });
                }
            });

            // Bind view-buttons
            document.querySelectorAll('[data-view]').forEach(button => {
                button.addEventListener('click', function() {
                    document.querySelectorAll('[data-view]').forEach(btn => btn.classList.remove('active'));
                    button.classList.add('active');
                    setCameraView(button.dataset.view);
                });
            });

            // Bind dimensionBtn
            const dimensionBtn = document.getElementById('dimensionBtn');
            if (dimensionBtn) {
                dimensionBtn.addEventListener('click', function(event) {
                    dimensionsVisible = !dimensionsVisible;
                    if (dimensionGroup) dimensionGroup.visible = dimensionsVisible;
                    event.currentTarget.classList.toggle('active', dimensionsVisible);
                });
            }

            // Bind gridBtn
            const gridBtn = document.getElementById('gridBtn');
            if (gridBtn) {
                gridBtn.addEventListener('click', function(event) {
                    if (grid) grid.visible = !grid.visible;
                    event.currentTarget.classList.toggle('active', grid.visible);
                });
            }

            // Bind shotBtn
            const shotBtn = document.getElementById('shotBtn');
            if (shotBtn) {
                shotBtn.addEventListener('click', downloadScreenshot);
            }

            function formatRupiah(amount) {
                return 'Rp ' + Number(amount).toLocaleString('id-ID', { minimumFractionDigits: 0, maximumFractionDigits: 0 });
            }

            function formatNumber(number, decimals = 2) {
                return Number(number).toLocaleString('id-ID', { minimumFractionDigits: decimals, maximumFractionDigits: decimals });
            }



            function updateUI(details, summary) {
                // Update active details reference
                activeDetails = details;

                // Update stats
                let statPakuEl = document.getElementById('stat-paku');
                if (statPakuEl) statPakuEl.innerText = summary.nail_points || 0;
                
                let statAreaEl = document.getElementById('stat-area');
                if (statAreaEl) statAreaEl.innerText = formatNumber(summary.area_kerja, 2);
                
                // Update cost summary
                let costRangkaEl = document.getElementById('cost-rangka');
                if (costRangkaEl) costRangkaEl.innerText = formatRupiah(summary.cost_rangka + (summary.cost_penyangga || 0));
                
                let costPenutupEl = document.getElementById('cost-penutup');
                if (costPenutupEl) costPenutupEl.innerText = formatRupiah(summary.cost_penutup);

                let costBawahEl = document.getElementById('cost-bawah');
                if (costBawahEl) costBawahEl.innerText = formatRupiah(summary.cost_bawah || 0);
                
                let costTotalEl = document.getElementById('cost-total');
                if (costTotalEl) costTotalEl.innerText = formatRupiah(summary.total_cost);
                
                // Update manpower estimates
                let wPotong = document.getElementById('waktu-potong');
                if (wPotong) wPotong.innerText = formatNumber(summary.manpower_potong || 0, 2);
                let wSerut = document.getElementById('waktu-serut');
                if (wSerut) wSerut.innerText = formatNumber(summary.manpower_serut || 0, 2);
                let wPerakitan = document.getElementById('waktu-perakitan');
                if (wPerakitan) wPerakitan.innerText = formatNumber(summary.manpower_perakitan || 0, 2);
                let wPrepare = document.getElementById('waktu-prepare');
                if (wPrepare) wPrepare.innerText = formatNumber(summary.manpower_prepare || 0, 2);
                let wTotal = document.getElementById('waktu-total');
                if (wTotal) wTotal.innerText = formatNumber((summary.total_waktu_manpower || 0) / 60, 2);
                let wTotalCard = document.getElementById('waktu-total-card');
                if (wTotalCard) wTotalCard.innerText = formatNumber((summary.total_waktu_manpower || 0) / 60, 2);
                let wVolumeM3 = document.getElementById('waktu-volume-m3');
                if (wVolumeM3) wVolumeM3.innerText = formatNumber(summary.volume_m3 || 0, 3);
                
                let volCells = document.querySelectorAll('.vol-m3-cell');
                volCells.forEach(cell => {
                    cell.innerText = formatNumber(summary.volume_m3 || 0, 3);
                });
                
                // Rebuild tables
                let htmlAll = '';
                let htmlRangka = '';
                let htmlPenyangga = '';
                let htmlPenutup = '';
                let htmlBawah = '';
                
                // Group details for Rancangan Material
                let groupedRancangan = {};
                
                details.forEach((detail, index) => {
                    let directionHtml = detail.direction;
                    let materialHtml = `<span class="fw-bold">${detail.material_kode || '-'}</span>`;

                    if (detail.section === 'Rangka') {
                        directionHtml = '-';
                    }

                    let isMergedParent = (detail.section === 'Rangka' && (detail.part_name === 'Atas Panjang' || detail.part_name === 'Bawah Panjang'));
                    let isMergedChild = (detail.section === 'Rangka' && (detail.part_name === 'Atas Lebar' || detail.part_name === 'Bawah Lebar'));
                    
                    let isBawahRangkaParent = (detail.section === 'Bawah' && detail.part_name === 'Rangka Panjang');
                    let isBawahRangkaChild = (detail.section === 'Bawah' && detail.part_name === 'Rangka Lebar');
                    
                    let isPenutupParent = false;
                    let isPenutupChild = false;
                    let penutupRowspan = 0;
                    if (detail.section === 'Penutup') {
                        let penutupItems = details.filter(d => d.section === 'Penutup');
                        let firstPenutupIdx = details.findIndex(d => d.section === 'Penutup');
                        if (index === firstPenutupIdx) {
                            isPenutupParent = true;
                            penutupRowspan = penutupItems.length;
                        } else {
                            isPenutupChild = true;
                        }
                    }

                    let rowspanAttr = isMergedParent ? ' rowspan="2"' : '';
                    let pRowspanAttr = isPenutupParent ? ` rowspan="${penutupRowspan}"` : '';
                    
                    let middleColsHtml = '';
                    if (detail.section === 'Penutup') {
                        let materialCols = '';
                        if (isPenutupParent) {
                            materialCols = `
                                <td${pRowspanAttr}>${materialHtml}</td>
                                <td${pRowspanAttr}>${formatNumber(detail.calculated_thickness, 0)}</td>
                                <td${pRowspanAttr}>${formatNumber(detail.calculated_width, 0)}</td>
                            `;
                        }
                        middleColsHtml = `
                            <td>${directionHtml}</td>
                            ${materialCols}
                        `;
                    } else {
                        if (isMergedParent) {
                            middleColsHtml = `
                                <td rowspan="2">${directionHtml}</td>
                                <td rowspan="2">${materialHtml}</td>
                                <td rowspan="2">${formatNumber(detail.calculated_thickness, 0)}</td>
                                <td rowspan="2">${formatNumber(detail.calculated_width, 0)}</td>
                            `;
                        } else if (isMergedChild) {
                            middleColsHtml = '';
                        } else if (isBawahRangkaParent) {
                            middleColsHtml = `
                                <td>${directionHtml}</td>
                                <td rowspan="2">${materialHtml}</td>
                                <td rowspan="2">${formatNumber(detail.calculated_thickness, 0)}</td>
                                <td rowspan="2">${formatNumber(detail.calculated_width, 0)}</td>
                            `;
                        } else if (isBawahRangkaChild) {
                            middleColsHtml = `
                                <td>${directionHtml}</td>
                            `;
                        } else {
                            middleColsHtml = `
                                <td>${directionHtml}</td>
                                <td>${materialHtml}</td>
                                <td>${formatNumber(detail.calculated_thickness, 0)}</td>
                                <td>${formatNumber(detail.calculated_width, 0)}</td>
                            `;
                        }
                    }

                    let isTr = (detail.material_kode && detail.material_kode.includes('TR')) || (detail.material_nama && detail.material_nama.toLowerCase().includes('triplex'));
                    let lenVal = parseFloat(detail.total_length || 0);
                    let unitStr = 'm';
                    if (isTr) {
                        let w = parseFloat(detail.calculated_width || 0);
                        let l = parseFloat(detail.calculated_length || 0);
                        let q = parseFloat(detail.total_quantity || 0);
                        lenVal = (w * l * q) / 1000000;
                        unitStr = 'm²';
                    }

                    let rowAll = `<tr>
                        <td><span class="badge bg-light text-dark">${detail.section}</span></td>
                        <td>${detail.part_name}</td>
                        ${middleColsHtml}
                        <td>${formatNumber(detail.calculated_length, 0)}</td>
                        <td>${detail.quantity}</td>
                        <td>${detail.side_count}</td>
                        <td class="fw-semibold">${detail.total_quantity}</td>
                        <td>${formatNumber(lenVal, 2)} <span style="font-size: 10px;" class="text-muted">${unitStr}</span></td>
                        <td>${formatNumber(detail.price_per_unit, 0)}</td>
                        <td class="fw-bold text-navy">${formatRupiah(detail.subtotal_price)}</td>
                    </tr>`;
                    htmlAll += rowAll;
                    
                    let rowSection = `<tr>
                        <td>${detail.part_name}</td>
                        ${middleColsHtml}
                        <td>${formatNumber(detail.calculated_length, 0)}</td>
                        <td>${detail.quantity}</td>
                        <td>${detail.side_count}</td>
                        <td class="fw-semibold">${detail.total_quantity}</td>
                        <td>${formatNumber(lenVal, 2)} <span style="font-size: 10px;" class="text-muted">${unitStr}</span></td>
                        <td>${formatNumber(detail.price_per_unit, 0)}</td>
                        <td class="fw-bold text-navy">${formatRupiah(detail.subtotal_price)}</td>
                    </tr>`;
                    
                    if (detail.section === 'Rangka') htmlRangka += rowSection;
                    if (detail.section === 'Penyangga') htmlPenyangga += rowSection;
                    if (detail.section === 'Penutup') htmlPenutup += rowSection;
                    if (detail.section === 'Bawah') htmlBawah += rowSection;
                    
                    // Grouping for Rancangan Material
                    let key = `${detail.material_kode || '-'}|${detail.calculated_thickness}|${detail.calculated_width}|${detail.calculated_length}`;
                    if (!groupedRancangan[key]) {
                        groupedRancangan[key] = {
                            material_kode: detail.material_kode || '-',
                            part_names: [],
                            calculated_thickness: detail.calculated_thickness,
                            calculated_width: detail.calculated_width,
                            calculated_length: detail.calculated_length,
                            total_quantity: 0,
                            material_satuan_harga: detail.material_satuan_harga || 'pcs'
                        };
                    }
                    
                    if (detail.part_name && !groupedRancangan[key].part_names.includes(detail.part_name)) {
                        groupedRancangan[key].part_names.push(detail.part_name);
                    }
                    
                    groupedRancangan[key].total_quantity += parseInt(detail.total_quantity || 0);
                });
                
                let htmlRancangan = '';
                Object.values(groupedRancangan).forEach(item => {
                    let uomLabel = (item.material_satuan_harga.toLowerCase() === 'sqm') ? 'Sqm' : 'Pcs';
                    let rowRancangan = `<tr>
                        <td class="fw-bold">${item.material_kode}</td>
                        <td>${item.part_names.join(', ')}</td>
                        <td>${formatNumber(item.calculated_thickness, 0)}</td>
                        <td>${formatNumber(item.calculated_width, 0)}</td>
                        <td>${formatNumber(item.calculated_length, 0)}</td>
                        <td>${item.total_quantity} ${uomLabel}</td>
                    </tr>`;
                    htmlRancangan += rowRancangan;
                });
                
                // Fallbacks
                const fallbackHtml = `<tr><td colspan="12" class="text-center text-muted py-4">Belum ada data kalkulasi</td></tr>`;
                const fallbackAllHtml = `<tr><td colspan="13" class="text-center text-muted py-4">Belum ada data kalkulasi</td></tr>`;
                const fallbackRancanganHtml = `<tr><td colspan="6" class="text-center text-muted py-4">Belum ada data kalkulasi</td></tr>`;
                
                let tabAllTbody = document.getElementById('tab-all-tbody');
                if (tabAllTbody) tabAllTbody.innerHTML = htmlAll || fallbackAllHtml;
                
                let tabRangkaTbody = document.getElementById('tab-rangka-tbody');
                if (tabRangkaTbody) tabRangkaTbody.innerHTML = htmlRangka || fallbackHtml;
                
                let tabPenyanggaTbody = document.getElementById('tab-penyangga-tbody');
                if (tabPenyanggaTbody) tabPenyanggaTbody.innerHTML = htmlPenyangga || fallbackHtml;
                
                let tabPenutupTbody = document.getElementById('tab-penutup-tbody');
                if (tabPenutupTbody) tabPenutupTbody.innerHTML = htmlPenutup || fallbackHtml;
                
                let tbodyBawah = document.getElementById('tab-bawah-tbody');
                if (tbodyBawah) tbodyBawah.innerHTML = htmlBawah || fallbackHtml;
                
                let rancanganMaterialTbody = document.getElementById('rancangan-material-tbody');
                if (rancanganMaterialTbody) rancanganMaterialTbody.innerHTML = htmlRancangan || fallbackRancanganHtml;

                // Update Wood Resume in Col 3
                let totalWoodLength = 0;
                let materialResume = {};

                details.forEach(detail => {
                    let matKode = detail.material_kode || '-';
                    if (matKode !== '-' && matKode !== '') {
                        let len = parseFloat(detail.total_length || 0);
                        let isTriplex = (matKode.includes('TR') || (detail.material_nama && detail.material_nama.toLowerCase().includes('triplex')));
                        if (isTriplex) {
                            let w = parseFloat(detail.calculated_width || 0);
                            let l = parseFloat(detail.calculated_length || 0);
                            let qty = parseFloat(detail.total_quantity || 0);
                            len = (w * l * qty) / 1000000;
                        }
                        totalWoodLength += len;

                        let matNama = detail.material_nama || matKode;

                        if (!materialResume[matKode]) {
                            materialResume[matKode] = {
                                kode: matKode,
                                nama: matNama,
                                length: 0
                            };
                        }
                        materialResume[matKode].length += len;
                    }
                });

                let resumeLengthEl = document.getElementById('resume-total-panjang');
                if (resumeLengthEl) {
                    resumeLengthEl.innerHTML = `${formatNumber(totalWoodLength, 2)} <span class="fw-bold text-muted" style="font-size: 14px;">m</span>`;
                }

                let resumeLuasEl = document.getElementById('resume-total-luas');
                if (resumeLuasEl) {
                    resumeLuasEl.innerHTML = `${formatNumber(summary.area_kerja, 2)} <span class="fw-bold text-muted" style="font-size: 14px;">m²</span>`;
                }

                let resumeContainer = document.getElementById('resume-material-container');
                if (resumeContainer) {
                    let htmlList = '';
                    let keys = Object.keys(materialResume);
                    if (keys.length === 0) {
                        htmlList = `<div class="text-center text-muted py-4" style="font-size: 14px;">Belum ada data kebutuhan material</div>`;
                    } else {
                        keys.forEach(key => {
                            let mat = materialResume[key];
                            let unit = (mat.kode.includes('TR') || mat.nama.toLowerCase().includes('triplex')) ? 'm²' : 'm';
                            htmlList += `
                            <div class="p-3 border rounded bg-white shadow-xs hover-shadow transition">
                                <div class="d-flex flex-column gap-2 w-100">
                                    <div class="d-flex align-items-start">
                                        <span class="badge text-white fw-bold py-2 px-3 text-wrap text-start" style="font-size: 13px; background-color: var(--navy); line-height: 1.4;">${mat.nama}</span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-end w-100">
                                        <div class="text-dark fw-bold text-wrap pe-2" style="font-size: 14px; line-height: 1.4; word-break: break-word;" title="${mat.kode}">
                                            ${mat.kode}
                                        </div>
                                        <div class="text-end flex-shrink-0">
                                            <span class="fw-extrabold text-success" style="font-size: 18px;">${formatNumber(mat.length, 1)}</span>
                                            <span class="text-muted fw-bold ms-1" style="font-size: 14px;">${unit}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>`;
                        });
                    }
                    resumeContainer.innerHTML = htmlList;
                }

                // Calculate manpower
                calculateManpower();
                
                // Calculate nails
                calculateNails();

                // Redraw crate
                drawCrate();
            }

            function syncMasterDropdownsToActiveDetails() {
                const getVal = name => {
                    let el = document.querySelector(`[name="${name}"]`);
                    return el ? el.value : null;
                };

                const getText = name => {
                    let el = document.querySelector(`[name="${name}"]`);
                    return el && el.options && el.selectedIndex > -1 ? el.options[el.selectedIndex].text : null;
                };

                const getWoodTypeAttr = name => {
                    let el = document.querySelector(`[name="${name}"]`);
                    return el && el.options && el.selectedIndex > -1 ? el.options[el.selectedIndex].getAttribute('data-wood-type') : null;
                };

                const updatePart = (section, partMatches, includeVal, arahVal, materialVal, materialText, materialWoodTypeAttr, excludeBawah = false, tipePenutupVal = null) => {
                    let partsToMatch = Array.isArray(partMatches) ? partMatches : [partMatches];
                    
                    partsToMatch.forEach(partName => {
                        let matchFound = false;
                        activeDetails.forEach(d => {
                            if (d.section === section && d.part_name === partName && (excludeBawah ? !d.part_name.includes('Bawah') : true)) {
                                matchFound = true;
                                if (includeVal === "0" || !materialVal) {
                                    d.material_kode = '-';
                                    d.material_nama = '-';
                                } else {
                                    d.material_kode = materialVal;
                                    let matNama = materialWoodTypeAttr;
                                    if (matNama) {
                                        matNama = matNama.toLowerCase().replace(/(?:^|\s)\w/g, match => match.toUpperCase());
                                    } else {
                                        matNama = materialText;
                                    }
                                    d.material_nama = matNama;
                                }
                                if (arahVal) {
                                    d.direction = arahVal;
                                }
                                if (tipePenutupVal !== null) {
                                    d.tipe_penutup = tipePenutupVal;
                                    d.include = (tipePenutupVal === 'Tanpa Penutup' || tipePenutupVal === 'Tidak makai penutup' || !tipePenutupVal) ? "0" : "1";
                                } else {
                                    d.include = includeVal;
                                }
                            }
                        });
                        
                        if (!matchFound) {
                            activeDetails.push({
                                section: section,
                                part_name: partName,
                                material_kode: (includeVal === "0" || !materialVal) ? '-' : materialVal,
                                material_nama: (includeVal === "0" || !materialVal) ? '-' : materialText,
                                direction: arahVal,
                                tipe_penutup: tipePenutupVal,
                                include: tipePenutupVal !== null ? ((tipePenutupVal === 'Tanpa Penutup' || tipePenutupVal === 'Tidak makai penutup' || !tipePenutupVal) ? "0" : "1") : includeVal
                            });
                        }
                    });
                };

                // Helper function to resolve include from tipe_penutup
                const getPenutupInclude = name => {
                    let val = getVal(name);
                    return (val === 'Tanpa Penutup' || val === 'Tidak makai penutup' || !val) ? "0" : "1";
                };

                // Area Atas (Rangka Atas dan Tinggi ditiadakan)
                updatePart('Penyangga', ['Atas', 'Kanan', 'Kiri', 'Depan', 'Belakang'], getVal('atas_penyangga_include'), getVal('atas_penyangga_arah'), getVal('atas_penyangga_material'), getText('atas_penyangga_material'), getWoodTypeAttr('atas_penyangga_material'), true);
                updatePart('Penutup', ['Atas', 'Kanan', 'Kiri', 'Depan', 'Belakang'], getPenutupInclude('atas_penutup_tipe'), getVal('atas_penutup_arah'), getVal('atas_penutup_material'), getText('atas_penutup_material'), getWoodTypeAttr('atas_penutup_material'), true, getVal('atas_penutup_tipe'));

                // Area Bawah

                updatePart('Bawah', ['Penyangga'], getVal('bawah_penyangga_include'), getVal('bawah_penyangga_arah'), getVal('bawah_penyangga_material'), getText('bawah_penyangga_material'), getWoodTypeAttr('bawah_penyangga_material'), false);
                updatePart('Bawah', ['Penutup'], getPenutupInclude('bawah_penutup_tipe'), getVal('bawah_penutup_arah'), getVal('bawah_penutup_material'), getText('bawah_penutup_material'), getWoodTypeAttr('bawah_penutup_material'), false, getVal('bawah_penutup_tipe'));
                updatePart('Bawah', ['Kaki Balok'], getVal('include_pallet_base'), getVal('bawah_kakibalok_arah'), getVal('bawah_kakibalok_material'), getText('bawah_kakibalok_material'), getWoodTypeAttr('bawah_kakibalok_material'), false);

                // Update Jumlah Kaki Balok logic in UI
                let lengthInput = document.querySelector('[name="length"]');
                let jumlahKakiInput = document.querySelector('[name="jumlah_kaki_balok"]');
                if (lengthInput && jumlahKakiInput) {
                    let L = parseFloat(lengthInput.value) || 0;
                    jumlahKakiInput.value = Math.max(2, Math.floor(L / 800) + 1);
                }
            }

            let debounceTimer = null;
            function runSimulation(isFromMaster = true) {
                if (!isEditModeActive) return;
                
                if (debounceTimer) clearTimeout(debounceTimer);
                
                debounceTimer = setTimeout(() => {
                    // Check if isFromMaster is true or an Event object
                    if (isFromMaster === true || (isFromMaster && isFromMaster.target)) {
                        syncMasterDropdownsToActiveDetails();
                    }
                    
                    let formData = {
                        _token: '{{ csrf_token() }}'
                    };
                    inputs.forEach(input => {
                        formData[input.name] = input.value;
                    });
                    selects.forEach(select => {
                        let val = select.value;
                        formData[select.name] = val;
                    });
                    
                    const kakibalokInc = document.querySelector('[name="include_pallet_base"]');
                    if (kakibalokInc) {
                        formData['include_pallet_base'] = kakibalokInc.value === '1' ? 1 : 0;
                    }
                    
                    formData['details'] = activeDetails;
                    
                    fetch('{{ route("packaging.calculations.simulate") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify(formData)
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.status === 'success') {
                            updateUI(data.details, data.summary);
                        }
                    })
                    .catch(err => console.error("Simulation error:", err));
                }, 300);
            }

            // Bind simulation to input changes
            inputs.forEach(input => {
                input.addEventListener('input', runSimulation);
            });
            selects.forEach(select => {
                select.addEventListener('change', runSimulation);
            });
            
            const bawahKakibalokInclude = document.querySelector('[name="include_pallet_base"]');
            const jarakBalokWrapper = document.getElementById('jarak_balok_additional_wrapper');
            
            function toggleJumlahKakiBalokOverlay(val) {
                const isIncluded = (val === '1' || (typeof val === 'string' && val.toLowerCase() === 'ya'));
                const styleStr = isIncluded ? '' : 'opacity: 0.5; pointer-events: none; background-color: #f8f9fa;';
                
                if (jarakBalokWrapper) {
                    jarakBalokWrapper.style.cssText = styleStr;
                    const input = jarakBalokWrapper.querySelector('input[name="jumlah_kaki_balok"]');
                    if (input) {
                        if (isIncluded) {
                            const lengthInput = document.querySelector('input[name="length"]');
                            const P = parseFloat(lengthInput ? lengthInput.value : 0) || 0;
                            input.value = Math.max(2, Math.floor(P / 800) + 1);
                        } else {
                            input.value = '-';
                        }
                    }
                }
            }

            if (bawahKakibalokInclude) {
                bawahKakibalokInclude.addEventListener('change', function() {
                    toggleJumlahKakiBalokOverlay(this.value);
                });
                toggleJumlahKakiBalokOverlay(bawahKakibalokInclude.value);
            }
            let isNewRecord = {{ isset($calculation) ? 'false' : 'true' }};
            const saveUrl = isNewRecord ? "{{ route('packaging.calculations.store') }}" : "{{ isset($calculation) ? route('packaging.calculations.update', $calculation->id ?? 0) : '' }}";
            
            function calculateManpower() {
                let P = 0, L = 0, T = 0, rate = {{ $manpowerRate }};
                let totalSqm = 0, totalBiayaMp = 0;
                let rows = [];

                if (!isEditModeActive && activeManpower && activeManpower.length > 0) {
                    rows = activeManpower;
                    totalSqm = rows.reduce((sum, r) => sum + r.total_luas, 0);
                    totalBiayaMp = rows.reduce((sum, r) => sum + r.total_biaya, 0);
                } else {
                    let lengthInput = document.querySelector('input[name="length"]');
                    let widthInput = document.querySelector('input[name="width"]');
                    let heightInput = document.querySelector('input[name="height"]');
                    
                    P = parseFloat(lengthInput ? lengthInput.value : 0) || 0;
                    L = parseFloat(widthInput ? widthInput.value : 0) || 0;
                    T = parseFloat(heightInput ? heightInput.value : 0) || 0;
                    
                    let hasAnyValidMaterial = typeof activeDetails !== 'undefined' && activeDetails.some(d => d.material_kode && d.material_kode !== '-');
                    if (!hasAnyValidMaterial) {
                        rate = 0; // Force cost to 0 if no material
                    }

                    
                    let luasAtasBawah = (P / 1000) * (L / 1000);
                    let totalLuasAtasBawah = luasAtasBawah * 2;
                    
                    let luasKananKiri = (L / 1000) * (T / 1000);
                    let totalLuasKananKiri = luasKananKiri * 2;
                    
                    let luasDepanBelakang = (P / 1000) * (T / 1000);
                    let totalLuasDepanBelakang = luasDepanBelakang * 2;
                    
                    totalSqm = totalLuasAtasBawah + totalLuasKananKiri + totalLuasDepanBelakang;
                    totalBiayaMp = totalSqm * rate;

                    rows = [
                        {
                            bagian: 'Atas & Bawah',
                            panjang: P,
                            lebar: L,
                            sisi: 2,
                            luas: luasAtasBawah,
                            total_luas: totalLuasAtasBawah,
                            rate: rate,
                            total_biaya: totalLuasAtasBawah * rate
                        },
                        {
                            bagian: 'Kanan & Kiri',
                            panjang: L,
                            lebar: T,
                            sisi: 2,
                            luas: luasKananKiri,
                            total_luas: totalLuasKananKiri,
                            rate: rate,
                            total_biaya: totalLuasKananKiri * rate
                        },
                        {
                            bagian: 'Depan & Belakang',
                            panjang: P,
                            lebar: T,
                            sisi: 2,
                            luas: luasDepanBelakang,
                            total_luas: totalLuasDepanBelakang,
                            rate: rate,
                            total_biaya: totalLuasDepanBelakang * rate
                        }
                    ];
                    activeManpower = rows;
                }
                
                let html = '';
                rows.forEach(r => {
                    html += `
                        <tr>
                            <td class="fw-semibold text-navy">${r.bagian}</td>
                            <td class="text-end text-secondary">${formatNumber(r.panjang, 0)}</td>
                            <td class="text-end text-secondary">${formatNumber(r.lebar, 0)}</td>
                            <td class="text-center text-secondary">${r.sisi}</td>
                            <td class="text-end text-secondary">${formatNumber(r.luas, 2)}</td>
                            <td class="text-end fw-black text-navy">${formatNumber(r.total_luas, 2)}</td>
                        </tr>
                    `;
                });
                
                let tbody = document.getElementById('manpower-tbody');
                if (tbody) tbody.innerHTML = html;
                
                let totalSqmEl = document.getElementById('mp-total-sqm');
                if (totalSqmEl) totalSqmEl.innerText = formatNumber(totalSqm, 2);
                
                let totalCostEl = document.getElementById('mp-total-cost');
                if (totalCostEl) totalCostEl.innerText = formatRupiah(totalBiayaMp);
                
                let costMpEl = document.getElementById('cost-manpower');
                if (costMpEl) costMpEl.innerText = formatRupiah(totalBiayaMp);
                
                updateTotalPackCost();
            }

            function getCurrencyElementValue(elementId) {
                const element = document.getElementById(elementId);
                if (!element) return null;

                const numericText = String(element.textContent || '')
                    .replace(/[^0-9]/g, '');

                return numericText === '' ? 0 : (Number.parseFloat(numericText) || 0);
            }

            function updateTotalPackCost() {
                const sourceIds = [
                    'cost-rangka',
                    'cost-penutup',
                    'cost-bawah',
                    'cost-manpower',
                    'cost-paku'
                ];

                const sourceValues = sourceIds.map(getCurrencyElementValue);

                /*
                 * Pada layout read-only yang baru, card rincian biaya lama dapat
                 * tidak dirender. Jangan membaca .innerText dari elemen null dan
                 * jangan menimpa total server menjadi Rp 0 saat semua sumber hilang.
                 */
                if (sourceValues.every(value => value === null)) {
                    return;
                }

                const grandTotal = sourceValues.reduce(
                    (total, value) => total + (value ?? 0),
                    0
                );

                const totalCostEl = document.getElementById('cost-total');
                if (totalCostEl) {
                    totalCostEl.textContent = formatRupiah(grandTotal);
                }

                const resumeTotalCostEl = document.getElementById('cost-total-packing-resume');
                if (resumeTotalCostEl) {
                    resumeTotalCostEl.textContent = formatRupiah(grandTotal);
                }
            }

            // Initial calculation on page load
            calculateManpower();
            calculateNails();


            function calculateNails() {
                // UPDATE pakuDetails dynamically based on activeDetails before calculation
                let coverSelect = document.querySelector('[name="atas_penutup_tipe"]'); // Fallback to atas for global checking if needed
                let skipPenutup = !coverSelect || !coverSelect.value || coverSelect.value.toLowerCase().includes('tidak') || coverSelect.value.toLowerCase().includes('tanpa');
                
                pakuDetails.forEach(r => {
                        let isPenyangga = r.bagian.startsWith('Penyangga');
                        let isPenutup = r.bagian.startsWith('Penutup');
                        let isKaki = r.bagian === 'Kaki Balok';
                        let part = r.bagian.replace('Penyangga ', '').replace('Penutup ', '');
                        
                        let qtyPenyangga = 0;
                        let qtyKaki = 0;
                        let qtyPenutupItem = 0;
                        
                        activeDetails.forEach(d => {
                            if ((d.section === 'Penyangga' && d.part_name === part) || (d.section === 'Bawah' && d.part_name === 'Penyangga' && part === 'Bawah')) {
                                qtyPenyangga += d.total_quantity;
                            }
                            if (d.section === 'Bawah' && d.part_name === 'Kaki Balok') {
                                qtyKaki += d.total_quantity; // Global kaki balok qty for Penyangga Bawah
                            }
                            if ((d.section === 'Penutup' && d.part_name === part) || (d.section === 'Bawah' && d.part_name === 'Penutup' && part === 'Bawah')) {
                                qtyPenutupItem += d.total_quantity;
                            }
                        });
                        
                        if (isPenyangga) {
                            if (part === 'Bawah') {
                                r.titik = qtyPenyangga * qtyKaki;
                            } else {
                                r.titik = qtyPenyangga * 2;
                            }
                        } else if (isKaki) {
                            r.titik = 0; // Kaki Balok = 0
                        } else if (isPenutup) {
                            if (skipPenutup) {
                                r.titik = 0;
                            } else {
                                r.titik = qtyPenutupItem * 4;
                            }
                        }
                });

                let pricePerKg = {{ $nailsPricePerKg }};
                let nailsWeightPerPiece = {{ $nailsWeightPerPiece }};
                
                let hasAnyValidMaterial = typeof activeDetails !== 'undefined' && activeDetails.some(d => d.material_kode && d.material_kode !== '-');
                if (!hasAnyValidMaterial) {
                    pricePerKg = 0;
                }
                
                let totalNailPoints = 0;
                let grandTotalNailsCost = 0;
                let totalEstWeight = 0;
                let html = '';

                pakuDetails.forEach((r, index) => {
                    let isPenutupRow = r.bagian.startsWith('Penutup');
                    if (skipPenutup && isPenutupRow) {
                        return; // Hide row and skip calculation if no cover is used
                    }

                    let totalPaku = r.titik * r.per_titik;
                    if (!hasAnyValidMaterial) {
                        totalPaku = 0; // Force nails to 0 if no material
                    }
                    let estBerat = totalPaku * nailsWeightPerPiece;
                    let totalHarga = estBerat * pricePerKg;

                    totalNailPoints += totalPaku;
                    totalEstWeight += estBerat;
                    grandTotalNailsCost += totalHarga;

                    let perTitikCol = '';
                    if (isEditModeActive) {
                        perTitikCol = `
                            <div class="d-flex justify-content-end align-items-center">
                                <input type="number" class="form-control form-control-sm text-end fw-bold paku-qty-input custom-input" 
                                       data-index="${index}" value="${r.per_titik}" min="0" style="width: 80px; font-size: 11px; height: 26px;">
                            </div>
                        `;
                    } else {
                        perTitikCol = formatNumber(r.per_titik, 0);
                    }

                    let rawKode = r.kode ? r.kode.toString().replace('NAIL-', '') : '-';
                    let kodeBadge = (rawKode !== '-' && rawKode !== 'null' && rawKode !== '') ? `<span class="fw-bold text-dark">${rawKode}</span>` : `<span class="text-muted small">-</span>`;
                    html += `
                        <tr>
                            <td class="fw-semibold text-navy">${r.bagian}</td>
                            <td class="text-center">${kodeBadge}</td>
                            <td class="text-end text-secondary">${formatNumber(r.titik, 0)}</td>
                            <td class="text-end text-secondary">${perTitikCol}</td>
                            <td class="text-end fw-bold text-navy">${formatNumber(totalPaku, 0)}</td>
                            <td class="text-end text-secondary">${formatNumber(estBerat, 2)}</td>
                            <td class="text-end text-secondary">${formatRupiah(pricePerKg)}</td>
                            <td class="text-end fw-black text-navy">${formatRupiah(totalHarga)}</td>
                        </tr>
                    `;
                });

                // Append Subtotal Row
                html += `
                    <tr class="fw-bold" style="background-color: rgba(241, 245, 249, 0.7); border-top: 2px solid #cbd5e1;">
                        <td class="fw-bold text-navy">TOTAL BIAYA PAKU</td>
                        <td class="text-center text-muted">-</td>
                        <td class="text-end text-muted">-</td>
                        <td class="text-end text-muted">-</td>
                        <td class="text-end fw-black text-navy">${formatNumber(totalNailPoints, 0)} pcs</td>
                        <td class="text-end fw-black text-navy">${formatNumber(totalEstWeight, 2)} Kg</td>
                        <td class="text-end text-muted">-</td>
                        <td class="text-end fw-black text-primary">${formatRupiah(grandTotalNailsCost)}</td>
                    </tr>
                `;

                let tbody = document.getElementById('paku-tbody');
                if (tbody) tbody.innerHTML = html;

                if (isEditModeActive) {
                    document.querySelectorAll('.paku-qty-input').forEach(input => {
                        input.addEventListener('input', function() {
                            let idx = parseInt(this.dataset.index);
                            let val = parseInt(this.value) || 0;
                            pakuDetails[idx].per_titik = val;
                            calculateNails();
                        });
                    });
                }

                let statPakuEl = document.getElementById('stat-paku');
                if (statPakuEl) statPakuEl.innerText = formatNumber(totalNailPoints, 0);

                let costPakuEl = document.getElementById('cost-paku');
                if (costPakuEl) costPakuEl.innerText = formatRupiah(grandTotalNailsCost);

                // Update summary card values
                let totalPcsEl = document.getElementById('paku-total-pcs');
                if (totalPcsEl) totalPcsEl.innerText = formatNumber(totalNailPoints, 0);

                let totalKgEl = document.getElementById('paku-total-kg');
                if (totalKgEl) totalKgEl.innerText = formatNumber(totalEstWeight, 2);

                let totalCostEl = document.getElementById('paku-total-cost');
                if (totalCostEl) totalCostEl.innerText = formatRupiah(grandTotalNailsCost);

                updateTotalPackCost();
                
                if (typeof window.drawMaterialStacks === 'function') {
                    window.drawMaterialStacks();
                }
            }

            calculateNails();

            if (btnSave) {
                btnSave.addEventListener('click', (e) => {
                    e.preventDefault();
                    const formEdit = document.getElementById('form-edit-config');
                    const len = document.querySelector('input[name="length"]').value || 0;
                    const wid = document.querySelector('input[name="width"]').value || 0;
                    const hgt = document.querySelector('input[name="height"]').value || 0;
                    const projectName = `${len} x ${wid} x ${hgt} mm`;
                    
                    let formData = {
                        project_name: projectName,
                        total_material_cost: 0,
                        _token: '{{ csrf_token() }}'
                    };
                    
                    formEdit.querySelectorAll('input').forEach(input => {
                        if (input.name) {
                            formData[input.name] = input.value;
                        }
                    });
                    
                    formEdit.querySelectorAll('select').forEach(select => {
                        if (select.name) {
                            let val = select.value;
                            if (select.name === 'include_pallet_base') {
                                val = (val === '1' || val.toLowerCase() === 'ya') ? 1 : 0;
                            }
                            formData[select.name] = val;
                        }
                    });
                    
                    formData['details'] = activeDetails;
                    formData['nails'] = pakuDetails;
                    
                    const originalContent = btnSave.innerHTML;
                    btnSave.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>Menyimpan...';
                    btnSave.setAttribute('disabled', 'disabled');

                    fetch(saveUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify(formData)
                    })
                    .then(response => response.json())
                    .then(data => {
                        btnSave.removeAttribute('disabled');
                        btnSave.innerHTML = originalContent;
                        
                        if (data.status === 'success') {
                             alert(data.message);
                             isNewRecord = false;
                             window.location.href = `/packaging-calculations/${data.data.id}`;
                        } else {
                            alert(data.message || 'Terjadi kesalahan saat menyimpan data.');
                            console.error(data.errors);
                        }
                    })
                    .catch(error => {
                        btnSave.removeAttribute('disabled');
                        btnSave.innerHTML = originalContent;
                        alert('Gagal menghubungi server.');
                        console.error(error);
                    });
                });
            }

            // Bind changes on row-level table select elements (direction and material)
            document.addEventListener('change', function(e) {
                if (e.target.classList.contains('table-direction-select')) {
                    let index = parseInt(e.target.dataset.index);
                    if (activeDetails[index]) {
                        activeDetails[index].direction = e.target.value;
                        runSimulation(false);
                    }
                }
                if (e.target.classList.contains('table-material-select')) {
                    let index = parseInt(e.target.dataset.index);
                    if (activeDetails[index]) {
                        let selectedValue = e.target.value;
                        let changedPart = activeDetails[index];
                        
                        changedPart.material_kode = selectedValue;

                        // Sync rules for Rangka section
                        if (changedPart.section === 'Rangka') {
                            if (changedPart.part_name === 'Atas Panjang' || changedPart.part_name === 'Atas Lebar') {
                                activeDetails.forEach(detail => {
                                    if (detail.section === 'Rangka' && (detail.part_name === 'Atas Panjang' || detail.part_name === 'Atas Lebar')) {
                                        detail.material_kode = selectedValue;
                                    }
                                });
                            } else if (changedPart.part_name === 'Bawah Panjang' || changedPart.part_name === 'Bawah Lebar') {
                                activeDetails.forEach(detail => {
                                    if (detail.section === 'Rangka' && (detail.part_name === 'Bawah Panjang' || detail.part_name === 'Bawah Lebar')) {
                                        detail.material_kode = selectedValue;
                                    }
                                });
                            }
                        }

                        // Sync rules for Bawah section (Rangka Panjang & Rangka Lebar)
                        if (changedPart.section === 'Bawah') {
                            if (changedPart.part_name === 'Rangka Panjang' || changedPart.part_name === 'Rangka Lebar') {
                                activeDetails.forEach(detail => {
                                    if (detail.section === 'Bawah' && (detail.part_name === 'Rangka Panjang' || detail.part_name === 'Rangka Lebar')) {
                                        detail.material_kode = selectedValue;
                                    }
                                });
                            }
                        }
                        
                        // Sync rules for Penutup section (Combine all into 1)
                        if (changedPart.section === 'Penutup') {
                            activeDetails.forEach((detail, idx) => {
                                if (detail.section === 'Penutup') {
                                    detail.material_kode = selectedValue;
                                    // Also update DOM element if it exists
                                    let selectEl = document.querySelector('.table-material-select[data-index="'+idx+'"]');
                                    if (selectEl) {
                                        selectEl.value = selectedValue;
                                    }
                                }
                            });
                        }

                        runSimulation(false);
                    }
                }
            });
            
            // Link Arah Pemasangan Penyangga and Penutup in Modal Configuration (One-way sync from Penyangga to Penutup)
            const atasPenyanggaArah = document.querySelector('[name="atas_penyangga_arah"]');
            const atasPenutupArah = document.querySelector('[name="atas_penutup_arah"]');
            const bawahPenyanggaArah = document.querySelector('[name="bawah_penyangga_arah"]');
            const bawahPenutupArah = document.querySelector('[name="bawah_penutup_arah"]');

            const syncOppositeArah = (sourceEl, targetEl) => {
                if (sourceEl && targetEl) {
                    sourceEl.addEventListener('change', function() {
                        const oppositeValue = this.value === 'Horizontal' ? 'Vertikal' : (this.value === 'Vertikal' ? 'Horizontal' : '-');
                        if (oppositeValue !== '-' && targetEl.value !== oppositeValue) {
                            targetEl.value = oppositeValue;
                            targetEl.dispatchEvent(new Event('change'));
                        }
                    });
                }
            };

            // Sync Penyangga -> Penutup
            syncOppositeArah(atasPenyanggaArah, atasPenutupArah);
            syncOppositeArah(bawahPenyanggaArah, bawahPenutupArah);

            window.printWith3DImage = function() {
                const canvas = document.getElementById('crate-canvas');
                if (!canvas) return;
                
                // Toggles
                const chkRangkaAtas = document.getElementById('vis-toggle-rangka-atas') || { checked: false, dispatchEvent: function(){} };
                const chkPenyanggaAtas = document.getElementById('vis-toggle-penyangga-atas');
                const chkPenutupAtas = document.getElementById('vis-toggle-penutup-atas');

                const chkPenyanggaBawah = document.getElementById('vis-toggle-penyangga-bawah');
                const chkPenutupBawah = document.getElementById('vis-toggle-penutup-bawah');
                const chkKakiBalok = document.getElementById('vis-toggle-kakibalok');
                
                if (!chkPenyanggaAtas || !chkPenutupAtas || !chkPenyanggaBawah || !chkPenutupBawah) {
                    submitPrintForm(canvas.toDataURL('image/png'), '', '', '', '', '', '', '', '', '', '', '');
                    return;
                }
                
                // Save original states
                const wasRangkaAtas = chkRangkaAtas.checked;
                const wasPenyanggaAtas = chkPenyanggaAtas.checked;
                const wasPenutupAtas = chkPenutupAtas.checked;

                const wasPenyanggaBawah = chkPenyanggaBawah.checked;
                const wasPenutupBawah = chkPenutupBawah.checked;
                const wasKakiBalok = chkKakiBalok ? chkKakiBalok.checked : true;
                
                const waitFrame = () => new Promise(resolve => requestAnimationFrame(() => requestAnimationFrame(resolve)));
                
                async function captureAll() {
                    const origZoom = camera.zoom;
                    camera.zoom = 1.15; // Zoom in sedikit (dikurangi dari 1.6) agar objek terlihat proporsional dan tidak terpotong
                    camera.updateProjectionMatrix();
                    
                    if (typeof grid !== 'undefined' && grid) grid.visible = false;
                    if (typeof dimensionGroup !== 'undefined' && dimensionGroup) dimensionGroup.visible = false;
                    dimensionsVisible = false;
                    
                    const atasPenutupTipe = document.querySelector('[name="atas_penutup_tipe"]')?.value || 'Tanpa Penutup';
                    const isHasAtasPenutup = (atasPenutupTipe !== 'Tanpa Penutup' && atasPenutupTipe !== 'Tidak makai penutup' && atasPenutupTipe !== 'Tidak Pakai Papan' && atasPenutupTipe !== '') ? '1' : '0';
                    const bawahPenutupTipe = document.querySelector('[name="bawah_penutup_tipe"]')?.value || 'Tanpa Penutup';
                    const isHasBawahPenutup = (bawahPenutupTipe !== 'Tanpa Penutup' && bawahPenutupTipe !== 'Tidak makai penutup' && bawahPenutupTipe !== 'Tidak Pakai Papan' && bawahPenutupTipe !== '') ? '1' : '0';
                    
                    const h_val = parseFloat(document.querySelector('input[name="height"]').value) || 0;
                    const t_m = h_val / 1000;
                    const l_val = parseFloat(document.querySelector('input[name="length"]').value) || 0;
                    const w_val = parseFloat(document.querySelector('input[name="width"]').value) || 0;
                    const maxDim = Math.max(l_val/1000, w_val/1000, t_m) || 1;
                    
                    // The object expands by 0.6 meters per side (1.2m total). We add 1.6 to maxDim for safe padding.
                    const maxDimExpanded = maxDim + 1.6; 
                    const d = maxDimExpanded * 2.15 + 1.2;
                    
                    // Manually frame the expanded bounding box
                    camera.position.set(d * 0.86, d * 0.66 * 0.65, d * 0.86); // 0.65 to lower the angle for better Kaki Balok view
                    controls.target.set(0, (t_m / 2) + 0.1, 0); 
                    
                    camera.zoom = 1.0; 
                    camera.near = 0.01;
                    camera.far = Math.max(100, d * 20);
                    camera.updateProjectionMatrix();
                    controls.update();

                    // 1-8. SEQUENTIAL CAPTURE
                    chkRangkaAtas.checked = true; chkPenyanggaAtas.checked = true; chkPenutupAtas.checked = true;
                    chkPenyanggaBawah.checked = true; chkPenutupBawah.checked = true;
                    if (chkKakiBalok) chkKakiBalok.checked = true;

                    const stepImages = [];
                    
                    for (let step = 1; step <= 8; step++) {
                        window.printSequenceStep = step;
                        
                        // Dynamic framing for base steps
                        if (step === 1 || step === 2) {
                            const maxBaseDim = Math.max(l_val/1000, w_val/1000) || 1;
                            const effectiveBaseDim = Math.max(maxBaseDim, 1.2); 
                            const dBase = (effectiveBaseDim + 1.6) * 2.15 + 1.2;
                            const optimalBaseZoom = Math.max(1.0, d / dBase);
                            
                            controls.target.set(0, -0.4, 0); // Aim at the bottom components which are expanded downwards
                            camera.zoom = optimalBaseZoom * 1.2; // Add a little extra 1.2x boost to fill the frame
                        } else if (step === 8) {
                            controls.target.set(0, (t_m / 2) + 0.3, 0); 
                            camera.zoom = 0.9;
                        } else {
                            controls.target.set(0, (t_m / 2) + 0.1, 0); 
                            camera.zoom = 1.0;
                        }
                        camera.updateProjectionMatrix();
                        controls.update();

                        if (typeof drawCrate === 'function') drawCrate(); else chkRangkaAtas.dispatchEvent(new Event('change'));
                        
                        await waitFrame();
                        stepImages.push(canvas.toDataURL('image/png'));
                    }
                    
                    // Capture Full Exploded (Step 9)
                    window.printSequenceStep = 9;
                    controls.target.set(0, (t_m / 2) - 0.1, 0);
                    const optimalZoom = (typeof d !== 'undefined') ? Math.max(1.0, d / ((Math.max((l_val||1)/1000, (w_val||1)/1000) + 1.6) * 2.15 + 1.2)) : 1.0;
                    camera.zoom = optimalZoom * 1.1; // Zoom in since base is no longer exploded vertically
                    camera.updateProjectionMatrix();
                    controls.update();
                    if (typeof drawCrate === 'function') drawCrate(); else chkRangkaAtas.dispatchEvent(new Event('change'));
                    await waitFrame();
                    const imgFullExploded = canvas.toDataURL('image/png');

                    // Capture Full Assembled (Step 0)
                    window.printSequenceStep = 0;
                    controls.target.set(0, (t_m / 2) + 0.1, 0);
                    camera.zoom = optimalZoom * 1.3;
                    camera.updateProjectionMatrix();
                    controls.update();
                    if (typeof drawCrate === 'function') drawCrate(); else chkRangkaAtas.dispatchEvent(new Event('change'));
                    await waitFrame();
                    const imgFull = canvas.toDataURL('image/png');
                    
                    // Cleanup sequence flag
                    window.printSequenceStep = 0;
                    
                    // Restore original state
                    chkRangkaAtas.checked = wasRangkaAtas;
                    chkPenyanggaAtas.checked = wasPenyanggaAtas;
                    chkPenutupAtas.checked = wasPenutupAtas;

                    chkPenyanggaBawah.checked = wasPenyanggaBawah;
                    chkPenutupBawah.checked = wasPenutupBawah;
                    if (chkKakiBalok) chkKakiBalok.checked = wasKakiBalok;
                    chkRangkaAtas.dispatchEvent(new Event('change'));
                    
                    // Restore camera
                    if (typeof grid !== 'undefined' && grid) grid.visible = true;
                    if (typeof dimensionGroup !== 'undefined' && dimensionGroup) dimensionGroup.visible = true;
                    dimensionsVisible = true;
                    camera.zoom = origZoom;
                    camera.updateProjectionMatrix();
                    setCameraView(currentView);
                    await waitFrame();
                    // Cek ketersediaan penutup
                    
                    // --- 8. Material Visualizer Snapshot ---
                    let imgMaterials = '';
                    if (typeof window.matRenderer !== 'undefined' && typeof window.matScene !== 'undefined' && typeof window.matCamera !== 'undefined') {
                            const oldCamX = typeof matCamX !== 'undefined' ? matCamX : 0;
                            const oldCamZ = window.matCamera.position.z;
                            const oldFrustum = window.matFrustumSize || 2.5;
                            const totalW = window.matTotalX || 10;
                            const totalZ = window.matTotalZ || 0; // negative value
                            
                            matCamX = totalW / 2;
                            const centerZ = totalZ / 2;
                            
                            const matContainer = document.getElementById('material-sorting-container');
                            if (matContainer) {
                                const aspect = matContainer.clientWidth / matContainer.clientHeight;
                                
                                // totalW must fit in frustumWidth = frustumSize * aspect
                                // depth must fit in frustumHeight = frustumSize
                                let neededFrustumX = (totalW + 2) / aspect;
                                let neededFrustumZ = Math.abs(totalZ) + 4; // Add padding for depth
                                window.matFrustumSize = Math.max(oldFrustum, neededFrustumX, neededFrustumZ);
                                
                                window.matCamera.left = window.matFrustumSize * aspect / -2;
                                window.matCamera.right = window.matFrustumSize * aspect / 2;
                                window.matCamera.top = window.matFrustumSize / 2;
                                window.matCamera.bottom = window.matFrustumSize / -2;
                                window.matCamera.position.x = matCamX;
                                window.matCamera.position.z = centerZ + 4.5;
                                window.matCamera.updateProjectionMatrix();
                                
                                window.matRenderer.render(window.matScene, window.matCamera);
                                imgMaterials = window.matRenderer.domElement.toDataURL('image/png');
                                
                                // Restore
                                matCamX = oldCamX;
                                window.matFrustumSize = oldFrustum;
                                window.matCamera.left = window.matFrustumSize * aspect / -2;
                                window.matCamera.right = window.matFrustumSize * aspect / 2;
                                window.matCamera.top = window.matFrustumSize / 2;
                                window.matCamera.bottom = window.matFrustumSize / -2;
                                window.matCamera.position.x = matCamX;
                                window.matCamera.position.z = oldCamZ;
                                window.matCamera.updateProjectionMatrix();
                        }
                    }
                    
                    submitPrintForm(stepImages[0], stepImages[1], stepImages[2], stepImages[3], stepImages[4], stepImages[5], stepImages[6], stepImages[7], imgFullExploded, imgFull, imgMaterials, isHasAtasPenutup, isHasBawahPenutup);
                }
                
                function submitPrintForm(img1, img2, img3, img4, img5, img6, img7, img8, imgFullExploded, imgFull, imgMaterials, hasAtas, hasBawah) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = "{{ route('packaging.calculations.print', $calculation->id ?? 0) }}";
                    form.target = '_blank';
                    
                    const csrfInput = document.createElement('input');
                    csrfInput.type = 'hidden'; csrfInput.name = '_token'; csrfInput.value = '{{ csrf_token() }}';
                    form.appendChild(csrfInput);
                    
                    const i1 = document.createElement('input'); i1.type = 'hidden'; i1.name = 'img_step_1'; i1.value = img1; form.appendChild(i1);
                    const i2 = document.createElement('input'); i2.type = 'hidden'; i2.name = 'img_step_2'; i2.value = img2; form.appendChild(i2);
                    const i3 = document.createElement('input'); i3.type = 'hidden'; i3.name = 'img_step_3'; i3.value = img3; form.appendChild(i3);
                    const i4 = document.createElement('input'); i4.type = 'hidden'; i4.name = 'img_step_4'; i4.value = img4; form.appendChild(i4);
                    
                    const i5 = document.createElement('input'); i5.type = 'hidden'; i5.name = 'img_step_5'; i5.value = img5; form.appendChild(i5);
                    const i6 = document.createElement('input'); i6.type = 'hidden'; i6.name = 'img_step_6'; i6.value = img6; form.appendChild(i6);
                    const i7 = document.createElement('input'); i7.type = 'hidden'; i7.name = 'img_step_7'; i7.value = img7; form.appendChild(i7);
                    const i8 = document.createElement('input'); i8.type = 'hidden'; i8.name = 'img_step_8'; i8.value = img8; form.appendChild(i8);
                    
                    const i9 = document.createElement('input'); i9.type = 'hidden'; i9.name = 'img_full_exploded'; i9.value = imgFullExploded; form.appendChild(i9);
                    const i10 = document.createElement('input'); i10.type = 'hidden'; i10.name = 'img_full'; i10.value = imgFull; form.appendChild(i10);
                    
                    const imat = document.createElement('input'); imat.type = 'hidden'; imat.name = 'crate_image_materials'; imat.value = imgMaterials; form.appendChild(imat);
                    
                    const ia = document.createElement('input'); ia.type = 'hidden'; ia.name = 'has_penutup_atas'; ia.value = hasAtas; form.appendChild(ia);
                    const ib = document.createElement('input'); ib.type = 'hidden'; ib.name = 'has_penutup_bawah'; ib.value = hasBawah; form.appendChild(ib);
                    
                    document.body.appendChild(form);
                    form.submit();
                    document.body.removeChild(form);
                }
                             captureAll();
            };

            const formEdit = document.getElementById('form-edit-config');

            // --- Visual Toggle for Include / Not Include ---
            function handleIncludeToggle(select) {
                let isIncluded = select.value === "1" || (select.name.includes('penutup_tipe') && select.value !== "Tanpa Penutup" && select.value !== "0");
                let row = select.closest('.group-item');
                if (row) {
                    let arah = row.querySelector('.master-arah');
                    let mat = row.querySelector('.master-material');
                    if (arah) {
                        arah.style.visibility = isIncluded ? 'visible' : 'hidden';
                        arah.style.opacity = isIncluded ? '1' : '0';
                    }
                    if (mat) {
                        mat.style.visibility = isIncluded ? 'visible' : 'hidden';
                        mat.style.opacity = isIncluded ? '1' : '0';
                    }
                }
            }

            // (Fungsi enforceAtasDependencies dihapus)
            function toggleEdit(enabled) {
                isEditModeActive = enabled;
                if (enabled) {
                    btnEdit.classList.add('d-none');
                    btnEdit.classList.remove('d-flex');
                    
                    btnCancel.classList.remove('d-none');
                    btnCancel.classList.add('d-flex');
                    
                    btnSave.classList.remove('d-none');
                    btnSave.classList.add('d-flex');
                    
                    const elements = formEdit.querySelectorAll('input, select');
                    elements.forEach(el => {
                        el.removeAttribute('readonly');
                        el.removeAttribute('disabled');
                    });
                } else {
                    btnEdit.classList.remove('d-none');
                    btnEdit.classList.add('d-flex');
                    
                    btnCancel.classList.add('d-none');
                    btnCancel.classList.remove('d-flex');
                    
                    btnSave.classList.add('d-none');
                    btnSave.classList.remove('d-flex');
                    
                    const elements = formEdit.querySelectorAll('input, select');
                    elements.forEach(el => {
                        if(el.tagName === 'SELECT' || el.type === 'checkbox' || el.type === 'radio') {
                            el.setAttribute('disabled', 'disabled');
                        } else {
                            el.setAttribute('readonly', 'readonly');
                        }
                    });
                }
                
                // enforceAtasDependencies() dihapus
                // Refresh penutup dropdown states (enabled/disabled and pointer-events) based on isEditModeActive
                if (typeof updatePenutupDropdown === 'function') {
                    if (bawahPenutupTipe) updatePenutupDropdown(bawahPenutupTipe, bawahPenutupSelect, document.querySelector('[name="bawah_penutup_arah"]'));
                    if (atasPenutupTipe) updatePenutupDropdown(atasPenutupTipe, atasPenutupSelect, document.querySelector('[name="atas_penutup_arah"]'));
                }
                
                if (typeof runSimulation === 'function') {
                    runSimulation();
                }
            }

            // Form konfigurasi tetap menjadi induk proses.
            // Tombol edit boleh tidak ada karena pada desain baru konfigurasi ditampilkan sebagai value/read-only.
            if (formEdit) {
                let initialValues = {};
                let initialDetails = []; // Added backup for activeDetails
                
                function storeInitialValues() {
                    initialValues = {};
                    const elements = formEdit.querySelectorAll('input, select');
                    elements.forEach(el => {
                        if(el.name) {
                            initialValues[el.name] = el.value;
                        }
                    });
                    if (typeof activeDetails !== 'undefined') {
                        initialDetails = JSON.parse(JSON.stringify(activeDetails));
                    }
                }
                
                function restoreInitialValues() {
                    const elements = formEdit.querySelectorAll('input, select');
                    elements.forEach(el => {
                        if(el.name && initialValues.hasOwnProperty(el.name)) {
                            if (el.value !== initialValues[el.name]) {
                                el.value = initialValues[el.name];
                                el.dispatchEvent(new Event('change'));
                            }
                        }
                    });
                    if (initialDetails && initialDetails.length > 0) {
                        activeDetails = JSON.parse(JSON.stringify(initialDetails));
                    } else {
                        activeDetails = [];
                    }
                }
                
                // Pada desain lama ketiga tombol selalu tersedia. Pada desain baru tombol-tombol
                // tersebut boleh dihapus, sehingga listener harus dipasang secara aman.
                if (isNewRecord && btnEdit && btnCancel && btnSave) {
                    storeInitialValues();
                    toggleEdit(true);
                }
                
                if (btnEdit) {
                    btnEdit.addEventListener('click', function() {
                        storeInitialValues();
                        toggleEdit(true);
                    });
                }
                
                if (btnCancel) {
                    btnCancel.addEventListener('click', function() {
                        window.location.reload();
                    });
                }
                
                // --- Dynamic Penutup Options ---
                const masterPapan = @json($materials->where('kategori', 'MASTER PAPAN')->values());
                const masterTripleks = @json($materials->whereIn('component', ['Triplek', 'Papan'])->values());
                
                
                function updatePenutupDropdown(tipeSelect, materialSelect, arahSelect) {
                    if (!tipeSelect || !materialSelect) return;
                    
                    const type = tipeSelect.value || '';
                    let optionsData = [];
                    
                    const isTripleks = type.toLowerCase().includes('triplex') || type.toLowerCase().includes('tripleks');
                    const isPapan = type.toLowerCase().includes('papan');
                    
                    if(isTripleks) {
                        optionsData = masterTripleks;
                    } else if(isPapan) {
                        optionsData = masterPapan;
                    }
                    
                    // Handle Arah Pemasangan state
                    if (arahSelect) {
                        if (isTripleks || !isPapan) {
                            arahSelect.innerHTML = '<option value="-">-</option>';
                            arahSelect.value = '-';
                            arahSelect.setAttribute('disabled', 'disabled');
                            arahSelect.style.pointerEvents = 'none';
                            arahSelect.classList.add('fixed-status-select');
                        } else {
                            if (arahSelect.innerHTML.includes('-')) {
                                arahSelect.innerHTML = '<option value="Horizontal">Horizontal</option><option value="Vertikal">Vertikal</option>';
                                arahSelect.value = 'Horizontal';
                            }
                            arahSelect.classList.remove('fixed-status-select');
                            if (typeof isEditModeActive !== 'undefined' && isEditModeActive) {
                                arahSelect.removeAttribute('disabled');
                                arahSelect.style.pointerEvents = 'auto';
                            } else {
                                arahSelect.setAttribute('disabled', 'disabled');
                                arahSelect.style.pointerEvents = 'none';
                            }
                        }
                    }

                    const currentMat = materialSelect.value;
                    let html = optionsData.map(mat => `<option value="${mat.kode}">${mat.kode} (${mat.tebal}x${mat.lebar})</option>`).join('');
                    if(html !== '') {
                        html = '<option value="" disabled selected>-</option>' + html;
                    } else {
                        html = '<option value="">-</option>';
                    }
                    
                    materialSelect.innerHTML = html;
                    
                    if(optionsData.find(m => m.kode === currentMat)) {
                        materialSelect.value = currentMat;
                    }
                    
                    applyShortLabelToSelect(materialSelect, true);
                }
                
                if (bawahPenutupTipe) bawahPenutupTipe.addEventListener('change', () => updatePenutupDropdown(bawahPenutupTipe, bawahPenutupSelect, document.querySelector('[name="bawah_penutup_arah"]')));
                if (atasPenutupTipe) atasPenutupTipe.addEventListener('change', () => updatePenutupDropdown(atasPenutupTipe, atasPenutupSelect, document.querySelector('[name="atas_penutup_arah"]')));
                
                // --- Short Code Label Trick for Selects ---
                function applyShortLabelToSelect(select, forceReset = false) {
                    Array.from(select.options).forEach(opt => {
                        if (!opt.dataset.fullText || forceReset) {
                            opt.dataset.fullText = opt.text;
                            opt.dataset.shortText = opt.value;
                        }
                        
                        if (opt.selected) {
                            opt.text = opt.dataset.shortText;
                        } else {
                            opt.text = opt.dataset.fullText;
                        }
                    });
                }
                
                function handleSelectDropdown(select) {
                    select.addEventListener('mousedown', function() {
                        Array.from(this.options).forEach(opt => {
                            if (opt.dataset.fullText) opt.text = opt.dataset.fullText;
                        });
                    });
                    
                    select.addEventListener('change', function() {
                        applyShortLabelToSelect(this);
                    });
                    
                    select.addEventListener('blur', function() {
                        applyShortLabelToSelect(this);
                    });
                    
                    applyShortLabelToSelect(select);
                }
                
                document.querySelectorAll('.master-material').forEach(handleSelectDropdown);
                if (bawahPenutupTipe) updatePenutupDropdown(bawahPenutupTipe, bawahPenutupSelect, document.querySelector('[name="bawah_penutup_arah"]'));
                if (atasPenutupTipe) updatePenutupDropdown(atasPenutupTipe, atasPenutupSelect, document.querySelector('[name="atas_penutup_arah"]'));

                // --- Initialize Dropdowns from Active Details (Load Saved Database Values) ---
                function initDropdownsFromActiveDetails() {
                    if (typeof activeDetails === 'undefined' || !activeDetails || activeDetails.length === 0) return;

                    const setSelectVal = (name, val) => {
                        let el = document.querySelector(`[name="${name}"]`);
                        if (el) {
                            el.value = val;
                            // Trigger dynamic visibility logic (hide/show direction and material)
                            if (el.classList.contains('master-include')) {
                                handleIncludeToggle(el);
                            }
                            if (typeof applyShortLabelToSelect === 'function' && el.classList.contains('master-material')) {
                                applyShortLabelToSelect(el);
                            }
                        }
                    };

                    // 1. Rangka Atas (Dihapus)


                    // 2. Penyangga Atas (Maps to section: Penyangga, part_name: Atas)
                    let pAtas = activeDetails.find(d => d.section === 'Penyangga' && d.part_name === 'Atas');
                    if (pAtas) {
                        setSelectVal('atas_penyangga_include', pAtas.total_quantity > 0 ? "1" : "0");
                        setSelectVal('atas_penyangga_arah', pAtas.direction);
                        setSelectVal('atas_penyangga_material', pAtas.material_kode);
                    } else {
                        setSelectVal('atas_penyangga_include', "0");
                    }

                    // 3. Penutup Atas (Maps to section: Penutup, part_name: Atas)
                    let ptAtas = activeDetails.find(d => d.section === 'Penutup' && d.part_name === 'Atas');
                    if (ptAtas) {
                        setSelectVal('atas_penutup_tipe', ptAtas.tipe_penutup || (ptAtas.total_quantity > 0 ? "Papan Full" : "Tanpa Penutup"));
                        if (typeof updatePenutupDropdown === 'function') {
                            updatePenutupDropdown(atasPenutupTipe, atasPenutupSelect, document.querySelector('[name="atas_penutup_arah"]'));
                        }
                        setSelectVal('atas_penutup_arah', ptAtas.direction);
                        setSelectVal('atas_penutup_material', ptAtas.material_kode);
                    } else {
                        setSelectVal('atas_penutup_tipe', "Tanpa Penutup");
                    }

                    // 4. Rangka Bawah (Maps to section: Bawah, part_name: Rangka Panjang)

                    // 5. Penyangga Bawah (Maps to section: Bawah, part_name: Penyangga)
                    let pBawah = activeDetails.find(d => d.section === 'Bawah' && d.part_name === 'Penyangga');
                    if (pBawah) {
                        setSelectVal('bawah_penyangga_material', pBawah.material_kode);
                    }

                    // 6. Penutup Bawah (Maps to section: Bawah, part_name: Penutup)
                    let ptBawah = activeDetails.find(d => d.section === 'Bawah' && d.part_name === 'Penutup');
                    if (ptBawah) {
                        setSelectVal('bawah_penutup_tipe', ptBawah.tipe_penutup || (ptBawah.total_quantity > 0 ? "Papan Full" : "Tanpa Penutup"));
                        if (typeof updatePenutupDropdown === 'function') {
                            updatePenutupDropdown(bawahPenutupTipe, bawahPenutupSelect, document.querySelector('[name="bawah_penutup_arah"]'));
                        }
                        setSelectVal('bawah_penutup_arah', ptBawah.direction);
                        setSelectVal('bawah_penutup_material', ptBawah.material_kode);
                    } else {
                        setSelectVal('bawah_penutup_tipe', "Tanpa Penutup");
                    }

                    // 7. Kaki Balok (Maps to section: Bawah, part_name: Kaki Balok)
                    let addBalok = activeDetails.find(d => d.section === 'Bawah' && (d.part_name === 'Kaki Balok' || d.part_name === 'Additional Balok'));
                    if (addBalok) {
                        setSelectVal('include_pallet_base', addBalok.total_quantity > 0 ? "1" : "0");
                        setSelectVal('bawah_kakibalok_arah', addBalok.direction);
                        setSelectVal('bawah_kakibalok_material', addBalok.material_kode);
                    } else {
                        setSelectVal('include_pallet_base', "0");
                    }
                }

                // initDropdownsFromActiveDetails(); // Disabled: prefer explicit columns in PHP

                // Recalculate and redraw using populated database values
                if (typeof calculateNails === 'function') {
                    calculateNails();
                }
                if (typeof drawCrate === 'function') {
                    drawCrate(true);
                }
                

                document.querySelectorAll('.matrix-select').forEach(select => {
                    select.addEventListener('change', function() {
                        if (this.classList.contains('master-include')) {
                            handleIncludeToggle(this);
                        }
                        // (Pengecekan atas_rangka_include dihapus)

                        // Recommended direction presets when user enables/includes sections in edit mode
                        if (typeof isEditModeActive !== 'undefined' && isEditModeActive) {
                            if (this.name === 'bawah_penyangga_include' && this.value === '1') {
                                const target = document.querySelector('[name="bawah_penyangga_arah"]');
                                if (target) { target.value = 'Horizontal'; target.dispatchEvent(new Event('change')); }
                            }
                            else if (this.name === 'bawah_penutup_tipe' && this.value !== 'Tanpa Penutup' && this.value !== '0' && this.value !== '') {
                                const target = document.querySelector('[name="bawah_penutup_arah"]');
                                if (target) { target.value = 'Vertikal'; target.dispatchEvent(new Event('change')); }
                            }
                            else if (this.name === 'include_pallet_base' && this.value === '1') {
                                const target = document.querySelector('[name="bawah_kakibalok_arah"]');
                                if (target) { target.value = 'Vertikal'; target.dispatchEvent(new Event('change')); }
                            }
                            else if (this.name === 'atas_penyangga_include' && this.value === '1') {
                                const target = document.querySelector('[name="atas_penyangga_arah"]');
                                if (target) { target.value = 'Vertikal'; target.dispatchEvent(new Event('change')); }
                            }
                            else if (this.name === 'atas_penutup_tipe' && this.value !== 'Tanpa Penutup' && this.value !== '0' && this.value !== '') {
                                const target = document.querySelector('[name="atas_penutup_arah"]');
                                if (target) { target.value = 'Horizontal'; target.dispatchEvent(new Event('change')); }
                            }
                        }
                        
                        if (typeof runSimulation === 'function') {
                            runSimulation();
                        }
                    });
                    // init
                    if (select.classList.contains('master-include')) {
                        handleIncludeToggle(select);
                    }
                });
                
                // enforceAtasDependencies() dihapus
                // --- Map Details on Submit ---
                formEdit.addEventListener('submit', function(e) {
                    // Remove disabled attributes so all selects are submitted
                    formEdit.querySelectorAll('select[disabled]').forEach(el => el.removeAttribute('disabled'));
                    
                    document.querySelectorAll('.generated-detail-input').forEach(el => el.remove());

                    let detailIndex = 0;
                    
                    function addDetail(section, part, include, arah, material, tipe_penutup = null) {
                        const base = `details[${detailIndex}]`;
                        const inputs = {
                            'section': section,
                            'part_name': part,
                            'include': include,
                            'direction': arah,
                            'material_kode': material
                        };
                        
                        if (tipe_penutup) {
                            inputs['tipe_penutup'] = tipe_penutup;
                        }
                        
                        for (const [key, value] of Object.entries(inputs)) {
                            const input = document.createElement('input');
                            input.type = 'hidden';
                            input.name = `${base}[${key}]`;
                            input.value = value;
                            input.className = 'generated-detail-input';
                            formEdit.appendChild(input);
                        }
                        detailIndex++;
                    }

                    // Section 3: Bawah

                    
                    const b_p_inc = document.querySelector('[name="bawah_penyangga_include"]').value;
                    const b_p_arah = document.querySelector('[name="bawah_penyangga_arah"]').value;
                    const b_p_mat = document.querySelector('[name="bawah_penyangga_material"]').value;
                    addDetail('Bawah', 'Penyangga', b_p_inc, b_p_arah, b_p_mat);
                    
                    const b_pt_tipe = document.querySelector('[name="bawah_penutup_tipe"]').value;
                    const b_pt_inc = (b_pt_tipe === 'Tanpa Penutup' || b_pt_tipe === 'Tidak makai penutup') ? "0" : "1";
                    const b_pt_arah = document.querySelector('[name="bawah_penutup_arah"]').value;
                    const b_pt_mat = document.querySelector('[name="bawah_penutup_material"]').value;
                    addDetail('Bawah', 'Penutup', b_pt_inc, b_pt_arah, b_pt_mat, b_pt_tipe);
                    
                    const b_k_inc = document.querySelector('[name="include_pallet_base"]').value;
                    const b_k_arah = document.querySelector('[name="bawah_kakibalok_arah"]').value;
                    const b_k_mat = document.querySelector('[name="bawah_kakibalok_material"]').value;
                    addDetail('Bawah', 'Kaki Balok', b_k_inc, b_k_arah, b_k_mat);

                    // Section 4: Atas -> Maps to Rangka, Penyangga, Penutup
                    // (Rangka Atas dan Rangka Tinggi dihapus)

                    const a_p_inc = document.querySelector('[name="atas_penyangga_include"]').value;
                    const a_p_arah = document.querySelector('[name="atas_penyangga_arah"]').value;
                    const a_p_mat = document.querySelector('[name="atas_penyangga_material"]').value;
                    const penyanggaParts = ['Atas', 'Kanan', 'Kiri', 'Depan', 'Belakang'];
                    penyanggaParts.forEach(part => {
                        addDetail('Penyangga', part, a_p_inc, a_p_arah, a_p_mat);
                    });
                    
                    const a_pt_tipe = document.querySelector('[name="atas_penutup_tipe"]').value;
                    const a_pt_inc = (a_pt_tipe === 'Tanpa Penutup' || a_pt_tipe === 'Tidak makai penutup') ? "0" : "1";
                    const a_pt_arah = document.querySelector('[name="atas_penutup_arah"]').value;
                    const a_pt_mat = document.querySelector('[name="atas_penutup_material"]').value;
                    const penutupParts = ['Atas', 'Kanan', 'Kiri', 'Depan', 'Belakang'];
                    penutupParts.forEach(part => {
                        addDetail('Penutup', part, a_pt_inc, a_pt_arah, a_pt_mat, a_pt_tipe);
                    });
                });

                // --- Live Updater: 3D Visualizer & Kaki Balok ---
                const lengthInput = document.querySelector('input[name="length"]');
                const kakiBalokInput = document.querySelector('input[name="jumlah_kaki_balok"]');
                const allConfigInputs = document.querySelectorAll('#form-edit-config input, #form-edit-config select');

                allConfigInputs.forEach(input => {
                    input.addEventListener('input', function() {
                        // 1. Auto-Update Jumlah Kaki Balok based on Length
                        if (this.name === 'length' && kakiBalokInput) {
                            const pValue = parseFloat(this.value) || 0;
                            const incPallet = document.querySelector('[name="include_pallet_base"]')?.value;
                            const isIncluded = (incPallet === '1' || (typeof incPallet === 'string' && incPallet.toLowerCase() === 'ya'));
                            if (isIncluded) {
                                kakiBalokInput.value = Math.max(2, Math.floor(pValue / 800) + 1);
                            }
                        }

                        // 2. Auto-Update 3D Visualizer
                        if (typeof drawCrate === 'function') {
                            drawCrate(false); // redraw without resetting camera
                        }
                    });

                    // For select dropdowns that might use 'change' event
                    input.addEventListener('change', function() {
                        if (typeof drawCrate === 'function') {
                            drawCrate(false);
                        }
                    });
                });
                
                // --- Material Sorting Visualizer ---
                let matScene, matCamera, matCameraGroup, matRenderer, matGridHelper;
                let matContainer = document.getElementById('material-sorting-container');
                let matLabels = document.getElementById('material-labels-container');
                let matCamX = 0; // target X for scrolling
                let matMaxCamX = 0; // max X scroll
                let matMeshes = [];
                let matAnimationFrameId = null;
                let matResizeHandlerBound = false;
                let matToolbarBound = false;

                function showMaterialWebGLFallback(message = 'Visualisasi potongan material tidak dapat ditampilkan, tetapi data dan proses perhitungan tetap berjalan.') {
                    if (!matContainer) return;

                    matContainer.innerHTML = `
                        <div class="d-flex align-items-center justify-content-center h-100 w-100 p-4 text-center">
                            <div>
                                <span class="material-symbols-rounded text-warning mb-2" style="font-size:34px">view_in_ar</span>
                                <div class="fw-bold text-dark" style="font-size:13px">Visualisasi material tidak tersedia</div>
                                <div class="text-muted mt-1" style="font-size:11px;max-width:360px">${message}</div>
                            </div>
                        </div>`;
                }

                function initMaterialVisualizer() {
                    if (!matContainer) return false;

                    if (typeof THREE === 'undefined') {
                        showMaterialWebGLFallback('Three.js belum berhasil dimuat.');
                        return false;
                    }

                    if (!isWebGLAvailable()) {
                        showMaterialWebGLFallback('Browser tidak dapat membuat WebGL context untuk visualisasi material.');
                        return false;
                    }

                    if (
                        matRenderer &&
                        matRenderer.domElement &&
                        matContainer.contains(matRenderer.domElement) &&
                        !matRenderer.getContext?.().isContextLost?.()
                    ) {
                        return true;
                    }

                    if (matRenderer) {
                        disposeThreeRenderer(matRenderer);
                        matRenderer = null;
                    }

                    matContainer.querySelectorAll('canvas[data-material-webgl="true"]').forEach((oldCanvas) => oldCanvas.remove());

                    try {
                        matScene = new THREE.Scene();
                        matScene.background = new THREE.Color(packagingThemePalette().materialSceneBackground);

                        const containerWidth = Math.max(matContainer.clientWidth || 0, 320);
                        const containerHeight = Math.max(matContainer.clientHeight || 0, 300);
                        const aspect = containerWidth / containerHeight;
                        window.matFrustumSize = 2.5;

                        matCamera = new THREE.OrthographicCamera(
                            window.matFrustumSize * aspect / -2,
                            window.matFrustumSize * aspect / 2,
                            window.matFrustumSize / 2,
                            window.matFrustumSize / -2,
                            0.1,
                            100
                        );
                        matCamera.position.set(0, 1.5, 4.5);
                        matCamera.lookAt(0, 0.8, 0);

                        matCameraGroup = new THREE.Group();
                        matCameraGroup.add(matCamera);
                        matScene.add(matCameraGroup);

                        matRenderer = new THREE.WebGLRenderer({
                            antialias: false,
                            alpha: false,
                            preserveDrawingBuffer: false,
                            powerPreference: 'default',
                            failIfMajorPerformanceCaveat: false
                        });
                        matRenderer.domElement.dataset.materialWebgl = 'true';

                        window.matRenderer = matRenderer;
                        window.matScene = matScene;
                        window.matCamera = matCamera;

                        matRenderer.setPixelRatio(Math.min(window.devicePixelRatio || 1, 1.25));
                        matRenderer.setSize(containerWidth, containerHeight, false);
                        matRenderer.domElement.style.position = 'absolute';
                        matRenderer.domElement.style.inset = '0';
                        matRenderer.domElement.style.width = '100%';
                        matRenderer.domElement.style.height = '100%';
                        matRenderer.domElement.style.display = 'block';
                        matRenderer.shadowMap.enabled = false;
                        matContainer.appendChild(matRenderer.domElement);

                        matScene.add(new THREE.AmbientLight(0xffffff, 0.85));

                        const dirLight = new THREE.DirectionalLight(0xffffff, 0.65);
                        dirLight.position.set(5, 10, 7);
                        matScene.add(dirLight);

                        const materialTheme = packagingThemePalette();
                        matGridHelper = new THREE.GridHelper(
                            100,
                            100,
                            materialTheme.materialGridCenter,
                            materialTheme.materialGridLine
                        );
                        matGridHelper.position.y = 0;
                        matScene.add(matGridHelper);

                        const plane = new THREE.Mesh(
                            new THREE.PlaneGeometry(100, 100),
                            new THREE.ShadowMaterial({ opacity: 0.1 })
                        );
                        plane.rotation.x = -Math.PI / 2;
                        matScene.add(plane);

                        if (!matToolbarBound) {
                            document.getElementById('btn-mat-left')?.addEventListener('click', () => {
                                matCamX = Math.max(0, matCamX - 3);
                            });
                            document.getElementById('btn-mat-right')?.addEventListener('click', () => {
                                matCamX = Math.min(matMaxCamX, matCamX + 3);
                            });
                            matToolbarBound = true;
                        }

                        if (!matResizeHandlerBound) {
                            window.addEventListener('resize', resizeMaterialVisualizer);
                            matResizeHandlerBound = true;
                        }

                        if (matAnimationFrameId === null) {
                            animateMat();
                        }

                        return true;
                    } catch (error) {
                        console.error('Gagal menginisialisasi visualisasi potongan material:', error);

                        if (matRenderer) {
                            disposeThreeRenderer(matRenderer);
                            matRenderer = null;
                        }

                        showMaterialWebGLFallback(error?.message || 'WebGL context tidak dapat dibuat.');
                        return false;
                    }
                }

                function resizeMaterialVisualizer() {
                    if (!matContainer || !matCamera || !matRenderer) return;

                    const width = Math.max(matContainer.clientWidth || 0, 320);
                    const height = Math.max(matContainer.clientHeight || 0, 300);
                    const aspect = width / height;

                    matCamera.left = window.matFrustumSize * aspect / -2;
                    matCamera.right = window.matFrustumSize * aspect / 2;
                    matCamera.top = window.matFrustumSize / 2;
                    matCamera.bottom = window.matFrustumSize / -2;
                    matCamera.updateProjectionMatrix();
                    matRenderer.setSize(width, height, false);
                }

                function animateMat() {
                    if (!matRenderer || !matScene || !matCamera || !matCameraGroup) {
                        matAnimationFrameId = null;
                        return;
                    }

                    matAnimationFrameId = window.requestAnimationFrame(animateMat);
                    matCameraGroup.position.x += (matCamX - matCameraGroup.position.x) * 0.05;

                    const gl = matRenderer.getContext?.();
                    if (!gl?.isContextLost?.()) {
                        matRenderer.render(matScene, matCamera);
                        updateMatLabels();
                    }
                }

                function updateMatLabels() {
                    if (!matLabels || !matCamera) return;
                    matMeshes.forEach(item => {
                        if (item.labelEl) {
                            const vector = item.worldPos.clone().project(matCamera);
                            const x = (vector.x * 0.5 + 0.5) * matContainer.clientWidth;
                            const y = (-(vector.y * 0.5) + 0.5) * matContainer.clientHeight;
                            
                            // hide if behind camera or too far off-screen
                            if (vector.z > 1 || x < -50 || x > matContainer.clientWidth + 50) {
                                item.labelEl.style.display = 'none';
                            } else {
                                item.labelEl.style.display = 'block';
                                if (item.isDim) {
                                    if (item.align === 'v') {
                                        item.labelEl.style.transform = `translate(-100%, -50%) translate(${x - 5}px, ${y}px)`;
                                    } else if (item.align === 'h') {
                                        item.labelEl.style.transform = `translate(-50%, -100%) translate(${x}px, ${y - 5}px)`;
                                    } else {
                                        item.labelEl.style.transform = `translate(0, -50%) translate(${x + 5}px, ${y}px)`;
                                    }
                                } else {
                                    item.labelEl.style.transform = `translate(-50%, 0) translate(${x}px, ${y + 20}px)`;
                                }
                            }
                        }
                    });
                }

                window.drawMaterialStacks = function() {
                    if (!matScene || !Array.isArray(activeDetails)) return;

                    if (!matLabels) {
                        matLabels = document.getElementById('material-labels-container');
                    }
                    
                    // Clear old meshes (keep initial lights, grid, and plane)
                    while(matScene.children.length > 4){ 
                        matScene.remove(matScene.children[4]); 
                    }
                    if (matLabels) {
                        matLabels.innerHTML = '';
                    }
                    matMeshes = [];
                    matCamX = 0; // Reset scroll

                    const cardsContainer = document.getElementById('material-cards-container');
                    if (cardsContainer) cardsContainer.innerHTML = '';

                    // 1. Group by dimensions
                    const groups = {};
                    activeDetails.forEach(d => {
                        const rawQty = parseFloat(d.total_quantity) || parseFloat(d.quantity) || 0;
                        const qty = Math.ceil(rawQty);
                        if (!qty || qty <= 0) return;

                        const t = parseFloat(d.calculated_thickness) || 0;
                        const w = parseFloat(d.calculated_width) || 0;
                        const l = parseFloat(d.calculated_length) || 0;
                        const mat = d.material_wood_type || d.material_nama || d.material_kode || 'Kayu';
                        
                        if (t === 0 || w === 0 || l === 0) return; // Skip invalid geometries
                        
                        const key = `${t}x${w}x${l}_${mat}`;
                        if (!groups[key]) {
                            groups[key] = { t, w, l, mat, qty: 0 };
                        }
                        groups[key].qty += qty;
                    });

                    // Helper to get category order: Balok (1) -> Papan (2) -> Triplek (3) -> Lainnya (4)
                    function getCategoryOrder(matName) {
                        const matLower = (matName || '').toLowerCase();
                        if (matLower.includes('bk') || matLower.includes('balok')) return 1;
                        if (matLower.includes('pn') || matLower.includes('papan')) return 2;
                        if (matLower.includes('tr') || matLower.includes('triplek') || matLower.includes('panel')) return 3;
                        return 4;
                    }

                    // 2. Sort groups by category then by size (tinggi/length ascending)
                    const sortedGroups = Object.values(groups).sort((a, b) => {
                        const orderA = getCategoryOrder(a.mat);
                        const orderB = getCategoryOrder(b.mat);
                        if (orderA !== orderB) return orderA - orderB;
                        return a.l - b.l;
                    });

                    // Jangan biarkan area tampak blank ketika data detail belum memiliki dimensi/qty valid.
                    const previousEmptyState = document.getElementById('material-visual-empty-state');
                    if (previousEmptyState) previousEmptyState.remove();

                    if (sortedGroups.length === 0) {
                        const emptyState = document.createElement('div');
                        emptyState.id = 'material-visual-empty-state';
                        emptyState.className = 'position-absolute top-50 start-50 translate-middle text-center text-muted';
                        emptyState.style.zIndex = '6';
                        emptyState.style.pointerEvents = 'none';
                        emptyState.innerHTML = `
                            <span class="material-symbols-rounded d-block mb-2" style="font-size: 38px; opacity: .55;">view_in_ar_off</span>
                            <div class="fw-bold text-dark">Data potongan belum dapat divisualisasikan</div>
                            <div style="font-size: 12px; max-width: 360px;">
                                Pastikan setiap detail memiliki calculated_width, calculated_length,
                                calculated_thickness dan total_quantity lebih dari 0.
                            </div>
                        `;
                        matContainer.appendChild(emptyState);
                        return;
                    }

                    if (sortedGroups.length === 0) {
                        if (matLabels) {
                            const emptyState = document.createElement('div');
                            emptyState.className = 'position-absolute top-50 start-50 translate-middle text-center text-muted';
                            emptyState.innerHTML = `
                                <span class="material-symbols-rounded d-block mb-2" style="font-size: 36px;">inventory_2</span>
                                <div class="fw-bold">Data potongan material belum tersedia</div>
                                <div style="font-size: 11px;">Pastikan detail material memiliki panjang, lebar, tebal, dan quantity.</div>
                            `;
                            matLabels.appendChild(emptyState);
                        }
                        window.matTotalX = 0;
                        window.matTotalZ = 0;
                        matMaxCamX = 0;
                        return;
                    }

                    // Helper to draw dimension lines
                    function drawDim(p1, p2, text, align) {
                        const points = [];
                        points.push(p1);
                        points.push(p2);
                        
                        const tickSize = 0.02;
                        // Add tick marks
                        if (align === 'v') { // vertical line, horizontal ticks
                            points.push(new THREE.Vector3(p2.x - tickSize, p2.y, p2.z));
                            points.push(new THREE.Vector3(p2.x + tickSize, p2.y, p2.z));
                            points.push(p2);
                            points.push(p1);
                            points.push(new THREE.Vector3(p1.x - tickSize, p1.y, p1.z));
                            points.push(new THREE.Vector3(p1.x + tickSize, p1.y, p1.z));
                        } else if (align === 'h') { // horizontal line, vertical ticks
                            points.push(new THREE.Vector3(p2.x, p2.y - tickSize, p2.z));
                            points.push(new THREE.Vector3(p2.x, p2.y + tickSize, p2.z));
                            points.push(p2);
                            points.push(p1);
                            points.push(new THREE.Vector3(p1.x, p1.y - tickSize, p1.z));
                            points.push(new THREE.Vector3(p1.x, p1.y + tickSize, p1.z));
                        } else if (align === 'd') { // depth line, vertical ticks
                            points.push(new THREE.Vector3(p2.x, p2.y + tickSize, p2.z));
                            points.push(new THREE.Vector3(p2.x, p2.y - tickSize, p2.z));
                            points.push(p2);
                            points.push(p1);
                            points.push(new THREE.Vector3(p1.x, p1.y + tickSize, p1.z));
                            points.push(new THREE.Vector3(p1.x, p1.y - tickSize, p1.z));
                        }
                    
                        const geo = new THREE.BufferGeometry().setFromPoints(points);
                        const labelTheme = packagingThemePalette();
                        const mat = new THREE.LineBasicMaterial({ color: labelTheme.dimensionLine });
                        const line = new THREE.Line(geo, mat);
                        matScene.add(line);
                    
                        const mid = new THREE.Vector3().addVectors(p1, p2).multiplyScalar(0.5);

                        const label = document.createElement('div');
                        label.className = 'fw-bold rounded shadow-sm';
                        label.style.position = 'absolute';
                        label.style.fontSize = '10px';
                        label.style.color = labelTheme.dimensionText;
                        label.style.background = labelTheme.labelBackground;
                        label.style.border = `1px solid ${labelTheme.labelBorder}`;
                        label.style.padding = '1px 4px';
                        label.innerHTML = text;
                        if (matLabels) {
                            matLabels.appendChild(label);
                        }
                        
                        matMeshes.push({
                            worldPos: mid,
                            labelEl: label,
                            isDim: true,
                            align: align
                        });
                    }

                    // Calculate dynamic scale based on the tallest object
                    let maxL_m = 0;
                    sortedGroups.forEach(group => {
                        const l_m = group.l / 1000;
                        if (l_m > maxL_m) maxL_m = l_m;
                    });
                    
                    // Base size 2.5, plus proportional padding based on tallest item
                    // Using 1.4 gives more vertical space to prevent labels getting cut off
                    window.matFrustumSize = Math.max(2.5, maxL_m * 1.4); 
                    
                    if (matCamera && matContainer) {
                        const aspect = matContainer.clientWidth / matContainer.clientHeight;
                        matCamera.left = window.matFrustumSize * aspect / -2;
                        matCamera.right = window.matFrustumSize * aspect / 2;
                        matCamera.top = window.matFrustumSize / 2;
                        matCamera.bottom = window.matFrustumSize / -2;
                        matCamera.updateProjectionMatrix();
                        
                        // Keep the camera perfectly horizontal to ensure a consistent flat 2D look
                        // We shift the camera down slightly (- 0.1 * maxL_m) so the wood appears higher,
                        // giving extra safe space at the bottom for the Qty labels.
                        const targetY = (maxL_m / 2) - (maxL_m * 0.1);
                        matCamera.position.set(0, targetY, 4.5);
                        matCamera.lookAt(0, targetY, 0);
                    }

                    // 3. Draw Stacks side by side, standing up
                    const minVis = Math.max(0.015, maxL_m * 0.015); 
                    const spacingGroup = Math.max(0.5, maxL_m * 0.08); // Jarak antar grup material disesuaikan agar pas
                    const spacingItem = Math.max(0.025, maxL_m * 0.02); // Jarak antar item agar tidak terlalu dempet
                    
                    let currentX = 0;
                    let maxX = 0;

                    sortedGroups.forEach(group => {
                        const t_m = Math.max(group.t / 1000, minVis);
                        const w_m = Math.max(group.w / 1000, minVis);
                        const l_m = group.l / 1000;

                        let meshColor = 0xc19a6b; // default: Kayu Papan
                        const order = getCategoryOrder(group.mat);
                        if (order === 1) meshColor = 0x5c4033; // Balok
                        else if (order === 3) meshColor = 0xf3c583; // Triplek

                        const matWood = new THREE.MeshStandardMaterial({ 
                            color: meshColor,
                            roughness: 0.9, 
                            metalness: 0.1 
                        });
                        
                        const geom = new THREE.BoxGeometry(w_m, l_m, t_m);
                        const edgesGeom = new THREE.EdgesGeometry(geom);
                        const lineMat = new THREE.LineBasicMaterial({ color: 0xa17a54, transparent: true, opacity: 0.5 }); 

                        const groupStartX = currentX;

                        // Draw Dimensions for the first item
                        const dimOffset = 0.06;
                        // P: Panjang (Height) -> left of the box
                        drawDim(
                            new THREE.Vector3(currentX - dimOffset, 0, 0),
                            new THREE.Vector3(currentX - dimOffset, l_m, 0),
                            `P:${group.l}`,
                            'v'
                        );
                        // L: Lebar (Width) -> top of the box
                        drawDim(
                            new THREE.Vector3(currentX, l_m + dimOffset, 0),
                            new THREE.Vector3(currentX + w_m, l_m + dimOffset, 0),
                            `L:${group.w}`,
                            'h'
                        );
                        // T: Tebal (Depth) -> right side of the box (depth axis)
                        drawDim(
                            new THREE.Vector3(currentX + w_m + dimOffset, 0, t_m / 2),
                            new THREE.Vector3(currentX + w_m + dimOffset, 0, -t_m / 2),
                            `T:${group.t}`,
                            'd'
                        );

                        // Draw boxes
                        for (let i = 0; i < group.qty; i++) {
                            const mesh = new THREE.Mesh(geom, matWood);
                            const edges = new THREE.LineSegments(edgesGeom, lineMat);
                            
                            const px = currentX + (w_m / 2); 
                            const py = l_m / 2; 
                            const pz = 0;
                            
                            mesh.position.set(px, py, pz);
                            edges.position.set(px, py, pz);
                            
                            mesh.castShadow = true;
                            mesh.receiveShadow = true;

                            matScene.add(mesh);
                            matScene.add(edges);
                            
                            currentX += w_m + spacingItem; // advance X
                            if (currentX > maxX) maxX = currentX;
                        }

                        // Add native WebGL Sprite for Group Name & Qty
                        const groupCenterX = groupStartX + (currentX - spacingItem - groupStartX) / 2;
                        const worldPos = new THREE.Vector3(groupCenterX, 0, 0);

                        const matNameLabel = document.createElement('div');
                        matNameLabel.className = 'bg-white rounded shadow-sm border border-light text-center px-2 py-1';
                        matNameLabel.style.position = 'absolute';
                        matNameLabel.style.whiteSpace = 'nowrap';
                        matNameLabel.innerHTML = `<div class="fw-bold text-dark" style="font-size: 11px;">${group.mat} - ${group.w} mm x ${group.l} mm</div><div class="badge bg-primary text-white mt-1" style="font-size: 10px;">Qty: ${group.qty}</div>`;
                        if (matLabels) {
                            matLabels.appendChild(matNameLabel);
                        }

                        matMeshes.push({
                            worldPos: worldPos,
                            labelEl: matNameLabel,
                            isDim: false
                        });

                        currentX += spacingGroup;
                        if (currentX > maxX) maxX = currentX;
                    });
                    
                    window.matTotalX = maxX;
                    window.matTotalZ = 0; 
                    matMaxCamX = Math.max(0, maxX - spacingGroup - 2); 
                };
                
                function refreshPackagingVisuals(resetCamera = false) {
                    try {
                        if (typeof drawCrate === 'function') {
                            drawCrate(resetCamera);
                        }

                        if (!matRenderer) {
                            initMaterialVisualizer();
                        }

                        if (typeof window.drawMaterialStacks === 'function') {
                            window.drawMaterialStacks();
                        }
                    } catch (error) {
                        console.error('Gagal memperbarui visualisasi packaging:', error);
                    }
                }

                window.refreshPackagingVisuals = refreshPackagingVisuals;

                window.addEventListener('packaging:configuration-updated', function() {
                    refreshPackagingVisuals(false);
                });


                function applyPackagingVisualTheme(redrawMaterial = true) {
                    const palette = packagingThemePalette();
                    syncPackagingThemeClass();

                    if (scene) {
                        scene.background = new THREE.Color(palette.sceneBackground);

                        if (grid) {
                            const wasVisible = grid.visible;
                            scene.remove(grid);
                            grid.geometry?.dispose?.();
                            if (Array.isArray(grid.material)) {
                                grid.material.forEach((material) => material.dispose?.());
                            } else {
                                grid.material?.dispose?.();
                            }

                            grid = new THREE.GridHelper(
                                40,
                                40,
                                palette.gridCenter,
                                palette.gridLine
                            );
                            grid.position.y = 0.002;
                            grid.material.transparent = true;
                            grid.material.opacity = 0.45;
                            grid.visible = wasVisible;
                            scene.add(grid);
                        }
                    }

                    if (matScene) {
                        matScene.background = new THREE.Color(
                            palette.materialSceneBackground
                        );

                        if (matGridHelper) {
                            matScene.remove(matGridHelper);
                            matGridHelper.geometry?.dispose?.();
                            if (Array.isArray(matGridHelper.material)) {
                                matGridHelper.material.forEach((material) => material.dispose?.());
                            } else {
                                matGridHelper.material?.dispose?.();
                            }

                            matGridHelper = new THREE.GridHelper(
                                100,
                                100,
                                palette.materialGridCenter,
                                palette.materialGridLine
                            );
                            matGridHelper.position.y = 0;
                            matScene.add(matGridHelper);
                        }
                    }

                    if (
                        redrawMaterial &&
                        typeof window.drawMaterialStacks === 'function'
                    ) {
                        window.drawMaterialStacks();
                    }
                }

                window.applyPackagingVisualTheme = applyPackagingVisualTheme;

                const packagingThemeObserver = new MutationObserver(() => {
                    applyPackagingVisualTheme(true);
                });

                packagingThemeObserver.observe(document.documentElement, {
                    attributes: true,
                    attributeFilter: ['class', 'data-bs-theme', 'data-theme'],
                });

                if (document.body) {
                    packagingThemeObserver.observe(document.body, {
                        attributes: true,
                        attributeFilter: ['class', 'data-bs-theme', 'data-theme'],
                    });
                }

                const packagingColorScheme = window.matchMedia?.(
                    '(prefers-color-scheme: dark)'
                );

                if (packagingColorScheme?.addEventListener) {
                    packagingColorScheme.addEventListener('change', () => {
                        applyPackagingVisualTheme(true);
                    });
                }

                applyPackagingVisualTheme(false);

                // Tunggu satu frame supaya ukuran container sudah final sebelum WebGL dibuat.
                window.requestAnimationFrame(function() {
                    try {
                        const initialized = initMaterialVisualizer();
                        if (initialized && typeof window.drawMaterialStacks === 'function') {
                            window.drawMaterialStacks();
                        }
                    } catch (error) {
                        console.error('Gagal menginisialisasi visualisasi potongan material:', error);
                    }
                });

                @if(request()->query('auto_open') == 'true' || request()->routeIs('packaging.calculations.create'))
                setTimeout(function() {
                    var productSetupModal = new bootstrap.Modal(document.getElementById('productSetupModal'));
                    productSetupModal.show();
                }, 300);
                @endif
            }
        });
    </script>

<style id="packaging-dark-final-hotfix">
/* =========================================================
   PACKAGING CALCULATION — FINAL DARK MODE HOTFIX
   Letakkan PALING BAWAH setelah seluruh CSS/HTML konfigurasi.
   ========================================================= */

/* Semua pemicu dark mode yang mungkin dipakai aplikasi. */
:where(
    html[data-bs-theme="dark"],
    html[data-theme="dark"],
    html.dark,
    html.theme-dark,
    body[data-bs-theme="dark"],
    body[data-theme="dark"],
    body.dark,
    body.dark-mode,
    body.theme-dark
) .crate-page,
.crate-page.packaging-dark {
    --crate-final-surface: #111827;
    --crate-final-surface-soft: #172033;
    --crate-final-hover: #1e293b;
    --crate-final-border: #334155;
    --crate-final-border-strong: #475569;
    --crate-final-text: #f8fafc;
    --crate-final-text-soft: #cbd5e1;
    --crate-final-muted: #94a3b8;
}

/* =========================================================
   1. INPUT DIMENSI DAN KONFIGURASI YANG MASIH PUTIH
   ========================================================= */
:where(
    html[data-bs-theme="dark"],
    html[data-theme="dark"],
    html.dark,
    html.theme-dark,
    body[data-bs-theme="dark"],
    body[data-theme="dark"],
    body.dark,
    body.dark-mode,
    body.theme-dark
) .crate-page .configuration-card :is(
    input,
    select,
    textarea,
    .form-control,
    .form-select,
    .custom-input,
    .custom-select,
    .matrix-select,
    .configuration-readonly-input
),
.crate-page.packaging-dark .configuration-card :is(
    input,
    select,
    textarea,
    .form-control,
    .form-select,
    .custom-input,
    .custom-select,
    .matrix-select,
    .configuration-readonly-input
) {
    color: var(--crate-final-text) !important;
    -webkit-text-fill-color: var(--crate-final-text) !important;
    background: var(--crate-final-surface-soft) !important;
    background-color: var(--crate-final-surface-soft) !important;
    border-color: var(--crate-final-border-strong) !important;
    box-shadow: inset 0 0 0 1000px var(--crate-final-surface-soft) !important;
    opacity: 1 !important;
    color-scheme: dark;
}

:where(
    html[data-bs-theme="dark"],
    html[data-theme="dark"],
    html.dark,
    html.theme-dark,
    body[data-bs-theme="dark"],
    body[data-theme="dark"],
    body.dark,
    body.dark-mode,
    body.theme-dark
) .crate-page .configuration-card :is(input, select, textarea):focus,
.crate-page.packaging-dark .configuration-card :is(input, select, textarea):focus {
    border-color: #60a5fa !important;
    box-shadow:
        inset 0 0 0 1000px var(--crate-final-surface-soft),
        0 0 0 3px rgba(96, 165, 250, .16) !important;
}

/* Chrome autofill dan number input. */
:where(
    html[data-bs-theme="dark"],
    html[data-theme="dark"],
    html.dark,
    html.theme-dark,
    body[data-bs-theme="dark"],
    body[data-theme="dark"],
    body.dark,
    body.dark-mode,
    body.theme-dark
) .crate-page .configuration-card input:-webkit-autofill,
.crate-page.packaging-dark .configuration-card input:-webkit-autofill {
    -webkit-text-fill-color: var(--crate-final-text) !important;
    -webkit-box-shadow: 0 0 0 1000px var(--crate-final-surface-soft) inset !important;
    caret-color: var(--crate-final-text);
}

/* =========================================================
   2. TEKS KONFIGURASI YANG MASIH BIRU GELAP
   Style ini harus berada setelah style lokal .config-display-value.
   ========================================================= */
:where(
    html[data-bs-theme="dark"],
    html[data-theme="dark"],
    html.dark,
    html.theme-dark,
    body[data-bs-theme="dark"],
    body[data-theme="dark"],
    body.dark,
    body.dark-mode,
    body.theme-dark
) .crate-page .configuration-card .config-display-value,
.crate-page.packaging-dark .configuration-card .config-display-value {
    color: var(--crate-final-text-soft) !important;
}

:where(
    html[data-bs-theme="dark"],
    html[data-theme="dark"],
    html.dark,
    html.theme-dark,
    body[data-bs-theme="dark"],
    body[data-theme="dark"],
    body.dark,
    body.dark-mode,
    body.theme-dark
) .crate-page .configuration-card .config-display-muted,
.crate-page.packaging-dark .configuration-card .config-display-muted {
    color: #64748b !important;
}

:where(
    html[data-bs-theme="dark"],
    html[data-theme="dark"],
    html.dark,
    html.theme-dark,
    body[data-bs-theme="dark"],
    body[data-theme="dark"],
    body.dark,
    body.dark-mode,
    body.theme-dark
) .crate-page .configuration-card .configuration-matrix-header,
.crate-page.packaging-dark .configuration-card .configuration-matrix-header {
    color: var(--crate-final-muted) !important;
}

:where(
    html[data-bs-theme="dark"],
    html[data-theme="dark"],
    html.dark,
    html.theme-dark,
    body[data-bs-theme="dark"],
    body[data-theme="dark"],
    body.dark,
    body.dark-mode,
    body.theme-dark
) .crate-page .configuration-card :is(
    .configuration-content,
    .configuration-content.container-input-group,
    .configuration-section,
    .configuration-footer
),
.crate-page.packaging-dark .configuration-card :is(
    .configuration-content,
    .configuration-content.container-input-group,
    .configuration-section,
    .configuration-footer
) {
    background: var(--crate-final-surface) !important;
    background-color: var(--crate-final-surface) !important;
    border-color: var(--crate-final-border) !important;
}

:where(
    html[data-bs-theme="dark"],
    html[data-theme="dark"],
    html.dark,
    html.theme-dark,
    body[data-bs-theme="dark"],
    body[data-theme="dark"],
    body.dark,
    body.dark-mode,
    body.theme-dark
) .crate-page .configuration-card :is(.group-item, .configuration-matrix-row):hover,
.crate-page.packaging-dark .configuration-card :is(.group-item, .configuration-matrix-row):hover {
    background: var(--crate-final-hover) !important;
}

/* Mengalahkan background putih dari inline style saat komponen nonaktif. */
:where(
    html[data-bs-theme="dark"],
    html[data-theme="dark"],
    html.dark,
    html.theme-dark,
    body[data-bs-theme="dark"],
    body[data-theme="dark"],
    body.dark,
    body.dark-mode,
    body.theme-dark
) .crate-page #jarak_balok_additional_wrapper,
.crate-page.packaging-dark #jarak_balok_additional_wrapper {
    background-color: transparent !important;
}

/* =========================================================
   3. BADGE KODE MATERIAL YANG MENJADI KOTAK PUTIH
   Penyebab: background menggunakan var(--navy), sementara --navy
   di dark mode diubah menjadi putih untuk kebutuhan teks.
   ========================================================= */
:where(
    html[data-bs-theme="dark"],
    html[data-theme="dark"],
    html.dark,
    html.theme-dark,
    body[data-bs-theme="dark"],
    body[data-theme="dark"],
    body.dark,
    body.dark-mode,
    body.theme-dark
) .crate-page #resume-material-container .badge,
.crate-page.packaging-dark #resume-material-container .badge {
    color: #ffffff !important;
    -webkit-text-fill-color: #ffffff !important;
    background: #17375e !important;
    background-color: #17375e !important;
    border: 1px solid #31557f !important;
    box-shadow: none !important;
}

:where(
    html[data-bs-theme="dark"],
    html[data-theme="dark"],
    html.dark,
    html.theme-dark,
    body[data-bs-theme="dark"],
    body[data-theme="dark"],
    body.dark,
    body.dark-mode,
    body.theme-dark
) .crate-page #resume-material-container > div > div,
.crate-page.packaging-dark #resume-material-container > div > div {
    background: var(--crate-final-surface) !important;
    border-color: var(--crate-final-border) !important;
}

:where(
    html[data-bs-theme="dark"],
    html[data-theme="dark"],
    html.dark,
    html.theme-dark,
    body[data-bs-theme="dark"],
    body[data-theme="dark"],
    body.dark,
    body.dark-mode,
    body.theme-dark
) .crate-page #resume-material-container .text-dark,
.crate-page.packaging-dark #resume-material-container .text-dark {
    color: var(--crate-final-text-soft) !important;
}

/* KPI dan total packing tetap gelap, tidak kembali putih. */
:where(
    html[data-bs-theme="dark"],
    html[data-theme="dark"],
    html.dark,
    html.theme-dark,
    body[data-bs-theme="dark"],
    body[data-theme="dark"],
    body.dark,
    body.dark-mode,
    body.theme-dark
) .crate-page .panel-card :is(.bg-white, .bg-light),
.crate-page.packaging-dark .panel-card :is(.bg-white, .bg-light) {
    background: var(--crate-final-surface) !important;
    border-color: var(--crate-final-border) !important;
}

/* Menjaga badge Bootstrap lain yang memang membutuhkan teks putih. */
:where(
    html[data-bs-theme="dark"],
    html[data-theme="dark"],
    html.dark,
    html.theme-dark,
    body[data-bs-theme="dark"],
    body[data-theme="dark"],
    body.dark,
    body.dark-mode,
    body.theme-dark
) .crate-page .badge.text-white,
.crate-page.packaging-dark .badge.text-white {
    color: #ffffff !important;
    -webkit-text-fill-color: #ffffff !important;
}

</style>
    <!-- Modal Validasi Data -->
    <div class="modal fade" id="validasiDataModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content rounded-4 border-0 shadow-lg">
                @include('packaging.Validasi-data')
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const tableCollapse = document.getElementById('packingDetailTable');
            const toggleButton = document.getElementById('packingTableToggle');
            const toggleLabel = document.getElementById('packingToggleLabel');
            const toggleIcon = document.getElementById('packingToggleIcon');

            if (!tableCollapse || !toggleButton || !toggleLabel || !toggleIcon) {
                return;
            }

            const setPackingTableState = function (isVisible) {
                toggleButton.setAttribute('aria-expanded', isVisible ? 'true' : 'false');
                toggleLabel.textContent = isVisible ? 'Hide Table' : 'Show Table';
                toggleIcon.textContent = isVisible ? 'expand_less' : 'expand_more';
            };

            tableCollapse.addEventListener('shown.bs.collapse', function () {
                setPackingTableState(true);
            });

            tableCollapse.addEventListener('hidden.bs.collapse', function () {
                setPackingTableState(false);
            });

            setPackingTableState(tableCollapse.classList.contains('show'));
        });
    </script>

</x-app-layout>
