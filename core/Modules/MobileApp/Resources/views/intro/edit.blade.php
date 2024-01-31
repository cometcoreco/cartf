@extends('tenant.admin.admin-master')
@section('title')
    {{__('Mobile Intro Edit')}}
@endsection
@section('style')
    <x-media-upload.css/>
    <x-datatable.css />
    <x-bulk-action.css />
@endsection
@section('content')
    <div class="col-lg-12 col-ml-12 padding-bottom-30">
        <div class="row">
            <div class="col-lg-12">
                <div class="">
                    <x-flash-msg/>
                    <x-error-msg/>
                </div>

                <div class="text-end">
                    <a class="btn btn-info" href="{{ route("tenant.admin.mobile.intro.all") }}">{{__("List")}}</a>
                </div>
            </div>
            <div class="col-lg-12 mt-4">
                <div class="card">
                    <div class="card-body">
                        <h4 class="header-title">{{__('Add new Mobile intro')}}</h4>
                        <form action="{{ route("tenant.admin.mobile.intro.edit", $mobileIntro->id) }}" method="post">
                            @csrf
                            <div class="form-group">
                                <label for="title">{{__("Title")}}</label>
                                <input class="form-control" id="title" name="title"
                                       placeholder="{{ __("Mobile intro Title...") }}" value="{{ $mobileIntro->title }}"/>
                            </div>
                            <div class="form-group">
                                <label for="description">{{__("Description")}}</label>
                                <textarea class="form-control" id="description" name="description"
                                          placeholder="{{ __("Mobile intro Description...") }}" rows="10">{{ $mobileIntro->description }}</textarea>
                            </div>

                            <x-fields.media-upload :title="__('Image')" :name="'image_id'" :id="$mobileIntro->image_id" :dimentions="'1280x1280'"/>

                            <div class="form-group">
                                <button class="btn btn-info">{{  __("Update") }}</button>
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
