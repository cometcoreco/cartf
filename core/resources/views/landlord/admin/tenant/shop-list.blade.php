@php
    $route_name ='landlord';
@endphp

@extends($route_name.'.admin.admin-master')
@section('title')
    {{__('All Shops')}}
@endsection

@section('style')
    <x-datatable.css/>
    <x-summernote.css/>

    <style>
        a {
            text-decoration: none;
        }
    </style>
@endsection

@section('content')
    <div class="col-12 stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title mb-4">{{__('All Shops')}} {{$all_tenants ? '('.count($all_tenants).')' : ''}}</h4>

                <x-error-msg/>
                <x-flash-msg/>

                <x-datatable.table>
                    <x-slot name="th">
                        <th>{{__('Shop ID')}}</th>
                        <th>{{__('Details')}}</th>
                        <th>{{__('Shop Address')}}</th>
                        <th>{{__('Browse')}}</th>
                        <th>{{__('Action')}}</th>
                    </x-slot>
                    <x-slot name="tr">
                        @foreach($all_tenants as $tenant)
                            @php
                                $url = '';
                                $central = '.'.env('CENTRAL_DOMAIN');

                                if(!empty($tenant->custom_domain?->custom_domain) && $tenant->custom_domain?->custom_domain_status == 'connected'){
                                    $custom_url = $tenant->custom_domain?->custom_domain ;
                                    $url = tenant_url_with_protocol($custom_url);
                                }else{
                                    $local_url = $tenant->id .$central ;
                                    $url = tenant_url_with_protocol($local_url);
                                }

                                $hash_token = hash_hmac('sha512',$tenant?->user?->username.'_'.$tenant->id, $tenant->unique_key);
                            @endphp

                            <tr>
                                <td>{{$tenant->id}}
                                </td>
                                <td>
                                    <p>{{__('User:').' '}} {{$tenant?->user?->name}}</p>
                                    <p>{{__('Package:').' '}} {{$tenant?->payment_log?->package_name}}</p>

                                    @php
                                        $custom_theme_name = get_static_option_central($tenant->theme_slug."_theme_name") ?? getIndividualThemeDetails($tenant->theme_slug)['name'];
                                    @endphp
                                    <p>{{__('Theme:').' '}} {{$custom_theme_name}}</p>
                                </td>
                                <td>
                                    <a href="{{$url}}" target="_blank">{{str_replace(['https://', 'http://'],'', $url)}}</a>
                                </td>

                                <td>
                                    <a class="badge rounded bg-primary px-4" href="{{$url}}" target="_blank">{{$tenant->id . '.'. env('CENTRAL_DOMAIN')}}</a>
                                    @can('users-direct-login')
                                        <a class="badge rounded bg-danger px-4" href="{{$url.'/token-login/'.$hash_token}}" target="_blank">{{__('Login as Super Admin')}}</a>
                                    @endcan
                                </td>
                                <td>
                                    <x-delete-popover permissions="domain-delete" url="{{route(route_prefix().'admin.tenant.domain.delete', $tenant->id)}}"/>
                                </td>
                            </tr>
                        @endforeach
                    </x-slot>
                </x-datatable.table>
            </div>
        </div>
    </div>


    {{--Assign Subscription Modal--}}
    <div class="modal fade" id="user_add_subscription" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{__('Assign Subscription')}}</h5>
                    <button type="button" class="close" data-bs-dismiss="modal"><span>×</span></button>
                </div>

                <form action="{{route('landlord.admin.tenant.assign.subscription')}}" id="user_add_subscription_form"
                      method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" name="subs_user_id" id="subs_user_id">
                        <input type="hidden" name="subs_pack_id" id="subs_pack_id">

                        <div class="form-group">
                            <label for="subdomain">{{__('Subdomain')}}</label>
                            <select class="form-select subdomain" id="subdomain" name="subdomain">

                            </select>
                        </div>

                        <div class="form-group custom_subdomain_wrapper mt-3">
                            <label for="custom-subdomain">{{__('Add new subdomain')}}</label>
                            <input class="form--control custom_subdomain" id="custom-subdomain" type="text"
                                   autocomplete="off" value="{{old('subdomain')}}"
                                   placeholder="{{__('Subdomain')}}"
                                   style="border:0;border-bottom: 1px solid #595959;width: 100%">
                            <div id="subdomain-wrap"></div>
                        </div>

                        <div class="form-group mt-3">
                            @php
                                $themes = getAllThemeSlug();
                            @endphp
                            <label for="custom-theme">{{__('Add Theme')}}</label>
                            <select class="form-select text-capitalize" name="custom_theme" id="custom-theme">
                                @foreach($themes as $theme)
                                    <option value="{{$theme}}">{{$theme}}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="">{{__('Select A Package')}}</label>
                            <select class="form-control package_id_selector" name="package">
                                <option value="">{{__('Select Package')}}</option>
                                @foreach(\App\Models\PricePlan::all() as $price)
                                    <option value="{{$price->id}}" data-id="{{$price->id}}">
                                        {{$price->title}} {{ '('.float_amount_with_currency_symbol($price->price).')' }}
                                        - {{\App\Enums\PricePlanTypEnums::getText($price->type)}}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="">{{__('Payment Status')}}</label>
                            <select class="form-control" name="payment_status">
                                <option value="complete">{{__('Complete')}}</option>
                                <option value="pending">{{__('Pending')}}</option>
                            </select>
                            <small
                                class="text-primary">{{__('You can set payment status pending or complete from here')}}</small>
                        </div>

                        <div class="form-group">
                            <label for="">{{__('Account Status')}}</label>
                            <select class="form-control" name="account_status">
                                <option value="complete">{{__('Complete')}}</option>
                                <option value="pending">{{__('Pending')}}</option>
                            </select>
                            <small
                                class="text-primary">{{__('You can set account status pending or complete from here')}}</small>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{__('Close')}}</button>
                        <button type="submit" class="btn btn-primary order-btn">{{__('Submit')}}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{--Change Password Modal--}}
    <div class="modal fade" id="tenant_password_change" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{__('Change Admin Password')}}</h5>
                    <button type="button" class="close" data-bs-dismiss="modal"><span>×</span></button>
                </div>

                <form action="{{route('landlord.admin.tenant.change.password')}}" id="user_password_change_modal_form"
                      method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" name="ch_user_id" id="ch_user_id">
                        <div class="form-group">
                            <label for="password">{{__('Password')}}</label>
                            <input type="password" class="form-control" name="password"
                                   placeholder="{{__('Enter Password')}}">
                        </div>
                        <div class="form-group">
                            <label for="password_confirmation">{{__('Confirm Password')}}</label>
                            <input type="password" class="form-control" id="password_confirmation"
                                   name="password_confirmation" placeholder="{{__('Confirm Password')}}">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{__('Close')}}</button>
                        <button type="submit" class="btn btn-primary">{{__('Change Password')}}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    {{--Send Mail Modal--}}
    <div class="modal fade" id="send_mail_to_tenant_modal" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{__('Send Mail To Subscriber')}}</h5>
                    <button type="button" class="close" data-bs-dismiss="modal"><span>×</span></button>
                </div>
                <form action="{{route(route_prefix().'admin.tenant.send.mail')}}"
                      id="send_mail_to_subscriber_edit_modal_form" method="post">
                    <div class="modal-body">
                        @csrf
                        <div class="form-group">
                            <label for="email">{{__('Email')}}</label>
                            <input type="text" readonly class="form-control" id="email" name="email"
                                   placeholder="{{__('Email')}}">
                        </div>
                        <div class="form-group">
                            <label for="edit_icon">{{__('Subject')}}</label>
                            <input type="text" class="form-control" id="subject" name="subject"
                                   placeholder="{{__('Subject')}}">
                        </div>
                        <div class="form-group">
                            <label for="message">{{__('Message')}}</label>
                            <input type="hidden" name="message">
                            <div class="summernote"></div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{__('Close')}}</button>
                        <button id="submit" type="submit" class="btn btn-primary">{{__('Send Mail')}}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <x-datatable.js/>
    <x-summernote.js/>
    <x-custom-js.landloard-unique-subdomain-check :name="'custom_subdomain'"/>
    {{--subdomain check--}}

    <script>
        $('.table-wrap > table').DataTable( {
            "order": [[0, "asc" ]]
        });

        $(document).ready(function () {
            let theme_selected_first = false; // if theme selected first then after domain selection do not change theme again

            $(document).on('click', '.user_change_password_btn', function (e) {
                e.preventDefault();
                var el = $(this);

                var form = $('#user_password_change_modal_form');
                form.find('#ch_user_id').val(el.data('id'));
            });


            //Assign Subscription Modal Code
            $(document).on('click', '.user_add_subscription', function () {
                let user_id = $(this).data('id');
                $('#subs_user_id').val(user_id);
                $("#user_add_subscription #subdomain").html($(this).data('select-markup'));
            });

            $(document).on('change', '.package_id_selector', function () {
                let el = $(this);
                let form = $('.user_add_subscription_form');
                $('#subs_pack_id').val(el.val());
            });

            let custom_subdomain_wrapper = $('.custom_subdomain_wrapper');
            custom_subdomain_wrapper.hide();
            $(document).on('change', '#subdomain', function (e) {
                let el = $(this);
                let subdomain_type = el.val();

                if (subdomain_type == 'custom_domain__dd') {
                    custom_subdomain_wrapper.slideDown();
                    custom_subdomain_wrapper.find('#custom-subdomain').attr('name', 'custom_subdomain');
                } else {
                    custom_subdomain_wrapper.slideUp();
                    custom_subdomain_wrapper.removeAttr('#custom-subdomain').attr('name', 'custom_subdomain');
                }
            });

            $(document).on('change', '#subdomain', function () {
                let el = $(this).parent().parent().find(".form-group #custom-theme");
                let subdomain = $(this).val();

                if (!theme_selected_first) {
                    $.ajax({
                        url: '{{route('landlord.admin.tenant.check.subdomain.theme')}}',
                        type: 'POST',
                        data: {
                            _token: '{{csrf_token()}}',
                            subdomain: subdomain
                        },
                        beforeSend: function () {
                            el.find('option').attr('selected', false);
                        },
                        success: function (res) {
                            el.find("option[value=" + res.theme_slug + "]").attr('selected', true);
                        }
                    });
                }
            });

            $(document).on('change', '#custom-theme', function () {
                theme_selected_first = true;
            });

            $(document).on('submit', '#user_add_subscription_form', function () {
                $(this).find('button[type=submit]').attr('disabled', true);
            });
        });
    </script>

    <script>
        (function ($) {
            "use strict";
            $(document).ready(function () {

                $(document).on('click', '.send_mail_to_tenant_btn', function () {
                    var el = $(this);
                    var email = el.data('id');

                    var form = $('#send_mail_to_subscriber_edit_modal_form');
                    form.find('#email').val(email);
                });
                $('.summernote').summernote({
                    height: 300,   //set editable area's height
                    codemirror: { // codemirror options
                        theme: 'monokai'
                    },
                    callbacks: {
                        onChange: function (contents, $editable) {
                            $(this).prev('input').val(contents);
                        }
                    }
                });
            });

        })(jQuery)
    </script>
@endsection

