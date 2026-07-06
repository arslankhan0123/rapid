@extends('layouts.app')
@section('title')
    {{ __('messages.company_loans.company_loans') }}
@endsection
@section('page_css')
    <link href="{{ asset('assets/css/jquery.dataTables.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="https://cdn.datatables.net/buttons/2.3.6/css/buttons.dataTables.min.css" rel="stylesheet" type="text/css" />
    <style>
        .filter-container {
            display: flex;
            gap: 15px;
            align-items: center;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }

        .filter-select {
            min-width: 150px;
        }

        .dynamic-filter {
            display: none;
        }

        .total-summary {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            border-left: 4px solid #007bff;
        }

        .total-amount {
            font-size: 18px;
            font-weight: bold;
            color: #28a745;
        }

        @media (max-width: 768px) {
            .filter-container {
                flex-direction: column;
                align-items: center;
            }

            .filter-select {
                width: 100%;
            }
        }

        .filter-container {
            display: flex;
            gap: 15px;
            align-items: center;
            justify-content: center;
            /* Center horizontally */
            margin-bottom: 20px;
            flex-wrap: wrap;
        }
    </style>
@endsection
@section('content')
    <section class="section">
        <div class="section-header item-align-right">
            <h1>{{ __('messages.company_loans.company_loans') }}</h1>
            <div class="section-header-breadcrumb float-right">
                <div class="card-header-action mr-3 select2-mobile-margin">
                </div>
            </div>
            <div class="float-right">
                <a href="{{ route('company-loans.create') }}" class="btn btn-primary form-btn">
                    {{ __('messages.company_loans.add') }} </a>
            </div>
        </div>
        <div class="section-body">
            <div class="card">
                <div class="card-body">
                    <!-- Filters Section -->
                    <div class="filter-container">
                        <div class="form-group">
                            <label for="typeFilter">{{ __('messages.company_loans.type') }}</label>
                            <select id="typeFilter" class="form-control filter-select">
                                <option value="">{{ __('messages.common.all') }}</option>
                                <option value="customer">{{ __('messages.company_loans.customer') }}</option>
                                <option value="supplier">{{ __('messages.company_loans.supplier') }}</option>
                                <option value="personal">{{ __('messages.company_loans.personal') }}</option>
                            </select>
                        </div>

                        <!-- Customer Filter (initially hidden) -->
                        <div id="customerFilterContainer" class="form-group dynamic-filter">
                            <label for="customerFilter">{{ __('messages.company_loans.customer') }}</label>
                            <select id="customerFilter" class="form-control filter-select">
                                <option value="">{{ __('messages.common.all') }}</option>
                                @foreach ($customers as $id => $name)
                                    <option value="{{ $id }}">{{ $name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Supplier Filter (initially hidden) -->
                        <div id="supplierFilterContainer" class="form-group dynamic-filter">
                            <label for="supplierFilter">{{ __('messages.company_loans.supplier') }}</label>
                            <select id="supplierFilter" class="form-control filter-select">
                                <option value="">{{ __('messages.common.all') }}</option>
                                @foreach ($suppliers as $id => $name)
                                    <option value="{{ $id }}">{{ $name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="monthFilter">{{ __('messages.common.month') }}</label>
                            <select id="monthFilter" class="form-control filter-select">
                                <option value="">{{ __('messages.common.all') }}</option>
                                @foreach (range(1, 12) as $month)
                                    <option value="{{ $month }}">
                                        {{ DateTime::createFromFormat('!m', $month)->format('F') }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="yearFilter">{{ __('messages.common.year') }}</label>
                            <select id="yearFilter" class="form-control filter-select">
                                <option value="">{{ __('messages.common.all') }}</option>
                                @foreach (range(date('Y') - 5, date('Y')) as $year)
                                    <option value="{{ $year }}" {{ $year == date('Y') ? 'selected' : '' }}>
                                        {{ $year }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group" style="align-self: flex-end;">
                            <button id="resetFilters" class="btn btn-light">{{ __('messages.common.reset') }}</button>
                        </div>
                    </div>

                    @include('company_loans.table')
                </div>
            </div>
        </div>
    </section>
@endsection
@section('page_scripts')
    <script src="{{ asset('assets/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ mix('assets/js/custom/custom-datatable.js') }}"></script>
    <script src="{{ mix('assets/js/select2.min.js') }}"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.6/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.print.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
@endsection
@section('scripts')
    <script>
        'use strict';

        let companyLoanCreateUrl = route('company-loans.store');
        let companyLoansUrl = route('company-loans.index');
        let customers = @json($customers);
        let suppliers = @json($suppliers);

        // Pass current user's permissions to JS dynamically
        let permissions = {
            viewItem: @json(auth()->user()->can('view_company_loans')),
            updateItem: @json(auth()->user()->can('update_company_loans')),
            deleteItem: @json(auth()->user()->can('delete_company_loans'))
        };

        let tbl = $('#companyLoansTable').DataTable({
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
            dom: '<"d-flex justify-content-between mb-2"<"left-buttons"B l><"right-search"f>>rtip',
            buttons: [{
                extend: 'csv',
                text: '<i class="fas fa-file-csv"></i> CSV',
                className: 'btn btn-outline-primary btn-sm',
                exportOptions: {
                    columns: ':visible',
                    modifier: {
                        page: 'all'
                    }
                },
                filename: function() {
                    return 'company_loans_' + moment().format('YYYY-MM-DD_HH-mm-ss');
                }
            }],
            ajax: {
                url: companyLoansUrl,
                data: function(d) {
                    d.type = $('#typeFilter').val();
                    d.customer_id = $('#customerFilter').val();
                    d.supplier_id = $('#supplierFilter').val();
                    d.month = $('#monthFilter').val();
                    d.year = $('#yearFilter').val();
                },
                beforeSend: function() {
                    startLoader();
                },
                complete: function() {
                    stopLoader();
                }
            },
            lengthMenu: [
                [10, 25, 50, 100, -1],
                [10, 25, 50, 100, "All"]
            ],
            pageLength: 100,
            columns: [{
                    data: 'id',
                    name: 'id',
                    width: '5%',
                    className: "text-center"
                },
                {
                    data: 'date',
                    name: 'date',
                    render: function(data, type, row) {
                        return moment(data).format('DD MMM, YYYY');
                    },
                    width: '10%',
                    className: "text-center"
                },
                {
                    data: 'type',
                    name: 'type',
                    width: '10%',
                    className: "text-center"
                },
                {
                    data: 'name',
                    name: 'name',
                    width: '15%',
                    className: "text-left"
                },
                {
                    data: 'loan_amount',
                    name: 'loan_amount',
                    render: function(data, type, row) {
                        if (type === 'display' || type === 'filter') {
                            return parseFloat(data).toLocaleString('en-IN', {
                                minimumFractionDigits: 2,
                                maximumFractionDigits: 2
                            });
                        }
                        return data;
                    },
                    width: '15%',
                    className: "text-right"
                },
                {
                    data: 'loan_received_date',
                    name: 'loan_received_date',
                    render: function(data, type, row) {
                        return moment(data).format('DD MMM, YYYY');
                    },
                    width: '10%',
                    className: "text-center"
                },
                {
                    data: 'loan_refund_date',
                    name: 'loan_refund_date',
                    render: function(data, type, row) {
                        return moment(data).format('DD MMM, YYYY');
                    },
                    width: '10%',
                    className: "text-center"
                },
                {
                    data: 'number_of_days',
                    name: 'number_of_days',
                    width: '10%',
                    className: "text-center"
                },
                {
                    data: function(row) {
                        return renderActionButtons(row.id);
                    },
                    name: 'id',
                    width: '15%',
                    className: "text-center"
                }
            ],
            responsive: true,
            footerCallback: function(row, data, start, end, display) {
                var api = this.api();
                var total = api.column(4, {
                    page: 'current'
                }).data().reduce(function(a, b) {
                    return parseFloat(a) + parseFloat(b);
                }, 0);
                $(api.column(4).footer()).html(total.toLocaleString('en-IN', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                }));
            }
        });

        // Show/hide customer and supplier filters based on type selection
        $('#typeFilter').change(function() {
            var type = $(this).val();
            $('.dynamic-filter').hide();
            if (type === 'customer') {
                $('#customerFilterContainer').show();
            } else if (type === 'supplier') {
                $('#supplierFilterContainer').show();
            }
            $('#customerFilter, #supplierFilter').val('');
        });

        $('#typeFilter, #customerFilter, #supplierFilter, #monthFilter, #yearFilter').change(function() {
            tbl.ajax.reload();
        });

        $('#resetFilters').click(function() {
            $('#typeFilter, #customerFilter, #supplierFilter, #monthFilter, #yearFilter').val('');
            $('.dynamic-filter').hide();
            tbl.ajax.reload();
        });

        $(document).on('click', '.delete-btn', function(event) {
            let companyLoanId = $(event.currentTarget).data('id');
            deleteItem(route('company-loans.destroy', companyLoanId), '#companyLoansTable',
                '{{ __('messages.company_loans.company_loans') }}');
        });

        function renderActionButtons(id) {
            let buttons = '';

            if (permissions.deleteItem) {
                buttons += `<a title="{{ __('messages.common.delete') }}" href="#" class="btn btn-danger action-btn has-icon delete-btn" data-id="${id}" style="margin:2px;">
                            <i class="fa fa-trash"></i>
                        </a>`;
            }

            if (permissions.viewItem) {
                let viewUrl = `{{ route('company-loans.show', ':id') }}`.replace(':id', id);
                buttons += `<a title="{{ __('messages.common.view') }}" href="${viewUrl}" class="btn btn-info action-btn has-icon view-btn" style="margin:2px;">
                            <i class="fa fa-eye"></i>
                        </a>`;
            }

            if (permissions.updateItem) {
                let editUrl = `{{ route('company-loans.edit', ':id') }}`.replace(':id', id);
                buttons += `<a title="{{ __('messages.common.edit') }}" href="${editUrl}" class="btn btn-warning action-btn has-icon edit-btn" style="margin:2px;">
                            <i class="fa fa-edit"></i>
                        </a>`;
            }

            return `<div style="display:flex; justify-content:center;">${buttons}</div>`;
        }
    </script>
@endsection
