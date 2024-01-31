<?php

namespace App\Actions\Sms;

use Modules\SmsGateway\Http\Services\OtpTraitService;
use Modules\SmsGateway\Http\Traits\OtpGlobalTrait;

class SmsSendAction
{
    private $otp_instance;

    public function smsSender($user): void
    {
        if ((moduleExists('SmsGateway') && isPluginActive('SmsGateway')) && get_static_option('otp_login_status'))
        {
            $this->otp_instance = new OtpTraitService();
            if (get_static_option('new_tenant_user'))
            {
                $this->smsToUserAboutNewTenant($user);
            }
            if (get_static_option('new_tenant_admin'))
            {
                $this->smsToAdminAboutNewTenant();
            }
        }
    }

    private function smsToUserAboutNewTenant($user)
    {
        $number = $user->mobile;
        try {
            $this->otp_instance->send([$number, __('Hello, Your new shop is created successfully - ' . get_static_option('site_title'))]);
        }
        catch (\Exception $exception) {}
    }

    private function smsToAdminAboutNewTenant()
    {
        $number = get_static_option('receiving_phone_number');
        try {
            $this->otp_instance->send([$number, __('A new shop has been created - '.get_static_option('site_title'))]);
        }
        catch (\Exception $exception) {}
    }
}
