<?php

namespace App\Http\Controllers;

use App\Services\AkurasiService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Throwable;

class SalesOrderController extends Controller
{
    protected AkurasiService $akurasiService;

    public function __construct(AkurasiService $akurasiService)
    {
        $this->akurasiService = $akurasiService;
    }

    /**
     * Halaman Sales Order
     */
    public function index(): View
    {
        return view('data-akurasi.so.index');
    }

    /**
     * Search / Load Sales Order
     *
     * Dipanggil oleh AJAX dari sales-orders/index.blade.php
     */
    public function search(Request $request): JsonResponse
    {
        try {
            $filters = [
                'page' => max(
                    1,
                    (int) $request->input('page', 1)
                ),

                'per_page' => min(
                    100,
                    max(1, (int) $request->input('per_page', 20))
                ),

                // Blade mengirim parameter "q"
                // Service memakai parameter "search"
                'search' => $request->input('q'),

                'date_from' => $request->input('date_from'),
                'date_to' => $request->input('date_to'),

                'status' => $request->input('status'),

                'status_hold' => $request->input('status_hold'),

                'sort_col' => $request->input(
                    'sort_col',
                    'tgl_so'
                ),

                'sort_dir' => $this->normalizeSortDirection(
                    $request->input('sort_dir', 'desc')
                ),
            ];

            $result = $this->akurasiService->searchSo($filters);

            return response()->json($result);

        } catch (Throwable $e) {
            report($e);

            return response()->json([
                'error' => true,
                'message' => $e->getMessage(),

                'data' => [],
                'current_page' => 1,
                'last_page' => 1,
                'total' => 0,
                'from' => 0,
                'to' => 0,
            ], 500);
        }
    }

    /**
     * Detail Sales Order
     */
    public function detail(string $noSo): JsonResponse
    {
        try {
            $noSo = urldecode($noSo);

            $result = $this->akurasiService->detailSo($noSo);

            return response()->json($result);

        } catch (Throwable $e) {
            report($e);

            return response()->json([
                'error' => $e->getMessage(),
            ], 404);
        }
    }

    /**
     * Endpoint untuk memenuhi trigger background
     * pada Blade Sales Order.
     *
     * Saat ini data menggunakan API Akurasi secara realtime,
     * sehingga tidak diperlukan proses sync database lokal.
     */
    public function triggerSync(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Data Sales Order menggunakan API Akurasi realtime.',
        ]);
    }

    /**
     * Pastikan direction hanya asc / desc
     */
    protected function normalizeSortDirection(?string $direction): string
    {
        return strtolower((string) $direction) === 'asc'
            ? 'asc'
            : 'desc';
    }
}
