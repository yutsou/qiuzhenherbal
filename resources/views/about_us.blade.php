@extends('layouts.general_member')

@section('content')
    <div class="uk-margin">
        <div class="uk-width-1-1">
            <a href="/" class="custom-link">首頁</a>
            >
            <a href="{{ url()->current() }}" class="custom-link">{{ $head }}</a>
        </div>
    </div>

    <div class="uk-card uk-card-default uk-card-body">
        <div class="uk-margin-medium">
            <h1 class="uk-card-title">{{ $head }}</h1>
        </div>
        <div class="uk-position-relative uk-visible-toggle uk-light" tabindex="-1" uk-slideshow="autoplay: true; min-height: 550;" style="color: #666;">
            <ul class="uk-slideshow-items">
                <li>
                    <div class="uk-card uk-card-body">
                        <h3 class="uk-card-title">品牌宗旨</h3>
                        <p>
                            以「草本養膚」為主，研發綠色、環保、護生、互助、互利、共生的產品，為了廣大民眾，有更多元的天然草本產品能選擇，我們仍持續研發，以感恩的心，愛護地球，研發出最好的產品，產品堅持選用有機檢驗認證原料，生產過程使用GMP品質系統監視與管理，全產品均在台灣生產製造，確保生產過程及品質，期盼讓更多人得到幫助，進而達成我們的品牌形象、信念及使命。
                        </p>
                    </div>
                </li>
                <li>
                    <div class="uk-card uk-card-body">
                        <h3 class="uk-card-title">品牌理念</h3>
                        <p>
                            古老文化智慧，博大而精深，求真草本為傳承及發揚古老智慧，追求真實天然草本植物對肌膚神奇效果，結合精湛的現代化高科技精密儀器，萃取天然草本植物精華，研發高修護、淨白、淡黑、保濕、抗老等一系列保養品，讓肌膚重返自然健康，滑嫩細緻，透亮而有光澤。<br>
                            <br>
                            我們用傳統東方醫學理論為基礎，利用大自然賦予我們的天然草本植物，嚴選天然草本為基底，調配出對肌膚零負擔的完美配方，以「草本養膚」的理念應用於保養品。
                        </p>
                    </div>
                </li>
                <li>
                    <div class="uk-card uk-card-body">
                        <h3 class="uk-card-title">品牌誕生</h3>
                        <p>
                            <span style="font-size: 1.3em">求真</span>，巧遇儒醫黃宮繡的一卷典籍【本草求真】該書<br>
                            對植物、藥物的型態、性味、功能、主治以及禁忌，記載甚詳<br>
                            對藥物的意義：<br>
                            <br>
                            <span class="uk-padding"></span>無不搜剔靡盡，牽引混說，概為刪除，俾令真處悉見<br>
                            <br>
                            故冠以「求真」之名<br>
                            當我們細細品味 一行字句深深震攝我的目光：<br>
                            <br>
                            <span class="uk-padding"></span>惟求理與病符  藥與病對<br>
                            <br>
                            這正是我們所希望帶給大家的求真精神，故名為「求真草本」
                        <p>
                    </div>
                </li>
                <li>
                    <div class="uk-card uk-card-body">
                        <p>
                            <span style="font-size: 1.3em">求美</span>，是人的天性而最美的容顏<br>
                            應是由內而外自然散發光彩<br>
                            每個女人都有她獨特的清妍素雅<br>
                            在於如何將自信之美喚醒<br>
                            而我們的使命，就是替每個期望獲得美麗的女人們，找尋一種最天然方式<br>
                            並且透過它，讓自己恢復最真、最美的模樣
                        </p>
                    </div>
                </li>
            </ul>
        </div>
    </div>

@endsection
