<?php

namespace Modules\WebHook\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Modules\WebHook\Entities\Webhook;
use function Clue\StreamFilter\fun;

class WebhookEventFire
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public $paylod;
    public $events;
    public $current_event;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($current_event,$payload)
    {
        $this->paylod = $payload;
        $this->current_event = $current_event;
        $this->events = Cache::remember('webhook-all-events',300,function() {
            return Webhook::with('events')->get();
        });
    }

    /**
     * Get the channels the event should be broadcast on.
     *
     * @return array
     */
    public function broadcastOn()
    {
        return [];
    }
}
