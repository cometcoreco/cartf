@extends('tenant.frontend.frontend-page-master')

@section('title')
    {{ $category->name }}
@endsection

@section('page-title')
    {{ucfirst($type)}}: {{ $category->name }}
@endsection

@section('content')
    <!-- Book Filter area start -->
    <div class="responsive-overlay"></div>
    <section class="shop-area section-bg-2 padding-top-100 padding-bottom-100">
        <div class="container custom-container-one">
            <div class="shop-contents-wrapper">
                <div class="shop-icon">
                    <div class="shop-icon-sidebar">
                        <i class="las la-bars"></i>
                    </div>
                </div>

                <!-- Book grid area start -->
                <div class="shop-grid-contents">
                    <div class="row gy-4 gy-lg-5">
                        @foreach($products as $product)
                            @php
                                $data_info = get_digital_product_dynamic_price($product);
                                $regular_price = $data_info['regular_price'];
                                $sale_price = $data_info['sale_price'];
                                $discount = $data_info['discount'];

                                $price = $sale_price > 0 ? $sale_price : $regular_price;
                            @endphp

                            <div class="col-xxl-3 col-xl-4 col-lg-4 col-md-4 col-sm-6">
                                <div class="global-card hover-overlay center-text bg-white book-filter-padding">
                                    <div class="global-card-thumb">
                                        <a href="{{route('tenant.digital.shop.product.details', $product->slug)}}">
                                            {!! render_image_markup_by_attachment_id($product->image_id, 'product-image') !!}
                                        </a>

                                        @if($discount > 0)
                                            <div class="global-badge left-side">
                                                <span class="global-badge-box"> {{$discount}}% {{__('off')}} </span>
                                            </div>
                                        @endif

                                        @if(!empty($product->additionalFields?->badge_id))
                                            <div class="global-badge left-side">
                                                <span class="global-badge-box bg-new"> {{$product->additionalFields?->badge?->name}} </span>
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Product options start -->
                                    @include(include_theme_path('digital-shop.partials.product-options'))
                                    <!-- Product options end -->

                                    <div class="global-card-contents mt-3">
                                        <h5 class="global-card-contents-title-two">
                                            <a href="{{route('tenant.digital.shop.product.details', $product->slug)}}"> {!! Str::words($product->name, 15) !!} </a>
                                        </h5>
                                        <span class="global-card-contents-subtitle mt-2"> {{$product->additionalFields?->author?->name}} </span>
                                        <div class="price-update-through mt-3">
                                            <span class="flash-prices color-one"> {{float_amount_with_currency_symbol($price)}} </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach

                        <div class="pagination mt-60">
                            {!! $products->links() !!}
                        </div>
                    </div>
                </div>
                <!-- Book grid area end -->
            </div>
        </div>
    </section>
    <!-- Book Filter area end -->
@endsection

