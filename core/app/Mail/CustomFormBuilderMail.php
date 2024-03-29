<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CustomFormBuilderMail extends Mailable
{
    use Queueable, SerializesModels;
    public $data = [];
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(array $args)
    {
        $this->data = $args;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $admin_mail_check = is_null(tenant()) ? get_static_option_central('site_global_email') : get_static_option('tenant_site_global_email');
        $mail = $this->from($admin_mail_check, get_static_option('site_title'))
            ->subject($this->data['subject'])
            ->view('emails.custom-form');

        if (!empty($this->data['data']['attachments'])){
            foreach ($this->data['data']['attachments'] as $field_name => $attached_file){
                if (file_exists($attached_file)){
                    $mail->attach($attached_file);
                }
            }
        }
        //write code for attachments
            return $mail;
    }
}
