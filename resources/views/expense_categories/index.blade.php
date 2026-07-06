@extends('layouts.app')
@section('title')
    {{ __('messages.expense_category.expense_categories') }}
@endsection
@section('page_css')
    <link href="{{ asset('assets/css/jquery.dataTables.min.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="{{ asset('assets/css/bs4-summernote/summernote-bs4.css') }}">
@endsection
@section('content')
    @include('expense_categories.view_modal')
    <section class="section">
        <div class="section-header">
            <h1>{{ __('messages.expense_categories') }}</h1>
            <div class="section-header-breadcrumb">
                @can('create_expense_categories')
                    <a href="#" class="btn btn-primary form-btn addExpenseCategoryModal float-right-mobile"
                        data-toggle="modal" data-target="#addModal">{{ __('messages.common.add') }}
                    </a>
                @endcan

            </div>
        </div>
        <div class="section-body">
            <div class="card">
                <div class="card-body">
                    @include('expense_categories.table')
                </div>
            </div>
        </div>

        @include('expense_categories.templates.templates')
        @include('expense_categories.add_modal')
        @include('expense_categories.edit_modal')

    </section>
@endsection
@section('page_scripts')
    <script src="{{ asset('assets/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ mix('assets/js/custom/custom-datatable.js') }}"></script>
    <script src="{{ mix('assets/js/bs4-summernote/summernote-bs4.js') }}"></script>
@endsection
@section('scripts')
    <script src="{{ asset('assets/js/expense-categories/expense-categories.js') }}"></script>
    <script>
        $(document).on('click', '.view-btn', function() {

            $('#viewModal').modal('show');
            $('#viewName').text("Please Wait...");
            var id = $(this).data('id'); // Get the ID of the expense category
            // Make an AJAX call to fetch the category details from the edit route
            $.ajax({
                url: "{{ route('expense-categories.edit', ':id') }}".replace(':id', id),
                type: 'GET',
                success: function(response) {
                    // Populate the modal fields with the fetched data
                    $('#categoryId').val(response.data.id);
                    $('#viewName').text(response.data.name); // Set the name as text for viewing
                    $('#viewDescription').html(response.data
                        .description); // Set the description as text for viewing
                    // Show the modal

                },
                error: function(xhr) {
                    console.log(xhr.responseText); // Log errors if any
                }
            });
        });
    </script>
@endsection
