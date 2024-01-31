<?php

namespace Modules\CountryManage\Http\Controllers\Tenant\Admin;

use App\Helpers\FlashMsg;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Modules\CountryManage\Entities\City;
use Modules\CountryManage\Entities\Country;
use Modules\CountryManage\Entities\State;

class CityController extends Controller
{
    public function __construct()
    {
        $this->middleware("auth:admin");
    }

    //todo: display all city and add new city
    public function all_city(Request $request)
    {
        if($request->isMethod('post')){
            $request->validate([
                'country'=> 'required',
                'state'=> 'required',
                'city'=> 'required|unique:cities,name|max:191',
            ]);

            City::create([
                'name' => $request->city,
                'country_id' => $request->country,
                'state_id' => $request->state,
                'status' => $request->status,
            ]);

            return redirect()->back()->with(FlashMsg::create_succeed('New City Successfully Added'));
        }
        $all_countries = Country::get();
        $all_states = State::get();
        $all_cities = City::with("state","country")->latest('id')->paginate(10);

        return view('countrymanage::tenant.admin.city.all-city',compact('all_states','all_countries','all_cities'));
    }

    //todo: edit city
    public function edit_city(Request $request)
    {
        $request->validate([
            'city'=> 'required|max:191|unique:cities,name,'.$request->city_id,
            'country'=> 'required',
            'state'=> 'required',
        ]);

        City::where('id',$request->city_id)->update([
            'name'=>$request->city,
            'state_id'=>$request->state,
            'country_id'=>$request->country,
        ]);

        return redirect()->back()->with(FlashMsg::create_succeed('City Successfully Updated'));
    }

    //todo: change status
    public function city_status($id)
    {
        $city = City::select('status')->where('id',$id)->first();
        $city->status== 'publish' ? $status= 'draft' : $status = 'publish';
        City::where('id',$id)->update(['status'=>$status]);
        return redirect()->back()->with(FlashMsg::create_succeed('Status Successfully Changed'));
    }

    //todo: delete single city
    public function delete_city($id)
    {
        City::findOrFail($id)->delete();
        return redirect()->back()->with(FlashMsg::create_succeed('City Successfully Deleted'));
    }

    //todo: delete multi city
    public function bulk_action_city(Request $request){
        City::whereIn('id',$request->ids)->delete();

        return redirect()->back()->with(FlashMsg::create_succeed('Selected City Successfully Deleted'));
    }

    //todo: pagination
    function pagination(Request $request)
    {
        if($request->ajax()){
            $all_cities = City::latest()->paginate(10);
            return view('countrymanage::city.search-result', compact('all_cities'))->render();
        }
    }

    //todo: search city
    public function search_city(Request $request)
    {
        $all_cities= City::where('name', 'LIKE', "%". strip_tags($request->string_search) ."%")
            ->paginate(10);
        if($all_cities->total() >= 1){
            return view('countrymanage::tenant.admin.city.search-result', compact('all_cities'))->render();
        }else{
            return response()->json([
                'status'=>__('nothing')
            ]);
        }
    }
}
