<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    protected $fillable = ['name', 'code', 'description', 'discount_type', 'discount_amount', 'status', 'expire_date'];

    public function scopePublished()
    {
        return $this->where('status', 1);
    }

    public function scopeActive()
    {
        return $this->where(function ($query) {
            $query->whereDate('expire_date', '>=', today())->orWhereDate('expire_date', NULL);
        });
    }
}
