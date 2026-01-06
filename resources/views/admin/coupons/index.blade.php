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
            <th>優惠券名稱</th>
            <th>優惠價格</th>
            <th>起始時間</th>
            <th>逾期時間</th>
            <th>使用次數</th>
            <th>分發狀態</th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        @foreach($coupons as $coupon)
            <tr>
                <td>{{ $coupon->name }}</td>
                <td>NT${{ number_format($coupon->discount_price) }}</td>
                <td>{{ $coupon->startAtDateMin }}</td>
                <td>{{ $coupon->endAtDateMin }}</td>
                <td>{{ $coupon->usage_count }}</td>
                @if($coupon->assigned == 0)
                    <td>未分發</td>
                @else
                    <td>已分發</td>
                @endif
                <td>
                    @if($coupon->assigned == 0)
                        <a href="{{ route('admin.coupons.edit', $coupon) }}" class="uk-button custom-button-1">修改</a> <a class="uk-button custom-button-1 assign" couponId="{{ $coupon->id }}">分發</a>
                    @endif
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>

@endsection
@push('scripts')
    <script>
        $(function() {
            $('.assign').click(function(){
               let couponId = $(this).attr('couponId');
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: "post",
                    url: '/admin/ajax/assign/coupons/'+couponId,
                    data: {couponId:couponId},
                    success: function () {
                        window.location.assign('{{ route('admin.coupons.index') }}');
                    }
                });
            });
        });
    </script>
@endpush
