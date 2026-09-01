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

</style>

    <div class="crate-page container-fluid py-4">
        <div class="page-header px-3 py-3 d-flex flex-col gap-3 flex-md-row align-items-md-center justify-content-md-between mb-3">
            <div class="d-flex align-items-center gap-3">
                <a href="{{ route('packaging.summary') }}" class="btn-soft px-3 py-2 rounded-3 shadow-none" style="border-color: #cbd5e1; height: 38px; width: 38px; display: inline-flex; align-items: center; justify-content: center;">
                    <span class="material-symbols-rounded text-lg" style="margin-left: 2px;">arrow_back</span>
                </a>
                <div>
                    <h1 class="h5 mb-0" style="color:#0f172a;font-weight:900;letter-spacing:-.01em; line-height: 1.2;">Calculation</h1>
                    <p class="mb-0 small text-secondary d-none d-md-block" style="font-size: 11px;">Halaman kalkulasi packaging crate untuk pengiriman barang berat.</p>
                </div>
            </div>
            <div class="d-flex flex-wrap align-items-center gap-2 justify-content-md-end">
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
                    <div class="card border-0 shadow-sm style-card-container">
                        <div class="card-header bg-white border-bottom-0 pt-3 pb-0 d-flex justify-content-between align-items-center">
                            <h6 class="fw-bold mb-0 text-navy d-flex align-items-center" style="font-size: 13px;">
                                <div class="bg-primary bg-opacity-10 p-1 rounded me-2 d-flex align-items-center justify-content-center text-primary">
                                    <span class="material-symbols-rounded" style="font-size: 16px;">inventory_2</span>
                                </div>
                                PACKING INFORMATION
                            </h6>
                            <span class="badge bg-primary text-primary bg-opacity-10 rounded-pill px-3 py-1 border border-primary border-opacity-25" style="font-size: 9px;"><i class="fas fa-circle me-1" style="font-size: 6px;"></i> DRAFT</span>
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
                                            <small class="text-muted d-block lh-1 mb-1" style="font-size: 11px;">Reference Number</small>
                                            <h5 class="fw-bold text-navy mb-0" style="font-size: 18px;">-</h5>
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
                                            <div class="fw-bold text-navy">-</div>
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
                                            <div class="fw-bold text-navy">-</div>
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
                                            <div class="fw-bold text-navy">-</div>
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
                                            <div class="fw-bold text-navy">-</div>
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
                                                    <div class="configuration-unit-field">
                                                        <input type="number" name="length" class="form-control form-control-sm border-0 p-0 shadow-none text-navy custom-input w-100" style="font-size: 8px; font-weight: 500;" placeholder="0" value="{{ isset($calculation) ? $calculation->length : '' }}" readonly>
                                                        <span class="configuration-unit text-muted">mm</span>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Lebar -->
                                            <div class="d-flex align-items-center px-3 py-2 group-item">
                                                <span class="material-symbols-rounded text-secondary me-3" style="font-size: 18px;">straighten</span>
                                                <div class="w-100">
                                                    <small class="text-muted d-block lh-1 mb-1" style="font-size: 8px;">Lebar</small>
                                                    <div class="configuration-unit-field">
                                                        <input type="number" name="width" class="form-control form-control-sm border-0 p-0 shadow-none text-navy custom-input w-100" style="font-size: 8px; font-weight: 500;" placeholder="0" value="{{ isset($calculation) ? $calculation->width : '' }}" readonly>
                                                        <span class="configuration-unit text-muted">mm</span>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Tinggi -->
                                            <div class="d-flex align-items-center px-3 py-2 group-item">
                                                <span class="material-symbols-rounded text-secondary me-3" style="font-size: 18px;">height</span>
                                                <div class="w-100">
                                                    <small class="text-muted d-block lh-1 mb-1" style="font-size: 8px;">Tinggi</small>
                                                    <div class="configuration-unit-field">
                                                        <input type="number" name="height" class="form-control form-control-sm border-0 p-0 shadow-none text-navy custom-input w-100" style="font-size: 8px; font-weight: 500;" placeholder="0" value="{{ isset($calculation) ? $calculation->height : '' }}" readonly>
                                                        <span class="configuration-unit text-muted">mm</span>
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
                                                <div class="configuration-cell">
                                                    <input type="hidden" name="bawah_penyangga_include" value="1">
                                                    <select class="form-select form-select-sm border-0 shadow-none matrix-select text-navy" style="pointer-events: none;" tabindex="-1" aria-label="Status Penyangga Bawah">
                                                        <option selected value="1">Include</option>
                                                    </select>
                                                </div>
                                                <div class="configuration-cell">
                                                    <select name="bawah_penyangga_arah" class="form-select form-select-sm border-0 shadow-none matrix-select master-arah text-navy" disabled>
                                                        <option value="Horizontal">Horizontal</option>
                                                    </select>
                                                </div>
                                                <div class="configuration-cell">
                                                    <select name="bawah_penyangga_material" class="form-select form-select-sm border-0 shadow-none matrix-select master-material text-navy" disabled>
                                                        <option value="" {{ !isset($calculation) ? 'selected' : '' }} disabled>-</option>
                                                        @foreach($materials->where('kategori', 'MASTER BALOK') as $mat)
                                                            <option value="{{ $mat->kode }}">{{ $mat->kode }} ({{ $mat->tebal }}x{{ $mat->lebar }})</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="configuration-matrix-row group-item">
                                                <div class="configuration-component">
                                                    <span class="material-symbols-rounded text-secondary" style="font-size: 16px;">grid_view</span>
                                                    <small class="text-muted">Penutup</small>
                                                </div>
                                                <div class="configuration-cell">
                                                    <select name="bawah_penutup_tipe" class="form-select form-select-sm border-0 shadow-none matrix-select master-include text-navy" disabled>
                                                        <option value="Tanpa Penutup">Tanpa Penutup</option>
                                                        <option value="Papan Setengah">Papan Setengah</option>
                                                        <option value="Papan Full">Papan Full</option>
                                                        <option value="Triplex">Triplex</option>
                                                    </select>
                                                </div>
                                                <div class="configuration-cell">
                                                    <select name="bawah_penutup_arah" class="form-select form-select-sm border-0 shadow-none matrix-select master-arah text-navy" disabled>
                                                        <option value="Horizontal">Horizontal</option>
                                                        <option value="Vertikal">Vertikal</option>
                                                    </select>
                                                </div>
                                                <div class="configuration-cell">
                                                    <select name="bawah_penutup_material" class="form-select form-select-sm border-0 shadow-none matrix-select master-material text-navy" disabled>
                                                        <option value="" {{ !isset($calculation) ? 'selected' : '' }} disabled>-</option>
                                                        @foreach($materials->where('kategori', 'MASTER TRIPLEKS') as $mat)
                                                            <option value="{{ $mat->kode }}">{{ $mat->kode }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="configuration-matrix-row group-item">
                                                <div class="configuration-component">
                                                    <span class="material-symbols-rounded text-secondary" style="font-size: 16px;">view_column_2</span>
                                                    <small class="text-muted">Kaki Balok</small>
                                                </div>
                                                <div class="configuration-cell">
                                                    <select name="include_pallet_base" class="form-select form-select-sm border-0 shadow-none matrix-select master-include text-navy" disabled>
                                                        <option value="1" {{ isset($calculation) && $calculation->include_pallet_base ? 'selected' : '' }}>Include</option>
                                                        <option value="0" {{ !isset($calculation) || !$calculation->include_pallet_base ? 'selected' : '' }}>Not Include</option>
                                                    </select>
                                                </div>
                                                <div class="configuration-cell">
                                                    <select name="bawah_kakibalok_arah" class="form-select form-select-sm border-0 shadow-none matrix-select master-arah text-navy" disabled>
                                                        <option value="Horizontal">Horizontal</option>
                                                        <option value="Vertikal">Vertikal</option>
                                                    </select>
                                                </div>
                                                <div class="configuration-cell">
                                                    <select name="bawah_kakibalok_material" class="form-select form-select-sm border-0 shadow-none matrix-select master-material text-navy" disabled>
                                                        <option value="" {{ !isset($calculation) ? 'selected' : '' }} disabled>-</option>
                                                        @foreach($materials->where('kategori', 'MASTER BALOK') as $mat)
                                                            <option value="{{ $mat->kode }}">{{ $mat->kode }} ({{ $mat->tebal }}x{{ $mat->lebar }})</option>
                                                        @endforeach
                                                    </select>
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
                                                <div class="configuration-cell">
                                                    <select name="atas_penyangga_include" class="form-select form-select-sm border-0 shadow-none matrix-select master-include text-navy" disabled>
                                                        <option value="1">Include</option>
                                                        <option value="0" {{ !isset($calculation) ? 'selected' : '' }}>Not Include</option>
                                                    </select>
                                                </div>
                                                <div class="configuration-cell">
                                                    <select name="atas_penyangga_arah" class="form-select form-select-sm border-0 shadow-none matrix-select master-arah text-navy" disabled>
                                                        <option value="Vertikal">Vertikal</option>
                                                    </select>
                                                </div>
                                                <div class="configuration-cell">
                                                    <select name="atas_penyangga_material" class="form-select form-select-sm border-0 shadow-none matrix-select master-material text-navy" disabled>
                                                        <option value="" {{ !isset($calculation) ? 'selected' : '' }} disabled>-</option>
                                                        @foreach($materials->where('kategori', 'MASTER BALOK') as $mat)
                                                            <option value="{{ $mat->kode }}">{{ $mat->kode }} ({{ $mat->tebal }}x{{ $mat->lebar }})</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="configuration-matrix-row group-item">
                                                <div class="configuration-component">
                                                    <span class="material-symbols-rounded text-secondary" style="font-size: 16px;">grid_view</span>
                                                    <small class="text-muted">Penutup</small>
                                                </div>
                                                <div class="configuration-cell">
                                                    <select name="atas_penutup_tipe" class="form-select form-select-sm border-0 shadow-none matrix-select master-include text-navy" disabled>
                                                        <option value="Tanpa Penutup">Tanpa Penutup</option>
                                                        <option value="Papan Setengah">Papan Setengah</option>
                                                        <option value="Papan Full">Papan Full</option>
                                                        <option value="Triplex">Triplex</option>
                                                    </select>
                                                </div>
                                                <div class="configuration-cell">
                                                    <select name="atas_penutup_arah" class="form-select form-select-sm border-0 shadow-none matrix-select master-arah text-navy" disabled>
                                                        <option value="Horizontal">Horizontal</option>
                                                        <option value="Vertikal">Vertikal</option>
                                                    </select>
                                                </div>
                                                <div class="configuration-cell">
                                                    <select name="atas_penutup_material" class="form-select form-select-sm border-0 shadow-none matrix-select master-material text-navy" disabled>
                                                        <option value="" {{ !isset($calculation) ? 'selected' : '' }} disabled>-</option>
                                                        @foreach($materials->where('kategori', 'MASTER TRIPLEKS') as $mat)
                                                            <option value="{{ $mat->kode }}">{{ $mat->kode }}</option>
                                                        @endforeach
                                                    </select>
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
                                                    <div class="configuration-unit-field">
                                                        <input type="number" name="distance_between_pillars" class="form-control form-control-sm border-0 p-0 shadow-none text-navy custom-input w-100" style="font-size: 13px; font-weight: 500;" placeholder="0" value="{{ isset($calculation) ? $calculation->distance_between_pillars : '300' }}" readonly>
                                                        <span class="configuration-unit text-muted">mm</span>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Celah Atas -->
                                            <div class="d-flex align-items-center px-3 py-2 group-item">
                                                <span class="material-symbols-rounded text-secondary me-3" style="font-size: 18px;">vertical_align_top</span>
                                                <div class="w-100">
                                                    <small class="text-muted d-block lh-1 mb-1" style="font-size: 13px;">Celah Atas</small>
                                                    <div class="configuration-unit-field">
                                                        <input type="number" name="gap_atas" class="form-control form-control-sm border-0 p-0 shadow-none text-navy custom-input w-100" style="font-size: 13px; font-weight: 500;" placeholder="0" value="{{ isset($calculation) ? ($calculation->gap_atas ?? $calculation->gap) : '10' }}" readonly>
                                                        <span class="configuration-unit text-muted">mm</span>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Celah Bawah -->
                                            <div class="d-flex align-items-center px-3 py-2 group-item">
                                                <span class="material-symbols-rounded text-secondary me-3" style="font-size: 18px;">vertical_align_bottom</span>
                                                <div class="w-100">
                                                    <small class="text-muted d-block lh-1 mb-1" style="font-size: 13px;">Celah Bawah</small>
                                                    <div class="configuration-unit-field">
                                                        <input type="number" name="gap_bawah" class="form-control form-control-sm border-0 p-0 shadow-none text-navy custom-input w-100" style="font-size: 13px; font-weight: 500;" placeholder="0" value="{{ isset($calculation) ? ($calculation->gap_bawah ?? $calculation->gap) : '10' }}" readonly>
                                                        <span class="configuration-unit text-muted">mm</span>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Jumlah Kaki Balok -->
                                            <div class="d-flex align-items-center px-3 py-2 group-item" id="jarak_balok_additional_wrapper" style="{{ (isset($calculation) && !$calculation->include_pallet_base) ? 'opacity: 0.5; pointer-events: none; background-color: #f8f9fa;' : '' }}">
                                                <span class="material-symbols-rounded text-secondary me-3" style="font-size: 18px;">view_agenda</span>
                                                <div class="w-100">
                                                    <small class="text-muted d-block lh-1 mb-1" style="font-size: 13px;">Jumlah Kaki Balok</small>
                                                    <div class="configuration-unit-field">
                                                        @php
                                                            $kakiBalokVal = 2;
                                                            if(isset($calculation)) {
                                                                $bawahKakiBalok = $calculation->details->where('section', 'Bawah')->whereIn('part_name', ['Kaki Balok', 'Additional Balok'])->first();
                                                                if ($bawahKakiBalok) {
                                                                    $kakiBalokVal = $bawahKakiBalok->quantity;
                                                                } else {
                                                                    $kakiBalokVal = max(2, (int)floor($calculation->length / 800) + 1);
                                                                }
                                                                if (!$calculation->include_pallet_base) {
                                                                    $kakiBalokVal = '-';
                                                                }
                                                            }
                                                        @endphp
                                                        <input type="text" name="jumlah_kaki_balok" class="form-control form-control-sm border-0 p-0 shadow-none text-navy custom-input w-100" style="font-size: 13px; font-weight: 500;" placeholder="0" value="{{ $kakiBalokVal }}" readonly>
                                                        <span class="configuration-unit text-muted">pcs</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <!-- Action / Edit Toggle -->
                    <div class="mt-2 d-flex justify-content-end gap-2 px-0">
                        <button type="button" id="btn-edit-config" class="btn btn-navy py-2 px-4 shadow-sm rounded-pill d-flex align-items-center gap-2 fw-bold text-white justify-content-center" style="font-size: 13px; min-width: 180px; transition: all 0.2s;">
                            <span class="material-symbols-rounded" style="font-size: 18px;">edit</span>
                            Edit Konfigurasi
                        </button>
                        <button type="button" id="btn-cancel-config" class="btn btn-light py-2 px-4 shadow-sm rounded-pill d-none align-items-center gap-2 fw-bold text-dark border justify-content-center" style="font-size: 13px; background-color: #fff; min-width: 120px;">
                            <span class="material-symbols-rounded" style="font-size: 18px;">close</span>
                            Batal
                        </button>
                        <button type="submit" form="form-edit-config" id="btn-save-config" class="btn btn-success py-2 px-4 shadow-sm rounded-pill d-none align-items-center gap-2 fw-bold text-white justify-content-center" style="font-size: 13px; background-color: #10b981; border: none; min-width: 120px;">
                            <span class="material-symbols-rounded" style="font-size: 18px;">save</span>
                            Simpan
                        </button>
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
                                            <div class="d-flex align-items-center gap-3 overflow-hidden pe-2">
                                                <span class="badge text-white fw-bold py-2 px-3" style="font-size: 13px; background-color: var(--navy);">{{ $mat['kode'] }}</span>
                                                <span class="text-dark fw-bold text-truncate" style="font-size: 14px;" title="{{ $mat['nama'] }}">{{ $mat['nama'] }}</span>
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
                    <div class="card-header bg-white border-bottom-0 pt-4 pb-3 px-4 d-flex flex-wrap justify-content-between align-items-center gap-3">
                        <div class="d-flex align-items-center gap-3">
                            <div class="bg-primary bg-opacity-10 text-primary rounded-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                                <span class="material-symbols-rounded fs-3">view_in_ar</span>
                            </div>
                            <div>
                                <h5 class="mb-0 fw-bold">Visualisasi Potongan Material</h5>
                                <small class="text-muted">Preview potongan material dalam skala 3D</small>
                            </div>
                        </div>
                        <div class="d-flex gap-2 align-items-center">
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
                    @include('packaging_calc.partials._table-material')
                </div>
            </div>
        </div>
    </div>

    @include('packaging_calc.partials._cost_resume_modal')


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

        // Global variables for 3D Visualizer
        let scene, camera, renderer, controls, woodTexture, frameMaterial, supportMaterial, coverMaterial, plywoodMaterial;
        let modelGroup, dimensionGroup, ground, grid;
        let nailModel = null;
        let dimensionsVisible = true;
        let currentView = 'iso';
        let currentMaxDimension = 2;
        let activeDetails = [];
        const calcId = "{{ $calculation->id ?? 'new' }}";

        function init3D() {
            try {
                const container = document.getElementById('crate-canvas-container');
                const canvas = document.getElementById('crate-canvas');
                const loading = document.getElementById('canvas-loading');

                if (!container || !canvas) return;

                // Create scene
                scene = new THREE.Scene();
                scene.background = new THREE.Color(0xf3f7fc);

                // Groups
                modelGroup = new THREE.Group();
                dimensionGroup = new THREE.Group();
                scene.add(modelGroup);
                scene.add(dimensionGroup);

                // Camera setup
                camera = new THREE.PerspectiveCamera(42, container.clientWidth / container.clientHeight, 0.1, 1000);
                camera.position.set(3, 3, 4);

                // Renderer setup
                renderer = new THREE.WebGLRenderer({ canvas: canvas, antialias: true, alpha: false, preserveDrawingBuffer: true });
                renderer.setSize(container.clientWidth, container.clientHeight);
                renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));
                renderer.shadowMap.enabled = true;
                renderer.shadowMap.type = THREE.PCFSoftShadowMap;

                // Load Nail GLB Model
                const gltfLoader = new THREE.GLTFLoader();
                gltfLoader.load('{{ asset("glb-3D/cc0_-_nail.glb") }}', function(gltf) {
                    nailModel = gltf.scene;
                    
                    // The GLB has two nails (Nail and NailRust) and they are tilted.
                    // Keep only 'Nail' (silver) and make it perfectly straight.
                    nailModel.traverse((child) => {
                        if (child.name.includes('NailRust')) {
                            child.visible = false;
                        } else if (child.name === 'Nail') {
                            child.position.set(0, 0, 0);
                            // Rotate the nail so its local Z axis (length) aligns with the Y axis (UP)
                            child.rotation.set(-Math.PI / 2, 0, 0);
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
                grid = new THREE.GridHelper(40, 40, 0xaab7c8, 0xd5dde8);
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
                    const observer = new ResizeObserver(() => {
                        onWindowResize();
                    });
                    observer.observe(container);
                }
                window.addEventListener('resize', onWindowResize);

                // Animation loop
                function animate() {
                    requestAnimationFrame(animate);
                    controls.update();
                    
                    if (camera && controls && typeof dimensionGroup !== 'undefined') {
                        const dist = camera.position.distanceTo(controls.target);
                        const maxD = typeof currentMaxDimension !== 'undefined' ? currentMaxDimension : 2;
                        const baseDist = Math.max(1.5, maxD * 1.5);
                        // Make scale proportional to distance so it maintains a readable size on screen
                        const scaleFactor = dist / baseDist;
                        dimensionGroup.children.forEach(child => {
                            if (child.isSprite && child.userData.baseScale) {
                                child.scale.copy(child.userData.baseScale).multiplyScalar(scaleFactor);
                            }
                        });
                    }
                    
                    renderer.render(scene, camera);
                }
                animate();
            } catch (err) {
                console.error("Error inside init3D:", err);
                showVisualizerError(err);
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
                let nx = mesh.position.x, ny = mesh.position.y, nz = mesh.position.z;
                
                if (axis === 'x') nx += off;
                else if (axis === 'y') ny += off;
                else nz += off;

                let thk = Math.min(w, h, d);
                
                if (faceId === 'top') {
                    ny += thk/2; 
                } else if (faceId === 'bottom') {
                    ny -= thk/2;
                    n.rotation.x = Math.PI; 
                } else if (faceId === 'front') {
                    nz += thk/2;
                    n.rotation.x = Math.PI/2;
                } else if (faceId === 'back') {
                    nz -= thk/2;
                    n.rotation.x = -Math.PI/2;
                } else if (faceId === 'right') {
                    nx += thk/2;
                    n.rotation.z = -Math.PI/2;
                } else if (faceId === 'left') {
                    nx -= thk/2;
                    n.rotation.z = Math.PI/2;
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
                } else if (faceId === 'bottom') {
                    ny -= thk/2;
                    n.rotation.x = Math.PI; 
                } else if (faceId === 'front') {
                    nz += thk/2;
                    n.rotation.x = Math.PI/2;
                } else if (faceId === 'back') {
                    nz -= thk/2;
                    n.rotation.x = -Math.PI/2;
                } else if (faceId === 'right') {
                    nx += thk/2;
                    n.rotation.z = -Math.PI/2;
                } else if (faceId === 'left') {
                    nx -= thk/2;
                    n.rotation.z = Math.PI/2;
                }
                
                if (mesh.visible === false) n.visible = false;
                
                n.position.set(nx, ny, nz);
                modelGroup.add(n);
            });
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
                const maxSpacing = parseFloat(document.querySelector('input[name="distance_between_pillars"]').value) || 500;
                const arahGlobal = document.querySelector('select[name="arah_pemasangan"]')?.value || 'Horizontal';
                const celahAtas = parseFloat(document.querySelector('input[name="gap_atas"]').value) || 0;
                const celahBawah = parseFloat(document.querySelector('input[name="gap_bawah"]').value) || 0;

                if (P <= 0 || L <= 0 || T <= 0) return;

                const bawahPenutupTipe = document.querySelector('select[name="bawah_penutup_tipe"]')?.value || 'Tanpa Penutup';
                const atasPenutupTipe = document.querySelector('select[name="atas_penutup_tipe"]')?.value || 'Tanpa Penutup';
                const hasBawahPenutup = (bawahPenutupTipe !== 'Tanpa Penutup' && bawahPenutupTipe !== 'Tidak makai penutup' && bawahPenutupTipe !== 'Tidak Pakai Papan' && bawahPenutupTipe !== '');
                const hasAtasPenutup = (atasPenutupTipe !== 'Tanpa Penutup' && atasPenutupTipe !== 'Tidak makai penutup' && atasPenutupTipe !== 'Tidak Pakai Papan' && atasPenutupTipe !== '');
                const hasCover = hasBawahPenutup || hasAtasPenutup;

                const includePallet = document.querySelector('select[name="include_pallet_base"]')?.value;
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
                            const baseY = (hasBawahPenutup ? -t_penutup_bawah : 0);
                            const y = baseY - (pDim.t / 2) - offP;
                            
                            let t_penutup_samping_p = 0;
                            if (typeof hasCover !== 'undefined' && hasCover) {
                                const pSamping = typeof activeDetails !== 'undefined' ? activeDetails.find(d => d.section === 'Penutup' && (d.part_name === 'Kanan' || d.part_name === 'Kiri')) : null;
                                t_penutup_samping_p = pSamping && pSamping.material_kode !== '-' ? parseFloat(pSamping.calculated_thickness) / 1000 : 0.02;
                            }
                            const outer_l = l_m + (typeof hasCover !== 'undefined' && hasCover ? t_penutup_samping_p * 2 : 0);
                            const outer_w = w_m + (typeof hasCover !== 'undefined' && hasCover ? t_penutup_samping_p * 2 : 0);

                            if (face.orientation === 'H') {
                                positions = evenlySpaced(count, -outer_w / 2 + pDim.hw, outer_w / 2 - pDim.hw);
                                positions.forEach(function (z) {
                                    let bMesh = addBeam(new THREE.Vector3(outer_l, pDim.t, pDim.w), new THREE.Vector3(0, y, z), supportMaterial, face.name + ' arah panjang');
                                    addNailsForPenyangga(bMesh, face.id);
                                });
                            } else {
                                positions = evenlySpaced(count, -outer_l / 2 + pDim.hw, outer_l / 2 - pDim.hw);
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
                                    let customPositions = [];
                                    let customSizes = [];
                                    let countPeny = parseInt(cBawah.qty);
                                    let langkahPeny = 1;
                                    let halfPeny = Math.floor(countPeny / 2);
                                    let t_py_w = 0.05;
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
                                    let coversPerSpace = Math.floor((forcedCount - 2) / spaces);
                                    
                                    for(let i = 0; i < spaces; i++) {
                                        let leftPenyCenter = penyPositions[i];
                                        let rightPenyCenter = penyPositions[i+1];
                                        
                                        let spaceWidth = rightPenyCenter - leftPenyCenter - t_py_w;
                                        
                                        let totalCoversWidth = (coversPerSpace * safeWidth) + ((coversPerSpace - 1) * gap);
                                        let sideMargin = (spaceWidth - totalCoversWidth) / 2;
                                        
                                        let currentPos = leftPenyCenter + (t_py_w / 2) + sideMargin + (safeWidth / 2);
                                        for(let j=0; j<coversPerSpace; j++) {
                                            customPositions.push(currentPos);
                                            customSizes.push(safeWidth);
                                            currentPos += safeWidth + gap;
                                        }
                                    }
                                    return { count: customPositions.length, pieceCross: safeWidth, positions: customPositions, sizes: customSizes };
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
                            if (face.id === 'front' || face.id === 'back') return 'H'; // Paksa selalu horizontal
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
                                faceArah = 'H'; // Paksa selalu horizontal
                                crossSpan = h_m - topKakiBalokY_c;
                                longSpan = l_m;
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
                            const layout = makeBoardLayout(crossSpan, w_penutup, gapM, isFull, forcedCount);

                            layout.positions.forEach(function (pos, index) {
                                const pSize = layout.sizes ? layout.sizes[index] : layout.pieceCross;
                                if (face.id === 'front' || face.id === 'back') {
                                    const z = face.id === 'front' ? w_m / 2 + t_penutup / 2 + offC : -w_m / 2 - t_penutup / 2 - offC;
                                    const frontLength = l_m;
                                    let bMesh = addBeam(new THREE.Vector3(frontLength, pSize, t_penutup), new THREE.Vector3(0, topKakiBalokY_c + (h_m - topKakiBalokY_c) / 2 + pos, z), material, face.name + ' papan horizontal');
                                    addNailsForBoard(bMesh, face.id);
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
                                    const y = face.id === 'top' ? h_m + t_penyangga_atas_c + t_penutup / 2 + offC : -t_penutup / 2 - offC;
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
                        const permukaanBawahPenyangga = (hasBawahPenutup ? -t_penutup_bawah : 0) - t_penyangga_bawah - offsetBalokY;
                        const y = permukaanBawahPenyangga - (balok_w / 2);
                        
                        // Kaki Balok disesuaikan agar sejajar presisi dengan panjang/lebar Rangka Bawah (termasuk penutup)
                        const isVert = direction === 'Vertikal';
                        
                        const outer_l = l_m + (typeof hasCover !== 'undefined' && hasCover ? t_penutup_samping * 2 : 0);
                        const outer_w = w_m + (typeof hasCover !== 'undefined' && hasCover ? t_penutup_samping * 2 : 0);
                        
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
            
            let bawahPenutupTipe = document.querySelector('select[name="bawah_penutup_tipe"]');
            let atasPenutupTipe = document.querySelector('select[name="atas_penutup_tipe"]');
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

            window.pakuDetails = {!! json_encode(isset($calculation) && $calculation->consumables->where('category', 'Paku')->isNotEmpty() ? $calculation->consumables->where('category', 'Paku')->map(function($c) {
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

                        if (!materialResume[matKode]) {
                            materialResume[matKode] = {
                                kode: matKode,
                                nama: detail.material_nama || matKode,
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
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="d-flex align-items-center gap-3 overflow-hidden pe-2">
                                        <span class="badge text-white fw-bold py-2 px-3" style="font-size: 13px; background-color: var(--navy);">${mat.kode}</span>
                                        <span class="text-dark fw-bold text-truncate" style="font-size: 14px;" title="${mat.nama}">${mat.nama}</span>
                                    </div>
                                    <div class="text-end flex-shrink-0">
                                        <span class="fw-extrabold text-success" style="font-size: 18px;">${formatNumber(mat.length, 1)}</span>
                                        <span class="text-muted fw-bold ms-1" style="font-size: 14px;">${unit}</span>
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

                const updatePart = (section, partMatches, includeVal, arahVal, materialVal, excludeBawah = false, tipePenutupVal = null) => {
                    let partsToMatch = Array.isArray(partMatches) ? partMatches : [partMatches];
                    
                    partsToMatch.forEach(partName => {
                        let matchFound = false;
                        activeDetails.forEach(d => {
                            if (d.section === section && d.part_name === partName && (excludeBawah ? !d.part_name.includes('Bawah') : true)) {
                                matchFound = true;
                                if (includeVal === "0" || !materialVal) {
                                    d.material_kode = '-';
                                } else {
                                    d.material_kode = materialVal;
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
                updatePart('Penyangga', ['Atas', 'Kanan', 'Kiri', 'Depan', 'Belakang'], getVal('atas_penyangga_include'), getVal('atas_penyangga_arah'), getVal('atas_penyangga_material'), true);
                updatePart('Penutup', ['Atas', 'Kanan', 'Kiri', 'Depan', 'Belakang'], getPenutupInclude('atas_penutup_tipe'), getVal('atas_penutup_arah'), getVal('atas_penutup_material'), true, getVal('atas_penutup_tipe'));

                // Area Bawah

                updatePart('Bawah', ['Penyangga'], getVal('bawah_penyangga_include'), getVal('bawah_penyangga_arah'), getVal('bawah_penyangga_material'), false);
                updatePart('Bawah', ['Penutup'], getPenutupInclude('bawah_penutup_tipe'), getVal('bawah_penutup_arah'), getVal('bawah_penutup_material'), false, getVal('bawah_penutup_tipe'));
                updatePart('Bawah', ['Kaki Balok'], getVal('include_pallet_base'), getVal('bawah_kakibalok_arah'), getVal('bawah_kakibalok_material'), false);

                // Update Jumlah Kaki Balok logic in UI
                let lengthInput = document.querySelector('[name="length"]');
                let jumlahKakiInput = document.querySelector('[name="jumlah_kaki_balok"]');
                if (lengthInput && jumlahKakiInput) {
                    let L = parseFloat(lengthInput.value) || 0;
                    jumlahKakiInput.value = Math.max(2, Math.floor(L / 800) + 1);
                }
            }

            let debounceTimer = null;
            function runSimulation() {
                if (!isEditModeActive) return;
                
                if (debounceTimer) clearTimeout(debounceTimer);
                
                debounceTimer = setTimeout(() => {
                    syncMasterDropdownsToActiveDetails();
                    
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
                    
                    const kakibalokInc = document.querySelector('select[name="include_pallet_base"]');
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
            
            const bawahKakibalokInclude = document.querySelector('select[name="include_pallet_base"]');
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

            function updateTotalPackCost() {
                let costRangka = parseFloat(document.getElementById('cost-rangka').innerText.replace(/[^0-9]/g, '')) || 0;
                let costPenutup = parseFloat(document.getElementById('cost-penutup').innerText.replace(/[^0-9]/g, '')) || 0;
                let costBawah = parseFloat(document.getElementById('cost-bawah').innerText.replace(/[^0-9]/g, '')) || 0;
                let costManpower = parseFloat(document.getElementById('cost-manpower').innerText.replace(/[^0-9]/g, '')) || 0;
                let costPaku = parseFloat(document.getElementById('cost-paku').innerText.replace(/[^0-9]/g, '')) || 0;
                
                let grandTotal = costRangka + costPenutup + costBawah + costManpower + costPaku;
                
                let totalCostEl = document.getElementById('cost-total');
                if (totalCostEl) totalCostEl.innerText = formatRupiah(grandTotal);
                
                let resumeTotalCostEl = document.getElementById('cost-total-packing-resume');
                if (resumeTotalCostEl) resumeTotalCostEl.innerText = formatRupiah(grandTotal);
            }

            // Initial calculation on page load
            calculateManpower();
            calculateNails();


            function calculateNails() {
                // UPDATE pakuDetails dynamically based on activeDetails before calculation
                let coverSelect = document.querySelector('select[name="atas_penutup_tipe"]'); // Fallback to atas for global checking if needed
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

                    let kodeBadge = r.kode !== '-' ? `<span class="badge bg-amber-100 text-amber-800 border border-amber-200 px-2 py-1 rounded-pill" style="font-size:10px;">${r.kode}</span>` : `<span class="text-muted small">-</span>`;
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
                        runSimulation();
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

                        runSimulation();
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
                    
                    const atasPenutupTipe = document.querySelector('select[name="atas_penutup_tipe"]')?.value || 'Tanpa Penutup';
                    const isHasAtasPenutup = (atasPenutupTipe !== 'Tanpa Penutup' && atasPenutupTipe !== 'Tidak makai penutup' && atasPenutupTipe !== 'Tidak Pakai Papan' && atasPenutupTipe !== '') ? '1' : '0';
                    const bawahPenutupTipe = document.querySelector('select[name="bawah_penutup_tipe"]')?.value || 'Tanpa Penutup';
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

            if (btnEdit && btnCancel && btnSave && formEdit) {
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
                
                if (isNewRecord) {
                    storeInitialValues();
                    toggleEdit(true);
                }
                
                btnEdit.addEventListener('click', function() {
                    storeInitialValues();
                    toggleEdit(true);
                });
                
                btnCancel.addEventListener('click', function() {
                    window.location.reload();
                });
                
                // --- Dynamic Penutup Options ---
                const masterPapan = @json($materials->where('kategori', 'MASTER PAPAN')->values());
                const masterTripleks = @json($materials->where('kategori', 'MASTER TRIPLEKS')->values());
                
                
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

                initDropdownsFromActiveDetails();

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
                            const incPallet = document.querySelector('select[name="include_pallet_base"]')?.value;
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
                let matScene, matCamera, matCameraGroup, matRenderer;
                let matContainer = document.getElementById('material-sorting-container');
                let matLabels = document.getElementById('material-labels-container');
                let matCamX = 0; // target X for scrolling
                let matMaxCamX = 0; // max X scroll
                let matMeshes = [];

                function initMaterialVisualizer() {
                    if (!matContainer) return;
                    
                    matScene = new THREE.Scene();
                    matScene.background = new THREE.Color(0xf8fafc);

                    const aspect = matContainer.clientWidth / matContainer.clientHeight;
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

                    matRenderer = new THREE.WebGLRenderer({ antialias: true, preserveDrawingBuffer: true });
                    window.matRenderer = matRenderer;
                    window.matScene = matScene;
                    window.matCamera = matCamera;
                    
                    matRenderer.setSize(matContainer.clientWidth, matContainer.clientHeight);
                    matRenderer.shadowMap.enabled = true;
                    matRenderer.shadowMap.type = THREE.PCFSoftShadowMap;
                    matContainer.appendChild(matRenderer.domElement);

                    // Add Lights
                    const ambientLight = new THREE.AmbientLight(0xffffff, 0.7);
                    matScene.add(ambientLight);
                    
                    const dirLight = new THREE.DirectionalLight(0xffffff, 0.5);
                    dirLight.position.set(5, 10, 7);
                    dirLight.castShadow = true;
                    dirLight.shadow.mapSize.width = 1024;
                    dirLight.shadow.mapSize.height = 1024;
                    matScene.add(dirLight);

                    // Grid helper for the floor
                    const gridHelper = new THREE.GridHelper(100, 100, 0xe2e8f0, 0xf1f5f9);
                    gridHelper.position.y = 0;
                    matScene.add(gridHelper);
                    
                    // Add an invisible plane to receive shadows
                    const planeGeom = new THREE.PlaneGeometry(100, 100);
                    const planeMat = new THREE.ShadowMaterial({ opacity: 0.1 });
                    const plane = new THREE.Mesh(planeGeom, planeMat);
                    plane.rotation.x = -Math.PI / 2;
                    plane.receiveShadow = true;
                    matScene.add(plane);

                    // Controls for buttons
                    document.getElementById('btn-mat-left')?.addEventListener('click', () => {
                        matCamX = Math.max(0, matCamX - 3); // scroll left
                    });
                    document.getElementById('btn-mat-right')?.addEventListener('click', () => {
                        matCamX = Math.min(matMaxCamX, matCamX + 3); // scroll right
                    });

                    // Start render loop
                    requestAnimationFrame(animateMat);

                    window.addEventListener('resize', () => {
                        if (!matContainer) return;
                        const aspect = matContainer.clientWidth / matContainer.clientHeight;
                        matCamera.left = window.matFrustumSize * aspect / -2;
                        matCamera.right = window.matFrustumSize * aspect / 2;
                        matCamera.top = window.matFrustumSize / 2;
                        matCamera.bottom = window.matFrustumSize / -2;
                        matCamera.updateProjectionMatrix();
                        matRenderer.setSize(matContainer.clientWidth, matContainer.clientHeight);
                    });
                }

                function animateMat() {
                    requestAnimationFrame(animateMat);
                    if (matRenderer && matScene && matCamera && matCameraGroup) {
                        // Decoupled camera logic:
                        // 1. Pan left/right smoothly via the group position
                        matCameraGroup.position.x += (matCamX - matCameraGroup.position.x) * 0.05;
                        
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
                    if (!matScene || !activeDetails) return;
                    
                    // Clear old meshes (keep initial lights, grid, and plane)
                    while(matScene.children.length > 4){ 
                        matScene.remove(matScene.children[4]); 
                    }
                    matLabels.innerHTML = '';
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
                        const mat = d.material_kode || d.material_nama || 'Kayu';
                        
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
                        const mat = new THREE.LineBasicMaterial({ color: 0x3b82f6 }); // Primary blue line
                        const line = new THREE.Line(geo, mat);
                        matScene.add(line);
                    
                        const mid = new THREE.Vector3().addVectors(p1, p2).multiplyScalar(0.5);

                        const label = document.createElement('div');
                        label.className = 'fw-bold rounded shadow-sm';
                        label.style.position = 'absolute';
                        label.style.fontSize = '10px';
                        label.style.color = '#2563eb'; // Blue text
                        label.style.background = '#ffffff';
                        label.style.padding = '1px 4px';
                        label.innerHTML = text;
                        matLabels.appendChild(label);
                        
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
                    const spacingGroup = Math.max(0.2, maxL_m * 0.05); // Moderate gap between groups
                    const spacingItem = Math.max(0.015, maxL_m * 0.01); // Moderate gap between items
                    
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
                        matNameLabel.innerHTML = `<div class="fw-bold text-dark" style="font-size: 11px;">${group.mat}</div><div class="badge bg-primary text-white mt-1" style="font-size: 10px;">Qty: ${group.qty}</div>`;
                        matLabels.appendChild(matNameLabel);

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
                
                // Initialize on load
                initMaterialVisualizer();
                if (typeof window.drawMaterialStacks === 'function') {
                    window.drawMaterialStacks();
                }

            }
        });
    </script>
</x-app-layout>
