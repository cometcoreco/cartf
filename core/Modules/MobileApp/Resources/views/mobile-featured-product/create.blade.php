@extends('tenant.admin.admin-master')
@section('title')
    {{__('Featured Product')}}
@endsection
@section('style')
    <x-media-upload.css/>
    <x-datatable.css />
    <x-bulk-action.css />
    <link rel="stylesheet" href="{{global_asset('assets/landlord/admin/css/nice-select.css')}}">
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
                        <h4 class="header-title">{{__('Add New Featured Product')}}</h4>
                        <form action="{{ route("tenant.admin.featured.product.create") }}" method="post">
                            @csrf
                            <div class="form-group mt-4">
                                <label for="category" style="display: inline">{{__('Enable Category')}}</label>
                                <input type="checkbox" id="category" name="category" {{ optional($selectedProduct)->type == "category" ? "checked" : "" }}/>
                            </div>

                            <div class="form-group" id="product-list" {{ optional($selectedProduct)->type == "category" ? "style=display:none" : "" }}>
                                <label for="products">{{__('Select Product')}}</label>
                                <select id="products" name="featured_product[]" multiple class="form-control nice-select wide">
                                    <option value="">{{__('Select Product')}}</option>
                                    @foreach($products as $product)
                                        <option {{ in_array($product->id,json_decode(optional($selectedProduct)->ids) ?? []) ? "selected" : "" }} value="{{ $product->id }}">{{ $product->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group"  {{ optional($selectedProduct)->type == "category" ? "" : "style=display:none" }} id="category-list">
                                <label for="products">{{__('Select Category')}}</label>
                                <select id="products" name="featured_category[]" class="form-control">
                                    <option value="">{{__('Select Category')}}</option>
                                    @foreach($categories as $category)
                                        <option {{ in_array($category->id,json_decode(optional($selectedProduct)->ids) ?? []) ? "selected" : "" }} value="{{ $category->id }}">
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <button class="btn btn-info">{{__('Update Featured Product')}}</button>
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
    <script src="{{global_asset('assets/landlord/admin/js/jquery.nice-select.min.js')}}"></script>

    <script>
        let nice_select = $('.nice-select');
        if (nice_select.length) {
            nice_select.niceSelect();
        }

        $("#category").on("change",function (){
            if($(this).is(":checked")){
                $("#product-list").fadeOut();
                setTimeout(function (){
                    $("#category-list").fadeIn();
                },200);
            }else{
                $("#category-list").fadeOut();
                setTimeout(function (){
                    $("#product-list").fadeIn();
                }, 200);
            }
        });
    </script>
@endsection
