@extends('tenant.frontend.user.dashboard.user-master')
@section('title')
    {{__('Manage My Account')}}
@endsection

@section('section')
    <style>
        .media-upload-btn-wrapper .btn-info{
            color: #fff;
            width: 50%;
            border-radius: 0;
            background-color: var(--main-color-one);
            border-color: var(--main-color-one);
        }
        .media-upload-btn-wrapper .img-wrap{
            margin: 0;
        }

        .dashboard-profile-flex label[for=image]{
            font-weight: bold;
            color: #0b0b0b;
        }

        .nav-link{
            color: #0b0b0b;
        }
        .nav-link:hover {
            color: var(--main-color-one);
        }
    </style>

    <ul class="nav nav-pills mb-3" id="v-pills-tab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="v-pills-manage-tab" data-bs-toggle="pill"
                    data-bs-target="#v-pills-manage" type="button" role="tab" aria-controls="v-pills-manage"
                    aria-selected="true">{{__('Manage My Account')}}</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="v-pills-profile-tab" data-bs-toggle="pill" data-bs-target="#v-pills-profile"
                    type="button" role="tab" aria-controls="v-pills-profile"
                    aria-selected="false">{{__('My Profile')}}</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="v-pills-address-tab" data-bs-toggle="pill" data-bs-target="#v-pills-address"
                    type="button" role="tab" aria-controls="v-pills-address"
                    aria-selected="false">{{__('Address Book')}}</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="v-pills-password-tab" data-bs-toggle="pill" data-bs-target="#v-pills-password"
                    type="button" role="tab" aria-controls="v-pills-password" aria-selected="false">{{__('Change Password')}}
            </button>
        </li>
    </ul>
    <div class="tab-content mt-5" id="v-pills-tabContent">
        <div class="tab-pane fade show active" id="v-pills-manage" role="tabpanel" aria-labelledby="v-pills-manage-tab">
            @include('tenant.frontend.user.dashboard.manage-account-partials.manage-my-account')
        </div>
        <div class="tab-pane fade" id="v-pills-profile" role="tabpanel" aria-labelledby="v-pills-profile-tab">
            @include('tenant.frontend.user.dashboard.manage-account-partials.my-profile')
        </div>
        <div class="tab-pane fade" id="v-pills-address" role="tabpanel" aria-labelledby="v-pills-address-tab">
            @include('tenant.frontend.user.dashboard.manage-account-partials.address-book')
        </div>
        <div class="tab-pane fade" id="v-pills-password" role="tabpanel" aria-labelledby="v-pills-password-tab">
            @include('tenant.frontend.user.dashboard.manage-account-partials.change-password')
        </div>
    </div>

    <x-media-upload.markup/>
@endsection

@section('scripts')
    <x-media-upload.js/>
    <x-custom-js.phone-number-config selector="#phone" key="1"/>
    <x-custom-js.phone-number-config selector="#address_phone" key="2"/>

    <script>
        $(document).ready(() => {
            setTimeout(() => {
                $("#phone").val(`{{$user_details->mobile}}`);
                $("#address_phone").val(`{{$user_details?->user_delivery_address?->phone}}`);
            }, 1000);
        });
    </script>

    <script>
        $(function () {
            $(document).on('click', '.attachment-preview .user-thumb', function () {
                $('.media_upload_form_btn').trigger('click');
            });

            $(document).on('click', '.address-submit-btn', function (e) {
                e.preventDefault();
                let name = $('.address_form input[name=full_name]').val();
                let email = $('.address_form input[name=email]').val();
                let phone = $('.address_form input[name=phone]').val();
                let country = $('.address_form select[name=country]').val();
                let state = $('.address_form select[name=state]').val();
                let city = $('.address_form select[name=city]').val();
                let postal_code = $('.address_form input[name=postal_code]').val();
                let address = $('.address_form textarea[name=address]').val();

                $.ajax({
                    type: 'POST',
                    url: '{{route('tenant.user.home.address.update')}}',
                    data: {
                        _token: '{{csrf_token()}}',
                        name: name,
                        email: email,
                        phone: phone,
                        country: country,
                        state: state,
                        city: city,
                        postal_code: postal_code,
                        address: address
                    },
                    beforeSend: function () {
                        $('.loader').show();
                    },
                    success: function (data) {
                        if (data.msg) {
                            toastr.success(data.msg)
                            $('.loader').hide();
                        }
                    },
                    error: function (data) {
                        var response = JSON.parse(data.responseText);
                        $.each(response.errors, function (key, value) {
                            toastr.error(value);
                        });

                        $('.loader').hide();
                    }
                })
            });

            $(document).on('submit', 'form.profile-edit-form', function (e) {
                e.preventDefault();

                let form = new FormData(e.target);

                $.ajax({
                    url: '{{route('tenant.user.profile.update')}}',
                    type: 'POST',
                    processData: false,
                    contentType: false,
                    data: form,
                    beforeSend: function () {
                        $('.loader').show();
                    },
                    success: function (data) {
                        if (data.msg) {
                            toastr.success(data.msg);
                        }
                        $('.loader').hide();
                    },
                    error: function (data) {
                        var response = JSON.parse(data.responseText);
                        $.each(response.errors, function (key, value) {
                            toastr.error(value);
                        });

                        $('.loader').hide();
                    }
                })
            });

            $(document).on('submit', '.change_password_form', function (e){
                e.preventDefault();

                let formData = new FormData(e.target);

                $.ajax({
                    url: '{{route('tenant.user.password.change')}}',
                    type: 'POST',
                    processData: false,
                    contentType: false,
                    data: formData,
                    beforeSend: function (){
                        $('.loader').show();
                    },
                    success: function (data){
                        $('.loader').hide();

                        if (data.type === 'success')
                        {
                            toastr.success(data.msg)
                            toastr.warning('{{__('We\'re logging you out for the security reason and redirecting to login page')}}');

                            setTimeout(()=>{
                                location.href = data.url;
                            }, 3000)
                        } else {
                            toastr.error(data.msg);
                        }
                    },
                    error: function (data){
                        $('.loader').hide();

                        var response = JSON.parse(data.responseText);
                        $.each(response.errors, function (key, value) {
                            toastr.error(value);
                        });
                    }
                });
            });

            $(document).on('change', 'select[name=country]', function (e) {
                e.preventDefault();

                let country_id = $(this).val();

                $.post(`{{route('tenant.admin.au.state.all')}}`,
                    {
                        _token: `{{csrf_token()}}`,
                        country: country_id
                    },
                    function (data) {
                        let stateField = $('.stateField');
                        stateField.empty();
                        stateField.append(`<option value="">{{__('Select a state')}}</option>`);

                        let cityField = $('.cityField');
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
                        let cityField = $('.cityField');
                        cityField.empty();

                        $.each(data.cities , function (index, value) {
                            cityField.append(
                                `<option value="${value.id}">${value.name}</option>`
                            );
                        });
                    }
                )
            });
        })
    </script>
@endsection
