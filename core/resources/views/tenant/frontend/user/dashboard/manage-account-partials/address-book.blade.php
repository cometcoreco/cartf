@php
    $user_delivery_address = $user_details?->delivery_address;

    $shipping_states = \Modules\CountryManage\Entities\State::where(['status' => 'publish' ,'country_id' => $user_delivery_address->country_id ?? ''])->get();
    $shipping_cities = \Modules\CountryManage\Entities\City::where(['status' => 'publish' ,'state_id' => $user_delivery_address->state_id ?? ''])->get();
@endphp

<div class="seller-profile-details-wrapper">
    <h3 class="title-seller"> {{__('Edit Billing Address')}} </h3>
    <form action="#" class="address_form">
        <div class="row margin-top-10">
            <div class="col-lg-12 col-md-12 margin-top-30">
                <div class="dashboard-address-details">
                    <h5 class="edit-title"> {{__('Billing Information')}} </h5>
                    <div class="single-dashboard-input">
                        <div class="single-info-input margin-top-30">
                            <label class="info-title"> {{__('Your Name')}} <x-fields.mandatory-indicator/></label>
                            <input class="form--control" type="text" name="full_name" placeholder="{{__('Type Your Name')}}" value="{{$user_delivery_address?->full_name}}">
                        </div>
                        <div class="single-info-input margin-top-30">
                            <label class="info-title"> {{__('Your Email')}} <x-fields.mandatory-indicator/></label>
                            <input class="form--control" type="email" name="email" placeholder="{{__('Type Your Email')}}" value="{{$user_delivery_address?->email}}">
                        </div>
                    </div>
                    <div class="single-dashboard-input">
                        <div class="single-info-input single-input margin-top-30">
                            <label class="info-title"> {{__('Phone Number')}} <x-fields.mandatory-indicator/></label>
                            <input class="form--control" id="address_phone" type="text" placeholder="{{__('Type Your Number')}}" name="phone" value="{{$user_delivery_address?->phone}}">
                        </div>
                    </div>
                    <div class="single-dashboard-input">
                        <div class="single-info-input margin-top-30">
                            <label class="info-title"> {{__('Your Country')}} <x-fields.mandatory-indicator/></label>
                            <select class="form--control countryField" name="country" id="countryField">
                                <option value="">{{__('Select a country')}}</option>
                                @foreach($countries ?? [] as $country)
                                    <option @selected($country->id == ($user_delivery_address->country_id ?? '')) value="{{$country->id}}">{{$country->name}}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="single-info-input margin-top-30">
                            <label class="info-title"> {{__('Your State')}} <x-fields.mandatory-indicator/></label>
                            <select class="form--control stateField" name="state" id="stateField">
                                <option value="">{{__('Select a state')}}</option>
                                @foreach($shipping_states ?? [] as $country)
                                    <option @selected($country->id == ($user_delivery_address->state_id ?? '')) value="{{$country->id}}">{{$country->name}}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="single-info-input margin-top-30">
                            <label class="info-title"> {{__('Your City')}} <x-fields.mandatory-indicator/></label>
                            <select class="form--control cityField" name="city" id="cityField">
                                <option value="">{{__('Select a city')}}</option>
                                @foreach($shipping_cities ?? [] as $country)
                                    <option @selected($country->id == ($user_delivery_address->city ?? '')) value="{{$country->id}}">{{$country->name}}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="single-info-input margin-top-30">
                            <label class="info-title"> {{__('Your Postal Code')}} <x-fields.mandatory-indicator/></label>
                            <input class="form--control" type="text" placeholder="{{__('Type your postal code')}}" name="postal_code" value="{{$user_delivery_address->postal_code ?? ''}}">
                        </div>
                    </div>

                    <div class="single-dashboard-input">
                        <div class="single-info-input margin-top-30">
                            <label class="info-title"> {{__('Your Address')}} <x-fields.mandatory-indicator/></label>
                            <textarea class="form--control" id="address" cols="30" rows="10" name="address"> {{$user_delivery_address?->address}} </textarea>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-12">
                <div class="btn-wrapper margin-top-10">
                    <button type="submit" class="btn-submit btn-bg-1 address-submit-btn"> {{__('Save Changes')}} </button>
                </div>
            </div>
        </div>
    </form>
</div>
