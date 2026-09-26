<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\Response;

class AkurasiService{
    /**
     * API Key Akurasi
     */
    protected string $apiKey = 'Ym95Y29tcG9zaXRpb25leHBsYW5hdGlvbnRob3VnaHRwZWFjZWdpcmxjb2FjaHNlbnM=';

    /**
     * Base URL API Akurasi
     */
    protected string $baseUrl = 'https://akurasi-api.aqpa-indonesia.com/api/integration';

    /**
     * Timeout request ke API dalam detik
     */
    protected int $timeout = 30;

    /**
     * ============================================================
     * DEFAULT DATE RANGE
     * ============================================================
     *
     * Default:
     * 1 minggu sebelum awal bulan berjalan
     * sampai akhir bulan berjalan.
     *
     * Contoh:
     * Bulan September 2026
     *
     * date_from = 2026-08-25
     * date_to   = 2026-09-30
     */
    protected function getDefaultDateRange(): array
    {
        return [
            'date_from' => Carbon::now()
                ->startOfMonth()
                ->subWeek()
                ->format('Y-m-d'),

            'date_to' => Carbon::now()
                ->endOfMonth()
                ->format('Y-m-d'),
        ];
    }

    /**
     * ============================================================
     * HTTP CLIENT
     * ============================================================
     *
     * Header standar untuk seluruh request Akurasi.
     */
    protected function request()
    {
        return Http::withHeaders([
            'X-API-Key' => $this->apiKey,
            'Accept' => 'application/json',
        ])
            ->timeout($this->timeout)
            ->connectTimeout(10);
    }

    /**
     * ============================================================
     * HANDLE API ERROR
     * ============================================================
     */
    protected function validateResponse(
        Response $response,
        string $context
    ): void {
        if ($response->successful()) {
            return;
        }

        $message = $response->json('message')
            ?? $response->json('error')
            ?? "Gagal mengambil data {$context} dari API Akurasi.";

        throw new \Exception(
            $message . ' [HTTP ' . $response->status() . ']'
        );
    }

    /**
     * ============================================================
     * NORMALIZE FILTER
     * ============================================================
     *
     * Digunakan agar string kosong dari Blade:
     *
     * date_from=
     * date_to=
     *
     * tetap menggunakan default date range.
     */
    protected function filterValue(
        array $filters,
        string $key,
        mixed $default = null
    ): mixed {
        if (!array_key_exists($key, $filters)) {
            return $default;
        }

        $value = $filters[$key];

        if ($value === null || $value === '') {
            return $default;
        }

        return $value;
    }

    /**
     * ============================================================
     * SALES ORDER
     * ============================================================
     */

    /**
     * Search / List Sales Orders
     */
    public function searchSo(array $filters = []): array
    {
        $defaultRange = $this->getDefaultDateRange();

        $page = max(
            1,
            (int) $this->filterValue(
                $filters,
                'page',
                1
            )
        );

        $limit = max(
            1,
            min(
                100,
                (int) $this->filterValue(
                    $filters,
                    'per_page',
                    20
                )
            )
        );

        $params = [
            'page' => $page,

            'limit' => $limit,

            'search' => $this->filterValue(
                $filters,
                'search'
            ),

            'date_from' => $this->filterValue(
                $filters,
                'date_from',
                $defaultRange['date_from']
            ),

            'date_to' => $this->filterValue(
                $filters,
                'date_to',
                $defaultRange['date_to']
            ),

            'status' => $this->filterValue(
                $filters,
                'status'
            ),

            'status_hold' => $this->filterValue(
                $filters,
                'status_hold'
            ),

            'sort_col' => $this->filterValue(
                $filters,
                'sort_col',
                'tgl_so'
            ),

            'sort_dir' => $this->normalizeSortDirection(
                $this->filterValue(
                    $filters,
                    'sort_dir',
                    'desc'
                )
            ),
        ];

        /**
         * Hapus null / string kosong.
         *
         * Jangan gunakan array_filter($params) tanpa callback
         * karena nilai "0" pada status_hold bisa ikut terhapus.
         */
        $params = array_filter(
            $params,
            fn ($value) => $value !== null && $value !== ''
        );

        $response = $this->request()
            ->get(
                $this->baseUrl . '/penjualan-so',
                $params
            );

        $this->validateResponse(
            $response,
            'Sales Order'
        );

        $json = $response->json();

        if (!is_array($json)) {
            throw new \Exception(
                'Format response Sales Order dari API Akurasi tidak valid.'
            );
        }

        $data = $json['data'] ?? [];
        $meta = $json['meta'] ?? [];

        if (!is_array($data)) {
            $data = [];
        }

        if (!is_array($meta)) {
            $meta = [];
        }

        $currentPage = (int) (
            $meta['current_page']
            ?? $meta['page']
            ?? $page
        );

        $lastPage = (int) (
            $meta['total_pages']
            ?? $meta['last_page']
            ?? 1
        );

        $total = (int) (
            $meta['total_rows']
            ?? $meta['total']
            ?? count($data)
        );

        $from = count($data) > 0
            ? (($currentPage - 1) * $limit) + 1
            : 0;

        $to = count($data) > 0
            ? $from + count($data) - 1
            : 0;

        return [
            'data' => $data,

            'current_page' => $currentPage,

            'last_page' => max(
                1,
                $lastPage
            ),

            'total' => $total,

            'from' => $from,

            'to' => $to,

            'per_page' => $limit,
        ];
    }

