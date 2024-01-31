@extends('tenant.admin.admin-master')
@section('title')
    {{ __('Trashed Digital Products') }}
@endsection

@section('style')
    <x-summernote.css/>
    <x-product::variant-info.css/>
    <style>
        .float-left {
            float: left;
        }
    </style>
@endsection
@section('content')
    @php
        $allProduct = '';
            if (!$products->isEmpty())
                {
                    if (count($products) > 1)
                        {
                            $allProduct = $products->pluck('id')->toArray();
                            $allProduct = implode('|',$allProduct);
                        } else {
                            $allProduct = current(current($products))->id;
                        }
                }
    @endphp

    <div class="dashboard-recent-order">
        <div class="row">
            <x-flash-msg/>
            <div class="col-lg-12">
                <div class="recent-order-wrapper dashboard-table bg-white">
                    <div class="product-list-title-flex header-wrap d-flex flex-wrap align-items-center justify-content-between gap-2">
                        <h4 class="dashboard-common-title-two mb-4">{{__('Product Trash')}}</h4>
                        <div class="product-trash-right-wrap d-flex flex-wrap align-items-center gap-2">
                            <a href="javascript:void(0)" class="btn btn-danger btn-sm delete-all"
                               data-product-delete-all-url="{{ route("tenant.admin.digital.product.trash.empty") }}"> {{__('Empty Trash')}}</a>
                            <div class="btn-wrapper">
                                <a class="btn btn-primary btn-sm"
                                   href="{{route('tenant.admin.digital.product.all')}}">{{__('Back')}}</a>
                            </div>
                        </div>
                    </div>
                    <div class="product-trash-left-wrap">
                        <x-bulk-action/>
                    </div>
                    <div class="table-responsive table-responsive--md mt-4">
                        <table class="custom--table pt-4" id="myTable">
                            <thead class="head-bg">
                            <tr>
                                <th class="check-all-rows">
                                    <div class="mark-all-checkbox">
                                        <input type="checkbox" class="all-checkbox">
                                    </div>
                                </th>
                                <th> {{__("ID")}} </th>
                                <th> {{__("Name")}} </th>
                                <th> {{__("Type")}} </th>
                                <th> {{__("Categories")}} </th>
                                <th> {{__("Price")}} </th>
                                <th> {{__("Actions")}} </th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($products as $product)
                                <tr class="table-cart-row">
                                    <td data-label="Check All">
                                        <x-bulk-delete-checkbox :id="$product->id"/>
                                    </td>

                                    <td>
                                        <span> {{ $product->id }} </span>
                                    </td>

                                    <td class="product-name-info">
                                        <div class="d-flex gap-2">
                                            <div class="logo-brand">
                                                {!! render_image_markup_by_attachment_id($product->image_id) !!}
                                            </div>
                                            <b class="">{{ $product->name }}</b>
                                            <p>{{Str::words($product->summary, 10)}}</p>
                                        </div>
                                    </td>

                                    <td class="price-td" data-label="Type">
                                        <span class="quantity-number"> {{ $product->productType()->name ?? '' }}</span>
                                    </td>

                                    <td class="price-td text-start" data-label="Name">
                                        @if($product?->category?->name)
                                            <b> {{__('Category')}}: </b>
                                        @endif{{ $product?->category?->name }} <br>
                                        @if($product?->subCategory?->name)
                                            <b> {{__('Sub Category')}}: </b>
                                        @endif{{ $product?->subCategory?->name }} <br>
                                    </td>

                                    <td class="price-td" data-label="Price">
                                        @php
                                            $price = $product->regular_price;
                                            $regular_price = null;
                                            if (!empty($product->sale_price) && $product->sale_price > 0)
                                            {
                                                $price = $product->sale_price;
                                                $regular_price = $product->regular_price;
                                            }
                                        @endphp

                                        @if($price > 0)
                                            <p class="quantity-number" )> {{ amount_with_currency_symbol($price) }}</p>

                                            @if(!empty($regular_price))
                                                <p class="text-small"><del>{{amount_with_currency_symbol($regular_price)}}</del></p>
                                            @endif

                                        @else
                                            <p class="quantity-number text-success" )> {{ __('Free') }}</p>
                                        @endif
                                    </td>

                                    <td data-label="Actions">
                                        <div class="action-icon">
                                            <a href="{{ route("tenant.admin.digital.product.trash.restore", $product->id) }}"
                                               class="product-restore btn btn-success btn-sm"> {{__('Restore')}} </a>
                                            <a data-product-delete-url="{{ route("tenant.admin.digital.product.trash.delete", $product->id) }}"
                                               href="javascript:void(0)"
                                               class="product-delete btn btn-danger btn-sm"> {{__('Delete')}} </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9"
                                        class="text-center text-warning"> {{__('No Trashed Product Available')}} </td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <x-product::table.bulk-action-js :url="route('tenant.admin.digital.product.trash.bulk.destroy')"/>
    <script>
        $(document).on("click", ".delete-all", function (e) {
            e.preventDefault();
            let el = $(this);
            let delete_url = el.data('product-delete-all-url');
            let allIds = '{{$allProduct}}';

            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete all!'
            }).then((result) => {
                if (result.isConfirmed) {
                    if (allIds != '') {
                        $(this).html('<i class="fas fa-spinner fa-spin mr-1"></i>{{__("Deleting")}}');
                        $.ajax({
                            'type': "POST",
                            'url': delete_url,
                            'data': {
                                _token: "{{csrf_token()}}",
                                ids: allIds
                            },
                            success: function (data) {
                                toastr.success('Trash in Empty');
                                setTimeout(() => {
                                    location.reload();
                                }, 1000)
                            }
                        });
                    }
                }
            });
        });

        $(document).on("click", ".product-delete", function (e) {
            e.preventDefault();
            let delete_url = $(this).data('product-delete-url');

            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    location.replace(delete_url);
                }
            });
        });
    </script>
@endsection
