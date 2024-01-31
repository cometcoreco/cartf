<?php

namespace App\Http\Controllers\Landlord\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaymentGateway;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class PaymentSettingsController extends Controller
{
    const BASE_PATH = 'landlord.admin.payment-settings.';

    public function __construct()
    {
        $this->middleware('permission:payment-settings-paypal', ['only' => [
            'paypal_settings', 'update_payment_settings']]);
        $this->middleware('permission:payment-settings-paytm', ['only' => [
            'paytm_settings', 'update_payment_settings']]);
        $this->middleware('permission:payment-settings-stripe', ['only' => [
            'stripe_settings', 'update_payment_settings']]);
        $this->middleware('permission:payment-settings-razorpay', ['only' => [
            'razorpay_settings', 'update_payment_settings']]);
        $this->middleware('permission:payment-settings-paystack', ['only' => [
            'paystack_settings', 'update_payment_settings']]);
        $this->middleware('permission:payment-settings-mollie', ['only' => [
            'mollie_settings', 'update_payment_settings']]);
        $this->middleware('permission:payment-settings-midtrans', ['only' => [
            'midtrans_settings', 'update_payment_settings']]);
        $this->middleware('permission:payment-settings-cashfree', ['only' => [
            'cashfree_settings', 'update_payment_settings']]);
        $this->middleware('permission:payment-settings-instamojo', ['only' => [
            'instamojo_settings', 'update_payment_settings']]);
        $this->middleware('permission:payment-settings-marcadopago', ['only' => [
            'marcadopago_settings', 'update_payment_settings']]);
        $this->middleware('permission:payment-settings-zitopay', ['only' => [
            'zitopay_settings', 'update_payment_settings']]);
        $this->middleware('permission:payment-settings-squareup', ['only' => [
            'squareup_settings', 'update_payment_settings']]);
        $this->middleware('permission:payment-settings-cinetpay', ['only' => [
            'cinetpay_settings', 'update_payment_settings']]);
        $this->middleware('permission:payment-settings-paytabs', ['only' => [
            'paytabs_settings', 'update_payment_settings']]);
        $this->middleware('permission:payment-settings-billplz', ['only' => [
            'billplz_settings', 'update_payment_settings']]);
        $this->middleware('permission:payment-settings-toyyibpay', ['only' => [
            'toyyibpay_settings', 'update_payment_settings']]);
        $this->middleware('permission:payment-settings-flutterwave', ['only' => [
            'flutterwave_settings', 'update_payment_settings']]);
        $this->middleware('permission:payment-settings-payfast', ['only' => [
            'payfast_settings', 'update_payment_settings']]);
        $this->middleware('permission:payment-settings-manual_payment', ['only' => [
            'manual_payment_settings', 'update_payment_settings']]);
        $this->middleware('permission:general-settings-payment-settings', ['only' => [
            'cod_settings', 'update_payment_settings']]);
    }

    public function paypal_settings()
    {
        $gateway = PaymentGateway::where('name', 'paypal')->first();
        return view(self::BASE_PATH . 'payment_settings', compact('gateway'));
    }

    public function paytm_settings()
    {
        $gateway = PaymentGateway::where('name', 'paytm')->first();
        return view(self::BASE_PATH . 'payment_settings', compact('gateway'));
    }

    public function stripe_settings()
    {
        $gateway = PaymentGateway::where('name', 'stripe')->first();
        return view(self::BASE_PATH . 'payment_settings', compact('gateway'));
    }

    public function razorpay_settings()
    {
        $gateway = PaymentGateway::where('name', 'razorpay')->first();
        return view(self::BASE_PATH . 'payment_settings', compact('gateway'));
    }

    public function paystack_settings()
    {
        $gateway = PaymentGateway::where('name', 'paystack')->first();
        return view(self::BASE_PATH . 'payment_settings', compact('gateway'));
    }

    public function mollie_settings()
    {
        $gateway = PaymentGateway::where('name', 'mollie')->first();
        return view(self::BASE_PATH . 'payment_settings', compact('gateway'));
    }

