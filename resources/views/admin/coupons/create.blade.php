@extends('layouts.admin')

@section('content')
    <div class="uk-margin-medium">
        <h1 class="uk-heading-medium">{{ $head }}</h1>
    </div>
    <form class="uk-form-stacked" method="POST" action="{{ route('admin.coupons.store') }}">
        @csrf
        <div class="uk-width-1-2">
            <div class="uk-margin">
                <label class="uk-form-label" for="name">優惠券名稱</label>
                <div class="uk-inline uk-form-controls uk-width-1-1">
                    <span class="uk-form-icon" uk-icon="icon: info"></span>
                    <input type="text" class="uk-input" name="name" value="{{ old('name') }}" required>
                </div>
            </div>
            <div class="uk-margin">
                <label class="uk-form-label" for="discountPrice">折扣價格</label>
                <div class="uk-inline uk-form-controls uk-width-1-1">
                    <span class="uk-form-icon" uk-icon="icon: tag"></span>
                    <input type="number" class="uk-input" id="discountPrice" name="discount_price" value="{{ old('discount_price') }}" required>
                </div>
            </div>
            <div class="uk-margin">
                <label class="uk-form-label" for="startAt">開始時間</label>
                <div class="uk-inline uk-form-controls uk-width-1-1">
                    <span class="uk-form-icon" uk-icon="icon: calendar"></span>
                    <input type="date" class="uk-input" id="startAt" name="start_at" value="{{ old('start_at') }}" required>
                </div>
            </div>
            <div class="uk-margin">
                <label class="uk-form-label" for="endAt">逾期時間</label>
                <div class="uk-inline uk-form-controls uk-width-1-1">
                    <span class="uk-form-icon" uk-icon="icon: calendar"></span>
                    <input type="date" class="uk-input" id="endAt" name="end_at" value="{{ old('end_at') }}" required>
                </div>
            </div>
            <div class="uk-margin">
                <button type="submit" class="uk-button custom-color-group-1">建立</button>
            </div>
        </div>
    </form>
@endsection
