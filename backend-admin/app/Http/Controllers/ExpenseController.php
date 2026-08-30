<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Shift;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $query = Expense::with(['shift', 'driver'])->latest('occurred_at');

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }
        if ($request->filled('driver_id')) {
            $query->where('driver_id', $request->driver_id);
        }

        $expenses = $query->get();
        $drivers = User::where('role', 'driver')->get();
        // Hanya ambil shift yang belum selesai (atau beberapa hari terakhir) untuk kemudahan entry
        $activeShifts = Shift::with(['driver', 'vehicle'])->latest()->limit(50)->get();

        $totalAmount = $expenses->sum('amount');
        $fuelTotal = $expenses->where('category', 'fuel')->sum('amount');
        $tollTotal = $expenses->where('category', 'toll')->sum('amount');

        return view('expenses.index', compact(
            'expenses', 
            'drivers', 
            'activeShifts', 
            'totalAmount', 
            'fuelTotal', 
            'tollTotal'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'shift_id' => 'required|uuid|exists:shifts,id',
            'category' => 'required|string|in:fuel,toll,parking,meal,other',
            'amount' => 'required|numeric|min:1',
            'description' => 'nullable|string',
            'receipt' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'occurred_at' => 'nullable|date',
        ]);

        $shift = Shift::findOrFail($request->shift_id);

        $expenseData = [
            'shift_id' => $shift->id,
            'driver_id' => $shift->driver_id,
            'category' => $request->category,
            'amount' => $request->amount,
            'description' => $request->description,
            'occurred_at' => $request->occurred_at ?? now(),
        ];

        if ($request->hasFile('receipt')) {
            $path = $request->file('receipt')->store('public/receipts');
            $expenseData['receipt_url'] = Storage::url($path);
        }

        Expense::create($expenseData);

        return redirect()->route('expenses.index')->with('success', 'Pengeluaran berhasil dicatat.');
    }

    public function destroy(Expense $expense)
    {
        if ($expense->receipt_url) {
            // Hapus dari storage jika ada
            $path = str_replace('/storage/', 'public/', $expense->receipt_url);
            Storage::delete($path);
        }

        $expense->delete();

        return redirect()->route('expenses.index')->with('success', 'Pengeluaran berhasil dihapus.');
    }
}
