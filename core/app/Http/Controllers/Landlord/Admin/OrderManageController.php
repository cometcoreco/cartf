<?php

namespace App\Http\Controllers\Landlord\Admin;

use App\Actions\Payment\PaymentGateways;
use App\Helpers\FlashMsg;
use App\Http\Controllers\Controller;
use App\Models\FormBuilder;
use App\Models\Language;
use App\Mail\BasicMail;
use App\Mail\OrderReply;
use App\Models\PaymentLogs;
use App\Models\PricePlan;
use App\Models\Tenant;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Elibyy\TCPDF\Facades\TCPDF;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use LaravelDaily\Invoices\Classes\InvoiceItem;
use LaravelDaily\Invoices\Classes\Party;
use LaravelDaily\Invoices\Invoice;
use Mccarlosen\LaravelMpdf\Facades\LaravelMpdf;

class OrderManageController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:package-order-all-order|package-order-pending-order|package-order-progress-order|
        package-order-edit|package-order-delete|package-order-complete-order|package-order-success-order-page|package-order-order-page-manage
        package-order-order-report');
    }

    private const ROOT_PATH = 'landlord.admin.package-order-manage.';

    public function all_orders(Request $request)
    {
        if ($request->filter != null && $request->filter != 'all') {
            $all_orders = PaymentLogs::where('status', $request->filter)->orderByDesc('id')->get();
        } else {
            $all_orders = PaymentLogs::orderByDesc('id')->get();
        }

        return view(self::ROOT_PATH . 'order-manage-all')->with(['all_orders' => $all_orders]);
    }

    public function view_order($id)
    {
        if (!empty($id)) {
            $order = PaymentLogs::find($id);
        }

        return view(self::ROOT_PATH . 'order-view', compact('order'));
    }

    public function pending_orders()
    {
        $all_orders = PaymentLogs::where('status', 'pending')->get();
        return view(self::ROOT_PATH . 'order-manage-pending')->with(['all_orders' => $all_orders]);
    }

    public function completed_orders()
    {
        $all_orders = PaymentLogs::where('status', 'complete')->get();
        return view(self::ROOT_PATH . 'order-manage-completed')->with(['all_orders' => $all_orders]);
    }

    public function in_progress_orders()
    {
        $all_orders = PaymentLogs::where('status', 'in_progress')->get();
        return view(self::ROOT_PATH . 'order-manage-in-progress')->with(['all_orders' => $all_orders]);
    }

    public function change_status(Request $request)
    {
        $this->validate($request, [
            'order_status' => 'required|string|max:191',
            'order_id' => 'required|string|max:191'
        ]);

        $order_details = PaymentLogs::find($request->order_id);
        $order_details->status = $request->order_status;
        $order_details->saveQuietly();
        $data['subject'] = __('your order status has been changed');
        $data['message'] = __('hello') . ' ' . $order_details->name . '<br>';
        $data['message'] .= __('your order') . ' #' . $order_details->id . ' ';
        $data['message'] .= __('status has been changed to') . ' ' . str_replace('_', ' ', $request->order_status) . '.';

        //send mail while order status change
        try {
            Mail::to($order_details->email)->send(new BasicMail($data['message'], $data['subject']));
        } catch (\Exception $e) {

            return redirect()->back()->with(['type' => 'danger', 'msg' => $e->getMessage()]);
        }


        return redirect()->back()->with(['msg' => __('PaymentLogs Status Update Success...'), 'type' => 'success']);
    }

    public function order_reminder(Request $request)
    {
        $order_details = PaymentLogs::find($request->id);
        $route = route('landlord.frontend.plan.order', optional($order_details->package)->id);
        //send order reminder mail
        $data['subject'] = __('your order is still in pending at') . ' ' . get_static_option('site_title');
        $data['message'] = __('hello') . ' ' . $order_details->name . '<br>';
        $data['message'] .= __('your order') . ' #' . $order_details->id . ' ';
        $data['message'] .= __('is still in pending, to complete your order go to');
        $data['message'] .= ' <br> <br><a href="' . $route . '">' . __('go to payment ') . '</a>';
        //send mail while order status change

        try {
            Mail::to($order_details->email)->send(new BasicMail($data['message'], $data['subject']));
        } catch (\Exception $e) {
            return redirect()->back()->with(['type' => 'danger', 'msg' => $e->getMessage()]);
        }

        return redirect()->back()->with(['msg' => __('PaymentLogs Reminder Mail Send Success...'), 'type' => 'success']);
    }

    public function order_delete(Request $request, $id)
    {
        $log = PaymentLogs::findOrFail($id);
        $user = \App\Models\User::findOrFail($log->user_id);

        if (!empty($user)) {
            return redirect()->back()->with(['msg' => __('You cannot delete this item, This data is associated with a user, please delete the user then it will be deleted automatically..!'), 'type' => 'danger']);
        }
        $log->delete();

        return redirect()->back()->with(['msg' => __('PaymentLogs Deleted Success...'), 'type' => 'danger']);
    }


    public function send_mail(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|string|max:191',
            'name' => 'required|string|max:191',
            'subject' => 'required|string',
            'message' => 'required|string',
        ]);
        $subject = str_replace('{site}', get_static_option('site_title'), $request->subject);
        $data = [
            'name' => $request->name,
            'message' => $request->message,
        ];
        try {
            Mail::to($request->email)->send(new OrderReply($data, $subject));
        } catch (\Exception $e) {
            return redirect()->back()->with(['type' => 'danger', 'msg' => $e->getMessage()]);
        }
        return redirect()->back()->with(['msg' => __('Order Reply Mail Send Success...'), 'type' => 'success']);
    }


    public function all_payment_logs()
    {
        $payment_logs = PaymentLogs::orderByDesc('id')->get();
        return view('landlord.admin.payment-logs.payment-logs-all')->with(['payment_logs' => $payment_logs]);
    }

    public function payment_logs_delete(Request $request, $id)
    {
        $log = PaymentLogs::findOrFail($id);
        $user = \App\Models\User::findOrFail($log->user_id);
        if (!empty($user)) {
            return redirect()->back()->with(['msg' => __('You cannot delete this item, This data is associated with a user, please delete the user then it will be deleted automatically..!'), 'type' => 'danger']);
        }

        $log->delete();
        return redirect()->back()->with(['msg' => __('Payment Log Delete Success...'), 'type' => 'danger']);
    }

    public function payment_logs_approve(Request $request, $id)
    {
        $payment_logs = PaymentLogs::find($id);
        $payment_logs->payment_status = 'complete';
        $payment_logs->save();

        $msg = __('Payment status changed successfully..!');
        if ($payment_logs->payment_status == 'complete') {
            (new PaymentGateways())->tenant_create_event_with_credential_mail($payment_logs->id);
            $payment_logs->order_id = $payment_logs->id;
            (new PaymentGateways())->update_tenant($payment_logs);
            $msg .= ' ' . __('And a new tenant has been created for the payment log');
        }

        $subject = __('Your order payment has been approved');
        $message = __('Your order has been approved.') . ' #' . $payment_logs->id;
        $message .= ' ' . __('Package:') . ' ' . $payment_logs->package_name;

        Mail::to($payment_logs->email)->send(new BasicMail($message, $subject));

        return redirect()->back()->with(['msg' => __('Manual Payment Accept Success'), 'type' => 'success']);
    }

    public function order_success_payment()
    {
        $all_languages = Language::all();
        return view(self::ROOT_PATH . 'order-success-page')->with(['all_languages' => $all_languages]);
    }

    public function update_order_success_payment(Request $request)
    {
        $this->validate($request, [
            'site_order_success_page_title' => 'nullable',
            'site_order_success_page_description' => 'nullable',
        ]);

        $title = 'site_order_success_page_title';
        $description = 'site_order_success_page_description';

        update_static_option($title, $request->$title);
        update_static_option($description, $request->$description);

        return redirect()->back()->with(['msg' => __('PaymentLogs Success Page Update Success...'), 'type' => 'success']);
    }

    public function order_cancel_payment()
    {
        $all_languages = Language::all();
        return view(self::ROOT_PATH . 'order-cancel-page')->with(['all_languages' => $all_languages]);
    }

    public function update_order_cancel_payment(Request $request)
    {
        $this->validate($request, [
            'site_order_cancel_page_title' => 'nullable',
            'site_order_cancel_page_subtitle' => 'nullable',
            'site_order_cancel_page_description' => 'nullable',
        ]);

        $title = 'site_order_cancel_page_title';
        $subtitle = 'site_order_cancel_page_subtitle';
        $description = 'site_order_cancel_page_description';

        update_static_option($title, $request->$title);
        update_static_option($subtitle, $request->$subtitle);
        update_static_option($description, $request->$description);

        return redirect()->back()->with(['msg' => __('PaymentLogs Cancel Page Update Success...'), 'type' => 'success']);
    }

    public function bulk_action(Request $request)
    {
        $all = PaymentLogs::find($request->ids);
        foreach ($all as $item) {
            $item->delete();
        }
        return response()->json(['status' => 'ok']);
    }

    public function payment_log_bulk_action(Request $request)
    {
        $all = PaymentLogs::find($request->ids);
        foreach ($all as $item) {
            $item->delete();
        }
        return response()->json(['status' => 'ok']);
    }

    public function order_report(Request $request)
    {

        $order_data = '';
        $query = PaymentLogs::query();
        if (!empty($request->start_date)) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        if (!empty($request->end_date)) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }
        if (!empty($request->order_status)) {
            $query->where(['status' => $request->order_status]);
        }
        if (!empty($request->payment_status)) {
            $query->where(['payment_status' => $request->payment_status]);
        }
        $error_msg = __('select start & end date to generate order report');
        if (!empty($request->start_date) && !empty($request->end_date)) {
            $query->orderBy('id', 'DESC');
            $order_data = $query->paginate($request->items);
            $error_msg = '';
        }

        return view(self::ROOT_PATH . 'order-report')->with([
            'order_data' => $order_data,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'items' => $request->items,
            'order_status' => $request->order_status,
            'payment_status' => $request->payment_status,
            'error_msg' => $error_msg
        ]);
    }

    public function payment_report(Request $request)
    {
        $order_data = '';
        $query = PaymentLogs::query();
        if (!empty($request->start_date)) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        if (!empty($request->end_date)) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }
        if (!empty($request->payment_status)) {
            $query->where(['status' => $request->payment_status]);
        }
        $error_msg = __('select start & end date to generate payment report');
        if (!empty($request->start_date) && !empty($request->end_date)) {
            $query->orderBy('id', 'DESC');
            $order_data = $query->paginate($request->items);
            $error_msg = '';
        }

        return view('landlord.admin.payment-logs.payment-report')->with([
            'order_data' => $order_data,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'items' => $request->items,
            'payment_status' => $request->payment_status,
            'error_msg' => $error_msg
        ]);
    }

    public function index()
    {
        $all_custom_form = FormBuilder::all();
        return view(self::ROOT_PATH . 'form-section')->with(['all_custom_form' => $all_custom_form]);
    }

    public function update(Request $request)
    {
        $this->validate($request, [
            'order_form' => 'nullable|string',
            'order_page_form_title' => 'nullable|string',
        ]);

        $field = 'order_page_form_title';
        update_static_option('order_page_form_title', $request->$field);
        update_static_option('order_form', $request->order_form);

        return redirect()->back()->with(['msg' => __('Settings Updated....'), 'type' => 'success']);
    }

    // todo: fix RTL issues in invoice
    public function generate_package_invoice(Request $request)
    {
        $payment_details = PaymentLogs::findOrFail($request->id);
        $invoice = $this->invoice_design($payment_details);
        return $invoice->stream();
    }

    public function generate_package_invoice_rtl(Request $request)
    {
        $payment_details = PaymentLogs::findOrFail($request->id);
        $invoice = $this->invoice_design($payment_details);
        return view('invoices::templates.landlord', compact('invoice'));
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

    public function payment_log_payment_status_change($id)
    {
        try {
            $payment_log = PaymentLogs::findOrFail($id);
            $payment_log->payment_status = 'complete';

            $payment_log->save();

            $msg = __('Payment status changed successfully..!');
            if ($payment_log->payment_status == 'complete') {
                (new PaymentGateways())->tenant_create_event_with_credential_mail($payment_log->id);
                $payment_log->order_id = $payment_log->id;
                (new PaymentGateways())->update_tenant($payment_log);

                $msg .= ' ' . __('And a new tenant has been created for the payment log');
            }
        } catch (\Exception $exception) {

        }

        return redirect()->back()->with(['msg' => $msg, 'type' => 'success']);
    }

    public function invoice_settings()
    {
        return view('landlord.admin.package-order-manage.invoice-settings');
    }

    public function invoice_settings_update(Request $request)
    {
        $validated_data = $request->validate([
            'currency_fraction_code' => 'required'
        ]);

        update_static_option('currency_fraction_code', $validated_data['currency_fraction_code']);

        return back()->with(FlashMsg::update_succeed('Invoice Settings'));
    }
}
