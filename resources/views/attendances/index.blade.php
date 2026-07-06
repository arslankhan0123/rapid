@extends('layouts.app')
@section('title')
    {{ __('messages.attendances.attendances') }}
@endsection
@section('page_css')
    <link href="{{ asset('assets/css/jquery.dataTables.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="{{ asset('assets/css/bs4-summernote/summernote-bs4.css') }}">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.3.6/css/buttons.dataTables.min.css">

    <style>
        #toggleButton {
            transition: background-color 0.1s ease, color .1s ease;
        }

        .nav-tabs {
            border-bottom: none;
            /* Remove the bottom border from the tabs */
        }

        .nav-tabs .nav-link {
            border: none;
            /* Remove border from each tab */
            border-radius: 0;
            /* Remove border radius for square tabs */
            margin-right: 0;
            /* Remove any right margin to align tabs */
            padding: 0.5rem 1rem;
            /* Adjust padding as needed */
        }

        .nav-tabs .nav-link.active {
            box-shadow: none;
            /* Remove the shadow from the active tab */
            border-bottom: 2px solid #007bff;
            /* Optional: Add a bottom border for the active tab */
            background: #007bff;
            color: white !important;
            border-radius: 5px;
        }
    </style>
@endsection
@section('content')
    <section class="section">
        <div class="section-header item-align-right">
            <h1>{{ __('messages.attendances.attendances') }}</h1>
            <div class="section-header-breadcrumb float-right">
                <div class="card-header-action mr-3 select2-mobile-margin">
                </div>
            </div>
            @can('import_attendances')
                <div class="float-right">
                    <button class="btn btn-primary form-btn" data-toggle="modal" data-target="#importModal">
                        {{ __('messages.attendances.import') }}</button>
                </div>
            @endcan

        </div>
        <div class="section-body">
            <div class="card">
                <div class="card-body">


                    <div class="row">
                        <div class="form-group col-sm-12 col-md-4">
                            {{ Form::label('employee_id', __('messages.customer.select_month') . ':') }}
                            <input type="month" id="globalSearch" class="form-control"
                                placeholder=" __('messages.customer.select_month')">
                        </div>
                        <div class="form-group col-sm-12  col-md-4">
                            {{ Form::label('employee_id', __('messages.customer.select_customer') . ':') }}
                            {{ Form::select('customer_id', $customers, null, ['class' => 'form-control', 'required', 'id' => 'customer_select', 'placeholder' => __('messages.customer.all')]) }}
                        </div>
                        <div class="form-group col-sm-12  col-md-4">
                            {{ Form::label('employee_id', __('messages.customer.select_project') . ':') }}
                            {{ Form::select('customer_id', $projects, null, ['class' => 'form-control', 'required', 'id' => 'project_select', 'placeholder' => __('messages.customer.all')]) }}
                        </div>
                    </div>

                </div>
            </div>
        </div>
        <div class="selection-body">
            <div class="card">
                <div class="card-body">
                    @if (session()->has('flash_notification'))
                        @foreach (session('flash_notification') as $message)
                            <div class="alert alert-{{ $message['level'] }}">
                                {{ $message['message'] }}
                            </div>
                        @endforeach
                    @endif
                    @include('attendances.table')
                </div>
            </div>
        </div>
        </div>



    </section>

    @include('attendances.templates.templates')
    @include('attendances.import')
    @include('attendances.edit_modal')
    @include('attendances.view_modal')
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


    <!-- Bootstrap JS (including Popper) -->
