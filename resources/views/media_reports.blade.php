@extends('layouts.general_member')

@section('content')
    <div class="uk-margin">
        <div class="uk-width-1-1">
            <a href="/" class="custom-link">首頁</a>
            >
            <a href="{{ url()->current() }}" class="custom-link">媒體報導</a>
        </div>
    </div>

    <div class="uk-card uk-card-default uk-card-body">
        <div class="uk-margin-medium">
            <h1 class="uk-card-title">媒體報導</h1>
        </div>
        <div class="videobox">
                <iframe src="https://www.youtube.com/embed/Fu39MblfM7s" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen class="uk-align-center"></iframe>
            </div>
    </div>
   

@endsection
