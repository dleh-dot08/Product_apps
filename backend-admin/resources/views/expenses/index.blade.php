@extends('layouts.app')

@section('content')
@php
    /*
    |--------------------------------------------------------------------------
    | View-only helper
    |--------------------------------------------------------------------------
    | Tidak mengubah controller / route yang sudah ada.
    | $totalAmount, $fuelTotal, $tollTotal, $expenses dan $drivers tetap
    | menggunakan data asli dari halaman lama.
    */
    $expenseRows = method_exists($expenses, 'getCollection')
        ? $expenses->getCollection()
        : collect($expenses);

    $totalTransactions = method_exists($expenses, 'total')
        ? $expenses->total()
        : $expenseRows->count();

    $otherTotal = max(0, (float) $totalAmount - (float) $fuelTotal - (float) $tollTotal);
@endphp

<style>
    .expense-page {
        --exp-bg: #f8fafc;
        --exp-card: #ffffff;
        --exp-border: #e5e7eb;
        --exp-border-soft: #eef2f7;
        --exp-text: #0f172a;
        --exp-muted: #64748b;
        --exp-orange: #f97316;
        --exp-orange-dark: #ea580c;
        --exp-orange-soft: #fff7ed;
        --exp-green: #059669;
        --exp-green-soft: #ecfdf5;
        --exp-red: #e11d48;
        --exp-red-soft: #fff1f2;
        --exp-blue: #2563eb;
        --exp-blue-soft: #eff6ff;
        --exp-purple: #7c3aed;
        --exp-purple-soft: #f5f3ff;
        min-height: calc(100vh - 60px);
        padding: 24px 28px 34px;
        background:
            radial-gradient(circle at top right, rgba(249, 115, 22, .035), transparent 26%),
            var(--exp-bg);
    }

    .expense-page * {
        box-sizing: border-box;
    }

    .expense-container {
        max-width: 1600px;
        margin: 0 auto;
    }

    /* ========================= HEADER ========================= */
    .expense-page-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;
        margin-bottom: 18px;
    }

    .expense-heading {
        display: flex;
        align-items: flex-start;
        gap: 12px;
    }

    .expense-heading-icon {
        width: 42px;
        height: 42px;
        border-radius: 12px;
        display: grid;
        place-items: center;
        flex: 0 0 42px;
        color: var(--exp-orange);
        background: var(--exp-orange-soft);
        border: 1px solid #ffedd5;
        font-size: 17px;
    }

    .expense-heading h1 {
        margin: 0;
        color: var(--exp-text);
        font-size: 22px;
        font-weight: 850;
        line-height: 1.2;
        letter-spacing: -.35px;
    }

    .expense-heading p {
        margin: 5px 0 0;
        color: var(--exp-muted);
        font-size: 12px;
        font-weight: 500;
    }

    .btn-create-expense {
        min-height: 42px;
        padding: 0 18px;
        border: 0;
        border-radius: 999px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        color: #fff;
        background: linear-gradient(135deg, var(--exp-orange), var(--exp-orange-dark));
        box-shadow: 0 8px 18px rgba(234, 88, 12, .18);
        font-size: 12px;
        font-weight: 800;
        transition: .18s ease;
    }

    .btn-create-expense:hover {
        color: #fff;
        transform: translateY(-1px);
        box-shadow: 0 10px 24px rgba(234, 88, 12, .24);
    }

    /* ========================= ALERT ========================= */
    .expense-alert {
        border: 0;
        border-radius: 13px;
        box-shadow: 0 6px 18px rgba(15, 23, 42, .05);
        font-size: 12px;
    }

    /* ========================= SUMMARY ========================= */
    .expense-summary-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 14px;
        margin-bottom: 18px;
    }

    .expense-summary-card {
        min-height: 104px;
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 17px 18px;
        border: 1px solid var(--exp-border);
        border-radius: 15px;
        background: var(--exp-card);
        box-shadow: 0 8px 22px rgba(15, 23, 42, .035);
        transition: .18s ease;
    }

    .expense-summary-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 12px 26px rgba(15, 23, 42, .06);
    }

    .summary-icon {
        width: 46px;
        height: 46px;
        flex: 0 0 46px;
        display: grid;
        place-items: center;
        border-radius: 12px;
        font-size: 18px;
    }

    .summary-icon.total {
        color: #fff;
        background: linear-gradient(135deg, var(--exp-orange), var(--exp-orange-dark));
        box-shadow: 0 7px 14px rgba(234, 88, 12, .16);
    }

    .summary-icon.fuel {
        color: var(--exp-red);
        background: var(--exp-red-soft);
    }

    .summary-icon.toll {
        color: var(--exp-blue);
        background: var(--exp-blue-soft);
    }

    .summary-icon.other {
        color: var(--exp-purple);
        background: var(--exp-purple-soft);
    }

    .summary-label {
        margin-bottom: 3px;
        color: var(--exp-muted);
        font-size: 9.5px;
        line-height: 1.2;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: .5px;
    }

    .summary-value {
        color: var(--exp-text);
        font-size: 21px;
        line-height: 1.2;
        font-weight: 850;
        white-space: nowrap;
    }

    .summary-meta {
        margin-top: 4px;
        color: var(--exp-muted);
        font-size: 10px;
        font-weight: 550;
    }

    /* ========================= HISTORY CARD ========================= */
    .expense-history-card {
        overflow: hidden;
        border: 1px solid var(--exp-border);
        border-radius: 16px;
        background: var(--exp-card);
        box-shadow: 0 10px 28px rgba(15, 23, 42, .04);
    }

    .expense-history-head {
        padding: 17px 18px 14px;
        border-bottom: 1px solid var(--exp-border-soft);
    }

    .expense-history-title-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
    }

    .expense-history-title {
        display: flex;
        align-items: flex-start;
        gap: 10px;
    }

    .expense-history-title > i {
        margin-top: 3px;
        color: var(--exp-orange);
        font-size: 15px;
    }

    .expense-history-title h2 {
        margin: 0;
        color: var(--exp-text);
        font-size: 15px;
        font-weight: 850;
    }

    .expense-history-title p {
        margin: 3px 0 0;
        color: var(--exp-muted);
        font-size: 10.5px;
    }

    .expense-count-badge {
        padding: 5px 9px;
        border-radius: 999px;
        color: #475569;
        background: #f1f5f9;
        font-size: 10px;
        font-weight: 800;
        white-space: nowrap;
    }

    /* ========================= FILTER ========================= */
    .expense-filter-form {
        display: grid;
        grid-template-columns: minmax(220px, 1fr) 170px 180px 40px;
        gap: 8px;
        margin-top: 14px;
    }

    .expense-search-wrap {
        position: relative;
    }

    .expense-search-wrap i {
        position: absolute;
        left: 13px;
        top: 50%;
        transform: translateY(-50%);
        color: #94a3b8;
        font-size: 12px;
        pointer-events: none;
    }

    .expense-filter-control {
        width: 100%;
        height: 39px;
        border: 1px solid #dbe3ec;
        border-radius: 9px;
        background-color: #fff;
        color: #334155;
        font-size: 11px;
        font-weight: 600;
        box-shadow: none;
    }

    .expense-search-wrap .expense-filter-control {
        padding-left: 36px;
    }

    .expense-filter-control:focus {
        border-color: #fdba74;
        box-shadow: 0 0 0 3px rgba(249, 115, 22, .08);
    }

    .btn-reset-expense {
        width: 40px;
        height: 39px;
        display: grid;
        place-items: center;
        border: 1px solid #dbe3ec;
        border-radius: 9px;
        background: #fff;
        color: #64748b;
        text-decoration: none;
        transition: .15s ease;
    }

    .btn-reset-expense:hover {
        color: var(--exp-orange);
        border-color: #fdba74;
        background: var(--exp-orange-soft);
    }

    /* ========================= TABLE ========================= */
    .expense-table-wrap {
        overflow-x: auto;
    }

    .expense-table {
        width: 100%;
        min-width: 980px;
        margin: 0;
        border-collapse: separate;
        border-spacing: 0;
        color: var(--exp-text);
        font-size: 11.5px;
    }

    .expense-table thead th {
        padding: 11px 14px;
        border-bottom: 1px solid var(--exp-border);
        background: #f8fafc;
        color: #64748b;
        font-size: 9.5px;
        font-weight: 850;
        text-transform: uppercase;
        letter-spacing: .35px;
        white-space: nowrap;
    }

    .expense-table tbody td {
        padding: 12px 14px;
        border-bottom: 1px solid var(--exp-border-soft);
        background: #fff;
        vertical-align: middle;
    }

    .expense-table tbody tr:last-child td {
        border-bottom: 0;
    }

    .expense-table tbody tr:hover td {
        background: #fffdfb;
    }

    .expense-date-main {
        color: var(--exp-text);
        font-size: 12px;
        font-weight: 800;
    }

    .expense-date-sub {
        margin-top: 3px;
        color: var(--exp-muted);
        font-size: 9.5px;
        font-weight: 600;
    }

    .expense-driver-wrap {
        display: flex;
        align-items: flex-start;
        gap: 8px;
    }

    .expense-driver-avatar {
        width: 26px;
        height: 26px;
        flex: 0 0 26px;
        display: grid;
        place-items: center;
        border-radius: 50%;
        color: var(--exp-blue);
        background: var(--exp-blue-soft);
        font-size: 10px;
    }

    .expense-driver-name {
        color: var(--exp-text);
        font-size: 11.5px;
        font-weight: 800;
    }

    .expense-driver-shift {
        margin-top: 2px;
        color: var(--exp-muted);
        font-size: 9.5px;
    }

    .expense-category {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 5px 8px;
        border-radius: 7px;
        font-size: 9.5px;
        font-weight: 800;
        white-space: nowrap;
    }

    .expense-category.fuel {
        color: var(--exp-red);
        background: var(--exp-red-soft);
    }

    .expense-category.toll {
        color: var(--exp-blue);
        background: var(--exp-blue-soft);
    }

    .expense-category.parking {
        color: var(--exp-purple);
        background: var(--exp-purple-soft);
    }

    .expense-category.meal {
        color: var(--exp-green);
        background: var(--exp-green-soft);
    }

    .expense-category.other {
        color: #475569;
        background: #f1f5f9;
    }

    .expense-description {
        max-width: 270px;
        color: #334155;
        line-height: 1.45;
        white-space: normal;
        word-break: break-word;
    }

    .expense-nominal {
        color: var(--exp-text);
        font-size: 12px;
        font-weight: 850;
        white-space: nowrap;
    }

    .expense-icon-btn {
        width: 32px;
        height: 32px;
        display: inline-grid;
        place-items: center;
        padding: 0;
        border: 1px solid #e2e8f0;
        border-radius: 50%;
        background: #fff;
        color: #64748b;
        transition: .15s ease;
    }

    .expense-icon-btn.receipt {
        color: #0ea5e9;
    }

    .expense-icon-btn.receipt:hover {
        color: #0284c7;
        border-color: #7dd3fc;
        background: #f0f9ff;
    }

    .expense-icon-btn.more:hover {
        color: var(--exp-orange);
        border-color: #fdba74;
        background: var(--exp-orange-soft);
    }

    .expense-action-dropdown .dropdown-menu {
        min-width: 150px;
        padding: 6px;
        border: 1px solid var(--exp-border);
        border-radius: 10px;
        box-shadow: 0 12px 28px rgba(15, 23, 42, .12);
        font-size: 11px;
    }

    .expense-action-dropdown .dropdown-item {
        padding: 8px 9px;
        border-radius: 7px;
        color: #475569;
        font-weight: 650;
    }

    .expense-action-dropdown .dropdown-item:hover {
        background: #f8fafc;
    }

    .expense-action-dropdown .dropdown-item.text-danger:hover {
        background: #fff1f2;
        color: #dc2626 !important;
    }

    /* ========================= TABLE FOOTER ========================= */
    .expense-table-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 18px;
        padding: 12px 16px;
        border-top: 1px solid var(--exp-border);
        background: #fcfcfd;
    }

    .expense-table-footer-left {
        color: var(--exp-muted);
        font-size: 10px;
        font-weight: 650;
    }

    .expense-table-footer-left strong {
        color: var(--exp-text);
    }

    .expense-table-footer-total {
        display: flex;
        align-items: center;
        gap: 12px;
        color: var(--exp-muted);
        font-size: 10px;
        font-weight: 700;
    }

    .expense-table-footer-total strong {
        color: var(--exp-green);
        font-size: 15px;
        font-weight: 900;
    }

    /* ========================= EMPTY ========================= */
    .expense-empty {
        padding: 48px 20px !important;
        text-align: center;
    }

    .expense-empty-icon {
        width: 50px;
        height: 50px;
        margin: 0 auto 10px;
        display: grid;
        place-items: center;
        border-radius: 14px;
        color: var(--exp-orange);
        background: var(--exp-orange-soft);
        font-size: 20px;
    }

    .expense-empty-title {
        color: var(--exp-text);
        font-size: 13px;
        font-weight: 850;
    }

    .expense-empty-desc {
        margin-top: 3px;
        color: var(--exp-muted);
        font-size: 10px;
    }

    /* ========================= RECEIPT MODAL ========================= */
    #receiptPreviewModal .modal-content {
        border: 0;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 24px 70px rgba(15, 23, 42, .2);
    }

    #receiptPreviewModal .modal-header {
        padding: 15px 18px;
        border-bottom: 1px solid var(--exp-border-soft);
    }

    #receiptPreviewModal .modal-title {
        color: var(--exp-text);
        font-size: 14px;
        font-weight: 850;
    }

    .receipt-preview-stage {
        min-height: 360px;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 16px;
        background: #f8fafc;
    }

    .receipt-preview-stage img {
        display: block;
        max-width: 100%;
        max-height: 70vh;
        object-fit: contain;
        border-radius: 9px;
    }

    .receipt-preview-stage iframe {
        width: 100%;
        height: 70vh;
        border: 0;
        border-radius: 9px;
        background: #fff;
    }

    /* ========================= DARK MODE ========================= */
    html[data-bs-theme="dark"] .expense-page {
        --exp-bg: #0f172a;
        --exp-card: #111827;
        --exp-border: #243244;
        --exp-border-soft: #1f2a3a;
        --exp-text: #f8fafc;
        --exp-muted: #94a3b8;
        --exp-orange-soft: rgba(249, 115, 22, .12);
        --exp-green-soft: rgba(16, 185, 129, .10);
        --exp-red-soft: rgba(225, 29, 72, .10);
        --exp-blue-soft: rgba(37, 99, 235, .12);
        --exp-purple-soft: rgba(124, 58, 237, .12);
    }

    html[data-bs-theme="dark"] .expense-filter-control,
    html[data-bs-theme="dark"] .btn-reset-expense,
    html[data-bs-theme="dark"] .expense-table tbody td,
    html[data-bs-theme="dark"] .expense-icon-btn {
        background: #111827;
        border-color: #334155;
        color: #e2e8f0;
    }

    html[data-bs-theme="dark"] .expense-table thead th,
    html[data-bs-theme="dark"] .expense-table-footer {
        background: #0f172a;
    }

    html[data-bs-theme="dark"] .expense-table tbody tr:hover td {
        background: #172033;
    }

    html[data-bs-theme="dark"] .expense-description {
        color: #cbd5e1;
    }

    html[data-bs-theme="dark"] .expense-count-badge {
        color: #cbd5e1;
        background: #1e293b;
    }

    /* ========================= RESPONSIVE ========================= */
    @media (max-width: 1200px) {
        .expense-summary-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .expense-filter-form {
            grid-template-columns: minmax(220px, 1fr) 160px 170px 40px;
        }
    }

    @media (max-width: 900px) {
        .expense-page {
            padding: 18px;
        }

        .expense-filter-form {
            grid-template-columns: 1fr 1fr;
        }

        .expense-search-wrap {
            grid-column: 1 / -1;
        }

        .btn-reset-expense {
            width: 100%;
        }
    }

    @media (max-width: 650px) {
        .expense-page {
            padding: 14px;
        }

        .expense-page-header {
            align-items: stretch;
            flex-direction: column;
        }

        .btn-create-expense {
            width: 100%;
        }

        .expense-summary-grid {
            grid-template-columns: 1fr;
        }

        .expense-filter-form {
            grid-template-columns: 1fr;
        }

        .expense-search-wrap {
            grid-column: auto;
        }

        .expense-table-footer {
            align-items: flex-start;
            flex-direction: column;
        }
    }
