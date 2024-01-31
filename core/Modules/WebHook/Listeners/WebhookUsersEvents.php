<?php

namespace Modules\WebHook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Str;
use Modules\WebHook\Entities\Webhook;
use Modules\WebHook\Entities\WebhookEvents;
use Modules\WebHook\Entities\WebhookLog;
use Spatie\WebhookServer\WebhookCall;

class WebhookUsersEvents
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
        //todo: check is it related to users event
        //user:register, user:login, user:delete
        if (in_array($event->current_event, ['user:register', 'user:login', 'user:delete'])){
            $payload = $event->paylod;
            $events = WebhookEvents::with('webhook')->where('event_name',$event->current_event)->get();

            foreach($events as $ev){
                try {
                    $payload = [
                        'event' => $event->current_event,
                        'name' => $payload->name,
                        'email' => $payload->email,
                        'mobile' => $payload->phone,
                        'username' => $payload->username,
                        'country' => $payload->country,
                        'city' => $payload->city,
                        'state' => $payload->state,
                        'address' => $payload->address,
                        'company' => $payload->company,
                    ];

                    WebhookCall::create()
                        ->useHttpVerb(strtolower($ev->webhook->method_name))
                        ->url($ev->webhook->url)
                        ->payload($payload)
                        ->useSecret(env('APP_NAME'))
                        ->dispatch();
                    
                    WebhookLog::create(["event_name" => $event->current_event,"url" => $ev->webhook->url,"payload" => json_encode($payload),"method_type" => $ev->webhook->method_name,'status' => 1]);
                }catch (\Exception $e){
                    \Log::info("#".$ev->webhook->id." ". "webhook fired failed, reason ".$e->getMessage());
                }
            }
        }
    }
}
