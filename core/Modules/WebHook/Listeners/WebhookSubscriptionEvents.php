<?php

namespace Modules\WebHook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class WebhookSubscriptionEvents
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        //todo check if it is related to subscription event
        if (in_array($event->current_event, ['subscription:create', 'subscription:update', 'subscription:cancel', 'subscription:delete'])){
            $paylod = $event->paylod;
            $events = $event->events;

            //        WebhookCall::create()
//            ->url('https://other-app.com/webhooks')
//            ->payload(['key' => 'value'])
////            ->useSecret('sign-using-this-secret')
//            ->dispatch();
        }

    }
}
