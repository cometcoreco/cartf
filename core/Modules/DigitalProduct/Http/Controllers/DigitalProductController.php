<?php

namespace Modules\DigitalProduct\Http\Controllers;

use App\Helpers\FlashMsg;
use App\Models\Status;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;
use Modules\Attributes\Entities\Tag;
use Modules\Badge\Entities\Badge;
use Modules\DigitalProduct\Entities\DigitalAuthor;
use Modules\DigitalProduct\Entities\DigitalCategories;
use Modules\DigitalProduct\Entities\DigitalChildCategories;
use Modules\DigitalProduct\Entities\DigitalLanguage;
use Modules\DigitalProduct\Entities\DigitalProduct;
use Modules\DigitalProduct\Entities\DigitalProductTags;
use Modules\DigitalProduct\Entities\DigitalSubCategories;
use Modules\DigitalProduct\Entities\DigitalTax;
use Modules\DigitalProduct\Http\Requests\DigitalProductRequest;
use Modules\DigitalProduct\Http\Requests\DigitalProductUpdateRequest;
use Modules\DigitalProduct\Http\Services\Admin\AdminDigitalProductServices;
use Modules\Product\Entities\Product;
use Modules\Product\Entities\ProductTag;
use Modules\Product\Http\Services\Admin\AdminProductServices;

