<?php

namespace Modules\MobileApp\Http\Controllers\Api\V1;

use FontLib\Table\Type\name;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\CountryManage\Entities\City;
use Modules\CountryManage\Entities\Country;
use Modules\CountryManage\Entities\State;

class CountryController extends Controller
{
    /*
    * fetch all country list from database
    */
    public function country()
    {
        $country = Country::select('id', 'name')->orderBy('name', 'asc')->paginate(10);

        return response()->json([
            'countries' => $country
        ]);
    }

    /*
    * fetch all state list based on provided country id from database
    */
    public function stateByCountryId($id)
    {
        if(empty($id)){
            return response()->json([
                'message' => __('provide a valid country id')
            ])->setStatusCode(422);
        }

        $state = State::select('id', 'name','country_id')->where('country_id',$id)->orderBy('name', 'asc')->paginate(10);

        return response()->json([
            'state' => $state
        ]);
    }

    public function cityByStateId($id)
    {
        if(empty($id)){
            return response()->json([
                'message' => __('provide a valid state id')
            ])->setStatusCode(422);
        }

        $cities = City::select('id', 'name','state_id')->where('state_id',$id)->orderBy('name', 'asc')->paginate(10);

        return response()->json([
            'cities' => $cities
        ]);
    }

    public function searchCountry($name)
    {
        if(empty($name)){
            return response()->json([
                'message' => __('provide a valid country name')
            ])->setStatusCode(422);
        }

        $country = Country::where('name', 'LIKE', '%'.$name.'%')->select('id', 'name')->orderBy('name', 'asc')->paginate(10);

        return response()->json([
            'countries' => $country
        ]);
    }

    public function searchState($name)
    {
        if(empty($name)){
            return response()->json([
                'message' => __('provide a valid state name')
            ])->setStatusCode(422);
        }

        $state = State::where('name', 'LIKE', '%'.$name.'%')->select('id', 'name','country_id')->orderBy('name', 'asc')->paginate(10);

        return response()->json([
            'state' => $state
        ]);
    }

    public function searchCity($id, $name)
    {
        if(empty($id) && empty($name)){
            return response()->json([
                'message' => __('provide a valid city name')
            ])->setStatusCode(422);
        }

        $city = City::where('state_id', $id)->where('name', 'LIKE', '%'.$name.'%')->select('id', 'name','state_id')->orderBy('name', 'asc')->paginate(10);

        return response()->json([
            'city' => $city
        ]);
    }


    // for POS
    public function getCountriesPos(Request $request): JsonResponse
    {
        // todo:: before change please mind it this method is also used on vendor api
        $country = Country::select('id', 'name')
            ->when($request->has('name'), function ($query) use ($request) {
                $query->where("name","LIKE", "%" . strip_tags($request->only("name")['name']) ."%");
            })
            ->orderBy('name', 'asc')->get();

        return response()->json([
            'countries' => $country,
        ]);
    }

    /*
    * fetch all state list based on provided country id from a database
    */
    public function getStateByCountryIdPos($id, Request $request)
    {
        // todo:: before change please mind it this method is also used on vendor api
        if(empty($id)){
            return response()->json([
                'message' => __('provide a valid country id'),
            ])->setStatusCode(422);
        }

        $state = State::select('id', 'name','country_id')
            ->when($request->has('name'), function ($query) use ($request) {
                $query->where("name","LIKE", "%" . strip_tags($request->only("name")['name']) ."%");
            })
            ->where('country_id',$id)
            ->orderBy('name', 'asc')->get();

        return response()->json([
            'state' => $state,
        ]);
    }

    public function getCityByCountryIdPos(Request $request, $id){
        // todo:: before change please mind it this method is also used on vendor api
        if(empty($id)){
            return response()->json([
                'message' => __('provide a valid country id'),
            ])->setStatusCode(422);
        }

        $cities = City::select('id', 'name','state_id')
            ->when($request->has('name'), function ($query) use ($request) {
                $query->where("name","LIKE", "%" . strip_tags($request->only("name")['name']) ."%");
            })->where('state_id',$id)->orderBy('name', 'asc')->get();

        return response()->json([
            'cities' => $cities,
        ]);
    }
}
