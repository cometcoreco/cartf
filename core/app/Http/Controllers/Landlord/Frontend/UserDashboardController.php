<?php

namespace App\Http\Controllers\Landlord\Frontend;

use App\Enums\PricePlanTypEnums;
use App\Events\SupportMessage;
use App\Helpers\LanguageHelper;
use App\Helpers\ResponseMessage;
use App\Helpers\SanitizeInput;
use App\Http\Controllers\Controller;
use App\Mail\BasicMail;
use App\Models\CustomDomain;
use App\Models\Newsletter;
use App\Models\Order;
use App\Models\Page;
use App\Models\PaymentLogs;
use App\Models\PricePlan;
use App\Models\SupportTicket;
use App\Models\SupportTicketMessage;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use LaravelDaily\Invoices\Classes\InvoiceItem;
use LaravelDaily\Invoices\Classes\Party;
use LaravelDaily\Invoices\Invoice;
use Modules\Blog\Entities\Blog;
use Barryvdh\DomPDF\Facade as PDF;
use Stancl\Tenancy\Database\Models\Domain;


class UserDashboardController extends Controller
{
    const BASE_PATH = 'landlord.frontend.user.dashboard.';

    public function __construct()
    {
        $this->middleware(['auth']);
    }

    public function user_index()
    {
        $package_orders = PaymentLogs::where('user_id', $this->logged_user_details()->id)->count();
        $support_tickets = SupportTicket::where('user_id', $this->logged_user_details()->id)->count();
        $recent_logs = PaymentLogs::where('user_id', $this->logged_user_details()->id)->orderBy('id', 'desc')->take(4)->get();
        return view(self::BASE_PATH . 'user-home')->with(
            [
                'package_orders' => $package_orders,
                'support_tickets' => $support_tickets,
                'recent_logs' => $recent_logs,
            ]);
    }

    // Tenant User
    public function user_email_verify_index()
    {
        $user_details = Auth::guard('web')->user();
        if (get_static_option('user_email_verify_status') && $user_details->email_verified == 1) {
            return redirect()->route('user.home');
        }

        if (is_null($user_details->email_verify_token)) {
            Tenant::find($user_details->id)->update(['email_verify_token' => Str::random(20)]);
            $user_details = Tenant::find($user_details->id);

            $message_body = __('Here is your verification code : ') . ' <span class="verify-code"> <b>' . $user_details->email_verify_token . '</b></span>';

            try {
                Mail::to($user_details->email)->send(new BasicMail([
                    'subject' => __('Verify your email address'),
                    'message' => $message_body
                ]));
            } catch (\Exception $e) {
                //hanle error
            }
        }
        return view('tenant.frontend.user.email-verify');
    }

    public function reset_user_email_verify_code()
    {
        $user_details = Auth::guard('web')->user();
        if ($user_details->email_verified == 1) {
            return redirect()->route('landlord.user.home');
        }

        $message_body = __('Here is your verification code : ') . ' <span class="verify-code">' . $user_details->email_verify_token . '</span>';
        try {

        } catch (\Exception $e) {
            Mail::to($user_details->email)->send(new BasicMail([
                'subject' => __('Verify your email address'),
                'message' => $message_body
            ]));
        }

        return redirect()->route('landlord.user.email.verify')->with(['msg' => __('Resend Verify Email Success'), 'type' => 'success']);
    }

    // Tenant Admin
    public function user_email_verify(Request $request)
    {
        $this->validate($request, [
            'verification_code' => 'required'
        ], [
            'verification_code.required' => __('verify code is required')
        ]);
        $user_details = Auth::guard('web')->user();
        $user_info = Tenant::where(['id' => $user_details->id, 'email_verify_token' => $request->verification_code])->first();
        if (empty($user_info)) {
            return redirect()->back()->with(['msg' => __('your verification code is wrong, try again'), 'type' => 'danger']);
        }
        $user_info->email_verified = 1;
        $user_info->save();
        return redirect()->route('landlord.user.home');
    }

