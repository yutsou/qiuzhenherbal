@extends('layouts.general_member')
@inject('shopPresenter', 'App\Presenters\ShopPresenter')
@section('content')
    <div class="uk-margin">
        <div class="uk-width-1-1">
            <a href="/" class="custom-link">首頁</a>
            >
            <a href="{{ route('account.dashboard.show') }}" class="custom-link">會員中心</a>
            >
            <a href="{{ url()->current() }}" class="custom-link">{{ $head }}</a>
        </div>
    </div>
    <div class="uk-card uk-card-default uk-card-body">
        <div class="uk-margin-medium">
            <h1 class="uk-heading-medium">{{ $head }}</h1>
        </div>
        <div class="uk-margin">
            <table class="uk-table uk-table-responsive uk-table-divider">
                <thead>
                <tr>
                    <th>優惠券</th>
                    <th>優惠折扣</th>
                    <th>起始時間</th>
                    <th>結束時間</th>
                </tr>
                </thead>
                <tbody>
                @foreach($coupons as $coupon)
                    <tr>
                        <td>{{ $coupon->name }}</td>
                        <td>NT${{ number_format($coupon->discount_price) }}</td>
                        <td><span class="uk-hidden@m">起始日期：</span>{{ $coupon->startAtDateMin }}</td>
                        <td><span class="uk-hidden@m">截止日期：</span>{{ $coupon->endAtDateMin }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
