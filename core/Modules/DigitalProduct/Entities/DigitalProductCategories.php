<?php

namespace Modules\DigitalProduct\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DigitalProductCategories extends Model
{
    use HasFactory;

    protected $table = 'digital_product_categories';
    protected $fillable = ['product_id', 'category_id'];

    protected static function newFactory()
    {
        return \Modules\DigitalProduct\Database\factories\DigitalProductCategoriesFactory::new();
    }
}
