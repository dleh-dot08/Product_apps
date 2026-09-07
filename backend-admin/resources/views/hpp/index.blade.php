<x-app-layout>

<style>
    #hppDashboardPage {
        --hpp-orange: #f97316;
        --hpp-orange-dark: #ea580c;
        --hpp-orange-soft: #fff7ed;
        --hpp-blue: #3b82f6;
        --hpp-blue-soft: #eff6ff;
        --hpp-green: #10b981;
        --hpp-green-soft: #ecfdf5;
        --hpp-amber: #f59e0b;
        --hpp-amber-soft: #fffbeb;
        --hpp-red: #ef4444;
        --hpp-red-soft: #fef2f2;
        --hpp-violet: #8b5cf6;
        --hpp-violet-soft: #f5f3ff;
        --hpp-cyan: #06b6d4;
        --hpp-cyan-soft: #ecfeff;
        --hpp-bg: #f8fafc;
        --hpp-card: #ffffff;
        --hpp-border: #e5e7eb;
        --hpp-border-soft: #eef2f7;
        --hpp-text: #0f172a;
        --hpp-muted: #64748b;
        min-height: 100vh;
        background: var(--hpp-bg);
        color: var(--hpp-text);
        padding: 22px 24px 32px;
        font-size: clamp(10pt, 1vw + 8pt, 14pt);
    }

    #hppDashboardPage .hpp-container {
        max-width: 1680px;
        margin: 0 auto;
    }

    /* =========================================================
       HEADER
    ========================================================= */
    #hppDashboardPage .hpp-page-head {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        gap: 24px;
        margin-bottom: 18px;
    }

    #hppDashboardPage .hpp-eyebrow {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 10px;
        margin-bottom: 7px;
        border: 1px solid #fed7aa;
        border-radius: 999px;
        background: var(--hpp-orange-soft);
        color: var(--hpp-orange-dark);
        font-size: 10px;
        font-weight: 850;
        text-transform: uppercase;
        letter-spacing: .45px;
    }

    #hppDashboardPage .hpp-title {
        margin: 0;
        color: var(--hpp-text);
        font-size: clamp(22px, 2vw, 30px);
        font-weight: 900;
        letter-spacing: -.65px;
        line-height: 1.12;
    }

    #hppDashboardPage .hpp-subtitle {
        margin: 6px 0 0;
        color: var(--hpp-muted);
        font-size: 12px;
        font-weight: 500;
    }

    #hppDashboardPage .hpp-head-actions {
        display: flex;
        align-items: flex-end;
        gap: 10px;
        flex-wrap: wrap;
        justify-content: flex-end;
    }

    #hppDashboardPage .hpp-control-group label {
        display: block;
        margin: 0 0 5px 2px;
        color: var(--hpp-muted);
        font-size: 10px;
        font-weight: 800;
    }

    #hppDashboardPage .hpp-date-range {
        display: flex;
        align-items: center;
        gap: 7px;
        height: 42px;
        padding: 0 11px;
        border: 1px solid #dbe3ec;
        border-radius: 10px;
        background: #fff;
    }

    #hppDashboardPage .hpp-date-range i {
        color: #64748b;
        font-size: 12px;
    }

    #hppDashboardPage .hpp-date-range input {
        width: 108px;
        border: 0;
        outline: 0;
        background: transparent;
        color: #334155;
        font-size: 11px;
        font-weight: 700;
    }

    #hppDashboardPage .hpp-range-separator {
        color: #94a3b8;
        font-size: 11px;
    }

    #hppDashboardPage .btn-hpp-filter,
    #hppDashboardPage .btn-hpp-reset,
    #hppDashboardPage .btn-hpp-export {
        height: 42px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 7px;
        padding: 0 14px;
        border-radius: 10px;
        font-size: 11px;
        font-weight: 800;
        text-decoration: none !important;
        transition: .18s ease;
        white-space: nowrap;
    }

    #hppDashboardPage .btn-hpp-filter,
    #hppDashboardPage .btn-hpp-reset {
        border: 1px solid #dbe3ec;
        background: #fff;
        color: #334155;
    }

    #hppDashboardPage .btn-hpp-filter:hover,
    #hppDashboardPage .btn-hpp-reset:hover {
        border-color: #fdba74;
        color: var(--hpp-orange-dark);
        background: #fffaf5;
    }

    #hppDashboardPage .btn-hpp-export {
        border: 0;
        background: linear-gradient(135deg, var(--hpp-orange), var(--hpp-orange-dark));
        color: #fff !important;
        box-shadow: 0 7px 18px rgba(234, 88, 12, .18);
    }

    #hppDashboardPage .btn-hpp-export:hover {
        transform: translateY(-1px);
        box-shadow: 0 10px 22px rgba(234, 88, 12, .24);
    }

    /* =========================================================
       STAT CARDS
    ========================================================= */
    #hppDashboardPage .hpp-stats-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 16px;
        margin-bottom: 16px;
    }

    #hppDashboardPage .hpp-stat-card {
        min-height: 106px;
        position: relative;
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 16px 18px;
        overflow: hidden;
        border: 1px solid var(--hpp-border);
        border-radius: 14px;
        background: #fff;
        box-shadow: 0 7px 20px rgba(15, 23, 42, .035);
    }

    #hppDashboardPage .hpp-stat-card::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 3px;
        background: var(--stat-color);
    }

    #hppDashboardPage .hpp-stat-icon {
        width: 46px;
        height: 46px;
        flex: 0 0 46px;
        display: grid;
        place-items: center;
        border-radius: 12px;
        background: var(--stat-bg);
        color: var(--stat-color);
        font-size: 18px;
    }

    #hppDashboardPage .hpp-stat-content {
        min-width: 0;
        flex: 1;
    }

    #hppDashboardPage .hpp-stat-label {
        margin-bottom: 2px;
        color: var(--hpp-muted);
        font-size: 11px;
        font-weight: 750;
    }

    #hppDashboardPage .hpp-stat-value {
        color: var(--hpp-text);
        font-size: 21px;
        font-weight: 900;
        line-height: 1.2;
        letter-spacing: -.35px;
        white-space: nowrap;
    }

    #hppDashboardPage .hpp-stat-value .unit {
        color: #64748b;
        font-size: 11px;
        font-weight: 700;
        letter-spacing: 0;
    }

    #hppDashboardPage .hpp-stat-meta {
        margin-top: 4px;
        color: #94a3b8;
        font-size: 10px;
        font-weight: 600;
    }

    #hppDashboardPage .hpp-stat-side {
        align-self: center;
        margin-left: auto;
        text-align: right;
        color: var(--stat-color);
        font-size: 11px;
        font-weight: 850;
        white-space: nowrap;
    }

    #hppDashboardPage .hpp-stat-side small {
        display: block;
        margin-top: 2px;
        color: #94a3b8;
        font-size: 9px;
        font-weight: 600;
    }

    /* =========================================================
       GENERIC CARD
    ========================================================= */
    #hppDashboardPage .hpp-card {
        height: 100%;
        overflow: hidden;
        border: 1px solid var(--hpp-border);
        border-radius: 14px;
        background: #fff;
        box-shadow: 0 7px 22px rgba(15, 23, 42, .035);
    }

    #hppDashboardPage .hpp-card-header {
        min-height: 48px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 14px;
        padding: 12px 16px;
        border-bottom: 1px solid var(--hpp-border-soft);
        background: #fff;
    }

    #hppDashboardPage .hpp-card-title {
        display: flex;
        align-items: center;
        gap: 8px;
        margin: 0;
        color: var(--hpp-text);
        font-size: 14px;
        font-weight: 850;
    }

    #hppDashboardPage .hpp-card-title-icon {
        width: 27px;
        height: 27px;
        display: grid;
        place-items: center;
        border-radius: 8px;
        background: #f1f5f9;
        color: #475569;
        font-size: 11px;
    }

    #hppDashboardPage .hpp-analytics-grid {
        display: grid;
        grid-template-columns: minmax(0, 1fr) minmax(0, 1fr);
        gap: 16px;
        margin-bottom: 16px;
    }

    /* =========================================================
       DONUT / COMPOSITION
    ========================================================= */
    #hppDashboardPage .chart-select {
        height: 33px;
        min-width: 135px;
        border: 1px solid #dbe3ec;
        border-radius: 9px;
        padding: 0 30px 0 10px;
        background-color: #fff;
        color: #475569;
        font-size: 10px;
        font-weight: 700;
    }

    #hppDashboardPage .composition-body {
        min-height: 245px;
        display: grid;
        grid-template-columns: 45% 55%;
        align-items: center;
        gap: 12px;
        padding: 12px 18px 16px;
    }

    #hppDashboardPage .donut-box {
        position: relative;
        height: 220px;
        display: grid;
        place-items: center;
    }

    #hppDashboardPage .donut-box canvas {
        width: 220px !important;
        height: 220px !important;
        max-width: 100%;
    }

    #hppDashboardPage .composition-list {
        display: grid;
        gap: 0;
        padding-right: 8px;
    }

    #hppDashboardPage .composition-item {
        display: grid;
        grid-template-columns: 1fr 55px 110px;
        gap: 10px;
        align-items: center;
        min-height: 42px;
        border-bottom: 1px solid var(--hpp-border-soft);
        font-size: 11px;
    }

    #hppDashboardPage .composition-item:last-child {
        border-bottom: 0;
    }

    #hppDashboardPage .composition-name {
        display: flex;
        align-items: center;
        gap: 8px;
        color: #334155;
        font-weight: 750;
    }

    #hppDashboardPage .composition-dot {
        width: 11px;
        height: 11px;
        border-radius: 50%;
        flex: 0 0 11px;
    }

    #hppDashboardPage .composition-percent {
        color: #475569;
        font-weight: 800;
        text-align: right;
    }

    #hppDashboardPage .composition-amount {
        color: var(--hpp-text);
        font-weight: 850;
        text-align: right;
        white-space: nowrap;
    }

    /* =========================================================
       OPERATION SUMMARY
    ========================================================= */
    #hppDashboardPage .operational-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 11px;
    }

    #hppDashboardPage .operational-table th {
        padding: 10px 16px;
        border-bottom: 1px solid var(--hpp-border);
        background: #f8fafc;
        color: #64748b;
        font-size: 9.5px;
        font-weight: 850;
        text-transform: uppercase;
        letter-spacing: .35px;
    }

    #hppDashboardPage .operational-table td {
        padding: 10px 16px;
        border-bottom: 1px solid var(--hpp-border-soft);
        vertical-align: middle;
    }

    #hppDashboardPage .operational-table tfoot td {
        border-bottom: 0;
        font-weight: 900;
        font-size: 12px;
    }

    #hppDashboardPage .operational-component {
        display: flex;
        align-items: center;
        gap: 8px;
        color: #334155;
        font-weight: 750;
    }

    #hppDashboardPage .summary-progress-wrap {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    #hppDashboardPage .summary-progress {
        flex: 1;
        height: 7px;
        overflow: hidden;
        border-radius: 999px;
        background: #e9eef5;
    }

    #hppDashboardPage .summary-progress > span {
        display: block;
        height: 100%;
        border-radius: inherit;
    }

    /* =========================================================
       LOG RITASE / FILTER
    ========================================================= */
    #hppDashboardPage .log-card {
        margin-bottom: 4px;
    }

    #hppDashboardPage .log-count-badge {
        display: inline-flex;
        align-items: center;
        min-height: 29px;
        padding: 0 11px;
        border-radius: 999px;
        background: var(--hpp-orange-soft);
        color: var(--hpp-orange-dark);
        font-size: 10px;
        font-weight: 850;
    }

    #hppDashboardPage .ritase-filterbar {
        display: grid;
        grid-template-columns: minmax(180px, 1.35fr) 150px 180px 180px 84px auto;
        gap: 10px;
        align-items: center;
        padding: 10px 16px;
        border-bottom: 1px solid var(--hpp-border-soft);
        background: #fff;
    }

    #hppDashboardPage .filter-search {
        position: relative;
    }

    #hppDashboardPage .filter-search i {
        position: absolute;
        left: 12px;
        top: 50%;
        transform: translateY(-50%);
        color: #94a3b8;
        font-size: 11px;
        pointer-events: none;
    }

    #hppDashboardPage .filter-search input,
    #hppDashboardPage .ritase-filterbar select {
        width: 100%;
        height: 36px;
        border: 1px solid #dbe3ec;
        border-radius: 9px;
        background: #fff;
        color: #334155;
        font-size: 10.5px;
        font-weight: 650;
        outline: 0;
        box-shadow: none;
    }

    #hppDashboardPage .filter-search input {
        padding: 0 10px 0 34px;
    }

    #hppDashboardPage .ritase-filterbar select {
        padding: 0 28px 0 10px;
    }

    #hppDashboardPage .filter-search input:focus,
    #hppDashboardPage .ritase-filterbar select:focus {
        border-color: #fdba74;
        box-shadow: 0 0 0 3px rgba(249, 115, 22, .08);
    }

    #hppDashboardPage .ritase-filter-label {
        display: flex;
        align-items: center;
        gap: 6px;
    }

    #hppDashboardPage .ritase-filter-label > span {
        color: #64748b;
        font-size: 9.5px;
        font-weight: 750;
        white-space: nowrap;
    }

    #hppDashboardPage .btn-filter-reset {
        height: 36px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        border: 1px solid #dbe3ec;
        border-radius: 9px;
        background: #fff;
        color: #475569;
        font-size: 10px;
        font-weight: 800;
    }

    #hppDashboardPage .btn-filter-reset:hover {
        border-color: #fdba74;
        color: var(--hpp-orange-dark);
        background: #fffaf5;
    }

    /* =========================================================
       TABLE
    ========================================================= */
    #hppDashboardPage .ritase-table-wrap {
        overflow-x: auto;
    }

    #hppDashboardPage .ritase-table {
        width: 100%;
        min-width: 1050px;
        margin: 0;
        border-collapse: separate;
        border-spacing: 0;
        font-size: 11px;
    }

    #hppDashboardPage .ritase-table thead th {
        padding: 9px 14px;
        border-top: 0;
        border-bottom: 1px solid var(--hpp-border);
        background: #f8fafc;
        color: #64748b;
        font-size: 9.5px;
        font-weight: 850;
        text-transform: uppercase;
        letter-spacing: .35px;
        white-space: nowrap;
    }

    #hppDashboardPage .ritase-table tbody td {
        padding: 9px 14px;
        vertical-align: middle;
        border-top: 0;
        border-bottom: 1px solid var(--hpp-border-soft);
        background: #fff;
        color: #334155;
    }

    #hppDashboardPage .ritase-table tbody tr.main-ritase-row:hover td {
        background: #fffdfb;
    }

    #hppDashboardPage .plate-number {
        display: inline-flex;
        align-items: center;
        padding: 4px 8px;
        border-radius: 6px;
        background: #f1f5f9;
        color: #334155;
        font-size: 10px;
        font-weight: 850;
        letter-spacing: .25px;
        white-space: nowrap;
    }

    #hppDashboardPage .driver-name {
        color: #334155;
        font-weight: 700;
        white-space: nowrap;
    }

    #hppDashboardPage .delivery-code {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        white-space: nowrap;
    }

    #hppDashboardPage .delivery-code-ref {
        display: inline-flex;
        align-items: center;
        min-height: 22px;
        padding: 0 7px;
        border-radius: 6px;
        background: #f1f5f9;
        color: #334155;
        font-size: 9.5px;
        font-weight: 750;
    }

    #hppDashboardPage .delivery-type {
        display: inline-flex;
        align-items: center;
        min-height: 22px;
        padding: 0 7px;
        border-radius: 6px;
        color: #fff;
        font-size: 9px;
        font-weight: 750;
    }

    #hppDashboardPage .delivery-type.pickup { background: var(--hpp-amber); }
    #hppDashboardPage .delivery-type.delivery { background: var(--hpp-blue); }

    #hppDashboardPage .trip-metric {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        color: #475569;
        font-size: 10px;
        font-weight: 650;
        white-space: nowrap;
    }

    #hppDashboardPage .trip-metric i {
        color: #64748b;
        font-size: 10px;
    }

    #hppDashboardPage .cost-value {
        color: #0f172a;
        font-weight: 850;
        white-space: nowrap;
    }

    #hppDashboardPage .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 4px 8px;
        border-radius: 999px;
        font-size: 9.5px;
        font-weight: 850;
        white-space: nowrap;
    }

    #hppDashboardPage .status-badge::before {
        content: '';
        width: 5px;
        height: 5px;
        border-radius: 50%;
        background: currentColor;
    }

    #hppDashboardPage .status-completed { background: #ecfdf5; color: #16a34a; }
    #hppDashboardPage .status-trip { background: #eff6ff; color: #2563eb; }
    #hppDashboardPage .status-planned { background: #f1f5f9; color: #64748b; }

    #hppDashboardPage .action-cell {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
    }

    #hppDashboardPage .btn-detail-ritase {
        height: 30px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 5px;
        padding: 0 10px;
        border: 1px solid #dbe3ec;
        border-radius: 7px;
        background: #fff;
        color: #334155 !important;
        font-size: 9.5px;
        font-weight: 800;
        text-decoration: none !important;
        cursor: pointer;
    }

    #hppDashboardPage .btn-detail-ritase:hover {
        border-color: #fdba74;
        color: var(--hpp-orange-dark) !important;
        background: #fffaf5;
    }

    #hppDashboardPage .btn-row-more {
        width: 30px;
        height: 30px;
        display: grid;
        place-items: center;
        border: 0;
        border-radius: 7px;
        background: transparent;
        color: #64748b;
        cursor: pointer;
    }

    #hppDashboardPage .btn-row-more:hover {
        background: #f1f5f9;
        color: #0f172a;
    }

    #hppDashboardPage .empty-ritase {
        padding: 48px 20px !important;
        color: #94a3b8 !important;
        font-size: 11px !important;
    }

    #hppDashboardPage .log-footer {
        min-height: 52px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 14px;
        padding: 10px 16px;
        border-top: 1px solid var(--hpp-border-soft);
        background: #fff;
    }

    #hppDashboardPage .log-footer-text {
        color: #64748b;
        font-size: 10px;
        font-weight: 600;
    }

    #hppDashboardPage .log-footer-right {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    #hppDashboardPage .per-page-select {
        height: 34px;
        min-width: 105px;
        border: 1px solid #dbe3ec;
        border-radius: 8px;
        padding: 0 28px 0 9px;
        background: #fff;
        color: #475569;
        font-size: 10px;
        font-weight: 700;
    }

    #hppDashboardPage .pagination {
        margin: 0 !important;
    }

    #hppDashboardPage .page-link {
        min-width: 32px;
        height: 32px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px !important;
        margin: 0 2px;
        border-color: #e2e8f0;
        color: #475569;
        font-size: 10px;
        font-weight: 700;
    }

    #hppDashboardPage .page-item.active .page-link {
        border-color: var(--hpp-orange);
        background: var(--hpp-orange);
        color: #fff;
    }

    /* DETAIL ROW */
    #hppDashboardPage .ritase-detail-panel {
        padding: 18px;
        background: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
    }

    /* =========================================================
       DARK MODE
    ========================================================= */
    [data-bs-theme="dark"] #hppDashboardPage {
        --hpp-bg: #0b1220;
        --hpp-card: #111827;
        --hpp-border: rgba(255,255,255,.09);
        --hpp-border-soft: rgba(255,255,255,.07);
        --hpp-text: #f8fafc;
        --hpp-muted: #94a3b8;
    }

    [data-bs-theme="dark"] #hppDashboardPage .hpp-date-range,
    [data-bs-theme="dark"] #hppDashboardPage .btn-hpp-filter,
    [data-bs-theme="dark"] #hppDashboardPage .btn-hpp-reset,
    [data-bs-theme="dark"] #hppDashboardPage .hpp-stat-card,
    [data-bs-theme="dark"] #hppDashboardPage .hpp-card,
    [data-bs-theme="dark"] #hppDashboardPage .hpp-card-header,
    [data-bs-theme="dark"] #hppDashboardPage .ritase-filterbar,
    [data-bs-theme="dark"] #hppDashboardPage .filter-search input,
    [data-bs-theme="dark"] #hppDashboardPage .ritase-filterbar select,
    [data-bs-theme="dark"] #hppDashboardPage .operational-table td,
    [data-bs-theme="dark"] #hppDashboardPage .ritase-table tbody td,
    [data-bs-theme="dark"] #hppDashboardPage .log-footer,
    [data-bs-theme="dark"] #hppDashboardPage .per-page-select,
    [data-bs-theme="dark"] #hppDashboardPage .chart-select,
    [data-bs-theme="dark"] #hppDashboardPage .btn-detail-ritase {
        background: #111827;
        color: #e5e7eb;
        border-color: rgba(255,255,255,.10);
    }

    [data-bs-theme="dark"] #hppDashboardPage .operational-table th,
    [data-bs-theme="dark"] #hppDashboardPage .ritase-table thead th {
        background: #0f172a;
        color: #94a3b8;
    }

    [data-bs-theme="dark"] #hppDashboardPage .hpp-stat-value,
    [data-bs-theme="dark"] #hppDashboardPage .hpp-card-title,
    [data-bs-theme="dark"] #hppDashboardPage .composition-amount,
    [data-bs-theme="dark"] #hppDashboardPage .cost-value,
    [data-bs-theme="dark"] #hppDashboardPage .driver-name,
    [data-bs-theme="dark"] #hppDashboardPage .operational-component {
        color: #f8fafc;
    }

    [data-bs-theme="dark"] #hppDashboardPage .composition-name,
    [data-bs-theme="dark"] #hppDashboardPage .trip-metric {
        color: #cbd5e1;
    }

    [data-bs-theme="dark"] #hppDashboardPage .ritase-detail-panel {
        background: #0f172a;
    }

    /* =========================================================
       RESPONSIVE
    ========================================================= */
    @media (max-width: 1250px) {
        #hppDashboardPage .hpp-stats-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        #hppDashboardPage .ritase-filterbar {
            grid-template-columns: 1fr 160px 180px 180px 80px;
        }
    }

    @media (max-width: 991.98px) {
        #hppDashboardPage {
            padding: 18px;
        }

        #hppDashboardPage .hpp-page-head {
            align-items: stretch;
            flex-direction: column;
        }

        #hppDashboardPage .hpp-head-actions {
            justify-content: flex-start;
        }

        #hppDashboardPage .hpp-analytics-grid {
            grid-template-columns: 1fr;
        }

        #hppDashboardPage .ritase-filterbar {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        #hppDashboardPage .filter-search {
            grid-column: 1 / -1;
        }
    }

    @media (max-width: 640px) {
        #hppDashboardPage {
            padding: 12px;
        }

        #hppDashboardPage .hpp-stats-grid {
            grid-template-columns: 1fr;
        }

        #hppDashboardPage .hpp-head-actions,
        #hppDashboardPage .hpp-date-range {
            width: 100%;
        }

        #hppDashboardPage .hpp-date-range {
            justify-content: space-between;
        }

        #hppDashboardPage .hpp-date-range input {
            width: 110px;
        }

        #hppDashboardPage .composition-body {
            grid-template-columns: 1fr;
        }

        #hppDashboardPage .ritase-filterbar {
            grid-template-columns: 1fr;
        }

        #hppDashboardPage .filter-search {
            grid-column: auto;
        }

        #hppDashboardPage .log-footer {
            align-items: flex-start;
            flex-direction: column;
        }
    }
