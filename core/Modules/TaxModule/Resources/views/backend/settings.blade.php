@extends('tenant.admin.admin-master')

@section("title", __("Tax Module Settings"))

@section("content")
    <div class="card">
        <div class="card-header">
            <h3 class="title">{{ __("Tax module settings") }}</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('tenant.admin.tax-module.settings') }}" method="post" class="row">
                @csrf
                @method('PUT')
                <div class="col-md-6">
                    <div class="form-group row">
                        <label for="tax_system" class="col-md-4">{{ __("Select Tax System") }}
                            <span id="enable-info-about-tax-system">
                                <i class="las la-info-circle"></i>
                            </span>
                        </label>
                        <div class="col-md-8">
                            <select class="form-control" name="tax_system" id="tax_system">
                                <option @selected(get_static_option('tax_system') == "zone_wise_tax_system") value="zone_wise_tax_system">{{ __("Zone wise tax system") }}</option>
                                <option @selected(get_static_option("tax_system") == "advance_tax_system") value="advance_tax_system">{{ __("Advance Tax system") }}</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-12 p-0 m-0" id="advance_tax_system_settings">
                        <div class="form-group row">
                            <label for="prices_entered_with_tax" class="col-md-4">{{ __("Prices entered with tax") }}</label>
                            <fieldset id="prices_entered_with_tax" class="col-md-8">
                                <ul>
                                    <li>
                                        <label><input name="prices_include_tax" @checked(get_static_option("prices_include_tax") == 'yes') value="yes" type="radio" style="" class="">{{__('Yes, I will enter prices inclusive of tax')}}</label>
                                    </li>
                                    <li>
                                        <label><input name="prices_include_tax" @checked(get_static_option("prices_include_tax") == 'no') value="no" type="radio" style="" class="">{{__('No, I will enter prices exclusive of tax')}}</label>
                                    </li>
                                </ul>
                            </fieldset>
                        </div>

                        <div class="form-group row">
                            <label for="calculate_tax_based_on" class="col-md-4">{{ __("Calculate tax based on") }}</label>
                            <div class="col-md-8">
                                <select name="calculate_tax_based_on" id="calculate_tax_based_on" class="form-control">
                                    <option @selected(get_static_option("calculate_tax_based_on") == "customer_account_address") value="customer_account_address"> {{ __("Customer Account Address") }} </option>
                                    <option @selected(get_static_option("calculate_tax_based_on") == "customer_billing_address") value="customer_billing_address"> {{ __("Customer Billing Address") }} </option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="shipping_tax_class" class="col-md-4">{{ __("Shipping tax class") }}</label>
                            <div class="col-md-8">
                                <select name="shipping_tax_class" id="shipping_tax_class" class="form-control">
                                    <option value="shipping_tax_class_based_on_cart_items"> {{__("Shipping tax class based on cart items")}} </option>
                                    @foreach($taxClasses as $taxClass)
                                        <option @selected(get_static_option("shipping_tax_class") == $taxClass->id) value="{{ $taxClass->id }}"> {{ $taxClass->name }} </option>
                                    @endforeach
                                </select>

                                <div class="mt-2">
                                    <a href="{{ route('tenant.admin.tax-module.tax-class') }}" class="text-primary">{{ __("Add tax class") }}</a>
                                </div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="tax_round_at_subtotal" class="col-md-4">{{ __("Rounding") }}</label>
                            <div class="col-md-8">
                                <label for="tax_round_at_subtotal" class="form-check-label">
                                    <input name="tax_round_at_subtotal" {{ get_static_option("tax_round_at_subtotal") ? "checked" : "" }} id="tax_round_at_subtotal" type="checkbox" class="form-control-check" value="1">
                                    <span>{{ __("Round tax at subtotal level, instead of rounding per line") }}</span>
                                </label>
                            </div>
                        </div>

                        <div class="form-group row display_price_wrapper">
                            <label for="tax_round_at_subtotal" class="col-md-4">{{ __("Display prices in the shop") }}</label>
                            <div class="col-md-8">
                                <select id="display_price_in_the_shop" name="display_price_in_the_shop" class="form-control">
                                    <option @selected(get_static_option("display_price_in_the_shop") == "including") value="including"> {{__("Including tax")}} </option>
                                    <option @selected(get_static_option("display_price_in_the_shop") == "exclusive") value="exclusive"> {{ __("Exclusive tax") }} </option>
                                </select>
                            </div>
                        </div>

                        @if(false)
                            <div class="form-group row">
                                <label for="tax_round_at_subtotal" class="col-md-4">{{ __("Display tax totals") }}</label>
                                <div class="col-md-8">
                                    <select id="display_tax_total" name="display_tax_total" class="form-control">
                                        <option @selected(get_static_option("display_tax_total") == "itemized") value="itemized"> {{__("Itemized")}} </option>
                                        <option @selected(get_static_option("display_tax_total") == "single") value="single"> {{ __("As a single total") }} </option>
                                    </select>
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">{{ __("Update Tax Settings") }}</button>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="info-tax-system d-none">
                        <p>{{ __("Our Advanced Tax System offers unparalleled features for seamless tax management.") }}</p>
                        <p>{{ __("With the ability to select multiple taxes across various regions, you can effortlessly comply with global regulations.") }}</p>
                        <p>{{ __("Choose to display taxes individually per product or as a subtotal at checkout, providing transparency and a streamlined customer experience.") }}</p>
                        <p>{{ __("Simplify tax calculations, enhance efficiency, and ensure compliance with ease.") }}</p>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@section("scripts")
    <script>
        $(document).on("click", "#enable-info-about-tax-system", function (){
            $(".info-tax-system").toggleClass("d-none");
        });

        tax_system();
        sidebar();
        display_price_in_shop($('input[name=prices_include_tax]:checked'));

        $(document).on("change", "#tax_system", function () {
            tax_system();

            let tax_type = $(this);
            $('label.info-message').remove();
            if (tax_type.val() === 'zone_wise_tax_system')
            {
                tax_type.after('<label class="info-message text-primary mt-2">{{__('Press update, you will get country and state tax option on sidebar')}}</label>')
            }
        });

        $(document).on('change', 'input[name=prices_include_tax]', function () {
            let el = $(this);

            display_price_in_shop(el);
        });

        function tax_system()
        {
            let tax_system = $("#tax_system").val();
            if(tax_system === 'zone_wise_tax_system'){
                $("#advance_tax_system_settings").fadeOut();
                return "";
            }

            $("#advance_tax_system_settings").fadeIn();
        }

        function sidebar()
        {
            let tax_system = $("#tax_system").val();
            let tax_manage_menu = $('#tax-manage-menu-items');
            let country_state = tax_manage_menu.find('ul').children().slice(0,2);
            let tax_class = tax_manage_menu.find('ul').children().slice(3,4);
            if(tax_system === 'zone_wise_tax_system'){
                country_state.fadeIn();
                tax_class.fadeOut();
            } else {
                country_state.fadeOut();
                tax_class.fadeIn();
            }
        }

        function display_price_in_shop(selector)
        {
            if(selector.val() === 'yes')
            {
                $('.display_price_wrapper').fadeIn();
            } else {
                $('.display_price_wrapper').hide();
            }
        }
    </script>
@endsection
