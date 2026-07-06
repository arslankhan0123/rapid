@extends('layouts.app')
@section('title')
    {{ __('messages.salary_generates.salary_generates') }}
@endsection
@section('page_css')
    <link href="{{ asset('assets/css/jquery.dataTables.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="{{ asset('assets/css/bs4-summernote/summernote-bs4.css') }}">
@endsection
@section('content')
    <section class="section">
        <div class="section-header item-align-right">
            <h1>{{ __('messages.salary_generates.salary_generates') }}</h1>
            <div class="section-header-breadcrumb float-right">
                <div class="card-header-action mr-3 select2-mobile-margin">
                </div>
            </div>

            <div class="float-right">
                {{ Form::select('branches', $usersBranches ?? [], null, ['id' => 'filterBranch', 'class' => 'form-control select2', 'placeholder' => __('messages.placeholder.branches')]) }}
                @can('create_salary_sheet')
                    <a href="{{ route('salary_generates.create') }}" class="btn btn-primary ml-2" style="line-height: 30px;">
                        {{ __('messages.salary_generates.add') }} </a>
                @endcan
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
                    @include('salary_generates.table')
                </div>
            </div>
        </div>
    </section>
    @include('salary_generates.templates.templates')
    @include('salary_generates.modals.delete_confirm')
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
        'use strict';
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
                url: route('salary_generates.index'),
                beforeSend: function() {
                    startLoader();
                },
                data: function(d) {
                    d.filterBranch = $("#filterBranch").val();
                },
                complete: function() {
                    stopLoader();
                }
            },
            order: [
                [0, 'desc'] // Ordering by the hidden 'created_at' column (index 2) in descending order
            ],
            lengthMenu: [
                [100, 300, 500, 999, -1],
                [100, 300, 500, 999, "All"]
            ],
            pageLength: 100, // Default page length
            columnDefs: [{
                targets: 0, // Adjust the index for the hidden 'created_at' column
                orderable: true,
                visible: false, // Hide the 'created_at' column
            }],
            columns: [{
                    data: 'salary_month', // Ensure this matches the data key for created_at in your data source
                    name: 'salary_month'
                },
                {
                    data: function(row) {
                        // Convert "2024-11" to a readable date format
                        const [year, month] = row.salary_month.split('-');
                        const date = new Date(year, month - 1); // month is zero-based

                        // Format the date as "Month Year"
                        const formatter = new Intl.DateTimeFormat('en-US', {
                            month: 'long',
                            year: 'numeric'
                        });
                        return formatter.format(date); // Output: "November 2024"
                    },
                    name: 'salary_month',
                    width: '25%',

                }, {
                    data: function(row) {
                        // Convert the generate_date to a Date object
                        const date = new Date(row.generate_date);

                        // Extract the day, month, and year parts
                        const day = String(date.getDate()).padStart(2, '0'); // Ensure two digits for day
                        const month = String(date.getMonth() + 1).padStart(2, '0'); // Month is zero-based
                        const year = String(date.getFullYear()).slice(-
                            2); // Get last two digits of the year

                        // Format the date as "d-m-y"
                        return `${day}-${month}-${year}`; // Output: "27-11-24"
                    },
                    name: 'generate_date',
                    width: '15%',

                },

                {
                    data: function(row) {
                        if (row.generated_by) {
                            return row.generated_by?.full_name ?? '';
                        }
                        return '';
                    },
                    name: 'generated_by.first_name',
                    width: '20%',

                },

                {
                    data: function(row) {
                        if (row.branch) {
                            return row.branch?.name ?? '';
                        }
                        return '';
                    },
                    name: 'branch.name',
                    width: '17%',

                },
                {
                    data: function(row) {
                        const formattedAmount = (row.amount ?? 0).toFixed(2); // Ensure 2 decimal places
                        return formattedAmount;
                    },
                    name: 'amount',
                    width: '30%',
                    className: 'text-right'
                },
                {
                    data: function(row) {
                        // Initialize an empty string for buttons
                        let buttons = '';
                        // Add the List button if user has the 'list' permission
                        @can('view_salary_sheet')
                            buttons += `

                                <button class="btn btn-info btn-sm list-btn" data-id="${row.id}" title="List">
                                    <i class="fas fa-list"></i>
                                </button>

                            `;
                        @endcan

                        @can('delete_generate_salaries')
                            buttons += `<button class="btn btn-danger btn-sm delete-btn" data-id="${row.id}" title="Delete">
                            <i class="fas fa-trash"></i>
                        </button> `;
                        @endcan
                        // Conditionally add the Verify button first if status is not 1 and user has the 'approve' permission
                        // @can('approve_generate_salaries')
                        //     if (row.status != 1) {
                        //         buttons += `
                    //     <button class="btn btn-primary btn-sm verify-btn" data-id="${row.id}" title="Verify">
                    //         <i class="fas fa-check"></i>
                    //     </button>
                    //     `;
                        //     }
                        // @endcan


                        return buttons;
                    },
                    name: 'actions',
                    orderable: false,
                    searchable: false
                }

            ],
            responsive: true // Enable responsive features
        });

        $('#filterBranch').change(function() {
            tbl.ajax.reload(); // Reload DataTable with new status filter
        });


        $('#designationTable').on('click', '.list-btn', function() {
            let id = $(this).data('id');
            window.location.href = route('salary_generates.sheets', {
                salaryGenerate: id
            });


        });

        // Handle Verify button click
        $('#designationTable').on('click', '.verify-btn', function() {
            let id = $(this).data('id');

            swal({
                    title: "{{ __('messages.salary_generates.approve_salary') }}" + '!',
                    text: "{{ __('messages.salary_generates.verify') }}",
                    type: 'warning',
                    showCancelButton: true,
                    closeOnConfirm: false,
                    showLoaderOnConfirm: true,
                    confirmButtonColor: '#6777ef',
                    cancelButtonColor: '#d33',
                    cancelButtonText: Lang.get('messages.common.no'),
                    confirmButtonText: Lang.get('messages.common.yes'),
                },
                function() {

                    $.ajax({
                        url: route('salary_generates.verify', {
                            salaryGenerate: id
                        }), // Pass the ID as a route parameter
                        type: 'get',
                        success: function(response) {
                            swal({
                                title: "{{ __('messages.salary_generates.verified') }}",
                                text: "{{ __('messages.salary_generates.verified') }}",
                                type: 'success',
                                timer: 2000,
                                showConfirmButton: false
                            });
                            // You may want to reload the table or update the UI
                            $('#designationTable').DataTable().ajax.reload();
                        },
                        error: function(xhr) {
                            swal({
                                title: "{{ __('messages.common.error') }}",
                                text: "{{ __('messages.salary_generates.failed') }}",
                                type: 'error',
                                timer: 2000,
                                showConfirmButton: false
                            });
                        }
                    });
                });
        });
    </script>

    <script>
        $(document).ready(function() {
            let deleteId = null; // Store ID to delete

            // Show modal when delete button is clicked
            $(document).on('click', '.delete-btn', function() {
                deleteId = $(this).data('id');
                deleteItem(route('employee-salaries.destroy', deleteId), '#designationTable',
                    'Salary Sheet');
            });


        });
    </script>
@endsection
