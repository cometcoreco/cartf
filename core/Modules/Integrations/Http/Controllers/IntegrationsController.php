<?php

namespace Modules\Integrations\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Http\File as HttpFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Modules\PluginManage\Http\Helpers\PluginManageHelpers;

class IntegrationsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
        $this->middleware('permission:integration',['only' => ['index','store','activate']]);
    }

    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        return view("integrations::integrations.index");
    }

    /**
     * Show the form for creating a new resource.
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'data_type' => 'required',
        ]);

        match ($request->data_type){
            "google_analytics" => $this->google_analytics(),
            "google_tag_manager" => $this->google_tag_manager(),
            "facebook_pixels" => $this->facebook_pixels(),
            "adroll_pixels" => $this->adroll_pixels(),
            "whatsapp" => $this->whatsapp(),
            "twakto" => $this->twakto(),
            "crsip" => $this->crsip(),
            "tidio" => $this->tidio(),
            "messenger" => $this->messenger(),
            "instagram" => $this->instagram(),
            "google_captcha_v3" => $this->google_captcha_v3(),
        };

        return back()->with(['msg' => __('Settings updated'),'type' => 'success']);
    }

    private function google_analytics(){
        $req = \request();
        if (is_null(tenant())){
            update_static_option_central('google_analytics_gt4_ID',$req->google_analytics_gt4_ID);
            update_static_option_central('google_analytics_gt4_tenant',$req->google_analytics_gt4_tenant ? 'on' : '');
        }else{
            update_static_option('google_analytics_gt4_ID',$req->google_analytics_gt4_ID);
        }
    }

    private function google_captcha_v3()
    {
        $req = \request();
        if (is_null(tenant())){
            update_static_option_central('site_google_captcha_v3_site_key',$req->site_google_captcha_v3_site_key);
            update_static_option_central('site_google_captcha_v3_secret_key',$req->site_google_captcha_v3_secret_key);
            update_static_option_central('site_google_captcha_v3_tenant',$req->site_google_captcha_v3_tenant ? 'on' : '');
        }else{
            update_static_option('site_google_captcha_v3_site_key',$req->site_google_captcha_v3_site_key);
            update_static_option('site_google_captcha_v3_secret_key',$req->site_google_captcha_v3_secret_key);
        }
    }

    public function activate(Request $request){
        $request->validate([
            'status' => 'nullable',
            'option_name' => "required"
        ]);
        if (is_null(tenant())){
            update_static_option_central($request->option_name,$request->status === 'on' ? 'off' : 'on');
        }else{
            update_static_option($request->option_name,$request->status === 'on' ? 'off' : 'on');
        }

        return response()->json(['msg' => __('Settings Updated'),'type' => 'success']);
    }

    private function google_tag_manager()
    {
        $req = \request();
        if (is_null(tenant())){
            update_static_option_central('google_tag_manager_ID',$req->google_tag_manager_ID);
            update_static_option_central('google_tag_manager_tenant',$req->google_tag_manager_tenant ? 'on' : '');
        }else{
            update_static_option('google_tag_manager_ID',$req->google_tag_manager_ID);
        }
    }

    private function facebook_pixels()
    {
        $req = \request();
        if (is_null(tenant())){
            update_static_option_central('facebook_pixels_id',$req->facebook_pixels_id);
            update_static_option_central('facebook_pixels_tenant',$req->facebook_pixels_tenant ? 'on' : '');
        }else{
            update_static_option('facebook_pixels_id',$req->facebook_pixels_id);
        }
    }

    private function adroll_pixels()
    {
        $req = \request();
        if (is_null(tenant())){
            update_static_option_central('adroll_adviser_id',$req->adroll_adviser_id);
            update_static_option_central('adroll_publisher_id',$req->adroll_publisher_id);
            update_static_option_central('adroll_pixels_tenant',$req->adroll_pixels_tenant ? 'on' : '');
        }else{
            update_static_option('adroll_adviser_id',$req->adroll_adviser_id);
            update_static_option('adroll_publisher_id',$req->adroll_publisher_id);
        }
    }

    private function whatsapp()
    {
        $req = \request();
        update_static_option('whatsapp_mobile_number',$req->whatsapp_mobile_number);
        update_static_option('whatsapp_initial_text',$req->whatsapp_initial_text);

    }

    private function twakto()
    {
        $req = \request();
        update_static_option('twakto_widget_id',$req->twakto_widget_id);
    }

    private function crsip()
    {
        $req = \request();
        update_static_option('crsip_website_id',$req->crsip_website_id);
    }

    private function tidio()
    {
        $req = \request();
        update_static_option('tidio_chat_page_id',$req->tidio_chat_page_id);
    }

    private function messenger()
    {
        $req = \request();
        update_static_option('messenger_page_id',$req->messenger_page_id);
    }

    private function instagram()
    {
        $req = \request();
        update_static_option('instagram_access_token',$req->instagram_access_token);
    }
}
