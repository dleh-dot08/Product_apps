<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$driver = App\Models\User::where('role', 'driver')->first() ?? App\Models\User::first();
$vehicle = App\Models\Vehicle::first();

if (!$vehicle) {
    echo "NO VEHICLE IN DB";
    exit;
}

// Create Shift
$shift = App\Models\Shift::create([
    'driver_id' => $driver->id,
    'vehicle_id' => $vehicle->id,
    'work_date' => now()->toDateString(),
    'check_in_at' => now()->subHours(8),
    'check_out_at' => now(),
    'start_odometer' => 10000,
    'end_odometer' => 10150, 
    'fuel_price_per_liter' => 10000, 
    'km_per_liter' => 10, 
    'manpower_rate_per_hour' => 20000, 
    'manpower_count' => 1,
]);

// Create PickupTasks
App\Models\PickupTask::create([
    'shift_id' => $shift->id,
    'driver_id' => $driver->id,
    'vehicle_id' => $vehicle->id,
    'reference_number' => 'DEL-DUMMY-001',
    'item_description' => 'Besi Beton',
    'quantity' => 10,
    'unit_price' => 500000,
    'line_total' => 5000000, 
    'status' => 'delivered'
]);

App\Models\PickupTask::create([
    'shift_id' => $shift->id,
    'driver_id' => $driver->id,
    'vehicle_id' => $vehicle->id,
    'reference_number' => 'DEL-DUMMY-002',
    'item_description' => 'Semen Portland',
    'quantity' => 50,
    'unit_price' => 60000,
    'line_total' => 3000000, 
    'status' => 'delivered'
]);

// Create Expenses
App\Models\Expense::create([
    'shift_id' => $shift->id,
    'driver_id' => $driver->id,
    'category' => 'toll',
    'description' => 'Tol Dalam Kota',
    'amount' => 35000,
    'occurred_at' => now()->subHours(4),
]);

App\Models\Expense::create([
    'shift_id' => $shift->id,
    'driver_id' => $driver->id,
    'category' => 'parking',
    'description' => 'Parkir Gedung',
    'amount' => 15000,
    'occurred_at' => now()->subHours(2),
]);

App\Models\Expense::create([
    'shift_id' => $shift->id,
    'driver_id' => $driver->id,
    'category' => 'other',
    'description' => 'Uang Makan',
    'amount' => 50000,
    'occurred_at' => now()->subHours(5),
]);

echo "Dummy data created for Shift ID: " . $shift->id;
