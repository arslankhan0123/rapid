@extends('layouts.app')
@section('title')
    {{ __('messages.payment_voucher.name') }}
@endsection
@section('page_css')
    <link rel="stylesheet" href="{{ mix('assets/css/expenses/expense.css') }}">
@endsection
@section('css')
    @livewireStyles
@endsection
@section('content')
    <section class="section">
        <div class="section-header item-align-right row pt-0 pb-0 ">

            <div class="col-md-2">
                <h1>{{ __('messages.payment_voucher.name') }}</h1>
            </div>
            <div class="col-md-10 col-lg-10 float-right  p-0 m-0 pt-2">
                <div class="row justify-content-between ">
                    <div class="form-group col-sm-12  col-md-2  pr-0 pl-0">
                        {{ Form::label('employee_id', __('messages.branches.name')) }}
                        {{ Form::select('branches', $usersBranches ?? [], null, ['id' => 'filterBranch', 'class' => 'form-control select2', 'placeholder' => count($usersBranches) > 1 ? 'Select Branch' : null]) }}
                    </div>
                    <div class="col-md-1 pl-1 pr-0">
                        <div class="form-group">
                            {{ Form::label('start_date', 'Payments') }}
                            {{ Form::select('expense_account', $accounts->pluck('account_name', 'id'), null, ['id' => 'expAccount', 'class' => 'form-control', 'placeholder' => 'Payments Modes']) }}

                        </div>
                    </div>
                    <div class="col-md-2 pl-1 pr-0">
                        <div class="form-group">
                            {{ Form::label('start_date', __('messages.expense.expense_category')) }}
                            {{ Form::select('expense_category', $expenseCategory, null, ['id' => 'expCategory', 'class' => 'form-control', 'placeholder' => __('messages.placeholder.select_expanse_category')]) }}

                        </div>
                    </div>
                    <div class="col-md-2 pl-1 pr-0">
                        <div class="form-group">
                            {{ Form::label('start_date', 'Sub Categories') }}
                            {{ Form::select('expense_sub_category', $expenseSubCategory->pluck('name', 'id'), null, ['id' => 'expSubCategory', 'class' => 'form-control', 'placeholder' => 'Sub Category']) }}

                        </div>
                    </div>
                    <div class="col-md-1 pl-1 pr-0">
                        <div class="form-group">
                            {{ Form::label('start_date', 'From') }}
                            {{ Form::date('start_date', null, ['class' => 'form-control', 'required', 'id' => 'start_date']) }}
                        </div>
                    </div>

                    <div class="col-md-1 pl-1 pr-0">
                        <div class="form-group">
                            {{ Form::label('end_date', 'To') }}
                            {{ Form::date('end_date', null, ['class' => 'form-control', 'required', 'id' => 'end_date']) }}
                        </div>
                    </div>

                    <div class="col-md-1 pl-1 pr-1">
                        <div class="form-group">
                            {{ Form::label('month', __('messages.task-status.month')) }}
                            {{ Form::month('month', null, ['class' => 'form-control', 'required', 'id' => 'month']) }}
                        </div>
                    </div>
                    <div class="col-md-1 p-0 " style="margin-top: 27px;">
                        <div class="dropdown">
                            <button class="btn btn-info dropdown-toggle" type="button" id="exportDropdown"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="line-height:30px;">
                                Export
                            </button>
                            <div class="dropdown-menu" aria-labelledby="exportDropdown">
                                <a class="dropdown-item" href="#" onclick="exportData('xls')">XLS</a>
                                <a class="dropdown-item" href="#" onclick="exportData('pdf')">PDF</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-1 p-0 pl-1" style="margin-top: 27px;">
                        @can('create_expenses')
                            <a href="{{ route('expenses.create') }}" class="btn btn-info" style="line-height:30px;">
                                {{ __('messages.common.add') }}
                            </a>
                        @endcan
                    </div>
                </div>
            </div>
        </div>
        <div class="section-body">
            @include('flash::message')
            <div class="card">
                <div class="card-body">
                    {{-- @livewire('expenses') --}}
                    @include('expenses.table')
                </div>
            </div>
        </div>
    </section>
