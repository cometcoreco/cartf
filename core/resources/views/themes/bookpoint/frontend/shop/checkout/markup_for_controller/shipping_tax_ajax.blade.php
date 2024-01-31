@php
    // physical product prices along with tax
    $physical_items = Cart::content('default')->where('options.type', \App\Enums\ProductTypeEnum::PHYSICAL);
    $carts = $physical_items;
    $enableTaxAmount = !\Modules\TaxModule\Services\CalculateTaxServices::isPriceEnteredWithTax();
    $shippingTaxClass = \Modules\TaxModule\Entities\TaxClassOption::where("class_id", get_static_option("shipping_tax_class"))->sum("rate");
    $tax = Modules\TaxModule\Services\CalculateTaxBasedOnCustomerAddress::init();
    $uniqueProductIds = $carts->pluck("id")->unique()->toArray();

    $country_id = $country ?? 0;
    $state_id = $state ?? 0;
    $city_id = $city ?? 0;


    if(empty($uniqueProductIds))
    {
        $taxProducts = collect([]);
    }
    else
    {
        if(\Modules\TaxModule\Services\CalculateTaxBasedOnCustomerAddress::is_eligible()){
            $taxProducts = $tax
                ->productIds($uniqueProductIds)
                ->customerAddress($country_id, $state_id, $city_id)
                ->generate();
        }
        else
        {
            $taxProducts = collect([]);
        }
    }
@endphp

@php
    $v_tax_total = 0;
    $subtotal = 0;
@endphp
@foreach($carts ?? [] as $data)
    @php
        $default_shipping_cost = null;
        $taxAmount = $taxProducts->where("id" , $data->id)->first();

        if(!empty($taxAmount)){
            $taxAmount->tax_options_sum_rate = $taxAmount->tax_options_sum_rate ?? 0;
            $price = calculatePrice($data->price, $taxAmount);
            $regular_price = calculatePrice($data->options->regular_price, $data->options);
            $v_tax_total += calculatePrice($data->price, $taxAmount, "percentage") * $data->qty;
        }else{
            $price = calculatePrice($data->price, $data->options);
            $regular_price = calculatePrice($data->options->regular_price, $data->options);
        }

        $subtotal += $price * $data->qty;
        $total = $subtotal + $v_tax_total;
    @endphp
@endforeach

@if(count($physical_items) > 0)
    @if(count($shipping_methods) > 0)
        <ul class="coupon-contents-details-list coupon-border">
            <h6>{{__('Shipping')}}</h6>
            @foreach($shipping_methods ?? [] as $key => $method)
                <li class="coupon-contents-details-list-item" data-country="{{$country}}" data-state="{{$state}}">
                <span class="coupon-radio-item">
                    <input type="radio" id="shipping-option-{{$method['id']}}" value="{{$method['id']}}" name="shipping_method">
                    <label for="shipping-option-{{$method['id']}}">
                        {{$method['name']}}
                    </label>
                </span>
                    <span>{{amount_with_currency_symbol($method['options']['cost'])}}</span>
                </li>
            @endforeach
        </ul>
    @elseif(count($default_shipping) > 0)
        <ul class="coupon-contents-details-list coupon-border">
            <h6>{{__('Shipping')}}</h6>
            @foreach($default_shipping ?? [] as $key => $method)
                <li class="coupon-contents-details-list-item" data-country="{{$country}}" data-state="{{$state}}">
                <span class="coupon-radio-item">
                    <input type="radio" id="shipping-option-{{$method['id']}}" value="{{$method['id']}}"
                           name="shipping_method">
                    <label for="shipping-option-{{$method['id']}}">
                        {{$method['name']}}
                    </label>
                </span>
                    <span>{{amount_with_currency_symbol($method['options']['cost'])}}</span>
                </li>
            @endforeach
        </ul>
    @endif
@endif

