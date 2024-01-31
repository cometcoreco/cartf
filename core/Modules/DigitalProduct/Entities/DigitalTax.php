<?php

namespace Modules\DigitalProduct\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DigitalTax extends Model
{
    use HasFactory;

    protected $table = 'digital_product_taxes';
    protected $fillable = ['name', 'tax_percentage', 'status'];

    protected static function newFactory()
    {
        return \Modules\DigitalProduct\Database\factories\DigitalTaxFactory::new();
    }
}
