@extends('layouts.app')
@section('title')
    {{ __('messages.salary_advances.name') }}
@endsection
@section('page_css')
    <link href="{{ asset('assets/css/jquery.dataTables.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="{{ asset('assets/css/bs4-summernote/summernote-bs4.css') }}">
@endsection
@section('content')
    <section class="section">
        <div class="section-header item-align-right">

            <div class="col-md-3">
                <h1>{{ __('messages.salary_advances.name') }}</h1>
            </div>
            <div class="col-md-9 col-lg-9  p-0 m-0 pt-2 justify-content-end">
                <div class="row justify-content-end">
                    <div class="form-group col-sm-12  col-md-2  pr-0 pl-0">
                        {{ Form::label('employee_id', __('messages.branches.name')) }}
                        {{ Form::select('branches', $usersBranches ?? [], null, ['id' => 'filterBranch', 'class' => 'form-control select2', 'placeholder' => count($usersBranches) > 1 ? 'Select Branch' : null]) }}
                    </div>
                    <div class="col-md-2 pr-0 pl-1">
                        <div class="form-group">
                            {{ Form::label('start_date', 'Payments') }}
                            {{ Form::select('expense_account', $accounts->pluck('account_name', 'id'), null, ['id' => 'expAccount', 'class' => 'form-control', 'placeholder' => 'Payments Modes']) }}

                        </div>
                    </div>

                    <div class="col-md-2 pr-0 pl-1">
                        <div class="form-group">
                            {{ Form::label('start_date', 'From') }}
                            {{ Form::date('start_date', null, ['class' => 'form-control', 'required', 'id' => 'start_date']) }}
                        </div>
                    </div>

                    <div class="col-md-2 pr-0 pl-1">
                        <div class="form-group">
                            {{ Form::label('end_date', 'To') }}
                            {{ Form::date('end_date', null, ['class' => 'form-control', 'required', 'id' => 'end_date']) }}
                        </div>
                    </div>

                    <div class="col-md-1 pr-0 pl-1">
                        <div class="form-group">
                            {{ Form::label('month', __('messages.task-status.month')) }}
                            {{ Form::month('month', null, ['class' => 'form-control', 'required', 'id' => 'month']) }}
                        </div>
                    </div>
                    <div class="col pr-0 pl-1" style="margin-top: 27px;">
                        <div class="btn-group" role="group">
                            <div class="dropdown">
                                <button class="btn btn-info dropdown-toggle" type="button" id="exportDropdown"
                                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                                    style="line-height:30px;">
                                    Export
                                </button>
                                <div class="dropdown-menu" aria-labelledby="exportDropdown">
                                    <a class="dropdown-item" href="#" onclick="exportData('xls')">XLS</a>
                                    <a class="dropdown-item" href="#" onclick="exportData('pdf')">PDF</a>
                                </div>
                            </div>

                            @can('create_salary_advances')
                                <a href="{{ route('salary_advances.create') }}" class="btn btn-primary" style="height: 42px;">
                                    {{ __('messages.common.add') }}
                                </a>
                            @endcan
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
                    @include('salary_advances.table')
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
                url: route('salary_advances.index'),
                data: function(d) {
                    // Pass the filter values to the server

                    d.start_date = $('#start_date').val();
                    d.end_date = $('#end_date').val();
                    d.month = $('#month').val();
                    d.filterBranch = $('#filterBranch').val();
                    d.account = $('#expAccount').val();
                },
                beforeSend: function() {
                    startLoader();
                },
                complete: function() {
                    stopLoader();
                }
            },
            lengthMenu: [
                [100, 300, 500, 999, -1],
                [100, 300, 500, 999, "All"]
            ],
            pageLength: 100, // Default page length
            columns: [{
                    data: function(row) {
                        return row.formatted_date ?? '';
                    },
                    width: '9%',
                    name: 'date',
                },
                {
                    data: function(row) {
                        let element = document.createElement('textarea');
                        if (row.employee && row.employee.iqama_no) {
                            return row.employee.iqama_no;
                        }
                        return '';
                    },
                    name: 'iqama_no',
                    width: '9%'

                }, {
                    data: function(row) {
                        let element = document.createElement('textarea');
                        if (row.employee && row.employee.name) {
                            return row.employee.name;
                        }
                        return '';
                    },
                    name: 'employee_id',


                },
                {
                    data: function(row) {
                        let element = document.createElement('textarea');
                        return row.employee.branch?.name ?? '';
                    },
                    name: 'employee.branch.name',

                },
                {
                    data: function(row) {
                        let element = document.createElement('textarea');
                        return row.amount ? row.amount.toFixed(2) : '0.00';
                    },
                    name: 'amount',

                    className: 'text-right'
                },
                {
                    data: function(row) {
                        return renderActionButtons(row.id);
                    },
                    name: 'id',
                    width: '70px'
                }
            ],

            responsive: true, // Disable responsive behavior to prevent column stacking

        });

        $('#start_date, #end_date, #month,#filterBranch,#expAccount').change(function() {
            tbl.ajax.reload(); // Reload the DataTable with the new filters
        });

        $(document).on('click', '.edit-btn', function(event) {
            let did = $(event.currentTarget).data('id');
            const url = route('salary_advances.edit', did);
            window.location.href = url;
        });

        $(document).on('click', '.delete-btn', function(event) {
            let assetCateogryId = $(event.currentTarget).data('id');
            deleteItem(route('salary_advances.destroy', assetCateogryId), '#designationTable',
                '{{ __('messages.salary_advances.name') }}');
        });


        const accounts = @json($accounts);

        function updateAccounts() {
            const branchId = $('#filterBranch').val();
            let filteredAccounts = [];

            // If no branch is selected, show all accounts
            if (branchId) {
                filteredAccounts = accounts.filter(account => account.branch_id == branchId);
            } else {
                filteredAccounts = accounts; // Show all accounts
            }

            $('#expAccount').empty();
            $('#expAccount').append(
                '<option value="">Select Payments Modes</option>'
            );

            $.each(filteredAccounts, function(index, account) {
                $('#expAccount').append(
                    $('<option>', {
                        value: account.id,
                        text: account.account_name
                    })
                );
            });
        }

        $('#filterBranch').on('change', updateAccounts);
        updateAccounts();
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
            updateItem: "{{ auth()->user()->can('update_salary_advances') ? 'true' : 'false' }}",
            deleteItem: "{{ auth()->user()->can('delete_salary_advances') ? 'true' : 'false' }}",
            viewItem: "{{ auth()->user()->can('view_salary_advances') ? 'true' : 'false' }}"
        };

        // Function to render action buttons based on permissions
        function renderActionButtons(id) {
            let buttons = '';



            if (permissions.updateItem === 'true') {
                let editUrl = `{{ route('salary_advances.edit', ':id') }}`;
                editUrl = editUrl.replace(':id', id);
                buttons += `
                <a title="${messages.edit}" href="${editUrl}" class="btn btn-warning action-btn has-icon edit-btn" style="float:right;margin:2px;">
                    <i class="fa fa-edit"></i>
                </a>
            `;
            }
            if (permissions.viewItem === 'true') {
                let viewUrl = `{{ route('salary_advances.view', ':id') }}`;
                viewUrl = viewUrl.replace(':id', id);
                buttons += `
                <a title="${messages.view}" href="${viewUrl}" class="btn btn-info action-btn has-icon view-btn" style="float:right;margin:2px;">
                    <i class="fa fa-eye"></i>
                </a>
            `;
            }

            if (permissions.deleteItem === 'true') {
                buttons += `
                <a title="${messages.delete}" href="#" class="btn btn-danger action-btn has-icon delete-btn" data-id="${id}" style="float:right;margin:2px;">
                    <i class="fa fa-trash"></i>
                </a>
            `;
            }

            return buttons;
        }
    </script>
@endsection
