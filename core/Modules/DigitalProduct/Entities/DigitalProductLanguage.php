<?php

namespace Modules\DigitalProduct\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DigitalProductLanguage extends Model
{
    use HasFactory;

    protected $fillable = ['product_id', 'language_id'];

    protected static function newFactory()
    {
        return \Modules\DigitalProduct\Database\factories\DigitalProductLanguageFactory::new();
    }
}
