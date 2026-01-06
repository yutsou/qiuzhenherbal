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
    <table class="uk-table uk-table-striped">
        <thead>
        <tr>
            <th>名稱</th>
            <th>顏色</th>
            <th>動作</th>
        </tr>
        </thead>
        <tbody>
        @foreach($tags as $tag)
            <tr>
                <td>{{ $tag->name }}</td>
                <td><span style="color: {{ $tag->color }};">&#9632;</span> {{ $tag->color }}</td>
                <td><a href="{{ route('admin.tags.edit', $tag) }}" class="uk-button custom-button-1">修改</a></td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endsection
