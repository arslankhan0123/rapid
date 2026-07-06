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
        </div>
        <div class="section-body">
            <div class="card">
                <div class="card-body">
                    @include('listing.payments.table')
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
        let paymentUrl = "{{ route('payments.list.index') }}/";
        let paymentViewUrl = "{{ route('payments.list.show') }}/";
        let ownerType = 'App\\Models\\Invoice';
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
                url: route('payments.list.index'),
            },
            columns: [{
                    data: 'updated_at', // Add the updated_at column
                    name: 'updated_at',
                    visible: false // Make the column invisible
                },
                {
                    data: function(row) {
                        let element = document.createElement('textarea');
                        element.innerHTML = row.payment_mode.name ?? '';
                        return element.value;
                    },
                    name: 'paymentMode.name',
                    width: '10%'
                },

                {
                    data: function(row) {
                        let element = document.createElement('textarea');
                        element.innerHTML = row.note ?? 'N/A';
                        return element.value;
                    },
                    name: 'note',
                    width: '18%',
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
                    width: '15%',
                    orderable: false
                },

                {
                    data: function(row) {
                        let element = document.createElement('textarea');
                        element.innerHTML = row.transaction_id ?? '';
                        return element.value;
                    },
                    name: 'transaction_id',
                    width: '18%',
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
                    width: '18%',
                    orderable: false
                },


                {
                    data: function(row) {
                        return renderActionButtons(row.id);
                    },
                    name: 'id',
                    width: '200px',
                    orderable: false
                }
            ],
            order: [
                [1, 'desc']
            ],
            responsive: true // Enable responsive features
        });

        $(document).on('click', '.delete-btn', function(event) {
            let assetCateogryId = $(event.currentTarget).data('id');
            deleteItem(route('task-assign.destroy', assetCateogryId), '#designationTable',
                '{{ __('messages.task-assign.name') }}');
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
            updateItem: "{{ auth()->user()->can('manage_payments') ? 'true' : 'false' }}",
            deleteItem: "{{ auth()->user()->can('manage_payments') ? 'true' : 'false' }}",
            viewItem: "{{ auth()->user()->can('manage_payments') ? 'true' : 'false' }}"
        };
        // Function to render action buttons based on permissions
        function renderActionButtons(id) {
            let buttons = '';
            if (permissions.updateItem === 'true') {
                let editUrl = `{{ route('task-assign.edit', ':id') }}`;
                editUrl = editUrl.replace(':id', id);
                buttons += `
                <a title="${messages.edit}" href="${editUrl}" class="btn btn-warning action-btn has-icon edit-btn" style="float:right;margin:2px;">
                    <i class="fa fa-edit"></i>
                </a>
            `;
            }
            if (permissions.viewItem === 'true') {
                let viewUrl = `{{ route('task-assign.view', ':id') }}`;
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
