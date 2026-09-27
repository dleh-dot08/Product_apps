<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

// Backfill for expired pickup tasks
DB::table('task_histories')
    ->where('notes', 'like', 'Status otomatis diubah menjadi PENDING - melewati batas waktu dispatch%')
    ->where('task_type', 'pickup')
    ->whereNotNull('driver_id')
    ->get()
    ->each(function($history) {
        $task = DB::table('pickup_tasks')->where('id', $history->task_id)->first();
        if ($task && $task->dispatch_date) {
            $manifest = DB::table('task_manifests')
                ->where('driver_id', $history->driver_id)
                ->whereDate('dispatch_date', $task->dispatch_date)
                ->first();
            
            if ($manifest) {
                DB::table('tasks_manifest_history')->updateOrInsert(
                    ['manifest_id' => $manifest->id, 'task_id' => $task->id, 'task_type' => 'pickup'],
                    ['status' => 'pending', 'created_at' => $history->created_at, 'updated_at' => $history->created_at]
                );
            }
        }
    });

// Backfill for expired delivery tasks
DB::table('task_histories')
    ->where('notes', 'like', 'Status otomatis diubah menjadi PENDING - melewati batas waktu dispatch%')
    ->where('task_type', 'delivery')
    ->whereNotNull('driver_id')
    ->get()
    ->each(function($history) {
        $task = DB::table('delivery_assignments')->where('id', $history->task_id)->first();
        if ($task && $task->dispatch_date) {
            $manifest = DB::table('task_manifests')
                ->where('driver_id', $history->driver_id)
                ->whereDate('dispatch_date', $task->dispatch_date)
                ->first();
            
            if ($manifest) {
                DB::table('tasks_manifest_history')->updateOrInsert(
                    ['manifest_id' => $manifest->id, 'task_id' => $task->id, 'task_type' => 'delivery'],
                    ['status' => 'pending', 'created_at' => $history->created_at, 'updated_at' => $history->created_at]
                );
            }
        }
    });

echo "done expired backfill";
