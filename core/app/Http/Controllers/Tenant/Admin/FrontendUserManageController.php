<?php

namespace App\Http\Controllers\Tenant\Admin;

use App\Helpers\FlashMsg;
use App\Helpers\ResponseMessage;
use App\Helpers\SanitizeInput;
use App\Http\Controllers\Controller;
use App\Mail\BasicMail;
use App\Models\Admin;
use App\Models\Brand;
use App\Models\PaymentLogs;
use App\Models\PricePlan;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Modules\Blog\Entities\Blog;
use Modules\CountryManage\Entities\City;
use Modules\CountryManage\Entities\Country;
use Modules\CountryManage\Entities\State;
use Modules\Service\Entities\Service;
use function __;
use function auth;
use function response;
use function view;

class FrontendUserManageController extends Controller
{
    const BASE_PATH = 'tenant.admin.frontend-user.';

    public function all_users(){
        $all_users = User::latest()->paginate(10);
        $trashed_users = User::onlyTrashed()->count();

        return view(self::BASE_PATH.'index',compact('all_users', 'trashed_users'));
    }

    public function new_user()
    {
        $countries = Country::published()->select('id', 'name')->get();
        return view(self::BASE_PATH.'new', compact('countries'));
    }

    public function new_user_store(Request $request)
    {
        $request->validate([
            'name'=> 'required|string|max:191',
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'country'=> 'required|integer',
            'state'=> 'nullable|integer',
            'city'=> 'nullable|integer',
            'postal_code'=> 'nullable',
            'mobile'=> 'nullable',
            'address'=> 'nullable',
            'image'=> 'nullable',
            'company'=> 'nullable',
        ]);

        User::create([
            'name' => SanitizeInput::esc_html($request->name),
            'email' => SanitizeInput::esc_html($request->email),
            'password' => Hash::make($request->password),
            'username' => Str::slug(SanitizeInput::esc_html($request->username)),
            'country' => $request->country,
            'state' => $request->state,
            'city' => $request->city,
            'postal_code' => SanitizeInput::esc_html($request->postal_code),
            'mobile' => SanitizeInput::esc_html($request->mobile),
            'address' => SanitizeInput::esc_html($request->address),
            'image' => $request->image,
            'company' => SanitizeInput::esc_html($request->company),
        ]);

        try {
            $sub =  __("Account created");
            $msg = '<p>'.__('Hello').' '.$request->name.',</p>';
            $msg .= '<p>'.__("Your user account is created successfully. click below to continue browsing your account").'</p>';
            $msg .= '<a href="'.url('/').'">'.__('Open').'</a>';

            Mail::to(trim($request->email))->send(new BasicMail($msg,$sub));
        }catch (\Exception $ex){
            return response()->danger(ResponseMessage::delete($ex->getMessage()));
        }

        return response()->success(ResponseMessage::success(__('Tenant has been created successfully')));
    }

    public function edit_profile($id)
    {
        $user = User::find($id);
        $countries = Country::published()->select('id', 'name')->get();
        $states = State::where(['status' => 'publish', 'country_id' => $user->country])->select('id', 'name')->get();
        $cities = City::where(['status' => 'publish', 'state_id' => $user->state])->select('id', 'name')->get();

        return view(self::BASE_PATH.'edit',compact('user','countries', 'states', 'cities'));
    }

    public function update_edit_profile(Request $request)
    {

        $request->validate([
            'name'=> 'required|string|max:191',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users','email')->ignore($request->id)],
            'username' => ['required', 'string','max:255', Rule::unique('users','username')->ignore($request->id)],
            'country'=> 'required|integer',
            'state'=> 'nullable|integer',
            'city'=> 'nullable|integer',
            'postal_code'=> 'nullable',
            'mobile'=> 'nullable',
            'address'=> 'nullable',
            'image'=> 'nullable',
            'company'=> 'nullable',
        ]);

        User::where('id',$request->id)->update([
            'name' => SanitizeInput::esc_html($request->name),
            'email' => SanitizeInput::esc_html($request->email),
            'password' => Hash::make($request->password),
            'username' => Str::slug(SanitizeInput::esc_html($request->username)),
            'country' => $request->country,
            'state' => $request->state,
            'city' => $request->city,
            'postal_code' => SanitizeInput::esc_html($request->postal_code),
            'mobile' => SanitizeInput::esc_html($request->mobile),
            'address' => SanitizeInput::esc_html($request->address),
            'image' => SanitizeInput::esc_html($request->image),
            'company' => SanitizeInput::esc_html($request->company),
        ]);

        return response()->success(ResponseMessage::success(__('Tenant updated successfully')));

    }

    public function delete($id)
    {
        $user = User::find($id);
        $user->delete();

        return response()->danger(ResponseMessage::delete(__('Tenant deleted successfully')));
    }

    public function trashed_users(){
        $all_users = User::onlyTrashed()->paginate(10);
        return view(self::BASE_PATH.'trash',compact('all_users'));
    }

    public function trashed_restore($id){
        User::onlyTrashed()->where('id', $id)->restore();
        return back()->with(FlashMsg::restore_succeed('user'));
    }

    public function trashed_delete(Request $request, $id){
        User::onlyTrashed()->where('id', $id)->forceDelete();
        return back()->with(FlashMsg::delete_succeed('user'));
    }

    public function update_change_password(Request $request)
    {
        $this->validate(
            $request,[
            'password' => 'required|string|min:8|confirmed'
        ],
            [
                'password.required' => __('password is required'),
                'password.confirmed' => __('password does not matched'),
                'password.min' => __('password minimum length is 8'),
            ]
        );
        $user = User::findOrFail($request->ch_user_id);
        $user->password = Hash::make($request->password);
        $user->save();
        return response()->success(ResponseMessage::success(__('Password updated successfully')));
    }


    public function send_mail(Request $request){

        $this->validate($request,[
            'email' => 'required|email',
            'subject' => 'required',
            'message' => 'required',
        ]);

        $sub =  $request->subject;
        $msg = $request->message;

        try {
            Mail::to($request->email)->send(new BasicMail($msg,$sub));
        }catch (\Exception $ex){
            return response()->danger(ResponseMessage::delete($ex->getMessage()));
        }

        return response()->success(ResponseMessage::success(__('Mail Send Successfully')));
    }

    public function resend_verify_mail(Request $request){

        $subscriber_details = User::findOrFail($request->id);
        $token = $subscriber_details->email_verify_token ? $subscriber_details->email_verify_token  : Str::random(32);

        if (empty($subscriber_details->email_verify_token)){
            $subscriber_details->email_verify_token = $token;
            $subscriber_details->save();
        }
        $message = __('Verify your email to get all news from '). get_static_option('site_title');
        $message .= '<div class="btn-wrap">
                        <a class="anchor-btn" style="background-color: #007cbd;color: white; padding: 10px; margin: 10px"  href="' . route('tenant.user.login') . '">' . __('Login') . '</a>
                     </div>';

        $msg = $message;
        $subject = __('Verify your email');


        try {
            Mail::to($subscriber_details->email)->send(new BasicMail($msg,$subject));
        }catch (\Exception $ex){
            return response()->danger(ResponseMessage::delete($ex->getMessage()));
        }

        return response()->success(ResponseMessage::success(__('Email Verify Mail Send Successfully')));
    }
}
