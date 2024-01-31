<section class="sale-area" data-padding-top="{{$data['padding_top']}}"
         data-padding-bottom="{{$data['padding_bottom']}}">
    <div class="container-three">
        <div class="row">
            <div class="col-lg-12">
                <div class="section-title justify-content-center">
                    <h2 class="title"> {{$data['title'] ?? ''}} </h2>
                </div>
            </div>
        </div>
        <div class="row margin-top-10 padding-top-10">
            @foreach($data['products'] ?? [] as $product)
                @php
                    $class = $loop->odd ? 'fadeInUp' : 'fadeInDown';
                    $delay = $loop->odd ? '.1s' : '.2s';

                    $image_markup = \App\Facades\ImageRenderFacade::getParent($product->image_id)
                            ->getChild(to_product_details($product->slug))
                            ->getGrandchild()
                            ->renderAll();
                @endphp

                <div class="col-xl-3 col-lg-4 col-md-4 col-sm-6 col-{{productCards()}} margin-top-30 wow {{$class}}" data-wow-delay="{{$delay}}">
                    <div class="signle-collection style-02 text-center">
                        <div class="collction-thumb">
                            {!! $image_markup !!}

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
        </div>
    </div>
</section>
