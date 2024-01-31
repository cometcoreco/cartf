@extends('tenant.frontend.frontend-page-master')

@section('title')
    {{__('User OTP Verification')}}
@endsection

@section('page-title')
    {{__('Verify OTP')}}
@endsection

@section('style')
    <style>
        .active:hover{
            color: var(--main-color-one);
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
                        <h4 class="title signin-contents-title text-center mb-4">{{__('Verify OTP')}}</h4>

                        <h5 class="countdown text-center my-2"></h5>
                        <p class="my-2 text-center">{{__('An OTP has been sent on your phone number.')}}</p>
                        <div class="form-wrapper custom--form mt-4">
                            <x-error-msg/>
                            <x-flash-msg/>

                            <form action="{{route(route_prefix().'user.login.otp.verification')}}" method="post" enctype="multipart/form-data" class="account-form" id="login_form_order_page">
                               @csrf
                                <div class="error-wrap"></div>

                                <div class="form-group single-input" style="z-index: unset">
                                    <label for="exampleInputEmail1" class="label-title mb-3">{{__('OTP Code')}} <x-fields.mandatory-indicator/></label>
                                    <input class="form--control" type="number" name="otp" value="{{old('otp')}}">
                                </div>

                                <div class="form-group single-input form-check mt-4">
                                    <div class="box-wrap">
                                        <div class="left">
                                            <div class="checkbox-inlines">
                                                <input type="checkbox" name="remember" class="form-check-input check-input" id="exampleCheck1">
                                                <label class="form-check-label checkbox-label" for="exampleCheck1">{{__('Remember me')}}</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="btn-wrapper mt-4">
                                    <button type="submit" id="login_btn" class="cmn-btn cmn-btn-bg-1 w-100">{{__('Send OTP')}}</button>
                                </div>
                            </form>
                            <p class="info mt-3 d-flex justify-content-between">
                                <a href="{{route(route_prefix().'user.login.otp')}}" class="active"> {{__('Update number?')}} </a>
                                <a href="{{route(route_prefix().'user.login.otp.resend')}}" class="active"> {{__('Resend OTP code again?')}} </a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- sign-in area end -->
@endsection

@section('scripts')
    @php
        $expire_time = 0;
        if (!now()->isAfter($userOtp->expire_date))
        {
            $expire_time = $userOtp ? now()->diffInRealSeconds($userOtp->expire_date) : 0;
        }
    @endphp
    <script>
        let expire_time = `{{$expire_time}}`;

        let interval = setInterval(function() {
            if (expire_time > 0)
            {
                expire_time--;
            }

            let countdown = $('.countdown');
            if (parseInt(expire_time) === 0)
            {
                countdown.removeClass('text-dark').addClass('text-danger').text(`{{__('The OTP is expired')}}`)
                return clearInterval(interval);
            }

            countdown.addClass('text-dark').text(expire_time + ` {{__('Seconds')}}`)
        }, 1000);
    </script>
@endsection
