@extends('layouts.app')
@section('title')
    {{ __('messages.loans.name') }}
@endsection
@section('page_css')
    <link href="{{ asset('assets/css/jquery.dataTables.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="{{ asset('assets/css/bs4-summernote/summernote-bs4.css') }}">
@endsection
@section('content')
    <section class="section">
        <div class="section-header item-align-right">
            <h1>{{ __('messages.loans.name') }}</h1>
            <div class="section-header-breadcrumb float-right">
                <div class="card-header-action mr-3 select2-mobile-margin">
                </div>
            </div>
            @can('create_loan')
                <div class="float-right">
                    <a href="{{ route('loans.create') }}" class="btn btn-primary form-btn">
                        {{ __('messages.loans.add_loan') }} </a>
                </div>
            @endcan
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
                    @include('loans.table')
                </div>
            </div>
        </div>
    </section>
    @include('loans.templates.templates')
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

        let designationCreateUrl = route('loans.store');
        $(document).on('submit', '#addNewForm', function(event) {
            event.preventDefault();
            processingBtn('#addNewForm', '#btnSave', 'loading');


            let description = $('<div />').
            html($('#createDescription').summernote('code'));
            let empty = description.text().trim().replace(/ \r\n\t/g, '') === '';

            if ($('#createDescription').summernote('isEmpty')) {
                $('#createDescription').val('');
            } else if (empty) {
                displayErrorMessage(
                    'Description field is not contain only white space');
                processingBtn('#addNewForm', '#btnSave', 'reset');
                return false;
            }
            $.ajax({
                url: designationCreateUrl,
                type: 'POST',
                data: $(this).serialize(),
                success: function(result) {
                    if (result.success) {
                        displaySuccessMessage(result.message);
                        $('#addModal').modal('hide');
                        $('#designation_name').val('');
                    }
                },
                error: function(result) {
                    displayErrorMessage(result.responseJSON.message);
                },
                complete: function() {
                    processingBtn('#addNewForm', '#btnSave');
                },
            });
        });

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
                url: route('loans.index'),
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
                    name: 'employee.name',
                    width: '12%'
                }, {
                    data: function(row) {
                        let element = document.createElement('textarea');
                        return row.employee.branch?.name ?? '';
                    },
                    name: 'employee.branch.name',
                    width: '10%'
                },
                {
                    data: function(row) {
                        let element = document.createElement('textarea');

                        if (row.permitted_by.name && row.permitted_by.name) {
                            return row.permitted_by.name;
                        }
                        return '';
                    },
                    name: 'permitted_by',
                    width: '10%'
                }, {
                    data: function(row) {
                        let amount = isNaN(parseFloat(row.amount)) ? row.amount : parseFloat(row.amount)
                            .toFixed(2);
                        return `<div class="text-right">${amount}</div>`;
                    },
                    name: 'amount',
                    width: '6%'
                }, {
                    data: function(row) {
                        let repaymentAmount = isNaN(parseFloat(row.repayment_amount)) ? row
                            .repayment_amount : parseFloat(row.repayment_amount).toFixed(2);
                        return `<div class="text-right">${repaymentAmount}</div>`;
                    },
                    name: 'repayment_amount',
                    width: '6%'
                }, {
                    data: function(row) {
                        let interestPercentage = isNaN(parseFloat(row.interest_percentage)) ? row
                            .interest_percentage : parseFloat(row.interest_percentage).toFixed(2);
                        return `<div class="text-right">${interestPercentage}%</div>`;
                    },
                    name: 'interest_percentage',
                    width: '7%'
                }, {
                    data: function(row) {
                        let installment = isNaN(parseFloat(row.installment)) ? row.installment : parseFloat(
                            row.installment).toFixed(2);
                        return `<div class="text-right">${installment}</div>`;
                    },
                    name: 'installment',
                    width: '8%'
                },
                
                {
                    data: function(row) {
                        let date = new Date(row.approved_date);
                        let formattedDate = ('0' + date.getDate()).slice(-2) + '-' +
                                            ('0' + (date.getMonth() + 1)).slice(-2) + '-' +
                                            date.getFullYear();
                        return formattedDate;
                    },
                    name: 'approved_date',
                    width: '8%'
                },
                
                {
                    data: function(row) {
                        let date = new Date(row.repayment_from);
                        let formattedDate = ('0' + date.getDate()).slice(-2) + '-' +
                                            ('0' + (date.getMonth() + 1)).slice(-2) + '-' +
                                            date.getFullYear();
                        return formattedDate;
                    },
                    name: 'repayment_from',
                    width: '8%'
                },

                {
                    data: function(row) {
                        let element = document.createElement('textarea');
                        return row.status;
                    },
                    name: 'status',
                    width: '5%'
                },
                {
                    data: function(row) {
                        return renderActionButtons(row.id);
                    },
                    name: 'id',
                    width: '10%'


                }
            ],
            responsive: true // Enable responsive features
        });

        $(document).on('click', '.edit-btn', function(event) {
            let did = $(event.currentTarget).data('id');
            const url = route('loans.edit', did);
            window.location.href = url;
        });

        $(document).on('click', '.delete-btn', function(event) {
            let assetCateogryId = $(event.currentTarget).data('id');
            deleteItem(route('loans.destroy', assetCateogryId), '#designationTable',
                '{{ __('messages.loans.name') }}');
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
            updateItem: "{{ auth()->user()->can('update_loan') ? 'true' : 'false' }}",
            deleteItem: "{{ auth()->user()->can('delete_loan') ? 'true' : 'false' }}",
            viewItem: "{{ auth()->user()->can('view_loan') ? 'true' : 'false' }}"
        };

        // Function to render action buttons based on permissions
        function renderActionButtons(id) {
            let buttons = '';



            if (permissions.updateItem === 'true') {
                let editUrl = `{{ route('loans.edit', ':id') }}`;
                editUrl = editUrl.replace(':id', id);
                buttons += `
                <a title="${messages.edit}" href="${editUrl}" class="btn btn-warning action-btn has-icon edit-btn" style="float:right;margin:2px;">
                    <i class="fa fa-edit"></i>
                </a>
            `;
            }
            if (permissions.viewItem === 'true') {
                let viewUrl = `{{ route('loans.view', ':id') }}`;
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
