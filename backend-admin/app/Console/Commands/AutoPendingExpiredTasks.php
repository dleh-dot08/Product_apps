<?php

namespace App\Console\Commands;

use App\Models\PickupTask;
use App\Models\DeliveryAssignment;
use App\Models\TaskHistory;
use Illuminate\Console\Command;
use Carbon\Carbon;

class AutoPendingExpiredTasks extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'tasks:auto-pending';

    /**
     * The console command description.
     */
    protected $description = 'Otomatis ubah status tugas menjadi pending jika sudah melewati dispatch_date pukul 23:00 dan belum selesai. Tugas luar kota dikecualikan jika masih dalam estimasi.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $now = Carbon::now();
        $affected = 0;

        // ===== PICKUP TASKS =====
        $pickupQuery = PickupTask::whereIn('status', ['assigned', 'on_route'])
            ->whereNotNull('dispatch_date');

        $pickups = $pickupQuery->get();

        foreach ($pickups as $task) {
            $deadline = Carbon::parse($task->dispatch_date)->setTime(23, 0, 0);

            if ($now->greaterThan($deadline)) {
                // Pengecualian: Luar kota dengan estimasi sampai yang belum lewat
                if ($task->is_out_of_city && $task->estimated_arrival) {
                    $eta = Carbon::parse($task->estimated_arrival);
                    if ($now->lessThanOrEqualTo($eta)) {
                        // Masih dalam estimasi, skip
                        continue;
                    }
                }

                $task->update(['status' => 'pending']);

                // Log ke history
                TaskHistory::create([
                    'task_id' => $task->id,
                    'task_type' => 'auto_pending',
                    'driver_id' => $task->driver_id,
                    'status' => 'pending',
                    'notes' => 'Status otomatis diubah menjadi PENDING - melewati batas waktu dispatch (' . $deadline->format('d/m/Y H:i') . ')' .
                               ($task->is_out_of_city ? ' [Luar Kota - estimasi terlewat]' : ''),
                    'recorded_by' => null, // system
                ]);

                $affected++;
            }
        }

        // ===== DELIVERY ASSIGNMENTS =====
        $deliveryQuery = DeliveryAssignment::whereIn('status', ['assigned', 'on_route'])
            ->whereNotNull('dispatch_date');

        $deliveries = $deliveryQuery->get();

        foreach ($deliveries as $task) {
            $deadline = Carbon::parse($task->dispatch_date)->setTime(23, 0, 0);

            if ($now->greaterThan($deadline)) {
                // Pengecualian: Luar kota dengan estimasi sampai yang belum lewat
                if ($task->is_out_of_city && $task->estimated_arrival) {
                    $eta = Carbon::parse($task->estimated_arrival);
                    if ($now->lessThanOrEqualTo($eta)) {
                        continue;
                    }
                }

                $task->update(['status' => 'pending']);

                TaskHistory::create([
                    'task_id' => $task->id,
                    'task_type' => 'auto_pending',
                    'driver_id' => $task->driver_id,
                    'status' => 'pending',
                    'notes' => 'Status otomatis diubah menjadi PENDING - melewati batas waktu dispatch (' . $deadline->format('d/m/Y H:i') . ')' .
                               ($task->is_out_of_city ? ' [Luar Kota - estimasi terlewat]' : ''),
                    'recorded_by' => null,
                ]);

                $affected++;
            }
        }

        $this->info("✅ Selesai: {$affected} tugas diubah ke status PENDING.");

        return Command::SUCCESS;
    }
}
