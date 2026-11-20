<?php

namespace App\Jobs;

use App\Models\NatsOutboxEvent;
use App\Services\NatsPublisher;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Throwable;

class PublishNatsOutboxEvent implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 5;

    public function __construct(public int $eventId) {}

    public function backoff(): array
    {
        return [10, 30, 60, 120];
    }

    public function handle(NatsPublisher $publisher): void
    {
        $event = NatsOutboxEvent::find($this->eventId);
        if (! $event || $event->status === 'published') {
            return;
        }

        $event->increment('attempts');
        $event->update(['status' => 'publishing', 'last_error' => null]);

        try {
            $publisher->publish(
                $event->subject,
                json_encode($event->payload, JSON_THROW_ON_ERROR)
            );
            $event->update([
                'status' => 'published',
                'published_at' => now(),
                'last_error' => null,
            ]);
        } catch (Throwable $exception) {
            $event->update([
                'status' => 'pending',
                'last_error' => $exception->getMessage(),
            ]);
            throw $exception;
        }
    }
}
