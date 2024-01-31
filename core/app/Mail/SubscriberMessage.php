<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SubscriberMessage extends Mailable
{
    use Queueable, SerializesModels;
    public $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function build()
    {
        $admin_mail_check = get_static_option_central('site_global_email');

        return $this->from($admin_mail_check, get_static_option('site_title'))
            ->subject($this->data['subject'])
            ->view('emails.subscriber');
    }
}
