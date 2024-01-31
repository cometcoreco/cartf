<?php

namespace Modules\MobileApp\Entities;

use App\Models\MediaUploader;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Attributes\Entities\Category;

class MobileSlider extends Model
{
    use HasFactory;

    protected $with = ["image"];

    protected $fillable = ["title","description","image_id","button_text","url","type","campaign","category"];

    public function image(): BelongsTo
    {
        return $this->belongsTo(MediaUploader::class, "image_id","id");
    }

    public function sliderCategory(): HasOne
    {
        return $this->hasOne(Category::class, "id", "category");
    }
}
