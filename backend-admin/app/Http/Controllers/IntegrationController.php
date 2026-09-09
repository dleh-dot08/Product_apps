<?php

namespace App\Http\Controllers;

use App\Models\PembelianOrderAkurasi;
use App\Models\SalesOrderAkurasi;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;

class IntegrationController extends Controller
{
    public function triggerSyncSo(): JsonResponse
    {
        set_time_limit(300);
        Artisan::call('akurasi:sync', ['--only' => 'so']);

        return response()->json([
            'status' => 'success',
            'message' => 'Sync SO berhasil dijalankan di latar belakang',
        ]);
    }

    public function searchSo(Request $request): JsonResponse
    {
        $search = (string) $request->input('q', '');
        $page = max((int) $request->input('page', 1), 1);
        $perPage = max((int) $request->input('per_page', 20), 1);

        try {
            $query = SalesOrderAkurasi::query();
            if ($search !== '') {
                $query->where(function ($query) use ($search) {
                    $query->where('no_so', 'like', "%{$search}%")
                        ->orWhere('deskripsi_barang', 'like', "%{$search}%")
                        ->orWhere('no_barang', 'like', "%{$search}%")
                        ->orWhere('nama_pelanggan', 'like', "%{$search}%");
                });
            }

            $local = $query->orderBy('tgl_so', 'desc')->paginate($perPage, ['*'], 'page', $page);
            if ($local->total() > 0) {
                return response()->json($local);
            }
        } catch (\Throwable $exception) {
            Log::warning('Local SO lookup failed', ['message' => $exception->getMessage()]);
        }

        $params = array_filter([
            'page' => $page,
            'limit' => $perPage,
            'search' => $search,
            'date_from' => $request->input('date_from'),
            'date_to' => $request->input('date_to'),
            'status' => $request->input('status'),
            'status_hold' => $request->input('status_hold'),
            'sort_col' => $request->input('sort_col'),
            'sort_dir' => $request->input('sort_dir'),
        ], static fn ($value) => $value !== null && $value !== '');

        return $this->proxyPaginated('penjualan-so', $params, $page, $perPage);
    }

    public function detailSo(string $noSo): JsonResponse
    {
        $local = SalesOrderAkurasi::whereRaw('LOWER(no_so) = ?', [strtolower(trim($noSo))])->get();
        if ($local->isNotEmpty()) {
            $first = $local->first();
            return response()->json([
                'no_so' => $first->no_so,
                'tgl_so' => $first->tgl_so,
                'est_kirim' => $first->tgl_estimasi,
                'pelanggan' => $first->nama_pelanggan,
                'shipto' => $first->shipto,
                'status' => $first->status,
                'total_amount' => $local->sum('subtotal'),
                'items' => $local,
            ]);
        }

        $response = $this->requestApi('penjualan-so/' . rawurlencode(trim($noSo)));
        if ($response instanceof JsonResponse) {
            return $response;
        }

        $items = $this->extractItems($response)
            ->filter(fn ($item) => $this->matches($item, $noSo, ['no_so']))
            ->values();
            
        // Jika detail endpoint tidak membuahkan hasil karena filter,
        // fallback pakai flat items jika ada.
        if ($items->isEmpty()) {
            $data = $response->json('data') ?? $response->json();
            if (is_array($data) && isset($data['no_so'])) {
                $items = collect([$data]);
            }
        }
            
        return $items->isEmpty() ? $this->notFound('SO', $noSo) : response()->json($this->formatSoDetail($items));
    }

    public function searchPo(Request $request): JsonResponse
    {
        $search = (string) $request->input('q', '');
        $page = max((int) $request->input('page', 1), 1);

        try {
            $query = PembelianOrderAkurasi::query();
            if ($search !== '') {
                $query->where(function ($query) use ($search) {
                    $query->where('no_pembelian', 'like', "%{$search}%")
                        ->orWhere('deskripsi_barang', 'like', "%{$search}%")
                        ->orWhere('no_barang', 'like', "%{$search}%")
                        ->orWhere('nama_pemasok', 'like', "%{$search}%");
                });
            }

            $local = $query->orderBy('tgl_pembelian', 'desc')->paginate(20, ['*'], 'page', $page);
            if ($local->total() > 0) {
                return response()->json($local);
            }
        } catch (\Throwable $exception) {
            Log::warning('Local PO lookup failed', ['message' => $exception->getMessage()]);
        }

        return $this->proxyPaginated('pembelian', [
            'page' => $page,
            'limit' => 20,
            'search' => $search,
        ], $page, 20);
    }