@endsection
@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('globalSearch').value =
                ''; // Clear the value to avoid showing the default month
        });
    </script>
    {{-- <script>
        $(document).ready(function() {
            // Get the current date
            const now = new Date();
            const year = now.getFullYear();
            const month = String(now.getMonth() + 1).padStart(2,
                '0'); // Months are 0-indexed, so add 1 and pad with zero if needed

            // Set the value of the input to the current month
            $('#globalSearch').val(`${year}-${month}`);
        });
    </script> --}}
    <script>
        let dailyCreate = route('attendances.store.daily');
        $(document).on('submit', '#add_daily_attendances', function(event) {
            event.preventDefault();
            processingBtn('#add_daily_attendances', '#btnSave', 'loading');
            $.ajax({
                url: dailyCreate,
                type: 'POST',
                data: $(this).serialize(),
                success: function(result) {
                    if (result.success) {
                        displaySuccessMessage(result.message);
                        $('#add_daily_attendances')[0].reset();
                        $('#employee_select').val(null).trigger('change');
                        tbl.ajax.reload(null, false);
                    }
                },
                error: function(result) {
                    displayErrorMessage(result.responseJSON.message);
                },
                complete: function() {
                    processingBtn('#add_daily_attendances', '#btnSave');
                },
            });
        });


        $(document).ready(function() {
            $('#employee_select').select2({
                width: '100%', // Set the width of the select element
                placeholder: '{{ __('messages.attendances.select_iqama') }}', // Placeholder text
                allowClear: true // Allow clearing the selection
            });
            $('#employee_select_monthly').select2({
                width: '100%', // Set the width of the select element
                placeholder: '{{ __('messages.awards.select_employee') }}', // Placeholder text
                allowClear: true // Allow clearing the selection
            });
            $('#customer_select').select2({
                width: '100%', // Set the width of the select element
                allowClear: true // Allow clearing the selection
            });
            $('#project_select').select2({
                width: '100%', // Set the width of the select element
                allowClear: true // Allow clearing the selection
            });
            // Cancel button
        });
    </script>

    <script>
        // Set default value for the month input


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
                url: route('attendances.index'),
                data: function(d) {
                    d.month = $('#globalSearch').val(); // Send the selected month to the server
                    d.customer_id = $('#customer_select').val(); // Send the selected customer ID
                    d.project_id = $('#project_select').val(); // Send the selected project ID
                }
            },
            columnDefs: [{
                    targets: 0, // Index of the iqama_no column
                    searchable: true,
                    render: function(data, type, row) {
                        return data;
                    }
                },
                {
                    targets: 1, // Index of the employee.name column
                    searchable: true,
                    render: function(data, type, row) {
                        return data; // Render the data as is
                    }
                }
            ],
            columns: [{
                    data: function(row) {
                        let iqamaNo = row.iqma_employee && row.iqma_employee.iqama_no ? row.iqma_employee
                            .iqama_no : '';
                        let element = document.createElement('textarea');
                        element.innerHTML = iqamaNo;
                        return element.value;
                    },
                    name: 'employee.iqama_no',
                    width: '10%',
                    orderable: false
                },
                {
                    data: function(row) {
                        let baseUrl = `{{ asset('uploads/public/employee_images/') }}`;
                        let imageHtml = row.iqma_employee && row.iqma_employee.image ?
                            `<img src="${baseUrl}/${row.iqma_employee.image}" style="width: 35px; height: 35px; border-radius: 5%; margin-right: 5px;">` :
                            '';
                        let name = row.iqma_employee && row.iqma_employee.name ? row.iqma_employee.name :
                            '';
                        let element = document.createElement('textarea');
                        element.innerHTML = name;
                        return `${imageHtml}${element.value}`;
                    },
                    name: 'employee.name',
                    width: '20%',
                    orderable: false
                },
                {
                    data: function(row) {
                        if (row.customer && row.customer.company_name) {
                            return row.customer.company_name;
                        }
                        return '';
                    },
                    name: 'customer_id',
                    width: '10%',
                    orderable: false
                },
                {
                    data: function(row) {
                        if (row.project && row.project.project_name) {
                            return row.project.project_name;
                        }
                        return '';
                    },
                    name: 'project_id',
                    width: '15%'
                },

                {
                    data: function(row) {
                        if (row.hours) {
                            return row.hours;
                        }
                        return '';
                    },
                    name: 'total_hours',
                    width: '10%',
                    orderable: false
                },
                {
                    data: function(row) {
                        let date = new Date(row.date);
                        let day = ('0' + date.getDate()).slice(-
                            2); // Get the day and pad with zero if needed
                        let month = ('0' + (date.getMonth() + 1)).slice(-
                            2); // Get the month (0-indexed) and pad with zero
                        let year = date.getFullYear(); // Get the full year
                        return `${day}-${month}-${year}`; // Format as d-m-Y
                    },
                    name: 'date',
                    width: '10%',
                    orderable: false
                }, {
                    data: function(row) {
                        return renderActionButtons(row.id);
                    },
                    name: 'id',
                    width: '200px',
                    orderable: false
                }
            ],
            responsive: true, // Enable responsive features,

            dom: "Bfrtip", // Ensure Buttons (B) is part of the DOM structure
            buttons: [
                @can('import_attendances')
                    {
                        extend: 'csvHtml5',
                        text: '<i class="fas fa-file-csv"></i> Export CSV',
                        className: 'btn btn-sm',
                        filename: function() {
                            return 'timesheet_export_' + new Date().toISOString().slice(0,
                                10); // Example: attendances_export_2024-09-01
                        },
                        exportOptions: {
                            // Exclude the last column (action column)
                            columns: function(idx, data, node) {
                                return idx !== tbl.settings().init().columns.length - 1;
                            },
                            format: {
                                header: function(d, columnIdx) {
                                    // Preserve column headers
                                    return d;
                                }
                            }
                        },
                        customize: function(csv) {
                            // Remove any extra rows (like company name rows) from the CSV output
                            const lines = csv.split('\n');
                            const nonEmptyLines = lines.filter(line => line.trim() !== '');
                            return nonEmptyLines.join('\n');
                        }
                    }, {
                        extend: 'excelHtml5',
                        text: '<i class="fas fa-file-excel"></i> Export Excel',
                        className: 'btn btn-sm',
                        filename: function() {
                            return 'timesheet_export_' + new Date().toISOString().slice(0,
                                10); // Example: attendances_export_2024-09-01
                        },
                        exportOptions: {
                            // Exclude the last column (action column)
                            columns: function(idx, data, node) {
                                return idx !== tbl.settings().init().columns.length - 1;
                            },
                            format: {
                                header: function(d, columnIdx) {
                                    // Preserve column headers
                                    return d;
                                }
                            }
                        },
                        customize: function(xlsx) {
                            var sheet = xlsx.xl.worksheets['sheet1.xml'];
                            var sheetData = sheet.getElementsByTagName('sheetData')[0];
                            var rows = sheetData.getElementsByTagName('row');

                            // Remove the first row if it is an unwanted header
                            if (rows.length > 0 && rows[0].getElementsByTagName('c').length === 0) {
                                sheetData.removeChild(rows[0]);
                            }
                        }
                    }, {
                        extend: 'pdfHtml5',
                        text: '<i class="fas fa-file-pdf"></i> Export PDF',
                        className: 'btn btn-sm', // Styled button
                        orientation: 'portrait',
                        pageSize: 'A4',
                        filename: function() {
                            return 'timesheet_export_' + new Date().toISOString().slice(0,
                                10); // Example: attendances_export_2024-09-01
                        },
                        exportOptions: {
                            // Exclude the last column (action column)
                            columns: function(idx, data, node) {
                                return idx !== tbl.settings().init().columns.length - 1;
                            },
                            format: {
                                header: function(d, columnIdx) {
                                    // Preserve column headers
                                    return d;
                                }
                            }
                        },
                        customize: function(doc) {
                            // Optional customization of the PDF
                            doc.content[1].table.widths =
                                Array(doc.content[1].table.body[0].length + 1).join('*').split('');

                            // Remove the title and any extra headers
                            if (doc.content.length > 0 && doc.content[0].text === 'Attendances') {
                                doc.content.shift(); // Remove the title if it exists
                            }

                            // Ensure no extra headers are included
                            if (doc.content.length > 0 && doc.content[0].text === 'InfyCRM') {
                                doc.content.shift(); // Remove any company name if it exists
                            }
                        }
                    }
                @endcan
            ],



            lengthMenu: [5, 10, 25, 50, 75, 100],
            pageLength: 10

        });

        $('#globalSearch, #customer_select, #project_select').on('change', function() {
            tbl.ajax.reload();
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
            updateItem: "{{ auth()->user()->can('update_attendances') ? 'true' : 'false' }}",
            deleteItem: "{{ auth()->user()->can('delete_attendances') ? 'true' : 'false' }}",
            viewItem: "{{ auth()->user()->can('create_attendances') ? 'true' : 'false' }}"
        };

        // Function to render action buttons based on permissions
        function renderActionButtons(id) {
            let buttons = '';
            if (permissions.updateItem === 'true') {
                let editUrl = `{{ route('attendances.edit', ':id') }}`;
                editUrl = editUrl.replace(':id', id);
                buttons += `
                <a title="${messages.edit}"  class="btn btn-warning action-btn has-icon edit-btn" data-id="${id}" style="float:right;margin:2px;">
                    <i class="fa fa-edit"></i>
                </a>
            `;
            }
            if (permissions.viewItem === 'true') {
                let viewUrl = `{{ route('attendances.view', ':id') }}`;
                viewUrl = viewUrl.replace(':id', id);
                buttons += `
                <a title="${messages.view}" href="${viewUrl}" class="btn btn-info action-btn has-icon view-btn" data-id="${id}" style="float:right;margin:2px;">
                    <i class="fa fa-eye"></i>
                </a>
            `;
            }

            if (permissions.deleteItem === 'true') {
                buttons += `
                <a title="${messages.delete}"  class="btn btn-danger action-btn has-icon delete-btn" data-id="${id}" style="float:right;margin:2px;">
                    <i class="fa fa-trash"></i>
                </a>
            `;
            }

            return buttons;
        }

        $(document).on('click', '.view-btn', function(event) {
            event.preventDefault(); // Prevent the default action

            let id = $(this).data('id'); // Get the ID of the attendance record
            let viewUrl = `{{ route('attendances.view', ':id') }}`.replace(':id', id);
            $('#viewModal').modal('show');
            $.ajax({
                url: viewUrl,
                method: 'GET',
                success: function(response) {


                    function formatTime(time) {
                        if (time) {
                            let [hours, minutes] = time.split(':').map(Number);
                            let period = hours >= 12 ? 'PM' : 'AM';
                            hours = hours % 12 ||
                                12; // Convert hour '0' to '12' and keep other hours as is
                            minutes = minutes < 10 ? '0' + minutes :
                                minutes; // Ensure minutes have two digits
                            return `${hours}:${minutes} ${period}`;
                        }
                        return '';
                    }



                    function formatDate(date) {
                        if (date) {
                            let d = new Date(date);
                            let day = ('0' + d.getDate()).slice(-2);
                            let month = ('0' + (d.getMonth() + 1)).slice(-2);
                            let year = d.getFullYear();
                            return `${day}-${month}-${year}`;
                        }
                        return '';
                    }


                    // Populate modal fields with data
                    $('#viewIqamaNo').text(response.iqma_employee && response.iqma_employee.iqama_no ||
                        '');
                    $('#viewEmployeeName').text(response.iqma_employee && response.iqma_employee.name ||
                        '');
                    $('#viewDesignationName').text(response.iqma_employee && response.iqma_employee
                        .designation && response.iqma_employee.designation.name || '');
                    $('#viewDepartment').text(response.iqma_employee && response.iqma_employee
                        .department && response.iqma_employee.department.name || '');
                    $('#viewSubDepartment').text(response.iqma_employee && response.iqma_employee
                        .sub_department && response.iqma_employee.sub_department.name || '');


                    if (response.iqma_employee && response.iqma_employee
                        .shifts && response.iqma_employee.shifts.shift_start_time) {
                        $('#viewTimeIn').text(formatTime(response.iqma_employee.shifts
                            .shift_start_time));
                    }

                    // Example usage:



                    $('#viewTimeOut').text(formatTime(response.iqma_employee && response.iqma_employee
                        .shifts && response.iqma_employee.shifts.shift_end_time));
                    $('#viewTotalHours').text(response.hours || '');
                    $('#viewDate').text(formatDate(response.date));
                    console.log(response);
                },
                error: function(xhr) {
                    console.error('An error occurred while fetching attendance details:', xhr);
                }
            });
        });


        $(document).on('click', '.edit-btn', function(event) {
            let attendanceId = $(event.currentTarget).data('id');
            const url = route('attendances.edit', attendanceId); // Use 'attendance' parameter
            $('#editModal').modal('show');
            $.ajax({
                url: url,
                method: 'GET',
                success: function(response) {

                    // Populate modal fields with data
                    $('#date').val(response.date);
                    $('#time_in_edit').val(response.time_in);
                    $('#time_out_edit').val(response.time_out);
                    $('#editId').val(response.id);
                },
                error: function(xhr) {
                    console.error('An error occurred:', xhr);
                }
            });
        });


        $('#updateAttendanceForm').on('submit', function(event) {
            event.preventDefault(); // Prevent the default form submission
            let attendanceId = $('#editId').val(); // Get the attendance ID from a hidden input field
            processingBtn('#editModal', '#btnSave', 'loading');
            $.ajax({
                url: route('attendances.update', attendanceId), // Form action URL
                method: 'PUT', // Or 'POST' depending on your needs
                data: $(this).serialize(), // Serialize the form data
                success: function(response) {
                    console.log("Succcess", response);
                    if (response.success) {
                        $('#designationTable').DataTable().ajax.reload(null, false);
                        displaySuccessMessage(response.message);
                        $('#editModal').modal('hide');
                    }
                    processingBtn('#editModal', '#btnSave');
                },
                error: function(xhr) {
                    displayErrorMessage(xhr.responseJSON.message);
                    processingBtn('#editModal', '#btnSave');
                }
            });
        });

        $(document).on('click', '.delete-btn', function(event) {
            let attendanceId = $(event.currentTarget).data('id');
            deleteItem(route('attendances.destroy', attendanceId), '#designationTable',
                '{{ __('messages.attendances.name') }}');
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

                var formData = new FormData(this); // Create a FormData object from the form
                let importUrl = route('attendances.import');
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

                            var txt = '';
                            if (response.inserted) {
                                txt += "{{ __('messages.attendances.inserted') }} " + response
                                    .inserted + " rows";
                            }
                            // if (response.errors.length > 0) {
                            //     txt += "<br>{{ __('messages.attendances.failed') }} " +
                            //         response.errors.length + " rows | Error in line  " +
                            //         response.errors;
                            // }
                            tbl.ajax.reload(null, false);
                            $('#importModal').modal('hide');
                            displaySuccessMessage(txt);
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
        $(document).ready(function() {
            // Employee data stored in a JavaScript object
            var employees = @json($employees);

            $('#employee_select').change(function() {
                var selectedEmployeeId = $(this).val();
                var employee = employees.find(emp => emp.id == selectedEmployeeId);

                if (employee) {
                    // Update image preview
                    if (employee.image) {
                        var imageUrl = "{{ asset('uploads/public/employee_images/') }}/" + employee.image;
                        $('#employeeImage').attr('src', imageUrl).show();
                    } else {
                        $('#employeeImage').hide();
                    }
                    // Update employee details
                    $('#employeeName').text('{{ __('messages.allowances.employee_name') }} : ' + employee
                        .name);
                    $('#employeeDepartment').text(
                        '{{ __('messages.allowances.employee_department') }} : ' + employee
                        .department.name);
                    $('#employeeSubDepartment').text(
                        '{{ __('messages.allowances.employee_sub_department') }} : ' + employee
                        .sub_department.name);
                    $('#employeeDesignation').text(
                        '{{ __('messages.allowances.employee_designation') }} : ' + (
                            employee.designation ? employee
                            .designation.name : 'N/A'));
                } else {
                    $('#employeeImage').hide();
                    $('#employeeName').text('');
                    $('#employeeSubDepartment').text('');
                    $('#employeeDepartment').text('');
                    $('#employeeDesignation').text('');
                }
            });

        });
    </script>

    <script>
        $(document).ready(function() {
            $('#toggleButton').click(function() {
                $('#collapseForm').on('shown.bs.collapse', function() {
                    $('#toggleButton')
                        .text('Close Form') // Change the text
                        .removeClass('btn-primary') // Remove the primary button style
                        .addClass('btn-danger'); // Add the danger button style
                });

                $('#collapseForm').on('hidden.bs.collapse', function() {
                    $('#toggleButton')
                        .text(' Add Attendance') // Change the text back
                        .removeClass('btn-danger') // Remove the danger button style
                        .addClass('btn-primary'); // Add the primary button style
                });
            });
        });
    </script>
@endsection
