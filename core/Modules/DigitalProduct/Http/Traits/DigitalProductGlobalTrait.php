<?php

namespace Modules\DigitalProduct\Http\Traits;

use App\Helpers\SanitizeInput;
use App\Http\Services\CustomPaginationService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\File;
use Modules\DigitalProduct\Entities\AdditionalCustomField;
use Modules\DigitalProduct\Entities\AdditionalField;
use Modules\DigitalProduct\Entities\DigitalProduct;
use Modules\DigitalProduct\Entities\DigitalProductCategories;
use Modules\DigitalProduct\Entities\DigitalProductChildCategories;
use Modules\DigitalProduct\Entities\DigitalProductDownload;
use Modules\DigitalProduct\Entities\DigitalProductGallery;
use Modules\DigitalProduct\Entities\DigitalProductRefundPolicies;
use Modules\DigitalProduct\Entities\DigitalProductSubCategories;
use Modules\DigitalProduct\Entities\DigitalProductTags;
use Modules\Product\Entities\Product;
use Modules\Product\Entities\ProductCategory;
use Modules\Product\Entities\ProductChildCategory;
use Modules\Product\Entities\ProductCreatedBy;
use Modules\Product\Entities\ProductDeliveryOption;
use Modules\Product\Entities\ProductGallery;
use Modules\Product\Entities\ProductInventory;
use Modules\Product\Entities\ProductInventoryDetail;
use Modules\Product\Entities\ProductInventoryDetailAttribute;
use Modules\Product\Entities\ProductShippingReturnPolicy;
use Modules\Product\Entities\ProductSubCategory;
use Modules\Product\Entities\ProductTag;
use Modules\Product\Entities\ProductUom;
use Str;

