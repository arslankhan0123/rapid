@extends('layouts.app')
@section('title')
    {{ __('messages.item-listing.name') }}
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
            <h1>{{ __('messages.item-listing.name') }}</h1>
            <div class="section-header-breadcrumb float-right">
                <div class="card-header-action mr-3 select2-mobile-margin">
                </div>
            </div>

            <div class="float-right">


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

                    @include('purchase_items.itemListingTable')
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
        let tbl = $('#itemListingTable').DataTable({
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
            dom: "Blrtip",
            lengthMenu: [
                [100, 300, 500, 999, -1],
                [100, 300, 500, 999, "All"]
            ],
            pageLength: 100, // Default page length
            buttons: [
                'excel'
            ],
            buttons: [

                {
                    extend: 'excelHtml5',
                    text: '<i class="fas fa-file-excel"></i>{{ __('messages.pos_label.export_excel') }}',
                    className: 'btn btn-sm',
                    exportOptions: {
                        // Exclude the action column from the export
                        columns: function(idx, data, node) {
                            return idx !== 1;
                        }
                    }
                }


            ],
            ajax: {
                url: route('item-listing.index'),
                beforeSend: function() {
                    startLoader()
                },
                complete: function() {
                    stopLoader()
                },
            },

            order: [
                [1, 'asc']
            ],
            columns: [{
                    data: function(data, type, row, meta) {
                        return meta.row + 1;
                    },
                    title: 'SN',
                    orderable: false,
                    searchable: false,
                    width: '2%',
                    className: 'text-center'
                },
                {
                    data: 'code',
                    name: 'code',
                    visible: false
                },
                {
                    data: function(row) {
                        let element = document.createElement('textarea');
                        element.innerHTML = row.code ?? '';
                        return element.value;
                    },
                    name: 'code',
                    width: '7%',
                    className: 'text-center'
                },
                {
                    data: function(row) {
                        let element = document.createElement('textarea');
                        element.innerHTML = row.barcode ?? '';
                        return element.value;
                    },
                    name: 'barcode',
                    width: '8%',
                    className: 'text-center'
                },
                {
                    data: function(row) {
                        let element = document.createElement('textarea');
                        element.innerHTML = row.name;
                        return element.value;
                    },
                    name: 'name',
                    width: '15%',
                    className: 'text-center'
                },
                {
                    data: function(row) {
                        let element = document.createElement('textarea');
                        element.innerHTML = row.qty_current ?? 0;
                        return element.value;
                    },
                    name: 'qty_current',
                    className: 'text-center',
                    width: '5%'
                },
                {
                    data: function(row) {
                        let element = document.createElement('textarea');
                        element.innerHTML = row.unit?.title ?? '';
                        return element.value;
                    },
                    name: 'unit.title',
                    className: 'text-center',
                    width: '6%'
                },
                {
                    data: function(row) {
                        let price = row.cost_price !== null && row.cost_price !== undefined ? row.cost_price
                            .toFixed(2) : '0.00';
                        return price;
                    },
                    className: 'text-center',
                    name: 'price',
                    width: '7%'
                },
                {
                    data: function(row) {
                        let price = row.price !== null && row.price !== undefined ? row.price.toFixed(2) :
                            '0.00';
                        return price;
                    },
                    className: 'text-center',
                    name: 'price',
                    width: '7%'
                }
            ],


            responsive: true // Enable responsive features
        });
    </script>
@endsection
