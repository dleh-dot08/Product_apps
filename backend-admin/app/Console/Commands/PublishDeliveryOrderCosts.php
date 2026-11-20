<?php

namespace App\Console\Commands;

use App\Models\DeliveryAssignment;
use App\Services\DeliveryOrderOutboxService;
use Illuminate\Console\Command;
use Throwable;

class PublishDeliveryOrderCosts extends Command
{
    protected $signature = 'delivery-orders:publish-costs
        {--id= : Delivery Assignment ID}
        {--no-dispatch : Create outbox records without dispatching jobs}';

    protected $description = 'Queue Delivery Order cost snapshots for NATS publishing.';

    public function handle(DeliveryOrderOutboxService $outbox): int
    {
        $query = DeliveryAssignment::query()->with('salesOrder.items');
        if ($this->option('id')) {
            $query->whereKey($this->option('id'));
        }

        $processed = 0;
        $failed = 0;
        $query->chunkById(100, function ($deliveries) use ($outbox, &$processed, &$failed) {
            foreach ($deliveries as $delivery) {
                try {
                    $outbox->enqueue($delivery, ! $this->option('no-dispatch'));
                    $processed++;
                } catch (Throwable $exception) {
                    $failed++;
                    $this->error("{$delivery->id}: {$exception->getMessage()}");
                }
            }
        });

        $this->info("Queued: {$processed}; failed: {$failed}.");

        return $failed > 0 ? self::FAILURE : self::SUCCESS;
    }
}
