<?php

namespace Modules\WebHook\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Modules\WebHook\Events\WebhookEventFire;
use Modules\WebHook\Listeners\WebhookSubscriptionEvents;
use Modules\WebHook\Listeners\WebhookUsersEvents;
use Modules\WebHook\Listeners\WebhookWalletEvents;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        WebhookEventFire::class => [
            WebhookWalletEvents::class,
            WebhookUsersEvents::class,
            WebhookSubscriptionEvents::class
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        /* tenant model observer */
//        User::observe(TenantRegisterObserver::class);
    }
}
