<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Shift;
use App\Models\Expense;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DriverShiftController extends Controller
{
    /**
     * Start a new shift (Absen Pagi)
     */
    public function startShift(Request $request)
    {
        $request->validate([
            'vehicle_id' => 'nullable|exists:vehicles,id',
            'start_odometer' => 'required|integer',
            'start_fuel' => 'required|string',
            'photos' => 'nullable|array|max:10',
            'photos.*' => 'image|max:5120', // Max 5MB per photo
            'vehicle_condition' => 'nullable|string', // Kondisi mobil
            'fuel_price_per_liter' => 'nullable|numeric'
        ]);

        $user = $request->user();

        // Cek apakah driver sudah memiliki shift aktif (belum checkout)
        $activeShift = Shift::where('driver_id', $user->id)
            ->whereNull('check_out_at')
            ->first();

        if ($activeShift) {
            return response()->json([
                'status' => 'error',
                'message' => 'Anda masih memiliki shift yang belum diakhiri.'
            ], 400);
        }

        // Tentukan folder: shift/[tanggal]/[nama-user]/keberangkatan
        $dateStr = now()->format('Y-m-d');
        // username bisa mengandung spasi, kita ganti dengan strip atau sanitize
        $userName = str_replace(' ', '-', $user->name ?? $user->id);
        $folderPath = "shift/{$dateStr}/{$userName}/keberangkatan";

        // Upload foto bukti (kondisi/odo)
        $primaryPhotoPath = null;
        $photoPaths = [];
        
        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $index => $file) {
                $filename = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs($folderPath, $filename, 'minio');
                $photoPaths[] = [
                    'path' => $path,
                    'name' => $filename
                ];
                
                if ($index === 0) {
                    $primaryPhotoPath = $path; // Ambil foto pertama sebagai foto utama shift
                }
            }
        } else if ($request->hasFile('start_evidence_photo')) { // Fallback for old app version
            $file = $request->file('start_evidence_photo');
            $filename = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
            $primaryPhotoPath = $file->storeAs($folderPath, $filename, 'minio');
            $photoPaths[] = [
                'path' => $primaryPhotoPath,
                'name' => $filename
            ];
        }

        $vehicleId = $request->vehicle_id;

        if (!$vehicleId) {
            $manifest = \Illuminate\Support\Facades\DB::table('task_manifests')
                ->where(function($query) use ($user) {
                    $query->where('driver_id', $user->id)
                          ->orWhere('co_driver_id', $user->id);
                })
                ->where(function($q) {
                    $q->whereDate('dispatch_date', now()->toDateString())
                      ->orWhere('is_out_of_city', true);
                })
                ->whereNotNull('vehicle_id')
                ->latest('created_at')
                ->first();
            
            if ($manifest) {
                $vehicleId = $manifest->vehicle_id;
            }
        }

        $vehicle = $vehicleId ? \App\Models\Vehicle::find($vehicleId) : null;
        $rateHour = $user->manpower_rate_per_hour ?? 0;

        $shift = Shift::create([
            'driver_id' => $user->id,
            'vehicle_id' => $vehicleId,
            'work_date' => now()->toDateString(),
            'check_in_at' => now(),
            'start_odometer' => $request->start_odometer ?? ($vehicle ? $vehicle->odometer : 0),
            'start_fuel' => $request->start_fuel,
            'start_evidence_photo' => $primaryPhotoPath, // Kembali menyimpan path string tunggal
            'fuel_price_per_liter' => $vehicle ? $vehicle->fuel_price_per_liter : ($request->fuel_price_per_liter ?? 0),
            'km_per_liter' => $vehicle ? $vehicle->km_per_liter : 0,
            'manpower_rate_per_hour' => $rateHour,
            'manpower_rate_per_minute' => $rateHour / 60,
            'manpower_rate_per_second' => $rateHour / 3600,
            'manpower_count' => $user->manpower_count ?? 1,
            'notes' => $request->vehicle_condition,
            'source' => 'attendance'
        ]);

        // Simpan semua foto ke dalam task_attachments
        if (count($photoPaths) > 0) {
            \App\Models\TaskAttachment::withoutEvents(function () use ($photoPaths, $shift, $user) {
                foreach ($photoPaths as $photo) {
                    \App\Models\TaskAttachment::create([
                        'shift_id' => $shift->id, // Menggunakan shift_id (task_id null)
                        'task_type' => 'shift',
                        'document_type' => 'Kondisi Kendaraan (Awal)',
                        'category' => 'Kondisi Kendaraan (Awal)',
                        'file_name' => $photo['name'],
                        'file_path' => $photo['path'],
                        'notes' => 'Foto saat mulai shift',
                        'uploaded_by' => $user->id ?? null,
                    ]);
                }
            });
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Shift berhasil dimulai.',
            'data' => $shift
        ]);
    }

    /**
     * End current shift and submit expenses
     */
    public function endShift(Request $request)
    {
        $request->validate([
            'end_odometer' => 'required|integer',
            'end_evidence_photo' => 'nullable|image|max:5120',
            'expenses' => 'nullable|string', // JSON string of expenses if any
        ]);

        $user = $request->user();

        $activeShift = Shift::where('driver_id', $user->id)
            ->whereNull('check_out_at')
            ->first();

        if (!$activeShift) {
            return response()->json([
                'status' => 'error',
                'message' => 'Tidak ada shift aktif yang ditemukan.'
            ], 404);
        }

        // Upload foto bukti akhir
        $photoPath = null;
        $photoPaths = [];
        $dateStr = now()->format('Y-m-d');
        $userName = str_replace(' ', '-', $user->name ?? $user->id);
        $folderPath = "shift/{$dateStr}/{$userName}/kepulangan";

        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $index => $file) {
                $filename = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs($folderPath, $filename, 'minio');
                $photoPaths[] = [
                    'path' => $path,
                    'name' => $filename
                ];
                
                if ($index === 0) {
                    $photoPath = $path; // Ambil foto pertama sebagai foto utama shift akhir
                }
            }
        } else if ($request->hasFile('end_evidence_photo')) { // Fallback
            $file = $request->file('end_evidence_photo');
            $filename = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
            $photoPath = $file->storeAs($folderPath, $filename, 'minio');
            $photoPaths[] = [
                'path' => $photoPath,
                'name' => $filename
            ];
        }

        // Simpan semua foto akhir ke dalam task_attachments
        if (count($photoPaths) > 0) {
            \App\Models\TaskAttachment::withoutEvents(function () use ($photoPaths, $activeShift, $user) {
                foreach ($photoPaths as $photo) {
                    \App\Models\TaskAttachment::create([
                        'shift_id' => $activeShift->id,
                        'task_type' => 'shift',
                        'document_type' => 'Kondisi Kendaraan (Akhir)',
                        'category' => 'Kondisi Kendaraan (Akhir)',
                        'file_name' => $photo['name'],
                        'file_path' => $photo['path'],
                        'notes' => 'Foto saat akhir shift',
                        'uploaded_by' => $user->id ?? null,
                    ]);
                }
            });
        }

        // Handle expenses
        if ($request->has('expenses')) {
            $expensesData = json_decode($request->expenses, true);
            if (is_array($expensesData)) {
                foreach ($expensesData as $exp) {
                    Expense::create([
                        'shift_id' => $activeShift->id,
                        'driver_id' => $user->id,
                        'category' => $exp['category'] ?? 'Lain-lain',
                        'amount' => $exp['amount'] ?? 0,
                        'notes' => $exp['notes'] ?? null,
                        'occurred_at' => now(),
                    ]);
                }
            }
        }

        $activeShift->update([
            'end_odometer' => $request->end_odometer,
            'end_evidence_photo' => $photoPath,
            'check_out_at' => now()
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Shift berhasil diakhiri.',
            'data' => $activeShift
        ]);
    }

    /**
     * Get recent shifts for the driver (up to 3 days ago).
     */
    public function recentShifts(Request $request)
    {
        $user = $request->user();
        
        $dates = collect();
        for ($i = 0; $i <= 3; $i++) {
            $dates->push(now()->subDays($i)->format('Y-m-d'));
        }

        $results = collect();

        foreach ($dates as $date) {
            $shifts = Shift::with(['vehicle:id,plate_number,name', 'pickupTasks', 'deliveryAssignments'])
                ->where('driver_id', $user->id)
                ->where('work_date', $date)
                ->orderBy('created_at', 'asc')
                ->get();

            // Check Manifests
            $dailyManifests = \Illuminate\Support\Facades\DB::table('task_manifests')
                ->where(function($q) use ($user) {
                    $q->where('driver_id', $user->id)
                      ->orWhere('co_driver_id', $user->id);
                })
                ->whereDate('dispatch_date', $date)
                ->select('id', 'manifest_number')
                ->get();

            if ($shifts->isEmpty()) {
                if ($dailyManifests->isNotEmpty()) {
                    $manifestText = $dailyManifests->pluck('manifest_number')->unique()->implode(', ');
                    $results->push([
                        'id' => 'date:'.$date,
                        'work_date' => $date,
                        'status' => 'completed',
                        'check_in_at' => null,
                        'check_out_at' => null,
                        'vehicle_plate_number' => null,
                        'vehicle_name' => null,
                        'manifests' => empty($manifestText) ? "Tidak ada manifest/DO" : $manifestText,
                        'manifest_list' => $dailyManifests->toArray(),
                    ]);
                }
            } else {
                foreach ($shifts as $shift) {
                    $manifestText = $dailyManifests->pluck('manifest_number')->unique()->implode(', ');
                    
                    $results->push([
                        'id' => $shift->id,
                        'work_date' => $date,
                        'status' => $shift->check_out_at ? 'completed' : 'active',
                        'check_in_at' => $shift->check_in_at,
                        'check_out_at' => $shift->check_out_at,
                        'vehicle_plate_number' => $shift->vehicle->plate_number ?? null,
                        'vehicle_name' => $shift->vehicle->name ?? null,
                        'manifests' => empty($manifestText) ? "Tidak ada manifest/DO" : $manifestText,
                        'manifest_list' => $dailyManifests->toArray(),
                    ]);
                }
            }
        }

        return response()->json([
            'status' => 'success',
            'data' => $results->values()
        ]);
    }
}
