<?php

namespace Modules\DigitalProduct\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DigitalProductSubCategories extends Model
{
    use HasFactory;

    protected $table = 'digital_product_sub_categories';
    protected $fillable = ['product_id', 'sub_category_id'];

    protected static function newFactory()
    {
        return \Modules\DigitalProduct\Database\factories\DigitalProductSubCategoriesFactory::new();
    }
}
