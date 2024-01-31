<?php

namespace Modules\MobileApp\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\MobileApp\Entities\MobileIntro;
use Modules\MobileApp\Http\Requests\StoreMobileIntroRequest;

class MobileIntrosController extends Controller
{
    public function index()
    {
        // first i need to get all intros those i have created
        $mobileIntros = MobileIntro::with("image")->get();

        return view("mobileapp::intro.list", compact("mobileIntros"));
    }

    public function create()
    {
        return view("mobileapp::intro.create");
    }

    public function store(StoreMobileIntroRequest $request)
    {
        $mobileIntro = MobileIntro::create($request->validated());

        return redirect(route("tenant.admin.mobile.intro.all"))->with($mobileIntro ? ["success" => true, "msg" => __("Mobile Intro created successfully")] : ["success" => false,"msg" => __("Failed to create mobile intro")]);
    }

    public function show($id)
    {

    }

    public function edit(MobileIntro $mobileIntro)
    {
        return view("mobileapp::intro.edit", compact("mobileIntro"));
    }

    public function update(StoreMobileIntroRequest $request, MobileIntro $mobileIntro)
    {
        $update = $mobileIntro->update($request->validated());

        return redirect(route("tenant.admin.mobile.intro.all"))
            ->with(
                $update ?
                    ["success" => true, "msg" => __("Mobile Intro updated successfully")]
                    :
                    ["success" => false,"msg" => __("Failed to update mobile intro")]
            );
    }

    public function destroy(MobileIntro $mobileIntro)
    {
        $delete = $mobileIntro->delete();

        return response()->json(["success" => (bool) $delete ?? false, "msg" => $delete ? __("Successfully delete mobile intro.") : __("Failed to delete mobile intro.")]);
    }
}