    public function midtrans_settings()
    {
        $gateway = PaymentGateway::where('name', 'midtrans')->first();
        return view(self::BASE_PATH . 'payment_settings', compact('gateway'));
    }

    public function cashfree_settings()
    {
        $gateway = PaymentGateway::where('name', 'cashfree')->first();
        return view(self::BASE_PATH . 'payment_settings', compact('gateway'));
    }

    public function instamojo_settings()
    {
        $gateway = PaymentGateway::where('name', 'instamojo')->first();
        return view(self::BASE_PATH . 'payment_settings', compact('gateway'));
    }

    public function marcadopago_settings()
    {
        $gateway = PaymentGateway::where('name', 'marcadopago')->first();
        return view(self::BASE_PATH . 'payment_settings', compact('gateway'));
    }

    public function zitopay_settings()
    {
        $gateway = PaymentGateway::where('name', 'zitopay')->first();
        return view(self::BASE_PATH . 'payment_settings', compact('gateway'));
    }

    public function squareup_settings()
    {
        $gateway = PaymentGateway::where('name', 'squareup')->first();
        return view(self::BASE_PATH . 'payment_settings', compact('gateway'));
    }

    public function cinetpay_settings()
    {
        $gateway = PaymentGateway::where('name', 'cinetpay')->first();
        return view(self::BASE_PATH . 'payment_settings', compact('gateway'));
    }

    public function paytabs_settings()
    {
        $gateway = PaymentGateway::where('name', 'paytabs')->first();
        return view(self::BASE_PATH . 'payment_settings', compact('gateway'));
    }

    public function billplz_settings()
    {
        $gateway = PaymentGateway::where('name', 'billplz')->first();
        return view(self::BASE_PATH . 'payment_settings', compact('gateway'));
    }

    public function toyyibpay_settings()
    {
        $gateway = PaymentGateway::where('name', 'toyyibpay')->first();
        return view(self::BASE_PATH . 'payment_settings', compact('gateway'));
    }

    public function flutterwave_settings()
    {
        $gateway = PaymentGateway::where('name', 'flutterwave')->first();
        return view(self::BASE_PATH . 'payment_settings', compact('gateway'));
    }

    public function payfast_settings()
    {
        $gateway = PaymentGateway::where('name', 'payfast')->first();
        return view(self::BASE_PATH . 'payment_settings', compact('gateway'));
    }

    public function manual_payment_settings()
    {
        $gateway = PaymentGateway::where('name', 'manual_payment')->first();
        return view(self::BASE_PATH . 'payment_settings', compact('gateway'));
    }

    public function cod_settings()
    {
        $gateway = PaymentGateway::where('name', 'manual_payment')->first();
        $cod = true;
        return view(self::BASE_PATH . 'payment_settings', compact('gateway', 'cod'));
    }

    public function update_payment_settings(Request $request)
    {
        $request->validate([
            'gateway_name' => 'required'
        ]);

        if ($request->gateway_name == 'cash_on_delivery')
        {
            update_static_option('cash_on_delivery', $request->cash_on_delivery);
        } else {
            $gateway = PaymentGateway::where('name', $request->gateway_name)->first();

            // todo: if manual payament gatewya then save description into database
            $image_name = $gateway->name . '_logo';
            $status_name = $gateway->name . '_gateway';
            $test_mode_name = $gateway->name . '_test_mode';

            $credentials = !empty($gateway->credentials) ? json_decode($gateway->credentials) : [];
            $update_credentials = [];
            foreach ($credentials as $cred_name => $cred_val) {
                $crd_req_name = $gateway->name . '_' . $cred_name;
                $update_credentials[$cred_name] = $request->$crd_req_name;
            }

            PaymentGateway::where(['name' => $gateway->name])->update([
                'image' => $request->$image_name,
                'status' => isset($request->$status_name) ? 1 : 0,
                'test_mode' => isset($request->$test_mode_name) ? 1 : 0,
                'credentials' => json_encode($update_credentials)
            ]);
        }

        Artisan::call('cache:clear');
        return redirect()->back()->with([
            'msg' => __('Payment Settings Updated..'),
            'type' => 'success'
        ]);
    }
}
