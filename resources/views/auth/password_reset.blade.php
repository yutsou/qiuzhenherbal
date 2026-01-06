@extends('layouts.general_member')

@section('content')
    <div class="uk-card uk-card-default uk-card-body">
        <div class="uk-margin-medium">
            <h1 class="uk-heading-medium">{{ $head }}</h1>
        </div>
        <form class="uk-form-stacked" method="POST" action="{{ route('password.set') }}" enctype="multipart/form-data">
            @csrf
            <input type="text" name="userId" value="{{ $user->id }}" hidden>
            <div class="uk-width-1-2@l">
                <div class="uk-margin">
                    <label class="uk-form-label" for="password">新的密碼</label>
                    <div class="uk-form-controls">
                        <input class="uk-input" id="password" type="password" name="password" autocomplete="new-password" required>
                    </div>
                </div>
                <div class="uk-margin">
                    <label class="uk-form-label" for="passwordConfirmation">重複新的密碼</label>
                    <div class="uk-form-controls">
                        <input class="uk-input" id="passwordConfirmation" type="password" name="password_confirmation" autocomplete="new-password" required>
                    </div>
                </div>
                @if ($errors->any())
                    <div class="uk-margin">
                        @foreach ($errors->all() as $error)
                            <div class="uk-alert-warning" uk-alert>
                                <a class="uk-alert-close" uk-close></a>
                                <p>{{ $error }}</p>
                            </div>
                        @endforeach
                    </div>
                @endif
                <div class="uk-margin">
                    <button type="submit" class="uk-button custom-color-group-1">重置密碼</button>
                </div>
            </div>
        </form>
    </div>
@endsection
