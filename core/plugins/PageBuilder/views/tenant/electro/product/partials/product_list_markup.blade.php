@foreach($products ?? [] as $product)
    @php
        $delay = '.1s';
        $class = 'fadeInUp';
        if ($loop->even)
        {
            $delay = '.2s';
            $class = 'fadeInDown';
        }
    @endphp

    <div class="col-xl-3 col-lg-3 col-md-4 col-sm-6 col-{{productCards()}} margin-top-30 grid-item wow {{$class}}" data-wow-delay="{{$delay}}">
        <div class="signle-collection style-02 text-center">
            <div class="collction-thumb">
                <a href="{{to_product_details($product->slug)}}">
                    {!! render_image_markup_by_attachment_id($product->image_id) !!}
                </a>

                @include('themes.electro.frontend.shop.partials.product-options')
            </div>
            <div class="collection-contents">
                <h2 class="collection-title color-four fs-26">
                    <a href="{{to_product_details($product->slug)}}"> {!! product_limited_text($product->name) !!} </a>
                </h2>

                <div class="d-flex align-items-center justify-content-center gap-2 mt-3">
                    @php
                        $price_class = 'fs-22 fw-500 flash-prices color-four';
                    @endphp

                    {!! render_product_dynamic_price_markup($product, sale_price_class: $price_class, regular_price_markup_tag: 's') !!}
                </div>
            </div>
        </div>
    </div>
@endforeach
