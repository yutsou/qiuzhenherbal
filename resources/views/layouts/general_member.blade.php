<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <title>{{ $title ?? env("APP_NAME") }}</title>
        <!-- version: {{ $version = '28' }} -->

        <!-- Meta -->
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="theme-color" content="#ee782e">
        <!-- <meta name="google-site-verification" content="jVJPFMw8D5dbN-Q8cPCgMJZTJPBVcy1JQ-58dyZX-Wc"> -->
        <meta name="description" content="{{ $description ?? '' }}">
        <!-- CSRF Token -->
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <!-- Styles -->
        <link rel="stylesheet" href="{{ asset('uikit-3.13.10/css/uikit.min.css') }}" />
        <link rel="stylesheet" href="{{ asset('css/custom.css') }}?v={{$version}}" />
        <link href="//cdn.jsdelivr.net/npm/@sweetalert2/theme-material-ui@5.0.3/material-ui.min.css" rel="stylesheet">
        <link rel="icon" href="/images/web/common/icon.png">
        @stack('style')

        <!-- Scripts -->
        <script src="{{ asset('uikit-3.13.10/js/uikit.min.js') }}"></script>
        <script src="{{ asset('uikit-3.13.10/js/uikit-icons.min.js') }}"></script>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/js-cookie@3.0.1/dist/js.cookie.min.js"></script>
        <script src="{{ asset('js/shop.js') }}?v={{$version}}"></script>
        <script src="//cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.js"></script>
        @if(app()->environment('production'))
            <!-- Meta Pixel Code -->
