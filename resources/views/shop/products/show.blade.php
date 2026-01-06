@extends('layouts.general_member')
@inject('shopPresenter', 'App\Presenters\ShopPresenter')
@section('content')
    <div class="uk-margin">
        <div class="uk-width-1-1">
            <a href="/" class="custom-link">首頁</a>
            >
            <a href="{{ route('shop.categories_products.index', $product->mainCategory) }}" class="custom-link">{{ $product->mainCategory->name }}</a>
            >
            <a href="{{ route('shop.products.show', $product) }}" class="custom-link">{{ $product->name }}</a>
        </div>
    </div>
    <div class="uk-card uk-card-default uk-card-body">
        <div class="uk-margin">
            <div class="uk-grid-medium" uk-grid>
                <div class="uk-width-3-5@m">
                    <div class="uk-position-relative" uk-slideshow="animation: fade">
                        <ul class="uk-slideshow-items">
                            <li>
                                <img src="{{ $product->image_url }}" alt="" style="height: 100%;" class="uk-align-center">
                            </li>
                            @foreach($product->otherImages as $image)
                                <li>
                                    <img src="{{ $image->image_url }}" alt="" style="height: 100%;" class="uk-align-center">
                                </li>
                            @endforeach
                        </ul>
                        <div class="uk-margin">
                            <div class="justify-center">
                                <div class="uk-overflow-auto">
                                    <ul class="uk-thumbnav uk-slider-items uk-grid-small" uk-grid>
                                        <li uk-slideshow-item="0"><a href="#"><img src="{{ $product->image_url }}" width="100" alt=""></a></li>
                                        @foreach($product->otherImages as $key=>$image)
                                            <li uk-slideshow-item="{{ $key+1 }}"><a href="#"><img src="{{ $image->image_url }}" width="100" alt=""></a></li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="uk-width-2-5@m">
                    <h1 style="color: #333; font-size: 1.5em;">{{ $head }}</h1>
                    <div class="uk-margin">
                        {{ $product->short_description }}
                    </div>
                    <div class="uk-margin">
                        {!! $shopPresenter->presentTags($product) !!}
                    </div>
                    @if (count($product->skus->first()->discounts) > 1)
                        <div class="uk-margin">
                            <ul class="uk-list">
                                @foreach ($product->skus->first()->discounts as $i=>$discount)
                                    @if($i != count(end($product->skus->first()->discounts))-1)
                                        <li>購買 {{ $discount->min }} - {{ $discount->max }} 個，NT${{ number_format($discount->discount) }}</li>
                                    @else
                                        <li>購買 {{ $discount->min }} 以上，NT${{ number_format($discount->discount) }}</li>
                                    @endif
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <div class="uk-margin">
                        {!! $shopPresenter->presentPrice($product) !!}
                    </div>
                    <div class="uk-margin">
                        <strong><p class="custom-color-1">{{ $product->reminder }}</p></strong>
                    </div>

                    @if($product->skus->first()->inventory_status === 1 || $product->skus->first()->inventory > 0)
                        <div class="uk-margin">
                            <label>購買數量<input type="number" class="uk-input" name="quantity" id="quantity" min="1" value="1"></label>
                        </div>
                    @endif

                    <div class="uk-margin">
                        @if($product->skus->first()->inventory_status === 1 || $product->skus->first()->inventory > 0)
                            <div class="uk-grid-small uk-child-width-1-2@m" uk-grid>
                                @auth
                                    <div>
                                        <a class="uk-button custom-button-2 uk-width-expand" id="addProductToCart" skuId="{{ $product->skus->first()->id }}">加入購物車</a>
                                    </div>
                                    <div>
                                        <a class="uk-button custom-button-1 uk-width-expand" id="directBuy" skuId="{{ $product->skus->first()->id }}">直接購買</a>
                                    </div>
                                @endauth
                                @guest
                                    <div>
                                        <a class="uk-button custom-button-2 uk-width-expand" id="addProductToCookieCart" skuId="{{ $product->skus->first()->id }}">加入購物車</a>
                                    </div>
                                    <div>
                                        <a class="uk-button custom-button-1 uk-width-expand" id="directCookieBuy" skuId="{{ $product->skus->first()->id }}">直接購買</a>
                                    </div>
                                @endguest
                            </div>
                        @elseif($product->skus->first()->inventory_status === 0)
                            <div class="uk-alert-danger" uk-alert>
                                <p style="font-size: 1.2em">已售完</p>
                            </div>
                        @elseif($product->skus->first()->inventory_status === 2)
                            <div class="uk-alert-warning" uk-alert>
                                <p style="font-size: 1.2em">補貨中</p>
                            </div>
                        @endif
                    </div>
                    <div class="uk-margin">
                        <table class="uk-table uk-table-divider uk-table-middle">
                            <tbody>
                            @foreach($product->groupItems as $item)
                                <tr>
                                    <td><img data-src="{{ $item->sku->product->image_url }}" width="40px" height="40px" uk-img></td>
                                    <td><a href="{{ route('shop.products.show', $item->sku->product) }}" class="custom-link">{{ $item->sku->product->name }}</a></td>
                                    <td>x{{ $item->quantity }}</td>
                                    <td>NT${{ number_format($item->quantity * $item->sku->regular_price) }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="uk-margin">
                <div uk-grid>
                    <div class="uk-width-1-5@m">
                        <ul class="uk-tab-left" uk-tab="connect: #component-tab-left; animation: uk-animation-fade">
                            <li><a href="#" style="font-size: 1.2em">商品介紹</a></li>
                            <li><a href="#" style="font-size: 1.2em">商品資訊</a></li>
                        </ul>
                    </div>
                    <div class="uk-width-expand@m">
                        <ul id="component-tab-left" class="uk-switcher">
                            <li>{!! $product->introduction !!}</li>
                            <li>{!! $product->information !!}</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
@push('scripts')
    @if(app()->environment('production'))
        <script>
        // 商品主資料給 Pixel：載入詳情頁就送 ViewContent
        (function() {
            var skuId   = '{{ $product->skus->first()->id }}';
            var name    = @json($product->name);
            var price   = {{ number_format($product->skus->first()->regular_price, 2, '.', '') }}; // 若有特價，改成特價
            var currency= 'TWD';

            if (window.fbq) {
            fbq('track', 'ViewContent', {
                content_ids: [String(skuId)],
                content_name: name,
                content_type: 'product',
                value: price,
                currency: currency
            });
            }
            // 也存到全域，下面加購物車時會用到
            window.__PX_PRODUCT__ = { skuId: skuId, name: name, price: price, currency: currency };
        })();
        </script>
    @endif
@endpush
@push('scripts')
    <script>
        $(function() {
            function trackAddToCart(qty) {
                if (!window.fbq || !window.__PX_PRODUCT__) return;
                var p = window.__PX_PRODUCT__;
                fbq('track', 'AddToCart', {
                content_ids: [String(p.skuId)],
                content_name: p.name,
                content_type: 'product',
                value: Number((p.price * qty).toFixed(2)),
                currency: p.currency,
                contents: [{id: String(p.skuId), quantity: Number(qty)}],
                num_items: Number(qty)
                });
            }

            let handleProductToCookieCart = function(results, skuId, quantity){
                if(results[0] === true) {
                    if (typeof(Cookies.get('skuIds')) === 'undefined') {
                        let skuIds = skuId;
                        let quantities = quantity;
                        Cookies.set('skuIds', skuIds);
                        Cookies.set('quantities', quantities);
                        let guestProductQuantity = $('.guestProductQuantity').first().text();
                        $('.guestProductQuantity').text(parseInt(guestProductQuantity)+1);
                    } else {
                        let skuIds = Cookies.get('skuIds');
                        let quantities = Cookies.get('quantities');
                        let skuIdsList = skuIds.split(',');
                        let quantitiesList = quantities.split(',');
                        if (skuIdsList.includes(skuId)) {
                            let index = skuIdsList.indexOf(skuId);
                            quantitiesList[index] = quantity;
                            let newQuantities = quantitiesList.join(',');
                            Cookies.set('quantities', newQuantities);
                        } else {
                            skuIds = skuIds+','+skuId;
                            quantities = quantities+','+quantity;
                            Cookies.set('skuIds', skuIds);
                            Cookies.set('quantities', quantities);
                            let guestProductQuantity = $('.guestProductQuantity').first().text();
                            $('.guestProductQuantity').text(parseInt(guestProductQuantity)+1);
                        }
                    }
                    return true;
                } else {
                    return false;
                }
            }

            $('#addProductToCookieCart').click(function () {
                let skuId = $(this).attr('skuId');
                let quantity = $("#quantity").val();
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: "post",
                    url: '{{ route('shop.ajax.cookie_carts.store') }}',
                    data: {skuId:skuId, quantity:quantity},
                    success: function (results) {
                        let result = handleProductToCookieCart(results, skuId, quantity);
                        if( result === true) {
                            Swal.fire({
                                position: 'center',
                                icon: 'success',
                                title: '加入成功',
                                showConfirmButton: false,
                                timer: 1500
                            })
                        } else {
                            Swal.fire({
                                position: 'center',
                                icon: 'warning',
                                title: '商品庫存量不足',
                                showConfirmButton: false,
                                timer: 1500
                            })
                        }
                    }
                });

            });

            $('#addProductToCart').click(function(){
                let skuId = $(this).attr('skuId');
                let quantity = $("#quantity").val();
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: "post",
                    url: '{{ route('shop.ajax.carts.store') }}',
                    data: {skuId:skuId, quantity:quantity},
                    success: function (results) {
                        if(results[0] === true) {
                            $('.cartItemsQuantity').text(results[1]);
                            trackAddToCart(quantity);
                            Swal.fire({
                                position: 'center',
                                icon: 'success',
                                title: '加入成功',
                                showConfirmButton: false,
                                timer: 1500
                            })
                        } else {
                            Swal.fire({
                                position: 'center',
                                icon: 'warning',
                                title: '商品庫存量不足',
                                showConfirmButton: false,
                                timer: 1500
                            })
                        }

                    }
                });
            });

            $('#directCookieBuy').click(function(){
                let skuId = $(this).attr('skuId');
                let quantity = $("#quantity").val();
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: "post",
                    url: '{{ route('shop.ajax.cookie_carts.store') }}',
                    data: {skuId:skuId, quantity:quantity},
                    success: function (results) {
                        let result = handleProductToCookieCart(results, skuId, quantity);
                        if( result === true) {
                            window.location.replace('/cart');
                        } else {
                            Swal.fire({
                                position: 'center',
                                icon: 'warning',
                                title: '商品庫存量不足',
                                showConfirmButton: false,
                                timer: 1500
                            })
                        }
                    }
                });
            });

            $('#directBuy').click(function(){
                let skuId = $(this).attr('skuId');
                let quantity = $("#quantity").val();
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: "post",
                    url: '{{ route('shop.ajax.carts.store') }}',
                    data: {skuId:skuId, quantity:quantity},
                    success: function (results) {
                        if(results[0] === true) {
                            $('.cartItemsQuantity').text(results[1]);
                            window.location.replace('/cart');
                        } else {
                            Swal.fire({
                                position: 'center',
                                icon: 'warning',
                                title: '商品庫存量不足',
                                showConfirmButton: false,
                                timer: 1500
                            })
                        }

                    }
                });
            });
        });
    </script>
@endpush