class DigitalProductController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index(Request $request)
    {
        $products = AdminDigitalProductServices::productSearch($request);
        $statuses = Status::all();

        return view('digitalproduct::admin.product.index',compact("products", "statuses"));
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        $data = [
            "taxes" => DigitalTax::where('status', 1)->select('id','name')->get(),
            "badges" => Badge::where("status","active")->get(),
            "tags" => Tag::select("id", "tag_text as name")->get(),
            "categories" => DigitalCategories::where('status', 1)->select("id", "name")->get(),
            "authors" => DigitalAuthor::where('status', 1)->select('id', 'name')->get(),
            "languages" => DigitalLanguage::where('status', 1)->select('id', 'name')->get(),
        ];

        return view('digitalproduct::admin.product.create', compact('data'));
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(DigitalProductRequest $request)
    {
        $data = $request->validated();
        if (!empty($data['file']))
        {
            $category_extension = DigitalCategories::find($data['category_id'])?->product_type;
            $extensions = json_decode($category_extension->extensions);

            $main_file = $data['file'];
            $file_extension = $main_file->getClientOriginalExtension();
            if (!in_array($file_extension, $extensions))
            {
                $imploded_extension = implode(', ', $extensions);

                return response()->json([
                    "success" => false,
                    'msg' => __('Invalid file formal, Supported file formats are '.$imploded_extension)
                ])->setStatusCode(200);
            }
        }

//        \DB::beginTransaction();
//        try {
        $product = (new AdminDigitalProductServices())->store($data, $request);
//            \DB::commit();
//        } catch (\Exception $exception)
//        {
//            \DB::rollBack();
//            return response(['success' => false]);
//        }

        return response()->json($product ? ["success" => true] : ["success" => false]);
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
    public function edit($id, $aria_name = null)
    {
        $data = [
            "badges" => Badge::where("status","active")->get(),
            "tags" => Tag::select("id", "tag_text as name")->get(),
            "categories" => DigitalCategories::where('status', 1)->select("id", "name")->get(),
            "authors" => DigitalAuthor::where('status', 1)->select("id", "name")->get(),
            "taxes" => DigitalTax::select("id", "name")->get(),
            "languages" => DigitalLanguage::where('status', 1)->select('id', 'name')->get(),
            'aria_name' => $aria_name
        ];

        $product = (new AdminDigitalProductServices())->get_edit_product($id);
        $subCat = $product?->subCategory?->id ?? null;
        $cat = $product?->category?->id ?? null;

        $sub_categories = DigitalSubCategories::select("id", "name")->where("category_id", $cat)->where("status", 1)->get();
        $child_categories = DigitalChildCategories::select("id", "name")->where("sub_category_id", $subCat)->where("status", 1)->get();

        return view('digitalproduct::admin.product.edit', compact("data", "product", "sub_categories", "child_categories"));
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(DigitalProductUpdateRequest $request, $id)
    {
        $data = $request->validated();

        if (!empty($data['file']))
        {
            $category_extension = DigitalCategories::find($data['category_id'])?->product_type;
            $extensions = json_decode($category_extension->extensions);

            $main_file = $data['file'];
            $file_extension = $main_file->getClientOriginalExtension();
            if (!in_array($file_extension, $extensions))
            {
                $imploded_extension = implode(', ', $extensions);

                return response()->json([
                    "success" => false,
                    'msg' => __('Invalid file formal, Supported file formats are '.$imploded_extension)
                ])->setStatusCode(200);
            }
        }

        return response()->json((new AdminDigitalProductServices)->update($data, $id) ? ["success" => true] : ["success" => false]);
    }

    private function validateUpdateStatus($req): array
    {
        return Validator::make($req,[
            "id" => "required",
            "status_id" => "required"
        ])->validated();
    }

    public function update_status(Request $request)
    {
        $data = $this->validateUpdateStatus($request->all());

        return (new AdminDigitalProductServices)->updateStatus($data["id"], $data["status_id"]);
    }

    public function clone($id)
    {
        return (new AdminDigitalProductServices)->clone($id) ? back()->with(FlashMsg::clone_succeed('Product')) : back()->with(FlashMsg::clone_failed('Product'));
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        return response()->json((new AdminDigitalProductServices)->delete($id) ? ["success" => true, "msg" => "Product deleted successfully"] : ["success" => false]);
    }

    public function bulk_destroy(Request $request): JsonResponse
    {
        return response()->json((new AdminDigitalProductServices)->bulk_delete_action($request->ids) ? ["success" => true] : ["success" => false]);
    }

    public function trash(): Renderable
    {
        $products = DigitalProduct::with('category','subCategory', 'childCategory')->orderByDesc('id')->onlyTrashed()->get();
        return view('digitalproduct::admin.product.trash',compact("products"));
    }

    public function restore($id)
    {
        $restore = DigitalProduct::onlyTrashed()->findOrFail($id)->restore();
        return back()->with($restore ? FlashMsg::restore_succeed('Trashed Product') : FlashMsg::restore_failed('Trashed Product'));
    }

    public function trash_delete($id)
    {
        return (new AdminDigitalProductServices)->trash_delete($id) ? back()->with(FlashMsg::delete_succeed('Trashed Product')) : back()->with(FlashMsg::delete_failed('Trashed Product'));
    }

    public function trash_bulk_destroy(Request $request)
    {
        return response()->json((new AdminDigitalProductServices)->trash_bulk_delete_action($request->ids) ? ["success" => true] : ["success" => false]);
    }

    public function trash_empty(Request $request)
    {
        $ids = explode('|', $request->ids);
        return response()->json((new AdminDigitalProductServices)->trash_bulk_delete_action($ids) ? ["success" => true] : ["success" => false]);
    }

    public function productSearch(Request $request): string
    {
        $products = AdminDigitalProductServices::productSearch($request);
        $statuses = Status::all();

        return view('digitalproduct::admin.product.search',compact("products","statuses"))->render();
    }

//    private function validateType($data)
//    {
//        $category_extension = DigitalCategories::find($data['category_id'])?->product_type;
//        $extensions = json_decode($category_extension->extensions);
//
//        $main_file = $data['main_file'];
//        $file_extension = $main_file->getClientOriginalExtension();
//        if (!in_array($file_extension, $extensions))
//        {
//            $imploded_extension = implode(', ', $extensions);
//
//            return response()->json([
//                "success" => false,
//                'msg' => __('Invalid file formal, Supported file formats are '.$imploded_extension)
//            ])->setStatusCode(200);
//        }
//
//        return true;
//    }
}
