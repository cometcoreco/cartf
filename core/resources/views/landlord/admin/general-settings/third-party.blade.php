@extends(route_prefix().'admin.admin-master')
@section('title') {{__('Third Party Scripts Settings')}} @endsection

@section('content')
    <div class="col-lg-12 col-ml-12">
        <div class="row">
            <div class="col-12">
                <x-error-msg/>
                <x-flash-msg/>
                <div class="card">
                    <div class="card-body">
                        <h4 class="header-title mb-4">{{__("Third Party Scripts Settings")}}</h4>
                        <form action="{{route(route_prefix().'admin.general.third.party.script.settings')}}" method="POST" enctype="multipart/form-data">
                            @csrf

                            <div class="form-group">
                                <label for="site_third_party_tracking_code">{{__('Third Party Api Code')}}</label>
                                <textarea name="site_third_party_tracking_code" id="site_third_party_tracking_code" cols="30" rows="10" class="form-control">{{get_static_option('site_third_party_tracking_code')}}</textarea>
                                <p>{{__('this code will be load before </head> tag')}}</p>
                            </div>

                            <button type="submit" class="btn btn-primary mt-4 pr-4 pl-4">{{__('Update Changes')}}</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
