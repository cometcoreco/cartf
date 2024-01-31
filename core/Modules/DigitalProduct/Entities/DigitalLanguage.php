<?php

namespace Modules\DigitalProduct\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DigitalLanguage extends Model
{
    use HasFactory;

    protected $fillable = ['id', 'name', 'slug', 'status', 'image_id'];

    protected static function newFactory()
    {
        return \Modules\DigitalProduct\Database\factories\DigitalLanguageFactory::new();
    }
}