trait DigitalProductGlobalTrait {
    private function search($request, $route = 'tenant.admin', $queryType = "admin"): array
    {
        $type = $request->type ?? 'default';
        $multiple_date = $this->is_date_range_multiple();

        // create product model instance
//        $all_products = Product::query()->with("brand", "category", "childCategory", "subCategory", "inventory");
        // first I need to check who is currently want to take data
        // run a condition that will check if vendor is currently login then only vendor product will return

        // create product model instance
        if($queryType == 'admin'){
            $all_products = DigitalProduct::query()->with("category", "childCategory", "subCategory");
        }else if ($queryType == 'frontend'){
            $all_products = DigitalProduct::query()->with('subCategory','badge')
                ->withAvg("reviews", "rating")
                ->withCount("reviews");
        }else if ($queryType == 'api'){
            $all_products = DigitalProduct::query()->with('campaign_sold_product','category','subCategory','childCategory','campaign_product', 'inventory','badge','uom')
                ->withAvg("reviews", "rating")
                ->withCount("reviews")
                ->where('status_id', 1);
        }

        // search product name
        $all_products->when(!empty($request->name) && $request->has("name"), function ($query) use ($request) {
            $query->where("name", "LIKE", "%" . $request->name . "%");
        })->when(!empty($request->tag) && $request->has("tag"), function ($query) use ($request) {// search by using tag
            $query->whereHas("tag", function ($i_query) use ($request) {
                $i_query->where("tag_name", "like", "%" . $request->tag . "%");
            });
        })->when(!empty($request->category) && $request->has("category"), function ($query) use ($request) { // category
            $query->whereHas("category", function ($i_query) use ($request) {
                $i_query->where("name", "like", "%" . $request->category . "%");
            });
        })->when(!empty($request->brand) && $request->has("brand"), function ($query) use ($request) { // Brand
            $query->whereHas("brand", function ($i_query) use ($request) {
                $i_query->where("name", "like", "%" . $request->brand . "%");
            });
        })->when(!empty($request->sub_category) && $request->has("sub_category"), function ($query) use ($request) { // sub category
            $query->whereHas("subCategory", function ($i_query) use ($request) {
                $i_query->where("name", "like", "%" . $request->sub_category . "%");
            });
        })->when(!empty($request->child_category) && $request->has("child_category"), function ($query) use ($request) { // child category
            $query->whereHas("childCategory", function ($i_query) use ($request) {
                $i_query->where("name", "like", "%" . $request->child_category . "%");
            });
        })->when(!empty($request->color) && $request->has("color"), function ($query) use ($request) { // color
            $query->whereHas("color", function ($i_query) use ($request) {
                $i_query->where("name", "like", "%" . $request->color . "%");
            });
        })->when(!empty($request->size) && $request->has("size"), function ($query) use ($request) { // size
            $query->whereHas("size", function ($i_query) use ($request) {
                $i_query->where("name", "like", "%" . $request->size . "%");
            });
        })->when(!empty($request->sku) && $request->has("sku"), function ($query) use ($request) { // sku
            $query->whereHas("inventory", function ($i_query) use ($request) {
                $i_query->where("sku", "like", "%" . $request->sku . "%");
            });
        })->when(!empty($request->from_price) && $request->has("from_price") && !empty($request->to_price) && $request->has("to_price"), function ($query) use ($request) { // price
            $query->whereBetween("sale_price", [$request->from_price, $request->to_price]);
        })->when($multiple_date[0] && $request->has("date_range"), function ($query) use ($request, $multiple_date) { // Order By
            // make separate to date in a array
            $arr = $multiple_date[1];
            $query->whereBetween("created_at", $arr);
        })->when(!empty($multiple_date[0]) && $request->has("date_range"), function ($query) use ($request, $multiple_date) { // Order By
            // make separate to date in a array
            $date = $multiple_date[1];
            $query->whereDate("created_at", $date);
        })->when(!empty($request->order_by) && $request->has("order_by"), function ($query) use ($request) { // Order By
            $query->orderBy("id", $request->order_by);
        })->when(!$request->has('order'), function ($query) {
            $query->orderBy("id", "DESC");
        });

        $display_item_count = request()->count ?? 10;

        $current_query = request()->all();
        $create_query = http_build_query($current_query);

        return CustomPaginationService::pagination_type($all_products, $display_item_count, "custom", route($route . ".digital.product.search") . '?' . $create_query);
    }

    private function is_date_range_multiple(): array
    {
        $date = explode(" to ", request()->date_range);

        if(count($date) > 1 && !empty(request()->date_range)){
            return [true , $date];
        }

        return [false, request()->date_range];
    }

    private function product_type(): int
    {
        return 1;
    }

    public function ProductData($data): array
    {
        return [
            "name"  => SanitizeInput::esc_html($data["name"]),
            "slug" => Str::slug($data["slug"] ?? $data["name"]),
            "summary" => SanitizeInput::esc_html($data["summary"]),
            "description" => str_replace('script','',$data['description']),
            "included_files" => SanitizeInput::esc_html($data['included_files']),
            "version" => SanitizeInput::esc_html($data['version']),
            "release_date" => $data['release_date'],
            "update_date" => $data['update_date'],
            "preview_link" => SanitizeInput::esc_url($data['preview_link']),
            "quantity" => !empty($data['quantity']) ? SanitizeInput::esc_html($data['quantity']) : null,
            "accessibility" => SanitizeInput::esc_html($data['accessibility']),
            "tax" => !empty($data['tax']) ? SanitizeInput::esc_html($data['tax']) : null,
            "regular_price" => SanitizeInput::esc_html($data['price']),
            "sale_price" => !empty($data['sale_price']) ? SanitizeInput::esc_html($data['sale_price']) : null,
            "free_date" => $data['free_date'],
            "promotional_date" => $data['promotional_date'],
            "promotional_price" => !empty($data['promotional_price']) ? SanitizeInput::esc_html($data['promotional_price']) : null,
            "author_id" => !empty($data['author_id']) ? SanitizeInput::esc_html($data['author_id']) : null,
            "page" => !empty($data['page']) ? SanitizeInput::esc_html($data['page']) : null,
            "language" => !empty($data['language']) ? SanitizeInput::esc_html($data['language']) : null,
            "formats" => !empty($data['formats']) ? SanitizeInput::esc_html($data['formats']) :null,
            "word" => !empty($data['word']) ? SanitizeInput::esc_html($data['word']) : null,
            "tool_used" => !empty($data['tool_used']) ? SanitizeInput::esc_html($data['tool_used']) : null,
            "database_used" => SanitizeInput::esc_html($data['database_used']),
            "compatible_browsers" => SanitizeInput::esc_html($data['compatible_browsers']),
            "compatible_os" => SanitizeInput::esc_html($data['compatible_os']),
            "high_resolution" => SanitizeInput::esc_html($data['high_resolution']),

            "option_name" => $data['option_name'],
            "option_value" => $data['option_value'],

            "tags" => $data["tags"],
            "file" => $data["file"] ?? '',
            "image_id" => $data["image_id"],
            "product_gallery" => $data["product_gallery"],
            "is_refundable" => !empty($data["is_refundable"]),
            "refund_description" => !empty($data["policy_description"]) ? $data["policy_description"] : ''
        ];
    }

