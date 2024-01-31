<?php

namespace App\Http\Requests;

use Cart;
use Illuminate\Foundation\Http\FormRequest;
use Modules\ShippingPlugin\Http\Rules\CustomValidatorRules;

class CheckoutFormRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        $arr = [];
        if ($this->shift_another_address == 'on') {
            $arr = [
                'shift_name' => "required",
                'shift_phone' => "required",
                'shift_email' => "required|email",
                'shift_country' => "required|numeric",
                'shift_state' => "required|numeric",
                'shift_city' => "nullable|numeric",
                'shift_address' => "required"
            ];
        } else {
            if (\Auth::guard('web')->user() == null) {
                $arr = [
                    'name' => "required",
                    'phone' => "required|max:17",
                    'email' => "required|email",
                    'country' => "required|numeric",
                    'state' => "required|numeric",
                    'city' => "nullable",
                    'address' => "required"
                ];

                $arr['create_accounts_input'] = 'nullable';
                if ($this->create_accounts_input != null)
                {
                    $arr['create_username'] = 'required';
                    $arr['create_password'] = 'required|same:create_password_confirmation|min:8';
                }
            } else {
                if(\Auth::guard('web')->user()->delivery_address == null)
                {
                    $arr = [
                        'name' => "required",
                        'phone' => "required",
                        'email' => "required|email", // user unique email needed?
                        'country' => "required|numeric",
                        'state' => "required|numeric",
                        'city' => "required|nullable",
                        'address' => "required"
                    ];
                }
            }
        }


        $arr['cash_on_delivery'] = 'nullable';
        if ($this->cash_on_delivery == null)
        {
            $arr['payment_gateway'] = 'required';
        }

        if ($this->payment_gateway == 'manual_payment') {
            $arr['manual_trasaction_id'] = 'required';
        }

        $arr['used_coupon'] = 'nullable';
        $arr['shipping_method'] = 'nullable';
        $arr['shift_another_address'] = 'nullable';
        $arr['message'] = 'nullable';

        $customRules = $this->customRules();
        return array_merge($arr, $customRules['rules']);
    }

    public function messages()
    {
        $customRules = $this->customRules();
        return [
            'shift_name.required' => __('Name field is required.'),
            'shift_phone.required' => __('Phone field is required.'),
            'shift_email.email' => __('Email field must be valid email.'),
            'shift_country.required' => __('Country field is required.'),
            'shift_state.required' => __('State field is required.'),
            'shift_city.required' => __('City field is required.'),
            'shift_address.required' => __('Address field is required.'),

            'name.required' => __('Name field is required.'),
            'phone.required' => __('Phone field is required.'),
            'country.required' => __('Country field is required.'),
            'state.required' => __('State field is required.'),
            'city.required' => __('City field is required.'),
            'address.required' => __('Address field is required.'),

            'create_username.required' => __('Name field is required.'),
            'create_password.required' => __('Password field is required.'),
            'create_password.same' => __('Password and password confirmation must match.'),

            'manual_trasaction_id.required' => __('Transaction ID is required.'),
            'payment_gateway.required' => __('Payment Gateway is required.')
        ] + $customRules['messages'];
    }

    private function customRules()
    {
        $rules_messages['rules']['postal_code'] = 'nullable|numeric';
        $rules_messages['messages'] = [];

        if (moduleExists('ShippingPlugin') && isPluginActive('ShippingPlugin'))
        {
            if (moduleExists('ShippingPlugin') && isPluginActive('ShippingPlugin'))
            {
                $class = CustomValidatorRules::class;
                if (class_exists($class))
                {
                    $rules_messages = (new $class)->customRules() ?? $rules_messages;
                }
            }
        }

        return $rules_messages;
    }
}
