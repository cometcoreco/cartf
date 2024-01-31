@extends('backend.admin-master')
@section('site-title')
    {{__('Country')}}
@endsection
@section('style')
    <x-datatable.css />
    <x-bulk-action.css />
@endsection
@section('content')
    <div class="col-lg-12 col-ml-12 padding-bottom-30">
        <div class="row">
            <div class="col-lg-12">
                <div class="margin-top-40">
                    <x-msg.error />
                    <x-msg.flash />
                </div>
                <a class="btn btn-info" href="{{ route("admin.mobile.slider.two.create") }}">Create</a>
            </div>
            <div class="col-lg-12 mt-2">
                <div class="card">
                    <div class="card-body">
                        <h4 class="header-title">{{__('List Mobile Slider')}}</h4>
                        <div class="table-wrap table-responsive">
                            <table class="table table-default">
                                <thead>
                                    <th>Sl NO</th>
                                    <th>Title</th>
                                    <th>Description</th>
                                    <th>image</th>
                                    <th>Button Text</th>
                                    <th>Button URL</th>
                                    <th>Action</th>
                                </thead>
                                <tbody>
                                    @foreach($mobileSliders as $slider)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $slider->title }}</td>
                                            <td>{{ $slider->description }}</td>
                                            <td style="width: 120px">
                                                {!! render_image_markup_by_attachment_id($slider->image_id) !!}
                                            </td>
                                            <td>{{ $slider->button_text }}</td>
                                            <td>{{ $slider->url }}</td>
                                            <td>
                                                @can('state-delete')
                                                    <x-table.btn.swal.delete :route="route('admin.mobile.slider.two.delete', $slider->id)" />
                                                @endcan
                                                @can('state-edit')
                                                    <a
                                                       class="btn btn-primary btn-xs mb-3 mr-1"
                                                       href="{{ route("admin.mobile.slider.two.edit",$slider->id) }}"
                                                    >
                                                        <i class="ti-pencil"></i>
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
@section('script')
    <x-media.js />
    <x-table.btn.swal.js />
@endsection
