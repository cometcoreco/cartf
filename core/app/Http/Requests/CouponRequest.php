<?php

namespace App\Http\Requests;

use App\Enums\LandlordCouponType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use phpDocumentor\Reflection\Types\This;

class CouponRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            "name" => "required",
            "code" => "required|unique:coupons,code",
            "description" => "nullable",
            "discount_amount" => "required",
            "discount_type" => "required|".Rule::in(LandlordCouponType::getCouponTypeValues()),
            "expire_date" => "nullable",
            "status" => "required|". Rule::in([0,1])
        ];
    }

    public function messages()
    {
        return [
            "discount_amount.required" => __('The discount field is required.')
        ];
    }
}