    public function CloneData($data): array
    {
        return [
            "name"  => $data->name,
            "slug" => create_slug(Str::slug($data->slug ?? $data->name), 'DigitalProduct', true, 'DigitalProduct', 'slug'),
            "summary" => $data->summary,
            "description" => $data->description,
            "included_files" => $data->included_files,
            "version" => $data->version,
            "release_date" => $data->release_date,
            "update_date" => $data->update_date,
            "preview_link" => $data->preview_link,
            "quantity" => $data->quantity,
            "accessibility" => $data->accessibility,
            "tax" => $data->tax,
            "image_id" => $data->image_id,
            "product_gallery" => $data->product_gallery,
            "price" => $data->price,
            "regular_price" => $data->regular_price,
            "sale_price" => $data->sale_price,
            "free_date" => $data->free_date,
            "promotional_date" => $data->promotional_date,
            "promotional_price" => $data->promotional_price,
            "author_id" => $data->author_id,
            "page" => $data->page,
            "language" => $data->language,
            "formats" => $data->formats,
            "word" => $data->word,
            "tool_used" => $data->tool_used,
            "database_used" => $data->database_used,
            "compatible_browsers" => $data->compatible_browsers,
            "compatible_os" => $data->compatible_os,
            "high_resolution" => $data->high_resolution,
            "tags" => $data->tags,
            "status_id" => 2,
            "product_type" => $this->product_type() ?? 2,
            "is_refundable" => !empty($data->is_refundable),
            "refund_description" => $data->refund_description,

            "file" => 'no file added',
        ];
    }

    private function productCategoryData($data,$id,$arrKey = "category_id",$column = "category_id"): array
    {
        return [
            $arrKey => $data[$column],
            "product_id" => $id
        ];
    }

    private function childCategoryData($data, $id): array
    {
        $cl_category = [];
        foreach ($data["child_category"] ?? [] as $item) {
            $cl_category[] = ["child_category_id" => $item, "product_id" => $id];
        }

        return $cl_category;
    }

    private function prepareProductGalleryData($data, $id): array
    {
        // explode string to array
        $arr = [];
        $galleries = $this->separateStringToArray($data["product_gallery"], "|");

        foreach($galleries as $image){
            $arr[] = [
                "product_id" => $id,
                "image_id" => $image
            ];
        }

        return $arr;
    }

    private function prepareProductTagData($data, $id): array
    {
        // explode string to array
        $arr = [];
        $galleries = $this->separateStringToArray($data["tags"], ",");

        foreach($galleries as $tag){
            $arr[] = [
                "product_id" => $id,
                "tag_name" => SanitizeInput::esc_html($tag),
                "type" => "digital"
            ];
        }

        return $arr;
    }

