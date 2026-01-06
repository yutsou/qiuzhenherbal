@extends('layouts.general_member')
@inject('shopPresenter', 'App\Presenters\ShopPresenter')
@section('content')
    <div class="uk-margin">
        <div class="uk-child-width-expand uk-grid-collapse" uk-grid>
            <div>
                <div class="uk-text-center" style="border-bottom: 1px solid #cccccc;">
                    購物車
                </div>
            </div>
            <div>
                <div class="uk-text-center" style="border-bottom: 1px solid #cccccc;">
                    運費計算
                </div>
            </div>
            <div>
                <div class="uk-text-center" style="border-bottom: 2px solid #ee782e;">
                    訂單確認
                </div>
            </div>
        </div>
    </div>
    <div class="uk-margin">
        <div class="uk-card uk-card-default uk-card-body">
            <h1 class="uk-card-title">{{ $head }}</h1>
            <table class="uk-table uk-table-divider uk-table-responsive">
                <tbody>
                <tr>
                    <td class="uk-width-small">姓名：</td>
                    <td class="uk-table-expand">
                        {{ $deliveryDetailed['receiverName'] }}
                    </td>
                    <td class="uk-width-small">電話：</td>
                    <td class="uk-table-expand">
                        {{ $deliveryDetailed['receiverCellPhone'] }}
                    </td>
                </tr>
                <tr>
                    <td class="uk-width-small">E-Mail：</td>
                    <td class="uk-table-expand">
                        {{ $deliveryDetailed['receiverEmail'] }}
                    </td>
                </tr>
                @if($deliveryDetailed['logisticsType'] === "home-delivery")
                    <tr>
                        <td class="uk-width-small">地址：</td>
                        <td>{{ $deliveryDetailed['zipcode'] }} {{ $deliveryDetailed['county'] }} {{ $deliveryDetailed['district'] }}
                            {{ $deliveryDetailed['address'] }}
                        </td>
                    </tr>
                @else
                    <tr>
                        <td class="uk-width-small">超商資訊：</td>
                        <?php $subLogisticsTransformedList=['UNIMARTC2C'=>'7-11', 'FAMIC2C'=>'全家']; ?>
                        <td>{{ $subLogisticsTransformedList[$deliveryDetailed['logisticsSubType']] }} ({{$deliveryDetailed['CVSStoreName']}})</td>
                    </tr>
                @endif
                <tr>
                    <td>取貨方式：</td>
                    <?php $logisticsTransformedList=['CVS'=>'超商取貨', 'home-delivery'=>'宅配']; ?>
                    <td>{{ $logisticsTransformedList[$deliveryDetailed['logisticsType']] }}</td>
                    <td>付款方式：</td>
                    <?php $paymentMethodTransformedList=['CVSPay'=>'超商取貨付款', 'creditCard'=>'信用卡', 'COD'=>'貨到付款', 'linePay'=>'Line Pay', 'pointPay'=>'點數付款', 'noPayment'=>'不用付款']; ?>
                    <td>
                        {{ $paymentMethodTransformedList[$deliveryDetailed['paymentMethod']] }}
                    </td>
                </tr>
                </tbody>
            </table>
            <table class="uk-table uk-table-responsive uk-table-divider uk-table-middle">
                <thead>
                <tr>
                    <th></th>
                    <th class="uk-table-expand">商品</th>
                    <th>單價</th>
                    <th class="uk-table-expand">數量</th>
                    <th>小計</th>
                </tr>
                </thead>
                <tbody id="cartItemsField">
                @foreach($cartItems as $cartItem)
                    <tr>
                        <td><img data-src="{{ $cartItem['imageUrl'] }}" width="50px" height="50px" uk-img></td>
                        <td><a href="{{ route('shop.products.show', $cartItem['productId']) }}" class="uk-link-text">{{ $cartItem['name'] }}</a></td>
                        <td class="uk-text-left@m uk-text-right"><span class="uk-hidden@m">單價：</span>NT$<span class="discountPrices" id="discountPrice-{{ $cartItem['skuId'] }}">{{ number_format($cartItem['discountPrice']) }}</span></td>
                        <td class="uk-text-left@m uk-text-right"><span class="uk-hidden@m">數量：</span>
                            <span>{{ $cartItem['quantity'] }}</span>
                        </td>
                        <td class="uk-text-left@m uk-text-right"><span class="uk-hidden@m">小計：</span>NT$<span id="itemSubtotal-{{ $cartItem['skuId'] }}">{{ number_format($cartItem['productSubtotal']) }}</span></td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            <hr>
            <div class="uk-child-width-1-3 uk-flex-right" uk-grid>
                <div>
                    <table class="uk-table uk-text-right">
                        <tbody id="totalItemsField">
                        <tr>
                            <td>小計</td>
                            <td>NT$<span id="subtotalDisplay">{{ number_format($cartSubtotal) }}</td>
                        </tr>
                        <tr>
                            <td>運費</td>
                            <td>
                                NT${{ number_format($deliveryDetailed['deliveryFee']) }}
                            </td>
                        </tr>
                        <tr id="inviteCodeDiscountTr" hidden>
                            <td>邀請碼優惠</td>
                            <td>
                                - NT$<span id="inviteCodeDiscountDisplay"></span>
                            </td>
                        </tr>
                        <tr id="couponDiscountTr" hidden>
                            <td>折價券優惠</td>
                            <td>
                                - NT$<span id="couponDiscountDisplay">0</span>
                            </td>
                        </tr>
                        <tr id="pointDiscountTr" hidden>
                            <td>購物金折扣</td>
                            <td>
                                - NT$<span id="pointDiscountDisplay">0</span>
                            </td>
                        </tr>
                        <tr>
                            <td>總計</td>
                            <td>
                                NT$
                                <span id="totalDisplay">{{ number_format($cartSubtotal) }}</span>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <form id="checkoutForm" method="post" action="{{ route('shop.checkout.check_out') }}" enctype= multipart/form-data>
        @csrf
        <div hidden>
            @foreach($cartItems as $cartItem)
                <input type="text" name="skuIds[]" value="{{ $cartItem['skuId'] }}">
                <input type="text" name="itemNames[]" value="{{ $cartItem['name'] }}">
                <input type="number" name="discountPrices[]" value="{{ $cartItem['discountPrice'] }}">
                <input type="number" name="quantities[]" value="{{ $cartItem['quantity'] }}">
                <input type="number" name="productSubtotals[]" value="{{ $cartItem['productSubtotal'] }}">
            @endforeach
            <input type="number" id="subtotal" name="subtotal" value="{{ $cartSubtotal }}">
            <input type="number" id="deliveryFee" name="deliveryFee" value="{{ $deliveryDetailed['deliveryFee'] }}">
            <input type="number" id="inviteCodeDiscount" name="inviteCodeDiscount" value="">
            <input type="number" id="couponDiscount" name="couponDiscount" value="">
            <input type="number" id="pointDiscount" name="pointDiscount" value="">
            <input type="number" id="total" name="total" value="">
            <input type="text" name="logisticsType" value="{{ $deliveryDetailed['logisticsType'] }}">
            <input type="text" name="logisticsSubType" value="{{ $deliveryDetailed['logisticsSubType'] }}">
            <input type="text" id="paymentMethod" name="paymentMethod" value="{{ $deliveryDetailed['paymentMethod'] }}">
            <input type="text" name="receiverName" value="{{ $deliveryDetailed['receiverName'] }}">
            <input type="text" name="receiverCellPhone" value="{{ $deliveryDetailed['receiverCellPhone'] }}">
            <input type="text" name="receiverEmail" value="{{ $deliveryDetailed['receiverEmail'] }}">
            <input type="text" name="county" value="{{ $deliveryDetailed['county'] }}">
            <input type="text" name="district" value="{{ $deliveryDetailed['district'] }}">
            <input type="text" name="zipcode" value="{{ $deliveryDetailed['zipcode'] }}">
            <input type="text" name="address" value="{{ $deliveryDetailed['address'] }}">
            <input type="text" name="cvsStoreId" value="{{ $deliveryDetailed['CVSStoreID'] ?? ''}}">
            <input type="text" name="cvsStoreName" value="{{ $deliveryDetailed['CVSStoreName'] ?? '' }}">
            <input type="text" name="cvsAddress" value="{{ $deliveryDetailed['CVSAddress'] ?? '' }}">

        </div>
        <div class="uk-margin">
            <div class="uk-grid-small uk-grid-match" uk-grid>
                <div class="uk-width-1-3@m">
                    <div class="uk-card uk-card-default uk-card-body">
                        <h2 class="uk-card-title">輸入邀請碼</h2>
                        <input type="text" class="uk-input" placeholder="沒有則無需輸入" id="inviteCodeInput">
                        <input type="text" name="inviteCode" id="inviteCode" value hidden>
                        <div class="uk-margin">
                            <a class="uk-button uk-button-default uk-width-expand" id="getInviteCodeDiscount">取得折扣</a>
                        </div>
                    </div>
                </div>
                @auth
                    <div class="uk-width-1-3@m">
                        <div class="uk-card uk-card-default uk-card-body">
                            <h2 class="uk-card-title">選擇優惠券</h2>
                            <select class="uk-select" id="coupon" name="coupon">
                                <option selected value>-- 選擇優惠券 --</option>
                                @auth
                                    {!! $shopPresenter->presentCoupons(Auth::user()->unusedUnexpiredCoupons) !!}
                                @endauth
                            </select>
                        </div>
                    </div>
                    <div class="uk-width-1-3@m">
                        <div class="uk-card uk-card-default uk-card-body">
                            <h2 class="uk-card-title">購物金折價</h2>
                            <h5>有 {{ Auth::user()->point }} 購物金可以使用</h5>
                            <label><input class="uk-checkbox" type="checkbox" id="pointCheck" disabled> 使用購物金折抵</label>
                        </div>
                    </div>
                @endauth
            </div>
        </div>
        <div class="uk-margin">
            <div class="uk-card uk-card-default uk-card-body">
                <div class="uk-margin">
                    <label class="uk-form-label">備註</label>
                    <div class="uk-form-controls">
                        <textarea class="uk-textarea" rows="5" placeholder="輸入您要備註的事項" name="remark"></textarea>
                    </div>
                </div>
            </div>
        </div>
        <div class="uk-margin uk-align-right">

                <a class="uk-button uk-button-large custom-button-1" id="checkout">結帳</a>

        </div>
    </form>
