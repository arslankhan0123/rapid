@extends('layouts.app')
@section('title')
    {{ __('messages.trading_accounts.trading_account') }}
@endsection
@section('page_css')
    <link href="{{ asset('assets/css/jquery.dataTables.min.css') }}" rel="stylesheet" type="text/css" />
    <style>
        .trading-account-table {
            width: 100%;
        }

        .trading-account-table th {
            background-color: #f8f9fa;
            font-weight: bold;
        }

        .trading-account-table .total-row {
            font-weight: bold;
            background-color: #e9ecef;
            border-top: 2px solid #dee2e6;
        }

        .amount-column {
            text-align: right;
        }

        .trading-account-table tr.header-row {
            background-color: #d1ecf1;
            font-weight: bold;
        }

        /* Compact filter section */
        .filter-section {
            background: #f8f9fa;
            padding: 10px 15px;
            border-radius: 5px;
            margin-bottom: 15px;
            display: flex;
            align-items: flex-end;
            flex-wrap: wrap;
            gap: 10px;
        }

        .filter-group {
            margin-bottom: 0;
        }

        .filter-label {
            font-weight: 600;
            margin-bottom: 3px;
            color: #495057;
            font-size: 12px;
        }

        .date-input-group {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .date-separator {
            color: #6c757d;
            font-weight: bold;
        }

        .date-input {
            width: 120px !important;
            font-size: 13px;
            padding: 4px 8px;
        }

        /* Compact select2 inputs */
        .select2-container .select2-selection--single {
            height: 32px !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 30px !important;
            font-size: 13px;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 30px !important;
        }

        /* Minimal checkbox section */
        .checkbox-section {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 15px;
            padding: 0 5px;
        }

        .checkbox-container {
            display: flex;
            align-items: center;
            padding: 5px 0;
        }

        .form-check-input {
            width: 16px;
            height: 16px;
            margin-right: 6px;
            cursor: pointer;
        }

        .checkbox-label {
            font-weight: 500;
            color: #495057;
            margin-bottom: 0;
            font-size: 13px;
            cursor: pointer;
        }

        /* Compact form controls */
        .form-control {
            font-size: 13px;
            padding: 4px 8px;
            height: 32px;
        }

        /* Adjust section header spacing */
        .section-header {
            padding: 15px 0;
        }

        .section-header-breadcrumb {
            margin-top: 8px;
        }
    </style>
@endsection
@section('content')
    <section class="section">
        <div class="section-header">
            <h1>{{ __('messages.trading_accounts.trading_account') }}</h1>
            <div class="section-header-breadcrumb">
                <div class="filter-section">
                    <!-- Branch Filter -->
                    <div class="filter-group">
                        <div class="filter-label">Branches</div>
                        {{ Form::select('branches', $usersBranches ?? [], null, [
                            'id' => 'filterBranch',
                            'class' => 'form-control select2',
                            'style' => 'width: 160px;',
                            'placeholder' => __('messages.placeholder.branches'),
                        ]) }}
                    </div>

                    <!-- Date Filters -->
                    <div class="filter-group">
                        <div class="filter-label">Date Range</div>
                        <div class="date-input-group">
                            <input type="date" id="filterDateFrom" class="form-control date-input" placeholder="From">
                            <span class="date-separator">-</span>
                            <input type="date" id="filterDateTo" class="form-control date-input" placeholder="To">
                        </div>
                    </div>

                    <!-- Level Filter -->
                    <div class="filter-group">
                        <div class="filter-label">Account Level</div>
                        {{ Form::select(
                            'account_level',
                            [
                                '' => 'All Levels',
                                'level-1' => 'Level 1',
                                'level-2' => 'Level 2',
                                'level-3' => 'Level 3',
                                'level-4' => 'Level 4',
                            ],
                            null,
                            [
                                'id' => 'filterLevel',
                                'class' => 'form-control select2',
                                'style' => 'width: 130px;',
                            ],
                        ) }}
                    </div>
                </div>
            </div>
        </div>

        <div class="section-body">
            <div class="card">
                <div class="card-body">
                    <!-- Minimal Checkbox for Show 0 Amount Data -->
                    <div class="checkbox-section">
                        <div class="checkbox-container">
                            <input type="checkbox" id="showZeroAmount" class="form-check-input">
                            <label for="showZeroAmount" class="checkbox-label">
                                Show 0 Amount Data
                            </label>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped trading-account-table" id="tradingAccountTable">
                            <thead>
                                <tr>
                                    <th width="60%">{{ __('messages.trading_accounts.account_name') }}</th>
                                    <th width="20%" class="amount-column">{{ __('messages.trading_accounts.debit') }}
                                    </th>
                                    <th width="20%" class="amount-column">{{ __('messages.trading_accounts.credit') }}
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- Data will be loaded via DataTables --}}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('page_scripts')
    <script src="{{ asset('assets/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('assets/js/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('assets/js/jszip.min.js') }}"></script>
    <script src="{{ asset('assets/js/pdfmake.min.js') }}"></script>
    <script src="{{ asset('assets/js/vfs_fonts.js') }}"></script>