</style>

@php
    $currentShiftCollection = method_exists($shifts, 'getCollection')
        ? $shifts->getCollection()
        : collect($shifts);

    $completedVisible = $currentShiftCollection->filter(fn($shift) => !empty($shift->check_out_at))->count();

    $componentConfig = [
        'BBM' => [
            'label' => 'BBM',
            'color' => '#f97316',
            'value' => (float) ($costComposition['BBM'] ?? 0),
        ],
        'Manpower' => [
            'label' => 'Manpower',
            'color' => '#10b981',
            'value' => (float) ($costComposition['Manpower'] ?? 0),
        ],
        'Tol' => [
            'label' => 'Tol',
            'color' => '#3b82f6',
            'value' => (float) ($costComposition['Tol'] ?? 0),
        ],
        'Parkir' => [
            'label' => 'Parkir',
            'color' => '#8b5cf6',
            'value' => (float) ($costComposition['Parkir'] ?? 0),
        ],
        'Lainnya' => [
            'label' => 'Lainnya',
            'color' => '#06b6d4',
            'value' => (float) ($costComposition['Lainnya'] ?? 0),
        ],
    ];

    $compositionTotal = collect($componentConfig)->sum('value');

    $driverFilterOptions = $currentShiftCollection
        ->map(function ($shift) {
            return $shift->driver->full_name ?? $shift->driver->name ?? null;
        })
        ->filter()
        ->unique()
        ->values();

    $vehicleFilterOptions = $currentShiftCollection
        ->map(fn($shift) => $shift->vehicle->plate_number ?? null)
        ->filter()
        ->unique()
        ->values();

    $periodStart = request('date_from', now()->startOfMonth()->format('Y-m-d'));
    $periodEnd = request('date_to', now()->endOfMonth()->format('Y-m-d'));
