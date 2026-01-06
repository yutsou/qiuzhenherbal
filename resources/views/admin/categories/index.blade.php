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
            <th>URL名稱</th>
            <th>上層分類</th>
            <th>動作</th>
        </tr>
        </thead>
        <tbody>
        @foreach($categories as $category)
            <tr>
                <td>{{ $category->name }}</td>
                <td>{{ $category->url_name }}</td>
                <td>{{ optional($category->parent)->name }}</td>
                <td><a href="{{ route('admin.categories.edit', $category) }}" class="uk-button custom-button-1">修改</a></td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endsection
