<?php

namespace Modules\WebHook\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class WebhookEvents extends Model
{
    use HasFactory;

    protected $fillable = ["event_name","webhook_id"];

    protected static function newFactory()
    {
        return \Modules\WebHook\Database\factories\WebhookEventsFactory::new();
    }

    public function webhook(){
        return $this->belongsTo(Webhook::class,"webhook_id");
    }
}
