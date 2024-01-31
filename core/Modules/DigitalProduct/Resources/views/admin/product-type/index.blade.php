@extends('tenant.admin.admin-master')
@section('title')
    {{ __('Digital Product Type') }}
@endsection

@section('style')
    <link rel="stylesheet" href="{{global_asset('assets/common/css/select2.min.css')}}">
    <x-media-upload.css/>

    <style>
        .select2-container {
            display: block;
            width: 100%;
            z-index: 1055;
        }

        .select2-container--default .select2-selection--single {
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 4px;
            width: 100%;
            height: 50px;
            line-height: 50px;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: #444;
            line-height: 50px;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 50px;
            position: absolute;
            top: 1px;
            right: 1px;
            width: 20px;
            line-height: 50px;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow b {
            border-color: #ddd transparent transparent transparent;
            top: 25px;
        }
        .attachment-preview .img-wrap img{
            width: 150px;
        }
    </style>
@endsection

@section('content')
    <div class="dashboard-recent-order">
        <div class="row">
            <div class="col-lg-12 col-ml-12">
                <x-error-msg/>
                <x-flash-msg/>
                <div class="row g-4">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="header-wrap d-flex flex-wrap justify-content-between">
                                    <h4 class="header-title mb-4">{{__('Digital Product Types')}}</h4>
                                    <div class="div">
                                        @can('product-category-create')
                                            <a href="#"
                                               data-bs-toggle="modal"
                                               data-bs-target="#category_create_modal"
                                               class="btn btn-sm btn-info mb-3 mr-1 text-light">{{__('New Product Type')}}</a>
                                        @endcan
                                    </div>
                                </div>
                                @can('product-category-delete')
                                    <x-bulk-action.dropdown/>
                                @endcan

                                <div class="table-wrap table-responsive">
                                    <table class="table table-default">
                                        <thead>
                                        <x-bulk-action.th/>
                                        <th>{{__('ID')}}</th>
                                        <th>{{__('Name')}}</th>
                                        <th>{{__('Type')}}</th>
                                        <th>{{__('Extensions')}}</th>
                                        <th>{{__('Image')}}</th>
                                        <th>{{__('Status')}}</th>
                                        <th>{{__('Action')}}</th>
                                        </thead>
                                        <tbody>

                                        @foreach($digital_product_types as $item)
                                            <tr>
                                                <x-bulk-action.td :id="$item->id"/>
                                                <td>{{$item->id}}</td>
                                                <td>{{$item->name}}</td>
                                                <td>{{\App\Enums\DigitalProductTypeEnums::getText($item->product_type)}}</td>
                                                <td>{{str_replace(['[',']','"'], ' ', $item->extensions)}}</td>
                                                <td>
                                                    <div class="attachment-preview">
                                                        <div class="img-wrap">
                                                            {!! render_image_markup_by_attachment_id($item->image_id ?? '') !!}
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>{{\App\Enums\StatusEnums::getText($item->status)}}</td>
                                                <td>
                                                    <x-status-span
                                                        :status="\App\Enums\StatusEnums::getText($item->status)"/>
                                                </td>
                                                <td>
                                                    @can('product-category-delete')
                                                        <x-table.btn.swal.delete
                                                            :route="route('tenant.admin.digital.product.type.delete', $item->id)"/>
                                                    @endcan

                                                    @can('product-category-edit')
                                                        @php
                                                            $image = get_attachment_image_by_id($item->image_id);
                                                            $img_path = !empty($image) ? $image['img_url'] : '';
                                                        @endphp
                                                        <a href="#"
                                                           data-bs-toggle="modal"
                                                           data-bs-target="#product_type_edit_modal"
                                                           class="btn btn-sm btn-primary btn-xs mb-3 mr-1 product_type_edit_btn"
                                                           data-id="{{$item->id}}"
                                                           data-name="{{$item->name}}"
                                                           data-slug="{{$item->slug}}"
                                                           data-product_type="{{$item->product_type}}"
                                                           data-extensions="{{str_replace(['[',']','"'], ' ', $item->extensions)}}"
                                                           data-all_extensions="{{json_encode($extensions[$item->product_type] ?? [])}}"
                                                           data-status="{{$item->status}}"
                                                           data-imageid="{{$item->image_id}}"
                                                           data-image="{{$img_path}}"
                                                        >
                                                            <i class="mdi mdi-lead-pencil"></i>
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

                @can('digital-product-type-edit')
                    <div class="modal fade" id="product_type_edit_modal" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">{{__('Update Category')}}</h5>
                                    <button type="button" class="close" data-bs-dismiss="modal"><span>×</span></button>
                                </div>
                                <form action="{{ route('tenant.admin.digital.product.type.update') }}" method="post">
                                    <input type="hidden" name="id" id="product_type_id">
                                    <div class="modal-body">
                                        @csrf
                                        <div class="form-group">
                                            <label for="edit_name">{{__('Name')}}</label>
                                            <input type="text" class="form-control" id="edit_name" name="name"
                                                   placeholder="{{__('Name')}}">
                                        </div>

                                        <div class="form-group">
                                            <label for="edit_slug">{{__('Slug')}}</label>
                                            <input type="text" class="form-control" id="edit_slug" name="slug"
                                                   placeholder="{{__('Slug')}}">
                                        </div>

                                        <div class="form-group">
                                            <label for="edit_product_type">{{__('Product Type')}}</label>
                                            <select name="type" class="form-control product-type"
                                                    id="edit_product_type">
                                                @foreach($types as $index => $type)
                                                    <option value="{{ $index }}">{{ __($type) }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <label for="edit_extensions">{{__('Product Extension')}}</label>
                                            <select name="extensions[]" class="form-control extensions select2"
                                                    id="edit_extensions" multiple>
                                            </select>
                                        </div>

                                        <x-fields.media-upload :title="__('Image')" :name="'image_id'"
                                                               :dimentions="'120x120'"/>

                                        <div class="form-group edit-status-wrapper">
                                            <label for="edit_status">{{__('Status')}}</label>
                                            <select name="status" class="form-control" id="edit_status">
                                                <option value="1">{{ __('Public') }}</option>
                                                <option value="0">{{ __('Draft') }}</option>
                                            </select>
                                        </div>

                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-sm btn-secondary"
                                                data-bs-dismiss="modal">{{__('Close')}}</button>
                                        <button type="submit" class="btn btn-primary">{{__('Save Change')}}</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                @endcan

                @can('digital-product-type-create')
                    <div class="modal fade" id="category_create_modal" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">{{__('Create Product Type')}}</h5>
                                    <button type="button" class="close" data-bs-dismiss="modal"><span>×</span></button>
                                </div>
                                <div class="modal-body">
                                    <form action="{{ route('tenant.admin.digital.product.type.new') }}" method="post"
                                          enctype="multipart/form-data">
                                        @csrf
                                        <div class="form-group">
                                            <label for="name">{{__('Name')}}</label>
                                            <input type="text" class="form-control" id="create-name" name="name"
                                                   placeholder="{{__('Name')}}">
                                        </div>

                                        <div class="form-group">
                                            <label for="slug">{{__('Slug')}}</label>
                                            <input type="text" class="form-control" id="create-slug" name="slug"
                                                   placeholder="{{__('Slug')}}">
                                        </div>

                                        <div class="form-group">
                                            <label for="status">{{__('Product Type')}}</label>
                                            <select name="type" class="form-control product-type" id="status">
                                                <option value="" selected
                                                        disabled>{{__('Select a product type')}}</option>
                                                @foreach($types as $index => $type)
                                                    <option value="{{ $index }}">{{ __($type) }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <label for="status">{{__('Product Extension')}}</label>
                                            <select name="extensions[]" class="form-control extensions select2"
                                                    id="status" multiple>

                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <label for="status">{{__('Status')}}</label>
                                            <select name="status" class="form-control" id="status">
                                                <option value="1">{{ __('Public') }}</option>
                                                <option value="0">{{ __('Draft') }}</option>
                                            </select>
                                        </div>

                                        <x-fields.media-upload :title="__('Image')" :name="'image_id'"
                                                               :dimentions="'120x120'"/>

                                        <div class="text-end">
                                            <button type="submit"
                                                    class="btn btn-primary btn-sm mt-4 pr-4 pl-4">{{__('Add New')}}</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @endcan
            </div>
        </div>
    </div>

    <x-media-upload.markup/>
@endsection
@section('scripts')
    <script src="{{global_asset('assets/common/js/select2.min.js')}}"></script>
    <x-product::table.status-js/>
    <x-product::table.bulk-action-js :url="route('tenant.admin.digital.product.type.bulk.action')"/>
    <x-table.btn.swal.js/>
    <x-media-upload.js/>

    <script>
        $(function () {
            $('.select2').select2({
                placeholder: `{{__('Product type extensions')}}`,
                language: {
                    noResults: function () {
                        return "{{__('No result found')}}"
                    }
                },
                dropdownParent: $('.modal')
            });

            $(document).on('keyup', '#create-name', function (e) {
                let name = $(this).val();
                let slug = converToSlug(name);

                $('#create-slug').val(slug);
            });

            $(document).on('change', 'select.product-type', function (e) {
                e.preventDefault();

                let type = $(this).val();

                $.ajax({
                    'method': 'POST',
                    'url': `{{route('tenant.admin.digital.product.type.extensions')}}`,
                    'data': {
                        '_token': '{{csrf_token()}}',
                        'type': type
                    },
                    beforeSend: function () {
                        $('.select2').select2({
                            placeholder: "Select extensions",
                            allowClear: true
                        });
                    },
                    success: function (data) {
                        let select = $('select.extensions');
                        select.children().remove();

                        $.each(data.data, function (index, value) {
                            select.append(`<option value="${value}">${value}</option>`)
                        });
                    },
                    error: function (data) {

                    }
                })
            });

            $(document).on('click', '.product_type_edit_btn', function () {
                let el = $(this);
                let id = el.data('id');
                let name = el.data('name');
                let slug = el.data('slug');
                let product_type = el.data('product_type');
                let allExtensions = el.data('all_extensions');

                let extensions = el.data('extensions');
                let extensionsArray = extensions.split(',');

                let status = el.data('status');

                let modal = $('#product_type_edit_modal');

                modal.find('#product_type_id').val(id);
                modal.find('#edit_status option[value="' + status + '"]').attr('selected', true);
                modal.find('#edit_name').val(name);
                modal.find('#edit_slug').val(slug);
                modal.find('#edit_product_type option').attr('selected', false);
                modal.find('#edit_product_type option[value="' + product_type + '"]').attr('selected', true);

                let select = $('select.extensions')
                select.children().remove();
                $.each(allExtensions, function (index, value) {
                    select.append(`<option value="${value}">${value}</option>`)
                });

                $.each(extensionsArray, function (index, value) {
                    modal.find('#edit_extensions option[value="' + value.trim() + '"]').attr('selected', true);
                });

                modal.find(".edit-status-wrapper .list li[data-value='" + status + "']").click();
                modal.find(".modal-footer").click();

                let image = el.data('image');
                let imageid = el.data('imageid');

                if (imageid != '') {
                    modal.find('.media-upload-btn-wrapper .img-wrap').html('<div class="attachment-preview"><div class="thumbnail"><div class="centered"><img class="avatar user-thumb" src="' + image + '" > </div></div></div>');
                    modal.find('.media-upload-btn-wrapper input').val(imageid);
                    modal.find('.media-upload-btn-wrapper .media_upload_form_btn').text('Change Image');
                }

            });
        });

        function converToSlug(slug) {
            let finalSlug = slug.replace(/[^a-zA-Z0-9]/g, ' ');
            finalSlug = slug.replace(/  +/g, ' ');
            finalSlug = slug.replace(/\s/g, '-').toLowerCase().replace(/[^\w-]+/g, '-');
            return finalSlug;
        }
    </script>
@endsection
