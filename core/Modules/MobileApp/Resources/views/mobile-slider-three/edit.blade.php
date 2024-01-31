@extends('backend.admin-master')
@section('site-title')
    {{__('Country')}}
@endsection
@section('style')
    <x-media.css />
    <x-datatable.css />
    <x-bulk-action.css />
@endsection
@section('content')
    <div class="col-lg-12 col-ml-12 padding-bottom-30">
        <div class="row">
            <div class="col-lg-12">
                <div class="margin-top-40">
                    <x-msg.error/>
                    <x-msg.flash/>
                </div>
                <a class="btn btn-info" href="{{ route("admin.mobile.slider.two.all") }}">List</a>
            </div>
            <div class="col-lg-12 mt-5">
                <div class="card">
                    <div class="card-body">
                        <h4 class="header-title">{{__('Add new mobile slider')}}</h4>
                        <form action="{{ route("admin.mobile.slider.two.edit", $mobileSlider->id ) }}" method="post">
                            @csrf
                            <div class="form-group">
                                <label for="title">Title</label>
                                <input class="form-control" id="title" name="title"
                                       placeholder="Mobile Slider Title..." value="{{ $mobileSlider->title }}"/>
                            </div>
                            <div class="form-group">
                                <label for="description">Description</label>
                                <textarea class="form-control" id="description" name="description"
                                          placeholder="Mobile Slider Description..."> {{ $mobileSlider->description }}</textarea>
                            </div>

                            <x-media-upload :title="__('Image')" :name="'image'" :dimentions="'1280x1280'" id="{{ $mobileSlider->image_id }}"/>

                            <div class="form-group">
                                <label for="button_text">Button Text</label>
                                <input class="form-control" id="button_text" name="button_text"
                                       placeholder="Mobile Slider Button Text..."
                                       value="{{ $mobileSlider->button_text }}"/>
                            </div>

                            <div class="form-group">
                                <label for="button_url">Button URL</label>
                                <input class="form-control" id="button_url" name="button_url"
                                       placeholder="Mobile Slider Button URL..."
                                       value="{{ $mobileSlider->url }}"/>
                            </div>

                            <div class="form-group">
                                <label for="category">Enable Category</label>
                                <input type="checkbox" id="category" name="category_type" {{ !empty($mobileSlider->category) ? "checked" : ""  }}/>
                            </div>

                            <div class="form-group" id="campaign-list" style="{{ !empty($mobileSlider->category) ? "display: none" : ""  }}">
                                <label for="campaigns">Select Campaign</label>
                                <select id="campaigns" name="campaign" class="form-control nice-select wide">
                                    <option value="">Select Campaign</option>
                                    @foreach($campaigns as $campaign)
                                        <option {{ $mobileSlider->campaign == $campaign->id ? "selected" : "" }} value="{{ $campaign->id }}">{{ $campaign->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group" id="category-list" style="{{ !empty($mobileSlider->category) ? "" : "display: none"  }}">
                                <label for="products">Select Category</label>
                                <select id="products" name="category" class="form-control">
                                    <option value="">Select Category</option>
                                    @foreach($categories as $category)
                                        <option {{ $mobileSlider->category == $category->id ? "selected" : "" }}  value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <button class="btn btn-info">Update</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        </div>
    </div>
    <x-media.markup />
@endsection
@section('script')
    <x-media.js />
    <script>
        $("#category").on("change",function (){
            if($(this).is(":checked")){
                $("#campaign-list").fadeOut();
                setTimeout(function (){
                    $("#category-list").fadeIn();
                },400);
            }else{
                $("#category-list").fadeOut();
                setTimeout(function (){
                    $("#campaign-list").fadeIn();
                }, 400);
            }
        });
    </script>
@endsection
