<?php

namespace Modules\SalesReport\Http\Controllers\Tenant;

use App\Helpers\FlashMsg;
use App\Http\Services\CustomPaginationService;
use App\Models\OrderProducts;
use App\Models\ProductOrder;
use Carbon\Carbon;
use Exception;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Routing\Controller;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;
use Modules\SalesReport\Http\Services\SalesReport;
use phpDocumentor\Reflection\Types\This;

class SalesReportController extends Controller
{
    public array $daysOfWeek = [
        "sunday" => "Sunday",
        "monday" => "Monday",
        "tuesday" => "Tuesday",
        "wednesday" => "Wednesday",
        "thursday" => "Thursday",
        "friday" => "Friday",
        "saturday" => "Saturday"
    ];

    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        $orders = ProductOrder::completed()->orderBy('id','desc')->get();

        $orders_today = ProductOrder::completed()->whereDate('updated_at', today())->orderBy('updated_at','asc')->get()
            ->groupBy(function ($query){
                // 'D = day name'
                // 'h = hour number'
                // 'A = AM/PM'
                return Carbon::parse($query->updated_at)->format('D h A');
            });

        $first_workday = get_static_option('first_workday') ?? 'sunday';
        $orders_weekly = $this->getWeeklyReport($first_workday);

        $orders_months = ProductOrder::completed()->orderBy('updated_at','asc')->get()
            ->groupBy(function ($query){
            // 'm' if month number is need, eg 05
            // 'M' if month name is needed, eg may
            return Carbon::parse($query->updated_at)->format('M Y');
        });

        $orders_years = ProductOrder::completed()->orderBy('id','desc')->get()
            ->groupBy(function ($query){
            // 'y' if year last two number is need, eg 23
            // 'Y' if full year number is needed, eg 2023
            return Carbon::parse($query->updated_at)->format('Y');
        });

        $reports = SalesReport::reports($orders);
        $total_report = [
            'total_sale' => $reports['total_sale'],
            'total_profit' => $reports['total_profit'],
            'total_revenue' => $reports['total_revenue'],
            'total_cost' => $reports['total_cost'],
            'products' => $reports['products']
        ];

        $today_report = $this->prepareDataForChart($orders_today);
        $weekly_report = $this->prepareDataForChart($orders_weekly);
        $monthly_report = $this->prepareDataForChart($orders_months);
        $yearly_report = $this->prepareDataForChart($orders_years);

        $display_item_count = request()->count ?? 10;
        $current_query = request()->all();
        $create_query = http_build_query($current_query);
        $route = 'tenant.admin';

