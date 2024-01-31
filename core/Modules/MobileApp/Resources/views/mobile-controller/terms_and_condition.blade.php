@extends('tenant.admin.admin-master')
@section('title')
    {{__('Terms and condition page')}}
@endsection
@section('style')
@endsection
@section('content')
    <div class="col-lg-12 col-ml-12 padding-bottom-30">
        <div class="row">
            <div class="col-lg-12">
                <div class="margin-top-40">
                    <x-flash-msg/>
                    <x-error-msg/>
                </div>
            </div>
            <div class="col-lg-12 mt-5">
                <div class="card">
                    <div class="card-body">
                        <h4 class="header-title">{{__('Update mobile terms and condition page')}}</h4>
                        <form action="{{ route("tenant.admin.mobile.settings.terms_and_condition") }}" method="post">
                            @csrf
                            <div class="form-group" id="product-list">
                                <label for="products">{{__('Select terms and condition page')}}</label>
                                <select id="products" name="page" class="form-control">
                                    <option value="">{{__('Select Page')}}</option>
                                    @foreach($pages as $item)
                                        <option value="{{ $item->slug }}" {{ get_static_option("mobile_terms_and_condition") == $item->slug ? "selected" : "" }}>{{ $item->title }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <button class="btn btn-info">{{__('Update Page')}}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
@endsection
