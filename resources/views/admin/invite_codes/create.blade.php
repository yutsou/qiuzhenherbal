@extends('layouts.admin')

@section('content')
    <div class="uk-margin-medium">
        <h1 class="uk-heading-medium">{{ $head }}</h1>
    </div>
    <form class="uk-form-stacked" method="POST" action="{{ route('admin.invite_codes.store') }}" enctype="multipart/form-data">
        @csrf
        <div class="uk-width-1-2">
            <div class="uk-margin">
                <label class="uk-form-label">邀請碼持有人名稱</label>
                <div class="uk-inline uk-form-controls uk-width-1-1">
                    <span class="uk-form-icon" uk-icon="icon: user"></span>
                    <input type="text" class="uk-input" name="holder_name" value="{{ old('holder_name') }}" required>
                </div>
            </div>
            <div class="uk-margin">
                <label class="uk-form-label">邀請碼</label>
                <div class="uk-inline uk-form-controls uk-width-1-1">
                    <span class="uk-form-icon" uk-icon="icon: thumbnails"></span>
                    <input type="text" class="uk-input" name="code" value="{{ old('code') }}" required>
                </div>
            </div>
            <div class="uk-margin">
                <label class="uk-form-label">折扣價格</label>
                <div class="uk-inline uk-form-controls uk-width-1-1">
                    <span class="uk-form-icon" uk-icon="icon:  tag"></span>
                    <input type="text" class="uk-input" name="discount" value="{{ old('discount') }}" required>
                </div>
            </div>
            <div class="uk-margin">
                <button type="submit" class="uk-button custom-color-group-1">建立</button>
            </div>
        </div>
    </form>
@endsection
