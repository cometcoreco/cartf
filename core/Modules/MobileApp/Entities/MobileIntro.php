<?php

namespace Modules\MobileApp\Entities;

use App\MediaUpload;
use App\Models\MediaUploader;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MobileIntro extends Model
{
    protected $with = ["image"];

    protected $fillable = [
        "title",
        "description",
        "image_id"
    ];

    public function image(): BelongsTo
    {
        return $this->belongsTo(MediaUploader::class,"image_id","id");
    }
}