@endsection
@section('scripts')
    <script src="{{ asset('vendor/livewire/livewire.js') }}"></script>
    @include('livewire.livewire-turbo')
    <script>
        let expenseUrl = "{{ route('expenses.index') }}";
        let downloadAttachmentUrl = "{{ url('admin/expense-attachment-download') }}";
    </script>
    <script src="{{ mix('assets/js/expenses/expenses.js') }}"></script>
    <script src="{{ asset('assets/js/jquery.dataTables.min.js') }}"></script>

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
                url: route('expenses.index'),
                data: function(d) {
                    // Pass the filter values to the server
                    d.expense_sub_category = $('#expSubCategory').val();
                    d.expense_category = $('#expCategory').val();
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
            autoWidth: false, // Disable automatic width calculation
            columns: [{
                    data: function(row) {
                        return row.branch?.name ?? '';
                    },
                    name: 'branch.name',
                    width: '10%'
                },
                {
                    data: function(row) {
                        const date = new Date(row.expense_date);
                        const day = String(date.getDate()).padStart(2, '0');
                        const month = String(date.getMonth() + 1).padStart(2, '0');
                        const year = date.getFullYear();
                        return `${day}-${month}-${year}`;
                    },
                    name: 'expense_date',
                    width: '8%'
                },
                {
                    data: function(row) {
                        return row.expense_number ?? '';
                    },
                    name: 'expense_number',
                    width: '10%'
                },
                {
                    data: function(row) {
                        return row.name ?? '';
                    },
                    name: 'name',
                    width: '12%'
                },

                {
                    data: function(row) {
                        return row.employee_name ?? '';
                    },
                    name: 'employee_name',
                    width: '13%'
                },
                {
                    data: function(row) {
                        return row.payment_mode ? row.payment_mode.account_name : '';
                    },
                    name: 'payment_mode.account_name',
                    width: '10%'
                },
                {
                    data: function(row) {
                        return row.expense_category ? row.expense_category.name : '';
                    },
                    name: 'expense_category.name',
                    width: '10%'
                },
                {
                    data: function(row) {
                        return row.expense_sub_category ? row.expense_sub_category.name : '';
                    },
                    name: 'expense_sub_category.name',
                    width: '10%'
                },
                {
                    data: function(row) {

                        return row.supplier?.company_name ?? "";
                    },
                    name: 'supplier.company_name',
                    width: '10%'
                },
                {
                    data: function(row) {
                        // Format the amount to 2 decimal places
                        return row.amount !== null ? parseFloat(row.amount).toFixed(2) : '';
                    },
                    name: 'amount',
                    width: '12%',
                    createdCell: function(td) {
                        $(td).css('text-align', 'right'); // Aligns the cell content to the right
                    }
                },
                {
                    data: function(row) {
                        return renderActionButtons(row.id);
                    },
                    name: 'id',

                }
            ],
            responsive: true,
            footerCallback: function(row, data, start, end, display) {
                let api = this.api();

                // Calculate total for the current page
                let pageTotal = api
                    .column(9, {
                        page: 'current'
                    }) // Select the "amount" column
                    .data()
                    .reduce(function(a, b) {
                        return (parseFloat(a) || 0) + (parseFloat(b) || 0); // Add the amounts
                    }, 0);

                // Update the footer with the total
                $(api.column(9).footer()).html(pageTotal.toFixed(2)).css('text-align', 'right');
            },
            // Customize the DataTable DOM layout
            dom: "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6 d-flex justify-content-end'f>>" +
                // Include search box in top right
                "<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-12 col-md-6'i><'col-sm-12 col-md-6 d-flex justify-content-end'p>>", // Pagination only at the bottom
        });


        $('#expCategory,#expSubCategory, #start_date, #end_date, #month,#filterBranch,#expAccount').change(function() {
            tbl.ajax.reload(); // Reload the DataTable with the new filters
        });
        $(document).on('click', '.edit-btn', function(event) {
            let did = $(event.currentTarget).data('id');
            const url = route('expenses.edit', did);
            window.location.href = url;
        });
        $(document).on('click', '.delete-btn', function(event) {
            let assetCateogryId = $(event.currentTarget).data('id');
            deleteItem(route('expenses.destroy', assetCateogryId), '#designationTable',
                '{{ __('messages.expense.name') }}');
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
            updateDepartments: "{{ auth()->user()->can('update_expenses') ? 'true' : 'false' }}",
            deleteDepartments: "{{ auth()->user()->can('delete_expenses') ? 'true' : 'false' }}",
            viewDepartments: "{{ auth()->user()->can('view_expenses') ? 'true' : 'false' }}"
        };

        // Function to render action buttons based on permissions
        function renderActionButtons(id) {
            let buttons = '';



            if (permissions.updateDepartments === 'true') {
                let editUrl = `{{ route('expenses.edit', ':id') }}`;
                editUrl = editUrl.replace(':id', id);
                buttons += `
                <a title="${messages.edit}" href="${editUrl}" class="btn btn-warning action-btn has-icon edit-btn" style="float:right;margin:2px;">
                    <i class="fa fa-edit"></i>
                </a>
            `;
            }
            if (permissions.viewDepartments === 'true') {
                let viewUrl = `{{ route('expenses.show', ':id') }}`;
                viewUrl = viewUrl.replace(':id', id);
                buttons += `
                <a title="${messages.view}" href="${viewUrl}" class="btn btn-info action-btn has-icon view-btn" style="float:right;margin:2px;">
                    <i class="fa fa-eye"></i>
                </a>
            `;
            }

            if (permissions.deleteDepartments === 'true') {
                buttons += `
                <a title="${messages.delete}" href="#" class="btn btn-danger action-btn has-icon delete-btn" data-id="${id}" style="float:right;margin:2px;">
                    <i class="fa fa-trash"></i>
                </a>
            `;
            }

            return buttons;
        }
    </script>
    <script>
        function exportData(type) {
            // Gather filter data
            const branch = $('#filterBranch').val();
            const account = $('#expAccount').val();
            const expenseCategory = $('#expCategory').val();
            const expense_sub_category = $('#expSubCategory').val();
            const startDate = $('#start_date').val();
            const endDate = $('#end_date').val();
            const month = $('#month').val();

            // Construct the URL with query parameters and type
            const url = "{{ route('expenses.export') }}?filterBranch=" + branch +
                "&account=" + account +
                "&expense_category=" + expenseCategory +
                "&expense_sub_category=" + expense_sub_category +
                "&start_date=" + startDate +
                "&end_date=" + endDate +
                "&month=" + month +
                "&type=" + type;

            // Redirect to the URL which triggers the backend export
            window.location.href = url;
        }
    </script>


    <script>
        $(document).ready(function() {
            const expenseSubCategories = @json($expenseSubCategory);

            function updateSubCategories() {
                const categoryId = $('#expCategory').val();
                let filteredSubCategories = [];

                // If no category is selected, show all subcategories
                if (categoryId) {
                    filteredSubCategories = expenseSubCategories.filter(sub => sub.expense_category_id ==
                        categoryId);
                } else {
                    filteredSubCategories = expenseSubCategories; // Show all subcategories
                }

                $('#expSubCategory').empty();
                $('#expSubCategory').append(
                    '<option value="">{{ __('messages.placeholder.select_expanse_category') }}</option>'
                );

                $.each(filteredSubCategories, function(index, sub) {
                    $('#expSubCategory').append(
                        $('<option>', {
                            value: sub.id,
                            text: sub.name
                        })
                    );
                });
            }

            $('#expCategory').on('change', updateSubCategories);
            updateSubCategories(); // Initialize with the current selected category (if any)


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
            updateAccounts(); // Initialize with the current selected branch (if any)
        });
    </script>
@endsection
