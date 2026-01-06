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
            <table class="uk-table uk-table-responsive uk-table-divider uk-table-middle">
                <thead>
                <tr>
                    <th>編號</th>
                    <th>日期</th>
                    <th>狀態</th>
                    <th>總計</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                @foreach($orders as $order)
                    <tr>
                        <td>#{{ $order->id }}</td>
                        <td>{{ $order->created_at }}</td>
                        <td>{!! $shopPresenter->presentOrderStatus($order->delivery_status, $order->payment_status) !!}</td>
                        <td>NT${{  number_format($order->total)  }}</td>
                        <td><a href="{{ route('account.orders.show', $order) }}" class="uk-button custom-button-1">查看</a></td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        <div class="uk-margin">
            <ul class="uk-pagination uk-flex-center" uk-margin>
                <li><a href="{{ $orders->previousPageUrl() }}"><span uk-pagination-previous></span></a></li>
                <li><a href="{{ $orders->nextPageUrl() }}"><span uk-pagination-next></span></a></li>
            </ul>
        </div>
    </div>
@endsection
