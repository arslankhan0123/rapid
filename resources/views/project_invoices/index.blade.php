@extends('layouts.app')
@section('title')
    {{ __('messages.project-invoices.name') }}
@endsection
@section('page_css')
    <link href="{{ asset('assets/css/jquery.dataTables.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="{{ asset('assets/css/bs4-summernote/summernote-bs4.css') }}">
@endsection
@section('content')
    <section class="section">
        <div class="section-header item-align-right">
            <h1>{{ __('messages.project-invoices.name') }}</h1>
            <div class="section-header-breadcrumb float-right">
                <div class="card-header-action mr-3 select2-mobile-margin">
                    @can('export_project_invoices')
                        <button type="button" id="btnExport" class="btn btn-primary">
                            Export
                        </button>
                    @endcan
                </div>
            </div>
        </div>
        <div class="section-body">
            <div class="card">
                <div class="card-body">


                    <div class="row">
                        <div class="form-group col-sm-12 col-md-3">
                            {{ Form::label('', __('messages.customer.select_month') . ':') }}
                            <input type="month" id="globalSearch" class="form-control" value="" title="All">

                        </div>

                        <div class="form-group col-sm-12 col-md-3">
                            {{ Form::label('customer_id', __('messages.customer.select_customer') . ':') }}
                            {{ Form::select('customer_id', ['' => __('messages.customer.all')] + $customers->toArray(), null, ['class' => 'form-control', 'required', 'id' => 'customer_select']) }}
                        </div>

                        <div class="form-group col-sm-12 col-md-3">
                            {{ Form::label('project_id', __('messages.customer.select_project') . ':') }}
                            {{ Form::select('project_id', ['' => __('messages.customer.all')] + $projects->pluck('project_name', 'id')->toArray(), null, ['class' => 'form-control', 'required', 'id' => 'project_select']) }}
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
                    @include('project_invoices.table')
                </div>
            </div>
        </div>
    </section>
    @include('project_invoices.templates.templates')
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
        let tbl = $('#projectInvoiceTable').DataTable({
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
                url: route('project-invoices.index'),
                data: function(d) {
                    // Send additional data for filtering
                    d.month = $('#globalSearch').val();
                    d.customer_id = $('#customer_select').val();
                    d.project_id = $('#project_select').val();
                }
            },
            columns: [{
                    data: function(row) {
                        // Format created_at to dd-mm-yyyy
                        let date = new Date(row.created_at);
                        let formattedDate = ('0' + date.getDate()).slice(-2) + '-' +
                            ('0' + (date.getMonth() + 1)).slice(-2) + '-' +
                            date.getFullYear(); // Get the full year
                        return formattedDate;
                    },
                    name: 'created_at',
                    width: '20%'
                }, {
                    data: function(row) {
                        let element = document.createElement('textarea');
                        element.innerHTML = row.id;
                        return element.value;
                    },
                    name: 'id',
                    width: '20%'
                },
                {
                    data: function(row) {
                        let element = document.createElement('textarea');
                        element.innerHTML = row.customer ? row.customer.company_name : '';
                        return element.value;
                    },
                    name: 'customer.company_name',
                    width: '30%'
                },
                {
                    data: function(row) {
                        let element = document.createElement('textarea');
                        element.innerHTML = row.project ? row.project.project_name : '';
                        return element.value;
                    },
                    name: 'project.project_name',
                    width: '30%'
                },
                {
                    data: function(row) {
                        let element = document.createElement('textarea');
                        element.innerHTML = row.total_employees;
                        return element.value;
                    },
                    name: 'total_employees',
                    width: '30%'
                },
                {
                    data: function(row) {
                        let element = document.createElement('textarea');
                        element.innerHTML = row.total_amount;
                        return element.value;
                    },
                    name: 'total_amount',
                    width: '30%'
                },
                {
                    data: function(row) {
                        let element = document.createElement('textarea');
                        element.innerHTML = row.status;
                        return element.value;
                    },
                    name: 'status',
                    width: '30%'
                },
                {
                    data: function(row) {
                        return renderActionButtons(row.id);
                    },
                    name: 'id',
                    width: '100px'
                }
            ],

            responsive: true // Enable responsive features
        });

        function renderActionButtons(id) {
            let buttons = '';
            let viewUrl = `{{ route('project-invoices.invoice', ':id') }}`;
            viewUrl = viewUrl.replace(':id', id);
            buttons += `
                <a  href="${viewUrl}" class="btn btn-info action-btn has-icon view-btn" style="float:right;margin:2px;">
                    ...
                </a>
            `;
            return buttons;
        }
        $('#submitButton').on('click', function() {
            // Reload the DataTable to reflect the new filters
            tbl.ajax.reload();
        });
    </script>

    <script>
        $(document).ready(function() {
            $('#customer_select').select2({
                width: '100%', // Set the width of the select element
                allowClear: true // Allow clearing the selection
            });

            $('#project_select').select2({
                width: '100%', // Set the width of the select element
            });

        });
    </script>
    <script>
        $(document).ready(function() {
            const allProjects = {!! json_encode($projects) !!}; // Pass all projects to JavaScript

            $('#customer_select').on('change', function() {
                const selectedCustomerId = $(this).val();

                // Clear the project select
                $('#project_select').empty();

                // Add "All" option to project select
                $('#project_select').append(new Option('{{ __('messages.customer.all') }}', ''));

                if (selectedCustomerId) {
                    // Filter projects based on the selected customer
                    const filteredProjects = allProjects.filter(project => project.customer_id ==
                        selectedCustomerId);

                    // Populate the project select with filtered projects
                    $.each(filteredProjects, function(index, project) {
                        $('#project_select').append(new Option(project.project_name, project.id));
                    });
                } else {
                    // If no customer is selected, show all projects
                    $.each(allProjects, function(index, project) {
                        $('#project_select').append(new Option(project.project_name, project.id));
                    });
                }
            });

            // Trigger change to populate projects on page load
            $('#customer_select').trigger('change');
        });
    </script>

    <script>
        $('#btnExport').on('click', function() {
            // Collect the current filters
            const month = $('#globalSearch').val();
            const customerId = $('#customer_select').val();
            const projectId = $('#project_select').val();

            // Make an AJAX call to export the data
            $.ajax({
                url: '{{ route('project-invoices.export') }}', // Adjust to your export route
                method: 'post',
                data: {
                    month: month,
                    customer_id: customerId,
                    project_id: projectId
                },
                success: function(response) {
                    console.log(response);
                    // Create a blob from the CSV data and trigger a download
                    const blob = new Blob([response], {
                        type: 'text/csv;charset=utf-8;'
                    });
                    const link = document.createElement('a');
                    const url = URL.createObjectURL(blob);
                    link.setAttribute('href', url);
                    link.setAttribute('download', 'project_invoices.csv');
                    link.style.visibility = 'hidden';
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                },
                error: function(xhr) {
                    console.error(xhr);
                }
            });
        });
    </script>
@endsection
