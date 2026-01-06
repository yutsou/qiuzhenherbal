@extends('layouts.admin')
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
        <div class="uk-card uk-card-default uk-card-body">
            <h1 class="uk-card-title">{{ $head }}#{{ $order->id }}
                - {!! $shopPresenter->presentOrderStatus($order->delivery_status, $order->payment_status) !!}@if(isset($order->refund_remark))
                    - {{ $order->refund_remark }}@endif</h1>
            <table class="uk-table uk-table-divider">
                <tbody>
                <tr>
                    <td class="uk-width-small">姓名</td>
                    <td class="uk-table-expand">{{ $order->receiver_name }}</td>
                    <td class="uk-width-small">電話</td>
                    <td class="uk-table-expand">{{ $order->receiver_cell_phone }}</td>
                </tr>
                <tr>
                    <td class="uk-width-small">E-Mail</td>
                    <td class="uk-table-expand">{{ $order->receiver_email }}</td>
                </tr>
                @if($order->logistics_type === "home-delivery")
                    <tr>
                        <td class="uk-width-small">地址</td>
                        <td>{{ $order->zip_code }} {{ $order->county }} {{ $order->district }} {{ $order->address }}</td>
                    </tr>
                @else
                    <tr>
                        <td class="uk-width-small">超商資訊</td>
                        <?php $subLogisticsTransformedList = ['UNIMARTC2C' => '7-11', 'FAMIC2C' => '全家']; ?>
                        <td>{{ $subLogisticsTransformedList[$order->logistics_sub_type] }}
                            - {{ $order->cvs_store_name }} ({{ $order->cvs_store_id }}
                            ) @if($order->delivery_status == 0)<a id="printCvsOrder" class="custom-link">列印托運單</a>@endif
                        </td>
                    </tr>
                @endif
                <tr>
                    <td>取貨方式</td>
                    <?php $logisticsTransformedList = ['CVS' => '超商取貨', 'home-delivery' => '宅配']; ?>
                    <td>{{ $logisticsTransformedList[$order->logistics_type] }}</td>
                    <td>付款方式</td>
                    <?php $paymentMethodTransformedList = ['CVSPay' => '超商取貨付款', 'creditCard' => '信用卡', 'COD' => '貨到付款', 'linePay' => 'Line Pay', 'pointPay' => '點數付款', 'noPayment' => '不用付款']; ?>
                    <td>
                        {{ $paymentMethodTransformedList[$order->payment_method] }}
                    </td>
                </tr>
                </tbody>
            </table>
            <table class="uk-table uk-table-divider">
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
                            <td>NT${{ number_format($orderItem->sku_discount_price) }}</td>
                            <td>{{ $orderItem->quantity }}</td>
                            <td>NT${{ number_format($orderItem->subtotal) }}</td>
                        </tr>
                        @foreach($orderItem->sku->product->groupItems as $groupItem)
                            <tr>
                                <td style="padding-left: 4em;"> - {{ $groupItem->sku->product->name }}</td>
                                <td></td>
                                <td>{{ $orderItem->quantity*$groupItem->quantity }}</td>
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
                <div class="uk-width-2-3">
                    <label>備註：</label>
                    <p>
                        {{ $order->remark }}
                    </p>
                </div>
                <div class="uk-width-1-3">
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
    <div class="uk-margin uk-align-right">
        @switch($order->delivery_status)
            @case(0)
                @if($order->payment_status !== 1)
                    <a class="uk-button uk-button-default" href="#order-cancel-modal-center" uk-toggle>取消訂單</a>
                    <div id="order-cancel-modal-center" class="uk-flex-top" uk-modal>
                        <div class="uk-modal-dialog uk-modal-body uk-margin-auto-vertical">
                            <button class="uk-modal-close-default" type="button" uk-close></button>
                            <p>確定取消訂單嗎？</p>
                            <div class="uk-text-right">
                                <form method="POST" action="{{ route('admin.orders.cancel', ['orderId'=>$order->id]) }}">
                                    @csrf
                                    <button class="uk-button uk-button-default uk-modal-close" type="button">取消</button>
                                    <button class="uk-button custom-button-1">確定</button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endif
                @if($order->payment_status !== 0)
                    <a class="uk-button custom-button-1" href="#shipment-confirm-modal-center" uk-toggle>通知出貨</a>
                    <div id="shipment-confirm-modal-center" class="uk-flex-top" uk-modal>
                        <div class="uk-modal-dialog uk-modal-body uk-margin-auto-vertical">
                            <button class="uk-modal-close-default" type="button" uk-close></button>
                            <p>確定通知出貨嗎？</p>
                            <form method="POST" action="{{ route('admin.orders.update', ['orderId'=>$order->id]) }}">
                                @csrf
                                <div class="uk-text-right">
                                    <button class="uk-button uk-button-default uk-modal-close" type="button">取消</button>
                                    <button type="submit" class="uk-button custom-button-1">確定</button>
                                </div>
                            </form>
                        </div>
                    </div>
                @endif
            @break
            @case(1)
                <form method="POST" action="{{ route('admin.orders.update', ['orderId'=>$order->id]) }}">
                    @csrf
                    <button class="uk-button custom-button-1">進入退款流程</button>
                </form>
            @break
            @case(2)
                <form method="POST" action="{{ route('admin.orders.update', ['orderId'=>$order->id]) }}">
                    @csrf
                    <button class="uk-button custom-button-1">完成訂單</button>
                </form>
            @break
            @case(3)
                <form method="POST" action="{{ route('admin.orders.update', ['orderId'=>$order->id]) }}">
                    @csrf
                    <div class="uk-grid-small" uk-grid>
                        <div class="uk-form-controls">
                            <select class="uk-select" id="refundMethod" name="refundMethod">
                                <option value="other">其他退款方式</option>
                                <option value="point" {{ ($order->user_id === null ? 'disabled' : '') }}>點數退款</option>
                                <option value="linePay" {{ ($order->payment_method === 'linePay' ? '' : 'disabled') }}>LINE Pay 退款</option>
                            </select>
                        </div>
                        <div>
                            <input type="text" class="uk-input" name="refund_remark" placeholder="退款備註"
                                   id="refundRemark">
                            <input type="number" class="uk-input" name="refundAmount" placeholder="退款數值"
                                   id="refundAmount" hidden disabled>
                        </div>
                        <div>
                            <button class="uk-button custom-button-1">退款</button>
                        </div>
                    </div>
                </form>
            @break
        @endswitch
    </div>
@endsection
@push('scripts')
    <script>
        $(function () {
            $('#printCvsOrder').click(function () {
                window.open('{{ route('admin.orders.print', $order) }}', '_blank');
            })

            $('#refundMethod').change(function () {
                if ($(this).val() === 'point' || $(this).val() === 'linePay') {
                    $('#refundRemark').prop('hidden', true);
                    $('#refundRemark').prop('disabled', true);
                    $('#refundAmount').prop('hidden', false);
                    $('#refundAmount').prop('disabled', false);
                } else {
                    $('#refundAmount').prop('hidden', true);
                    $('#refundAmount').prop('disabled', true);
                    $('#refundRemark').prop('hidden', false);
                    $('#refundRemark').prop('disabled', false);
                }
            });
        });
    </script>
@endpush
