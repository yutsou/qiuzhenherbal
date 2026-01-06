<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<html>
<head>
    <title>{{ $title ?? '' }}</title>
<!-- version: {{ $version = '11' }} -->

    <!-- Meta -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Styles -->
    <link rel="stylesheet" href="{{ asset('uikit-3.13.10/css/uikit.min.css') }}"/>
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}?v={{$version}}"/>
    <link href="//cdn.jsdelivr.net/npm/@sweetalert2/theme-material-ui@5.0.3/material-ui.min.css" rel="stylesheet">
    @stack('styles')

<!-- Scripts -->
    <script src="{{ asset('uikit-3.13.10/js/uikit.min.js') }}"></script>
    <script src="{{ asset('uikit-3.13.10/js/uikit-icons.min.js') }}"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://kit.fontawesome.com/311d0e7753.js" crossorigin="anonymous"></script>
    <script src="{{ asset('js/admin.js') }}?v={{$version}}"></script>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.js"></script>
    @stack('scripts')
</head>
<body>
<div uk-grid uk-height-viewport="offset-top: true">
    <div class="uk-width-1-6 uk-background-secondary">
        <div>
            <div class="uk-height-small uk-flex uk-flex-center uk-flex-middle custom-color-group-1"
                 style="font-size: 1.6em"><a href="{{ route('admin.dashboard') }}" class="custom-link-2">管理員中心</a>
            </div>
        </div>
        <ul class="uk-nav uk-nav-default uk-margin-left uk-margin-right uk-text-default" uk-nav>
            <li class="uk-parent {{ ((isset(explode("/", Request::path())[2]) ? explode("/", Request::path())[2] : 'unset') == "orders" ? 'uk-open' : '') }}">
                <a href="#" class="uk-margin-top" style="color: white; font-size: 1.2em;">訂單</a>
                <ul class="uk-nav-sub">
                    <li><a href="{{ route('admin.orders.manage') }}" class="uk-link-muted">管理訂單</a></li>
                    <li><a href="{{ route('admin.orders.index') }}" class="uk-link-muted">全部訂單</a></li>
                </ul>
            </li>
            <li class="uk-parent {{ ((isset(explode("/", Request::path())[2]) ? explode("/", Request::path())[2] : 'unset') == "products" ? 'uk-open' : '') }}">
                <a href="#" class="uk-margin-top" style="color: white; font-size: 1.2em;">商品</a>
                <ul class="uk-nav-sub">
                    <li><a href="{{ route('admin.products.create') }}" class="uk-link-muted">建立商品</a></li>
                    <li><a href="{{ route('admin.products.index') }}" class="uk-link-muted">管理商品</a></li>
                </ul>
            </li>
            <li class="uk-parent {{ ((isset(explode("/", Request::path())[2]) ? explode("/", Request::path())[2] : 'unset') == "coupons" ? 'uk-open' : '') }}">
                <a href="#" class="uk-margin-top" style="color: white; font-size: 1.2em;">優惠券</a>
                <ul class="uk-nav-sub">
                    <li><a href="{{ route('admin.coupons.create') }}" class="uk-link-muted">建立優惠券</a></li>
                    <li><a href="{{ route('admin.coupons.index') }}" class="uk-link-muted">管理優惠券</a></li>
                </ul>
            </li>
            <li class="uk-parent {{ ((isset(explode("/", Request::path())[2]) ? explode("/", Request::path())[2] : 'unset') == "invite-codes" ? 'uk-open' : '') }}">
                <a href="#" class="uk-margin-top" style="color: white; font-size: 1.2em;">邀請碼</a>
                <ul class="uk-nav-sub">
                    <li><a href="{{ route('admin.invite_codes.create') }}" class="uk-link-muted">建立邀請碼</a></li>
                    <li><a href="{{ route('admin.invite_codes.index') }}" class="uk-link-muted">管理邀請碼</a></li>
                </ul>
            </li>
            <li class="uk-parent {{ ((isset(explode("/", Request::path())[2]) ? explode("/", Request::path())[2] : 'unset') == "categories" ? 'uk-open' : '') }}">
                <a href="#" class="uk-margin-top" style="color: white; font-size: 1.2em;">分類</a>
                <ul class="uk-nav-sub">
                    <li><a href="{{ route('admin.categories.create') }}" class="uk-link-muted">建立分類</a></li>
                    <li><a href="{{ route('admin.categories.index') }}" class="uk-link-muted">管理分類</a></li>
                </ul>
            </li>
            <li class="uk-parent {{ ((isset(explode("/", Request::path())[2]) ? explode("/", Request::path())[2] : 'unset') == "tags" ? 'uk-open' : '') }}">
                <a href="#" class="uk-margin-top" style="color: white; font-size: 1.2em;">標籤</a>
                <ul class="uk-nav-sub">
                    <li><a href="{{ route('admin.tags.create') }}" class="uk-link-muted">建立標籤</a></li>
                    <li><a href="{{ route('admin.tags.index') }}" class="uk-link-muted">管理標籤</a></li>
                </ul>
            </li>
            <li class="uk-parent {{ ((isset(explode("/", Request::path())[2]) ? explode("/", Request::path())[2] : 'unset') == "users" ? 'uk-open' : '') }}">
                <a href="#" class="uk-margin-top" style="color: white; font-size: 1.2em;">會員</a>
                <ul class="uk-nav-sub">
                    <li><a href="{{ route('admin.users.index') }}" class="uk-link-muted">查看會員</a></li>
                </ul>
            </li>
            <li class="uk-nav-divider uk-margin"></li>
            <li><a href="/" class="uk-link-muted">回到首頁</a></li>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <li>
                    <a href="{{ route('logout') }}" onclick="event.preventDefault(); this.closest('form').submit();"
                       class="uk-link-muted">登出</a>
                </li>
            </form>
        </ul>
    </div>
    <div class="uk-width-5-6">
        <div class="uk-section">
            <div class="uk-container">
                @yield('content')
            </div>
        </div>
    </div>
</div>
</body>
</html>
