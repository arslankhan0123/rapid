@extends('layouts.app')
@section('title')
    {{ __('messages.manage_attendances.name') }}
@endsection
@section('page_css')
    <link href="{{ asset('assets/css/jquery.dataTables.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="{{ asset('assets/css/bs4-summernote/summernote-bs4.css') }}">
@endsection
@section('content')
    <section class="section">
        <div class="section-header item-align-right">
            <h1>{{ __('messages.manage_attendances.name') }}</h1>
            <div class="section-header-breadcrumb float-right">
                <div class="card-header-action mr-3 select2-mobile-margin">
                </div>
            </div>

        </div>
        <div class="section-body">
            <div class="card">
                <div class="card-body">


                    <div class="row">
                        <div class="form-group col-sm-12 col-md-3">
                            {{ Form::label('employee_id', __('messages.customer.select_month') . ':') }}
                            <input type="month" id="globalSearch" class="form-control"
                                value="{{ \Carbon\Carbon::now()->format('Y-m') }}"
                                placeholder=" __('messages.customer.select_month')">
                        </div>

                        <div class="form-group col-sm-12 col-md-3">
                            {{ Form::label('employee_id', __('messages.customer.select_customer') . ':') }}
                            {{ Form::select('customer_id', $customers, null, ['class' => 'form-control', 'required', 'id' => 'customer_select']) }}
                        </div>

                        <div class="form-group col-sm-12 col-md-3">
                            {{ Form::label('employee_id', __('messages.customer.select_project') . ':') }}
                            {{ Form::select('project_id', [], null, ['class' => 'form-control', 'required', 'id' => 'project_select']) }}
                        </div>

                        <div class="col-sm-12 col-md-2 mt-4" style="padding:5px;">


                            <button type="button" id="submitButton" class="btn btn-primary">
                                {{ __('messages.common.search') }}
                            </button>
                        </div>

                    </div>
                    <div class="row">
                        <div class="form-group col-md-6">

                        </div>

                    </div>


                </div>
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

                    @include('manage_attendances.table')
                </div>
            </div>
        </div>
    </section>
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
        function generateDayColumns() {
            const columns = [];
            const currentDate = new Date($('#globalSearch').val());
            let thead = '';

            const currentMonth = currentDate.getMonth();
            const currentYear = currentDate.getFullYear();
          let  daysInMonth = new Date(currentYear, currentMonth + 1, 0).getDate(); // Get the number of days in the month
            const hours = 8;

            const holiday = "none"; // Set to the day name (e.g., "Friday") or "none" if there is no holiday

            thead = $('#manageAttendancesTable thead tr'); // Select the header row

            for (let day = 1; day <= daysInMonth; day++) {
                console.log(day);
                const date = new Date(currentYear, currentMonth, day);
                const dayNumber = date.getDate();
                const dayName = date.toLocaleString('en-us', {
                    weekday: 'long'
                }); // Full day name (e.g., Monday, Tuesday)

                // Skip the day if it's the holiday and holiday is not "none"
                if (holiday !== "none" && dayName === holiday) {
                    continue; // Skip this iteration
                }

                // Append header <th> element for non-holiday days
                thead.append(`<th>${dayName} ${dayNumber}</th>`);

                // Add a column for each day except the holiday
                columns.push({
                    data: function(row) {
                        // Generate dynamic ID and name for each input based on employee_id and date
                        const inputId = `input_id_${dayNumber}_${row.id}`;
                        const inputName = `attendance[${row.id}][${dayNumber}]`; // PHP-friendly name format
                        return `<input type="number" id="${inputId}" name="${inputName}" class="form-control" style="width:70px;" value="${hours}">`;
                    },
                    title: ` ${dayNumber} ${dayName}`,
                    orderable: false,
                    searchable: false,
                    width: '50px'
                });

            }
            return columns;
        }
    </script>
    <script>
        const currentMonth = new Date().toISOString().slice(0, 7);
        $('#globalSearch').val(currentMonth);

        $('#submitButton').on('click', function() {
            let dayColumns = generateDayColumns();

            $('#manageAttendancesTable').DataTable().clear().destroy();

            let tbl = $('#manageAttendancesTable').DataTable({
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
                    url: route('manage_attendances.index'),
                    data: function(d) {
                        d.month = $('#globalSearch').val(); // Send the selected month to the server
                        d.customer_id = $('#customer_select').val(); // Send the selected customer ID
                        d.project_id = $('#project_select').val(); // Send the selected project ID
                    }
                },
                columns: [

                {
                        data: function(row, type, set, meta) {
                            return meta.row + 1; // Serial number
                        },
                        name: 'serial_number',
                        searchable: false,
                        orderable: false,
                        width: '5%'
                    },
                    {
                        data: function(row) {
                            return row.iqama_no ?? '';
                        },
                        name: 'iqama_no',
                        width: '15%',
                        orderable: false,
                        searchable: false,
                    },
                    {
                        data: function(row) {
                            return row.name ?? '';
                        },
                        name: 'name',
                        width: '15%',

                    },
                    {
                        data: function(row) {
                            if (row.designation) {
                                return row.designation.name ?? '';
                            }
                            return '';
                        },
                        name: 'designation_name',
                        width: '15%',
                        orderable: false,
                        searchable: false,
                    },
                    {
                        data: function(row) {
                            if (row.designation) {
                                return row.designation.name ?? '';
                            }
                            return '';
                        },
                        name: 'designation_name',
                        width: '15%',
                        orderable: false,
                        searchable: false,
                    },
                    {
                        data: function(row) {
                            if (row.designation) {
                                return row.designation.name ?? '';
                            }
                            return '';
                        },
                        name: 'designation_name',
                        width: '15%',
                        orderable: false,
                        searchable: false,
                    },
                    ...dayColumns // Inject the dynamic columns here
                ],
                responsive: true,
                searching: false,
                deferLoading: 0 // Prevent automatic initial load
            });
            tbl.ajax.reload(); // Assuming tbl is your DataTable instance

        });
    </script>

    <script>
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
@endsection
