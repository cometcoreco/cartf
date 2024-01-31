<?php

namespace App\Console\Commands;

use App\Mail\BasicMail;
use App\Models\PaymentLogs;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class AccountRemoval extends Command
{
    protected $signature = 'account:remove';
    protected $description = 'Command description';


    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $remove_account_status = get_static_option('tenant_account_auto_remove') ?? '';
        $day_list = json_decode(get_static_option('tenant_account_delete_notify_mail_days')) ?? [];
        $remove_day = get_static_option('account_remove_day_within_expiration');

        $all_user = User::all();

        foreach ($all_user as $user){
            if (empty($remove_account_status))
            {
                break;
            }

            $table_user_id  = $user->tenant_info->id ?? '';

            if(!empty($table_user_id)){
                $tenant = Tenant::where('id', $table_user_id)->first();
                $payment_log = PaymentLogs::where('tenant_id', $tenant->id)->latest()->first();

                rsort($day_list);

                foreach ($day_list as $day){
                    $today = now()->startOfDay();
                    $expirationDate = \Carbon\Carbon::parse($tenant->expire_date)->addDays($remove_day)->subDays($day)->startOfDay();

                    if ($today->lte($expirationDate))
                    {
                        $daysLeft = $today->diffInDays($expirationDate);

                        if (in_array($daysLeft, $day_list))
                        {
                            $message['subject'] = __('Account will be deleted -' . get_static_option('site_title'));
                            $message['body'] = __('Your Account will be removed within ' . ($daysLeft) . ' days. Please subscribe to a plan before we remove your account ');
                            $message['body'].= '<br><br><a href="'.route('landlord.frontend.plan.order',optional($payment_log->package)->id).'">'.__('Go to plan page').'</a>';
                            Mail::to($payment_log->email)->send(new BasicMail( $message['body'],$message['subject']));
                            break;
                        }
                    }
                }
            }
        }

        return 0;
    }
}
