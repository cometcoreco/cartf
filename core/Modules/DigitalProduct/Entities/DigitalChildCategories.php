<?php

namespace Modules\DigitalProduct\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DigitalChildCategories extends Model
{
    use HasFactory;

    protected $fillable = [];

    public function category(): BelongsTo
    {
        return $this->belongsTo(DigitalCategories::class);
    }

    public function subcategory(): BelongsTo
    {
        return $this->belongsTo(DigitalSubCategories::class, 'sub_category_id', 'id');
    }

    protected static function newFactory()
    {
        return \Modules\DigitalProduct\Database\factories\DigitalProductChildCategoriesFactory::new();
    }
}
