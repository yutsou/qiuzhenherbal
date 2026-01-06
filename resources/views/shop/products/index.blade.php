@extends('layouts.general_member')
@inject('shopPresenter', 'App\Presenters\ShopPresenter')
@section('content')
    <div class="uk-margin">
        <div class="uk-width-1-1">
            <a href="/" class="custom-link">首頁</a>
            >
            <a href="{{ route('shop.products.index') }}" class="custom-link">所有商品</a>
        </div>
    </div>
    <div class="uk-width-1-1@s">
        <div class="uk-card uk-card-default uk-card-body">
            <div class="uk-margin-medium">
                <h1 class="uk-card-title">{{ $head }}</h1>
            </div>
            <div class="uk-child-width-1-4@m uk-grid-medium uk-grid-match" uk-grid>
                @foreach($products as $product)
                    <div>
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
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection
