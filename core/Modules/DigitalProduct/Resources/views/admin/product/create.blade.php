@extends('tenant.admin.admin-master')
@section('title')
    {{__('Add New Digital Product')}}
@endsection
@section('site-title')
    {{__('Add New Digital Product')}}
@endsection
@section('style')
    <link rel="stylesheet" href="{{ global_asset('assets/common/css/flatpickr.min.css') }}">
    <link rel="stylesheet" href="{{global_asset('assets/tenant/backend/css/bootstrap-taginput.css')}}">
    <link rel="stylesheet" href="{{global_asset('assets/common/css/select2.min.css')}}">
    <x-digitalproduct::product-file-uploader.css/>
    <x-media-upload.css/>
    <x-summernote.css/>

    <style>
        @-webkit-keyframes spin {
            0% {
                -webkit-transform: rotate(0deg);
                transform: rotate(0deg);
            }

            100% {
                -webkit-transform: rotate(360deg);
                transform: rotate(360deg);
            }
        }

        @keyframes spin {
            0% {
                -webkit-transform: rotate(0deg);
                transform: rotate(0deg);
            }

            100% {
                -webkit-transform: rotate(360deg);
                transform: rotate(360deg);
            }
        }

        .icon-container {
            position: absolute;
            top: 20px;
            left: 50%;
        }

        .loading-icon {
            border-radius: 50%;
            width: 40px;
            height: 40px;
            border: 0.55rem solid #ddd;
            border-top-color: #333;
            display: inline-block;
            margin: 0 8px;

            -webkit-animation-name: spin;
            -webkit-animation-duration: 1s;
            -webkit-animation-iteration-count: infinite;

            animation-name: spin;
            animation-duration: 1s;
            animation-iteration-count: infinite;
        }

        .full-circle {
            -webkit-animation-timing-function: cubic-bezier(0.6, 0, 0.4, 1);
            animation-timing-function: cubic-bezier(0.6, 0, 0.4, 1);
        }

        @media screen and (max-width: 700px) {
            .container {
                width: 100%;
            }
        }

        .custom-plus,.custom-minus{
            padding-inline: 25px;
        }
    </style>
@endsection
@section('content')
    <div class="dashboard-top-contents">
        <div class="row">
            <div class="col-lg-12">
                <div class="top-inner-contents search-area top-searchbar-wrapper">
                    <div class="dashboard-flex-contetns">
                        <div class="dashboard-left-flex">
                            <h3 class="heading-three fw-500"> {{ __("Add Products") }} </h3>
                        </div>
                        <div class="dashboard-right-flex">
                            <div class="top-search-input">
                                <a class="btn btn-info btn-sm px-4"
                                   href="{{route('tenant.admin.digital.product.all')}}">{{__('Back')}}</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="dashboard-products-add bg-white radius-20 mt-4">
        <div class="row g-4">
            <div class="col-md-12">
                <div class="row gy-4 d-flex align-items-start">
                    <div class="col-xxl-2 col-xl-3 col-lg-12">
                        <div class="nav flex-column nav-pills border-1 radius-10 me-3" id="v-pills-tab" role="tablist"
                             aria-orientation="vertical">
                            <button class="nav-link active" id="v-pills-general-info-tab" data-bs-toggle="pill"
                                    data-bs-target="#v-general-info-tab" type="button" role="tab"
                                    aria-controls="v-general-info-tab" aria-selected="true"><span
                                    style='font-size:15px; padding-right: 7px;'>&#9679;</span> {{__("General Info")}}
                            </button>
                            <button class="nav-link" id="v-pills-price-tab" data-bs-toggle="pill"
                                    data-bs-target="#v-price-tab" type="button" role="tab" aria-controls="v-price-tab"
                                    aria-selected="false"><span
                                    style='font-size:15px; padding-right: 7px;'>&#9679;</span> {{ __("Price & Tax") }}
                            </button>
                            <button class="nav-link" id="v-pills-additional-tab" data-bs-toggle="pill"
                                    data-bs-target="#v-additional-tab" type="button" role="tab"
                                    aria-controls="v-additional-tab"
                                    aria-selected="false"><span
                                    style='font-size:15px; padding-right: 7px;'>&#9679;</span> {{ __("Additional Fields") }}
                            </button>
                            <button class="nav-link" id="v-pills-settings-tab" data-bs-toggle="pill"
                                    data-bs-target="#v-categories-tab" type="button" role="tab"
                                    aria-controls="v-categories-tab" aria-selected="false"><span
                                    style='font-size:15px; padding-right: 7px;'>&#9679;</span> {{ __("Categories") }}
                            </button>
                            <button class="nav-link" id="v-pills-images-tab-tab" data-bs-toggle="pill"
                                    data-bs-target="#v-images-tab" type="button" role="tab" aria-controls="v-images-tab"
                                    aria-selected="false"><span
                                    style='font-size:15px; padding-right: 7px;'>&#9679;</span> {{ __("File & Images") }}
                            </button>
                            <button class="nav-link" id="v-pills-tags-and-label" data-bs-toggle="pill"
                                    data-bs-target="#v-tags-and-label" type="button" role="tab"
                                    aria-controls="v-tags-and-label" aria-selected="false"><span
                                    style='font-size:15px; padding-right: 7px;'>&#9679;</span> {{ __("Tags & Label") }}
                            </button>
                            <button class="nav-link" id="v-pills-meta-tag-tab" data-bs-toggle="pill"
                                    data-bs-target="#v-meta-tag-tab" type="button" role="tab"
                                    aria-controls="v-meta-tag-tab" aria-selected="false"><span
                                    style='font-size:15px; padding-right: 7px;'>&#9679;</span> {{ __("Product Meta") }}
                            </button>
                            <button class="nav-link" id="v-pills-policy-tab" data-bs-toggle="pill"
                                    data-bs-target="#v-policy-tab" type="button" role="tab"
                                    aria-controls="v-policy-tab" aria-selected="false"><span
                                    style='font-size:15px; padding-right: 7px;'>&#9679;</span> {{ __("Refund Policy") }}
                            </button>
                        </div>
                    </div>
                    <div class="col-xxl-10 col-xl-9 col-lg-12">
                        <div class="info-right-wrapper">
                            <form data-request-route="{{ route("tenant.admin.digital.product.new") }}" method="post"
                                  id="product-create-form">
                                @csrf
                                <div class="form-button text-end">
                                    <button class="btn-sm btn btn-success">{{ __("Create Product") }}</button>
                                </div>
                                <div class="info-right-inner">
                                    <div class="tab-content margin-top-10" id="v-pills-tabContent">
                                        <div class="tab-pane fade show active" id="v-general-info-tab" role="tabpanel"
                                             aria-labelledby="v-general-info-tab">
                                            <x-digitalproduct::general-info/>
                                        </div>
                                        <div class="tab-pane fade" id="v-price-tab" role="tabpanel"
                                             aria-labelledby="v-price-tab">
                                            <x-digitalproduct::product-price :taxes="$data['taxes']"/>
                                        </div>
                                        <div class="tab-pane fade" id="v-additional-tab" role="tabpanel"
                                             aria-labelledby="v-additional-tab">
                                            <x-digitalproduct::product-additional-field :languages="$data['languages']" :authors="$data['authors']"/>
                                        </div>
                                        <div class="tab-pane fade" id="v-categories-tab" role="tabpanel"
                                             aria-labelledby="v-categories-tab">
                                            <x-digitalproduct::categories :categories="$data['categories']"/>
                                        </div>
                                        <div class="tab-pane fade" id="v-images-tab" role="tabpanel"
                                             aria-labelledby="v-images-tab">
                                            <x-digitalproduct::product-image/>
                                        </div>
                                        <div class="tab-pane fade" id="v-tags-and-label" role="tabpanel"
                                             aria-labelledby="v-tags-and-label">
                                            <x-digitalproduct::tags-and-badge :badges="$data['badges']"/>
                                        </div>
                                        <div class="tab-pane fade" id="v-meta-tag-tab" role="tabpanel"
                                             aria-labelledby="v-meta-tag-tab">
                                            <x-digitalproduct::meta-seo/>
                                        </div>
                                        <div class="tab-pane fade" id="v-policy-tab" role="tabpanel"
                                             aria-labelledby="v-policy-tab">
                                            <x-digitalproduct::policy/>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <x-media-upload.markup/>
        @endsection
        @section('scripts')
            <script src="{{ global_asset('assets/common/js/jquery-ui.min.js') }}" rel="stylesheet"></script>
            <script src="{{global_asset('assets/tenant/backend/js/bootstrap-taginput.min.js')}}"></script>
            <script src="{{ global_asset('assets/common/js/flatpickr.js') }}"></script>
            <script src="{{global_asset('assets/common/js/select2.min.js')}}"></script>

            <x-digitalproduct::product-file-uploader.js/>
            <x-media-upload.js/>
            <x-summernote.js/>

            <script>
                $(document).ready(function () {
                    flatpickr(".flatpickr", {
                        altInput: true,
                        altFormat: "F j, Y",
                        dateFormat: "Y-m-d",
                    });

                    $('.select2').select2({
                        placeholder: '{{__('Select an option')}}',
                        language: {
                            noResults: function () {
                                return "{{__('No result found')}}"
                            }
                        }
                    });
                });


                let temp = false;
                $(document).on("change", ".dashboard-products-add .form--control", function () {
                    $(".dashboard-products-add .form--control").each(function () {
                        if ($(this).val() != '') {
                            temp = true;
                            return false;
                        } else {
                            temp = false;
                        }
                    })
                })

                $(document).ready(function () {
                    String.prototype.capitalize = String.prototype.capitalize || function () {
                        return this.charAt(0).toUpperCase() + this.slice(1);
                    }

                    function convertToSlug(text) {
                        return text
                            .toLowerCase()
                            .replace(/ /g, '-')
                            .replace(/[^\w-]+/g, '');
                    }

                    $('#product-name , #product-slug').on('keyup', function () {
                        let title_text = $(this).val();
                        $('#product-slug').val(convertToSlug(title_text))
                    });

                    $(document).on("submit", "#product-create-form", function (e) {
                        e.preventDefault();

                        send_ajax_request("post", new FormData(e.target), $(this).attr("data-request-route"), function () {
                            toastr.warning("{{__('Request sent successfully')}}");
                        }, function (data) {
                            if (data.success) {
                                toastr.success("{{__('Product Created Successfully')}}");
                                toastr.success("{{__('You are redirected to product list page')}}");

                                $("#product-create-form").trigger("reset");
                                temp = false;
                                setTimeout(function () {
                                    window.location.href = "{{ route("tenant.admin.digital.product.all") }}";
                                }, 1000);
                            } else if (data.restricted) {
                                toastr.error("{{__('Sorry you can not upload more products due to your product upload limit')}}");

                                let nav_product = $('.product-limits-nav');
                                nav_product.find('span').css({'color': 'red', 'font-weight': 'bold'});
                                nav_product.effect("shake", {direction: "up left", times: 2, distance: 3}, 500);
                            } else if (!data.success) {
                                toastr.error(data.msg);
                            }
                        }, function (xhr) {

                            ajax_toastr_error_message(xhr);
                        });
                    })

                    let inventory_item_id = 0;
                    $(document).on("click", ".delivery-item", function () {
                        $(this).toggleClass("active");
                        $(this).effect("shake", {direction: "up", times: 1, distance: 2}, 500);
                        let delivery_option = "";
                        $.each($(".delivery-item.active"), function () {
                            delivery_option += $(this).data("delivery-option-id") + " , ";
                        })

                        delivery_option = delivery_option.slice(0, -3)

                        $(".delivery-option-input").val(delivery_option);
                    });

                    $(document).on("change", "#category", function () {
                        let data = new FormData();
                        data.append("_token", "{{ csrf_token() }}");
                        data.append("category_id", $(this).val());

                        send_ajax_request("post", data, '{{ route('tenant.admin.digital.category.sub-category') }}', function () {
                            $("#sub_category").html("<option value=''>{{__('Select Sub Category')}}</option>");
                            $("#child_category").html("<option value=''>{{__('Select Child Category')}}</option>");
                            $("#select2-child_category-container").html('');
                        }, function (data) {
                            $("#sub_category").html(data.html);
                        }, function () {

                        });
                    });

                    $(document).on("change", "#sub_category", function () {
                        let data = new FormData();
                        data.append("_token", "{{ csrf_token() }}");
                        data.append("sub_category_id", $(this).val());

                        let child_category_wrapper = $("#child_category");
                        send_ajax_request("post", data, '{{ route('tenant.admin.digital.category.child-category') }}', function () {
                            child_category_wrapper.parent().css('position', 'relative')
                            child_category_wrapper.parent().append(`<div class="icon-container text-center">
                                <div class="loading-icon full-circle"></div>
                            </div>`);

                            child_category_wrapper.html("<option value=''>{{__('Select Child Category')}}</option>");
                            $("#select2-child_category-container").html('');

                        }, function (data) {
                            child_category_wrapper.html(data.html);
                        }, function () {

                        });

                        child_category_wrapper.parent().css('position', 'unset');
                        $('.icon-container').remove();
                    });

                    $(document).on('click', '.badge-item', function (e) {
                        if ($(this).hasClass("active")) {
                            $(this).removeClass("active")
                            $("#badge_id_input").val('');
                        } else {
                            $(".badge-item").removeClass("active");
                            $(this).addClass("active");
                            $("#badge_id_input").val($(this).attr("data-badge-id"));
                        }

                        $(this).effect("shake", {direction: "up", times: 1, distance: 2}, 500);
                    });

                    $(document).on("click", ".close-icon", function () {
                        $('#media_upload_modal').modal('hide');
                    });

                    $(document).on('change' ,'#accessibility', function (){
                        let value = $(this).val();
                        let tax_price_div = $('#tax-price-info');

                        if(value === 'free')
                        {
                            tax_price_div.fadeOut();
                            tax_price_div.find('select#tax').val('');
                            tax_price_div.find('select').attr('selected', false);
                            tax_price_div.find('input').val('');
                        } else {
                            tax_price_div.fadeIn();
                        }
                    });

                    $(document).on('click', '.custom-plus', function (){
                        let custom_wrapper = $('.custom-additional-field-row');

                        let option_name_text = '{{__("Option Name")}}';
                        let option_name_value = '{{__("Option Value")}}';
                        let custom_wrapper_option = `<div class="row custom-additional-field-row mt-4">
                                                    <div class="col-5">
                                                        <input type="text" class="form--control radius-10" value="" name="option_name[]"
                                                               placeholder="${option_name_text}">
                                                    </div>
                                                    <div class="col-5">
                                                        <input type="text" class="form--control radius-10" value="" name="option_value[]"
                                                               placeholder="${option_name_value}">
                                                    </div>
                                                    <div class="col-2">
                                                        <div class="custom-button d-flex gap-3">
                                                            <a class="btn btn-info custom-plus" href="javascript:void(0)"><span class="mdi mdi-plus"></span></a>
                                                            <a class="btn btn-danger custom-minus" href="javascript:void(0)"><span class="mdi mdi-minus"></span></a>
                                                        </div>
                                                    </div>
                                                </div>`;

                        $(custom_wrapper.parent()).append(custom_wrapper_option);
                    });

                    $(document).on('click', '.custom-minus', function (){
                        let custom_wrapper = $('.custom-additional-field-row');

                        if(custom_wrapper.length > 1)
                        {
                            $(this).closest('.row').remove();
                        }
                    });

                    function send_ajax_request(request_type, request_data, url, before_send, success_response, errors) {
                        $.ajax({
                            url: url,
                            type: request_type,
                            headers: {
                                'X-CSRF-TOKEN': "{{csrf_token()}}",
                            },
                            beforeSend: (typeof before_send !== "undefined" && typeof before_send === "function") ? before_send : () => {
                                return "";
                            },
                            processData: false,
                            contentType: false,
                            data: request_data,
                            success: (typeof success_response !== "undefined" && typeof success_response === "function") ? success_response : () => {
                                return "";
                            },
                            error: (typeof errors !== "undefined" && typeof errors === "function") ? errors : () => {
                                return "";
                            }
                        });
                    }

                    function prepare_errors(data, form, msgContainer, btn) {
                        let errors = data.responseJSON;

                        if (errors.success != undefined) {
                            toastr.error(errors.msg.errorInfo[2]);
                            toastr.error(errors.custom_msg);
                        }

                        $.each(errors.errors, function (index, value) {

                            toastr.error(value[0]);
                        });
                    }


                    function ajax_toastr_error_message(xhr) {
                        $.each(xhr.responseJSON.errors, function (key, value) {
                            toastr.error((key.capitalize()).replace("-", " ").replace("_", " "), value);
                        });
                    }

                    function ajax_toastr_success_message(data) {
                        if (data.success) {
                            toastr.success(data.msg)
                        } else {
                            toastr.warning(data.msg);
                        }
                    }
                });

                $(window).bind('beforeunload', function () {
                    if (temp) {
                        return '{{__('Are you sure you want to leave?')}}';
                    }
                });
            </script>
@endsection
