@extends('layouts.general_member')
@inject('shopPresenter', 'App\Presenters\ShopPresenter')
@section('content')
    @if (session('Success'))
        <script>
            Swal.fire({
                position: 'center',
                icon: 'success',
                title: '{{session('Success')}}',
                showConfirmButton: false,
                timer: 1500
            })
        </script>
    @elseif (session('Error'))
        <script>
            Swal.fire({
                position: 'center',
                icon: 'warning',
                title: '{{session('Error')}}',
                showConfirmButton: false,
                timer: 1500
            })
        </script>
    @endif
    <div class="uk-margin">
        <div class="uk-width-1-1">
            <a href="/" class="custom-link">首頁</a>
            >
            <a href="{{ route('account.dashboard.show') }}" class="custom-link">會員中心</a>
            >
            <a href="{{ route('account.orders.index') }}" class="custom-link">訂單</a>
            >
            <a href="{{ url()->current() }}" class="custom-link">#{{ $order->id }}</a>
        </div>
    </div>
    <div class="uk-margin">
        <div class="uk-card uk-card-default uk-card-body">
            <h1 class="uk-card-title">{{ $head }}#{{ $order->id }} - {!! $shopPresenter->presentOrderStatus($order->delivery_status, $order->payment_status) !!}@if(isset($order->refund_remark)) - {{ $order->refund_remark }}@endif</h1>
            <table class="uk-table uk-table-divider uk-table-responsive">
                <tbody>
                <tr>
                    <td class="uk-width-small">姓名：</td>
                    <td class="uk-table-expand">
                        @if(isset($order->user_id))
                            {{ $order->receiver_name }}
                        @else
                            {!! $shopPresenter->hideInfo($order->receiver_name, 'name') !!}
                        @endif
                    </td>
                    <td class="uk-width-small">電話：</td>
                    <td class="uk-table-expand">
                        @if(isset($order->user_id))
                            {{ $order->receiver_cell_phone }}
                        @else
                            {!! $shopPresenter->hideInfo($order->receiver_cell_phone, 'phone') !!}
                        @endif
                    </td>
                </tr>
                <tr>
                    <td class="uk-width-small">E-Mail：</td>
                    <td class="uk-table-expand">
                        @if(isset($order->user_id))
                            {{ $order->receiver_email }}
                        @else
                            {!! $shopPresenter->hideInfo($order->receiver_email, 'email') !!}
                        @endif
                    </td>
                </tr>
                @if($order->logistics_type === "home-delivery")
                    <tr>
                        <td class="uk-width-small">地址：</td>
                        <td>{{ $order->zip_code }} {{ $order->county }} {{ $order->district }}
                            @if(isset($order->user_id))
                                {{ $order->address }}
                            @else
                                {!! $shopPresenter->hideInfo($order->address, 'address') !!}
                            @endif
                        </td>
                    </tr>
                @else
                    <tr>
                        <td class="uk-width-small">超商資訊：</td>
                        <?php $subLogisticsTransformedList=['UNIMARTC2C'=>'7-11', 'FAMIC2C'=>'全家']; ?>
                        <td>{{ $subLogisticsTransformedList[$order->logistics_sub_type] }} - {{ $order->cvs_store_name }} ({{ $order->cvs_store_id }})</td>
                    </tr>
                @endif
                <tr>
                    <td>取貨方式：</td>
                    <?php $logisticsTransformedList=['CVS'=>'超商取貨', 'home-delivery'=>'宅配']; ?>
                    <td>{{ $logisticsTransformedList[$order->logistics_type] }}</td>
                    <td>付款方式：</td>
                    <?php $paymentMethodTransformedList=['CVSPay'=>'超商取貨付款', 'creditCard'=>'信用卡', 'COD'=>'貨到付款', 'linePay'=>'Line Pay', 'pointPay'=>'點數付款', 'noPayment'=>'不用付款']; ?>
                    <td>
                        {{ $paymentMethodTransformedList[$order->payment_method] }}
                    </td>
                </tr>
                </tbody>
            </table>
            <table class="uk-table uk-table-divider uk-table-responsive">
                <thead>
                <tr>
                    <th class="uk-table-expand">商品</th>
                    <th>單價</th>
                    <th class="uk-table-expand">數量</th>
                    <th>小計</th>
                </tr>
                </thead>
                <tbody>
                @foreach($order->orderItems as $orderItem)
                    @if($orderItem->sku->product->type == 'group')
                        <tr>
                            <td>{{ $orderItem->name }}</td>
                            <td class="uk-text-left@m uk-text-right"><span class="uk-hidden@m">單價：</span>NT${{ number_format($orderItem->sku_discount_price) }}</td>
                            <td class="uk-text-left@m uk-text-right"><span class="uk-hidden@m">數量：</span>{{ $orderItem->quantity }}</td>
                            <td class="uk-text-left@m uk-text-right"><span class="uk-hidden@m">小計：</span>NT${{ number_format($orderItem->subtotal) }}</td>
                        </tr>
                        @foreach($orderItem->sku->product->groupItems as $groupItem)
                            <tr>
                                <td class="uk-padding"> - {{ $groupItem->sku->product->name }}</td>
                                <td></td>
                                <td class="uk-text-left@m uk-text-right"><span class="uk-hidden@m">數量：</span>{{ $orderItem->quantity*$groupItem->quantity }}</td>
                                <td></td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td>{{ $orderItem->name }}</td>
                            <td>NT${{ number_format($orderItem->sku_discount_price) }}</td>
                            <td>{{ $orderItem->quantity }}</td>
                            <td>NT${{ number_format($orderItem->subtotal) }}</td>
                        </tr>
                    @endif

                @endforeach
                </tbody>
            </table>
            <hr>
            <div uk-grid>
                <div class="uk-width-2-3@m">
                    <label>備註：</label>
                    <p>
                        {{ $order->remark }}
                    </p>
                </div>
                <div class="uk-width-1-3@m">
                    <table class="uk-table uk-text-right">
                        <tbody>
                        <tr>
                            <td>小計</td>
                            <td>NT${{ number_format($order->subtotal) }}</td>
                        </tr>
                        <tr>
                            <td>運費</td>
                            <td>NT${{ number_format($order->delivery_fee) }}</td>
                        </tr>
                        @if($order->invite_discount !== null)
                            <tr>
                                <td>邀請碼優惠<br>({{ $order->invite_code }})</td>
                                <td>
                                    - NT${{ number_format($order->invite_discount) }}
                                </td>
                            </tr>
                        @endif
                        @if($order->coupon_discount !== null)
                            <tr>
                                <td>折價券優惠<br>({{ $order->coupon_name }})</td>
                                <td>
                                    - NT${{ number_format($order->coupon_discount) }}
                                </td>
                            </tr>
                        @endif
                        @if($order->point_discount !== null)
                            <tr>
                                <td>購物金折抵</td>
                                <td>
                                    - NT${{ number_format($order->point_discount) }}
                                </td>
                            </tr>
                        @endif
                        <tr>
                            <td>總計</td>
                            <td>NT${{  number_format($order->total)  }}</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>


        </div>
    </div>
    @if($order->payment_status === 0)
        <form method="POST" action="{{ route('shop.repay', $order) }}">
            @csrf
            <div class="uk-margin uk-align-right">
                <button class="uk-button uk-button-large custom-button-1">重新付款</button>
            </div>
        </form>
    @endif
@endsection
