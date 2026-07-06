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

            {{-- REMOVE BRANCH FILTER COMPLETELY --}}

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
        let tbl;

        // Define format function first
        function formatAmountToLocaleString(amount) {
            return Number(amount).toLocaleString(undefined, {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        }

        function renderActionButtons(row) {
            let buttons = '';

            // Calculate unpaid amount
            let unpaid = (row.input || 0) - (row.output || 0) - (row.paid || 0);

            // Get the year and period from the row data
            let year = row.year;
            let period = row.period;

            // For aggregated view, we have report ID now
            let viewUrl = `{{ route('vat-reports.view.aggregated') }}?year=${year}&period=${period}`;

            const messages = {
                view: "{{ __('messages.common.view') }}",
                pay: "Pay"
            };

            const permissions = {
                payItem: "{{ auth()->user()->can('pay_vat_reports') ? 'true' : 'false' }}",
                viewItem: "{{ auth()->user()->can('view_vat_reports') ? 'true' : 'false' }}"
            };

            if (permissions.viewItem === 'true') {
                buttons += `
        <a title="${messages.view}" href="${viewUrl}" class="btn btn-info action-btn has-icon view-btn" style="float:right;margin:2px;">
            <i class="fa fa-eye"></i>
        </a>`;
            }

            // DOWNLOAD BUTTON REMOVED AS REQUESTED

            if (permissions.payItem === 'true') {
                if (unpaid > 0) {
                    buttons += `
            <a title="${messages.pay}" href="#" class="btn btn-success pay-btn has-icon"
               data-id="${row.id}" style="float:right;margin:2px;width:50px;padding:0px;"
               onclick="showPaymentModal(${row.id}); return false;">
                Pay
            </a>`;
                } else {
                    buttons += `
            <a href="#" class="btn btn-secondary pay-btn has-icon"
               style="float:right;margin:2px;width:50px;padding:0px;">
                Pay
            </a>`;
                }
            }

            return buttons;
        }
        // Define renderActionButtons function
        // function renderActionButtons(row) {
        //     let buttons = '';

        //     // Calculate unpaid amount
        //     let unpaid = (row.input || 0) - (row.output || 0) - (row.paid || 0);

        //     // Get the year and period from the row data
        //     let year = row.year;
        //     let period = row.period;

        //     // For aggregated view, we have report ID now
        //     let viewUrl = `{{ route('vat-reports.view.aggregated') }}?year=${year}&period=${period}`;

        //     const messages = {
        //         delete: "{{ __('messages.common.delete') }}",
        //         edit: "{{ __('messages.common.edit') }}",
        //         view: "{{ __('messages.common.view') }}",
        //         pay: "Pay"
        //     };

        //     const permissions = {
        //         updateItem: "{{ auth()->user()->can('export_vat_reports') ? 'true' : 'false' }}",
        //         payItem: "{{ auth()->user()->can('pay_vat_reports') ? 'true' : 'false' }}",
        //         viewItem: "{{ auth()->user()->can('view_vat_reports') ? 'true' : 'false' }}"
        //     };

        //     if (permissions.viewItem === 'true') {
        //         buttons += `
    //     <a title="${messages.view}" href="${viewUrl}" class="btn btn-info action-btn has-icon view-btn" style="float:right;margin:2px;">
    //         <i class="fa fa-eye"></i>
    //     </a>`;
        //     }

        //     if (permissions.updateItem === 'true') {
        //         let editUrl = `{{ route('vat-reports.vat-history.download', ':id') }}`;
        //         editUrl = editUrl.replace(':id', row.id);
        //         buttons += `
    //     <a title="${messages.edit}" href="${editUrl}" class="btn btn-warning action-btn has-icon edit-btn" style="float:right;margin:2px;">
    //         <i class="fa fa-download"></i>
    //     </a>`;
        //     }

        //     if (permissions.payItem === 'true') {
        //         if (unpaid > 0) {
        //             buttons += `
    //         <a title="${messages.pay}" href="#" class="btn btn-success pay-btn has-icon"
    //            data-id="${row.id}" style="float:right;margin:2px;width:50px;padding:0px;"
    //            onclick="showPaymentModal(${row.id}); return false;">
    //             Pay
    //         </a>`;
        //         } else {
        //             buttons += `
    //         <a href="#" class="btn btn-secondary pay-btn has-icon"
    //            style="float:right;margin:2px;width:50px;padding:0px;">
    //             Pay
    //         </a>`;
        //         }
        //     }

        //     return buttons;
        // }
        // Function to show payment modal (make it global)
        // Function to show payment modal (make it global)
        window.showPaymentModal = function(id) {
            event.preventDefault();
            startLoader();

            $.ajax({
                url: '{{ route('vat-reports.modal', ['report' => '__reportId__']) }}'.replace('__reportId__',
                    id),
                method: 'GET',
                success: function(response) {
                    if (response.success) {
                        // Set period and year
                        let period = response.data.period?.toUpperCase() || '';
                        let year = response.data.year || '';
                        $("#period").val(period);
                        $("#year").val(year);

                        // Set report ID
                        $("#vat_report_id").val(response.data.id);

                        // Set bank details
                        $("#bank_name").val(response.data.bank_name || '');
                        $("#account_number").val(response.data.account_number || '');

                        // Set branch information
                        if (response.data.branch) {
                            $("#branch").val(response.data.branch.name || 'Branch');
                        } else if (response.data.branch_id) {
                            $("#branch").val('Branch #' + response.data.branch_id);
                        } else {
                            $("#branch").val("All Branches (Aggregated)");
                        }

                        // Calculate and display unpaid amount
                        let input = parseFloat(response.data.input) || 0;
                        let output = parseFloat(response.data.output) || 0;
                        let paid = parseFloat(response.data.paid) || 0;
                        let unpaid = input - output - paid;

                        $("#paid").val('');
                        $("#remaining-unpaid").text(formatAmountToLocaleString(unpaid));

                        // Set validation attributes
                        $("#paid").attr({
                            'max': unpaid,
                            'min': 0,
                            'step': '0.01'
                        }).attr('placeholder', 'Max: ' + formatAmountToLocaleString(unpaid));

                        // Update modal title with period info
                        $('#payModalLabel').text('VAT Payment - ' + period + ' ' + year);

                    } else {
                        displayErrorMessage(response.message || 'Failed to load payment data');
                    }
                    $('#payModal').modal('show');
                    stopLoader();
                },
                error: function(xhr, status, error) {
                    stopLoader();
                    let errorMessage = 'Error loading payment information';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    displayErrorMessage(errorMessage);
                }
            });
        };
        // Function to show payment modal (make it global)
        // window.showPaymentModal = function(id) {
        //     event.preventDefault();
        //     startLoader();

        //     $.ajax({
        //         url: '{{ route('vat-reports.modal', ['report' => '__reportId__']) }}'.replace('__reportId__',
        //             id),
        //         method: 'GET',
        //         success: function(response) {
        //             if (response.success) {
        //                 $("#period").val(response.data.period?.toUpperCase() ?? '');
        //                 $("#year").val(response.data.year ?? '');
        //                 $("#vat_report_id").val(response.data.id);
        //                 $("#bank_name").val(response.data.bank_name ?? '');
        //                 $("#account_number").val(response.data.account_number ?? '');

        //                 // Always show "All Branches (Aggregated)" for aggregated records
        //                 $("#branch").val("All Branches (Aggregated)");

        //                 // Calculate and display unpaid amount
        //                 let unpaid = (response.data.input || 0) - (response.data.output || 0) - (response
        //                     .data.paid || 0);
        //                 $("#paid").val('');
        //                 $("#remaining-unpaid").text(formatAmountToLocaleString(unpaid));

        //                 // Set max amount for validation
        //                 $("#paid").attr('max', unpaid);
        //             }
        //             $('#payModal').modal('show');
        //             stopLoader();
        //         },
        //         error: function(xhr, status, error) {
        //             stopLoader();
        //             alert('Error loading payment information: ' + error);
        //         }
        //     });
        // };

        // Initialize everything when document is ready
        $(document).ready(function() {
            // Populate year dropdown
            var currentYear = new Date().getFullYear();
            var startYear = 1900;
            var endYear = currentYear;

            for (var year = endYear; year >= startYear; year--) {
                $('#year-select').append(new Option(year, year));
            }
            $('#year-select').val(currentYear);

            // Initialize DataTable
            tbl = $('#assetCategoryTable').DataTable({
                oLanguage: {
                    'sEmptyTable': Lang.get('messages.common.no_data_available_in_table'),
                    'sInfo': Lang.get('messages.common.data_base_entries'),
                    sLengthMenu: Lang.get('messages.common.menu_entry'),
                    sInfoEmpty: Lang.get('messages.common.no_entry'),
                    sInfoFiltered: Lang.get('messages.common.filter_by'),
                    sZeroRecords: Lang.get('messages.common.no_matching'),
                },
                pageLength: 15,
                processing: true,
                serverSide: true,
                ajax: {
                    url: route('vat-reports.index'),
                    data: function(d) {
                        d.year = $('#year-select').val();
                    },
                    beforeSend: function() {
                        startLoader();
                    },
                    complete: function() {
                        stopLoader();
                    },
                },
                columns: [{
                        data: function(row) {
                            let element = document.createElement('textarea');
                            element.innerHTML = row.period?.toUpperCase() || '';
                            element.style.textAlign = 'center';
                            element.style.width = '100%';
                            return element.value;
                        },
                        name: 'period',
                        width: '25%',
                        className: 'text-center'
                    },
                    {
                        data: function(row) {
                            let element = document.createElement('textarea');
                            element.innerHTML = formatAmountToLocaleString(row.input ?? 0);
                            element.style.textAlign = 'center';
                            element.style.width = '100%';
                            return element.value;
                        },
                        name: 'input',
                        width: '15%',
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
                        width: '15%',
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
                            element.style.width = '100%';
                            return element.value;
                        },
                        name: 'paid',
                        width: '15%',
                        className: 'text-right',
                    },
                    {
                        data: function(row) {
                            let element = document.createElement('textarea');
                            var totalUnpaid = (row.input || 0) - (row.output || 0) - (row.paid ||
                                0);
                            element.innerHTML = formatAmountToLocaleString(totalUnpaid);
                            element.style.textAlign = 'right';
                            element.style.width = '100%';
                            return element.value;
                        },
                        className: 'text-right',
                        name: 'unpaid',
                        width: '15%'
                    },
                    {
                        data: function(row) {
                            return renderActionButtons(row);
                        },
                        name: 'id',
                        width: '200px',
                        orderable: false,
                        searchable: false
                    }
                ],
                responsive: true,
                dom: "Bfrtip",
                buttons: [{
                    extend: 'csvHtml5',
                    text: '<i class="fas fa-file-csv"></i> Export CSV',
                    className: 'btn btn-sm',
                    exportOptions: {
                        columns: function(idx, data, node) {
                            return idx !== 6;
                        }
                    },
                    filename: function() {
                        var year = $('#year-select').val() || new Date().getFullYear();
                        return 'VAT_Report_' + year;
                    },
                    bom: true
                }],
                footerCallback: function(row, data, start, end, display) {
                    let api = this.api();

                    let intVal = function(i) {
                        return typeof i === 'string' ? i.replace(/[\$,]/g, '') * 1 : typeof i ===
                            'number' ? i : 0;
                    };

                    let totalInput = api.column(1, {
                            page: 'current'
                        }).data()
                        .reduce((a, b) => intVal(a) + intVal(b), 0);
                    let totalOutput = api.column(2, {
                            page: 'current'
                        }).data()
                        .reduce((a, b) => intVal(a) + intVal(b), 0);
                    let totalNet = api.column(3, {
                            page: 'current'
                        }).data()
                        .reduce((a, b) => intVal(a) + intVal(b), 0);
                    let totalPaid = api.column(4, {
                            page: 'current'
                        }).data()
                        .reduce((a, b) => intVal(a) + intVal(b), 0);
                    let totalUnpaid = api.column(5, {
                            page: 'current'
                        }).data()
                        .reduce((a, b) => intVal(a) + intVal(b), 0);

                    $(api.column(1).footer()).html(formatAmountToLocaleString(totalInput));
                    $(api.column(2).footer()).html(formatAmountToLocaleString(totalOutput));
                    $(api.column(3).footer()).html(formatAmountToLocaleString(totalNet));
                    $(api.column(4).footer()).html(formatAmountToLocaleString(totalPaid));
                    $(api.column(5).footer()).html(formatAmountToLocaleString(totalUnpaid));
                }
            });

            // Year change handler
            $('#year-select').on('change', function() {
                tbl.ajax.reload();
            });

            // Payment form submission
            $(document).on('click', '#submit-report', function(e) {
                e.preventDefault();
                var paidValue = parseFloat($('#paid').val());
                var remainingUnpaid = parseFloat($('#remaining-unpaid').text().replace(/[^0-9.-]+/g, ""));

                if (!paidValue || paidValue <= 0) {
                    alert('Please enter a valid amount for payment.');
                    return;
                }

                if (paidValue > remainingUnpaid) {
                    alert('Payment amount cannot exceed the remaining unpaid amount of ' +
                        formatAmountToLocaleString(remainingUnpaid));
                    return;
                }

                var formData = $('#report-form').serialize();
                startLoader();

                $.ajax({
                    url: route('vat-reports.pay'),
                    method: 'POST',
                    data: formData,
                    success: function(response) {
                        if (response.success) {
                            displaySuccessMessage(response.message);
                            tbl.ajax.reload();
                            $('#payModal').modal('hide');
                            $('#report-form')[0].reset();
                        } else {
                            displayErrorMessage(response.message || 'Payment failed');
                        }
                        stopLoader();
                    },
                    error: function(xhr, status, error) {
                        stopLoader();
                        let errorMessage = 'Error processing payment';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
                        displayErrorMessage(errorMessage);
                    }
                });
            });

            // Close modal handler
            $(".btn-close, .close-modal").click(function() {
                $('#payModal').modal('hide');
            });
        });
    </script>
@endsection

{{-- @section('scripts')
    <script src="{{ mix('assets/js/custom/input-price-format.js') }}"></script>
    <script>
        'use strict';

        let categoryCreateUrl = route('vat-reports.store');
        let categoryUrl = route('vat-reports.index') + '/';
        let tbl; // Declare variable but don't initialize yet

        // Define format function first
        function formatAmountToLocaleString(amount) {
            return Number(amount).toLocaleString(undefined, {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        }

        // Define renderActionButtons function
        function renderActionButtons(row) {
            let buttons = '';


            // Get the year and period from the row data
            let year = row.year;
            let period = row.period;

            // For aggregated view, we don't have a report ID
            // We need to create URL with year and period parameters
            let viewUrl = `{{ route('vat-reports.view.aggregated') }}?year=${year}&period=${period}`;

            const messages = {
                delete: "{{ __('messages.common.delete') }}",
                edit: "{{ __('messages.common.edit') }}",
                view: "{{ __('messages.common.view') }}",
                pay: "Pay"
            };

            const permissions = {
                updateItem: "{{ auth()->user()->can('export_vat_reports') ? 'true' : 'false' }}",
                payItem: "{{ auth()->user()->can('pay_vat_reports') ? 'true' : 'false' }}",
                viewItem: "{{ auth()->user()->can('view_vat_reports') ? 'true' : 'false' }}"
            };

            if (permissions.viewItem === 'true') {
                buttons += `
        <a title="${messages.view}" href="${viewUrl}" class="btn btn-info action-btn has-icon view-btn" style="float:right;margin:2px;">
            <i class="fa fa-eye"></i>
        </a>`;
            }

            // For aggregated view, we can't have download or pay buttons
            // because there's no specific report ID
            // These would need to be handled differently

            return buttons;
        }

        // Function to show payment modal (make it global)
        window.showPaymentModal = function(id) {
            event.preventDefault();
            startLoader();

            $.ajax({
                url: '{{ route('vat-reports.modal', ['report' => '__reportId__']) }}'.replace('__reportId__',
                    id),
                method: 'GET',
                success: function(response) {
                    if (response.success) {
                        $("#period").val(response.data.period?.toUpperCase() ?? '');
                        $("#vat_report_id").val(response.data.id);
                        $("#bank_name").val(response.data.bank_name ?? '');
                        $("#account_number").val(response.data.account_number ?? '');
                        $("#branch").val(response.data?.branch?.name ?? '');
                        $("#paid").val(response.data.unpaid ?? 0);
                    }
                    $('#payModal').modal('show');
                    stopLoader();
                },
                error: function(xhr, status, error) {
                    stopLoader();
                    alert('Error loading payment modal');
                }
            });
        };

        // Initialize everything when document is ready
        $(document).ready(function() {
            // Populate year dropdown
            var currentYear = new Date().getFullYear();
            var startYear = 1900;
            var endYear = currentYear;

            for (var year = endYear; year >= startYear; year--) {
                $('#year-select').append(new Option(year, year));
            }
            $('#year-select').val(currentYear);

            // Initialize DataTable
            tbl = $('#assetCategoryTable').DataTable({
                oLanguage: {
                    'sEmptyTable': Lang.get('messages.common.no_data_available_in_table'),
                    'sInfo': Lang.get('messages.common.data_base_entries'),
                    sLengthMenu: Lang.get('messages.common.menu_entry'),
                    sInfoEmpty: Lang.get('messages.common.no_entry'),
                    sInfoFiltered: Lang.get('messages.common.filter_by'),
                    sZeroRecords: Lang.get('messages.common.no_matching'),
                },
                pageLength: 15,
                processing: true,
                serverSide: true,
                ajax: {
                    url: route('vat-reports.index'),
                    data: function(d) {
                        d.year = $('#year-select').val();
                    },
                    beforeSend: function() {
                        startLoader();
                    },
                    complete: function() {
                        stopLoader();
                    },
                },
                columns: [{
                        data: function(row) {
                            let element = document.createElement('textarea');
                            element.innerHTML = row.period_description?.toUpperCase() || '';
                            element.style.textAlign = 'center';
                            element.style.width = '100%';
                            return element.value;
                        },
                        name: 'period',
                        width: '25%',
                        className: 'text-center'
                    },
                    {
                        data: function(row) {
                            let element = document.createElement('textarea');
                            element.innerHTML = formatAmountToLocaleString(row.input ?? 0);
                            element.style.textAlign = 'center';
                            element.style.width = '100%';
                            return element.value;
                        },
                        name: 'input',
                        width: '15%',
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
                        width: '15%',
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
                            element.style.width = '100%';
                            return element.value;
                        },
                        name: 'paid',
                        width: '15%',
                        className: 'text-right',
                    },
                    {
                        data: function(row) {
                            let element = document.createElement('textarea');
                            var totalUnpaid = (row.input || 0) - (row.output || 0) - (row.paid ||
                                0);
                            element.innerHTML = formatAmountToLocaleString(totalUnpaid);
                            element.style.textAlign = 'right';
                            element.style.width = '100%';
                            return element.value;
                        },
                        className: 'text-right',
                        name: 'unpaid',
                        width: '15%'
                    },
                    {
                        // FIXED: Pass the entire row object
                        data: function(row) {
                            return renderActionButtons(row);
                        },
                        name: 'id',
                        width: '200px',
                        orderable: false,
                        searchable: false
                    }
                ],
                responsive: true,
                dom: "Bfrtip",
                buttons: [{
                    extend: 'csvHtml5',
                    text: '<i class="fas fa-file-csv"></i> Export CSV',
                    className: 'btn btn-sm',
                    exportOptions: {
                        columns: function(idx, data, node) {
                            return idx !== 6; // Exclude the action column (now index 6)
                        }
                    },
                    filename: function() {
                        var year = $('#year-select').val() || new Date().getFullYear();
                        return 'VAT_Report_' + year;
                    },
                    bom: true
                }],
                footerCallback: function(row, data, start, end, display) {
                    let api = this.api();

                    let intVal = function(i) {
                        return typeof i === 'string' ? i.replace(/[\$,]/g, '') * 1 : typeof i ===
                            'number' ? i : 0;
                    };

                    // Adjust column indices since we removed the Branch column
                    let totalInput = api.column(1, {
                            page: 'current'
                        }).data()
                        .reduce((a, b) => intVal(a) + intVal(b), 0);
                    let totalOutput = api.column(2, {
                            page: 'current'
                        }).data()
                        .reduce((a, b) => intVal(a) + intVal(b), 0);
                    let totalNet = api.column(3, {
                            page: 'current'
                        }).data()
                        .reduce((a, b) => intVal(a) + intVal(b), 0);
                    let totalPaid = api.column(4, {
                            page: 'current'
                        }).data()
                        .reduce((a, b) => intVal(a) + intVal(b), 0);
                    let totalUnpaid = api.column(5, {
                            page: 'current'
                        }).data()
                        .reduce((a, b) => intVal(a) + intVal(b), 0);

                    $(api.column(1).footer()).html(formatAmountToLocaleString(totalInput));
                    $(api.column(2).footer()).html(formatAmountToLocaleString(totalOutput));
                    $(api.column(3).footer()).html(formatAmountToLocaleString(totalNet));
                    $(api.column(4).footer()).html(formatAmountToLocaleString(totalPaid));
                    $(api.column(5).footer()).html(formatAmountToLocaleString(totalUnpaid));
                }
            });

            // Year change handler
            $('#year-select').on('change', function() {
                tbl.ajax.reload();
            });

            // Payment form submission
            $(document).on('click', '#submit-report', function(e) {
                e.preventDefault();
                var paidValue = $('#paid').val();

                if (!paidValue || paidValue <= 0) {
                    alert('Please enter a valid amount for "Paid"');
                    return;
                }

                var formData = $('#report-form').serialize();
                startLoader();

                $.ajax({
                    url: route('vat-reports.pay'),
                    method: 'POST',
                    data: formData,
                    success: function(response) {
                        if (response.success) {
                            displaySuccessMessage(response.message);
                            tbl.ajax.reload();
                            $('#payModal').modal('hide');
                            $('#report-form')[0].reset();
                        }
                        stopLoader();
                    },
                    error: function(xhr, status, error) {
                        stopLoader();
                        alert('Error processing payment');
                    }
                });
            });

            // Close modal handler
            $(".btn-close, .close-modal").click(function() {
                $('#payModal').modal('hide');
            });
        });
    </script>
@endsection --}}



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
