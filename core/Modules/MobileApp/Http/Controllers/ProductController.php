<?php

namespace Modules\MobileApp\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Tenant\Frontend\TenantFrontendController;
use App\Http\Requests\CheckoutFormRequest;
use App\Http\Services\CheckoutCouponService;
use App\Http\Services\CheckoutToPaymentService;
use App\Http\Services\ProductCheckoutService;
use App\Models\OrderProducts;
use App\Models\ProductOrder;
use App\Models\ProductReviews;
use App\Models\StaticOption;
use Gloudemans\Shoppingcart\Facades\Cart;
use Modules\Attributes\Entities\Brand;
use Modules\Attributes\Entities\Category;
use Modules\Attributes\Entities\Color;
use Modules\Attributes\Entities\Size;
use Modules\Attributes\Entities\Unit;
use Modules\CountryManage\Entities\Country;
use Modules\CountryManage\Entities\State;
use Modules\CouponManage\Entities\ProductCoupon;
use Modules\MobileApp\Http\Requests\Api\MobileCheckoutFormRequest;
use Modules\MobileApp\Http\Resources\Api\MobileFeatureProductResource;
use Modules\MobileApp\Http\Services\Api\MobileCheckoutServices;
use Modules\Product\Entities\Product;
use Modules\Product\Entities\ProductAttribute;
use Modules\Product\Entities\ProductCategory;
use Modules\Product\Entities\ProductShippingReturnPolicy;
use Modules\Product\Entities\ProductTag;
use Modules\Product\Entities\ProductUnit;
use Modules\Product\Entities\ProductUom;
use Illuminate\Http\Request;
use Modules\MobileApp\Http\Services\Api\ApiProductServices;
use Modules\Product\Services\Web\FrontendProductServices;
use Modules\ShippingModule\Entities\ShippingMethod;
use Modules\ShippingModule\Entities\Zone;
use Modules\ShippingModule\Entities\ZoneRegion;
use Modules\TaxModule\Entities\CountryTax;
use Modules\TaxModule\Entities\StateTax;
use Modules\TaxModule\Entities\TaxClassOption;
use Modules\TaxModule\Services\CalculateTaxBasedOnCustomerAddress;
use Modules\TaxModule\Services\CalculateTaxServices;

class ProductController extends Controller
{
    public function search(Request $request){
        $all_products = ApiProductServices::productSearch($request, "api", "api");
        $products = $all_products["items"];
        unset($all_products["items"]);
        $additional = $all_products;

        return MobileFeatureProductResource::collection($products)->additional($additional);
    }

