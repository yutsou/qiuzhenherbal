@extends('layouts.general_member')

@section('content')
<div class="uk-width-1-3@l uk-align-center">
    <div class="uk-card uk-card-default uk-card-body">
        <div class="uk-margin-medium">
            <h1 class="uk-card-title">註冊</h1>
        </div>
        <form class="uk-form-stacked" method="POST" action="{{ route('register') }}">
            @csrf
            <div class="uk-margin">
                <label class="uk-form-label" for="name">姓名</label>
                <div class="uk-inline uk-form-controls uk-width-1-1">
                    <span class="uk-form-icon" uk-icon="icon: user"></span>
                    <input type="text" class="uk-input" id="name" name="name" value="{{ old('name') }}" required>
                </div>
            </div>
            <div class="uk-margin">
                <label class="uk-form-label" for="phone">行動電話</label>
                <div class="uk-inline uk-form-controls uk-width-1-1">
                    <span class="uk-form-icon" uk-icon="icon: receiver"></span>
                    <input class="uk-input" id="phone" type="text" name="phone" autocomplete="tel" value="{{ old('phone') }}">
                </div>
            </div>
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
                <label class="uk-form-label" for="birthday">生日</label>
                <div class="uk-form-controls">
                    <input class="uk-input" id="birthday" type="date" name="birthday" autocomplete="birthday" value="{{ old('birthday') }}">
                </div>
            </div>

            <div class="uk-margin">
                <label class="uk-form-label">縣市、鄉鎮</label>
                <div class="uk-grid-small uk-child-width-1-3@m uk-form-controls twzipcode" uk-grid>
                    <div data-role="county" data-style="uk-select" data-name="county" data-value="{{ old('county')}}"></div>
                    <div data-role="district" data-style="uk-select" data-name="district" data-value="{{ old('district') }}"></div>
                    <div data-role="zipcode" data-style="uk-select" data-name="zip_code" data-value="{{ old('zipcode') }}"></div>
                </div>
                <div class="uk-margin">
                    <label class="uk-form-label" for="address">街道地址</label>
                    <div class="uk-form-controls">
                        <input class="uk-input" type="text" id="address" name="address" placeholder="輸入您的地址" value="{{ old('address') }}" autocomplete="street-address">
                    </div>
                </div>
            </div>

            <div class="uk-margin">
                <label class="uk-form-label" for="password">密碼</label>
                <div class="uk-inline uk-form-controls uk-width-1-1">
                    <span class="uk-form-icon" uk-icon="icon: lock"></span>
                    <input type="password" class="uk-input" id="password" name="password" required autocomplete="new-password">
                </div>
            </div>

            <div class="uk-margin">
                <label class="uk-form-label" for="password">再確認一次密碼</label>
                <div class="uk-inline uk-form-controls uk-width-1-1">
                    <span class="uk-form-icon" uk-icon="icon: lock"></span>
                    <input type="password" class="uk-input" id="password_confirmation" name="password_confirmation" required autocomplete="new-password">
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
                <button type="submit" class="uk-button custom-color-group-1 uk-width-1-1">註冊</button>
            </div>
        </form>
        <div class="separator">或是</div>
        <div class="uk-margin">
            <a class="uk-button uk-text-capitalize uk-width-1-1" href="{{ route('facebook.login') }}" style="background-color: #1977f3; color: #fff; border: solid #e5e5e5 1px;">
                <img width="20px" style="margin-bottom:3px; margin-right:5px;" src="/images/web/common/facebook_login_reverse.png" />
            使用 Facebook 註冊
            </a>
        </div>
        <div class="uk-margin">
            <a class="uk-button uk-text-capitalize uk-width-1-1" href="{{ route('line.login') }}" style="background-color: #00b900; color: #fff; border: solid #e5e5e5 1px;">
                <img width="20px" style="margin-bottom:3px; margin-right:5px;" src="/images/web/common/line_login_reverse.png" />
            使用 LINE 註冊
            </a>
        </div>
    </div>
</div>
@endsection
@push('scripts')
    <script src="{{ asset('js/jQuery-TWzipcode/twzipcode.js') }}"></script>
    <script>
        $(function() {
            const twzipcode = new TWzipcode(".twzipcode",
                {
                    'county': {
                        'css'      : 'uk-select',
                    },
                    'district': {
                        'css'      : 'uk-select',
                    },
                    'zipcode': {
                        'css'      : 'uk-input',
                        'readonly' : true,
                    },
                }
            );
        });
    </script>
@endpush
