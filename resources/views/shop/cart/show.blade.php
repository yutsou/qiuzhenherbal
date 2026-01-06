@extends('layouts.general_member')

@section('content')
    <div class="uk-margin">
        <div class="uk-child-width-expand uk-grid-collapse" uk-grid>
            <div>
                <div class="uk-text-center" style="border-bottom: 2px solid #ee782e;">
                    購物車
                </div>
            </div>
            <div>
                <div class="uk-text-center" style="border-bottom: 1px solid #cccccc;">
                    運費計算
                </div>
            </div>
            <div>
                <div class="uk-text-center" style="border-bottom: 1px solid #cccccc;">
                訂單確認
                </div>
            </div>
        </div>
    </div>
    <div class="uk-width-1-1@s">
        <div class="uk-card uk-card-default uk-card-body">
            <div class="uk-margin-medium">
                <h1 class="uk-card-title">{{ $head }}</h1>
            </div>
            @if($cartExisted === true)
                <table class="uk-table uk-table-responsive uk-table-divider uk-table-middle">
                    <thead>
                    <tr>
                        <th></th>
                        <th class="uk-table-expand">商品</th>
                        <th>單價</th>
                        <th class="uk-table-expand">數量</th>
                        <th>小計</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody id="cartItemsField">
                    @foreach($cartItems as $cartItem)
                        <tr>
                            <td><img data-src="{{ $cartItem['imageUrl'] }}" width="50px" height="50px" uk-img></td>
                            <td>{!! $cartItem['name'] !!}</td>
                            <td class="uk-text-right"><span class="uk-hidden@m">單價：</span>NT$<span class="discountPrices" id="discountPrice-{{ $cartItem['skuId'] }}">{{ number_format($cartItem['discountPrice']) }}</span></td>
                            <td class="uk-text-right">
                                <label><input type="number" class="uk-input uk-text-center quantities" min="1" value="{{ $cartItem['quantity'] }}" skuId="{{$cartItem['skuId']}}"></label>
                            </td>
                            <td class="uk-text-right"><span class="uk-hidden@m">小計：</span>NT$<span id="itemSubtotal-{{ $cartItem['skuId'] }}">{{ number_format($cartItem['productSubtotal']) }}</span></td>
                            <td class="uk-text-nowrap uk-text-right">
                                @auth
                                    <form method="post" action="{{ route('shop.carts.delete', ['skuId'=>$cartItem['skuId']]) }}">
                                        @csrf
                                        <button class="uk-button uk-button-default">移除</button>
                                    </form>

                                @endauth
                                @guest
                                    <a class="uk-button uk-button-default removeCookieCartProduct" skuId="{{ $cartItem['skuId'] }}">移除</a>
                                @endguest
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
                <div class="uk-margin uk-text-right">
                    購物車小計：NT$<span id="cartSubtotal">{{ number_format($cartSubtotal) }}</span>
                </div>
                <div class="uk-margin uk-align-right">
                    <a class="uk-button custom-button-1 product-store" id="validInventory">計算運費</a>
                </div>
            @else
                <div class="uk-margin">
                    <div class="uk-alert-warning" uk-alert>
                        <p>購物車還沒有任何物品，先去逛逛吧！</p>
                    </div>
                </div>
                <div class="uk-margin uk-text-right">
                    <a href="{{ route('shop.products.index') }}" class="uk-button custom-button-1">前往購物</a>
                </div>
            @endif
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        let flashSubtotal = function (skuId, quantity, discountPrice) {
            $('#discountPrice-'+skuId).text(number_format(discountPrice));
            $('#itemSubtotal-'+skuId).text(number_format(parseInt(quantity)*parseInt(discountPrice)));
            let discountPrices = [];
            $('.discountPrices').each(function(){
                discountPrices.push(parseInt($(this).text()));
            });
            let quantities = [];
            $('.quantities').each(function(){
                quantities.push(parseInt($(this).val()));
            });
            let cartSubtotal = 0;
            for (let i=0; i<discountPrices.length; i++) {
                cartSubtotal = cartSubtotal + (quantities[i]*discountPrices[i])
            }
            $('#cartSubtotal').text(number_format(cartSubtotal));
        };

        let changeCookieQuantities = function (skuId, quantity) {
            //get Cookies
            let skuIds = Cookies.get('skuIds');
            let quantities = Cookies.get('quantities');
            //turn to array
            let skuIdsList = skuIds.split(',');
            let quantitiesList = quantities.split(',');
            //find index
            let index = skuIdsList.indexOf(skuId);
            //change quantitiesList quantity
            quantitiesList[index] = quantity;
            let newQuantities = quantitiesList.join(',');
            Cookies.set('quantities', newQuantities);
        };

        let selectQuantityonLoad = function () { //載入頁面時設置數量
            let skuIds = Cookies.get('skuIds');
            let quantities = Cookies.get('quantities');
            //turn to array
            let skuIdsList = skuIds.split(',');
            let quantitiesList = quantities.split(',');
            for (let i=0; i<skuIdsList.length; i++) {
                $('#quantity-'+skuIdsList[i]).val(quantitiesList[i]);
            }
        };

        let removeCookieCartProduct = function (skuId) {
            //get Cookies
            let skuIds = Cookies.get('skuIds');
            let quantities = Cookies.get('quantities');
            //turn to array
            let skuIdsList = skuIds.split(',');
            let quantitiesList = quantities.split(',');
            //find index
            let index = skuIdsList.indexOf(skuId);
            //change quantitiesList quantity
            quantitiesList.splice(index, 1);
            skuIdsList.splice(index, 1);
            if(skuIdsList.length === 0 || quantitiesList.length === 0){
                Cookies.remove('quantities');
                Cookies.remove('skuIds')
            } else {
                let newQuantities = quantitiesList.join(',');
                let newSkuIds = skuIdsList.join(',');
                Cookies.set('quantities', newQuantities);
                Cookies.set('skuIds', newSkuIds);
            }
        }

        $(function() {

            let loggedIn = {{ auth()->check() ? 'true' : 'false' }};

            if(loggedIn === true){
                $('.quantities').change(function() {
                    let skuId = $(this).attr('skuId');
                    let quantity = $(this).val();
                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        type: "post",
                        url: '{{ route('shop.ajax.update_cart') }}',
                        data: {skuId:skuId, quantity:quantity},
                        success: function (discountPrice) {
                            flashSubtotal(skuId, quantity, discountPrice);
                        }
                    });
                });

                $('.removeCartProduct').click(function() {
                    let skuId = $(this).attr('skuId');
                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        type: "post",
                        url: '/ajax/delete/carts/'+skuId,
                        data: {skuId:skuId},
                        success: function () {
                            window.location.assign('/cart');
                        }
                    });
                });
            } else {
                if(typeof Cookies.get('skuIds') != "undefined")
                {
                    selectQuantityonLoad();
                }
                $('.removeCookieCartProduct').click(function() {
                    let skuId = $(this).attr('skuId');
                    removeCookieCartProduct(skuId);
                    window.location.assign('/cart');
                });

                $('.quantities').change(function() {
                    let skuId = $(this).attr('skuId');
                    let quantity = $(this).val();
                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        type: "post",
                        url: '{{ route('shop.ajax.flash_cart') }}',
                        data: {skuId:skuId, quantity:quantity},
                        success: function (discountPrice) {
                            flashSubtotal(skuId, quantity, discountPrice);
                            changeCookieQuantities(skuId, quantity);
                        }
                    });
                });
            }

            $('#validInventory').click(function() {
                let skuIds = [];
                let quantities = [];
                $('.quantities').each(function() {
                    skuIds.push($(this).attr('skuId'));
                    quantities.push($(this).val());
                });
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: "post",
                    url: '{{ route('ajax.inventory.valid') }}',
                    data: {skuIds:skuIds, quantities:quantities},
                    success: function (data) {
                        if (typeof (data.error) !== 'undefined') {
                            let errors = data.error.join('<br>');
                            Swal.fire({
                                icon: 'warning',
                                html: errors,
                                showConfirmButton: false,
                                timer: 2000
                            })
                        } else {
                            window.location.replace('/delivery-fee/calculate');
                        }
                    },
                });

            });
        });
    </script>
@endpush
