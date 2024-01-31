<?php

namespace Modules\WebHook\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Webhook extends Model
{
    use HasFactory;

    protected $fillable = ["name","status","url","method_type"];

    protected static function newFactory()
    {
        return \Modules\WebHook\Database\factories\WebhookFactory::new();
    }

    public function events(){
        return $this->hasMany(WebhookEvents::class,"webhook_id");
    }
}
