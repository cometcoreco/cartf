@extends(route_prefix().'admin.admin-master')
@section('title')
    {{__('Digital Product Tax Manage')}}
@endsection
@section('style')
    <x-datatable.css/>
    <x-bulk-action.css/>

    <style>
        .img-wrap img{
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
                            <h4 class="header-title mb-4">{{__('Digital Product Tax Manage')}}</h4>
                            <div class="div">
                                @can('digital-tax-create')
                                    <a href="#"
                                       data-bs-toggle="modal"
                                       data-bs-target="#category_create_modal"
                                       class="btn btn-sm btn-info mb-3 mr-1 text-light">{{__('Add New Tax')}}</a>
                                @endcan
                            </div>
                        </div>
                        @can('digital-tax-delete')
                            <x-bulk-action.dropdown/>
                        @endcan

                        <div class="table-wrap table-responsive">
                            <table class="table table-default">
                                <thead>
                                <x-bulk-action.th/>
                                <th>{{__('ID')}}</th>
                                <th>{{__('Name')}}</th>
                                <th>{{__('Tax Percent (%)')}}</th>
                                <th>{{__('Status')}}</th>
                                <th>{{__('Action')}}</th>
                                </thead>
                                <tbody>
                                @foreach($all_taxes ?? [] as $key => $tax)
                                    <tr>
                                        <x-bulk-action.td :id="$tax->id"/>
                                        <td>{{$tax->id}}</td>
                                        <td>{{$tax->name}}</td>
                                        <td>{{$tax->tax_percentage}}</td>
                                        <td>
                                            {{\App\Enums\StatusEnums::getText($tax->status)}}
                                        </td>
                                        <td>
                                            @can('digital-tax-delete')
                                                <x-table.btn.swal.delete
                                                    :route="route('tenant.admin.digital.product.tax.delete', $tax->id)"/>
                                            @endcan

                                            @can('digital-tax-edit')
                                                <a href="#"
                                                   data-bs-toggle="modal"
                                                   data-bs-target="#subcategory_edit_modal"
                                                   class="btn btn-sm btn-primary btn-xs mb-3 mr-1 subcategory_edit_btn"
                                                   data-id="{{$tax->id}}"
                                                   data-name="{{$tax->name}}"
                                                   data-percent="{{$tax->tax_percentage}}"
                                                   data-status="{{$tax->status}}"
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

    @can('digital-tax-edit')
        <div class="modal fade" id="subcategory_edit_modal" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{__('Update Category')}}</h5>
                        <button type="button" class="close" data-bs-dismiss="modal"><span>×</span></button>
                    </div>
                    <form action="{{ route('tenant.admin.digital.product.tax.update') }}" method="post">
                        <input type="hidden" name="id" id="category_id">
                        <div class="modal-body">
                            @csrf
                            <div class="form-group">
                                <label for="edit_name">{{__('Name')}}</label>
                                <input type="text" class="form-control" id="edit_name" name="name"
                                       placeholder="{{__('Name')}}">
                            </div>

                            <div class="form-group">
                                <label for="edit_tax_percent">{{__('Tax Percent (%)')}}</label>
                                <input type="number" class="form-control" id="edit_tax_percent" name="tax_percent"
                                       placeholder="{{__('Tax Percent (%)')}}">
                            </div>

                            <div class="form-group edit-status-wrapper">
                                <label for="edit_status">{{__('Status')}}</label>
                                <select name="status_id" class="form-control" id="edit_status">
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

    @can('digital-tax-create')
        <div class="modal fade" id="category_create_modal" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{__('Add New Tax')}}</h5>
                        <button type="button" class="close" data-bs-dismiss="modal"><span>×</span></button>
                    </div>
                    <div class="modal-body">
                        <form action="{{ route('tenant.admin.digital.product.tax.new') }}" method="post"
                              enctype="multipart/form-data">
                            @csrf
                            <div class="form-group">
                                <label for="name">{{__('Name')}}</label>
                                <input type="text" class="form-control" id="create-name" name="name"
                                       placeholder="{{__('Name')}}">
                            </div>

                            <div class="form-group">
                                <label for="create-percent">{{__('Tax Percent (%)')}}</label>
                                <input type="number" class="form-control" id="create-percent" name="tax_percent"
                                       placeholder="{{__('Tax Percent (%)')}}">
                            </div>

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
@endsection

@section('scripts')
    <x-datatable.js/>
    <x-table.btn.swal.js/>
    @can('digital-tax-delete')
        <x-bulk-action.js :route="route('tenant.admin.digital.product.tax.bulk.action')"/>
    @endcan

    <script>
        $(document).ready(function () {
            $(document).on('click', '.subcategory_edit_btn', function () {
                let el = $(this);
                let id = el.data('id');
                let name = el.data('name');
                let percent = el.data('percent');
                let status = el.data('status');
                let modal = $('#subcategory_edit_modal');

                modal.find('#category_id').val(id);
                modal.find('#edit_status option').attr('selected', false);
                modal.find('#edit_status option[value="' + status + '"]').attr('selected', true);
                modal.find('#edit_name').val(name);
                modal.find('#edit_tax_percent').val(percent);
                modal.find(".modal-footer").click();
            });
        });
    </script>
@endsection
