<?php

namespace Modules\WebHook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Spatie\WebhookServer\WebhookCall;

class WebhookWalletEvents
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
        if (in_array($event->current_event, ['wallet:deposit', 'wallet:deduct'])){
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
