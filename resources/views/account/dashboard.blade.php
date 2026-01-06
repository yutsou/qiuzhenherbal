@extends('layouts.general_member')

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
@elseif(session('Warning'))
    <script>
        Swal.fire({
            position: 'center',
            icon: 'warning',
            title: '{{session('Warning')}}',
            showConfirmButton: false,
            timer: 1500
        })
    </script>
@endif
<div class="uk-width-1-3@l uk-align-center">
    <div class="uk-card uk-card-default uk-card-body">
        <div class="uk-margin-medium">
            <h1 class="uk-card-title">會員中心</h1>
            <p>點數：{{ Auth::user()->point }}</p>
        </div>
        <div class="uk-child-width-1-1@m uk-grid-medium uk-grid-match" uk-grid>
            <div>
                <div class="uk-card uk-card-body custom-card-color-group-1">
                    <ul class="uk-list uk-link-text uk-list-divider">
                        <li><a href="{{ route('account.profile.edit') }}">帳戶設定</a>
                            @if(Auth::user()->phone == null || Auth::user()->birthday == null || Auth::user()->address == null)
                                <a uk-tooltip="title: 完成資料領取優惠券"><span class="uk-badge" style="background-color: #4c8c74;">!</span></a>
                            @endif
                        </li>
                        @if(Auth::user()->oauth_type === null)

                            <li><a href="{{ route('account.password.edit') }}">更改密碼</a></li>
                        @endif
                        <li><a href="{{ route('account.orders.index') }}">訂單</a></li>
                        <li><a href="{{ route('account.coupons.index') }}">持有的優惠券</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
