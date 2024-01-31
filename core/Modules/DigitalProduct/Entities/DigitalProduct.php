<?php

namespace Modules\DigitalProduct\Entities;

use App\Models\MediaUploader;
use App\Models\MetaInfo;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Attributes\Entities\Category;

class DigitalProduct extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'digital_products';
    protected $fillable = [
        'name', 'slug', 'summary', 'description', 'image_id', 'status_id',
        'included_files', 'version', 'release_date', 'update_date', 'preview_link', 'accessibility', 'is_licensable', 'quantity',
        'tax', 'file', 'regular_price', 'sale_price', 'free_date', 'promotional_date', 'promotional_price', 'badge_id'
    ];

    protected $dates = ['release_date', 'update_date'];

    public function category() : HasOneThrough {
        return $this->hasOneThrough(DigitalCategories::class,DigitalProductCategories::class,'product_id','id','id','category_id');
    }

    public function subCategory(): HasOneThrough {
        return $this->hasOneThrough(DigitalSubCategories::class,DigitalProductSubCategories::class,"product_id","id","id","sub_category_id");
    }

    public function childCategory(): hasManyThrough {
        return $this->hasManyThrough(DigitalChildCategories::class, DigitalProductChildCategories::class,"product_id","id","id","child_category_id");
    }

    public function metaData(): MorphOne {
        return $this->morphOne(MetaInfo::class,"metainfoable");
    }

    public function productType()
    {
        return $this->category?->product_type;
    }

    public function product_category(): HasOne {
        return $this->hasOne(DigitalProductCategories::class,"product_id","id");
    }

    public function product_sub_category(): HasOne {
        return $this->hasOne(DigitalProductSubCategories::class,"product_id","id");
    }

    public function product_child_category() : hasMany {
        return $this->hasMany(DigitalProductChildCategories::class,"product_id","id");
    }

    public function tag() : hasMany {
        return $this->hasMany(DigitalProductTags::class, "product_id","id");
    }

    public function tax(): HasOne
    {
        return $this->hasOne(DigitalTax::class, 'id', 'tax');
    }

    public function getTax(): HasOne
    {
        return $this->hasOne(DigitalTax::class, 'id', 'tax');
    }

    public function additionalFields(): HasOne
    {
        return $this->hasOne(AdditionalField::class, 'product_id', 'id');
    }

    public function additionalCustomFields(): HasManyThrough
    {
        return $this->hasManyThrough(AdditionalCustomField::class, AdditionalField::class, 'product_id', 'additional_field_id', 'id', 'id');
    }

    public function gallery_images(): HasManyThrough {
        return $this->hasManyThrough(MediaUploader::class, DigitalProductGallery::class,"product_id","id","id","image_id");
    }

    public function refund_policy(): HasOne {
        return $this->hasOne(DigitalProductRefundPolicies::class, 'product_id', 'id');
    }

    public function author(): HasOneThrough
    {
        return $this->hasOneThrough(DigitalAuthor::class, AdditionalField::class, 'author_id', 'id', 'id', 'product_id');
    }

    public function downloads(): HasMany
    {
        return $this->hasMany(DigitalProductDownload::class, 'product_id', 'id');
    }

    public function reviews(): HasMany {
        return $this->hasMany(DigitalProductReviews::class, 'product_id', 'id');
    }

    public function ratings(){
        return $this->reviews()->avg("rating");
    }

    protected static function newFactory()
    {
        return \Modules\DigitalProduct\Database\factories\DigitalProductFactory::new();
    }
}
