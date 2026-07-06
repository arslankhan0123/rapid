@extends('layouts.app')

@section('title')
    {{ __('messages.sales_tax_report.name') }}
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

        .filter-section {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }

        /* Optional: Adjust the spacing between form elements */
        .form-group {
            margin-bottom: 0;
        }

        /* Make the buttons align properly */
        .d-flex.align-items-end .form-group {
            display: flex;
            justify-content: flex-end;
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

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .filter-section .col-md-2 {
                margin-bottom: 15px;
            }

            .d-flex.align-items-end .form-group {
                justify-content: center;
            }
        }
    </style>
@endsection

@section('content')
    <section class="section">
        <div class="section-header d-flex justify-content-between align-items-center">
            <h1>{{ __('messages.sales_tax_report.menu') }}</h1>
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
            <!-- Filter Section -->
            <div class="filter-section">
                <form id="filterForm">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>{{ __('messages.sales_reports.filter.customer') }}</label>
                                <select name="customer_name" id="filterCustomer" class="form-control select2">
                                    <option value="">{{ __('messages.sales_reports.filter.select_customer') }}
                                    </option>
                                    <option value="all" {{ request('customer_name') == 'all' ? 'selected' : '' }}>
                                        {{ __('messages.sales_reports.filter.all_customers') }}
                                    </option>
                                    @foreach ($customers as $id => $name)
                                        <option value="{{ $id }}"
                                            {{ request('customer_name') == $id ? 'selected' : '' }}>
                                            {{ $name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label>{{ __('messages.sales_reports.filter.start_date') }}</label>
                                <input type="text" name="from_date" id="filterStartDate" placeholder="dd-mm-yyyy"
                                    class="form-control datepicker" value="{{ request('from_date') }}" autocomplete="off">
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label>{{ __('messages.sales_reports.filter.end_date') }}</label>
                                <input type="text" name="to_date" id="filterEndDate" placeholder="dd-mm-yyyy"
                                    class="form-control datepicker" value="{{ request('to_date') }}" autocomplete="off">
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

            <!-- Report Data Table -->
            <div class="card card-report">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="salesTaxReportTable">
                            <thead>
                                <tr>
                                    <th>Sl</th>
                                    <th>Invoice Number</th>
                                    <th>Customer</th>
                                    <th>Invoice Date</th>
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
    <script src="{{ asset('assets/js/buttons.print.min.js') }}"></script>
    <!-- Bootstrap Datepicker -->
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            'use strict';

            // Initialize select2
            $('.select2').select2({
                width: '100%',
                placeholder: "{{ __('messages.sales_reports.select_option') }}"
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

            // DataTable initialization
            let salesTaxTable = $('#salesTaxReportTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('sales-tax-reports.index') }}",
                    data: function(d) {
                        // Get filter values
                        const customerValue = $('#filterCustomer').val();

                        // Handle "all" customer option
                        d.customer_name = customerValue === 'all' ? '' : customerValue;
                        d.from_date = $('#filterStartDate').val();
                        d.to_date = $('#filterEndDate').val();
                    }
                },
                columns: [{
                        data: null,
                        name: 'serial',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row, meta) {
                            return meta.row + meta.settings._iDisplayStart + 1;
                        }
                    },
                    {
                        data: 'invoice_number',
                        name: 'invoice_number'
                    },
                    {
                        data: 'customer_name',
                        name: 'customer.company_name',
                        orderable: false
                    },
                    {
                        data: 'invoice_date',
                        name: 'invoice_date'
                    },
                    {
                        data: 'taxable_amount',
                        name: 'taxable_amount',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'tax_amount',
                        name: 'tax_amount',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'total_amount',
                        name: 'total_amount'
                    }
                ],
                order: [
                    [3, 'desc']
                ],
                pageLength: 10,
                lengthMenu: [
                    [10, 25, 50, 100, -1],
                    [10, 25, 50, 100, "All"]
                ],
                dom: "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>" +
                    "<'row'<'col-sm-12'tr>>" +
                    "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
                footerCallback: function(row, data, start, end, display) {
                    var api = this.api();

                    // Remove any formatting to get integer data for summation
                    var intVal = function(i) {
                        return typeof i === 'string' ?
                            i.replace(/[^\d.-]/g, '') * 1 :
                            typeof i === 'number' ?
                            i : 0;
                    };

                    // Total over all pages - taxable amount (column 4)
                    var taxableTotal = api
                        .column(4)
                        .data()
                        .reduce(function(a, b) {
                            return intVal(a) + intVal(b);
                        }, 0);

                    // Total over all pages - tax amount (column 5)
                    var taxTotal = api
                        .column(5)
                        .data()
                        .reduce(function(a, b) {
                            return intVal(a) + intVal(b);
                        }, 0);

                    // Total over all pages - total amount (column 6)
                    var totalAmount = api
                        .column(6)
                        .data()
                        .reduce(function(a, b) {
                            return intVal(a) + intVal(b);
                        }, 0);

                    // Update footer
                    $(api.column(4).footer()).html('$' + taxableTotal.toFixed(2));
                    $(api.column(5).footer()).html('$' + taxTotal.toFixed(2));
                    $(api.column(6).footer()).html('$' + totalAmount.toFixed(2));
                }
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
                const newUrl = "{{ route('sales-tax-reports.index') }}" + (queryString ? '?' + queryString : '');

                // Update URL without page reload
                window.history.pushState({}, '', newUrl);
            }

            // Filter form submission - WITHOUT PAGE RELOAD
            $('#filterForm').on('submit', function(e) {
                e.preventDefault(); // Prevent default form submission
                updateURL(); // Update URL with current filters
                salesTaxTable.ajax.reload(); // Reload table data via AJAX
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
                window.history.pushState({}, '', "{{ route('sales-tax-reports.index') }}");

                // Reload table without filters
                salesTaxTable.ajax.reload();

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
                salesTaxTable.ajax.reload();
            });
        });

        // Export function
        function exportData(type) {
            const customer = $('#filterCustomer').val() === 'all' ? '' : $('#filterCustomer').val();
            const fromDate = $('#filterStartDate').val();
            const toDate = $('#filterEndDate').val();

            const url = "{{ route('sales-tax-reports.export') }}?" +
                "customer_name=" + encodeURIComponent(customer || '') +
                "&from_date=" + encodeURIComponent(fromDate || '') +
                "&to_date=" + encodeURIComponent(toDate || '') +
                "&type=" + encodeURIComponent(type);

            window.location.href = url;
        }
    </script>
@endsection
