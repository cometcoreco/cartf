<?php

namespace App\Http\Controllers\Landlord\Admin;

use App\Enums\LandlordCouponType;
use App\Enums\StatusEnums;
use App\Helpers\FlashMsg;
use App\Http\Controllers\Controller;
use App\Http\Requests\CouponRequest;
use App\Http\Requests\CouponUpdateRequest;
use App\Models\Coupon;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    public function index()
    {
        $all_coupon = Coupon::all();
        return view('landlord.admin.coupon.index', compact('all_coupon'));
    }

    public function store(CouponRequest $request)
    {
        Coupon::create($request->validated());

        return back()->with(FlashMsg::update_succeed("coupon"));
    }

    public function update(CouponUpdateRequest $request)
    {
        $validated = $request->validated();
        $id = $validated['id'];
        unset($validated['id']);

        Coupon::find($id)->update($validated);

        return back()->with(FlashMsg::update_succeed("coupon"));
    }

    public function delete(Request $request, $id)
    {
        abort_if(empty($id), 404);

        Coupon::findOrFail($id)->delete();

        return back()->with(FlashMsg::delete_succeed("coupon"));
    }
}
