@extends('layouts.app')
@section('title')
    {{ __('messages.purchase-items.name') }}
@endsection
@section('page_css')
    <link href="{{ asset('assets/css/jquery.dataTables.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="{{ asset('assets/css/bs4-summernote/summernote-bs4.css') }}">
@endsection
@section('content')
    <section class="section">
        <div class="section-header item-align-right">
            <h1>{{ __('messages.purchase-items.name') }}</h1>
            <div class="section-header-breadcrumb float-right">
                <div class="card-header-action mr-3 select2-mobile-margin">
                </div>
            </div>

            <div class="float-right">
                @can('create_purchase_items')
                    <a href="{{ route('purchase-items.create') }}" class="btn btn-primary form-btn">
                        {{ __('messages.purchase-items.add') }} </a>
                @endcan

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
                    @include('purchase_items.table_unit')
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
@endsection
@section('scripts')
    <script src="{{ mix('assets/js/custom/input-price-format.js') }}"></script>
    <script>
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
                url: route('purchase-items.index'),
                beforeSend: function() {
                    startLoader()
                },
                complete: function() {
                    stopLoader()
                },
            },
            order: [
                [0, 'desc']
            ],
            lengthMenu: [
                [100, 300, 500, 999, -1],
                [100, 300, 500, 999, "All"]
            ],
            pageLength: 100, // Default page length
            columns: [{
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
                        element.innerHTML = row.category?.name ?? '';
                        return element.value;
                    },
                    name: 'category.name',
                    width: '8%',
                    className: 'text-center'
                },
                {
                    data: function(row) {
                        let element = document.createElement('textarea');
                        element.innerHTML = row.description;
                        return element.value;
                    },
                    name: 'description',
                    width: '10%',
                    className: 'text-center'
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
                },
                {
                    data: function(row) {
                        return renderActionButtons(row.id);
                    },
                    name: 'id',
                    width: '7%',
                    className: 'text-center'
                }
            ],


            responsive: true // Enable responsive features
        });
        $(document).on('click', '.edit-btn', function(event) {
            let assetCateogryId = $(event.currentTarget).data('id');
            const url = route('purchase-items.edit', assetCateogryId);
            window.location.href = url;
        });

        $(document).on('click', '.delete-btn', function(event) {
            let assetCateogryId = $(event.currentTarget).data('id');
            deleteItem(route('purchase-items.destroy', assetCateogryId), '#assetCategoryTable',
                "{{ __('messages.purchase-items.short_name') }}");
        });
    </script>

    <script>
        // Define messages for translations
        var messages = {
            delete: "{{ __('messages.common.delete') }}",
            edit: "{{ __('messages.common.edit') }}",
            view: "{{ __('messages.common.view') }}"
        };
        // Define permissions
        var permissions = {
            updateItem: "{{ auth()->user()->can('update_purchase_items') ? 'true' : 'false' }}",
            deleteItem: "{{ auth()->user()->can('delete_purchase_items') ? 'true' : 'false' }}",
            viewItem: "{{ auth()->user()->can('view_purchase_items') ? 'true' : 'false' }}"
        };
        // Function to render action buttons based on permissions
        function renderActionButtons(id) {
            let buttons = '';
            if (permissions.updateItem === 'true') {
                let editUrl = `{{ route('purchase-items.edit', ':id') }}`;
                editUrl = editUrl.replace(':id', id);
                buttons += `
                <a title="${messages.edit}" href="${editUrl}" class="btn btn-warning action-btn has-icon edit-btn" style="float:right;margin:2px;">
                    <i class="fa fa-edit"></i>
                </a>
            `;
            }
            if (permissions.viewItem === 'true') {
                let viewUrl = `{{ route('purchase-items.view', ':id') }}`;
                viewUrl = viewUrl.replace(':id', id);
                buttons += `
                <a title="${messages.view}" href="${viewUrl}" class="btn btn-info action-btn has-icon view-btn" style="float:right;margin:2px;">
                    <i class="fa fa-eye"></i>
                </a>
            `;
            }

            if (permissions.deleteItem === 'true') {
                buttons += `
                <a title="${messages.delete}" href="#" class="btn btn-danger action-btn has-icon delete-btn" data-id="${id}" style="float:right;margin:2px;">
                    <i class="fa fa-trash"></i>
                </a>
            `;
            }
            return buttons;
        }
    </script>
@endsection
