<?php

namespace Modules\DigitalProduct\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AdditionalCustomField extends Model
{
    use HasFactory;

    protected $fillable = ['additional_field_id', 'option_name', 'option_value'];

    protected static function newFactory()
    {
        return \Modules\DigitalProduct\Database\factories\AdditionalCustomFieldFactory::new();
    }
}
