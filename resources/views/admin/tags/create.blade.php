@extends('layouts.admin')

@section('content')
    <div class="uk-margin-medium">
        <h1 class="uk-heading-medium">{{ $head }}</h1>
    </div>
    <form class="uk-form-stacked" method="POST" action="{{ route('admin.tags.store') }}" enctype="multipart/form-data">
        @csrf
        <div class="uk-width-1-2">
            <div class="uk-margin">
                <label class="uk-form-label">標籤名稱</label>
                <div class="uk-inline uk-form-controls uk-width-1-1">
                    <span class="uk-form-icon" uk-icon="icon: thumbnails"></span>
                    <input type="text" class="uk-input" name="name" value="{{ old('name') }}" required>
                </div>
            </div>
            <div class="uk-margin">
                <label class="uk-form-label">標籤顏色</label>
                <div class="uk-inline uk-form-controls uk-width-1-1">
                    <span class="uk-form-icon" uk-icon="icon: paint-bucket"></span>
                    <input type="color" class="uk-input" name="color" value="#EE782E" required>
                </div>
            </div>
            <div class="uk-margin">
                <button type="submit" class="uk-button custom-color-group-1">建立</button>
            </div>
        </div>
    </form>
@endsection
