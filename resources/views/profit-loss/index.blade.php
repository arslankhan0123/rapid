@extends('layouts.app')
@section('title')
    {{ __('messages.profit-loss.name') }}
@endsection
@section('page_css')
    <link href="{{ asset('assets/css/jquery.dataTables.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="{{ asset('assets/css/bs4-summernote/summernote-bs4.css') }}">

    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.3.6/css/buttons.dataTables.min.css">
    <!-- DataTables and Buttons Extension CSS -->
@endsection
@section('content')
    <section class="section">
        <div class="section-header item-align-right">
            <h1>{{ __('messages.profit-loss.name') }}</h1>
            <div class="section-header-breadcrumb float-right">
                <div class="card-header-action mr-3 select2-mobile-margin">


                </div>
            </div>
            <div class="float-right">
                <div class="row">
                    <div class="col d-flex align-items-center">
                        <span class="me-2 pr-2">Branch</span>
                        {{ Form::select('branches', $usersBranches ?? [], null, ['id' => 'filterBranch', 'class' => 'form-control select2', 'style' => 'width:220px;', 'placeholder' => __('messages.placeholder.branches')]) }}
                    </div>
                    <div class="col d-flex align-items-center">
                        <span class="me-2">Month</span>
                        <input type="month" id="from_date" name="from_date" class="form-control ml-2"
                            value="{{ \Carbon\Carbon::now()->format('Y-m') }}">

                    </div>
                    <div class="col">
                        @can('export_statement')
                            <div class="dropdown">
                                <button class="btn btn-primary dropdown-toggle" type="button" id="exportDropdown"
                                    style="line-height: 30px;" data-toggle="dropdown" aria-haspopup="true"
                                    aria-expanded="false">
                                    Export
                                </button>
                                <div class="dropdown-menu " aria-labelledby="exportDropdown" style="width: 50px;">
                                    <a class="dropdown-item" href="#" id="export-pdf">PDF</a>
                                    <a class="dropdown-item" href="#" id="export-csv">CSV</a>
                                </div>
                            </div>
                        @endcan
                    </div>
                </div>
            </div>
        </div>

        <div class="section-body">
            <div class="card">
                <div class="card-body">

                    @include('profit-loss.table')
                </div>
            </div>
        </div>
    </section>
    {{-- @include('salary_generates.templates.templates') --}}
