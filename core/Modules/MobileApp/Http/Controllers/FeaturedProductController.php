<?php

namespace Modules\MobileApp\Http\Controllers;

use Illuminate\Http\Request;
use Modules\Campaign\Entities\Campaign;
use Modules\Campaign\Entities\CampaignProduct;
use App\Http\Controllers\Controller;
use Modules\MobileApp\Entities\MobileFeaturedProduct;
use Modules\MobileApp\Http\Resources\Api\MobileFeatureProductResource;
use Modules\MobileApp\Http\Services\Api\ApiProductServices;
use Modules\MobileApp\Http\Services\Api\MobileFeaturedProductService;
use Modules\MobileApp\Entities\MobileCampaign;
use JetBrains\PhpStorm\ArrayShape;
use Modules\Product\Entities\Product;

class FeaturedProductController extends Controller
{
    public function index($limit = 4){
        $product = MobileFeaturedProductService::get_product($limit);

        return MobileFeatureProductResource::collection($product);
    }

    #[ArrayShape(["products" => "array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable", "campaign_info" => "array"])]
    public function campaign($id = null){
        // fetch all product id from selected campaign
        if(!empty($id)){
            $campaignId = $id;
        }else{
            $mobileCampaign = MobileCampaign::first();
            $campaignId = $mobileCampaign ? $mobileCampaign->campaign_id : null;
        }

        $campaign = Campaign::where("id" , $campaignId)->first();
        $selectedCampaignProductId = CampaignProduct::select("product_id")
            ->where("campaign_id", $campaignId)->get()->pluck("product_id")->toArray();
        // get all product from this campaign
        $products = Product::whereIn('id',$selectedCampaignProductId)->withSum('taxOptions', 'rate')->get();

        $products = MobileFeatureProductResource::collection($products)->toArray($products);

        return ["products" => $products ,"campaign_info" => optional($campaign)->toArray()];
    }

    public function homepageCamapaign(){
        $campaignId = MobileCampaign::first();

        return $campaignId;
    }

    public function recent(Request $request){
        $all_products = ApiProductServices::productSearch($request, "api", "api");
        $products = $all_products["items"];
        unset($all_products["items"]);
        $additional = $all_products;

        return MobileFeatureProductResource::collection($products)->additional($additional);
    }
}
