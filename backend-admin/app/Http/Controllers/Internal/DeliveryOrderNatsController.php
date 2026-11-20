<?php

namespace App\Http\Controllers\Internal;

use App\Http\Controllers\Controller;
use App\Jobs\PublishNatsOutboxEvent;
use App\Models\DeliveryAssignment;
use App\Models\NatsOutboxEvent;
use App\Services\DeliveryOrderOutboxService;
use Illuminate\Http\JsonResponse;

class DeliveryOrderNatsController extends Controller
{
    public function publish(
        DeliveryAssignment $deliveryOrder,
        DeliveryOrderOutboxService $outbox
    ): JsonResponse {
        $event = $outbox->enqueue($deliveryOrder);

        return response()->json([
            'status' => 'queued',
            'event_id' => $event->event_id,
            'outbox_id' => $event->id,
        ], 202);
    }

    public function recalculate(
        DeliveryAssignment $deliveryOrder,
        DeliveryOrderOutboxService $outbox
    ): JsonResponse {
        $event = $outbox->enqueue($deliveryOrder, false);

        return response()->json([
            'status' => 'created',
            'event_id' => $event->event_id,
            'outbox_id' => $event->id,
            'payload' => $event->payload,
        ], 201);
    }

    public function status(DeliveryAssignment $deliveryOrder): JsonResponse
    {
        $events = NatsOutboxEvent::query()
            ->where('aggregate_type', 'delivery_order')
            ->where('aggregate_id', (string) $deliveryOrder->id)
            ->latest()
            ->limit(20)
            ->get([
                'event_id', 'subject', 'status', 'attempts',
                'published_at', 'last_error', 'created_at',
            ]);

        return response()->json(['data' => $events]);
    }

    public function replay(NatsOutboxEvent $event): JsonResponse
    {
        if ($event->status === 'published') {
            $event->update([
                'status' => 'pending',
                'published_at' => null,
                'last_error' => null,
            ]);
        }

        PublishNatsOutboxEvent::dispatch($event->id);

        return response()->json([
            'status' => 'queued',
            'event_id' => $event->event_id,
        ], 202);
    }
}
