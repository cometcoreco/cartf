@extends('tenant.admin.admin-master')
@section('title')
    {{__('Product Campaign')}}
@endsection
@section('style')
    <x-media-upload.css/>
    <x-datatable.css/>
    <x-bulk-action.css/>
    <link rel="stylesheet" href="{{global_asset('assets/tenant/frontend/css/nice-select.css')}}">
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
                        <h4 class="header-title">{{__('Add Mobile Campaign')}}</h4>
                        <form action="{{ route("tenant.admin.mobile.campaign.update") }}" method="post">
                            @csrf
                            <div class="form-group" id="product-list">
                                <label for="products">{{__('Select Campaign')}}</label>
                                <select id="products" name="campaign" class="form-control">
                                    <option value="">{{__('Select Campaign')}}</option>
                                    @foreach($campaigns as $item)
                                        <option
                                            value="{{ $item->id }}" {{ $item->id == optional($selectedCampaign)->campaign_id ?? '' ? "selected" : "" }}>{{ $item->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <button class="btn btn-info">{{__('Update Campaign')}}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <x-media-upload.markup/>
@endsection
@section('scripts')
    <x-media-upload.js/>
@endsection