    /**
     * Get Detail Sales Order
     */
    public function detailSo(string $noSo): array
    {
        $noSo = trim(
            urldecode($noSo)
        );

        if ($noSo === '') {
            throw new \Exception(
                'Nomor Sales Order tidak boleh kosong.'
            );
        }

        /**
         * Gunakan endpoint detail khusus:
         * GET /api/integration/penjualan-so/{no_so}
         *
         * Endpoint ini langsung mengembalikan detail SO
         * beserta daftar item-nya, tanpa perlu search + filter.
         */
        $response = $this->request()
            ->get(
                $this->baseUrl . '/penjualan-so/' . urlencode($noSo)
            );

        $this->validateResponse(
            $response,
            'Detail Sales Order'
        );

        $json = $response->json();

        if (!is_array($json)) {
            throw new \Exception(
                'Format response Detail Sales Order tidak valid.'
            );
        }

        /**
         * Response dari endpoint detail bisa berupa:
         * - { data: [...items] } (array of items)
         * - { data: { no_so, items: [...] } } (object with items)
         * - langsung flat object
         */
        $data = $json['data'] ?? $json;

        // Jika data adalah object tunggal dengan key 'items'
        if (isset($data['items']) && is_array($data['items'])) {
            $items = collect($data['items']);
            $header = $data;
        }
        // Jika data adalah array of items (sama seperti search endpoint)
        elseif (is_array($data) && isset($data[0])) {
            $items = collect($data)
                ->filter(function ($item) use ($noSo) {
                    if (!is_array($item)) {
                        return false;
                    }

                    $itemNoSo = trim(
                        (string) ($item['no_so'] ?? '')
                    );

                    return strcasecmp(
                        $itemNoSo,
                        $noSo
                    ) === 0;
                })
                ->values();
            $header = null;
        }
        // Fallback: single object tanpa items
        else {
            $items = collect([$data]);
            $header = $data;
        }

        if ($items->isEmpty()) {
            throw new \Exception(
                "Sales Order {$noSo} tidak ditemukan."
            );
        }

        $first = $items->first();

        /**
         * Total.
         *
         * Prioritas:
         * header total_amount -> sum(amount) -> sum(subtotal).
         */
        $totalAmount = isset($header['total_amount'])
            ? (float) $header['total_amount']
            : $items->sum(
                function ($item) {
                    if (isset($item['amount'])) {
                        return (float) $item['amount'];
                    }

                    return (float) (
                        $item['subtotal']
                        ?? 0
                    );
                }
            );

        return [
            'no_so' => $header['no_so']
                ?? $first['no_so']
                ?? $noSo,

            'tgl_so' => $header['tgl_so']
                ?? $first['tgl_so']
                ?? null,

            'est_kirim' => $header['tgl_estimasi']
                ?? $first['tgl_estimasi']
                ?? $first['est_kirim']
                ?? null,

            'pelanggan' => $header['nama_pelanggan']
                ?? $first['nama_pelanggan']
                ?? null,

            'shipto' => $header['shipto']
                ?? $first['shipto']
                ?? $first['ship_to']
                ?? null,

            'status' => $header['status']
                ?? $first['status']
                ?? null,

            'total_amount' => $totalAmount,

            /**
             * Raw item dikembalikan agar Blade bisa membaca:
             *
             * no_barang
             * deskripsi_barang
             * category_produk
             * qty
             * qty_shipped
             * sisa_kirim
             * stok_tersedia
             * uom
             * unit_price
             * discount_amount
             * ppn_rate
             * subtotal
             * amount
             * no_pengiriman
             * tgl_pengiriman
             * status
             * nama_salesman
             * no_po_customer
             * is_held
             * hold_note
             */
            'items' => $items->toArray(),
        ];
    }