<script>
    !function(f,b,e,v,n,t,s)
    {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
    n.callMethod.apply(n,arguments):n.queue.push(arguments)};
    if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
    n.queue=[];t=b.createElement(e);t.async=!0;
    t.src=v;s=b.getElementsByTagName(e)[0];
    s.parentNode.insertBefore(t,s)}(window, document,'script',
    'https://connect.facebook.net/en_US/fbevents.js');
    fbq('init', '1279540406746602');
    fbq('track', 'PageView');
    </script>
    <noscript><img height="1" width="1" style="display:none"
    src="https://www.facebook.com/tr?id=1279540406746602&ev=PageView&noscript=1"
    /></noscript>
    <!-- End Meta Pixel Code -->
        @endif
        @stack('scripts')
    </head>
    <body>
        <div class="uk-visible@l">
            <div class="uk-navbar-container uk-visible@l" uk-navbar style="background-color: #fff; padding-top: 20px; padding-bottom: 10px">
                <div class="uk-navbar-center">
                    <a href="/" class="uk-navbar-item uk-logo"><img data-src="/images/web/common/logo-gold.png" alt="" uk-img style="height: 80px;"></a>
                </div>
            </div>
            <div class="uk-container-expand" style="background-color: #fff; box-shadow: 0 8px 6px -6px #ced4da;" uk-sticky>
                <nav class="uk-container" uk-navbar >
                    <div class="uk-navbar-left">
                        <ul class="uk-navbar-nav">
                            <li>
                                <a href="#" style="font-size: 1em;">關於求真草本</a>
                                <div class="uk-navbar-dropdown">
                                    <ul class="uk-nav uk-navbar-dropdown-nav">
                                        <li><a href="/about-us">品牌理念</a></li>
                                        <li><a href="/contract-us">聯絡我們</a></li>
                                    </ul>
                                </div>
                            </li>
                            <li>
                                <a href="#" style="font-size: 1em;">產品系列</a>
                                <div class="uk-navbar-dropdown">
                                    <ul class="uk-nav uk-navbar-dropdown-nav">
                                        <li class="uk-nav-header">一般食品</li>
                                        <li class="uk-nav-divider"></li>
                                        <li><a href="{{ route('shop.categories_products.index', ['categoryId'=>2]) }}">隨身包系列</a></li>
                                        <li><a href="{{ route('shop.categories_products.index', ['categoryId'=>3]) }}">全素食膠囊</a></li>
                                        <li class="uk-nav-header">日常保養</li>
                                        <li class="uk-nav-divider"></li>
                                        <li><a href="{{ route('shop.categories_products.index', ['categoryId'=>5]) }}">舒緩呵護系列</a></li>
                                        <li class="uk-nav-header">臉部、身體清潔</li>
                                        <li class="uk-nav-divider"></li>
                                        <li><a href="{{ route('shop.categories_products.index', ['categoryId'=>7]) }}">冷製手工皂系列</a></li>
                                        <li class="uk-nav-header">美妝保養</li>
                                        <li class="uk-nav-divider"></li>
                                        <li><a href="{{ route('shop.categories_products.index', ['categoryId'=>9]) }}">沙棘系列</a></li>
                                        <li class="uk-nav-header">所有商品</li>
                                        <li class="uk-nav-divider"></li>
                                        <li><a href="{{ route('shop.products.index') }}">所有商品</a></li>
                                    </ul>
                                </div>
                            </li>
                            <li>
                                <a href="#" style="font-size: 1em;">優惠活動</a>
                                <div class="uk-navbar-dropdown">
                                    <ul class="uk-nav uk-navbar-dropdown-nav">
                                        <li><a href="{{ route('shop.categories_products.index', ['categoryId'=>11]) }}">活動特惠</a></li>
                                        <li><a href="{{ route('shop.categories_products.index', ['categoryId'=>12]) }}">本月特惠</a></li>
                                        <li><a href="{{ route('shop.categories_products.index', ['categoryId'=>13]) }}">組合特惠</a></li>
                                    </ul>
                                </div>
                            </li>
                            <li>
                                <a href="/media-reports" style="font-size: 1em;">媒體報導</a>
                            </li>
                        </ul>
                    </div>
                    <div class="uk-width-expand" uk-navbar>
                        <div class="uk-navbar-center ">
                            <form method="POST" action="{{ route('shop.products.search') }}" class="uk-flex uk-flex-middle" style="gap: 10px;">
                                @csrf
                                <input class="uk-input" 
                                    type="text" 
                                    name="keyword"
                                    style="flex:1; min-width: 380px;">
                                <button class="uk-button custom-color-group-1">搜尋</button>
                            </form>
                        </div>
                    </div>
                    <div class="uk-navbar-right">
                        <ul class="uk-navbar-nav">
                            @guest
                                <li>
                                    <a href="{{ route('login.show') }}" style="font-size: 1em;">會員登入</a>
                                </li>
                                <li><a href="{{ route('shop.cart.show') }}" style="font-size: 1em;" id="guestCart">購物車 (<span class="guestProductQuantity">0</span>)</a></li>
                            @endguest
                            @auth
                                <li>
                                    <a href="#" style="font-size: 1em;">{{ Auth::user()->name }}，您好</a>
                                    <div class="uk-navbar-dropdown">
                                        <ul class="uk-nav uk-navbar-dropdown-nav">
                                            <li><a href="/dashboard" style="font-size: 1.2em;">會員中心</a></li>
                                            <li>
                                                <form method="POST" action="{{ route('logout') }}">
                                                    @csrf
                                                    <a href="{{ route('logout') }}" onclick="event.preventDefault(); this.closest('form').submit();" style="font-size: 1.2em; color: #999;">登出</a>
                                                </form>
                                            </li>
                                        </ul>
                                    </div>
                                </li>
                                <li><a href="{{ route('shop.cart.show') }}" style="font-size: 1em;">購物車 (<span class="cartItemsQuantity">{{ Auth::user()->carts->count() }}</span>)</a></li>
                            @endauth
                        </ul>
                    </div>
                </nav>
            </div>
        </div>
        <div class="uk-hidden@l">
            <div style="box-shadow: 0 8px 6px -6px #ced4da; z-index: 980;" uk-sticky>
                <div class="uk-container-expand" style="background-color: #fff;">
                    <nav class="uk-container" uk-navbar>
                        <div class="uk-navbar-left">
                        </div>
                        <div class="uk-navbar-center">
                            <a href="/" class="uk-navbar-item uk-logo uk-text-center"><img data-src="/images/web/common/logo-gold.png" alt="" uk-img style="height: 60px;"></a>
                        </div>

                        <div class="uk-navbar-right">
                            <a class="uk-navbar-toggle" uk-navbar-toggle-icon href="#toggle-animation" uk-toggle="target: #toggle-animation; animation: uk-animation-fade" style="margin-right: 10px;"></a>
                        </div>
                    </nav>


                    <div id="toggle-animation" class="uk-card uk-card-default uk-card-body" hidden>
                        <ul class="uk-nav-default" uk-nav>
                            <li class="uk-padding" >
                                <form method="POST" action="{{ route('shop.products.search') }}" class="uk-grid-small" uk-grid>
                                    @csrf
                                    <div class="uk-width-expand">
                                        <input class="uk-input" type="text" name="keyword" placeholder="搜尋商品">
                                    </div>
                                    <div class="uk-width-auto">
                                        <button class="uk-button custom-button-1">搜尋</button>
                                    </div>
                                </form>
                            </li>
                        </ul>
                        <ul class="uk-nav-default uk-nav-parent-icon uk-nav-left" uk-nav>
                            <li class="uk-parent">
                                <a href="#">關於求真草本</a>
                                <ul class="uk-nav-sub">
                                    <li><a href="/about-us">品牌理念</a></li>
                                    <li><a href="/contract-us">聯絡我們</a></li>
                                </ul>
                            </li>
                            <li class="uk-parent">
                                <a href="#">產品系列</a>
                                <ul class="uk-nav-sub">
                                    <li class="uk-nav-header">一般食品</li>
                                    <li class="uk-nav-divider"></li>
                                    <li style="padding-left: 60px"><a href="{{ route('shop.categories_products.index', ['categoryId'=>2]) }}">隨身包系列</a></li>
                                    <li style="padding-left: 60px"><a href="{{ route('shop.categories_products.index', ['categoryId'=>3]) }}">全素食膠囊</a></li>
                                    <li class="uk-nav-header">日常保養</li>
                                    <li class="uk-nav-divider"></li>
                                    <li style="padding-left: 60px"><a href="{{ route('shop.categories_products.index', ['categoryId'=>5]) }}">舒緩呵護系列</a></li>
                                    <li class="uk-nav-header">臉部、身體清潔</li>
                                    <li class="uk-nav-divider"></li>
                                    <li style="padding-left: 60px"><a href="{{ route('shop.categories_products.index', ['categoryId'=>7]) }}">冷製手工皂系列</a></li>
                                    <li class="uk-nav-header">美妝保養</li>
                                    <li class="uk-nav-divider"></li>
                                    <li style="padding-left: 60px"><a href="{{ route('shop.categories_products.index', ['categoryId'=>9]) }}">沙棘系列</a></li>
                                    <li class="uk-nav-header">所有商品</li>
                                    <li class="uk-nav-divider"></li>
                                    <li style="padding-left: 60px"><a href="{{ route('shop.products.index') }}">所有商品</a></li>
                                </ul>
                            </li>
                            <li class="uk-parent">
                                <a href="#">優惠活動</a>
                                <ul class="uk-nav-sub">
                                    <li><a href="{{ route('shop.categories_products.index', ['categoryId'=>11]) }}">活動特惠</a></li>
                                    <li><a href="{{ route('shop.categories_products.index', ['categoryId'=>12]) }}">本月特惠</a></li>
                                    <li><a href="{{ route('shop.categories_products.index', ['categoryId'=>13]) }}">組合特惠</a></li>
                                </ul>
                            </li>
                            <li class="uk-nav-divider"></li>
                            @auth
                                <li class="uk-nav-header">{{ Auth::user()->name }}，您好</li>
                                <li><a href="/dashboard">會員中心</a></li>
                                <li><a href="{{ route('shop.cart.show') }}" style="font-size: 1em;">購物車 (<span class="cartItemsQuantity">{{ Auth::user()->carts->count() }}</span>)</a></li>
                            @endauth
                            @guest
                                <li><a href="{{ route('login.show') }}">會員登入</a></li>
                                <li><a href="{{ route('shop.cart.show') }}" id="guestCart">購物車 (<span class="guestProductQuantity">0</span>)</a></li>
                            @endguest
                        </ul>

                        @auth
                            <ul class="uk-nav-default uk-nav-center" uk-nav style="padding-top: 2em;">
                                <li class="uk-active">
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <a href="{{ route('logout') }}" onclick="event.preventDefault(); this.closest('form').submit();" class="custom-link">登出</a>
                                    </form>
                                </li>
                            </ul>
                        @endauth

                    </div>
                </div>
            </div>
        </div>

        @yield('sub-content')

        <div class="uk-section">
            <div class="uk-container">
                @yield('content')
            </div>
        </div>


        <div class="uk-hidden@m">
            <div style="box-shadow: 0 -8px 6px -6px #ced4da;" class="uk-position-bottom uk-position-fixed">
                <div class="uk-container-expand" style="background-color: #fff;">
                    <div class="uk-child-width-1-3 uk-text-center" uk-grid>
                        <div>
                            <a href="/">
                                <div style="padding-bottom: 24px; padding-top: 24px;">
                                    <img data-src="{{ asset('images/web/common/home.png') }}" width="32" height="32" alt="" uk-img>
                                </div>
                            </a>
                        </div>
                        <div>
                            <a href="/account/dashboard">
                                <div style="padding-bottom: 24px; padding-top: 24px;">
                                    <img data-src="{{ asset('images/web/common/user.png') }}" width="32" height="32" alt="" uk-img>
                                </div>
                            </a>
                        </div>
                        <div>
                            <a href="/cart">
                                <div style="padding-bottom: 24px; padding-top: 24px;">
                                    <img data-src="{{ asset('images/web/common/cart.png') }}" width="32" height="32" alt="" uk-img>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div style="box-shadow: 0 -8px 6px -6px #ced4da">
            <div class="uk-section uk-transparent uk-height-large" style="background-color: #fff;">
                <div class="uk-container">
                    <div class="uk-margin-large-bottomg">
                        <a href="/"><img data-src="/images/web/common/logo.png" alt="" uk-img  class="uk-align-center" style="height: 80px;"></a>
                    </div>
                    <div class="uk-margin uk-text-center">
                        <div class="uk-child-width-1-4@m" uk-grid>
                            <div>
                                <p class="uk-text-center custom-color-1" style="line-height: 2em; letter-spacing: 2px; font-family: cwTeXKai; font-size: 1.1em;">
                                    求真草本<br>
                                    自然之道 平衡之理<br>
                                    集天地之靈<br>
                                    養肌膚之根 合草木之精<br>
                                    孕肌膚之本<br>
                                    美麗根源 求真草本<br>
                                    反璞歸真<br>
                                </p>
                            </div>
                            <div>
                                <label style="color: #333;">客戶須知</label>
                                <ul class="uk-list uk-link-text">
                                    <li><a href="/privacy-policy">隱私政策</a></li>
                                    <li><a href="/terms">會員服務條款</a></li>
                                    <li><a href="anti-fraud">防止詐騙、165反詐騙</a></li>
                                </ul>
                            </div>
                            <div>
                                <label style="color: #333;">購物須知</label>
                                <ul class="uk-list uk-link-text">
                                    <li><a href="/payment-method">付款方式</a></li>
                                    <li><a href="/delivery-method-and-fee-calculation">配送方式與運費計算</a></li>
                                    <li><a href="/invoice-processing-procedure">發票處理程序</a></li>
                                    <li><a href="/return-or-exchange-and-refund-method">退換貨及退款方式</a></li>
                                </ul>
                            </div>
                            <div>
                                <label style="color: #333;">聯絡我們</label>
                                <ul class="uk-list">
                                    <li>服務專線：08-7796578</li>
                                    <li>服務時間：周一至周五 - 9:00 ~ 17:00</li>
                                    <li>信箱：service@qiuzhenherbal.com.tw</li>
                                    <li>地址：912 屏東縣內埔鄉昇華路206-1號</li>
                                </ul>
                                <hr>
                                <div class="uk-child-width-1-2@m uk-grid-match" uk-grid>
                                    <div>
                                        <a href="https://www.facebook.com/%E6%B1%82%E7%9C%9F%E8%8D%89%E6%9C%ACQiuzhen-Herbal-1697362140520972/" target="_bank">
                                            <img data-src="{{ asset('images/web/common/facebook-icon-64x64.png') }}" width="64" height="64" uk-img>
                                        </a>
                                    </div>
                                    <div>
                                        <a href="https://line.me/ti/p/HtEf7FiGh1" target="_bank">
                                            <img data-src="{{ asset('images/web/common/line_qrcode.jpeg') }}" width="96" height="96" uk-img>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="uk-padding">
                        <div class="uk-text-center">© {{ now()->year }} 求真草本 Qiuzhen&Herbal</div>
                        <hr>
                        <ul class="uk-list uk-text-center">
                            <li>營業人名稱：鍾慶展</li>
                            <li>統一編號：54798242</li>
                        </ul>
                    </div>
                    <div class="uk-hidden@m" style="padding-bottom: 40px"></div>
                </div>
            </div>
        </div>
    </body>
</html>
