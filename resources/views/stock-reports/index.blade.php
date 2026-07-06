@extends('layouts.app')
@section('title')
    {{ __('messages.stock-reports.name') }}
@endsection
@section('page_css')
    <link href="{{ asset('assets/css/jquery.dataTables.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="{{ asset('assets/css/bs4-summernote/summernote-bs4.css') }}">
    <link href="{{ asset('assets/css/buttons.dataTables.min.css') }}" rel="stylesheet" type="text/css" />
@endsection
@section('content')
    <section class="section">
        <div class="section-header item-align-right">
            <h1>{{ __('messages.stock-reports.name') }}</h1>
            <div class="section-header-breadcrumb float-right">
                <div class="card-header-action mr-3 select2-mobile-margin">
                    <div class="d-flex justify-content-between mb-3">
                        <!-- Status Dropdown -->
                        <div class="mr-3 d-none">
                            <label for="statusDropdown">Branches</label><br>
                            {{ Form::select('branches', $branches ?? [], null, ['id' => 'filterBranch', 'class' => 'form-control select2 ', 'placeholder' => __('messages.placeholder.branches')]) }}
                        </div>
                        <div class="mr-3">
                            <label for="statusDropdown">Groups</label><br>
                            {{ Form::select('branches', $groups ?? [], null, ['id' => 'filterGroup', 'class' => 'form-control select2', 'placeholder' => 'Select']) }}
                        </div>
                        <div class="mr-3">
                            <label for="statusDropdown">Categories</label><br>
                            {{ Form::select('branches', $categories->pluck('name', 'id') ?? [], null, ['id' => 'filterCategory', 'class' => 'form-control select2', 'placeholder' => 'Select']) }}
                        </div>
                        <div class="mr-3">
                            <label for="statusDropdown">Sub Categories</label><br>
                            {{ Form::select('branches', $subcategories->pluck('name', 'id') ?? [], null, ['id' => 'filterSubCategory', 'class' => 'form-control select2', 'placeholder' => 'Select']) }}
                        </div>
                        <div class="mr-3">
                            <label for="statusDropdown">Items</label><br>
                            {{ Form::select('branches', $allItems->pluck('name', 'id') ?? [], null, ['id' => 'filterItem', 'class' => 'form-control select2', 'placeholder' => 'Select']) }}
                        </div>
                    </div>
                </div>
            </div>



        </div>
        <div class="section-body">
            <div class="card">
                <div class="card-body">
                    @if (session()->has('flash_notification'))
                        @foreach (session('flash_notification') as $message)
                            <div class="alert alert-{{ $message['level'] }}">
                                {{ $message['message'] }}
                            </div>
                        @endforeach
                    @endif
                    @include('stock-reports.table_unit')
                </div>
            </div>
        </div>
    </section>
@endsection
@section('page_scripts')
    <script src="{{ asset('assets/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ mix('assets/js/custom/custom-datatable.js') }}"></script>
    <script src="{{ mix('assets/js/bs4-summernote/summernote-bs4.js') }}"></script>
    <script src="{{ mix('assets/js/select2.min.js') }}"></script>


    <script src="{{ asset('assets/js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('assets/js/jszip.min.js') }}"></script>
    <script src="{{ asset('assets/js/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('assets/js/buttons.print.min.js') }}"></script>
    <script src="{{ asset('assets/js/buttons.colVis.min.js') }}"></script>
