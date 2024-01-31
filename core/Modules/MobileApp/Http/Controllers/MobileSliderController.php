<?php

namespace Modules\MobileApp\Http\Controllers;

use Illuminate\Routing\Controller;
use Modules\Attributes\Entities\Category;
use Modules\Campaign\Entities\Campaign;
use Modules\MobileApp\Entities\MobileSlider;
use Modules\MobileApp\Http\Requests\MobileSlider\StoreMobileSliderRequest;
use Modules\Product\Entities\ProductCategory;

class MobileSliderController extends Controller
{
    public function __construct()
    {
        $this->middleware("auth:admin");
    }

    public function index(){
        $mobileSliders = MobileSlider::where("type", 1)->orderBy('id', 'desc')->paginate(20);
        // now return only view

        return view("mobileapp::mobile-slider.list",compact("mobileSliders"));
    }

    public function create(){
        // todo:: first i need to get all campaigns first
        $campaigns = Campaign::select("id","title as name")->get();
        // todo:: now fetch all category
        $categories = Category::select("id","name")->get();

        return view("mobileapp::mobile-slider.create",compact("campaigns", "categories"));
    }

    public function store(StoreMobileSliderRequest $request){
        $data = $request->validated();

        $mobileSlider = MobileSlider::create($data + ["type" => 1]);

        return redirect(route("tenant.admin.mobile.slider.all"))->with(["success" => $mobileSlider ?? false,"msg" => __("Successfully Created Mobile Slider")]);
    }

    public function destroy(MobileSlider $mobileSlider){
        $bool = $mobileSlider->delete();
        return response()->json(["success" => $bool ?? false]);
    }

    public function edit(MobileSlider $mobileSlider){
        $campaigns = Campaign::select("id","title as name")->get();
        $categories = Category::select("id","name")->get();

        return view("mobileapp::mobile-slider.edit", compact("mobileSlider","campaigns", "categories"));
    }

    public function update(MobileSlider $mobileSlider, StoreMobileSliderRequest $request){
        $data = $request->validated();
        $bool = $mobileSlider->update($data);

        return redirect(route("tenant.admin.mobile.slider.all"))->with(["success" => $bool ?? false,"msg" => __("Successfully updated mobile slider")]);
    }

    public function two_index(){
        $mobileSliders = MobileSlider::where("type", 2)->paginate(20);
        // now return only view
        return view("mobileapp::mobile-slider-two.list",compact("mobileSliders"));
    }

    public function two_create(){
        $campaigns = Campaign::select("id","title as name")->get();
        $categories = Category::select("id","name")->get();

        return view("mobileapp::mobile-slider-two.create",compact("campaigns", "categories"));
    }

    public function two_store(StoreMobileSliderRequest $request){
        $data = $request->validated();

        $mobileSlider = MobileSlider::create($data + ["type" => 2]);

        return redirect(route("admin.mobile.slider.two.all"))->with(["success" => $mobileSlider ?? false,"msg" => "Successfully Created Mobile Slider"]);
    }

    public function two_destroy(MobileSlider $mobileSlider){
        $bool = $mobileSlider->delete();
        return response()->json(["success" => $bool ?? false]);
    }

    public function two_edit(MobileSlider $mobileSlider){
        $campaigns = Campaign::select("id","title as name")->get();
        $categories = Category::select("id","name")->get();

        return view("mobileapp::mobile-slider-two.edit", compact("mobileSlider","campaigns","categories"));
    }

    public function two_update(MobileSlider $mobileSlider,StoreMobileSliderRequest $request){
        $data = $request->validated();
        $bool = $mobileSlider->update($data);

        return redirect(route("admin.mobile.slider.two.all"))->with(["success" => $bool ?? false,"msg" => "Successfully updated mobile slider"]);
    }

    public function three_index(){
        $mobileSliders = MobileSlider::where("type", 3)->paginate(20);
        // now return only view
        return view("mobileapp::mobile-slider-three.list",compact("mobileSliders"));
    }

    public function three_create(){
        $campaigns = Campaign::select("id","title as name")->get();
        $categories = Category::select("id","name")->get();

        return view("mobileapp::mobile-slider-three.create",compact("campaigns", "categories"));
    }

    public function three_store(StoreMobileSliderRequest $request){
        $data = $request->validated();

        $mobileSlider = MobileSlider::create($data + ["type" => 3]);

        return redirect(route("admin.mobile.slider.three.all"))->with(["success" => $mobileSlider ?? false,"msg" => "Successfully Created Mobile Slider"]);
    }

    public function three_destroy(MobileSlider $mobileSlider){
        $bool = $mobileSlider->delete();
        return response()->json(["success" => $bool ?? false]);
    }

    public function three_edit(MobileSlider $mobileSlider){
        $campaigns = Campaign::select("id","title as name")->get();
        $categories = Category::select("id","name")->get();

        return view("mobileapp::mobile-slider-three.edit", compact("mobileSlider","campaigns","categories"));
    }

    public function three_update(MobileSlider $mobileSlider,StoreMobileSliderRequest $request){
        $data = $request->validated();
        $bool = $mobileSlider->update($data);

        return redirect(route("admin.mobile.slider.three.all"))->with(["success" => $bool ?? false,"msg" => "Successfully updated mobile slider"]);
    }
}
