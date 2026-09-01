@php
    /*
    |--------------------------------------------------------------------------
    | Initial Product Setup Data
    |--------------------------------------------------------------------------
    */
    $initialSO = '';
    $initialProduct = '';
    $initialCustomer = '-';
    $initialDelivery = '-';
    $initialAddress = '-';
    $initialDesc = '-';
    $initialQtyOrder = '-';
    $initialQtyRemaining = '-';

    $hasJob = isset($calculation);
    $jobItems = [];

    if ($hasJob) {
        $items = $calculation->items()->get();

        foreach ($items as $item) {
            $jobItems[] = [
                'soNumber' => $item->no_so ?? '',
                'customer' => $item->customer ?? '-',
                'itemNumber' => $item->no_product ?? '',
                'description' => $item->desc_product ?? '-',
                'qty' => $item->qty ?? 0,
            ];
        }
    }

    $productSetupInitialData = [
        'hasJob' => $hasJob,
        'items' => $jobItems,

        'packagingNumber' => $calculation->packaging_number ?? 'PKG-AUTO-001',
        'packerId' => $calculation->packer_id ?? auth()->id(),
        'qtyPacking' => $calculation->qty_packaging ?? 1,
        'deliveryDate' => $calculation->completion_date ?? '',
        'typePackaging' => $calculation->type_packaging ?? 'Box',
        'dimensions' => [
            'length' => $calculation->panjang ?? '',
            'width' => $calculation->lebar ?? '',
            'height' => $calculation->tinggi ?? '',
        ],
        'cartonMaterial' => $calculation->carton_material ?? '',
        'terpalMaterial' => $calculation->terpal_material ?? '',
        'additional' => [
            'topGap' => $calculation->gap_atas ?? '',
            'bottomGap' => $calculation->gap_bawah ?? '',
            'supportSpacingAtas' => $calculation->jarak_penyanggah_atas ?? 300,
            'supportSpacingBawah' => $calculation->jarak_penyanggah_bawah ?? 300,
        ],
        'bottom' => [
            'support' => [
                'usage' => $calculation->bawah_penyanggah_status ?? 'Include',
                'direction' => $calculation->bawah_penyanggah_arahpemasangan ?? 'Horizontal',
                'material' => $calculation->bawah_penyanggah_material ?? '',
            ],
            'cover' => [
                'usage' => $calculation->bawah_penutup_status ?? 'Tanpa Penutup',
                'direction' => $calculation->bawah_penutup_arahpemasangan ?? 'Horizontal',
                'material' => $calculation->bawah_penutup_material ?? '',
            ],
            'blockFeet' => [
                'usage' => $calculation->bawah_kakibalok_status ?? 'Exclude',
                'direction' => $calculation->bawah_kakibalok_arahpemasangan ?? 'Vertikal',
                'material' => $calculation->bawah_kakibalok_material ?? '',
            ],
        ],
        'top' => [
            'support' => [
                'usage' => $calculation->atas_penyanggah_status ?? 'Include',
                'direction' => $calculation->atas_penyanggah_arahpemasangan ?? 'Vertikal',
                'material' => $calculation->atas_penyanggah_material ?? '',
            ],
            'cover' => [
                'usage' => $calculation->atas_penutup_status ?? 'Tanpa Penutup',
                'direction' => $calculation->atas_penutup_arahpemasangan ?? 'Horizontal',
                'material' => $calculation->atas_penutup_material ?? '',
            ],
        ],
    ];
@endphp

<style>
/* =========================================================
       PRODUCT SETUP MODAL
       Scope seluruh style ke #productSetupModal agar tidak
       mengganggu halaman atau modal lain.
       ========================================================= */

    #productSetupModal {
        --ps-primary: #ea580c;
        --ps-primary-dark: #c2410c;
        --ps-primary-rgb: 234, 88, 12;
        --ps-navy: #162033;
        --ps-text: #1e293b;
        --ps-muted: #64748b;
        --ps-bg: #f4f7fb;
        --ps-card: #ffffff;
        --ps-soft: #f8fafc;
        --ps-border: #e2e8f0;
        --ps-border-strong: #cbd5e1;
        --ps-success: #15803d;
        --ps-danger: #dc2626;
        --ps-shadow:
            0 30px 80px rgba(15, 23, 42, .24),
            0 8px 28px rgba(15, 23, 42, .12);
    }

    [data-bs-theme="dark"] #productSetupModal,
    body.dark-mode #productSetupModal {
        --ps-navy: #f8fafc;
        --ps-text: #e5edf7;
        --ps-muted: #94a3b8;
        --ps-bg: #0f172a;
        --ps-card: #111c2c;
        --ps-soft: #172337;
        --ps-border: rgba(148, 163, 184, .18);
        --ps-border-strong: rgba(148, 163, 184, .32);
        --ps-shadow:
            0 32px 90px rgba(0, 0, 0, .55),
            0 8px 28px rgba(0, 0, 0, .32);
    }

    #productSetupModal .modal-dialog {
        width: calc(100% - 28px);
        max-width: 1120px;
        margin: 14px auto;
    }

    #productSetupModal .modal-content {
        max-height: calc(100vh - 28px);
        overflow: hidden;
        border: 1px solid rgba(255, 255, 255, .72) !important;
        border-radius: 18px;
        background: var(--ps-card);
        box-shadow: var(--ps-shadow) !important;
    }

    /* Header */

    #productSetupModal .ps-modal-header {
        min-height: 72px;
        padding: 14px 18px;
        border-bottom: 1px solid var(--ps-border);
        background:
            radial-gradient(
                circle at 88% 0,
                rgba(var(--ps-primary-rgb), .10),
                transparent 26%
            ),
            var(--ps-card);
    }

    #productSetupModal .ps-title-wrap {
        display: flex;
        align-items: center;
        gap: 12px;
        min-width: 0;
    }

    #productSetupModal .ps-title-icon {
        width: 40px;
        height: 40px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex: 0 0 40px;
        border: 1px solid rgba(var(--ps-primary-rgb), .16);
        border-radius: 12px;
        color: var(--ps-primary);
        background: rgba(var(--ps-primary-rgb), .08);
    }

    #productSetupModal .ps-title-icon i {
        font-size: 17px;
    }

    #productSetupModal .ps-modal-title {
        margin: 0;
        color: var(--ps-navy);
        font-size: 17px;
        font-weight: 850;
        letter-spacing: -.02em;
    }

    #productSetupModal .ps-modal-subtitle {
        margin: 3px 0 0;
        color: var(--ps-muted);
        font-size: 11px;
        line-height: 1.4;
    }

    #productSetupModal .btn-close {
        width: 34px;
        height: 34px;
        margin: 0;
        padding: 0;
        border-radius: 50%;
        background-size: 11px;
        opacity: .58;
    }

    #productSetupModal .btn-close:hover {
        opacity: 1;
        background-color: var(--ps-soft);
    }

    /* =========================================================
       WIZARD INDICATOR
       ========================================================= */

    #productSetupModal .ps-wizard {
        display: flex;
        align-items: center;
        min-width: 390px;
    }

    #productSetupModal .ps-wizard-step {
        display: flex;
        align-items: center;
        gap: 8px;
        color: var(--ps-muted);
    }

    #productSetupModal .ps-wizard-number {
        width: 28px;
        height: 28px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: 1px solid var(--ps-border-strong);
        border-radius: 50%;
        background: var(--ps-card);
        font-size: 10px;
        font-weight: 800;
    }

    #productSetupModal .ps-wizard-label {
        font-size: 10px;
        font-weight: 700;
        white-space: nowrap;
    }

    #productSetupModal .ps-wizard-line {
        height: 2px;
        width: 30px;
        flex: 0 0 30px;
        margin: 0 10px;
        background: var(--ps-border);
    }

    #productSetupModal .ps-wizard-step.is-active,
    #productSetupModal .ps-wizard-step.is-complete {
        color: var(--ps-primary);
    }

    #productSetupModal .ps-wizard-step.is-active .ps-wizard-number,
    #productSetupModal .ps-wizard-step.is-complete .ps-wizard-number {
        border-color: var(--ps-primary);
        color: #ffffff;
        background: var(--ps-primary);
        box-shadow: 0 5px 12px rgba(var(--ps-primary-rgb), .20);
    }

    #productSetupModal .ps-wizard-line.is-complete {
        background: var(--ps-primary);
    }

    /* Body */

    #productSetupModal .ps-modal-body {
        max-height: calc(100vh - 154px);
        overflow-y: auto;
        padding: 14px;
        background:
            linear-gradient(
                180deg,
                rgba(var(--ps-primary-rgb), .025),
                transparent 190px
            ),
            var(--ps-bg);
    }

    #productSetupModal .ps-step-panel {
        display: none;
    }

    #productSetupModal .ps-step-panel.is-active {
        display: block;
        animation: psFadeIn .18s ease;
    }

    @keyframes psFadeIn {
        from {
            opacity: 0;
            transform: translateY(4px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    #productSetupModal .ps-layout {
        display: grid;
        grid-template-columns: minmax(0, .92fr) minmax(0, 1.08fr);
        gap: 12px;
    }

    #productSetupModal .ps-column {
        display: flex;
        flex-direction: column;
        gap: 12px;
        min-width: 0;
    }

    #productSetupModal .ps-card {
        overflow: hidden;
        border: 1px solid var(--ps-border);
        border-radius: 14px;
        background: var(--ps-card);
        box-shadow: 0 5px 16px rgba(15, 23, 42, .045);
    }

    #productSetupModal .ps-card-header {
        min-height: 51px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding: 10px 13px;
        border-bottom: 1px solid var(--ps-border);
    }

    #productSetupModal .ps-card-heading {
        display: flex;
        align-items: center;
        gap: 10px;
        min-width: 0;
    }

    #productSetupModal .ps-card-icon {
        width: 31px;
        height: 31px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex: 0 0 31px;
        border-radius: 9px;
        color: var(--ps-primary);
        background: rgba(var(--ps-primary-rgb), .08);
    }

    #productSetupModal .ps-card-title {
        margin: 0;
        color: var(--ps-text);
        font-size: 12px;
        font-weight: 850;
    }

    #productSetupModal .ps-card-description {
        margin: 2px 0 0;
        color: var(--ps-muted);
        font-size: 9px;
        line-height: 1.35;
    }

    #productSetupModal .ps-card-body {
        padding: 12px;
    }

    /* Search controls */

    #productSetupModal .ps-field {
        margin-bottom: 12px;
    }

    #productSetupModal .ps-field:last-child {
        margin-bottom: 0;
    }

    #productSetupModal .ps-label {
        display: flex;
        align-items: center;
        gap: 6px;
        margin-bottom: 6px;
        color: var(--ps-text);
        font-size: 10px;
        font-weight: 800;
    }

    #productSetupModal .ps-label i {
        width: 14px;
        color: var(--ps-primary);
        text-align: center;
    }

    #productSetupModal .ps-required {
        color: var(--ps-danger);
    }

    #productSetupModal .ps-search-row {
        display: grid;
        grid-template-columns: minmax(0, 1fr) auto;
        gap: 8px;
    }

    #productSetupModal .ps-input-wrap {
        position: relative;
    }

    #productSetupModal .ps-input-icon {
        position: absolute;
        top: 50%;
        left: 12px;
        z-index: 2;
        color: var(--ps-muted);
        font-size: 13px;
        transform: translateY(-50%);
        pointer-events: none;
    }

    #productSetupModal .ps-control {
        width: 100%;
        height: 42px;
        min-height: 42px;
        border: 1px solid var(--ps-border-strong);
        border-radius: 10px;
        color: var(--ps-text);
        background-color: var(--ps-soft);
        font-size: 11px;
        font-weight: 600;
        box-shadow: none;
        transition:
            border-color .18s ease,
            box-shadow .18s ease,
            background-color .18s ease;
    }

    #productSetupModal input.ps-control {
        padding: 8px 11px;
    }

    #productSetupModal .ps-input-wrap .ps-control {
        padding-left: 35px;
    }

    #productSetupModal select.ps-control {
        padding: 8px 34px 8px 11px;
        cursor: pointer;
    }

    #productSetupModal .ps-control::placeholder {
        color: var(--ps-muted);
        opacity: .68;
    }

    #productSetupModal .ps-control:focus {
        border-color: rgba(var(--ps-primary-rgb), .55);
        outline: none;
        background-color: var(--ps-card);
        box-shadow: 0 0 0 3px rgba(var(--ps-primary-rgb), .10);
    }

    #productSetupModal .ps-control:disabled {
        cursor: not-allowed;
        opacity: .68;
    }

    #productSetupModal .ps-fetch-button {
        height: 42px;
        min-width: 118px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 7px;
        border: 0;
        border-radius: 10px;
        color: #ffffff;
        background: linear-gradient(
            135deg,
            var(--ps-primary),
            #fb923c
        );
        font-size: 10px;
        font-weight: 850;
        box-shadow: 0 7px 16px rgba(var(--ps-primary-rgb), .22);
        transition:
            transform .18s ease,
            box-shadow .18s ease;
    }

    #productSetupModal .ps-fetch-button:hover {
        color: #ffffff;
        transform: translateY(-1px);
        box-shadow: 0 10px 21px rgba(var(--ps-primary-rgb), .28);
    }

    #productSetupModal .ps-fetch-button:disabled {
        cursor: wait;
        opacity: .75;
        transform: none;
    }

    #productSetupModal .ps-helper {
        display: flex;
        align-items: flex-start;
        gap: 8px;
        margin-top: 9px;
        padding: 8px 9px;
        border: 1px dashed rgba(var(--ps-primary-rgb), .18);
        border-radius: 9px;
        color: var(--ps-muted);
        background: rgba(var(--ps-primary-rgb), .035);
        font-size: 9px;
        line-height: 1.45;
    }

    #productSetupModal .ps-helper i {
        margin-top: 2px;
        color: var(--ps-primary);
    }

    /* Detail rows */

    #productSetupModal .ps-detail-list {
        overflow: hidden;
        border: 1px solid var(--ps-border);
        border-radius: 10px;
    }

    #productSetupModal .ps-detail-row {
        display: grid;
        grid-template-columns: 154px minmax(0, 1fr);
        min-height: 42px;
        border-bottom: 1px solid var(--ps-border);
    }

    #productSetupModal .ps-detail-row:last-child {
        border-bottom: 0;
    }

    #productSetupModal .ps-detail-label {
        display: flex;
        align-items: center;
        gap: 7px;
        padding: 8px 10px;
        border-right: 1px solid var(--ps-border);
        color: var(--ps-muted);
        background: var(--ps-soft);
        font-size: 9px;
        font-weight: 800;
    }

    #productSetupModal .ps-detail-label i {
        width: 13px;
        color: var(--ps-primary);
        text-align: center;
    }

    #productSetupModal .ps-detail-value {
        min-width: 0;
        display: flex;
        align-items: center;
        padding: 8px 10px;
        color: var(--ps-text);
        font-size: 10px;
        font-weight: 700;
        line-height: 1.45;
        overflow-wrap: anywhere;
    }

    #productSetupModal .ps-detail-row:hover .ps-detail-value {
        background: rgba(var(--ps-primary-rgb), .025);
    }

    #productSetupModal .ps-copy-button {
        width: 29px;
        height: 29px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex: 0 0 29px;
        border: 1px solid var(--ps-border);
        border-radius: 8px;
        color: var(--ps-muted);
        background: var(--ps-soft);
    }

    #productSetupModal .ps-copy-button:hover {
        border-color: rgba(var(--ps-primary-rgb), .30);
        color: var(--ps-primary);
        background: rgba(var(--ps-primary-rgb), .06);
    }

    #productSetupModal .ps-remaining-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 4px 8px;
        border: 1px solid rgba(var(--ps-primary-rgb), .16);
        border-radius: 999px;
        color: var(--ps-primary);
        background: rgba(var(--ps-primary-rgb), .08);
        font-size: 9px;
        font-weight: 800;
    }

    #productSetupModal .font-monospace {
        word-break: break-all;
    }

    /* Step 2 */

    #productSetupModal .ps-empty-step {
        min-height: 360px;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 28px;
        border: 1px dashed var(--ps-border-strong);
        border-radius: 14px;
        background: var(--ps-card);
        text-align: center;
    }

    #productSetupModal .ps-empty-icon {
        width: 70px;
        height: 70px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 14px;
        border: 1px solid rgba(var(--ps-primary-rgb), .14);
        border-radius: 20px;
        color: var(--ps-primary);
        background: rgba(var(--ps-primary-rgb), .07);
        font-size: 25px;
    }

    #productSetupModal .ps-empty-title {
        margin: 0 0 5px;
        color: var(--ps-text);
        font-size: 14px;
        font-weight: 850;
    }

    #productSetupModal .ps-empty-text {
        max-width: 420px;
        margin: 0 auto;
        color: var(--ps-muted);
        font-size: 10px;
        line-height: 1.6;
    }

    /* Footer */

    #productSetupModal .ps-modal-footer {
        min-height: 66px;
        padding: 11px 16px;
        border-top: 1px solid var(--ps-border);
        background: var(--ps-card);
    }

    #productSetupModal .ps-footer-status {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        color: var(--ps-muted);
        font-size: 9px;
        font-weight: 700;
    }

    #productSetupModal .ps-footer-status-dot {
        width: 7px;
        height: 7px;
        border-radius: 50%;
        background: #22c55e;
        box-shadow: 0 0 0 4px rgba(34, 197, 94, .10);
    }

    #productSetupModal .ps-button {
        height: 38px;
        min-width: 96px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 7px;
        border-radius: 9px;
        font-size: 10px;
        font-weight: 850;
    }

    #productSetupModal .ps-button-secondary {
        border: 1px solid var(--ps-border-strong);
        color: var(--ps-text);
        background: var(--ps-card);
    }

    #productSetupModal .ps-button-secondary:hover {
        background: var(--ps-soft);
    }

    #productSetupModal .ps-button-primary {
        min-width: 128px;
        border: 1px solid var(--ps-primary);
        color: #ffffff;
        background: linear-gradient(
            135deg,
            var(--ps-primary),
            #f97316
        );
        box-shadow: 0 7px 16px rgba(var(--ps-primary-rgb), .20);
    }

    #productSetupModal .ps-button-primary:hover {
        color: #ffffff;
        background: linear-gradient(
            135deg,
            var(--ps-primary-dark),
            var(--ps-primary)
        );
        box-shadow: 0 10px 20px rgba(var(--ps-primary-rgb), .26);
        transform: translateY(-1px);
    }

    #productSetupModal .ps-spinner {
        width: 14px;
        height: 14px;
        display: inline-block;
        border: 2px solid rgba(255, 255, 255, .40);
        border-top-color: #ffffff;
        border-radius: 50%;
        animation: psSpin .7s linear infinite;
    }

    @keyframes psSpin {
        to {
            transform: rotate(360deg);
        }
    }

    #productSetupModal .ps-modal-body::-webkit-scrollbar {
        width: 7px;
    }

    #productSetupModal .ps-modal-body::-webkit-scrollbar-thumb {
        border: 2px solid transparent;
        border-radius: 999px;
        background: #cbd5e1;
        background-clip: padding-box;
    }

    @media (max-width: 991.98px) {
        #productSetupModal .ps-layout {
            grid-template-columns: 1fr;
        }

        #productSetupModal .ps-wizard {
            display: none;
        }
    }

    @media (max-width: 575.98px) {
        #productSetupModal .modal-dialog {
            width: calc(100% - 12px);
            margin: 6px auto;
        }

        #productSetupModal .modal-content {
            max-height: calc(100vh - 12px);
            border-radius: 13px;
        }

        #productSetupModal .ps-modal-header {
            min-height: 62px;
            padding: 10px 12px;
        }

        #productSetupModal .ps-title-icon {
            width: 35px;
            height: 35px;
            flex-basis: 35px;
        }

        #productSetupModal .ps-modal-title {
            font-size: 14px;
        }

        #productSetupModal .ps-modal-subtitle {
            display: none;
        }

        #productSetupModal .ps-modal-body {
            max-height: calc(100vh - 132px);
            padding: 9px;
        }

        #productSetupModal .ps-search-row {
            grid-template-columns: 1fr;
        }

        #productSetupModal .ps-fetch-button {
            width: 100%;
        }

        #productSetupModal .ps-detail-row {
            grid-template-columns: 1fr;
        }

        #productSetupModal .ps-detail-label {
            border-right: 0;
            border-bottom: 1px solid var(--ps-border);
        }

        #productSetupModal .ps-footer-status {
            display: none;
        }

        #productSetupModal .ps-modal-footer {
            padding: 9px;
        }

        #productSetupModal .ps-footer-actions {
            width: 100%;
        }

        #productSetupModal .ps-button {
            flex: 1 1 0;
        }
    }

    /* =========================================================
       STEP 1 — BOOTSTRAP LAYOUT OVERRIDE
       Ubah bagian ini saja bila ingin mengatur jarak/layout.
       ========================================================= */

    #productSetupModal .ps-search-card .ps-card-body {
        padding: 14px;
    }

    #productSetupModal .ps-search-card .ps-fetch-button {
        min-width: 0;
    }

    #productSetupModal .ps-info-row > [class*="col-"] {
        min-width: 0;
    }

    #productSetupModal .ps-info-row .ps-card {
        height: 100%;
    }

    #productSetupModal .ps-info-row .ps-card-body {
        flex: 1 1 auto;
    }

    #productSetupModal .ps-info-row .ps-detail-list {
        height: 100%;
    }


    /* =========================================================
       STEP 1 — PRODUCT TABLE
       Ukuran font disamakan dengan komponen modal lainnya.
       ========================================================= */

    #productSetupModal .ps-table {
        width: 100%;
        margin-bottom: 0;
        color: var(--ps-text);
        border-color: var(--ps-border);
    }

    #productSetupModal .ps-table > :not(caption) > * > * {
        border-color: var(--ps-border);
        box-shadow: none;
    }

    #productSetupModal .ps-table thead th {
        padding: 9px 11px;
        vertical-align: middle;
        border-top: 0;
        border-bottom: 1px solid var(--ps-border);
        color: var(--ps-muted);
        background: var(--ps-soft);
        font-size: 10px;
        font-weight: 800;
        line-height: 1.25;
        letter-spacing: .2px;
        text-transform: uppercase;
        white-space: nowrap;
    }

    #productSetupModal .ps-table tbody td {
        padding: 9px 11px;
        vertical-align: middle;
        color: var(--ps-text);
        background: var(--ps-card);
        font-size: 10px;
        font-weight: 600;
        line-height: 1.4;
    }

    #productSetupModal .ps-table tbody tr:hover td {
        background: rgba(var(--ps-primary-rgb), .03);
    }

    #productSetupModal .ps-table .row-qty {
        width: 76px !important;
        min-height: 30px;
        padding: 4px 7px;
        border: 1px solid var(--ps-border-strong);
        border-radius: 7px;
        color: var(--ps-text);
        background: var(--ps-card);
        font-size: 10px;
        font-weight: 700;
        text-align: center;
        box-shadow: none;
    }

    #productSetupModal .ps-table .row-qty:focus {
        border-color: var(--ps-primary);
        outline: 0;
        box-shadow: 0 0 0 3px rgba(var(--ps-primary-rgb), .10);
    }

    #productSetupModal .ps-table .btn-remove-item {
        width: 28px;
        height: 28px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0 !important;
        border-radius: 7px;
        font-size: 10px;
        line-height: 1;
    }

    #productSetupModal .ps-table .btn-remove-item i {
        font-size: 9px;
    }

    #productSetupModal .ps-table tbody:empty::after {
        display: table-row;
        height: 46px;
        color: var(--ps-muted);
        font-size: 10px;
        text-align: center;
        content: "Belum ada barang yang dipilih";
    }

    #productSetupModal .ps-table tbody:empty::before {
        display: none;
    }

    @media (min-width: 992px) {
        #productSetupModal .ps-search-card .row {
            --bs-gutter-x: 12px;
        }
    }

    @media (max-width: 991.98px) {
        #productSetupModal .ps-search-card .ps-fetch-button {
            width: 100%;
        }
    }

