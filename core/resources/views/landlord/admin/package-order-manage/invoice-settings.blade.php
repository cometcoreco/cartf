@extends(route_prefix().'admin.admin-master')
@section('title')
    {{__('Invoice Settings')}}
@endsection
@section('style')
    <x-media-upload.css/>
@endsection
@section('title')
    {{__('Invoice Settings')}}
@endsection
@section('content')
    <div class="col-lg-12 col-ml-12 padding-bottom-30">
        <div class="row">
            <div class="col-12">
                <x-flash-msg/>

                <div class="card">
                    <div class="card-body">
                        <div class="header-wrap d-flex justify-content-between">
                            <div class="left-wrapper">
                                <h4 class="header-title mb-4">{{__('Invoice Settings')}}</h4>
                            </div>
                        </div>

                        <div class="table-wrap table-responsive">
                            <form action="{{route('landlord.admin.invoice.settings')}}" method="POST">
                                @csrf

                                <div class="form-group">
                                    <label for="currency-fraction-code">{{__('Currency Fraction Code')}}</label>
                                    <input id="currency-fraction-code" type="text" class="form-control" name="currency_fraction_code" value="{{get_static_option('currency_fraction_code') ?? 'ct'}}">
                                    <p class="text-primary mt-2">{{__('Example, $100.5 - One Hundred USD and 5 Cent, 5 Cent is Fraction Here.')}}</p>
                                </div>

                                <button class="btn btn-primary" type="submit">{{__('Update')}}</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <x-media-upload.markup/>
@endsection

@section('scripts')

@endsection

