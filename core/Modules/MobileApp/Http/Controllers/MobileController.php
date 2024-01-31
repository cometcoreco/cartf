<?php

namespace Modules\MobileApp\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Models\PaymentGateway;
use Illuminate\Http\Request;

class MobileController extends Controller
{
    public function termsAndCondition(){
        $selected_page = get_static_option("mobile_terms_and_condition");

        $page = Page::where('slug', $selected_page)->select( "title","page_content")->first();

        return response()->json($page);
    }

    public function privacyPolicy(){
        $selected_page = get_static_option("mobile_privacy_and_policy");

        $page = Page::where('slug', $selected_page)->select( "title","page_content")->first();
        return response()->json($page);
    }

    public function site_currency_symbol(){
        $is_rtl_on_or_not = get_user_lang_direction() == 1 ?? false;

        return response()->json(["symbol" => site_currency_symbol(),"currencyPosition" => get_static_option('site_currency_symbol_position'),
            "rtl" => $is_rtl_on_or_not]);
    }

    public function paymentMethodList()
    {
        $payment_gateways = PaymentGateway::where('status', 1)->select('id', 'name', 'image', 'description')->get();
        return response()->json(['data' => $payment_gateways]);
    }

    public function permission()
    {
        $permission = false;
        $current_tenant_payment_data = tenant()->payment_log ?? []; // Getting the tenant payment log

        if (!empty($current_tenant_payment_data)) // If the tenant subscribed to any plan and if the route has the permission name
        {
            $package = $current_tenant_payment_data?->package;

            if (!empty($package))
            {
                $features = $package?->plan_features?->pluck('feature_name')->toArray();
                $permission = in_array('app_api', (array)$features);
            }
        }

        return response()->json(['permission' => $permission]);
    }
}
