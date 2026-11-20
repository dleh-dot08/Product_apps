<?php

namespace App\Services;

use App\Jobs\PublishNatsOutboxEvent;
use App\Models\DeliveryAssignment;
use App\Models\NatsOutboxEvent;
use Illuminate\Support\Str;
use RuntimeException;

class DeliveryOrderOutboxService
{
    public function __construct(private DeliveryOrderCostService $costService) {}

    public function enqueue(DeliveryAssignment $delivery, bool $dispatch = true): NatsOutboxEvent
    {
        $subject = trim((string) config('services.nats.production_subject'));
        if ($subject === '') {
            throw new RuntimeException('NATS_PRODUCTION_SUBJECT belum dikonfigurasi.');
        }

        $data = $this->costService->resolve($delivery);
        $eventId = (string) Str::uuid();
        $payload = [
            'event' => 'delivery_order.cost.updated',
            'event_id' => $eventId,
            'occurred_at' => now()->toIso8601String(),
            'source' => config('app.name', 'product-apps'),
            'version' => 1,
            'data' => $data,
        ];

        $event = NatsOutboxEvent::create([
            'event_id' => $eventId,
            'aggregate_type' => 'delivery_order',
            'aggregate_id' => (string) $delivery->id,
            'subject' => $subject,
            'payload' => $payload,
            'status' => 'pending',
        ]);

        if ($dispatch) {
            PublishNatsOutboxEvent::dispatch($event->id);
        }

        return $event;
    }
}
