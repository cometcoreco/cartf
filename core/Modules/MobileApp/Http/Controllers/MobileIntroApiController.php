<?php

namespace Modules\MobileApp\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\MobileApp\Entities\MobileIntro;
use Modules\MobileApp\Http\Resources\MobileIntroResource;

class MobileIntroApiController extends Controller
{
    public function mobileIntro(){
        return MobileIntroResource::collection(MobileIntro::all());
    }
}