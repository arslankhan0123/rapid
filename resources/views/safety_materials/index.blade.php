@extends('layouts.app')

@section('title')
    {{ __('messages.safety_materials.safety_materials') }}
@endsection

@section('page_css')
    <link href="{{ asset('assets/css/jquery.dataTables.min.css') }}" rel="stylesheet" />
    <link href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css" rel="stylesheet" />
    <link href="{{ asset('assets/css/select2.min.css') }}" rel="stylesheet" />

    <style>
        /* Right align numeric columns */
        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }
    </style>
@endsection

@section('content')
    <section class="section">
        <div class="section-header">
            <h1>{{ __('messages.safety_materials.safety_materials') }}</h1>
            <div class="section-header-breadcrumb">
                <a href="{{ route('safety-materials.create') }}" class="btn btn-primary">
                    {{ __('messages.safety_materials.add') }}
                </a>
            </div>
        </div>

        <div class="section-body">
            <div class="card">
                <div class="card-body">

                    <!-- Employee Filter -->
                    {{-- <div class="mb-3">
                        <select id="employee_filter" class="form-control w-25 d-inline-block">
                            <option value="">{{ __('messages.safety_materials.all_employees') }}</option>
                            @foreach ($employees as $id => $name)
                                <option value="{{ $id }}">{{ $name }}</option>
                            @endforeach
                        </select>
                        <button id="clear_filter"
                            class="btn btn-primary btn-sm ml-2">{{ __('messages.safety_materials.clear') }}</button>
                    </div> --}}

                    <!-- Employee Filter -->
                    <div class="mb-3">
                        <label for="employee_filter">{{ __('messages.safety_materials.employee') }}</label>
                        <select id="employee_filter" class="form-control select2 w-50" style="width:300px;">
                            <option value="">{{ __('messages.safety_materials.all_employees') }}</option>
                            @foreach ($employees as $id => $name)
                                <option value="{{ $id }}">{{ $name }}</option>
                            @endforeach
                        </select>
                        {{-- <button id="clear_filter" class="btn btn-primary btn-sm ml-2">
                            {{ __('messages.safety_materials.clear') }}
                        </button> --}}
                    </div>


                    @include('safety_materials.table')

                </div>
            </div>
        </div>
    </section>
@endsection

@section('page_scripts')
    <script src="{{ asset('assets/js/jquery.dataTables.min.js') }}"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
    <script src="{{ mix('assets/js/custom/custom-datatable.js') }}"></script>
    <script src="{{ mix('assets/js/select2.min.js') }}"></script>
@endsection

@section('scripts')
    <script>
        let safetyMaterialsUrl = "{{ route('safety-materials.index') }}";

        let tbl = $('#safetyMaterialsTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: safetyMaterialsUrl,
                data: function(d) {
                    d.employee_id = $('#employee_filter').val();
                }
            },
            dom: '<"d-flex justify-content-between align-items-center mb-2"<"d-flex flex-column align-items-start"<"mb-2"B><"mb-2"l>>f>rtip',
            buttons: [{
                extend: 'csvHtml5',
                text: 'CSV',
                className: 'btn btn-info btn-sm'
            }],
            lengthMenu: [
                [10, 25, 50, 100, -1],
                [10, 25, 50, 100, "All"]
            ],
            columnDefs: [{
                    className: 'text-center',
                    targets: [0, 5]
                },
                {
                    className: 'text-right',
                    targets: [3]
                },
                {
                    className: 'text-left',
                    targets: [1, 2, 4]
                }
            ],
            columns: [{
                    data: 'date',
                    name: 'date',
                    render: data => moment(data).format('DD MMM, YYYY')
                },
                {
                    data: 'employee_name',
                    name: 'employee.name',
                    defaultContent: ''
                },
                {
                    data: 'category',
                    name: 'category'
                },
                {
                    data: 'amount',
                    name: 'amount',
                    render: data => parseFloat(data).toLocaleString()
                },
                {
                    data: 'duration',
                    name: 'duration',
                    render: data => ({
                        '1month': '1 Month',
                        '2m': '2 Months',
                        '3m': '3 Months',
                        '6m': '6 Months',
                        '9m': '9 Months',
                        '1year': '1 Year'
                    } [data] || data)
                },
                {
                    data: 'next_date',
                    name: 'next_date',
                    render: data => moment(data).format('DD MMM, YYYY')
                },
                {
                    data: row => `
                <button class="btn btn-danger btn-sm delete-btn" data-id="${row.id}">
                    <i class="fas fa-trash"></i>
                </button>
                <a href="{{ route('safety-materials.show', ':id') }}" class="btn btn-info btn-sm">
                    <i class="fas fa-eye"></i>
                </a>
                <a href="{{ route('safety-materials.edit', ':id') }}" class="btn btn-warning btn-sm">
                    <i class="fas fa-edit"></i>
                </a>
            `.replace(/:id/g, row.id),
                    orderable: false,
                    searchable: false,
                    className: 'text-center'
                }
            ]
        });


        // Initialize Select2
        $('#employee_filter').select2({
            placeholder: "{{ __('messages.safety_materials.select_employee') }}",
            allowClear: true,
            width: 'resolve'
        });

        // Reload table when employee changes
        $('#employee_filter').on('change', () => tbl.ajax.reload());

        // Clear button
        $('#clear_filter').on('click', () => {
            $('#employee_filter').val(null).trigger('change'); // clear and update Select2
            tbl.ajax.reload();
        });

        // Delete button
        $(document).on('click', '.delete-btn', function() {
            let id = $(this).data('id');
            deleteItem('{{ route('safety-materials.destroy', '') }}/' + id, '#safetyMaterialsTable',
                'Safety Material');
        });
    </script>
@endsection
