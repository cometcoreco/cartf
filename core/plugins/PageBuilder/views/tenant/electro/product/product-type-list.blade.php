<section class="stoere-area" data-padding-top="{{$data['padding_top']}}"
         data-padding-bottom="{{$data['padding_bottom']}}">
    <div class="container-three">
        <div class="row">
            <div class="col-lg-12">
                <div class="section-title justify-content-center">
                    <h2 class="title"> {{$data['title'] ?? ''}} </h2>
                </div>
            </div>
        </div>
        <div class="row margin-top-10">
            <div class="col-lg-12 margin-top-40">
                <div class="product-list product-list-electro">
                    <ul class="product-button isootope-button style-02 color-four justify-content-center">
                        @php
                            $all = !empty($data['categories']) ? $data['categories']->pluck('id')->toArray() : '';
                            $allIds = implode(',', $all);
                        @endphp

                        <li class="list active"
                            data-limit="{{$data['product_limit']}}"
                            data-tab="all"
                            data-all-id="{{$allIds}}"
                            data-sort_by="{{$data['sort_by']}}"
                            data-sort_to="{{$data['sort_to']}}"
                            data-filter="*">{{__('All')}}</li>
                        @foreach($data['categories'] ?? [] as $category)
                            <li class="list"
                                data-tab="{{$category->slug}}"
                                data-limit="{{$data['product_limit']}}"
                                data-sort_by="{{$data['sort_by']}}"
                                data-sort_to="{{$data['sort_to']}}">{{$category->name}} </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
        <div class="imageloaded">
            <div class="row grid margin-top-10 padding-10 markup_wrapper">
                @foreach($data['products'] ?? [] as $product)
                    @php
                        $delay = '.1s';
                        $class = 'fadeInUp';
                        if ($loop->even)
                        {
                            $delay = '.2s';
                            $class = 'fadeInDown';
                        }

                        $image_markup = \App\Facades\ImageRenderFacade::getParent($product->image_id)
                        ->getChild(to_product_details($product->slug))
                        ->getGrandChild()
                        ->renderAll();
                    @endphp

                    <div class="col-xl-3 col-lg-3 col-md-4 col-sm-6 col-{{productCards()}} margin-top-30 grid-item wow {{$class}}"
                         data-wow-delay="{{$delay}}">
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

        @if(!empty($data['view_all_button_text']))
            <div class="row">
                <div class="col-lg-12">
                    <div class="btn-wrapper text-center margin-top-50">
                        <a href="{{$data['view_all_button_url']}}"
                           class="cmn-btn btn-outline-four color-four radius-0"> {{$data['view_all_button_text']}} </a>
                    </div>
                </div>
            </div>
        @endif
    </div>
</section>
