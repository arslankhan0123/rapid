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
        
        <!-- Image Preview Modal -->
        <div class="modal fade" id="imagePreviewModal" tabindex="-1" role="dialog" aria-labelledby="imagePreviewModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="imagePreviewModalLabel">Image Preview</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body text-center">
                        <img src="" id="previewModalImage" class="img-fluid" alt="Preview Image" style="max-height: 400px; object-fit: contain;">
                    </div>
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
                        if (row.image) {
                            let imageUrl = row.image.startsWith('/') ? row.image : '/' + row.image;
                            let title = row.full_name ?? row.name ?? 'Image';
                            return `<a href="javascript:void(0)" class="view-image-btn" data-image-url="${imageUrl}" data-title="${title}">
                                        <img src="${imageUrl}" width="50" height="50" style="object-fit:cover; border-radius:4px;" alt="Image" />
                                    </a>`;
                        }
                        return '';
                    },
                    name: 'image',
                    width: '7%',
                    className: 'text-center'
                },
                {
                    data: function(row) {
                        return row.code ?? '';
                    },
                    name: 'code',
                    width: '7%',
                    className: 'text-center'
                },
                {
                    data: function(row) {
                        return row.full_name ?? row.name ?? '';
                    },
                    name: 'full_name',
                    width: '15%',
                    className: 'text-center'
                },
                {
                    data: function(row) {
                        let price = row.price !== null && row.price !== undefined ? parseFloat(row.price).toFixed(2) :
                            '0.00';
                        return price;
                    },
                    className: 'text-center',
                    name: 'price',
                    width: '7%'
                },
                {
                    data: function(row) {
                        return row.group?.name ?? '';
                    },
                    name: 'group.name',
                    width: '10%',
                    className: 'text-center'
                },
                {
                    data: function(row) {
                        return row.category?.name ?? '';
                    },
                    name: 'category.name',
                    width: '10%',
                    className: 'text-center'
                },
                {
                    data: function(row) {
                        if (row.created_at) {
                            let date = new Date(row.created_at);
                            if (isNaN(date.getTime())) {
                                // Fallback parsing if date is invalid, though JS parses ISO 8601 fine
                                date = new Date(row.created_at.replace(/-/g, "/"));
                            }
                            let day = String(date.getDate()).padStart(2, '0');
                            let month = String(date.getMonth() + 1).padStart(2, '0');
                            let year = date.getFullYear();
                            
                            let hours = date.getHours();
                            let minutes = String(date.getMinutes()).padStart(2, '0');
                            let seconds = String(date.getSeconds()).padStart(2, '0');
                            let ampm = hours >= 12 ? 'pm' : 'am';
                            hours = hours % 12;
                            hours = hours ? hours : 12; // the hour '0' should be '12'
                            let strHours = String(hours).padStart(2, '0');
                            
                            return day + '-' + month + '-' + year + ' ' + strHours + ':' + minutes + ':' + seconds + ampm;
                        }
                        return '';
                    },
                    name: 'created_at',
                    width: '15%',
                    className: 'text-center'
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

        $(document).on('click', '.view-image-btn', function() {
            let imageUrl = $(this).data('image-url');
            let title = $(this).data('title');
            $('#imagePreviewModalLabel').text(title);
            $('#previewModalImage').attr('src', imageUrl);
            
            // Append modal to body to avoid z-index/backdrop issues
            $('#imagePreviewModal').appendTo("body").modal('show');
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
