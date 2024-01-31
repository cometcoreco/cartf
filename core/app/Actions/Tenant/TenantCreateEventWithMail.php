<?php

namespace App\Actions\Tenant;

use App\Events\TenantRegisterEvent;
use App\Mail\TenantCredentialMail;
use App\Models\PaymentLogs;
use App\Models\Tenant;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Modules\SmsGateway\Http\Services\OtpTraitService;
use Modules\SmsGateway\Http\Traits\OtpGlobalTrait;

class TenantCreateEventWithMail
{
    private static $otp_instance;

    public static function tenant_create_event_with_credential_mail($user, $subdomain)
    {
        Tenant::create(['id' => $subdomain]);
        try {
            $raw_pass = get_static_option_central('tenant_admin_default_password') ?? '12345678';
            $credential_password = $raw_pass;
            $credential_email = $user->email;
            $credential_username = get_static_option_central('tenant_admin_default_username') ?? 'super_admin';

            Mail::to($credential_email)->send(new TenantCredentialMail($credential_username, $credential_password));

            if ((moduleExists('SmsGateway') && isPluginActive('SmsGateway')) && get_static_option('otp_login_status'))
            {
                self::smsSender($user);
            }

            return true;
        } catch (\Exception $e) {
        }
    }

    private static function smsSender($user)
    {
        self::$otp_instance = new OtpTraitService();
        if (get_static_option('new_tenant_user'))
        {
            self::smsToUserAboutNewTenant($user);
        }
        if (get_static_option('new_tenant_admin'))
        {
            self::smsToAdminAboutNewTenant();
        }
    }

    private static function smsToUserAboutNewTenant($user)
    {
        $number = $user->mobile;
        try {
            self::$otp_instance::sendSms([$number, __('Hello, Your new shop is created successfully - ' . get_static_option('site_title'))]);
        }
        catch (\Exception $exception) {}
    }

    private static function smsToAdminAboutNewTenant()
    {
        $number = get_static_option('receiving_phone_number');
        try {
            self::$otp_instance::sendSms([$number, __('A new shop has been created - '.get_static_option('site_title'))]);
        }
        catch (\Exception $exception) {}
    }
}