@endphp

<div id="hppDashboardPage" class="content">
    <div class="hpp-container">

        {{-- HEADER --}}
        <div class="hpp-page-head">
            <div>
                <div class="hpp-eyebrow">
                    <i class="fa-solid fa-chart-line"></i>
                    HPP Ritase Dashboard
                </div>
                <h1 class="hpp-title">Monitor Harga Pokok Penjualan Trip</h1>
                <p class="hpp-subtitle">Pantau seluruh log ritase, kalkulasi biaya operasional armada, dan ekspor laporan dengan mudah.</p>
            </div>

            <div class="hpp-head-actions">
                <div class="hpp-control-group">
                    <label>Periode</label>
                    <div class="hpp-date-range">
                        <i class="far fa-calendar"></i>
                        <input type="date" id="hppDateFrom" value="{{ $periodStart }}">
                        <span class="hpp-range-separator">—</span>
                        <input type="date" id="hppDateTo" value="{{ $periodEnd }}">
                    </div>
                </div>

                <button type="button" class="btn-hpp-filter" id="hppApplyDateFilter">
                    <i class="fas fa-filter"></i>
                    Filter
                </button>

                <a href="{{ route('hpp.export') }}" class="btn-hpp-export">
                    <i class="fas fa-download"></i>
                    Export Excel
                </a>
            </div>
        </div>

        {{-- SUMMARY CARDS --}}
        <div class="hpp-stats-grid">
            <div class="hpp-stat-card" style="--stat-color:#3b82f6; --stat-bg:#eff6ff;">
                <div class="hpp-stat-icon"><i class="fas fa-truck"></i></div>
                <div class="hpp-stat-content">
                    <div class="hpp-stat-label">Total Ritase</div>
                    <div class="hpp-stat-value">{{ number_format($totalTrips, 0, ',', '.') }}</div>
                    <div class="hpp-stat-meta">{{ $completedVisible }} trip selesai pada halaman ini</div>
                </div>
            </div>

            <div class="hpp-stat-card" style="--stat-color:#10b981; --stat-bg:#ecfdf5;">
                <div class="hpp-stat-icon"><i class="fas fa-money-bill-wave"></i></div>
                <div class="hpp-stat-content">
                    <div class="hpp-stat-label">Total Biaya</div>
                    <div class="hpp-stat-value">Rp {{ number_format($totalCost, 0, ',', '.') }}</div>
                    <div class="hpp-stat-meta">Akumulasi biaya seluruh ritase</div>
                </div>
            </div>

            <div class="hpp-stat-card" style="--stat-color:#f59e0b; --stat-bg:#fffbeb;">
                <div class="hpp-stat-icon"><i class="fas fa-coins"></i></div>
                <div class="hpp-stat-content">
                    <div class="hpp-stat-label">Rata-rata HPP</div>
                    <div class="hpp-stat-value">Rp {{ number_format($avgHppPerItem, 0, ',', '.') }} <span class="unit">/ barang</span></div>
                    <div class="hpp-stat-meta">Rata-rata HPP per item barang</div>
                </div>
            </div>

            <div class="hpp-stat-card" style="--stat-color:#ef4444; --stat-bg:#fef2f2;">
                <div class="hpp-stat-icon"><i class="fas fa-location-dot"></i></div>
                <div class="hpp-stat-content">
                    <div class="hpp-stat-label">Total Jarak</div>
                    <div class="hpp-stat-value">{{ number_format($totalJarak, 0, ',', '.') }} <span class="unit">KM</span></div>
                    <div class="hpp-stat-meta">{{ floor($totalDurasi / 60) }} jam {{ $totalDurasi % 60 }} menit total durasi</div>
                </div>
            </div>
        </div>

        {{-- ANALYTICS --}}
        <div class="hpp-analytics-grid">
            {{-- KOMPOSISI BIAYA --}}
            <section class="hpp-card">
                <div class="hpp-card-header">
                    <h2 class="hpp-card-title">
                        <span class="hpp-card-title-icon"><i class="fas fa-chart-pie"></i></span>
                        Komposisi Biaya
                    </h2>
                    <select class="chart-select" aria-label="Jenis chart">
                        <option>Total Biaya</option>
                    </select>
                </div>

                <div class="composition-body">
                    <div class="donut-box">
                        <canvas id="costChart"></canvas>
                    </div>

                    <div class="composition-list">
                        @foreach($componentConfig as $component)
                            @php
                                $pct = $compositionTotal > 0
                                    ? ($component['value'] / $compositionTotal) * 100
                                    : 0;
                            @endphp
                            <div class="composition-item">
                                <div class="composition-name">
                                    <span class="composition-dot" style="background:{{ $component['color'] }};"></span>
                                    {{ $component['label'] }}
                                </div>
                                <div class="composition-percent">{{ number_format($pct, 0) }}%</div>
                                <div class="composition-amount">Rp {{ number_format($component['value'], 0, ',', '.') }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>

            {{-- RINGKASAN OPERASIONAL --}}
            <section class="hpp-card">
                <div class="hpp-card-header">
                    <h2 class="hpp-card-title">
                        <span class="hpp-card-title-icon"><i class="fas fa-file-lines"></i></span>
                        Ringkasan Operasional
                    </h2>
                </div>

                <div class="table-responsive">
                    <table class="operational-table">
                        <thead>
                            <tr>
                                <th>Komponen Biaya</th>
                                <th>Jumlah</th>
                                <th style="width:44%;">Persentase</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($componentConfig as $component)
                                @php
                                    $pct = $compositionTotal > 0
                                        ? ($component['value'] / $compositionTotal) * 100
                                        : 0;
                                @endphp
                                <tr>
                                    <td>
                                        <div class="operational-component">
                                            <span class="composition-dot" style="background:{{ $component['color'] }};"></span>
                                            {{ $component['label'] }}
                                        </div>
                                    </td>
                                    <td style="font-weight:850; white-space:nowrap;">Rp {{ number_format($component['value'], 0, ',', '.') }}</td>
                                    <td>
                                        <div class="summary-progress-wrap">
                                            <span style="width:38px; text-align:right; font-weight:800; color:#475569;">{{ number_format($pct, 0) }}%</span>
                                            <div class="summary-progress">
                                                <span style="width:{{ min(100, $pct) }}%; background:{{ $component['color'] }};"></span>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <td>Total</td>
                                <td>Rp {{ number_format($compositionTotal, 0, ',', '.') }}</td>
                                <td>100%</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </section>
        </div>

        {{-- LOG RITASE --}}
        <section class="hpp-card log-card">
            <div class="hpp-card-header">
                <h2 class="hpp-card-title">
                    <span class="hpp-card-title-icon"><i class="fas fa-truck"></i></span>
                    Log Ritase
                </h2>
                <span class="log-count-badge">{{ $shifts->total() ?? $shifts->count() }} perjalanan</span>
            </div>

            <div class="ritase-filterbar">
                <div class="filter-search">
                    <i class="fas fa-magnifying-glass"></i>
                    <input type="text" id="ritaseSearch" placeholder="Cari driver / kendaraan / delivery...">
                </div>

                <div class="ritase-filter-label">
                    <span>Status</span>
                    <select id="ritaseStatusFilter">
                        <option value="">Semua Status</option>
                        <option value="completed">Completed</option>
                        <option value="trip">On Trip</option>
                        <option value="planned">Planned</option>
                    </select>
                </div>

                <div class="ritase-filter-label">
                    <span>Driver</span>
                    <select id="ritaseDriverFilter">
                        <option value="">Semua Driver</option>
                        @foreach($driverFilterOptions as $driverName)
                            <option value="{{ strtolower($driverName) }}">{{ $driverName }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="ritase-filter-label">
                    <span>Kendaraan</span>
                    <select id="ritaseVehicleFilter">
                        <option value="">Semua Kendaraan</option>
                        @foreach($vehicleFilterOptions as $plate)
                            <option value="{{ strtolower($plate) }}">{{ $plate }}</option>
                        @endforeach
                    </select>
                </div>

                <button type="button" class="btn-filter-reset" id="ritaseResetFilter">
                    <i class="fas fa-rotate-left"></i>
                    Reset
                </button>
                <a href="{{ route('hpp.export') }}" class="btn-hpp-export" style="height: 36px; padding: 0 14px; font-size: 10px; border-radius: 9px; box-shadow:none;">
                    <i class="fas fa-file-excel"></i>
                    Generate Excel
                </a>
            </div>

            <div class="ritase-table-wrap">
                <table class="table ritase-table">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Kendaraan</th>
                            <th>Driver</th>
                            <th>Kode Delivery</th>
                            <th>Perjalanan</th>
                            <th>Total Biaya</th>
                            <th>Status</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="ritaseTableBody">

                        @forelse($shifts as $shift)
                            @php
                                $distanceValue = null;
                                $distanceLabel = '-';
                                if ($shift->start_odometer !== null && $shift->end_odometer !== null) {
                                    $distanceValue = max(0, $shift->end_odometer - $shift->start_odometer);
                                    $distanceLabel = number_format($distanceValue, 1) . ' KM';
                                }

                                $duration = '-';
                                if ($shift->check_in_at && $shift->check_out_at) {
                                    $durationMinutes = $shift->check_in_at->diffInMinutes($shift->check_out_at);
                                    $hours = floor($durationMinutes / 60);
                                    $minutes = $durationMinutes % 60;
                                    $duration = "{$hours}j {$minutes}m";
                                }

                                $driverName = $shift->driver->full_name ?? $shift->driver->name ?? '-';
                                $plateNumber = $shift->vehicle->plate_number ?? '-';
                                $statusKey = $shift->check_out_at ? 'completed' : ($shift->check_in_at ? 'trip' : 'planned');
                                $rowDate = $shift->work_date ? $shift->work_date->format('Y-m-d') : '';
                                $searchText = strtolower(trim(
                                    ($driverName ?? '') . ' ' .
                                    ($plateNumber ?? '') . ' ' .
                                    ($shift->task_reference ?? '')
                                ));
                            @endphp

                            <tr class="main-ritase-row"
                                data-search="{{ $searchText }}"
                                data-status="{{ $statusKey }}"
                                data-driver="{{ strtolower($driverName) }}"
                                data-vehicle="{{ strtolower($plateNumber) }}"
                                data-date="{{ $rowDate }}"
                            >
                                <td>
                                    <span style="white-space:nowrap;">
                                        <i class="far fa-calendar-alt" style="color:#64748b; margin-right:5px;"></i>
                                        {{ $shift->work_date->format('d M Y') }}
                                    </span>
                                </td>

                                <td>
                                    <span class="plate-number">{{ $plateNumber }}</span>
                                </td>

                                <td>
                                    <span class="driver-name">{{ $driverName }}</span>
                                </td>

                                <td>
                                    @if($shift->task_reference)
                                        <div class="delivery-code">
                                            <span class="delivery-code-ref">{{ $shift->task_reference }}</span>
                                            @if(stripos($shift->task_reference, 'PO') !== false || stripos($shift->task_reference, 'purchase') !== false)
                                                <span class="delivery-type pickup">Ambil</span>
                                            @else
                                                <span class="delivery-type delivery">Kirim</span>
                                            @endif
                                        </div>
                                    @else
                                        <span style="color:#94a3b8;">-</span>
                                    @endif
                                </td>

                                <td>
                                    <div class="trip-metric">
                                        <i class="fas fa-location-dot"></i>
                                        <span>{{ $duration }}</span>
                                        <span style="color:#cbd5e1;">•</span>
                                        <span>{{ $distanceLabel }}</span>
                                    </div>
                                </td>

                                <td>
                                    <span class="cost-value">Rp {{ number_format($shift->total_cost, 0, ',', '.') }}</span>
                                </td>

                                <td>
                                    @if($statusKey === 'completed')
                                        <span class="status-badge status-completed">Completed</span>
                                    @elseif($statusKey === 'trip')
                                        <span class="status-badge status-trip">On Trip</span>
                                    @else
                                        <span class="status-badge status-planned">Planned</span>
                                    @endif
                                </td>

                                <td>
                                    <div class="action-cell">
                                        <button type="button" class="btn-row-more toggle-btn" data-toggle="collapse" data-target="#detail-{{ $shift->id }}" title="Buka rincian">
                                            <i class="fas fa-chevron-down transition-icon"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                                {{-- Collapsible Detail Row --}}
                                <tr>
                                    <td colspan="8" class="p-0 border-0">
                                        <div id="detail-{{ $shift->id }}" class="collapse">
                                            <div class="ritase-detail-panel">
                                            <div class="row">
                                                {{-- Rincian Biaya Operasional --}}
                                                <div class="col-12 mb-4">
                                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                                        <h6 class="font-weight-bold m-0" style="color: #1e293b;"><i class="fas fa-money-bill-wave text-success mr-2"></i> Rincian Biaya Operasional</h6>
                                                        <span class="badge" style="background-color: #f1f5f9; color: #475569; font-size:11px; padding: 6px 10px;">
                                                            Total Jarak: {{ number_format(max(0, $shift->end_odometer - $shift->start_odometer), 0) }} KM &bull;
                                                            Durasi: {{ $shift->check_out_at && $shift->check_in_at ? floor(\Carbon\Carbon::parse($shift->check_in_at)->diffInMinutes($shift->check_out_at)/60) . 'j ' . (\Carbon\Carbon::parse($shift->check_in_at)->diffInMinutes($shift->check_out_at)%60) . 'm' : '-' }}
                                                        </span>
                                                    </div>
                                                    
                                                    <div class="row">
                                                        <div class="col-md-3 col-sm-6 mb-2">
                                                            <div style="background: #fff; border: 1px solid #e2e8f0; border-radius: 8px; padding: 12px;">
                                                                <div class="text-muted mb-1" style="font-size: 10px; font-weight:700; text-transform:uppercase;">Biaya BBM</div>
                                                                <div class="font-weight-bold" style="color:#0f172a;">Rp {{ number_format($shift->calc_details['costs']['fuel'], 0, ',', '.') }}</div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3 col-sm-6 mb-2">
                                                            <div style="background: #fff; border: 1px solid #e2e8f0; border-radius: 8px; padding: 12px;">
                                                                <div class="text-muted mb-1" style="font-size: 10px; font-weight:700; text-transform:uppercase;">Biaya Manpower</div>
                                                                <div class="font-weight-bold" style="color:#0f172a;">Rp {{ number_format($shift->calc_details['costs']['manpower'], 0, ',', '.') }}</div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3 col-sm-6 mb-2">
                                                            <div style="background: #fff; border: 1px solid #e2e8f0; border-radius: 8px; padding: 12px;">
                                                                <div class="text-muted mb-1" style="font-size: 10px; font-weight:700; text-transform:uppercase;">Tol & Parkir</div>
                                                                <div class="font-weight-bold" style="color:#0f172a;">Rp {{ number_format($shift->calc_details['costs']['toll'] + $shift->calc_details['costs']['parking'], 0, ',', '.') }}</div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3 col-sm-6 mb-2">
                                                            <div style="background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 8px; padding: 12px;">
                                                                <div class="text-primary mb-1" style="font-size: 10px; font-weight:800; text-transform:uppercase;">Total Biaya</div>
                                                                <div class="font-weight-bold text-primary" style="font-size: 15px;">Rp {{ number_format($shift->calc_details['costs']['total'], 0, ',', '.') }}</div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                {{-- Alokasi HPP per Barang --}}
                                                <div class="col-12">
                                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                                        <h6 class="font-weight-bold m-0" style="color: #1e293b;"><i class="fas fa-box-open text-primary mr-2"></i> Alokasi HPP per Barang</h6>
                                                        <span class="badge" style="background-color: #fff7ed; color: #ea580c; border: 1px solid #fed7aa; padding: 6px 12px;">
                                                            <i class="fas fa-calculator mr-1"></i> Skenario: {{ $shift->calc_details['is_prorata'] ? 'Prorata Nilai Barang (Value)' : 'Bagi Rata (Flat)' }}
                                                        </span>
                                                    </div>
                                                    
                                                    <div class="table-responsive" style="border: 1px solid #e2e8f0; border-radius: 10px; overflow:hidden;">
                                                        <table class="table table-sm table-hover mb-0" style="background: #fff;">
                                                            <thead style="background: #f8fafc;">
                                                                <tr>
                                                                    <th class="border-0 text-muted" style="font-size:10px; font-weight:800; text-transform:uppercase; padding: 12px 15px;">Kode / Deskripsi</th>
                                                                    <th class="border-0 text-muted text-right" style="font-size:10px; font-weight:800; text-transform:uppercase; padding: 12px 15px;">Qty</th>
                                                                    <th class="border-0 text-muted text-right" style="font-size:10px; font-weight:800; text-transform:uppercase; padding: 12px 15px;">Harga Satuan</th>
                                                                    <th class="border-0 text-muted text-right" style="font-size:10px; font-weight:800; text-transform:uppercase; padding: 12px 15px;">Nilai Barang (Total)</th>
                                                                    <th class="border-0 text-muted text-right" style="font-size:10px; font-weight:800; text-transform:uppercase; padding: 12px 15px;">HPP per Baris</th>
                                                                    <th class="border-0 text-muted text-right" style="font-size:10px; font-weight:800; text-transform:uppercase; padding: 12px 15px;">HPP / Qty</th>
                                                                    <th class="border-0 text-muted text-right" style="font-size:10px; font-weight:800; text-transform:uppercase; padding: 12px 15px;">% Beban</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @forelse($shift->calc_details['allocations'] as $item)
                                                                @php
                                                                    $descRaw = $item['item_description'] ?? '';
                                                                    $descJson = json_decode($descRaw, true);
                                                                    $descText = (is_array($descJson) && isset($descJson['summary'])) ? $descJson['summary'] : $descRaw;
                                                                @endphp
                                                                <tr>
                                                                    <td style="padding: 12px 15px; border-color: #f1f5f9;">
                                                                        <strong style="color: #0f172a; font-size:12px;">{{ $item['reference_number'] }}</strong><br>
                                                                        <small class="text-muted" style="font-size:11px;">{{ $descText }}</small>
                                                                    </td>
                                                                    <td class="text-right" style="padding: 12px 15px; border-color: #f1f5f9;">
                                                                        <span style="color:#334155; font-weight:700;">{{ number_format($item['quantity'], 2) }}</span>
                                                                        <small class="text-muted ml-1">{{ $item['unit'] }}</small>
                                                                    </td>
                                                                    <td class="text-right" style="padding: 12px 15px; border-color: #f1f5f9; color:#475569;">
                                                                        Rp {{ number_format(isset($item['unit_price']) ? $item['unit_price'] : ($item['quantity'] > 0 ? $item['line_total'] / $item['quantity'] : 0), 0, ',', '.') }}
                                                                    </td>
                                                                    <td class="text-right" style="padding: 12px 15px; border-color: #f1f5f9; color:#475569;">
                                                                        Rp {{ number_format($item['line_total'], 0, ',', '.') }}
                                                                    </td>
                                                                    <td class="text-right font-weight-bold" style="padding: 12px 15px; border-color: #f1f5f9; color:#ea580c;">
                                                                        Rp {{ number_format($item['hpp_per_baris'], 0, ',', '.') }}
                                                                    </td>
                                                                    <td class="text-right font-weight-bold" style="padding: 12px 15px; border-color: #f1f5f9; color:#0f172a;">
                                                                        Rp {{ number_format($item['hpp_per_qty'], 0, ',', '.') }}
                                                                    </td>
                                                                    <td class="text-right" style="padding: 12px 15px; border-color: #f1f5f9; width: 120px;">
                                                                        <div class="progress progress-xs mb-1" style="height: 6px; border-radius: 3px; background: #e2e8f0;">
                                                                            <div class="progress-bar" style="width: {{ $item['percentage'] }}%; background: #ea580c;"></div>
                                                                        </div>
                                                                        <small class="font-weight-bold" style="color:#64748b;">{{ number_format($item['percentage'], 1) }}%</small>
                                                                    </td>
                                                                </tr>
                                                                @empty
                                                                <tr>
                                                                    <td colspan="7" class="text-center text-muted py-4" style="background:#f8fafc;">
                                                                        <i class="fas fa-box-open mb-2 text-muted" style="font-size:24px; opacity: 0.5;"></i><br>
                                                                        Tidak ada rincian barang dalam ritase ini.
                                                                    </td>
                                                                </tr>
                                                                @endforelse
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        </div>
                                    </td>
                                </tr>
                                    
                        @empty
                            <tr>
                                <td colspan="8" class="text-center empty-ritase">
                                    <i class="fas fa-inbox d-block mb-2" style="font-size:26px;"></i>
                                    Belum ada data ritase.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="log-footer">
                <div class="log-footer-text">
                    Menampilkan <strong id="visibleRitaseCount">{{ $shifts->count() }}</strong> dari {{ $shifts->total() ?? $shifts->count() }} data
                </div>

                <div class="log-footer-right">
                    @if($shifts->hasPages())
                        {{ $shifts->appends(request()->query())->links('pagination::bootstrap-4') }}
                    @endif

                    <select class="per-page-select" onchange="changePerPage(this)">
                        <option value="10" {{ request('per_page', 10) == '10' ? 'selected' : '' }}>10 / halaman</option>
                        <option value="20" {{ request('per_page') == '20' ? 'selected' : '' }}>20 / halaman</option>
                        <option value="50" {{ request('per_page') == '50' ? 'selected' : '' }}>50 / halaman</option>
                        <option value="100" {{ request('per_page') == '100' ? 'selected' : '' }}>100 / halaman</option>
                    </select>
                </div>
            </div>
        </section>
    </div>
</div>

<script>
    function changePerPage(select) {
        const url = new URL(window.location.href);
        url.searchParams.set('per_page', select.value);
        url.searchParams.set('page', 1);
        window.location.href = url.href;
    }

    document.addEventListener('DOMContentLoaded', function () {
        /* =====================================================
           DONUT CHART
        ====================================================== */
        const canvas = document.getElementById('costChart');

        if (canvas && typeof Chart !== 'undefined') {
            const totalCostForChart = {{ (float) $compositionTotal }};

            const centerTextPlugin = {
                id: 'centerText',
                afterDraw(chart) {
                    const { ctx, chartArea } = chart;
                    if (!chartArea) return;

                    const x = (chartArea.left + chartArea.right) / 2;
                    const y = (chartArea.top + chartArea.bottom) / 2;

                    ctx.save();
                    ctx.textAlign = 'center';
                    ctx.textBaseline = 'middle';

                    ctx.fillStyle = getComputedStyle(document.documentElement)
                        .getPropertyValue('--bs-body-color') || '#0f172a';
                    ctx.font = '800 15px Inter, Arial, sans-serif';
                    ctx.fillText(
                        'Rp ' + Number(totalCostForChart).toLocaleString('id-ID'),
                        x,
                        y - 7
                    );

                    ctx.fillStyle = '#64748b';
                    ctx.font = '600 10px Inter, Arial, sans-serif';
                    ctx.fillText('Total Biaya', x, y + 13);
                    ctx.restore();
                }
            };

            new Chart(canvas, {
                type: 'doughnut',
                data: {
                    labels: @json(collect($componentConfig)->pluck('label')->values()),
                    datasets: [{
                        data: @json(collect($componentConfig)->pluck('value')->values()),
                        backgroundColor: @json(collect($componentConfig)->pluck('color')->values()),
                        borderWidth: 0,
                        hoverOffset: 3
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '64%',
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: '#0f172a',
                            titleColor: '#fff',
                            bodyColor: '#fff',
                            padding: 10,
                            cornerRadius: 8,
                            callbacks: {
                                label(context) {
                                    const value = Number(context.raw || 0);
                                    const total = context.dataset.data.reduce((a, b) => Number(a) + Number(b), 0);
                                    const percent = total > 0 ? (value / total) * 100 : 0;
                                    return `${context.label}: Rp ${value.toLocaleString('id-ID')} (${percent.toFixed(1)}%)`;
                                }
                            }
                        }
                    }
                },
                plugins: [centerTextPlugin]
            });
        }

        /* =====================================================
           CLIENT FILTER UNTUK DATA YANG SEDANG DITAMPILKAN
        ====================================================== */
        const searchInput = document.getElementById('ritaseSearch');
        const statusFilter = document.getElementById('ritaseStatusFilter');
        const driverFilter = document.getElementById('ritaseDriverFilter');
        const vehicleFilter = document.getElementById('ritaseVehicleFilter');
        const dateFrom = document.getElementById('hppDateFrom');
        const dateTo = document.getElementById('hppDateTo');
        const applyDateFilter = document.getElementById('hppApplyDateFilter');
        const resetButton = document.getElementById('ritaseResetFilter');
        const countLabel = document.getElementById('visibleRitaseCount');

        function applyFilters() {
            const searchValue = (searchInput?.value || '').trim().toLowerCase();
            const statusValue = statusFilter?.value || '';
            const driverValue = (driverFilter?.value || '').toLowerCase();
            const vehicleValue = (vehicleFilter?.value || '').toLowerCase();
            const startValue = dateFrom?.value || '';
            const endValue = dateTo?.value || '';

            let visible = 0;

            document.querySelectorAll('#ritaseTableBody .main-ritase-row').forEach(row => {
                const haystack = row.dataset.search || '';
                const status = row.dataset.status || '';
                const driver = row.dataset.driver || '';
                const vehicle = row.dataset.vehicle || '';
                const rowDate = row.dataset.date || '';

                const matchSearch = !searchValue || haystack.includes(searchValue);
                const matchStatus = !statusValue || status === statusValue;
                const matchDriver = !driverValue || driver === driverValue;
                const matchVehicle = !vehicleValue || vehicle === vehicleValue;
                const matchStart = !startValue || !rowDate || rowDate >= startValue;
                const matchEnd = !endValue || !rowDate || rowDate <= endValue;

                const show = matchSearch && matchStatus && matchDriver && matchVehicle && matchStart && matchEnd;
                row.style.display = show ? '' : 'none';

                const nextRow = row.nextElementSibling;
                if (nextRow && nextRow.id && nextRow.id.startsWith('detail-')) {
                    if (!show) {
                        nextRow.classList.remove('show');
                        nextRow.style.display = 'none';
                    } else {
                        nextRow.style.display = '';
                    }
                }

                if (show) visible++;
            });

            if (countLabel) countLabel.textContent = visible;
        }

        [searchInput, statusFilter, driverFilter, vehicleFilter].forEach(element => {
            if (!element) return;
            element.addEventListener(element.tagName === 'INPUT' ? 'input' : 'change', applyFilters);
        });

        applyDateFilter?.addEventListener('click', applyFilters);

        resetButton?.addEventListener('click', function () {
            if (searchInput) searchInput.value = '';
            if (statusFilter) statusFilter.value = '';
            if (driverFilter) driverFilter.value = '';
            if (vehicleFilter) vehicleFilter.value = '';
            if (dateFrom) dateFrom.value = '';
            if (dateTo) dateTo.value = '';
            applyFilters();
        });

        /* =====================================================
           MANUAL TOGGLE HANDLER
        ====================================================== */
        document.querySelectorAll('.toggle-btn').forEach(function (btn) {
            btn.addEventListener('click', function (e) {
                e.preventDefault();
                var targetSelector = this.getAttribute('data-target') || this.getAttribute('data-bs-target');
                if (targetSelector) {
                    var targetEl = document.querySelector(targetSelector);
                    if (targetEl) {
                        targetEl.classList.toggle('show');
                        // Toggle icon if needed
                        var icon = this.querySelector('i');
                        if (icon) {
                            if (targetEl.classList.contains('show')) {
                                icon.classList.remove('fa-chevron-down');
                                icon.classList.add('fa-chevron-up');
                            } else {
                                icon.classList.remove('fa-chevron-up');
                                icon.classList.add('fa-chevron-down');
                            }
                        }
                    }
                }
            });
        });
    });
</script>

</x-app-layout>