    private function prepareProductAdditionalFieldData($product, $id)
    {
        $arr = [];
        $arr['product_id'] = $id;
        $arr['badge_id'] = $product['badge_id'] ?? null;
        $arr['pages'] = $product['page'] ?? null;
        $arr['language'] = $product['language'] ?? null;
        $arr['formats'] = $product['formats'] ?? null;
        $arr['words'] = $product['word'] ?? null;
        $arr['tool_used'] = $product['tool_used'] ?? null;
        $arr['database_used'] = $product['database_used'] ?? null;
        $arr['compatible_browsers'] = $product['compatible_browsers'] ?? null;
        $arr['compatible_os'] = $product['compatible_os'] ?? null;
        $arr['high_resolution'] = $product['high_resolution'] ?? null;
        $arr['author_id'] = $product['author_id'] ?? null;

        return $arr;
    }

    private function prepareProductAdditionalCustomFieldData($product, $product_id, $additional_field_id)
    {
        $arr = [];
        $option_name = $product['option_name'];
        $option_value = $product['option_value'];

        $i = 0;
        foreach ($option_name ?? [] as $key => $name)
        {
            if (empty($name))
            {
                continue;
            }
            $arr[$i]['additional_field_id'] = $additional_field_id;
            $arr[$i]['option_name'] = $name ?? null;
            $arr[$i]['option_value'] = $option_value[$key] ?? null;
            $i++;
        }

        return $arr;
    }

    private function separateStringToArray(string | null $string,string $separator = " , "): array|bool
    {
        if(empty($string)) return [];
        return explode($separator, $string);
    }

    public function prepareMetaData($data): array
    {
        return [
            'title' => SanitizeInput::esc_html($data["general_title"]) ?? '',
            'description' => SanitizeInput::esc_html($data["general_description"]) ?? '',
            'fb_title' => SanitizeInput::esc_html($data["facebook_title"]) ?? '',
            'fb_description' => SanitizeInput::esc_html($data["facebook_description"]) ?? '',
            'fb_image' => $data["facebook_image"] ?? '',
            'tw_title' => SanitizeInput::esc_html($data["twitter_title"]) ?? '',
            'tw_description' => SanitizeInput::esc_html($data["twitter_description"]) ?? '',
            'tw_image' => $data["twitter_image"] ?? ''
        ];
    }

    private function userId(){
        return \Auth::guard("admin")->check() ? \Auth::guard("admin")->user()->id : '';
    }

    private function getGuardName(): string
    {
        return \Auth::guard("admin")->check() ? "admin" : "vendor";
    }


    private function createArrayCreatedBy($product_id, $type){
        $arr = [];

        if($type == 'create'){
            $arr = [
                "product_id" => $product_id,
                "created_by_id" => $this->userId(),
                "guard_name" => $this->getGuardName(),
            ];
        }elseif($type == 'update'){
            $arr = [
                "product_id" => $product_id,
                "updated_by" => $this->userId(),
                "updated_by_guard" => $this->getGuardName(),
            ];
        }elseif($type == 'delete'){
            $arr = [
                "product_id" => $product_id,
                "deleted_by" => $this->userId(),
                "deleted_by_guard" => $this->getGuardName(),
            ];
        }

        return $arr;
    }

    public function createdByUpdatedBy($product_id, $type= "create"){
        return ProductCreatedBy::updateOrCreate(
            [
                "product_id" => $product_id
            ],
            $this->createArrayCreatedBy($product_id, $type)
        );
    }

    public function updateStatus($productId, $statusId): JsonResponse
    {
        $product = DigitalProduct::find($productId)->update(["status_id" => $statusId]);

        $response_status = $product ? ["success" => true, "msg" => __("Successfully updated status")] : ["success" => false,"msg" => __("Failed to update status")];
        return response()->json($response_status)->setStatusCode(200);
    }

