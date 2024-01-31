<header class="header-style-01">
    <!-- Topbar area Starts -->
    @if(get_static_option('topbar_show_hide'))
        <div class="topbar-area index-07 color-04 topbar-bg-1">
            <div class="container-three">
                <div class="row justify-content-between align-items-center">
                    <div class="col-lg-6 col-md-6">
                        <div class="topbar-left-contents">
                            <div class="topbar-left-flex">
                                @if(get_static_option('social_info_show_hide'))
                                    @php
                                        $social_links = \App\Models\TopbarInfo::all();
                                    @endphp
                                    <ul class="topbar-social">
                                        @foreach($social_links as $item)
                                            <li>
                                                <a href="{{$item->url ?? '#'}}">
                                                    <i class="{{$item->icon}}"></i>
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-6">
                        <div class="topbar-right-contents">
                            <div class="topbar-right-flex">
                                @if(get_static_option('topbar_menu_show_hide'))
                                    @php
                                        $topbar_menu_id = get_static_option('topbar_menu') ?? 1;

                                    @endphp
                                    <div class="topbar-faq text-white">
                                        <ul class="d-flex gap-3">
                                            {!! render_frontend_menu($topbar_menu_id) !!}
                                        </ul>
                                    </div>
                                @endif

                                @if(get_static_option('contact_info_show_hide'))
                                    @php
                                        $topbar_phone = get_static_option('topbar_phone');
                                    @endphp
                                    <span class="call-us text-white"> {{__('Call Us:')}} <a href="tel:{{$topbar_phone}}"> {{$topbar_phone}} </a> </span>
                                @endif

                                <div class="login-account">
                                    <a href="javascript:void(0)" class="accounts hover-color-four text-white"> Account
                                        <i class="las la-user"></i> </a>
                                    <ul class="account-list-item hover-color-four">
                                        @auth('web')
                                            <li class="list">
                                                <a href="{{route('tenant.user.home')}}"> {{__('Dashboard')}} </a>
                                            </li>
                                            <li class="list">
                                                <a href="{{route('tenant.user.logout')}}"> {{__('Log Out')}} </a>
                                            </li>
                                        @else
                                            <li class="list">
                                                <a href="{{route('tenant.user.login')}}"> {{__('Sign In')}} </a>
                                            </li>
                                            <li class="list">
                                                <a href="{{route('tenant.user.register')}}"> {{__('Sign Up')}} </a>
                                            </li>
                                        @endauth
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
    <!-- Topbar area Ends -->

    <div class="searchbar-area">
        <!-- Menu area Starts -->
        <nav class="navbar navbar-area nav-color-four index-07 nav-two navbar-expand-lg navbar-border">
            <div class="container container-three nav-container">
                <div class="responsive-mobile-menu">
                    <div class="logo-wrapper">
                        <a href="{{url('/')}}" class="logo">
                            {!! render_image_markup_by_attachment_id(get_static_option('site_logo')) !!}
                        </a>
                    </div>
                    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#bizcoxx_main_menu"
                            aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                </div>
                <div class="collapse navbar-collapse" id="bizcoxx_main_menu">
                    <ul class="navbar-nav">
                        {!! render_frontend_menu($primary_menu) !!}
                    </ul>
                </div>
                <div class="nav-right-content">
                    <ul>
                        <li>
                            <div class="info-bar-item">
                                <div class="track-icon-list style-02">
                                    <a href="javascript:void(0)" class="single-icon search-open">
                                        <span class="icon"> <i class="las la-search"></i> </span>
                                    </a>
                                    <div class="single-icon cart-shopping">
                                        <a class="icon" href="{{route('tenant.shop.compare.product.page')}}"> <i
                                                class="las la-sync"></i> </a>
                                    </div>
                                    <div class="single-icon cart-shopping">
                                        @php
                                            $cart = \Gloudemans\Shoppingcart\Facades\Cart::instance("wishlist")->content();
                                            $subtotal = \Gloudemans\Shoppingcart\Facades\Cart::instance("wishlist")->subtotal();
                                        @endphp
                                        <a href="javascript:void(0)" class="icon"> <i class="lar la-heart"></i>
                                        </a>
                                        <a href="javascript:void(0)"
                                           class="icon-notification"> {{\Gloudemans\Shoppingcart\Facades\Cart::instance("wishlist")->content()->count()}} </a>
                                        <div class="addto-cart-contents">
                                            <div class="single-addto-cart-wrappers">
                                                @forelse($cart as $cart_item)
                                                    <div class="single-addto-carts">
                                                        <div class="addto-cart-flex-contents">
                                                            <div class="addto-cart-thumb">
                                                                {!! render_image_markup_by_attachment_id($cart_item?->options?->image) !!}
                                                            </div>
                                                            <div class="addto-cart-img-contents">
                                                                <h6 class="addto-cart-title fs-18"> {{Str::words($cart_item->name, 5)}} </h6>
                                                                <span class="name-subtitle d-block">
                                                                        @if($cart_item?->options?->color_name)
                                                                        {{__('Color:')}} {{$cart_item?->options?->color_name}}
                                                                        ,
                                                                    @endif

                                                                    @if($cart_item?->options?->size_name)
                                                                        {{__('Size:')}} {{$cart_item?->options?->size_name}}
                                                                    @endif

                                                                    @if($cart_item?->options?->attributes)
                                                                        <br>
                                                                        @foreach($cart_item?->options?->attributes as $key => $attribute)
                                                                            {{$key.':'}} {{$attribute}}{{!$loop->last ? ',' : ''}}
                                                                        @endforeach
                                                                    @endif
                                                                </span>

                                                                <div class="price-updates margin-top-10">
                                                                    <span
                                                                        class="price-title fs-16 fw-500 color-heading"> {{amount_with_currency_symbol($cart_item->price)}} </span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <span
                                                            class="addto-cart-counts color-heading fw-500"> {{$cart_item->qty}} </span>
                                                        <a href="javascript:void(0)" class="close-cart">
                                                            <span class="icon-close color-heading"> <i
                                                                    class="las la-times"></i> </span>
                                                        </a>
                                                    </div>
                                                @empty
                                                    <div class="single-addto-carts">
                                                        <p class="text-center">{{__('No Item in Wishlist')}}</p>
                                                    </div>
                                                @endforelse
                                            </div>
                                        </div>
                                    </div>
                                    <div class="single-icon cart-shopping">
                                        @php
                                            $cart = \Gloudemans\Shoppingcart\Facades\Cart::instance("default")->content();
                                            $subtotal = 0;
                                        @endphp
                                        <a href="javascript:void(0)" class="icon"> <i class="las la-shopping-cart"></i>
                                        </a>
                                        <a href="javascript:void(0)"
                                           class="icon-notification"> {{\Gloudemans\Shoppingcart\Facades\Cart::instance("default")->content()->count()}} </a>
                                        <div class="addto-cart-contents">
                                            <div class="single-addto-cart-wrappers">
                                                @forelse($cart as $cart_item)
                                                    <div class="single-addto-carts">
                                                        <div class="addto-cart-flex-contents">
                                                            <div class="addto-cart-thumb">
                                                                {!! render_image_markup_by_attachment_id($cart_item?->options?->image) !!}
                                                            </div>
                                                            <div class="addto-cart-img-contents">
                                                                <h6 class="addto-cart-title fs-18"> {{Str::words($cart_item->name, 5)}} </h6>
                                                                <span class="name-subtitle d-block">
                                                                        @if($cart_item?->options?->color_name)
                                                                        {{__('Color:')}} {{$cart_item?->options?->color_name}}
                                                                        ,
                                                                    @endif

                                                                    @if($cart_item?->options?->size_name)
                                                                        {{__('Size:')}} {{$cart_item?->options?->size_name}}
                                                                    @endif

                                                                    @if($cart_item?->options?->attributes)
                                                                        <br>
                                                                        @foreach($cart_item?->options?->attributes as $key => $attribute)
                                                                            {{$key.':'}} {{$attribute}}{{!$loop->last ? ',' : ''}}
                                                                        @endforeach
                                                                    @endif
                                                                </span>

                                                                @php
                                                                    $subtotal += calculatePrice($cart_item->price * $cart_item->qty, $cart_item->options)
                                                                @endphp
                                                                <div class="price-updates margin-top-10">
                                                                    <span
                                                                        class="price-title fs-16 fw-500 color-heading"> {{amount_with_currency_symbol(calculatePrice($cart_item->price, $cart_item->options))}} </span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <span
                                                            class="addto-cart-counts color-heading fw-500"> {{$cart_item->qty}} </span>
                                                        <a href="javascript:void(0)" class="close-cart">
                                                            <span class="icon-close color-heading"> <i
                                                                    class="las la-times"></i> </span>
                                                        </a>
                                                    </div>
                                                @empty
                                                    <div class="single-addto-carts">
                                                        <p class="text-center">{{__('No Item in Wishlist')}}</p>
                                                    </div>
                                                @endforelse
                                            </div>

                                            @if($cart->count() != 0)
                                                <div class="cart-total-amount">
                                                    <h6 class="amount-title"> {{__('Total Amount:')}} </h6> <span
                                                        class="fs-18 fw-500 color-light"> {{amount_with_currency_symbol($subtotal)}} </span>
                                                </div>
                                                <div class="btn-wrapper margin-top-20">
                                                    <a href="{{route('tenant.shop.checkout')}}"
                                                       class="cmn-btn btn-bg-1 radius-0 w-100">
                                                        {{__('CheckOut')}} </a>
                                                </div>
                                                <div class="btn-wrapper margin-top-20">
                                                    <a href="{{route('tenant.shop.cart')}}"
                                                       class="cmn-btn btn-outline-one radius-0 w-100">
                                                        {{__('View Cart')}} </a>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                @if(!get_static_option('topbar_show_hide'))
                                    <div class="login-account">
                                        <a href="javascript:void(0)" class="accounts">
                                            <i class="las la-user"></i>
                                        </a>
                                        <ul class="account-list-item">
                                            @auth('web')
                                                <li class="list">
                                                    <a href="{{route('tenant.user.home')}}"> {{__('Dashboard')}} </a>
                                                </li>
                                                <li class="list">
                                                    <a href="{{route('tenant.user.logout')}}"> {{__('Log Out')}} </a>
                                                </li>
                                            @else
                                                <li class="list">
                                                    <a href="{{route('tenant.user.login')}}"> {{__('Sign In')}} </a>
                                                </li>
                                                <li class="list">
                                                    <a href="{{route('tenant.user.register')}}"> {{__('Sign Up')}} </a>
                                                </li>
                                            @endauth
                                        </ul>
                                    </div>
                                @endif
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
        <!-- Menu area end -->

        <!-- Search Bar -->
        <div class="search-bar">
            <form class="menu-search-form" action="#">
                <div class="search-open-form">
                    <div class="search-close"><i class="las la-times"></i></div>
                    <input class="item-search" type="text" placeholder="{{__('Search Here....')}}"
                           id="search_form_input">
                    <button type="submit">{{__('Search Now')}}</button>
                </div>
                <div class="search-suggestions" id="search_suggestions_wrap">
                    <div class="search-suggestions-inner">
                        <h6 class="search-suggestions-title">{{__('Product Suggestions')}}</h6>
                        <ul class="product-suggestion-list mt-4">

                        </ul>
                    </div>
                </div>
            </form>
        </div>
    </div>
</header>