    public function productDetail($id){
        $product = Product::where('id', $id)
            ->with(
                'category',
                'tag',
                'color',
                'sizes',
                'campaign_product',
                'inventoryDetail',
                'inventoryDetail.productColor',
                'inventoryDetail.productSize',
                'inventoryDetail.attribute',
                'reviews',
                'delivery_option',
            )
            ->where("status_id", 1)
            ->withSum('taxOptions', 'rate')
            ->first();

        if(empty($product)){
            return response()->json([
                "success" => false,
                "msg" => "no product found"
            ])->setStatusCode(404);
        }

        $dynamic_campaign = get_product_dynamic_price($product);
        $product->regular_price = calculatePrice($dynamic_campaign['regular_price'], $product);
        $product->sale_price = calculatePrice($dynamic_campaign['sale_price'], $product);

        // get selected attributes in this product ( $available_attributes )
        $inventoryDetails = optional($product->inventoryDetail);
        $product_inventory_attributes = $inventoryDetails->toArray();

        $all_included_attributes = array_filter(array_column($product_inventory_attributes, 'attribute', 'id'));
        $all_included_attributes_prd_id = array_keys($all_included_attributes);

        $available_attributes = [];  // FRONTEND : All displaying attributes
        $product_inventory_set = []; // FRONTEND : attribute store
        $additional_info_store = []; // FRONTEND : $additional info_store

        foreach ($all_included_attributes as $id => $included_attributes) {
            $single_inventory_item = [];
            $color_code = "";
            foreach ($included_attributes as $included_attribute_single) {
                $available_attributes[$included_attribute_single['attribute_name']][$included_attribute_single['attribute_value']] = 1;

                // individual inventory item
                $single_inventory_item[$included_attribute_single['attribute_name']] = $included_attribute_single['attribute_value'];

                if (optional($inventoryDetails->find($id))->productColor) {
                    $single_inventory_item['Color'] = optional(optional($inventoryDetails->find($id))->productColor)->name;
                    $color_code = optional(optional($inventoryDetails->find($id))->productColor)->color_code;
                }

                if (optional($inventoryDetails->find($id))->productSize) {
                    $single_inventory_item['Size'] = optional(optional($inventoryDetails->find($id))->productSize)->name;
                }
            }

            $item_additional_price = optional(optional($product->inventoryDetail)->find($id))->additional_price ?? 0;
            $item_additional_stock = optional(optional($product->inventoryDetail)->find($id))->stock_count ?? 0;
            $image = get_attachment_image_by_id(optional(optional($product->inventoryDetail)->find($id))->image)['img_url'] ?? '';

            $hash = md5(json_encode($single_inventory_item));
            $single_inventory_item['hash'] = $hash;
            $product_inventory_set[] = $single_inventory_item;

            $sorted_inventory_item = $single_inventory_item;
            ksort($sorted_inventory_item);
            $single_inventory_item['color_code'] = $color_code;

            $additional_info_store[$hash] = [
                'pid_id' => $id, //Info: ProductInventoryDetails id
                'additional_price' => calculatePrice($item_additional_price, $product),
                'stock_count' => $item_additional_stock,
                'image' => $image,
            ];
        }

        $productColors = $product->color->unique();
        $productSizes = $product->sizes->unique();

        $colorAndSizes = $product?->inventoryDetail?->whereNotIn("id", $all_included_attributes_prd_id);

        if (!empty($colorAndSizes)) {
            foreach ($colorAndSizes as $inventory) {
                $product_id = $inventory['id'] ?? $product->id;

                // if this inventory has attributes then it will fire continue statement
                if (in_array($inventory->product_id, $all_included_attributes_prd_id)) {
                    continue;
                }

                $color_code = "";

                $single_inventory_item = [];

                if (optional($inventoryDetails->find($product_id))->color) {
                    $single_inventory_item['Color'] = optional($inventory->productColor)->name;
                    $color_code = optional($inventory->productColor)->color_code;
                }

                if (optional($inventoryDetails->find($product_id))->size) {
                    $single_inventory_item['Size'] = optional($inventory->productSize)->name;
                }

                $hash = md5(json_encode($single_inventory_item));
                $single_inventory_item['hash'] = $hash;
                $product_inventory_set[] = $single_inventory_item;

                $item_additional_price = optional($inventory)->additional_price ?? 0;
                $item_additional_stock = optional($inventory)->stock_count ?? 0;
                $image = get_attachment_image_by_id(optional($inventory)->image)['img_url'] ?? '';

                $sorted_inventory_item = $single_inventory_item;
                ksort($sorted_inventory_item);
                $single_inventory_item['color_code'] = $color_code;

                $additional_info_store[$hash] = [
                    'pid_id' => $product_id,
                    'additional_price' => calculatePrice($item_additional_price, $product),
                    'stock_count' => $item_additional_stock,
                    'image' => $image,
                ];
            }
        }

        // todo:: write code for product category only add image path into category array
        $categoryImage = get_attachment_image_by_id($product->category->image_id);
        $product->category->categoryImage = !empty($categoryImage) ? $categoryImage['img_url'] : '';
        unset($product->category->image_id);
        unset($product->category->laravel_through_key);
        unset($product->category->image_id);

        // todo:: write code for product sub category only add image path into category array
        if ($product->subCategory)
        {
            $subCategoryImage = get_attachment_image_by_id($product->subCategory?->image_id);
            $product->subCategory->categoryImage = !empty($subCategoryImage) ? $subCategoryImage['img_url'] : '';
            unset($product->subCategory->image_id);
            unset($product->subCategory->laravel_through_key);
            unset($product->subCategory->image_id);
        }

        // todo:: write code for product sub category only add image path into category array
        if ($product->childCategory)
        {
            $product->childCategory->transform(function ($item){
                $image = $item->image_id;
                unset($item->image_id);
                unset($item->image_id);
                unset($item->laravel_through_key);

                $image = get_attachment_image_by_id($image);
                $item->image = !empty($image) ? $image['img_url'] : '';
                return $item;
            });
        }

        foreach($product->gallery_images as $gallery) {
            $image = get_attachment_image_by_id($gallery->id);
            $gallery->image = !empty($image) ? $image['img_url'] : '';
            unset($gallery->id);
            unset($gallery->title);
            unset($gallery->path);
            unset($gallery->alt);
            unset($gallery->size);
            unset($gallery->dimensions);
            unset($gallery->user_id);
            unset($gallery->created_at);
            unset($gallery->updated_at);
            unset($gallery->laravel_through_key);
        }

        // test
        $productImage = $product->image_id;
        unset($product->image_id);
        $productImage = get_attachment_image_by_id($productImage);
        $product->image = !empty($productImage) ? $productImage['img_url'] : '';

        $product->reviews->transform(function ($item){
            $p_image = get_attachment_image_by_id($item->user->image);
            unset($item->user->image);
            $item->user->image = !empty($p_image) ? $p_image['img_url'] : '';
            return $item;
        });

        $available_attributes = array_map(fn($i) => array_keys($i), $available_attributes);

        $sub_category_arr = json_decode($product->sub_category_id, true);
        $ratings = ProductReviews::where('product_id', $product->id)->with('user')->get();
        $ratings->transform(function ($item){
            $p_image = get_attachment_image_by_id($item->user->image);
            unset($item->user->image);
            $item->user->image = !empty($p_image) ? $p_image['img_url'] : '';
            return $item;
        });

        $avg_rating = $ratings->count() ? round($ratings->sum('rating') / $ratings->count()) : null;

        // related products
        $product_category = $product?->category?->id;
        $product_id = $product->id;
        $related_products = Product::with('campaign_product','campaign_sold_product','reviews','inventory','badge','uom')->where('status_id', 1)
            ->whereIn('id', function ($query) use ($product_id, $product_category) {
                $query->select('product_categories.product_id')
                    ->from(with(new ProductCategory())->getTable())
                    ->where('product_id', '!=', $product_id)
                    ->where('category_id', '=', $product_category)
                    ->get();
            })
            ->withSum('taxOptions', 'rate')
            ->inRandomOrder()
            ->take(5)
            ->get();

        // (bool) Check logged-in user bought this item (needed for review)
        $user = getUserByGuard('sanctum');

        $user_has_item = $user
            ? !!ProductOrder::where('user_id', $user->id)
                ->where('product_id', $product->id)->count()
            : null;

        $user_rated_already = !!! ProductReviews::where('product_id', optional($product)->id)->where('user_id', optional($user)->id)->count();

        $setting_text = StaticOption::whereIn('option_name', [
            'product_in_stock_text',
            'product_out_of_stock_text',
            'details_tab_text',
            'additional_information_text',
            'reviews_text',
            'your_reviews_text',
            'write_your_feedback_text',
            'post_your_feedback_text',
        ])->get()->mapWithKeys(fn ($item) => [$item->option_name => $item->option_value])->toArray();

        $return_policy = ProductShippingReturnPolicy::where('product_id' ,$product->id)->first();

        // sidebar data
        $all_units = ProductUom::all();
        $maximum_available_price = Product::query()->with('category')->max('price');
        $min_price = request()->pr_min ? request()->pr_min : Product::query()->min('price');
        $max_price = request()->pr_max ? request()->pr_max : $maximum_available_price;
        $all_tags = ProductTag::all();

        return [
            'product' => $product,
            'product_url' => route("tenant.shop.product.details", $product->slug),
            'related_products' => MobileFeatureProductResource::collection($related_products),
            'user_has_item' => $user_has_item,
            'ratings' => $ratings,
            'avg_rating' => $avg_rating,
            'available_attributes' => $available_attributes,
            'product_inventory_set' => $product_inventory_set,
            'additional_info_store' => $additional_info_store,
//            'all_units' => $all_units,
            'maximum_available_price' => $maximum_available_price,
//            'min_price' => $min_price,
//            'max_price' => $max_price,
//            'all_tags' => $all_tags,
            'productColors' => $productColors,
            'productSizes' => $productSizes,
            'setting_text' => $setting_text,
            'user_rated_already' => $user_rated_already,
            'return_policy' => $return_policy
        ];
    }

