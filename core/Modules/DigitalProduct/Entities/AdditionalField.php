<?php

namespace Modules\DigitalProduct\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Modules\Badge\Entities\Badge;

class AdditionalField extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id', 'badge_id', 'pages', 'language', 'formats',
        'words', 'tool_used', 'database_used', 'compatible_browsers',
        'compatible_os', 'high_resolution', 'author_id'
    ];

    public function author(): HasOne
    {
        return $this->hasOne(DigitalAuthor::class, 'id', 'author_id');
    }

    public function language(): HasOne
    {
        return $this->hasOne(DigitalLanguage::class, 'id', 'language');
    }

    public function getLanguage(): HasOne
    {
        return $this->hasOne(DigitalLanguage::class, 'id', 'language');
    }

    public function badge(): HasOne
    {
        return $this->hasOne(Badge::class, 'id', 'badge_id');
    }

    protected static function newFactory()
    {
        return \Modules\DigitalProduct\Database\factories\AdditionalFieldFactory::new();
    }
}
