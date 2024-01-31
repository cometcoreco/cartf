<?php

namespace Modules\DigitalProduct\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\Attributes\Entities\Category;
use Modules\DigitalProduct\Entities\DigitalCategories;

class DigitalProductUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            "name" => "required",
            "slug" => "required|unique:digital_products,id," . $this->id ?? 0,
            "summary" => "required",
            "description" => "required",
            "included_files" => "nullable",
            "version" => "nullable",
            "release_date" => "nullable|date",
            "update_date" => "nullable|date",
            "preview_link" => "nullable",
            "quantity" => "nullable",
            "accessibility" => "required",
            "tax" => "nullable",
            "price" => "required|numeric",
            "sale_price" => "nullable|numeric",
            "free_date" => "nullable|date",
            "promotional_date" => "nullable|date",
            "promotional_price" => "nullable|numeric",
            "author_id" => "nullable",
            "page" => "nullable|integer",
            "language" => "nullable",
            "formats" => "nullable",
            "word" => "nullable|integer",
            "tool_used" => "nullable",
            "database_used" => "nullable",
            "compatible_browsers" => "nullable",
            "compatible_os" => "nullable",
            "high_resolution" => "nullable",

            "option_name" => "nullable|array",
            "option_value" => "nullable|array",

            "image_id" => "required",
            "product_gallery" => "nullable",
            "tags" => "required",
            "badge_id" => "nullable",

            "category_id" => "required",
            "sub_category" => "nullable",
            "child_category" => "nullable",

            "file" => "nullable",

            "general_title" => "nullable",
            "general_description" => "nullable",
            "general_image" => "nullable",
            "facebook_title" => "nullable",
            "facebook_description" => "nullable",
            "facebook_image" => "nullable",
            "twitter_title" => "nullable",
            "twitter_description" => "nullable",
            "twitter_image" => "nullable",
            "is_refundable" => "nullable",
            "policy_description" => "nullable",
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    public function messages(): array
    {
        return [
            "name.required" => __('Product name field is required'),
            "price.required" => __("Regular price is required."),
        ];
    }
}