@endsection

@section('scripts')
    <script>
        'use strict';

        let tradingAccountTable = $('#tradingAccountTable').DataTable({
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
                url: "{{ route('trading-accounts.index') }}",
                beforeSend: function() {
                    startLoader();
                },
                complete: function() {
                    stopLoader();
                }
            },
            columns: [{
                    data: 'name',
                    name: 'name',
                    orderable: false,
                    searchable: true
                },
                {
                    data: 'debit_amount',
                    name: 'debit_amount',
                    orderable: false,
                    searchable: false,
                    className: 'amount-column'
                },
                {
                    data: 'credit_amount',
                    name: 'credit_amount',
                    orderable: false,
                    searchable: false,
                    className: 'amount-column'
                }
            ],
            ordering: false,
            searching: true,
            paging: true,
            info: true,
            lengthMenu: [
                [10, 25, 50, 100, -1],
                [10, 25, 50, 100, "All"]
            ],
            pageLength: 25,

            dom: '<"d-flex justify-content-between align-items-center mb-2"<"d-flex flex-column"<"d-flex mb-1"B><"d-flex"l>><"d-flex"f>>rtip',

            buttons: [{
                    extend: 'csvHtml5',
                    text: 'CSV',
                    className: 'btn btn-sm btn-outline-dark',
                    bom: true,
                    charset: 'utf-8'
                },
                {
                    extend: 'pdfHtml5',
                    text: 'PDF',
                    className: 'btn btn-sm btn-outline-dark',
                    orientation: 'landscape',
                    pageSize: 'A4',
                    customize: function(doc) {
                        doc.defaultStyle.fontSize = 10;
                    }
                }
            ],

            initComplete: function() {
                $('div.dataTables_filter input')
                    .attr('placeholder', 'Search...')
                    .addClass('form-control form-control-sm ml-2')
                    .css({
                        'width': '250px',
                        'display': 'inline-block'
                    });
            }
        });

        // Native date input validation
        $(document).ready(function() {
            // Set max date to today for both fields
            var today = new Date().toISOString().split('T')[0];
            $('#filterDateFrom').attr('max', today);
            $('#filterDateTo').attr('max', today);

            // Date From change event
            $('#filterDateFrom').on('change', function() {
                var fromDate = $(this).val();
                if (fromDate) {
                    $('#filterDateTo').attr('min', fromDate);

                    // If Date To is before Date From, clear it
                    var toDate = $('#filterDateTo').val();
                    if (toDate && toDate < fromDate) {
                        $('#filterDateTo').val('');
                    }
                } else {
                    $('#filterDateTo').removeAttr('min');
                }
            });

            // Auto-refresh table when filters change
            $('#filterBranch, #filterLevel, #filterDateFrom, #filterDateTo').on('change', function() {
                // Show loading message for filter changes
                var dateFrom = $('#filterDateFrom').val();
                var dateTo = $('#filterDateTo').val();

                var filterMessage = 'Filter applied';
                if (dateFrom && dateTo) {
                    filterMessage += ` (Date: ${dateFrom} to ${dateTo})`;
                }

                displayInfoMessage('Filter functionality will be implemented in the future. ' +
                    filterMessage);

                // Auto-refresh the table (optional - remove if you don't want auto-refresh)
                tradingAccountTable.ajax.reload();
            });

            // Handle Show Zero Amount checkbox change
            $('#showZeroAmount').on('change', function() {
                if ($(this).is(':checked')) {
                    displayInfoMessage('Showing data with 0 amounts');
                } else {
                    displayInfoMessage('Hiding data with 0 amounts');
                }
                // Add your logic to filter data based on checkbox state
                // tradingAccountTable.ajax.reload();
            });
        });

        // Initialize Select2 for filters
        $('#filterBranch, #filterLevel').select2({
            width: 'style',
            placeholder: 'Select an option',
            allowClear: true
        });
    </script>
@endsection
