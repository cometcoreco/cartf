@php
    $route_name ='landlord';
@endphp
@extends($route_name.'.admin.admin-master')

@section('title')
    {{__('Account Settings')}}
@endsection

@section('style')
    <link rel="stylesheet" href="{{global_asset('assets/common/css/select2.min.css')}}">
@endsection

@section('content')
    <div class="col-12 stretch-card">
        <div class="card">
            <div class="card-body">
                <x-admin.header-wrapper>
                <x-slot name="left">
                <h4 class="card-title mb-4"> {{__('Account Settings')}}</h4>
                </x-slot>

                <x-slot name="right">
                    <a href="{{route('landlord.admin.tenant')}}" class="btn btn-info btn-sm">{{__('All Tenants')}}</a>
                </x-slot>

                </x-admin.header-wrapper>
                <x-error-msg/>
                <x-flash-msg/>

                <form class="forms-sample"  action="{{route('landlord.admin.tenant.settings')}}" method="post">
                    @csrf

                    <x-fields.switcher class="auto-remove-switcher" label="Auto remove account" name="tenant_account_auto_remove" value="{{get_static_option('tenant_account_auto_remove')}}"/>

                    @php
                        $fields = [ 1 => __('One Day'), 2 => __('Two Day'), 3 => __('Three Day'), 4 => __('Four Day'), 5 => __('Five Day'), 6 => __('Six Day'), 7 => __('Seven Day'), 14 => __('Fourteen Days'), 30 => __('Thirty Days')];
                    @endphp

                    <div class="action-wrapper">
                        <div class="form-group mt-3">
                            <label for="site_logo">{{__('Select How many days earlier after expiration  account deleted mail alert will be send')}}</label>
                            <select name="tenant_account_delete_notify_mail_days[]" class="form-control expiration_dates" multiple="multiple">
                                @foreach($fields as $key => $field)
                                    @php
                                        $account_expiry = get_static_option('tenant_account_delete_notify_mail_days');
                                        $decoded = json_decode($account_expiry) ?? [];
                                    @endphp
                                    <option value="{{$key}}"
                                    @foreach($decoded as  $day)
                                        {{$day == $key ? 'selected' : ''}}
                                        @endforeach
                                    >{{__($field)}}</option>
                                @endforeach
                            </select>
                        </div>

                        <x-fields.input type="number" min="1" name="account_remove_day_within_expiration" class="form-control"
                                        value="{{get_static_option('account_remove_day_within_expiration')}}" label="{{__('Account will be removed after expiration this Day')}}"/>
                        <small>{{__('It will not remove accounts automatically. Admin have to delete the accounts manually')}} <p class="text-primary">{{__('This feature requires cron jobs')}}</p></small>
                    </div>

                    <button type="submit" class="btn btn-gradient-primary mt-4">{{__('Submit')}}</button>
                </form>


            </div>
        </div>
    </div>

@endsection

@section('scripts')
    <script src="{{global_asset('assets/common/js/select2.min.js')}}"></script>
    <script>
        $(document).ready(function() {
            $('.expiration_dates').select2();

            let auto_remove = `{{get_static_option('tenant_account_auto_remove')}}`;
            console.log(auto_remove);
            let wrapper = $('.action-wrapper');

            if (auto_remove === '')
            {
                wrapper.hide();
            }

            $(document).on('change', '.auto-remove-switcher', function (){
                wrapper.toggle();
            });
        });
    </script>
@endsection
