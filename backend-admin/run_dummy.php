<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$shift = \App\Models\Shift::whereHas('pickupTasks')->first();
if ($shift) {
    $shift->start_odometer = 10000;
    $shift->end_odometer = 10250; // 250 KM
    $shift->km_per_liter = 5; // 50 Liters
    $shift->fuel_price_per_liter = 10000; // 500,000 Fuel
    $shift->check_in_at = now()->subHours(8); // 8 Hours
    $shift->check_out_at = now();
    $shift->manpower_rate_per_hour = 25000;
    $shift->manpower_count = 2; // 8 * 25k * 2 = 400,000 Manpower
    $shift->save();

    // Tambah Expenses
    \App\Models\Expense::updateOrCreate(
        ['shift_id' => $shift->id, 'category' => 'toll'],
        ['driver_id' => $shift->driver_id, 'amount' => 150000, 'description' => 'Tol Trans Jawa']
    );
    \App\Models\Expense::updateOrCreate(
        ['shift_id' => $shift->id, 'category' => 'parking'],
        ['driver_id' => $shift->driver_id, 'amount' => 50000, 'description' => 'Parkir Rest Area']
    );

    echo 'Shift ID: ' . $shift->id . ' updated successfully with dummy data!';
} else {
    echo 'No Shift with pickupTasks found!';
}
