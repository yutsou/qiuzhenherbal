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
                <div class="uk-text-center" style="border-bottom: 2px solid #ee782e;">
                    {{ $head }}
                </div>
            </div>
            <div>
                <div class="uk-text-center" style="border-bottom: 1px solid #cccccc;">
                    訂單確認
                </div>
            </div>
        </div>
        <form method="POST" action="{{ route('shop.delivery_detailed.submit') }}" id="deliveryDetailed">
            @csrf
            <input type="number" name="deliveryFee" id="deliveryFee" value="0" hidden>
            <div class="uk-margin">
                <div class="uk-grid-small uk-grid-match" uk-grid>
                    <div class="uk-width-expand@m">
                        <div class="uk-card uk-card-default uk-card-body">
                            <h2 class="uk-card-title">選擇運送方式</h2>
                            <div class="uk-margin">
                                <label><select class="uk-select" id="logisticsType" name="logisticsType">
                                    <option value="home-delivery">宅配</option>
                                    <option value="CVS" id="CVS">超商取貨</option>
                                </select></label>
                            </div>
                            <div class="uk-margin" id="shipmentMethodSubField" hidden>
                                <label><select class="uk-select" id="logisticsSubType" name="logisticsSubType">
                                    <option selected value>-- 選擇超商 --</option>
                                    <option value="UNIMARTC2C">7-11</option>
                                    <option value="FAMIC2C">全家</option>
                                </select></label>
                            </div>
                        </div>
                    </div>
                    <div class="uk-width-expand@m">
                        <div class="uk-card uk-card-default uk-card-body">
                            <h2 class="uk-card-title">選擇付款方式</h2>
                            <div class="uk-margin">
                                <ul id="paymentMethod" class="uk-list">
                                    <li class="paymentMethod" id="CVSPayDisplay" hidden>
                                        <label><input class="uk-radio" type="radio" id="CVPay" name="paymentMethod" value="CVSPay"> 超商取貨付款</label>
                                    </li>
                                    <li class="paymentMethod" id="CODDisplay">
                                        <input class="uk-radio" type="radio" id="COD" name="paymentMethod" value="COD"> <label id="CODLabel" for="COD">貨到付款</label>
                                    </li>
                                    <li class="paymentMethod">
                                        <label><input class="uk-radio" type="radio" name="paymentMethod" value="creditCard"> 信用卡</label>
                                    </li>
                                    <li class="paymentMethod" >
                                        <label><input class="uk-radio" type="radio" name="paymentMethod" value="linePay"> <img data-src="{{ asset('images/web/common/LINE-Pay(h)_W61_n.png') }}" uk-img></label>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="uk-margin">
                <div class="uk-card uk-card-default uk-card-body">
                    <div class="uk-form-stacked">
                        <div class="uk-margin">
                            <div uk-grid>
                                <div class="uk-width-2-5@m">
                                    <label class="uk-form-label" for="receiverName">收件人姓名</label>
                                    <div class="uk-form-controls">
                                        <input class="uk-input" id="receiverName" type="text" name="receiverName" placeholder="輸入您的中文姓名" value="{{ Auth::user()->name ?? null }}" autocomplete="name">
                                    </div>
                                </div>
                                <div class="uk-width-expand@m">
                                    <label class="uk-form-label" for="receiverCellPhone">收件人電話</label>
                                    <div class="uk-form-controls">
                                        <input class="uk-input" id="receiverCellPhone" type="text" name="receiverCellPhone" placeholder="輸入您的行動電話號碼，以09開頭" value="{{ Auth::user()->phone ?? null }}" autocomplete="tel">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="uk-margin">
                            <label class="uk-form-label" for="email">收件人電子信箱</label>
                            <div class="uk-form-controls">
                                <input class="uk-input" id="email" type="email" name="receiverEmail" placeholder="輸入您的電子信箱" value="{{ Auth::user()->email ?? null }}" autocomplete="email">
                            </div>
                        </div>
                        <div id="addressField">
                            <div class="uk-margin">
                                <label class="uk-form-label">收件人縣市、鄉鎮</label>
                                <div class="uk-grid-small uk-child-width-1-3 uk-form-controls twzipcode" uk-grid>
                                    <div data-role="county" data-style="uk-select" data-name="county" data-value="{{ Auth::user()->county ?? null }}"></div>
                                    <div data-role="district" data-style="uk-select" data-name="district" data-value="{{ Auth::user()->district ?? null }}"></div>
                                    <div data-role="zipcode" data-style="uk-select" data-name="zipcode" data-value="{{ Auth::user()->zip_code ?? null }}"></div>
                                </div>
                                <div class="uk-margin">
                                    <label class="uk-form-label" for="address">街道地址</label>
                                    <div class="uk-form-controls">
                                        <input class="uk-input" type="text" id="address" name="address" placeholder="輸入您的地址" value="{{ Auth::user()->address ?? null }}" autocomplete="street-address">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="uk-margin">
                <div class="uk-child-width-1-3@m uk-flex-right" uk-grid>
                    <div>
                        <div class="uk-card uk-card-default uk-card-body">
                            <div class="uk-text-right">
                                運費：NT$<span id="deliveryFeeDisplay"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="uk-margin uk-align-right">
                <a class="uk-button uk-button-large custom-button-1" id="validCheckout">確認訂單</a>
            </div>
        </form>
    </div>
