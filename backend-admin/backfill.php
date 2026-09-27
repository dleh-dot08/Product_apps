<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

DB::table('pickup_tasks')->whereNotNull('manifest_id')->get()->each(function($t) {
    DB::table('tasks_manifest_history')->updateOrInsert(
        ['manifest_id' => $t->manifest_id, 'task_id' => $t->id, 'task_type' => 'pickup'],
        ['status' => $t->status, 'created_at' => now(), 'updated_at' => now()]
    );
});

DB::table('delivery_assignments')->whereNotNull('manifest_id')->get()->each(function($t) {
    DB::table('tasks_manifest_history')->updateOrInsert(
        ['manifest_id' => $t->manifest_id, 'task_id' => $t->id, 'task_type' => 'delivery'],
        ['status' => $t->status, 'created_at' => now(), 'updated_at' => now()]
    );
});

echo "done";
