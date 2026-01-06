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
        <p>
            驗證信箱可以幫助您在忘記本網站密碼時找回密碼，並提供訂單狀態通知，以及優惠券發放通知<br>
            提醒您，您必須完成信箱驗證才能在網站上消費
        </p>
        <a href="{{ route('account.email_verification.send') }}" class="uk-button custom-button-1">寄送認證信</a>
    </div>
@endsection
