<?php

namespace App\Jobs;

use App\Models\DeliveryAssignment;
use App\Services\DeliveryOrderOutboxService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class QueueDeliveryOrderCost implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public function __construct(public string $deliveryOrderId) {}

    public function handle(DeliveryOrderOutboxService $outbox): void
    {
        $delivery = DeliveryAssignment::find($this->deliveryOrderId);
        if ($delivery) {
            $outbox->enqueue($delivery);
        }
    }
}
