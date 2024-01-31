<?php

namespace Modules\MobileApp\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Routing\Controller;
use Modules\Campaign\Entities\Campaign;
use Modules\MobileApp\Http\Resources\Api\CampaignResource;

class CampaignController extends Controller
{
    public function index(){
        // fetch all campaign those are active and those are eligible
        $campaigns = Campaign::with("campaignImage")
            ->where("status","publish")->whereDate("end_date" , '>', Carbon::now())->get();

        return CampaignResource::collection($campaigns);
    }
}
