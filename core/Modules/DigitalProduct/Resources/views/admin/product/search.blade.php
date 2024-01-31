<table class="customs-tables pt-4 position-relative" id="myTable">
    <div class="load-ajax-data"></div>
    <thead class="head-bg">
    <tr>
        <th class="check-all-rows p-3">
            <div class="mark-all-checkbox text-center">
                <input type="checkbox" class="all-checkbox">
            </div>
        </th>
        <th> {{__("ID")}} </th>
        <th> {{__("Name")}} </th>
        <th> {{__("Type")}} </th>
        <th> {{__("Categories")}} </th>
        <th> {{__("Price")}} </th>
        <th> {{__("Status")}} </th>
        <th> {{__("Actions")}} </th>
    </tr>
    </thead>
    <tbody>
    @forelse($products['items'] as $product)
        <tr class="table-cart-row">
            <td data-label="Check All">
                <x-bulk-delete-checkbox :id="$product->id"/>
            </td>

            <td>
                <span class="quantity-number">{{$product->id}}</span>
            </td>

            <td class="product-name-info">
                <div class="d-flex gap-2">
                    <div class="logo-brand">
                        {!! render_image_markup_by_attachment_id($product->image_id) !!}
                    </div>
                    <div class="product-summary">
                        <p class="font-weight-bold mb-1">{{ $product->name }}</p>
                        <p>{{Str::words($product->summary, 5)}}</p>

                        @if($product->file == 'no file added')
                            <small class="py-0 my-0 text-danger">{{__('No file added')}}</small>
                        @endif
                    </div>
                </div>
            </td>

            <td class="price-td" data-label="Type">
                <span class="quantity-number"> {{ $product->productType()->name ?? '' }}</span>
            </td>

            <td class="price-td text-start" data-label="Name">
                <span class="category-field">@if($product?->category?->name)
                      <b> {{__('Category')}}:  </b>
                      @endif{{ $product?->category?->name }}
                </span> <br>
                <span class="category-field">
                    @if($product?->subCategory?->name)
                      <b> {{__('Sub Category')}}:  </b>
                    @endif{{ $product?->subCategory?->name }}
                </span> <br>
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

            <td data-label="Status">
                <x-product::table.status :statuses="$statuses" :statusId="$product?->status_id"
                                         :id="$product->id"/>
            </td>

            <td data-label="Actions">
                <div class="action-icon">
                    <a href="{{route('tenant.shop.product.details', $product->slug)}}" class="icon eye" target="_blank"
                       title="View the product" data-bs-toggle="tooltip" data-bs-placement="top">
                        <i class="las la-eye"></i>
                    </a>
                    <a href="{{ route("tenant.admin.digital.product.edit", $product->id) }}"
                       class="icon edit" title="Edit the product" data-bs-toggle="tooltip" data-bs-placement="top"> <i
                            class="las la-pen-alt"></i> </a>
                    <a href="{{ route("tenant.admin.digital.product.clone", $product->id) }}"
                       class="icon clone" title="Make duplicate" data-bs-toggle="tooltip" data-bs-placement="top"> <i
                            class="las la-copy"></i> </a>
                    <a data-product-url="{{ route("tenant.admin.digital.product.destroy", $product->id) }}"
                       href="javascript:void(0)" class="delete-row icon deleted" title="Delete the product"
                       data-bs-toggle="tooltip" data-bs-placement="top">
                        <i class="las la-trash-alt"></i>
                    </a>
                </div>
            </td>
        </tr>
    @empty
        <tr>
            <td colspan="7" class="text-warning text-center">{{__('No Product Available')}}</td>
        </tr>
    @endforelse
    </tbody>
</table>

<div class="custom-pagination-wrapper">
    <div class="pagination-info">
        <p>
            <strong>{{__('Per Page:')}}</strong>
            <span>{{ $products["per_page"] }}</span>
        </p>
        <p>
            <strong>{{__('From:')}}</strong>
            <span>{{ $products["from"] }}</span>
            <strong> {{__('To:')}}</strong>
            <span>{{ $products["to"] }}</span>
        </p>
        <p>
            <strong>{{__('Total Page:')}}</strong>
            <span>{{ $products["total_page"] }}</span>
        </p>
        <p>
            <strong>{{__('Total Products:')}}</strong>
            <span>{{ $products["total_items"] }}</span>
        </p>
    </div>

    <div class="pagination">
        <ul class="pagination-list">
            @foreach($products["links"] as $link)
                @php if($loop->iteration == 1):  continue; endif @endphp
                <li><a href="{{ $link }}"
                       class="page-number {{ ($loop->iteration - 1) == $products["current_page"] ? "current" : "" }}">{{ $loop->iteration - 1 }}</a>
                </li>
            @endforeach
        </ul>
    </div>
</div>

