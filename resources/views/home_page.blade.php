@extends('layouts.general_member')
@inject('shopPresenter', 'App\Presenters\ShopPresenter')
@section('sub-content')

    <div class="uk-text-center">
        <div class="uk-visible@m">
            <div uk-slideshow="animation: slide; min-height:480; max-height: 960">

                <div class="uk-position-relative uk-visible-toggle uk-light" tabindex="-1">

                    <ul class="uk-slideshow-items">
                        <li>
                            <img src="{{ asset("/images/web/banners/banner_mothers_day_v4.jpg") }}">
                        </li>
                        <li>
                            <img src="{{ asset("/images/web/banners/banner-4-2.jpg") }}">
                        </li>
                        <li>
                            <img src="{{ asset("/images/web/banners/banner-3-2.jpg") }}">
                        </li>
                        <li>
                            <img src="{{ asset("/images/web/banners/banner-2-1.jpg") }}">
                        </li>
                        <li>
                            <img src="{{ asset("/images/web/banners/banner-1-1.jpg") }}">
                        </li>
                    </ul>

                    <a class="uk-position-center-left uk-position-small uk-hidden-hover" href="#" uk-slidenav-previous uk-slideshow-item="previous"></a>
                    <a class="uk-position-center-right uk-position-small uk-hidden-hover" href="#" uk-slidenav-next uk-slideshow-item="next"></a>

                </div>

                <ul class="uk-slideshow-nav uk-dotnav uk-flex-center uk-margin"></ul>

            </div>
        </div>
        <div class="uk-hidden@m">
            <div uk-slideshow="animation: slide; min-height:580; max-height: 780">

                <div class="uk-position-relative uk-visible-toggle uk-light" tabindex="-1">

                    <ul class="uk-slideshow-items">
                        <li>
                            <img src="{{ asset("/images/web/banners/banner_mothers_day_v4-m.jpg") }}">
                        <li>
                            <img src="{{ asset("/images/web/banners/banner-4-2-m.jpg") }}">
                        </li>
                        <li>
                            <img src="{{ asset("/images/web/banners/banner-3-2-m.jpg") }}">
                        </li>
                        <li>
                            <img src="{{ asset("/images/web/banners/banner-2-1-m.jpg") }}">
                        </li>
                        <li>
                            <img src="{{ asset("/images/web/banners/banner-1-1-m.jpg") }}">
                        </li>
                    </ul>

                    <a class="uk-position-center-left uk-position-small uk-hidden-hover" href="#" uk-slidenav-previous uk-slideshow-item="previous"></a>
                    <a class="uk-position-center-right uk-position-small uk-hidden-hover" href="#" uk-slidenav-next uk-slideshow-item="next"></a>

                </div>

                <ul class="uk-slideshow-nav uk-dotnav uk-flex-center uk-margin"></ul>

            </div>
        </div>
    </div>



@endsection

