<?php

namespace Modules\DigitalProduct\Entities;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DigitalProductReviews extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id', 'user_id', 'rating', 'review_text'
    ];

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
    protected static function newFactory()
    {
        return \Modules\DigitalProduct\Database\factories\DigitalProductReviewsFactory::new();
    }
}
