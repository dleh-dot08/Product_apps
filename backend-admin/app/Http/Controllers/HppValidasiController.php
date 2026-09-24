<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Vehicle;
use App\Models\User;

class HppValidasiController extends Controller
{
    public function index(Request $request)
    {
        // Validasi Konsumsi BBM: Ambil data kendaraan (Vehicle)
        // Menampilkan Plat, Nama Mobil, Konsumsi BBM (km_per_liter) dan Harga (fuel_price_per_liter)
        $bbmValidations = Vehicle::orderBy('id', 'asc')
            ->paginate(10, ['*'], 'bbm_page');

        // Validasi Rate Manpower: Ambil data langsung dari tabel validasi_mp_deliverypickup
        $manpowerValidations = \App\Models\ValidasiMpDeliveryPickup::paginate(10, ['*'], 'manpower_page');

        return view('hpp.data-validasi.index', compact('bbmValidations', 'manpowerValidations'));
    }

    public function updateBbm(Request $request, $id)
    {
        $request->validate([
            'km_per_liter' => 'required|numeric|min:0',
            'fuel_price_per_liter' => 'required|numeric|min:0',
        ]);

        $vehicle = Vehicle::findOrFail($id);
        $vehicle->update([
            'km_per_liter' => $request->km_per_liter,
            'fuel_price_per_liter' => $request->fuel_price_per_liter,
        ]);

        return redirect()->route('hpp.validasi')->with('success', 'Data BBM Kendaraan berhasil diperbarui!');
    }

    public function updateManpower(Request $request, $id)
    {
        $request->validate([
            'rate_per_hour' => 'required|numeric|min:0',
            'rate_per_minute' => 'required|numeric|min:0',
            'rate_per_second' => 'required|numeric|min:0',
        ]);

        $validasi = \App\Models\ValidasiMpDeliveryPickup::findOrFail($id);
        $validasi->update([
            'rate_per_hour' => $request->rate_per_hour,
            'rate_per_minute' => $request->rate_per_minute,
            'rate_per_second' => $request->rate_per_second,
            'status_validasi' => 'Valid Sesuai'
        ]);

        return redirect()->route('hpp.validasi')->with('success', 'Data Rate Manpower berhasil diperbarui!');
    }
}
