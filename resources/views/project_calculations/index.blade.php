@extends('layouts.app')
@section('title')
    {{ __('messages.project_calculations.project_calculations') }}
@endsection
@section('page_css')
    <link href="{{ asset('assets/css/jquery.dataTables.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css" rel="stylesheet" />
@endsection
@section('content')
    <section class="section">
        <div class="section-header">
            <h1>{{ __('messages.project_calculations.project_calculations') }}</h1>
            <div class="section-header-breadcrumb">
                <a href="{{ route('project-calculations.create') }}" class="btn btn-primary form-btn">
                    {{ __('messages.project_calculations.add') }}
                </a>
            </div>
        </div>
        <div class="section-body">
            <div class="card">
                <div class="card-body">
                    @include('project_calculations.table')
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
        let projectCalculationsUrl = "{{ route('project-calculations.index') }}";
        let tbl = $('#projectCalculationsTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: projectCalculationsUrl,
            dom: 'Blfrtip',
            buttons: [
                'csvHtml5',
                'pdfHtml5'
            ],
            lengthMenu: [
                [10, 25, 50, 100, -1],
                [10, 25, 50, 100, "All"]
            ],
            columns: [{
                    data: 'code',
                    name: 'code'
                },
                {
                    data: 'name',
                    name: 'name'
                },
                {
                    data: 'created_at',
                    name: 'created_at',
                    render: function(data) {
                        return moment(data).format('DD MMM, YYYY');
                    }
                },
                {
                    data: function(row) {
                        return `
                <a href="{{ route('project-calculations.show', ':id') }}" class="btn btn-info btn-sm" title="View">
                    <i class="fas fa-eye"></i>
                </a>
                <a href="{{ route('project-calculations.edit', ':id') }}" class="btn btn-warning btn-sm" title="Edit">
                    <i class="fas fa-edit"></i>
                </a>
                <button class="btn btn-danger btn-sm delete-btn" data-id="${row.id}" title="Delete">
                    <i class="fas fa-trash"></i>
                </button>
            `.replace(/:id/g, row.id);
                    },
                    orderable: false,
                    searchable: false
                }
            ]
        });

        $(document).on('click', '.delete-btn', function() {
            let projectId = $(this).data('id');
            deleteItem('{{ route('project-calculations.destroy', '') }}/' + projectId, '#projectCalculationsTable',
                'Project Calculation');
        });
    </script>
@endsection
