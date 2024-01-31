<?php

namespace Modules\TaxModule\Services;

use Modules\TaxModule\Traits\TaxCalculatorTrait;

class CalculateTaxServices
{
    use TaxCalculatorTrait;

    private ?CalculateTaxServices $instance = null;

    public static function init(): ?CalculateTaxServices
    {
        $self = new self();
        if(!is_null($self->instance)){
            return $self->instance;
        }

        return $self;
    }

    // todo:: first method will check product price if admin enable advance tax module with prices entered with tax then this method will returned product price with tax
    public static function productPrice($price, $product, $for = "product")
    {
        // todo:: create a instace of this class first
        $init = self::init();
        // todo:: first need to get all information related to static options
        // todo:: check is prices entered with tax is enable or not
        if($init->taxSystem() == 'advance_tax_system' && $init->priceIncludeTax() == 'yes' && $for == 'product'){
            $price = $price + calculatePercentageAmount($price, $product->tax_options_sum_rate);
        }elseif($init->taxSystem() == 'advance_tax_system' && $for == 'shipping'){
            $price = $price + calculatePercentageAmount($price, $product);
        }elseif(
            $init->taxSystem() == 'advance_tax_system'
            && (get_static_option("calculate_tax_based_on") == 'customer_billing_address' || get_static_option("calculate_tax_based_on") == 'customer_account_address')
            && $init->priceIncludeTax() == 'no' && $for == 'percentage'
        ){
            $price = calculatePercentageAmount($price, $product->tax_options_sum_rate);
        }

        if(get_static_option("tax_round_at_subtotal") == 1){
            return round($price, 2);
        }

        return $price;
    }

    public static function pricesEnteredWithTax($for = "product"): bool
    {
        // todo:: create a instace of this class first
        $init = self::init();
        // todo:: first need to get all information related to static options
        // todo:: check is prices entered with tax is enable or not
        if($init->taxSystem() == 'advance_tax_system' && $init->priceIncludeTax() == 'yes' && $for == 'product'){
            return true;
        }elseif($init->taxSystem() == 'advance_tax_system' && $for == 'shipping'){
            return true;
        }

        return false;
    }


    public static function isPriceEnteredWithTax(): bool
    {
        // todo:: create a instace of this class first
        $init = self::init();
        return $init->taxSystem() == 'advance_tax_system' && $init->priceIncludeTax() == 'yes';
    }
}
