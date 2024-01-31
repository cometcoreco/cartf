@extends('landlord.admin.admin-master')

@section('title')
    {{__('Price Plan Coupon')}}
@endsection

@section('style')
    <x-datatable.css />
    <x-bulk-action.css />

    <style>
        #form_category, #edit_#form_category,
        #form_subcategory, #edit_#form_subcategory,
        #form_childcategory, #edit_#form_childcategory,
        #form_products, #edit_#form_products {
            display: none;
        }

        .lds-ellipsis {
            position: fixed;
            width: 80px;
            height: 80px;
            left: 50vw;
            top: 40vh;
            z-index: 50;
            display: none;
        }
        .lds-ellipsis div {
            position: absolute;
            top: 33px;
            width: 13px;
            height: 13px;
            border-radius: 50%;
            background: {{ get_static_option('site_color') }};
            animation-timing-function: cubic-bezier(0, 1, 1, 0);
        }
        .lds-ellipsis div:nth-child(1) {
            left: 8px;
            animation: lds-ellipsis1 0.6s infinite;
        }
        .lds-ellipsis div:nth-child(2) {
            left: 8px;
            animation: lds-ellipsis2 0.6s infinite;
        }
        .lds-ellipsis div:nth-child(3) {
            left: 32px;
            animation: lds-ellipsis2 0.6s infinite;
        }
        .lds-ellipsis div:nth-child(4) {
            left: 56px;
            animation: lds-ellipsis3 0.6s infinite;
        }
        @keyframes lds-ellipsis1 {
            0% {
                transform: scale(0);
            }
            100% {
                transform: scale(1);
            }
        }
        @keyframes lds-ellipsis3 {
            0% {
                transform: scale(1);
            }
            100% {
                transform: scale(0);
            }
        }
        @keyframes lds-ellipsis2 {
            0% {
                transform: translate(0, 0);
            }
            100% {
                transform: translate(24px, 0);
            }
        }

        /*.select2-dropdown ,*/
        .select2-container
        {
            z-index: 1072;
        }
    </style>
