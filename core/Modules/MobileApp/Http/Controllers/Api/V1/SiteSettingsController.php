<?php

namespace Modules\MobileApp\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\PaymentGateway;
use Illuminate\Http\Request;
use Modules\MobileApp\Http\Resources\PaymentGatewayResource;

class SiteSettingsController extends Controller
{
    public function payment_gateway_list(Request $request)
    {
        if ($request->header("x-api-key") !== "b8f4a0ba4537ad6c3ee41ec0a43549d1") {
            return response()->json(["error" => "Unauthenticated."], 401);
        }

        $payment_gateways = PaymentGateway::where('status' , 1)->get()->toArray();

        $cash_on_delivery_option = get_static_option('cash_on_delivery');
        $cash_on_delivery = [];
        if (!empty($cash_on_delivery_option))
        {
            $index = !empty($payment_gateways) ? count($payment_gateways) + 1 : 0;
            $id = !empty($payment_gateways) ? data_get(max($payment_gateways), 'id') + 1 : 0;

            $cash_on_delivery[$index] = [
                'id' => $id,
                'name' => 'cash_on_delivery',
                'description' => '',
                'image' => '',
                'status' => 1,
                'test_mode' => 1,
                'credentials' => ''
            ];
        }

        $merged = collect(array_merge($payment_gateways, $cash_on_delivery));

        return PaymentGatewayResource::collection($merged);
    }
}
