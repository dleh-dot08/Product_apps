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
        'typePackaging' => $calculation->type_packaging ?? 'Box',
        'dimensions' => [
            'length' => $calculation->panjang ?? '',
            'width' => $calculation->lebar ?? '',
            'height' => $calculation->tinggi ?? '',
        ],
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

    /* Wizard indicator */

    #productSetupModal .ps-wizard {
        display: flex;
        align-items: center;
        min-width: 250px;
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
        font-size: 11px;
        font-weight: 900;
    }

    #productSetupModal .ps-wizard-label {
        font-size: 11px;
        font-weight: 800;
        white-space: nowrap;
    }

    #productSetupModal .ps-wizard-line {
        height: 2px;
        flex: 1 1 auto;
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

</style>


<style>
    /* =========================================================
       PRODUCT SETUP — STEP 2
       CSS dipisahkan dari style modal utama agar mudah diubah.
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
        grid-template-columns: 1.15fr 1fr .75fr .95fr;
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
        grid-template-columns: 125px 1fr 1.15fr 1fr;
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
        grid-template-columns: 125px 1fr 1.15fr 1fr;
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
            grid-template-columns: 110px 1fr 1fr 1fr;
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
                        <div
                            class="ps-wizard-step is-active"
                            data-wizard-indicator="1"
                        >
                            <span class="ps-wizard-number">1</span>
                            <span class="ps-wizard-label">Pilih Produk</span>
                        </div>

                        <div
                            class="ps-wizard-line"
                            data-wizard-line
                        ></div>

                        <div
                            class="ps-wizard-step"
                            data-wizard-indicator="2"
                        >
                            <span class="ps-wizard-number">2</span>
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
                     STEP 2 — KONFIGURASI PACKAGING
                     ===================================================== -->
                <div
                    class="ps-step-panel"
                    id="step2"
                    data-setup-step="2"
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
                    <label class="s2-label" for="s2_type_packaging">
                        <i class="fa-solid fa-box"></i>
                        Type Packing
                    </label>

                    @php
                        $selectedTypePackaging = trim(
                            (string) ($calculation->type_packaging ?? 'Box')
                        );

                        $typePackagingOptions = [
                            'Box',
                            'Palet',
                            'Peti',
                            'Kerangka',
                        ];
                    @endphp

                    <select
                        class="form-select"
                        id="s2_type_packaging"
                        name="type_packaging"
                    >
                        <option value="">Pilih Type Packing...</option>

                        @foreach ($typePackagingOptions as $type)
                            <option
                                value="{{ $type }}"
                                @selected(strcasecmp($selectedTypePackaging, $type) === 0)
                            >
                                {{ $type }}
                            </option>
                        @endforeach

                        {{-- Tampilkan nilai lama jika belum ada di daftar pilihan --}}
                        @if (
                            $selectedTypePackaging !== '' &&
                            !collect($typePackagingOptions)->contains(
                                fn ($type) => strcasecmp($type, $selectedTypePackaging) === 0
                            )
                        )
                            <option value="{{ $selectedTypePackaging }}" selected>
                                {{ $selectedTypePackaging }}
                            </option>
                        @endif
                    </select>
                </div>

            </div>
        </div>

        <!-- Baris 1 -->
        <div class="s2-row">

            <!-- Dimensi -->
            <section class="s2-panel s2-dimension">
                <div class="s2-panel-head">
                    <span class="s2-panel-number">1</span>

                    <span class="s2-panel-icon">
                        <i class="fa-solid fa-ruler-combined"></i>
                    </span>

                    <h6 class="s2-panel-title">Dimensi</h6>
                </div>

                <div class="s2-panel-body">
                    <div class="s2-compact-stack">

                        <div class="s2-compact-field">
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

                        <div class="s2-compact-field">
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

                        <div class="s2-compact-field">
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

            <!-- Konfigurasi Area Bawah -->
            <section class="s2-panel s2-bottom">
                <div class="s2-panel-head">
                    <span class="s2-panel-number">2</span>

                    <span class="s2-panel-icon">
                        <i class="fa-solid fa-sliders"></i>
                    </span>

                    <h6 class="s2-panel-title">Konfigurasi Area Bawah</h6>
                </div>

                <div class="s2-panel-body">
                    <div class="s2-component-table">

                        <div class="s2-component-head">
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
                                        <option value="{{ $mat->code ?? $mat->id }}" data-wood-type="{{ $mat->wood_type ?? '' }}">{{ ucwords(strtolower($mat->wood_type)) }} - {{ (float)$mat->thickness }}x{{ (float)$mat->width }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>

                        <!-- Penutup -->
                        <div class="s2-component-row">
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
                                        <option value="{{ $mat->code ?? $mat->id }}" data-type="{{ $matType }}" data-wood-type="{{ $mat->wood_type ?? '' }}">{{ ucwords(strtolower($mat->wood_type)) }} - {{ (float)$mat->thickness }}x{{ (float)$mat->width }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>

                        <!-- Kaki Balok -->
                        <div class="s2-component-row">
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
                                        <option value="{{ $mat->code ?? $mat->id }}" data-wood-type="{{ $mat->wood_type ?? '' }}">{{ ucwords(strtolower($mat->wood_type)) }} - {{ (float)$mat->thickness }}x{{ (float)$mat->width }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>

                    </div>
                </div>
            </section>
        </div>

        <!-- Baris 2 -->
        <div class="s2-row">

            <!-- Konfigurasi Tambahan -->
            <section class="s2-panel s2-additional">
                <div class="s2-panel-head">
                    <span class="s2-panel-number">3</span>

                    <span class="s2-panel-icon">
                        <i class="fa-solid fa-sliders"></i>
                    </span>

                    <h6 class="s2-panel-title">Konfigurasi</h6>
                </div>

                <div class="s2-panel-body">
                    <div class="s2-compact-stack">

                        <div class="s2-compact-field">
                            <label class="s2-compact-label" for="s2_jarak_atas">
                                <span class="s2-compact-icon">
                                    <i class="fa-solid fa-ruler"></i>
                                </span>
                                Jarak Penyanggah Atas
                            </label>

                            <input
                                type="number"
                                class="form-control"
                                id="s2_jarak_atas"
                                value="300"
                                placeholder="0"
                            >

                            <span class="s2-unit">mm</span>
                        </div>

                        <div class="s2-compact-field">
                            <label class="s2-compact-label" for="s2_jarak_bawah">
                                <span class="s2-compact-icon">
                                    <i class="fa-solid fa-ruler"></i>
                                </span>
                                Jarak Penyanggah Bawah
                            </label>

                            <input
                                type="number"
                                class="form-control"
                                id="s2_jarak_bawah"
                                value="300"
                                placeholder="0"
                            >

                            <span class="s2-unit">mm</span>
                        </div>

                        <div class="s2-compact-field">
                            <label class="s2-compact-label" for="s2_gap_atas">
                                <span class="s2-compact-icon">
                                    <i class="fa-solid fa-arrow-up"></i>
                                </span>
                                Celah Atas
                            </label>

                            <input
                                type="number"
                                class="form-control"
                                id="s2_gap_atas"
                                placeholder="0"
                            >

                            <span class="s2-unit">mm</span>
                        </div>

                        <div class="s2-compact-field">
                            <label class="s2-compact-label" for="s2_gap_bawah">
                                <span class="s2-compact-icon">
                                    <i class="fa-solid fa-arrow-down"></i>
                                </span>
                                Celah Bawah
                            </label>

                            <input
                                type="number"
                                class="form-control"
                                id="s2_gap_bawah"
                                placeholder="0"
                            >

                            <span class="s2-unit">mm</span>
                        </div>

                    </div>
                </div>
            </section>

            <!-- Konfigurasi Area Atas -->
            <section class="s2-panel s2-top">
                <div class="s2-panel-head">
                    <span class="s2-panel-number">4</span>

                    <span class="s2-panel-icon">
                        <i class="fa-solid fa-sliders"></i>
                    </span>

                    <h6 class="s2-panel-title">Konfigurasi Area Atas</h6>
                </div>

                <div class="s2-panel-body">
                    <div class="s2-component-table">

                        <div class="s2-component-head">
                            <span>Komponen</span>
                            <span>Penggunaan</span>
                            <span>Arah Pemasangan</span>
                            <span>Material</span>
                        </div>

                        <!-- Penyanggah -->
                        <div class="s2-component-row">
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
                                        <option value="{{ $mat->code ?? $mat->id }}" data-wood-type="{{ $mat->wood_type ?? '' }}">{{ ucwords(strtolower($mat->wood_type)) }} - {{ (float)$mat->thickness }}x{{ (float)$mat->width }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>

                        <!-- Penutup -->
                        <div class="s2-component-row">
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
                                        <option value="{{ $mat->code ?? $mat->id }}" data-type="{{ $matType }}" data-wood-type="{{ $mat->wood_type ?? '' }}">{{ ucwords(strtolower($mat->wood_type)) }} - {{ (float)$mat->thickness }}x{{ (float)$mat->width }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>

                    </div>
                </div>
            </section>
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
                        class="btn ps-button ps-button-secondary"
                        data-bs-dismiss="modal"
                    >
                        Cancel
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
            currentStep = step;

            document.querySelectorAll(
                '#productSetupModal [data-setup-step]'
            ).forEach((panel) => {
                panel.classList.toggle(
                    'is-active',
                    Number(panel.dataset.setupStep) === step
                );
            });

            const indicatorOne = modalElement?.querySelector(
                '[data-wizard-indicator="1"]'
            );

            const indicatorTwo = modalElement?.querySelector(
                '[data-wizard-indicator="2"]'
            );

            const wizardLine = modalElement?.querySelector(
                '[data-wizard-line]'
            );

            indicatorOne?.classList.toggle(
                'is-active',
                step === 1
            );

            indicatorOne?.classList.toggle(
                'is-complete',
                step === 2
            );

            indicatorTwo?.classList.toggle(
                'is-active',
                step === 2
            );

            wizardLine?.classList.toggle(
                'is-complete',
                step === 2
            );

            previousButton?.classList.toggle(
                'd-none',
                step === 1
            );

            if (nextButton) {
                nextButton.innerHTML = step === 1
                    ? '<span>Next Step</span><i class="fa-solid fa-arrow-right"></i>'
                    : '<i class="fa-solid fa-floppy-disk"></i><span>Save Configuration</span>';
            }

            if (statusText) {
                statusText.textContent = step === 1
                    ? 'Lengkapi data produk untuk melanjutkan.'
                    : 'Review konfigurasi sebelum disimpan.';
            }
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
                } else {
                    if (statusText) {
                        statusText.textContent =
                            'Tarik data SO dan tambahkan barang ke daftar terlebih dahulu.';
                    }
                }
                return;
            }

            if (!validateStepTwo()) {
                return;
            }

            const payload = {
                salesOrder: searchInput?.value?.trim() || '',
                items: window.selectedItemsList,
                configuration: getPackagingConfiguration(),
            };

            document.dispatchEvent(
                new CustomEvent('productSetupSaveRequested', {
                    detail: payload,
                })
            );

            console.log(
                'Product Setup payload:',
                payload
            );

            if (statusText) {
                statusText.textContent =
                    'Konfigurasi siap disimpan.';
            }
        });

        previousButton?.addEventListener(
            'click',
            () => setStep(1)
        );

        modalElement?.addEventListener(
            'hidden.bs.modal',
            () => setStep(1)
        );


        /* =====================================================
           STEP 2 HELPERS
           ===================================================== */
        const getFieldValue = (id) => {
            const element = document.getElementById(id);
            return element ? element.value : '';
        };

        const getPackagingConfiguration = () => ({
            packagingNumber: getFieldValue('s2_pkg_number'),
            packerId: getFieldValue('s2_packer'),
            qtyPacking: Number(getFieldValue('s2_qty_pack') || 0),
            typePackaging: getFieldValue('s2_type_packaging'),

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
            },
        });

        const validateStepTwo = () => {
            const requiredIds = [
                's2_packer',
                's2_qty_pack',
                's2_type_packaging',
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
            setVal('s2_type_packaging', initialData.typePackaging);

            setVal('s2_length', initialData.dimensions?.length);
            setVal('s2_width', initialData.dimensions?.width);
            setVal('s2_height', initialData.dimensions?.height);

            setVal('s2_jarak_atas', initialData.additional?.supportSpacingAtas);
            setVal('s2_jarak_bawah', initialData.additional?.supportSpacingBawah);
            setVal('s2_gap_atas', initialData.additional?.topGap);
            setVal('s2_gap_bawah', initialData.additional?.bottomGap);

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
                    type_packaging:
                        payload.configuration.typePackaging,

                    length:
                        payload.configuration.dimensions.length,
                    width:
                        payload.configuration.dimensions.width,
                    height:
                        payload.configuration.dimensions.height,

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
                        document.getElementById(
                            'infoDeliveryDate'
                        )?.textContent?.trim() || null,
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

                        length:
                            payload.configuration.dimensions.length,
                        width:
                            payload.configuration.dimensions.width,
                        height:
                            payload.configuration.dimensions.height,

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
    });
</script>