@endsection
@section('scripts')
    <script src="{{ mix('assets/js/custom/input-price-format.js') }}"></script>
    <script>
        'use strict';


        let tbl = $('#assetCategoryTable').DataTable({
            oLanguage: {
                'sEmptyTable': Lang.get('messages.common.no_data_available_in_table'),
                'sInfo': Lang.get('messages.common.data_base_entries'),
                sLengthMenu: Lang.get('messages.common.menu_entry'),
                sInfoEmpty: Lang.get('messages.common.no_entry'),
                sInfoFiltered: Lang.get('messages.common.filter_by'),
                sZeroRecords: Lang.get('messages.common.no_matching'),
            },
            processing: true,
            serverSide: true,
            ajax: {
                url: route('stock-reports.index'),
                data: function(d) {
                    d.filterBranch = $("#filterBranch").val();
                    d.filterCategory = $("#filterCategory").val();
                    d.filterGroup = $("#filterGroup").val();
                    d.filterSubCategory = $("#filterSubCategory").val();
                    d.filterItem = $("#filterItem").val();
                },
                beforeSend: function() {
                    startLoader();
                },
                complete: function() {
                    stopLoader();
                }
            },
            pageLength: 25, // Set the page size to 30 items
            lengthChange: false,
            dom: 'Bfrtip',
            buttons: [{
                extend: 'excelHtml5',
                text: '<i class="fas fa-file-excel"></i> Export to Excel',
                titleAttr: 'Export table data to Excel',
                className: 'btn btn-outline-dark btn-sm',
                footer: true, // Ensure footer is included in export
                exportOptions: {
                    columns: ':visible',
                    format: {
                        body: function(data, row, column, node) {
                            return $('<div>').html(data).text().trim(); // Clean HTML
                        },
                        footer: function(data, row, column, node) {
                            return $('<div>').html(data).text().trim();
                        }
                    }
                }
            }],

            columns: [{
                    data: null,
                    name: 'serial',
                    orderable: false,
                    searchable: false,
                    render: function(data, type, row, meta) {
                        return meta.row + 1;
                    },
                    width: '3%'
                },
                {
                    data: function(row) {
                        let element = document.createElement('textarea');
                        element.innerHTML = row.code ?? '';
                        return element.value;
                    },
                    name: 'code',
                    width: '7%'
                },
                {
                    data: function(row) {
                        let element = document.createElement('textarea');
                        element.innerHTML = row.barcode ?? '';
                        return element.value;
                    },
                    name: 'barcode',
                    width: '8%'
                },
                {
                    data: function(row) {
                        let element = document.createElement('textarea');
                        element.innerHTML = row.name;
                        return element.value;
                    },
                    name: 'name',
                    width: '15%'
                },
                {
                    data: function(row) {
                        let element = document.createElement('textarea');
                        element.innerHTML = row.qty_in ?? 0;
                        return element.value;
                    },
                    name: 'qty_in',
                    width: '10%',
                    className: 'text-right'
                },
                {
                    data: function(row) {
                        let element = document.createElement('textarea');
                        element.innerHTML = row.qty_out ?? 0;
                        return element.value;
                    },
                    name: 'qty_out',
                    width: '10%',
                    className: 'text-right'
                },
                {
                    data: function(row) {
                        let value = parseFloat(row.qty_current ?? 0);
                        let cssClass = value < 0 ? 'text-danger' : '';
                        return `<span class="${cssClass}">${value}</span>`;
                    },
                    name: 'qty_current',
                    width: '10%',
                    className: 'text-right',
                    orderable: true,
                },

                {
                    data: function(row) {
                        let element = document.createElement('textarea');
                        element.innerHTML = parseFloat(row.cost_price).toFixed(2);
                        return element.value;
                    },
                    name: 'cost_price',
                    width: '12%',
                    className: 'text-right'
                },
                {
                    data: function(row) {
                        let element = document.createElement('textarea');
                        element.innerHTML = parseFloat(row.price).toFixed(2);
                        return element.value;
                    },
                    name: 'price',
                    width: '15%',
                    className: 'text-right',
                    orderable: false,
                },



            ],
            order: [
                [1, 'asc']
            ],
            responsive: true, // Enable responsive features
            footerCallback: function(row, data, start, end, display) {
                const api = this.api();

                const intVal = function(i) {
                    return typeof i === 'string' ?
                        parseFloat(i.replace(/[\$,]/g, '')) || 0 :
                        typeof i === 'number' ?
                        i :
                        0;
                };

                const sumColumn = (index) =>
                    api
                    .column(index, {
                        page: 'current'
                    })
                    .data()
                    .reduce((a, b) => intVal(b) + a, 0)
                    .toFixed(2);

                // Indexes based on your column order (adjust if needed)
                const qtyInTotal = sumColumn(4);
                const qtyOutTotal = sumColumn(5);
                const qtyCurrentTotal = sumColumn(6);
                const costPriceTotal = sumColumn(7);
                const totalValue = sumColumn(8);

                $(api.column(4).footer()).html(qtyInTotal);
                $(api.column(5).footer()).html(qtyOutTotal);
                $(api.column(6).footer()).html(qtyCurrentTotal);
                $(api.column(7).footer()).html(costPriceTotal);
                $(api.column(8).footer()).html(totalValue);
            }

        });

        $('#filterBranch,#filterCategory,#filterGroup,#filterSubCategory,#filterItem').change(function() {
            tbl.ajax.reload(); // Reload DataTable with new status filter

        });
    </script>
@endsection
