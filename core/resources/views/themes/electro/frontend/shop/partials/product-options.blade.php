<div class="shopping-icon bg-color-four justify-content-between">
    @if($product->inventory_detail_count < 1)
            <a href="javascript:void(0)" class="cart-loading add-to-cart-btn"
               data-product_id="{{ $product->id }}"> {{__('Add to Cart')}} </a>

            <div class="icon-list">
                <a href="javascript:void(0)" class="icons cart-loading add-to-wishlist-btn"
                   data-product_id="{{ $product->id }}">
                    <i class="lar la-heart"></i>
                </a>

                <a href="javascript:void(0)" class="icons cart-loading compare-btn"
                   data-product_id="{{ $product->id }}">
                    <i class="las la-sync"></i>
                </a>

                <a href="javascript:void(0)" class="icons popup-modal cart-loading product-quick-view-ajax"
                   data-action-route="{{ route('tenant.products.single-quick-view', $product->slug) }}">
                    <i class="lar la-eye"></i>
                </a>
            </div>
    @else
        <a href="javascript:void(0)" class="cart-loading product-quick-view-ajax"
           data-action-route="{{ route('tenant.products.single-quick-view', $product->slug) }}"> {{__('Add to Cart')}} </a>

        <div class="icon-list">
            <a href="javascript:void(0)" class="icons cart-loading product-quick-view-ajax"
               data-action-route="{{ route('tenant.products.single-quick-view', $product->slug) }}">
                <i class="lar la-heart"></i>
            </a>

            <a href="javascript:void(0)" class="icons cart-loading product-quick-view-ajax"
               data-action-route="{{ route('tenant.products.single-quick-view', $product->slug) }}">
                <i class="las la-sync"></i>
            </a>

            <a href="javascript:void(0)" class="icons cart-loading product-quick-view-ajax"
               data-action-route="{{ route('tenant.products.single-quick-view', $product->slug) }}">
                <i class="lar la-eye"></i>
            </a>
        </div>
    @endif
</div>
