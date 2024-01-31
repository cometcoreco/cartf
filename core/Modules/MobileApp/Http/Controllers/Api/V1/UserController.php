<?php

namespace Modules\MobileApp\Http\Controllers\Api\V1;

use App\Helpers\FlashMsg;
use App\Http\Controllers\Landlord\Admin\MediaUploaderController;
use App\Mail\BasicMail;
use App\Models\MediaUploader;
use App\Models\Order;
use App\Models\OrderProducts;
use App\Models\ProductOrder;
use App\Models\SupportDepartment;
use App\Models\SupportTicket;
use App\Models\SupportTicketMessage;
use App\Models\User;
use App\Models\UserDeliveryAddress;
use DB;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Modules\MobileApp\Http\Services\Api\UserServices;
use Modules\Product\Entities\ProductSellInfo;
use Modules\RefundModule\Entities\RefundChat;
use Modules\RefundModule\Entities\RefundChatMessage;
use Modules\RefundModule\Entities\RefundProduct;
use Modules\ShippingModule\Entities\ShippingAddress;
use Modules\ShippingModule\Http\Requests\StoreShippingAddressRequest;
use Modules\ShippingModule\Http\Services\ShippingAddressServices;

class UserController extends Controller
{
    public function login(Request $request)
    {
        $validate = UserServices::validateLoginRequest($request->all());

        if ($validate->fails()) {
            return UserServices::validationErrorsResponse($validate);
        }

        $validated = $validate->validated();

        // Set login type
        $user_login_type = UserServices::loginUserType($validated["username"]);

        if ($user_login_type == 'email' && !UserServices::isValideEmail($validated["username"])) {
            return UserServices::emailValidationResponse();
        }

        $user = User::select('id', 'password', $user_login_type, 'email_verified')->where($user_login_type, $validated["username"])->first();
        if (!$user || !Hash::check($validated["password"], $user->password)) {
            return response()->json([
                'message' => __('Invalid ' . $user_login_type . ' or Password')
            ])->setStatusCode(422);
        }

        $token = $user->createToken(Str::slug(get_static_option('site_title', 'Nazmart')) . 'api_keys')->plainTextToken;

        return response()->json([
            'users' => $user,
            'token' => $token,
        ]);
    }

    public function username(Request $request)
    {
        $validated = $request->validate([
            'username' => 'required'
        ]);

        $username = User::where('username', $validated['username'])->first();

        return response()->json([
            'username' => $validated['username'],
            'msg' => empty($username) ? $validated['username'].' name is available' : $validated['username'].' name is already taken',
            'type' => empty($username) ? 'success' : 'danger',
        ]);
    }

    //social login
    public function socialLogin(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        // Set login type
        $user_login_type = UserServices::loginUserType($request->email);

        if ($user_login_type == 'email' && !UserServices::isValideEmail($request->email)) {
            return UserServices::emailValidationResponse();
        }

        $username = $request->isGoogle === 0 ? 'fb_' . Str::slug($request->displayName) : 'gl_' . Str::slug($request->displayName);
        $user = User::select('id', 'email', 'username')
            ->where('email', $request->email)
            ->first();

        if (User::where("username", $username)->count() > 0) {
            $username = $username . uniqid();
        }

        if (is_null($user)) {
            $user = User::create([
                'name' => $request->displayName,
                'email' => $request->email,
                'username' => $username,
                'password' => Hash::make(\Str::random(8)),
                'terms_condition' => 1,
                'google_id' => $request->isGoogle == 1 ? $request->id : null,
                'facebook_id' => $request->isGoogle == 0 ? $request->id : null
            ]);
        }

        $token = $user->createToken(Str::slug(get_static_option('site_title', 'qixer')) . 'api_keys')->plainTextToken;

        return response()->json([
            'users' => $user,
            'token' => $token,
        ]);
    }

