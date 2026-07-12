@extends('layouts.app')
@section('title')
    Job Skills
@endsection
@section('page_css')
    <link href="{{ asset('assets/css/jquery.dataTables.min.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="{{ asset('assets/css/select2.min.css') }}">
@endsection
@section('content')
    <section class="section">
        <div class="section-header">
            <h1>Job Skills</h1>
            <div class="section-header-breadcrumb">
                @canany(['create_job_skills', 'manage_job_skills'])
                    <a href="{{ route('job-skills.create') }}" class="btn btn-primary form-btn">
                        Add Job Skill <i class="fas fa-plus"></i>
                    </a>
                @endcanany
            </div>
        </div>
        <div class="section-body">
            <div class="card">
                <div class="card-body">
                    @include('job_skills.table')
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
        let tableName = '#jobSkillsTable';
        $(tableName).DataTable({
            processing: true,
            serverSide: true,
            'order': [[1, 'asc']],
            ajax: {
                url: "{{ route('job-skills.index') }}",
            },
            columnDefs: [
                {
                    'targets': [6],
                    'orderable': false,
                    'className': 'text-center',
                    'width': '8%',
                },
                {
                    'targets': [5],
                    'orderable': true,
                    'className': 'text-center',
                    'width': '8%',
                }
            ],
            columns: [
                {
                    data: 'skill_code',
                    name: 'skill_code'
                },
                {
                    data: 'name',
                    name: 'name'
                },
                {
                    data: function(row) {
                        return row.category ? row.category.name : 'N/A';
                    },
                    name: 'category_id'
                },
                {
                    data: 'level',
                    name: 'level'
                },
                {
                    data: function (row) {
                        if(row.creator) {
                            return row.creator.first_name + ' ' + row.creator.last_name;
                        }
                        return 'N/A';
                    },
                    name: 'created_by'
                },
                {
                    data: function(row) {
                        let checked = row.is_active ? 'checked' : '';
                        return `<label class="custom-switch pl-0">
                                    <input type="checkbox" name="is_active" value="${row.is_active}" class="custom-switch-input is-active-skill" data-id="${row.id}" ${checked} disabled>
                                    <span class="custom-switch-indicator"></span>
                                </label>`;
                    },
                    name: 'is_active'
                },
                {
                    data: function(row) {
                        let viewUrl = route('job-skills.view', row.id);
                        let editUrl = route('job-skills.edit', row.id);
                        let actions = '';
                        
                        @canany(['view_job_skills', 'manage_job_skills'])
                            actions += `<a title="View" class="btn btn-warning action-btn has-icon" href="${viewUrl}">
                                            <i class="fa fa-eye"></i>
                                        </a>`;
                        @endcanany
                        
                        @canany(['update_job_skills', 'manage_job_skills'])
                            actions += `<a title="Edit" class="btn btn-primary action-btn edit-btn has-icon" href="${editUrl}">
                                            <i class="fa fa-edit"></i>
                                        </a>`;
                        @endcanany
                        
                        @canany(['delete_job_skills', 'manage_job_skills'])
                            actions += `<button title="Delete" class="btn btn-danger action-btn delete-btn has-icon" data-id="${row.id}">
                                            <i class="fa fa-trash"></i>
                                        </button>`;
                        @endcanany
                        
                        return actions;
                    },
                    name: 'id'
                }
            ]
        });

        $(document).on('click', '.delete-btn', function(event) {
            let id = $(this).attr('data-id');
            deleteItem(route('job-skills.destroy', id), tableName, 'Job Skill');
        });
    </script>
@endsection