</style>

<div class="expense-page">
    <div class="expense-container">

        {{-- HEADER --}}
        <div class="expense-page-header">
            <div class="expense-heading">
                <div class="expense-heading-icon">
                    <i class="fa-solid fa-money-bill-wave"></i>
                </div>
                <div>
                    <h1>Pengeluaran Operasional</h1>
                    <p>Kelola dan pantau seluruh pengeluaran driver selama shift kerja berjalan.</p>
                </div>
            </div>

            <button
                type="button"
                class="btn-create-expense"
                data-bs-toggle="modal"
                data-bs-target="#createExpenseModal"
            >
                <i class="fa-solid fa-plus"></i>
                Catat Pengeluaran
            </button>
        </div>

        {{-- ALERT SUCCESS --}}
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show expense-alert mb-3" role="alert">
                <div class="d-flex align-items-center gap-2">
                    <i class="fa-solid fa-circle-check"></i>
                    <div><strong>Berhasil!</strong> {{ session('success') }}</div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger expense-alert mb-3" role="alert">
                <div class="d-flex align-items-start gap-2">
                    <i class="fa-solid fa-circle-exclamation mt-1"></i>
                    <div>
                        <strong>Data belum dapat diproses.</strong>
                        <ul class="mb-0 mt-1 ps-3">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        {{-- SUMMARY --}}
        <div class="expense-summary-grid">
            <div class="expense-summary-card">
                <div class="summary-icon total">
                    <i class="fa-solid fa-wallet"></i>
                </div>
                <div>
                    <div class="summary-label">Total Pengeluaran</div>
                    <div class="summary-value">Rp {{ number_format($totalAmount, 0, ',', '.') }}</div>
                    <div class="summary-meta">{{ number_format($totalTransactions, 0, ',', '.') }} transaksi tercatat</div>
                </div>
            </div>

            <div class="expense-summary-card">
                <div class="summary-icon fuel">
                    <i class="fa-solid fa-gas-pump"></i>
                </div>
                <div>
                    <div class="summary-label">BBM / Fuel</div>
                    <div class="summary-value">Rp {{ number_format($fuelTotal, 0, ',', '.') }}</div>
                    <div class="summary-meta">Biaya bahan bakar kendaraan</div>
                </div>
            </div>

            <div class="expense-summary-card">
                <div class="summary-icon toll">
                    <i class="fa-solid fa-road-barrier"></i>
                </div>
                <div>
                    <div class="summary-label">E-Toll</div>
                    <div class="summary-value">Rp {{ number_format($tollTotal, 0, ',', '.') }}</div>
                    <div class="summary-meta">Biaya perjalanan jalan tol</div>
                </div>
            </div>

            <div class="expense-summary-card">
                <div class="summary-icon other">
                    <i class="fa-solid fa-receipt"></i>
                </div>
                <div>
                    <div class="summary-label">Pengeluaran Lainnya</div>
                    <div class="summary-value">Rp {{ number_format($otherTotal, 0, ',', '.') }}</div>
                    <div class="summary-meta">Parkir, makan dan kategori lain</div>
                </div>
            </div>
        </div>

        {{-- HISTORY --}}
        <section class="expense-history-card">
            <div class="expense-history-head">
                <div class="expense-history-title-row">
                    <div class="expense-history-title">
                        <i class="fa-solid fa-list-check"></i>
                        <div>
                            <h2>Riwayat Pengeluaran</h2>
                            <p>Kelola seluruh transaksi operasional driver dan bukti pengeluarannya.</p>
                        </div>
                    </div>

                    <span class="expense-count-badge">
                        {{ number_format($totalTransactions, 0, ',', '.') }} Transaksi
                    </span>
                </div>

                <form action="{{ route('expenses.index') }}" method="GET" class="expense-filter-form" id="expenseFilterForm">
                    {{-- Search hanya filter baris yang sedang tampil. Tidak butuh perubahan controller. --}}
                    <div class="expense-search-wrap">
                        <i class="fa-solid fa-magnifying-glass"></i>
                        <input
                            type="search"
                            id="expenseTableSearch"
                            class="form-control expense-filter-control"
                            placeholder="Cari driver, deskripsi, kategori..."
                            autocomplete="off"
                        >
                    </div>

                    <select
                        name="category"
                        class="form-select expense-filter-control"
                        onchange="this.form.submit()"
                    >
                        <option value="">Semua Kategori</option>
                        <option value="fuel" {{ request('category') == 'fuel' ? 'selected' : '' }}>Bensin / Fuel</option>
                        <option value="toll" {{ request('category') == 'toll' ? 'selected' : '' }}>Tol</option>
                        <option value="parking" {{ request('category') == 'parking' ? 'selected' : '' }}>Parkir</option>
                        <option value="meal" {{ request('category') == 'meal' ? 'selected' : '' }}>Makan</option>
                        <option value="other" {{ request('category') == 'other' ? 'selected' : '' }}>Lainnya</option>
                    </select>

                    <select
                        name="driver_id"
                        class="form-select expense-filter-control"
                        onchange="this.form.submit()"
                    >
                        <option value="">Semua Driver</option>
                        @foreach($drivers as $driver)
                            <option
                                value="{{ $driver->id }}"
                                {{ request('driver_id') == $driver->id ? 'selected' : '' }}
                            >
                                {{ $driver->name }}
                            </option>
                        @endforeach
                    </select>

                    <a
                        href="{{ route('expenses.index') }}"
                        class="btn-reset-expense"
                        title="Reset Filter"
                    >
                        <i class="fa-solid fa-rotate-left"></i>
                    </a>
                </form>
            </div>

            <div class="expense-table-wrap">
                <table class="expense-table" id="expenseTable">
                    <thead>
                        <tr>
                            <th style="width: 160px;">Tanggal / Waktu</th>
                            <th style="width: 170px;">Driver</th>
                            <th style="width: 110px;">Shift</th>
                            <th style="width: 140px;">Kode Delivery</th>
                            <th style="width: 130px;">Kategori</th>
                            <th>Deskripsi</th>
                            <th style="width: 150px;">Nominal</th>
                            <th style="width: 85px;" class="text-center">Struk</th>
                            <th style="width: 75px;" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($expenses as $expense)
                            @php
                                $category = strtolower($expense->category ?? 'other');

                                $categoryMeta = match($category) {
                                    'fuel' => [
                                        'label' => 'Fuel',
                                        'class' => 'fuel',
                                        'icon' => 'fa-gas-pump',
                                    ],
                                    'toll' => [
                                        'label' => 'E-Toll',
                                        'class' => 'toll',
                                        'icon' => 'fa-road-barrier',
                                    ],
                                    'parking' => [
                                        'label' => 'Parkir',
                                        'class' => 'parking',
                                        'icon' => 'fa-square-parking',
                                    ],
                                    'meal' => [
                                        'label' => 'Makan',
                                        'class' => 'meal',
                                        'icon' => 'fa-utensils',
                                    ],
                                    default => [
                                        'label' => ucfirst($expense->category ?? 'Lainnya'),
                                        'class' => 'other',
                                        'icon' => 'fa-receipt',
                                    ],
                                };

                                $driverName = $expense->driver->full_name ?? $expense->driver->name ?? 'Belum ditentukan';
                                $shiftDate = $expense->shift->work_date ?? '-';
                                $description = $expense->description ?? '-';

                                $deliveryCode = '-';
                                if ($expense->shift_id) {
                                    $taskData = \App\Models\PickupTask::where('shift_id', $expense->shift_id)->first();
                                    if ($taskData && $taskData->reference_number) {
                                        $deliveryCode = $taskData->reference_number;
                                    }
                                }

                                $searchText = strtolower(
                                    $driverName . ' ' .
                                    $shiftDate . ' ' .
                                    $deliveryCode . ' ' .
                                    $categoryMeta['label'] . ' ' .
                                    $description . ' ' .
                                    number_format($expense->amount, 0, ',', '.')
                                );
                            @endphp

                            <tr class="expense-row" data-search="{{ $searchText }}">
                                <td>
                                    <div class="expense-date-main">
                                        {{ $expense->occurred_at->format('d M Y') }}
                                    </div>
                                    <div class="expense-date-sub">
                                        <i class="fa-regular fa-clock"></i>
                                        {{ $expense->occurred_at->format('H:i') }}
                                    </div>
                                </td>

                                <td>
                                    <div class="expense-driver-wrap" style="align-items: center;">
                                        <span class="expense-driver-avatar">
                                            <i class="fa-solid fa-user"></i>
                                        </span>
                                        <div class="expense-driver-name">{{ $driverName }}</div>
                                    </div>
                                </td>

                                <td>
                                    <div style="font-size: 11.5px; font-weight: 700; color: #475569;">
                                        {{ $shiftDate }}
                                    </div>
                                </td>

                                <td>
                                    <div style="font-size: 13px; font-weight: 700; color: #475569;">
                                        {{ $deliveryCode }}
                                    </div>
                                </td>

                                <td>
                                    <span class="expense-category {{ $categoryMeta['class'] }}">
                                        <i class="fa-solid {{ $categoryMeta['icon'] }}"></i>
                                        {{ $categoryMeta['label'] }}
                                    </span>
                                </td>

                                <td>
                                    <div class="expense-description">
                                        {{ $description }}
                                    </div>
                                </td>

                                <td>
                                    <div class="expense-nominal">
                                        Rp {{ number_format($expense->amount, 0, ',', '.') }}
                                    </div>
                                </td>

                                <td class="text-center">
                                    @if($expense->receipt_url)
                                        <button
                                            type="button"
                                            class="expense-icon-btn receipt btn-preview-receipt"
                                            data-receipt-url="{{ $expense->receipt_url }}"
                                            title="Lihat Struk"
                                        >
                                            <i class="fa-regular fa-image"></i>
                                        </button>
                                    @else
                                        <span class="text-muted" style="font-size:10px;">—</span>
                                    @endif
                                </td>

                                <td class="text-center">
                                    <div class="dropdown expense-action-dropdown">
                                        <button
                                            type="button"
                                            class="expense-icon-btn more"
                                            data-bs-toggle="dropdown"
                                            aria-expanded="false"
                                            title="Aksi"
                                        >
                                            <i class="fa-solid fa-ellipsis"></i>
                                        </button>

                                        <div class="dropdown-menu dropdown-menu-end">
                                            @if($expense->receipt_url)
                                                <button
                                                    type="button"
                                                    class="dropdown-item btn-preview-receipt"
                                                    data-receipt-url="{{ $expense->receipt_url }}"
                                                >
                                                    <i class="fa-regular fa-eye me-2"></i>
                                                    Lihat Struk
                                                </button>
                                            @endif

                                            <form
                                                action="{{ route('expenses.destroy', $expense->id) }}"
                                                method="POST"
                                                onsubmit="return confirm('Apakah Anda yakin ingin menghapus pengeluaran ini?')"
                                            >
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="dropdown-item text-danger w-100 text-start">
                                                    <i class="fa-regular fa-trash-can me-2"></i>
                                                    Hapus
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr id="expenseEmptyDatabaseRow">
                                <td colspan="7" class="expense-empty">
                                    <div class="expense-empty-icon">
                                        <i class="fa-solid fa-receipt"></i>
                                    </div>
                                    <div class="expense-empty-title">Belum Ada Pengeluaran</div>
                                    <div class="expense-empty-desc">
                                        Data pengeluaran operasional akan muncul di sini.
                                    </div>
                                </td>
                            </tr>
                        @endforelse

                        {{-- Empty state client-side search --}}
                        <tr id="expenseSearchEmptyRow" style="display:none;">
                            <td colspan="7" class="expense-empty">
                                <div class="expense-empty-icon">
                                    <i class="fa-solid fa-magnifying-glass"></i>
                                </div>
                                <div class="expense-empty-title">Data Tidak Ditemukan</div>
                                <div class="expense-empty-desc">
                                    Coba gunakan kata pencarian yang berbeda.
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            @if($totalTransactions > 0)
                <div class="expense-table-footer">
                    <div class="expense-table-footer-left">
                        Menampilkan <strong id="visibleExpenseCount">{{ $expenseRows->count() }}</strong>
                        dari <strong>{{ $totalTransactions }}</strong> transaksi
                    </div>

                    <div class="expense-table-footer-total">
                        <span>Total Pengeluaran</span>
                        <strong>Rp {{ number_format($totalAmount, 0, ',', '.') }}</strong>
                    </div>
                </div>
            @endif
        </section>

        {{-- Pagination tetap menggunakan paginator bawaan jika tersedia --}}
        @if(method_exists($expenses, 'links'))
            <div class="mt-3">
                {{ $expenses->withQueryString()->links() }}
            </div>
        @endif
    </div>