@endsection
@push('scripts')
    <script>
        let flashTotal = function () {
            let sutotal = $('#subtotal').val();
            let deliveryFee = $('#deliveryFee').val();
            let total = 0;
            total = total + parseInt(sutotal) + parseInt(deliveryFee);
            let inviteCodeDiscount = $('#inviteCodeDiscount').val();
            if (inviteCodeDiscount !== "") {
                total = total - parseInt(inviteCodeDiscount);
            }
            let couponDiscount = $('#couponDiscount').val();
            if (couponDiscount !== "") {
                total = total - parseInt(couponDiscount);
            }
            if(total <= 0) {
                total = 0;
                $('#pointCheck').prop('checked', false)
                $('#pointCheck').prop('disabled', true)
            } else {
                $('#pointCheck').prop('disabled', false)
            }
            flashPoint(total);
            let pointDiscount = $('#pointDiscount').val();
            if (pointDiscount !== "") {
                total = total - parseInt(pointDiscount);
            }
            if(total === 0) {
                noPayment();
            } else {
                needPayment();
            }
            $('#totalDisplay').text(number_format(total));
            $('#total').val(total);
        };
        let noPayment = function (){
            $('#paymentMethod').val('noPayment');
        }
        let needPayment = function (){
            $('#paymentMethod').val('{{ $deliveryDetailed['paymentMethod'] }}');
        }
        let getInviteCodeDiscount = function (inviteCode){
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: "get",
                url: '/ajax/getInviteCodeDiscount/'+inviteCode,
                success: function (discount) {
                    if(discount === 'false') {
                        Swal.fire({
                            position: 'center',
                            icon: 'warning',
                            title: '沒有此邀請碼',
                            showConfirmButton: false,
                            timer: 1500
                        })
                    } else {
                        $('#inviteCodeInput').prop('readonly', true);
                        $('#inviteCode').val(inviteCode);
                        $('#inviteCodeDiscount').val(parseInt(discount));
                        $('#inviteCodeDiscountDisplay').text(number_format(discount));
                        $('#inviteCodeDiscountTr').prop('hidden', false);
                        Swal.fire({
                            position: 'center',
                            icon: 'success',
                            title: '邀請碼使用成功',
                            showConfirmButton: false,
                            timer: 1500
                        })
                    }
                    flashTotal();
                }
            });
        };
        let getCouponDiscount = function (couponDiscount){
            if(couponDiscount !== undefined){
                $('#couponDiscount').val(parseInt(couponDiscount));
                $('#couponDiscountDisplay').text(number_format(parseInt(couponDiscount)));
                $('#couponDiscountTr').prop('hidden', false);
            } else {
                $('#couponDiscountTr').prop('hidden', true);
                $('#couponDiscountDisplay').text(0);
                $('#couponDiscount').val("");
            }
            flashTotal();
        };
        let flashPoint = function (total) {
            if($('#pointCheck').prop('checked') === true) {
                let pointDiscount = $('#pointDiscount');
                let userPoint = parseInt({{ Auth::user()->point ?? 0 }});
                if (userPoint > total) {
                    pointDiscount.val(total);
                    $('#pointDiscountDisplay').text(total);
                } else {
                    pointDiscount.val(userPoint);
                    $('#pointDiscountDisplay').text(userPoint);
                }
                $('#pointDiscountTr').prop('hidden', false);
            } else {
                $('#pointDiscount').val('');
                $('#pointDiscountTr').prop('hidden', true);
                $('#pointDiscountDisplay').text(0);
            }
        }

        $(function() {

            flashTotal();

            $('#getInviteCodeDiscount').click(function(){
                let inviteCode = $('#inviteCodeInput').val();
                getInviteCodeDiscount(inviteCode);
            });
            $('#coupon').change(function(){
                let couponDiscount = $(this).find('option:selected').attr('discount');
                getCouponDiscount(couponDiscount);
            });
            $('#pointCheck').click(function() {
                flashTotal();
            });

            $('#checkout').click(function() {
                let formData = $('#checkoutForm').serialize();
                let url = '{{ route('shop.checkout.check_out') }}';
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: "post",
                    data:formData,
                    url: url,
                    success: function (response) {
                        let type = response[0];
                        let redirectUrl = response[1];
                        if(type === '1') {
                            Swal.fire({
                                position: 'center',
                                icon: 'success',
                                title: '訂單建立成功',
                                showConfirmButton: false,
                            });
                            setTimeout(function(){
                                window.location.replace(redirectUrl);
                            }, 1500)
                        } else if(type === '2') {
                            window.location.replace(redirectUrl);
                            let ajaxActionUrl = response[2];
                            let ajaxReturnUrl = response[3];
                            let intvalId = setInterval(function(){
                                $.ajax({
                                    headers: {
                                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                    },
                                    type: "get",
                                    url: ajaxActionUrl,
                                    success: function (status) {
                                        if(status === '1'){
                                            window.location.replace(ajaxReturnUrl);
                                            clearInterval(intvalId);
                                        }
                                    }
                                });
                            }, 3000);
                        } else {
                            window.location.replace(redirectUrl);
                        }
                    }
                });
            });
        });
    </script>
@endpush