/* =========================================================
       STEP 3 — KONFIGURASI PACKAGING
       ========================================================= */

    #step-2 {
        --s2-primary: #ea580c;
        --s2-primary-dark: #c2410c;
        --s2-primary-soft: #fff7ed;
        --s2-primary-rgb: 234, 88, 12;

        --s2-card: #ffffff;
        --s2-soft: #f8fafc;
        --s2-text: #172033;
        --s2-muted: #64748b;
        --s2-border: #e2e8f0;
        --s2-shadow: 0 12px 34px rgba(15, 23, 42, .07);

        color: var(--s2-text);
        padding: 3px;
    }

    [data-bs-theme="dark"] #step-2,
    body.dark-mode #step-2 {
        --s2-card: #111827;
        --s2-soft: #172033;
        --s2-text: #f1f5f9;
        --s2-muted: #94a3b8;
        --s2-border: rgba(148, 163, 184, .18);
        --s2-primary-soft: rgba(234, 88, 12, .12);
        --s2-shadow: 0 14px 38px rgba(0, 0, 0, .32);
    }

    #step-2 .s2-shell {
        overflow: hidden;
        background: var(--s2-card);
        border: 1px solid var(--s2-border);
        border-radius: 18px;
        box-shadow: var(--s2-shadow);
    }

    /* =========================================================
       DATA UTAMA
       ========================================================= */
    #step-2 .s2-main-data {
        padding: .9rem;
        background:
            linear-gradient(
                180deg,
                rgba(var(--s2-primary-rgb), .035),
                transparent
            ),
            var(--s2-soft);
        border-bottom: 1px solid var(--s2-border);
    }

    #step-2 .s2-main-data-title {
        display: flex;
        align-items: center;
        gap: .55rem;
        margin-bottom: .75rem;
        color: var(--s2-text);
        font-size: .78rem;
        font-weight: 800;
    }

    #step-2 .s2-main-data-title-icon {
        width: 30px;
        height: 30px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: var(--s2-primary);
        background: var(--s2-primary-soft);
        border: 1px solid rgba(var(--s2-primary-rgb), .16);
        border-radius: 9px;
    }

    #step-2 .s2-main-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: .65rem;
    }

    #step-2 .s2-field-card {
        position: relative;
        min-width: 0;
        padding: .72rem;
        background: var(--s2-card);
        border: 1px solid var(--s2-border);
        border-radius: 12px;
        transition:
            border-color .18s ease,
            box-shadow .18s ease,
            transform .18s ease;
    }

    #step-2 .s2-field-card:hover {
        transform: translateY(-1px);
        border-color: rgba(var(--s2-primary-rgb), .22);
        box-shadow: 0 7px 18px rgba(15, 23, 42, .05);
    }

    #step-2 .s2-label {
        display: flex;
        align-items: center;
        gap: .4rem;
        margin-bottom: .4rem;
        color: var(--s2-muted);
        font-size: .64rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: .35px;
    }

    #step-2 .s2-label i {
        color: var(--s2-primary);
    }

    /* =========================================================
       LAYOUT 4 / 8
       ========================================================= */
    #step-2 .s2-row {
        display: grid;
        grid-template-columns: minmax(0, 4fr) minmax(0, 8fr);
        align-items: stretch;
        border-bottom: 1px solid var(--s2-border);
    }

    #step-2 .s2-row:last-child {
        border-bottom: 0;
    }

    #step-2 .s2-panel {
        min-width: 0;
        background: var(--s2-card);
    }

    #step-2 .s2-panel:first-child {
        border-right: 1px solid var(--s2-border);
    }

    #step-2 .s2-panel-head {
        display: flex;
        align-items: center;
        gap: .65rem;
        min-height: 48px;
        padding: .72rem .85rem;
        background:
            linear-gradient(
                90deg,
                rgba(var(--s2-primary-rgb), .035),
                transparent
            ),
            var(--s2-soft);
        border-bottom: 1px solid var(--s2-border);
    }

    #step-2 .s2-panel-number {
        width: 25px;
        height: 25px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex: 0 0 25px;
        color: #fff;
        background: linear-gradient(
            135deg,
            var(--s2-primary-dark),
            var(--s2-primary)
        );
        border-radius: 7px;
        font-size: .68rem;
        font-weight: 800;
        box-shadow: 0 4px 10px rgba(var(--s2-primary-rgb), .20);
    }

    #step-2 .s2-panel-icon {
        width: 28px;
        height: 28px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex: 0 0 28px;
        color: var(--s2-primary);
        background: var(--s2-primary-soft);
        border: 1px solid rgba(var(--s2-primary-rgb), .12);
        border-radius: 8px;
        font-size: .68rem;
    }

    #step-2 .s2-panel-title {
        margin: 0;
        color: var(--s2-text);
        font-size: .78rem;
        font-weight: 800;
    }

    #step-2 .s2-panel-body {
        padding: .85rem;
        overflow: hidden;
        transition: max-height .3s ease, padding .3s ease, opacity .2s ease;
    }

    #step-2 .s2-panel-body.is-collapsed {
        max-height: 0 !important;
        padding-top: 0 !important;
        padding-bottom: 0 !important;
        opacity: 0;
        border-top: none;
    }

    #step-2 .s2-panel-head[data-toggle-panel] {
        cursor: pointer;
        user-select: none;
        transition: background .15s ease;
    }

    #step-2 .s2-panel-head[data-toggle-panel]:hover {
        background:
            linear-gradient(
                90deg,
                rgba(var(--s2-primary-rgb), .07),
                transparent
            ),
            var(--s2-soft);
    }

    #step-2 .s2-panel-toggle {
        margin-left: auto;
        width: 24px;
        height: 24px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex: 0 0 24px;
        color: var(--s2-muted);
        border-radius: 6px;
        font-size: .7rem;
        transition: transform .3s ease, background .15s ease;
    }

    #step-2 .s2-panel-head[data-toggle-panel]:hover .s2-panel-toggle {
        background: rgba(var(--s2-primary-rgb), .08);
        color: var(--s2-primary);
    }

    #step-2 .s2-panel-toggle.is-collapsed {
        transform: rotate(180deg);
    }

    /* Carton Capacity Bar */
    #step-2 .carton-capacity-wrap {
        margin-top: 10px;
        padding: 10px 12px;
        border-radius: 10px;
        border: 1px solid var(--s2-border);
        background: var(--s2-soft);
    }

    #step-2 .carton-capacity-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 6px;
    }

    #step-2 .carton-capacity-label {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: .72rem;
        font-weight: 700;
        color: var(--s2-text);
    }

    #step-2 .carton-capacity-label i {
        color: var(--s2-primary);
    }

    #step-2 .carton-capacity-pct {
        font-size: .75rem;
        font-weight: 800;
        padding: 2px 8px;
        border-radius: 6px;
    }

    #step-2 .carton-capacity-bar {
        height: 10px;
        border-radius: 99px;
        background: rgba(var(--s2-primary-rgb), .1);
        overflow: hidden;
    }

    #step-2 .carton-capacity-fill {
        height: 100%;
        border-radius: 99px;
        transition: width .4s ease, background .3s ease;
        min-width: 0;
    }

    #step-2 .carton-capacity-fill.is-ok {
        background: linear-gradient(90deg, #22c55e, #16a34a);
    }

    #step-2 .carton-capacity-fill.is-warn {
        background: linear-gradient(90deg, #f59e0b, #d97706);
    }

    #step-2 .carton-capacity-fill.is-over {
        background: linear-gradient(90deg, #ef4444, #dc2626);
    }

    #step-2 .carton-capacity-info {
        margin-top: 4px;
        font-size: .68rem;
        font-weight: 600;
    }

    /* =========================================================
       DIMENSI + KONFIGURASI SEDERHANA
       ========================================================= */
    #step-2 .s2-compact-stack {
        display: grid;
        gap: .65rem;
    }

    #step-2 .s2-compact-field {
        display: grid;
        grid-template-columns: 105px minmax(0, 1fr) 30px;
        align-items: center;
        gap: .55rem;
        padding: .58rem .65rem;
        background: var(--s2-soft);
        border: 1px solid var(--s2-border);
        border-radius: 11px;
        transition:
            border-color .18s ease,
            background-color .18s ease;
    }

    #step-2 .s2-compact-field:hover {
        background: rgba(var(--s2-primary-rgb), .025);
        border-color: rgba(var(--s2-primary-rgb), .18);
    }

    #step-2 .s2-compact-label {
        display: flex;
        align-items: center;
        gap: .45rem;
        margin: 0;
        color: var(--s2-text);
        font-size: .71rem;
        font-weight: 750;
    }

    #step-2 .s2-compact-icon {
        width: 25px;
        height: 25px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex: 0 0 25px;
        color: var(--s2-primary);
        background: var(--s2-primary-soft);
        border-radius: 7px;
        font-size: .62rem;
    }

    #step-2 .s2-unit {
        color: var(--s2-muted);
        font-size: .68rem;
        font-weight: 700;
        text-align: right;
    }

    /* =========================================================
       TABEL KOMPONEN
       ========================================================= */
    #step-2 .s2-component-table {
        display: grid;
        gap: .45rem;
    }

    #step-2 .s2-component-head {
        display: grid;
        grid-template-columns: 1.2fr 0.8fr 2px 125px 1fr 1.15fr 1fr;
        gap: .55rem;
        padding: 0 .55rem .35rem;
        color: var(--s2-primary);
        font-size: .59rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: .32px;
    }

    #step-2 .s2-component-row {
        display: grid;
        grid-template-columns: 1.2fr 0.8fr 2px 125px 1fr 1.15fr 1fr;
        gap: .55rem;
        align-items: center;
        padding: .52rem;
        background: var(--s2-soft);
        border: 1px solid var(--s2-border);
        border-radius: 11px;
        transition:
            border-color .18s ease,
            background-color .18s ease,
            transform .18s ease;
    }

    #step-2 .s2-component-row:hover {
        transform: translateX(2px);
        background: rgba(var(--s2-primary-rgb), .025);
        border-color: rgba(var(--s2-primary-rgb), .20);
    }

    #step-2 .s2-component-name {
        display: flex;
        align-items: center;
        gap: .5rem;
        min-width: 0;
        color: var(--s2-text);
        font-size: .71rem;
        font-weight: 800;
    }

    #step-2 .s2-mini-icon {
        width: 27px;
        height: 27px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex: 0 0 27px;
        color: var(--s2-primary);
        background: var(--s2-primary-soft);
        border: 1px solid rgba(var(--s2-primary-rgb), .10);
        border-radius: 8px;
        font-size: .64rem;
    }

    #step-2 .s2-v-divider {
        width: 2px;
        height: 100%;
        background-color: var(--s2-border);
        border-radius: 2px;
        opacity: 0.7;
    }

    /* =========================================================
       INPUT
       ========================================================= */
    #step-2 .form-control,
    #step-2 .form-select {
        min-height: 36px;
        color: var(--s2-text) !important;
        background-color: var(--s2-card) !important;
        border: 1px solid var(--s2-border) !important;
        border-radius: 8px;
        box-shadow: none !important;
        font-size: .72rem;
        transition:
            border-color .18s ease,
            box-shadow .18s ease;
    }

    #step-2 .form-control:hover,
    #step-2 .form-select:hover {
        border-color: rgba(var(--s2-primary-rgb), .24) !important;
    }

    #step-2 .form-control:focus,
    #step-2 .form-select:focus {
        border-color: var(--s2-primary) !important;
        box-shadow: 0 0 0 3px rgba(var(--s2-primary-rgb), .10) !important;
    }

    #step-2 .form-control:disabled {
        color: var(--s2-muted) !important;
        background: var(--s2-soft) !important;
        opacity: .84;
    }

    /* =========================================================
       FOOTER NOTE
       ========================================================= */
    #step-2 .s2-note {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: .8rem;
        flex-wrap: wrap;
        padding: .72rem .9rem;
        color: var(--s2-muted);
        background: var(--s2-soft);
        border-top: 1px solid var(--s2-border);
        font-size: .68rem;
    }

    #step-2 .s2-note i {
        color: var(--s2-primary);
    }

    /* =========================================================
       RESPONSIVE
       ========================================================= */
    @media (max-width: 1199.98px) {
        #step-2 .s2-main-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        #step-2 .s2-component-head,
        #step-2 .s2-component-row {
            grid-template-columns: 1fr 1fr 2px 110px 1fr 1fr 1fr;
        }
    }

    @media (max-width: 767.98px) {
        #step-2 .s2-main-grid,
        #step-2 .s2-row {
            grid-template-columns: 1fr;
        }

        #step-2 .s2-panel:first-child {
            border-right: 0;
            border-bottom: 1px solid var(--s2-border);
        }

        #step-2 .s2-component-head {
            display: none;
        }

        #step-2 .s2-component-row {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 575.98px) {
        #step-2 .s2-compact-field {
            grid-template-columns: 1fr;
        }

        #step-2 .s2-unit {
            text-align: left;
        }
    }