    /**
     * @param array $data
     * @param $id
     * @param $product
     * @return bool
     */
    public function insert_product_data(array $data, $id, $product): bool
    {
        $category = DigitalProductCategories::create($this->productCategoryData($product, $id));

        if (!empty($data['sub_category']))
        {
            $subcategory = DigitalProductSubCategories::create($this->productCategoryData($product, $id, "sub_category_id", "sub_category"));
        }

        if (!empty($data['child_category']))
        {
            $childCategory = DigitalProductChildCategories::insert($this->childCategoryData($product, $id));
        }

        $productGallery = DigitalProductGallery::insert($this->prepareProductGalleryData($product, $id));
        $productTag = DigitalProductTags::insert($this->prepareProductTagData($product, $id));

        $productAdditionalFiled = AdditionalField::insertGetId($this->prepareProductAdditionalFieldData($data, $id));

        $productAdditionalCustomFiled = $this->prepareProductAdditionalCustomFieldData($data, $id, $productAdditionalFiled);
        if (!empty($productAdditionalCustomFiled))
        {
            AdditionalCustomField::insert($productAdditionalCustomFiled);
        }

        $productPolicy = DigitalProductRefundPolicies::create([
            'product_id' => $id,
            'refund_description' => purify_html(str_replace(["<script>","</script>", 'script'],"", $product['policy_description']))
        ]);

        return true;
    }

    protected function productInstance($type): Builder
    {
        $product = DigitalProduct::query();
        if($type == "edit"){
            $product->with(["product_category","product_sub_category","product_child_category","tag","tax", "additionalFields", "additionalCustomFields"]);
        }elseif ($type == "single"){
            $product->with(["category","gallery_images","tag","uom","subCategory","childCategory","image","inventory","delivery_option"]);
        }elseif ($type == "list"){
            $product->with(["category","uom" , "subCategory", "childCategory", "brand", "badge" , "image" , "inventory"]);
        }elseif ($type == "search"){
            $product->with(["category","uom" , "subCategory", "childCategory", "brand", "badge" , "image" , "inventory"]);
        }else{
            $product = Product::query()->with(["category" , "subCategory", "childCategory", "brand", "badge" , "image" , "inventory"]);
        }

        return $product;
    }

    private function get_product($type = "single", $id): Model|Builder|null
    {
        // get product instance
        $product = $this->productInstance($type);
        return $product->where("id",$id)->first();
    }

    private function storeProductFile($data)
    {
        $main_file = $data['file'];
        $file_extension = $main_file->getClientOriginalExtension();
        $file_new_name = time().'.'.$file_extension;

        $folder_path = global_assets_path('assets/tenant/uploads/digital-product-file/'.tenant()->id);
        $main_file->move($folder_path, $file_new_name);
        return $file_new_name;
    }

    private function removeProductFile($fileName)
    {
        $file_path = global_assets_path('assets/tenant/uploads/digital-product-file/'.tenant()->id.'/'.$fileName);
        if (!is_dir($file_path) && file_exists($file_path) && is_file($file_path))
        {
            unlink($file_path);
        }

        return true;
    }

    public function productStore($data, $request){
        $product_data = self::ProductData($data);
        $slug = $data['slug'] ?? $data['name'];
        $product_data['slug'] = create_slug(Str::slug($slug), 'DigitalProduct', true, 'DigitalProduct', 'slug');
        $product_data['status_id'] = 1;
        $product_data['regular_price'] = $product_data['accessibility'] == 'free' ? 0 : $product_data['regular_price'];

        $main_file_name = $this->storeProductFile($data);
        $product_data['file'] = $main_file_name;

        \DB::beginTransaction();
        try {
            $product = DigitalProduct::create($product_data);
            \DB::commit();
        } catch (\Exception $e) {
            $this->removeProductFile($main_file_name);
            \DB::rollBack();
        }

        $id = $product->id;
        $product->metaData()->updateOrCreate(["metainfoable_id" => $id],$this->prepareMetaData($data));

        return $this->insert_product_data($data, $id, $data);
    }

