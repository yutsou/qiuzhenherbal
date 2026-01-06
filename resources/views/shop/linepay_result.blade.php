@extends('layouts.general_member')

@section('content')
    @if (session('Warning'))
        <script>
            Swal.fire({
                position: 'center',
                icon: 'warning',
                title: '{{session('Warning')}}',
                showConfirmButton: false,
            });
        </script>
    @elseif (session('Info'))
        <script>
            Swal.showLoading();
        </script>
    @endif
    <input id="authCheck" value="{{ Auth::check() }}" hidden>
@endsection
@push('scripts')
    <script>
        $(function () {
            let intvalId = setInterval(function(){
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: "get",
                    url: '/ajax/pay/line/getPaymentStatus/'+{{ $orderId }},
                    success: function (status) {
                        if(status === '1'){
                            let authCheck = $('#authCheck').val();
                            if(authCheck === '1') {
                                Swal.fire({
                                    position: 'center',
                                    icon: 'success',
                                    title: '付款成功',
                                    showConfirmButton: false,
                                })
                                setTimeout(function(){
                                    window.location.replace('/account/orders/'+{{$orderId}});
                                }, 1500)

                            } else {
                                Swal.fire({
                                    position: 'center',
                                    icon: 'success',
                                    title: '付款成功，請關閉視窗',
                                    showConfirmButton: false,
                                })
                            }
                            clearInterval(intvalId);
                        }
                    }
                });
            }, 3000);
        });
    </script>
@endpush
