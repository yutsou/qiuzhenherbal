@extends('layouts.general_member')

@section('content')
    @if (session('Warning'))
        <script>
            Swal.fire({
                position: 'center',
                icon: 'warning',
                title: '{{session('Warning')}}',
                showConfirmButton: false,
            })
        </script>
    @endif
@endsection
