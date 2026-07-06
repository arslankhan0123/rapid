@extends('layouts.app')
@section('title')
    {{ __('messages.invoice.invoice_payments') }}
@endsection
@section('page_css')
    <link href="{{ asset('assets/css/jquery.dataTables.min.css') }}" rel="stylesheet" type="text/css" />
@endsection
@section('content')
    <section class="section">
        <div class="section-header">
            <h1>{{ __('messages.invoice.invoice_payments') }}</h1>
            <div class="section-header-breadcrumb float-right">

                <div class="card-header-action mr-3 select2-mobile-margin">
                </div>
            </div>
            @can('manage_payment_mode')
                <div class="float-right">
                    {{-- {{ Form::select('branches', $usersBranches ?? [], null, ['id' => 'filterBranch', 'class' => 'form-control select2', 'placeholder' => __('messages.placeholder.branches')]) }} --}}
                    <a href="{{ route('purchase-invoice-payments.list.exportCsv') }}" id="exportCsvButton"
                        class="btn btn-primary  mr-2 ml-2" style="line-height: 30px;">Export </a>
                    <a href="{{ route('purchase-invoice-payments.list.create') }}" class="btn btn-primary "
                        style="line-height: 30px;">
                        Add Payment </a>
                </div>
            @endcan
        </div>
        <div class="section-body">
            <div class="card">
                <div class="card-body">
                    @include('listing.purchase-payment.table')
                </div>
            </div>
        </div>
    </section>
@endsection
@section('page_scripts')
    <script src="{{ asset('assets/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ mix('assets/js/custom/custom-datatable.js') }}"></script>
@endsection
@section('scripts')
    <script>
        let paymentUrl = "{{ route('purchase-invoice-payments.list.index') }}/";

        let ownerType = 'App\\Models\\PurchaseInvoice';
    </script>
    <script src="{{ mix('assets/js/custom/get-price-format.js') }}"></script>
    {{-- <script src="{{mix('assets/js/listing/payments/payments.js')}}"></script> --}}

    <script>
        'use strict';


        let tbl = $('#paymentsTbl').DataTable({
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
                url: route('purchase-invoice-payments.list.index'),
                data: function(d) {
                    d.filterBranch = $("#filterBranch").val();
                },
                beforeSend: function() {
                    startLoader();
                },
                complete: function() {
                    stopLoader();
                }
            },
            lengthMenu: [
                [100, 300, 500, 999, -1],
                [100, 300, 500, 999, "All"]
            ],
            pageLength: 100, // Default page length
            columns: [{
                    data: 'updated_at', // Add the updated_at column
                    name: 'updated_at',
                    visible: false // Make the column invisible
                },
                {
                    data: function(row) {
                        let element = document.createElement('textarea');
                        element.innerHTML = row.branch?.name ?? '';
                        return element.value;
                    },
                    name: 'branch.name',
                    visible: false,
                },
                {
                    data: function(row) {
                        let element = document.createElement('textarea');
                        element.innerHTML = row.invoice?.estimate_number ?? '';
                        return element.value;
                    },
                    name: 'invoice.estimate_number',
                    className:'text-center'

                },
                {
                    data: function(row) {
                        let element = document.createElement('textarea');
                        element.innerHTML = row.payment_mode.name ?? '';
                        return element.value;
                    },
                    name: 'paymentMode.name',

                },

                {
                    data: function(row) {
                        let element = document.createElement('textarea');
                        element.innerHTML = row.note ?? 'N/A';
                        return element.value;
                    },
                    name: 'note',

                    orderable: false
                },

                {
                    data: function(row) {
                        if (row.payment_date) {
                            let date = new Date(row.payment_date);
                            // Manually extract day, month, and year, and format as 'd-m-y'
                            let day = date.getDate();
                            let month = date.getMonth() + 1; // Months are zero-indexed, so add 1
                            let year = date.getFullYear();

                            return `${day}-${month}-${year}`;
                        }
                        return ''; // Return empty string if no payment_date
                    },
                    name: 'payment_date',

                    orderable: false
                },

                {
                    data: function(row) {
                        let element = document.createElement('textarea');
                        element.innerHTML = row.transaction_id ?? '';
                        return element.value;
                    },
                    name: 'transaction_id',

                    orderable: false
                },
                {
                    data: function(row) {
                        let amount = parseFloat(row.amount_received) ||
                            0; // Parse as a float, default to 0 if null
                        let formattedAmount = amount.toFixed(2); // Ensure two decimal places
                        return `<div style="text-align: right;">${formattedAmount} SAR</div>`; // Align right
                    },
                    name: 'amount_received',

                    orderable: false
                },


                {
                    data: function(row) {
                        return renderActionButtons(row.id);
                    },
                    name: 'id',

                    orderable: false
                }
            ],
            order: [
                [1, 'desc']
            ],
            responsive: true // Enable responsive features
        });
        $('#filterBranch').change(function() {
            tbl.ajax.reload(); // Reload DataTable with new status filter
        });

        $(document).on('click', '.delete-btn', function(event) {
            let assetCateogryId = $(event.currentTarget).data('id');
            deleteItem(route('purchase-invoice-payments.list.destroy', assetCateogryId), '#paymentsTbl',
                '{{ __('messages.invoice.payment') }}');
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
            updateItem: "{{ auth()->user()->can('manage_payment_mode') ? 'true' : 'false' }}",
            deleteItem: "{{ auth()->user()->can('manage_payment_mode') ? 'true' : 'false' }}",
            viewItem: "{{ auth()->user()->can('manage_payment_mode') ? 'true' : 'false' }}"
        };

        // Function to render action buttons based on permissions
        function renderActionButtons(id) {


            let buttons = '';
            if (permissions.updateItem === 'true') {
                let editUrl = `{{ route('purchase-invoice-payments.list.edit', ':id') }}`;
                editUrl = editUrl.replace(':id', id);
                buttons += `
                <a title="${messages.edit}" href="${editUrl}" class="btn btn-warning action-btn has-icon edit-btn" style="float:right;margin:2px;">
                    <i class="fa fa-edit"></i>
                </a>
            `;
            }
            if (permissions.viewItem === 'true') {
                let viewUrl = `{{ route('purchase-invoice-payments.list.show', ':id') }}`;
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
