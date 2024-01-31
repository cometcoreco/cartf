<?php

namespace Modules\MobileApp\Http\Resources\Api;

use Modules\MobileApp\Http\Services\Api\ProductServices;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Product\Entities\ProductInventoryDetail;

class MobileFeatureProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        // campaign data check
        $campaign_product = !is_null($this->campaignProduct) ? $this->campaignProduct : getCampaignProductById($this->id);
//        $sale_price = $campaign_product ? optional($campaign_product)->campaign_price : $this->sale_price;
//        $sale_price = calculatePrice($sale_price, $this);

        $dynamic_campaign = get_product_dynamic_price($this);
        $deleted_price = calculatePrice($dynamic_campaign['regular_price'], $this);
        $sale_price = calculatePrice($dynamic_campaign['sale_price'], $this);

//        $deleted_price = !is_null($campaign_product) ? $this->sale_price : $this->price;
//        $deleted_price = calculatePrice($deleted_price, $this);

        $campaign_percentage = !is_null($campaign_product) ? getPercentage($deleted_price, $sale_price) : false;

        $add_to_cart = ProductInventoryDetail::where("product_id",$this->id)->count();

        $badge = get_attachment_image_by_id($this->badge?->image);
        $badge_image_url = !empty($badge) ? $badge['img_url'] : '';

        $image = get_attachment_image_by_id($this->image_id);
        $image_url = !empty($image) ? $image['img_url'] : '';

        return [
            "prd_id" => $this->id,
            "title" => html_entity_decode(htmlspecialchars_decode($this->name)),
            "img_url" => $image_url ?? null,
            "campaign_percentage" => round($campaign_percentage,2),
            "price" => round($deleted_price,2),
            "discount_price" => round($sale_price, 2),
            "tax_options_sum_rate" => $this->tax_options_sum_rate ?? 0,
            "badge" => [
                "badge_name" => $this->badge?->name ?? null,
                "image" => $image_url,
            ],
            "campaign_product" => !empty($campaign_product),
            "stock_count" => optional($this->inventory)->stock_count,
            "avg_ratting" => $this->rating_avg_rating,
            "is_cart_able" => !$add_to_cart,
            "vendor_id" => $this->vendor_id ?? null,
            "vendor_name" => $this->vendor?->business_name,
            "category_id" => $this->category?->id,
            "sub_category_id" => $this->subCategory?->id,
            "child_category_ids" => $this->childCategory?->pluck("id")?->toArray(),
        ];
    }
}