    public function productUpdate($data, $id){
        $product_data = self::ProductData($data);
        $product = DigitalProduct::find($id);

        if ($product->slug != $data['slug'])
        {
            $slug = $data['slug'] ?? $data['name'];
            $slug = create_slug(Str::slug($slug), 'DigitalProduct', true, 'DigitalProduct', 'slug');
            $product_data['slug'] = $slug;
        }

        $main_file_name = $product->file;
        if (!empty($product_data['file']))
        {
            $this->removeProductFile($product->file);
            $main_file_name = $this->storeProductFile($product_data);
        }

        $product_data['file'] = $main_file_name;

        \DB::beginTransaction();
        try {
            $product->update($product_data);
            \DB::commit();
        } catch (\Exception $e) {
            $this->removeProductFile($main_file_name);
            \DB::rollBack();
        }

        $product->metaData()->updateOrCreate(["metainfoable_id" => $id],$this->prepareMetaData($data));

        $category = $product?->product_category?->updateOrCreate(["product_id" => $id], $this->productCategoryData($data,$id));

        if (!empty($data['sub_category']))
        {
            $subcategory = $product?->product_sub_category?->updateOrCreate(["product_id" => $id], $this->productCategoryData($data,$id,"sub_category_id","sub_category"));
        }

        // delete product child category
        DigitalProductChildCategories::where("product_id", $id)->delete();
        DigitalProductGallery::where("product_id", $id)->delete();
        DigitalProductTags::where("product_id", $id)->delete();

        if (!empty($data['child_category']))
        {
            $childCategory = DigitalProductChildCategories::insert($this->childCategoryData($data, $id));
        }
        $productGallery = DigitalProductGallery::insert($this->prepareProductGalleryData($data,$id));
        $productTag = DigitalProductTags::insert($this->prepareProductTagData($data,$id));

        AdditionalField::where("product_id", $id)->delete();
        $productAdditionalFiled = AdditionalField::insertGetId($this->prepareProductAdditionalFieldData($data, $id));

        AdditionalCustomField::where("additional_field_id", $productAdditionalFiled)->delete();
        $productAdditionalCustomFiled = $this->prepareProductAdditionalCustomFieldData($data, $id, $productAdditionalFiled);
        if (!empty($productAdditionalCustomFiled))
        {
            AdditionalCustomField::insert($productAdditionalCustomFiled);
        }

        $productPolicy = DigitalProductRefundPolicies::updateOrCreate(
            ['product_id' => $id],
            [
                'product_id' => $id,
                'refund_description' => str_replace('script','', $product_data['refund_description'])
            ]);

        return true;
    }

