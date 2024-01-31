<?php

namespace Modules\DigitalProduct\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DigitalProductRefundPolicies extends Model
{
    use HasFactory;

    protected $table = 'digital_product_refund_policies';
    protected $fillable = ["product_id","refund_description"];

    protected static function newFactory()
    {
        return \Modules\DigitalProduct\Database\factories\DigitalProductRefundPoliciesFactory::new();
    }
}
