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
                <a href="{{ route('salary_generates.create') }}" class="btn btn-primary form-btn">
                    {{ __('messages.salary_generates.add') }} </a>
            </div>
        </div>
        <div class="section-body">
            <div class="card">
                <div class="card-body">
                    @include('salary_generates.table_employee_salaries')
                </div>
            </div>
        </div>
    </section>
    @include('salary_generates.templates.templates')
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
                url: route('employee-salaries.index'),
            },
            columnDefs: [],
            columns: [{
                    data: function(row) {
                        if (row.employee) {
                            return row.employee.name;
                        }
                        return '';
                    },
                    name: 'employee_id',
                    width: '30%'
                },
                {
                    data: function(row) {
                        if (row.salary_generate) {
                            return row.salary_generate.salary_month;
                        }
                        return '';
                    },
                    name: 'salary_month',
                    width: '20%'
                },
                {
                    data: function(row) {
                        return row.salary_advance;

                    },
                    name: 'salary_advance',
                    width: '20%'
                }, {
                    data: function(row) {
                        return row.net_salary;

                    },
                    name: 'net_salary',
                    width: '20%'
                },
                {
                    data: function(row) {
                        return `
                                <button class="btn btn-primary btn-sm view-btn" onclick="viewItem(${row.id})">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="btn btn-success btn-sm download-btn" onclick="downloadItem(${row.id})">
                                    <i class="fas fa-download"></i>
                                </button>
                        `;
                    },
                    name: 'action',
                    width: '20%'
                },

            ],
            responsive: true // Enable responsive features
        });

        function viewItem(id) {
            const url = route('employee-salaries.payslip.view', {
                salarySheet: id
            });
            window.location.href = url;
        }

        function downloadItem(id) {
            console.log('Download ID: ' + id);
        }
    </script>
@endsection
