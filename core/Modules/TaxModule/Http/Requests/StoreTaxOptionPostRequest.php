<?php

namespace Modules\TaxModule\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTaxOptionPostRequest extends FormRequest
{
    public function rules(): array
    {
        return [
//            'class_id' => "nullable",
            'tax_name' => "required|array",
            'tax_name.*' => "required|string",
            'country_id' => "nullable",
            'state_id' => "nullable",
            'city_id' => "nullable",
            'postal_code' => "nullable",
            'priority' => "required|array",
            'priority.*' => "required|string",
            'is_compound' => "nullable",
            'is_shipping' => "nullable",
            'rate' => "nullable",
        ];
    }

    public function authorize(): bool
    {
        return true;
    }

    public function messages()
    {
        return [
            'tax_name.*' => __('Each tax name must not be empty'),
            'priority.*' => __('Each priority must not be empty')
        ];
    }
}
