@extends('layouts.app')
@section('title')
    {{ __('messages.estimate.new_estimate') }}
@endsection
@section('page_css')
    <link href="{{ asset('assets/css/jquery.dataTables.min.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="{{ asset('assets/css/bs4-summernote/summernote-bs4.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/select2.min.css') }}">
    <link href="{{ asset('css/bootstrap-datetimepicker.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="{{ mix('assets/css/estimates/estimates.css') }}">
@endsection
@section('content')
    <section class="section">
        <div class="section-header">
            <h1>{{ __('messages.estimate.new_estimate') }}</h1>
            <div class="section-header-breadcrumb">
                <a href="{{ url()->previous() }}" class="btn btn-primary form-btn float-right-mobile">
                    {{ __('messages.common.back') }}
                </a>
            </div>
        </div>
        <div class="section-body">
            @include('layouts.errors')
            <div class="card">
                {{ Form::open(['route' => 'estimates.store', 'validated' => false, 'id' => 'estimateForm']) }}
                @include('estimates.address_modal')
                @include('estimates.fields')
                {{ Form::close() }}
            </div>
        </div>
    </section>
    @include('estimates.templates.templates')
    @include('tags.common_tag_modal')
@endsection
@section('page_scripts')
    <script src="{{ asset('assets/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ mix('assets/js/custom/custom-datatable.js') }}"></script>
    <script src="{{ mix('assets/js/bs4-summernote/summernote-bs4.js') }}"></script>
    <script src="{{ asset('js/bootstrap-datetimepicker.min.js') }}"></script>
    <script src="{{ mix('assets/js/select2.min.js') }}"></script>
@endsection
@section('scripts')
    <script>
        let taxData = JSON.parse('@json($data['taxes'])');
        let productUrl = "{{ route('products.index') }}";
        let estimateUrl = "{{ route('estimates.index') }}";
        let isCreate = true;
        let createData = true;
        let createEstimateAddress = true;
        let customerURL = "{{ route('get.estimate.customer.address') }}";
        let editData = false;
        let tagSaveUrl = "{{ route('tags.store') }}";
    </script>
    <script src="{{ mix('assets/js/sales/sales.js') }}"></script>
    <script src="{{ mix('assets/js/custom/input-price-format.js') }}"></script>
    <script src="{{ mix('assets/js/estimates/estimates.js') }}"></script>

    <script>
        $(document).ready(function() {
            $('#EmployeeAddAddBtn').on('click', function(e) {
                e.preventDefault();
                // Create a new row with the necessary inputs and a delete button
                var newRow = `
            <tr>
                <td style="width: 40%;">
                   {{ Form::select('employee_id[]', $employees, null, [
                       'class' => 'form-control',
                       'required' => true,
                   ]) }}
                </td>
                <td><input type="number" name="rate[]" class="form-control " required placeholder="{{ __('messages.estimate.rate') }}"></td>
                <td><input type="text" name="remarks[]" class="form-control " ></td>
                <td >

                    <a class='btn text-danger remove-invoice-item '><i class="far fa-trash-alt"></i></a>

                </td>
            </tr>
        `;

                // Append the new row to the table
                $('.items-container').append(newRow);

                // Reinitialize select2 for the new row (if using select2 for tax dropdowns)
                $('.tax-rates').select2();
            });

            // Event delegation to handle removing rows
            $(document).on('click', '.remove-row', function() {
                $(this).closest('tr').remove(); // Remove the row
            });

            // Initialize select2 for existing tax-rates (if needed)
            $('.tax-rates').select2();
        });
    </script>
@endsection
