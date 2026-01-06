@extends('layouts.general_member')

@section('content')
    <div class="uk-margin">
        <div class="uk-width-1-1">
            <a href="/" class="custom-link">首頁</a>
            >
            <a href="{{ url()->current() }}" class="custom-link">{{ $head }}</a>
        </div>
    </div>
    <div class="uk-width-1-1@s">
        <div class="uk-card uk-card-default uk-card-body">
            <div class="uk-margin-medium">
                <h1 class="uk-card-title">{{ $head }}</h1>
            </div>
            <p>
                歡迎您來到求真草本 Qiuzhen&Herbal 的網站（以下簡稱本網站），本網站提供以下配貨方式給您選擇，請您詳閱下列內容：
            </p>
            <br>
            <div>
                <h4>超商取貨(全家、7-11便利商店)</h4>
                <ul class="uk-list uk-list-hyphen">
                    <li>商品將以『超商店到店』方式寄出，配送時間提醒商品到齊出貨後約2~3天可送達指定門市，實際到貨日須依超商貨運量為準。</li>
                </ul>
            </div>
            <br>
            <div>
                <h4>宅配(新竹貨運)</h4>
                <ul class="uk-list uk-list-hyphen">
                    <li>商品將以『新竹貨運』宅配方式寄出，宅配範圍目前限於台灣本島地區；外島地區請先詢問客服確認配送地址是否可成立訂單。 配送時間提醒商品到齊出貨後約2~3天可配達，實際到貨日須依宅配貨運量為準。
                    <br>※外島訂單每筆限重20kg，若超過限制將無法成功送出訂單。
                    </li>
                </ul>
            </div>
            <br>
            <div>
                <h4>免運門檻</h4>
                <table class="uk-table uk-table-middle uk-table-divider">
                    <thead>
                    <tr>
                        <th></th>
                        <th></th>
                        <th>未滿 NT$999</th>
                        <th>滿 NT$999</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td rowspan="2" class="uk-text-center">台灣本島</td>
                        <td>超商(全家/7-11)</td>
                        <td>NT$60</td>
                        <td>免運</td>
                    </tr>
                    <tr>
                        <td>宅配</td>
                        <td>NT$100</td>
                        <td>免運</td>
                    </tr>
                    </tbody>
                </table>
                <br>
                <table class="uk-table uk-table-middle uk-table-divider">
                    <thead>
                    <tr>
                        <th></th>
                        <th></th>
                        <th>未滿 NT$5,000</th>
                        <th>滿 NT$5,000</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td rowspan="2" class="uk-text-center">外島地區</td>
                        <td>超商(全家/7-11)</td>
                        <td>NT$60</td>
                        <td>免運</td>
                    </tr>
                    <tr>
                        <td>宅配</td>
                        <td>NT$320</td>
                        <td>免運</td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <br>
            <p>
                <strong>提醒您：</strong>
                <br>單筆結帳金額 超商超過台幣五千元、宅配超過台幣一萬元，無提供貨到付款。請您以「信用卡」、「 <img data-src="{{ asset('images/web/common/LINE-Pay(h)_W61_n.png') }}" uk-img> 」作為結帳選項。
            </p>
        </div>
    </div>
@endsection
