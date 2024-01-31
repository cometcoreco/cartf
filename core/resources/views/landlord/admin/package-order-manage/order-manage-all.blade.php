@extends('landlord.admin.admin-master')
@section('title')
    {{__('All Orders')}}
@endsection

@section('style')
    <x-media-upload.css/>
    <x-datatable.css/>
    <x-summernote.css/>
@endsection

@section('title')
    {{__('All orders')}}
@endsection

@section('content')
    <div class="col-lg-12 col-ml-12 padding-bottom-30">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <x-error-msg/>
                        <x-flash-msg/>
                        <h4 class="header-title mb-4">{{__('All Orders')}}</h4>
                        <div class="d-flex flex-wrap justify-content-between">
                            <x-bulk-action permissions="package-order-delete"/>
                            <div class="filter-order-wrapper">
                                <div class="select-box-wrap mt-3">
                                    <select name="filter_order" id="filter_order">
                                        <option value="all">{{{__('All Order')}}}</option>
                                        <option
                                            value="pending" {{request()->filter == 'pending' ? 'selected' : ''}}>{{{__('Pending')}}}</option>
                                        <option
                                            value="in_progress" {{request()->filter == 'in_progress' ? 'selected' : ''}}>{{{__('In Progress')}}}</option>
                                        <option
                                            value="cancel" {{request()->filter == 'cancel' ? 'selected' : ''}}>{{{__('Cancel')}}}</option>
                                        <option
                                            value="complete" {{request()->filter == 'complete' ? 'selected' : ''}}>{{{__('Complete')}}}</option>
                                    </select>
                                    <button class="btn btn-primary btn-sm" id="filter_btn">{{__('Filter')}}</button>
                                </div>
                            </div>
                        </div>

                        <div class="table-wrap table-responsive">
                            <table class="table table-default table-striped table-bordered">
                                <thead class="text-white" style="background-color: #b66dff">
                                <tr>
                                    <th class="no-sort">
                                        <div class="mark-all-checkbox">
                                            <input type="checkbox" class="all-checkbox">
                                        </div>
                                    </th>
                                    <th>{{__('ID')}}</th>
                                    <th>{{__('Package Name')}}</th>
                                    <th>{{__('Package Price')}}</th>
                                    <th>{{__('Subdomain')}}</th>
                                    <th>{{__('Payment Status')}}</th>
                                    <th>{{__('Order Status')}}</th>
                                    <th>{{__('Renew Taken')}}</th>
                                    <th>{{__('Start Date')}}</th>
                                    <th>{{__('Expire Date')}}</th>
                                    <th>{{__('Action')}}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($all_orders as $data)
                                    <tr>
                                        <td>
                                            <div class="bulk-checkbox-wrapper">
                                                <input type="checkbox" class="bulk-checkbox" name="bulk_delete[]"
                                                       value="{{$data->id}}">
                                            </div>
                                        </td>
                                        <td>{{$data->id}}</td>
                                        <td>{{$data->package_name}}</td>
                                        <td>{{amount_with_currency_symbol($data->package_price)}}</td>
                                        <td>{{$data->tenant_id}}</td>
                                        <td>
                                            @php
                                                $subdomain = \App\Models\Tenant::find($data->tenant_id);
                                            @endphp

                                            <div>
                                                @if($data->payment_status == 'complete')
                                                    <span
                                                        class="alert alert-success text-capitalize">{{__($data->payment_status)}}</span>
                                                @else
                                                    <span
                                                        class="alert alert-warning text-capitalize">{{$data->payment_status ? __($data->payment_status) : __('Pending')}}</span>
                                                @endif

                                                @if($data->payment_status == 'pending')
                                                    @if($data->status != 'trial')
                                                        <a href="{{route('landlord.admin.package.order.payment.status.change',$data->id)}}"
                                                           class="btn btn-success btn-xs"
                                                           title="{{__('Make Complete')}}"><i class="las la-check"></i></a>
                                                    @endif
                                                @endif
                                            </div>

                                            @if($data->status != 'trial')
                                                @if($subdomain !== null && $data->payment_status == 'pending' && $data->renew_status != 1)
                                                    <div class="mt-3">
                                                        <small>{{__('This user has multiple same order and one of them is already approved')}}</small>
                                                    </div>
                                                @endif
                                            @endif
                                        </td>
                                        <td>
                                            @if($data->status == 'pending')
                                                <span
                                                    class="alert alert-warning text-capitalize">{{__($data->status)}}</span>
                                            @elseif($data->status == 'cancel')
                                                <span
                                                    class="alert alert-danger text-capitalize">{{__($data->status)}}</span>
                                            @elseif($data->status == 'in_progress')
                                                <span
                                                    class="alert alert-info text-capitalize">{{__(str_replace('_', ' ', $data->status))}}</span>
                                            @elseif($data->status == 'trial')
                                                <span
                                                    class="alert alert-primary text-capitalize">{{__($data->status)}}</span>
                                            @else
                                                <span
                                                    class="alert alert-success text-capitalize">{{__($data->status)}}</span>
                                            @endif
                                        </td>

                                        <td class="text-center">{{ $data->renew_status ?? 0 }}</td>
                                        <td>{{date('d-m-Y',strtotime($data->start_date))}}</td>
                                        <td>{{date('d-m-Y',strtotime($data->expire_date))}}</td>
                                        <td>

                                            <x-delete-popover permissions="package-order-delete"
                                                              url="{{route(route_prefix().'admin.package.order.manage.delete', $data->id)}}"/>

                                            <a href="#"
                                               data-bs-toggle="modal"
                                               data-bs-target="#user_edit_modal"
                                               class="btn btn-lg btn-info btn-sm mb-3 mr-1 user_edit_btn"
                                            >
                                                <i class="las la-envelope"></i>
                                            </a>
                                            <a href="{{route(route_prefix().'admin.package.order.manage.view',$data->id)}}"
                                               class="btn btn-lg btn-primary btn-sm mb-3 mr-1 view_order_details_btn">
                                                <i class="las la-eye"></i>
                                            </a>

                                            @can('package-order-edit')
                                                @if($data->status != 'trial')
                                                    <a href="#"
                                                       data-id="{{$data->id}}"
                                                       data-status="{{$data->status}}"
                                                       data-bs-toggle="modal"
                                                       data-bs-target="#order_status_change_modal"
                                                       class="btn btn-lg btn-info btn-sm mb-3 mr-1 order_status_change_btn"
                                                    >
                                                        {{__("Update Status")}}

                                                    </a>
                                                @endif
                                                @if(!empty($data->user_id) && $data->payment_status == 'pending' || $data->payment_status == null)

                                                    <form
                                                        action="{{route(route_prefix().'admin.package.order.reminder')}}"
                                                        method="post">
                                                        @csrf
                                                        <input type="hidden" name="id" value="{{$data->id}}">
                                                        <button class="btn btn-secondary mb-2 btn-sm" type="submit"><i
                                                                class="las la-bell"></i></button>
                                                    </form>
                                                @endif
                                            @endcan

                                            @if(get_lang_direction() == 'rtl')
                                                <a class="btn btn-dark btn-sm btn-invoice-rtl" href="javascript:void(0)"
                                                   data-id="{{$data->id}}">{{__('Invoice')}}</a>
                                                {{--                                                <a class="btn btn-dark btn-sm btn-invoice-rtl" href="{{route('landlord.package.invoice.generate.rtl')}}" data-id="{{$data->id}}">{{__('Invoice')}}</a>--}}
                                            @else
                                                <form action="{{route('landlord.package.invoice.generate')}}"
                                                      method="POST"
                                                      enctype="multipart/form-data" target="_blank">
                                                    @csrf
                                                    <input type="hidden" name="id" value="{{$data->id}}">
                                                    <button class="btn btn-dark btn-sm"
                                                            type="submit">{{__('Invoice')}}</button>
                                                </form>
                                            @endif
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

    @can('package-order-edit')
        @include('landlord.admin.package-order-manage.portion.status-and-mail-send')
    @endcan

    <x-media-upload.markup/>
