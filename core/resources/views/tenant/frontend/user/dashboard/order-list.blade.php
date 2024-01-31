@extends('tenant.frontend.user.dashboard.user-master')

@section('title')
    {{__('Payment Logs')}}
@endsection

@section('section')
    @if(count($order_list) > 0)
        <div class="table-responsive">
            <!-- Order history start-->
            <div class="order-history-inner">
                <table>
                    <thead>
                    <tr>
                        <th>
                            {{__('Order ID')}}
                        </th>
                        <th>
                            {{__('Date')}}
                        </th>
                        <th>
                            {{__('Status')}}
                        </th>

                        <th>
                            {{__('Amount')}}
                        </th>
                        <th>
                            {{__('Action')}}
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($order_list as $data)
                        <tr class="completed">
                            <td class="order-numb">
                                #{{ $data->id ?? 0 }}
                            </td>
                            <td class="date">
                                {{ $data->created_at->format("d M, Y") }}
                            </td>
                            @php
                                $text_color = ['default' => 'text-dark' ,'success' => 'text-success', 'complete' => 'text-success', 'cancel' => 'text-danger', 'pending' => 'text-warning'];
                            @endphp
                            <td class="status">
                                <p>
                                    <span>{{__('Order Status:')}}</span>
                                    <span class="{{$text_color[$data->status ?? 'default']}}">{{ __($data->status) ?? ""}}</span>
                                </p>
                                <p>
                                    <span>{{__('Payment Status:')}}</span>
                                    <span class="{{$text_color[$data->payment_status ?? 'default']}}">{{__($data->payment_status) ?? ""}}</span>
                                </p>
                            </td>

                            <td class="amount">
                                {{ amount_with_currency_symbol($data->total_amount) }}
                            </td>
                            <td class="table-btn">
                                <div class="d-flex gap-2">
                                    <div class="btn-wrapper">
                                        <a href="{{ route('tenant.user.dashboard.package.order', $data->id) }}" class="btn-default rounded-btn">{{__('Details')}}</a>
                                    </div>

                                    @if($data->status === 'pending')
                                        <div class="btn-wrapper">
                                            <form class="order-cancel-form" action="{{route('tenant.user.dashboard.order.change.status')}}" method="POST">
                                                @csrf
                                                <input type="hidden" name="order_id" value="{{$data->id}}">
                                                <button type="submit" class="btn-default rounded-btn cancel-btn">{{__('Cancel')}} <x-btn.button-loader class="d-none"/></button>
                                            </form>
                                        </div>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            <!-- Order history end-->
        </div>
        <div class="blog-pagination">
            {{ $order_list->links() }}
        </div>
    @else
        <div class="alert alert-warning">{{__('No Order Found')}}</div>
    @endif
@endsection

@section('scripts')
    <script>
        $(document).on('click', '.cancel-btn', function (e) {
            e.preventDefault();

            Swal.fire({
                title: "{{ __('Do you want to cancel this order?') }}",
                showCancelButton: true,
                confirmButtonText: `{{__('Confirm')}}`,
                confirmButtonColor: '#dd3333',
            }).then((result) => {
                if (result.isConfirmed) {
                    let el = $(this);
                    el.find('.loading-icon').removeClass('d-none');
                    el.find('.loading-icon').attr('disabled', true);
                    el.parents('form.order-cancel-form').submit();
                }
            });
        });
    </script>
@endsection
