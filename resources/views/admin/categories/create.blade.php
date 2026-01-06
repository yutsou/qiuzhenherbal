@extends('layouts.admin')

@section('content')
    <div class="uk-margin-medium">
        <h1 class="uk-heading-medium">{{ $head }}</h1>
    </div>
    <form class="uk-form-stacked" method="POST" action="{{ route('admin.categories.store') }}" enctype="multipart/form-data">
        @csrf
        <div class="uk-width-1-2">
            <div class="uk-margin">
                <label class="uk-form-label" for="name">分類名稱</label>
                <div class="uk-inline uk-form-controls uk-width-1-1">
                    <span class="uk-form-icon" uk-icon="icon: thumbnails"></span>
                    <input type="text" class="uk-input" name="name" value="{{ old('name') }}" required>
                </div>
            </div>
            <div class="uk-margin">
                <label class="uk-form-label" for="urlName">分類英文名稱</label>
                <div class="uk-inline uk-form-controls uk-width-1-1">
                    <span class="uk-form-icon" uk-icon="icon: thumbnails"></span>
                    <input type="text" class="uk-input" id="urlName" name="url_name" value="{{ old('url_name') }}" required>
                </div>
            </div>
            <div class="uk-margin">
                <label class="uk-form-label" for="parentId">上層分類</label>
                <div class="uk-form-controls">
                    <select class="uk-select" id="parentId" name="parent_id">
                        <option value="0">無上層類別</option>
                        @foreach($rootCategories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="uk-margin">
                <button type="submit" class="uk-button custom-color-group-1">建立</button>
            </div>
        </div>
    </form>
@endsection
