<?php

namespace Modules\MobileApp\Http\Services\Api;

use Illuminate\Pagination\LengthAwarePaginator;
use Modules\MobileApp\Entities\MobileFeaturedProduct;
use Modules\Product\Entities\Product;
use Illuminate\Database\Eloquent\Collection;

class MobileFeaturedProductService
{
    public static function get_product($limit = 4): Collection|array|null|LengthAwarePaginator
    {
        $selectedProduct = MobileFeaturedProduct::first();
        if (!empty($selectedProduct))
        {
            $product = Product::query()->with("category","inventoryDetail");
            $ids = json_decode($selectedProduct->ids);

            if($selectedProduct->type == 'product'){
                return $product->whereIn("id",$ids)->withSum('taxOptions', 'rate')->limit($limit)->paginate(10);
            }elseif ($selectedProduct->type == 'category'){
                return $product->whereHas("category", function ($query) use ($ids) {
                    $query->whereIn("categories.id", $ids);
                })->withSum('taxOptions', 'rate')->limit($limit)->paginate(10);
            }
        }

        return [];
    }
}
