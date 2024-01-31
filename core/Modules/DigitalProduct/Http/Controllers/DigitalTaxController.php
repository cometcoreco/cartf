<?php

namespace Modules\DigitalProduct\Http\Controllers;

use App\Helpers\FlashMsg;
use App\Helpers\SanitizeInput;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\DigitalProduct\Entities\DigitalTax;

class DigitalTaxController extends Controller
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
        $all_taxes = DigitalTax::orderBy('id', 'desc')->get();
        return view('digitalproduct::admin.tax.all', compact('all_taxes'));
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        return view('digitalproduct::create');
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|max:255',
            'tax_percent' => 'required|numeric',
            'status_id' => 'required|boolean'
        ]);

        $digital_author = new DigitalTax();
        $digital_author->name = SanitizeInput::esc_html($validatedData['name']);
        $digital_author->tax_percentage = $validatedData['tax_percent'];
        $digital_author->status = $validatedData['status_id'];
        $digital_author->save();

        return back()->with(FlashMsg::create_succeed(__('Digital Product Tax')));
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


    public function update(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|max:255',
            'tax_percent' => 'required|numeric',
            'status_id' => 'required|boolean',
        ]);

        $digital_author = DigitalTax::find($request->id);
        $digital_author->name = SanitizeInput::esc_html($validatedData['name']);
        $digital_author->tax_percentage = $validatedData['tax_percent'];
        $digital_author->status = $validatedData['status_id'];
        $digital_author->save();

        return back()->with(FlashMsg::update_succeed(__('Digital Product Tax')));
    }

    public function destroy($id)
    {
        $digital_author = DigitalTax::findOrFail($id);
        $digital_author->delete();

        return back()->with(FlashMsg::delete_succeed(__('Product Digital')));
    }

    public function bulk_action(Request $request): JsonResponse
    {
        DigitalTax::WhereIn('id', $request->ids)->delete();

        return response()->json(['status' => 'ok']);
    }
}
