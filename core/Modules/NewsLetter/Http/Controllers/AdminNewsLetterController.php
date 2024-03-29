<?php

namespace Modules\NewsLetter\Http\Controllers;

use App\Helpers\SanitizeInput;
use App\Mail\BasicMail;
use App\Mail\SubscriberMessage;
use Modules\NewsLetter\Entities\NewsLetter;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Modules\NewsLetter\Http\Requests\AdminSendAllEmailRequest;
use Modules\NewsLetter\Http\Requests\AdminSendMailReqeuest;

class AdminNewsLetterController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
        $this->middleware('permission:newsletter-list|newsletter-create|newsletter-mail-send|newsletter-delete',['only' => ['index']]);
        $this->middleware('permission:newsletter-create',['only' => ['add_new_sub']]);
        $this->middleware('permission:newsletter-mail-send',['only' => ['send_mail_all_index','send_mail_all','verify_mail_send']]);
        $this->middleware('permission:newsletter-delete',['only' => ['delete','bulk_action']]);
    }

    public function index(){
        $all_subscriber = Newsletter::all();
        return view('newsletter::backend.newsletter-index')->with(['all_subscriber' => $all_subscriber]);
    }

    public function send_mail(AdminSendMailReqeuest $request){
        // validation are done by request
        $data = $request->validated();

        $subscriber = NewsLetter::where('email', $data['email'])->first();
        $data['uid'] = rand(1111, 9999).$subscriber->id.rand(1111, 9999);

        try {
            Mail::to($data['email'])->send(new SubscriberMessage($data));
        } catch (\Throwable $th) {
            //throw $th;
        }

        return redirect()->back()->with([
            'msg' => __('Mail Send Success...'),
            'type' => 'success'
        ]);
    }
    public function delete($id){
        Newsletter::find($id)->delete();
        return redirect()->back()->with(['msg' => __('Subscriber Delete Success....'),'type' => 'danger']);
    }

    public function send_mail_all_index(){
        return view('newsletter::backend.send-main-to-all');
    }

    public function send_mail_all(AdminSendAllEmailRequest $request){
        $all_subscriber = Newsletter::all();

        foreach ($all_subscriber as $subscriber){
            $id = $subscriber->id;
            $uid = rand(1111, 9999).$id.rand(1111, 9999);

            $data = [
                'uid' => $uid,
                'subject' => SanitizeInput::esc_html($request->subject),
                'message' => esc_javascript($request->message),
            ];

            try {
                Mail::to($subscriber->email)->send(new SubscriberMessage($data));
            } catch (\Throwable $th) {
                //throw $th;
            }
        }

        return redirect()->back()->with([
            'msg' => __('Mail Send Success..'),
            'type' => 'success'
        ]);
    }

    public function add_new_sub(Request $request){
        $request->validate([
            'email' => 'required|email|unique:newsletters'
        ]);

        Newsletter::create($request->all());
        return redirect()->back()->with([
            'msg' => __('New Subscriber Added..'),
            'type' => 'success'
        ]);
    }

    public function bulk_action(Request $request){
        $all = Newsletter::find($request->ids);
        foreach($all as $item){
            $item->delete();
        }
        return response()->json(['status' => 'ok']);
    }

    public function unsubscribe($uid)
    {
        abort_if(empty($uid), 403);

        $id = substr($uid, 4, -4);

        try {
            NewsLetter::findOrNew((int) $id)->delete();
        } catch (\Exception $exception) {}

        return "You have successfully unsubscribed from the platform";
    }
}
