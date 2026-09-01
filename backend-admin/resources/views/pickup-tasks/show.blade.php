<x-app-layout>
<style>
    :root {
        --page-bg: #f8fafc;
        --card-bg: #ffffff;
        --border: #e5e7eb;
        --border-soft: #eef2f7;
        --text: #0f172a;
        --muted: #64748b;
        --orange: #f97316;
        --orange-dark: #ea580c;
        --orange-soft: #fff7ed;
        --green: #059669;
    }

    .task-show-page {
        min-height: 100vh;
        background:
            radial-gradient(circle at top right, rgba(249, 115, 22, .045), transparent 26%),
            var(--page-bg);
        padding: 24px;
    }

    .task-show-container {
        max-width: 1500px;
        margin: 0 auto;
    }

    /* TOP BAR */
    .task-topbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 18px;
        margin-bottom: 18px;
    }

    .btn-back-task {
        display: inline-flex;
        align-items: center;
        gap: 9px;
        min-height: 42px;
        padding: 0 16px;
        border: 1px solid var(--border);
        border-radius: 11px;
        background: #fff;
        color: var(--text);
        font-size: 13px;
        font-weight: 700;
        text-decoration: none;
        box-shadow: 0 3px 10px rgba(15, 23, 42, .025);
        transition: .2s ease;
    }

    .btn-back-task:hover {
        color: var(--orange-dark);
        border-color: #fdba74;
        background: #fffaf5;
        transform: translateX(-2px);
    }

    .status-control {
        width: 235px;
        position: relative;
    }

    .status-control label {
        display: block;
        margin: 0 0 5px 3px;
        color: var(--muted);
        font-size: 9px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: .7px;
    }

    .status-select {
        height: 46px;
        border: 1px solid #fed7aa;
        border-radius: 12px;
        padding: 0 40px 0 14px;
        background-color: #fff;
        color: var(--text);
        font-size: 13px;
        font-weight: 700;
        box-shadow: 0 5px 16px rgba(249, 115, 22, .08);
    }

    .status-select:focus {
        border-color: var(--orange);
        box-shadow: 0 0 0 3px rgba(249, 115, 22, .10);
    }

    /* GENERAL CARD */
    .task-card {
        background: var(--card-bg);
        border: 1px solid var(--border);
        border-radius: 16px;
        box-shadow: 0 10px 28px rgba(15, 23, 42, .04);
    }

    /* HEADER */
    .task-hero {
        position: relative;
        overflow: hidden;
        padding: 22px 28px;
        margin-bottom: 18px;
    }

    .task-hero::before {
        content: "";
        position: absolute;
        inset: 0 auto 0 0;
        width: 5px;
        background: linear-gradient(180deg, var(--orange), var(--orange-dark));
    }

    .hero-inner {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 28px;
    }

    .task-type-badge {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 6px 10px;
        border-radius: 8px;
        background: var(--orange-soft);
        color: var(--orange-dark);
        font-size: 10px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: .45px;
    }

    .task-number {
        margin: 7px 0 2px;
        color: var(--text);
        font-size: 29px;
        line-height: 1;
        font-weight: 850;
    }

    .task-subtitle {
        color: var(--muted);
        font-size: 13px;
        font-weight: 500;
    }

    .task-created {
        display: flex;
        align-items: center;
        gap: 14px;
        text-align: right;
    }

    .task-created-label {
        margin-bottom: 3px;
        color: var(--muted);
        font-size: 10px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: .55px;
    }

    .task-created-value {
        color: var(--text);
        font-size: 14px;
        font-weight: 800;
    }

    .task-created-icon {
        width: 46px;
        height: 46px;
        border-radius: 50%;
        display: grid;
        place-items: center;
        flex: 0 0 46px;
        background: var(--orange-soft);
        border: 1px solid #ffedd5;
        color: var(--orange);
        font-size: 16px;
    }

    /* SECTION */
    .section-card {
        padding: 22px;
        margin-bottom: 18px;
    }

    .section-heading {
        display: flex;
        align-items: center;
        gap: 9px;
        margin-bottom: 20px;
        color: var(--text);
        font-size: 13px;
        font-weight: 850;
        text-transform: uppercase;
        letter-spacing: .25px;
    }

    .section-heading i {
        color: var(--orange);
        font-size: 15px;
    }

    /* DELIVERY INFO - 1 CARD FULL WIDTH */
    .delivery-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        border: 1px solid var(--border-soft);
        border-radius: 13px;
        overflow: hidden;
        background: #fff;
    }

    .delivery-info {
        min-height: 92px;
        padding: 17px 18px;
        border-right: 1px solid var(--border-soft);
        border-bottom: 1px solid var(--border-soft);
    }

    .delivery-info:nth-child(4n) {
        border-right: 0;
    }

    .delivery-info:nth-last-child(-n+4) {
        border-bottom: 0;
    }

    .info-label {
        margin-bottom: 7px;
        color: var(--muted);
        font-size: 9.5px;
        font-weight: 800;
        line-height: 1.3;
        text-transform: uppercase;
        letter-spacing: .5px;
    }

    .info-value {
        color: var(--text);
        font-size: 13.5px;
        font-weight: 700;
        line-height: 1.45;
        word-break: break-word;
    }

    .info-value i {
        width: 18px;
        margin-right: 4px;
        color: var(--orange);
        text-align: center;
    }

    .info-value .icon-blue { color: #2563eb; }
    .info-value .icon-gray { color: #64748b; }
    .info-value .icon-green { color: #10b981; }
    .info-value .icon-cyan { color: #06b6d4; }

    .type-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 5px 8px;
        border-radius: 7px;
        background: var(--orange-soft);
        color: var(--orange-dark);
        font-size: 11px;
        font-weight: 800;
    }

    /* ITEM TABLE */
    .items-card {
        padding: 0;
        overflow: hidden;
    }

    .items-card-head {
        padding: 20px 22px 15px;
    }

    .items-card .section-heading {
        margin-bottom: 0;
    }

    .items-table-wrap {
        padding: 0 20px 20px;
        overflow-x: auto;
    }

    .items-table-shell {
        min-width: 860px;
        border: 1px solid var(--border);
        border-radius: 12px;
        overflow: hidden;
    }

    .items-table {
        width: 100%;
        margin: 0;
        border-collapse: separate;
        border-spacing: 0;
        color: var(--text);
        font-size: 12px;
    }

    .items-table thead th {
        padding: 12px 13px;
        background: #f8fafc;
        color: #334155;
        font-size: 10.5px;
        font-weight: 850;
        white-space: nowrap;
        border-right: 1px solid var(--border);
        border-bottom: 1px solid var(--border);
    }

    .items-table thead th:last-child {
        border-right: 0;
    }

    .items-table tbody td {
        padding: 12px 13px;
        vertical-align: middle;
        background: #fff;
        border-right: 1px solid var(--border-soft);
        border-bottom: 1px solid var(--border-soft);
    }

    .items-table tbody td:last-child {
        border-right: 0;
    }

    .items-table tbody tr:last-child td {
        border-bottom: 0;
    }

    .qty-cell {
        font-weight: 800;
    }

    .price-cell {
        color: var(--green);
        font-weight: 700;
        white-space: nowrap;
    }

    .items-summary {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 18px;
        padding: 13px 15px;
        border-top: 1px solid var(--border);
        background: #fcfcfd;
    }

    .summary-left,
    .summary-right {
        display: flex;
        align-items: center;
        gap: 28px;
        color: var(--muted);
        font-size: 11px;
        font-weight: 700;
    }

    .summary-value {
        color: var(--text);
        font-weight: 850;
    }

    .summary-price {
        color: var(--green);
        font-size: 15px;
        font-weight: 850;
    }

    .empty-items {
        padding: 34px 20px;
        text-align: center;
        color: var(--muted);
        font-size: 12px;
    }

    .empty-items i {
        display: block;
        margin-bottom: 8px;
        color: #fdba74;
        font-size: 28px;
    }

    @media (max-width: 1100px) {
        .delivery-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .delivery-info:nth-child(4n) {
            border-right: 1px solid var(--border-soft);
        }

        .delivery-info:nth-child(2n) {
            border-right: 0;
        }

        .delivery-info:nth-last-child(-n+4) {
            border-bottom: 1px solid var(--border-soft);
        }

        .delivery-info:nth-last-child(-n+2) {
            border-bottom: 0;
        }
    }

    @media (max-width: 767px) {
        .task-show-page {
            padding: 14px;
        }

        .task-topbar,
        .hero-inner {
            align-items: stretch;
            flex-direction: column;
        }

        .status-control {
            width: 100%;
        }

        .task-created {
            justify-content: space-between;
            text-align: left;
        }

        .delivery-grid {
            grid-template-columns: 1fr;
        }

        .delivery-info,
        .delivery-info:nth-child(2n),
        .delivery-info:nth-child(4n) {
            border-right: 0;
            border-bottom: 1px solid var(--border-soft);
        }

        .delivery-info:last-child {
            border-bottom: 0;
        }

        .items-summary {
            align-items: flex-start;
            flex-direction: column;
        }
    }
</style>

@php
    $isPickup = $task->task_type === 'pickup';
    $itemsList = [];

    /*
    |--------------------------------------------------------------------------
    | DETAIL ITEM
    |--------------------------------------------------------------------------
    | Tetap mempertahankan seluruh fallback data dari file lama:
    | 1. relation items
    | 2. JSON legacy pickup
    | 3. kolom legacy pickup
    | 4. source_data delivery
    | 5. kolom Sales Order
    */

    if ($isPickup) {
        $relatedItems = $task->items ?? collect();
    } else {
        $relatedItems = isset($task->salesOrder) ? ($task->salesOrder->items ?? collect()) : collect();
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
                    $rawItems = $json['items'] ?? [];

                    foreach ($rawItems as $raw) {
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

                $rawItems = $sourceData['items'] ?? [];

                foreach ($rawItems as $raw) {
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

    $totalQty = collect($itemsList)->sum(fn ($item) => (float) ($item['qty'] ?? 0));
    $totalHarga = collect($itemsList)->sum(function ($item) {
        return (float) ($item['qty'] ?? 0) * (float) ($item['harga_satuan'] ?? 0);
    });

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

    $statusOptions = [
        'assigned' => 'Assigned (Baru)',
        'on_route' => 'On Route (Sedang Jalan)',
        'arrived' => 'Arrived (Telah Sampai)',
        'delivered' => 'Delivered (Selesai Dikirim)',
        'failed' => 'Failed (Gagal)',
        'cancelled' => 'Cancelled (Dibatalkan)',
    ];
@endphp

<div class="task-show-page">
    <div class="task-show-container">

        {{-- TOP BAR --}}
        <div class="task-topbar">
            <a href="{{ route('pickup-tasks.index') }}" class="btn-back-task">
                <i class="fa-solid fa-arrow-left"></i>
                <span>Kembali ke Daftar Tugas</span>
            </a>

            <div class="status-control">
                <label for="taskStatusSelect">Status Saat Ini</label>
                <select
                    id="taskStatusSelect"
                    class="form-select status-select"
                    data-task-id="{{ $task->id }}"
                    aria-label="Status tugas"
                >
                    @if(!array_key_exists($task->status, $statusOptions))
                        <option value="{{ $task->status }}" selected>
                            {{ ucwords(str_replace('_', ' ', $task->status)) }}
                        </option>
                    @endif

                    @foreach($statusOptions as $value => $label)
                        <option value="{{ $value }}" {{ $task->status === $value ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        {{-- HEADER TASK --}}
        <div class="task-card task-hero">
            <div class="hero-inner">
                <div>
                    <span class="task-type-badge">
                        <i class="fa-solid {{ $isPickup ? 'fa-box-open' : 'fa-truck' }}"></i>
                        {{ $isPickup ? 'Pickup Task' : 'Delivery Task' }}
                    </span>

                    <h1 class="task-number">
                        {{ $isPickup
                            ? ($task->reference_number ?? '-')
                            : ($task->salesOrder->so_number ?? 'N/A') }}
                    </h1>

                    <div class="task-subtitle">
                        @if($isPickup)
                            {{ $task->pickup_name ?? 'Pickup' }}
                        @else
                            Customer: {{ $task->salesOrder->customer_name ?? '-' }}
                        @endif
                    </div>
                </div>

                <div class="task-created">
                    <div>
                        <div class="task-created-label">Dibuat / Ditugaskan Pada</div>
                        <div class="task-created-value">
                            @if($isPickup)
                                {{ $task->created_at ? $task->created_at->format('d M Y, H:i') : '-' }}
                            @else
                                {{ $task->assigned_at
                                    ? \Carbon\Carbon::parse($task->assigned_at)->format('d M Y, H:i')
                                    : '-' }}
                            @endif
                        </div>
                    </div>

                    <div class="task-created-icon">
                        <i class="fa-regular fa-calendar"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- INFORMASI PENGIRIMAN - FULL WIDTH --}}
        <div class="task-card section-card">
            <div class="section-heading">
                <i class="fa-solid fa-truck"></i>
                <span>Informasi Pengiriman & Lokasi</span>
            </div>

            <div class="delivery-grid">
                {{-- ROW 1 --}}
                <div class="delivery-info">
                    <div class="info-label">Driver</div>
                    <div class="info-value">
                        <i class="fa-solid fa-circle-user icon-blue"></i>
                        {{ $task->driver->full_name ?? 'N/A' }}
                    </div>
                </div>

                <div class="delivery-info">
                    <div class="info-label">Kendaraan</div>
                    <div class="info-value">
                        <i class="fa-solid fa-truck icon-gray"></i>
                        {{ $task->vehicle->plate_number ?? 'N/A' }}
                        @if(!empty($task->vehicle->name))
                            <span style="color: var(--muted); font-weight: 600;">
                                — {{ $task->vehicle->name }}
                            </span>
                        @endif
                    </div>
                </div>

                <div class="delivery-info">
                    <div class="info-label">{{ $isPickup ? 'Nama Tempat Pengambilan' : 'Nama Pelanggan' }}</div>
                    <div class="info-value">
                        <i class="fa-solid {{ $isPickup ? 'fa-warehouse' : 'fa-building' }} icon-cyan"></i>
                        {{ $isPickup
                            ? ($task->pickup_name ?? '-')
                            : ($task->salesOrder->customer_name ?? '-') }}
                    </div>
                </div>

                <div class="delivery-info">
                    <div class="info-label">{{ $isPickup ? 'Alamat Pengambilan' : 'Alamat Tujuan Pengiriman' }}</div>
                    <div class="info-value">
                        <i class="fa-solid fa-location-dot icon-green"></i>
                        {{ $isPickup
                            ? ($task->pickup_location ?? '-')
                            : $deliveryAddress }}
                    </div>
                </div>

                {{-- ROW 2 --}}
                <div class="delivery-info">
                    <div class="info-label">Jenis Tugas</div>
                    <div class="info-value">
                        <span class="type-pill">
                            <i class="fa-solid {{ $isPickup ? 'fa-box-open' : 'fa-truck-fast' }}" style="color:inherit; margin:0;"></i>
                            {{ $isPickup ? 'Mengambil Barang (Pickup)' : 'Mengirim Barang (Delivery)' }}
                        </span>
                    </div>
                </div>

                <div class="delivery-info">
                    <div class="info-label">Nomor Referensi</div>
                    <div class="info-value">
                        {{ $isPickup
                            ? ($task->reference_number ?? '-')
                            : ($task->salesOrder->so_number ?? '-') }}
                    </div>
                </div>

                <div class="delivery-info">
                    <div class="info-label">{{ $isPickup ? 'Alamat Tujuan' : 'Nama Tempat Tujuan' }}</div>
                    <div class="info-value">
                        @if($isPickup)
                            {{ $task->destination ?? $task->pickup_destination ?? '-' }}
                        @else
                            {{ $deliveryDestinationName }}
                        @endif
                    </div>
                </div>

                <div class="delivery-info">
                    <div class="info-label">Status Pengiriman</div>
                    <div class="info-value">
                        <i class="fa-solid fa-circle-dot"></i>
                        {{ $statusOptions[$task->status] ?? ucwords(str_replace('_', ' ', $task->status)) }}
                    </div>
                </div>
            </div>
        </div>

        {{-- DETAIL BARANG - TABLE FULL WIDTH --}}
        <div class="task-card items-card">
            <div class="items-card-head">
                <div class="section-heading">
                    <i class="fa-solid fa-box"></i>
                    <span>Detail Barang</span>
                </div>
            </div>

            <div class="items-table-wrap">
                <div class="items-table-shell">
                    @if(count($itemsList) > 0)
                        <table class="items-table">
                            <thead>
                                <tr>
                                    <th style="width: 60px;" class="text-center">No</th>
                                    <th style="width: 145px;">No Barang</th>
                                    <th>Nama / Deskripsi Barang</th>
                                    <th style="width: 110px;" class="text-center">Qty</th>
                                    <th style="width: 120px;">Satuan</th>
                                    <th style="width: 160px;" class="text-end">Harga Satuan</th>
                                    <th style="width: 170px;" class="text-end">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($itemsList as $index => $item)
                                    @php
                                        $qty = (float) ($item['qty'] ?? 0);
                                        $harga = (float) ($item['harga_satuan'] ?? 0);
                                        $lineTotal = $qty * $harga;
                                    @endphp
                                    <tr>
                                        <td class="text-center">{{ $index + 1 }}</td>
                                        <td>{{ $item['no_barang'] ?? '-' }}</td>
                                        <td style="font-weight: 650;">{{ $item['deskripsi_barang'] ?? '-' }}</td>
                                        <td class="text-center qty-cell">
                                            {{ number_format($qty, 2, ',', '.') }}
                                        </td>
                                        <td>{{ $item['uom'] ?? '-' }}</td>
                                        <td class="text-end price-cell">
                                            {{ $harga > 0 ? 'Rp ' . number_format($harga, 0, ',', '.') : '-' }}
                                        </td>
                                        <td class="text-end price-cell">
                                            {{ $lineTotal > 0 ? 'Rp ' . number_format($lineTotal, 0, ',', '.') : '-' }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                        <div class="items-summary">
                            <div class="summary-left">
                                <span>
                                    Total Item:
                                    <span class="summary-value">{{ count($itemsList) }}</span>
                                </span>

                                <span>
                                    Total Qty:
                                    <span class="summary-value">
                                        {{ number_format($totalQty, 2, ',', '.') }}
                                    </span>
                                </span>
                            </div>

                            <div class="summary-right">
                                <span>Total Harga</span>
                                <span class="summary-price">
                                    Rp {{ number_format($totalHarga, 0, ',', '.') }}
                                </span>
                            </div>
                        </div>
                    @else
                        <div class="empty-items">
                            <i class="fa-solid fa-box-open"></i>
                            Belum ada detail barang pada tugas ini.
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- JADWAL & SHIFT DRIVER (PLACEHOLDER HPP/RITASE) --}}
        <div class="task-card section-card">
            <div class="section-heading">
                <i class="fa-solid fa-clock-rotate-left"></i>
                <span>Jadwal & Shift Driver</span>
            </div>
            <div class="items-table-wrap px-0 pb-0">
                <div class="items-table-shell" style="min-width: unset;">
                    <table class="items-table">
                        <thead>
                            <tr>
                                <th style="width: 25%;">Shift / Jadwal Penugasan</th>
                                <th>Waktu Mulai Perjalanan</th>
                                <th>Waktu Tiba (Kedatangan)</th>
                                <th>Status HPP / Keuangan</th>
                                <th class="text-end" style="width: 100px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="5" class="empty-items text-center" style="padding: 30px 20px;">
                                    <i class="fa-regular fa-calendar-xmark" style="font-size: 24px; color: var(--muted); margin-bottom: 8px;"></i>
                                    <div style="color: var(--text); font-weight: 700;">Belum ada data shift</div>
                                    <div style="font-size: 11px; margin-top: 4px;">Data ritase dan jadwal shift driver akan terintegrasi otomatis dengan modul Keuangan / HPP.</div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- CHECKLIST KEBERANGKATAN --}}
        @php
            $checklist = $task->departure_checklist;
            if (is_string($checklist)) $checklist = json_decode($checklist, true);
        @endphp
        @if(!empty($checklist))
        <div class="task-card section-card">
            <div class="section-heading">
                <i class="fa-solid fa-clipboard-check"></i>
                <span>Checklist Keberangkatan</span>
            </div>
            <div class="delivery-grid" style="grid-template-columns: repeat(2, 1fr);">
                @foreach($checklist as $label => $status)
                    <div class="delivery-info">
                        <div class="info-label">{{ $label }}</div>
                        <div class="info-value">
                            @if($status === 'check')
                                <i class="fa-solid fa-circle-check" style="color:#10b981;"></i>
                                <span style="color:#10b981; font-weight:700;">OK</span>
                            @elseif($status === 'cross')
                                <i class="fa-solid fa-circle-xmark" style="color:#ef4444;"></i>
                                <span style="color:#ef4444; font-weight:700;">Rusak / Tidak OK</span>
                            @elseif($status === 'warning')
                                <i class="fa-solid fa-triangle-exclamation" style="color:#f59e0b;"></i>
                                <span style="color:#f59e0b; font-weight:700;">Perlu Perhatian</span>
                            @else
                                <i class="fa-regular fa-square" style="color:#94a3b8;"></i>
                                <span style="color:#94a3b8;">Belum Diisi</span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- BUKTI LAMPIRAN (FOTO / PDF) --}}
        @php
            $attachments = $task->attachments ?? collect();
        @endphp
        @if($attachments->count() > 0)
        <div class="task-card section-card">
            <div class="section-heading">
                <i class="fa-solid fa-images"></i>
                <span>Bukti Lampiran</span>
            </div>
            <div style="display:flex; flex-wrap:wrap; gap:12px;">
                @foreach($attachments as $att)
                    @php
                        $url = asset('storage/' . $att->file_path);
                        $isPdf = str_ends_with(strtolower($att->file_path), '.pdf');
                    @endphp
                    <div style="width:140px; text-align:center;">
                        @if($isPdf)
                            <a href="{{ $url }}" target="_blank" style="display:block; width:140px; height:100px; border:1px solid var(--border); border-radius:10px; background:#fff8f0; display:flex; align-items:center; justify-content:center;">
                                <i class="fa-solid fa-file-pdf" style="font-size:36px; color:#ef4444;"></i>
                            </a>
                        @else
                            <a href="{{ $url }}" target="_blank">
                                <img src="{{ $url }}" alt="Bukti" style="width:140px; height:100px; object-fit:cover; border-radius:10px; border:1px solid var(--border);" />
                            </a>
                        @endif
                        <div style="margin-top:6px; font-size:10px; color:var(--muted); font-weight:700; text-transform:uppercase;">
                            {{ str_replace('_', ' ', $att->category) }}
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const statusSelect = document.getElementById('taskStatusSelect');

    if (!statusSelect) return;

    /*
     * SELECT STATUS SUDAH DIBUAT SESUAI UI.
     *
     * File show lama belum memberikan route/controller khusus untuk update status,
     * jadi di sini sengaja TIDAK membuat route Laravel baru agar tidak menyebabkan
     * RouteNotFoundException.
     *
     * Jika route update status Anda sudah ada, event change/AJAX dapat dipasang
     * pada statusSelect ini tanpa mengubah desain.
     */
});
</script>
</x-app-layout>