    public function priceRange(){
        $max_price = Product::query()->with('category')->max('price');
        $min_price = Product::query()->min('price');

        return response()->json(["min_price" => $min_price, "max_price" => $max_price]);
    }

    public function storeReview(Request $request){
        $user = auth('sanctum')->user();
        if (!$user) {
            return response()->json(['msg' => 'Login to submit rating'])->setStatusCode(422);
        }

        $request->validate([
            'id' => 'required|exists:products',
            'rating' => 'required|integer',
            'comment' => 'required|string',
        ]);

        if ($request->rating > 5) {
            $rating = 5;
        }

        // ensure rating not inserted before
        $user_rated_already = !! ProductReviews::where('product_id', $request->id)->where('user_id', $user->id)->count();
        if ($user_rated_already) {
            return response()->json(['msg' => __('You have rated before')])->setStatusCode(422);
        }

        $rating = ProductReviews::create([
            'product_id' => $request->id,
            'user_id' => $user->id,
            'rating' => $request->rating,
            'review_text' => $request->comment,
        ]);

        return response()->json(["success" => true,"data" => $rating]);
    }

    public function searchItems(){
        return FrontendProductServices::shopPageSearchContent();

//        $min_price = Product::query()->min('sale_price');
//        $max_price = $maximum_available_price;
//        $item_style =['grid','list'];
//
//        return view('frontend.dynamic-redirect.product', compact(
//            'all_category',
//            'all_attributes',
//            'all_tags',
//            'all_colors',
//            'all_sizes',
//            'all_units',
//            'all_brands',
//            'min_price',
//            'max_price',
//            'maximum_available_price',
//            'item_style',
//        ));
    }

