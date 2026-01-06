@extends('layouts.admin')

@section('content')
    <div class="uk-margin-medium">
        <h1 class="uk-heading-medium">{{ $head }}</h1>
    </div>
    <div class="uk-margin">
        <h2>待出貨訂單</h2>
        <table class="uk-table uk-table-striped">
            <thead>
            <tr>
                <th>訂單</th>
                <th>日期</th>
                <th>總計</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            @foreach($waitDeliverOrders as $order)
                <tr>
                    <td>#{{ $order->id }} {{ $order->receiver_name }}</td>
                    <td>{{ $order->created_at }}</td>
                    <td>NT${{ number_format($order->total) }}</td>
                    <td><a class="uk-button custom-button-1" href="{{ route('admin.orders.show', $order) }}">查看</a></td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    <div class="uk-margin">
        <h2>已出貨訂單</h2>
        <table class="uk-table uk-table-striped">
            <thead>
            <tr>
                <th>訂單</th>
                <th>日期</th>
                <th>總計</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            @foreach($deliveredOrders as $order)
                <tr>
                    <td>#{{ $order->id }} {{ $order->receiver_name }}</td>
                    <td>{{ $order->created_at }}</td>
                    <td>NT${{ number_format($order->total) }}</td>
                    <td><a class="uk-button custom-button-1" href="{{ route('admin.orders.show', $order) }}">查看</a></td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    <div class="uk-margin">
        <h2>待付款訂單</h2>
        <table class="uk-table uk-table-striped">
            <thead>
            <tr>
                <th>訂單</th>
                <th>日期</th>
                <th>總計</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            @foreach($notPaidOrders as $order)
                <tr>
                    <td>#{{ $order->id }} {{ $order->receiver_name }}</td>
                    <td>{{ $order->created_at }}</td>
                    <td>NT${{ number_format($order->total) }}</td>
                    <td><a class="uk-button custom-button-1" href="{{ route('admin.orders.show', $order) }}">查看</a></td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endsection
