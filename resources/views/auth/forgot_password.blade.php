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
    @elseif(session('Warning'))
        <script>
            Swal.fire({
                position: 'center',
                icon: 'warning',
                title: '{{session('Warning')}}',
                showConfirmButton: false,
                timer: 1500
            })
        </script>
    @endif
    <div class="uk-card uk-card-default uk-card-body">
        <div class="uk-margin-medium">
            <h1 class="uk-heading-medium">{{ $head }}</h1>
        </div>
        <form class="uk-form-stacked" method="POST" action="{{ route('password.reset_password_confirmation.send') }}" enctype="multipart/form-data">
            @csrf
            <div class="uk-width-1-2@l">
                <div class="uk-margin">
                    <label class="uk-form-label" for="email">Email帳號</label>
                    <div class="uk-form-controls">
                        <input class="uk-input" id="email" type="email" name="email" autocomplete="email" required>
                    </div>
                    <p>我們將會寄出一封密碼重置信件到您的電子郵件，請您點擊信件中的按鈕並重置密碼</p>
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
                    <button type="submit" class="uk-button custom-color-group-1">送出</button>
                </div>
            </div>
        </form>
    </div>
@endsection
