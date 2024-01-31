<?php

namespace Modules\DigitalProduct\Http\Controllers;

use App\Helpers\FlashMsg;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\DigitalProduct\Entities\DigitalAuthor;
use Modules\DigitalProduct\Entities\DigitalCategories;

class DigitalAuthorController extends Controller
{
    public function __construct(){
        $this->middleware("auth:admin");
    }
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        $all_author = DigitalAuthor::orderBy('id', 'desc')->get();
        return view('digitalproduct::admin.author.all', compact('all_author'));
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
            'slug' => 'nullable|max:255|unique:digital_authors,slug',
            'status_id' => 'required|boolean',
            'image_id' => 'nullable|numeric'
        ]);

        $digital_author = new DigitalAuthor();
        $digital_author->name = $validatedData['name'];
        $digital_author->slug = \Str::slug($validatedData['slug']);
        $digital_author->status = $validatedData['status_id'];
        $digital_author->image_id = $validatedData['image_id'] ?? null;
        $digital_author->save();

        return back()->with(FlashMsg::create_succeed(__('Digital Author')));
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
            'slug' => 'nullable|max:255|unique:digital_authors,slug,'.$request->id,
            'status_id' => 'required|boolean',
            'image_id' => 'nullable|numeric'
        ]);

        $digital_author = DigitalAuthor::find($request->id);
        $digital_author->name = $validatedData['name'];
        $digital_author->slug = \Str::slug($validatedData['slug']);
        $digital_author->status = $validatedData['status_id'];
        $digital_author->image_id = $validatedData['image_id'] ?? null;
        $digital_author->save();

        return back()->with(FlashMsg::update_succeed(__('Digital Author')));
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        $digital_author = DigitalAuthor::findOrFail($id);
        $digital_author->delete();

        return back()->with(FlashMsg::delete_succeed(__('Digital Author')));
    }

    public function bulk_action(Request $request): JsonResponse
    {
        DigitalAuthor::WhereIn('id', $request->ids)->delete();

        return response()->json(['status' => 'ok']);
    }
}
