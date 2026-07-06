@extends('layouts.app')
@section('title')
    {{ __('messages.cash-transfers.name') }}
@endsection
@section('page_css')
    <link href="{{ asset('assets/css/jquery.dataTables.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="{{ asset('assets/css/bs4-summernote/summernote-bs4.css') }}">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.3.6/css/buttons.dataTables.min.css">
@endsection
@section('content')
    <section class="section">
        <div class="section-header item-align-right">
            <h1>{{ __('messages.cash-transfers.name') }} </h1>
            <div class="section-header-breadcrumb float-right">
                <div class="card-header-action mr-3 select2-mobile-margin">
                </div>
            </div>
            @can('create_leave_groups')
                <div class="float-right">
                    {{ Form::select('branches', $usersBranches ?? [], null, ['id' => 'filterBranch', 'class' => 'form-control select2', 'placeholder' => __('messages.placeholder.branches')]) }}

                    <a href="{{ route('cash-transfers.create') }}" class="btn btn-primary ml-2" style="line-height: 30px;">
                        {{ __('messages.cash-transfers.add') }} </a>
                </div>
            @endcan
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
                    @include('cash-transfers.table')
                </div>
            </div>
        </div>
    </section>
    @include('cash-transfers.templates.templates')
@endsection
@section('page_scripts')
    <script src="{{ asset('assets/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ mix('assets/js/custom/custom-datatable.js') }}"></script>
    <script src="{{ mix('assets/js/bs4-summernote/summernote-bs4.js') }}"></script>
    <script src="{{ mix('assets/js/select2.min.js') }}"></script>

    <script src="https://cdn.datatables.net/buttons/2.3.6/js/dataTables.buttons.min.js"></script>

    <script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.print.min.js"></script>
    <!-- DataTables and Buttons Extension JS -->
    <script src="https://cdn.datatables.net/buttons/2.3.6/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.colVis.min.js"></script>

    <!-- pdfmake for PDF export -->
    <script src="{{ asset('assets/js/jszip.min.js') }}"></script>
    <script src="{{ asset('assets/js/pdfmake.min.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.68/vfs_fonts.js"></script>
@endsection
@section('scripts')
    <script src="{{ mix('assets/js/custom/input-price-format.js') }}"></script>
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
            ajax: {
                url: route('cash-transfers.index'),
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
            columns: [{
                    data: function(row) {
                        let element = document.createElement('textarea');
                        element.innerHTML = row.branch?.name ?? '';
                        return element.value;
                    },
                    name: 'branch.name',

                }, {
                    data: function(row) {
                        let element = document.createElement('textarea');
                        element.innerHTML = row.transfer_id;
                        return element.value;
                    },
                    name: 'transfer_id',

                }, {
                    data: function(row) {
                        let element = document.createElement('textarea');
                        element.innerHTML = row.from_account?.account_name;
                        return element.value;
                    },
                    name: 'from_account.account_name',

                }, {
                    data: function(row) {
                        let element = document.createElement('textarea');
                        element.innerHTML = row.to_account?.account_name;
                        return element.value;
                    },
                    name: 'to_account.account_name',

                }, {
                    data: function(row) {
                        let element = document.createElement('p');
                        let formattedAmount = parseFloat(row.transfer_amount).toFixed(
                            2); // Format the amount to 2 decimal places
                        element.textContent = formattedAmount;
                        element.classList.add('text-right'); // Add class to the span for right-alignment
                        return element.outerHTML;
                    },
                    name: 'opening_balance',
                    width: '25%'
                },
                {
                    data: function(row) {
                        return renderActionButtons(row.id);
                    },
                    name: 'id',

                }
            ],
            responsive: true,
            dom: "Bfrtip", // Ensure Buttons (B) is part of the DOM structure
            buttons: [
                @can('export_accounts')
                    {
                        extend: 'csvHtml5',
                        text: '<i class="fas fa-file-csv"></i> Export CSV',
                        className: 'btn btn-sm',
                        title: 'Cash Transfers', // Set the custom file name here
                        exportOptions: {
                            // Exclude the action column from the export
                            columns: function(idx, data, node) {
                                return idx !== 5; // Exclude the action column at index 4
                            }
                        }
                    }, {
                        extend: 'excelHtml5',
                        text: '<i class="fas fa-file-excel"></i> Export Excel',
                        className: 'btn btn-sm',
                        title: 'Cash Transfers',
                        exportOptions: {
                            // Exclude the action column from the export
                            columns: function(idx, data, node) {
                                return idx !== 5; // Exclude the action column at index 4
                            }
                        }
                    }, {
                        extend: 'pdfHtml5',
                        text: '<i class="fas fa-file-pdf"></i> Export PDF',
                        className: 'btn btn-sm', // Styled button
                        orientation: 'portrait',
                        title: 'Cash Transfers',
                        pageSize: 'A4',
                        exportOptions: {
                            // Exclude the action column from the export
                            columns: function(idx, data, node) {
                                return idx !== 5; // Exclude the action column at index 3
                            }
                        },
                        customize: function(doc) {
                            // Optional customization of the PDF
                            doc.content[1].table.widths =
                                Array(doc.content[1].table.body[0].length + 1).join('*').split('');

                            // Right-align the last column
                            var lastColumnIndex = doc.content[1].table.body[0].length -
                                1; // Get index of the last column
                            doc.content[1].table.body.forEach(function(row, rowIndex) {
                                if (rowIndex > 0) { // Skip the header row
                                    row[lastColumnIndex].alignment = 'right';
                                }
                            });
                        }
                    }
                @endcan
            ],
            pageLength: 10
        });

        $('#filterBranch').change(function() {
            tbl.ajax.reload(); // Reload DataTable with new status filter
        });
        $(document).on('click', '.edit-btn', function(event) {
            let did = $(event.currentTarget).data('id');
            const url = route('cash-transfers.edit', did);
            window.location.href = url;
        });
        $(document).on('click', '.delete-btn', function(event) {
            let assetCateogryId = $(event.currentTarget).data('id');
            deleteItem(route('cash-transfers.destroy', assetCateogryId), '#designationTable',
                '{{ __('messages.cash-transfers.name') }}');
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
            updateItem: "{{ auth()->user()->can('update_cash_transfers') ? 'true' : 'false' }}",
            deleteItem: "{{ auth()->user()->can('delete_cash_transfers') ? 'true' : 'false' }}",
            viewItem: "{{ auth()->user()->can('view_cash_transfers') ? 'true' : 'false' }}"
        };

        // Function to render action buttons based on permissions
        function renderActionButtons(id) {
            let buttons = '';
            if (permissions.updateItem === 'true') {
                let editUrl = `{{ route('cash-transfers.edit', ':id') }}`;
                editUrl = editUrl.replace(':id', id);
                buttons += `
                <a title="${messages.edit}" href="${editUrl}" class="btn btn-warning action-btn has-icon edit-btn" style="float:right;margin:2px;">
                    <i class="fa fa-edit"></i>
                </a>
            `;
            }
            if (permissions.viewItem === 'true') {
                let viewUrl = `{{ route('cash-transfers.view', ':id') }}`;
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
