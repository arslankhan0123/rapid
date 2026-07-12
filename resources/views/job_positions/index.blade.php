@extends('layouts.app')
@section('title')
    Job Positions
@endsection
@section('page_css')
    <link href="{{ asset('assets/css/jquery.dataTables.min.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="{{ asset('assets/css/select2.min.css') }}">
@endsection
@section('content')
    <section class="section">
        <div class="section-header">
            <h1>Job Positions</h1>
            <div class="section-header-breadcrumb">
                @canany(['create_job_positions', 'manage_job_positions'])
                    <a href="{{ route('job-positions.create') }}" class="btn btn-primary form-btn">
                        Add Job Position <i class="fas fa-plus"></i>
                    </a>
                @endcanany
            </div>
        </div>
        <div class="section-body">
            <div class="card">
                <div class="card-body">
                    @include('job_positions.table')
                </div>
            </div>
        </div>
    </section>
@endsection
@section('page_scripts')
    <script src="{{ asset('assets/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ mix('assets/js/custom/custom-datatable.js') }}"></script>
    <script src="{{ mix('assets/js/select2.min.js') }}"></script>
@endsection
@section('scripts')
    <script>
        let tableName = '#jobPositionsTable';
        $(tableName).DataTable({
            processing: true,
            serverSide: true,
            'order': [[1, 'asc']],
            ajax: {
                url: "{{ route('job-positions.index') }}",
            },
            columnDefs: [
                {
                    'targets': [6],
                    'orderable': false,
                    'className': 'text-center',
                    'width': '15%',
                }
            ],
            columns: [
                {
                    data: 'position_code',
                    name: 'position_code'
                },
                {
                    data: 'title',
                    name: 'title'
                },
                {
                    data: function(row) {
                        return row.category ? row.category.name : 'N/A';
                    },
                    name: 'category_id'
                },
                {
                    data: function(row) {
                        return row.department ? row.department.name : 'N/A';
                    },
                    name: 'department_id'
                },
                {
                    data: 'employment_type',
                    name: 'employment_type'
                },
                {
                    data: 'status',
                    name: 'status'
                },
                {
                    data: function(row) {
                        let viewUrl = route('job-positions.view', row.id);
                        let editUrl = route('job-positions.edit', row.id);
                        let actions = '';
                        
                        @canany(['delete_job_positions', 'manage_job_positions'])
                            actions += `<button title="Delete" class="btn btn-danger action-btn delete-btn has-icon" data-id="${row.id}" style="float:right;margin:2px;">
                                            <i class="fa fa-trash"></i>
                                        </button>`;
                        @endcanany

                        @canany(['view_job_positions', 'manage_job_positions'])
                            actions += `<a title="View" class="btn btn-info action-btn has-icon view-btn" href="${viewUrl}" style="float:right;margin:2px;">
                                            <i class="fa fa-eye"></i>
                                        </a>`;
                        @endcanany
                        
                        @canany(['update_job_positions', 'manage_job_positions'])
                            actions += `<a title="Edit" class="btn btn-warning action-btn edit-btn has-icon" href="${editUrl}" style="float:right;margin:2px;">
                                            <i class="fa fa-edit"></i>
                                        </a>`;
                        @endcanany
                        
                        return actions;
                    },
                    name: 'id'
                }
            ]
        });

        $(document).on('click', '.delete-btn', function(event) {
            let id = $(this).attr('data-id');
            deleteItem(route('job-positions.destroy', id), tableName, 'Job Position');
        });
    </script>
@endsection
