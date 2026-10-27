<?php

namespace App\Http\Controllers;

use App\Models\ExpeditionVendor;
use Illuminate\Http\Request;

class ExpeditionVendorController extends Controller
{
    /**
     * Display a listing of the vendors.
     */
    public function index(Request $request)
    {
        $query = ExpeditionVendor::withCount('expeditions');

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('vendor_code', 'ilike', "%{$search}%")
                  ->orWhere('vendor_name', 'ilike', "%{$search}%")
                  ->orWhere('contact_person', 'ilike', "%{$search}%")
                  ->orWhere('phone_number', 'ilike', "%{$search}%")
                  ->orWhere('email', 'ilike', "%{$search}%");
            });
        }

        if ($request->filled('vendor_type') && $request->vendor_type !== 'ALL') {
            $query->where('vendor_type', $request->vendor_type);
        }

        if ($request->filled('status') && $request->status !== 'ALL') {
            $query->where('is_active', $request->status === 'active');
        }

        $vendors = $query->orderBy('vendor_name')->paginate(15)->withQueryString();

        $stats = [
            'total_vendors' => ExpeditionVendor::count(),
            'active_vendors' => ExpeditionVendor::where('is_active', true)->count(),
            'total_cargo' => ExpeditionVendor::where('vendor_type', 'cargo')->count(),
            'total_charter' => ExpeditionVendor::where('vendor_type', 'trucking_charter')->count(),
        ];

        return view('ekspedisi.vendors.index', compact('vendors', 'stats'));
    }

    /**
     * Store a newly created vendor.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'vendor_code' => 'required|string|max:50|unique:expedition_vendors,vendor_code',
            'vendor_name' => 'required|string|max:150',
            'vendor_type' => 'required|in:cargo,courier,trucking_charter,forwarder',
            'contact_person' => 'nullable|string|max:150',
            'phone_number' => 'nullable|string|max:50',
            'telephone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:100',
            'office_address' => 'nullable|string',
            'supported_services' => 'nullable|string', // comma-separated in modal
            'payment_terms' => 'required|in:cash_cod,tempo_14,tempo_30,monthly_invoice',
            'website_tracking_url' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        if (!empty($validated['supported_services'])) {
            $services = array_map('trim', explode(',', $validated['supported_services']));
            $validated['supported_services'] = array_values(array_filter($services));
        } else {
            $validated['supported_services'] = ['Cargo Darat'];
        }

        $validated['is_active'] = $request->has('is_active');

        ExpeditionVendor::create($validated);

        return redirect()->route('ekspedisi.vendors.index')->with('success', "Vendor {$validated['vendor_name']} berhasil ditambahkan.");
    }

    /**
     * Update the specified vendor.
     */
    public function update(Request $request, ExpeditionVendor $vendor)
    {
        $validated = $request->validate([
            'vendor_code' => 'required|string|max:50|unique:expedition_vendors,vendor_code,' . $vendor->id,
            'vendor_name' => 'required|string|max:150',
            'vendor_type' => 'required|in:cargo,courier,trucking_charter,forwarder',
            'contact_person' => 'nullable|string|max:150',
            'phone_number' => 'nullable|string|max:50',
            'telephone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:100',
            'office_address' => 'nullable|string',
            'supported_services' => 'nullable|string',
            'payment_terms' => 'required|in:cash_cod,tempo_14,tempo_30,monthly_invoice',
            'website_tracking_url' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        if (!empty($validated['supported_services'])) {
            $services = array_map('trim', explode(',', $validated['supported_services']));
            $validated['supported_services'] = array_values(array_filter($services));
        } else {
            $validated['supported_services'] = [];
        }

        $validated['is_active'] = $request->has('is_active');

        $vendor->update($validated);

        return redirect()->route('ekspedisi.vendors.index')->with('success', "Vendor {$vendor->vendor_name} berhasil diperbarui.");
    }

    /**
     * Remove the specified vendor.
     */
    public function destroy(ExpeditionVendor $vendor)
    {
        if ($vendor->expeditions()->count() > 0) {
            return redirect()->route('ekspedisi.vendors.index')->with('error', "Vendor {$vendor->vendor_name} tidak bisa dihapus karena memiliki riwayat transaksi pengiriman.");
        }

        $name = $vendor->vendor_name;
        $vendor->delete();

        return redirect()->route('ekspedisi.vendors.index')->with('success', "Vendor {$name} berhasil dihapus.");
    }
}
