<?php

namespace App\Http\Controllers;

use App\Services\AkurasiService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Throwable;

class PurchaseOrderController extends Controller
{
    protected AkurasiService $akurasiService;

    public function __construct(AkurasiService $akurasiService)
    {
        $this->akurasiService = $akurasiService;
    }

    /**
     * Halaman Purchase Order
     */
    public function index(): View
    {
        return view('data-akurasi.po.index');
    }

    /**
     * Search / Load Purchase Order
     *
     * Dipanggil oleh AJAX dari purchase-orders/index.blade.php
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

                // Blade mengirim "q"
                // AkurasiService menggunakan "search"
                'search' => $request->input('q'),

                'date_from' => $request->input('date_from'),
                'date_to' => $request->input('date_to'),

                'sort_col' => $request->input(
                    'sort_col',
                    'tgl_pembelian'
                ),

                'sort_dir' => $this->normalizeSortDirection(
                    $request->input('sort_dir', 'desc')
                ),
            ];

            $result = $this->akurasiService->searchPo($filters);

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
     * Detail Purchase Order
     */
    public function detail(string $noPo): JsonResponse
    {
        try {
            $noPo = urldecode($noPo);

            $result = $this->akurasiService->detailPo($noPo);

            return response()->json($result);

        } catch (Throwable $e) {
            report($e);

            return response()->json([
                'error' => $e->getMessage(),
            ], 404);
        }
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
