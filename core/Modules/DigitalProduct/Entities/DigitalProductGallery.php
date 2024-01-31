<?php

namespace Modules\DigitalProduct\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DigitalProductGallery extends Model
{
    use HasFactory;

    protected $fillable = ["product_id","image_id"];

    protected static function newFactory()
    {
        return \Modules\DigitalProduct\Database\factories\DigitalProductGalleryFactory::new();
    }
}
