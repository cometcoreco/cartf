<?php

namespace Modules\DigitalProduct\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DigitalProductDownload extends Model
{
    use HasFactory;

    protected $fillable = ['product_id', 'download_count', 'user_id'];

    protected static function newFactory()
    {
        return \Modules\DigitalProduct\Database\factories\DigitalProductDownloadFactory::new();
    }
}
