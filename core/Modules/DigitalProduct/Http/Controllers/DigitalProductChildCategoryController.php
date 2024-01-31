<?php

namespace Modules\DigitalProduct\Http\Controllers;

use App\Helpers\FlashMsg;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\DigitalProduct\Entities\DigitalCategories;
use Modules\DigitalProduct\Entities\DigitalChildCategories;
use Modules\DigitalProduct\Entities\DigitalSubCategories;
use function GuzzleHttp\Promise\all;

class DigitalProductChildCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        $all_category = DigitalCategories::where('status', 1)->select('id', 'name', 'slug')->get();
        $all_subcategory = DigitalSubCategories::where('status', 1)->select('id', 'name', 'slug')->get();
        $all_childcategory = DigitalChildCategories::all();
        return view('digitalproduct::admin.child-category.all', compact('all_category', 'all_subcategory', 'all_childcategory'));
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        return view('digitalproduct::create');
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|max:255',
            'slug' => 'nullable|max:255|unique:digital_child_categories,slug',
            'description' => 'nullable|max:255',
            'status_id' => 'required|boolean',
            'image_id' => 'nullable|numeric',
            'category' => 'required|numeric|exists:digital_sub_categories,category_id',
            'subcategory' => 'required|numeric|exists:digital_sub_categories,id',
        ]);

        $digital_product_category = new DigitalChildCategories();
        $digital_product_category->name = $validatedData['name'];
        $digital_product_category->slug = \Str::slug($validatedData['slug']);
        $digital_product_category->description = $validatedData['description'];
        $digital_product_category->category_id = $validatedData['category'];
        $digital_product_category->sub_category_id = $validatedData['subcategory'];
        $digital_product_category->status = $validatedData['status_id'];
        $digital_product_category->image_id = $validatedData['image_id'] ?? null;
        $digital_product_category->save();

        return back()->with(FlashMsg::create_succeed(__('Product Child Category')));
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        return view('digitalproduct::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        return view('digitalproduct::edit');
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|max:255',
            'slug' => 'nullable|max:255|unique:digital_child_categories,slug,'.$request->id,
            'description' => 'nullable|max:255',
            'status_id' => 'required|boolean',
            'image_id' => 'nullable|numeric',
            'category' => 'required|numeric|exists:digital_sub_categories,category_id',
            'subcategory' => 'required|numeric|exists:digital_sub_categories,id',
        ]);

        $digital_product_category = DigitalChildCategories::find($request->id);
        $digital_product_category->name = $validatedData['name'];
        $digital_product_category->slug = \Str::slug($validatedData['slug']);
        $digital_product_category->description = $validatedData['description'];
        $digital_product_category->category_id = $validatedData['category'];
        $digital_product_category->sub_category_id = $validatedData['subcategory'];
        $digital_product_category->status = $validatedData['status_id'];
        $digital_product_category->image_id = $validatedData['image_id'] ?? null;
        $digital_product_category->save();

        return back()->with(FlashMsg::update_succeed(__('Product Child Category')));
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        $digital_product_child_category = DigitalChildCategories::findOrFail($id);
        $digital_product_child_category->delete();

        return back()->with(FlashMsg::delete_succeed(__('Product Child Category')));
    }

    public function bulk_action(Request $request): JsonResponse
    {
        DigitalChildCategories::WhereIn('id', $request->ids)->delete();

        return response()->json(['status' => 'ok']);
    }

    public function categoryBasedSubcategory(Request $request)
    {
        $request->validate([
            'category' => 'required'
        ]);

        $subcategories = DigitalSubCategories::where('category_id', $request->category)->get();

        $markup = '';
        foreach ($subcategories as $item)
        {
            $markup .= '<option value="'.$item->id.'">'.$item->name.'</option>';
        }

        return response()->json([
            'status' => 'success',
            'markup' => $markup ?? ''
        ]);
    }
}
