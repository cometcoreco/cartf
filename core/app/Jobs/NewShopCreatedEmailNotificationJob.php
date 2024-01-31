<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\Tenant;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;
use Stancl\Tenancy\Contracts\TenantWithDatabase;

class NewShopCreatedEmailNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /** @var TenantWithDatabase */
    protected $tenant;

    public function __construct(TenantWithDatabase $tenant)
    {
        $this->tenant = $tenant;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //todo:: send notification to admin about new website create...

        try {
           $tenant = Tenant::find($this->tenant->id);
            $user = User::find($tenant->user_id);
            if (!is_null($user)){
                Mail::to($user->email)->send(new \App\Mail\NewShopCreatedEmailNotification($this->tenant));
            }
            $msg = 'Hello'.'<br>';
            $msg .= __('A New "'.$this->tenant->id.current(config('tenancy.central_domains')).'" website has been created at ').Carbon::now()->format('D, d-M-Y').'<br>';

            Mail::to(get_static_option('site_global_email'))->send(new \App\Mail\BasicMail($msg,__('New website has been Created')));
        }catch (\Exception $e){
            //handle error
        }

    }
}
