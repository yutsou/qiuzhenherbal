@extends('layouts.admin')

@section('content')
    <div class="uk-margin-medium">
        <h1 class="uk-heading-medium">{{ $head }}</h1>
    </div>
    <table id="userDatas" class="uk-table uk-table-striped">
        <thead>
        <tr>
            <th>ID</th>
            <th>名稱</th>
            <th>E-Mail</th>
            <th>登入方式</th>
            <th>消費總計</th>
        </tr>
        </thead>
    </table>
@endsection
@push('styles')
    <link rel="stylesheet" href="{{ asset('datatable-1.10.25/css/dataTables.uikit.min.css') }}">
@endpush
@push('scripts')
    <script defer src="{{ asset('datatable-1.10.25/js/jquery.dataTables.min.js') }}"></script>
    <script defer src="{{ asset('datatable-1.10.25/js/dataTables.uikit.min.js') }}"></script>
    <script defer>
        $(document).ready(function()
        {   //datatable設定
            $('#userDatas').DataTable({
                "order": [],//取消datatable第一欄預設sort
                "language": {
                    "processing":   "處理中...",
                    "loadingRecords": "載入中...",
                    "lengthMenu":   "顯示 _MENU_ 項結果",
                    "zeroRecords":  "沒有符合的結果",
                    "info":         "顯示第 _START_ 至 _END_ 項結果，共 _TOTAL_ 項",
                    "infoEmpty":    "顯示第 0 至 0 項結果，共 0 項",
                    "infoFiltered": "(從 _MAX_ 項結果中過濾)",
                    "infoPostFix":  "",
                    "search":       "搜尋:",
                    "paginate": {
                        "first":    "第一頁",
                        "previous": "上一頁",
                        "next":     "下一頁",
                        "last":     "最後一頁"
                    },
                    "aria": {
                        "sortAscending":  ": 升冪排列",
                        "sortDescending": ": 降冪排列"
                    }
                },
                "ajax": "{{ route('admin.ajax.all_users') }}",
                "columns":
                    [
                        { "data": "id", "orderable": false},
                        { "data": "name", "orderable": false},
                        { "data": "email", "orderable": false},
                        { "data": "oauth_type", "orderable": false},
                        { "data": "total", "orderable": true},
                    ]
            });
        });
    </script>
@endpush