    public function productCoupon(Request $request)
    {
        $request->validate([
            'coupon' => 'required',
            'total_amount' => 'required|numeric',
            'ids' => 'required'
        ]);

        $discounted_price = CheckoutCouponService::calculateCoupon($request, $request->total_amount, $request->ids, 'DISCOUNT');

        return response()->json(["discounted_price" => $discounted_price]);
    }

    public function shippingCharge(Request $request)
    {
        $request->validate([
            'country' => 'required',
            'state' => 'nullable',
            'city' => 'nullable',
            'product_ids' => 'required'
        ]);

        $product_tax = $this->get_product_shipping_tax($request);

        if ($request->has('state') && $request->has('country'))
        {
            $shipping_zones = ZoneRegion::whereJsonContains('country', $request->country)->whereJsonContains('state', $request->state)->get();
        }
        elseif($request->has('country') && !$request->has('state'))
        {
            $shipping_zones = ZoneRegion::whereJsonContains('country', $request->country)->get();
        }

        $zone_ids = [];
        foreach ($shipping_zones ?? [] as $zone) {
            $zone_ids[] = $zone->zone_id;
        }

        $shipping_methods = ShippingMethod::whereIn('zone_id', $zone_ids)
            ->orWhere('is_default', 1)->get();

        foreach ($shipping_methods ?? [] as $method)
        {
            $method->options->cost = $this->calculateShippingWithTax($method, $request);
            $product_tax = 0;
        }

        $default_shipping = $shipping_methods->where('is_default', 1)->first();

        $product_tax_info = $this->calculateProductWithTax($request->product_ids, $request->country, $request->state, $request->city);

        return response()->json([
            'tax' => $product_tax,
            'shipping_options' => $shipping_methods,
            'default_shipping_options' => $default_shipping,
            'product_tax_info' => $product_tax_info
        ]);
    }

