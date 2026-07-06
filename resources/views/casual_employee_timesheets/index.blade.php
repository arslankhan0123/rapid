@extends('layouts.app')
@section('title')
    {{ __('Casual Employee Timesheets') }}
@endsection
@section('page_css')
    <link href="{{ asset('assets/css/jquery.dataTables.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css" rel="stylesheet" />
@endsection
@section('content')
    <section class="section">
        <div class="section-header mobile-sec-header">
            <h1>{{ __('Casual Employee Timesheets') }}</h1>

            <div class="section-header-breadcrumb float-right">
                <!-- Client Filter -->
                <div class="card-header-action mr-3" style="width:180px;">
                    <label>{{ __('Client') }}</label>
                    <select id="filter-client" class="form-control">
                        <option value="">{{ __('All Clients') }}</option>
                        @foreach ($clients as $client)
                            <option value="{{ $client }}">{{ $client }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Project Filter -->
                <div class="card-header-action mr-3" style="width:180px;">
                    <label>{{ __('Project') }}</label>
                    <select id="filter-project" class="form-control">
                        <option value="">{{ __('All Projects') }}</option>
                        @foreach ($projects as $project)
                            <option value="{{ $project }}">{{ $project }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Month Filter -->
                <div class="card-header-action mr-3" style="width:180px;">
                    <label>{{ __('Month') }}</label>
                    <input type="month" id="filter-month" class="form-control" />
                </div>
            </div>

            <!-- Add Timesheet Button -->
            <div class="float-right">
                <a href="{{ route('casual-employee-timesheets.create') }}" class="btn btn-primary form-btn">
                    {{ __('Add Timesheet') }}
                </a>
            </div>
        </div>

        <div class="section-body">
            <div class="card">
                <div class="card-body">
                    @include('casual_employee_timesheets.table')
                </div>
            </div>
        </div>
    </section>
@endsection
@section('page_scripts')
    <script src="{{ asset('assets/js/jquery.dataTables.min.js') }}"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
    <script src="{{ mix('assets/js/custom/custom-datatable.js') }}"></script>
@endsection
@section('scripts')
    <script>
        let timesheetsUrl = "{{ route('casual-employee-timesheets.index') }}";

        $('#filter-client, #filter-project, #filter-month').on('change', function() {
            tbl.ajax.reload(); // automatically reload DataTable with selected filters
        });
        let tbl = $('#casualEmployeeTimesheetsTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: timesheetsUrl,
                data: function(d) {
                    d.client_name = $('#filter-client').val();
                    d.project_name = $('#filter-project').val();
                    d.month = $('#filter-month').val();
                }
            },
            dom: 'Blfrtip',
            buttons: ['csvHtml5', 'pdfHtml5'],
            lengthMenu: [
                [10, 25, 50, 100, -1],
                [10, 25, 50, 100, "All"]
            ],
            columns: [{
                    data: 'batch_id',
                    name: 'batch_id',
                    visible: false,
                    searchable: false
                },
                {
                    data: 'date',
                    name: 'date',
                    render: data => data ? moment(data).format('DD MMM, YYYY') : 'N/A'
                },
                {
                    data: 'type',
                    name: 'type',
                    render: data => `<span class="badge badge-info">${data}</span>`
                },
                {
                    data: 'client_name',
                    name: 'client_name',
                    defaultContent: 'N/A'
                },
                {
                    data: 'project_name',
                    name: 'project_name',
                    defaultContent: 'N/A'
                },
                {
                    data: 'employee_count',
                    name: 'employee_count',
                    render: data => data + ' Employees'
                },
                {
                    data: 'total_amount',
                    name: 'total_amount',
                    render: data => 'SAR ' + parseFloat(data).toFixed(2)
                },
                {
                    data: 'created_at',
                    name: 'created_at',
                    render: data => moment(data).format('DD MMM, YYYY')
                },
                {
                    data: function(row) {
                        let viewUrl = "{{ route('casual-employee-timesheets.show', ':id') }}".replace(
                            ':id', row.batch_id);
                        let editUrl = "{{ route('casual-employee-timesheets.edit', ':id') }}".replace(
                            ':id', row.batch_id);
                        return `
                    <a href="${viewUrl}" class="btn btn-info btn-sm"><i class="fas fa-eye"></i></a>
                    <a href="${editUrl}" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i></a>
                    <button class="btn btn-danger btn-sm delete-btn" data-id="${row.batch_id}">
                        <i class="fas fa-trash"></i>
                    </button>
                `;
                    },
                    orderable: false,
                    searchable: false
                }
            ],
            columnDefs: [{
                orderable: false,
                targets: [8]
            }]
        });

        // Auto reload table when filter changes
        $('#filter-client, #filter-project, #filter-month').on('change', function() {
            tbl.ajax.reload();
        });


        $(document).on('click', '.delete-btn', function() {
            let batchId = $(this).data('id');
            deleteItem('{{ route('casual-employee-timesheets.destroy', '') }}/' + batchId,
                '#casualEmployeeTimesheetsTable', 'Timesheet Batch');
        });
    </script>
@endsection