    //register api
    public function register(Request $request)
    {
        $validate = UserServices::validateRegisterRequest($request->all());

        if ($validate->fails()) {
            return UserServices::validationErrorsResponse($validate);
        }

        $validated = $validate->validated();
        if (!UserServices::isValideEmail($validated["email"])) {
            return UserServices::emailValidationResponse();
        }

        $user = UserServices::createNewUser($validated);

        if (!is_null($user)) {
            $token = $user->createToken(Str::slug(get_static_option('site_title', 'Nazmart')) . 'api_keys')->plainTextToken;
            return response()->json([
                'users' => $user,
                'token' => $token,
            ]);
        }

        return response()->json([
            'message' => __('Something Went Wrong'),
        ])->setStatusCode(422);
    }

    public function get_all_shipping_address()
    {
        $user_id = auth('sanctum')->user()->id;

        return response()->json(["data" => UserDeliveryAddress::with(["state", "country:id,name", "state:id,name", "city_rel:id,name"])->where('user_id', $user_id)->get()]);
    }

    // send otp
    public function sendOTPSuccess(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'user_id' => 'required|integer',
            'email_verified' => 'required|integer',
        ]);

        if ($validate->fails()) {
            return response()->json([
                'validation_errors' => $validate->messages()
            ])->setStatusCode(422);
        }

        if (!in_array($request->email_verified, [0, 1])) {
            return response()->json([
                'message' => __('email verify code must have to be 1 or 0'),
            ])->setStatusCode(422);
        }

        $user = User::where('id', $request->user_id)->update([
            'email_verified' => $request->email_verified
        ]);

        if (is_null($user)) {
            return response()->json([
                'message' => __('Something went wrong, please try after sometime,'),
            ])->setStatusCode(422);
        }

        return response()->json([
            'message' => __('Email Verify Success'),
        ]);
    }

    public function sendOTP(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'email' => 'required',
        ]);

        if ($validate->fails()) {

            return response()->json([
                'validation_errors' => $validate->messages()
            ])->setStatusCode(422);
        }
        $otp_code = sprintf("%d", random_int(1234, 9999));
        $user_email = User::where('email', $request->email)->first();

        if (!is_null($user_email)) {
            try {
                $message_body = __('Here is your otp code') . ' <span class="verify-code">' . $otp_code . '</span>';
                Mail::to($request->email)->send(new BasicMail($message_body, __('Your OTP Code')));
            } catch (\Exception $e) {
                return response()->json([
                    'message' => $e->getMessage(),
                ])->setStatusCode(422);
            }

            return response()->json([
                'email' => $request->email,
                'otp' => $otp_code,
            ]);

        }

        return response()->json([
            'message' => __('Email Does not Exists'),
        ])->setStatusCode(422);

    }

    //reset password
    public function resetPassword(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'email' => 'required',
            'password' => 'required',
        ]);

        if ($validate->fails()) {
            return response()->josn([
                'validation_errors' => $validate->messages()
            ])->setStatusCode(422);
        }
        $email = $request->email;
        $user = User::select('email')->where('email', $email)->first();
        if (!is_null($user)) {
            User::where('email', $user->email)->update([
                'password' => Hash::make($request->password),
            ]);
            return response()->json([
                'message' => 'success',
            ]);
        } else {
            return response()->json([
                'message' => __('Email Not Found'),
            ])->setStatusCode(422);
        }
    }

    //logout
    public function logout()
    {
        auth()->user()->tokens()->delete();

        return response()->json([
            'message' => __('Logout Success'),
        ]);
    }

    //User Profile
    public function profile()
    {
        $user_id = auth('sanctum')->id();
        $user = User::with('userCountry', 'userState', 'userCity','delivery_address')
            ->where('id', $user_id)->first();

        $image_url = null;
        if (!empty($user->image)) {
            $image = get_attachment_image_by_id($user->image);
            $image_url = !empty($image) ? $image['img_url'] : '';
        }

        $user->profile_image_url = $image_url ?: null;

        return response()->json([
            'user_details' => $user
        ]);
    }

