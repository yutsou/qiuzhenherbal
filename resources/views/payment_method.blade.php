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
                歡迎您來到求真草本 Qiuzhen&Herbal 的網站（以下簡稱本網站），為讓您購物結帳更加便捷，本網站提供以下幾種不同方式給您選擇，請您詳閱下列內容：
            </p>
            <br>
            <div>
                <h4>貨到付款(超取/宅配)</h4>
                <ul class="uk-list uk-list-disc">
                    <li>超商取貨付款</li>
                    <li>
                        宅配貨到付款
                        <p>
                            當您希望使用［貨到付款］作為您的付款方式時，請於結帳頁面中，先行選擇［運送方式］後，再點選付款方式→［取貨付款］選項確認。
                            <br>
                            <br><strong>提醒您：</strong>
                            <br>單筆結帳金額 超商超過台幣五千元、宅配超過台幣一萬元，無提供貨到付款。 請您以「信用卡」、「 <img data-src="{{ asset('images/web/common/LINE-Pay(h)_W61_n.png') }}" uk-img> 」作為結帳選項。
                        </p>
                    </li>
                </ul>
            </div>
            <br>
            <div>
                <h4>信用卡付款</h4>
                <p>
                    僅支援台灣發行的Visa、Master與JCB信用卡/簽帳金融卡（需有刷卡消費功能）。
                    <br>
                    <br>請於結帳頁面選擇運送方式後，付款方式點選【信用卡】，新增您要使用的信用卡/金融卡後按確認，銀行將進行OTP簡訊驗證，驗證完畢即完成信用卡結帳。
                    <br>
                    <br><strong>提醒您：</strong>
                    <br>新增信用卡時，為驗證您的信用卡資訊， 可能會向您的信用卡進行一次1元授權，此筆紀錄不會請款，請您放心。
                </p>
            </div>
            <br>
            <div>
                <h4><img data-src="{{ asset('images/web/common/LINE-Pay(h)_W61_n.png') }}" uk-img> 付款</h4>
                <p>
                    結帳時選擇 LINE Pay 作為付款方式，頁面跳轉到 LINE Pay 完成付款。
                </p>
            </div>
        </div>
    </div>
@endsection
