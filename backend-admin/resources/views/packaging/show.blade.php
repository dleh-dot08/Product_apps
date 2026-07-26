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
    if (isset($calculation) && $calculation->length && $calculation->width && $calculation->height) {
        $P_m = $calculation->length / 1000;
        $L_m = $calculation->width / 1000;
        $T_m = $calculation->height / 1000;
        $areaKerja = 2 * (($P_m * $L_m) + ($P_m * $T_m) + ($L_m * $T_m));
    }
@endphp
    <!-- Import Material Symbols Rounded -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    
    <style>
        .crate-page {
            --navy: #0b2a55;
            --navy-2: #123f78;
            --blue: #ea580c; /* Diubah menjadi orange sesuai permintaan sebelumnya */
            --soft: #f4f7fb;
            --line: #e5eaf3;
            --text: #0f172a;
            --muted: #64748b;
        }

        /* Dark Mode Support */
        [data-bs-theme="dark"] .crate-page {
            --navy: #f8fafc;
            --navy-2: #e2e8f0;
            --soft: #1e293b;
            --line: #334155;
            --text: #f1f5f9;
            --muted: #94a3b8;
        }

        [data-bs-theme="dark"] .crate-page .page-header,
        [data-bs-theme="dark"] .crate-page .input-card,
        [data-bs-theme="dark"] .crate-page .panel-card,
        [data-bs-theme="dark"] .crate-page .stat-card {
            background: #1e293b;
            border-color: #334155;
            box-shadow: 0 10px 24px rgba(0, 0, 0, .2);
        }
        
        [data-bs-theme="dark"] .crate-page .bg-white,
        [data-bs-theme="dark"] .crate-page .bg-light {
            background-color: #1e293b !important;
        }

        [data-bs-theme="dark"] .crate-page .text-dark,
        [data-bs-theme="dark"] .crate-page .text-body {
            color: #f8fafc !important;
        }

        [data-bs-theme="dark"] .crate-page .btn-soft {
            background: #0f172a;
            border-color: #334155;
            color: #f8fafc;
        }

        [data-bs-theme="dark"] .crate-page .btn-soft:hover {
            background: #1e293b;
            color: var(--blue);
        }

        [data-bs-theme="dark"] .crate-page .icon-box {
            background: rgba(234, 88, 12, 0.15);
            color: var(--blue);
        }

        [data-bs-theme="dark"] .crate-page .form-control,
        [data-bs-theme="dark"] .crate-page .form-select {
            background-color: #0f172a;
            border-color: #334155;
            color: #f8fafc;
        }

        [data-bs-theme="dark"] .crate-page .visual-stage {
            background: radial-gradient(circle at 50% 48%, rgba(234, 88, 12, 0.08), transparent 34%), linear-gradient(180deg, #1e293b 0%, #0f172a 100%);
        }

        .crate-page .page-header {
            background: var(--soft);
            border: 1px solid var(--line);
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

</style>

    <div class="crate-page container-fluid py-4">
        <div class="page-header px-3 py-3 d-flex flex-col gap-3 flex-md-row align-items-md-center justify-content-md-between mb-3">
            <div class="d-flex align-items-center gap-3">
                <a href="{{ route('packaging.index') }}" class="btn-soft px-3 py-2 rounded-3 shadow-none" style="border-color: #cbd5e1; height: 38px; width: 38px; display: inline-flex; align-items: center; justify-content: center;">
                    <span class="material-symbols-rounded text-lg" style="margin-left: 2px;">arrow_back</span>
                </a>
                <div>
                    <h1 class="h5 mb-0" style="color:#0f172a;font-weight:900;letter-spacing:-.01em; line-height: 1.2;">Calculation</h1>
                    <p class="mb-0 small text-secondary d-none d-md-block" style="font-size: 11px;">Halaman kalkulasi packaging crate untuk pengiriman barang berat.</p>
                </div>
            </div>
            <div class="d-flex flex-wrap align-items-center gap-2 justify-content-md-end">
                @if(isset($calculation))
                    <button type="button" onclick="openEditModal()" class="btn-soft text-white border-0 shadow-sm" style="background-color: var(--blue);">
                        <span class="material-symbols-rounded text-lg">edit</span>
                        <span class="fw-bold">EDIT DATA</span>
                    </button>
                @else
                    <!-- Trigger Modal Step 1 -->
                    <button type="button" data-bs-toggle="modal" data-bs-target="#step1Modal" class="btn-soft text-white border-0 shadow-sm" style="background-color: var(--blue);">
                        <span class="material-symbols-rounded text-lg">add_circle</span>
                        <span class="fw-bold">ADD DATA</span>
                    </button>
                @endif

                @if(isset($calculation))
                    @if(auth()->check() && auth()->user()->hasRole('admin'))
                    <button type="button" data-bs-toggle="modal" data-bs-target="#validasiModal" class="btn-soft text-success border-success" style="color: #0e8a63; border-color: rgba(14, 138, 99, 0.3); background-color: rgba(14, 138, 99, 0.03); cursor: pointer; text-decoration: none;">
                        <span class="material-symbols-rounded text-lg">fact_check</span>
                        <span>VALIDASI</span>
                    </button>
                    @endif
                <button type="button" onclick="printWith3DImage()" class="btn-soft text-primary border-primary" style="color: #1769e8; border-color: rgba(23, 105, 232, 0.3); background-color: rgba(23, 105, 232, 0.03); cursor: pointer;">
                    <span class="material-symbols-rounded text-lg">print</span>
                    <span>PRINT</span>
                </button>
                @endif
            </div>
        </div>

        {{-- Main Row: Inputs, Visual, Stats --}}
        <div class="container-fluid pt-4 pb-4 rounded-4" style="background-color: rgba(255, 255, 255, 0.4); backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px); border: 1px solid rgba(255, 255, 255, 0.5); box-shadow: 0 4px 6px rgba(0,0,0,0.02);">
            <div class="row g-4">
                <!-- Full Width Column: Inputs (col-12) -->
                <div class="col-12 d-flex flex-column gap-3">
                    
                    <!-- PACKING INFORMATION -->
                    <div class="card border-0 shadow-sm style-card-container position-relative overflow-hidden">
                        <!-- DRAFT Badge - Absolute Positioned to Top Right Corner -->
                        <span class="badge bg-primary text-primary bg-opacity-10 px-3 py-1 border-bottom border-start border-primary border-opacity-25 position-absolute" style="top: 0; right: 0; border-bottom-left-radius: 0.5rem; font-size: 9px; z-index: 10;"><i class="fas fa-circle me-1" style="font-size: 6px;"></i> DRAFT</span>
                        
                        <div class="card-header bg-white border-bottom-0 pt-3 pb-0 d-flex justify-content-between align-items-center">
                            <h6 class="fw-bold mb-0 text-navy d-flex align-items-center" style="font-size: 13px;">
                                <div class="bg-primary bg-opacity-10 p-1 rounded me-2 d-flex align-items-center justify-content-center text-primary">
                                    <span class="material-symbols-rounded" style="font-size: 16px;">inventory_2</span>
                                </div>
                                PACKING INFORMATION
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <!-- Reference Number -->
                                <div class="col-12 border-bottom pb-3">
                                    <div class="d-flex align-items-center">
                                        <div class="bg-primary bg-opacity-10 p-2 rounded me-3 text-primary d-flex align-items-center justify-content-center">
                                            <span class="material-symbols-rounded">description</span>
                                        </div>
                                        <div>
                                            <small class="text-muted d-block lh-1 mb-1" style="font-size: 11px;">Nomor Packaging</small>
                                            <h5 class="fw-bold text-navy mb-0" style="font-size: 18px;" id="ui_ref_no">{{ $calculation->packaging_number ?? '-' }}</h5>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Details Row -->
                                <div class="col-md-3 border-end">
                                    <div class="d-flex align-items-center">
                                        <div class="bg-primary bg-opacity-10 p-2 rounded me-3 text-primary d-flex align-items-center justify-content-center">
                                            <span class="material-symbols-rounded">receipt_long</span>
                                        </div>
                                        <div>
                                            <small class="text-muted d-block lh-1 mb-1" style="font-size: 11px;">SO Number</small>
                                            <div class="fw-bold text-navy" id="ui_so_number">{{ $calculation->job->no_so ?? $calculation->job->so_number ?? '-' }}</div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-3 border-end">
                                    <div class="d-flex align-items-center">
                                        <div class="bg-success bg-opacity-10 p-2 rounded me-3 text-success d-flex align-items-center justify-content-center">
                                            <span class="material-symbols-rounded">domain</span>
                                        </div>
                                        <div>
                                            <small class="text-muted d-block lh-1 mb-1" style="font-size: 11px;">Customer Name</small>
                                            <div class="fw-bold text-navy" id="ui_customer">{{ $calculation->job->customer ?? $calculation->job->customer_name ?? '-' }}</div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-3 border-end">
                                    <div class="d-flex align-items-center">
                                        <div class="bg-purple bg-opacity-10 p-2 rounded me-3 d-flex align-items-center justify-content-center" style="color: #8b5cf6; background-color: rgba(139, 92, 246, 0.1);">
                                            <span class="material-symbols-rounded">engineering</span>
                                        </div>
                                        <div>
                                            <small class="text-muted d-block lh-1 mb-1" style="font-size: 11px;">Packer</small>
                                            <div class="fw-bold text-navy">{{ $calculation->packer->name ?? '-' }}</div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-3">
                                    <div class="d-flex align-items-center">
                                        <div class="bg-warning bg-opacity-10 p-2 rounded me-3 text-warning d-flex align-items-center justify-content-center" style="color: #f59e0b; background-color: rgba(245, 158, 11, 0.1);">
                                            <span class="material-symbols-rounded">person</span>
                                        </div>
                                        <div>
                                            <small class="text-muted d-block lh-1 mb-1" style="font-size: 11px;">Assigned By</small>
                                            <div class="fw-bold text-navy">{{ $calculation->job->user->name ?? 'Admin' }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Single Combined Card -->
                    <div class="card border-0 shadow-sm style-card-container configuration-card">
                        <form action="{{ isset($calculation) ? route('packaging.calculations.update', $calculation->id) : '#' }}" method="POST" id="form-edit-config">
                            @csrf
                            @if(isset($calculation))
                                @method('PUT')
                            @endif
                            
                            <!-- Hidden Inputs untuk Data SO dari Modal Step 1 -->
                            <input type="hidden" name="so_number" id="input_so_number" value="{{ $calculation->job->so_number ?? '' }}">
                            <input type="hidden" name="customer_name" id="input_customer_name" value="{{ $calculation->job->customer_name ?? '' }}">
                            <input type="hidden" name="item_id" id="input_item_id" value="">
                            <input type="hidden" name="item_no" id="input_item_no" value="">
                            <input type="hidden" name="item_description" id="input_item_description" value="">
                            <input type="hidden" name="shipping_qty" id="input_shipping_qty" value="">
                            
                            <div class="card-body p-0">
                                <!-- Custom Grid Layout -->
                                <div class="configuration-grid">

                                    <!-- 1. Dimensi -->
                                    <div class="d-flex flex-column h-100 configuration-section configuration-section-dimension">
                                        <h6 class="fw-bold text-navy mb-0 d-flex align-items-center configuration-heading">
                                            <span class="badge bg-navy me-2 rounded-circle d-flex align-items-center justify-content-center" style="width: 24px; height: 24px; font-size: 13px;">1</span>
                                            Dimensi
                                        </h6>
                                        <style>
                                            .container-input-group.flex-grow-1 > .group-item {
                                                flex-grow: 1;
                                                display: flex;
                                                align-items: center;
                                            }
                                        </style>
                                        <div class="d-flex flex-column border rounded bg-white container-input-group overflow-hidden flex-grow-1 configuration-content configuration-content-narrow">
                                            <div class="configuration-matrix-header" style="visibility: hidden; min-height: 28px;" aria-hidden="true"><span>&nbsp;</span></div>
                                            <!-- Panjang -->
                                            <div class="d-flex align-items-center px-3 py-2 group-item">
                                                <span class="material-symbols-rounded text-secondary me-3" style="font-size: 18px;">swap_horiz</span>
                                                <div class="w-100">
                                                    <small class="text-muted d-block lh-1 mb-1" style="font-size: 8px;">Panjang</small>
                                                    <div class="d-flex align-items-center gap-1">
                                                        <div class="fw-bold text-navy" style="font-size: 13px;">{{ isset($calculation) ? ($calculation->length ?? $calculation->panjang ?? 0) : '0' }}</div>
                                                        <span class="text-muted" style="font-size: 11px;">mm</span>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Lebar -->
                                            <div class="d-flex align-items-center px-3 py-2 group-item">
                                                <span class="material-symbols-rounded text-secondary me-3" style="font-size: 18px;">straighten</span>
                                                <div class="w-100">
                                                    <small class="text-muted d-block lh-1 mb-1" style="font-size: 8px;">Lebar</small>
                                                    <div class="d-flex align-items-center gap-1">
                                                        <div class="fw-bold text-navy" style="font-size: 13px;">{{ isset($calculation) ? ($calculation->width ?? $calculation->lebar ?? 0) : '0' }}</div>
                                                        <span class="text-muted" style="font-size: 11px;">mm</span>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Tinggi -->
                                            <div class="d-flex align-items-center px-3 py-2 group-item">
                                                <span class="material-symbols-rounded text-secondary me-3" style="font-size: 18px;">height</span>
                                                <div class="w-100">
                                                    <small class="text-muted d-block lh-1 mb-1" style="font-size: 8px;">Tinggi</small>
                                                    <div class="d-flex align-items-center gap-1">
                                                        <div class="fw-bold text-navy" style="font-size: 13px;">{{ isset($calculation) ? ($calculation->height ?? $calculation->tinggi ?? 0) : '0' }}</div>
                                                        <span class="text-muted" style="font-size: 11px;">mm</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- 2. Konfigurasi Area Bawah -->
                                    <div class="d-flex flex-column h-100 configuration-section configuration-section-bottom configuration-section-wide">
                                        <h6 class="fw-bold text-navy mb-0 d-flex align-items-center configuration-heading">
                                            <span class="badge bg-navy me-2 rounded-circle d-flex align-items-center justify-content-center" style="width: 24px; height: 24px; font-size: 13px;">2</span>
                                            Konfigurasi Area Bawah
                                        </h6>
                                        <div class="d-flex flex-column bg-white container-input-group overflow-hidden border rounded flex-grow-1 configuration-content configuration-matrix">
                                            <div class="configuration-matrix-header" aria-hidden="true">
                                                <span>Komponen</span>
                                                <span>Penggunaan</span>
                                                <span>Arah Pemasangan</span>
                                                <span>Material</span>
                                            </div>



                                            <div class="configuration-matrix-row group-item">
                                                <div class="configuration-component">
                                                    <span class="material-symbols-rounded text-secondary" style="font-size: 16px;">change_history</span>
                                                    <small class="text-muted">Penyangga</small>
                                                </div>
                                                <div class="configuration-cell px-2 py-1">
                                                    @php
                                                        $kb = isset($calculation->konfigurasi_bawah) ? (is_array($calculation->konfigurasi_bawah) ? $calculation->konfigurasi_bawah : json_decode($calculation->konfigurasi_bawah, true)) : [];
                                                    @endphp
                                                    <div class="fw-bold text-navy" style="font-size: 12px;">{{ $kb['penyanggah']['status'] ?? 'Include' }}</div>
                                                </div>
                                                <div class="configuration-cell px-2 py-1">
                                                    <div class="fw-bold text-navy" style="font-size: 12px;">{{ $kb['penyanggah']['arah'] ?? 'Horizontal' }}</div>
                                                </div>
                                                <div class="configuration-cell px-2 py-1">
                                                    <div class="fw-bold text-navy" style="font-size: 12px;">{{ $kb['penyanggah']['material'] ?? '-' }}</div>
                                                </div>
                                            </div>

                                            <div class="configuration-matrix-row group-item">
                                                <div class="configuration-component">
                                                    <span class="material-symbols-rounded text-secondary" style="font-size: 16px;">grid_view</span>
                                                    <small class="text-muted">Penutup</small>
                                                </div>
                                                <div class="configuration-cell px-2 py-1">
                                                    <div class="fw-bold text-navy" style="font-size: 12px;">{{ $kb['penutup']['status'] ?? 'Tanpa Penutup' }}</div>
                                                </div>
                                                <div class="configuration-cell px-2 py-1">
                                                    <div class="fw-bold text-navy" style="font-size: 12px;">{{ $kb['penutup']['arah'] ?? '-' }}</div>
                                                </div>
                                                <div class="configuration-cell px-2 py-1">
                                                    <div class="fw-bold text-navy" style="font-size: 12px;">{{ $kb['penutup']['material'] ?? '-' }}</div>
                                                </div>
                                            </div>

                                            <div class="configuration-matrix-row group-item">
                                                <div class="configuration-component">
                                                    <span class="material-symbols-rounded text-secondary" style="font-size: 16px;">view_column_2</span>
                                                    <small class="text-muted">Kaki Balok</small>
                                                </div>
                                                <div class="configuration-cell px-2 py-1">
                                                    <div class="fw-bold text-navy" style="font-size: 12px;">{{ $kb['kaki_balok']['status'] ?? 'Include' }}</div>
                                                </div>
                                                <div class="configuration-cell px-2 py-1">
                                                    <div class="fw-bold text-navy" style="font-size: 12px;">{{ $kb['kaki_balok']['arah'] ?? '-' }}</div>
                                                </div>
                                                <div class="configuration-cell px-2 py-1">
                                                    <div class="fw-bold text-navy" style="font-size: 12px;">{{ $kb['kaki_balok']['material'] ?? '-' }}</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- 3. Konfigurasi Area Atas -->
                                    <div class="d-flex flex-column h-100 configuration-section configuration-section-top configuration-section-wide">
                                        <h6 class="fw-bold text-navy mb-0 d-flex align-items-center configuration-heading">
                                            <span class="badge bg-navy me-2 rounded-circle d-flex align-items-center justify-content-center" style="width: 24px; height: 24px; font-size: 13px;">3</span>
                                            Konfigurasi Area Atas
                                        </h6>
                                        <div class="d-flex flex-column bg-white container-input-group overflow-hidden border rounded flex-grow-1 configuration-content configuration-matrix">
                                            <div class="configuration-matrix-header" aria-hidden="true">
                                                <span>Komponen</span>
                                                <span>Penggunaan</span>
                                                <span>Arah Pemasangan</span>
                                                <span>Material</span>
                                            </div>

                                            <div class="configuration-matrix-row group-item">
                                                <div class="configuration-component">
                                                    <span class="material-symbols-rounded text-secondary" style="font-size: 16px;">change_history</span>
                                                    <small class="text-muted">Penyangga</small>
                                                </div>
                                                <div class="configuration-cell px-2 py-1">
                                                    @php
                                                        $ka = isset($calculation->konfigurasi_atas) ? (is_array($calculation->konfigurasi_atas) ? $calculation->konfigurasi_atas : json_decode($calculation->konfigurasi_atas, true)) : [];
                                                    @endphp
                                                    <div class="fw-bold text-navy" style="font-size: 12px;">{{ $ka['penyanggah']['status'] ?? 'Not Include' }}</div>
                                                </div>
                                                <div class="configuration-cell px-2 py-1">
                                                    <div class="fw-bold text-navy" style="font-size: 12px;">{{ $ka['penyanggah']['arah'] ?? '-' }}</div>
                                                </div>
                                                <div class="configuration-cell px-2 py-1">
                                                    <div class="fw-bold text-navy" style="font-size: 12px;">{{ $ka['penyanggah']['material'] ?? '-' }}</div>
                                                </div>
                                            </div>

                                            <div class="configuration-matrix-row group-item">
                                                <div class="configuration-component">
                                                    <span class="material-symbols-rounded text-secondary" style="font-size: 16px;">grid_view</span>
                                                    <small class="text-muted">Penutup</small>
                                                </div>
                                                <div class="configuration-cell px-2 py-1">
                                                    <div class="fw-bold text-navy" style="font-size: 12px;">{{ $ka['penutup']['status'] ?? 'Tanpa Penutup' }}</div>
                                                </div>
                                                <div class="configuration-cell px-2 py-1">
                                                    <div class="fw-bold text-navy" style="font-size: 12px;">{{ $ka['penutup']['arah'] ?? '-' }}</div>
                                                </div>
                                                <div class="configuration-cell px-2 py-1">
                                                    <div class="fw-bold text-navy" style="font-size: 12px;">{{ $ka['penutup']['material'] ?? '-' }}</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- 4. Konfigurasi Tambahan -->
                                    <div class="d-flex flex-column h-100 configuration-section configuration-section-general">
                                        <h6 class="fw-bold text-navy mb-0 d-flex align-items-center configuration-heading">
                                            <span class="badge bg-navy me-2 rounded-circle d-flex align-items-center justify-content-center" style="width: 24px; height: 24px; font-size: 13px;">4</span>
                                            Konfigurasi
                                        </h6>
                                        <div class="d-flex flex-column border rounded bg-white container-input-group overflow-hidden flex-grow-1 configuration-content configuration-content-narrow">
                                            <div class="configuration-matrix-header" style="visibility: hidden; min-height: 28px;" aria-hidden="true"><span>&nbsp;</span></div>


                                            <!-- Jarak Penyanggah -->
                                            <div class="d-flex align-items-center px-3 py-2 group-item">
                                                <span class="material-symbols-rounded text-secondary me-3" style="font-size: 18px;">straighten</span>
                                                <div class="w-100">
                                                    <small class="text-muted d-block lh-1 mb-1" style="font-size: 13px;">Jarak Penyanggah</small>
                                                    <div class="d-flex align-items-center gap-1">
                                                        <div class="fw-bold text-navy" style="font-size: 13px;">{{ isset($calculation) ? ($calculation->distance_between_pillars ?? $calculation->jarak_penyanggah ?? '300') : '300' }}</div>
                                                        <span class="text-muted" style="font-size: 11px;">mm</span>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Celah Atas -->
                                            <div class="d-flex align-items-center px-3 py-2 group-item">
                                                <span class="material-symbols-rounded text-secondary me-3" style="font-size: 18px;">vertical_align_top</span>
                                                <div class="w-100">
                                                    <small class="text-muted d-block lh-1 mb-1" style="font-size: 13px;">Celah Atas</small>
                                                    <div class="d-flex align-items-center gap-1">
                                                        <div class="fw-bold text-navy" style="font-size: 13px;">{{ isset($calculation) ? ($calculation->gap_atas ?? $calculation->gap ?? 10) : '10' }}</div>
                                                        <span class="text-muted" style="font-size: 11px;">mm</span>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Celah Bawah -->
                                            <div class="d-flex align-items-center px-3 py-2 group-item">
                                                <span class="material-symbols-rounded text-secondary me-3" style="font-size: 18px;">vertical_align_bottom</span>
                                                <div class="w-100">
                                                    <small class="text-muted d-block lh-1 mb-1" style="font-size: 13px;">Celah Bawah</small>
                                                    <div class="d-flex align-items-center gap-1">
                                                        <div class="fw-bold text-navy" style="font-size: 13px;">{{ isset($calculation) ? ($calculation->gap_bawah ?? $calculation->gap ?? 10) : '10' }}</div>
                                                        <span class="text-muted" style="font-size: 11px;">mm</span>
                                                    </div>
                                                </div>
                                            </div>


                                        </div>
                                    </div>
                                </div>
                            </div>

                        </form>
                    </div>
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
                            $matKode = $detail->material->kode ?? '-';
                            if ($matKode !== '-' && $matKode !== '') {
                                $len = (float)($detail->total_length ?? 0);
                                $totalWoodLength += $len;

                                if (!isset($materialResume[$matKode])) {
                                    $materialResume[$matKode] = [
                                        'kode' => $matKode,
                                        'nama' => $detail->material->nama ?? $matKode,
                                        'wood_type' => $detail->material->wood_type ?? $matKode,
                                        'thickness' => $detail->material->thickness ?? 0,
                                        'width' => $detail->material->width ?? 0,
                                        'length' => 0
                                    ];
                                }
                                $materialResume[$matKode]['length'] += $len;
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
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div class="d-flex flex-column gap-1 overflow-hidden pe-2">
                                                <span class="badge text-white fw-bold py-1 px-2 align-self-start text-wrap text-break" style="font-size: 12px; background-color: var(--navy); text-align: left; word-break: break-all;">{{ $mat['wood_type'] }}</span>
                                                <span class="text-dark fw-bold text-wrap" style="font-size: 13px; line-height: 1.4;">{{ $mat['nama'] }}</span>
                                            </div>
                                            <div class="text-end flex-shrink-0">
                                                <span class="fw-extrabold text-success" style="font-size: 18px;">{{ number_format($mat['length'], 1, ',', '.') }}</span>
                                                <span class="text-muted fw-bold ms-1" style="font-size: 14px;">m</span>
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
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-3 bg-white">
                    <!-- Header -->
                    <div class="card-header bg-white border-bottom-0 pt-4 pb-3 px-4 d-flex justify-content-between align-items-start position-relative">
                        <div class="d-flex align-items-center gap-3 pe-5">
                            <div class="bg-primary bg-opacity-10 text-primary rounded-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                                <span class="material-symbols-rounded fs-3">view_in_ar</span>
                            </div>
                            <div>
                                <h5 class="mb-0 fw-bold">Visualisasi Potongan Material</h5>
                                <small class="text-muted">Preview potongan material dalam skala 2D</small>
                            </div>
                        </div>
                        <div class="d-flex gap-2 align-items-center position-absolute" style="top: 1.2rem; right: 1.2rem; z-index: 5;">
                            <button type="button" class="btn btn-light bg-white border shadow-sm d-flex align-items-center me-1" data-bs-toggle="modal" data-bs-target="#materialDetailModal"><span class="material-symbols-rounded fs-6 me-2">list</span>Lihat Daftar</button>
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
                    <div class="position-relative border-top" style="background: #f8fafc;">
                        <div id="material-sorting-container" style="width: 100%; height: 450px; position: relative; overflow: hidden;"></div>
                        <div id="material-labels-container" style="position: absolute; top: 0; left: 0; pointer-events: none; width: 100%; height: 100%; overflow: hidden;"></div>
                    </div>

                    <!-- Legend / Categories -->
                    <div class="px-4 py-2 bg-light border-top d-flex flex-wrap gap-4 align-items-center justify-content-center">
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
                    <div class="bg-light px-4 pb-3 pt-2">
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
            <div class="modal-content border-0 shadow-lg">
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
        
        <!-- MODAL STEP 1 SO SEARCH -->
        <div class="modal fade" id="step1Modal" tabindex="-1" aria-labelledby="step1ModalLabel" aria-hidden="true" data-bs-backdrop="static">
            <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content border-0 shadow-lg">
                    <div class="modal-header border-0 pb-2 d-flex align-items-center">
                        <div class="bg-primary bg-opacity-10 p-2 rounded me-3 text-primary d-flex align-items-center justify-content-center">
                            <span class="material-symbols-rounded" style="color: var(--blue);">search</span>
                        </div>
                        <h5 class="modal-title fw-bold" id="step1ModalLabel" style="color: var(--text);">Cari & Tarik Data Sales Order</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-0" style="background-color: var(--soft);">
                        @include('packaging.partials.modals.step1')
                    </div>
                    <div class="modal-footer border-0" style="background-color: var(--card);">
                        <button type="button" class="btn btn-light rounded-pill px-4 border shadow-sm" data-bs-dismiss="modal">Batal</button>
                        <button type="button" id="btnNextStep1" class="btn btn-primary rounded-pill px-4 shadow-sm text-white" disabled>Next <i class="fa-solid fa-arrow-right ms-2"></i></button>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- MODAL STEP 2 CONFIGURATION -->
        <div class="modal fade" id="step2Modal" tabindex="-1" aria-labelledby="step2ModalLabel" aria-hidden="true" data-bs-backdrop="static">
            <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content border-0 shadow-lg">
                    <div class="modal-header border-0 pb-2 d-flex align-items-center">
                        <div class="bg-primary bg-opacity-10 p-2 rounded me-3 text-primary d-flex align-items-center justify-content-center">
                            <span class="material-symbols-rounded" style="color: var(--blue);">settings</span>
                        </div>
                        <h5 class="modal-title fw-bold" id="step2ModalLabel" style="color: var(--text);">Konfigurasi Packaging</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-0" style="background-color: var(--soft);">
                        @include('packaging.partials.modals.step2')
                    </div>
                    <div class="modal-footer border-0" style="background-color: var(--card);">
                        <button type="button" class="btn btn-light rounded-pill px-4 border shadow-sm" data-bs-target="#step1Modal" data-bs-toggle="modal">Kembali</button>
                        <button type="button" id="btnSubmitWizard" class="btn btn-success rounded-pill px-4 shadow-sm text-white">Simpan <i class="fa-solid fa-save ms-2"></i></button>
                    </div>
                </div>
            </div>
        </div>
        
    </div>

    <!-- Modal Validasi Gabungan -->
    @if(auth()->check() && auth()->user()->hasRole('admin'))
    <div class="modal fade" id="validasiModal" tabindex="-1" aria-labelledby="validasiModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" style="max-width: 98vw; height: 98vh; margin: 1vh auto;">
            <div class="modal-content border-0 shadow-sm rounded-4" style="background-color: #f8fafc; height: 100%;">
                
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
                            @include('packaging_calc.Validasi-data.partials._material')
                        </div>
                        
                        <!-- TAB: RUMUS PERHITUNGAN -->
                        <div class="tab-pane fade" id="tab-perhitungan" role="tabpanel" aria-labelledby="perhitungan-tab">
                            @include('packaging_calc.Validasi-data.partials._perhitungan')
                        </div>
                        
                        <!-- TAB: FASTENER -->
                        <div class="tab-pane fade p-4" id="tab-fastener" role="tabpanel" aria-labelledby="fastener-tab">
                            @include('packaging_calc.Validasi-data.partials._fastener')
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

    <script>
        // === LOGIKA FETCH SO MODAL STEP 1 ===
        document.addEventListener('DOMContentLoaded', function() {
            const btnSearchSO = document.getElementById('btnSearchSO');
            const searchSOInput = document.getElementById('searchSO');
            const btnNextStep1 = document.getElementById('btnNextStep1');
            const checkAllItems = document.getElementById('checkAllItems');
            const infoNoSO = document.getElementById('infoNoSO');
            const infoCustomer = document.getElementById('infoCustomer');
            const infoDeliveryDate = document.getElementById('infoDeliveryDate');
            const infoShipto = document.getElementById('infoShipto');
            const tableBodySO = document.getElementById('tableBodySO');
            
            let currentSOData = null;

            if (btnSearchSO) {
                btnSearchSO.addEventListener('click', async () => {
                    const query = searchSOInput.value.trim();
                    if (!query) {
                        alert("Masukkan Nomor SO untuk ditarik datanya.");
                        return;
                    }
                    
                    if (typeof window.setSOSearchLoading === 'function') {
                        window.setSOSearchLoading(true);
                    } else {
                        btnSearchSO.disabled = true;
                        btnSearchSO.innerHTML = `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...`;
                    }
                    
                    if (typeof window.resetSOHeaderInfo === 'function') {
                        window.resetSOHeaderInfo();
                    }
                    
                    if (checkAllItems) {
                        checkAllItems.disabled = true;
                        checkAllItems.checked = false;
                    }
                    
                    currentSOData = null;
                    
                    try {
                        const response = await fetch(`{{ url('/api/packaging/search-so') }}?q=${encodeURIComponent(query)}`);
                        const result = await response.json();
                        
                        if (result.data && result.data.length > 0) {
                            currentSOData = result.data;
                            window.raw_api_data = result.data;
                            const headerData = result.data[0];
                            
                            if (infoNoSO) infoNoSO.innerText = headerData.no_so || '-';
                            if (infoCustomer) infoCustomer.innerText = headerData.nama_pelanggan || '-';
                            if (infoDeliveryDate) infoDeliveryDate.innerText = headerData.tgl_estimasi || headerData.tgl_so || '-';
                            if (infoShipto) infoShipto.innerText = headerData.shipto || '-';
                            
                            if (typeof window.renderSOItems === 'function') {
                                window.renderSOItems(result.data);
                            }
                            
                            // Aktifkan tombol next untuk melanjutkan
                            if(btnNextStep1) {
                                btnNextStep1.disabled = false;
                            }
                        } else {
                            if (typeof window.showSOEmptyState === 'function') {
                                window.showSOEmptyState('Data SO Tidak Ditemukan', `Tidak ada data untuk SO: ${query}`);
                            }
                            if(btnNextStep1) btnNextStep1.disabled = true;
                        }
                    } catch (error) {
                        console.error('Error fetching SO data:', error);
                        if (typeof window.showSOEmptyState === 'function') {
                            window.showSOEmptyState('Error Terhubung ke Server', 'Terjadi kesalahan saat menarik data dari server.');
                        }
                        if(btnNextStep1) btnNextStep1.disabled = true;
                    } finally {
                        if (typeof window.setSOSearchLoading === 'function') {
                            window.setSOSearchLoading(false);
                        } else {
                            btnSearchSO.disabled = false;
                            btnSearchSO.innerHTML = `<i class="fa-solid fa-cloud-arrow-down me-2"></i><span>Tarik Data SO</span>`;
                        }
                    }
                });
            }
            
            // Ketika menekan tombol Gunakan Data SO di Modal
            if(btnNextStep1) {
                btnNextStep1.addEventListener('click', function() {
                    const itemDropdown = document.getElementById('itemDropdown');
                    const qtyInput = document.getElementById('itemQtyKirim');
                    
                    if(!itemDropdown || !itemDropdown.value) {
                        alert('Silakan pilih barang terlebih dahulu!');
                        return;
                    }
                    
                    if(!qtyInput || !qtyInput.value || qtyInput.value <= 0) {
                        alert('Silakan masukkan Qty Pengiriman yang valid!');
                        qtyInput.focus();
                        return;
                    }

                    const activeSOData = currentSOData || window.raw_api_data;
                    if(activeSOData && activeSOData.length > 0) {
                        const headerData = activeSOData[0];
                        
                        // Update UI form di belakang
                        document.getElementById('ui_ref_no').innerText = headerData.no_so || '-';
                        document.getElementById('ui_so_number').innerText = headerData.no_so || '-';
                        document.getElementById('ui_customer').innerText = headerData.nama_pelanggan || '-';
                        
                        // Update hidden input header
                        document.getElementById('input_so_number').value = headerData.no_so || '';
                        document.getElementById('input_customer_name').value = headerData.nama_pelanggan || '';
                        
                        // Dapatkan data item terpilih dari attribute option
                        const selectedOption = itemDropdown.options[itemDropdown.selectedIndex];
                        document.getElementById('input_item_id').value = selectedOption.value;
                        document.getElementById('input_item_no').value = selectedOption.getAttribute('data-item-no') || '';
                        document.getElementById('input_item_description').value = selectedOption.getAttribute('data-item-desc') || '';
                        document.getElementById('input_shipping_qty').value = qtyInput.value;
                        
                        // Tutup modal step 1 dan buka modal step 2
                        const step1ModalEl = document.getElementById('step1Modal');
                        const step1Modal = bootstrap.Modal.getInstance(step1ModalEl) || new bootstrap.Modal(step1ModalEl);
                        step1Modal.hide();
                        
                        // Populate data ke Step 2 (meskipun sidebar sudah dihapus, jika ada text SO bisa diset di sini)
                        // Buka Step 2
                        const step2ModalEl = document.getElementById('step2Modal');
                        const step2Modal = bootstrap.Modal.getInstance(step2ModalEl) || new bootstrap.Modal(step2ModalEl);
                        step2Modal.show();
                    }
                });
            }
        });
    </script>
    @include('packaging.partials._3d_visualizer')
        let isEditingModal = false;
        let editCalculationId = null;

        window.openEditModal = function() {
            isEditingModal = true;
            editCalculationId = '{{ $calculation->id ?? "" }}';

            // Populate Step 1 Headers (simulate search result)
            document.getElementById('infoNoSO').innerText = '{{ $calculation->job->no_so ?? "" }}';
            const searchSOInputEl = document.getElementById('searchSO');
            if (searchSOInputEl) searchSOInputEl.value = '{{ $calculation->job->no_so ?? "" }}';
            
            document.getElementById('infoCustomer').innerText = '{{ $calculation->job->customer ?? "" }}';
            let deliveryDate = '{{ $calculation->job->date_delivery ?? "" }}';
            document.getElementById('infoDeliveryDate').innerText = deliveryDate ? deliveryDate.substring(0, 10) : '-';
            document.getElementById('infoShipto').innerText = '{{ $calculation->job->address ?? "" }}';
            document.getElementById('detailPartNo').innerText = '{{ $calculation->no_product ?? "" }}';
            document.getElementById('detailDesc').innerText = '{{ $calculation->desc_product ?? "" }}';

            // Populate Item Dropdown in Step 1
            if (typeof window.renderSOItems === 'function') {
                let soItems = {!! isset($calculation->job->daftar_iso_item_json) ? (is_string($calculation->job->daftar_iso_item_json) ? $calculation->job->daftar_iso_item_json : json_encode($calculation->job->daftar_iso_item_json)) : '[]' !!};
                if (typeof soItems === 'string') {
                    try { soItems = JSON.parse(soItems); } catch(e) { soItems = []; }
                }
                
                // Fallback: If daftar_iso_item_json is empty in the database, use current calculation data
                if (!soItems || soItems.length === 0) {
                    soItems = [{
                        no_barang: '{{ $calculation->no_product ?? "" }}',
                        deskripsi_barang: '{{ $calculation->desc_product ?? "" }}',
                        qty: 1,
                        sisa_kirim: 1,
                        uom: 'Unit'
                    }];
                }
                
                // If items exist, render them
                if (soItems.length > 0) {
                    window.raw_api_data = soItems;
                    window.renderSOItems(soItems);
                    
                    // Auto-select the correct item
                    const dropdown = document.getElementById('itemDropdown');
                    const targetPartNo = '{{ $calculation->no_product ?? "" }}';
                    if (dropdown && targetPartNo) {
                        for (let i = 0; i < dropdown.options.length; i++) {
                            if (dropdown.options[i].getAttribute('data-item-no') === targetPartNo) {
                                dropdown.selectedIndex = i;
                                // Trigger change event so it updates detail section correctly
                                const event = new Event('change');
                                dropdown.dispatchEvent(event);
                                break;
                            }
                        }
                    }
                }
            }

            const btnNextStep1 = document.getElementById('btnNextStep1');
            if (btnNextStep1) btnNextStep1.disabled = false;

            // Populate Step 2 Fields
            const setVal = (id, val) => { const el = document.getElementById(id); if (el && val) el.value = val; };
            
            @php
                $ka = $calculation->konfigurasi_atas ?? [];
                if (is_string($ka)) $ka = json_decode($ka, true) ?? [];
                
                $kb = $calculation->konfigurasi_bawah ?? [];
                if (is_string($kb)) $kb = json_decode($kb, true) ?? [];
            @endphp
            
            setVal('s2_pkg_number', '{{ $calculation->packaging_number ?? "" }}');
            setVal('s2_length', '{{ $ka["panjang"] ?? $calculation->panjang ?? $calculation->length ?? "" }}');
            setVal('s2_width', '{{ $ka["lebar"] ?? $calculation->lebar ?? $calculation->width ?? "" }}');
            setVal('s2_height', '{{ $ka["tinggi"] ?? $calculation->tinggi ?? $calculation->height ?? "" }}');

            setVal('s2_pb_status', '{{ $kb["penyanggah"]["status"] ?? $calculation->bawah_penyangga_status ?? "Include" }}');
            setVal('s2_pb_material', '{{ $kb["penyanggah"]["material"] ?? $calculation->bawah_penyangga_material ?? "" }}');
            
            setVal('s2_ptb_status', '{{ $kb["penutup"]["status"] ?? $calculation->bawah_penutup_status ?? "Tanpa Penutup" }}');
            setVal('s2_ptb_arah', '{{ $kb["penutup"]["arah"] ?? $calculation->bawah_penutup_arah ?? "Horizontal" }}');
            setVal('s2_ptb_material', '{{ $kb["penutup"]["material"] ?? $calculation->bawah_penutup_material ?? "" }}');
            
            setVal('s2_kb_status', '{{ $kb["kaki_balok"]["status"] ?? $calculation->bawah_kakibalok_status ?? "Include" }}');
            setVal('s2_kb_arah', '{{ $kb["kaki_balok"]["arah"] ?? $calculation->bawah_kakibalok_arah ?? "Vertikal" }}');
            setVal('s2_kb_material', '{{ $kb["kaki_balok"]["material"] ?? $calculation->bawah_kakibalok_material ?? "" }}');

            setVal('s2_pa_status', '{{ $ka["penyanggah"]["status"] ?? $calculation->atas_penyangga_status ?? "Not Include" }}');
            setVal('s2_pa_arah', '{{ $ka["penyanggah"]["arah"] ?? $calculation->atas_penyangga_arah ?? "Vertikal" }}');
            setVal('s2_pa_material', '{{ $ka["penyanggah"]["material"] ?? $calculation->atas_penyangga_material ?? "" }}');
            setVal('s2_pta_status', '{{ $ka["penutup"]["status"] ?? $calculation->atas_penutup_status ?? "Tanpa Penutup" }}');
            setVal('s2_pta_arah', '{{ $ka["penutup"]["arah"] ?? $calculation->atas_penutup_arah ?? "Horizontal" }}');
            setVal('s2_pta_material', '{{ $ka["penutup"]["material"] ?? $calculation->atas_penutup_material ?? "" }}');

            setVal('s2_jarak', '{{ $kb["jarak_penyanggah"] ?? $calculation->jarak_penyanggah ?? 300 }}');
            setVal('s2_gap_atas', '{{ $ka["gap_atas"] ?? $calculation->gap_atas ?? 0 }}');
            setVal('s2_gap_bawah', '{{ $kb["gap_bawah"] ?? $calculation->gap_bawah ?? 10 }}');

            // Set Pack Type
            const packTypeInput = document.querySelector(`input[name="packType"][value="{{ $calculation->job->pack_type ?? 'Plywood' }}"]`);
            if(packTypeInput) packTypeInput.checked = true;

            // Show Step 1 modal (user can review SO info, then click Selanjutnya to go to Step 2)
            const step1ModalEl = document.getElementById('step1Modal');
            const step1Modal = bootstrap.Modal.getInstance(step1ModalEl) || new bootstrap.Modal(step1ModalEl);
            step1Modal.show();
        };

        // Submit wizard logic
        document.getElementById('btnSubmitWizard')?.addEventListener('click', async function(e) {
            e.preventDefault();
            
            let so_number = document.getElementById('searchSO')?.value?.trim() || '';
            if (!so_number || so_number === '-') {
                so_number = document.getElementById('infoNoSO')?.innerText?.trim() || '';
            }
            if (so_number === '-') so_number = '';

            const customer = document.getElementById('infoCustomer')?.innerText?.trim() || '';
            let date_delivery = document.getElementById('infoDeliveryDate')?.innerText?.trim() || '';
            const address = document.getElementById('infoShipto')?.innerText?.trim() || '';
            
            // Validation to avoid invalid date format like '-'
            if (date_delivery === '-') date_delivery = '';

            const packTypeEl = document.querySelector('input[name="packType"]:checked');
            const packType = packTypeEl ? packTypeEl.value : 'Plywood';
            
            let no_product = document.getElementById('detailPartNo')?.innerText?.trim() || '';
            if (no_product === '-') no_product = '';
            
            let desc_product = document.getElementById('detailDesc')?.innerText?.trim() || '';
            if (desc_product === '-') desc_product = '';
            
            const qty_kirim = document.getElementById('itemQtyKirim')?.value || 1;
            
            if (!so_number) {
                alert('Silakan cari dan pilih Sales Order terlebih dahulu pada Step 1.');
                return;
            }
            if (!no_product) {
                alert('Silakan pilih produk dari Sales Order pada Step 1.');
                return;
            }
            
            const btn = this;
            const originalText = btn.innerHTML;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Menyimpan...';
            btn.disabled = true;

            if (isEditingModal && editCalculationId) {
                const updatePayload = {
                    length: document.getElementById('s2_length')?.value || 0,
                    width: document.getElementById('s2_width')?.value || 0,
                    height: document.getElementById('s2_height')?.value || 0,
                    
                    konfigurasi_atas: {
                        penyanggah: {
                            status: document.getElementById('s2_pa_status')?.value || 'Include',
                            arah: document.getElementById('s2_pa_arah')?.value || 'Vertikal',
                            material: document.getElementById('s2_pa_material')?.value || 'A001'
                        },
                        penutup: {
                            status: document.getElementById('s2_pta_status')?.value || 'Tanpa Penutup',
                            arah: document.getElementById('s2_pta_arah')?.value || 'Horizontal',
                            material: document.getElementById('s2_pta_material')?.value || 'A001'
                        },
                        panjang: document.getElementById('s2_length')?.value || 0,
                        lebar: document.getElementById('s2_width')?.value || 0,
                        tinggi: document.getElementById('s2_height')?.value || 0,
                        gap_atas: document.getElementById('s2_gap_atas')?.value || 0,
                    },
                    konfigurasi_bawah: {
                        kaki_balok: {
                            status: document.getElementById('s2_kb_status')?.value || 'Include',
                            arah: document.getElementById('s2_kb_arah')?.value || 'Vertikal',
                            material: document.getElementById('s2_kb_material')?.value || 'A001'
                        },
                        penutup: {
                            status: document.getElementById('s2_ptb_status')?.value || 'Tanpa Penutup',
                            arah: document.getElementById('s2_ptb_arah')?.value || 'Horizontal',
                            material: document.getElementById('s2_ptb_material')?.value || 'A001'
                        },
                        penyanggah: {
                            status: document.getElementById('s2_pb_status')?.value || 'Include',
                            material: document.getElementById('s2_pb_material')?.value || 'A001'
                        },
                        jarak_penyanggah: document.getElementById('s2_jarak')?.value || 300,
                        gap_bawah: document.getElementById('s2_gap_bawah')?.value || 0,
                    },
                    
                    // Fallback backward compatibility just in case
                    include_pallet_base: 1,
                    bawah_penyangga_include: document.getElementById('s2_pb_status')?.value === 'Include' ? 1 : 0,
                    bawah_penutup_tipe: document.getElementById('s2_ptb_status')?.value || 'Tanpa Penutup',
                    atas_penyangga_include: document.getElementById('s2_pa_status')?.value === 'Include' ? 1 : 0,
                    atas_penutup_tipe: document.getElementById('s2_pta_status')?.value || 'Tanpa Penutup',
                    arah_pemasangan: document.getElementById('s2_ptb_arah')?.value || 'Horizontal',
                    jarak_penyanggah: document.getElementById('s2_jarak')?.value || 300,
                    gap_atas: document.getElementById('s2_gap_atas')?.value || 0,
                    gap_bawah: document.getElementById('s2_gap_bawah')?.value || 0,
                    _token: '{{ csrf_token() }}',
                    _method: 'PUT'
                };

                try {
                    const response = await fetch(`{{ url('/packaging/calc-update') }}/${editCalculationId}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify(updatePayload)
                    });
                    const result = await response.json();
                    
                    if (result.status === 'success') {
                        alert('Data berhasil diperbarui!');
                        window.location.reload();
                    } else {
                        alert('Error: ' + (result.message || 'Gagal menyimpan'));
                    }
                } catch (err) {
                    console.error(err);
                    alert('Terjadi kesalahan jaringan.');
                } finally {
                    btn.innerHTML = originalText;
                    btn.disabled = false;
                }
                return;
            }

            const payload = {
                _token: '{{ csrf_token() }}',
                no_so: so_number,
                customer: customer,
                date_delivery: date_delivery,
                address: address,
                packType: packType,
                items: [
                    {
                        no_product: no_product,
                        desc_product: desc_product,
                        qty_kirim: qty_kirim,
                        qty_pack: document.getElementById('s2_qty_pack')?.value || 1,
                        qty_per_pack: document.getElementById('s2_qty_per_pack')?.value || 1,
                        packer: document.getElementById('s2_packer')?.value || '',
                        
                        length: document.getElementById('s2_length')?.value || 0,
                        width: document.getElementById('s2_width')?.value || 0,
                        height: document.getElementById('s2_height')?.value || 0,
                        
                        jarak: document.getElementById('s2_jarak')?.value || 500,
                        gap_bawah: document.getElementById('s2_gap_bawah')?.value || 0,
                        gap_atas: document.getElementById('s2_gap_atas')?.value || 0,
                        
                        kb_status: document.getElementById('s2_kb_status')?.value || 'Include',
                        kb_arah: document.getElementById('s2_kb_arah')?.value || 'Horizontal',
                        kb_material: document.getElementById('s2_kb_material')?.value || '-',
                        
                        pb_status: document.getElementById('s2_pb_status')?.value || 'Include',
                        pb_arah: document.getElementById('s2_pb_arah')?.value || 'Horizontal',
                        pb_material: document.getElementById('s2_pb_material')?.value || '-',
                        
                        ptb_status: document.getElementById('s2_ptb_status')?.value || 'Tanpa Penutup',
                        ptb_arah: document.getElementById('s2_ptb_arah')?.value || '-',
                        ptb_material: document.getElementById('s2_ptb_material')?.value || '-',
                        
                        pa_status: document.getElementById('s2_pa_status')?.value || 'Not Include',
                        pa_arah: document.getElementById('s2_pa_arah')?.value || '-',
                        pa_material: document.getElementById('s2_pa_material')?.value || '-',
                        
                        pta_status: document.getElementById('s2_pta_status')?.value || 'Tanpa Penutup',
                        pta_arah: document.getElementById('s2_pta_arah')?.value || '-',
                        pta_material: document.getElementById('s2_pta_material')?.value || '-'
                    }
                ],
                raw_api_data: (typeof currentSOData !== 'undefined' && currentSOData) ? currentSOData : (window.raw_api_data || [])
            };

            fetch('{{ route("packaging.store") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify(payload)
            })
            .then(res => res.json())
            .then(data => {
                if(data.status === 'success' || data.redirect) {
                    window.location.href = data.redirect || '{{ route("packaging.index") }}';
                } else {
                    alert('Error: ' + (data.message || 'Gagal menyimpan'));
                    btn.innerHTML = originalText;
                    btn.disabled = false;
                }
            })
            .catch(err => {
                console.error(err);
                alert('Terjadi kesalahan koneksi saat menyimpan.');
                btn.innerHTML = originalText;
                btn.disabled = false;
            });
        });
    </script>
</x-app-layout>