    public function user_profile_update(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|string|max:191',
            'email' => 'required|email|max:191',
            'phone' => 'nullable|string|max:191',
            'state' => 'nullable|string|max:191',
            'city' => 'nullable|string|max:191',
            'zipcode' => 'nullable|string|max:191',
            'country' => 'nullable|string|max:191',
            'address' => 'nullable|string',
            'image' => 'nullable|string',
        ], [
            'name.' => __('name is required'),
            'email.required' => __('email is required'),
            'email.email' => __('provide valid email'),
        ]);
        User::find(Auth::guard('web')->user()->id)->update([
                'name' => SanitizeInput::esc_html($request->name),
                'email' => SanitizeInput::esc_html($request->email),
                'mobile' => SanitizeInput::esc_html($request->mobile),
                'company' => SanitizeInput::esc_html($request->company),
                'address' => SanitizeInput::esc_html($request->address),
                'state' => SanitizeInput::esc_html($request->state),
                'city' => SanitizeInput::esc_html($request->city),
                'country' => SanitizeInput::esc_html($request->country),
                'image' => $request->image
            ]
        );

        return redirect()->back()->with(['msg' => __('Profile Update Success'), 'type' => 'success']);
    }

    public function user_password_change(Request $request)
    {
        $this->validate($request, [
            'old_password' => 'required|string',
            'password' => 'required|string|min:8|confirmed'
        ],
            [
                'old_password.required' => __('Old password is required'),
                'password.required' => __('Password is required'),
                'password.confirmed' => __('password must have be confirmed')
            ]
        );

        $user = User::findOrFail(Auth::guard('web')->user()->id);

        if (Hash::check($request->old_password, $user->password)) {

            $user->password = Hash::make($request->password);
            $user->save();
            Auth::guard('web')->logout();

            return redirect()->route('landlord.user.login')->with(['msg' => __('Password Changed Successfully'), 'type' => 'success']);
        }

        return redirect()->back()->with(['msg' => __('Somethings Going Wrong! Please Try Again or Check Your Old Password'), 'type' => 'danger']);
    }


    public function logged_user_details()
    {
        $old_details = '';
        if (empty($old_details)) {
            $old_details = User::findOrFail(Auth::guard('web')->user()->id);
        }
        return $old_details;
    }

    public function edit_profile()
    {
        return view(self::BASE_PATH . 'edit-profile')->with(['user_details' => $this->logged_user_details()]);
    }

    public function change_password()
    {
        return view(self::BASE_PATH . 'change-password');
    }

    public function support_tickets()
    {
        $all_tickets = SupportTicket::where('user_id', $this->logged_user_details()->id)->paginate(10);
        return view(self::BASE_PATH . 'support-tickets')->with(['all_tickets' => $all_tickets]);
    }

    public function support_ticket_priority_change(Request $request)
    {
        $this->validate($request, [
            'priority' => 'required|string|max:191'
        ]);
        SupportTicket::findOrFail($request->id)->update([
            'priority' => $request->priority,
        ]);
        return 'ok';
    }

    public function support_ticket_status_change(Request $request)
    {
        $this->validate($request, [
            'status' => 'required|string|max:191'
        ]);
        SupportTicket::findOrFail($request->id)->update([
            'status' => $request->status,
        ]);
        return 'ok';
    }

    public function support_ticket_view(Request $request, $id)
    {
        $ticket_details = SupportTicket::findOrFail($id);
        $all_messages = SupportTicketMessage::where(['support_ticket_id' => $id])->get();
        $q = $request->q ?? '';
        return view(self::BASE_PATH . 'view-ticket')->with(['ticket_details' => $ticket_details, 'all_messages' => $all_messages, 'q' => $q]);
    }

    public function support_ticket_message(Request $request)
    {
        $this->validate($request, [
            'ticket_id' => 'required',
            'user_type' => 'required|string|max:191',
            'message' => 'required',
            'send_notify_mail' => 'nullable|string',
            'file' => 'nullable|mimes:zip',
        ]);

        $ticket_info = SupportTicketMessage::create([
            'support_ticket_id' => $request->ticket_id,
            'user_id' => Auth::guard('web')->id(),
            'type' => $request->user_type,
            'message' => str_replace('script', '', $request->message),
            'notify' => $request->send_notify_mail ? 'on' : 'off',
        ]);

        if ($request->hasFile('file')) {
            $uploaded_file = $request->file;
            $file_extension = $uploaded_file->getClientOriginalExtension();
            $file_name = pathinfo($uploaded_file->getClientOriginalName(), PATHINFO_FILENAME) . time() . '.' . $file_extension;
            $uploaded_file->move('assets/uploads/ticket', $file_name);
            $ticket_info->attachment = $file_name;
            $ticket_info->save();
        }

        //send mail to user
        event(new SupportMessage($ticket_info));

        return redirect()->back()->with(['msg' => __('Mail Send Success'), 'type' => 'success']);
    }

    public function package_orders()
    {
        $package_orders = PaymentLogs::where('user_id', $this->logged_user_details()->id)->orderBy('id', 'DESC')->paginate(5);
        return view(self::BASE_PATH . 'package-order')->with(['package_orders' => $package_orders]);
    }


    public function generate_package_invoice(Request $request)
    {
        $payment_details = PaymentLogs::find($request->id);
        abort_if(empty($payment_details), 404);

        $invoice = $this->invoice_design($payment_details);
        return $invoice->stream();
    }

    private function invoice_design($payment_details)
    {
        $client = new Party([
            'name' => site_title(),
            'custom_fields' => [
                'email' => get_static_option('site_global_email'),
                'website' => str_replace(['http://', 'https://'], '', url('/'))
            ]
        ]);

        $user = User::find($payment_details->user_id);
        $customer = new Party([
            'name' => $payment_details->name,
            'phone' => $payment_details->phone,
            'custom_fields' => [
                'username' => $user->username,
                'email' => $payment_details->email
            ]
        ]);

        $currency_symbol = site_currency_symbol();
        $currency = site_currency_symbol(true);
        $currency_symbol_position = get_static_option('site_currency_symbol_position');

        $payment_status = $payment_details->payment_status == 'complete' ? __('paid') : __('unpaid');
        if ($payment_details->status == 'cancel') {
            $payment_status = __('cancel');
        }
        if ($payment_details->status == 'trial')
        {
            $payment_status = __('unpaid').'-'.__('trial');
        }

        $invoice_number_padding = get_static_option('invoice_number_padding') ?? 2;
        $thousand_separator = get_static_option('site_custom_currency_thousand_separator') ?? ',';
        $decimal_separator = get_static_option('site_custom_currency_decimal_separator') ?? '.';
        $currency_fraction = get_static_option('currency_fraction_code') ?? 'cents.';

        $site_logo = get_attachment_image_by_id(get_static_option('site_logo'))['img_url'] ?? '';

        $tenant = Tenant::find($payment_details->tenant_id);
        $package_details = PricePlan::find($payment_details->package_id);

        $package_title = '';
        $description = '';
        if ($tenant)
        {
            $package_title = __('Package:').' '.$package_details->title;
            $description = '<p>Shop Name: '.$tenant->id.'</p>';
            $description .= '<p>Order Date: '.Carbon::parse($payment_details->created_at)->format('d/m/Y').'</p>';
            $description .= '<p>Expire Date: '.Carbon::parse($tenant->expire_date)->format('d/m/Y').'</p>';
        }

        $InvoiceItem = (new InvoiceItem())
            ->title($package_title)
            ->description($description)
            ->pricePerUnit($package_details->price);

        $invoiceInstance = Invoice::make(site_title() .' - '.__('Order Invoice'))
            ->template('landlord')
            ->status($payment_status)
            ->sequence($payment_details->id)
            ->sequencePadding($invoice_number_padding)
            ->serialNumberFormat('{SEQUENCE}')
            ->seller($client)
            ->buyer($customer)
            ->date(now())
            ->dateFormat('d/m/Y')
            ->currencySymbol($currency_symbol)
            ->currencyCode($currency)
            ->currencyFormat($currency_symbol_position == 'left' ? '{SYMBOL}{VALUE}' : '{VALUE}{SYMBOL}')
            ->currencyThousandsSeparator($thousand_separator)
            ->currencyDecimalPoint($decimal_separator)
            ->currencyFraction($currency_fraction)
            ->addItem($InvoiceItem)
            ->payUntilDays(1)
            ->logo($site_logo);

        return $invoiceInstance->save();
    }

    public function order_details($id)
    {

        $order_details = Order::find($id);
        if (empty($order_details)) {
            abort(404);
        }
        $package_details = PricePlan::find($order_details->package_id);
        $payment_details = PaymentLogs::where('order_id', $id)->first();
        return view('frontend.pages.package.view-order')->with(
            [
                'order_details' => $order_details,
                'package_details' => $package_details,
                'payment_details' => $payment_details,
            ]
        );
    }

    public function package_order_cancel(Request $request)
    {
        $this->validate($request, [
            'order_id' => 'required'
        ]);

        $order_details = PaymentLogs::where(['id' => $request->order_id, 'user_id' => Auth::guard('web')->user()->id])->first();

        //send mail to admin
        $order_page_form_mail = get_static_option('order_page_form_mail');
        $order_mail = $order_page_form_mail ? $order_page_form_mail : get_static_option('site_global_email');
        $order_details->status = 'cancel';
        $order_details->save();
        //send mail to customer
        $data['subject'] = __('one of your package order has been cancelled');
        $data['message'] = __('hello') . '<br>';
        $data['message'] .= __('your package order ') . ' #' . $order_details->id . ' ';
        $data['message'] .= __('has been cancelled by the user');

        //send mail while order status change
        try {
            Mail::to($order_mail)->send(new BasicMail($data['message'], $data['subject']));
        } catch (\Exception $e) {
            //handle error
            return redirect()->back()->with(['msg' => __('Order Cancel, mail send failed'), 'type' => 'warning']);
        }
        if (!empty($order_details)) {
            //send mail to customer
            $data['subject'] = __('your order status has been cancel');
            $data['message'] = __('hello') . '<br>';
            $data['message'] .= __('your order') . ' #' . $order_details->id . ' ';
            $data['message'] .= __('status has been changed to cancel');
            try {
                //send mail while order status change
                Mail::to($order_details->email)->send(new BasicMail($data['message'], $data['subject']));
            } catch (\Exception $e) {
                //handle error
                return redirect()->back()->with(['msg' => __('Order Cancel, mail send failed'), 'type' => 'warning']);
            }

        }
        return redirect()->back()->with(['msg' => __('Order Cancel'), 'type' => 'warning']);
    }

    public function custom_domain()
    {
        $user_id = Auth::guard('web')->user()->id;
        $user_domain_infos = User::with('tenant_details')->find($user_id);

        return view(self::BASE_PATH . 'custom-domain', compact('user_domain_infos'));
    }

    public function submit_custom_domain(Request $request)
    {
        $request->validate([
            'old_domain' => 'required',
            'custom_domain' => 'required|regex:/^([a-zA-Z0-9-]+\.)+[a-zA-Z0-9]{2,}+$/',
        ]);

        $tenant = Tenant::findOrFail($request->old_domain);
        if (!empty($tenant) && !tenant_plan_sidebar_permission('custom_domain',$tenant))
        {
            return response()->danger(ResponseMessage::delete(__('Your request can not be processed')));
        }

        $all_tenant = Domain::where('tenant_id', $request->custom_domain)->first();

        if (!is_null($all_tenant)) {
            return response()->danger(ResponseMessage::delete(__('You can not add this as your domain, this is reserved to landlord hosting domain')));
        }

        CustomDomain::updateOrCreate(
            [
                'user_id' => $request->user_id,
                'old_domain' => $request->old_domain
            ],
            [
                'user_id' => $request->user_id,
                'old_domain' => $request->old_domain,
                'custom_domain' => $request->custom_domain,
                'custom_domain_status' => 'pending'
            ]
        );

        return response()->success(ResponseMessage::SettingsSaved(__('Custom domain change request sent successfully..!')));
    }

    public function package_check(Request $request)
    {
        $validated = $request->validate([
            'package_id' => 'required|integer|exists:price_plans,id',
            'subdomain' => 'nullable'
        ]);

        $price_plan = PricePlan::findOrFail($validated['package_id']);
        $price = $price_plan->price;
        $type = PricePlanTypEnums::getText($price_plan->type);
        $validity = $this->getValidity($price_plan)['package_expire_date'] ?? __('Lifetime');

        $tenant = Tenant::find($validated['subdomain'] ?? '');

        $markup = '';
        $plan_themes = $price_plan->plan_themes;
        foreach ($plan_themes ?? [] as $theme)
        {
            $selected = !empty($tenant) ? ($tenant->theme_slug == $theme->theme_slug ? 'selected' : '') : (get_static_option('default_theme') == $theme->theme_slug ? 'selected' : '');
            $markup .= "<option value='".$theme->theme_slug."' ${selected}>".$theme->theme_slug."</option>";
        }

        return response()->json([
            'price' => $price,
            'type' => $type,
            'validity' => $validity,
            'theme_list' => $markup,
            'theme' => $tenant->theme_slug ?? null
        ]);
    }

    private function getValidity($package)
    {
        $package_start_date = '';
        $package_expire_date =  '';
        if(!empty($package)){
            if($package->type == 0){ //monthly
                $package_start_date = Carbon::now()->format('d-m-Y h:i:s');
                $package_expire_date = Carbon::now()->addMonth()->format('d-m-Y h:i:s');

            }elseif ($package->type == 1){ //yearly
                $package_start_date = Carbon::now()->format('d-m-Y h:i:s');
                $package_expire_date = Carbon::now()->addYear()->format('d-m-Y h:i:s');
            }else{ //lifetime
                $package_start_date = Carbon::now()->format('d-m-Y h:i:s');
                $package_expire_date = null;
            }
        }

        return [
            'package_start_date' => $package_start_date,
            'package_expire_date' => $package_expire_date
        ];
    }
}