    /**
     * ============================================================
     * PURCHASE ORDER
     * ============================================================
     */

    /**
     * Search / List Purchase Orders
     */
    public function searchPo(array $filters = []): array
    {
        $defaultRange = $this->getDefaultDateRange();

        $page = max(
            1,
            (int) $this->filterValue(
                $filters,
                'page',
                1
            )
        );

        $limit = max(
            1,
            min(
                100,
                (int) $this->filterValue(
                    $filters,
                    'per_page',
                    20
                )
            )
        );

        $params = [
            'page' => $page,

            'limit' => $limit,

            'search' => $this->filterValue(
                $filters,
                'search'
            ),

            'date_from' => $this->filterValue(
                $filters,
                'date_from',
                $defaultRange['date_from']
            ),

            'date_to' => $this->filterValue(
                $filters,
                'date_to',
                $defaultRange['date_to']
            ),

            /**
             * Sorting PO
             *
             * Ini diperlukan karena Blade PO kamu
             * mengirim sort_col dan sort_dir.
             */
            'sort_col' => $this->filterValue(
                $filters,
                'sort_col',
                'tgl_pembelian'
            ),

            'sort_dir' => $this->normalizeSortDirection(
                $this->filterValue(
                    $filters,
                    'sort_dir',
                    'desc'
                )
            ),
        ];

        $params = array_filter(
            $params,
            fn ($value) => $value !== null && $value !== ''
        );

        $response = $this->request()
            ->get(
                $this->baseUrl . '/pembelian',
                $params
            );

        $this->validateResponse(
            $response,
            'Purchase Order'
        );

        $json = $response->json();

        if (!is_array($json)) {
            throw new \Exception(
                'Format response Purchase Order dari API Akurasi tidak valid.'
            );
        }

        $data = $json['data'] ?? [];
        $meta = $json['meta'] ?? [];

        if (!is_array($data)) {
            $data = [];
        }

        if (!is_array($meta)) {
            $meta = [];
        }

        $currentPage = (int) (
            $meta['current_page']
            ?? $meta['page']
            ?? $page
        );

        $lastPage = (int) (
            $meta['total_pages']
            ?? $meta['last_page']
            ?? 1
        );

        $total = (int) (
            $meta['total_rows']
            ?? $meta['total']
            ?? count($data)
        );

        $from = count($data) > 0
            ? (($currentPage - 1) * $limit) + 1
            : 0;

        $to = count($data) > 0
            ? $from + count($data) - 1
            : 0;

        return [
            'data' => $data,

            'current_page' => $currentPage,

            'last_page' => max(
                1,
                $lastPage
            ),

            'total' => $total,

            'from' => $from,

            'to' => $to,

            'per_page' => $limit,
        ];
    }

