<section class="collection-area" data-padding-top="{{$data['padding_top']}}" data-padding-bottom="{{$data['padding_bottom']}}">
    <div class="container-three">
        <div class="row">
            <div class="col-lg-12">
                <div class="section-title justify-content-center">
                    <h2 class="title"> {{$data['title']}} </h2>
                </div>
            </div>
        </div>
        <div class="row margin-top-10">
            <div class="col-lg-12 margin-top-40">
                <div class="global-slick-init collection-slider slider-inner-margin dot-style-one dot-color-four dot-03 slider-inner-margin" data-infinite="true" data-slidesToShow="4" data-dots="true" data-swipeToSlide="true" data-autoplay="false" data-autoplaySpeed="2500"
                     data-responsive='[{"breakpoint": 1400,"settings": {"slidesToShow": 3}},{"breakpoint": 1200,"settings": {"slidesToShow": 3}},{"breakpoint": 992,"settings": {"slidesToShow": 3}},{"breakpoint": 768, "settings": {"slidesToShow": 3}},{"breakpoint": 576, "settings": {"slidesToShow": {{phoneScreenProducts()}} }}]'
                     data-rtl="{{get_user_lang_direction() == 1 ? 'true' : 'false'}}">
                    @foreach($data['products'] ?? [] as $product)
                        @php
                            $class = $loop->odd ? 'fadeInUp' : 'fadeInDown';
                            $delay = $loop->odd ? '.1s' : '.2s';

                            $image_markup = \App\Facades\ImageRenderFacade::getParent($product->image_id)
                            ->getChild(to_product_details($product->slug))
                            ->getGrandchild()
                            ->renderAll();
                        @endphp

                        <div class="signle-collection style-02 text-center wow {{$class}}" data-wow-delay="{{$delay}}">
                            <div class="collction-thumb">
                                {!! $image_markup !!}

                                @include('themes.electro.frontend.shop.partials.product-options')
                            </div>

                            <div class="collection-contents">
                                <h3 class="collection-title color-four">
                                    <a href="{{to_product_details($product->slug)}}"> {!! product_limited_text($product->name) !!} </a>
                                </h3>
                                <div class="d-flex align-items-center justify-content-center gap-2 mt-3">
                                    @php
                                        $price_class = 'fs-22 fw-500 flash-prices color-four';
                                    @endphp
                                    {!! render_product_dynamic_price_markup($product, sale_price_class: $price_class, regular_price_markup_tag: 's') !!}
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>
