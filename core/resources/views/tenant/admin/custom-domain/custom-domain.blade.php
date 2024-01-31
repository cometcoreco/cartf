@extends('tenant.admin.admin-master')
@section('title')
    {{__('Custom Domain Request')}}
@endsection

@section('style')
    <x-summernote.css/>
@endsection

@section('title')
    {{__('Custom Domain Request')}}
@endsection

@section('content')
    @php
        $central_domain = env('CENTRAL_DOMAIN');
    @endphp
    <div class="col-lg-12 col-ml-12">
        <div class="row g-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h3 class="custom_domain_title">{{get_static_option_central('custom_domain_settings_title')}}</h3>
                        <p class="custom_domain_para">{{get_static_option_central('custom_domain_settings_description')}}</p>
                        <h5 class="custom_domain_title">{{get_static_option_central('custom_domain_table_title')}}</h5>
                        <div class="recent_order_logs mt-4">
                            <table class="table table-default table-striped table-bordered">
                                <thead class="text-white bg-dark">
                                <tr>
                                    <th>{{__('Type')}}</th>
                                    <th>{{__('Host')}}</th>
                                    <th>{{__('Value')}}</th>
                                    <th>{{__('TTL')}}</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td>CNAME Record</td>
                                    <td>www</td>
                                    <td>{{env('CENTRAL_DOMAIN')}}</td>
                                    <td>Automatic</td>
                                </tr>

                                <tr>
                                    <td>CNAME Record</td>
                                    <td>@</td>
                                    <td>{{env('CENTRAL_DOMAIN')}}</td>
                                    <td>Automatic</td>
                                </tr>

                                <tr>
                                    <td colspan="4">{{__('Use this if you are using cloulflare')}}</td>
                                </tr>
                                <tr>
                                    <td>A Record</td>
                                    <td>@</td>
                                    <td>{{get_static_option_central('server_ip') ?? $_SERVER['SERVER_ADDR']}}</td>
                                    <td>Automatic</td>
                                </tr>

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <x-admin.header-wrapper>
                            <x-slot name="left">
                                <h4 class="card-title mb-4">{{__('Custom Domain Request')}}</h4>
                            </x-slot>
                            <x-slot name="right" class="d-flex">
                                <button class="btn btn-info btn-sm mb-3" data-bs-toggle="modal"
                                        data-bs-target="#new_custom_domain">{{__('Request Custom Domain')}}</button>
                            </x-slot>
                        </x-admin.header-wrapper>
                        <x-error-msg/>
                        <x-flash-msg/>
                        <div class="table-wrap table-responsive">
                            <table class="table table-default table-striped table-bordered">
                                <thead class="text-white" style="background-color: #b66dff">
                                <tr>
                                    <th>{{__('Current Domain')}}</th>
                                    <th>{{__('Requested Domain')}}</th>
                                    <th>{{__('Requested Domain Status')}}</th>
                                    <th>{{__('Date')}}</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td>{{ optional(optional(tenant()->domains)->first())->domain }}</td>
                                    <td>{{optional($custom_domain_info)->custom_domain}}</td>
                                    <td class="py-4">
                                        @if(optional($custom_domain_info)->custom_domain_status == 'pending')
                                            <span
                                                class="alert alert-warning text-capitalize">{{optional($custom_domain_info)->custom_domain_status}}</span>
                                        @elseif(optional($custom_domain_info)->custom_domain_status == 'in_progress')
                                            <span
                                                class="alert alert-info text-capitalize">{{ str_replace('_',' ',optional($custom_domain_info)->custom_domain_status) }}</span>
                                        @elseif(optional($custom_domain_info)->custom_domain_status == 'connected')
                                            <span
                                                class="alert alert-success text-capitalize">{{optional($custom_domain_info)->custom_domain_status}}</span>
                                        @elseif(optional($custom_domain_info)->custom_domain_status == 'rejected')
                                            <span
                                                class="alert alert-info text-capitalize">{{str_replace('_', ' ',ucwords(optional($custom_domain_info)->custom_domain_status))}}</span>
                                        @elseif(optional($custom_domain_info)->custom_domain_status == null)

                                        @else
                                            <span
                                                class="alert alert-danger text-capitalize">{{optional($custom_domain_info)->custom_domain_status ?? __('Removed')}}</span>
                                        @endif
                                    </td>
                                    <td>{{date('d-m-Y',strtotime($user_domain_infos->created_at))}}</td>
                                </tr>

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="new_custom_domain" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{__('Request Custom Domain')}}</h5>
                    <button type="button" class="close" data-bs-dismiss="modal"><span>×</span></button>
                </div>
                <form action="{{route('tenant.admin.custom.domain.requests')}}" method="post">
                    @csrf
                    <div class="modal-body">

                        <div class="alert-alert-warning">
                            <p>
                                {{__('You already have a custom domain ('.$central_domain.') connected with your portfolio website.
                                    if you request another domain now & if it gets connected with our server, then your current domain ('.$central_domain.') will be removed')
                                 }}
                            </p>
                        </div>

                        <div class="form-group my-3">
                            <label for="name">{{__('Enter your custom domain')}}</label>
                            <input type="hidden" name="user_id" value="{{$user_domain_infos->id}}">
                            <input type="text" class="form-control" name="custom_domain"
                                   value="{{$custom_domain_info->custom_domain ?? ''}}">
                            <p id="subdomain-wrap"></p>
                        </div>

                        <div class="form-group">
                            <p class="font-weight-bold">
                                {{sprintf(__('Do not use http:// or https://
                                  The valid format will be exactly like this one - domain.tld, www.domain.tld or subdomain.domain.tld, www.subdomain.domain.tld'))}}

                            </p>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{__('Close')}}</button>
                        <button type="submit" class="btn btn-primary" id="login_button">{{__('Send Request')}}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <x-summernote.js/>

    <script>
        $(function () {
            $(document).ready(function ($) {
                "use strict";

                function removeTags(str) {
                    if ((str === null) || (str === '')) {
                        return false;
                    }
                    str = str.toString();
                    return str.replace(/(<([^>]+)>)/ig, '');
                }

                $(document).on('keyup paste change', 'input[name="custom_domain"]', function (e) {
                    var value = '';
                    if ($(this).val() != '') {
                        value = removeTags($(this).val()).toLowerCase().replace(/\s/g, "-");
                        $(this).val(value)
                    }

                    if (value.length < 1) {
                        $('#subdomain-wrap').html('');
                        return;
                    }

                    let msgWrap = $('#subdomain-wrap');
                    msgWrap.html('');
                    msgWrap.append('<span class="text-warning">{{__('availability checking..')}}</span>');

                    axios({
                        url: "{{route('tenant.admin.custom.domain.check')}}",
                        method: 'post',
                        responseType: 'json',
                        data: {
                            subdomain: value
                        }
                    }).then(function (res) {
                        res = res.data;
                        msgWrap.html('');
                        if (res.type == 'success')
                        {
                            msgWrap.append('<span class="text-success"> ' + res.msg + ' </span>');
                            $('#login_button').attr('disabled', false)
                        } else {
                            msgWrap.append('<span class="text-danger"> ' + res.msg + ' </span>');
                            $('#login_button').attr('disabled', true)
                        }
                    }).catch(function (error) {
                        var responseData = error.response.data.errors;
                        msgWrap.html('');
                        msgWrap.append('<span class="text-danger"> ' + responseData.subdomain + '</span>');
                        $('#login_button').attr('disabled', true)
                    });
                });
            });
        });
    </script>
@endsection

