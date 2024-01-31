@extends(route_prefix().'admin.admin-master')
@section('title')
    {{__('Digital Product Child Category')}}
@endsection
@section('style')
    <x-media-upload.css/>
    <x-datatable.css/>
    <x-bulk-action.css/>

    <style>
        .img-wrap img {
            width: 100%;
        }
    </style>
@endsection

@section('content')
    <div class="col-lg-12 col-ml-12">
        <x-error-msg/>
        <x-flash-msg/>
        <div class="row g-4">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div class="header-wrap d-flex flex-wrap justify-content-between">
                            <h4 class="header-title mb-4">{{__('All Digital Products Child Categories')}}</h4>
                            <div class="div">
                                @can('product-category-create')
                                    <a href="#"
                                       data-bs-toggle="modal"
                                       data-bs-target="#category_create_modal"
                                       class="btn btn-sm btn-info mb-3 mr-1 text-light">{{__('New Child Category')}}</a>
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
                                <th>{{__('Category')}}</th>
                                <th>{{__('Sub Category')}}</th>
                                <th>{{__('Description')}}</th>
                                <th>{{__('Image')}}</th>
                                <th>{{__('Status')}}</th>
                                <th>{{__('Action')}}</th>
                                </thead>
                                <tbody>
                                @foreach($all_childcategory ?? [] as $key => $subcategory)
                                    <tr>
                                        <x-bulk-action.td :id="$subcategory->id"/>
                                        <td>{{$subcategory->id}}</td>
                                        <td>{{$subcategory->name}}</td>
                                        <td>{{$subcategory->category?->name}}</td>
                                        <td>{{$subcategory->subcategory?->name}}</td>
                                        <td>{{$subcategory->description}}</td>
                                        <td>
                                            <div class="attachment-preview">
                                                <div class="img-wrap">
                                                    {!! render_image_markup_by_attachment_id($subcategory->image_id) !!}
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            {{\App\Enums\StatusEnums::getText($subcategory->status)}}
                                        </td>
                                        <td>
                                            @can('product-category-delete')
                                                <x-table.btn.swal.delete
                                                    :route="route('tenant.admin.digital.product.childcategory.delete', $subcategory->id)"/>
                                            @endcan

                                            @can('product-category-edit')
                                                @php
                                                    $image = get_attachment_image_by_id($subcategory->image_id, null, true);
                                                    $img_path = $image['img_url'];

                                                    $all_subcategories = \Modules\DigitalProduct\Entities\DigitalSubCategories::where('id', $subcategory->id)->get();
                                                @endphp

                                                <a href="#"
                                                   data-bs-toggle="modal"
                                                   data-bs-target="#subcategory_edit_modal"
                                                   class="btn btn-sm btn-primary btn-xs mb-3 mr-1 subcategory_edit_btn"
                                                   data-id="{{$subcategory->id}}"
                                                   data-name="{{$subcategory->name}}"
                                                   data-slug="{{$subcategory->slug}}"
                                                   data-category="{{$subcategory->category_id}}"
                                                   data-subcategory="{{$subcategory->sub_category_id}}"
                                                   data-subcategories="{{json_encode($all_subcategories)}}"
                                                   data-description="{{$subcategory->description}}"
                                                   data-status="{{$subcategory->status}}"
                                                   data-imageid="{{$subcategory->image_id}}"
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
    </div>

    @can('product-category-edit')
        <div class="modal fade" id="subcategory_edit_modal" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{__('Update Child Category')}}</h5>
                        <button type="button" class="close" data-bs-dismiss="modal"><span>×</span></button>
                    </div>
                    <form action="{{ route('tenant.admin.digital.product.childcategory.update') }}" method="post">
                        <input type="hidden" name="id" id="category_id">
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
                                <label for="edit_description">{{__('Description')}}</label>
                                <textarea type="text" class="form-control" id="edit_description" name="description"
                                          placeholder="{{__('Description')}}" rows="8"></textarea>
                            </div>

                            <div class="form-group">
                                <label for="edit_category">{{__('Category')}}</label>
                                <select name="category" class="form-control" id="edit_category">
                                    @foreach($all_category as $item)
                                        <option value="{{$item->id}}">{{ $item->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="edit_subcategory">{{__('Sub Category')}}</label>
                                <select name="subcategory" class="form-control" id="edit_subcategory">

                                </select>
                            </div>

                            <x-fields.media-upload :title="__('Image')" :name="'image_id'" :dimentions="'120x120'"/>

                            <div class="form-group edit-status-wrapper">
                                <label for="edit_status">{{__('Status')}}</label>
                                <select name="status_id" class="form-control" id="status">
                                    <option value="1">{{ __('Publish') }}</option>
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

    @can('product-category-create')
        <div class="modal fade" id="category_create_modal" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{__('Create Child Category')}}</h5>
                        <button type="button" class="close" data-bs-dismiss="modal"><span>×</span></button>
                    </div>
                    <div class="modal-body">
                        <form action="{{ route('tenant.admin.digital.product.childcategory.new') }}" method="post"
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
                                <label for="description">{{__('Description')}}</label>
                                <textarea type="text" class="form-control" id="description" name="description"
                                          placeholder="{{__('Description')}}" rows="8"></textarea>
                            </div>

                            <div class="form-group">
                                <label for="edit_category">{{__('Category')}}</label>
                                <select name="category" class="form-control" id="category">
                                    <option value="">{{__('Select a category')}}</option>
                                    @foreach($all_category as $item)
                                        <option value="{{$item->id}}">{{ $item->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="edit_subcategory">{{__('Sub Category')}}</label>
                                <select name="subcategory" class="form-control" id="subcategory">

                                </select>
                            </div>

                            <x-fields.media-upload :title="__('Image')" :name="'image_id'" :dimentions="'120x120'"/>

                            <div class="form-group">
                                <label for="status">{{__('Status')}}</label>
                                <select name="status_id" class="form-control" id="status">
                                    <option value="1">{{ __('Publish') }}</option>
                                    <option value="0">{{ __('Draft') }}</option>
                                </select>
                            </div>

                            <button type="submit" class="btn btn-primary mt-4 pr-4 pl-4">{{__('Add New')}}</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endcan
    <div class="body-overlay-desktop"></div>
    <x-media-upload.markup/>
@endsection

@section('scripts')
    <x-datatable.js/>
    <x-media-upload.js/>
    <x-table.btn.swal.js/>
    @can('product-category-delete')
        <x-bulk-action.js :route="route('tenant.admin.digital.product.childcategory.bulk.action')"/>
    @endcan

    <script>
        $(document).ready(function () {
            $(document).on('change', '#category', function () {
                let category_id = $(this).val();
                let subcategory_select = $('select#subcategory');

                $.ajax({
                    'method': 'POST',
                    'url': `{{route('tenant.admin.digital.product.childcategory.category.based.subcategory')}}`,
                    'data': {
                        '_token': '{{csrf_token()}}',
                        'category': category_id
                    },
                    beforeSend: function () {
                        subcategory_select.html('<option value="" selected disabled>'+'{{__('Select a subcategory')}}'+'</option>');
                    },
                    success: function (data) {
                        subcategory_select.children().remove();
                        subcategory_select.html(data.markup);
                    },
                    error: function (data) {

                    }
                });
            });

            $(document).on('change', '#edit_category', function () {
                let category_id = $(this).val();
                let subcategory_select = $('select#edit_subcategory');

                $.ajax({
                    'method': 'POST',
                    'url': `{{route('tenant.admin.digital.product.childcategory.category.based.subcategory')}}`,
                    'data': {
                        '_token': '{{csrf_token()}}',
                        'category': category_id
                    },
                    beforeSend: function () {
                        subcategory_select.html('<option value="" selected disabled>'+'{{__('Select a subcategory')}}'+'</option>');
                    },
                    success: function (data) {
                        subcategory_select.children().remove();
                        subcategory_select.html(data.markup);
                    },
                    error: function (data) {

                    }
                });
            });

            $(document).on('click', '.subcategory_edit_btn', function () {
                let el = $(this);
                let id = el.data('id');
                let name = el.data('name');
                let slug = el.data('slug');
                let description = el.data('description');
                let status = el.data('status');
                let category = el.data('category');
                let subcategory = el.data('subcategory');
                let subcategories = el.data('subcategories');
                let modal = $('#subcategory_edit_modal');

                modal.find('#category_id').val(id);
                modal.find('#edit_status option[value="' + status + '"]').attr('selected', true);
                modal.find('#edit_category option').attr('selected', false);
                modal.find('#edit_category option[value="' + category + '"]').attr('selected', true);

                modal.find('#edit_subcategory').children().remove();
                $.each(subcategories, function (index, value) {
                    modal.find('#edit_subcategory').append(`<option value=${value.id} ${value.id == subcategory ? 'selected' : ''}>${value.name}</option>`);
                });

                modal.find('#edit_name').val(name);
                modal.find('#edit_slug').val(slug);
                modal.find('#edit_description').val(description);
                modal.find(".edit-status-wrapper .list li[data-value='" + status + "']").click();
                modal.find(".modal-footer").click();

                let image = el.data('image');
                let imageid = el.data('imageid');

                if (imageid != '') {
                    modal.find('.media-upload-btn-wrapper .img-wrap').html('<div class="attachment-preview"><div class="thumbnail"><div class="centered"><img class="avatar user-thumb" src="' + image + '" > </div></div></div>');
                    modal.find('.media-upload-btn-wrapper input').val(imageid);
                    modal.find('.media-upload-btn-wrapper .media_upload_form_btn').text('{{__('Change Image')}}');
                }

            });

            $('#create-name , #create-slug').on('keyup', function () {
                let title_text = $(this).val();
                $('#create-slug').val(convertToSlug(title_text))
            });

            $('#edit_name , #edit_slug').on('keyup', function () {
                let title_text = $(this).val();
                $('#edit_slug').val(convertToSlug(title_text))
            });
        });

        function convertToSlug(text) {
            return text
                .toLowerCase()
                .replace(/ /g, '-')
                .replace(/[^\w-]+/g, '');
        }
    </script>
@endsection