//    change password after login
    public function changePassword(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'current_password' => 'required|min:6',
            'new_password' => 'required|min:6',
        ]);
        if ($validate->fails()) {
            return response()->json([
                'validation_errors' => $validate->messages()
            ])->setStatusCode(422);
        }

        $user = User::select('id', 'password')->where('id', auth('sanctum')->user()->id)->first();
        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'message' => __('Current Password is Wrong'),
            ])->setStatusCode(422);
        }

        User::where('id', auth('sanctum')->user()->id)->update([
            'password' => Hash::make($request->new_password),
        ]);

        return response()->json([
            'current_password' => $request->current_password,
            'new_password' => $request->new_password,
        ]);
    }

    public function updateProfile(Request $request)
    {
        $user = auth('sanctum')->user();
        $user_id = auth('sanctum')->user()->id;

        $request->validate([
            'full_name' => 'required|string|max:191',
            'email' => 'required|email|max:191|unique:users,id,' . $request->user_id,
            'country_code' => 'nullable|string|max:191',
            'mobile' => 'nullable|string|max:191',
            'country_id' => 'required',
            'state_id' => 'required',
            'city_id' => 'nullable',
            'postal_code' => 'nullable|string|max:191',
            'address' => 'nullable|string',
        ], [
            'full_name.' => __('name is required'),
            'email.required' => __('email is required'),
            'email.email' => __('provide valid email'),
        ]);

        if ($request->file('file')) {
            (new MediaUploaderController())->upload_media_file($request);
            $last_image_id = DB::getPdo()->lastInsertId();
        }

        User::find($user_id)->update(
            [
                'name' => $request->full_name,
                'email' => $request->email,
                'image' => $last_image_id ?? $user->image,
                'mobile' => ($request->country_code ?? '') . $request->mobile,
                'country_id' => $request->country_id,
                'state_id' => $request->state_id,
                'city_id' => $request->city_id,
                'postal_code' => $request->postal_code,
                'address' => $request->address,
            ]
        );

        return response()->json(['success' => true]);
    }

    public function get_all_tickets()
    {
        $user_id = auth('sanctum')->user()->id;

        return SupportTicket::where('user_id', $user_id)->paginate(10)->withQueryString();
    }

    public function single_ticket($id)
    {
        $user_id = auth('sanctum')->user()->id;

        $ticket_details = SupportTicket::where('user_id', $user_id)
            ->where("id", $id)
            ->first();
        $all_messages = SupportTicketMessage::where(['support_ticket_id' => $id])->get()->transform(function ($item) {
            $item->attachment = !empty($item->attachment) ? global_asset('assets/uploads/ticket/' . $item->attachment) : null;

            return $item;
        });

        return response()->json(["ticket_details" => $ticket_details, "all_messages" => $all_messages]);
    }

    public function fetch_support_chat($ticket_id)
    {
        $all_messages = SupportTicketMessage::where(['support_ticket_id' => $ticket_id])->get()->transform(function ($item) {
            $item->attachment = !empty($item->attachment) ? global_asset('assets/uploads/ticket/' . $item->attachment) : null;

            return $item;
        });

        return response()->json($all_messages);
    }

    public function priority_change(Request $request)
    {
        $request->validate(['priority' => 'required|string|max:191']);

        SupportTicket::findOrFail($request->id)->update([
            'priority' => $request->priority,
        ]);
        return response()->json(['success' => true]);
    }

    public function status_change(Request $request)
    {
        $request->validate(['status' => 'required|string|max:191']);

        SupportTicket::findOrFail($request->id)->update([
            'status' => $request->status,
        ]);
        return response()->json(['success' => true]);
    }

    public function trackOrder(Request $request)
    {
        $request->validate([
            'order_id' => 'required|numeric',
            'email' => 'required|email'
        ]);

        $sell_info = ProductSellInfo::where('id', $request->order_id)
            ->where('email', $request->email)
            ->first();

        if ($sell_info) {
            try {
                \Mail::to($sell_info->email)->send(new TrackOrder(
                    $sell_info
                ));

                return response()->json(["payment_status" => ucwords($sell_info->payment_status), "order_status" => ucwords($sell_info->status)]);
            } catch (\Exception $e) {
                return response()->json(["msg" => "Server error"]);
            }
        }

        return response()->json(["msg" => __('No order found for the given information.')]);
    }

    public function send_support_chat(Request $request, $ticket_id)
    {
        $request->validate([
            'user_type' => 'required|string|max:191',
            'message' => 'required',
            'send_notify_mail' => 'nullable|string',
            'file' => 'nullable|mimes:zip,jpg,jpeg,png,gif',
        ]);

        $ticket_info = SupportTicketMessage::create([
            'support_ticket_id' => $ticket_id,
            'type' => $request->user_type,
            'message' => $request->message,
            'notify' => $request->send_notify_mail ? 'on' : 'off',
            'attachment' => null,
        ]);

        if ($request->hasFile('file')) {
            $uploaded_file = $request->file;
            $file_extension = $uploaded_file->getClientOriginalExtension();
            $file_name = pathinfo($uploaded_file->getClientOriginalName(), PATHINFO_FILENAME) . time() . '.' . $file_extension;
            $uploaded_file->move('assets/uploads/ticket', $file_name);
            $ticket_info->attachment = $file_name;
            $ticket_info->save();
        }

        $ticket = $ticket_info->toArray();
        $ticket["attachment"] = empty($ticket["attachment"]) ? null : global_asset('assets/uploads/ticket/' . $ticket["attachment"]);

        return response()->json($ticket);
    }

    public function storeShippingAddress(StoreShippingAddressRequest $request)
    {
        $data = $request->validated();

        return ShippingAddressServices::store($data);
    }

    public function viewTickets(Request $request, $id = null)
    {
        $all_messages = SupportTicketMessage::where(['support_ticket_id' => $id])->get()->transform(function ($item) {
            $item->attachment = !empty($item->attachment) ? asset('assets/uploads/ticket/' . $item->attachment) : null;
            return $item;
        });
        $q = $request->q ?? '';
        return response()->json([
            'ticket_id' => $id,
            'all_messages' => $all_messages,
            'q' => $q,
        ]);
    }

    public function sendMessage(Request $request)
    {
        $request->validate([
            'ticket_id' => 'required',
            'user_type' => 'required|string|max:191',
            'message' => 'required',
            'file' => 'nullable|mimes:jpg,png,jpeg,gif',
        ]);

        $ticket_info = SupportTicketMessage::create([
            'support_ticket_id' => $request->ticket_id,
            'type' => $request->user_type,
            'message' => $request->message,
        ]);

        if ($request->hasFile('file')) {
            $uploaded_file = $request->file;
            $file_extension = $uploaded_file->extension();
            $file_name = pathinfo($uploaded_file->getClientOriginalName(), PATHINFO_FILENAME) . time() . '.' . $file_extension;
            $uploaded_file->move('assets/uploads/ticket', $file_name);
            $ticket_info->attachment = $file_name;
            $ticket_info->save();
        }

        return response()->json([
            'message' => __('Message Send Success'),
            'ticket_id' => $request->ticket_id,
            'user_type' => $request->user_type,
            'ticket_info' => $ticket_info,
        ]);
    }

    public function get_department()
    {

        $data = SupportDepartment::select("id", "name", "status")->where(['status' => 1])->get();
        return response()->json(["data" => $data]);
    }

    public function createTicket(Request $request)
    {
        $user_info = auth('sanctum')->user()->id;
        $request->validate([
            'title' => 'required|string|max:191',
            'subject' => 'required|string|max:191',
            'priority' => 'required|string|max:191',
            'description' => 'required|string',
            'departments' => 'required|string',
        ], [
            'title.required' => __('title required'),
            'subject.required' => __('subject required'),
            'priority.required' => __('priority required'),
            'description.required' => __('description required'),
            'departments.required' => __('departments required'),
        ]);

        $ticket = SupportTicket::create([
            'title' => $request->title,
            'via' => $request->via,
            'operating_system' => null,
            'user_agent' => $_SERVER['HTTP_USER_AGENT'],
            'description' => $request->description,
            'subject' => $request->subject,
            'status' => 'open',
            'priority' => $request->priority,
            'user_id' => $user_info,
            'admin_id' => null,
            'departments' => $request->departments
        ]);

        $msg = get_static_option('support_ticket_success_message') ?? __('Thanks for contact us, we will reply soon');

        return response()->json(["msg" => $msg, "ticket" => $ticket]);
    }

    public function delete_shipping_address($shipping_id)
    {
        $shipping = UserDeliveryAddress::find($shipping_id);
        if (empty($shipping)) {
            return response()->json(["msg" => __("Shipping zone not found on the server.")])->setStatusCode(404);
        }

        $bool = $shipping->user_id == auth('sanctum')->id() ? $shipping->delete() : false;
        $msg = $bool ? __("Successfully Deleted Shipping Zone") : __("You are not eligible to delete this shipping address");

        return response()->json(["msg" => $msg]);
    }

    public function all_order_list()
    {
        $user_id = auth('sanctum')->user()->id;

        return ProductOrder::with('getCountry', 'getState', 'getCity')->where('user_id', $user_id)->paginate(10)->withQueryString();
    }

    public function single_order_details($order_id)
    {
        $user_id = auth('sanctum')->user()->id;
        $order_details = ProductOrder::with('getCountry', 'getState', 'getCity')->where(['user_id' => $user_id, 'id' => $order_id])->first();

        $details = [];
        foreach (json_decode($order_details->order_details) as $key => $order)
        {
            $image = get_attachment_image_by_id($order?->options?->image);
            $image_url = !empty($image) ? $image['img_url'] : '';

            unset($order->options->image);
            $order->options->image = $image_url;
            $details[$key] = $order;
        }

        unset($order_details->order_details);
        $order_details->order_details = $details;

        return response()->json(['data' => $order_details]);
    }

    public function request_order_refund(Request $request)
    {
        $validated = $request->validate([
            'order_id' => 'required|numeric',
            'refund_products' => 'required|string'
        ]);

        $refund_products = explode(',', $validated['refund_products']);

        $user_id = auth('sanctum')->user()->id;
        $existing_order = ProductOrder::find($validated['order_id']);
        if (empty($existing_order))
        {
            return response()->json([
                'msg' => __('Order is not available')
            ]);
        }

        foreach ($refund_products ?? [] as $key => $product)
        {
            $order_product = OrderProducts::where('product_id', $product)->first();
            if (empty($order_product)) {
                return response()->json([
                    'msg' => __('Product is not available')
                ]);
            }

            $refund = RefundProduct::where([
                'user_id' => $user_id,
                'order_id' => $validated['order_id'],
                'product_id' => $product,
            ])->first();

            if (empty($refund))
            {
                RefundProduct::create([
                    'user_id' => $user_id,
                    'order_id' => $validated['order_id'],
                    'product_id' => $product,
                    'status' => false,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            } else {
                return response()->json([
                    'msg' => __('Refund request is already sent')
                ]);
            }
        }

        return response()->json([
            'msg' => __('Your refund request is sent successfully')
        ]);
    }

    public function get_all_refund_list()
    {
        $user_id = auth('sanctum')->user()->id;

        return RefundProduct::with('user', 'user.userCountry:id,name', 'user.userState:id,name', 'user.userCity:id,name', 'product')->where('user_id', $user_id)->paginate(10)->withQueryString();
    }

    public function refund_create_ticket(Request $request)
    {
        $user_info = auth('sanctum')->user()->id;
        $request->validate([
            'title' => 'required|string|max:191',
            'subject' => 'required|string|max:191',
            'description' => 'required|string'
        ], [
            'title.required' => __('title required'),
            'subject.required' => __('subject required'),
            'priority.required' => __('priority required'),
            'description.required' => __('description required'),
        ]);

        $ticket = RefundChat::create([
            'title' => $request->title,
            'via' => 'app',
            'operating_system' => null,
            'user_agent' => $_SERVER['HTTP_USER_AGENT'],
            'description' => $request->description,
            'subject' => $request->subject,
            'status' => 'open',
            'user_id' => $user_info,
            'admin_id' => null,
        ]);

        $msg = get_static_option('support_ticket_success_message') ?? __('Thanks for contact us, we will reply soon');

        return response()->json(["msg" => $msg, "ticket" => $ticket]);
    }

    public function refund_get_all_tickets()
    {
        $user_id = auth('sanctum')->user()->id;

        return RefundChat::where('user_id', $user_id)->paginate(10)->withQueryString();
    }

    public function refund_single_ticket($id)
    {
        $user_id = auth('sanctum')->user()->id;

        $ticket_details = RefundChat::where('user_id', $user_id)
            ->where("id", $id)
            ->first();

        $all_messages = RefundChatMessage::where(['refund_chat_id' => $id])->get()->transform(function ($item) {
            $item->attachment = !empty($item->attachment) ? global_asset('assets/uploads/refund_chat/' . $item->attachment) : null;

            return $item;
        });

        return response()->json(["ticket_details" => $ticket_details, "all_messages" => $all_messages]);
    }

    public function refund_fetch_support_chat($refund_ticket_id)
    {
        $all_messages = RefundChatMessage::where(['refund_chat_id' => $refund_ticket_id])->get()->transform(function ($item) {
            $item->attachment = !empty($item->attachment) ? global_asset('assets/uploads/refund_chat/' . $item->attachment) : null;

            return $item;
        });

        return response()->json($all_messages);
    }

    public function refund_send_support_chat(Request $request, $ticket_id)
    {
        $request->validate([
            'user_type' => 'required|string|max:191',
            'message' => 'required',
            'send_notify_mail' => 'nullable|string',
            'file' => 'nullable|mimes:zip,jpg,jpeg,png,gif',
        ]);

        $user_id = auth('sanctum')->user()->id;

        $ticket_info = RefundChatMessage::create([
            'refund_chat_id' => $ticket_id,
            'user_id' => $user_id,
            'type' => $request->user_type,
            'message' => $request->message,
            'notify' => $request->send_notify_mail ? 'on' : 'off',
            'attachment' => null,
        ]);

        if ($request->hasFile('file')) {
            $uploaded_file = $request->file;
            $file_extension = $uploaded_file->getClientOriginalExtension();
            $file_name = pathinfo($uploaded_file->getClientOriginalName(), PATHINFO_FILENAME) . time() . '.' . $file_extension;
            $uploaded_file->move('assets/uploads/refund_chat', $file_name);
            $ticket_info->attachment = $file_name;
            $ticket_info->save();
        }

        $ticket = $ticket_info->toArray();
        $ticket["attachment"] = empty($ticket["attachment"]) ? null : global_asset('assets/uploads/refund_chat/' . $ticket["attachment"]);

        return response()->json($ticket);
    }

    public function deleteAccount($id)
    {
        $user_id = auth('sanctum')->user()->id;
        if ($user_id != $id)
        {
            return response()->json([
                'message' => __('Something Went Wrong'),
            ])->setStatusCode(422);
        }

        $user = User::find($user_id);
        $user->tokens()->delete();
        $user->delete();

        return response()->json([
            'msg' => __('Your account is deleted')
        ]);
    }
}