@endsection

@section('scripts')
    <x-datatable.js/>
    <x-media-upload.js/>
    <x-summernote.js/>
    <x-bulk-action-js :url="route(route_prefix().'admin.package.order.bulk.action')"/>
{{--    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>--}}
{{--    <script src="https://unpkg.com/jspdf@latest/dist/jspdf.umd.min.js"></script>--}}
    <script>

        (function ($) {
            "use strict";
            $(document).ready(function () {
                $(document).on('click', '.order_status_change_btn', function (e) {
                    e.preventDefault();
                    var el = $(this);
                    var form = $('#order_status_change_modal');
                    form.find('#order_id').val(el.data('id'));
                    form.find('#order_status option[value="' + el.data('status') + '"]').attr('selected', true);
                });

                $('#all_user_table').DataTable({
                    "order": [[1, "desc"]],
                    'columnDefs': [{
                        'targets': 'no-sort',
                        'orderable': false
                    }]
                });

                $('.summernote').summernote({
                    height: 250,   //set editable area's height
                    codemirror: { // codemirror options
                        theme: 'monokai'
                    },
                    callbacks: {
                        onChange: function (contents, $editable) {
                            $(this).prev('input').val(contents);
                        }
                    }
                });

                $(document).on('click', '#filter_btn', function () {
                    let type = $('#filter_order').val();

                    location.href = '{{route('landlord.admin.package.order.manage.all').'?filter='}}' + type;
                });
            });

            $(document).on('click', '.btn-invoice-rtl', function (e) {
                e.preventDefault();

                let order_id = $(this).attr('data-id');

                $.ajax({
                    type: 'POST',
                    url: `{{route('landlord.package.invoice.generate.rtl')}}`,
                    data: {
                        _token: `{{csrf_token()}}`,
                        id: order_id
                    },
                    success: function (data)
                    {
                        let mywindow = window.open('', 'new div', 'height=874,width=840');
                        mywindow.document.write(data);
                        mywindow.document.close();
                        mywindow.focus();
                    }
                });
            });
        })(jQuery);

        // let invoice = () => {
        //     // Create a new jsPDF instance
        //     const doc = new jsPDF({
        //         orientation: "landscape",
        //         unit: "in",
        //         format: [4, 2]
        //     });
        //
        //     // Define the content of your invoice
        //     var invoiceTitle = "Sales Invoice";
        //     var customerName = "John Doe";
        //     var invoiceDate = "2023-09-07";
        //     var invoiceNumber = "12345";
        //     var items = [
        //         {description: "Item 1", quantity: 2, price: 10},
        //         {description: "Item 2", quantity: 3, price: 15},
        //         {description: "Item 3", quantity: 1, price: 20}
        //     ];
        //
        //     // Set the font size and position for the title
        //     doc.setFontSize(18);
        //     doc.text(invoiceTitle, 20, 20);
        //
        //     // Set the font size and position for customer information
        //     doc.setFontSize(12);
        //     doc.text("Customer: " + customerName, 20, 30);
        //     doc.text("Date: " + invoiceDate, 20, 40);
        //     doc.text("Invoice Number: " + invoiceNumber, 20, 50);
        //
        //     // Set the font size and position for the table headers
        //     doc.text("Description", 20, 70);
        //     doc.text("Quantity", 80, 70);
        //     doc.text("Price", 120, 70);
        //     doc.text("Total", 160, 70);
        //
        //     // Initialize variables to keep track of the Y position and total amount
        //     var y = 80;
        //     var totalAmount = 0;
        //
        //     // Loop through the items and add them to the table
        //     items.forEach(function (item) {
        //         doc.text(item.description, 20, y);
        //         doc.text(item.quantity.toString(), 80, y);
        //         doc.text("$" + item.price.toFixed(2), 120, y);
        //
        //         var total = item.quantity * item.price;
        //         doc.text("$" + total.toFixed(2), 160, y);
        //
        //         y += 10;
        //         totalAmount += total;
        //     });
        //
        //     // Set the font size and position for the total amount
        //     doc.setFontSize(14);
        //     doc.text("Total Amount: $" + totalAmount.toFixed(2), 120, y + 10);
        //
        //     // Save the PDF
        //     doc.save("sales_invoice.pdf");
        // }
    </script>
@endsection

