@extends('layouts.app')
@section('title')
    {{ __('messages.manage_attendances.name') }}
@endsection
@section('page_css')
    <style>
        #importBtn {
            line-height: 30px !important;

        }

        #btnPost,
        #exportButton {
            /* background: red !important; */
            border: none;
            height: 40px;
            line-height: 35px;
            min-width: 100px;
            font-size: 18px !important;
        }

        .btn-primary i {
            font-size: 20px !important;
        }



        .table thead {
            background-color: #f8f9fa;
            /* Optional background color */
            position: sticky;
            top: 0;
            z-index: 1;
            /* Ensures the header stays above the body */
        }

        .main-footer {

            margin-top: 0px !important;
        }

        .page_contents {
            height: auto;
            /* Allow JS to handle height */
            background-color: transparent;
            /* Default */
            overflow: hidden;
            /* Optional default */
        }

        .main-wrapper-1 .section .section-header {
            margin-top: -28px !important;
        }
    </style>
@endsection
@section('content')
    <section class="section">
        <div class="section-header item-align-right" style="height: 65px;margin-bottom:10px;">
            <h1>{{ __('messages.manage_attendances.name') }}</h1>
            <div class="section-header-breadcrumb float-right">
                <div class="card-header-action mr-3 select2-mobile-margin">
                    @can('import_manage_attendances')
                        <a class="btn btn-primary" data-toggle="modal" data-target="#importModal" id="importBtn"
                            style="display: none;">
                            {{ __('messages.manage_attendances.import') }}</a>
                    @endcan
                </div>
            </div>

        </div>

        <div class="section-body mb-2 bg-white">
            <div class="card mb-0">
                <div class="card-body  pl-10 pb-0 pt-1 m-0">
                    <div class="row">
                        <div class="form-group col-sm-12 col-md-2">
                            {{ Form::label('employee_id', __('messages.customer.select_month') . ':') }}
                            <input type="month" id="globalSearch" class="form-control"
                                value="{{ \Carbon\Carbon::now()->format('Y-m') }}"
                                placeholder=" __('messages.customer.select_month')">
                        </div>
                        <div class="form-group  col-md-2">
                            {{ Form::label('department', __('messages.common.department') . ':') }}
                            {{ Form::select('department_id', $departments, null, ['class' => 'form-control select2', 'required', 'id' => 'department_select', 'placeholder' => 'All']) }}
                        </div>

                        <div class="form-group  col-md-2">
                            {{ Form::label('designation', __('messages.designations.name') . ':') }}
                            {{ Form::select('designation_id', [], null, ['class' => 'form-control select2', 'required', 'id' => 'designation_select']) }}
                        </div>

                        <div class="form-group  col-md-3">
                            {{ Form::label('employee_id', __('messages.employees.name') . ':') }}
                            {{ Form::select('employee_id', [], null, ['class' => 'form-control select2', 'required', 'id' => 'employee_select']) }}
                        </div>

                        <div class="form-group col-md-2">
                            {{ Form::label('bonus_type_id', __('messages.branches.name') . ':') }}<span
                                class="required">*</span>
                            {{ Form::select('from', $usersBranches, null, [
                                'class' => 'form-control select2',
                                'id' => 'from_branch',
                            ]) }}
                        </div>

                        <div class="col-sm-12 col-md-1 mt-4" style="padding:5px;">
                            <button type="button" id="submitButton" class="btn btn-primary">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                </div>

            </div>

        </div>

        <div class="section-body ">
            <div class="card mb-0">
                <div class="card-body pl-10 pb-0 pt-1 m-0">

                    <div class="row mb-1 justify-content-end">
                        <div class="col-md-2 col-sm-6 col-lg-2 ">
                            <input type="text" name="iqama_no" id="iqama_search" class="form-control"
                                placeholder="{{ __('messages.manage_attendances.search_iqama') }}" style="display: none; ">
                        </div>
                    </div>

                    <div style="display: none;" id="datatable">

                        {{ Form::open(['url' => route('manage-attendances.store'), 'method' => 'POST', 'id' => 'addNewFormDepartmentNew']) }}

                        <table class="table table-responsive table-md  table-striped table-bordered page_contents"
                            id="manageAttendancesTable">
                            <thead>
                                <tr>
                                    <th scope="col" class="p-2">{{ __('messages.manage_attendances.sl') }}</th>
                                    <th scope="col" class="p-1">{{ __('messages.branches.name') }}</th>
                                    <th scope="col" class="p-1">{{ __('messages.employees.iqama_no') }}</th>
                                    <th scope="col" class="p-1">{{ __('messages.manage_attendances.employee_name') }}
                                    </th>
                                    <th scope="col" class="p-1">{{ __('messages.manage_attendances.postition') }}
                                    </th>
                                    <th scope="col" class="p-1">{{ __('messages.manage_attendances.actual_hours') }}
                                    </th>
                                    <th scope="col" class="p-1">
                                        {{ __('messages.manage_attendances.overtime_hours') }}</th>
                                    <th scope="col" class="p-1">{{ __('messages.manage_attendances.total_hours') }}
                                    </th>
                                    <th scope="col" class="p-1">{{ __('messages.manage_attendances.absent_hours') }}
                                    </th>
                                    <th scope="col" class="p-1">{{ __('messages.manage_attendances.net_hours') }}
                                    </th>
                                    {{-- <th scope="col">{{ __('messages.manage_attendances.rate') }}</th> --}}
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                </tr>
                            </tbody>
                        </table>
                        <div class="row justify-content-center">
                            <p>{{ __('messages.manage_attendances.total_hours') }} : <span
                                    class="all_employee_total_hours"></span></p>
                        </div>


                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div>
                                @can('export_manage_attendances')
                                    <a id="exportButton" class="btn btn-success">
                                        <i class="fas fa-file-csv"></i> {{ __('messages.manage_attendances.export') }}
                                    </a>
                                @endcan
                            </div>
                            <div>
                                <h6 class="text-danger errMsg"></h6>
                            </div>
                            <div>

                                {{-- @can('post_manage_attendances')
                                    {{ Form::button(__('messages.common.submit_with_post'), ['type' => 'button', 'class' => 'btn btn-info', 'id' => 'btnPost', 'data-loading-text' => "<span class='spinner-border spinner-border-sm'></span> Processing..."]) }}
                                @endcan --}}
                                @can('create_manage_attendances')
                                    {{ Form::button(__('messages.common.submit'), ['type' => 'submit', 'class' => 'btn btn-primary mr-2', 'id' => 'btnSave', 'data-loading-text' => "<span class='spinner-border spinner-border-sm'></span> Processing..."]) }}
                                @endcan
                            </div>
                        </div>
                        {{ Form::close() }}

                    </div>
                </div>
            </div>
        </div>
    </section>
    @include('manage_attendances.import')
