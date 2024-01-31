<?php

namespace Modules\WebHook\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class WebhookLog extends Model
{
    use HasFactory;

    protected $fillable = ["event_name","url","payload","method_type",'status'];

    protected static function newFactory()
    {
        return \Modules\WebHook\Database\factories\WebhookFactory::new();
    }

}
