@extends(route_prefix().'admin.admin-master')

    @section('title') {{__('Add New User')}} @endsection
@section('style')
    <x-media-upload.css/>
@endsection
@section('content')
    <div class="col-12 stretch-card">
        <div class="card">
            <div class="card-body">
                <x-admin.header-wrapper>
                <x-slot name="left">
                <h4 class="card-title mb-4">{{__('Add New User')}}</h4>
                </x-slot>

                <x-slot name="right">
                    <a href="{{route('tenant.admin.user')}}" class="btn btn-info btn-sm">{{__('All Tenants')}}</a>
                </x-slot>

                </x-admin.header-wrapper>
                <x-error-msg/>
                <x-flash-msg/>

                <form class="forms-sample"  action="{{route('tenant.admin.user.new')}}" method="post">
                    @csrf
                    <x-fields.input type="text" name="name" class="form-control" placeholder="{{__('name')}}" label="{{__('Name')}}" value="{{old('name')}}"/>
                    <x-fields.input type="text" name="username" class="form-control" placeholder="{{__('username')}}" label="{{__('Username')}}" value="{{old('username')}}"/>
                    <x-fields.input type="email" name="email" class="form-control" placeholder="{{__('email')}}" label="{{__('Email')}}" value="{{old('email')}}"/>
                    <x-fields.input type="text" name="mobile" class="form-control" placeholder="{{__('mobile')}}" label="{{__('Mobile')}}" value="{{old('mobile')}}"/>

                    <x-fields.select title="Country" name="country" class="countryField" id="countryField">
                        <option value="">{{__('Select a country')}}</option>
                        @foreach($countries ?? [] as $country)
                            <option value="{{$country->id}}">{{$country->name}}</option>
                        @endforeach
                    </x-fields.select>

                    <x-fields.select title="State" name="state" class="stateField" id="stateField">
                        <option value="">{{__('Select a state')}}</option>
                        @foreach($states ?? [] as $country)
                            <option value="{{$country->id}}">{{$country->name}}</option>
                        @endforeach
                    </x-fields.select>

                    <x-fields.select title="City" name="city" class="cityField" id="cityField">
                        <option value="">{{__('Select a city')}}</option>
                        @foreach($cities ?? [] as $country)
                            <option value="{{$country->id}}">{{$country->name}}</option>
                        @endforeach
                    </x-fields.select>

                    <x-fields.input type="text" name="postal_code" class="form-control" placeholder="{{__('Postal code')}}" label="{{__('Postal Code')}}" value="{{old('postal_code')}}"/>

                    <x-fields.input type="text" name="company" class="form-control" placeholder="{{__('company')}}" label="{{__('Company')}}" value="{{old('company')}}"/>
                    <x-fields.input type="text" name="address" class="form-control" placeholder="{{__('address')}}" label="{{__('Address')}}" value="{{old('address')}}"/>

                    <x-fields.media-upload name="image" title="{{__('Image')}}" dimentions="{{__('120 X 120 px image recommended')}}"/>
                    <x-fields.input type="password" name="password" class="form-control"  label="{{__('Password')}}"/>
                    <x-fields.input type="password" name="password_confirmation" class="form-control"  label="{{__('Confirm Password')}}"/>

                    <button type="submit" class="btn btn-gradient-primary me-2 mt-5">{{__('Submit')}}</button>

                </form>


            </div>
        </div>
    </div>

    <x-media-upload.markup/>
@endsection

@section('scripts')
    <x-media-upload.js/>

    <script>
        $(document).ready(function () {
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
        });
    </script>
@endsection

