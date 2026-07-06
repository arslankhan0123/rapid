@extends('layouts.app')
@section('title')
    {{ __('messages.salary_generates.salary_generates') }}
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
        <div class="section-header item-align-right  pt-0 pb-0">
            <h1>{{ __('messages.employee_salaries.name') }}</h1>
            <div class="section-header-breadcrumb float-right">
                <div class="card-header-action mr-3 select2-mobile-margin">
                </div>
            </div>
            <div class="section-body  bg-white mb-3 mt-1">
                <div class="row ">
                    <div class="col ">

                        {{ Form::label('title', 'Branch') }}
                        {{ Form::select('from', $usersBranches, null, [
                            'class' => 'form-control select2',
                            'id' => 'filterBranch',
                            'placeholder' => 'All',
                        ]) }}

                    </div>
                    <div class="col pl-0">
                        {{ Form::label('title', 'Month') }}
                        <input type="month" id="globalSearch" class="form-control"
                            placeholder="{{ __('messages.customer.select_month') }}">
                    </div>

                    <div class="col pl-0">
                        {{ Form::label('title', __('messages.employees.departments')) }}
                        {{ Form::select('department_id', $departments, null, ['class' => 'form-control', 'id' => 'departmentSelect', 'autocomplete' => 'off', 'placeholder' => __('messages.employees.select_department')]) }}
                    </div>
                    <div class="col pl-0">
                        {{ Form::label('designation_id', __('messages.employees.designations')) }}
                        {{ Form::select('designation_id', $designations->pluck('name', 'id'), null, ['class' => 'form-control', 'autocomplete' => 'off', 'id' => 'designationSelect', 'placeholder' => 'Select Designation']) }}
                    </div>


                    <div class="btn-group" role="group" aria-label="Bulk Actions" style="margin-top: 29px;">
                        <button type="button" class="btn btn-outline-info">Bulk</button>
                        <button type="button" class="btn btn-info" id="fullPaySlipBulkExport">Payslip</button>
                        <button type="button" class="btn btn-info" id="paySlipBulkExport">Salary</button>
                        <button type="button" class="btn btn-info" id="overtimeBulkExport">Overtime</button>
                    </div>

                </div>


            </div>

        </div>
        {{-- <div class="section-body">
            <div class="card">
                <div class="card-body">

                    <div class="row">

                        <div class="form-group col-sm-12  col-md-4">
                            {{ Form::label('employee_id', __('messages.customer.select_customer') . ':') }}
                            {{ Form::select('customer_id', $customers, null, ['class' => 'form-control', 'required', 'id' => 'customer_select', 'placeholder' => __('messages.customer.all')]) }}
                        </div>
                        <div class="form-group col-sm-12  col-md-4">
                            {{ Form::label('employee_id', __('messages.customer.select_project') . ':') }}
                            {{ Form::select('customer_id', [], null, ['class' => 'form-control', 'required', 'id' => 'project_select', 'placeholder' => __('messages.customer.all')]) }}
                        </div>
                    </div>

                </div>
            </div>
        </div> --}}

        <div class="section-body">
            <div class="card">
                <div class="card-body">
                    @if (session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif
                    @include('employee_salaries.table_employee_salaries')
                </div>
            </div>
        </div>
    </section>
    @include('salary_generates.templates.templates')
    @include('employee_salaries.modals.pay')
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
                url: route('employee-salaries.index'),
                data: function(d) {
                    // Attach the selected month, customer, and project to the AJAX request
                    d.month = $('#globalSearch').val();
                    d.customer_id = $('#customer_select').val();
                    d.project_id = $('#project_select').val();
                    d.filterBranch = $("#filterBranch").val();
                    d.department = $("#departmentSelect").val();
                    d.designation = $("#designationSelect").val();
                },
                beforeSend: function() {
                    startLoader();
                },
                complete: function() {
                    stopLoader();
                }
            },
            columnDefs: [],
            order: [
                [2, 'desc']
            ],
            columns: [{
                    data: function(row) {
                        if (row.employee) {
                            return row.employee.iqama_no;
                        }
                        return '';
                    },
                    name: 'employee_id',

                },
                {
                    data: function(row) {
                        if (row.employee) {
                            return row.employee.name;
                        }
                        return '';
                    },
                    name: 'employee.name',

                },
                {
                    data: function(row) {
                        if (row.employee.designation) {
                            return row.employee.designation.name;
                        }
                        return '';
                    },
                    name: 'employee.designation.name',

                },

                {
                    data: function(row) {

                        return row.branch?.name ?? '';
                    },
                    name: 'branch.name',

                },
                {
                    data: function(row) {
                        // Ensure salary_payment and net_salary exist
                        if (row.salary_payment && row.salary_payment.amount) {
                            if (parseFloat(row.salary_payment.amount) === parseFloat(row.net_salary)) {
                                return '<span class="text-success">Paid</span>';
                            } else if (parseFloat(row.salary_payment.amount) < parseFloat(row.net_salary)) {
                                return '<span class="text-warning">Partially Paid</span>';
                            }
                        }
                        return '<span class="text-danger">Unpaid</span>';
                    },
                    className: 'text-right'
                },

                {
                    data: function(row) {
                        const formattedAmount = (row.net_salary ?? 0).toFixed(2); // Ensure 2 decimal places
                        return formattedAmount;
                    },
                    name: 'net_salary',

                    className: 'text-right'
                },

                {
                    data: function(row) {
                        if (row.net_salary > 0) {
                            const paidAmount = parseFloat(row.salary_payment?.amount ?? 0);
                            const netSalary = parseFloat(row.net_salary);
                
                            let actionButtons = '<div style="float:right;">';
                
                            // Only show Pay button if not fully paid
                            if (paidAmount < netSalary) {
                                actionButtons += `
                                    <button class="btn btn-danger btn-sm download-btn" data-row='${JSON.stringify(row)}' onclick='payItem(${JSON.stringify(row)})'>
                                        Pay
                                    </button>`;
                            }
                
                            actionButtons += `
                                <button class="btn btn-warning btn-sm download-btn" onclick="printItem(${row.id})">
                                    Print
                                </button>
                                <button class="btn btn-success btn-sm download-btn" onclick="downloadItem(${row.id})">
                                    Pdf
                                </button>
                                <button class="btn btn-info btn-sm view-btn" onclick="viewItem(${row.id})">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>`;
                
                            return actionButtons;
                        } else {
                            return '';
                        }
                    },
                    name: 'action',
                    width: '15%'


                },
            ],
            responsive: true,
            dom: "<'row'<'col-sm-6'l><'col-sm-6'f>>" + // Length dropdown and search bar
                "B" + // Buttons
                "rt" + // Table
                "<'row'<'col-sm-6'i><'col-sm-6'p>>", // Information and pagination
            buttons: [
                @can('export_employee_salaries')
                    {
                        extend: 'csvHtml5',
                        text: '<i class="fas fa-file-csv"></i> Export CSV',
                        className: 'btn btn-sm',
                        exportOptions: {
                            // Exclude the action column from the export
                            columns: function(idx, data, node) {
                                return idx !== 6; // Exclude the action column at index 4
                            }
                        }
                    }, {
                        extend: 'excelHtml5',
                        text: '<i class="fas fa-file-excel"></i> Export Excel',
                        className: 'btn btn-sm',
                        exportOptions: {
                            // Exclude the action column from the export
                            columns: function(idx, data, node) {
                                return idx !== 6; // Exclude the action column at index 4
                            }
                        }
                    }, {
                        extend: 'pdfHtml5',
                        text: '<i class="fas fa-file-pdf"></i> Export PDF',
                        className: 'btn btn-sm', // Styled button
                        orientation: 'portrait',
                        pageSize: 'A4',
                        exportOptions: {
                            // Exclude the action column from the export
                            columns: function(idx, data, node) {
                                return idx !== 6; // Exclude the action column at index 4
                            }
                        },
                        customize: function(doc) {
                            // Optional customization of the PDF
                            doc.content[1].table.widths =
                                Array(doc.content[1].table.body[0].length + 1).join('*').split('');
                            // Add any other PDF customization here if needed
                        }
                    }
                @endcan
            ],
            lengthMenu: [
                [100, 300, 500, 999, -1],
                [100, 300, 500, 999, "All"]
            ],
            pageLength: 100, // Default page length
        });

        // Event handlers to trigger table reload when inputs change
        $('#globalSearch,#customer_select, #project_select,#filterBranch,#departmentSelect,#designationSelect').on('change',
            function() {
                tbl.ajax.reload(); // Reload the DataTable
            });

        function viewItem(id) {
            const url = route('employee-salaries.payslip.view', {
                salarySheet: id
            });
            window.location.href = url;
        }

        function printItem(id) {

            const url = route('employee-salaries.payslip.download-view', {
                salarySheet: id
            });

            window.open(url, '_blank');
        }

        function downloadItem(id) {
            const url = route('employee-salaries.payslip.download', {
                salarySheet: id
            });
            window.open(url, '_blank');
        }



        let banks = @JSON($banks);

        function payItem(row) {
            if (typeof row === "string") {
                try {
                    row = JSON.parse(row);
                } catch (error) {
                    console.error("Invalid JSON:", error);
                    return; // Stop execution if JSON is invalid
                }
            }

            // Extract salary_payment data if available
            let salaryPayment = row.salary_payment || {}; // Use empty object if not present

            // Set salary ID
            $("#salaryId").val(row.id || "");

            // Get payment type (priority: salary_payment > row)
            let paymentType = salaryPayment.payment_type || row.payment_type || "cash";
            $("#paymentMethod").val(paymentType).trigger("change");

            // Show or hide bank options based on payment type
            if (paymentType === "bank") {
                $("#bankOptions").removeClass("d-none");
            } else {
                $("#bankOptions").addClass("d-none");
            }

            // Populate bank select dropdown
            let $bankSelect = $("#bankSelect");
            $bankSelect.empty().append('<option value="">Select Bank</option>'); // Clear previous options

            Object.entries(banks).forEach(([id, name]) => {
                let selected = id == (salaryPayment.bank_id || row.bank_id) ? "selected" : "";
                $bankSelect.append(`<option value="${id}" ${selected}>${name}</option>`);
            });

            // Set amount (priority: salary_payment > row)
            $("#amount").val(salaryPayment.amount || row.amount || "0.00");
            $("#listPayModal").modal("show");
        }

        // Show or hide bank options based on selection change
        $(document).on("change", "#paymentMethod", function() {
            if ($(this).val() === "bank") {
                $("#bankOptions").removeClass("d-none");
            } else {
                $("#bankOptions").addClass("d-none");
            }
        });

        // Handle form submission
        $('#payForm').on('submit', function(e) {
            e.preventDefault();
            const formData = $(this).serialize();
            startLoader();
            console.log(formData);
            // Example: Perform an AJAX request to submit the form
            $.ajax({
                url: '{{ route('employee-salaries.pay-salary') }}', // Your route URL
                type: 'POST',
                data: formData,
                success: function(response) {
                    stopLoader();
                    displaySuccessMessage(response.message);
                    $('#payModal').modal('hide'); // Close modal
                    location.reload();
                },
                error: function(xhr) {
                    stopLoader();
                    const errors = xhr.responseJSON.errors;
                    let errorMsg = '';
                    $.each(errors, function(key, value) {
                        errorMsg += value + '\n';
                    });
                    displayErrorMessage(errorMsg);
                }
            });
        });
    </script>

    <script>
        $(document).ready(function() {
            // Initialize Select2 for customer and project selects
            $('#customer_select').select2({
                width: '100%',
                allowClear: true
            });
            $('#project_select').select2({
                width: '100%',
                allowClear: true
            });

            // Store all projects with customer_id as a JavaScript object
            var projects = @json($projects); // Convert Laravel collection to JSON object

            // Function to populate project dropdown based on selected customer
            function updateProjects(customerId) {
                var projectSelect = $('#project_select');
                projectSelect.empty(); // Clear the project dropdown

                // Add a default 'All' option if no customer is selected
                projectSelect.append($('<option>', {
                    value: '',
                    text: '{{ __('messages.customer.all') }}'
                }));

                // If a customer is selected, filter and populate the projects
                if (customerId) {
                    var filteredProjects = projects.filter(function(project) {
                        return project.customer_id == customerId;
                    });

                    // Add the filtered projects to the project dropdown
                    $.each(filteredProjects, function(index, project) {
                        projectSelect.append($('<option>', {
                            value: project.id,
                            text: project.project_name
                        }));
                    });

                    // Automatically select the first project if there is only one
                    if (filteredProjects.length === 1) {
                        projectSelect.val(filteredProjects[0].id).trigger('change');
                    }
                }

                // Refresh Select2 to show updated options
                projectSelect.trigger('change.select2');
            }

            // On customer select change, update the project dropdown
            $('#customer_select').on('change', function() {
                var customerId = $(this).val();
                updateProjects(customerId); // Update project dropdown based on selected customer
            });

            // Trigger an update on page load in case customer is preselected
            updateProjects($('#customer_select').val());



            const designations = @json($designations);

            function updateDesignations() {
                const departmentId = $('#departmentSelect').val(); // Get selected department
                const filteredDesignations = designations.filter(designation => designation.department_id ==
                    departmentId); // Filter by department_id

                $('#designationSelect').empty(); // Clear the existing designations
                $('#designationSelect').append(
                    '<option value="">{{ __('messages.employees.designations') }}</option>'
                );

                $.each(filteredDesignations, function(index, designation) {
                    console.log(designation);
                    $('#designationSelect').append(
                        $('<option>', {
                            value: designation.id,
                            text: designation.name
                        })
                    );
                });
            }
            $('#departmentSelect').on('change', updateDesignations); // Update designations on department change
            updateDesignations();


        });
    </script>

    <script>
        function exportData(exportType) {
            displaySuccessMessage(exportType === 'payslip' ? "Downloading Bulk Payslip" : "Downloading Bulk Overtime");

            // Gather filter data
            const branch = $('#filterBranch').val();
            const month = $('#globalSearch').val();
            const department = $('#departmentSelect').val();
            const designation = $('#designationSelect').val();

            // Construct the URL with query parameters and exportType (either 'payslip' or 'overtime')
            const url = "{{ route('employee-salaries.bulk.export') }}?filterBranch=" + encodeURIComponent(branch) +
                "&month=" + encodeURIComponent(month) +
                "&department=" + encodeURIComponent(department) +
                "&designation=" + encodeURIComponent(designation) +
                "&type=" + encodeURIComponent(exportType); // Added the export type parameter

            // Redirect to the URL which triggers the backend export
            window.open(url, '_blank');
        }
        $(document).on('click', '#fullPaySlipBulkExport', function() {
            exportData('full_salary');
        });

        // Bind the function to the Bulk Payslip Export button
        $(document).on('click', '#paySlipBulkExport', function() {
            exportData('payslip');
        });

        // Bind the function to the Bulk Overtime Export button
        $(document).on('click', '#overtimeBulkExport', function() {
            exportData('overtime');
        });
    </script>
@endsection
