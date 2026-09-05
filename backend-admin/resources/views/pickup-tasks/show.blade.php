<x-app-layout>
<style>
    :root {
        --task-bg: #f8fafc;
        --task-card: #ffffff;
        --task-text: #0f172a;
        --task-muted: #64748b;
        --task-border: #e2e8f0;
        --task-border-soft: #eef2f7;
        --task-orange: #f97316;
        --task-orange-dark: #ea580c;
        --task-orange-soft: #fff7ed;
        --task-green: #16a34a;
        --task-green-soft: #ecfdf3;
        --task-blue: #2563eb;
        --task-blue-soft: #eff6ff;
        --task-purple: #7c3aed;
        --task-purple-soft: #f5f3ff;
        --task-red: #ef4444;
        --task-shadow: 0 8px 24px rgba(15, 23, 42, .045);
    }

    .task-show-page {
        min-height: 100vh;
        padding: 18px 20px 28px;
        background:
            radial-gradient(circle at top right, rgba(249, 115, 22, .035), transparent 28%),
            var(--task-bg);
        color: var(--task-text);
    }

    .task-show-container {
        width: 100%;
        max-width: 1600px;
        margin: 0 auto;
    }

    /* =========================================================
       TOP BAR
    ========================================================= */
    .task-topbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        margin-bottom: 12px;
    }

    .btn-back-task,
    .btn-edit-status,
    .btn-more-task {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
        transition: .18s ease;
    }

    .btn-back-task {
        min-height: 42px;
        padding: 0 17px;
        gap: 9px;
        border: 1px solid var(--task-border);
        border-radius: 10px;
        background: #fff;
        color: #334155;
        font-size: 14px;
        font-weight: 800;
        box-shadow: 0 2px 8px rgba(15,23,42,.03);
    }

    .btn-back-task:hover {
        color: var(--task-orange-dark);
        border-color: #fdba74;
        background: #fffaf5;
    }

    .topbar-actions {
        display: flex;
        align-items: flex-end;
        gap: 10px;
    }

    .status-control {
        width: 230px;
    }

    .status-control label {
        display: block;
        margin: 0 0 4px 3px;
        color: var(--task-muted);
        font-size: 12px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: .65px;
    }

    .status-select {
        height: 42px;
        border: 1px solid var(--task-border);
        border-radius: 10px;
        padding: 0 38px 0 14px;
        background-color: #fff;
        color: var(--task-text);
        font-size: 14px;
        font-weight: 750;
        box-shadow: none;
    }

    .status-select:focus {
        border-color: #fdba74;
        box-shadow: 0 0 0 3px rgba(249,115,22,.10);
    }

    .btn-edit-status {
        min-height: 42px;
        padding: 0 18px;
        gap: 8px;
        border: 0;
        border-radius: 10px;
        background: linear-gradient(135deg, #fb923c, var(--task-orange));
        color: #fff;
        font-size: 14px;
        font-weight: 800;
        box-shadow: 0 7px 15px rgba(249,115,22,.18);
    }

    .btn-edit-status:hover {
        color: #fff;
        background: linear-gradient(135deg, var(--task-orange), var(--task-orange-dark));
    }

    .btn-more-task {
        width: 42px;
        height: 42px;
        border: 1px solid var(--task-border);
        border-radius: 10px;
        background: #fff;
        color: #64748b;
    }

    .btn-more-task:hover {
        color: var(--task-orange-dark);
        border-color: #fdba74;
    }

    /* =========================================================
       BASE CARD / HEADING
    ========================================================= */
    .task-card {
        background: var(--task-card);
        border: 1px solid var(--task-border);
        border-radius: 13px;
        box-shadow: var(--task-shadow);
    }

    .section-card {
        padding: 15px 16px;
    }

    .section-heading {
        display: flex;
        align-items: center;
        gap: 9px;
        margin-bottom: 12px;
        padding-bottom: 10px;
        border-bottom: 1px solid var(--task-border-soft);
        color: var(--task-text);
        font-size: 14px;
        font-weight: 850;
    }

    .section-heading i {
        color: var(--task-orange);
        font-size: 14px;
    }

    .section-heading.no-line {
        padding-bottom: 0;
        border-bottom: 0;
    }

    /* =========================================================
       HERO
    ========================================================= */
    .task-hero {
        position: relative;
        overflow: hidden;
        margin-bottom: 12px;
    }

    .task-hero::before {
        content: '';
        position: absolute;
        inset: 0 auto 0 0;
        width: 4px;
        background: linear-gradient(180deg, var(--task-orange), var(--task-orange-dark));
    }

    .hero-summary {
        display: grid;
        grid-template-columns: 1.25fr 1fr 1fr 1.15fr;
        min-height: 102px;
        padding: 13px 20px 10px;
    }

    .hero-block {
        min-width: 0;
        display: flex;
        align-items: center;
        gap: 13px;
        padding: 6px 18px;
        border-right: 1px solid var(--task-border-soft);
    }

    .hero-block:first-child { padding-left: 8px; }
    .hero-block:last-child { border-right: 0; }

    .hero-main-icon {
        width: 58px;
        height: 58px;
        flex: 0 0 58px;
        display: grid;
        place-items: center;
        border: 1px solid #fed7aa;
        border-radius: 12px;
        background: var(--task-orange-soft);
        color: var(--task-orange);
        font-size: 14px;
    }

    .hero-kicker {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        margin-bottom: 4px;
        padding: 3px 7px;
        border-radius: 6px;
        background: var(--task-orange-soft);
        color: var(--task-orange-dark);
        font-size: 12px;
        font-weight: 850;
        text-transform: uppercase;
        letter-spacing: .3px;
    }

    .hero-number {
        margin: 0;
        color: #0f2547;
        font-size: 25px;
        line-height: 1.05;
        font-weight: 900;
        letter-spacing: -.35px;
    }

    .hero-subtitle {
        margin-top: 4px;
        color: #475569;
        font-size: 13px;
        font-weight: 650;
    }

    .hero-label {
        margin-bottom: 7px;
        color: #64748b;
        font-size: 12px;
        font-weight: 850;
        text-transform: uppercase;
        letter-spacing: .55px;
    }

    .hero-value {
        display: flex;
        align-items: flex-start;
        gap: 9px;
        color: var(--task-text);
        font-size: 14px;
        font-weight: 800;
        line-height: 1.4;
    }

    .hero-value > i {
        width: 18px;
        margin-top: 2px;
        color: #526681;
        font-size: 14px;
        text-align: center;
    }

    .hero-secondary {
        display: block;
        margin-top: 2px;
        color: var(--task-muted);
        font-size: 12.5px;
        font-weight: 600;
    }

    /* =========================================================
       PROGRESS TRACKER
    ========================================================= */
    .progress-wrap {
        padding: 8px 42px 15px;
        border-top: 1px solid var(--task-border-soft);
    }

    .journey-progress {
        display: grid;
        grid-template-columns: repeat(5, minmax(0,1fr));
    }

    .progress-step {
        position: relative;
        text-align: center;
    }

    .progress-step::after {
        content: '';
        position: absolute;
        z-index: 0;
        top: 19px;
        left: calc(50% + 24px);
        width: calc(100% - 48px);
        height: 2px;
        background: #dbe3ec;
    }

    .progress-step:last-child::after { display: none; }

    .progress-icon {
        position: relative;
        z-index: 1;
        width: 40px;
        height: 40px;
        margin: 0 auto 6px;
        display: grid;
        place-items: center;
        border: 1px solid #cbd5e1;
        border-radius: 50%;
        background: #f8fafc;
        color: #64748b;
        font-size: 14px;
    }

    .progress-step.complete .progress-icon {
        border-color: var(--task-orange);
        background: var(--task-orange);
        color: #fff;
        box-shadow: 0 0 0 4px #fff7ed;
    }

    .progress-title {
        color: #1e293b;
        font-size: 12.5px;
        font-weight: 850;
    }

    .progress-subtitle {
        margin-top: 2px;
        color: #64748b;
        font-size: 12px;
        font-weight: 550;
    }

    /* =========================================================
       2 COLUMN PAGE
    ========================================================= */
    .task-layout {
        display: grid;
        grid-template-columns: minmax(0, 1.4fr) minmax(420px, 1fr);
        gap: 12px;
        align-items: start;
    }

    .task-column {
        display: flex;
        flex-direction: column;
        gap: 12px;
        min-width: 0;
    }

    /* =========================================================
       ASSIGNMENT INFO
    ========================================================= */
    .section-heading-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 10px;
        padding-bottom: 9px;
        border-bottom: 1px solid var(--task-border-soft);
    }

    .section-heading-row .section-heading {
        margin: 0;
        padding: 0;
        border: 0;
    }

    .task-type-pill {
        padding: 4px 9px;
        border: 1px solid #fed7aa;
        border-radius: 7px;
        background: var(--task-orange-soft);
        color: var(--task-orange-dark);
        font-size: 12px;
        font-weight: 850;
    }

    .assignment-grid {
        display: grid;
        grid-template-columns: repeat(5, minmax(0, 1fr));
    }

    .assignment-cell {
        min-height: 69px;
        padding: 8px 12px;
        border-right: 1px solid var(--task-border-soft);
    }

    .assignment-cell:nth-child(5n) { border-right: 0; }
    .assignment-cell:nth-child(n+6) { border-top: 1px solid var(--task-border-soft); }

    .assignment-label {
        margin-bottom: 5px;
        color: var(--task-muted);
        font-size: 12px;
        font-weight: 850;
        text-transform: uppercase;
        letter-spacing: .4px;
    }

    .assignment-value {
        display: flex;
        align-items: flex-start;
        gap: 7px;
        color: #1e293b;
        font-size: 12.5px;
        font-weight: 800;
        line-height: 1.35;
    }

    .assignment-value > i {
        width: 16px;
        margin-top: 2px;
        color: #526681;
        font-size: 14px;
        text-align: center;
    }

    .assignment-sub {
        display: block;
        margin-top: 2px;
        color: var(--task-muted);
        font-size: 12px;
        font-weight: 550;
    }

    .priority-badge,
    .mini-state {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 3px 8px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 850;
        white-space: nowrap;
    }

    .priority-badge {
        border: 1px solid #fed7aa;
        background: var(--task-orange-soft);
        color: var(--task-orange-dark);
    }

    .mini-state.success {
        border: 1px solid #bbf7d0;
        background: #f0fdf4;
        color: #15803d;
    }

    .mini-state.pending {
        border: 1px solid #e2e8f0;
        background: #f8fafc;
        color: #64748b;
    }

    /* =========================================================
       CHECKLIST
    ========================================================= */
    .checklist-tabs {
        display: flex;
        gap: 8px;
        margin-bottom: 12px;
        border-bottom: 1px solid var(--task-border-soft);
        padding-bottom: 8px;
    }
    .checklist-tab-btn {
        padding: 6px 12px;
        font-size: 12px;
        font-weight: 800;
        color: var(--task-muted);
        background: transparent;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        transition: all 0.2s;
    }
    .checklist-tab-btn:hover {
        color: var(--task-text);
        background: var(--task-bg);
    }
    .checklist-tab-btn.active {
        color: var(--task-orange-dark);
        background: var(--task-orange-soft);
        border: 1px solid #fed7aa;
    }
    .checklist-tab-content {
        display: none;
    }
    .checklist-tab-content.active {
        display: block;
    }

    .checklist-list {
        display: flex;
        flex-direction: column;
        gap: 7px;
    }

    .checklist-row {
        display: flex;
        align-items: center;
        gap: 8px;
        min-height: 22px;
        color: #475569;
        font-size: 12.5px;
        font-weight: 650;
    }

    .checklist-row .check-icon {
        width: 17px;
        flex: 0 0 17px;
        color: var(--task-green);
        font-size: 14px;
    }

    .checklist-row .check-icon.pending { color: #94a3b8; }
    .checklist-row .check-icon.warning { color: #f59e0b; }
    .checklist-row .check-icon.danger { color: var(--task-red); }
    .checklist-row .check-label { flex: 1 1 auto; min-width: 0; }

    /* =========================================================
       TABLES
    ========================================================= */
    .table-shell {
        overflow: hidden;
        border: 1px solid var(--task-border);
        border-radius: 9px;
        background: #fff;
    }

    .table-responsive-task { overflow-x: auto; }

    .task-table {
        width: 100%;
        margin: 0;
        border-collapse: separate;
        border-spacing: 0;
        color: #334155;
        font-size: 12px;
    }

    .task-table thead th {
        padding: 8px 9px;
        border-right: 1px solid var(--task-border);
        border-bottom: 1px solid var(--task-border);
        background: #f8fafc;
        color: #475569;
        font-size: 12px;
        font-weight: 850;
        white-space: nowrap;
    }

    .task-table tbody td {
        padding: 8px 9px;
        border-right: 1px solid var(--task-border-soft);
        border-bottom: 1px solid var(--task-border-soft);
        background: #fff;
        vertical-align: middle;
    }

    .task-table th:last-child,
    .task-table td:last-child { border-right: 0; }
    .task-table tbody tr:last-child td { border-bottom: 0; }

    .task-table .strong { font-weight: 800; color: #1e293b; }
    .task-table .money { font-weight: 800; white-space: nowrap; }
    .task-table .money-green { color: var(--task-green); }

    .table-summary {
        display: grid;
        grid-template-columns: 1fr 1fr 1fr;
        gap: 10px;
        padding: 9px 11px;
        border-top: 1px solid var(--task-border);
        background: #fcfcfd;
        color: #475569;
        font-size: 12px;
        font-weight: 750;
    }

    .table-summary > div:nth-child(2) { text-align: center; }
    .table-summary > div:nth-child(3) { text-align: right; }

    .table-summary strong {
        margin-left: 5px;
        color: #172033;
        font-weight: 900;
    }

    .table-summary .grand-total {
        color: var(--task-green);
        font-size: 14px;
    }

    .empty-table {
        padding: 17px 12px;
        text-align: center;
        color: #94a3b8;
        font-size: 12px;
        font-weight: 650;
    }

    /* =========================================================
       ROUTE
    ========================================================= */
    .route-grid {
        display: grid;
        grid-template-columns: 1.05fr 1fr;
        gap: 14px;
    }

    .sub-head {
        display: flex;
        align-items: center;
        gap: 7px;
        margin-bottom: 10px;
        color: #334155;
        font-size: 12px;
        font-weight: 850;
    }

    .sub-head i { color: #526681; }

    .route-list {
        position: relative;
        padding-left: 18px;
    }

    .route-list::before {
        content: '';
        position: absolute;
        top: 10px;
        bottom: 10px;
        left: 5px;
        width: 1px;
        background: #cbd5e1;
    }

    .route-point {
        position: relative;
        display: grid;
        grid-template-columns: 1fr auto;
        gap: 10px;
        padding: 0 0 14px 4px;
    }

    .route-point:last-child { padding-bottom: 0; }

    .route-point::before {
        content: '';
        position: absolute;
        top: 4px;
        left: -17px;
        width: 10px;
        height: 10px;
        border: 3px solid var(--task-orange);
        border-radius: 50%;
        background: #fff;
    }

    .route-title {
        color: #1e293b;
        font-size: 12.5px;
        font-weight: 850;
    }

    .route-address,
    .route-time-sub {
        margin-top: 2px;
        color: var(--task-muted);
        font-size: 12px;
        font-weight: 550;
    }

    .route-time {
        text-align: right;
        color: #526681;
        font-size: 12px;
        font-weight: 800;
        white-space: nowrap;
    }

    .trip-note {
        min-height: 88px;
        padding: 10px 11px;
        border: 1px solid var(--task-border);
        border-radius: 8px;
        background: #fbfcfe;
        color: #475569;
        font-size: 12px;
        line-height: 1.5;
        font-weight: 600;
    }

    .trip-note .emergency {
        margin-top: 9px;
        padding-top: 8px;
        border-top: 1px solid var(--task-border-soft);
    }

    /* =========================================================
       REPORTS
    ========================================================= */
    .report-list {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .report-row {
        border: 1px solid var(--task-border-soft);
        border-radius: 9px;
        background: #fff;
        overflow: hidden;
    }

    .report-main {
        display: grid;
        grid-template-columns: 34px 1fr auto auto;
        gap: 9px;
        align-items: center;
        min-height: 49px;
        padding: 7px 9px;
    }

    .report-icon {
        width: 30px;
        height: 30px;
        display: grid;
        place-items: center;
        border-radius: 8px;
        font-size: 14px;
    }

    .report-icon.departure { background: var(--task-blue-soft); color: var(--task-blue); }
    .report-icon.finance { background: #ecfdf5; color: #16a34a; }
    .report-icon.arrival { background: #fff7ed; color: var(--task-orange); }
    .report-icon.handover { background: var(--task-purple-soft); color: var(--task-purple); }

    .report-title {
        color: #172033;
        font-size: 12.5px;
        font-weight: 850;
    }

    .report-desc {
        margin-top: 2px;
        color: var(--task-muted);
        font-size: 12px;
        font-weight: 550;
    }

    .report-action {
        min-height: 29px;
        padding: 0 10px;
        border: 1px solid #fb923c;
        border-radius: 7px;
        background: #fff;
        color: var(--task-orange-dark);
        font-size: 12px;
        font-weight: 850;
        white-space: nowrap;
    }

    .report-action:hover { background: var(--task-orange-soft); }

    .expense-wrap {
        padding: 0 9px 9px;
    }

    .expense-title {
        margin: 0 0 6px 2px;
        color: #334155;
        font-size: 12px;
        font-weight: 850;
    }

    .expense-total {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding: 7px 8px;
        border-top: 1px solid var(--task-border);
        background: #fcfcfd;
        color: #334155;
        font-size: 12px;
        font-weight: 850;
    }

    .expense-total strong {
        color: var(--task-green);
        font-size: 14px;
        font-weight: 900;
    }

    .proof-link {
        display: inline-grid;
        place-items: center;
        width: 22px;
        height: 22px;
        border: 1px solid var(--task-border);
        border-radius: 6px;
        color: #526681;
        text-decoration: none;
    }

    /* =========================================================
       ATTACHMENTS
    ========================================================= */
    .attachment-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 8px;
    }

    .attachment-card {
        min-width: 0;
        overflow: hidden;
        border: 1px solid var(--task-border);
        border-radius: 8px;
        background: #fff;
    }

    .attachment-preview {
        height: 76px;
        display: grid;
        place-items: center;
        overflow: hidden;
        background: #f8fafc;
        color: #ef4444;
        text-decoration: none;
    }

    .attachment-preview img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .attachment-preview i { font-size: 26px; }

    .attachment-meta {
        padding: 6px 7px 7px;
    }

    .attachment-name {
        overflow: hidden;
        color: #1e293b;
        font-size: 12px;
        font-weight: 850;
        text-overflow: ellipsis;
        white-space: nowrap;
        text-transform: capitalize;
    }

    .attachment-date {
        margin-top: 2px;
        color: #94a3b8;
        font-size: 12px;
        font-weight: 600;
    }

    /* =========================================================
       ACTIVITY
    ========================================================= */
    .activity-list {
        position: relative;
        padding-left: 13px;
    }

    .activity-list::before {
        content: '';
        position: absolute;
        top: 7px;
        bottom: 7px;
        left: 3px;
        width: 1px;
        background: #e2e8f0;
    }

    .activity-item {
        position: relative;
        display: grid;
        grid-template-columns: minmax(180px,.85fr) 1fr;
        gap: 16px;
        padding: 0 0 11px 9px;
    }

    .activity-item:last-child { padding-bottom: 0; }

    .activity-item::before {
        content: '';
        position: absolute;
        top: 5px;
        left: -13px;
        width: 8px;
        height: 8px;
        border: 2px solid #fff;
        border-radius: 50%;
        background: var(--task-orange);
        box-shadow: 0 0 0 1px #fed7aa;
    }

    .activity-title {
        color: #1e293b;
        font-size: 12px;
        font-weight: 850;
    }

    .activity-meta,
    .activity-desc {
        margin-top: 2px;
        color: var(--task-muted);
        font-size: 12px;
        font-weight: 550;
    }

    /* =========================================================
       DARK MODE - KEEP USABLE
    ========================================================= */
    html[data-bs-theme="dark"] .task-show-page {
        --task-bg: #0b0f14;
        --task-card: #111827;
        --task-text: #f8fafc;
        --task-muted: #94a3b8;
        --task-border: #263142;
        --task-border-soft: #202a38;
        background: #0b0f14;
    }

    html[data-bs-theme="dark"] .task-card,
    html[data-bs-theme="dark"] .btn-back-task,
    html[data-bs-theme="dark"] .btn-more-task,
    html[data-bs-theme="dark"] .status-select,
    html[data-bs-theme="dark"] .report-row,
    html[data-bs-theme="dark"] .task-table tbody td,
    html[data-bs-theme="dark"] .attachment-card,
    html[data-bs-theme="dark"] .table-shell {
        background: #111827;
        color: #e2e8f0;
    }

    html[data-bs-theme="dark"] .task-table thead th,
    html[data-bs-theme="dark"] .table-summary,
    html[data-bs-theme="dark"] .expense-total,
    html[data-bs-theme="dark"] .trip-note,
    html[data-bs-theme="dark"] .attachment-preview {
        background: #0f172a;
    }

    html[data-bs-theme="dark"] .hero-number,
    html[data-bs-theme="dark"] .hero-value,
    html[data-bs-theme="dark"] .assignment-value,
    html[data-bs-theme="dark"] .report-title,
    html[data-bs-theme="dark"] .route-title,
    html[data-bs-theme="dark"] .section-heading,
    html[data-bs-theme="dark"] .activity-title {
        color: #f8fafc;
    }

    /* =========================================================
       RESPONSIVE
    ========================================================= */
    @media (max-width: 1250px) {
        .task-layout { grid-template-columns: 1fr; }
        .hero-summary { grid-template-columns: repeat(2, 1fr); }
        .hero-block:nth-child(2) { border-right: 0; }
        .hero-block:nth-child(n+3) { border-top: 1px solid var(--task-border-soft); }
        .assignment-grid { grid-template-columns: repeat(2, minmax(0,1fr)); }
        .assignment-cell { border-bottom: 1px solid var(--task-border-soft); }
        .assignment-cell:nth-child(5n) { border-right: 1px solid var(--task-border-soft); }
        .assignment-cell:nth-child(2n) { border-right: 0; }
        .assignment-cell:nth-last-child(-n+2) { border-bottom: 0; }
    }

    @media (max-width: 767px) {
        .task-show-page { padding: 12px; }
        .task-topbar { align-items: stretch; flex-direction: column; }
        .topbar-actions { align-items: stretch; flex-wrap: wrap; }
        .status-control { width: 100%; }
        .btn-edit-status { flex: 1 1 auto; }
        .hero-summary { grid-template-columns: 1fr; padding: 10px 14px; }
        .hero-block,
        .hero-block:nth-child(2) { border-right: 0; border-top: 1px solid var(--task-border-soft); padding: 12px 4px; }
        .hero-block:first-child { border-top: 0; }
        .progress-wrap { padding: 10px 8px 14px; overflow-x: auto; }
        .journey-progress { min-width: 600px; }
        .assignment-grid { grid-template-columns: 1fr; }
        .assignment-cell,
        .assignment-cell:nth-child(2n),
        .assignment-cell:nth-child(5n) { border-right: 0; border-bottom: 1px solid var(--task-border-soft); }
        .assignment-cell:last-child { border-bottom: 0; }
        .route-grid { grid-template-columns: 1fr; }
        .attachment-grid { grid-template-columns: repeat(2, minmax(0,1fr)); }
        .activity-item { grid-template-columns: 1fr; gap: 3px; }
        .report-main { grid-template-columns: 34px 1fr auto; }
        .report-main .report-action { grid-column: 2 / -1; justify-self: start; }
    }
</style>

@php
    $isPickup = $task->task_type === 'pickup';
    $itemsList = [];

    /* =========================================================
       DETAIL ITEM - PERTAHANKAN FALLBACK LAMA
    ========================================================= */
    if ($isPickup) {
        $relatedItems = $task->items ?? collect();
    } else {
        $relatedItems = isset($task->salesOrder)
            ? ($task->salesOrder->items ?? collect())
            : collect();
    }

    if ($relatedItems && $relatedItems->count() > 0) {
        foreach ($relatedItems as $item) {
            $itemsList[] = [
                'no_barang' => $item->item_number ?? '-',
                'deskripsi_barang' => $item->item_description ?? '-',
                'qty' => $item->quantity ?? 0,
                'uom' => $item->unit ?? '-',
                'harga_satuan' => $item->unit_price ?? 0,
            ];
        }
    } else {
        if ($isPickup) {
            if (!empty($task->item_description) && \Illuminate\Support\Str::startsWith($task->item_description, '{')) {
                $json = json_decode($task->item_description, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    foreach (($json['items'] ?? []) as $raw) {
                        $itemsList[] = [
                            'no_barang' => $raw['no_barang'] ?? $raw['item_number'] ?? '-',
                            'deskripsi_barang' => $raw['deskripsi_barang'] ?? $raw['item_description'] ?? '-',
                            'qty' => $raw['qty'] ?? $raw['quantity'] ?? 0,
                            'uom' => $raw['uom'] ?? $raw['unit'] ?? '-',
                            'harga_satuan' => $raw['harga_satuan'] ?? $raw['unit_price'] ?? 0,
                        ];
                    }
                }
            }

            if (count($itemsList) === 0 && !empty($task->item_description)) {
                $itemsList[] = [
                    'no_barang' => $task->item_number ?? '-',
                    'deskripsi_barang' => $task->item_description,
                    'qty' => $task->quantity ?? 0,
                    'uom' => $task->unit ?? '-',
                    'harga_satuan' => $task->unit_price ?? 0,
                ];
            }
        } else {
            if (isset($task->salesOrder->source_data)) {
                $sourceData = is_string($task->salesOrder->source_data)
                    ? json_decode($task->salesOrder->source_data, true)
                    : $task->salesOrder->source_data;

                foreach (($sourceData['items'] ?? []) as $raw) {
                    $itemsList[] = [
                        'no_barang' => $raw['no_barang'] ?? $raw['item_number'] ?? '-',
                        'deskripsi_barang' => $raw['deskripsi_barang'] ?? $raw['item_description'] ?? '-',
                        'qty' => $raw['qty'] ?? $raw['quantity'] ?? 0,
                        'uom' => $raw['uom'] ?? $raw['unit'] ?? '-',
                        'harga_satuan' => $raw['harga_satuan'] ?? $raw['unit_price'] ?? 0,
                    ];
                }

                if (count($itemsList) === 0 && isset($sourceData['deskripsi_barang'])) {
                    $itemsList[] = [
                        'no_barang' => $sourceData['no_barang'] ?? '-',
                        'deskripsi_barang' => $sourceData['deskripsi_barang'] ?? '-',
                        'qty' => $sourceData['qty'] ?? 0,
                        'uom' => $sourceData['uom'] ?? '-',
                        'harga_satuan' => $sourceData['harga_satuan'] ?? 0,
                    ];
                }
            }

            if (count($itemsList) === 0 && isset($task->salesOrder)) {
                $itemsList[] = [
                    'no_barang' => $task->salesOrder->item_number ?? '-',
                    'deskripsi_barang' => $task->salesOrder->item_description ?? '-',
                    'qty' => $task->salesOrder->ordered_quantity ?? 0,
                    'uom' => $task->salesOrder->unit ?? '-',
                    'harga_satuan' => 0,
                ];
            }
        }
    }

    $totalQty = collect($itemsList)->sum(fn($item) => (float)($item['qty'] ?? 0));
    $totalHarga = collect($itemsList)->sum(fn($item) =>
        (float)($item['qty'] ?? 0) * (float)($item['harga_satuan'] ?? 0)
    );

    /* =========================================================
       DELIVERY SOURCE
    ========================================================= */
    if (!$isPickup && isset($task->salesOrder->source_data)) {
        $sourceDataDelivery = is_string($task->salesOrder->source_data)
            ? json_decode($task->salesOrder->source_data, true)
            : $task->salesOrder->source_data;
    } else {
        $sourceDataDelivery = [];
    }

    $deliveryAddress = $sourceDataDelivery['address'] ?? '-';
    $deliveryDestinationName = $sourceDataDelivery['destination_name']
        ?? $sourceDataDelivery['warehouse_name']
        ?? $sourceDataDelivery['customer_name']
        ?? ($task->salesOrder->customer_name ?? '-');

    /* =========================================================
       STATUS
    ========================================================= */
    $statusOptions = [
        'assigned' => 'Assigned (Baru)',
        'on_route' => 'On Route (Sedang Jalan)',
        'arrived' => 'Arrived (Telah Sampai)',
        'delivered' => 'Delivered (Selesai Dikirim)',
        'failed' => 'Failed (Gagal)',
        'cancelled' => 'Cancelled (Dibatalkan)',
    ];

    $currentStatus = $task->status ?? 'assigned';

    /* =========================================================
       BASIC DISPLAY VALUE
    ========================================================= */
    $referenceNumber = $isPickup
        ? ($task->reference_number ?? '-')
        : ($task->salesOrder->so_number ?? '-');

    $supplierName = $isPickup
        ? ($task->pickup_name ?? '-')
        : ($task->delivery_pickup_name ?? $task->pickup_name ?? 'Gudang Utama');

    $pickupAddress = $isPickup
        ? ($task->pickup_location ?? '-')
        : ($task->delivery_pickup_location ?? $task->pickup_location ?? '-');

    $destinationName = $isPickup
        ? ($task->destination_name ?? $task->destination ?? $task->pickup_destination ?? '-')
        : $deliveryDestinationName;

    $destinationAddress = $isPickup
        ? ($task->pickup_destination ?? $task->destination ?? '-')
        : $deliveryAddress;

    $dispatchAtRaw = $task->dispatch_date ?? $task->assigned_at ?? $task->created_at;
    $arrivalAtRaw = $task->estimated_arrival ?? null;

    $dispatchAt = $dispatchAtRaw ? \Carbon\Carbon::parse($dispatchAtRaw) : null;
    $arrivalAt = $arrivalAtRaw ? \Carbon\Carbon::parse($arrivalAtRaw) : null;

    $driverPhone = $task->driver->phone_number
        ?? $task->driver->phone
        ?? $task->driver->mobile_phone
        ?? null;

    $pickupPic = $task->pickup_pic_name
        ?? $task->pic_name
        ?? $task->supplier_pic
        ?? '-';

    $pickupPicPhone = $task->pickup_pic_phone
        ?? $task->pic_phone
        ?? null;

    $priority = ucfirst($task->priority ?? 'Normal');
    $tripNote = $task->notes ?? $task->task_notes ?? $task->description ?? null;

    /* =========================================================
       CHECKLIST
    ========================================================= */
    // Keberangkatan
    $departureChecklistRaw = $task->departure_checklist ?? [];
    if (is_string($departureChecklistRaw)) {
        $departureChecklistRaw = json_decode($departureChecklistRaw, true) ?: [];
    }
    $defaultDepartureChecklist = [
        'Dokumen tugas' => null,
        'Surat jalan / ' . ($isPickup ? 'PO' : 'SO') => null,
        'Kendaraan siap' => null,
        'Bahan bakar cukup' => null,
        'Atribut & perlengkapan' => null,
        'Foto kendaraan (sebelum berangkat)' => null,
    ];
    $departureChecklist = count($departureChecklistRaw) > 0 ? $departureChecklistRaw : $defaultDepartureChecklist;

    // Kedatangan
    $arrivalChecklistRaw = $task->arrival_checklist ?? [];
    if (is_string($arrivalChecklistRaw)) {
        $arrivalChecklistRaw = json_decode($arrivalChecklistRaw, true) ?: [];
    }
    $defaultArrivalChecklist = [
        'Lapor satpam/penerima' => null,
        'Parkir di area yang ditentukan' => null,
        'Kondisi barang aman' => null,
        'Serahkan surat jalan' => null,
        'Foto di lokasi kedatangan' => null,
    ];
    $arrivalChecklist = count($arrivalChecklistRaw) > 0 ? $arrivalChecklistRaw : $defaultArrivalChecklist;

    // Serah Terima
    $handoverChecklistRaw = $task->handover_checklist ?? [];
    if (is_string($handoverChecklistRaw)) {
        $handoverChecklistRaw = json_decode($handoverChecklistRaw, true) ?: [];
    }
    $defaultHandoverChecklist = [
        'Tanda tangan penerima' => null,
        'Stempel perusahaan/toko' => null,
        'Foto bukti serah terima' => null,
        'Dokumen kembali lengkap' => null,
    ];
    $handoverChecklist = count($handoverChecklistRaw) > 0 ? $handoverChecklistRaw : $defaultHandoverChecklist;

    /* =========================================================
       ATTACHMENTS
    ========================================================= */
    $attachments = collect($task->attachments ?? []);

    $attachmentMatches = function(array $needles) use ($attachments) {
        return $attachments->filter(function($att) use ($needles) {
            $category = strtolower((string)($att->category ?? ''));
            foreach ($needles as $needle) {
                if (str_contains($category, strtolower($needle))) return true;
            }
            return false;
        });
    };

    $departureAttachments = $attachmentMatches(['keberangkatan','departure','berangkat']);
    $arrivalAttachments = $attachmentMatches(['kedatangan','arrival','sampai']);
    $handoverAttachments = $attachmentMatches(['serah','handover','penerimaan']);

    /* =========================================================
       EXPENSES / LAPORAN PENGELUARAN
       Mencoba beberapa nama relation/attribute agar aman.
    ========================================================= */
    $expenses = collect();
    foreach (['expenses', 'tripExpenses', 'financialExpenses', 'driverExpenses'] as $expenseKey) {
        try {
            $candidate = method_exists($task, $expenseKey)
                ? $task->{$expenseKey}
                : $task->getAttribute($expenseKey);
            if ($candidate) {
                $expenses = collect($candidate);
                if ($expenses->count() > 0) break;
            }
        } catch (\Throwable $e) {
            // abaikan fallback yang tidak tersedia
        }
    }

    $expenseValue = function($expense, array $keys, $default = null) {
        foreach ($keys as $key) {
            $value = data_get($expense, $key);
            if ($value !== null && $value !== '') return $value;
        }
        return $default;
    };

    $totalExpense = $expenses->sum(function($expense) use ($expenseValue) {
        return (float)$expenseValue($expense, ['amount','nominal','total','total_amount','value'], 0);
    });

    /* =========================================================
       REPORT STATUS (UI ONLY - TIDAK MEMBUAT ROUTE BARU)
    ========================================================= */
    $departureDone = in_array($currentStatus, ['on_route','arrived','delivered'], true)
        || $departureAttachments->count() > 0;
    $financeDone = $expenses->count() > 0;
    $arrivalDone = in_array($currentStatus, ['arrived','delivered'], true)
        || $arrivalAttachments->count() > 0;
    $handoverDone = $currentStatus === 'delivered'
        || $handoverAttachments->count() > 0;

    /* =========================================================
       ACTIVITY - HANYA DATA YANG MEMANG ADA
    ========================================================= */
    $activities = collect();

    if ($task->created_at) {
        $activities->push([
            'title' => 'Tugas dibuat',
            'meta' => 'Oleh ' . (auth()->user()->name ?? 'Admin') . ' • ' . \Carbon\Carbon::parse($task->created_at)->format('d M Y, H:i'),
            'desc' => 'Penugasan baru dibuat dan dikirim ke driver.',
        ]);
    }

    if (!empty($task->assigned_at)) {
        $activities->push([
            'title' => 'Tugas ditugaskan',
            'meta' => \Carbon\Carbon::parse($task->assigned_at)->format('d M Y, H:i'),
            'desc' => 'Driver dan kendaraan telah ditetapkan pada tugas ini.',
        ]);
    }

    if (!empty($task->updated_at) && $task->created_at && \Carbon\Carbon::parse($task->updated_at)->ne(\Carbon\Carbon::parse($task->created_at))) {
        $activities->push([
            'title' => 'Data tugas diperbarui',
            'meta' => \Carbon\Carbon::parse($task->updated_at)->format('d M Y, H:i'),
            'desc' => 'Informasi atau status tugas telah diperbarui.',
        ]);
    }
@endphp

<div class="task-show-page">
    <div class="task-show-container">

        {{-- =====================================================
             TOP BAR
        ====================================================== --}}
        <div class="task-topbar">
            <a href="{{ route('pickup-tasks.index') }}" class="btn-back-task">
                <i class="fa-solid fa-arrow-left"></i>
                <span>Kembali ke Daftar Tugas</span>
            </a>

            <div class="topbar-actions">
                <div class="status-control">
                    <label for="taskStatusSelect">Status Tugas</label>
                    <select
                        id="taskStatusSelect"
                        class="form-select status-select"
                        data-task-id="{{ $task->id }}"
                        aria-label="Status tugas"
                    >
                        @if(!array_key_exists($currentStatus, $statusOptions))
                            <option value="{{ $currentStatus }}" selected>
                                {{ ucwords(str_replace('_', ' ', $currentStatus)) }}
                            </option>
                        @endif

                        @foreach($statusOptions as $value => $label)
                            <option value="{{ $value }}" {{ $currentStatus === $value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <button type="button" class="btn-edit-status" id="btnEditTaskStatus">
                    <i class="fa-solid fa-pen"></i>
                    Edit Status
                </button>

                <button type="button" class="btn-more-task" title="Aksi lainnya">
                    <i class="fa-solid fa-ellipsis"></i>
                </button>
            </div>
        </div>

        {{-- =====================================================
             HERO + PROGRESS
        ====================================================== --}}
        <section class="task-card task-hero">
            <div class="hero-summary">
                <div class="hero-block">
                    <div class="hero-main-icon">
                        <i class="fa-solid {{ $isPickup ? 'fa-box-open' : 'fa-truck-fast' }}"></i>
                    </div>
                    <div style="min-width:0;">
                        <div class="hero-kicker">
                            <i class="fa-solid fa-circle" style="font-size:12px;"></i>
                            {{ $isPickup ? 'Pickup Task' : 'Delivery Task' }}
                        </div>
                        <h1 class="hero-number">{{ $referenceNumber }}</h1>
                        <div class="hero-subtitle">{{ $supplierName }}</div>
                    </div>
                </div>

                <div class="hero-block">
                    <div style="min-width:0;">
                        <div class="hero-label">{{ $isPickup ? 'Supplier' : 'Lokasi Asal' }}</div>
                        <div class="hero-value">
                            <i class="fa-solid fa-building"></i>
                            <div>
                                {{ $supplierName }}
                                <span class="hero-secondary">{{ $pickupAddress }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="hero-block">
                    <div style="min-width:0;">
                        <div class="hero-label">Tujuan</div>
                        <div class="hero-value">
                            <i class="fa-solid fa-location-dot"></i>
                            <div>
                                {{ $destinationName }}
                                <span class="hero-secondary">{{ $destinationAddress }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="hero-block">
                    <div style="min-width:0;">
                        <div class="hero-label">Jadwal Tugas</div>
                        <div class="hero-value">
                            <i class="fa-regular fa-calendar-days"></i>
                            <div>
                                {{ $dispatchAt ? $dispatchAt->format('d M Y, H:i') : '-' }}
                                <span class="hero-secondary">
                                    Estimasi tiba {{ $arrivalAt ? $arrivalAt->format('H:i') : '-' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="progress-wrap">
                <div class="journey-progress">
                    <div class="progress-step complete">
                        <div class="progress-icon"><i class="fa-solid fa-truck"></i></div>
                        <div class="progress-title">Tugas Dibuat</div>
                        <div class="progress-subtitle">{{ $task->created_at ? \Carbon\Carbon::parse($task->created_at)->format('d M Y, H:i') : '-' }}</div>
                    </div>

                    <div class="progress-step {{ $departureDone ? 'complete' : '' }}">
                        <div class="progress-icon"><i class="fa-solid fa-play"></i></div>
                        <div class="progress-title">Keberangkatan</div>
                        <div class="progress-subtitle">{{ $departureDone ? 'Sudah tercatat' : 'Belum tercatat' }}</div>
                    </div>

                    <div class="progress-step {{ $financeDone ? 'complete' : '' }}">
                        <div class="progress-icon"><i class="fa-solid fa-wallet"></i></div>
                        <div class="progress-title">Keuangan</div>
                        <div class="progress-subtitle">{{ $financeDone ? 'Sudah ada laporan' : 'Belum laporan' }}</div>
                    </div>

                    <div class="progress-step {{ $arrivalDone ? 'complete' : '' }}">
                        <div class="progress-icon"><i class="fa-solid fa-location-dot"></i></div>
                        <div class="progress-title">Sampai</div>
                        <div class="progress-subtitle">{{ $arrivalDone ? 'Sudah tercatat' : 'Belum tercatat' }}</div>
                    </div>

                    <div class="progress-step {{ $handoverDone ? 'complete' : '' }}">
                        <div class="progress-icon"><i class="fa-solid fa-file-lines"></i></div>
                        <div class="progress-title">Serah Terima</div>
                        <div class="progress-subtitle">{{ $handoverDone ? 'Sudah laporan' : 'Belum laporan' }}</div>
                    </div>
                </div>
            </div>
        </section>

        <div class="task-layout">
            {{-- =================================================
                 LEFT COLUMN
            ================================================== --}}
            <div class="task-column">

                {{-- INFORMASI PENUGASAN --}}
                <section class="task-card section-card">
                    <div class="section-heading-row">
                        <div class="section-heading">
                            <i class="fa-solid fa-box"></i>
                            <span>Informasi Penugasan & Lokasi</span>
                        </div>
                        <span class="task-type-pill">
                            {{ $isPickup ? 'Mengambil Barang (Pickup)' : 'Mengirim Barang (Delivery)' }}
                        </span>
                    </div>

                    <div class="assignment-grid">
                        <div class="assignment-cell">
                            <div class="assignment-label">Driver</div>
                            <div class="assignment-value">
                                <i class="fa-solid fa-user"></i>
                                <div>
                                    {{ $task->driver->full_name ?? 'N/A' }}
                                    @if($driverPhone)
                                        <span class="assignment-sub">{{ $driverPhone }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="assignment-cell">
                            <div class="assignment-label">Kendaraan</div>
                            <div class="assignment-value">
                                <i class="fa-solid fa-car-side"></i>
                                <div>
                                    {{ $task->vehicle->plate_number ?? 'N/A' }}
                                    @if(!empty($task->vehicle->name))
                                        <span class="assignment-sub">{{ $task->vehicle->name }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="assignment-cell">
                            <div class="assignment-label">Tanggal Penugasan</div>
                            <div class="assignment-value">
                                <i class="fa-regular fa-calendar-days"></i>
                                <div>
                                    {{ $dispatchAt ? $dispatchAt->format('d M Y, H:i') : '-' }}
                                    <span class="assignment-sub">Estimasi tiba {{ $arrivalAt ? $arrivalAt->format('H:i') : '-' }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="assignment-cell">
                            <div class="assignment-label">Prioritas</div>
                            <div class="assignment-value">
                                <i class="fa-regular fa-flag"></i>
                                <span class="priority-badge">{{ $priority }}</span>
                            </div>
                        </div>

                        <div class="assignment-cell">
                            <div class="assignment-label">Nomor Referensi</div>
                            <div class="assignment-value">
                                <i class="fa-solid fa-hashtag"></i>
                                <div>{{ $referenceNumber }}</div>
                            </div>
                        </div>

                        <div class="assignment-cell">
                            <div class="assignment-label">{{ $isPickup ? 'Supplier / Vendor' : 'Lokasi Asal' }}</div>
                            <div class="assignment-value">
                                <i class="fa-solid fa-building"></i>
                                <div>
                                    {{ $supplierName }}
                                    <span class="assignment-sub">{{ $pickupAddress }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="assignment-cell">
                            <div class="assignment-label">Nama PIC</div>
                            <div class="assignment-value">
                                <i class="fa-solid fa-user"></i>
                                <div>
                                    {{ $pickupPic }}
                                    @if($pickupPicPhone)
                                        <span class="assignment-sub">{{ $pickupPicPhone }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="assignment-cell">
                            <div class="assignment-label">Lokasi Pickup</div>
                            <div class="assignment-value">
                                <i class="fa-solid fa-location-dot"></i>
                                <div>
                                    {{ $supplierName }}
                                    <span class="assignment-sub">{{ $pickupAddress }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="assignment-cell">
                            <div class="assignment-label">Lokasi Tujuan</div>
                            <div class="assignment-value">
                                <i class="fa-solid fa-location-dot"></i>
                                <div>
                                    {{ $destinationName }}
                                    <span class="assignment-sub">{{ $destinationAddress }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="assignment-cell">
                            <div class="assignment-label">Status Pengiriman</div>
                            <div class="assignment-value">
                                <i class="fa-solid fa-truck"></i>
                                <div>{{ $statusOptions[$currentStatus] ?? ucwords(str_replace('_',' ', $currentStatus)) }}</div>
                            </div>
                        </div>
                    </div>
                </section>

                {{-- DETAIL BARANG --}}
                <section class="task-card section-card">
                    <div class="section-heading">
                        <i class="fa-solid fa-box"></i>
                        <span>Detail Barang</span>
                    </div>

                    <div class="table-shell">
                        <div class="table-responsive-task">
                            <table class="task-table" style="min-width:720px;">
                                <thead>
                                    <tr>
                                        <th class="text-center" style="width:46px;">No</th>
                                        <th style="width:105px;">No Barang</th>
                                        <th>Nama/Deskripsi Barang</th>
                                        <th class="text-center" style="width:70px;">Qty</th>
                                        <th style="width:85px;">Satuan</th>
                                        <th class="text-end" style="width:120px;">Harga Satuan</th>
                                        <th class="text-end" style="width:120px;">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                @forelse($itemsList as $index => $item)
                                    @php
                                        $qty = (float)($item['qty'] ?? 0);
                                        $price = (float)($item['harga_satuan'] ?? 0);
                                        $lineTotal = $qty * $price;
                                    @endphp
                                    <tr>
                                        <td class="text-center strong">{{ $index + 1 }}</td>
                                        <td class="strong">{{ $item['no_barang'] ?? '-' }}</td>
                                        <td class="strong">{{ $item['deskripsi_barang'] ?? '-' }}</td>
                                        <td class="text-center strong">{{ number_format($qty, 2, ',', '.') }}</td>
                                        <td>{{ $item['uom'] ?? '-' }}</td>
                                        <td class="text-end money">{{ $price > 0 ? 'Rp '.number_format($price,0,',','.') : '-' }}</td>
                                        <td class="text-end money">{{ $lineTotal > 0 ? 'Rp '.number_format($lineTotal,0,',','.') : '-' }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="7" class="empty-table">Belum ada detail barang pada tugas ini.</td></tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="table-summary">
                            <div>Total Item: <strong>{{ count($itemsList) }}</strong></div>
                            <div>Total Qty: <strong>{{ number_format($totalQty, 2, ',', '.') }}</strong></div>
                            <div>Total Harga: <strong class="grand-total">Rp {{ number_format($totalHarga,0,',','.') }}</strong></div>
                        </div>
                    </div>
                </section>

                {{-- RESUME PENGELUARAN PERJALANAN --}}
                @php
                    $resumePengeluaran = [
                        'Tol' => 0,
                        'Bensin' => 0,
                        'Parkir' => 0,
                        'Lainnya' => 0,
                    ];
                    $totalResume = 0;
                    
                    if (isset($expenses)) {
                        foreach($expenses as $exp) {
                            $eType = strtolower((string)$expenseValue($exp, ['expense_type','type','category','name'], '-'));
                            $eAmount = (float)$expenseValue($exp, ['amount','nominal','total','total_amount','value'], 0);
                            $totalResume += $eAmount;
                            
                            if (str_contains($eType, 'tol')) {
                                $resumePengeluaran['Tol'] += $eAmount;
                            } elseif (str_contains($eType, 'bensin') || str_contains($eType, 'bbm') || str_contains($eType, 'fuel') || str_contains($eType, 'solar')) {
                                $resumePengeluaran['Bensin'] += $eAmount;
                            } elseif (str_contains($eType, 'parkir')) {
                                $resumePengeluaran['Parkir'] += $eAmount;
                            } else {
                                $resumePengeluaran['Lainnya'] += $eAmount;
                            }
                        }
                    }
                @endphp
                <section class="task-card section-card" style="margin-bottom: 12px;">
                    <div class="section-heading">
                        <i class="fa-solid fa-wallet"></i>
                        <span>Resume Pengeluaran Perjalanan</span>
                    </div>
                    <div class="assignment-grid" style="grid-template-columns: repeat(4, 1fr);">
                        <div class="assignment-cell" style="min-height: 55px;">
                            <div class="assignment-label">TOL</div>
                            <div class="assignment-value" style="font-size: 14px;">Rp {{ number_format($resumePengeluaran['Tol'], 0, ',', '.') }}</div>
                        </div>
                        <div class="assignment-cell" style="min-height: 55px;">
                            <div class="assignment-label">BENSIN</div>
                            <div class="assignment-value" style="font-size: 14px;">Rp {{ number_format($resumePengeluaran['Bensin'], 0, ',', '.') }}</div>
                        </div>
                        <div class="assignment-cell" style="min-height: 55px;">
                            <div class="assignment-label">PARKIR</div>
                            <div class="assignment-value" style="font-size: 14px;">Rp {{ number_format($resumePengeluaran['Parkir'], 0, ',', '.') }}</div>
                        </div>
                        <div class="assignment-cell" style="min-height: 55px;">
                            <div class="assignment-label">LAINNYA</div>
                            <div class="assignment-value" style="font-size: 14px;">Rp {{ number_format($resumePengeluaran['Lainnya'], 0, ',', '.') }}</div>
                        </div>
                    </div>
                    <div style="padding: 10px 12px; background: #fcfcfd; border-top: 1px solid var(--task-border-soft); display: flex; justify-content: space-between; align-items: center; border-radius: 0 0 13px 13px;">
                        <span style="font-size: 12px; font-weight: 850; color: #475569; text-transform: uppercase;">Total Keseluruhan</span>
                        <div style="display: flex; gap: 15px; align-items: center;">
                            <strong style="font-size: 14px; color: var(--task-green);">Rp {{ number_format($totalResume, 0, ',', '.') }}</strong>
                            <button type="button" onclick="openReportModal('finance')" style="padding: 6px 12px; font-size: 13px; font-weight: 700; border-radius: 6px; cursor: pointer; border:none; background: var(--task-blue); color: #fff; display: flex; align-items: center; gap: 5px;">
                                <i class="fa-solid fa-expand"></i> Lihat Detail Laporan
                            </button>
                        </div>
                    </div>
                </section>

                {{-- JADWAL & RUTE --}}
                <section class="task-card section-card">
                    <div class="section-heading">
                        <i class="fa-solid fa-route"></i>
                        <span>Jadwal & Rute Perjalanan</span>
                    </div>

                    <div class="route-grid">
                        <div>
                            <div class="sub-head"><i class="fa-solid fa-route"></i> Rute Perjalanan</div>
                            <div class="route-list">
                                <div class="route-point">
                                    <div>
                                        <div class="route-title">Titik Awal - {{ $supplierName }}</div>
                                        <div class="route-address">{{ $pickupAddress }}</div>
                                    </div>
                                    <div class="route-time">{{ $dispatchAt ? $dispatchAt->format('H:i') : '-' }}</div>
                                </div>

                                <div class="route-point">
                                    <div>
                                        <div class="route-title">Titik Tujuan - {{ $destinationName }}</div>
                                        <div class="route-address">{{ $destinationAddress }}</div>
                                    </div>
                                    <div class="route-time">
                                        {{ $arrivalAt ? $arrivalAt->format('H:i') : '-' }}
                                        <div class="route-time-sub">Estimasi tiba</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div>
                            <div class="sub-head"><i class="fa-regular fa-file-lines"></i> Catatan Perjalanan</div>
                            <div class="trip-note">
                                {{ $tripNote ?: 'Belum ada catatan perjalanan untuk tugas ini.' }}
                                @if($driverPhone)
                                    <div class="emergency">Kontak Driver: {{ $driverPhone }}</div>
                                @endif
                            </div>
                        </div>
                    </div>
                </section>

                {{-- RIWAYAT AKTIVITAS --}}
                <section class="task-card section-card">
                    <div class="section-heading">
                        <i class="fa-solid fa-clock"></i>
                        <span>Riwayat Aktivitas</span>
                    </div>

                    <div class="activity-list">
                        @forelse($activities as $activity)
                            <div class="activity-item">
                                <div>
                                    <div class="activity-title">{{ $activity['title'] }}</div>
                                    <div class="activity-meta">{{ $activity['meta'] }}</div>
                                </div>
                                <div class="activity-desc">{{ $activity['desc'] }}</div>
                            </div>
                        @empty
                            <div class="empty-table">Belum ada riwayat aktivitas.</div>
                        @endforelse
                    </div>
                </section>
            </div>

            {{-- =================================================
                 RIGHT COLUMN
            ================================================== --}}
            <div class="task-column">

                {{-- CHECKLIST --}}
                <section class="task-card section-card">
                    <div class="section-heading">
                        <i class="fa-solid fa-square-check"></i>
                        <span>Checklist Tugas</span>
                    </div>

                    <div class="checklist-tabs">
                        <button class="checklist-tab-btn active" onclick="switchChecklistTab('keberangkatan')">Keberangkatan</button>
                        <button class="checklist-tab-btn" onclick="switchChecklistTab('kedatangan')">Kedatangan</button>
                        <button class="checklist-tab-btn" onclick="switchChecklistTab('serah_terima')">Serah Terima</button>
                    </div>

                    @php
                        $allChecklists = [
                            'keberangkatan' => $departureChecklist,
                            'kedatangan' => $arrivalChecklist,
                            'serah_terima' => $handoverChecklist,
                        ];
                    @endphp

                    @foreach($allChecklists as $tabId => $chkList)
                        <div class="checklist-tab-content {{ $tabId === 'keberangkatan' ? 'active' : '' }}" id="checklist-tab-{{ $tabId }}">
                            <div class="checklist-list">
                                @foreach($chkList as $label => $state)
                                    @php
                                        $isOk = in_array($state, ['check', true, 1, '1', 'ok'], true);
                                        $isWarning = $state === 'warning';
                                        $isBad = in_array($state, ['cross','bad','not_ok'], true);
                                    @endphp
                                    <div class="checklist-row">
                                        @if($isOk)
                                            <i class="fa-solid fa-circle-check check-icon"></i>
                                        @elseif($isWarning)
                                            <i class="fa-solid fa-triangle-exclamation check-icon warning"></i>
                                        @elseif($isBad)
                                            <i class="fa-solid fa-circle-xmark check-icon danger"></i>
                                        @else
                                            <i class="fa-regular fa-circle check-icon pending"></i>
                                        @endif

                                        <div class="check-label">{{ ucwords(str_replace('_',' ', $label)) }}</div>

                                        @if($isOk)
                                            <span class="mini-state success">OK</span>
                                        @elseif($isWarning)
                                            <span class="mini-state pending" style="color:#b45309;border-color:#fde68a;background:#fffbeb;">Perhatian</span>
                                        @elseif($isBad)
                                            <span class="mini-state pending" style="color:#dc2626;border-color:#fecaca;background:#fef2f2;">Tidak OK</span>
                                        @else
                                            <span class="mini-state pending">Belum</span>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                    
                    <script>
                        function switchChecklistTab(tabId) {
                            // Reset buttons
                            document.querySelectorAll('.checklist-tab-btn').forEach(btn => btn.classList.remove('active'));
                            // Reset contents
                            document.querySelectorAll('.checklist-tab-content').forEach(content => content.classList.remove('active'));
                            
                            // Activate selected
                            event.currentTarget.classList.add('active');
                            document.getElementById('checklist-tab-' + tabId).classList.add('active');
                        }
                    </script>
                </section>

                {{-- LAPORAN TUGAS --}}
                <section class="task-card section-card">
                    <div class="section-heading">
                        <i class="fa-solid fa-file-invoice"></i>
                        <span>Laporan Tugas</span>
                    </div>

                    <div class="report-list">
                        {{-- KEBERANGKATAN --}}
                        <div class="report-row">
                            <div class="report-main">
                                <div class="report-icon departure"><i class="fa-solid fa-play"></i></div>
                                <div>
                                    <div class="report-title">Laporan Keberangkatan</div>
                                    <div class="report-desc">Catatan saat driver berangkat</div>
                                </div>
                                <span class="mini-state {{ $departureDone ? 'success' : 'pending' }}">
                                    {{ $departureDone ? 'Selesai' : 'Belum' }}
                                </span>
                                <button type="button" class="report-action" data-report="departure">
                                    {{ $departureDone ? 'Lihat Laporan' : 'Isi Laporan' }}
                                </button>
                            </div>
                        </div>

                        {{-- KEUANGAN + TABLE PENGELUARAN --}}
                        <div class="report-row">
                            <div class="report-main">
                                <div class="report-icon finance"><i class="fa-solid fa-wallet"></i></div>
                                <div>
                                    <div class="report-title">Laporan Keuangan</div>
                                    <div class="report-desc">Biaya & pengeluaran perjalanan</div>
                                </div>
                                <span class="mini-state {{ $financeDone ? 'success' : 'pending' }}">
                                    {{ $financeDone ? 'Selesai' : 'Belum' }}
                                </span>
                                <button type="button" class="report-action" data-report="finance">
                                    {{ $financeDone ? 'Lihat Laporan' : 'Isi Laporan' }}
                                </button>
                            </div>


                        </div>

                        {{-- SAMPAI --}}
                        <div class="report-row">
                            <div class="report-main">
                                <div class="report-icon arrival"><i class="fa-solid fa-location-dot"></i></div>
                                <div>
                                    <div class="report-title">Laporan Sampai</div>
                                    <div class="report-desc">Catatan saat tiba di lokasi</div>
                                </div>
                                <span class="mini-state {{ $arrivalDone ? 'success' : 'pending' }}">
                                    {{ $arrivalDone ? 'Selesai' : 'Belum' }}
                                </span>
                                <button type="button" class="report-action" data-report="arrival">
                                    {{ $arrivalDone ? 'Lihat Laporan' : 'Isi Laporan' }}
                                </button>
                            </div>
                        </div>

                        {{-- SERAH TERIMA --}}
                        <div class="report-row">
                            <div class="report-main">
                                <div class="report-icon handover"><i class="fa-solid fa-file-lines"></i></div>
                                <div>
                                    <div class="report-title">Laporan Serah Terima</div>
                                    <div class="report-desc">Konfirmasi penyerahan barang</div>
                                </div>
                                <span class="mini-state {{ $handoverDone ? 'success' : 'pending' }}">
                                    {{ $handoverDone ? 'Selesai' : 'Belum' }}
                                </span>
                                <button type="button" class="report-action" data-report="handover">
                                    {{ $handoverDone ? 'Lihat Laporan' : 'Isi Laporan' }}
                                </button>
                            </div>
                        </div>
                    </div>
                </section>

                {{-- BUKTI LAMPIRAN --}}
                <section class="task-card section-card">
                    <div class="section-heading">
                        <i class="fa-solid fa-paperclip"></i>
                        <span>Bukti Lampiran</span>
                    </div>

                    @if($attachments->count() > 0)
                        <div class="attachment-grid">
                            @foreach($attachments->take(8) as $att)
                                @php
                                    $url = asset('storage/' . $att->file_path);
                                    $isPdf = \Illuminate\Support\Str::endsWith(strtolower((string)$att->file_path), '.pdf');
                                    $category = ucwords(str_replace('_',' ', $att->category ?? 'Lampiran'));
                                @endphp
                                <div class="attachment-card">
                                    <a href="{{ $url }}" target="_blank" class="attachment-preview">
                                        @if($isPdf)
                                            <i class="fa-solid fa-file-pdf"></i>
                                        @else
                                            <img src="{{ $url }}" alt="{{ $category }}">
                                        @endif
                                    </a>
                                    <div class="attachment-meta">
                                        <div class="attachment-name" title="{{ $category }}">{{ $category }}</div>
                                        <div class="attachment-date">
                                            {{ !empty($att->created_at) ? \Carbon\Carbon::parse($att->created_at)->format('d M Y') : '' }}
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="empty-table">Belum ada bukti lampiran pada tugas ini.</div>
                    @endif
                </section>
            </div>
        </div>
    </div>
</div>

<!-- REPORT MODAL -->
<style>
.report-modal {
    display: none;
    position: fixed;
    z-index: 9999;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(15, 23, 42, 0.5);
    backdrop-filter: blur(4px);
    align-items: center;
    justify-content: center;
}
.report-modal.show {
    display: flex;
}
.report-modal-content {
    background: #fff;
    border-radius: 12px;
    width: 90%;
    max-width: 500px;
    box-shadow: 0 10px 25px rgba(0,0,0,0.1);
    animation: modalFadeIn 0.3s ease;
    transition: max-width 0.3s ease;
}
.report-modal-content.modal-lg {
    max-width: 800px;
}
@keyframes modalFadeIn {
    from { opacity: 0; transform: translateY(-20px); }
    to { opacity: 1; transform: translateY(0); }
}
.report-modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 16px 20px;
    border-bottom: 1px solid var(--task-border-soft);
}
.report-modal-header h3 {
    margin: 0;
    font-size: 14px;
    font-weight: 800;
    color: var(--task-text);
}
.report-modal-close {
    background: transparent;
    border: none;
    font-size: 14px;
    cursor: pointer;
    color: var(--task-muted);
}
.report-modal-body {
    padding: 20px;
    font-size: 14px;
    color: var(--task-text);
    min-height: 100px;
}
.report-modal-footer {
    padding: 12px 20px;
    border-top: 1px solid var(--task-border-soft);
    text-align: right;
}
</style>

<div id="reportModal" class="report-modal">
    <div class="report-modal-content">
        <div class="report-modal-header">
            <h3 id="reportModalTitle">Laporan</h3>
            <button class="report-modal-close" onclick="closeReportModal()"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div class="report-modal-body" id="reportModalBody">
            <!-- Content will be injected here -->
        </div>
        <div class="report-modal-footer">
            <button class="btn-back-task" onclick="closeReportModal()">Tutup</button>
        </div>
    </div>
</div>

<!-- Data Laporan Templates (Hidden) -->
<div id="tpl-departure" style="display:none;">
    <p><strong>Waktu Berangkat:</strong> {{ $task->started_at ? \Carbon\Carbon::parse($task->started_at)->format('d M Y, H:i') : 'Belum tercatat' }}</p>
    <p><strong>Catatan:</strong> {{ $task->departure_notes ?? 'Tidak ada catatan.' }}</p>
    @if(isset($departureAttachments) && $departureAttachments->count() > 0)
        <p style="margin-top: 12px; margin-bottom: 6px; font-weight: 800;">Lampiran ({{ $departureAttachments->count() }} file):</p>
        <div class="attachment-grid" style="grid-template-columns: repeat(3, minmax(0, 1fr));">
            @foreach($departureAttachments as $att)
                @php
                    $url = asset('storage/' . $att->file_path);
                    $isPdf = \Illuminate\Support\Str::endsWith(strtolower((string)$att->file_path), '.pdf');
                    $category = ucwords(str_replace('_',' ', $att->category ?? 'Lampiran'));
                @endphp
                <div class="attachment-card">
                    <a href="{{ $url }}" target="_blank" class="attachment-preview">
                        @if($isPdf)
                            <i class="fa-solid fa-file-pdf"></i>
                        @else
                            <img src="{{ $url }}" alt="{{ $category }}">
                        @endif
                    </a>
                </div>
            @endforeach
        </div>
    @endif
</div>
<div id="tpl-finance" style="display:none;">
    <p><strong>Total Pengeluaran:</strong> Rp {{ isset($totalExpense) ? number_format($totalExpense, 0, ',', '.') : 0 }}</p>
    <p><strong>Jumlah Transaksi:</strong> {{ isset($expenses) ? $expenses->count() : 0 }}</p>
    
    <div class="expense-wrap" style="margin-top: 15px;">
        <div class="expense-title">Rincian Laporan Pengeluaran</div>
        <div class="table-shell">
            <div class="table-responsive-task">
                <table class="task-table" style="min-width:535px;">
                    <thead>
                        <tr>
                            <th style="width:84px;">Tanggal</th>
                            <th style="width:92px;">Jenis Biaya</th>
                            <th>Keterangan</th>
                            <th class="text-center" style="width:58px;">Qty</th>
                            <th class="text-end" style="width:100px;">Nominal</th>
                            <th class="text-center" style="width:50px;">Bukti</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(isset($expenses))
                            @forelse($expenses as $expense)
                                @php
                                    $expenseDateRaw = $expenseValue($expense, ['expense_date','date','transaction_date','created_at']);
                                    $expenseDate = $expenseDateRaw ? \Carbon\Carbon::parse($expenseDateRaw)->format('d/m/Y') : '-';
                                    $expenseType = $expenseValue($expense, ['expense_type','type','category','name'], '-');
                                    $expenseDesc = $expenseValue($expense, ['description','notes','note','remark'], '-');
                                    $expenseQty = $expenseValue($expense, ['quantity','qty'], 1);
                                    $expenseAmount = (float)$expenseValue($expense, ['amount','nominal','total','total_amount','value'], 0);
                                    $proofPath = $expenseValue($expense, ['receipt_path','proof_path','attachment_path','file_path']);
                                @endphp
                                <tr>
                                    <td>{{ $expenseDate }}</td>
                                    <td class="strong">{{ $expenseType }}</td>
                                    <td>{{ $expenseDesc }}</td>
                                    <td class="text-center">{{ $expenseQty }}</td>
                                    <td class="text-end money">Rp {{ number_format($expenseAmount,0,',','.') }}</td>
                                    <td class="text-center">
                                        @if($proofPath)
                                            <a href="{{ asset('storage/'.$proofPath) }}" target="_blank" class="proof-link" title="Lihat bukti">
                                                <i class="fa-regular fa-file-lines"></i>
                                            </a>
                                        @else
                                            <span style="color:#cbd5e1;">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="empty-table">Belum ada laporan pengeluaran.</td>
                                </tr>
                            @endforelse
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<div id="tpl-arrival" style="display:none;">
    <p><strong>Waktu Sampai:</strong> {{ $task->arrived_at ? \Carbon\Carbon::parse($task->arrived_at)->format('d M Y, H:i') : 'Belum tercatat' }}</p>
    <p><strong>Catatan:</strong> {{ $task->arrival_notes ?? 'Tidak ada catatan.' }}</p>
    @if(isset($arrivalAttachments) && $arrivalAttachments->count() > 0)
        <p style="margin-top: 12px; margin-bottom: 6px; font-weight: 800;">Lampiran ({{ $arrivalAttachments->count() }} file):</p>
        <div class="attachment-grid" style="grid-template-columns: repeat(3, minmax(0, 1fr));">
            @foreach($arrivalAttachments as $att)
                @php
                    $url = asset('storage/' . $att->file_path);
                    $isPdf = \Illuminate\Support\Str::endsWith(strtolower((string)$att->file_path), '.pdf');
                    $category = ucwords(str_replace('_',' ', $att->category ?? 'Lampiran'));
                @endphp
                <div class="attachment-card">
                    <a href="{{ $url }}" target="_blank" class="attachment-preview">
                        @if($isPdf)
                            <i class="fa-solid fa-file-pdf"></i>
                        @else
                            <img src="{{ $url }}" alt="{{ $category }}">
                        @endif
                    </a>
                </div>
            @endforeach
        </div>
    @endif
</div>
<div id="tpl-handover" style="display:none;">
    <p><strong>Waktu Serah Terima:</strong> {{ $task->completed_at ? \Carbon\Carbon::parse($task->completed_at)->format('d M Y, H:i') : 'Belum tercatat' }}</p>
    <p><strong>Catatan:</strong> {{ $task->handover_notes ?? 'Tidak ada catatan.' }}</p>
    @if(isset($handoverAttachments) && $handoverAttachments->count() > 0)
        <p style="margin-top: 12px; margin-bottom: 6px; font-weight: 800;">Lampiran ({{ $handoverAttachments->count() }} file):</p>
        <div class="attachment-grid" style="grid-template-columns: repeat(3, minmax(0, 1fr));">
            @foreach($handoverAttachments as $att)
                @php
                    $url = asset('storage/' . $att->file_path);
                    $isPdf = \Illuminate\Support\Str::endsWith(strtolower((string)$att->file_path), '.pdf');
                    $category = ucwords(str_replace('_',' ', $att->category ?? 'Lampiran'));
                @endphp
                <div class="attachment-card">
                    <a href="{{ $url }}" target="_blank" class="attachment-preview">
                        @if($isPdf)
                            <i class="fa-solid fa-file-pdf"></i>
                        @else
                            <img src="{{ $url }}" alt="{{ $category }}">
                        @endif
                    </a>
                </div>
            @endforeach
        </div>
    @endif
</div>

<script>
function openReportModal(reportType) {
    const modal = document.getElementById('reportModal');
    const content = modal.querySelector('.report-modal-content');
    const title = document.getElementById('reportModalTitle');
    const body = document.getElementById('reportModalBody');
    
    let label = 'Laporan';
    if(reportType === 'departure') label = 'Laporan Keberangkatan';
    if(reportType === 'finance') label = 'Laporan Keuangan';
    if(reportType === 'arrival') label = 'Laporan Kedatangan';
    if(reportType === 'handover') label = 'Laporan Serah Terima';
    
    if (reportType === 'finance') {
        content.classList.add('modal-lg');
    } else {
        content.classList.remove('modal-lg');
    }
    
    title.innerText = label;
    
    const tpl = document.getElementById('tpl-' + reportType);
    if(tpl) {
        body.innerHTML = tpl.innerHTML;
    } else {
        body.innerHTML = '<p>Data tidak ditemukan.</p>';
    }
    
    modal.classList.add('show');
}

function closeReportModal() {
    document.getElementById('reportModal').classList.remove('show');
}

document.addEventListener('DOMContentLoaded', function () {
    const statusSelect = document.getElementById('taskStatusSelect');
    const editStatusButton = document.getElementById('btnEditTaskStatus');

    if (editStatusButton && statusSelect) {
        editStatusButton.addEventListener('click', function () {
            statusSelect.focus();
            if (typeof statusSelect.showPicker === 'function') {
                try { statusSelect.showPicker(); } catch (e) {}
            }
        });
    }

    document.querySelectorAll('.report-action[data-report]').forEach(function (button) {
        button.addEventListener('click', function () {
            openReportModal(this.dataset.report);
            
            document.dispatchEvent(new CustomEvent('task-report:open', {
                detail: {
                    report: this.dataset.report,
                    taskId: @json($task->id)
                }
            }));
        });
    });
});
</script>
</x-app-layout>
