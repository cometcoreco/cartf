@extends('tenant.admin.admin-master')
@section('title')
    {{__('Featured Product List')}}
@endsection
@section('style')
    <x-datatable.css />
    <x-bulk-action.css />

    <style>
        .slider_image{
            width: 150px;
        }
        .td_description{
            white-space: unset !important;
        }
    </style>
@endsection
@section('content')
    <div class="col-lg-12 col-ml-12 padding-bottom-30">
        <div class="row">
            <div class="col-lg-12">
                <div class="">
                    <x-error-msg/>
                    <x-flash-msg/>
                </div>
                <a class="btn btn-info" href="{{ route("tenant.admin.featured.product.create") }}">{{ __("Create") }}</a>
            </div>
            <div class="col-lg-12 mt-4">
                <div class="card">
                    <div class="card-body">
                        <h4 class="header-title">{{__('List Featured Product')}}</h4>
                        <div class="table-wrap table-responsive">
                            <table class="table table-default">
                                <thead>
                                    <th>{{ __("Sl NO") }}</th>
                                    <th>{{ __("Title") }}</th>
                                    <th>{{ __("Description") }}</th>
                                    <th>{{ __("image") }}</th>
                                    <th>{{ __("Button Text") }}</th>
                                    <th>{{ __("Button URL") }}</th>
                                    <th>{{ __("Action") }}</th>
                                </thead>
                                <tbody>
                                    @foreach($mobileFeaturedProducts as $slider)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $slider->title }}</td>
                                            <td class="td_description">{{ $slider->description }}</td>
                                            <td style="width: 120px">
                                                {!! render_image_markup_by_attachment_id($slider->image_id, 'slider_image', 'full') !!}
                                            </td>
                                            <td>{{ $slider->button_text }}</td>
                                            <td>{{ $slider->url }}</td>
                                            <td>
                                                @can('state-delete')
                                                    <x-table.btn.swal.delete :route="route('tenant.admin.mobile.slider.delete', $slider->id)" />
                                                @endcan
                                                @can('state-edit')
                                                    <a
                                                       class="btn btn-primary btn-sm btn-xs mb-3 mr-1"
                                                       href="{{ route("tenant.admin.mobile.slider.edit",$slider->id) }}"
                                                    >
                                                        <i class="mdi mdi-pencil"></i>
                                                    </a>
                                                @endcan
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <x-media-upload.js />
    <x-table.btn.swal.js />
@endsection
