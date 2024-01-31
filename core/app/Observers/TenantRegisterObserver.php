<?php

namespace App\Observers;

use App\Helpers\EmailHelpers\MarkupGenerator;
use App\Helpers\EmailHelpers\VerifyUserMailSend;
use App\Mail\BasicMail;
use App\Models\User;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Mail;
use Modules\SmsGateway\Entities\SmsGateway;
use Modules\SmsGateway\Http\Services\OtpTraitService;
use Modules\WebHook\Events\WebhookEventFire;

class TenantRegisterObserver
{
    private $otp_instance;
    public function created(User $user)
    {
        /* send mail to admin about new user registration */
        $this->mailToAdminAboutUserRegister($user);
        /* send email verify mail to user */
        VerifyUserMailSend::sendMail($user);
//        CustomDomain::create(['user_id' => $user->id]);

        $this->smsSender($user);

        if (!\tenant())
        {
            Event::dispatch(new WebhookEventFire('user:register', $user));
        }
    }


    private function mailToAdminAboutUserRegister(User $user)
    {
        $msg = MarkupGenerator::paragraph(__('Hello,'));
        $msg .= MarkupGenerator::paragraph(sprintf(__('You have a user registration at %s'),site_title()));
        $subject = sprintf(__('New user registration at %s'),site_title());
        try {
            Mail::to(site_global_email())->send(new BasicMail($msg,$subject));
        }catch (\Exception $e){
            //handle exception
        }
    }

    private function smsSender($user)
    {
        if ((moduleExists('SmsGateway') && isPluginActive('SmsGateway')) && get_static_option('otp_login_status'))
        {
            if (SmsGateway::active()->exists())
            {
                $this->otp_instance = new OtpTraitService();
                if (get_static_option('new_user_user'))
                {
                    $this->smsToUserAboutUserRegister($user);
                }
                if (get_static_option('new_user_admin'))
                {
                    $this->smsToAdminAboutUserRegister();
                }
            }
        }
    }

    private function smsToUserAboutUserRegister(User $user)
    {
        $number = $user->mobile;
        try {
            $this->otp_instance->send([$number, __('Welcome to '.get_static_option('site_title').'. Your account registration is successful')], 'notify', 'register', 'user');
        }
        catch (\Exception $exception) {}
    }

    private function smsToAdminAboutUserRegister()
    {
        $number = get_static_option('receiving_phone_number');
        try {
            $this->otp_instance->send([$number, __('A new user has been registered - '.get_static_option('site_title'))], 'notify', 'register', 'admin');
        }
        catch (\Exception $exception) {}
    }
}
