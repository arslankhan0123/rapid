@extends('layouts.app')
@section('title')
    {{ __('messages.balance_sheets.balance_sheet') }}
@endsection
@section('page_css')
    <link href="{{ asset('assets/css/jquery.dataTables.min.css') }}" rel="stylesheet" type="text/css" />
    <style>
        .balance-sheet-table {
            width: 100%;
        }

        .balance-sheet-table th {
            background-color: #f8f9fa;
            font-weight: bold;
        }

        .balance-sheet-table .total-row {
            font-weight: bold;
            background-color: #e9ecef;
        }

        .balance-sheet-table .account-type-header {
            background-color: #d1ecf1;
            font-weight: bold;
        }

        .amount-column {
            text-align: right;
        }

        .balance-sheet-table tr.header-row {
            background-color: #d1ecf1;
            font-weight: bold;
        }

        .balance-sheet-table tr.subtotal-row {
            background-color: #e9ecef;
            font-weight: bold;
            border-top: 2px solid #dee2e6;
        }

        .filter-section {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }

        .filter-group {
            margin-right: 15px;
            margin-bottom: 10px;
        }

        .filter-label {
            font-weight: 600;
            margin-bottom: 5px;
            color: #495057;
            font-size: 14px;
        }

        .date-input {
            width: 150px !important;
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
    </style>
@endsection
@section('content')
    <section class="section">
        <div class="section-header">
            <h1>{{ __('messages.balance_sheets.balance_sheet') }}</h1>
            <div class="section-header-breadcrumb d-flex align-items-center flex-wrap">
                <div class="filter-section d-flex align-items-center flex-wrap">
                    <!-- Branch Filter -->
                    <div class="filter-group">
                        <div class="filter-label">Branches</div>
                        {{ Form::select('branches', $usersBranches ?? [], null, [
                            'id' => 'filterBranch',
                            'class' => 'form-control select2',
                            'style' => 'width: 200px;',
                            'placeholder' => __('messages.placeholder.branches'),
                        ]) }}
                    </div>

                    <!-- Single Date Filter (As on Date) -->
                    <div class="filter-group">
                        <div class="filter-label">As on Date</div>
                        <input type="date" id="filterAsOnDate" class="form-control date-input" placeholder="As on Date">
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
                                'style' => 'width: 150px;',
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
                        <table class="table table-bordered table-striped balance-sheet-table" id="balanceSheetTable">
                            <thead>
                                <tr>
                                    <th width="60%">{{ __('messages.balance_sheets.name_type') }}</th>
                                    <th width="20%" class="amount-column">{{ __('messages.balance_sheets.assets') }}</th>
                                    <th width="20%" class="amount-column">
                                        {{ __('messages.balance_sheets.liabilities_equity') }}
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- Data will be loaded via DataTables --}}
                            </tbody>
                            <tfoot>
                                <tr class="total-row">
                                    <td><strong>{{ __('messages.balance_sheets.total') }}</strong></td>
                                    <td class="amount-column"><strong id="total-assets">0.00</strong></td>
                                    <td class="amount-column"><strong id="total-liabilities">0.00</strong></td>
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
    <script src="{{ asset('assets/js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('assets/js/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('assets/js/jszip.min.js') }}"></script>
    <script src="{{ asset('assets/js/pdfmake.min.js') }}"></script>
    <script src="{{ asset('assets/js/vfs_fonts.js') }}"></script>
@endsection

@section('scripts')
    <script>
        'use strict';

        let balanceSheetTable = $('#balanceSheetTable').DataTable({
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
                url: "{{ route('balance-sheets.index') }}",
                beforeSend: function() {
                    startLoader();
                },
                complete: function() {
                    stopLoader();
                }
            },
            columns: [{
                    data: 'name_type',
                    name: 'name_type',
                    orderable: false,
                    searchable: true
                },
                {
                    data: 'assets_amount',
                    name: 'assets_amount',
                    orderable: false,
                    searchable: false,
                    className: 'amount-column'
                },
                {
                    data: 'liabilities_amount',
                    name: 'liabilities_amount',
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

            drawCallback: function(settings) {
                let assetsTotal = 0;
                let liabilitiesTotal = 0;

                this.api().rows().every(function() {
                    const assetsAmount = parseFloat(this.data().assets_amount.replace(/,/g, '')) || 0;
                    const liabilitiesAmount = parseFloat(this.data().liabilities_amount.replace(/,/g,
                        '')) || 0;
                    assetsTotal += assetsAmount;
                    liabilitiesTotal += liabilitiesAmount;
                });

                $('#total-assets').text(assetsTotal.toFixed(2));
                $('#total-liabilities').text(liabilitiesTotal.toFixed(2));
            },

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

        // Initialize filters
        $(document).ready(function() {
            // Set max date to today for as on date
            var today = new Date().toISOString().split('T')[0];
            $('#filterAsOnDate').attr('max', today);

            // Initialize Select2 for filters
            $('#filterBranch, #filterLevel').select2({
                width: 'style',
                placeholder: 'Select an option',
                allowClear: true
            });

            // Handle Show Zero Amount checkbox change
            $('#showZeroAmount').on('change', function() {
                if ($(this).is(':checked')) {
                    displayInfoMessage('Showing data with 0 amounts');
                } else {
                    displayInfoMessage('Hiding data with 0 amounts');
                }
                // Add your logic to filter data based on checkbox state
                // balanceSheetTable.ajax.reload();
            });

            // Auto-refresh table when filters change
            $('#filterBranch, #filterLevel, #filterAsOnDate').on('change', function() {
                // Show filter message
                var asOnDate = $('#filterAsOnDate').val();
                var filterMessage = 'Filter applied';

                if (asOnDate) {
                    filterMessage += ` (As on: ${asOnDate})`;
                }

                displayInfoMessage('Filter functionality will be implemented in the future. ' +
                    filterMessage);

                // Auto-refresh the table
                balanceSheetTable.ajax.reload();
            });
        });

        // Manual refresh functionality (if needed)
        $(document).on('click', '#refreshBalanceSheet', function() {
            balanceSheetTable.ajax.reload();
        });
    </script>
@endsection
