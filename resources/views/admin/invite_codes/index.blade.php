@extends('layouts.admin')

@section('content')
    <div class="uk-margin-medium">
        <h1 class="uk-heading-medium">{{ $head }}</h1>
    </div>
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
    <table class="uk-table uk-table-divider">
        <thead>
        <tr>
            <th>持有人名稱</th>
            <th>邀請碼</th>
            <th>折扣價格</th>
            <th>使用次數</th>
            <th>邀請碼訂單總計</th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        @foreach($allInviteCodes as $inviteCode)
            <tr>
                <td>{{ $inviteCode->holder_name }}</td>
                <td>{{ $inviteCode->code }}</td>
                <td>NT${{ number_format($inviteCode->discount) }}</td>
                <td>{{ $inviteCode->usage_count }}</td>
                <td>NT${{ number_format($inviteCode->order_total) }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>

@endsection

