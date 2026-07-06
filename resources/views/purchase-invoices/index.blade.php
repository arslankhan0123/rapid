@extends('layouts.app')
@section('title')
    {{ __('messages.purchase-invoices.name') }}
@endsection
@section('page_css')
<link href="{{ asset('assets/css/buttons.dataTables.min.css') }}" rel="stylesheet" type="text/css" />
@endsection
@section('css')
    @livewireStyles
@endsection
@section('content')
    <section class="section">
        <div class="section-header">
            <h1>{{ __('messages.purchase-invoices.name') }}</h1>
            <div class="section-header-breadcrumb float-right">
                <div class="card-header-action mr-3 select2-mobile-margin">
                    {{-- {{Form::select('status', $statusArr, null, ['id' => 'filterStatus', 'class' => 'form-control','placeholder' =>__('messages.placeholder.select_status')]) }} --}}
                </div>
            </div>
            <div class="float-right">
                @can('create_purchase_invoices')
                    <a href="{{ route('purchase-invoices.create') }}"
                        class="btn btn-primary form-btn">{{ __('messages.common.add') }}
                    </a>
                @endcan

            </div>
        </div>
        <div class="section-body">
            @include('flash::message')
            <div class="card">
                <div class="card-body">
                    @include('purchase-invoices.table')
                </div>
            </div>
        </div>
    </section>
@endsection
@section('page_scripts')
@endsection
@section('scripts')
    <script>
        let customerId = null;
    </script>
    <script src="{{ mix('assets/js/estimates/estimates-datatable.js') }}"></script>
    <script src="{{ mix('assets/js/status-counts/status-counts.js') }}"></script>
    <script src="{{ asset('assets/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ mix('assets/js/custom/custom-datatable.js') }}"></script>
    <script src="{{ mix('assets/js/select2.min.js') }}"></script>


    <script src="{{ asset('assets/js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('assets/js/jszip.min.js') }}"></script>
    <script src="{{ asset('assets/js/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('assets/js/buttons.print.min.js') }}"></script>
    <script src="{{ asset('assets/js/buttons.colVis.min.js') }}"></script>

    <script>
        let tbl = $('#designationTable').DataTable({
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
              dom: "B" + // Buttons
                "rt",

            buttons: [

                {
                    extend: 'excelHtml5',
                    text: '<i class="fas fa-file-excel"></i>{{ __('messages.pos_label.export_excel') }}',
                    className: 'btn btn-sm',
                    exportOptions: {
                        // Exclude the action column from the export
                        columns: function(idx, data, node) {
                            return idx !== 6;
                        }
                    }
                }


            ],
            ajax: {
                url: route('purchase-invoices.index'),
            },
            columns: [{
                    data: function(row) {
                        return row.estimate_number ?? '';
                    },
                    name: 'estimate_number',

                    width: '12%'
                },
                {
                    data: function(row) {
                        let element = document.createElement('textarea');
                        element.innerHTML = row.po_number ?? '';
                        return element.value;
                    },
                    name: 'po_number',

                },
                {
                    data: function(row) {
                        // Convert created_at to Date object and format it as d-m-Y
                        let date = new Date(row.created_at);
                        if (!isNaN(date)) {
                            // Format the date as day-month-year (d-m-Y)
                            let day = String(date.getDate()).padStart(2, '0');
                            let month = String(date.getMonth() + 1).padStart(2,
                                '0'); // Months are zero-based
                            let year = date.getFullYear();
                            return `${day}-${month}-${year}`;
                        } else {
                            return ''; // Handle case where created_at is null or invalid
                        }
                    },
                    name: 'created_at',
                },

                {
                    data: function(row) {
                        let element = document.createElement('textarea');
                        element.innerHTML = row.customer ? row.customer.company_name ?? '' : row
                            .customer_name ?? '';
                        return element.value;
                    },
                    name: 'customer.company_name',
                },
                {
                    data: function(row) {
                        let element = document.createElement('textarea');
                        element.innerHTML = row.discount;

                        // Check the discount_type and append either $ or %
                        if (row.discount_type == 1) {
                            return `${element.value}`; // Append $ for type 1
                        } else if (row.discount_type == 2) {
                            return `${element.value}%`; // Append % for type 2
                        } else {
                            return element.value; // Fallback if discount_type is not 1 or 2
                        }
                    },
                    name: 'discount',
                    width: '10%',
                    className: 'text-right'
                },

                {
                    data: function(row) {
                        return parseFloat(row.total_amount).toFixed(2);
                    },
                    name: 'total_amount',
                    className: 'text-right'
                },

                {
                    data: function(row) {
                        return renderActionButtons(row.id);
                    },
                    name: 'id',
                    width: '11%'
                }
            ],
            responsive: true // Enable responsive features
        });

        $(document).on('click', '.edit-btn', function(event) {
            let did = $(event.currentTarget).data('id');
            const url = route('purchase-invoices.edit', did);
            window.location.href = url;
        });

        $(document).on('click', '.delete-btn', function(event) {
            let assetCateogryId = $(event.currentTarget).data('id');
            deleteItem(route('purchase-invoices.destroy', assetCateogryId), '#designationTable',
                '{{ __('messages.purchase-invoices.name') }}');
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
            updateItem: "{{ auth()->user()->can('edit_purchase_invoices') ? 'true' : 'false' }}",
            deleteItem: "{{ auth()->user()->can('delete_purchase_orders') ? 'true' : 'false' }}",
            viewItem: "{{ auth()->user()->can('view_purchase_invoices') ? 'true' : 'false' }}"
        };

        // Function to render action buttons based on permissions
        function renderActionButtons(id) {
            let buttons = '';



            if (permissions.updateItem === 'true') {
                let editUrl = `{{ route('purchase-invoices.edit', ':id') }}`;
                editUrl = editUrl.replace(':id', id);
                buttons += `
                <a title="${messages.edit}" href="${editUrl}" class="btn btn-warning action-btn has-icon edit-btn" style="float:right;margin:2px;">
                    <i class="fa fa-edit"></i>
                </a>
            `;
            }
            if (permissions.viewItem === 'true') {
                let viewUrl = `{{ route('purchase-invoices.view', ':id') }}`;
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
