<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Shift;
use App\Services\HppCalculationService;

class TripHppController extends Controller
{
    protected $hppService;

    public function __construct(HppCalculationService $hppService)
    {
        $this->hppService = $hppService;
    }

    public function index(Request $request)
    {
        // 1. Load Shifts (Armada Internal)
        $allShifts = Shift::with(['vehicle', 'driver', 'pickupTasks.items', 'deliveryAssignments.salesOrder.items', 'expenses'])
            ->orderBy('work_date', 'desc')
            ->get();

        $totalTrips = $allShifts->count();
        
        $totalCost = 0;
        $totalItems = 0;
        $totalJarak = 0;
        $totalDurasi = 0;
        $costComposition = [
            'BBM' => 0,
            'Manpower' => 0,
            'Tol' => 0,
            'Parkir' => 0,
            'Lainnya' => 0,
            'Depresiasi' => 0,
            'Ekspedisi' => 0,
        ];

        // Process each shift and attach calculated properties
        foreach ($allShifts as $shift) {
            $shift->is_expedition = false;
            $calc = $this->hppService->getSavedOrCalculateProrata($shift);
            $shift->calc_details = $calc;
            $shift->total_cost = $calc['costs']['total'];
            $shift->sort_date = $shift->work_date ? $shift->work_date->timestamp : 0;

            $totalCost += $calc['costs']['total'];
            $totalItems += $shift->pickupTasks->sum('quantity') + 
                           $shift->deliveryAssignments->sum(function($d) { return $d->salesOrder->ordered_quantity ?? 0; });

            if ($shift->start_odometer && $shift->end_odometer) {
                $totalJarak += max(0, $shift->end_odometer - $shift->start_odometer);
            }
            if ($shift->check_in_at && $shift->check_out_at) {
                $totalDurasi += $shift->check_in_at->diffInMinutes($shift->check_out_at);
            }

            $costComposition['BBM'] += $calc['costs']['fuel'];
            $costComposition['Manpower'] += $calc['costs']['manpower'];
            $costComposition['Tol'] += $calc['costs']['toll'];
            $costComposition['Parkir'] += $calc['costs']['parking'];
            $costComposition['Lainnya'] += $calc['costs']['other'];
            $costComposition['Depresiasi'] += $calc['costs']['depreciation'] ?? 0;
        }

        // 2. Load Expeditions (Vendor 3PL / Kargo)
        $allExpeditions = \App\Models\Expedition::with(['items', 'vendor'])
            ->orderBy('shipping_date', 'desc')
            ->get();

        $totalExpeditions = $allExpeditions->count();
        $totalExpeditionCost = 0;
        $totalExpeditionItems = 0;

        foreach ($allExpeditions as $expedition) {
            $expedition->is_expedition = true;
            $calc = $this->hppService->calculateExpeditionProrata($expedition);
            $expedition->calc_details = $calc;
            $expCost = (float) ($expedition->cost_amount ?? 0);
            $expedition->total_cost = $expCost;
            $expedition->sort_date = $expedition->shipping_date 
                ? $expedition->shipping_date->timestamp 
                : ($expedition->created_at ? $expedition->created_at->timestamp : 0);

            $totalExpeditionCost += $expCost;
            $itemQtySum = $expedition->items->sum('quantity');
            $totalExpeditionItems += ($itemQtySum > 0 ? $itemQtySum : ($expedition->total_colli ?: 1));
        }

        $costComposition['Ekspedisi'] = $totalExpeditionCost;

        // Akumulasi Keseluruhan HPP Logistik
        $grandTotalCost = $totalCost + $totalExpeditionCost;
        $grandTotalItems = $totalItems + $totalExpeditionItems;
        $avgHppPerItem = $grandTotalItems > 0 ? $grandTotalCost / $grandTotalItems : 0;

        // Gabungkan seluruh logistik (Armada Internal + Vendor Ekspedisi) diurutkan berdasarkan tanggal terbaru
        $combinedLogistics = $allShifts->concat($allExpeditions)->sortByDesc('sort_date')->values();

        // Paginate the combined collection manually for the table
        $perPage = $request->get('per_page', 10);
        $page = \Illuminate\Pagination\Paginator::resolveCurrentPage() ?: 1;
        
        $shifts = new \Illuminate\Pagination\LengthAwarePaginator(
            $combinedLogistics->forPage($page, $perPage),
            $combinedLogistics->count(),
            $perPage,
            $page,
            ['path' => \Illuminate\Pagination\Paginator::resolveCurrentPath(), 'query' => $request->query()]
        );

        return view('hpp.index', compact(
            'shifts',
            'totalTrips',
            'totalCost',
            'totalExpeditions',
            'totalExpeditionCost',
            'grandTotalCost',
            'grandTotalItems',
            'avgHppPerItem',
            'costComposition',
            'totalJarak',
            'totalDurasi'
        ));
    }

    public function show($id)
    {
        $shift = Shift::with(['vehicle', 'driver', 'pickupTasks.items', 'deliveryAssignments.salesOrder.items', 'expenses'])->findOrFail($id);
        
        $prorataDetails = $this->hppService->getSavedOrCalculateProrata($shift);

        return view('hpp.show', compact('shift', 'prorataDetails'));
    }

    public function export()
    {
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\HppRitaseExport, 'Laporan_HPP_Ritase_' . date('Ymd_His') . '.xlsx');
    }

    public function destroy($id)
    {
        // Hanya Super Admin yang boleh delete
        if (!auth()->check() || !auth()->user()->roleRelation || strtoupper(auth()->user()->roleRelation->name) !== 'SUPER ADMIN') {
            return redirect()->back()->with('error', 'Anda tidak memiliki hak akses untuk menghapus data ini.');
        }

        $shift = Shift::findOrFail($id);
        $shift->delete();

        return redirect()->route('hpp.index')->with('success', 'Data HPP / Trip berhasil dihapus.');
    }

    public function recalculateAll()
    {
        $count = $this->hppService->recalculateAll();
        return redirect()->route('hpp.index')->with('success', "Berhasil menyinkronkan dan menghitung ulang HPP untuk {$count} data ritase!");
    }
}
