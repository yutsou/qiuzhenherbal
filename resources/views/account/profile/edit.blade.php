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
        <form class="uk-form-stacked" method="POST" action="{{ route('account.profile.update') }}" enctype="multipart/form-data">
            @csrf
            <div class="uk-width-1-2@l">
                <div class="uk-margin">
                    <label class="uk-form-label" for="name">姓名</label>
                    <div class="uk-form-controls">
                        <input class="uk-input" id="name" type="text" name="name" placeholder="輸入您的姓名" autocomplete="name" value="{{ $user->name }}" required>
                    </div>
                </div>
                <div class="uk-margin">
                    <label class="uk-form-label" for="phone">行動電話</label>
                    <div class="uk-form-controls">
                        <input class="uk-input" id="phone" type="text" name="phone" placeholder="輸入您的行動電話號碼" autocomplete="tel" value="{{ $user->phone }}">
                    </div>
                </div>
                <div class="uk-margin">
                    <label class="uk-form-label" for="email">E-Mail</label>
                    <div class="uk-form-controls">
                        @if($user->oauth_type !== null)
                            <input class="uk-input" id="email" type="email" name="email" placeholder="輸入您的電子信箱" value="{{ $user->email }}" autocomplete="email">
                        @else
                            <input class="uk-input" id="email" type="email" name="email" placeholder="輸入您的電子信箱" value="{{ $user->email }}" autocomplete="email" disabled>
                        @endif
                    </div>
                </div>
                <div class="uk-margin">
                    <label class="uk-form-label" for="birthday">生日</label>
                    <div class="uk-form-controls">
                        <input class="uk-input" id="birthday" type="date" name="birthday" autocomplete="birthday" value="{{ optional($user->birthday)->format('Y-m-d') }}" {{ ($user->birthday !== null ? 'readonly' : 'required') }}>
                    </div>
                </div>
                <div class="uk-margin">
                    <label class="uk-form-label">縣市、鄉鎮</label>
                    <div class="uk-grid-small uk-child-width-1-3@m uk-form-controls twzipcode" uk-grid>
                        <div data-role="county" data-style="uk-select" data-name="county" data-value="{{ $user->county }}"></div>
                        <div data-role="district" data-style="uk-select" data-name="district" data-value="{{ $user->district }}"></div>
                        <div data-role="zipcode" data-style="uk-select" data-name="zip_code" data-value="{{ $user->zipcode }}"></div>
                    </div>
                    <div class="uk-margin">
                        <label class="uk-form-label" for="address">街道地址</label>
                        <div class="uk-form-controls">
                            <input class="uk-input" type="text" id="address" name="address" placeholder="輸入您的地址" value="{{ $user->address }}" autocomplete="street-address">
                        </div>
                    </div>
                </div>
                <div class="uk-margin uk-text-right">
                    <button type="submit" class="uk-button custom-color-group-1">保存</button>
                </div>
                @if ($errors->any())
                    <div class="uk-alert" uk-alert>
                        <a class="uk-alert-close" uk-close></a>
                        @foreach ($errors->all() as $error)
                            <p class="custom-color-2">{{ $error }}</p>
                        @endforeach
                    </div>
                @endif
            </div>
        </form>
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
