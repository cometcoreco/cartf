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
        <th> {{__("Brand")}} </th>
        <th> {{__("Categories")}} </th>
        <th> {{__("Tax Info")}} </th>
        <th> {{__("Stock Qty")}} </th>
        <th> {{__("Variant")}} </th>
        <th> {{__("Status")}} </th>
        <th> {{__("Actions")}} </th>
    </tr>
    </thead>
    <tbody>
        @forelse($products['items'] as $product)
            @php
                // Inventory Warnings
                $threshold_amount = get_static_option('stock_threshold_amount') ?? 5;
                $stock_over = $product?->inventory?->stock_count <= $threshold_amount;
            @endphp

            <tr @class(['table-cart-row', 'out_of_stock' => $stock_over])>
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
                            <p class="font-weight-bold mb-1">{!! $product->name !!}</p>
                            <p>{{Str::words($product->summary, 5)}}</p>
                        </div>
                    </div>
                </td>

                <td data-label="Image">
                    <div class="d-flex gap-2">
                        <div class="logo-brand product-brand">
                            {!! render_image_markup_by_attachment_id($product?->brand?->image_id) !!}
                        </div>
                        <b class="">{{ $product?->brand?->name }}</b>
                    </div>
                </td>

                <td class="price-td" data-label="Name">
                                            <span class="category-field">@if($product?->category?->name)
                                                    <b> {{__('Category')}}:  </b>
                                                @endif{{ $product?->category?->name }}</span> <br>
                    <span class="category-field">@if($product?->subCategory?->name)
                            <b> {{__('Sub Category')}}:  </b>
                        @endif{{ $product?->subCategory?->name }} </span><br>
                </td>

                <td class="price-td">
                    <span class="category-field">
                        @if($product?->product_tax_class)
                            <b> {{__('Tax class')}}: </b><br>
                        @endif
                        <span style="font-size: 15px">{{ $product?->product_tax_class?->name }}</span>
                    </span>
                </td>

                <td class="price-td" data-label="Quantity">
                    <span @class(['quantity-number', 'text-danger' => $stock_over])> {{ $product?->inventory?->stock_count }}</span>
                </td>

                <td class="price-td" data-label="Quantity">
                    <p @class(['badge', 'rounded', 'bg-secondary', 'custom-success-badge' => $product->inventory_detail_count])>{{ !$product->inventory_detail_count ? __('No') : __('Yes') }}</p>
                </td>

                <td data-label="Status">
                    <x-product::table.status :statuses="$statuses" :statusId="$product?->status_id"
                                             :id="$product->id"/>
                </td>

                <td data-label="Actions">
                    <div class="action-icon">
                        <a href="{{route('tenant.shop.product.details', $product->slug)}}" class="icon eye" target="_blank" title="{{__('View the product')}}" data-bs-toggle="tooltip" data-bs-placement="top">
                            <i class="las la-eye"></i>
                        </a>
                        <a href="{{ route("tenant.admin.product.edit", $product->id) }}"
                           class="icon edit" title="{{__('Edit the product')}}" data-bs-toggle="tooltip" data-bs-placement="top"> <i class="las la-pen-alt"></i> </a>
                        <a href="{{ route("tenant.admin.product.clone", $product->id) }}"
                           class="icon clone" title="{{__('Make duplicate')}}" data-bs-toggle="tooltip" data-bs-placement="top"> <i class="las la-copy"></i> </a>
                        <a data-product-url="{{ route("tenant.admin.product.destroy", $product->id) }}"
                           href="javascript:void(0)" class="delete-row icon deleted" title="{{__('Delete the product')}}" data-bs-toggle="tooltip" data-bs-placement="top">
                            <i class="las la-trash-alt"></i>
                        </a>
                        <a data-product-name="{{$product->name}}" data-sku="{{$product?->inventory->sku}}" data-barcode="{{DNS1D::getBarcodePNG($product?->inventory->sku, 'C39+', 3, 80, array(1,1,1), true)}}" class="icon barcode" href="#0" title="{{__('View and print barcode')}}" data-bs-toggle="tooltip" data-bs-placement="top">
                            <i class="las la-barcode"></i>
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
                <li><a href="{{ $link }}" class="page-number {{ ($loop->iteration - 1) == $products["current_page"] ? "current" : "" }}">{{ $loop->iteration - 1 }}</a></li>
            @endforeach
        </ul>
    </div>
</div>

<div class="modal barcode-modal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Modal title</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body bg-white">
                <div class="barcode-wrapper">
                    <p>{{__('Product barcode:')}}</p>
                    <div class="text-center barcode-canvas-wrapper">
                        <canvas id="barcodeCanvas" width="700px" height="200px"></canvas>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">{{__('Close')}}</button>
                <a href="#0" download="#" class="btn btn-success download-barcode-btn">{{__('Download')}}</a>
            </div>
        </div>
    </div>
</div>