@endsection
@section('content')
    <div class="col-lg-12 col-ml-12">
        <div class="row g-4">
            <div class="col-xl-7 col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="header-title mb-4">{{__('All Coupons')}}</h4>
                        @can('product-coupon-delete')
                            <x-bulk-action.dropdown />
                        @endcan
                        <div class="table-wrap table-responsive">
                            <table class="table table-default">
                                <thead>
                                <x-bulk-action.th />
                                <th>{{__('ID')}}</th>
                                <th>{{__('Name & Code')}}</th>
                                <th>{{__('Discount')}}</th>
                                <th>{{__('Expire Date')}}</th>
                                <th>{{__('Status')}}</th>
                                <th>{{__('Action')}}</th>
                                </thead>
                                <tbody>
                                @foreach($all_coupon as $data)
                                    <tr>
                                        <x-bulk-action.td :id="$data->id" />
                                        <td>{{$data->id}}</td>
                                        <td>
                                            <strong>{{__('Name:')}}</strong> {{$data->name}}
                                            <br>
                                            <br>
                                            <strong>{{__('Code:')}}</strong> {{$data->code}}
                                        </td>
                                        <td>@if($data->discount_type == \App\Enums\LandlordCouponType::Percentage) {{$data->discount_amount}}% @else {{amount_with_currency_symbol($data->discount_amount)}} @endif</td>
                                        <td>{{ $data->expire_date ? date('d M Y', strtotime($data->expire_date)) : '' }}</td>
                                        <td>
                                            <x-status-span :status="strtolower(\App\Enums\StatusEnums::getText($data->status))"/>
                                        </td>
                                        <td>
                                            @can('product-coupon-delete')
                                                <x-table.btn.swal.delete :route="route('landlord.admin.coupon.delete', $data->id)" />
                                            @endcan
                                            @can('product-coupon-edit')
                                                <a href="#"
                                                   data-bs-toggle="modal"
                                                   data-bs-target="#category_edit_modal"
                                                   class="btn btn-sm btn-primary btn-xs mb-3 mr-1 category_edit_btn"
                                                   data-id="{{$data->id}}"
                                                   data-name="{{$data->name}}"
                                                   data-code="{{$data->code}}"
                                                   data-description="{{$data->description}}"
                                                   data-discount_amount="{{$data->discount_amount}}"
                                                   data-discount_type="{{$data->discount_type}}"
                                                   data-expire_date="{{$data->expire_date}}"
                                                   data-status="{{$data->status}}"
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

            @can('product-coupon-create')
                <div class="col-xl-5 col-lg-12">
                    <x-error-msg/>
                    <x-flash-msg/>
                    <div class="card">
                        <div class="card-body">
                            <h4 class="header-title mb-4">{{__('Add New Coupon')}}</h4>
                            <form action="{{route('landlord.admin.coupon.store')}}" method="post">
                                @csrf

                                <div class="form-group">
                                    <label for="title">{{__('Coupon Title')}}<x-fields.mandatory-indicator/></label>
                                    <input type="text" class="form-control"  id="title" name="name" placeholder="{{__('Title')}}" value="{{old('title')}}" required>
                                </div>

                                <div class="form-group">
                                    <label for="code">{{__('Coupon Code')}}<x-fields.mandatory-indicator/></label>
                                    <input type="text" class="form-control"  id="code" name="code" placeholder="{{__('Code')}}" value="{{old('code')}}" required>
                                    <span id="status_text" class="text-danger" style="display: none"></span>
                                </div>

                                <div class="form-group">
                                    <label for="code">{{__('Coupon Description')}}<sup>({{__('Optional')}})</sup></label>
                                    <input type="text" class="form-control"  id="description" name="description" placeholder="{{__('Description')}}" value="{{old('description')}}">
                                </div>

                                <div class="form-group">
                                    <label for="discount">{{__('Discount')}}<x-fields.mandatory-indicator/></label>
                                    <input type="number" class="form-control"  id="discount" name="discount_amount" placeholder="{{__('Discount')}}" value="{{old('discount_amount')}}" required>
                                </div>
                                <div class="form-group">
                                    <label for="discount_type">{{__('Coupon Type')}}<x-fields.mandatory-indicator/></label>
                                    <select name="discount_type" class="form-control" id="discount_type" required>
                                        <option value="{{\App\Enums\LandlordCouponType::Percentage}}">{{__("Percentage")}}</option>
                                        <option value="{{\App\Enums\LandlordCouponType::Amount}}">{{__("Amount")}}</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="expire_date">{{__('Expire Date')}}</label>
                                    <input type="date" class="form-control flatpickr"  id="expire_date" name="expire_date" placeholder="{{__('Expire Date')}}" required>
                                </div>
                                <div class="form-group">
                                    <label for="status">{{__('Status')}}</label>
                                    <select name="status" class="form-control" id="status" required>
                                        <option value="1">{{__("Publish")}}</option>
                                        <option value="0">{{__("Draft")}}</option>
                                    </select>
                                </div>
                                <button type="submit" id="coupon_create_btn" class="btn btn-primary mt-4 pr-4 pl-4">{{__('Add New Coupon')}}</button>
                            </form>
                        </div>
                    </div>
                </div>
            @endcan
        </div>
    </div>

    @can('product-coupon-edit')
        <div class="modal fade" id="category_edit_modal" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{__('Update Coupon')}}</h5>
                        <button type="button" class="close" data-bs-dismiss="modal"><span>×</span></button>
                    </div>
                    <form action="{{route('landlord.admin.coupon.update')}}"  method="post">
                        <input type="hidden" name="id" id="coupon_id">
                        <div class="modal-body">
                            @csrf
                            <div class="form-group">
                                <label for="title">{{__('Coupon Title')}} <x-fields.mandatory-indicator/></label>
                                <input type="text" class="form-control"  id="edit_title" name="name" placeholder="{{__('Title')}}" required>
                            </div>
                            <div class="form-group">
                                <label for="edit_code">{{__('Coupon Code')}} <x-fields.mandatory-indicator/></label>
                                <input type="text" class="form-control"  id="edit_code" name="code" placeholder="{{__('Code')}}">
                                <span id="status_text" class="text-danger" style="display: none"></span>
                            </div>
                            <div class="form-group">
                                <label for="code">{{__('Coupon Description')}} <sup>({{__('Optional')}})</sup></label>
                                <textarea class="form-control"  id="edit_description" name="description" placeholder="{{__('Description')}}" value="{{old('description')}}"></textarea>
                            </div>
                            <div class="form-group">
                                <label for="edit_discount">{{__('Discount')}} <x-fields.mandatory-indicator/></label>
                                <input type="number" class="form-control"  id="edit_discount" name="discount_amount" placeholder="{{__('Discount')}}">
                            </div>
                            <div class="form-group">
                                <label for="edit_discount_type">{{__('Coupon Type')}} <x-fields.mandatory-indicator/></label>
                                <select name="discount_type" class="form-control" id="edit_discount_type">
                                    <option value="{{\App\Enums\LandlordCouponType::Percentage}}">{{__("Percentage")}}</option>
                                    <option value="{{\App\Enums\LandlordCouponType::Amount}}">{{__("Amount")}}</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="edit_expire_date">{{__('Expire Date')}}</label>
                                <input type="date" class="form-control flatpickr"  id="edit_expire_date" name="expire_date" placeholder="{{__('Expire Date')}}">
                            </div>
                            <div class="form-group">
                                <label for="edit_status">{{__('Status')}}</label>
                                <select name="status" class="form-control" id="edit_status">
                                    <option value="0">{{__("Draft")}}</option>
                                    <option value="1">{{__("Publish")}}</option>
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{__('Close')}}</button>
                            <button type="submit" class="btn btn-primary" id="coupon_edit_btn">{{__('Save Change')}}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endcan

    <div class="lds-ellipsis"><div></div><div></div><div></div><div></div></div>
