<?php

namespace Modules\MobileApp\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Modules\MobileApp\Http\Resources\MobileSlider\MobileSliderResource;
use Modules\MobileApp\Entities\MobileSlider;

class MobileSliderController extends Controller
{
    public function index(){
         return MobileSliderResource::collection(MobileSlider::with("sliderCategory:id,name")->get());
    }
}
