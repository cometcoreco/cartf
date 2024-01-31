<?php

namespace Modules\WebHook\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Modules\WebHook\Entities\Webhook;
use Modules\WebHook\Entities\WebhookEvents;

class WebhookManageController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        $all_webhook = Webhook::with("events")->orderBy("id","desc")->paginate(20);
        return view('webhook::webhook-manage.index',compact("all_webhook"));
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        $request->validate([
           "name" => "required|string",
           "url" => "required|url",
           "method_type" => "required",
           "event" => "array|required",
           "status" => "required",
        ]);
        $webhook = Webhook::create([
            "name" => $request->name,
            "url" => $request->url,
            "method_type" => $request->method_type,
            "status" => $request->status,
        ]);
        $events = [];
        foreach($request->event as $vent){
            $events[] = [
              "event_name" => $vent,
               "webhook_id" => $webhook->id,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];
        }
        WebhookEvents::insert($events);
        Cache::forget('webhook-all-events');
        return back()->with(['msg' => __('New webhook added'),'type' => 'success']);
    }


    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, $id)
    {
        $webhook = Webhook::find($id)->update([
            "name" => $request->name,
            "url" => $request->url,
            "method_type" => $request->method_type,
            "status" => $request->status,
        ]);

        WebhookEvents::where("webhook_id" , $id)->delete();
        $events = [];
        foreach($request->event as $vent){
            $events[] = [
                "event_name" => $vent,
                "webhook_id" => $id,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];
        }
        WebhookEvents::insert($events);
        Cache::forget('webhook-all-events');
        return back()->with(['msg' => __('webhook updated'),'type' => 'success']);
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        Cache::forget('webhook-all-events');
        $webhook = Webhook::find($id);
        WebhookEvents::where('webhook_id',$id)->delete();
        $webhook->delete();
        return back()->with(['msg' => __('webhook deleted'),'type' => 'danger']);
    }
}
