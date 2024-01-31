<?php

namespace App\Mail;

use App\Helpers\LanguageHelper;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use PDF;

class NewShopCreatedEmailNotification extends Mailable
{
    use Queueable, SerializesModels;
    public $tenant;

    public function __construct($tenant)
    {
        $this->tenant = $tenant;
    }

    public function build()
    {
        $mail = $this->from(get_static_option_central('site_global_email'))
                 ->subject(__('Your Website is Ready'))
                 ->view('emails.new-shop-created');

        return $mail;

    }
}
