<?php

namespace Modules\WebHook\Http\Services;

use App\Enums\ProductTypeEnum;
use App\Enums\StatusEnums;
use App\Helpers\SanitizeInput;
use App\Http\Services\HandleImageUploadService;
use Automattic\WooCommerce\Client;
use Illuminate\Support\Str;
use Modules\Attributes\Entities\Category;
use Modules\Product\Http\Services\Admin\AdminProductServices;
use Modules\Product\Http\Traits\ProductGlobalTrait;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class WooCommerceService
{
    use ProductGlobalTrait;

    private Client $woocommerce_client;
    const image_path = 'assets/uploads/temp-images/';

    public function __construct()
    {
        $site_url = get_static_option('woocommerce_site_url') ?? '';
        $consumer_key = get_static_option('woocommerce_consumer_key') ?? '';
        $consumer_secret = get_static_option('woocommerce_consumer_secret') ?? '';

        $this->woocommerce_client = \Cache::remember('woocommerce_client', 15, function () use ($site_url, $consumer_key, $consumer_secret) {
            return new Client($site_url, $consumer_key, $consumer_secret, ['version' => 'wc/v3']);
        });
    }

    public function getProducts()
    {
        try {
            return $this->woocommerce_client->get('products');
        } catch (\Exception $exception) {
            return $exception->getCode();
        }
    }

    public function prepareProducts($products)
    {
        $count = 0;
        $productArr = [];
        foreach ($products ?? [] as $product) {
            if (count($product->variations) > 0) {
                continue;
            }

            $productArr[$count]['name'] = $product->name;
            $productArr[$count]['slug'] = $product->slug;
            $productArr[$count]['summery'] = preg_replace("/\r|\n/", "", $product->short_description);
            $productArr[$count]['description'] = preg_replace("/\r|\n/", "", $product->description);

//            foreach ($product->categories ?? [] as $category) {
            $productArr[$count]['categories'] = current($product->categories)->name;
//            }

            $productArr[$count]['tags'] = [];
            foreach ($product->tags ?? [] as $tag) {
                $productArr[$count]['tags'][] = $tag->name;
            }

            $productArr[$count]['cost'] = $product->price;
            $productArr[$count]['price'] = $product->regular_price ?? $product->price;
            $productArr[$count]['sale_price'] = $product->sale_price;

            $productArr[$count]['status_id'] = $product->status == 'publish' ? StatusEnums::PUBLISH : 2;
            $productArr[$count]['product_type'] = ProductTypeEnum::PHYSICAL;

            $productArr[$count]['sku'] = $product->sku;
            $productArr[$count]['unit_id'] = get_static_option('woocommerce_default_unit') ?? 6;
            $productArr[$count]['uom'] = get_static_option('woocommerce_default_uom') ?? 1;
            $productArr[$count]['quantity'] = $product->stock_quantity ?? 0;

            foreach ($product->images ?? [] as $image_index => $each_image) {
                if ($image_index == 0) {
                    $productArr[$count]['primary_image_data'] = $this->downloadImageFromURL($each_image->src, $each_image->name);
                } else {
                    $productArr[$count]['gallery_image_data'][] = $this->downloadImageFromURL($each_image->src, $each_image->name);
                }
            }

            $count++;
        }

        return $productArr;
    }

    public function getSelectiveProducts($ids)
    {
        $products = collect($this->getProducts());
        return $products->whereIn('id', $ids) ?? [];
    }

    public function filterForStore($arr_data)
    {
        $image_data = $this->getImageIds($arr_data);

        $product = [
            'name' => $arr_data['name'],
            'slug' => $arr_data['slug'],
            'summery' => $arr_data['summery'],
            'description' => $arr_data['description'],
            'cost' => $arr_data['cost'],
            'price' => $arr_data['price'],
            'sale_price' => !empty($arr_data['sale_price']) ? $arr_data['sale_price'] : $arr_data['price'],
            'status_id' => StatusEnums::PUBLISH,
            'product_type' => ProductTypeEnum::PHYSICAL,
            'sku' => create_slug($arr_data['sku'] ?? 'default', 'ProductInventory', true, 'Product', 'sku'),
            'quantity' => $arr_data['quantity'] ?? 0,
            'unit_id' => $arr_data['unit_id'],
            'uom' => $arr_data['uom'],
            'image_id' => $image_data['primary_image_id'],
            'product_gallery' => $image_data['gallery_image_id'],
            'tags' => $this->getTags($arr_data['tags']),
            'category_id' => $this->getOrCreateCategory($arr_data['categories']),
            'sub_category' => null,
            'badge_id' => null,
            'brand' => null,
            'min_purchase' => null,
            'max_purchase' => null,
            'is_inventory_warn_able' => null,
            'item_stock_count' => [0],

            'delivery_option' => null,
            'policy_description' => null,

            'general_title' => '',
            'general_description' => '',
            'facebook_title' => '',
            'facebook_description' => '',
            'facebook_image' => '',
            "twitter_title" => '',
            "twitter_description" => '',
            "twitter_image" => ''
        ];

        return (new AdminProductServices())->store($product);
    }

    private function getTags($tags): ?string
    {
        $tags_text = null;
        foreach ($tags as $index => $tag) {
            $tags_text .= $tag . (count($tags) > 1 && $index < count($tags) - 1 ? ',' : '');
        }

        return $tags_text;
    }

    private function getOrCreateCategory($category_name)
    {
        $category = null;
//        foreach ($arr_data ?? [] as $category_name) {
            $category = Category::where('name', $category_name)->first();
            if (!$category) {
                $category = $this->createCategory($category_name);
            }
//        }

        return $category ? $category->id : null;
    }

    private function createCategory($category_name)
    {
        $data['name'] = esc_html($category_name);

        $text = preg_replace('/[^\w-]+/', '', str_replace(' ', '-', strtolower($category_name)));
        $text = preg_replace(['/\bamp\b|&amp;/', '/-+/'], '-', $text);

        $sluggable_text = Str::slug(trim($text));
        $slug = create_slug($sluggable_text, model_name: 'category', is_module: true, module_name: 'attributes');
        $data['slug'] = $slug;

        return Category::create($data);
    }

    private function getImageIds($arr_data): array
    {
        $primary_image_id = '';
        $gallery_image_ids = '';
//        foreach ($arr_data ?? [] as $data) {
            if (!empty($arr_data['primary_image_data'])) {
                $primary_image_id = $this->uploadImage($arr_data['primary_image_data']);
            }

            if (array_key_exists('gallery_image_data', $arr_data) && !empty($arr_data['gallery_image_data'])) {
                $gallery = $arr_data['gallery_image_data'];
                foreach ($gallery as $index => $image) {
                    $gallery_image_ids .= $this->uploadImage($image) . (count($gallery) > 1 && $index < count($gallery) - 1 ? '|' : '');
                }
            }
//        }

        return [
            'primary_image_id' => $primary_image_id,
            'gallery_image_id' => $gallery_image_ids
        ];
    }

    public function downloadImageFromURL($url, $file_name = 'image'): array
    {
        $image_url = $url;
        $image_data_arr = explode('.', $file_name);
        $image_name = current($image_data_arr);
        $image_extension = last($image_data_arr);

        // Fetch the image content
        $image_content = file_get_contents($image_url);

        // Create temp-images directory
        $dir_path = 'assets/uploads/temp-images';
        if (!\File::exists($dir_path))
        {
            @mkdir($dir_path, 0777);
        }

        // Generate a unique filename for the stored image
        $unique_filename = uniqid($image_name . '-');
        $unique_filename_with_extension = $unique_filename . '.' . $image_extension;
        $store_path = $dir_path . '/' . $unique_filename_with_extension;

        try {
            file_put_contents($store_path, $image_content);
            $status = true;
        } catch (\Exception $exception) {
            $status = false;
        }

        return $status ? [
            'status' => true,
            'image_name' => $unique_filename_with_extension,
            'image_name_extension' => $unique_filename_with_extension,
            'image_object' => $this->getImageAsObject($unique_filename_with_extension)
        ] : ['status' => false];
    }

    public function uploadImage($file_data)
    {
        if ($file_data['status']) {
            $image_name = $file_data['image_name'];
            $image_object = $file_data['image_object'];
            $image_name_with_extension = $file_data['image_name_extension'];
            $folder_path = global_assets_path('assets/tenant/uploads/media-uploader/' . tenant()->id . '/');
            $request['file'] = $image_object;
            $request['user_type'] = 'admin';
            $request = (object)$request;

            try {
                $uploaded_id = HandleImageUploadService::handle_image_upload
                (
                    $image_name,
                    $image_object,
                    $image_name_with_extension,
                    $folder_path,
                    $request,
                    true,
                    self::image_path
                );
            } catch (\Exception $exception) {
                $uploaded_id = null;
            }

            return $uploaded_id;
        }

        return null;
    }

    private function getImageAsObject($image_name): ?UploadedFile
    {
        $file = null;
        $path = self::image_path . $image_name;
        if (file_exists($path)) {
            $file = new UploadedFile($path, $image_name);
        }

        return $file;
    }
}
