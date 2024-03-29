<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentGateway extends Model
{
    use HasFactory;
    protected $table = 'payment_gateways';
    protected $fillable = ['name','image','description','status','test_mode','credentials'];

    protected $casts = [
        'test_mode' => 'integer',
        'status' => 'integer'
    ];

    public function scopePermittedPaymentGateway()
    {
        $permissions = tenant_plan_payment_gateway_list() ?? [];
        return $this->whereIn('name', $permissions);
    }
}
