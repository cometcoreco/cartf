@extends('tenant.frontend.frontend-page-master')

@section('title')
    {{__('User OTP Login')}}
@endsection

@section('page-title')
    {{__('User OTP Login')}}
@endsection

@section('style')
    <style>
        #telephone.error{
            border-color: var(--main-color-one);
        }
        #telephone.success{
            border-color: var(--main-color-three);
        }
        .single-input .iti {
            width: 100%;
        }
    </style>
@endsection

@section('content')
    <!-- sign-in area start -->
    <div class="sign-in-area-wrapper padding-top-100 padding-bottom-100">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-9 col-lg-7 col-xl-6 col-xxl-5">
                    <div class="sign-in register signIn-signUp-wrapper">
                        <h4 class="title signin-contents-title">{{__('OTP Sign In')}}</h4>
                        <div class="form-wrapper custom--form mt-5">
                            <x-error-msg/>
                            <x-flash-msg/>

                            <form action="{{route(route_prefix().'user.login.otp')}}" method="post" enctype="multipart/form-data" class="account-form" id="login_form_order_page">
                               @csrf
                                <div class="error-wrap"></div>

                                <div class="form-group single-input" style="z-index: unset">
                                    <label for="exampleInputEmail1" class="label-title mb-3">{{__('Phone Number')}} <x-fields.mandatory-indicator/></label>
                                    <input class="form--control" type="tel" name="phone" placeholder="" id="telephone" value="{{old('phone')}}">
                                </div>

                                <div class="btn-wrapper mt-4">
                                    <button type="submit" id="login_btn" class="cmn-btn cmn-btn-bg-1 w-100">{{__('Send OTP')}}</button>
                                </div>
                            </form>
                            <p class="info mt-3">{{__("Do not have an account")}} <a href="{{route(route_prefix().'user.login')}}" class="active"> <strong>{{__('Sign In')}}</strong> </a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- sign-in area end -->
@endsection
@section('scripts')
    <x-custom-js.phone-number-config selector="#telephone" submit-button-id="login_btn"/>
@endsection