</div>

{{-- Modal asli create expense tetap dipakai --}}
@include('expenses.Partials.create-modal')

{{-- RECEIPT PREVIEW MODAL --}}
<div class="modal fade" id="receiptPreviewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fa-regular fa-image me-2" style="color:#f97316;"></i>
                    Bukti Pengeluaran
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body p-0">
                <div class="receipt-preview-stage" id="receiptPreviewStage">
                    <img id="receiptPreviewImage" src="" alt="Bukti Pengeluaran" style="display:none;">
                    <iframe id="receiptPreviewPdf" src="" title="Bukti Pengeluaran PDF" style="display:none;"></iframe>
                </div>
            </div>

            <div class="modal-footer py-2 px-3">
                <a
                    href="#"
                    id="receiptOpenNewTab"
                    target="_blank"
                    rel="noopener"
                    class="btn btn-sm btn-outline-secondary"
                >
                    <i class="fa-solid fa-arrow-up-right-from-square me-1"></i>
                    Buka File
                </a>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    /* =========================================================
       CLIENT-SIDE SEARCH
       Hanya memfilter transaksi yang sedang tampil di halaman.
       Filter kategori dan driver tetap memakai backend lama.
    ========================================================= */
    const searchInput = document.getElementById('expenseTableSearch');
    const expenseRows = Array.from(document.querySelectorAll('#expenseTable .expense-row'));
    const searchEmptyRow = document.getElementById('expenseSearchEmptyRow');
    const visibleExpenseCount = document.getElementById('visibleExpenseCount');

    function filterExpenseRows() {
        if (!searchInput) return;

        const keyword = searchInput.value.toLowerCase().trim();
        let visible = 0;

        expenseRows.forEach(function (row) {
            const haystack = (row.dataset.search || '').toLowerCase();
            const show = !keyword || haystack.includes(keyword);

            row.style.display = show ? '' : 'none';
            if (show) visible++;
        });

        if (visibleExpenseCount) {
            visibleExpenseCount.textContent = visible;
        }

        if (searchEmptyRow) {
            searchEmptyRow.style.display = expenseRows.length > 0 && visible === 0 ? '' : 'none';
        }
    }

    if (searchInput) {
        searchInput.addEventListener('input', filterExpenseRows);
    }

    /* =========================================================
       RECEIPT PREVIEW
    ========================================================= */
    const receiptModalEl = document.getElementById('receiptPreviewModal');
    const receiptImage = document.getElementById('receiptPreviewImage');
    const receiptPdf = document.getElementById('receiptPreviewPdf');
    const receiptOpenNewTab = document.getElementById('receiptOpenNewTab');

    let receiptModal = null;

    if (receiptModalEl && window.bootstrap) {
        receiptModal = bootstrap.Modal.getOrCreateInstance(receiptModalEl);
    }

    document.addEventListener('click', function (event) {
        const button = event.target.closest('.btn-preview-receipt');
        if (!button) return;

        const url = button.dataset.receiptUrl;
        if (!url) return;

        const cleanUrl = url.split('?')[0].toLowerCase();
        const isPdf = cleanUrl.endsWith('.pdf');

        receiptImage.style.display = 'none';
        receiptImage.src = '';
        receiptPdf.style.display = 'none';
        receiptPdf.src = '';

        if (isPdf) {
            receiptPdf.src = url;
            receiptPdf.style.display = 'block';
        } else {
            receiptImage.src = url;
            receiptImage.style.display = 'block';
        }

        receiptOpenNewTab.href = url;

        if (receiptModal) {
            receiptModal.show();
        } else {
            window.open(url, '_blank', 'noopener');
        }
    });

    if (receiptModalEl) {
        receiptModalEl.addEventListener('hidden.bs.modal', function () {
            receiptImage.src = '';
            receiptPdf.src = '';
        });
    }
});
</script>
@endsection
