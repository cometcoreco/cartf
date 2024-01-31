<?php

namespace App\Providers;

use App\Events\SupportMessage;
use App\Events\TenantRegisterEvent;
use App\Listeners\SupportSendMailToAdmin;
use App\Listeners\SupportSendMailToUser;
use App\Listeners\TenantDataSeedListener;
use App\Listeners\TenantDomainCreate;
use App\Models\ProductOrder;
use App\Models\Tenant;
use App\Models\User;
use App\Observers\TenantOrderObserver;
use App\Observers\TenantRegisterObserver;
use App\Observers\WalletBalanceObserver;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Modules\Wallet\Entities\Wallet;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        SupportMessage::class => [
            SupportSendMailToAdmin::class,
            SupportSendMailToUser::class
        ],

        TenantRegisterEvent::class => [
            TenantDomainCreate::class,
            TenantDataSeedListener::class,
        ]
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        User::observe(TenantRegisterObserver::class);
        // Landlord new tenants creations sms sending are handling from the controllers
        ProductOrder::observe(TenantOrderObserver::class);
    }
}
