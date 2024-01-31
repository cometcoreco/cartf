@extends('tenant.frontend.frontend-page-master')

@section('title')
    {{__('User Register')}}
@endsection

@section('style')
    <style>
        .toggle-password {
            position: absolute;
            bottom: 13px;
            right: 20px;
            cursor: pointer;
        }
        .generate-password:hover{
            color: var(--main-color-one);
        }
        .single-input{
            position: relative;
            z-index: 1;
            display: inline-block;
        }
        .toggle-password.show-pass .show-icon {
            display: none;
        }
        .toggle-password.show-pass .hide-icon {
            display: block;
        }
        .hide-icon {
            display: none;
        }
    </style>
@endsection

@section('page-title')
    {{__('User Register')}}
@endsection

@section('content')
    <div class="sign-in-area-wrapper" data-padding-top="50" data-padding-bottom="50">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8 col-lg-8">
                    <div class="sign-up register">
                        <h4 class="title">{{__('Sign Up')}}</h4>
                        <div class="form-wrapper mt-5">
                            <x-error-msg/>
                            <x-flash-msg/>
                            <form action="{{route('tenant.user.register.store')}}" method="post"
                                  enctype="multipart/form-data" class="contact-page-form style-01">
                                @csrf
                                <div class="row">
                                    <div class="col-md-12 col-lg-12">
                                        <div class="form-group">
                                            <label for="exampleInputEmail1">{{__('Name')}} <x-fields.mandatory-indicator/></label>
                                            <input type="text" class="form-control" name="name" id="exampleInputEmail1"
                                                   placeholder="{{__('Type your full name')}}" value="{{old('name')}}">
                                        </div>
                                    </div>

                                    <div class="col-md-12 col-lg-12 mt-4">
                                        <div class="form-group">
                                            <label for="exampleInputEmail1">{{__('Username')}} <x-fields.mandatory-indicator/></label>
                                            <input type="text" class="form-control" name="username"
                                                   id="exampleInputEmail1"
                                                   placeholder="{{__('Type your username')}}"
                                                   value="{{old('username')}}">
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group mt-4">
                                    <label for="exampleInputEmail1">{{__('Email Address')}} <x-fields.mandatory-indicator/></label>
                                    <input type="email" name="email" class="form-control" id="exampleInputEmail1"
                                           placeholder="{{__('Type your email')}}">
                                </div>

                                <div class="form-group single-input mt-2">
                                    <label for="phone_number">{{__('Phone Number')}} <x-fields.mandatory-indicator/></label>
                                    <input type="tel" name="phone" class="form-control" id="phone_number"
                                           placeholder="{{__('Type your phone')}}">
                                </div>

                                <div class="form-group">
                                    <label for="countryField">{{__('Country')}}
                                        <x-fields.mandatory-indicator/>
                                    </label>
                                    <select class="form-control" name="country" id="countryField">
                                        <option value="">{{__('Select a country')}}</option>
                                        @foreach($countries ?? [] as $country)
                                            <option value="{{$country->id}}">{{$country->name}}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="stateField">{{__('State')}}</label>
                                    <select class="form-control" name="state" id="stateField">
                                        <option value="">{{__('Select a state')}}</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="cityField">{{__('City')}}</label>
                                    <select class="form-control" name="city" id="cityField">
                                        <option value="">{{__('Select a city')}}</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="exampleInputEmail1">{{__('Post Code')}}</label>
                                    <input type="text" class="form-control" name="postal_code" id="exampleInputEmail1"
                                           placeholder="{{__('Type your postal code')}}" value="{{old('postal_code')}}">
                                </div>

                                <div class="row">
                                    <div class="col-md-12 col-lg-6">
                                        <div class="form-group single-input">
                                            <label for="exampleInputEmail1">{{__('Password')}} <x-fields.mandatory-indicator/></label>
                                            <input type="password" name="password" class="form-control"
                                                   id="exampleInputPassword1"
                                                   placeholder="{{__('Password')}}">
                                            <div class="icon toggle-password">
                                                <div class="show-icon"><i class="las la-eye-slash"></i></div>
                                                <span class="hide-icon"> <i class="las la-eye"></i> </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12 col-lg-6">
                                        <div class="form-group single-input">
                                            <label for="exampleInputEmail1">{{__('Confirmed Password')}} <x-fields.mandatory-indicator/></label>
                                            <input type="password" name="password_confirmation" class="form-control"
                                                   id="exampleInputPassword1"
                                                   placeholder="{{__('Confirmed Password')}}">
                                            <div class="icon toggle-password">
                                                <div class="show-icon"><i class="las la-eye-slash"></i></div>
                                                <span class="hide-icon"> <i class="las la-eye"></i> </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="input-item mt-2">
                                    <a class="generate-password" href="javascript:void(0)"><i class="las la-magic"></i> {{__('Generate random password')}}</a>
                                </div>

                                <div class="btn-wrapper mt-4">
                                    <button type="submit" class="btn-default rounded-btn">{{__('sign up')}}</button>
                                </div>
                            </form>
                            <p class="info">{{__('Already have an Account?')}} <a href="{{route('tenant.user.login')}}"
                                                                                  class="active">{{__('Sign in')}}</a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
@section('scripts')
    <x-custom-js.generate-password/>
    <x-custom-js.phone-number-config selector="#phone_number"/>

    <script>
        (function ($) {
            "use strict";
            $(document).ready(function () {
                <x-btn.custom :id="'register'" :title="__('Please Wait..')"/>

                    $(document).on('change', 'select[name=country]', function (e) {
                        e.preventDefault();

                        let country_id = $(this).val();

                        $.post(`{{route('tenant.admin.au.state.all')}}`,
                            {
                                _token: `{{csrf_token()}}`,
                                country: country_id
                            },
                            function (data) {
                                let stateField = $('#stateField');
                                stateField.empty();
                                stateField.append(`<option value="">{{__('Select a state')}}</option>`);

                                let cityField = $('#cityField');
                                cityField.empty();
                                cityField.append(`<option value="">{{__('Select a city')}}</option>`);

                                $.each(data.states , function (index, value) {
                                    stateField.append(
                                        `<option value="${value.id}">${value.name}</option>`
                                    );
                                });
                            }
                        )
                    });

                    $(document).on('change', 'select[name=state]', function (e) {
                    e.preventDefault();

                    let state_id = $(this).val();

                    $.post(`{{route('tenant.admin.au.city.all')}}`,
                        {
                            _token: `{{csrf_token()}}`,
                            state: state_id
                        },
                        function (data) {
                            let cityField = $('#cityField');
                            cityField.empty();

                            $.each(data.cities , function (index, value) {
                                cityField.append(
                                    `<option value="${value.id}">${value.name}</option>`
                                );
                            });
                        }
                    )
                });

                $(document).on('click', '.generate-password', function () {
                    let password = generateRandomPassword();

                    $('input[name=password]').val(password);
                    $('input[name=password_confirmation]').val(password);
                });
            });
        })(jQuery);
    </script>
@endsection
