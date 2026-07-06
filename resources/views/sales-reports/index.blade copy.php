@extends('layouts.app')

@section('title')
    {{ __('messages.sales_reports.name') }}
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
            color: #666!important;
            font-size: 14px;
            letter-spacing: .5px;
        }

        .dataTables_length select {
            width: 65px;
            display: inline-block;
            border: 1px solid #e4e6fc!important;
            border-radius: .25rem;
            padding: .25rem .5rem;
            font-size: .875rem;
            line-height: 1.5;
            background-color: #fff;
            height: 42px;
        }

        .dataTables_length select:focus {
            background-color: #fefeff!important;
            border-color: #95a0f4!important;
        }


        #voiceSearchBtn {
        margin-left: 8px;
        cursor: pointer;
        background: #f1f1f1;
        border: 1px solid #ccc;
        padding: 5px 10px;
        border-radius: 4px;
        }
        #voiceSearchBtn.listening {
            background: #d1e7dd; /* greenish when recording */
        }
    </style>
@endsection

@section('content')
    <section class="section">
        <div class="section-header d-flex justify-content-between align-items-center">
            <h1>{{ __('messages.sales_reports.name') }}</h1>

            <div class="dropdown">
                <button class="btn btn-info dropdown-toggle" type="button" id="exportDropdown"
                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="line-height:30px;">
                    Export
                </button>
                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="exportDropdown">
                    {{-- <a class="dropdown-item" href="#" onclick="exportData('csv')">CSV</a> --}}
                    <a class="dropdown-item" href="#" onclick="exportData('xls')">Excel</a>
                    <a class="dropdown-item" href="#" onclick="exportData('pdf')">PDF</a>
                    {{-- <a class="dropdown-item" href="#" onclick="exportData('print')">Print</a> --}}
                </div>
            </div>
        </div>
        <div class="section-body">
            <!-- Always Visible Filter Section -->
            <div class="filter-section">
                <form id="filterForm">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>{{ __('messages.sales_reports.filter.customer') }}</label>
                                <select name="customer_name" id="filterCustomer" class="form-control select2">
                                    <option value=""> </option>
                                    <option value="all" {{ request('customer_name') == 'all' ? 'selected' : '' }}>
                                        {{ __('messages.sales_reports.filter.all_customers') }}
                                    </option>
                                    @foreach ($customers as $name => $value)
                                        <option value="{{ $value }}"
                                            {{ request('customer_name') == $value ? 'selected' : '' }}>
                                            {{ $name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label>{{ __('messages.sales_reports.filter.user') }}</label>
                                <select name="sales_agent_id" id="filterSalesAgent" class="form-control select2">
                                    <option value="">{{ __('messages.sales_reports.filter.select_user') }}</option>
                                    <option value="all" {{ request('sales_agent_id') == 'all' ? 'selected' : '' }}>
                                        {{ __('messages.sales_reports.filter.all_users') }}
                                    </option>
                                    @foreach ($users as $id => $name)
                                        <option value="{{ $id }}"
                                            {{ request('sales_agent_id') == $id ? 'selected' : '' }}>
                                            {{ $name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-md-2">
                            <div class="form-group">
                                <label>{{ __('messages.sales_reports.filter.start_date') }}</label>
                                <input type="text" name="start_date" id="filterStartDate" placeholder="dd/mm/yyyy" class="form-control"
                                    value="{{ request('start_date') }}" autocomplete="off">
                            </div>
                        </div>

                        <div class="col-md-2">
                            <div class="form-group">
                                <label>{{ __('messages.sales_reports.filter.end_date') }}</label>
                                <input type="text" name="end_date" id="filterEndDate" placeholder="dd/mm/yyyy" class="form-control"
                                value="{{ request('end_date') }}" autocomplete="off">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>&nbsp;</label>
                                <div class="d-flex" style="gap:5px;">
                                    {{-- <button type="button" id="btnReset" class="btn btn-secondary" data-toggle="tooltip" title="{{ __('messages.sales_reports.reset') }}">
                                        <i class="fas fa-redo"></i>
                                    </button> --}}
                                    <button type="submit" class="btn btn-primary" data-toggle="tooltip" title="{{ __('messages.common.show') }}">
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
                    @include('sales-reports.table')
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
    <script src="{{ asset('assets/js/buttons.colVis.min.js') }}"></script>

@endsection

@section('scripts')
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        // Wait until DataTable renders the filter input
        $('#salesReportTable_filter').append('<button id="voiceSearchBtn" title="Voice Search"><i class="fas fa-microphone"></i></button>');
    });
    </script>
    <script>
        $(document).ready(function() {
            'use strict';

            // Initialize Select2
            $('.select2').select2({
                width: '100%',
                placeholder: "{{ __('messages.sales_reports.select_option') }}"
            });

            // Initialize DataTable
            let salesReportTable = $('#salesReportTable').DataTable({

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
                    url: route('sales-reports.index'),
                    data: function(d) {
                        d.customer_name = $('#filterCustomer').val() === 'all' ? '' : $(
                            '#filterCustomer').val();
                        d.sales_agent_id = $('#filterSalesAgent').val() === 'all' ? '' : $(
                            '#filterSalesAgent').val();
                        d.from_date = $('#filterStartDate').val();
                        d.to_date = $('#filterEndDate').val();
                    },
                    beforeSend: function() {
                        startLoader();
                    },
                    complete: function() {
                        stopLoader();
                    }
                },
                pageLength: 10,
                lengthMenu: [10, 20, 50, 100],
                lengthChange: true,
                dom: "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>" +
                    "<'row'<'col-sm-12'tr>>" +
                    "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
                // buttons: [{
                //         extend: 'excelHtml5',
                //         text: '<i class="fas fa-file-excel"></i> Excel',
                //         className: 'btn btn-light btn-sm border mr-2',
                //         title: 'Sales Report - {{ now()->format('Y-m-d') }}',
                //         exportOptions: {
                //             columns: ':visible',
                //             format: {
                //                 body: function(data, row, column, node) {
                //                     return $('<div>').html(data).text().trim();
                //                 }
                //             }
                //         }
                //     },
                //     {
                //         extend: 'pdfHtml5',
                //         text: '<i class="fas fa-file-pdf"></i> PDF',
                //         className: 'btn btn-light btn-sm border',
                //         title: 'Sales Report - {{ now()->format('Y-m-d') }}',
                //         orientation: 'landscape',
                //         pageSize: 'A4',
                //         exportOptions: {
                //             columns: ':visible',
                //             format: {
                //                 body: function(data, row, column, node) {
                //                     return $('<div>').html(data).text().trim();
                //                 }
                //             }
                //         },
                //         customize: function(doc) {
                //             doc.defaultStyle.fontSize = 8;
                //             doc.styles.tableHeader.fontSize = 9;
                //             doc.styles.title.fontSize = 12;
                //             doc.pageMargins = [20, 40, 20, 30];
                //             doc.content[0].margin = [0, 0, 0, 10];
                //         }
                //     }
                // ],
                columns: [{
                        data: null,
                        name: 'serial',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row, meta) {
                            return meta.row + meta.settings._iDisplayStart + 1;
                        },
                        width: '5%',
                        className: 'text-center'
                    },
                    {
                        data: 'po_number',
                        name: 'po_number',
                        width: '10%'
                    },
                    {
                        data: 'customer_id',
                        name: 'customer_id',
                        width: '10%'
                    },
                    {
                        data: 'customer_name',
                        name: 'customer_name',
                        width: '15%'
                    },
                    {
                        data: 'user',
                        name: 'user.first_name',
                        width: '10%',
                        render: function(data) {
                            return data ? data.first_name + ' ' + data.last_name : 'N/A';
                        }
                    },
                    {
                        data: 'sub_total',
                        name: 'sub_total',
                        width: '10%'
                    },
                    {
                        data: 'discount',
                        name: 'discount',
                        width: '10%'
                    },
                    {
                        data: 'taxable',
                        name: 'taxable',
                        width: '10%',
                        searchable: false
                    },
                    {
                        data: 'tax',
                        name: 'tax',
                        width: '10%',
                        searchable: false
                    },
                    {
                        data: 'total',
                        name: 'total',
                        width: '10%',
                        searchable: false
                    },
                    {
                        data: 'created_at',
                        name: 'sales_invoices.created_at',
                        width: '10%',
                        render: function(data) {
                            return data ? moment(data).format('DD MMM YYYY') : '';
                        }
                    }
                ],
                order: [
                    [10, 'desc']
                ],
                responsive: true,

                // Footer Sum Section
                footerCallback: function(row, data, start, end, display) {
                    let api = this.api();

                    // Helper to parse numbers
                    let intVal = function(i) {
                        return typeof i === 'string' ?
                            i.replace(/[\$,]/g, '') * 1 :
                            typeof i === 'number' ?
                            i :
                            0;
                    };

                    // Calculate totals
                    let subTotal = api.column(5, {
                        page: 'current'
                    }).data().reduce((a, b) => intVal(a) + intVal(b), 0);
                    let discount = api.column(6, {
                        page: 'current'
                    }).data().reduce((a, b) => intVal(a) + intVal(b), 0);
                    let taxable = api.column(7, {
                        page: 'current'
                    }).data().reduce((a, b) => intVal(a) + intVal(b), 0);
                    let tax = api.column(8, {
                        page: 'current'
                    }).data().reduce((a, b) => intVal(a) + intVal(b), 0);
                    let netTotal = api.column(9, {
                        page: 'current'
                    }).data().reduce((a, b) => intVal(a) + intVal(b), 0);

                    // Update footer
                    $(api.column(5).footer()).html(subTotal.toFixed(2));
                    $(api.column(6).footer()).html(discount.toFixed(2));
                    $(api.column(7).footer()).html(taxable.toFixed(2));
                    $(api.column(8).footer()).html(tax.toFixed(2));
                    $(api.column(9).footer()).html(netTotal.toFixed(2));
                }
            });
            $('#filterCustomer').select2({
                placeholder: "{{ __('messages.sales_reports.filter.select_customer') }}",
                allowClear: true
            });
            $('#filterSalesAgent').select2({
                placeholder: "{{ __('messages.sales_reports.filter.select_user') }}",
                allowClear: true
            });

            // Check browser support
        window.SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;

        if (window.SpeechRecognition) {
            const recognition = new SpeechRecognition();
            recognition.lang = 'en-US';          // or 'bn-BD' if you prefer Bangla
            recognition.interimResults = false;  // we only need the final result

            const voiceBtn = document.getElementById('voiceSearchBtn');

            voiceBtn.addEventListener('click', () => {
                recognition.start();
                voiceBtn.classList.add('listening');
            });

            recognition.addEventListener('result', (e) => {
                const transcript = e.results[0][0].transcript.trim();
                // Set DataTable global search value
                $('#salesReportTable_filter input[type="search"]').val(transcript);
                $('#salesReportTable').DataTable().search(transcript).draw();
            });

            recognition.addEventListener('end', () => {
                voiceBtn.classList.remove('listening');
            });
        } else {
            console.warn('SpeechRecognition not supported in this browser.');
        }



            flatpickr("#filterStartDate", {
                dateFormat: "d-m-Y",
                allowInput: true,
            });
            flatpickr("#filterEndDate", {
                dateFormat: "d-m-Y",
                allowInput: true,
            });
            // Filter form submission
            $('#filterForm').on('submit', function(e) {
                e.preventDefault();
                salesReportTable.ajax.reload();
            });

            // Reset filters
            $('#btnReset').click(function() {
                $('#filterForm')[0].reset();
                $('.select2').val(null).trigger('change');
                salesReportTable.ajax.reload();
            });

            // Date validation
            $('#filterEndDate').change(function() {
                let fromDate = $('#filterStartDate').val();
                let toDate = $(this).val();
                const messages = {
                    dateRangeValidation: "{{ __('messages.common.date_range_validation') }}"
                };
                if (fromDate && toDate && new Date(fromDate) > new Date(toDate)) {
                    displayErrorMessage(messages.dateRangeValidation);
                    $(this).val('');
                }
            });
             // Date validation
               // Date validation
                function parseDDMMYYYY(str) {
                    const [day, month, year] = str.split('-');
                    return new Date(year, month - 1, day); // month is 0-based
                }

                $('#filterStartDate, #filterEndDate').change(function () {
                    const fromDate = $('#filterStartDate').val();
                    const toDate   = $('#filterEndDate').val();

                    if (fromDate && toDate) {
                        const start = parseDDMMYYYY(fromDate);
                        const end   = parseDDMMYYYY(toDate);

                        // normalise to midnight to avoid time-zone quirks
                        start.setHours(0,0,0,0);
                        end.setHours(0,0,0,0);

                        if (start > end) {
                            displayErrorMessage("{{ __('messages.common.date_range_validation') }}");
                            $('#filterEndDate').val('');
                        }
                    }
                });
        });

        function exportData(type) {
            // Get filter values
            const customer = $('#filterCustomer').val() === 'all' ? '' : $('#filterCustomer').val();
            const salesAgent = $('#filterSalesAgent').val() === 'all' ? '' : $('#filterSalesAgent').val();
            const fromDate = $('#filterStartDate').val();
            const toDate = $('#filterEndDate').val();

            // Build URL with query parameters
            const url = "{{ route('sales-reports.export') }}?" +
                "customer_name=" + encodeURIComponent(customer) +
                "&sales_agent_id=" + encodeURIComponent(salesAgent) +
                "&from_date=" + encodeURIComponent(fromDate) +
                "&to_date=" + encodeURIComponent(toDate) +
                "&type=" + encodeURIComponent(type);

            window.location.href = url;
        }

    </script>
@endsection
