<?php

namespace Modules\MobileApp\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Language;
use Illuminate\Http\Request;


class LanguageController extends Controller
{
    public function languageInfo(){

        $languages = Language::select('id','name','slug','direction')->where('default',1)->first()->toArray();

        if(!is_null($languages)){
            return response()->json([
                'language'=>$languages,
            ]);
        }

        return response()->json([
                'language'=> [
                    "slug" => "en_GB",
                    "direction" => "ltr"
                ],
        ]);
    }

    public function translateString(Request $request){
        $translatable_array = json_decode($request->get('strings'),true);

        $translated_array = [];
        if($request->has('strings')){
            foreach($translatable_array as $key => $string){
                $translated_array[$key] = __($key);
            }
        }

        return response()->json([
            'strings'=> $translated_array
        ]);
    }
}
