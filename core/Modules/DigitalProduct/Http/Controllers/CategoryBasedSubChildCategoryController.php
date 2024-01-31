<?php

namespace Modules\DigitalProduct\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\DigitalProduct\Entities\DigitalCategories;
use Modules\DigitalProduct\Entities\DigitalChildCategories;
use Modules\DigitalProduct\Entities\DigitalSubCategories;

class CategoryBasedSubChildCategoryController extends Controller
{
    public function getCategory(): JsonResponse
    {
        // need to fetch all categories and return as laravel eloquent object
        $categories = DigitalCategories::all();

        $options = view("digitalproduct::category-option.option", compact("categories"));
        return response()->json(["success" => true,"html" => $options]);
    }

    public function getSubCategory(Request $req)
    {
        // fetch sub category from category
        $categories = DigitalSubCategories::where("category_id" , $req->category_id)->get();

        // load view file for select option
        $options = view("digitalproduct::category-option.option", compact("categories"))->render();
        return response()->json(["success" => true,"html" => $options]);
    }

    public function getChildCategory(Request $req)
    {
        // fetch sub category from category
        $categories = DigitalChildCategories::where("sub_category_id" , $req->sub_category_id)->get();
        // load view file for select option
        $options = view("digitalproduct::category-option.option", compact("categories"))->render();
        return response()->json(["success" => true,"html" => $options]);
    }
}