@endsection
@section('page_scripts')
    <script src="{{ asset('assets/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ mix('assets/js/custom/custom-datatable.js') }}"></script>
    <script src="{{ mix('assets/js/bs4-summernote/summernote-bs4.js') }}"></script>
    <script src="{{ mix('assets/js/select2.min.js') }}"></script>

    <script src="https://cdn.datatables.net/buttons/2.3.6/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.print.min.js"></script>
    <!-- DataTables and Buttons Extension JS -->
    <script src="https://cdn.datatables.net/buttons/2.3.6/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.colVis.min.js"></script>

    <!-- pdfmake for PDF export -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.68/pdfmake.min.js"></script>
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
                url: route('profit-loss.index'),
                data: function(d) {
                    // Attach the selected month, customer, and project to the AJAX request
                    d.from_date = $('#from_date').val();
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
            columnDefs: [{
                targets: 0, // Adjust the index for the hidden 'created_at' column
                orderable: true,
                visible: false, // Hide the 'created_at' column
            }],
            columns: [{
                    data: 'order_key', // Ensure this matches the data key for created_at in your data source
                    name: 'order_key'
                }, {
                    data: function(row) {
                        // If the row has a category, return "Expense - <category name>"
                        if (row.category) {
                            return `Expense - ${row.category}`;
                        }
                        // Otherwise, return the type or an empty string
                        return row.type ?? '';
                    },
                    name: 'type',
                    className: 'text-left',
                },
                {
                    data: function(row) {
                        // Ensure null values return 0.00 and format to two decimal places
                        return row.debit?.toFixed(2);
                    },
                    name: 'debit',
                    className: 'text-right',
                }, {
                    data: function(row) {
                        // Ensure null values return 0.00 and format to two decimal places
                        return row.credit?.toFixed(2);
                    },
                    name: 'credit',
                    className: 'text-right',
                },


            ],
            footerCallback: function(row, data, start, end, display) {
                let totalDebit = 0;
                let totalCredit = 0;

                // Calculate totals
                data.forEach(function(row) {
                    totalDebit += parseFloat(row.debit || 0);
                    totalCredit += parseFloat(row.credit || 0);
                });

                const grossProfit = totalCredit - totalDebit;

                // Update footer rows
                const $footer = $(this.api().table().footer());
                const grossProfitClass = grossProfit >= 0 ? 'text-success' : 'text-danger';
                $footer.html(`
                    <tr>
                        <td  class="text-left p-0" style="padding-left:10px !important;"><strong>Gross Profit</strong></td>
                        <td class="text-right ${grossProfitClass} font-weight-bold p-0" style="padding-right:10px !important;">${grossProfit.toFixed(2)}</td>
                        <td class="text-right"></td>
                    </tr>
                    <tr>
                        <td class="text-right"><strong>Total</strong></td>
                        <td class="text-right font-weight-bold" style="padding-right:10px !important;">${totalDebit.toFixed(2)}</td>
                        <td class="text-right font-weight-bold" style="padding-right:10px !important;">${totalCredit.toFixed(2)}</td>
                    </tr>
                `);
            },
            responsive: true,



            // dom: "Bfrtip", // Ensure Buttons (B) is part of the DOM structure
            // buttons: [
            //     @can('export_statement_employee')
            //         {
            //             extend: 'csvHtml5',
            //             text: '<i class="fas fa-file-csv"></i> Export CSV',
            //             className: 'btn btn-sm',
            //             exportOptions: {
            //                 // Exclude the action column from the export
            //                 columns: function(idx, data, node) {
            //                     return idx !== 4; // Exclude the action column at index 4
            //                 }
            //             }
            //         }, {
            //             extend: 'excelHtml5',
            //             text: '<i class="fas fa-file-excel"></i> Export Excel',
            //             className: 'btn btn-sm',
            //             exportOptions: {
            //                 // Exclude the action column from the export
            //                 columns: function(idx, data, node) {
            //                     return idx !== 4; // Exclude the action column at index 4
            //                 }
            //             }
            //         }, {
            //             extend: 'pdfHtml5',
            //             text: '<i class="fas fa-file-pdf"></i> Export PDF',
            //             className: 'btn btn-sm', // Styled button
            //             orientation: 'portrait',
            //             pageSize: 'A4',
            //             exportOptions: {
            //                 // Exclude the action column from the export
            //                 columns: function(idx, data, node) {
            //                     return idx !== 4; // Exclude the action column at index 4
            //                 }
            //             },
            //             customize: function(doc) {
            //                 // Center the text in each cell of the table
            //                 doc.content[1].table.body.forEach(function(row, rowIndex) {
            //                     row.forEach(function(cell, cellIndex) {
            //                         cell.alignment =
            //                             'right'; // Set text alignment to center for each cell
            //                     });
            //                 });

            //                 // Optional: Set the column widths to auto-adjust
            //                 doc.content[1].table.widths = Array(doc.content[1].table.body[0].length + 1)
            //                     .join('*').split('');
            //             }
            //         }
            //     @endcan
            // ],


        });

        // Event handlers to trigger table reload when inputs change
        $('#from_date,#filterBranch').on('change', function() {
            tbl.ajax.reload(); // Reload the DataTable
        });
    </script>


    <script>
        $(document).ready(function() {

            $('#export-pdf').on('click', function(e) {
                e.preventDefault();
                var fromDate = $('#from_date').val();
                var filterBranch = $("#filterBranch").val();
                var exportUrl = "{{ route('profit-loss.export') }}" +
                    "?type=pdf" +
                    "&filterBranch=" + filterBranch +
                    "&from_date=" + encodeURIComponent(fromDate);
                window.location.href = exportUrl;
            });

            // Handle CSV export
            $('#export-csv').on('click', function(e) {
                e.preventDefault();
                var fromDate = $('#from_date').val();
                var filterBranch = $("#filterBranch").val();
                var exportUrl = "{{ route('profit-loss.export') }}" +
                    "?type=csv" +
                    "&filterBranch=" + filterBranch +
                    "&from_date=" + encodeURIComponent(fromDate);
                window.location.href = exportUrl;
            });


            $('#export-link').on('click', function(e) {
                e.preventDefault(); // Prevent default link behavior

                // Get the values from form inputs (adjust IDs to match your form)
                var fromDate = $('#from_date').val();
                var toDate = $('#to_date').val();
                var department_id = $('#department_select').val();
                var designation_id = $('#designation_select').val();
                var employee_id = $('#employee_select').val();


                // Construct the URL with query parameters
                var exportUrl = "{{ route('profit-loss.export') }}?from_date=" + fromDate +
                    "&to_date=" + toDate +
                    "&department_id=" + department_id +
                    "&designation_id=" + designation_id +
                    "&employee_id=" + employee_id;

                // Redirect to the constructed URL
                window.location.href = exportUrl;
            });
        });
    </script>
@endsection
