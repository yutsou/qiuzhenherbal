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
            <ul class="uk-list">
                <li>服務電話：08 779 6578</li>
                <li>地址：912屏東縣內埔鄉昇華路206-1號</li>
            </ul>
            <div class="videobox">
                <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d230.17310332802197!2d120.5871403793207!3d22.6250753789209!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x346e233c96b323b7%3A0xc9dda9d30da7df6b!2z5pel5YWJ5piO55Sf54mp56eR5oqA5pyJ6ZmQ5YWs5Y-4!5e0!3m2!1szh-TW!2stw!4v1529621449978" frameborder="0" style="border:0" allowfullscreen></iframe>
            </div>
        </div>
    </div>
@endsection
