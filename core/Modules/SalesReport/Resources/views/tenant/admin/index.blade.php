@extends('tenant.admin.admin-master')
@section('title')
    {{__('Sales Dashboard')}}
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

                <div class="row g-3 my-3">
                    <div class="col-lg-6">
                        <div class="card">
                            <div class="card-body">
                                <div class="my-2" id="chart-daily"></div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="card">
                            <div class="card-body">
                                <div class="my-2" id="chart-weekly"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-lg-6">
                        <div class="card">
                            <div class="card-body">
                                <div class="my-2" id="chart-monthly"></div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="card">
                            <div class="card-body">
                                <div class="my-2" id="chart-yearly"></div>
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
@section('scripts')
    <script src="{{global_asset('assets/landlord/admin/js/apexcharts.js')}}"></script>
    <x-datatable.js/>
    <x-table.btn.swal.js/>

    @php
        $today = $today_report;
        $weekly = $weekly_report;
        $monthly = $monthly_report;
        $yearly = $yearly_report;
    @endphp

    <script>
        $(document).ready(function () {
            const chartByToday = () => {
                return {
                    series: [
                        {
                            name: '{{__('Total Sale')}}',
                            data: {{json_encode($today['salesData'])}}
                        },
                        {
                            name: '{{__('Total Revenue')}}',
                            data: {{json_encode($today['revenueData'])}}
                        },
                        {
                            name: '{{__('Total Cost')}}',
                            data: {{json_encode($today['costData'])}}
                        },
                        {
                            name: '{{__('Total Profit')}}',
                            data: {{json_encode($today['profitData'])}}
                        },
                    ],
                    chart: {
                        height: 350,
                        type: 'line',
                        toolbar: {
                            show: false
                        },
                        zoom: {
                            enabled: false
                        }
                    },
                    colors: ['#ff5252', '#0079FF', '#8F43EE', '#22A699'],
                    dataLabels: {
                        enabled: true,
                    },
                    stroke: {
                        curve: 'smooth'
                    },
                    title: {
                        text: '{{__('Today Revenue, Cost and Profit')}}',
                        align: 'left'
                    },
                    grid: {
                        borderColor: '#e7e7e7',
                        row: {
                            colors: ['#f3f3f3', 'transparent'], // takes an array which will be repeated on columns
                            opacity: 0.5
                        },
                    },
                    markers: {
                        size: 1
                    },
                    xaxis: {
                        categories: <?php echo json_encode($today['categories']) ?>,
                        title: {
                            text: '{{__('Time')}}'
                        }
                    },
                    yaxis: {
                        title: {
                            text: '{{__('Amount')}}'
                        },
                        min: 0,
                        max: {{$today['max_value']}}
                    },
                    legend: {
                        position: 'top',
                        horizontalAlign: 'right',
                        floating: true,
                        offsetY: -25,
                        offsetX: -5
                    }
                };
            }
            const chartByWeekly = () => {
                return {
                    series: [
                        {
                            name: '{{__('Total Sale')}}',
                            data: {{json_encode($weekly['salesData'])}}
                        },
                        {
                            name: '{{__('Total Revenue')}}',
                            data: {{json_encode($weekly['revenueData'])}}
                        },
                        {
                            name: '{{__('Total Cost')}}',
                            data: {{json_encode($weekly['costData'])}}
                        },
                        {
                            name: '{{__('Total Profit')}}',
                            data: {{json_encode($weekly['profitData'])}}
                        },
                    ],
                    chart: {
                        height: 350,
                        type: 'line',
                        toolbar: {
                            show: false
                        },
                        zoom: {
                            enabled: false
                        }
                    },
                    colors: ['#ff5252', '#0079FF', '#8F43EE', '#22A699'],
                    dataLabels: {
                        enabled: true,
                    },
                    stroke: {
                        curve: 'smooth'
                    },
                    title: {
                        text: '{{__('Current Week Revenue, Cost and Profit')}}',
                        align: 'left'
                    },
                    grid: {
                        borderColor: '#e7e7e7',
                        row: {
                            colors: ['#f3f3f3', 'transparent'], // takes an array which will be repeated on columns
                            opacity: 0.5
                        },
                    },
                    markers: {
                        size: 1
                    },
                    xaxis: {
                        categories: <?php echo json_encode($weekly['categories']) ?>,
                        title: {
                            text: '{{__('Days')}}'
                        }
                    },
                    yaxis: {
                        title: {
                            text: '{{__('Amount')}}'
                        },
                        min: 0,
                        max: {{$weekly['max_value']}}
                    },
                    legend: {
                        position: 'top',
                        horizontalAlign: 'right',
                        floating: true,
                        offsetY: -25,
                        offsetX: -5
                    }
                };
            }
            const chartByMonth = () => {
                return {
                    series: [
                        {
                            name: '{{__('Total Revenue')}}',
                            data: {{json_encode($monthly['revenueData'])}}
                        },
                        {
                            name: '{{__('Total Cost')}}',
                            data: {{json_encode($monthly['costData'])}}
                        },
                        {
                            name: '{{__('Total Profit')}}',
                            data: {{json_encode($monthly['profitData'])}}
                        },
                    ],
                    chart: {
                        height: 500,
                        type: 'line',
                        toolbar: {
                            show: false
                        },
                        zoom: {
                            enabled: false
                        }
                    },
                    colors: ['#0079FF', '#8F43EE', '#22A699'],
                    dataLabels: {
                        enabled: true,
                    },
                    stroke: {
                        curve: 'smooth'
                    },
                    title: {
                        text: '{{__('Monthly Revenue, Cost and Profit')}}',
                        align: 'left'
                    },
                    grid: {
                        borderColor: '#e7e7e7',
                        row: {
                            colors: ['#f3f3f3', 'transparent'], // takes an array which will be repeated on columns
                            opacity: 0.5
                        },
                    },
                    markers: {
                        size: 1
                    },
                    xaxis: {
                        categories: <?php echo json_encode($monthly['categories']) ?>,
                        title: {
                            text: '{{__('Month')}}'
                        }
                    },
                    yaxis: {
                        title: {
                            text: '{{__('Amount')}}'
                        },
                        min: 0,
                        max: {{$monthly['max_value']}}
                    },
                    legend: {
                        position: 'top',
                        horizontalAlign: 'right',
                        floating: true,
                        offsetY: -25,
                        offsetX: -5
                    }
                };
            }
            const chartByYear = () => {
                return {
                    series: [
                        {
                            name: '{{__('Total Revenue')}}',
                            data: {{json_encode($yearly['revenueData'])}}
                        },
                        {
                            name: '{{__('Total Cost')}}',
                            data: {{json_encode($yearly['costData'])}}
                        },
                        {
                            name: '{{__('Total Profit')}}',
                            data: {{json_encode($yearly['profitData'])}}
                        },
                    ],
                    chart: {
                        height: 500,
                        type: 'line',
                        toolbar: {
                            show: false
                        },
                        zoom: {
                            enabled: false
                        }
                    },
                    colors: ['#0079FF', '#8F43EE', '#22A699'],
                    dataLabels: {
                        enabled: true,
                    },
                    stroke: {
                        curve: 'smooth'
                    },
                    title: {
                        text: '{{__('Yearly Revenue, Cost and Profit')}}',
                        align: 'left'
                    },
                    grid: {
                        borderColor: '#e7e7e7',
                        row: {
                            colors: ['#f3f3f3', 'transparent'], // takes an array which will be repeated on columns
                            opacity: 0.5
                        },
                    },
                    markers: {
                        size: 1
                    },
                    xaxis: {
                        categories: <?php echo json_encode($yearly['categories']) ?>,
                        title: {
                            text: '{{__('Year')}}'
                        }
                    },
                    yaxis: {
                        title: {
                            text: '{{__('Amount')}}'
                        },
                        min: 0,
                        max: {{$yearly['max_value']}}
                    },
                    legend: {
                        position: 'top',
                        horizontalAlign: 'right',
                        floating: true,
                        offsetY: -25,
                        offsetX: -5
                    }
                };
            }

            new ApexCharts(document.querySelector("#chart-daily"), chartByToday()).render();
            new ApexCharts(document.querySelector("#chart-weekly"), chartByWeekly()).render();
            new ApexCharts(document.querySelector("#chart-monthly"), chartByMonth()).render();
            new ApexCharts(document.querySelector("#chart-yearly"), chartByYear()).render();
        });
    </script>
@endsection