@section('scripts')
    <script>
        $(function () {
            $(document).on('click', 'ul.pagination .page-item a', function (e) {
                e.preventDefault();

                filter_product_request($(this).data('page'));
            })

            $(document).on('click', '.ad-values, .active-list .list, .price-search-btn', function (e) {
                // e.preventDefault();
                let currentPage = $(".pagination .page-item .page-link.active").attr("data-page");
                filter_product_request(currentPage);
            })

            // Wishlist Product
            $(document).on('click', '.wishlist-btn', function (e) {
                let el = $(this);
                let product = el.data('product_id');

                $.ajax({
                    url: '{{route('tenant.shop.wishlist.product')}}',
                    type: 'GET',
                    data: {
                        product_id: product
                    },
                    beforeSend: function () {
                        $('.loader').show();
                    },
                    success: function (data) {
                        $('.loader').hide();


                        if (data.type === 'success') {
                            toastr.success(data.msg)
                        } else {
                            toastr.error(data.msg);
                        }
                    },
                    error: function (data) {
                        $('.loader').hide();
                    }
                });
            });

            /*========================================
                Click Clear Filter
            ========================================*/
            $(document).on('click', '.click-hide-filter .click-hide', function () {
                let filter_name = '.' + $(this).parent().data('filter') + ' .active';
                $(filter_name).removeClass('active');

                filter_product_request();

                $(this).parent().remove();

                let filter_children = $('.selected-flex-list').children();
                if (filter_children.length === 0) {
                    $('.selectder-filter-contents').fadeOut();
                }
            });

            $(document).on('click', '.click-hide-filter .click-hide-parent', function () {
                let filter_name = $(this).data('filter');

                if (filter_name === 'all') {
                    $('.active-list .active').removeClass('active');

                    $('.ui-range-value-min .min_price').text('0');
                    $('.ui-range-value-min input').val(0);

                    $('.ui-range-value-max .max_price').text('10000');
                    $('.ui-range-value-max input').val(10000);

                    $('.noUi-base .noUi-connect').css('left', '0%');
                    $('.noUi-base .noUi-background').css('left', '100%');

                    filter_product_request();

                    $('.selectder-filter-contents').fadeOut();
                    $(this).siblings('ul').html('');
                }
            });

            $(document).on('click', '.shop-nice-select ul.list li.option', function (e) {
                let sort = $(this).data('value');
                let currentPage = $(".pagination .page-item .page-link.active").attr("data-page");

                filter_product_request(currentPage, sort);
            });

            /*========================================
                Range Slider
            ========================================*/
            let i = document.querySelector(".ui-range-slider");
            if (void 0 !== i && null !== i) {
                let j = parseInt(i.parentNode.getAttribute("data-start-min"), 10),
                    k = parseInt(i.parentNode.getAttribute("data-start-max"), 10),
                    l = parseInt(i.parentNode.getAttribute("data-min"), 10),
                    m = parseInt(i.parentNode.getAttribute("data-max"), 10),
                    n = parseInt(i.parentNode.getAttribute("data-step"), 10),
                    o = document.querySelector(".ui-range-value-min span"),
                    p = document.querySelector(".ui-range-value-max span"),
                    q = document.querySelector(".ui-range-value-min input"),
                    r = document.querySelector(".ui-range-value-max input");

                noUiSlider.create(i, {
                    start: [j, k],
                    connect: !0,
                    step: n,
                    range: {
                        min: l,
                        max: m
                    },
                    behaviour: 'tap'
                }), i.noUiSlider.on("change", function (a, b) {
                    let c = a[b];

                    b ? (p.innerHTML = Math.round(c), r.value = Math.round(c)) : (o.innerHTML = Math.round(c), q.value = Math.round(c))

                    let currentPage = $(".pagination .page-item .page-link.active").attr("data-page");

                    filter_product_request(currentPage);
                })
            }

            function filter_product_request(page = null, sort = null) {
                let url = '{{route('tenant.shop')}}';
                let category_slug = $('.category-lists .active').data('slug')
                let size_slug = $('.size-lists .active').data('slug')
                let color_slug = $('.color-lists .active').data('slug')
                let rating = $('.filter-lists .active').data('slug');
                let min_price = $('.ui-range-value-min input').val();
                let max_price = $('.ui-range-value-max input').val();
                let tag_slug = $('.tag-lists .active').data('slug');
                let requestPage = null;
                if (page !== null) {
                    requestPage = page
                }

                let sortBy = null;
                if (sort !== null) {
                    sortBy = sort;
                }

                $.ajax({
                    type: 'GET',
                    url: url,
                    data: {
                        'category': category_slug,
                        'size': size_slug,
                        'color': color_slug,
                        'rating': rating,
                        'min_price': min_price,
                        'max_price': max_price,
                        'tag': tag_slug,
                        'page': requestPage,
                        'sort': sortBy
                    },

                    beforeSend: function () {
                        $('.loader').show();
                    },
                    success: function (data) {
                        $(".grid-product-list").html(data.grid)
                        $(".list-product-list").html(data.list)

                        $(".shop-icons.active").trigger('click');

                        let paginationData = data.pagination;
                        let fromItems = paginationData.from;
                        let toItems = paginationData.to;
                        let totalItems = paginationData.total;

                        $('.showing-results').text('{{__('Showing')}} ' + fromItems + ' - ' + totalItems + ' of ' + totalItems + ' {{__('Results')}}');

                        setInterval(() => {
                            $('.loader').hide();
                        }, 700)
                    },
                    error: function (data) {

                    }
                });
            }

            $(document).on('keyup', 'input[name=search]', function (e) {
                let search = $(this).val();

                if (search === '') {
                    setTimeout(() => {
                        location.reload();
                    }, 500)
                }

                $.ajax({
                    type: 'GET',
                    url: '{{route('tenant.shop.search')}}',
                    data: {
                        'search': search,
                    },

                    beforeSend: function () {
                        $('.loader').show();
                    },
                    success: function (data) {
                        $(".grid-product-list").html(data.grid)
                        $(".list-product-list").html(data.list)

                        $(".shop-icons.active").trigger('click');

                        let paginationData = data.pagination;
                        let fromItems = paginationData.from !== null ? paginationData.from : 0;
                        let toItems = paginationData.to;
                        let totalItems = paginationData.total;

                        $('.showing-results').text('{{__('Showing')}} ' + fromItems + ' - ' + totalItems + ' of ' + totalItems + ' {{__('Results')}}');

                        setInterval(() => {
                            $('.loader').hide();
                        }, 700)
                    },
                    error: function (data) {

                    }
                });
            });

            /*========================================
                Product Quick View Modal
            ========================================*/
            $(document).on('click', 'a.popup-modal', function (e) {
                let el = $(this).parent();
                let id = el.data('id');
                let modal = $('#product-modal');

                $.ajax({
                    type: 'GET',
                    url: '{{route('tenant.shop.quick.view')}}',
                    data: {
                        'id': id,
                    },

                    beforeSend: function () {
                        $('.loader').show();
                    },
                    success: function (data) {
                        modal.html(data.product_modal);

                        setInterval(() => {
                            $('.loader').hide();
                        }, 700)
                    },
                    error: function (data) {

                    }
                });
            });
        });
    </script>
@endsection
