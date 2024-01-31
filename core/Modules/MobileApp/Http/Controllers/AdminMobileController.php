<?php

namespace Modules\MobileApp\Http\Controllers;

use App\Helpers\FlashMsg;
use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\Request;

class AdminMobileController extends Controller
{
    public function __construct()
    {
        $this->middleware("auth:admin");
    }

    public function terms_and_condition(){
        $pages = Page::select("id","slug","title")->get();

        return view("mobileapp::mobile-controller.terms_and_condition", compact("pages"));
    }

    public function update_terms_and_condition(Request $request){
        update_static_option("mobile_terms_and_condition", $request->page);

        return redirect()->back()->with(FlashMsg::update_succeed(__("Terms and condition")));
    }

    public function privacy_and_policy(){
        $pages = Page::select("id","slug","title")->get();

        return view("mobileapp::mobile-controller.privacy_policy", compact("pages"));
    }

    public function update_privacy_and_policy(Request $request){
        update_static_option("mobile_privacy_and_policy", $request->page);

        return redirect()->back()->with(FlashMsg::update_succeed(__("Privacy and policy")));
    }
}