@endsection
@section('scripts')
    <x-datatable.js />
    <x-table.btn.swal.js />
    <x-bulk-action.js :route="route('tenant.admin.product.coupon.bulk.action')" />
    <x-unique-checker selector="input[name=code]" table="coupons" column="code" disable-btn="true" disable-btn-selector="#coupon_create_btn, #coupon_edit_btn"/>

    <script>
        $(document).ready(function () {
            flatpickr(".flatpickr", {
                altInput: true,
                altFormat: "F j, Y",
                dateFormat: "Y-m-d",
            });

            $(document).on('click','.category_edit_btn',function(){
                let el = $(this);
                let id = el.data('id');
                let name = el.data('name');
                let code = el.data('code');
                let description = el.data('description');
                let status = el.data('status');
                let discount_type = el.data('discount_type');
                let discount_amount = el.data('discount_amount');
                let expire_date = el.data('expire_date');
                let modal = $('#category_edit_modal');

                modal.find('#coupon_id').val(id);
                modal.find('#edit_status option[value="'+status+'"]').attr('selected',true);
                modal.find('#edit_code').val(code);
                modal.find('#edit_description').val(description);
                modal.find('#edit_discount').val(discount_amount);
                modal.find('#edit_discount_type').val(discount_type);
                modal.find('#edit_discount_type[value="'+discount_type+'"]').attr('selected',true);
                modal.find('#edit_title').val(name);

                flatpickr(modal.find('#edit_expire_date'), {
                    defaultDate: expire_date,
                    altInput: true,
                    altFormat: "F j, Y",
                    dateFormat: "Y-m-d",
                });
            });
        });
    </script>
@endsection
