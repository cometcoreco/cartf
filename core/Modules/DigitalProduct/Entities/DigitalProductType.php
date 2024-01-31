<?php

namespace Modules\DigitalProduct\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DigitalProductType extends Model
{
    use HasFactory;

    protected $table = 'digital_product_types';
    protected $fillable = ['name', 'slug', 'product_type', 'extensions','image_id', 'status'];

    protected static function newFactory()
    {
        return \Modules\DigitalProduct\Database\factories\DigitalProductTypeFactory::new();
    }
}
