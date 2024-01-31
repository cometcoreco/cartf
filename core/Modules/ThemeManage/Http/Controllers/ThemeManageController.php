<?php

namespace Modules\ThemeManage\Http\Controllers;

use App\Facades\ThemeDataFacade;
use App\Helpers\SeederHelpers\JsonDataModifier;
use App\Models\Page;
use App\Models\PageBuilder;
use App\Models\Tenant;
use App\Models\Themes;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ThemeManageController extends Controller
{
    const BASE_PATH = 'thememanage::tenant.backend.';

    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        ;
        return view(self::BASE_PATH . 'index');
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        return view('thememanage::create');
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        return view('thememanage::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        return view('thememanage::edit');
    }

    public function update(Request $request, $slug)
    {
        $request->validate([
            'theme_setting_type' => ['required', Rule::in(['set_theme', 'set_theme_with_demo_data'])],
            'tenant_default_theme' => 'required',
        ], [
            'theme_setting_type.required' => __('Please select theme setting type by clicking on the theme image..!')
        ]);

        $all_theme_slugs = getAllThemeSlug();
        if (!in_array($slug, $all_theme_slugs)) {
            return response()->json([
                'status' => false
            ]);
        }

        $theme_setting_type = $request->theme_setting_type;
        $requested_theme = $request->tenant_default_theme;

        try {
            $tenant_id = \tenant()->id;
            Tenant::where('id', $tenant_id)->update([
                'theme_slug' => $requested_theme
            ]);
        } catch (\Exception $exception) {}


        if($theme_setting_type == 'set_theme_with_demo_data'){
            $data_imported = $this->set_new_home($requested_theme);

            if (!$data_imported['status'])
            {
                return response()->json($data_imported);
            }
        }

        return response()->json([
            'status' => true,
            'msg' => __('Theme selected successfully')
        ]);
    }

    public function set_new_home($requested_theme)
    {
        $current_theme = $requested_theme;

        $object = new JsonDataModifier('', 'dynamic-pages');
        $data = $object->getColumnDataForDynamicPage([
            'id',
            'title',
            'page_content',
            'slug',
            'page_builder',
            'breadcrumb',
            'status',
            'theme_slug'
        ],true, true);

        //For home pages

        $filter_data = array_filter($data,function ($item) use ($current_theme){
            if (in_array($item['theme_slug'],[null,$current_theme])){
                if($item['theme_slug'] == $current_theme){
                    return $item;
                }
            }
        });

        $homepageData = current($filter_data);

        $mapped_data = array_map(function ($item){
            unset($item['theme_slug']);
            return $item;
        },$filter_data);

        $main_data = current($mapped_data);

        $old_page = Page::find($main_data['id']);
        if($old_page)
        {
            $new_page = Page::latest('id')->select('id')->first();
            $new_page_id = $new_page->id + 1;
            $homepageData['id'] = $main_data['id'] = $new_page_id;

            $main_data['slug'] = $old_page->slug.'-'.$new_page_id;
        }

        Page::insert($main_data);

        $homepage_id = $homepageData['id'] ?? null;
        $home_page_layout_file = $current_theme.'-layout.json';
        $this->upload_layout($home_page_layout_file, $homepage_id);

        update_static_option('home_page', $homepage_id);

        return ['status' => true, 'msg' => __('Theme Data Imported Successfully')];
    }

    private function upload_layout($file, $page_id)
    {
        $file_contents =  json_decode(file_get_contents('assets/tenant/page-layout/home-pages/'.$file));
        $file_contents = $file_contents->data ?? $file_contents;

        $contentArr = [];
        if (current($file_contents)->addon_page_type == 'dynamic_page')
        {

            foreach ($file_contents as $key => $content)
            {
                unset($content->id);
                $content->addon_page_id = (int)trim($page_id);
                $content->created_at = now();
                $content->updated_at = now();

                foreach ($content as $key2 => $con)
                {
                    $contentArr[$key][$key2] = $con;
                }
            }

            Page::findOrFail($page_id)->update(['page_builder' => 1]);
            PageBuilder::where('addon_page_id', $page_id)->delete();

            PageBuilder::insert($contentArr);

        } else {
            Page::findOrFail($page_id)->update([
                'page_builder' => 0,
                'page_content' => current($file_contents)->text
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        //
    }
}
