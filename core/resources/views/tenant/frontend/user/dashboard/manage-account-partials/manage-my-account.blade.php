@php
    $city_id = $user_details->city;
    $city_show = \Modules\CountryManage\Entities\City::where('id', $city_id)->select('id', 'name')->first();

    $state_id = $user_details->state;
    $state_show = \Modules\CountryManage\Entities\State::where('id', $state_id)->select('id', 'name')->first();

    $country_id = $user_details->country;
    $country_show = \Modules\CountryManage\Entities\Country::where('id', $country_id)->select('id', 'name')->first();
@endphp

<div class="seller-profile-details-wrapper">
    <div class="seller-profile-edit-flex">
        <h3 class="title-seller"> {{__('Profile Information')}} </h3>
    </div>
    <div class="dashboard-address-details margin-top-50">
        <ul class="details-list">
            <li class="lists">
                <span class="list-span"> {{__('Username:')}} </span>
                <span class="list-strong"> {{$user_details->username}} </span>
            </li>
            <li class="lists">
                <span class="list-span"> {{__('Name:')}} </span>
                <span class="list-strong"> {{$user_details->name}} </span>
            </li>
            <li class="lists">
                <span class="list-span"> {{__('Email:')}} </span>
                <span class="list-strong"> {{$user_details->email}}</span>
            </li>
            <li class="lists">
                <span class="list-span"> {{__('Phone:')}} </span>
                <span class="list-strong"> {{$user_details->mobile}} </span>
            </li>
            <li class="lists">
                <span class="list-span"> {{__('Company:')}} </span>
                <span class="list-strong"> {{$user_details->company}} </span>
            </li>
            <li class="lists">
                <span class="list-span"> {{__('City:')}} </span>
                <span class="list-strong"> {{$city_show->name ?? ''}} </span>
            </li>
            <li class="lists">
                <span class="list-span"> {{__('State:')}} </span>
                <span class="list-strong"> {{$state_show->name ?? ''}}</span>
            </li>
            <li class="lists">
                <span class="list-span"> {{__('Country:')}} </span>
                <span class="list-strong"> {{$country_show->name ?? ''}}</span>
            </li>
            <li class="lists">
                <span class="list-span"> {{__('Postal Code:')}} </span>
                <span class="list-strong"> {{$user_details->postal_code}}</span>
            </li>
        </ul>
        <ul class="details-list column-count-one">
            <li class="lists">
                <span class="list-span"> {{__('Address:')}} </span>
                <span class="list-strong">  {{$user_details->address}} </span>
            </li>
        </ul>
    </div>
</div>

@php
    $delivery_address = $user_details?->delivery_address;
@endphp
<div class="seller-profile-details-wrapper padding-top-80">
    <div class="seller-profile-edit-flex">
        <h3 class="title-seller"> {{__('Billing Information')}} </h3>
    </div>
    <div class="dashboard-address-details margin-top-50">
        <ul class="details-list">
            <li class="lists">
                <span class="list-span"> {{__('Name:')}} </span>
                <span class="list-strong"> {{$delivery_address?->full_name}} </span>
            </li>
            <li class="lists">
                <span class="list-span"> {{__('Phone:')}} </span>
                <span class="list-strong"> {{$delivery_address?->phone}} </span>
            </li>
            <li class="lists">
                <span class="list-span"> {{__('Email:')}} </span>
                <span class="list-strong"> {{$delivery_address?->email}} </span>
            </li>
            <li class="lists">
                <span class="list-span"> {{__('Postal Code:')}} </span>
                <span class="list-strong"> {{$delivery_address?->postal_code}} </span>
            </li>
            <li class="lists">
                <span class="list-span"> {{__('City:')}} </span>
                <span class="list-strong"> {{$delivery_address?->city_rel?->name}} </span>
            </li>
            <li class="lists">
                <span class="list-span"> {{__('State:')}} </span>
                <span class="list-strong"> {{$delivery_address?->state?->name}} </span>
            </li>
            <li class="lists">
                <span class="list-span"> {{__('Country:')}} </span>
                <span class="list-strong"> {{$delivery_address?->country?->name}} </span>
            </li>
        </ul>
        <ul class="details-list column-count-one">
            <li class="lists">
                <span class="list-span"> {{__('Address:')}} </span>
                <span class="list-strong"> {{$delivery_address?->address}} </span>
            </li>
        </ul>
    </div>
</div>