@section('content')
    <script>
        $(function() {
            if(localStorage.getItem("show_announce5_again") === null)
            {
                UIkit.modal('#modal-sections').show()
            }
            $('#understand').click( function(){
                if($('#check_show_again').prop("checked") === true){
                    localStorage.setItem("show_announce5_again", "false");
                }
            });
            $('.new-products').click( function(){
                let productId = $(this).attr('product-id');
                window.location.assign('/products/'+productId);
            });
        });
    </script>
    <div id="modal-sections" uk-modal>
        <div class="uk-modal-dialog">
            <button class="uk-modal-close-default" type="button" uk-close></button>
            <div class="uk-modal-header">
                <h2 class="uk-modal-title">求真草本官網公告</h2>
            </div>
            <div class="uk-modal-body">
                <ul>
                    <li>
                        即刻起 註冊新會員 即享有 100元優惠卷。
                    </li>
                    <li>
                        會員享有當月壽星生日券
                    </li>
                    <li>
                        訂單免運費條件調整至 NT$999
                    </li>
                </ul>
            </div>
            <div class="uk-modal-footer uk-text-right">
                <label><input type="checkbox" id="check_show_again">不再顯示</label>
                &nbsp;
                <a id="understand" class="uk-button custom-button-1 uk-modal-close">了解</a>
            </div>
        </div>
    </div>
    <div class="uk-margin">
        <a class="custom-link">
            <div class="uk-card uk-card-default uk-card-body">
                <div class="uk-position-relative uk-visible-toggle uk-light" tabindex="-1" uk-slideshow="min-height: 470; max-height: 500; animation: fade; autoplay: true">
                    <ul class="uk-slideshow-items">
                        @foreach($newCategory->products->where('visible', 1) as $product)
                        <li>
                            <div class="uk-child-width-1-2@m" uk-grid>
                                <div>
                                    <h3 class="uk-card-title">新品上市 - {{ $product->name }}</h3>
                                    <p class="custom-color-1">{{ $product->short_description }}</p>
                                </div>
                                <div class="new-products" product-id="{{ $product->id }}">
                                    <img src="{{ $product->image_url }}" alt="" uk-scrollspy="cls: uk-animation-kenburns; repeat: true">
                                </div>
                            </div>
                        </li>
                        @endforeach
                    </ul>

                    <a class="uk-position-center-left uk-position-small uk-hidden-hover" href="#" uk-slidenav-previous uk-slideshow-item="previous"></a>
                    <a class="uk-position-center-right uk-position-small uk-hidden-hover" href="#" uk-slidenav-next uk-slideshow-item="next"></a>

                </div>
            </div>
        </a>
    </div>
    <div class="uk-margin">
        <div class="uk-card uk-card-default uk-card-body">
            <h3 class="uk-card-title">優惠商品</h3>
            <div class="uk-child-width-1-2@s uk-grid-medium uk-grid-match" uk-grid>
                <div>
                    <div class="uk-card uk-card-body">

                        <h3 class="uk-card-title">其他優惠</h3>
                        <ul class="uk-list">
                            <li>
                                訂單滿999免運費（外島訂單滿5000免運費）
                            </li>
                            <li>
                                生日發放優惠券
                            </li>
                        </ul>
                    </div>
                </div>
                <div>
                    <div class="uk-visible@m">
                        <div uk-slideshow="animation: slide; min-height: 500;">
                            <div class="uk-position-relative uk-visible-toggle uk-light" tabindex="-1">
                                <ul class="uk-slideshow-items">
                                    @foreach($saleCategory->children->pluck('products')->flatten()->where('visible', 1)->unique('id')->chunk(2) as $products)
                                        <li>
                                            <div style="background-color: #fff;">
                                                <div class="uk-child-width-1-2 uk-grid-small uk-grid-match" uk-grid>
                                                    @foreach($products as $product)
                                                        <div>
                                                            <div class="uk-card uk-text-center">
                                                                <a href="{{ route('shop.products.show', $product) }}" style="text-decoration: none;">
                                                                    <div class="uk-card-media-top">
                                                                        <img data-src="{{ $product->image_url }}" alt="" uk-img>
                                                                    </div>
                                                                    <div class="uk-card-body">
                                                                        <h3 class="uk-card-title uk-text-default">{{ $product->name }}</h3>
                                                                        <p class="uk-text-small custom-color-1">{{ $product->short_description }}</p>
                                                                        {!! $shopPresenter->presentTags($product) !!}
                                                                        {!! $shopPresenter->indexPresentPrice($product) !!}
                                                                    </div>
                                                                </a>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                            <ul class="uk-slideshow-nav uk-dotnav uk-flex-center uk-margin"></ul>
                        </div>
                    </div>
                    <div class="uk-hidden@m">
                        <div class="uk-slider-container-offset" uk-slider="finite: true">

                            <div class="uk-position-relative uk-visible-toggle uk-light" tabindex="-1">

                                <ul class="uk-slider-items uk-child-width-1-2@s uk-grid-medium uk-grid-match" uk-grid>
                                    @foreach($saleCategory->children->pluck('products')->flatten()->where('visible', 1)->unique('id') as $product)
                                        <li>
                                            <div class="uk-card uk-card-default uk-text-center">
                                                <a href="{{ route('shop.products.show', $product) }}" style="text-decoration: none;">
                                                    <div class="uk-card-media-top">
                                                        <img data-src="{{ $product->image_url }}" alt="" uk-img>
                                                    </div>
                                                    <div class="uk-card-body">
                                                        <h3 class="uk-card-title uk-text-default">{{ $product->name }}</h3>
                                                        <p class="uk-text-small custom-color-1">{{ $product->short_description }}</p>
                                                        {!! $shopPresenter->presentTags($product) !!}
                                                        {!! $shopPresenter->indexPresentPrice($product) !!}
                                                    </div>
                                                </a>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>

                            <ul class="uk-slider-nav uk-dotnav uk-flex-center uk-margin"></ul>

                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
    <div class="uk-margin">
        <div class="uk-card uk-card-default uk-card-body">
            <div class="uk-card-title">熱賣商品</div>
            <div class="uk-margin">
                <div class="uk-visible@m">
                    <div uk-slideshow="animation: slide; min-height: 500;">
                        <div class="uk-position-relative uk-visible-toggle uk-light" tabindex="-1">
                            <ul class="uk-slideshow-items">
                                @foreach($hotCategory->products->where('visible', 1)->chunk(4) as $products)
                                    <li>
                                        <div style="background-color: #fff;">
                                            <div class="uk-child-width-1-4 uk-grid-small uk-grid-match" uk-grid>
                                                @foreach($products as $product)
                                                    <div>
                                                        <div class="uk-card uk-text-center ">
                                                            <a href="{{ route('shop.products.show', $product) }}" style="text-decoration: none;">
                                                                <div class="uk-card-media-top">
                                                                    <img data-src="{{ $product->image_url }}" alt="" uk-img>
                                                                </div>
                                                                <div class="uk-card-body">
                                                                    <h3 class="uk-card-title uk-text-default">{{ $product->name }}</h3>
                                                                    <p class="uk-text-small custom-color-1">{{ $product->short_description }}</p>
                                                                    {!! $shopPresenter->presentTags($product) !!}
                                                                    {!! $shopPresenter->indexPresentPrice($product) !!}
                                                                </div>
                                                            </a>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                        <ul class="uk-slideshow-nav uk-dotnav uk-flex-center uk-margin"></ul>
                    </div>
                </div>
                <div class="uk-hidden@m">
                    <div class="uk-slider-container-offset" uk-slider="finite: true">

                        <div class="uk-position-relative uk-visible-toggle uk-light" tabindex="-1">

                            <ul class="uk-slider-items uk-child-width-1-4@s uk-grid uk-grid-match uk-grid-small">
                                @foreach($hotCategory->products->where('visible', 1) as $product)
                                    <li>
                                        <div class="uk-card uk-card-default uk-text-center product-card">
                                            <a href="{{ route('shop.products.show', $product) }}" style="text-decoration: none;">
                                                <div class="uk-card-media-top">
                                                    <img data-src="{{ $product->image_url }}" alt="" uk-img>
                                                </div>
                                                <div class="uk-card-body">
                                                    <h3 class="uk-card-title uk-text-default">{{ $product->name }}</h3>
                                                    <p class="uk-text-small custom-color-1">{{ $product->short_description }}</p>
                                                    {!! $shopPresenter->presentTags($product) !!}
                                                    {!! $shopPresenter->indexPresentPrice($product) !!}
                                                </div>
                                            </a>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                        <ul class="uk-slider-nav uk-dotnav uk-flex-center uk-margin"></ul>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="uk-margin" hidden>
        <a>
            <div class="uk-card uk-card-default uk-text-center">
                <div class="uk-inline">
                    <div class="uk-overflow-hidden">
                        <img src="{{ asset('images/web/common/group_1.jpg') }}" uk-scrollspy="cls: uk-animation-kenburns; repeat: true">
                    </div>
                    <div class="uk-overlay uk-light uk-position-bottom">
                        <p style="color: #666;">沙棘保養套組</p>
                    </div>
                </div>
            </div>
        </a>
    </div>
@endsection
