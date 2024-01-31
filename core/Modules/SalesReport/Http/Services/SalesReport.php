<?php

namespace Modules\SalesReport\Http\Services;

use App\Enums\ProductTypeEnum;

class SalesReport
{
    public static function reports($orders)
    {
        $total_sale = 0;
        $total_revenue = 0;
        $total_profit = 0;
        $total_cost = 0;

        $products = [];

        foreach ($orders ?? [] as $key => $order)
        {
            $order_details = json_decode($order->order_details);

            $index = 0;
            foreach ($order_details ?? [] as $item)
            {
                $product_cost = ($item->options->base_cost ?? 0) * $item->qty;
                $product_price = $item->price * $item->qty;

                $total_sale += $item->qty;
                $total_cost += $product_cost;
                $total_profit += ($product_price - $product_cost);

                $products[$key][$index++] = [
                    'product_id' => $item->id,
                    'product_type' => $item->options->type ?? ProductTypeEnum::PHYSICAL,
                    'name' => $item->name,
                    'qty' => $item->qty,
                    'cost' => $product_cost,
                    'price' => $product_price,
                    'profit' => ($product_price - $product_cost),
                    'sale_date' => $order->updated_at,
                    'variant' => [
                        'color' => $item->options->color_name ?? '',
                        'size' => $item->options->size_name ?? '',
                        'attributes' => $item->options->attributes ?? [],
                    ]
                ];
            }


            $total_revenue += json_decode($order->payment_meta)->subtotal;
        }

        return [
            'total_sale' => $total_sale,
            'total_revenue' => $total_revenue,
            'total_cost' => $total_cost,
            'total_profit' => $total_profit,
            'products' => $products
        ];
    }

    /**
     * @method reportByMonthsOrYears
     * @param $orders_months
     * @return array
     * This method is responsible for both month and year
     * */
    public static function reportByMonthsOrYears($orders_months): array
    {
        $monthly_reports = [];

        foreach($orders_months ?? [] as $month => $orders){
            $reports = self::reports($orders);
            $monthly_reports[$month] = [
                'total_sale' => $reports['total_sale'],
                'total_profit' => $reports['total_profit'],
                'total_revenue' => $reports['total_revenue'],
                'total_cost' => $reports['total_cost'],
                'products' => $reports['products'],
            ];
        }

        return $monthly_reports;
    }
}
