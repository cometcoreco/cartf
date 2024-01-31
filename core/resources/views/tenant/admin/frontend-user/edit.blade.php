@extends(route_prefix().'admin.admin-master')

    @section('title') {{__('Edit User')}} @endsection
@section('style')
    <x-media-upload.css/>
@endsection
@section('content')
    <div class="col-12 stretch-card">
        <div class="card">
            <div class="card-body">
                <x-admin.header-wrapper>

                <x-slot name="left">
                     <h4 class="card-title mb-4">{{__('Edit User')}}</h4>
                </x-slot>

                <x-slot name="right">
                    <a href="{{route('tenant.admin.user')}}" class="btn btn-info btn-sm">{{__('All Users')}}</a>
                </x-slot>

                </x-admin.header-wrapper>
                    <x-error-msg/>
                    <x-flash-msg/>

                <form action="{{route('tenant.admin.user.update.profile')}}" method="post">
                    @csrf
                    <input type="hidden" name="id" value="{{$user->id}}">
                    <x-fields.input type="text" name="name" value="{{$user->name}}" label="{{__('Name')}}"/>
                    <x-fields.input type="text" name="username" value="{{$user->username}}" label="{{__('Username')}}"/>
                    <x-fields.input type="email" name="email" value="{{$user->email}}" label="{{__('Email')}}"/>
                    <x-fields.input type="text" name="mobile" value="{{$user->mobile}}" label="{{__('Mobile')}}"/>

                    <x-fields.select title="Country" name="country" class="countryField" id="countryField">
                        <option value="">{{__('Select a country')}}</option>
                        @foreach($countries ?? [] as $country)
                            <option @selected($country->id == $user->country) value="{{$country->id}}">{{$country->name}}</option>
                        @endforeach
                    </x-fields.select>

                    <x-fields.select title="State" name="state" class="stateField" id="stateField">
                        <option value="">{{__('Select a state')}}</option>
                        @foreach($states ?? [] as $country)
                            <option @selected($country->id == $user->state) value="{{$country->id}}">{{$country->name}}</option>
                        @endforeach
                    </x-fields.select>

                    <x-fields.select title="City" name="city" class="cityField" id="cityField">
                        <option value="">{{__('Select a city')}}</option>
                        @foreach($cities ?? [] as $country)
                            <option @selected($country->id == $user->city) value="{{$country->id}}">{{$country->name}}</option>
                        @endforeach
                    </x-fields.select>

                    <x-fields.input type="text" name="postal_code" value="{{$user->postal_code}}" label="{{__('Postal Code')}}"/>

                    <x-fields.input type="text" name="company" value="{{$user->company}}" label="{{__('Company')}}"/>
                    <x-fields.input type="text" name="address" value="{{$user->address}}"  label="{{__('Address')}}"/>

                    <x-fields.media-upload name="image" title="{{__('Image')}}" id="{{$user->image}}" value="{{$user->image}}" dimentions="{{__('120 X 120 px image recommended')}}"/>


                    <button type="submit" class="btn btn-gradient-primary me-2 mt-5">{{__('Update')}}</button>

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