    public function productClone($id): bool
    {
        $data = array();
        $product = DigitalProduct::findOrFail($id);
        $product_data = self::CloneData($product);

        $newProduct = $product->create($product_data);
        $id = $newProduct->id;


        $metaData = [];
        if ($product?->metaData != null)
        {
            $metaData = [
                'general_title' => $product?->metaData?->title,
                'general_description' => $product?->metaData?->description,
                'facebook_title' => $product?->metaData?->fb_title,
                'facebook_description' => $product?->metaData?->fb_description,
                'facebook_image' => $product?->metaData?->fb_image,
                'twitter_title' => $product?->metaData?->tw_title,
                'twitter_description' => $product?->metaData?->tw_description,
                'twitter_image' => $product?->metaData?->tw_image,
            ];

            $newProduct->metaData()->create(["metainfoable_id" => $id],$this->prepareMetaData($metaData));
        }

        $data['category_id'] = optional($product->category)->id;
        $data['sub_category'] = optional($product->subCategory)->id;
        $data['child_category'] = optional($product->childCategory)->pluck('id');

        $product_gallery = optional($product->product_gallery)->pluck('image_id');
        $data['product_gallery'] = !empty($product_gallery) ? implode('|', $product_gallery) : '';

        $product_tags = optional($product->tag)->pluck('tag_name')->toArray();
        $data['tags'] = !empty($product_tags) ? implode(',', $product_tags) : '';

        $data['badge_id'] = $product?->additionalFields?->badge_id;
        $data['pages'] = $product?->additionalFields?->pages;
        $data['language'] = $product?->additionalFields?->language;
        $data['formats'] = $product?->additionalFields?->formats;
        $data['words'] = $product?->additionalFields?->words;
        $data['tool_used'] = $product?->additionalFields?->tool_used;
        $data['database_used'] = $product?->additionalFields?->database_used;
        $data['compatible_browsers'] = $product?->additionalFields?->compatible_browsers;
        $data['compatible_os'] = $product?->additionalFields?->compatible_os;
        $data['high_resolution'] = $product?->additionalFields?->high_resolution;
        $data['author_id'] = $product?->additionalFields?->author_id;

        $customFields = $product?->additionalCustomFields;

        $index = 0;
        $option_name = [];
        $option_value = [];
        foreach ($customFields as $field)
        {
            $option_name['option_name'][$index] = $field->option_name;
            $option_value['option_value'][$index] = $field->option_value;
            $index++;
        }

        $data['option_name'] = count($option_name) > 0 ? current($option_name) : [];
        $data['option_value'] = count($option_value) > 0 ? current($option_value) : [];

        $data['policy_description'] = $product?->refund_policy?->refund_description;

        return $this->insert_product_data($data, $id, $data);
    }

    protected function destroy($id){
        return DigitalProduct::find($id)->delete();
    }

    protected function trash_destroy($id){
        $product = DigitalProduct::onlyTrashed()->findOrFail($id);
        DigitalProductTags::where('product_id', $product->id)->delete();
        DigitalProductGallery::where('product_id', $product->id)->delete();
        DigitalProductChildCategories::where('product_id', $product->id)->delete();
        DigitalProductSubCategories::where('product_id', $product->id)->delete();
        DigitalProductCategories::where('product_id', $product->id)->delete();

        $additional_field = AdditionalField::where('product_id', $product->id)->first();
        if (!empty($additional_field))
        {
            AdditionalCustomField::where('additional_field_id', $additional_field->id)->delete();
            $additional_field->delete();
        }

        DigitalProductDownload::where('product_id', $product->id)->delete();

        $product->forceDelete();

        return (bool)$product;
    }

    protected function bulk_delete($ids)
    {
        $product = DigitalProduct::whereIn('id' ,$ids)->delete();
        return (bool)$product;
    }

    protected function trash_bulk_delete($ids)
    {
        try {
            DigitalProductTags::whereIn('product_id', $ids)->delete();
            DigitalProductGallery::whereIn('product_id', $ids)->delete();
            DigitalProductChildCategories::whereIn('product_id', $ids)->delete();
            DigitalProductSubCategories::whereIn('product_id', $ids)->delete();
            DigitalProductCategories::whereIn('product_id', $ids)->delete();

            $additional_field = AdditionalField::whereIn('product_id', $ids)->get();
            if (!empty($additional_field))
            {
                $additional_field_ids = $additional_field->pluck('id');
                if (!empty($additional_field_ids))
                {
                    AdditionalCustomField::whereIn('additional_field_id', $additional_field_ids)->delete();
                }

                AdditionalField::whereIn('product_id', $ids)->delete();
            }

            DigitalProductDownload::whereIn('product_id', $ids)->delete();

            $products = DigitalProduct::onlyTrashed()->whereIn('id', $ids)->forceDelete();
        } catch (\Exception $exception) { return false; }

        return (bool)$products;
    }
}
