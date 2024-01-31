<?php

namespace Modules\DigitalProduct\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DigitalProductChildCategories extends Model
{
    use HasFactory;

    protected $table = 'digital_product_child_categories';
    protected $fillable = ['product_id', 'child_category_id'];

    protected static function newFactory()
    {
        return \Modules\DigitalProduct\Database\factories\DigitalProductChildCategoriesFactory::new();
    }
}
