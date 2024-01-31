@extends('tenant.admin.admin-master')
@section('title')
    {{__('Sales Dashboard')}}
@endsection
@section('style')
    <x-datatable.css/>
    <x-bulk-action.css/>
@endsection
@section('content')
    <div class="col-lg-12 col-ml-12">
        <div class="row g-4">
            <div class="col-lg-12">
                <div class="margin-top-40">
                    <x-error-msg/>
                    <x-flash-msg/>
                </div>
            </div>
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <form action="{{route('tenant.admin.sales.settings')}}" method="POST">
                            @csrf

                                <div class="form-group">
                                    <label for="workday">{{__('Week Starting Day')}}</label>
                                    <select class="form-control mt-3" name="first_workday" id="workday">
                                        @foreach($daysOfWeek as $key => $day)
                                            <option value="{{$key}}" {{get_static_option('first_workday') == $key ? 'selected' : ''}}>{{__($day)}}</option>
                                        @endforeach
                                    </select>

                                <div class="form-group mt-3">
                                    <button class="btn btn-primary" type="submit">{{__('Update')}}</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script src="{{asset('landlord/admin/js/apexcharts.js')}}"></script>
@endsection
