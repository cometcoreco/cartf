@extends('tenant.admin.admin-master')
@section('title')
    {{__(ucfirst($page_title).' '.'Sales Report')}}
@endsection
@section('style')
    <x-datatable.css/>
    <x-bulk-action.css/>
@endsection
@section('content')
    <style>
        .box {
            padding: 20px 10px;
            padding-left: 25px;
        }

        .box_wrapper:nth-child(1) .box {
            color: #FC4F00;
            background: rgba(252, 79, 0, 0.1);
        }

        .box_wrapper:nth-child(2) .box {
            color: #0079FF;
            background: rgba(0, 121, 255, 0.1);
        }

        .box_wrapper:nth-child(3) .box {
            color: #22A699;
            background: rgba(34, 166, 153, 0.1);
        }

        .box_wrapper:nth-child(4) .box {
            color: #8F43EE;
            background: rgba(143, 67, 238, 0.1);
        }
        .product-type-item-para {
            font-size: 11px;
            font-weight: 400;
            position: relative;
        }

    </style>

    <div class="col-lg-12 col-ml-12">
        <div class="row g-4">
            <div class="col-lg-12">
                <div class="margin-top-40">
                    <x-error-msg/>
                    <x-flash-msg/>
                </div>
            </div>
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-3 box_wrapper">
                                <div class="box">
                                    <p>{{__('Number of Sales')}}</p>
                                    <h2>{{$total_report['total_sale']}}</h2>
                                </div>
                            </div>

                            <div class="col-lg-3 box_wrapper">
                                <div class="box">
                                    <p>{{__('Total Revenue')}}</p>
                                    <h2>{{amount_with_currency_symbol($total_report['total_revenue'])}}</h2>
                                </div>
                            </div>

                            <div class="col-lg-3 box_wrapper">
                                <div class="box">
                                    <p>{{__('Total Profit')}}</p>
                                    <h2>{{amount_with_currency_symbol($total_report['total_profit'])}}</h2>
                                </div>
                            </div>
                            <div class="col-lg-3 box_wrapper">
                                <div class="box">
                                    <p>{{__('Total Cost')}}</p>
                                    <h2>{{amount_with_currency_symbol($total_report['total_cost'])}}</h2>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mt-5">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="sales_table_wrapper">
                                    <table class="table table-hover">
                                        <thead>
                                        <tr>
                                            <th>{{__('ID')}}</th>
                                            <th>{{__('Date')}}</th>
                                            <th>{{__('Type')}}</th>
                                            <th>{{__('Product')}}</th>
                                            <th>{{__('Qty')}}</th>
                                            <th>{{__('Cost')}}</th>
                                            <th>{{__('Price')}}</th>
                                            <th>{{__('Profit')}}</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @forelse($products['items'] ?? [] as $product)
                                            @foreach($product ?? [] as $item)
                                                <tr>
                                                    <td>{{$item['product_id']}}</td>
                                                    <td>{{$item['sale_date']->format('m/d/Y')}}</td>
                                                    <td class="text-capitalize">{{\App\Enums\ProductTypeEnum::getText($item['product_type'])}}</td>
                                                    <td>
                                                        <div class="product-type">
                                                            <h6 class="product-type-title">{{$item['name']}}</h6>
                                                            @if(!empty($item['variant']))
                                                                <div class="product-type-inner mt-2">
                                                                    <div class="product-type-item">
                                                                        <span class="product-type-item-para">{{$item['variant']['color']}}</span>
                                                                        <span class="product-type-item-para">{{$item['variant']['size']}}</span>
                                                                    </div>

                                                                    @foreach($item['variant']['attributes'] as $attribute_name => $attribute_vale)
                                                                        <div class="product-type-item mt-1">
                                                                            <span class="product-type-item-para">{{$attribute_name}}: {{$attribute_vale}}</span>
                                                                        </div>
                                                                    @endforeach
                                                                </div>
                                                            @endif
                                                        </div>

                                                    </td>
                                                    <td>{{$item['qty']}}</td>
                                                    <td>{{amount_with_currency_symbol($item['cost'])}}</td>
                                                    <td>{{amount_with_currency_symbol($item['price'])}}</td>
                                                    <td>{{amount_with_currency_symbol($item['profit'])}}</td>
                                                </tr>
                                            @endforeach
                                        @empty
                                            <tr>
                                                <td class="text-center" colspan="8">{{__('No Data Available')}}</td>
                                            </tr>
                                        @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="pagination mt-4">
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
                </div>
            </div>
        </div>
    </div>
@endsection
