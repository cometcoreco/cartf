@auth('web')
    @php
        $user = auth('web')->user();
        $downloaded = \Modules\DigitalProduct\Entities\DigitalProductDownload::where(['user_id' => $user->id, 'product_id' => $product->id])->exists();
    @endphp
@endauth

@if(isset($downloaded) && $downloaded)
    <a href="{{route('tenant.user.dashboard.download.file', $product->slug)}}" class="product-cart-btn cart-btn-absolute radius-5"> {{__('Download')}} </a>
@else
    <a href="javascript:void(0)" class="product-cart-btn cart-btn-absolute radius-5 digital-add-to-cart-btn" data-product_id="{{ $product->id }}"> {{__('Add to Cart')}} </a>
@endif

<a href="{{route('tenant.digital.shop.product.details', $product->slug)}}" class="cart-details-btn cart-details-absolute radius-5"> {{__('View Details')}} </a>
