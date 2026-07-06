@extends('layouts.app')
@section('title')
    {{ __('messages.member.new_member') }}
@endsection
@section('page_css')
    <link href="{{ asset('assets/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="{{ asset('assets/css/int-tel/css/intlTelInput.css') }}">
@endsection
@section('content')
    <section class="section">
        <div class="section-header">
            <h1>{{ __('messages.member.new_member') }}</h1>
            <div class="section-header-breadcrumb">
                <a href="{{ route('members.index') }}"
                    class="btn btn-primary form-btn float-right-mobile">{{ __('messages.common.list') }}</a>
            </div>
        </div>
        <div class="section-body">
            @include('layouts.errors')
            <div class="card">
                <div class="card-body">
                    {{ Form::open(['route' => 'members.store', 'id' => 'createMember', 'files' => 'true']) }}

                    @include('members.fields')

                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </section>
@endsection
@section('page_scripts')
    <script src="{{ asset('assets/js/select2.min.js') }}"></script>
    <script src="{{ asset('assets/js/int-tel/js/intlTelInput.min.js') }}"></script>
    <script src="{{ asset('assets/js/int-tel/js/utils.min.js') }}"></script>
@endsection
@section('scripts')
    <script>
        let utilsScript = "{{ asset('assets/js/int-tel/js/utils.min.js') }}";
        let phoneNo = "{{ old('prefix_code') . old('phone') }}";
        let isEdit = false;
    </script>
    <script src="{{ mix('assets/js/custom/phone-number-country-code.js') }}"></script>
    <script src="{{ mix('assets/js/members/create-edit.js') }}"></script>
    <script>
        const employees = @JSON($employees);

        $(document).ready(function() {

            $('#employeeID').select2({
                width: '100%', // Set the width of the select element
                // allowClear: true // Allow clearing the selection
            });

            // Bind the change event on the employee dropdown
            $('#employeeID').on('change', function() {
                // Get the selected employee's ID
                var employeeId = $(this).val();

                // Find the employee data from the employees array
                var selectedEmployee = employees.find(employee => employee.id == employeeId);

                if (selectedEmployee) {
                    // Split employee name into first and last names
                    var nameParts = selectedEmployee.name.split(' ');
                    var firstName = nameParts[0] || '';
                    var lastName = nameParts.slice(1).join(' ') || ''; // Everything after the first name

                    // Set the form fields
                    $('input[name="first_name"]').val(firstName);
                    $('input[name="last_name"]').val(lastName);
                    $('input[name="email"]').val(selectedEmployee.email);
                    $('input[name="phone"]').val(selectedEmployee.phone);

                    // Check if employee image exists
                    if (selectedEmployee.image) {
                        // If the image is not null, set the employee image
                        $('#logoPreview').attr('src', "{{ asset('uploads/public/employee_images') }}/" +
                            selectedEmployee.image);
                    } else {
                        // Otherwise, use the default image
                        $('#logoPreview').attr('src', "{{ asset('assets/img/infyom-logo.png') }}");
                    }

                    // You can set other fields here similarly
                    // For example, if you have a date field for the employee's DOB:
                    // $('input[name="dob"]').val(selectedEmployee.dob);
                }
            });
        });
    </script>
@endsection