        $products = $this->pagination_type($total_report['products'], $display_item_count, 'custom', route($route . ".sales.dashboard") . '?' . $create_query);
        return view('salesreport::tenant.admin.index', compact('total_report', 'today_report', 'weekly_report', 'monthly_report', 'yearly_report', 'products'));
    }

    private function prepareDataForChart($orders_months)
    {
        $data = SalesReport::reportByMonthsOrYears($orders_months);

        $categories = [];
        $salesData = [];
        $profitData = [];
        $revenueData = [];
        $costData = [];

        foreach ($data ?? [] as $month => $values) {
            $categories[] = $month;
            $salesData[] = $values['total_sale'];
            $profitData[] = $values['total_profit'];
            $revenueData[] = $values['total_revenue'];
            $costData[] = $values['total_cost'];
        }

        if (!empty($profitData) && !empty($revenueData) && !empty($costData))
        {
            $max_value = max(array_merge($profitData, $revenueData, $costData));
        } else {
            $max_value = 0;
        }

        return [
            'categories' => $categories,
            'salesData' => $salesData,
            'profitData' => $profitData,
            'revenueData' => $revenueData,
            'costData' => $costData,
            'max_value' => $max_value
        ];
    }

    public function paginate($items, $perPage = 50, $page = null, $options = [])
    {
        $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
        $items = $items instanceof Collection ? $items : Collection::make($items);
        return new LengthAwarePaginator($items->forPage($page, $perPage), $items->count(), $perPage, $page, $options);
    }

    public function pagination_type($all_products, $count, $type = "custom", $route=null){
        $display_item_count = $count ?? 10;
        $all_products = $this->paginate($all_products, $display_item_count);

        if(!empty($route)){
            $all_products->withPath($route);
        }


        if($type == "custom"){
            $current_items = (($all_products->currentPage() - 1) * $display_item_count);
            return [
                "items" => $all_products->items(),
                "current_page" => $all_products->currentPage(),
                "total_items" => $all_products->total(),
                "total_page" => $all_products->lastPage(),
                "next_page" => $all_products->nextPageUrl(),
                "previous_page" => $all_products->previousPageUrl(),
                "last_page" => $all_products->lastPage(),
                "per_page" => $all_products->perPage(),
                "path" => $all_products->path(),
                "current_list" => $all_products->count(),
                "from" => $all_products->count() ? $current_items + 1 : 0,
                "to" => $current_items + $all_products->count(),
                "on_first_page" => $all_products->onFirstPage(),
                "hasMorePages" => $all_products->hasMorePages(),
                "links" => $all_products->getUrlRange(0,$all_products->lastPage())
            ];
        }else{
            return $all_products;
        }
    }

    public function getWeeklyReport($dayOfWeek)
    {
        $dayOfWeek = ucfirst(strtolower($dayOfWeek));

        // Get the current date and time
        $now = Carbon::now();

        // Find the last occurrence of the day
        $startDate = $now->copy();
        while($startDate->format('l') !== $dayOfWeek) {
            $startDate->subDay();
        }

        // Set the end date as 7 days from the start date
        $endDate = $startDate->copy()->addDays(7);

        return ProductOrder::whereBetween('updated_at', [$startDate->startOfDay(), $endDate->endOfDay()])
            ->orderBy('updated_at', 'asc')->get()
            ->groupBy(function ($query){
                // 'D' if day name if needed, eg Sun
                return Carbon::parse($query->updated_at)->format('D');
            });
    }

    public function weekly_report()
    {

    }

    public function dynamic_report()
    {
        $current_url = \request()->url();
        $url_array = explode('/', $current_url);

        $orders = ProductOrder::completed();

        $era = last($url_array);
        if ($era == 'weekly')
        {
            $workDay = get_static_option('first_workday') ?? 'sunday';
            $workDay = $this->getCarbonDays($workDay);

            $weekStartDate = Carbon::now()->startOfWeek($workDay);
            $weekEndDate = Carbon::now()->endOfWeek();

            $orders->whereBetween('updated_at', [$weekStartDate, $weekEndDate]);
        }
        elseif ($era == 'monthly')
        {
            $orders->whereMonth('updated_at', now()->month);
        }
        elseif ($era == 'yearly')
        {
            $orders->whereYear('updated_at', now()->year);
        } else {
            return to_route('tenant.admin.sales.dashboard');
        }

        $orders = $orders->orderBy('updated_at','desc')->get();
        $page_title = $era ?? 'all';

        $reports = SalesReport::reports($orders);
        $total_report = [
            'total_sale' => $reports['total_sale'],
            'total_profit' => $reports['total_profit'],
            'total_revenue' => $reports['total_revenue'],
            'total_cost' => $reports['total_cost'],
            'products' => $reports['products']
        ];

        $display_item_count = request()->count ?? 20;
        $current_query = request()->all();
        $create_query = http_build_query($current_query);
        $route = 'tenant.admin';

        $products = $this->pagination_type($total_report['products'], $display_item_count, 'custom', route($route . ".sales.report.".$page_title) . '?' . $create_query);

        return view('salesreport::tenant.admin.monthly_report', compact('total_report','products', 'page_title'));
    }

    private function getCarbonDays($day_name)
    {
        switch (strtoupper($day_name)) {
            case 'SUNDAY':
                return Carbon::SUNDAY;
            case 'MONDAY':
                return Carbon::MONDAY;
            case 'TUESDAY':
                return Carbon::TUESDAY;
            case 'WEDNESDAY':
                return Carbon::WEDNESDAY;
            case 'THURSDAY':
                return Carbon::THURSDAY;
            case 'FRIDAY':
                return Carbon::FRIDAY;
            case 'SATURDAY':
                return Carbon::SATURDAY;
            default:
                throw new Exception('Invalid day name');
        }
    }

    public function settings()
    {
        return view('salesreport::tenant.admin.settings', ['daysOfWeek' => $this->daysOfWeek]);
    }

    public function settings_update(Request $request)
    {
        $request->validate([
            'first_workday' => 'required|min:6|max:9'
        ]);
        abort_if(!array_key_exists($request->first_workday, $this->daysOfWeek), 404);

        update_static_option('first_workday', trim($request->first_workday));

        return back()->with(FlashMsg::settings_update(__('First Day of The Week is Updated')));
    }
}
