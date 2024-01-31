<?php

namespace App\Http\Services;

use Carbon\Carbon;

class TaxRenderService
{
    public array $prices;
    public function __construct(public object $product_object){}

    public function getProductPrice()
    {
        $this->prices['regular_price'] = $this->product_object->price ? (double) $this->product_object->price : null;
        $this->prices['sale_price'] = (double) $this->product_object->sale_price;

        $this->getCampaignPrice();
        $this->getTaxedPrice();

        return $this->prices;
    }

    private function getCampaignPrice(): void
    {
        if ($this->hasCampaign()) {
            if ($this->campaignStatus()) {
                $start_date = Carbon::parse($this->product_object?->campaign_product?->start_date);
                $end_date = Carbon::parse($this->product_object?->campaign_product?->end_date);

                if ($start_date->lessThanOrEqualTo(now()) && $end_date->greaterThanOrEqualTo(now())) {
                    $this->prices['campaign_name'] = (string) $this->product_object?->campaign_product?->campaign?->title;
                    $this->prices['sale_price'] = (double) $this->product_object?->campaign_product?->campaign_price;
                    $this->prices['regular_price'] = (double) $this->product_object->sale_price;

                    $this->prices['discount'] = 100 - round(($this->prices['sale_price'] / ($this->prices['regular_price'] ?? 1)) * 100);
                }
            }
        }
    }

    public function hasCampaign(): bool
    {
        return !empty($this->product_object?->campaign_product);
    }

    public function campaignStatus(): bool
    {
        return $this->product_object?->campaign_product?->campaign?->status == 'publish';
    }

    public function getTaxedPrice()
    {
        if ($this->isAdvancedTaxSystem())
        {
            if ($this->isPriceIncludingTax())
            {
                $product = $this->product_object;
                if($product->is_taxable)
                {
                    $tax_options = $this->getTaxClassOptions($product);
                    $tax_rate = 0;
                    foreach ($tax_options ?? [] as $option)
                    {
                        $tax_rate += $option['option']->rate;
                    }

                    $sale_price = $this->prices['sale_price'];
                    $this->prices['sale_price'] = $sale_price + ($sale_price * ($tax_rate / 100));
                }
            }
        }
    }

    public function getTaxClassOptions($product)
    {
        $auth_address = $this->getUserAddress();
        $tax_class = $this->getTaxClass($product);
        if ($tax_class)
        {
            $tax_class_options = $tax_class->classOption;

            foreach ($tax_class_options ?? [] as $option)
            {
                $priority = 0;
                if ($option->country_id == $auth_address['country_id'])
                {
                    $priority++;
                }

                if ($option->state_id == $auth_address['state_id'])
                {
                    $priority++;
                }

                if ($option->city_id == $auth_address['city_id'])
                {
                    $priority++;
                }

                if ($option->postal_code == $auth_address['postal_code'])
                {
                    $priority++;
                }

                //TODO:: Fix it
                $prioritize_option[] = [
                    "priority" => $priority,
                    "option" => $option
                ];
            }

            $max_priority = max(array_column($prioritize_option, 'priority'));

            return array_filter($prioritize_option, function($item) use ($max_priority) {
                return $item['priority'] == $max_priority;
            });
        }
    }

    public function getTaxClass($product)
    {
        return $product->product_tax_class;
    }

    const ADVANCE_TAX_SYSTEM = 'advance_tax_system';
    const ZONE_WISE_TAX_SYSTEM = 'zone_wise_tax_system';
    const CUSTOMER_ACCOUNT_ADDRESS = 'customer_account_address';
    const CUSTOMER_BILLING_ADDRESS = 'customer_billing_address';
    public function getTaxInfo(): array
    {
        $prices_include_tax = get_static_option("prices_include_tax", "no");
        $calculate_tax_based_on = (get_static_option('calculate_tax_based_on') ?? "customer_account_address");
//        update_static_option("shipping_tax_class", $request->shipping_tax_class ?? "");
//        update_static_option("tax_round_at_subtotal", $request->tax_round_at_subtotal ?? "");
//        update_static_option("display_tax_total", $request->display_tax_total ?? "");
        $display_price_in_the_shop = get_static_option("display_price_in_the_shop", "exclusive");
        $tax_system = get_static_option("tax_system", self::ZONE_WISE_TAX_SYSTEM);

        return [
            "prices_include_tax" => $prices_include_tax,
            "tax_system" => $tax_system,
            "display_price_in_the_shop" => $display_price_in_the_shop,
            "calculate_tax_based_on" => $calculate_tax_based_on
        ];
    }

    private function isAdvancedTaxSystem(): bool
    {
        $tax_info = $this->getTaxInfo();
        return $tax_info['tax_system'] == self::ADVANCE_TAX_SYSTEM;
    }

    private function isPriceIncludingTax(): bool
    {
        $tax_info = $this->getTaxInfo();
        return $tax_info['prices_include_tax'] == 'yes';
    }

    private function includeTaxPriceInShop(): bool
    {
        $tax_info = $this->getTaxInfo();
        return $tax_info['display_price_in_the_shop'] == 'including';
    }

    private function calculateTaxBasedOn()
    {
        $tax_info = $this->getTaxInfo();
        return $tax_info['calculate_tax_based_on'];
    }

    public function user()
    {
        return auth('web')->user();
    }

    public function getUserAddress()
    {
        $user = $this->user();
        if (!empty($user))
        {
            if ($this->calculateTaxBasedOn() == self::CUSTOMER_ACCOUNT_ADDRESS)
            {
                return [
                    'country_id' => $user->country,
                    'state_id' => $user->state,
                    'city_id' => $user->city,
                    'postal_code' => $user->postal_code
                ];
            } else {
                return [
                    'country_id' => $user->delivery_address->country_id,
                    'state_id' => $user->delivery_address->state_id,
                    'city_id' => $user->delivery_address->city,
                    'postal_code' => $user->delivery_address->postal_code
                ];
            }
        }
    }
}
