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
            歡迎您來到求真草本 Qiuzhen&Herbal 的網站（以下簡稱本網站），以下為您解說發票處理程序 請您詳閱下列內容：
        </p>
        <br>
        <div>
            <h4>發票</h4>
            <ul class="uk-list uk-list-hyphen">
                <li>本網站開立紙本發票，發票一律隨貨附上，請在收貨開箱時確認是否有發票，如沒收到請聯繫客服：08-7796578。</li>
            </ul>
        </div>
        <br>
        <div>
            <h4>公司統編發票</h4>
            <ul class="uk-list uk-list-hyphen">
                <li>如需三聯式紙本發票，請於結帳時在備註欄註明貴公司抬頭、統一編號。</li>
            </ul>
        </div>
        <br>
        <div>
            <h4>退貨發票</h4>
            <ul class="uk-list uk-list-hyphen">
                <li>請您將商品放回原包裝袋或原紙箱內，連同商品清單、紙本統一發票，隨同退貨商品一起退回。</li>
            </ul>
        </div>
        <br>
        <p>
            <strong>提醒您：</strong>
            <br>每一筆訂單之正本發票僅開立乙次，遺失怒不補發。如因填寫謬誤，請於收到發票當月底前郵局掛號寄回更正。逾期跨月因應政府法令限定，無法更改，敬請見諒！
        </p>
    </div>
</div>
@endsection
