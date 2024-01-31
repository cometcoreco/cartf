@php
    if(!isset($product)){
        $product = null;
    }
@endphp

<div class="general-info-wrapper px-3">
    <h4 class="dashboard-common-title-two">{{ __("Product Settings") }}</h4>
    <div class="general-info-form mt-0 mt-lg-4">
        <div class="form-group">
            <label for="min_purchase">{{ __("Minimum quantity of Purchase") }}</label>
            <input id="min_purchase" name="min_purchase" class="form--control" value="{{ $product?->min_purchase }}" placeholder="{{ __("Minimum quantity of purchase") }}">
        </div>

        <div class="form-group">
            <label for="max_purchase">{{ __("Maximum quantity of Purchase") }}</label>
            <input id="max_purchase" name="max_purchase" class="form--control" value="{{ $product?->max_purchase }}" placeholder="{{ __("Maximum quantity of Purchase") }}">
        </div>
    </div>
</div>