    private function calculateProductWithTax($product_ids, $country, $state, $city)
    {
        $ids = json_decode($product_ids);

        $enableTaxAmount = !CalculateTaxServices::isPriceEnteredWithTax();
        $shippingTaxClass = TaxClassOption::where("class_id", get_static_option("shipping_tax_class"))->sum("rate");
        $tax = CalculateTaxBasedOnCustomerAddress::init();
        $uniqueProductIds = $ids;

        $country_id = $country ?? 0;
        $state_id = $state ?? 0;
        $city_id = $city ?? 0;

        if(empty($uniqueProductIds))
        {
            $taxProducts = collect([]);
        }
        else
        {
            if(CalculateTaxBasedOnCustomerAddress::is_eligible()){
                $taxProducts = $tax
                    ->productIds($uniqueProductIds)
                    ->customerAddress($country_id, $state_id, $city_id)
                    ->generate();
            }
            else
            {
                $taxProducts = collect([]);
            }
        }

        $products = Product::whereIn('id', $uniqueProductIds)->withSum('taxOptions', 'rate')->get();
        $tax_data = [];
        foreach ($products ?? [] as $data)
        {
            $v_tax_total = 0;
            $taxAmount = $taxProducts->where("id" , $data->id)->first();

            if(!empty($taxAmount)){
                $taxAmount->tax_options_sum_rate = $taxAmount->tax_options_sum_rate ?? 0;
                $v_tax_total = calculatePrice($data->sale_price, $taxAmount, "percentage");
            }

            $tax_data[] = [
                'product_id' => $data->id,
                'tax_amount' => $v_tax_total,
                'tax_type' => 'amount'
            ];
        }

        return $tax_data;
    }

    private function calculateShippingWithTax($method, $request)
    {
        $shippingTaxClass = TaxClassOption::where("class_id", get_static_option("shipping_tax_class"));
        if(!empty($country_id)){
            $shippingTaxClass->where("country_id", $request->country);
        }
        if(!empty($state_id)){
            $shippingTaxClass->where("state_id", $request->state);
        }
        if(!empty($city_id)){
            $shippingTaxClass->where("city_id", $request->city);
        }

        $shippingTaxClass = $shippingTaxClass->sum("rate");

        // add shipping charge tax
        return calculatePrice($method->options->cost, $shippingTaxClass, "shipping");
    }

    private function get_product_shipping_tax($request)
    {
        $product_tax = 0;
        $country_tax = CountryTax::where('country_id', $request->country)->select('id', 'tax_percentage')->first();

        if ($request->state && $request->country) {
            $product_tax = StateTax::where(['country_id' => $request->country, 'state_id' => $request->state])
                ->select('id', 'tax_percentage')
                ->first();

            if (!empty($product_tax)) {
                $product_tax = $product_tax->toArray();
                $product_tax = $product_tax ? $product_tax['tax_percentage'] : 0;
            } else {
                if (!empty($country_tax))
                {
                    $product_tax = $country_tax->toArray();
                    $product_tax = $product_tax ? $product_tax['tax_percentage'] : 0;
                }
            }
        } else {
            $product_tax = $country_tax->toArray()['tax_percentage'];
            $product_tax = $product_tax ? $product_tax['tax_percentage'] : 0;
        }

        return $product_tax;
    }

    public function checkout(MobileCheckoutFormRequest $request)
    {
        $validated_data = $request->validated();

        if (array_key_exists('cash_on_delivery', $validated_data))
        {
            $validated_data['checkout_type'] = $validated_data['cash_on_delivery'] === 'on' ? 'cod' : 'digital';
        } else {
            $validated_data['checkout_type'] = 'digital';
        }

        $checkout_service = new MobileCheckoutServices();
        $user = $checkout_service->getOrCreateUser($validated_data);
        $order_log_id = $checkout_service->createOrder($validated_data, $user);

        // Checking shipping method is selected
        if(!$order_log_id) {
            return back()->withErrors(['error' => __('Please select a shipping method')]);
        }

        return response()->json([
            'order_id' => $order_log_id,
            'order_details' => ProductOrder::find($order_log_id)
        ]);
    }

    public function paymentUpdate(Request $request)
    {
        $validated = $request->validate([
            'order_id' => 'required',
            'status' => 'required'
        ]);

        $order = ProductOrder::find($validated['order_id']);
        if (!empty($order) && $order->payment_status == 'pending')
        {
            $order->payment_status = $validated['status'] == 1 ? 'success' : 'pending';
        }

        return response()->json([
            'success' => true,
            'msg' => __('Order Status Updated Successfully')
        ]);
    }
}
