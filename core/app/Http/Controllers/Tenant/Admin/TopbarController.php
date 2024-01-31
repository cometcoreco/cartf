<?php

namespace App\Http\Controllers\Tenant\Admin;

use App\Helpers\FlashMsg;
use App\Helpers\SanitizeInput;
use App\Http\Controllers\Controller;
use App\Models\Language;
use App\Models\TopbarInfo;
use Illuminate\Http\Request;

class TopbarController extends Controller
{
    public function index(){
        $topbar_menu = get_static_option('topbar_menu');
        $menu_list = \App\Models\Menu::all();
        $all_social_icons = TopbarInfo::all();

        return view('tenant.admin.pages.topbar-settings')->with([
            'topbar_menu' => $topbar_menu,
            'menu_list' => $menu_list,
            'all_social_icons' => $all_social_icons
        ]);
    }

    public function update_topbar(Request $request)
    {
        $data = [
            'topbar_menu' => 'required',
            'topbar_phone' => 'nullable',
            'topbar_email' => 'nullable',
            'topbar_menu_show_hide' => 'nullable',
            'contact_info_show_hide' => 'nullable',
            'social_info_show_hide' => 'nullable',
            'topbar_show_hide' => 'nullable'
        ];

        $request->validate($data);

        foreach ($data as $index => $value)
        {
            update_static_option($index, esc_html($request->$index));
        }

        return redirect()->back()->with(FlashMsg::update_succeed(__('Topbar')));
    }

    public function new_social_item(Request $request){
        $data = $this->validate($request,[
            'icon' => 'required|string',
            'url' => 'required|string',
        ]);

        $data['url'] = SanitizeInput::esc_html($data['url']);

        TopbarInfo::create($data);

        return redirect()->back()->with([
            'msg' => __('New Social Item Added...'),
            'type' => 'success'
        ]);
    }
    public function update_social_item(Request $request){
        $data = $this->validate($request,[
            'icon' => 'required|string',
            'url' => 'required|string',
        ]);

        $data['url'] = SanitizeInput::esc_html($data['url']);

        TopbarInfo::find($request->id)->update($data);
        return redirect()->back()->with([
            'msg' => __('Social Item Updated...'),
            'type' => 'success'
        ]);
    }
    public function delete_social_item(Request $request,$id){
        TopbarInfo::find($id)->delete();
        return redirect()->back()->with([
            'msg' => __('Social Item Deleted...'),
            'type' => 'danger'
        ]);
    }
}
