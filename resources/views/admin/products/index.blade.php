@extends('layouts.admin')

@section('content')
    <div class="uk-margin-medium">
        <h1 class="uk-heading-medium">{{ $head }}</h1>
    </div>
    <form action="{{ route('admin.products.delete') }}" method="post">
        @csrf
        <div class="uk-margin">
            <h2>上架商品</h2>
            <table id="visibleTrueProductDatas" class="uk-table uk-table-striped">
                <thead>
                <tr>
                    <th></th>
                    <th>商品名稱</th>
                    <th>商品庫存</th>
                    <th>價格</th>
                    <th>類型</th>
                    <th>分類</th>
                    <th>標籤</th>
                </tr>
                </thead>
            </table>
        </div>
        <hr>
        <div class="uk-margin">
            <h2>下架商品</h2>
            <table id="visibleFalseProductDatas" class="uk-table uk-table-striped">
                <thead>
                <tr>
                    <th></th>
                    <th>商品名稱</th>
                    <th>商品庫存</th>
                    <th>價格</th>
                    <th>類型</th>
                    <th>分類</th>
                    <th>標籤</th>
                </tr>
                </thead>
            </table>
        </div>

        <div class="uk-margin">
            <button class="uk-button test-button-1">刪除</button>
        </div>
    </form>

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
            $('#visibleTrueProductDatas').DataTable({
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
                "ajax": "{{ route('admin.ajax.visible_products', ['visible'=>"1"]) }}",
                "columns":
                    [
                        { "data": "checkbox", "orderable": false},
                        { "data": "name", "orderable": false},
                        { "data": "inventoryStatus", "orderable": false},
                        { "data": "regularPrice", "orderable": false},
                        { "data": "type", "orderable": false},
                        { "data": "categories", "orderable": false},
                        { "data": "tags", "orderable": false},
                    ]
            });

            $('#visibleFalseProductDatas').DataTable({
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
                "ajax": "{{ route('admin.ajax.visible_products', ['visible'=>"0"]) }}",
                "columns":
                    [
                        { "data": "checkbox", "orderable": false},
                        { "data": "name", "orderable": false},
                        { "data": "inventoryStatus", "orderable": false},
                        { "data": "regularPrice", "orderable": false},
                        { "data": "type", "orderable": false},
                        { "data": "categories", "orderable": false},
                        { "data": "tags", "orderable": false},
                    ]
            });
        });
    </script>
@endpush
