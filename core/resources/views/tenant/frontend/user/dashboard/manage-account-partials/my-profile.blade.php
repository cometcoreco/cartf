<form action="#" class="profile-edit-form">
    <div class="seller-profile-details-wrapper">
        <h3 class="title-seller"> {{__('Edit Profile')}} </h3>
        <div class="dashboard-profile-flex">
            <div class="thumbs margin-top-40">
                <x-fields.media-upload :name="'image'" :title="'Image'" :id="$user_details->image"/>
            </div>
        </div>
        @csrf
        <div class="row margin-top-10">
            <div class="col-lg-12 col-md-12 margin-top-30">
                <div class="dashboard-address-details">
                    <h5 class="edit-title"> {{__('Profile Information')}} </h5>
                    <div class="single-dashboard-input">
                        <div class="single-info-input margin-top-30">
                            <label class="info-title"> {{__('Your Name')}}<span class="text-danger">*</span> </label>
                            <input class="form--control" type="text" name="name" placeholder="{{__('Type Your Name')}}" value="{{$user_details->name}}">
                        </div>
                        <div class="single-info-input margin-top-30">
                            <label class="info-title"> {{__('Your Email')}}<span class="text-danger">*</span> </label>
                            <input class="form--control" type="email" name="email" placeholder="{{__('Type Your Email')}}" value="{{$user_details->email}}">
                        </div>
                    </div>
                    <div class="single-dashboard-input">
                        <div class="single-info-input single-input margin-top-30">
                            <label class="info-title"> {{__('Phone Number')}} </label>
                            <input class="form--control" id="phone" type="text" name="phone" placeholder="{{__('Type Your Number')}}" value="{{$user_details->mobile}}">
                        </div>

                        <div class="single-info-input margin-top-30">
                            <label class="info-title"> {{__('Company')}} </label>
                            <input class="form--control" type="text" name="company" placeholder="{{__('Type Your Number')}}" value="{{$user_details->company}}">
                        </div>
                    </div>
                    <div class="single-dashboard-input">
                        <div class="single-info-input margin-top-30">
                            <label class="info-title"> {{__('Your Country')}}<x-fields.mandatory-indicator/></label>
                            <select class="form--control" name="country" id="countryField">
                                <option value="">{{__('Select a country')}}</option>
                                @foreach($countries ?? [] as $country)
                                    <option @selected($country->id == $user_details->country) value="{{$country->id}}">{{$country->name}}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="single-info-input margin-top-30">
                            <label class="info-title"> {{__('Your State')}} </label>
                            <select class="form--control stateField" name="state" id="stateField">
                                <option value="">{{__('Select a state')}}</option>
                                @foreach($states ?? [] as $country)
                                    <option @selected($country->id == $user_details->state) value="{{$country->id}}">{{$country->name}}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="single-info-input margin-top-30">
                            <label class="info-title"> {{__('Your City')}} </label>
                            <select class="form--control cityField" name="city" id="cityField">
                                <option value="">{{__('Select a city')}}</option>
                                @foreach($cities ?? [] as $country)
                                    <option @selected($country->id == $user_details->city) value="{{$country->id}}">{{$country->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="single-dashboard-input">
                        <div class="single-info-input margin-top-30">
                            <label class="info-title"> {{__('Postal Code')}} </label>
                            <input class="form--control" type="text" name="postal_code" placeholder="{{__('Type your postal code')}}" value="{{$user_details->postal_code}}">
                        </div>
                    </div>
                    <div class="single-dashboard-input">
                        <div class="single-info-input margin-top-30">
                            <label class="info-title"> {{__('Your Address')}} </label>
                            <textarea class="form--control" name="address" id="address" cols="30" rows="10"> {{$user_details->address}} </textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-12">
                <div class="btn-wrapper margin-top-10">
                    <button type="submit"
                            class="btn-submit btn-bg-1 profile-submit-btn"> {{__('Save Changes')}} </button>
                </div>
            </div>
        </div>
    </div>
</form>
