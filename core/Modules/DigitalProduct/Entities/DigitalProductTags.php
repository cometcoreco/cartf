<?php

namespace Modules\DigitalProduct\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DigitalProductTags extends Model
{
    use HasFactory;
    protected $fillable = ["product_id", "tag_name"];
    protected static function newFactory()
    {
        return \Modules\DigitalProduct\Database\factories\DigitalProductTagsFactory::new();
    }
}