    public function detailPo(string $noPo): JsonResponse
    {
        $normalized = strtolower(trim($noPo));

        // 1. Cari di DB lokal (exact match)
        $local = PembelianOrderAkurasi::whereRaw('LOWER(no_pembelian) = ?', [$normalized])->get();
        if ($local->isNotEmpty()) {
            $first = $local->first();
            return response()->json([
                'no_po' => $first->no_pembelian,
                'tgl_po' => $first->tgl_pembelian,
                'est_kirim' => $first->tgl_ekspetasi,
                'pemasok' => $first->nama_pemasok,
                'shipto' => $first->so_no,
                'status' => $first->status_bayar,
                'total_amount' => $local->sum('amount'),
                'items' => $local,
            ]);
        }

        // 2. Langsung ke detail endpoint (exact match by no_po)
        $detailResponse = $this->requestApi('pembelian/' . rawurlencode(trim($noPo)));
        if ($detailResponse instanceof JsonResponse) {
            return $detailResponse;
        }

        $items = $this->extractItems($detailResponse)
            ->filter(fn ($item) => $this->matches($item, $noPo, ['no_pembelian', 'no_po']))
            ->values();
            
        if ($items->isEmpty()) {
            $data = $detailResponse->json('data') ?? $detailResponse->json();
            if (is_array($data) && (isset($data['no_pembelian']) || isset($data['no_po']))) {
                $items = collect([$data]);
            }
        }
            
        return $items->isEmpty() ? $this->notFound('PO', $noPo) : response()->json($this->formatPoDetail($items));
    }

    private function proxyPaginated(string $endpoint, array $params, int $page, int $perPage): JsonResponse
    {
        $response = $this->requestApi($endpoint, $params);
        if ($response instanceof JsonResponse) {
            return $response;
        }

        $meta = $response->json('meta', []);
        $data = $response->json('data', []);
        return response()->json([
            'data' => $data,
            'current_page' => $meta['current_page'] ?? $page,
            'last_page' => $meta['total_pages'] ?? 1,
            'total' => $meta['total_rows'] ?? count($data),
            'from' => count($data) ? (($page - 1) * $perPage) + 1 : 0,
            'to' => count($data) ? (($page - 1) * $perPage) + count($data) : 0,
        ]);
    }

    private function requestApi(string $endpoint, array $params = []): mixed
    {
        try {
            $response = Http::withHeaders([
                'X-API-Key' => config('services.akurasi.api_key'),
                'Accept' => 'application/json',
            ])->withOptions(['verify' => config('services.akurasi.verify_ssl', true)])
                ->timeout((int) config('services.akurasi.timeout', 30))
                ->get(rtrim(config('services.akurasi.url'), '/') . '/' . $endpoint, $params);

            if (!$response->successful()) {
                Log::error('Akurasi API returned an error', ['endpoint' => $endpoint, 'status' => $response->status(), 'body' => $response->body()]);
                
                $status = $response->status();
                if ($status >= 400 && $status < 500) {
                    return response()->json(['error' => 'Data tidak ditemukan di API Akurasi', 'upstream_status' => $status], 404);
                }
                
                return response()->json(['error' => 'Akurasi API mengembalikan error', 'upstream_status' => $status], 502);
            }

            return $response;
        } catch (ConnectionException|RequestException $exception) {
            Log::error('Akurasi API request failed', ['endpoint' => $endpoint, 'message' => $exception->getMessage()]);
            return response()->json(['error' => 'Akurasi API tidak dapat dihubungi', 'message' => $exception->getMessage()], 502);
        }
    }

    private function extractItems($response)
    {
        $payload = $response->json();
        $data = $payload['data'] ?? $payload;

        if (is_array($data) && array_is_list($data)) {
            return collect($data);
        }

        if (is_array($data)) {
            return collect([$data]);
        }

        return collect();
    }

    private function matches(array $item, string $value, array $keys): bool
    {
        $value = strtolower(trim($value));
        foreach ($keys as $key) {
            if (strtolower(trim((string) ($item[$key] ?? ''))) === $value) {
                return true;
            }
        }
        return false;
    }

    private function formatSoDetail($items): array
    {
        $first = $items->first();
        return ['no_so' => $first['no_so'] ?? '-', 'tgl_so' => $first['tgl_so'] ?? null, 'est_kirim' => $first['tgl_estimasi'] ?? null, 'pelanggan' => $first['nama_pelanggan'] ?? null, 'shipto' => $first['shipto'] ?? null, 'status' => $first['status'] ?? null, 'total_amount' => $items->sum('subtotal'), 'items' => $items];
    }

    private function formatPoDetail($items): array
    {
        $first = $items->first();
        return ['no_po' => $first['no_pembelian'] ?? $first['no_po'] ?? '-', 'tgl_po' => $first['tgl_pembelian'] ?? null, 'est_kirim' => $first['tgl_ekspetasi'] ?? null, 'pemasok' => $first['nama_pemasok'] ?? null, 'shipto' => $first['so_no'] ?? null, 'status' => $first['status_pembayaran'] ?? $first['status_bayar'] ?? null, 'total_amount' => $items->sum('amount'), 'items' => $items];
    }

    private function notFound(string $type, string $number): JsonResponse
    {
        return response()->json(['error' => "{$type} {$number} tidak ditemukan pada DB lokal maupun Akurasi API"], 404);
    }
}
