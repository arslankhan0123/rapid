@extends('layouts.app')
@section('title')
{{ __('messages.vat-reports.name') }}
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
        <h1>{{ __('messages.vat-reports.name') }}</h1>
        <div class="section-header-breadcrumb float-right">
            <div class="card-header-action mr-3 select2-mobile-margin">
            </div>
        </div>
        <div class=" col-md-2">
            {{ Form::select('branches', $usersBranches ?? [], null, ['id' => 'filterBranch', 'class' => 'form-control select2', 'placeholder' => 'Branches']) }}
        </div>

        <div class="float-right">

            <select id="year-select" class="form-control select2" style="width: 150px;">
                <!-- Options will be added dynamically using JavaScript -->
            </select>

        </div>

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
                @include('vat-reports.table_unit')
            </div>
        </div>
    </div>
</section>
@include('vat-reports.pay_modal')
@endsection
@section('page_scripts')
<script src="{{ asset('assets/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ mix('assets/js/custom/custom-datatable.js') }}"></script>
<script src="{{ mix('assets/js/bs4-summernote/summernote-bs4.js') }}"></script>
<script src="{{ mix('assets/js/select2.min.js') }}"></script>

{{-- <script src="https://cdn.datatables.net/buttons/2.3.6/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.print.min.js"></script>

    <script src="https://cdn.datatables.net/buttons/2.3.6/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.colVis.min.js"></script> --}}

