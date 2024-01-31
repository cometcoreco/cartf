@extends('tenant.admin.admin-master')
@section('title')
    {{__('Mobile Intro')}}
@endsection
@section('style')
    <x-datatable.css />
    <x-bulk-action.css />

    <style>
        .image{
            width: 100%;
        }
    </style>
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
                    <a class="btn btn-info" href="{{ route("tenant.admin.mobile.intro.create") }}">{{ __("Create") }}</a>
                </div>
            </div>
            <div class="col-lg-12 mt-4">
                <div class="card">
                    <div class="card-body">
                        <h4 class="header-title">{{__('List Mobile Slider')}}</h4>
                        <div class="table-wrap table-responsive">
                            <table class="table table-default">
                                <thead>
                                    <th>{{ __("Sl NO") }}</th>
                                    <th>{{ __("Title") }}</th>
                                    <th>{{ __("Description") }}</th>
                                    <th>{{ __("image") }}</th>
                                    <th>{{ __("Action") }}</th>
                                </thead>
                                <tbody>
                                    @forelse($mobileIntros as $slider)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $slider->title }}</td>
                                            <td>{{ Str::words($slider->description, 8) }}</td>
                                            <td style="width: 120px">
                                                {!! render_image_markup_by_attachment_id($slider->image_id, 'image') !!}
                                            </td>
                                            <td>
                                                <x-table.btn.swal.delete :route="route('tenant.admin.mobile.intro.delete', $slider->id)" />

                                                <a class="btn btn-primary btn-sm btn-xs mb-3 mr-1" href="{{ route("tenant.admin.mobile.intro.edit",$slider->id) }}" >
                                                    <i class="mdi mdi-pencil"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td class="text-center text-warning" colspan="5">{{__('No Data Available')}}</td>
                                        </tr>
                                    @endforelse
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
    <x-media-upload.js/>
    <x-table.btn.swal.js />
@endsection
