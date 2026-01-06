@extends('layouts.general_member')

@section('content')
    @if (session('Success'))
        <script>
            Swal.fire({
                position: 'center',
                icon: 'success',
                title: '{{session('Success')}}',
                showConfirmButton: false,
                timer: 1500
            })
        </script>
    @endif
    <div class="uk-margin">
        <div class="uk-width-1-1">
            <a href="/" class="custom-link">首頁</a>
            >
            <a href="{{ route('account.dashboard.show') }}" class="custom-link">會員中心</a>
            >
            <a href="{{ url()->current() }}" class="custom-link">{{ $head }}</a>
        </div>
    </div>
    <div class="uk-card uk-card-default uk-card-body">
        <div class="uk-margin-medium">
            <h1 class="uk-heading-medium">{{ $head }}</h1>
        </div>
        <form class="uk-form-stacked" method="POST" action="{{ route('account.password.update') }}" enctype="multipart/form-data">
            @csrf
            <div class="uk-width-1-2@l">
                <div class="uk-margin">
                    <label class="uk-form-label" for="currentPassword">目前的密碼</label>
                    <div class="uk-form-controls">
                        <input class="uk-input" id="currentPassword" type="password" name="current_password" autocomplete="current-password" required>
                    </div>
                </div>
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
                <div class="uk-margin uk-text-right">
                    <button type="submit" class="uk-button custom-color-group-1">修改</button>
                </div>
            </div>
        </form>
    </div>
@endsection
