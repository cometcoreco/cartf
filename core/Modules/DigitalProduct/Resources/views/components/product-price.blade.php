<?php
    if (!isset($product)) {
        $product = null;
    }

    if (!isset($taxes)) {
        $taxes = [];
    }
?>

<div class="general-info-wrapper px-3">
    <h4 class="dashboard-common-title-two"> {{__('Price Manage')}} </h4>
    <div class="general-info-form mt-0 mt-lg-4">
        <div class="dashboard-input mt-4">
            <label class="dashboard-label color-light mb-2"> {{ __("Accessibility") }} </label>
            <select name="accessibility" id="accessibility" class="form-control">
                <option value="paid" {{$product?->accessibility == 'paid' ? 'selected' : ''}}>{{__('Paid')}}</option>
{{--                <option value="free" {{$product?->accessibility == 'free' ? 'selected' : ''}}>{{__('Free')}}</option>--}}
            </select>
        </div>

        <div id="tax-price-info" style="{{$product?->accessibility == 'free' ? 'display:none' : ''}}">
            <div class="dashboard-input mt-4">
                <label class="dashboard-label color-light mb-2"> {{ __("Tax") }} </label>
                <select name="tax" id="tax" class="form-control">
                    <option value="">{{__('No tax applicable')}}</option>
                    @foreach($taxes as $tax)
                        <option value="{{$tax->id}}" {{$product?->tax == $tax->id ? 'selected' : ''}}>{{$tax->name}}</option>
                    @endforeach
                </select>
            </div>

            <div class="dashboard-input mt-4">
                <label class="dashboard-label color-light mb-2"> {{ __("Regular Price") }} </label>
                <input type="text" class="form--control radius-10" value="{{ $product?->regular_price }}" name="price"
                       placeholder="{{ __("Enter Regular Price...") }}">
                <small class="text-warning">{{ __("This price will display like this") }}
                    <del>( {{ site_currency_symbol() }}10)</del> {{', '.__('If you add sale price too')}}</small>
            </div>

            <div class="dashboard-input mt-4">
                <label class="dashboard-label color-light mb-2"> {{ __("Sale Price") }} </label>
                <input type="text" class="form--control radius-10" value="{{ $product?->sale_price }}" name="sale_price"
                       placeholder="{{ __("Enter Sale Price...") }}">
                <small>{{ __("This will be your product selling price") }}</small>
            </div>

            <div class="dashboard-input mt-4">
                <label class="dashboard-label color-light mb-2"> {{ __("Free Date") }} <sup
                        class="text-primary">{{__('(Optional)')}}</sup> </label>
                <input type="date" class="form--control radius-10 flatpickr" id="free_date"
                       value="{{ $product?->free_date ?? "" }}" name="free_date"
                       placeholder="{{ __("Select free date...") }}">
                <small>{{__('This product will be free until this selected date is over')}}</small>
            </div>

            <div class="dashboard-input mt-4">
                <label class="dashboard-label color-light mb-2"> {{ __("Promotional Date") }} <sup
                        class="text-primary">{{__('(Optional)')}}</sup> </label>
                <input type="date" class="form--control radius-10 flatpickr" id="promotional_date"
                       value="{{ $product?->promotional_date ?? "" }}" name="promotional_date"
                       placeholder="{{ __("Select promotional date...") }}">
                <small>{{__('Promotional discounted price will be applied on this product until this selected date is over')}}</small>
            </div>

            <div class="dashboard-input mt-4">
                <label class="dashboard-label color-light mb-2"> {{ __("Promotional Price") }} </label>
                <input type="text" class="form--control radius-10" value="{{ $product?->promotional_price }}"
                       name="promotional_price" placeholder="{{ __("Enter promotional price...") }}">
                <small>{{ __("This price will be applied on this product during the promotional time period") }}</small>
            </div>
        </div>
    </div>
</div>
