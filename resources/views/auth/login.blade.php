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
<div class="uk-width-1-3@l uk-align-center">
    <div class="uk-card uk-card-default uk-card-body">
        <div class="uk-margin-medium">
            <h1 class="uk-card-title">登入</h1>
        </div>
        <form class="uk-form-stacked" method="POST" action="{{ route('login') }}">
            @csrf
            <div class="uk-margin">
                <label class="uk-form-label" for="email">電子郵件</label>
                <div class="uk-inline uk-form-controls uk-width-1-1">
                    <span class="uk-form-icon" uk-icon="icon: mail"></span>
                    <input type="email" class="uk-input" id="email" name="email" value="{{ old('email') }}" required>
                </div>
            </div>

            @if ($errors->has('email'))
                <div class="uk-alert" uk-alert>
                    <a class="uk-alert-close" uk-close></a>
                    @foreach ($errors->get('email') as $error)
                        <p class="custom-color-2">{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <div class="uk-margin">
                <label class="uk-form-label" for="password">密碼</label>
                <div class="uk-inline uk-form-controls uk-width-1-1">
                    <span class="uk-form-icon" uk-icon="icon: lock"></span>
                    <input type="password" class="uk-input" id="password" name="password" required autocomplete="current-password">
                </div>
            </div>

            <div class="uk-margin">
                <div class="uk-child-width-1-2" uk-grid>
                    <div class="uk-text-left">
                        <label><input class="uk-checkbox" type="checkbox" id="remember_me" name="remember"> 記住我</label>

                    </div>
                    <div class="uk-text-right">
                        <a class="uk-link-text " href="{{ route('password.forgot') }}">忘記密碼？</a>
                    </div>

                </div>
            </div>

            @if ($errors->has('password'))
                <div class="uk-alert" uk-alert>
                    <a class="uk-alert-close" uk-close></a>
                    @foreach ($errors->get('password') as $error)
                        <p class="custom-color-2">{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <div class="uk-margin">
                <button type="submit" class="uk-button custom-color-group-1 uk-width-1-1" style="background-color: #ee782e; color: #fff;"></span>登入</button>
            </div>

            <div class="uk-margin">

            </div>
        </form>
        <div class="separator">或是</div>
        <div class="uk-margin">
            <a class="uk-button uk-text-capitalize uk-width-1-1" href="{{ route('facebook.login') }}" style="background-color: #1977f3; color: #fff; border: solid #e5e5e5 1px;">
                <img width="20px" style="margin-bottom:3px; margin-right:5px;" src="/images/web/common/facebook_login_reverse.png" />
            使用 Facebook 登入 或 註冊
            </a>
        </div>
        <div class="uk-margin">
            <a class="uk-button uk-text-capitalize uk-width-1-1" href="{{ route('line.login') }}" style="background-color: #00b900; color: #fff; border: solid #e5e5e5 1px;">
                <img width="20px" style="margin-bottom:3px; margin-right:5px;" src="/images/web/common/line_login_reverse.png" />
            使用 LINE 登入 或 註冊
            </a>
        </div>
        <div class="uk-margin uk-text-center">
            <a class="uk-link-text" href="{{ route('register.show') }}">註冊新會員</a>
        </div>
    </div>
</div>
@endsection

