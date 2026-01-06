@extends('layouts.admin')
@include('ckfinder::setup')
@section('content')
    <div class="uk-margin-medium">
        <h1 class="uk-heading-medium">{{ $head }}</h1>
    </div>
    <form id="product">
        <div class="uk-margin">
            <div class="uk-card uk-card-default uk-card-body">
                <h3 class="uk-card-title uk-form-label">商品主圖片</h3>
                <div class="uk-margin" id="mainImageDisplay">

                </div>
                <a class="uk-button uk-button-default ckFinder" id="mainImage">選擇主圖片</a>
            </div>
        </div>
        <div class="uk-margin">
            <div class="uk-card uk-card-default uk-card-body">
                <h3 class="uk-card-title uk-form-label">商品其他圖片</h3>
                <div class="uk-grid uk-grid-small uk-margin" id="otherImagesDisplay" uk-grid>

                </div>
                <a class="uk-button uk-button-default ckFinder" id="otherImages">選擇其他圖片</a>
            </div>
        </div>
        <div class="uk-margin">
            <div class="uk-grid-small uk-child-width-expand@s" uk-grid uk-height-match="target: > div > .uk-card">
                <div>
                    <div class="uk-card uk-card-default uk-card-body">
                        <h3 class="uk-card-title">商品分類</h3>
                        <ul class="uk-list">
                            @foreach($rootCategories as $rootCategory)
                                <li><h4>{{ $rootCategory->name }}</h4></li>
                                @if(count($rootCategory->children) !== 0 )
                                    <ul class="uk-list">
                                        @foreach($rootCategory->children as $childrenCategory)
                                            <li class="uk-margin-left"><label><input class="uk-checkbox"
                                                                                     type="checkbox" name="categories[]" value="{{ $childrenCategory->id }}"> {{ $childrenCategory->name }}
                                                </label></li>
                                        @endforeach
                                    </ul>
                                @endif
                            @endforeach
                        </ul>
                    </div>
                </div>
                <div>
                    <div class="uk-card uk-card-default uk-card-body">
                        <h3 class="uk-card-title">商品標籤</h3>
                        <ul class="uk-list">
                            @foreach($tags as $tag)
                                <li><label><input type="checkbox" class="uk-checkbox" name="tags[]" value="{{ $tag->id }}"> {{ $tag->name }}</label></li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div class="uk-margin">
            <div class="uk-card uk-card-default uk-card-body">
                <h3 class="uk-card-title uk-form-label">商品名稱</h3>
                <div class="uk-form-controls">
                    <input class="uk-input" name="name" type="text">
                </div>
                <h3 class="uk-card-title uk-form-label">商品簡短介紹</h3>
                <div class="uk-form-controls">
                    <input class="uk-input" name="short_description" type="text">
                </div>
                <h3 class="uk-card-title uk-form-label">商品提醒</h3>
                <div class="uk-form-controls">
                    <input class="uk-input" name="reminder" type="text">
                </div>
                <h3>商品介紹</h3>
                <textarea class="ckeditor" id="introduction"></textarea>
                <h3>商品資訊</h3>
                <textarea class="ckeditor" id="information"></textarea>
            </div>
        </div>
        <div class="uk-margin">
            <div class="uk-card uk-card-default uk-card-body">
                <h3 class="uk-card-title uk-form-label">產品號</h3>
                <div class="uk-form-controls">
                    <input class="uk-input" name="number" type="text">
                </div>
            </div>
        </div>
        <div class="uk-margin">
            <div class="uk-grid-small uk-child-width-expand@s" uk-grid uk-height-match="target: > div > .uk-card">
                <div>
                    <div class="uk-card uk-card-default uk-card-body">
                        <h3 class="uk-card-title uk-form-label">售價</h3>
                        <div class="uk-form-controls">
                            <input class="uk-input" name="regular_price" type="number" min="0">
                        </div>
                    </div>
                </div>
                <div>
                    <div class="uk-card uk-card-default uk-card-body">
                        <h3 class="uk-card-title uk-form-label">折扣</h3>
                        <div class="uk-child-width-1-2 uk-grid-small uk-margin" uk-grid>
                            <div>
                                <label>
                                    起始時間
                                    <input type="date" class="uk-input" name="discount_start_at">
                                </label>
                            </div>
                            <div>
                                <label>
                                    結束時間
                                    <input type="date" class="uk-input" name="discount_end_at">
                                </label>
                            </div>
                        </div>
                        <div id="discountsField">

                        </div>
                        <div class="uk-margin">
                            <div class="uk-grid-small uk-child-width-expand@s" uk-grid>
                                <div>
                                    <a class="uk-button uk-button-default uk-width-expand@s" id="addDiscount">增加區間</a>
                                </div>
                                <div>
                                    <a class="uk-button uk-button-default uk-width-expand@s" id="minusDiscount">減少區間</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="uk-margin">
            <div class="uk-grid-small uk-child-width-expand@s" uk-grid uk-height-match="target: > div > .uk-card">
                <div>
                    <div class="uk-card uk-card-default uk-card-body">
                        <h3 class="uk-card-title uk-form-label">可見度</h3>
                        <select class="uk-select uk-form-controls" name="visible">
                            <option value="1">上架</option>
                            <option value="0">下架</option>
                        </select>
                    </div>
                </div>
                <div>
                    <div class="uk-card uk-card-default uk-card-body">
                        <div class="uk-grid-small uk-child-width-expand@s" uk-grid>
                            <div>
                                <h3 class="uk-card-title uk-form-label">庫存狀態</h3>
                            </div>
                            <div class="uk-text-right">
                                <label><input class="uk-checkbox" id="manageInventory" type="checkbox"> 管理庫存</label>
                            </div>
                        </div>
                        <div class="uk-margin" id="inventoryFiled">
                            <select class="uk-select uk-form-controls" name="inventory_status">
                                <option value="1">尚有庫存</option>
                                <option value="2">補貨中</option>
                                <option value="0">售完</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
    <ul class="uk-child-width-expand@s" uk-tab>
        <li class="uk-active"><a href="#">一般商品</a></li>
        <li><a href="#">群組商品</a></li>
    </ul>
    <ul class="uk-switcher uk-margin">
        <li>
            <div class="uk-margin uk-align-right">
                <a class="uk-button custom-button-1 product-store" id="generalProductStore">建立一般商品</a>
            </div>
        </li>
        <li>
            <div class="uk-card uk-card-default uk-card-body">
                <h3 class="uk-card-title uk-form-label">搜尋商品</h3>
                <div class="uk-form-controls">
                    <input class="uk-input" type="text" id="searchSku">
                </div>
                <div id="searchResultField" uk-drop="mode: click; pos: bottom-justify; offset: 0;">

                </div>
                <form id="group">
                    <div id="groupItemsField">
                        <table class="uk-table uk-table-divider">
                            <tbody>

                            </tbody>
                        </table>
                    </div>
                </form>
            </div>
            <div class="uk-margin uk-align-right">
                <a class="uk-button custom-button-1 product-store" id="groupProductStore">建立群組商品</a>
            </div>
        </li>
    </ul>
