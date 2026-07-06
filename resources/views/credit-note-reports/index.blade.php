@extends('layouts.app')

@section('title')
    {{ __('messages.credit_note_reports.name') }}
@endsection

@section('page_css')
    <link href="{{ asset('assets/css/jquery.dataTables.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/buttons.dataTables.min.css') }}" rel="stylesheet" type="text/css" />
    <style>
        .filter-section {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            border: 1px solid #eaeaea;
        }

        .filter-section label {
            font-weight: 600;
            color: #34395e;
        }

        .filter-actions {
            margin-top: 10px;
        }

        .card-report {
            border-top: 3px solid #6777ef;
        }

        /* Make the page-length dropdown look like the search input */
        .dataTables_length {
            display: flex;
            align-items: center;
            gap: .5rem;
        }

        .dataTables_length label {
            font-weight: 600;
            color: #666 !important;
            font-size: 14px;
            letter-spacing: .5px;
        }

        .dataTables_length select {
            width: 65px;
            display: inline-block;
            border: 1px solid #e4e6fc !important;
            border-radius: .25rem;
            padding: .25rem .5rem;
            font-size: .875rem;
            line-height: 1.5;
            background-color: #fff;
            height: 42px;
        }

        .dataTables_length select:focus {
            background-color: #fefeff !important;
            border-color: #95a0f4 !important;
        }

        /* Voice Search */
        #voiceSearchBtn {
            position: absolute;
            top: 41%;
            right: 25px;
            transform: translateY(-50%);
            color: #6ec6f0;
            font-size: 18px;
            cursor: pointer;
        }

        #voiceSearchBtn.listening {
            color: red;
        }
    </style>
@endsection

