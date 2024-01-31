<?php

namespace App\Listeners;

use App\Events\TenantRegisterEvent;
use App\Helpers\GenerateTenantToken;
use App\Models\Tenant;
use App\Models\TenantUniqueKey;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Str;

class TenantDomainCreate
{

    public function __construct()
    {

    }

    public function handle(TenantRegisterEvent $event)
    {
        $tenant = Tenant::create(['id' => $event->subdomain]);

        $hash_key = GenerateTenantToken::token();

        DB::table('tenants')->where('id', $tenant->id)->update([
            'user_id' => $event->user_info->id,
            'theme_slug' => $event->theme,
            'unique_key' => $hash_key
        ]);

        TenantUniqueKey::create([
            'tenant_id' =>  $tenant->id,
            'unique_key' => $hash_key
        ]);

        $tenant->domains()->create(['domain' => $event->subdomain . '.' . env('CENTRAL_DOMAIN')]);
    }
}