@endsection
@push('scripts')
    <script type="text/javascript" src="/js/ckfinder/ckfinder.js"></script>
    <script src="{{ asset('js/ckeditor5/v1/ckeditor.js') }}"></script>
    <script>
        CKFinder.config({connectorPath: '/ckfinder/connector'});
    </script>
    <script>
        function selectFileWithCKFinder(elementId) {
            CKFinder.popup({
                chooseFiles: true,
                width: 800,
                height: 600,
                onInit: function (finder) {
                    finder.on('files:choose', function (evt) {
                        if (elementId === 'mainImage') {
                            let mainImageDisplay = $("#mainImageDisplay");
                            mainImageDisplay.empty();
                            let file = evt.data.files.first();
                            let output = document.getElementById(elementId);
                            output.value = file.getUrl();
                            let mainImage = $('<img>', {
                                'width': '200px',
                                'height': '200px',
                                'uk-img': '',
                                'src': output.value
                            });
                            let inputMainImage = $('<input>', {
                                'type': 'text',
                                'id': 'mainImageUrl',
                                'name': 'mainImageUrl',
                                'value': output.value,
                                'hidden': ''
                            });
                            mainImageDisplay.append([mainImage, inputMainImage]);
                        } else {
                            let files = evt.data.files;
                            let otherImagesDisplay = $("#otherImagesDisplay");
                            otherImagesDisplay.empty();

                            let otherImages = [];
                            files.forEach(function (file, i) {
                                let output = document.getElementById(elementId);
                                output.value = file.getUrl();
                                let otherImage = $('<img>', {
                                    'width': '200px',
                                    'height': '200px',
                                    'uk-img': '',
                                    'src': output.value
                                });
                                let inputOtherImage = $('<input>', {
                                    'type': 'text',
                                    'class': 'otherImageUrls',
                                    'name': 'otherImageUrls[]',
                                    'value': output.value,
                                    'hidden': ''
                                });
                                otherImages.push($('<div>').append([otherImage, inputOtherImage]));
                            });
                            otherImagesDisplay.append(otherImages);
                        }

                    });

                    finder.on('file:choose:resizedImage', function (evt) {
                        var output = document.getElementById(elementId);
                        output.value = evt.data.resizedUrl;
                        document.getElementById("mainImageDisplay").setAttribute('src', evt.data.resizedUrl);
                    });
                }
            });
        }
    </script>
    <script>
        $(document).ready(function () {
            $('.ckFinder').click(function () {
                selectFileWithCKFinder($(this).attr("id"));
            });
        });
    </script>
    <script>
        $(document).ready(function () {
            window.editors = [];
            let allEditors = document.querySelectorAll('.ckeditor');
            for (let i = 0; i < allEditors.length; ++i) {
                ClassicEditor
                    .create(allEditors[i], {
                        ckfinder: {

                            // Use named route for CKFinder connector entry point
                            uploadUrl: '{{ route('ckfinder_connector') }}?command=QuickUpload&type=Files',
                            options: {
                                language: 'zh-tw'
                            },
                            openerMethod: 'popup'
                        },
                        fontColor: {
                            colors: [
                                {
                                    color: 'hsl(0, 0%, 0%)',
                                    label: 'Black'
                                },
                                {
                                    color: 'hsl(0, 0%, 30%)',
                                    label: 'Dim grey'
                                },
                                {
                                    color: 'hsl(0, 0%, 60%)',
                                    label: 'Grey'
                                },
                                {
                                    color: 'hsl(0, 0%, 90%)',
                                    label: 'Light grey'
                                },
                                {
                                    color: 'hsl(0, 0%, 100%)',
                                    label: 'White',
                                    hasBorder: true
                                },

                                // ...
                            ]
                        },
                        toolbar: {
                            items: [
                                'heading',
                                '|',
                                'bold',
                                'italic',
                                'link',
                                'bulletedList',
                                'numberedList',
                                '|',
                                'fontFamily',
                                'fontSize',
                                'fontBackgroundColor',
                                'fontColor',
                                '|',
                                'alignment',
                                'outdent',
                                'indent',
                                '|',
                                'CKFinder',
                                'insertTable',
                                'mediaEmbed',
                                '|',
                                'htmlEmbed',
                                'sourceEditing',
                                '|',
                                'undo',
                                'redo'
                            ]
                        },
                        image: {
                            // Configure the available styles.
                            styles: [
                                'alignLeft', 'alignCenter', 'alignRight'
                            ],
                            // You need to configure the image toolbar, too, so it shows the new style
                            // buttons as well as the resize buttons.
                            toolbar: [
                                'imageStyle:alignLeft', 'imageStyle:alignCenter', 'imageStyle:alignRight',
                                '|',
                                'imageTextAlternative'
                            ]
                        },
                        table: {
                            contentToolbar: [
                                'tableColumn',
                                'tableRow',
                                'mergeTableCells',
                                'tableProperties',
                                'tableCellProperties'
                            ]
                        },
                        language: 'zh',
                        licenseKey: '',
                    })
                    .then(
                        editor => {editors.push(editor);}

                    )
                    .catch(error => {
                        console.error('Oops, something went wrong!');
                        console.error('Please, report the following error on https://github.com/ckeditor/ckeditor5/issues with the build id and the error stack trace:');
                        console.warn('Build id: 16hefg2wr7wn-4nhiufdle9yy');
                        console.error(error);
                    });
            }

        });
    </script>
    <script>
        $(document).ready(function () {
            let manageInventory = $('#manageInventory');
            let inventoryFiled = $('#inventoryFiled');
            manageInventory.change(function () {
                if (manageInventory.is(':checked')) {
                    inventoryFiled.empty();
                    inventoryFiled.append($('<input>', {
                        'type': 'text',
                        'class': 'uk-input',
                        'name': 'inventory',
                        'placeholder': '輸入庫存量'
                    }));
                } else {
                    inventoryFiled.empty();
                    let appendElement = $('<select/>', {
                        'class': 'uk-select uk-form-controls',
                        'name': 'inventory_status'
                    }).append([$('<option/>', {'value': 1}).text('尚有庫存'), $('<option/>', {'value': 2}).text('補貨中'), $('<option/>', {'value': 0}).text('售完')]);
                    inventoryFiled.append(appendElement);
                }
            });
        });
    </script>
    <script>
        $(document).ready(function () {
            $('#addDiscount').click(function () {
                let discountsField = $('#discountsField');
                let discountsQuantity = $('.discounts').length;
                let discountsQuantityS = String(discountsQuantity);
                let discounts = $('<div/>', {
                    'class': "uk-grid-small discounts",
                    'id': 'discount-' + discountsQuantityS,
                    'uk-grid': ''
                });
                let min;
                let max;
                if (discountsQuantity === 0) {
                    min = $('<div/>', {'class': 'uk-width-1-4@s'}).append($('<input>', {
                        'class': 'uk-input mins',
                        'id': 'min-' + discountsQuantityS,
                        'name': 'mins[]',
                        'type': 'number',
                        'min': '1',
                        'value': '1',
                        'placeholder': '最小'
                    }));
                    max = $('<div/>', {'class': 'uk-width-1-4@s'}).append($('<input>', {
                        'class': 'uk-input maxs',
                        'id': 'max-' + discountsQuantityS,
                        'name': 'maxs[]',
                        'type': 'number',
                        'min': '0',
                        'placeholder': '最大'
                    }));
                } else {
                    let maxValue = $('#max-' + (discountsQuantity - 1).toString()).val();
                    min = $('<div/>', {'class': 'uk-width-1-4@s'}).append($('<input>', {
                        'class': 'uk-input mins',
                        'id': 'min-' + discountsQuantityS,
                        'name': 'mins[]',
                        'type': 'number',
                        'min': '0',
                        'value': parseInt(maxValue) + 1,
                        'placeholder': '最小',
                        'readonly': ''
                    }));
                    max = $('<div/>', {'class': 'uk-width-1-4@s'}).append($('<input>', {
                        'class': 'uk-input maxs',
                        'id': 'max-' + discountsQuantityS,
                        'name': 'maxs[]',
                        'type': 'number',
                        'min': '0',
                        'placeholder': '最大'
                    }));
                }
                let discountPrice = $('<div/>', {'class': 'uk-width-1-2@s'}).append($('<input>', {
                    'class': 'uk-input discountPrices',
                    'id': 'discountPrice-' + discountsQuantityS,
                    'name': 'discount_price[]',
                    'type': 'number',
                    'min': '0',
                    'placeholder': '單價'
                }));
                discounts.append([min, max, discountPrice]);
                discountsField.append(discounts);
            });

            $('#discountsField').on('change', '.maxs', function () {
                let maxId = $(this).attr('id');
                let maxValue = parseInt($(this).val());
                let nextNumber = String(parseInt(maxId.split('-')[1]) + 1)
                $('#min-' + nextNumber).val(maxValue + 1);
            });

            $('#minusDiscount').click(function () {
                let discountsQuantity = $('.discounts').length;
                $('#discount-' + String(discountsQuantity - 1)).remove();
            });
        });
    </script>
    <script>
        $(document).ready(function () {
            $('.product-store').click(function () {
                let inputData = $('#product').serializeArray();
                inputData.push({ name: "introduction", value: editors[0].getData() });
                inputData.push({ name: "information", value: editors[1].getData() });
                if ($(this).attr('id') === 'generalProductStore') {
                    inputData.push({ name: "type", value: "general" });
                } else {
                    inputData.push({ name: "type", value: "group" });
                    inputData = inputData.concat($('#group').serializeArray());
                }

                Swal.showLoading()

                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: "post",
                    url: '{{ route('admin.products.store') }}',
                    data: inputData,
                    success: function (data) {
                        if (typeof (data.error) !== 'undefined') {
                            Swal.fire({
                                icon: 'warning',
                                html: data.error,
                                showConfirmButton: false,
                                timer: 2000
                            })
                        } else {
                            window.location.assign('/admin/dashboard/products/'+data.success+'/edit');
                        }
                    }
                });
            });
        });
    </script>
    <script>
        $(document).ready(function () {
            $('#searchSku').on('input', function () {
                $('#searchResultField').empty();
                let keyword = $(this).val();
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: "post",
                    url: '{{ route('admin.ajax.search.sku') }}',
                    data: {keyword:keyword},
                    success: function (results) {
                        let resultsQuantity = Object.keys(results).length;
                        if ( resultsQuantity !== 0) {
                            let card = $('<div/>', {'class':'uk-card uk-card-default uk-card-body'}).append($('<ul/>', {'class':'uk-nav-default', 'uk-nav':''}));
                            $('#searchResultField').append(card);
                            for (let i=0; i<resultsQuantity; i++) {
                                $('#searchResultField > div > ul').append($('<li/>').append($('<a/>', {'class':'results', 'skuId':results[i].id}).text(results[i].name)));
                            }
                        }
                        UIkit.drop('#searchResultField').show();
                    }
                });
            });

            $(document).on('click', '.results', function () {
                $('#searchResultField').empty();
                let skuName = $(this).text();
                let skuId = $('<input>', {'type':'text', 'name':'groupSkuIds[]', 'value':$(this).attr('skuId'), 'hidden':''});
                let groupItemQuantity = $('<input>', {'type':'number', 'class':'uk-input', 'name':'groupItemQuantities[]', 'min':'1', 'value':'1', 'placeholder': '數量'});
                let deleteGroupItem = $('<a/>', {'class':'uk-button uk-button-default uk-width-expand delete-group-item-buttons', 'skuId':$(this).attr('skuId')}).text('移除')
                let tr = $('<tr/>', {'id':'groupItem-'+$(this).attr('skuId')}).append([$('<td/>').append(skuName), $('<td/>').append([skuId, groupItemQuantity]), $('<td/>').append(deleteGroupItem)]);
                $('#groupItemsField > table > tbody').append(tr);
            });

            $('#groupItemsField').on('click', '.delete-group-item-buttons', function () {
                let skuId = $(this).attr('skuId');
                $('#groupItem-'+skuId).remove();
            });
        });
    </script>
@endpush
