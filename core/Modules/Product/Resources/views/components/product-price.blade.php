<?php
    if(!isset($product)){
        $product = null;
    }
?>

<div class="general-info-wrapper px-3">
    <h4 class="dashboard-common-title-two"> {{__('Price Manage')}} </h4>
    <div class="general-info-form mt-0 mt-lg-4">
        <div class="dashboard-input mt-4">
            <label class="dashboard-label color-light mb-2"> {{ __("Base Cost") }} <x-fields.mandatory-indicator/></label>
            <input type="text" class="form--control radius-10" value="{{ $product?->cost }}" name="cost" placeholder="{{ __("Base Cost...") }}">
            <p>{{ __("Purchase price of this product.") }}</p>
        </div>

        <div class="dashboard-input mt-4">
            <label class="dashboard-label color-light mb-2"> {{ __("Regular Price") }} </label>
            <input type="text" class="form--control radius-10" value="{{ $product?->price }}" name="price" placeholder="{{ __("Enter Regular Price...") }}">
            <small>{{ __("This price will display like this") }} <del>( {{ site_currency_symbol() }} 10)</del></small>
        </div>

        <div class="dashboard-input mt-4">
            <label class="dashboard-label color-light mb-2"> {{ __("Sale Price") }} <x-fields.mandatory-indicator/></label>
            <input type="text" class="form--control radius-10" value="{{ $product?->sale_price }}" name="sale_price" placeholder="{{ __("Enter Sale Price...") }}">
            <small>{{ __("This will be your product selling price") }}</small>
        </div>

        @if($product)
            <div class="dashboard-input mt-4">
                <div class="row">
                    <div class="col-6 is_taxable_wrapper">
                        <label class="dashboard-label color-light mb-2"> {{ __("Is Taxable?") }}</label>
                        <select name="is_taxable" class="form--control radius-10">
                            <option @selected($product->is_taxable == 0) value="no">{{__('No')}}</option>
                            <option @selected($product->is_taxable == 1) value="yes">{{__('Yes')}}</option>
                        </select>
                    </div>

                    <div class="col-6 tax_classes_wrapper" @style(['display: none' => !$product->is_taxable])>
                        <label class="dashboard-label color-light mb-2"> {{ __("Tax classes") }}</label>
                        <select name="tax_class" class="form--control radius-10">
                            <option value="">{{__('Select an option')}}</option>
                            @foreach($taxClasses ?? [] as $class)
                                <option @selected($product->tax_class_id == $class->id) value="{{$class->id}}">{{ $class->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        @else
            <div class="dashboard-input mt-4">
                <div class="row">
                    <div class="col-6 is_taxable_wrapper">
                        <label class="dashboard-label color-light mb-2"> {{ __("Is Taxable?") }}</label>
                        <select name="is_taxable" class="form--control radius-10">
                            <option value="no">{{__('No')}}</option>
                            <option value="yes">{{__('Yes')}}</option>
                        </select>
                    </div>

                    <div class="col-6 tax_classes_wrapper" style="display:none">
                        <label class="dashboard-label color-light mb-2"> {{ __("Tax classes") }}</label>
                        <select name="tax_class" class="form--control radius-10">
                            <option value="">{{__('Select an option')}}</option>
                            @foreach($taxClasses ?? [] as $class)
                                <option value="{{$class->id}}">{{ $class->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