@if(count($physical_items) > 0)
    @php
        $addressObj = new stdClass();
        $addressObj->country_id = $country;
        $addressObj->state_id = $state;
        $location_tax_data = get_product_shipping_tax_data($addressObj);
        $tax_ = calculatePercentageAmount($total, $location_tax_data['product_tax'] ?? 0) ?? 0;
    @endphp

    <ul class="coupon-contents-details-list coupon-border">
        @if(get_static_option('tax_system') == 'advance_tax_system')
            @if($enableTaxAmount)
                <li class="coupon-contents-details-list-item"><span> {{__('Tax (Incl)')}} </span>
                    <span> {{amount_with_currency_symbol($v_tax_total ?? 0)}} </span>
                </li>
            @else
                <li class="coupon-contents-details-list-item"><span> {{__('Tax (Incl)')}} </span>
                    <span> {{ get_static_option("display_price_in_the_shop") == 'including' ? __("Inclusive Tax") : "" }} </span>
                </li>
            @endif
        @else
            <li class="coupon-contents-details-list-item"><span> {{__('Tax (Incl)')}} </span>
                <span> {{amount_with_currency_symbol($tax_)}} </span>
            </li>
        @endif

        <li class="coupon-contents-details-list-item coupon-price"><span> {{__('Coupon Discount (-)')}} </span>
            <span>
            @php
                if (isset($coupon)) {
                    if ($coupon['discount_type'] == 'amount') {
                        $discount = site_currency_symbol().$coupon['discount'];
                    } else {
                        $discount = $coupon['discount'].'%';
                    }
                }
            @endphp

                {{isset($coupon) ? $discount : amount_with_currency_symbol(0.00)}}
        </span>
        </li>
        <li class="coupon-contents-details-list-item price-shipping">
            <span> {{__('Shipping Cost (+)')}} </span>
            <span> -- </span>
        </li>
    </ul>
@endif
<ul class="coupon-contents-details-list coupon-border">
    @php
        $subtotal = null;
        $itemsTotal = null;
        $v_tax_total = 0;
        $total = 0;
    @endphp
    @foreach($carts ?? [] as $data)
        @php
            $default_shipping_cost = null;
            $taxAmount = $taxProducts->where("id" , $data->id)->first();

            if(!empty($taxAmount)){
                $taxAmount->tax_options_sum_rate = $taxAmount->tax_options_sum_rate ?? 0;
                $price = calculatePrice($data->price, $taxAmount);
                $regular_price = calculatePrice($data->options->regular_price, $data->options);
                $v_tax_total += calculatePrice($data->price, $taxAmount, "percentage") * $data->qty;
            }else{
                $price = calculatePrice($data->price, $data->options);
                $regular_price = calculatePrice($data->options->regular_price, $data->options);
            }

            $subtotal += $price * $data->qty;
            $total = $subtotal + $v_tax_total;
        @endphp
    @endforeach

    @php
//        // physical product prices along with tax
//        $physical_items = Cart::content('default')->where('options.type', \App\Enums\ProductTypeEnum::PHYSICAL);
//        $subtotal = 0.0;
//        foreach ($physical_items ?? [] as $item)
//        {
//            $subtotal += $item->price * $item->qty;
//        }
//
//        $taxed_price = ($subtotal * $product_tax) / 100;
//        $total = $subtotal + $taxed_price;

        // digital product prices
        $digital_items = Cart::content('default')->where('options.type', \App\Enums\ProductTypeEnum::DIGITAL);
        $subtotal = 0.0;
        foreach ($digital_items ?? [] as $item)
        {
            $digital_product = \Modules\DigitalProduct\Entities\DigitalProduct::find($item->id);
            $taxed_price = 0.0;
            if (!is_null($digital_product->tax))
            {
                $tax = $digital_product?->getTax?->tax_percentage;
                $taxed_price = ($item->price * $tax) / 100;
            }
            $subtotal += $item->price + $taxed_price;
        }

        $total += $subtotal;
    @endphp

    @if(get_static_option('tax_system') == 'advance_tax_system')
        <li class="coupon-contents-details-list-item price-total" data-total="{{$total}}">
            <h6 class="coupon-title"> {{__('Total Amount')}} </h6> <span
                class="coupon-price fw-500 color-heading"> {{amount_with_currency_symbol($total)}} </span>
        </li>
    @else
        <li class="coupon-contents-details-list-item price-total" data-total="{{$total+$tax_}}">
            <h6 class="coupon-title"> {{__('Total Amount')}} </h6> <span
                class="coupon-price fw-500 color-heading"> {{amount_with_currency_symbol($total+$tax_)}} </span>
        </li>
    @endif

</ul>
