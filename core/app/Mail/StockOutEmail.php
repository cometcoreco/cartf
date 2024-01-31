<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;

use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use PDF;

class StockOutEmail extends Mailable
{
    use Queueable, SerializesModels;
    public $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function build()
    {
        $stock_details = $this->data;
        $mail = $this->from(get_static_option('tenant_site_global_email'))
                 ->subject(__('Product Stock Warning').' From '.get_static_option('site_title'))
                 ->view('emails.stock_out', compact('stock_details'));

        return $mail;
    }
}