@section('content')
    <section class="section">
        <div class="section-header d-flex justify-content-between align-items-center">
            <h1>{{ __('messages.credit_note_reports.name') }}</h1>
            <div class="dropdown">
                <button class="btn btn-info dropdown-toggle" type="button" id="exportDropdown" data-toggle="dropdown"
                    aria-haspopup="true" aria-expanded="false" style="line-height:30px;">
                    Export
                </button>
                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="exportDropdown">
                    <a class="dropdown-item" href="#" onclick="exportData('xls')">Excel</a>
                    <a class="dropdown-item" href="#" onclick="exportData('pdf')">PDF</a>
                </div>
            </div>
        </div>

        <div class="section-body">
            <div class="filter-section">
                <form id="filterForm">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>{{ __('messages.credit_note_reports.filter.customer') }}</label>
                                <select name="customer_name" id="filterCustomer" class="form-control select2">
                                    <option value="">{{ __('messages.sales_reports.filter.select_customer') }}
                                    </option>
                                    <option value="all">{{ __('messages.sales_reports.filter.all_customers') }}</option>
                                    @foreach ($customers as $id => $name)
                                        <option value="{{ $id }}">{{ $name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label>{{ __('messages.sales_reports.filter.start_date') }}</label>
                                <input type="text" name="start_date" id="filterStartDate" placeholder="dd-mm-yyyy"
                                    class="form-control datepicker" value="{{ request('start_date') }}" autocomplete="off">
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label>{{ __('messages.sales_reports.filter.end_date') }}</label>
                                <input type="text" name="end_date" id="filterEndDate" placeholder="dd-mm-yyyy"
                                    class="form-control datepicker" value="{{ request('end_date') }}" autocomplete="off">
                            </div>
                        </div>

                        <div class="col-md-2">
                            <div class="form-group">
                                <label>&nbsp;</label>
                                <div class="d-flex" style="gap:5px;">
                                    <button type="button" id="btnReset" class="btn btn-secondary" data-toggle="tooltip"
                                        title="{{ __('messages.sales_reports.reset') }}">
                                        <i class="fas fa-redo"></i>
                                    </button>
                                    <button type="submit" class="btn btn-primary" data-toggle="tooltip"
                                        title="{{ __('messages.common.show') }}">
                                        <i class="fas fa-filter"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <div class="card card-report">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="creditNoteReportTable">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Credit Note Number</th>
                                    <th>Customer</th>
                                    <th>Credit Note Date</th>
                                    <th>Discount</th>
                                    <th>Taxable Amount</th>
                                    <th>Tax Amount</th>
                                    <th>Total Amount</th>
                                </tr>
                            </thead>
                            <tfoot>
                                <tr>
                                    <th colspan="4" style="text-align:right">Total:</th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('page_scripts')
    <script src="{{ asset('assets/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ mix('assets/js/custom/custom-datatable.js') }}"></script>
    <script src="{{ mix('assets/js/select2.min.js') }}"></script>
    <script src="{{ asset('assets/js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('assets/js/jszip.min.js') }}"></script>
    <script src="{{ asset('assets/js/buttons.html5.min.js') }}"></script>
    <!-- Bootstrap Datepicker -->
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            'use strict';

            // Initialize Select2
            $('.select2').select2({
                width: '100%',
                placeholder: "{{ __('messages.sales_reports.filter.select_customer') }}"
            });

            // Initialize datepicker
            $('.datepicker').datepicker({
                format: 'dd-mm-yyyy',
                autoclose: true,
                todayHighlight: true,
                clearBtn: true,
                changeMonth: true,
                changeYear: true
            });

            // Set initial filter values from URL parameters (if any)
            function setInitialFilterValues() {
                const urlParams = new URLSearchParams(window.location.search);

                if (urlParams.has('customer_name')) {
                    const customerValue = urlParams.get('customer_name');
                    $('#filterCustomer').val(customerValue).trigger('change');
                }

                if (urlParams.has('from_date')) {
                    $('#filterStartDate').val(urlParams.get('from_date'));
                }

                if (urlParams.has('to_date')) {
                    $('#filterEndDate').val(urlParams.get('to_date'));
                }
            }

            // Call this on page load
            setInitialFilterValues();

            // Initialize DataTable
            let creditNoteReportTable = $('#creditNoteReportTable').DataTable({
                footerCallback: function(row, data, start, end, display) {
                    let api = this.api();

                    let parseNumber = function(value) {
                        if (typeof value === 'string') {
                            return parseFloat(value.replace(/[^\d.-]/g, '')) || 0;
                        }
                        return typeof value === 'number' ? value : 0;
                    };

                    // Calculate totals by iterating through all rows
                    let discountTotal = 0;
                    let taxableTotal = 0;
                    let taxTotal = 0;
                    let amountTotal = 0;

                    // Loop through all data on current page
                    for (let i = 0; i < data.length; i++) {
                        const row = data[i];

                        // Get values from the row
                        const rowDiscount = parseFloat(row.discount) || 0;
                        const rowTotalAmount = parseFloat(row.total_amount) || 0;

                        // Calculate tax from sales items
                        let rowTax = 0;
                        if (row.sales_items && row.sales_items.length > 0) {
                            rowTax = row.sales_items.reduce((sum, item) => sum + parseFloat(item.tax ||
                                0), 0);
                        }

                        const rowTaxable = rowTotalAmount - rowTax;

                        discountTotal += rowDiscount;
                        taxableTotal += rowTaxable;
                        taxTotal += rowTax;
                        amountTotal += rowTotalAmount;
                    }

                    // Update footer
                    $(api.column(4).footer()).html(discountTotal.toFixed(2));
                    $(api.column(5).footer()).html(taxableTotal.toFixed(2));
                    $(api.column(6).footer()).html(taxTotal.toFixed(2));
                    $(api.column(7).footer()).html(amountTotal.toFixed(2));
                },
                oLanguage: {
                    'sEmptyTable': "{{ __('messages.common.no_data_available_in_table') }}",
                    'sInfo': "{{ __('messages.common.data_base_entries') }}",
                    sLengthMenu: "{{ __('messages.common.menu_entry') }}",
                    sInfoEmpty: "{{ __('messages.common.no_entry') }}",
                    sInfoFiltered: "{{ __('messages.common.filter_by') }}",
                    sZeroRecords: "{{ __('messages.common.no_matching') }}",
                },
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('credit-note-reports.index') }}",
                    data: function(d) {
                        d.customer_name = $('#filterCustomer').val() === 'all' ? '' : $(
                            '#filterCustomer').val();
                        d.from_date = $('#filterStartDate').val();
                        d.to_date = $('#filterEndDate').val();
                    }
                },
                pageLength: 10,
                lengthMenu: [
                    [10, 20, 50, 100, -1],
                    [10, 20, 50, 100, "All"]
                ],
                lengthChange: true,
                dom: "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>" +
                    "<'row'<'col-sm-12'tr>>" +
                    "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
                columns: [{
                        data: null,
                        name: 'serial',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row, meta) {
                            return meta.row + meta.settings._iDisplayStart + 1;
                        },
                        width: '5%'
                    },
                    {
                        data: 'credit_note_number',
                        name: 'credit_note_number',
                        width: '15%'
                    },
                    {
                        data: 'customer_name',
                        name: 'customer.company_name',
                        orderable: false,
                        render: function(data, type, row) {
                            return row.customer ? row.customer.company_name : 'N/A';
                        },
                        width: '25%'
                    },
                    {
                        data: 'credit_note_date',
                        name: 'credit_note_date',
                        render: function(data) {
                            return data ? moment(data).format('DD MMM YYYY') : '';
                        },
                        width: '15%'
                    },
                    {
                        data: 'discount',
                        name: 'discount',
                        className: 'text-right',
                        width: '12%',
                        render: function(data) {
                            return data ? parseFloat(data).toFixed(2) : '0.00';
                        }
                    },
                    {
                        data: null,
                        name: 'taxable_amount',
                        className: 'text-right',
                        width: '14%',
                        searchable: false,
                        orderable: false,
                        render: function(data, type, row) {
                            // Calculate taxable amount: total_amount - tax_amount
                            const totalAmount = parseFloat(row.total_amount) || 0;
                            let taxAmount = 0;

                            // Calculate tax from sales items
                            if (row.sales_items && row.sales_items.length > 0) {
                                taxAmount = row.sales_items.reduce((sum, item) => sum + parseFloat(
                                    item.tax || 0), 0);
                            }

                            return (totalAmount - taxAmount).toFixed(2);
                        }
                    },
                    {
                        data: null,
                        name: 'tax_amount',
                        className: 'text-right',
                        width: '12%',
                        searchable: false,
                        orderable: false,
                        render: function(data, type, row) {
                            // Calculate tax amount from sales items
                            if (row.sales_items && row.sales_items.length > 0) {
                                const taxAmount = row.sales_items.reduce((sum, item) => sum +
                                    parseFloat(item.tax || 0), 0);
                                return taxAmount.toFixed(2);
                            }
                            return '0.00';
                        }
                    },
                    {
                        data: 'total_amount',
                        name: 'total_amount',
                        className: 'text-right',
                        width: '12%',
                        render: function(data) {
                            return data ? parseFloat(data).toFixed(2) : '0.00';
                        }
                    }
                ],
                order: [
                    [3, 'desc']
                ]
            });

            // Update URL with current filters
            function updateURL() {
                const customerValue = $('#filterCustomer').val();
                const startDate = $('#filterStartDate').val();
                const endDate = $('#filterEndDate').val();

                const params = new URLSearchParams();

                if (customerValue && customerValue !== 'all' && customerValue !== '') {
                    params.append('customer_name', customerValue);
                }
                if (startDate) {
                    params.append('from_date', startDate);
                }
                if (endDate) {
                    params.append('to_date', endDate);
                }

                const queryString = params.toString();
                const newUrl = "{{ route('credit-note-reports.index') }}" + (queryString ? '?' + queryString : '');

                // Update URL without page reload
                window.history.pushState({}, '', newUrl);
            }

            // Filter form submission - WITHOUT PAGE RELOAD
            $('#filterForm').on('submit', function(e) {
                e.preventDefault(); // Prevent default form submission
                updateURL(); // Update URL with current filters
                creditNoteReportTable.ajax.reload(); // Reload table data via AJAX
                return false; // Extra safeguard against page reload
            });

            // Reset filters - WITHOUT PAGE RELOAD
            $('#btnReset').on('click', function(e) {
                e.preventDefault(); // Prevent any default behavior

                // Reset form fields
                $('#filterForm')[0].reset();

                // Clear Select2 dropdowns properly - set to empty and trigger change
                $('#filterCustomer').val(null).trigger('change');

                // Clear datepickers explicitly
                $('#filterStartDate').val('');
                $('#filterEndDate').val('');
                $('.datepicker').datepicker('update', '');

                // Clear URL parameters
                window.history.pushState({}, '', "{{ route('credit-note-reports.index') }}");

                // Reload table without filters
                creditNoteReportTable.ajax.reload();

                return false; // Extra safeguard against page reload
            });

            // Date validation
            $('#filterStartDate, #filterEndDate').on('change', function() {
                const from = $('#filterStartDate').val();
                const to = $('#filterEndDate').val();
                if (from && to) {
                    const fromDate = new Date(from.split('-').reverse().join('-'));
                    const toDate = new Date(to.split('-').reverse().join('-'));

                    if (fromDate > toDate) {
                        alert("{{ __('messages.common.date_range_validation') }}");
                        $('#filterEndDate').val('').datepicker('update', '');
                    }
                }
            });

            // Handle browser back/forward buttons
            window.addEventListener('popstate', function() {
                setInitialFilterValues();
                creditNoteReportTable.ajax.reload();
            });
        });

        function exportData(type) {
            const customer = $('#filterCustomer').val() === 'all' ? '' : $('#filterCustomer').val();
            const fromDate = $('#filterStartDate').val();
            const toDate = $('#filterEndDate').val();

            const url = "{{ route('credit-note-reports.export') }}?" +
                "customer_name=" + encodeURIComponent(customer || '') +
                "&from_date=" + encodeURIComponent(fromDate || '') +
                "&to_date=" + encodeURIComponent(toDate || '') +
                "&type=" + encodeURIComponent(type);

            window.location.href = url;
        }
    </script>
@endsection