<!-- pdfmake for PDF export -->
{{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.68/pdfmake.min.js"></script> --}}
<script src="{{ asset('assets/js/jszip.min.js') }}"></script>
<script src="{{ asset('assets/js/pdfmake.min.js') }}"></script>
{{-- <script src="{{ asset('assets/js/vfs_fonts.js"') }}"></script> --}}
{{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.68/vfs_fonts.js"></script> --}}



<script src="{{ asset('assets/js/dataTables.buttons.min.js') }}"></script>
<script src="{{ asset('assets/js/jszip.min.js') }}"></script>
<script src="{{ asset('assets/js/buttons.html5.min.js') }}"></script>
<script src="{{ asset('assets/js/buttons.print.min.js') }}"></script>
<script src="{{ asset('assets/js/buttons.colVis.min.js') }}"></script>
@endsection
@section('scripts')
<script src="{{ mix('assets/js/custom/input-price-format.js') }}"></script>
<script>
    'use strict';

    let categoryCreateUrl = route('vat-reports.store');
    let categoryUrl = route('vat-reports.index') + '/';

    let tbl = $('#assetCategoryTable').DataTable({
        oLanguage: {
            'sEmptyTable': Lang.get('messages.common.no_data_available_in_table'),
            'sInfo': Lang.get('messages.common.data_base_entries'),
            sLengthMenu: Lang.get('messages.common.menu_entry'),
            sInfoEmpty: Lang.get('messages.common.no_entry'),
            sInfoFiltered: Lang.get('messages.common.filter_by'),
            sZeroRecords: Lang.get('messages.common.no_matching'),
        },
        pageLength: 15, // Sets the number of records per page to 15

        processing: true,
        serverSide: true,
        ajax: {
            url: route('vat-reports.index'),
            data: function(d) {
                // Add the selected year to the DataTable's ajax request
                d.year = $('#year-select').val(); // Pass the selected year to the server
                d.filterBranch = $("#filterBranch").val();
            },
            beforeSend: function() {
                startLoader()
            },
            complete: function() {
                stopLoader()
            },
        },
        columns: [{
                data: function(row) {
                    return row.branch?.name ?? "";
                },
                name: 'period',
                width: '10%',
                className: 'text-center'
            },
            {
                data: function(row) {

                    let element = document.createElement('textarea');
                    element.innerHTML = row.period_description.toUpperCase();
                    element.style.textAlign = 'center';
                    element.style.width = '100%';
                    return element.value;
                },
                name: 'period',
                width: '20%',
                className: 'text-center'
            }, {
                data: function(row) {
                    let element = document.createElement('textarea');
                    element.innerHTML = formatAmountToLocaleString(row.input ?? 0);
                    element.style.textAlign = 'center';
                    element.style.width = '100%';
                    return element.value;
                },
                name: 'input',
                width: '12%',
                className: 'text-center'
            },
            {
                data: function(row) {
                    let element = document.createElement('textarea');
                    element.innerHTML = formatAmountToLocaleString(row.output ?? 0);
                    element.style.textAlign = 'center';
                    element.style.width = '100%';
                    return element.value;
                },
                name: 'output',
                width: '12%',
                className: 'text-center'
            },
            {
                data: function(row) {
                    let element = document.createElement('textarea');
                    element.innerHTML = formatAmountToLocaleString(row.net ?? 0);
                    element.style.textAlign = 'center';
                    element.style.width = '100%';
                    return element.value;
                },
                name: 'net',
                width: '10%',
                className: 'text-center',


            },
            {
                data: function(row) {
                    let element = document.createElement('textarea');
                    element.innerHTML = formatAmountToLocaleString(row.paid ?? 0);
                    element.style.textAlign = 'right';
                    element.style.width = '100%'; // Ensure full width for proper alignment
                    return element.value;
                },
                name: 'paid',
                width: '15%',
                className: 'text-right',
            },
            {
                data: function(row) {
                    let element = document.createElement('textarea');
                    var totalUnpaid = row.input - row.output - row.paid;
                    element.innerHTML = totalUnpaid;
                    element.style.textAlign = 'right';
                    element.style.width = '100%'; // Ensure full width for proper alignment
                    return element.value;
                },
                className: 'text-right',
                name: 'unpaid',
                width: '15%'


            }, {
                data: function(row) {

                    return renderActionButtons(row.id, row.unpaid ?? 0);
                },
                name: 'id',
                width: '200px'
            }
        ],

        responsive: true,
        dom: "Bfrtip", // Ensure Buttons (B) is part of the DOM structure
        buttons: [{
            extend: 'csvHtml5',
            text: '<i class=" fas fa-file-csv"></i> Export CSV',
            className: 'btn btn-sm',
            exportOptions: {
                // Exclude the action column from the export (assuming it is the last column, i.e. index 6)
                columns: function(idx, data, node) {
                    return idx !== 7; // Exclude the action column at index 6 (last column)
                }
            },
            filename: function() {
                // Customize the filename here
                var year = new Date()
                    .getFullYear(); // Example: include the current year in the file name
                return 'VAT_Report_' +
                    year; // The downloaded file will be named "Vat_Report_2024.csv"
            },
            bom: true // <<== THIS IS THE KEY TO FIX YOUR ISSUE

        }],


        footerCallback: function(row, data, start, end, display) {
            // Calculate the totals for each column except period and action
            let api = this.api();

            // Helper function to parse the column values and calculate the total
            let intVal = function(i) {
                return typeof i === 'string' ? i.replace(/[\$,]/g, '') * 1 : typeof i === 'number' ? i :
                    0;
            };

            // Total for Input
            let totalInput = api
                .column(2, {
                    page: 'current'
                })
                .data()
                .reduce((a, b) => intVal(a) + intVal(b), 0);


            let totalOutput = api
                .column(3, {
                    page: 'current'
                })
                .data()
                .reduce((a, b) => intVal(a) + intVal(b), 0);


            let totalNet = api
                .column(4, {
                    page: 'current'
                })
                .data()
                .reduce((a, b) => intVal(a) + intVal(b), 0);


            let totalPaid = api
                .column(5, {
                    page: 'current'
                })
                .data()
                .reduce((a, b) => intVal(a) + intVal(b), 0);


            let totalUnpaid = api
                .column(6, {
                    page: 'current'
                })
                .data()
                .reduce((a, b) => intVal(a) + intVal(b), 0);


            $(api.column(2).footer()).html(formatAmountToLocaleString(totalInput));
            $(api.column(3).footer()).html(formatAmountToLocaleString(totalOutput));
            $(api.column(4).footer()).html(formatAmountToLocaleString(totalNet));
            $(api.column(5).footer()).html(formatAmountToLocaleString(totalPaid));
            $(api.column(6).footer()).html(formatAmountToLocaleString(totalUnpaid));
        }


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
        updateItem: "{{ auth()->user()->can('export_vat_reports') ? 'true' : 'false' }}",
        payItem: "{{ auth()->user()->can('pay_vat_reports') ? 'true' : 'false' }}",
        viewItem: "{{ auth()->user()->can('view_vat_reports') ? 'true' : 'false' }}"
    };
    // Function to render action buttons based on permissions
    function renderActionButtons(id, unpaid) {
        let buttons = '';


        if (permissions.viewItem === 'true') {
            let viewUrl = `{{ route('vat-reports.view', ':id') }}`;
            viewUrl = viewUrl.replace(':id', id);
            buttons += `
                <a title="${messages.view}" href="${viewUrl}" class="btn btn-info action-btn has-icon view-btn" style="float:right;margin:2px;">
                    <i class="fa fa-eye"></i>
                </a>
            `;
        }
        if (permissions.updateItem === 'true') {
            let editUrl = `{{ route('vat-reports.vat-history.download', ':id') }}`;
            editUrl = editUrl.replace(':id', id);
            buttons += `
                <a title="${messages.edit}" href="${editUrl}" class="btn btn-warning action-btn has-icon edit-btn" style="float:right;margin:2px;">
                    <i class="fa fa-download"></i>
                </a>
            `;
        }
        if (permissions.payItem === 'true') {
            if (unpaid > 0) {
                buttons += `
                <a title="${messages.pay}" href="#" class="btn btn-success pay-btn has-icon "  data-id="${id}" style="float:right;margin:2px;width:50px;padding:0px;" onclick="showPaymentModal(${id})">
                    Pay
                </a>
                     `;
            } else {
                buttons += `
                        <a href="#" class="btn btn-secondary pay-btn has-icon " style="float:right;margin:2px;width:50px;padding:0px;" >
                            Pay
                        </a>
                     `;
            }

        }

        return buttons;
    }
</script>



<script>
    function formatAmountToLocaleString(amount) {
        // Ensure amount is a number and format it to 2 decimal places with locale string
        return Number(amount).toLocaleString(undefined, {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    }
    // JavaScript function to show the payment modal
    function showPaymentModal(id) {
        // Prevent default action (e.g., if the anchor tag has a href)
        event.preventDefault();
        startLoader();
        // Make AJAX call to fetch the modal content based on the ID
        $.ajax({
            url: '{{ route('
            vat - reports.modal ', ['
            report ' => '
            __reportId__ ']) }}'.replace('__reportId__',
                id), // Replace with dynamic report ID
            method: 'GET',
            success: function(response) {

                if (response.success) {
                    $("#period").val(response.data.period.toUpperCase() ?? '');
                    $("#vat_report_id").val(response.data.id);
                    $("#bank_name").val(response.data.bank_name ?? '');
                    $("#account_number").val(response.data.account_number ?? '');
                    $("#branch").val(response.data?.branch?.name ?? '');
                    $("#paid").val(response.data.unpaid);
                }
                // Show the modal
                $('#payModal').modal('show');
                stopLoader();
            },
            error: function(xhr, status, error) {
                stopLoader();
            }
        });
    }


    $(".btn-close").click(function() {
        $('#payModal').modal('hide'); // Open the modal
    });



    $(document).on('click', '#submit-report', function(e) {
        // Prevent the default form submission
        e.preventDefault();
        // Get the value of the 'paid' field
        var paidValue = $('#paid').val();
        // Check if the 'paid' field is empty
        if (!paidValue || paidValue <= 0) {
            // Display an alert if the 'paid' field is empty or invalid
            alert('Please enter a valid amount for "Paid"');
            return; // Stop the form submission if the field is invalid
        }
        var formData = $('#report-form').serialize(); // Serialize the form data


        processingBtn('#report-form', '#submit-report');
        stopLoader();

        $.ajax({
            url: route('vat-reports.pay'), // Use the form's action URL
            method: 'POST', // Send a POST request
            data: formData, // Send the form data
            success: function(response) {
                // Handle the success response here
                if (response.success) {
                    displaySuccessMessage(response.message);
                    tbl.ajax.reload(); // Reload the DataTable
                    $('#payModal').modal('hide'); // Hide the modal
                    stopLoader(); // Stop the loader
                    $('#report-form')[0].reset(); // Reset the form fields
                }
            },
            error: function(xhr, status, error) {
                stopLoader(); // Stop the loader in case of error
            }
        });
    });
</script>

<script>
    $(document).ready(function() {
        // Get current year
        var currentYear = new Date().getFullYear();

        // Populate the year dropdown with a range of years
        var startYear = 1900; // Starting year
        var endYear = currentYear; // Current year
        // Loop through years and append options to the year dropdown
        for (var year = endYear; year >= startYear; year--) {
            $('#year-select').append(new Option(year, year));
        }
        // Set default value to current year
        $('#year-select').val(currentYear);

        // Initialize DataTable with server-side processing


        // On year selection change, reload the DataTable with the new year
        $('#year-select,#filterBranch').on('change', function() {
            // Reload the DataTable with the new year
            tbl.ajax.reload();
        });

        // Optionally, reload the DataTable on page load with the current year
        tbl.ajax.reload();
    });
</script>
@endsection



{{-- drawCallback: function(settings) {
                var api = this.api();
                var firstRowData = api.row(0).data();
                var updateStatus = false;
                if (firstRowData) {
                    var updatedAt = new Date(firstRowData.updated_at); // Convert updated_at to a Date object
                    var twoDaysAgo = new Date();
                    twoDaysAgo.setDate(twoDaysAgo.getDate() - 2); // Calculate the date 2 days ago

                    if (updatedAt < twoDaysAgo) {
                        updateStatus = true;
                    }
                }



                var rowCount = api.rows({
                    page: 'current'
                }).count(); // Get the current page row count
                if (rowCount === 0) {
                    startLoader();
                    $.ajax({
                        url: route('vat-reports.make', {
                            year: $('#year-select').val()
                        }), // Pass the selected year to the route
                        method: 'GET',
                        success: function(response) {
                            if (response.data.length > 0) {
                                // displaySuccessMessage(response.message);
                                tbl.ajax.reload(); // Reload the DataTable
                            }

                        },
                        error: function(xhr, status, error) {
                            console.error('Error fetching additional data:', error);
                        }
                    });
                }
            }, --}}