@endsection
@section('page_scripts')
    <script src="{{ asset('assets/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ mix('assets/js/custom/custom-datatable.js') }}"></script>
@endsection
@section('scripts')
    <script>
        adjustHeights();

        // Adjust heights on window resize
        $(window).resize(function() {
            adjustHeights();
        });

        function adjustHeights() {
            // Get the window height and width
            const windowHeight = $(window).height();
            const windowWidth = $(window).width();

            let contentHeight;

            // Check the screen size and adjust height percentage
            if (windowWidth >= 1440) {
                // Large screens (Desktops)
                contentHeight = (52 / 100) * windowHeight; // 40% of window height
            } else if (windowWidth >= 1024) {
                // Medium screens (Laptops)
                contentHeight = (35 / 100) * windowHeight; // 36% of window height
            } else {
                // Smaller screens (Fallback)
                contentHeight = (30 / 100) * windowHeight; // 30% for smaller screens
            }

            // Apply the height and overflow styles
            $('.page_contents').css({
                'height': `${contentHeight}px`,
                'overflow': 'auto' // Add scrollbar if overflow
            });
        }

        // Run initially
        adjustHeights();

        // Adjust on window resize
        $(window).resize(function() {
            adjustHeights();
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
                employeeSelect.append(new Option('All', ''));

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
    <script>
        var all_employee_total_hours = 0;
        $(document).ready(function() {
            // Set the current month by default
            const currentMonth = new Date().toISOString().slice(0, 7);
            $('#globalSearch').val(currentMonth);

            // Get all projects data from Laravel PHP and convert to JavaScript array
            let allProjects = @json($projects);

            // Get the customer select and project select elements
            let customerSelect = $('#customer_select');
            let projectSelect = $('#project_select');

            // Function to filter and populate the projects based on the selected customer
            function filterProjects(customerId) {
                projectSelect.empty();

                if (customerId) {
                    let filteredProjects = allProjects.filter(project => project.customer_id == customerId);

                    // Populate the filtered projects in the dropdown
                    $.each(filteredProjects, function(index, project) {
                        projectSelect.append('<option value="' + project.id + '">' + project.project_name +
                            '</option>');
                    });

                    // Set the first project as selected by default
                    if (filteredProjects.length > 0) {
                        projectSelect.val(filteredProjects[0].id); // Automatically select the first project
                    }
                }
            }

            // Automatically select the first customer and load its projects
            if (customerSelect.val()) {
                let firstCustomerId = customerSelect.val();
                filterProjects(firstCustomerId);
            }

            // Handle customer selection change
            customerSelect.change(function() {
                let customerId = $(this).val();
                filterProjects(customerId);
            });
        });
    </script>

    <script>
        function areDatesEqual(dateStr1, dateStr2) {
            // Function to normalize date strings
            const normalizeDate = (dateStr) => {
                // Split the date string into parts and pad day and month with leading zeros
                const [year, month, day] = dateStr.split('-').map(part => part.padStart(2, '0'));
                // Return the normalized date string in YYYY-MM-DD format
                return `${year}-${month}-${day}`;
            };

            // Normalize both date strings
            const normalizedDateStr1 = normalizeDate(dateStr1);
            const normalizedDateStr2 = normalizeDate(dateStr2);

            // Create Date objects from the normalized date strings
            const date1 = new Date(normalizedDateStr1);
            const date2 = new Date(normalizedDateStr2);

            // Compare the two dates and return true if they are the same, false otherwise
            return date1.getTime() === date2.getTime();
        }
    </script>

    <script>
        $(document).ready(function() {
            function addDaysToTable(month, year) {
                // Clear existing day headers
                $('#manageAttendancesTable thead tr th.day-header').remove();

                // Get the number of days in the selected month and year
                let daysInMonth = new Date(year, month, 0).getDate();

                // Add each day as a column header
                for (let day = 1; day <= daysInMonth; day++) {
                    let date = new Date(year, month - 1, day);
                    let dayName = date.toLocaleString('default', {
                        weekday: 'short'
                    }); // Get the day name (e.g., Sun, Mon)
                    let formattedDay = String(day).padStart(2, '0');
                    $('#manageAttendancesTable thead tr').append(
                        `<th class="day-header text-center p-0">${formattedDay}<br>${dayName}</th>`
                    );
                }
            }

            function populateTable(attendanceData, customerId, projectId, month, year) {
                // Clear existing rows in the table body
                $('#manageAttendancesTable tbody').empty();
                // Loop through the response data to add rows
                var actual_hour = 0;
                attendanceData.forEach((employee, index) => {

                    console.log(employee);
                    var rate = 0;
                    if (employee.employee_rates && employee.employee_rates[0]) {
                        rate = employee.employee_rates[0].rate;
                    }
                    // Create a new row for each employee
                    let row =
                        `<tr data-employee-id="${employee.id}">
                        <td>${index + 1}</td>
                        <td class="pl-1"><div  style="width:100px  !important;color:black;">${employee.branch?.name??''}</div></td>
                        <td class="pl-1" style='color:black;'>${employee.iqama_no}</td>
                        <td class="pl-1" ><div  style="width:160px  !important;color:black;">${employee.name}</div></td>
                        <td class="pl-1"><div  style="width:100px  !important;color:black;">${employee.designation.name??""}</div></td>
                         <td class="p-1">
                            <input type="hidden" name="branch_id[${employee.id}]" class="form-control">
                            <input type="text" name="actual_hours[${employee.id}]" class="form-control actual_hours" style="width:100px !important; color:black; text-align:center;" readonly>
                        </td>
                        <td class="p-1">
                            <input type="text" name="overtime_hours[${employee.id}]" class="form-control overtime_hours" style="width:120px !important; color:black; text-align:center;" readonly>
                        </td>
                        <td class="p-1">
                            <input type="text" name="total_hours[${employee.id}]" class="form-control total-hours" style="width:100px !important; color:black; text-align:center;" readonly>
                        </td class="p-1">
                        <td class="p-1">
                            <input type="text" name="total_absent[${employee.id}]" class="form-control total-absent" style="width:100px !important; color:black; text-align:center;" readonly>
                        </td>
                        <td class="p-1">
                            <input type="text" name="net_hours[${employee.id}]" class="form-control net_hours" style="width:70px !important; color:black; text-align:center;" readonly>
                        </td>


                        `;
                    let daysInMonth = new Date(year, month, 0).getDate();
                    var hours = 8;

                    var net_hours = 0;
                    var overtime_hours = 0
                    // Add input fields for each day
                    for (let day = 1; day <= daysInMonth; day++) {
                        let date = new Date(year, month - 1, day);
                        let dayName = date.toLocaleString('default', {
                            weekday: 'short'
                        }); // Get the day name (e.g., Sun, Mon)


                        // const latestDate = new Date(employee
                        //     .latest_date);
                        // if (date >= latestDate) {
                        //     hours = 8;
                        // } else {
                        //     hours = 0;
                        // }

                        // Skip Fridays
                        if (dayName === 'Fri') {
                            hours = 0;
                        };

                        // console.log(date, `${employee.latest_date}`);


                        actual_hour += hours;
                        let formattedDate =
                            `${year}-${String(month).padStart(2, '0')}-${String(day).padStart(2, '0')}`; // Format date as YYYY-MM-DD

                        if (employee.attendance) {
                            // Handle the case when employee.attendance exists
                            employee.attendance.forEach(attendanceRecord => {
                                // Process each attendance record
                                if (areDatesEqual(attendanceRecord.date, formattedDate) &&
                                    attendanceRecord.customer_id == customerId &&
                                    projectId == attendanceRecord.project_id) {
                                    hours = attendanceRecord.hours;
                                }
                            });
                        }

                        row += `<td class="day-input p-1">
                        <input type="number" name="attendance[${employee.id}][${formattedDate}]" class="form-control" style="width: 75px;" value='${hours}' >
                    </td>`;
                        hours = 8;
                    }

                    // Append total hours and total absent columns to the right
                    row += ` </tr>`;

                    // Append the row to the table body
                    $('#manageAttendancesTable tbody').append(row);
                    $(".actual_hours").val(actual_hour);
                    actual_hour = 0;
                });

                // Initialize totals after populating the table
                updateTotals(actual_hour);
                //all_employee_total_hours=0;
            }

            // Function to update total_hours and total_absent
            function updateTotals() {
                const hoursPerDay = 8;
                let all_employee_total_hours = 0; // Initialize total hours for all employees

                $('#manageAttendancesTable tbody tr').each(function() {
                    let totalHours = 0;
                    let totalAbsentHours = 0;

                    $(this).find('td.day-input input').each(function() {
                        let value = parseFloat($(this).val()) ||
                            0; // Get the hours value or default to 0

                        // Extract the date from the input name attribute
                        let dateStr = $(this).attr('name').match(/\d{4}-\d{2}-\d{2}/)[0];
                        let date = new Date(dateStr);
                        let dayName = date.toLocaleString('default', {
                            weekday: 'short'
                        });

                        // Check if the day is not Friday
                        if (dayName !== 'Fri') {
                            // If the value is less than 8 and it's not a Friday, add the difference to absent hours
                            if (value < hoursPerDay) {
                                totalAbsentHours += (hoursPerDay - value);
                            }
                        }

                        // Sum the total hours worked
                        totalHours += value;
                    });

                    // Add the total hours of the current employee to the overall total
                    all_employee_total_hours += totalHours;

                    // Retrieve the actual hours value from the corresponding column
                    let actualHours = parseFloat($(this).find('.actual_hours').val()) || 0;

                    // Update the total hours and total absent hours (in hours) in the table
                    $(this).find('.total-hours').val(totalHours);
                    $(this).find('.total-absent').val(totalAbsentHours); // Show total absent in hours

                    // Calculate and update overtime hours if total hours exceed actual hours
                    let overtimeHours = Math.max(0, totalHours - actualHours);
                    $(this).find('.overtime_hours').val(overtimeHours);
                    $(this).find('.net_hours').val(totalHours - totalAbsentHours);
                });

                // Display the total hours of all employees in the target div
                $(".all_employee_total_hours").text(all_employee_total_hours);
            }


            // Set up an event listener for the Search button click
            $('#submitButton').on('click', function() {
                let selectedMonth = $('#globalSearch').val(); // Expected format: YYYY-MM
                let [year, month] = selectedMonth.split('-');
                let customerId = $('#customer_select').val();
                let projectId = $('#project_select').val();
                let iqamaNo = $('#iqama_search').val();

                let department_id = $('#department_select').val();
                let desgnation_id = $("#designation_select").val();
                let employee_id = $("#employee_select").val();
                let branch_id = $("#from_branch").val();


                $("#datatable").hide();
                $("#importBtn").hide();
                $(".infy-loader").show();
                // Perform the AJAX call to fetch data
                $.ajax({
                    url: route('manage-attendances.index'), // Replace with your actual route URL
                    method: 'GET',
                    data: {
                        month: selectedMonth,
                        customer_id: customerId,
                        project_id: projectId,
                        iqama_no: iqamaNo,
                        department_id: department_id,
                        desgnation_id: desgnation_id,
                        employee_id: employee_id,
                        branch_id: branch_id
                    },
                    success: function(response) {

                        $("#datatable").show();
                        $("#importBtn").show();
                        $(".infy-loader").hide();
                        // Call the function to add day columns based on selected month and year
                        addDaysToTable(parseInt(month), parseInt(year));
                        // Call the function to populate table rows with the fetched data
                        populateTable(response.data, customerId, projectId, month, year);
                        $("#iqama_search").show();
                        $('.customer').html();
                        $('.project').html();



                        if (response.salaryStatus || response.data.length == 0) {
                            $('#manageAttendancesTable').find('input[type="number"]').prop(
                                'disabled', true);
                            $("#btnSave").prop(
                                'disabled', true);
                            $(".errMsg").text("Salary Sheet has been generated of this month");
                        } else {
                            $('#manageAttendancesTable').find('input[type="number"]').prop(
                                'disabled', false);
                            $("#btnSave").prop(
                                'disabled', false);
                            $(".errMsg").text("");
                        }

                        if (response.data.length == 0) {
                            $(".errMsg").text("No data found for the selected criteria");
                        }


                    },
                    error: function(error) {
                        console.error('Error fetching data:', error);
                    }
                });
            });

            // Event listener to update totals on input change
            $('#manageAttendancesTable').on('input', 'td.day-input input', function() {
                updateTotals();
            });
        });
    </script>

    <script>
        $(document).ready(function() {


            let isPost = false; // Variable to track if "Post" was clicked

            // Event listener for the "Submit with Post" button
            $('#btnPost').on('click', function(event) {
                event.preventDefault();

                isPost = true; // Set to true when "Submit with Post" button is clicked
                $('#btnSave').trigger('click'); // Trigger the default submit button click
            });


            // Event listener for the "Submit" button
            $('#btnSave').on('click', function(event) {
                event.preventDefault(); // Prevent the default form submission

                // Gather form data
                let formData = $('#addNewFormDepartmentNew').serializeArray();

                // If "Submit with Post" is clicked, add the extra parameter
                if (isPost) {
                    formData.push({
                        name: 'post',
                        value: true
                    });
                }
                processingBtn('#addNewFormDepartmentNew', '#btnPost', 'loading');
                processingBtn('#addNewFormDepartmentNew', '#btnSave', 'loading');

                // Perform the AJAX call to submit the form data
                $.ajax({
                    url: $('#addNewFormDepartmentNew').attr('action'),
                    method: 'POST',
                    data: formData,
                    success: function(response) {
                        console.log(response); // Log the response for debugging
                        if (response.success) {
                            // Handle success (e.g., show a success message, refresh data, etc.)
                            displaySuccessMessage(response.message);
                        } else {
                            // Handle failure (e.g., show an error message)
                            alert('An error occurred while saving attendance data.');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX Error:', status, error);
                        // Handle AJAX error (e.g., show an error message)
                        alert('An error occurred while saving attendance data.');
                    },
                    complete: function() {
                        processingBtn('#addNewFormDepartmentNew', '#btnSave');
                        processingBtn('#addNewFormDepartmentNew', '#btnPost');
                        isPost = false; // Reset the "Post" flag after the request
                    },
                });
            });
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            function exportToCSV() {
                let csv = [];

                // Get table headers
                let header = [];
                $('#manageAttendancesTable thead th').each(function() {

                    const text = $(this).text();
                    const matches = text.match(/(\d+)([A-Za-z]+)/);

                    if (matches) {
                        const number = matches[1];
                        const word = matches[2];
                        const formattedText = `${number}\n${word}`;
                        $(this).text(formattedText);

                    }

                    // Ensure header height is 40px
                    $(this).css('text-align', 'end');

                    header.push('"' + $(this).text().replace(/"/g, '""') + '"'); // Escape quotes
                });
                csv.push(header.join(','));

                // Get table rows data including input values
                $('#manageAttendancesTable tbody tr').each(function() {
                    let row = [];
                    $(this).find('td').each(function() {
                        let input = $(this).find('input, select, textarea');
                        if (input.length) {
                            // Add the input value to CSV, handle select and text areas as well
                            row.push('"' + input.val().replace(/"/g, '""') + '"'); // Escape quotes
                        } else {
                            // If there is no input, use the cell's text
                            row.push('"' + $(this).text().replace(/"/g, '""') +
                                '"'); // Escape quotes
                        }
                    });
                    csv.push(row.join(','));
                });

                // Manually add the extra row for total hours
                let totalHours = $(".all_employee_total_hours")
                    .html(); // Example value, replace with actual total if needed
                let totalRow = ['', '', '', '', '', '', '"Total Hours"' + ',' +
                    totalHours
                ]; // Adjust according to your column structure
                csv.push(totalRow.join(','));

                // Convert array to CSV string
                return csv.join('\n');
            }

            document.getElementById('exportButton').addEventListener('click', function() {
                // Get the selected month, customer name, and project name
                const selectedMonth = document.getElementById('globalSearch').value;


                // Generate CSV data from the table
                const csvData = exportToCSV();

                // Create a blob for download
                const blob = new Blob([csvData], {
                    type: 'text/csv;charset=utf-8;'
                });

                // Generate a dynamic filename
                const fileName =
                    `attendance_data_${selectedMonth}.csv`;

                // Create a download link
                const link = document.createElement('a');
                const url = URL.createObjectURL(blob);
                link.setAttribute('href', url);

                // Set the dynamic filename for the download
                link.setAttribute('download', fileName);

                link.style.visibility = 'hidden';
                document.body.appendChild(link);
                link.click(); // Trigger download
                document.body.removeChild(link); // Clean up
            });
        });
    </script>

    <script>
        $(document).ready(function() {
            // Trigger the submit button after the page has loaded
            $('#submitButton').trigger('click');
        });
    </script>

    <script>
        $(document).ready(function() {
            // Optional: Programmatically hide the modal for debugging
            $('#btnCancel,.close').on('click', function() {
                $('#importModal').modal('hide');
            });


            $('#importAttendances').on('submit', function(e) {
                e.preventDefault(); // Prevent the default form submission

                let selectedMonth = $('#globalSearch').val(); // Expected format: YYYY-MM
                let customerId = $('#customer_select').val();
                let projectId = $('#project_select').val();

                var formData = new FormData(this); // Create a FormData object from the form
                formData.append('csv_file', $('input[name="csv_file"]')[0].files[0]); // Add the CSV file
                formData.append('month', selectedMonth); // Add the selected month
                formData.append('customer_id', customerId); // Add the customer ID
                formData.append('project_id', projectId); // Add the project ID


                let importUrl = route('manage-attendances.import');
                $.ajax({
                    url: importUrl, // Replace with your server-side upload URL
                    type: 'POST',
                    data: formData,
                    processData: false, // Important for file uploads
                    contentType: false, // Important for file uploads
                    beforeSend: function() {
                        $('#btnImport').button('loading');

                    },
                    success: function(response) {
                        if (response.success) {
                            $('input[name="csv_file"]').val('');
                            $('#importModal').modal('hide');
                            displaySuccessMessage(response.message);
                            $('#submitButton').click();
                        }

                    },
                    error: function(xhr) {
                        // Handle error response
                        var errors = xhr.responseJSON.errors;
                        var errorHtml = '';
                        $.each(errors, function(key, error) {
                            errorHtml += '<p>' + error[0] + '</p>';
                        });

                    },
                    complete: function() {
                        // Optional: Hide loading spinner
                        $('#btnImport').button('reset');
                    }
                });
            });

        });
    </script>

    <script>
        $('#iqama_search').keyup(function() {
            var $this = $(this);
            clearTimeout($this.data('timeout'));
            $this.data('timeout', setTimeout(function() {
                $('#submitButton').click();
            }, 500)); // Adjust the timeout duration as needed
        });
    </script>
@endsection