/* =========================================================
       STEP 2 — PACKING TYPE
       ========================================================= */
    #packingTypeStep {
        --pt-primary: #ea580c;
        --pt-primary-dark: #c2410c;
        --pt-primary-rgb: 234, 88, 12;
        --pt-text: #172033;
        --pt-muted: #64748b;
        --pt-card: #ffffff;
        --pt-soft: #f8fafc;
        --pt-border: #e2e8f0;
        --pt-border-strong: #cbd5e1;
        color: var(--pt-text);
    }

    [data-bs-theme="dark"] #packingTypeStep,
    body.dark-mode #packingTypeStep {
        --pt-text: #f1f5f9;
        --pt-muted: #94a3b8;
        --pt-card: #111827;
        --pt-soft: #172033;
        --pt-border: rgba(148, 163, 184, .18);
        --pt-border-strong: rgba(148, 163, 184, .32);
    }

    #packingTypeStep .pt-shell {
        overflow: hidden;
        border: 1px solid var(--pt-border);
        border-radius: 16px;
        background: var(--pt-card);
        box-shadow: 0 8px 26px rgba(15, 23, 42, .06);
    }

    #packingTypeStep .pt-header {
        display: flex;
        align-items: center;
        gap: 12px;
        min-height: 74px;
        padding: 14px 16px;
        border-bottom: 1px solid var(--pt-border);
        background: linear-gradient(90deg, rgba(var(--pt-primary-rgb), .045), transparent), var(--pt-card);
    }

    #packingTypeStep .pt-header-icon {
        width: 42px;
        height: 42px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex: 0 0 42px;
        border: 1px solid rgba(var(--pt-primary-rgb), .18);
        border-radius: 12px;
        color: var(--pt-primary);
        background: rgba(var(--pt-primary-rgb), .07);
        font-size: 18px;
    }

    #packingTypeStep .pt-header-title {
        margin: 0;
        color: var(--pt-text);
        font-size: 15px;
        font-weight: 850;
    }

    #packingTypeStep .pt-header-description {
        margin: 3px 0 0;
        color: var(--pt-muted);
        font-size: 10px;
        line-height: 1.5;
    }

    #packingTypeStep .pt-content {
        display: grid;
        grid-template-columns: minmax(235px, 3fr) minmax(0, 9fr);
        min-height: 470px;
    }

    #packingTypeStep .pt-type-column {
        padding: 16px;
        border-right: 1px solid var(--pt-border);
    }

    #packingTypeStep .pt-right-column {
        min-width: 0;
    }
    #packingTypeStep .pt-section-heading {
        margin-bottom: 13px;
    }

    #packingTypeStep .pt-section-title {
        margin: 0;
        color: var(--pt-text);
        font-size: 11px;
        font-weight: 850;
        text-transform: uppercase;
        letter-spacing: .25px;
    }

    #packingTypeStep .pt-section-number {
        margin-right: 5px;
    }

    #packingTypeStep .pt-section-description {
        margin: 5px 0 0;
        color: var(--pt-muted);
        font-size: 9px;
        line-height: 1.5;
    }

    #packingTypeStep .pt-type-list {
        display: grid;
        gap: 9px;
    }
    #packingTypeStep .pt-option,
    #packingTypeStep .pt-cover-option { position: relative; display: block; margin: 0; cursor: pointer; }
    #packingTypeStep .pt-option input,
    #packingTypeStep .pt-cover-option input { position: absolute; opacity: 0; pointer-events: none; }

    #packingTypeStep .pt-option-card {
        min-height: 28px;
        display: flex;
        align-items: center;
        gap: 11px;
        padding: 11px 12px;
        border: 1px solid var(--pt-border-strong);
        border-radius: 11px;
        background: var(--pt-card);
        transition: .18s ease;
    }

    #packingTypeStep .pt-option:hover .pt-option-card,
    #packingTypeStep .pt-cover-option:hover .pt-cover-card {
        border-color: rgba(var(--pt-primary-rgb), .38);
        background: rgba(var(--pt-primary-rgb), .025);
    }

    #packingTypeStep .pt-radio {
        width: 18px;
        height: 18px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex: 0 0 18px;
        border: 2px solid #94a3b8;
        border-radius: 50%;
        background: var(--pt-card);
    }

    #packingTypeStep .pt-radio::after {
        width: 8px;
        height: 8px;
        content: "";
        border-radius: 50%;
        background: transparent;
    }

    #packingTypeStep .pt-option input:checked + .pt-option-card,
    #packingTypeStep .pt-cover-option input:checked + .pt-cover-card {
        border-color: var(--pt-primary);
        background: rgba(var(--pt-primary-rgb), .045);
        box-shadow: 0 0 0 2px rgba(var(--pt-primary-rgb), .07);
    }

    #packingTypeStep input:checked + * .pt-radio {
        border-color: var(--pt-primary);
    }
    #packingTypeStep input:checked + * .pt-radio::after {
        background: var(--pt-primary);
    }
    #packingTypeStep .pt-option-name {
        color: var(--pt-text);
        font-size: 11px;
        font-weight: 800;
    }

    #packingTypeStep .pt-cover-options {
        display: grid;
        grid-template-columns: 1fr;
        gap: 9px;
    }

    #packingTypeStep .pt-cover-card {
        min-height: 25px;
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 5px 14px;
        border: 1px solid var(--pt-border-strong);
        border-radius: 11px;
        color: var(--pt-text);
        background: var(--pt-card);
        font-size: 11px;
        font-weight: 800;
        transition: .18s ease;
    }

    #packingTypeStep .pt-visual-section {
        padding: 16px;
    }

    #packingTypeStep .pt-visual-frame {
        min-height: 270px;
        display: grid;
        grid-template-columns: minmax(0, 1fr) 250px;
        overflow: hidden;
        border: 1px solid var(--pt-border);
        border-radius: 13px;
        background: var(--pt-soft);
    }

    #packingTypeStep .pt-visual-placeholder {
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: 270px;
        border-right: 1px solid var(--pt-border);
        background: var(--pt-card);
    }

    #packingTypeStep .pt-empty-visual {
        color: var(--pt-muted);
        text-align: center;
    }

    #packingTypeStep .pt-empty-visual-icon {
        width: 62px;
        height: 62px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 10px;
        border: 1px dashed var(--pt-border-strong);
        border-radius: 16px;
        color: var(--pt-primary);
        background: var(--pt-soft);
        font-size: 21px;
    }

    #packingTypeStep .pt-empty-visual-title { margin: 0 0 3px; color: var(--pt-text); font-size: 11px; font-weight: 800; }
    #packingTypeStep .pt-empty-visual-text { margin: 0; font-size: 9px; }
    #packingTypeStep .pt-detail-panel { padding: 18px; background: var(--pt-card); }
    #packingTypeStep .pt-detail-title { margin: 0 0 17px; color: var(--pt-text); font-size: 10px; font-weight: 850; text-transform: uppercase; }
    #packingTypeStep .pt-detail-list { display: grid; gap: 13px; }

    #packingTypeStep .pt-detail-item {
        display: grid;
        grid-template-columns: 90px 10px minmax(0, 1fr);
        color: var(--pt-muted);
        font-size: 9px;
        line-height: 1.45;
    }

    #packingTypeStep .pt-detail-item strong { color: var(--pt-text); }
    #packingTypeStep .pt-detail-value { color: var(--pt-muted); font-weight: 700; }
    #packingTypeStep .pt-detail-value.has-value { color: var(--pt-primary); }
    #packingTypeStep .pt-detail-divider { margin: 16px 0; border-top: 1px solid var(--pt-border); }
    #packingTypeStep .pt-description-label { margin-bottom: 6px; color: var(--pt-muted); font-size: 9px; font-weight: 800; }
    #packingTypeStep .pt-description-value { margin: 0; color: var(--pt-muted); font-size: 9px; line-height: 1.6; }

    @media (max-width: 991.98px) {
        #packingTypeStep .pt-content { grid-template-columns: 1fr; }
        #packingTypeStep .pt-type-column { border-right: 0; border-bottom: 1px solid var(--pt-border); }
        #packingTypeStep .pt-type-list { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    }

    @media (max-width: 767.98px) {
        #packingTypeStep .pt-visual-frame { grid-template-columns: 1fr; }
        #packingTypeStep .pt-visual-placeholder { border-right: 0; border-bottom: 1px solid var(--pt-border); }
    }

    @media (max-width: 575.98px) {
        #packingTypeStep .pt-type-list,
        #packingTypeStep .pt-cover-options { grid-template-columns: 1fr; }
    }
</style>

<!-- =========================================================
     PRODUCT SETUP MODAL
     ========================================================= -->
<div
    class="modal fade"
    id="productSetupModal"
    tabindex="-1"
    aria-labelledby="productSetupModalLabel"
    aria-hidden="true"
>
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content border-0">

            <!-- Header -->
            <div class="modal-header ps-modal-header">
                <div class="ps-title-wrap">
                    <div class="ps-title-icon">
                        <i class="fa-solid fa-boxes-packing"></i>
                    </div>

                    <div>
                        <h5
                            class="modal-title ps-modal-title"
                            id="productSetupModalLabel"
                        >
                            Product Setup
                        </h5>

                        <p class="ps-modal-subtitle">
                            Pilih Sales Order dan barang sebelum membuat konfigurasi packaging.
                        </p>
                    </div>
                </div>

                <div class="d-flex align-items-center gap-3 ms-auto">
                    <div class="ps-wizard" aria-label="Product setup steps">
                        <div class="ps-wizard-step is-active" data-wizard-indicator="1">
                            <span class="ps-wizard-number">1</span>
                            <span class="ps-wizard-label">Pilih Produk</span>
                        </div>

                        <div class="ps-wizard-line" data-wizard-line="1"></div>

                        <div class="ps-wizard-step" data-wizard-indicator="2">
                            <span class="ps-wizard-number">2</span>
                            <span class="ps-wizard-label">Packing Type</span>
                        </div>

                        <div class="ps-wizard-line" data-wizard-line="2"></div>

                        <div class="ps-wizard-step" data-wizard-indicator="3">
                            <span class="ps-wizard-number">3</span>
                            <span class="ps-wizard-label">Konfigurasi</span>
                        </div>
                    </div>

                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Close"
                    ></button>
                </div>
            </div>

            <!-- Body -->
            <div class="modal-body ps-modal-body">
                <div
                    class="ps-step-panel is-active"
                    id="step1"
                    data-setup-step="1"
                >
                    <!-- =====================================================
                         STEP 1 LAYOUT — BOOTSTRAP GRID
                         ===================================================== -->
                    <div class="container-fluid p-0">

                        <!-- Cari Sales Order: full width -->
                        <div class="row g-3 mb-3">
                            <div class="col-12">
                                <section class="ps-card ps-search-card">
                                    <div class="ps-card-header">
                                        <div class="ps-card-heading">
                                            <span class="ps-card-icon">
                                                <i class="fa-solid fa-magnifying-glass"></i>
                                            </span>

                                            <div>
                                                <h6 class="ps-card-title">
                                                    Cari Sales Order
                                                </h6>

                                                <p class="ps-card-description">
                                                    Masukkan nomor SO, tarik data, lalu pilih barang yang akan dipacking.
                                                </p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="ps-card-body">
                                        <div class="row g-3 align-items-end">

                                            <!-- Field Nomor SO -->
                                            <div class="col-12 col-lg-3">
                                                <label
                                                    for="searchSO"
                                                    class="ps-label"
                                                >
                                                    <i class="fa-solid fa-file-invoice"></i>
                                                    Nomor Sales Order
                                                    <span class="ps-required">*</span>
                                                </label>

                                                <div class="ps-input-wrap">
                                                    <i class="fa-solid fa-search ps-input-icon"></i>

                                                    <input
                                                        type="text"
                                                        class="form-control ps-control"
                                                        id="searchSO"
                                                        name="search_so"
                                                        placeholder="Contoh: AI-PP-261840"
                                                        autocomplete="off"
                                                    >
                                                </div>
                                            </div>

                                            <!-- Tombol Tarik Data -->
                                            <div class="col-12 col-lg-2">
                                                <button
                                                    type="button"
                                                    class="btn ps-fetch-button w-100"
                                                    id="btnSearchSO"
                                                >
                                                    <i class="fa-solid fa-cloud-arrow-down"></i>
                                                    <span>Tarik Data</span>
                                                </button>
                                            </div>

                                            <!-- Dropdown Product -->
                                            <div
                                                class="col-12 col-lg-4"
                                                id="itemSelectionSection"
                                            >
                                                <label
                                                    for="itemDropdown"
                                                    class="ps-label"
                                                >
                                                    <i class="fa-solid fa-box-open"></i>
                                                    Pilih Barang
                                                    <span class="ps-required">*</span>
                                                </label>

                                                <select
                                                    class="form-select ps-control"
                                                    id="itemDropdown"
                                                    disabled
                                                >
                                                    <option value="">
                                                        Pilih Barang...
                                                    </option>
                                                </select>
                                            </div>

                                            <!-- Input Qty -->
                                            <div class="col-12 col-lg-2">
                                                <label for="inputQtyItem" class="ps-label">
                                                    <i class="fa-solid fa-layer-group"></i> Qty
                                                    <span class="ps-required">*</span>
                                                </label>
                                                <input
                                                    type="number"
                                                    class="form-control ps-control"
                                                    id="inputQtyItem"
                                                    name="qty_item"
                                                    placeholder="0"
                                                    min="1"
                                                >
                                            </div>

                                            <!-- Tombol Tambah Data -->
                                            <div class="col-12 col-lg-1">
                                                <label class="ps-label d-none d-lg-block">&nbsp;</label>
                                                <button
                                                    type="button"
                                                    class="btn ps-fetch-button w-100"
                                                    id="btnAddItemSO"
                                                    title="Tambah Data"
                                                >
                                                    <i class="fa-solid fa-plus"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </section>
                            </div>
                        </div>

                        <!-- Informasi SO dan Produk: Table based -->
                        <div class="row g-3 ps-info-row">
                            <div class="col-12">
                                <section class="ps-card">
                                    <div class="ps-card-header">
                                        <h6 class="ps-card-title">Daftar Barang</h6>
                                    </div>
                                    <div class="ps-card-body p-0">
                                        <div class="table-responsive">
                                            <table class="table align-middle ps-table" id="productSelectionTable">
                                                <thead>
                                                    <tr>
                                                        <th>SO</th>
                                                        <th>Customer</th>
                                                        <th>Part No</th>
                                                        <th>Deskripsi</th>
                                                        <th>Qty</th>
                                                        <th>Pilih</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="productSelectionBody">
                                                    <!-- Data akan di-render via JS -->
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </section>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- =====================================================
                     STEP 2 — PACKING TYPE
                     ===================================================== -->
                <div class="ps-step-panel" id="step2" data-setup-step="2">
                    <div id="packingTypeStep">
                        <div class="pt-shell">
                            <div class="pt-header">
                                <span class="pt-header-icon"><i class="fa-solid fa-cube"></i></span>
                                <div>
                                    <h6 class="pt-header-title">Packing Type</h6>
                                    <p class="pt-header-description">Pilih jenis packaging dan bahan penutup yang sesuai dengan kebutuhan pengiriman.</p>
                                </div>
                            </div>

                            <div class="pt-content">
                                <section class="pt-type-column">
                                    <div class="pt-section-heading">
                                        <h6 class="pt-section-title"><span class="pt-section-number">1.</span> Pilih Type Packing</h6>
                                        <p class="pt-section-description">Pilih jenis packaging yang akan digunakan.</p>
                                    </div>

                                    <div class="pt-type-list">
                                        @php
                                            $selectedTypePackaging = trim((string) ($calculation->type_packaging ?? ''));
                                        @endphp
                                        @foreach ([
                                            'Box' => 'Box Kayu',
                                            'Palet' => 'Palet Kayu',
                                            'Peti' => 'Peti Kayu',
                                            'Kerangka' => 'Kerangka Kayu',
                                        ] as $packingValue => $packingLabel)
                                            <label class="pt-option">
                                                <input type="radio" name="packing_type" value="{{ $packingValue }}" {{ strcasecmp($selectedTypePackaging, $packingValue) === 0 ? 'checked' : '' }}>
                                                <span class="pt-option-card">
                                                    <span class="pt-radio"></span>
                                                    <span class="pt-option-name">{{ $packingLabel }}</span>
                                                </span>
                                            </label>
                                        @endforeach
                                    </div>
                                    
                                    <hr style="margin: 24px 0; border: 0; border-top: 1px solid var(--pt-border);">

                                    <div class="pt-section-heading">
                                        <h6 class="pt-section-title"><span class="pt-section-number">2.</span> Pilih Bahan Penutup</h6>
                                        <p class="pt-section-description">Pilih bahan penutup yang akan digunakan pada packaging.</p>
                                    </div>

                                    <div class="pt-cover-options">
                                        @php
                                            $selectedTipePenutup = trim((string) ($calculation->tipe_penutup ?? ''));
                                        @endphp
                                        @foreach (['Papan', 'Triplek'] as $coverMaterial)
                                            <label class="pt-cover-option">
                                                <input type="radio" name="cover_material" value="{{ $coverMaterial }}" {{ strcasecmp($selectedTipePenutup, $coverMaterial) === 0 ? 'checked' : '' }}>
                                                <span class="pt-cover-card">
                                                    <span class="pt-radio"></span>
                                                    <span>{{ $coverMaterial }}</span>
                                                </span>
                                            </label>
                                        @endforeach
                                    </div>
                                    
                                    <hr style="margin: 10px 0; border: 0; border-top: 1px solid var(--pt-border);">

                                    <div class="pt-section-heading">
                                        <h6 class="pt-section-title"><span class="pt-section-number">2a.</span> Additional Mat (Opsional)</h6>
                                        <p class="pt-section-description">Pilih material tambahan jika diperlukan.</p>
                                    </div>

                                    <div class="pt-cover-options">
                                        @php
                                            $selectedAdditionalMat = trim((string) ($calculation->additional_mat ?? ''));
                                        @endphp
                                        <label class="pt-cover-option">
                                            <input type="radio" name="additional_mat" value="" {{ empty($selectedAdditionalMat) ? 'checked' : '' }}>
                                            <span class="pt-cover-card">
                                                <span class="pt-radio"></span>
                                                <span>Tidak Ada</span>
                                            </span>
                                        </label>
                                        @foreach (['Terpal', 'Carton', 'Terpal + Carton'] as $addMat)
                                            <label class="pt-cover-option">
                                                <input type="radio" name="additional_mat" value="{{ $addMat }}" {{ strcasecmp($selectedAdditionalMat, $addMat) === 0 ? 'checked' : '' }}>
                                                <span class="pt-cover-card">
                                                    <span class="pt-radio"></span>
                                                    <span>{{ $addMat }}</span>
                                                </span>
                                            </label>
                                        @endforeach
                                    </div>
                                </section>

                                <div class="pt-right-column" style="padding-top: 16px;">
                                    <section class="pt-visual-section">
                                        <div class="pt-section-heading">
                                            <h6 class="pt-section-title"><span class="pt-section-number">3.</span> Visualisasi Packaging</h6>
                                        </div>

                                        <div class="pt-visual-frame">
                                            <div class="pt-visual-placeholder">
                                                <div class="pt-empty-visual">
                                                    <span class="pt-empty-visual-icon"><i class="fa-regular fa-image"></i></span>
                                                    <h6 class="pt-empty-visual-title">Area Visual Packaging</h6>
                                                    <p class="pt-empty-visual-text">Visual masih dikosongkan. Bingkai siap digunakan.</p>
                                                </div>
                                            </div>

                                            <aside class="pt-detail-panel">
                                                <h6 class="pt-detail-title">Detail Pilihan</h6>
                                                <div class="pt-detail-list">
                                                    <div class="pt-detail-item">
                                                        <strong>Type Packing</strong><span>:</span>
                                                        <span class="pt-detail-value" id="selectedPackingTypeText">-</span>
                                                    </div>
                                                    <div class="pt-detail-item">
                                                        <strong>Bahan Penutup</strong><span>:</span>
                                                        <span class="pt-detail-value" id="selectedCoverMaterialText">-</span>
                                                    </div>
                                                </div>
                                                <div class="pt-detail-divider"></div>
                                                <div class="pt-description-label">Deskripsi</div>
                                                <p class="pt-description-value" id="selectedPackingDescription">Pilih type packing untuk menampilkan detail pilihan.</p>
                                            </aside>
                                        </div>
                                    </section>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- =====================================================
                     STEP 3 — KONFIGURASI PACKAGING
                     ===================================================== -->
                <div
                    class="ps-step-panel"
                    id="step3"
                    data-setup-step="3"
                >
                    <div id="step-2">
<div class="s2-shell">

        <!-- Data Utama Packaging -->
        <div class="s2-main-data">
            <div class="s2-main-data-title">
                <span class="s2-main-data-title-icon">
                    <i class="fa-solid fa-circle-info"></i>
                </span>
                Data Utama Packaging
            </div>

            <div class="s2-main-grid">
                <div class="s2-field-card">
                    <label class="s2-label" for="s2_pkg_number">
                        <i class="fa-solid fa-box"></i>
                        Packaging Number
                    </label>

                    <input
                        type="text"
                        class="form-control fw-bold"
                        id="s2_pkg_number"
                        value="PKG-AUTO-001"
                        disabled
                    >
                </div>

                <div class="s2-field-card">
                    <label class="s2-label" for="s2_packer">
                        <i class="fa-solid fa-user"></i>
                        Packer By
                    </label>

                    <select class="form-select" id="s2_packer">
                        <option value="">Pilih Packer...</option>
                        @php
                            $users = class_exists('\App\Models\User') ? \App\Models\User::all() : collect();
                        @endphp
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ auth()->id() == $user->id ? 'selected' : '' }}>
                                {{ $user->full_name ?? $user->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="s2-field-card">
                    <label class="s2-label" for="s2_qty_pack">
                        <i class="fa-solid fa-boxes-stacked"></i>
                        Qty Packing
                    </label>

                    <input
                        type="number"
                        class="form-control"
                        id="s2_qty_pack"
                        value="1"
                        min="1"
                    >
                </div>

                <div class="s2-field-card">
                    <label class="s2-label" for="s2_delivery_date">
                        <i class="fa-solid fa-calendar-days"></i>
                        Delivery Date
                    </label>

                    <input
                        type="date"
                        class="form-control"
                        id="s2_delivery_date"
                    >
                </div>



            </div>
        </div>

        <!-- Inner Carton Box Section -->
        @php
            $savedInnerBoxes = json_decode($calculation->inner_carton_boxes ?? '[]', true);
            $hasSavedInnerBoxes = is_array($savedInnerBoxes) && count($savedInnerBoxes) > 0;
            $cartonBoxMaterials = \Illuminate\Support\Facades\DB::table('packing_material_prices')
                                    ->where('material_type', 'Carton')
                                    ->where('component', 'NOT LIKE', '%Lembaran%')
                                    ->get();
        @endphp

        <div class="row mb-3">
            <div class="col-12">
                <section class="s2-panel" style="border: 1px solid var(--s2-border, #e2e8f0); border-radius: 12px; overflow: hidden;">
                    <div class="s2-panel-head" style="display: flex; align-items: center; justify-content: space-between;">
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <span class="s2-panel-icon">
                                <i class="fa-solid fa-boxes-packing"></i>
                            </span>
                            <h6 class="s2-panel-title" style="margin: 0;">Inner Carton Box</h6>
                        </div>
                        <div class="form-check form-switch" style="margin: 0;">
                            <input class="form-check-input" type="checkbox" id="use_inner_carton_box" style="cursor: pointer;" {{ $hasSavedInnerBoxes ? 'checked' : '' }}>
                            <label class="form-check-label" for="use_inner_carton_box" style="font-size: 0.75rem; cursor: pointer;">
                                Gunakan Inner Box
                            </label>
                        </div>
                    </div>

                    <div class="s2-panel-body" id="inner_carton_box_container" style="display: {{ $hasSavedInnerBoxes ? 'block' : 'none' }}; padding: 12px;">
                        <p style="font-size: 0.72rem; color: var(--s2-muted, #64748b); margin-bottom: 10px;">
                            <i class="fa-solid fa-circle-info me-1"></i>
                            Tambahkan carton box yang akan disusun di dalam peti kayu. Dimensi akan divalidasi terhadap ukuran peti.
                        </p>

                        <div id="carton_box_rows">
                            <!-- Dynamic rows will be inserted here -->
                        </div>

                        <button type="button" class="btn btn-sm btn-outline-primary mt-2" id="btn_add_carton_box" style="font-size: 0.75rem;">
                            <i class="fa-solid fa-plus me-1"></i> Tambah Ukuran Box
                        </button>

                        <div id="carton_box_validation_warning" class="text-danger mt-2 fw-bold text-center" style="display: none; font-size: 0.75rem;"></div>

                        <!-- Carton Capacity Bar -->
                        <div class="carton-capacity-wrap" id="carton_capacity_bar" style="display: none;">
                            <div class="carton-capacity-header">
                                <span class="carton-capacity-label">
                                    <i class="fa-solid fa-chart-pie"></i>
                                    Carton Capacity
                                </span>
                                <span class="carton-capacity-pct" id="carton_capacity_pct">0%</span>
                            </div>
                            <div class="carton-capacity-bar">
                                <div class="carton-capacity-fill is-ok" id="carton_capacity_fill" style="width: 0%;"></div>
                            </div>
                            <div class="carton-capacity-info" id="carton_capacity_info">
                                <span style="color: var(--s2-muted);">0 L required / 0 L available</span>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>

        <template id="carton-box-row-template">
            <div class="carton-box-row" style="display: flex; align-items: flex-end; gap: 8px; margin-bottom: 8px; padding: 10px; border: 1px solid var(--s2-border, #e2e8f0); border-radius: 8px; background: var(--s2-soft, #f8fafc);">
                <div style="flex: 2;">
                    <label class="form-label" style="font-size: 0.7rem; font-weight: 600; margin-bottom: 2px;">Carton Box *</label>
                    <select class="form-select form-select-sm carton_material" style="font-size: 0.75rem;">
                        <option value="">Pilih Material...</option>
                        @foreach($cartonBoxMaterials as $mat)
                            <option value="{{ $mat->code ?? $mat->id }}"
                                    data-component="{{ $mat->component }}"
                                    data-size="{{ $mat->size }}">
                                {{ $mat->component }} - {{ $mat->size }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div style="flex: 0 0 80px;">
                    <label class="form-label" style="font-size: 0.7rem; font-weight: 600; margin-bottom: 2px;">Qty</label>
                    <input type="number" class="form-control form-control-sm carton_qty" value="1" min="1" style="font-size: 0.75rem; text-align: center;">
                </div>
                <div style="flex: 0 0 32px; align-self: flex-end;">
                    <button type="button" class="btn btn-sm btn-outline-danger btn-remove-carton-row" style="padding: 4px 8px;" title="Hapus">
                        <i class="fa-solid fa-trash-can"></i>
                    </button>
                </div>
            </div>
        </template>

        <script>
            window.SAVED_INNER_BOXES = @json($savedInnerBoxes);
        </script>

        <!-- Dimensi Card (Full Width) -->
        <div class="row mb-3">
            <div class="col-12">
                <section class="s2-panel s2-dimension">
                <div class="s2-panel-head" data-toggle-panel="dimensi">
                    <span class="s2-panel-number">1</span>

                    <span class="s2-panel-icon">
                        <i class="fa-solid fa-ruler-combined"></i>
                    </span>

                    <h6 class="s2-panel-title">Dimensi</h6>
                    <span class="s2-panel-toggle"><i class="fa-solid fa-chevron-up"></i></span>
                </div>

                <div class="s2-panel-body">
                    <div class="d-flex align-items-center gap-4 w-100">

                        <div class="s2-compact-field flex-fill">
                            <label class="s2-compact-label" for="s2_length">
                                <span class="s2-compact-icon">
                                    <i class="fa-solid fa-arrows-left-right"></i>
                                </span>
                                Panjang
                            </label>

                            <input
                                type="number"
                                class="form-control"
                                id="s2_length"
                                placeholder="0"
                            >

                            <span class="s2-unit">mm</span>
                        </div>

                        <div class="s2-compact-field flex-fill">
                            <label class="s2-compact-label" for="s2_width">
                                <span class="s2-compact-icon">
                                    <i class="fa-solid fa-ruler-horizontal"></i>
                                </span>
                                Lebar
                            </label>

                            <input
                                type="number"
                                class="form-control"
                                id="s2_width"
                                placeholder="0"
                            >

                            <span class="s2-unit">mm</span>
                        </div>

                        <div class="s2-compact-field flex-fill">
                            <label class="s2-compact-label" for="s2_height">
                                <span class="s2-compact-icon">
                                    <i class="fa-solid fa-arrows-up-down"></i>
                                </span>
                                Tinggi
                            </label>

                            <input
                                type="number"
                                class="form-control"
                                id="s2_height"
                                placeholder="0"
                            >

                            <span class="s2-unit">mm</span>
                        </div>

                    </div>
                </div>
            </section>
            </div>
        </div>

        <!-- Baris 1 -->
        <div class="row">
            <div class="col-12">
            <!-- Konfigurasi Area Bawah -->
            <section class="s2-panel s2-bottom">
                <div class="s2-panel-head" data-toggle-panel="area-bawah">
                    <span class="s2-panel-number">2</span>

                    <span class="s2-panel-icon">
                        <i class="fa-solid fa-sliders"></i>
                    </span>

                    <h6 class="s2-panel-title">Konfigurasi Area Bawah</h6>
                    <span class="s2-panel-toggle"><i class="fa-solid fa-chevron-up"></i></span>
                </div>

                <div class="s2-panel-body">
                    <div class="s2-component-table">

                        <div class="s2-component-head">
                            <span>Keterangan</span>
                            <span>Nilai</span>
                            <div></div>
                            <span>Komponen</span>
                            <span>Penggunaan</span>
                            <span>Arah Pemasangan</span>
                            <span>Material</span>
                        </div>

                        @php
                            $balokMaterials = \Illuminate\Support\Facades\DB::table('packing_material_prices')
                                                ->where('component', 'LIKE', '%Balok%')
                                                ->get();
                            
                            $penutupMaterials = \Illuminate\Support\Facades\DB::table('packing_material_prices')
                                                ->where(function($q) {
                                                    $q->where('component', 'LIKE', '%Papan%')
                                                      ->orWhere('component', 'LIKE', '%Triplek%');
                                                })
                                                ->get();
                        @endphp

                        <!-- Penyanggah -->
                        <div class="s2-component-row">
                            <div class="s2-component-name" style="font-size: 0.65rem;">
                                Jarak Penyanggah Bawah
                            </div>
                            <div class="d-flex align-items-center gap-1">
                                <input type="number" class="form-control" id="s2_jarak_bawah" value="300" placeholder="0">
                                <span class="s2-unit" style="font-size: 0.6rem;">mm</span>
                            </div>

                            <div class="s2-v-divider"></div>

                            <div class="s2-component-name">
                                <span class="s2-mini-icon">
                                    <i class="fa-solid fa-grip-lines"></i>
                                </span>
                                Penyanggah
                            </div>

                            <select class="form-select" id="s2_pb_status">
                                <option value="Include">Include</option>
                                <option value="Exclude">Exclude</option>
                            </select>

                            <!-- Penyanggah Bawah arah -->
                            <select class="form-select" id="s2_pb_arah">
                                <option value="Horizontal">Horizontal</option>
                                <option value="Vertikal">Vertikal</option>
                            </select>

                            <select class="form-select" id="s2_pb_material">
                                <option value="">Pilih Material...</option>
                                @if(isset($balokMaterials) && count($balokMaterials) > 0)
                                    @foreach($balokMaterials as $mat)
                                        <option value="{{ $mat->code ?? $mat->id }}" data-wood-type="{{ $mat->material_type ?? '' }}">{{ ucwords(strtolower($mat->material_type ?? '')) }} - {{ (float)$mat->thickness }}x{{ (float)$mat->width }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>

                        <!-- Penutup -->
                        <div class="s2-component-row">
                            <div class="s2-component-name" style="font-size: 0.65rem;">
                                Celah Bawah
                            </div>
                            <div class="d-flex align-items-center gap-1">
                                <input type="number" class="form-control" id="s2_gap_bawah" placeholder="0">
                                <span class="s2-unit" style="font-size: 0.6rem;">mm</span>
                            </div>

                            <div class="s2-v-divider"></div>

                            <div class="s2-component-name">
                                <span class="s2-mini-icon">
                                    <i class="fa-solid fa-border-all"></i>
                                </span>
                                Penutup
                            </div>

                            <select class="form-select" id="s2_ptb_status">
                                <option value="Tanpa Penutup">Tanpa Penutup</option>
                                <option value="Papan Full">Papan Full</option>
                                <option value="Papan Setengah">Papan Setengah</option>
                                <option value="Tripleks">Tripleks</option>
                            </select>

                            <select class="form-select" id="s2_ptb_arah">
                                <option value="Horizontal">Horizontal</option>
                                <option value="Vertikal">Vertikal</option>
                            </select>

                            <select class="form-select" id="s2_ptb_material">
                                <option value="">Pilih Material...</option>
                                @if(isset($penutupMaterials) && count($penutupMaterials) > 0)
                                    @foreach($penutupMaterials as $mat)
                                        @php $matType = (stripos($mat->component, 'triplek') !== false) ? 'Triplek' : 'Papan'; @endphp
                                        <option value="{{ $mat->code ?? $mat->id }}" data-type="{{ $matType }}" data-wood-type="{{ $mat->material_type ?? '' }}">{{ ucwords(strtolower($mat->material_type ?? '')) }} - {{ (float)$mat->thickness }}x{{ (float)$mat->width }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>

                        <!-- Kaki Balok -->
                        <div class="s2-component-row">
                            <div></div>
                            <div></div>
                            <div class="s2-v-divider"></div>

                            <div class="s2-component-name">
                                <span class="s2-mini-icon">
                                    <i class="fa-solid fa-grip-lines-vertical"></i>
                                </span>
                                Kaki Balok
                            </div>

                            <select class="form-select" id="s2_kb_status">
                                <option value="Include">Include</option>
                                <option value="Exclude">Exclude</option>
                            </select>

                            <select class="form-select" id="s2_kb_arah">
                                <option value="Vertikal">Vertikal</option>
                                <option value="Horizontal">Horizontal</option>
                            </select>

                            <select class="form-select" id="s2_kb_material">
                                <option value="">Pilih Material...</option>
                                @if(isset($balokMaterials) && count($balokMaterials) > 0)
                                    @foreach($balokMaterials as $mat)
                                        <option value="{{ $mat->code ?? $mat->id }}" data-wood-type="{{ $mat->material_type ?? '' }}">{{ ucwords(strtolower($mat->material_type ?? '')) }} - {{ (float)$mat->thickness }}x{{ (float)$mat->width }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>

                    </div>
                </div>
            </section>
            </div>
        </div>

        <!-- Baris 2 -->
        <div class="row">
            <div class="col-12">
            <!-- Konfigurasi Area Atas -->
            <section class="s2-panel s2-top">
                <div class="s2-panel-head" data-toggle-panel="area-atas">
                    <span class="s2-panel-number">3</span>

                    <span class="s2-panel-icon">
                        <i class="fa-solid fa-sliders"></i>
                    </span>

                    <h6 class="s2-panel-title">Konfigurasi Area Atas</h6>
                    <span class="s2-panel-toggle"><i class="fa-solid fa-chevron-up"></i></span>
                </div>

                <div class="s2-panel-body">
                    <div class="s2-component-table">

                        <div class="s2-component-head">
                            <span>Keterangan</span>
                            <span>Nilai</span>
                            <div></div>
                            <span>Komponen</span>
                            <span>Penggunaan</span>
                            <span>Arah Pemasangan</span>
                            <span>Material</span>
                        </div>

                        <!-- Penyanggah -->
                        <div class="s2-component-row">
                            <div class="s2-component-name" style="font-size: 0.65rem;">
                                Jarak Penyanggah Atas
                            </div>
                            <div class="d-flex align-items-center gap-1">
                                <input type="number" class="form-control" id="s2_jarak_atas" value="300" placeholder="0">
                                <span class="s2-unit" style="font-size: 0.6rem;">mm</span>
                            </div>

                            <div class="s2-v-divider"></div>

                            <div class="s2-component-name">
                                <span class="s2-mini-icon">
                                    <i class="fa-solid fa-grip-lines"></i>
                                </span>
                                Penyanggah
                            </div>

                            <select class="form-select" id="s2_pa_status">
                                <option value="Include">Include</option>
                                <option value="Exclude">Exclude</option>
                            </select>

                            <!-- Penyanggah Atas arah -->
                            <select class="form-select" id="s2_pa_arah">
                                <option value="Horizontal">Horizontal</option>
                                <option value="Vertikal">Vertikal</option>
                            </select>

                            <select class="form-select" id="s2_pa_material">
                                <option value="">Pilih Material...</option>
                                @if(isset($balokMaterials) && count($balokMaterials) > 0)
                                    @foreach($balokMaterials as $mat)
                                        <option value="{{ $mat->code ?? $mat->id }}" data-wood-type="{{ $mat->material_type ?? '' }}">{{ ucwords(strtolower($mat->material_type ?? '')) }} - {{ (float)$mat->thickness }}x{{ (float)$mat->width }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>

                        <!-- Penutup -->
                        <div class="s2-component-row">
                            <div class="s2-component-name" style="font-size: 0.65rem;">
                                Celah Atas
                            </div>
                            <div class="d-flex align-items-center gap-1">
                                <input type="number" class="form-control" id="s2_gap_atas" placeholder="0">
                                <span class="s2-unit" style="font-size: 0.6rem;">mm</span>
                            </div>

                            <div class="s2-v-divider"></div>

                            <div class="s2-component-name">
                                <span class="s2-mini-icon">
                                    <i class="fa-solid fa-border-all"></i>
                                </span>
                                Penutup
                            </div>

                            <select class="form-select" id="s2_pta_status">
                                <option value="Tanpa Penutup">Tanpa Penutup</option>
                                <option value="Papan Full">Papan Full</option>
                                <option value="Papan Setengah">Papan Setengah</option>
                                <option value="Tripleks">Tripleks</option>
                            </select>

                            <select class="form-select" id="s2_pta_arah">
                                <option value="Horizontal">Horizontal</option>
                                <option value="Vertikal">Vertikal</option>
                            </select>

                            <select class="form-select" id="s2_pta_material">
                                <option value="">Pilih Material...</option>
                                @if(isset($penutupMaterials) && count($penutupMaterials) > 0)
                                    @foreach($penutupMaterials as $mat)
                                        @php $matType = (stripos($mat->component, 'triplek') !== false) ? 'Triplek' : 'Papan'; @endphp
                                        <option value="{{ $mat->code ?? $mat->id }}" data-type="{{ $matType }}" data-wood-type="{{ $mat->material_type ?? '' }}">{{ ucwords(strtolower($mat->material_type ?? '')) }} - {{ (float)$mat->thickness }}x{{ (float)$mat->width }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>

                    </div>
                </div>
            </section>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
            <!-- Additional Mat -->
            <section class="s2-panel s2-carton" style="display: none;">
                <div class="s2-panel-head" data-toggle-panel="additional-mat">
                    <span class="s2-panel-number">4</span>
                    <span class="s2-panel-icon">
                        <i class="fa-solid fa-box"></i>
                    </span>
                    <h6 class="s2-panel-title">Additional Mat</h6>
                    <span class="s2-panel-toggle"><i class="fa-solid fa-chevron-up"></i></span>
                </div>
                <div class="s2-panel-body">
                    <!-- Carton -->
                    <div id="carton_material_container" class="d-none gap-3 align-items-center mb-3">
                        <div class="flex-grow-1">
                            @php
                                $cartonMaterials = \Illuminate\Support\Facades\DB::table('packing_material_prices')
                                                    ->where('material_type', 'Carton')
                                                    ->where('component', 'like', '%Lembaran%')
                                                    ->get();
                            @endphp
                            <label class="form-label" style="font-size: 0.75rem; font-weight: 600;">Type Carton</label>
                            <select name="inner_carton_box" class="form-select pt-custom-select" id="s2_inner_carton_box">
                                <option value="">Pilih Type Carton...</option>
                                @if(isset($cartonMaterials) && count($cartonMaterials) > 0)
                                    @foreach($cartonMaterials as $mat)
                                        <option value="{{ $mat->code ?? $mat->id }}">{{ $mat->component }} - {{ $mat->size }} (Polos)</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    </div>

                    <!-- Terpal -->
                    <div id="terpal_material_container" style="display: none;">
                        <div class="flex-grow-1">
                            @php
                                $terpalMaterials = \Illuminate\Support\Facades\DB::table('packing_material_prices')
                                                    ->where('material_type', 'Terpal')
                                                    ->get();
                            @endphp
                            <label class="form-label" style="font-size: 0.75rem; font-weight: 600;">Terpal</label>
                            <select name="terpal_material" class="form-select pt-custom-select" id="s2_terpal_material">
                                <option value="">Pilih Material Terpal...</option>
                                @if(isset($terpalMaterials) && count($terpalMaterials) > 0)
                                    @foreach($terpalMaterials as $mat)
                                        <option value="{{ $mat->code ?? $mat->id }}">{{ $mat->component }} - {{ $mat->size }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    </div>
                </div>
            </section>
            </div>
        </div>


        <!-- Catatan -->
        <div class="s2-note">
            <span>
                <i class="fa-solid fa-circle-info me-1"></i>
                Pilih <strong>Not Include</strong> untuk menonaktifkan komponen.
            </span>

            <span>
                <i class="fa-solid fa-ruler-combined me-1"></i>
                Semua ukuran menggunakan satuan milimeter.
            </span>
        </div>

    </div>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="modal-footer ps-modal-footer d-flex justify-content-between">
                <div class="ps-footer-status">
                    <span class="ps-footer-status-dot"></span>
                    <span id="productSetupStatus">
                        Lengkapi data produk untuk melanjutkan.
                    </span>
                </div>

                <div class="ps-footer-actions d-flex align-items-center gap-2">
                    <button
                        type="button"
                        class="btn ps-button ps-button-secondary d-none"
                        id="btn_prev_step"
                    >
                        <i class="fa-solid fa-arrow-left"></i>
                        Back
                    </button>


                    <button
                        type="button"
                        class="btn ps-button ps-button-primary"
                        id="btn_next_step"
                    >
                        <span>Next Step</span>
                        <i class="fa-solid fa-arrow-right"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        'use strict';

        /* =====================================================
           ELEMENT REFERENCES
           ===================================================== */
        const modalElement = document.getElementById('productSetupModal');
        const searchInput = document.getElementById('searchSO');
        const searchButton = document.getElementById('btnSearchSO');
        const itemDropdown = document.getElementById('itemDropdown');
        const inputQtyItem = document.getElementById('inputQtyItem');
        const btnAddItemSO = document.getElementById('btnAddItemSO');
        const nextButton = document.getElementById('btn_next_step');
        const previousButton = document.getElementById('btn_prev_step');
        const statusText = document.getElementById('productSetupStatus');
        const tableBody = document.getElementById('productSelectionBody');

        let currentStep = 1;
        window.selectedItemsList = [];
        let selectedItemIndex = -1;

        /* =====================================================
           HELPERS
           ===================================================== */
        const setStep = (step) => {
            currentStep = Math.max(1, Math.min(3, Number(step)));

            document.querySelectorAll('#productSetupModal [data-setup-step]').forEach((panel) => {
                panel.classList.toggle(
                    'is-active',
                    Number(panel.dataset.setupStep) === currentStep
                );
            });

            modalElement?.querySelectorAll('[data-wizard-indicator]').forEach((indicator) => {
                const indicatorStep = Number(indicator.dataset.wizardIndicator);
                indicator.classList.toggle('is-active', indicatorStep === currentStep);
                indicator.classList.toggle('is-complete', indicatorStep < currentStep);
            });

            modalElement?.querySelectorAll('[data-wizard-line]').forEach((line) => {
                const lineStep = Number(line.dataset.wizardLine);
                line.classList.toggle('is-complete', currentStep > lineStep);
            });

            previousButton?.classList.toggle('d-none', currentStep === 1);

            if (nextButton) {
                nextButton.innerHTML = currentStep < 3
                    ? '<span>Next Step</span><i class="fa-solid fa-arrow-right"></i>'
                    : '<i class="fa-solid fa-floppy-disk"></i><span>Save Configuration</span>';
            }

            if (statusText) {
                const messages = {
                    1: 'Langkah 1 dari 3: Pilih produk yang akan dipacking.',
                    2: 'Langkah 2 dari 3: Pilih type packing dan bahan penutup.',
                    3: 'Langkah 3 dari 3: Lengkapi konfigurasi packaging.',
                };
                statusText.textContent = messages[currentStep];
            }

            modalElement?.querySelector('.ps-modal-body')?.scrollTo({ top: 0, behavior: 'smooth' });
        };

        const getSelectedItemData = () => {
            const option = itemDropdown?.selectedOptions?.[0];

            if (!option || !option.value) {
                return null;
            }

            return {
                id: option.value,
                partNumber: option.dataset.itemNo || '-',
                description: option.dataset.itemDesc || '-',
                customer:
                    option.dataset.customer ||
                    window.currentFetchedCustomer ||
                    '-',
                qtyOrder: Number(option.dataset.qtyOrder || 0),
                qtyRemaining: Number(option.dataset.qtyRemaining || 0),
            };
        };

        const renderSelectedItemsTable = () => {
            tableBody.innerHTML = '';
            window.selectedItemsList.forEach((item, index) => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${item.soNumber}</td>
                    <td>${item.customer}</td>
                    <td>${item.itemNumber}</td>
                    <td>${item.description}</td>
                    <td class="text-center fw-bold align-middle">
                        <input type="number" class="form-control form-control-sm text-center row-qty" value="${item.qty}" min="1" data-index="${index}" style="width: 80px; margin: 0 auto;">
                    </td>
                    <td class="text-center align-middle">
                        <button type="button" class="btn btn-sm btn-outline-danger btn-remove-item" data-index="${index}" style="padding: 2px 6px;">
                            <i class="fa-solid fa-minus"></i>
                        </button>
                    </td>
                `;
                tableBody.appendChild(row);
            });
        };

        tableBody?.addEventListener('click', (e) => {
            const btn = e.target.closest('.btn-remove-item');
            if (btn) {
                const index = parseInt(btn.dataset.index, 10);
                window.selectedItemsList.splice(index, 1);
                renderSelectedItemsTable();
            }
        });

        tableBody?.addEventListener('input', (e) => {
            if (e.target.classList.contains('row-qty')) {
                const index = parseInt(e.target.dataset.index, 10);
                window.selectedItemsList[index].qty = parseInt(e.target.value, 10) || 1;
            }
        });

        /* =====================================================
           PUBLIC SO FUNCTIONS
           ===================================================== */
        window.setSOSearchLoading = (isLoading) => {
            if (!searchButton) return;

            searchButton.disabled = Boolean(isLoading);

            searchButton.innerHTML = isLoading
                ? `
                    <span class="ps-spinner"></span>
                    <span>Menarik Data...</span>
                `
                : `
                    <i class="fa-solid fa-cloud-arrow-down"></i>
                    <span>Tarik Data</span>
                `;
        };

        const escapeSOHtml = (str) => {
            if (str === null || str === undefined) return '';
            return String(str)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        };

        let rawFetchedItems = [];

        window.renderSOItems = (items) => {
            if (!itemDropdown) return;

            if (!Array.isArray(items) || items.length === 0) {
                itemDropdown.innerHTML = '<option value="">Pilih Barang...</option>';
                itemDropdown.disabled = true;
                return;
            }

            rawFetchedItems = items;

            const options = items.map((item, index) => {
                const itemId = item.id ?? index;
                const itemNumber =
                    item.no_barang ??
                    item.item_no ??
                    item.part_no ??
                    '-';

                const description =
                    item.deskripsi_barang ??
                    item.description ??
                    item.item_description ??
                    '-';

                const customer = String(
                    item.nama_pelanggan ??
                    item.nama_customer ??
                    item.customer_name ??
                    item.nm_customer ??
                    item.customer ??
                    window.currentFetchedCustomer ??
                    '-'
                ).trim() || '-';

                const qtyOrder = Number(item.qty ?? item.qty_order ?? 0);
                const qtyRemaining = Number(item.sisa_kirim ?? item.qty_remaining ?? 0);

                const fullText = `${itemNumber} - ${description}`;

                return `
                    <option
                        value="${escapeSOHtml(itemId)}"
                        data-item-no="${escapeSOHtml(itemNumber)}"
                        data-item-desc="${escapeSOHtml(description)}"
                        data-customer="${escapeSOHtml(customer)}"
                        data-qty-order="${qtyOrder}"
                        data-qty-remaining="${qtyRemaining}"
                        data-short-text="${escapeSOHtml(itemNumber)}"
                        data-full-text="${escapeSOHtml(fullText)}"
                    >
                        ${escapeSOHtml(fullText)}
                    </option>
                `;
            }).join('');

            itemDropdown.innerHTML = `
                <option value="">Pilih Barang...</option>
                ${options}
            `;
            itemDropdown.disabled = false;
        };

        itemDropdown?.addEventListener('change', () => {
            const item = getSelectedItemData();
            if (item && inputQtyItem) {
                // Default input qty to remaining qty or 1
                inputQtyItem.max = item.qtyRemaining > 0 ? item.qtyRemaining : item.qtyOrder;
                inputQtyItem.value = inputQtyItem.max;
            }
        });

        btnAddItemSO?.addEventListener('click', () => {
            const item = getSelectedItemData();
            if (!item) {
                alert('Silakan pilih barang dari dropdown terlebih dahulu.');
                return;
            }

            const qty = parseInt(inputQtyItem?.value) || 1;
            
            // Check if item already exists in table
            const exists = window.selectedItemsList.find(i => i.itemNumber === item.partNumber);
            if (exists) {
                alert('Barang ini sudah ada di dalam tabel.');
                return;
            }

            // Push to selectedItemsList
            window.selectedItemsList.push({
                soNumber: searchInput?.value?.trim() || '-',
                itemNumber: item.partNumber,
                description: item.description,
                customer: item.customer || window.currentFetchedCustomer || '-',
                qty: qty
            });

            // Re-render
            renderSelectedItemsTable();

            // Reset selection
            itemDropdown.value = '';
            if (inputQtyItem) inputQtyItem.value = '';
        });

        /* =====================================================
           STEP 2 — PACKING TYPE
           ===================================================== */
        const packingTypeLabels = {
            Box: 'Box Kayu',
            Palet: 'Palet Kayu',
            Peti: 'Peti Kayu',
            Kerangka: 'Kerangka Kayu',
        };

        const packingTypeDescriptions = {
            Box: 'Kotak kayu rapat yang menutup seluruh bagian barang.',
            Palet: 'Alas datar untuk menumpuk barang agar mudah dipindahkan menggunakan forklift.',
            Peti: 'Kotak kayu yang menutup sebagian bagian barang.',
            Kerangka: 'Bingkai kayu terbuka yang memberikan perlindungan struktur pada barang.',
        };

        // ==========================================
        // UI & Event Binding
        // ==========================================
        const syncPackingTypeSelection = () => {
            const packingInput = document.querySelector('#packingTypeStep input[name="packing_type"]:checked');
            const coverInput = document.querySelector('#packingTypeStep input[name="cover_material"]:checked');

            const packingValue = packingInput ? packingInput.value : '';
            let coverValue = coverInput ? coverInput.value : '';
            // Handle Peti Kayu disabling Triplek in Step 2
            const triplekCoverInput = document.querySelector('#packingTypeStep input[name="cover_material"][value="Triplek"]');
            const papanCoverInput = document.querySelector('#packingTypeStep input[name="cover_material"][value="Papan"]');
            if (triplekCoverInput && papanCoverInput) {
                if (packingValue === 'Peti') {
                    triplekCoverInput.disabled = true;
                    if (coverValue === 'Triplek') {
                        papanCoverInput.checked = true;
                        coverValue = 'Papan';
                    }
                } else {
                    triplekCoverInput.disabled = false;
                }
            }

            const summaryPacking = document.getElementById('summary_packing_type');
            if (summaryPacking) {
                summaryPacking.textContent = packingValue + (coverValue ? ` + ${coverValue}` : '');
            }
            
            // Logic for Konfigurasi Atas & Bawah
            const s2_ptb_status = document.getElementById('s2_ptb_status');
            const s2_pta_status = document.getElementById('s2_pta_status');
            const topPanel = document.querySelector('.s2-panel.s2-top');
            const s2_pa_status = document.getElementById('s2_pa_status');

            // Helper to handle select state
            const setSelectState = (select, allowedValues, forcedValue) => {
                if (!select) return;
                let valueChanged = false;

                Array.from(select.options).forEach(opt => {
                    if (allowedValues.includes(opt.value) || opt.value === 'Tanpa Penutup') {
                        opt.hidden = false;
                        opt.disabled = false;
                    } else {
                        opt.hidden = true;
                        opt.disabled = true;
                    }
                });
                
                if (forcedValue) {
                    if (select.value !== forcedValue) {
                        select.value = forcedValue;
                        valueChanged = true;
                    }
                    select.disabled = true; // prevent user change
                    select.style.backgroundColor = 'var(--s2-soft, #f8fafc)'; // visual cue
                } else {
                    select.disabled = false;
                    select.style.backgroundColor = '';
                    if (!allowedValues.includes(select.value) && select.value !== 'Tanpa Penutup') {
                        select.value = allowedValues[0];
                        valueChanged = true;
                    }
                }

                if (valueChanged) {
                    select.dispatchEvent(new Event('change'));
                }
            };

            // ==========================================
            // KOMPLEKSITAS LOGIKA PACKING & BAHAN PENUTUP
            // 1. Box Kayu: Papan -> Atas & Bawah otomatis Papan Full. Triplek -> Atas & Bawah otomatis Tripleks.
            // 2. Palet Kayu: Panel Atas disembunyikan sepenuhnya (Exclude). Bawah bisa Papan Full/Setengah atau Tripleks.
            // 3. Peti Kayu: Triplek di Step 2 didisable (hanya Papan). Atas & Bawah otomatis Papan Setengah.
            // 4. Kerangka Kayu: Panel Atas TAMPIL namun Penutup Atas dikunci ke "Tanpa Penutup". Bawah bisa Papan Full/Setengah atau Tripleks.
            // ==========================================
            
            // 1. Show/Hide Konfigurasi Atas
            if (packingValue === 'Palet') {
                if (topPanel) topPanel.style.display = 'none';
                if (s2_pta_status && s2_pta_status.value !== 'Tanpa Penutup') {
                    s2_pta_status.value = 'Tanpa Penutup';
                    s2_pta_status.dispatchEvent(new Event('change'));
                }
                if (s2_pa_status && s2_pa_status.value !== 'Exclude') {
                    s2_pa_status.value = 'Exclude';
                    s2_pa_status.dispatchEvent(new Event('change'));
                }
            } else {
                if (topPanel) topPanel.style.display = '';
                if (s2_pa_status && s2_pa_status.value === 'Exclude') {
                    s2_pa_status.value = 'Include';
                    s2_pa_status.dispatchEvent(new Event('change'));
                }
            }

            // 2. Options per combination
            if (packingValue === 'Box') {
                if (coverValue === 'Papan') {
                    setSelectState(s2_pta_status, ['Papan Full'], 'Papan Full');
                    setSelectState(s2_ptb_status, ['Papan Full'], 'Papan Full');
                } else if (coverValue === 'Triplek') {
                    setSelectState(s2_pta_status, ['Tripleks'], 'Tripleks');
                    setSelectState(s2_ptb_status, ['Tripleks'], 'Tripleks');
                }
            } else if (packingValue === 'Peti') {
                setSelectState(s2_pta_status, ['Papan Setengah'], 'Papan Setengah');
                setSelectState(s2_ptb_status, ['Papan Setengah'], 'Papan Setengah');
            } else if (packingValue === 'Palet' || packingValue === 'Kerangka') {
                if (packingValue === 'Kerangka') {
                    setSelectState(s2_pta_status, ['Tanpa Penutup'], 'Tanpa Penutup');
                }
                if (coverValue === 'Papan') {
                    setSelectState(s2_ptb_status, ['Papan Full', 'Papan Setengah'], null);
                } else if (coverValue === 'Triplek') {
                    setSelectState(s2_ptb_status, ['Tripleks'], 'Tripleks');
                }
            }

            const packingText = document.getElementById('selectedPackingTypeText');
            const coverText = document.getElementById('selectedCoverMaterialText');
            const descriptionText = document.getElementById('selectedPackingDescription');

            if (packingText) {
                packingText.textContent = packingTypeLabels[packingValue] || '-';
                packingText.classList.toggle('has-value', Boolean(packingValue));
            }

            if (coverText) {
                coverText.textContent = coverValue || '-';
                coverText.classList.toggle('has-value', Boolean(coverValue));
            }

            if (descriptionText) {
                descriptionText.textContent = packingTypeDescriptions[packingValue]
                    || 'Pilih type packing untuk menampilkan detail pilihan.';
            }

            // Logic for Additional Mat
            const addMatSelect = document.querySelector('input[name="additional_mat"]:checked');
            const addMatValue = addMatSelect ? addMatSelect.value : '';

            const cartonContainer = document.getElementById('carton_material_container');
            if (cartonContainer) {
                if (addMatValue.includes('Carton')) {
                    cartonContainer.classList.remove('d-none');
                    cartonContainer.classList.add('d-flex');
                } else {
                    cartonContainer.classList.remove('d-flex');
                    cartonContainer.classList.add('d-none');
                }
            }

            const terpalContainer = document.getElementById('terpal_material_container');
            if (terpalContainer) {
                if (addMatValue.includes('Terpal')) {
                    terpalContainer.style.display = 'block';
                } else {
                    terpalContainer.style.display = 'none';
                }
            }

            const cartonPanel = document.querySelector('.s2-carton');
            if (cartonPanel) {
                if (addMatValue.includes('Carton') || addMatValue.includes('Terpal')) {
                    cartonPanel.style.display = 'block';
                } else {
                    cartonPanel.style.display = 'none';
                }
            }
        };

        document.querySelectorAll(
            '#packingTypeStep input[name="packing_type"], #packingTypeStep input[name="cover_material"], #packingTypeStep input[name="additional_mat"]'
        ).forEach((input) => input.addEventListener('change', syncPackingTypeSelection));

        const validatePackingTypeStep = () => {
            const packingInput = document.querySelector('#packingTypeStep input[name="packing_type"]:checked');
            const coverInput = document.querySelector('#packingTypeStep input[name="cover_material"]:checked');

            if (!packingInput) {
                if (statusText) statusText.textContent = 'Pilih type packing terlebih dahulu.';
                return false;
            }

            if (!coverInput) {
                if (statusText) statusText.textContent = 'Pilih bahan penutup terlebih dahulu.';
                return false;
            }

            return true;
        };

        /* =====================================================
           EVENTS
           ===================================================== */
        searchInput?.addEventListener('keydown', (event) => {
            if (event.key !== 'Enter') return;

            event.preventDefault();
            searchButton?.click();
        });

        itemDropdown?.addEventListener('change', () => {
            if (statusText) {
                statusText.textContent = itemDropdown.value
                    ? 'Produk terpilih. Masukkan Qty dan klik (+)'
                    : 'Pilih satu barang untuk melanjutkan.';
            }
        });

        inputQtyItem?.addEventListener('input', function () {
            const max = Number(this.max || 0);
            const value = Number(this.value || 0);
            const invalid = value <= 0 || (max > 0 && value > max);
            this.classList.toggle('is-invalid', invalid);
        });


        nextButton?.addEventListener('click', () => {
            if (currentStep === 1) {
                if (window.selectedItemsList && window.selectedItemsList.length > 0) {
                    setStep(2);
                } else if (statusText) {
                    statusText.textContent = 'Tarik data SO dan tambahkan barang ke daftar terlebih dahulu.';
                }
                return;
            }

            if (currentStep === 2) {
                if (!validatePackingTypeStep()) return;
                setStep(3);
                return;
            }

            if (!validateStepTwo()) return;

            const payload = {
                salesOrder: searchInput?.value?.trim() || '',
                items: window.selectedItemsList,
                packingType: {
                    type: document.querySelector('#packingTypeStep input[name="packing_type"]:checked')?.value || '',
                    coverMaterial: document.querySelector('#packingTypeStep input[name="cover_material"]:checked')?.value || '',
                },
                configuration: getPackagingConfiguration(),
            };

            document.dispatchEvent(new CustomEvent('productSetupSaveRequested', { detail: payload }));
            console.log('Product Setup payload:', payload);

            if (statusText) statusText.textContent = 'Konfigurasi siap disimpan.';
        });

        previousButton?.addEventListener('click', () => {
            if (currentStep > 1) setStep(currentStep - 1);
        });

        modalElement?.addEventListener('hidden.bs.modal', () => {
            setStep(1);
            document.querySelectorAll('#packingTypeStep input[type="radio"]').forEach((input) => {
                input.checked = false;
            });
            syncPackingTypeSelection();
        });


        /* =====================================================
           STEP 2 HELPERS
           ===================================================== */
        const getFieldValue = (id) => {
            const element = document.getElementById(id);
            return element ? element.value : '';
        };

        const getPackagingConfiguration = () => {
            const addMatSelected = document.querySelector('input[name="additional_mat"]:checked')?.value || '';
            
            // Extract inner carton boxes
            let innerBoxes = null;
            const useInnerCartonBox = document.getElementById('use_inner_carton_box');
            if (useInnerCartonBox && useInnerCartonBox.checked) {
                innerBoxes = [];
                const rowsContainer = document.getElementById('carton_box_rows');
                if (rowsContainer) {
                    const rows = rowsContainer.querySelectorAll('.carton-box-row');
                    rows.forEach(row => {
                        const select = row.querySelector('.carton_material');
                        const qtyInput = row.querySelector('.carton_qty');
                        if (select && select.value) {
                            const selectedOption = select.selectedOptions[0];
                            const sizeStr = selectedOption?.getAttribute('data-size') || '';
                            let length = null, width = null, height = null;
                            
                            // Simple parsing of dimension (e.g. "300mm x 300mm x 115mm")
                            if (sizeStr) {
                                const cleaned = sizeStr.replace(/\(.*?\)/g, '').trim();
                                const nums = cleaned.match(/(\d+)\s*mm/gi);
                                if (nums && nums.length >= 3) {
                                    length = parseInt(nums[0]);
                                    width = parseInt(nums[1]);
                                    height = parseInt(nums[2]);
                                }
                            }
                            
                            innerBoxes.push({
                                material: select.value,
                                materialName: selectedOption?.textContent?.trim() || '',
                                size: sizeStr,
                                length: length,
                                width: width,
                                height: height,
                                qty: parseInt(qtyInput?.value) || 1
                            });
                        }
                    });
                }
            }

            return {
                packagingNumber: getFieldValue('s2_pkg_number'),
                packerId: getFieldValue('s2_packer'),
                qtyPacking: Number(getFieldValue('s2_qty_pack') || 0),
                deliveryDate: getFieldValue('s2_delivery_date'),
                additionalMat: addMatSelected,
                innerCartonBox: addMatSelected.includes('Carton') ? getFieldValue('s2_inner_carton_box') : null,
                innerCartonBoxesArray: innerBoxes,
                terpalMaterial: addMatSelected.includes('Terpal') ? getFieldValue('s2_terpal_material') : null,
                typePackaging: document.querySelector('#packingTypeStep input[name="packing_type"]:checked')?.value || '',

            dimensions: {
                length: Number(getFieldValue('s2_length') || 0),
                width: Number(getFieldValue('s2_width') || 0),
                height: Number(getFieldValue('s2_height') || 0),
            },

            additional: {
                supportSpacingAtas: Number(
                    getFieldValue('s2_jarak_atas') || 0
                ),
                supportSpacingBawah: Number(
                    getFieldValue('s2_jarak_bawah') || 0
                ),
                topGap: Number(
                    getFieldValue('s2_gap_atas') || 0
                ),
                bottomGap: Number(
                    getFieldValue('s2_gap_bawah') || 0
                ),
            },

            bottom: {
                support: {
                    usage: getFieldValue('s2_pb_status'),
                    direction: getFieldValue('s2_pb_arah'),
                    material: getFieldValue('s2_pb_material'),
                },
                cover: {
                    usage: getFieldValue('s2_ptb_status'),
                    direction: getFieldValue('s2_ptb_arah'),
                    material: getFieldValue('s2_ptb_material'),
                },
                blockFeet: {
                    usage: getFieldValue('s2_kb_status'),
                    direction: getFieldValue('s2_kb_arah'),
                    material: getFieldValue('s2_kb_material'),
                },
            },

            top: {
                support: {
                    usage: getFieldValue('s2_pa_status'),
                    direction: getFieldValue('s2_pa_arah'),
                    material: getFieldValue('s2_pa_material'),
                },
                cover: {
                    usage: getFieldValue('s2_pta_status'),
                    direction: getFieldValue('s2_pta_arah'),
                    material: getFieldValue('s2_pta_material'),
                },
            }
        };
    };

        const validateStepTwo = () => {
            const requiredIds = [
                's2_packer',
                's2_qty_pack',
                's2_length',
                's2_width',
                's2_height',
            ];

            let firstInvalid = null;

            requiredIds.forEach((id) => {
                const element = document.getElementById(id);

                if (!element) return;

                const numericField = element.type === 'number';
                const value = numericField
                    ? Number(element.value || 0)
                    : String(element.value || '').trim();

                const invalid = numericField
                    ? value <= 0
                    : !value;

                element.classList.toggle('is-invalid', invalid);

                if (invalid && !firstInvalid) {
                    firstInvalid = element;
                }
            });

            if (firstInvalid) {
                firstInvalid.focus();

                if (statusText) {
                    statusText.textContent =
                        'Lengkapi Packer, Qty, dan Dimensi terlebih dahulu.';
                }

                return false;
            }

            return true;
        };


        /* =====================================================
           INITIAL VALUE FROM DATABASE
           Menggunakan json_encode agar aman untuk tanda kutip dan karakter
           khusus dari database.
           ===================================================== */
        const initialData = {!! json_encode($productSetupInitialData) !!};

        if (initialData.hasJob && initialData.items && initialData.items.length > 0) {
            // First item's SO is used for the search box
            if (searchInput) {
                searchInput.value = initialData.items[0].soNumber;
            }

            // Populate the table directly
            window.selectedItemsList = initialData.items.map(item => ({
                soNumber: item.soNumber,
                itemNumber: item.itemNumber,
                description: item.description,
                customer: item.customer,
                qty: item.qty,
            }));
            
            renderSelectedItemsTable();
        }

            // Populasikan nilai Step 2
            const setVal = (id, val) => {
                const element = document.getElementById(id);

                if (
                    !element ||
                    val === undefined ||
                    val === null ||
                    String(val).trim() === ''
                ) {
                    return;
                }

                const cleanValue = String(val).trim();

                if (element.tagName === 'SELECT') {
                    const matchingOption = Array.from(element.options).find(
                        option =>
                            String(option.value).trim().toLowerCase() ===
                            cleanValue.toLowerCase()
                    );

                    if (matchingOption) {
                        element.value = matchingOption.value;
                    } else {
                        const newOption = new Option(
                            cleanValue,
                            cleanValue,
                            true,
                            true
                        );

                        element.add(newOption);
                    }

                    element.dispatchEvent(new Event('change', {
                        bubbles: true
                    }));

                    return;
                }

                element.value = cleanValue;
            };

            setVal('s2_pkg_number', initialData.packagingNumber);
            setVal('s2_packer', initialData.packerId);
            setVal('s2_qty_pack', initialData.qtyPacking);
            if (initialData.deliveryDate) {
                // Formatting to YYYY-MM-DD for date input
                const dateObj = new Date(initialData.deliveryDate);
                if (!isNaN(dateObj)) {
                    setVal('s2_delivery_date', dateObj.toISOString().split('T')[0]);
                }
            }
            
            if (initialData.typePackaging) {
                const radio = document.querySelector(`#packingTypeStep input[name="packing_type"][value="${initialData.typePackaging}"]`);
                if (radio) {
                    radio.checked = true;
                    syncPackingTypeSelection();
                }
            }

            setVal('s2_length', initialData.dimensions?.length);
            setVal('s2_width', initialData.dimensions?.width);
            setVal('s2_height', initialData.dimensions?.height);

            setVal('s2_jarak_atas', initialData.additional?.supportSpacingAtas);
            setVal('s2_jarak_bawah', initialData.additional?.supportSpacingBawah);
            setVal('s2_gap_atas', initialData.additional?.topGap);
            setVal('s2_gap_bawah', initialData.additional?.bottomGap);

            setVal('s2_inner_carton_box', initialData.cartonMaterial);
            setVal('s2_terpal_material', initialData.terpalMaterial);

            setVal('s2_pb_status', initialData.bottom?.support?.usage);
            setVal('s2_pb_arah', initialData.bottom?.support?.direction);
            setVal('s2_pb_material', initialData.bottom?.support?.material);
            
            setVal('s2_ptb_status', initialData.bottom?.cover?.usage);
            setVal('s2_ptb_arah', initialData.bottom?.cover?.direction);
            setVal('s2_ptb_material', initialData.bottom?.cover?.material);
            
            setVal('s2_kb_status', initialData.bottom?.blockFeet?.usage);
            setVal('s2_kb_arah', initialData.bottom?.blockFeet?.direction);
            setVal('s2_kb_material', initialData.bottom?.blockFeet?.material);
            
            setVal('s2_pa_status', initialData.top?.support?.usage);
            setVal('s2_pa_arah', initialData.top?.support?.direction);
            setVal('s2_pa_material', initialData.top?.support?.material);
            
            setVal('s2_pta_status', initialData.top?.cover?.usage);
            setVal('s2_pta_arah', initialData.top?.cover?.direction);
            setVal('s2_pta_material', initialData.top?.cover?.material);

        setStep(1);
    });
    
    // --- API HANDLING FOR MODAL ---
    document.addEventListener('DOMContentLoaded', () => {
        const searchBtn = document.getElementById('btnSearchSO');
        const searchInput = document.getElementById('searchSO');
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
        
        // 1. Handle SO Search
        if (searchBtn) {
            searchBtn.addEventListener('click', async () => {
                const soNumber = searchInput?.value?.trim();
                if (!soNumber) {
                    alert('Harap masukkan nomor SO!');
                    return;
                }

                if (window.setSOSearchLoading) window.setSOSearchLoading(true);

                try {
                    const response = await fetch(`/api/packaging/search-so?q=${encodeURIComponent(soNumber)}`);
                    const result = await response.json();
                    
                    if (result.data && result.data.length > 0) {
                        const firstItem = result.data[0];
                        
                        // Populate Header Info
                        const setText = (id, text) => {
                            const el = document.getElementById(id);
                            if (el) el.textContent = text || '-';
                        };
                        setText('infoNoSO', soNumber);
                        const customerName = String(
                            firstItem.nama_pelanggan ??
                            firstItem.nama_customer ??
                            firstItem.customer_name ??
                            firstItem.nm_customer ??
                            firstItem.customer ??
                            ''
                        ).trim() || '-';

                        setText('infoCustomer', customerName);
                        window.currentFetchedCustomer = customerName;
                        // Misal kalau ada delivery date & address dari API
                        setText('infoDeliveryDate', firstItem.tgl_estimasi || firstItem.tgl_pengiriman || firstItem.tgl_so || '-');
                        setText('infoShipto', firstItem.shipto || '-');

                        // Populate Dropdown
                        if (window.renderSOItems) {
                            window.renderSOItems(result.data);
                        }

                        if (window.statusText) {
                            window.statusText.textContent = `Ditemukan ${result.data.length} barang. Silakan pilih.`;
                        }
                    } else {
                        if (window.showSOEmptyState) {
                            window.showSOEmptyState('Data tidak ditemukan', result.message || 'SO tidak terdaftar.');
                        }
                    }
                } catch (error) {
                    console.error('Error fetching SO:', error);
                    alert('Terjadi kesalahan saat mencari SO.');
                    if (window.resetSOHeaderInfo) window.resetSOHeaderInfo();
                } finally {
                    if (window.setSOSearchLoading) window.setSOSearchLoading(false);
                }
            });
        }
    });

    // 2. Handle Save / Update Configuration
    document.addEventListener('productSetupSaveRequested', async (e) => {
        const payload = e.detail;
        const submitBtn = document.getElementById('btn_next_step');
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="ps-spinner me-2"></span> Menyimpan...';
        }

        // Ambil ID calculation dari backend langsung
        const calcId = "{{ isset($calculation) ? $calculation->id : '' }}";

        try {
            let url = '';
            let finalPayload = {};

            const selectedItems = Array.isArray(window.selectedItemsList)
                ? window.selectedItemsList
                : [];

            const itemsList = selectedItems
                .map(item => ({
                    no_so: String(item.soNumber || '').trim(),
                    customer: String(item.customer || '').trim() || '-',
                    no_product: String(item.itemNumber || '').trim(),
                    desc_product: String(item.description || '').trim(),
                    qty: Math.max(1, Number(item.qty || 1)),
                }))
                .filter(item => item.no_product !== '');

            if (itemsList.length === 0) {
                throw new Error(
                    'Daftar barang Step 1 kosong. Tambahkan minimal satu produk.'
                );
            }

            if (calcId) {
                // =========================================================
                // UPDATE EXISTING CALCULATION
                // Route: PackagingCalculationController@update
                // =========================================================
                url = `/packaging/calc-update/${calcId}`;

                finalPayload = {
                    _token: document.querySelector(
                        'meta[name="csrf-token"]'
                    )?.content,
                    _method: 'PUT',

                    // Step 1: WAJIB dikirim agar SO, customer, produk,
                    // penambahan/penghapusan item dan qty dapat diperbarui.
                    items: itemsList,

                    // Step 2
                    qty_pack: payload.configuration.qtyPacking,
                    packer_id: payload.configuration.packerId,
                    completion_date: payload.configuration.deliveryDate,
                    type_packaging:
                        payload.configuration.typePackaging,
                    tipe_penutup:
                        payload.packingType.coverMaterial,

                    length:
                        payload.configuration.dimensions.length,
                    width:
                        payload.configuration.dimensions.width,
                    height:
                        payload.configuration.dimensions.height,

                    additional_mat:
                        payload.configuration.additionalMat,
                    carton_material:
                        payload.configuration.innerCartonBox,
                    terpal_material:
                        payload.configuration.terpalMaterial,
                    inner_carton_boxes:
                        payload.configuration.innerCartonBoxesArray,

                    jarak_penyanggah_atas:
                        payload.configuration.additional
                            .supportSpacingAtas,
                    jarak_penyanggah_bawah:
                        payload.configuration.additional
                            .supportSpacingBawah,
                    gap_atas:
                        payload.configuration.additional.topGap,
                    gap_bawah:
                        payload.configuration.additional.bottomGap,

                    bawah_penyangga_include:
                        payload.configuration.bottom.support.usage ===
                        'Include' ? 1 : 0,
                    bawah_penyangga_arah:
                        payload.configuration.bottom.support.direction,
                    bawah_penyangga_material:
                        payload.configuration.bottom.support.material,

                    bawah_penutup_tipe:
                        payload.configuration.bottom.cover.usage,
                    bawah_penutup_arah:
                        payload.configuration.bottom.cover.direction,
                    bawah_penutup_material:
                        payload.configuration.bottom.cover.material,

                    include_pallet_base:
                        payload.configuration.bottom.blockFeet.usage ===
                        'Include' ? 1 : 0,
                    bawah_kakibalok_arah:
                        payload.configuration.bottom.blockFeet.direction,
                    bawah_kakibalok_material:
                        payload.configuration.bottom.blockFeet.material,

                    atas_penyangga_include:
                        payload.configuration.top.support.usage ===
                        'Include' ? 1 : 0,
                    atas_penyangga_arah:
                        payload.configuration.top.support.direction,
                    atas_penyangga_material:
                        payload.configuration.top.support.material,

                    atas_penutup_tipe:
                        payload.configuration.top.cover.usage,
                    atas_penutup_arah:
                        payload.configuration.top.cover.direction,
                    atas_penutup_material:
                        payload.configuration.top.cover.material,
                };
            } else {
                // =========================================================
                // CREATE NEW PACKAGING JOB
                // Route: PackagingController@store
                // =========================================================
                url = '/packaging/store';

                const firstItem = itemsList[0];

                finalPayload = {
                    _token: document.querySelector(
                        'meta[name="csrf-token"]'
                    )?.content,

                    no_so: firstItem.no_so,
                    customer: firstItem.customer,
                    date_delivery:
                        payload.configuration.deliveryDate ||
                        document.getElementById(
                            'infoDeliveryDate'
                        )?.textContent?.trim() || null,
                    completion_date:
                        payload.configuration.deliveryDate || null,
                    address:
                        document.getElementById(
                            'infoShipto'
                        )?.textContent?.trim() || null,

                    packType:
                        payload.configuration.typePackaging ||
                        'Box',
                    type_packaging:
                        payload.configuration.typePackaging ||
                        'Box',
                    tipe_penutup:
                        payload.packingType.coverMaterial,

                    items: itemsList.map(item => ({
                        // Data Step 1 per produk
                        no_so: item.no_so,
                        customer: item.customer,
                        no_product: item.no_product,
                        desc_product: item.desc_product,
                        qty: item.qty,

                        // Alias lama agar tetap kompatibel
                        qty_kirim: item.qty,

                        // Data Step 2; item pertama dipakai controller
                        // sebagai konfigurasi utama packaging.
                        packer:
                            payload.configuration.packerId,
                        qty_pack:
                            payload.configuration.qtyPacking,
                        type_packaging:
                            payload.configuration.typePackaging,
                        tipe_penutup:
                            payload.packingType.coverMaterial,

                        length:
                            payload.configuration.dimensions.length,
                        width:
                            payload.configuration.dimensions.width,
                        height:
                            payload.configuration.dimensions.height,

                        additional_mat:
                            payload.configuration.additionalMat,
                        carton_material:
                            payload.configuration.innerCartonBox,
                        terpal_material:
                            payload.configuration.terpalMaterial,
                        inner_carton_boxes:
                            payload.configuration.innerCartonBoxesArray,

                        jarak_penyanggah_atas:
                            payload.configuration.additional
                                .supportSpacingAtas,
                        jarak_penyanggah_bawah:
                            payload.configuration.additional
                                .supportSpacingBawah,
                        gap_atas:
                            payload.configuration.additional.topGap,
                        gap_bawah:
                            payload.configuration.additional.bottomGap,

                        pb_status:
                            payload.configuration.bottom.support.usage,
                        pb_arah:
                            payload.configuration.bottom.support.direction,
                        pb_material:
                            payload.configuration.bottom.support.material,

                        ptb_status:
                            payload.configuration.bottom.cover.usage,
                        ptb_arah:
                            payload.configuration.bottom.cover.direction,
                        ptb_material:
                            payload.configuration.bottom.cover.material,

                        kb_status:
                            payload.configuration.bottom.blockFeet.usage,
                        kb_arah:
                            payload.configuration.bottom.blockFeet.direction,
                        kb_material:
                            payload.configuration.bottom.blockFeet.material,

                        pa_status:
                            payload.configuration.top.support.usage,
                        pa_arah:
                            payload.configuration.top.support.direction,
                        pa_material:
                            payload.configuration.top.support.material,

                        pta_status:
                            payload.configuration.top.cover.usage,
                        pta_arah:
                            payload.configuration.top.cover.direction,
                        pta_material:
                            payload.configuration.top.cover.material,
                    })),
                };
            }

            console.log('FINAL STEP 1 ITEMS:', itemsList);
            console.log('FINAL SAVE PAYLOAD:', finalPayload);

            const response = await fetch(url, {
                method: 'POST', // Walaupun _method = PUT untuk calc-update, tetap pakai POST karena HTML form limits
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(finalPayload)
            });

            const result = await response.json();
            
            if (response.ok && result.status === 'success') {
                alert('Data berhasil disimpan!');
                if (result.redirect) {
                    window.location.href = result.redirect; // Ke halaman detail baru
                } else {
                    window.location.reload(); // Reload halaman untuk memuat data terbaru
                }
            } else {
                console.error('Save validation/error response:', result);

                const validationMessages = result.errors
                    ? Object.values(result.errors).flat().join('\n')
                    : '';

                alert(
                    'Gagal menyimpan: ' +
                    (result.message || 'Unknown error') +
                    (validationMessages
                        ? `\n\n${validationMessages}`
                        : '')
                );
            }
        } catch (error) {
            console.error('Save error:', error);
            alert(error.message || 'Terjadi kesalahan jaringan.');
        } finally {
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.innerHTML = 'Save Configuration <i class="fa-solid fa-check ms-2"></i>';
            }
        }
    });
</script>

<script>
document.addEventListener('DOMContentLoaded', function() {
        function filterPenutupMaterial(statusSelectId, materialSelectId) {
            const statusSelect = document.getElementById(statusSelectId);
            const materialSelect = document.getElementById(materialSelectId);
            if (!statusSelect || !materialSelect) return;

            if (!materialSelect.originalOptions) {
                materialSelect.originalOptions = Array.from(materialSelect.options).filter(opt => opt.value !== "");
            }

            function updateMaterialOptions() {
                const status = statusSelect.value;
                const currentVal = materialSelect.value;
                
                materialSelect.innerHTML = '<option value="">Pilih Material...</option>';
                
                let targetType = null;
                if (status === 'Papan Full' || status === 'Papan Setengah') {
                    targetType = 'Papan';
                } else if (status === 'Tripleks' || status === 'Triplek') {
                    targetType = 'Triplek';
                }

                materialSelect.originalOptions.forEach(opt => {
                    if (targetType === null || opt.getAttribute('data-type') === targetType) {
                        materialSelect.appendChild(opt.cloneNode(true));
                    }
                });

                if (Array.from(materialSelect.options).some(o => o.value === currentVal)) {
                    materialSelect.value = currentVal;
                }
                
                if (status === 'Tanpa Penutup') {
                    materialSelect.value = "";
                    materialSelect.disabled = true;
                } else {
                    materialSelect.disabled = false;
                }
            }

            statusSelect.addEventListener('change', updateMaterialOptions);
            updateMaterialOptions(); // trigger on load
        }

        // Jalankan untuk Area Bawah dan Area Atas
        filterPenutupMaterial('s2_ptb_status', 's2_ptb_material');
        filterPenutupMaterial('s2_pta_status', 's2_pta_material');

        // Validate Dimensions
        const validateDimensions = () => {
            const p = parseFloat(document.getElementById('s2_length')?.value) || 0;
            const l = parseFloat(document.getElementById('s2_width')?.value) || 0;
            const t = parseFloat(document.getElementById('s2_height')?.value) || 0;
            
            // Just simple validation or other dimension checks if needed in the future
        };

        ['s2_length', 's2_width', 's2_height', 's2_inner_carton_box'].forEach(id => {
            const el = document.getElementById(id);
            if (el) {
                el.addEventListener('input', validateDimensions);
                el.addEventListener('change', validateDimensions);
            }
        });
        
        document.querySelectorAll('input[name="additional_mat"]').forEach(el => {
            el.addEventListener('change', validateDimensions);
        });

    });
</script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const useInnerCartonBox = document.getElementById('use_inner_carton_box');
    const container = document.getElementById('inner_carton_box_container');
    const rowsContainer = document.getElementById('carton_box_rows');
    const btnAdd = document.getElementById('btn_add_carton_box');
    const template = document.getElementById('carton-box-row-template');

    const dimP = document.getElementById('s2_length');
    const dimL = document.getElementById('s2_width');
    const dimT = document.getElementById('s2_height');

    let validationWarning = document.getElementById('carton_box_validation_warning');

    if (!useInnerCartonBox || !container || !rowsContainer || !template) return;

    // Toggle visibility
    useInnerCartonBox.addEventListener('change', function() {
        container.style.display = this.checked ? 'block' : 'none';
        if (this.checked && rowsContainer.children.length === 0) {
            addCartonRow();
        }
    });

    // Add row from template
    function addCartonRow(materialVal, qty) {
        const clone = template.content.cloneNode(true);
        const row = clone.querySelector('.carton-box-row');

        if (materialVal) {
            const select = row.querySelector('.carton_material');
            if (select) {
                // Try to find matching option
                const match = Array.from(select.options).find(o =>
                    o.value === materialVal || o.getAttribute('data-size') === materialVal
                );
                if (match) {
                    select.value = match.value;
                } else {
                    select.value = materialVal;
                }
            }
        }

        if (qty) {
            const qtyInput = row.querySelector('.carton_qty');
            if (qtyInput) qtyInput.value = qty;
        }

        // Remove button handler
        row.querySelector('.btn-remove-carton-row')?.addEventListener('click', function() {
            row.remove();
            validateInnerBoxDimensions();
        });

        // Validate on change
        row.querySelector('.carton_material')?.addEventListener('change', validateInnerBoxDimensions);
        row.querySelector('.carton_qty')?.addEventListener('input', validateInnerBoxDimensions);

        rowsContainer.appendChild(row);
        validateInnerBoxDimensions();
    }

    // Add button
    btnAdd?.addEventListener('click', () => addCartonRow());

    // Parse dimension from size string like "300mm x 300mm x 115mm"
    function parseSizeDimensions(sizeStr) {
        if (!sizeStr) return null;
        // Remove text in parentheses like "(Include Sablon AQPA)"
        const cleaned = sizeStr.replace(/\(.*?\)/g, '').trim();
        // Match patterns: "300mm x 300mm x 115mm" or "P : 580mm x L : 310mm x T : 310mm"
        const nums = cleaned.match(/(\d+)\s*mm/gi);
        if (nums && nums.length >= 3) {
            return {
                p: parseInt(nums[0]),
                l: parseInt(nums[1]),
                t: parseInt(nums[2])
            };
        }
        return null;
    }

    // Validate inner box dimensions against wooden crate
    function validateInnerBoxDimensions() {
        if (!validationWarning) return;

        const crateP = parseFloat(dimP?.value) || 0;
        const crateL = parseFloat(dimL?.value) || 0;
        const crateT = parseFloat(dimT?.value) || 0;

        if (crateP === 0 || crateL === 0 || crateT === 0) {
            validationWarning.style.display = 'none';
            return;
        }

        const rows = rowsContainer.querySelectorAll('.carton-box-row');
        let totalVolume = 0;
        let hasError = false;
        let errorMsg = '';

        rows.forEach(row => {
            const select = row.querySelector('.carton_material');
            const qtyInput = row.querySelector('.carton_qty');
            const selectedOption = select?.selectedOptions[0];
            const sizeStr = selectedOption?.getAttribute('data-size') || '';
            const qty = Math.max(0, parseInt(qtyInput?.value, 10) || 0);
            const dims = parseSizeDimensions(sizeStr);

            if (dims) {
                // Check if individual box exceeds crate
                if (dims.p > crateP || dims.l > crateL || dims.t > crateT) {
                    hasError = true;
                    errorMsg = `⚠ Ukuran carton box (${dims.p}x${dims.l}x${dims.t}mm) melebihi dimensi peti kayu (${crateP}x${crateL}x${crateT}mm). Kurangi qty atau sesuaikan dimensi peti.`;
                    row.style.borderColor = '#dc2626';
                } else {
                    row.style.borderColor = '';
                }
                totalVolume += (dims.p * dims.l * dims.t * qty);
            }
        });

        const crateVolume = crateP * crateL * crateT;
        if (!hasError && totalVolume > crateVolume && crateVolume > 0) {
            hasError = true;
            errorMsg = `⚠ Total volume carton box (${(totalVolume/1000000).toFixed(1)} L) melebihi volume peti kayu (${(crateVolume/1000000).toFixed(1)} L). Kurangi qty atau perbesar dimensi peti.`;
        }

        if (hasError) {
            validationWarning.textContent = errorMsg;
            validationWarning.style.display = 'block';
        } else {
            validationWarning.style.display = 'none';
        }
    }

    // Listen to dimension changes for re-validation
    [dimP, dimL, dimT].forEach(el => {
        if (el) {
            el.addEventListener('input', validateInnerBoxDimensions);
            el.addEventListener('change', validateInnerBoxDimensions);
        }
    });

    // Load saved data
    if (window.SAVED_INNER_BOXES && Array.isArray(window.SAVED_INNER_BOXES) && window.SAVED_INNER_BOXES.length > 0) {
        window.SAVED_INNER_BOXES.forEach(box => {
            addCartonRow(box.material, box.qty);
        });
    }


});
</script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    /* =====================================================
       COLLAPSIBLE PANEL TOGGLE
       ===================================================== */
    document.querySelectorAll('#step-2 [data-toggle-panel]').forEach(head => {
        head.addEventListener('click', function(e) {
            // Don't toggle when clicking form elements inside head
            if (e.target.closest('input, select, button, label, .form-check')) return;

            const panel = this.closest('.s2-panel');
            if (!panel) return;

            const body = panel.querySelector('.s2-panel-body');
            const toggle = this.querySelector('.s2-panel-toggle');
            if (!body) return;

            const isCollapsed = body.classList.contains('is-collapsed');

            if (isCollapsed) {
                // Expand
                body.classList.remove('is-collapsed');
                body.style.maxHeight = body.scrollHeight + 'px';
                body.style.opacity = '1';
                if (toggle) toggle.classList.remove('is-collapsed');

                // Remove max-height after animation for dynamic content
                setTimeout(() => { body.style.maxHeight = ''; }, 350);
            } else {
                // Collapse
                body.style.maxHeight = body.scrollHeight + 'px';
                // Force reflow
                body.offsetHeight;
                body.classList.add('is-collapsed');
                if (toggle) toggle.classList.add('is-collapsed');
            }
        });
    });

    /* =====================================================
       CARTON CAPACITY BAR
       ===================================================== */
    const capacityBar = document.getElementById('carton_capacity_bar');
    const capacityFill = document.getElementById('carton_capacity_fill');
    const capacityPct = document.getElementById('carton_capacity_pct');
    const capacityInfo = document.getElementById('carton_capacity_info');
    const rowsContainer = document.getElementById('carton_box_rows');
    const dimP = document.getElementById('s2_length');
    const dimL = document.getElementById('s2_width');
    const dimT = document.getElementById('s2_height');

    function parseSizeDims(sizeStr) {
        if (!sizeStr) return null;
        const cleaned = sizeStr.replace(/\(.*?\)/g, '').trim();
        const nums = cleaned.match(/(\d+)\s*mm/gi);
        if (nums && nums.length >= 3) {
            return {
                p: parseInt(nums[0]),
                l: parseInt(nums[1]),
                t: parseInt(nums[2])
            };
        }
        return null;
    }

    function updateCapacityBar() {
        if (!capacityBar || !capacityFill || !capacityPct || !capacityInfo || !rowsContainer) return;

        const crateP = parseFloat(dimP?.value) || 0;
        const crateL = parseFloat(dimL?.value) || 0;
        const crateT = parseFloat(dimT?.value) || 0;
        const crateVolume = crateP * crateL * crateT;

        const rows = rowsContainer.querySelectorAll('.carton-box-row');

        if (rows.length === 0) {
            capacityBar.style.display = 'none';
            return;
        }

        let totalBoxVolume = 0;
        rows.forEach(row => {
            const select = row.querySelector('.carton_material');
            const qtyInput = row.querySelector('.carton_qty');
            const selectedOption = select?.selectedOptions[0];
            const sizeStr = selectedOption?.getAttribute('data-size') || '';
            const qty = Math.max(0, parseInt(qtyInput?.value, 10) || 0);
            const dims = parseSizeDims(sizeStr);
            if (dims) {
                totalBoxVolume += (dims.p * dims.l * dims.t * qty);
            }
        });

        capacityBar.style.display = '';

        const requiredL = (totalBoxVolume / 1000000).toFixed(1);
        const availableL = (crateVolume / 1000000).toFixed(1);

        let pct = 0;
        if (crateVolume > 0) {
            pct = (totalBoxVolume / crateVolume * 100);
        } else if (totalBoxVolume > 0) {
            pct = 999;
        }

        const pctDisplay = pct > 999 ? '999+' : pct.toFixed(1);

        // Update fill bar
        capacityFill.style.width = Math.min(pct, 100) + '%';
        capacityFill.className = 'carton-capacity-fill';
        if (pct <= 80) {
            capacityFill.classList.add('is-ok');
            capacityPct.style.color = '#16a34a';
            capacityPct.style.background = 'rgba(22, 163, 74, .1)';
        } else if (pct <= 100) {
            capacityFill.classList.add('is-warn');
            capacityPct.style.color = '#d97706';
            capacityPct.style.background = 'rgba(217, 119, 6, .1)';
        } else {
            capacityFill.classList.add('is-over');
            capacityPct.style.color = '#dc2626';
            capacityPct.style.background = 'rgba(220, 38, 38, .1)';
        }

        capacityPct.textContent = pctDisplay + '%';

        const infoColor = pct > 100 ? '#dc2626' : 'var(--s2-muted)';
        capacityInfo.innerHTML = `<span style="color: ${infoColor};">${requiredL} L required / ${availableL} L available</span>`;
    }

    // Listen for dimension changes
    [dimP, dimL, dimT].forEach(el => {
        if (el) {
            el.addEventListener('input', updateCapacityBar);
            el.addEventListener('change', updateCapacityBar);
        }
    });

    // Listen for carton box row changes via event delegation
    if (rowsContainer) {
        rowsContainer.addEventListener('change', updateCapacityBar);
        rowsContainer.addEventListener('input', updateCapacityBar);

        // Observe new rows being added
        const observer = new MutationObserver(updateCapacityBar);
        observer.observe(rowsContainer, { childList: true });
    }

    // Initial update
    setTimeout(updateCapacityBar, 500);
});
</script>