@endsection
@push('scripts')
    <script src="{{ asset('js/jQuery-TWzipcode/twzipcode.js') }}"></script>
    <script>
        let flashAll = function(county) {
            flashDeliveryFee(county);
            flashField();
        }

        let flashDeliveryFee = function (county) {
            let logisticsType = $('#logisticsType').val();
            let cartSubtotal = parseInt({{ $cartSubtotal }});
            let deliveryFee = 0;

            if(logisticsType === 'home-delivery') {
                if(county === '') {
                    if(cartSubtotal >= 999) {
                        deliveryFee = 0;
                    } else {
                        deliveryFee = 100;
                    }
                } else {
                    if(county === '金門縣' || county === '連江縣' || county === '澎湖縣') {
                        if(cartSubtotal >= 5000) {
                            deliveryFee = 0;
                        } else {
                            deliveryFee = 320;
                        }
                    } else {
                        if(cartSubtotal >= 999) {
                            deliveryFee = 0;
                        } else {
                            deliveryFee = 100;
                        }
                    }
                }
            } else {//CVS
                if(cartSubtotal >= 999) {
                    deliveryFee = 0;
                } else {
                    deliveryFee = 60;
                }
            }
            $('#deliveryFee').val(deliveryFee);
            $('#deliveryFeeDisplay').text(number_format(deliveryFee));
        }

        let flashField = function() {
            let cartSubtotal = parseInt({{ $cartSubtotal }});
            let logisticsType = $('#logisticsType').val();

            if(logisticsType === 'home-delivery') {
                $('#CODDisplay').prop('hidden', false);
                $('#addressField').prop('hidden', false);
                $('#shipmentMethodSubField').prop('hidden', true)
                $('#CVSPayDisplay').prop('hidden', true);
            } else {
                $('#shipmentMethodSubField').prop('hidden', false)
                $('#CVSPayDisplay').prop('hidden', false);
                $('#CODDisplay').prop('hidden', true);
                $('#addressField').prop('hidden', true);
            }

            if(cartSubtotal > 5000)
            {
                $('#CVS').prop('disabled', true);
                $('#CVS').text('大於5,000，超商取貨不可用');
            }
            if(cartSubtotal > 10000) {
                $('#CVS').prop('disabled', true);
                $('#CVS').text('大於10,000，超商取貨不可用');
                $('#COD').prop('disabled', true);
                $('#CODLabel').text('大於10,000，貨到付款不可用');
            }
        }
    </script>
    <script>
        $(function() {
            const twzipcode = new TWzipcode(".twzipcode",
                {
                    'county': {
                        'css'      : 'uk-select',
                        'onSelect' : function (e) { // change 事件
                            // HTMLSelectElement
                            flashAll(this.value);
                        }
                    },
                    'district': {
                        'css'      : 'uk-select',
                    },
                    'zipcode': {
                        'css'      : 'uk-input',
                        'readonly' : true,
                    },
                }
            );

            flashAll(twzipcode.get('county')[0]);

            $('#logisticsType').change(function(){
                /*twzipcode.set({
                    'county': '',
                    'district': ''
                });*/
                let county = twzipcode.get('county').value;
                flashAll(county);
            });

            $('#validCheckout').click(function() {
                let inputData = $('#deliveryDetailed').serializeArray();
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: "post",
                    url: '{{ route('ajax.delivery_detailed.valid') }}',
                    data: inputData,
                    success: function (data) {
                        if (typeof (data.error) !== 'undefined') {
                            let errors = data.error.join('<br>');
                            Swal.fire({
                                icon: 'warning',
                                html: errors,
                                showConfirmButton: false,
                                timer: 1500
                            })
                        } else {
                            $('#deliveryDetailed').submit();
                        }
                    },
                });
            });
        });
    </script>
@endpush
