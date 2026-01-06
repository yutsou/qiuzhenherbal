@extends('layouts.admin')

@section('content')
    <div class="uk-margin-medium">
        <h1 class="uk-heading-medium">{{ $head }}</h1>
    </div>
    <div class="uk-margin">
        <h2>全部訂單</h2>
        <table id="orderDatas" class="uk-table uk-table-striped">
            <thead>
            <tr>
                <th>訂單</th>
                <th>日期</th>
                <th>狀態</th>
                <th>總計</th>
                <th></th>
            </tr>
            </thead>
        </table>
    </div>
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
            $('#orderDatas').DataTable({
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
                "ajax": "{{ route('admin.ajax.all_orders') }}",
                "columns":
                    [
                        { "data": "idName", "orderable": false},
                        { "data": "created_at", "orderable": false},
                        { "data": "status", "orderable": false},
                        { "data": "total", "orderable": false},
                        { "data": "action", "orderable": false},
                    ]
            });
        });
    </script>
@endpush
