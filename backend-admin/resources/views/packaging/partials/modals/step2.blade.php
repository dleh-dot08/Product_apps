{{-- 
    STEP 2 — KONFIGURASI PACKAGING
    Bootstrap 5 + Font Awesome
    Orange Theme + Dark Mode
    Tanpa header internal dan tanpa field Jumlah Kaki Balok
--}}

<style>
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

<div class="form-step" id="step-2">
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
                                {{ $user->name }}
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
                    <label class="s2-label" for="s2_qty_per_pack">
                        <i class="fa-solid fa-box-open"></i>
                        Qty Product per Packing
                    </label>

                    <input
                        type="number"
                        class="form-control"
                        id="s2_qty_per_pack"
                        value="1"
                        min="1"
                    >
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
                                <option value="Not Include">Not Include</option>
                            </select>

                            <!-- Penyanggah Bawah arah Horizontal -->
                            <input type="text" class="form-control text-center" id="s2_pb_arah" value="Horizontal" readonly style="background-color: #e9ecef; cursor: not-allowed;">

                            <select class="form-select" id="s2_pb_material">
                                <option value="">Pilih Material...</option>
                                @if(isset($balokMaterials) && count($balokMaterials) > 0)
                                    @foreach($balokMaterials as $mat)
                                        <option value="{{ $mat->code ?? $mat->id }}">{{ $mat->wood_type }} - {{ (float)$mat->thickness }}x{{ (float)$mat->width }}</option>
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
                                        <option value="{{ $mat->code ?? $mat->id }}">{{ $mat->wood_type }} - {{ (float)$mat->thickness }}x{{ (float)$mat->width }}</option>
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
                                <option value="Not Include">Not Include</option>
                            </select>

                            <select class="form-select" id="s2_kb_arah">
                                <option value="Vertikal">Vertikal</option>
                                <option value="Horizontal">Horizontal</option>
                            </select>

                            <select class="form-select" id="s2_kb_material">
                                <option value="">Pilih Material...</option>
                                @if(isset($balokMaterials) && count($balokMaterials) > 0)
                                    @foreach($balokMaterials as $mat)
                                        <option value="{{ $mat->code ?? $mat->id }}">{{ $mat->wood_type }} - {{ (float)$mat->thickness }}x{{ (float)$mat->width }}</option>
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
                            <label class="s2-compact-label" for="s2_jarak">
                                <span class="s2-compact-icon">
                                    <i class="fa-solid fa-ruler"></i>
                                </span>
                                Jarak Penyanggah
                            </label>

                            <input
                                type="number"
                                class="form-control"
                                id="s2_jarak"
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
                                <option value="Not Include">Not Include</option>
                            </select>

                            <!-- Penyanggah Atas arah Vertikal -->
                            <input type="text" class="form-control text-center" id="s2_pa_arah" value="Vertikal" readonly style="background-color: #e9ecef; cursor: not-allowed;">

                            <select class="form-select" id="s2_pa_material">
                                <option value="">Pilih Material...</option>
                                @if(isset($balokMaterials) && count($balokMaterials) > 0)
                                    @foreach($balokMaterials as $mat)
                                        <option value="{{ $mat->code ?? $mat->id }}">{{ $mat->wood_type }} - {{ (float)$mat->thickness }}x{{ (float)$mat->width }}</option>
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
                                        <option value="{{ $mat->code ?? $mat->id }}">{{ $mat->wood_type }} - {{ (float)$mat->thickness }}x{{ (float)$mat->width }}</option>
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