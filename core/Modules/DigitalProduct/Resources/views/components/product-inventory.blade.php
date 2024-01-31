@php
    if(!isset($inventory)){
        $inventory = null;
    }

    if(!isset($uom)){
        $uom = null;
    }
@endphp

<h4 class="dashboard-common-title-two"> {{ __("Product Inventory") }} </h4>

<div class="dashboard-input mt-4">
    <label class="dashboard-label color-light mb-2"> {{ __("SKU") }} </label>
    <input type="text" class="form--control radius-10" name="sku" value="{{ $inventory?->sku }}">
    <p>{{ __("Custom Unique Code for this product.") }}</p>
</div>