    /**
     * Get Detail Purchase Order
     */
    public function detailPo(string $noPo): array
    {
        $noPo = trim(
            urldecode($noPo)
        );

        if ($noPo === '') {
            throw new \Exception(
                'Nomor Purchase Order tidak boleh kosong.'
            );
        }

        /**
         * Gunakan endpoint detail khusus:
         * GET /api/integration/pembelian/{no_po}
         *
         * Endpoint ini langsung mengembalikan detail PO
         * beserta daftar item-nya, tanpa perlu search + filter.
         */
        $response = $this->request()
            ->get(
                $this->baseUrl . '/pembelian/' . urlencode($noPo)
            );

        $this->validateResponse(
            $response,
            'Detail Purchase Order'
        );

        $json = $response->json();

        if (!is_array($json)) {
            throw new \Exception(
                'Format response Detail Purchase Order tidak valid.'
            );
        }

        /**
         * Response dari endpoint detail bisa berupa:
         * - { data: [...items] } (array of items)
         * - { data: { no_po, items: [...] } } (object with items)
         * - langsung flat object
         */
        $data = $json['data'] ?? $json;

        // Jika data adalah object tunggal dengan key 'items'
        if (isset($data['items']) && is_array($data['items'])) {
            $items = collect($data['items']);
            $header = $data;
        }
        // Jika data adalah array of items (sama seperti search endpoint)
        elseif (is_array($data) && isset($data[0])) {
            $items = collect($data)
                ->filter(function ($item) use ($noPo) {
                    if (!is_array($item)) {
                        return false;
                    }

                    $noPembelian = trim(
                        (string) (
                            $item['no_pembelian']
                            ?? ''
                        )
                    );

                    $poNumber = trim(
                        (string) (
                            $item['no_po']
                            ?? ''
                        )
                    );

                    return (
                        strcasecmp(
                            $noPembelian,
                            $noPo
                        ) === 0
                    ) || (
                        strcasecmp(
                            $poNumber,
                            $noPo
                        ) === 0
                    );
                })
                ->values();
            $header = null;
        }
        // Fallback: single object tanpa items
        else {
            $items = collect([$data]);
            $header = $data;
        }

        if ($items->isEmpty()) {
            throw new \Exception(
                "Purchase Order {$noPo} tidak ditemukan."
            );
        }

        $first = $items->first();

        /**
         * Total keseluruhan PO.
         */
        $totalAmount = isset($header['total_amount'])
            ? (float) $header['total_amount']
            : $items->sum(
                function ($item) {
                    if (isset($item['amount'])) {
                        return (float) $item['amount'];
                    }

                    if (isset($item['subtotal'])) {
                        return (float) $item['subtotal'];
                    }

                    /**
                     * Fallback:
                     * qty x harga_satuan
                     */
                    $qty = (float) (
                        $item['qty']
                        ?? 0
                    );

                    $price = (float) (
                        $item['price']
                        ?? $item['harga_satuan']
                        ?? 0
                    );

                    return $qty * $price;
                }
            );

        return [
            'no_po' => $header['no_pembelian']
                ?? $header['no_po']
                ?? $first['no_pembelian']
                ?? $first['no_po']
                ?? $noPo,

            'tgl_po' => $header['tgl_pembelian']
                ?? $first['tgl_pembelian']
                ?? $first['tgl_po']
                ?? null,

            'est_kirim' => $header['tgl_ekspetasi']
                ?? $first['tgl_ekspetasi']
                ?? $first['tgl_estimasi']
                ?? null,

            'pemasok' => $header['nama_pemasok']
                ?? $first['nama_pemasok']
                ?? null,

            /**
             * Blade lama menggunakan variable shipto,
             * sedangkan API PO memiliki SO NO.
             */
            'shipto' => $header['so_no']
                ?? $first['so_no']
                ?? $first['shipto']
                ?? $first['ship_to']
                ?? null,

            'status' => $header['status_pembayaran']
                ?? $first['status_pembayaran']
                ?? $first['status_bayar']
                ?? null,

            'total_amount' => $totalAmount,

            /**
             * Raw items supaya seluruh kolom PO
             * tetap tersedia di Blade.
             */
            'items' => $items->toArray(),
        ];
    }

    /**
     * ============================================================
     * HELPER
     * ============================================================
     */

    /**
     * Normalize sorting direction
     */
    protected function normalizeSortDirection(
        mixed $direction
    ): string {
        return strtolower(
            (string) $direction
        ) === 'asc'
            ? 'asc'
            : 'desc';
    }
}