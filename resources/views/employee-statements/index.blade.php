@extends('layouts.app')
@section('title')
    {{ __('messages.employee-statements.name') }}
@endsection
@section('page_css')
    <link href="{{ asset('assets/css/jquery.dataTables.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="{{ asset('assets/css/bs4-summernote/summernote-bs4.css') }}">
@endsection
@section('content')
    <section class="section">
        <div class="section-header item-align-right">
            <h1>{{ __('messages.employee-statements.name') }}</h1>
            <div class="section-header-breadcrumb float-right">
                <div class="card-header-action mr-3 select2-mobile-margin">
                    @can('export_statement')
                        <div class="dropdown">
                            <button class="btn btn-primary dropdown-toggle" type="button" id="exportDropdown"
                                style="line-height: 30px;" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
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
            <div class="float-right">

            </div>
        </div>
        <div class="section-body">
            <div class="card">
                <div class="card-body">

                    <div class="row">
                        <div class="form-group col-sm-12 col-md-2">
                            {{ Form::label('from_date', __('messages.employee-statements.from')) }}
                            <input type="date" id="from_date" name="from_date" class="form-control">
                        </div>

                        <div class="form-group col-sm-12 col-md-2">
                            {{ Form::label('to_date', __('messages.employee-statements.to')) }}
                            <input type="date" id="to_date" name="to_date" class="form-control">
                        </div>
                        <div class="form-group col-12 col-md-2">
                            {{ Form::label('department', __('messages.common.department') . ':') }}
                            {{ Form::select('department_id', $departments, null, ['class' => 'form-control select2', 'required', 'id' => 'department_select', 'placeholder' => 'All']) }}
                        </div>

                        <div class="form-group col-12 col-md-2">
                            {{ Form::label('designation', __('messages.designations.name') . ':') }}
                            {{ Form::select('designation_id', [], null, ['class' => 'form-control select2', 'required', 'id' => 'designation_select']) }}
                        </div>

                        <div class="form-group col-12 col-md-3">
                            {{ Form::label('employee_id', __('messages.employees.name') . ':') }}
                            {{ Form::select('employee_id', [], null, ['class' => 'form-control select2', 'required', 'id' => 'employee_select']) }}
                        </div>

                    </div>

                </div>
            </div>
        </div>
        <div class="section-body">
            <div class="card">
                <div class="card-body">

                    @include('employee-statements.table')
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
@endsection
@section('scripts')
    <script src="{{ mix('assets/js/custom/input-price-format.js') }}"></script>
    <script>
        var tmpBalance = 0;
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
                url: route('employee-statements.index'),
                data: function(d) {

                    // Attach the selected month, customer, and project to the AJAX request
                    d.from_date = $('#from_date').val();
                    d.to_date = $('#to_date').val();
                    d.department_id = $('#department_select').val();
                    d.designation_id = $('#designation_select').val();
                    d.employee_id = $("#employee_select").val();
                },
                beforeSend: function() {
                    startLoader();
                },
                complete: function() {
                    stopLoader();
                }
            },

            columnDefs: [],
            columns: [{
                    data: function(row) {
                        return row.doc_date ??
                            ''; // Use the provided doc_date, or empty string if not available
                    },
                    name: 'posted_at',
                    className: 'text-left',
                },
                {
                    data: function(row, type, set, meta) {
                        return meta.row + 1; // Row number (starting from 1)
                    },
                    name: 'sl',
                    className: 'text-center',
                },
                {
                    data: function(row) {
                        return row.type ?? ''; // "Salary" or "Payslip"
                    },
                    name: '',
                    orderable: false, // Disable sorting
                    searchable: false,
                    className: 'text-start',
                },
                {
                    data: function(row) {
                        return row.month ?? ''; // Month in the format "Dec 2024"
                    },
                    name: 'salaryGenerate.salary_month',
                    className: 'text-center',
                },
                {
                    data: function(row) {
                        return Number(row.debit ?? 0).toFixed(2); // Credit value (formatted to 2 decimal places)
                    },
                    name: '',
                    className: 'text-right',
                },
                {
                    data: function(row) {
                        return Number(row.credit ?? 0).toFixed(2); // Debit value (formatted to 2 decimal places)
                    },
                    name: '',
                    className: 'text-right',
                },
                {
                    data: function(row) {

                        // Calculate the difference (debit - credit) if needed
                        return Number(row.balance ?? 0).toFixed(2);
                    },
                    name: '',
                    className: 'text-right',
                }

            ],


            responsive: true,

            lengthMenu: [5, 10, 25, 50, 75, 100],
            pageLength: 10,
            // Callback for row creation

            footerCallback: function(row, data, start, end, display) {
                var api = this.api();

                // Sum up values for debit, credit, and balance columns
                var totalDebit = api.column(4).data().reduce(function(a, b) {
                    return parseFloat(a) + parseFloat(b);
                }, 0);

                var totalCredit = api.column(5).data().reduce(function(a, b) {
                    return parseFloat(a) + parseFloat(b);
                }, 0);



                // Update the footer
                $(api.column(4).footer()).html(totalDebit.toFixed(2));
                $(api.column(5).footer()).html(totalCredit.toFixed(2));
                $(api.column(6).footer()).html((totalCredit - totalDebit).toFixed(2));
            },



        });

        $('#from_date,#to_date,#employee_select').on('change', function() {
            tbl.ajax.reload(); // Reload the DataTable
        });
    </script>


    <script>
        $(document).ready(function() {

            $('#export-pdf').on('click', function(e) {
                e.preventDefault();

                // Get the values from form inputs
                var fromDate = $('#from_date').val();
                var toDate = $('#to_date').val();
                var department_id = $('#department_select').val();
                var designation_id = $('#designation_select').val();
                var employee_id = $('#employee_select').val();

                // Check if employee ID is selected
                if (!employee_id) {
                    displayErrorMessage("Please select an employee before exporting.");
                    return; // Stop further execution
                }

                // Construct the URL for PDF export
                var exportUrl = "{{ route('employee-statements.export') }}" +
                    "?type=pdf" +
                    "&from_date=" + encodeURIComponent(fromDate) +
                    "&to_date=" + encodeURIComponent(toDate) +
                    "&department_id=" + encodeURIComponent(department_id) +
                    "&designation_id=" + encodeURIComponent(designation_id) +
                    "&employee_id=" + encodeURIComponent(employee_id);

                // Redirect to the constructed URL
                window.location.href = exportUrl;
            });

            // Handle CSV export
            $('#export-csv').on('click', function(e) {
                e.preventDefault();

                // Get the values from form inputs
                var fromDate = $('#from_date').val();
                var toDate = $('#to_date').val();
                var department_id = $('#department_select').val();
                var designation_id = $('#designation_select').val();
                var employee_id = $('#employee_select').val();

                // Check if employee ID is selected
                if (!employee_id) {
                    displayErrorMessage("Please select an employee before exporting.");
                    return; // Stop further execution
                }

                // Construct the URL for CSV export
                var exportUrl = "{{ route('employee-statements.export') }}" +
                    "?type=csv" +
                    "&from_date=" + encodeURIComponent(fromDate) +
                    "&to_date=" + encodeURIComponent(toDate) +
                    "&department_id=" + encodeURIComponent(department_id) +
                    "&designation_id=" + encodeURIComponent(designation_id) +
                    "&employee_id=" + encodeURIComponent(employee_id);


                // Redirect to the constructed URL
                window.location.href = exportUrl;
            });

        });
    </script>

    <script>
        var designations = @json($designations);
        var employees = @json($allEmployees);

        $(document).ready(function() {
            var allDesignations = @json($designations); // Get all designations data
            var allEmployees = @json($allEmployees); // Get all employees data

            // Function to populate designations dropdown
            function populateDesignations(departmentId) {
                var designationSelect = $('#designation_select');
                designationSelect.empty(); // Clear current options

                // Add 'All' option
                designationSelect.append(new Option('All', ''));

                // Filter designations by department
                $.each(allDesignations, function(index, designation) {
                    if (departmentId === '' || designation.department_id == departmentId) {
                        designationSelect.append(new Option(designation.name, designation.id));
                    }
                });

                designationSelect.trigger('change'); // Trigger change event to update employees
            }

            // Function to populate employees dropdown
            function populateEmployees(departmentId, designationId) {
                var employeeSelect = $('#employee_select');
                employeeSelect.empty(); // Clear current options

                // Add 'All' option
                // employeeSelect.append(new Option('All', ''));

                // Filter employees by department and designation
                $.each(allEmployees, function(index, employee) {
                    if (
                        (departmentId === '' || employee.department_id == departmentId) &&
                        (designationId === '' || employee.designation_id == designationId)
                    ) {
                        employeeSelect.append(new Option(employee.name, employee.id));
                    }
                });

                employeeSelect.trigger('change');
            }

            // Initialize designations and employees with 'All' options
            populateDesignations('');
            populateEmployees('', '');

            // Handle department selection change
            $('#department_select').on('change', function() {
                var departmentId = $(this).val(); // Get selected department ID
                populateDesignations(departmentId); // Update designations based on department
                populateEmployees(departmentId,
                    ''); // Update employees with the selected department and all designations
            });

            // Handle designation selection change
            $('#designation_select').on('change', function() {
                var departmentId = $('#department_select').val(); // Get selected department ID
                var designationId = $(this).val(); // Get selected designation ID
                populateEmployees(departmentId,
                    designationId); // Update employees based on department and designation
            });
        });
    </script>
@endsection
