@extends('layouts.app')
@section('title')
    {{ __('messages.employees.view') }}
@endsection
@section('page_css')
    <link href="{{ asset('assets/css/jquery.dataTables.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="{{ asset('assets/css/int-tel/css/intlTelInput.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/bs4-summernote/summernote-bs4.css') }}">
@endsection
@section('content')
    <section class="section">
        <div class="section-header item-align-right">
            <h1>{{ __('messages.casual_employees.view') }}</h1>
            <div class="section-header-breadcrumb float-right">
                <div class="card-header-action mr-3 select2-mobile-margin">
                </div>
            </div>
            <div class="float-right">
                <a href="{{ route('casual.employees.card.view', $employee->id) }}"
                    class="btn btnWarning  form-btn text-white">
                    Download ID CARD</a>
                <a href="{{ route('casual.employees.index') }}" class="btn btn-primary form-btn">
                    {{ __('messages.casual_employees.list') }}</a>
            </div>
        </div>
        <div class="section-body">
            <div class="card">
                <div class="card-body">
                    <div style="width:100%;">
                        <div>

                            {{-- basic info ends here --}}

                            <div class="modal-body employeeForm">
                                <div class="alert alert-danger d-none" id="validationErrorsBox"></div>
                                <div
                                    style="background:#6777ef;width:100%;border-radius:2px; color:white;height:50px;padding:10px 0px 10px 0px;margin-bottom:10px;">
                                    <h4 class="text-center">{{ __('messages.employees.basic_information') }}</h4>
                                </div>
                                <div class="row">
                                    <div class="form-group col-sm-12  col-md-3">
                                        <strong>{{ Form::label('title', __('messages.employees.code')) }}</strong>
                                        <p style="color: #555;">{{ $employee->code }}</p>
                                    </div>
                                    <div class="form-group col-sm-12  col-md-3">
                                        <strong>{{ Form::label('title', __('messages.employees.employee_name')) }}</strong>
                                        <p style="color: #555;">{{ $employee->name }}</p>
                                    </div>
                                    {{-- <div class="form-group col-sm-12  col-md-3">
                                        <strong>{{ Form::label('title', __('messages.employees.employment_type')) }}</strong>
                                        <p style="color: #555;">{{ $employee->employment_type }}</p>
                                    </div> --}}

                                    {{-- <div class="form-group col-sm-12 col-md-3">
                                        <strong>
                                            {{ Form::label('title', __('messages.employees.departments')) }}</strong>
                                        <p style="color: #555;">{{ $employee->department->name ?? null }}</p>
                                    </div> --}}
                                    {{-- <div class="form-group col-sm-12 col-md-3">

                                        <strong>{{ Form::label('is_working', __('messages.department.sub_departments')) }}</strong>
                                        <p style="color: #555;">{{ $employee->subDepartment->name ?? null }}</p>
                                    </div> --}}




                                    {{-- <div class="form-group col-sm-12 col-md-3">

                                        <strong>{{ Form::label('title', __('messages.employees.designations')) }}</strong>
                                        <p style="color: #555;">{{ $employee->designation->name ?? null }}</p>
                                    </div> --}}
                                    <div class="form-group col-sm-12 col-md-3">
                                        <strong> {{ Form::label('title', __('messages.employees.phone')) }}</strong>
                                        <p style="color: #555;">{{ $employee->phone }}</p>
                                    </div>
                                    {{-- <div class="form-group col-sm-12 col-md-3">
                                        <strong>{{ Form::label('shift', __('messages.employees.shift') ) }}</strong>
                                        <p style="color: #555;">{{ $employee->shifts->name ?? null }}</p>
                                    </div> --}}


                                    <div class="form-group col-sm-12 col-md-3">
                                        <strong>{{ Form::label('title', __('messages.employees.email')) }}</strong>
                                        <p style="color: #555;">{{ $employee->email }}</p>
                                    </div>


                                    <div class="form-group col-sm-12 col-md-3">
                                        <strong>{{ Form::label('title', __('messages.employees.joining_date')) }}</strong>
                                        <p style="color: #555;">
                                            {{ \Carbon\Carbon::parse($employee->join_date)->format('d M Y') }}</p>
                                    </div>
                                    <div class="form-group col-sm-12 col-md-3">
                                        <strong>{{ Form::label('title', __('messages.employees.dob')) }}</strong>
                                        <p style="color: #555;">
                                            {{ \Carbon\Carbon::parse($employee->dob)->format('d-m-Y') }}</p>

                                    </div>

                                    <div class="form-group col-sm-12 col-md-3">
                                        <strong>{{ Form::label('marital_status', __('messages.employees.marital_status')) }}</strong>
                                        <p style="color: #555;">{{ $employee->marital_status }}</p>
                                    </div>

                                    <div class="form-group col-sm-12 col-md-3">
                                        <strong>
                                            {{ Form::label('blood_group', __('messages.employees.blood_group')) }}</strong>
                                        <p style="color: #555;">{{ $employee->blood_group }}</p>

                                    </div>

                                    {{-- <div class="form-group col-sm-12 col-md-3">
                                        <strong>{{ Form::label('religion', __('messages.employees.religion')) }}</strong>
                                        <p style="color: #555;">{{ $employee->religion }}</p>
                                    </div> --}}
                                    <div class="form-group col-sm-12 col-md-3">
                                        <strong>{{ Form::label('gender', __('messages.employees.gender')) }}</strong>
                                        <p style="color: #555;">{{ $employee->gender ?? null }}</p>
                                    </div>
                                    <div class="form-group col-md-3 col-sm-12">
                                        <strong>{{ Form::label('country', __('messages.employees.country')) }}</strong>
                                        <p style="color: #555;">{{ $employee->countryEmployee->name ?? null }}</p>
                                    </div>

                                    {{-- <div class="form-group col-sm-12 col-md-3">
                                        <strong> {{ Form::label('is_working', __('messages.employees.status')) }}</strong>
                                        <p style="color: #555;">{{ $employee->status ? 'Yes' : 'No' }}</p>
                                    </div> --}}
                                    <div class="form-group col-sm-12 col-md-3">
                                        <strong>{{ Form::label('basic_salary', __('messages.employees.basic_salary')) }}</strong>
                                        <p style="color: #555;">{{ $employee->basic_salary }}</p>
                                    </div>
                                    {{-- <div class="form-group col-sm-12 col-md-3">
                                        <strong>
                                            {{ Form::label('transport_allowance', __('messages.employees.transport_allowance')) }}</strong>
                                        <p style="color: #555;">{{ $employee->transport_allowance }}</p>
                                    </div> --}}
                                    <div class="form-group col-sm-12 col-md-3">
                                        <strong>{{ Form::label('gross_salary', __('messages.employees.gross_salary')) }}</strong>
                                        <p style="color: #555;">{{ $employee->gross_salary }}</p>
                                    </div>
                                    <div class="form-group col-sm-12 col-md-3">
                                        <strong>{{ Form::label('gross_salary', __('messages.branches.name')) }}</strong>
                                        <p style="color: #555;">{{ $employee->branch?->name ?? '' }}</p>
                                    </div>
                                    {{-- <div class="form-group col-sm-12 col-md-3">
                                        <strong>{{ Form::label('gross_salary', __('messages.employees.absent_allowance_deduction')) }}</strong>
                                        <p style="color: #555;">{{ $employee->absent_allowance_deduction ?? 0 }}</p>
                                    </div> --}}
                                    {{-- <div class="form-group col-sm-12 col-md-3">
                                        <strong>{{ Form::label('hourly_rate', __('messages.employees.hourly_rate')) }}</strong>
                                        <p style="color: #555;">{{ $employee->hourly_rate }}</p>
                                    </div> --}}

                                    <div class="form-group col-sm-12 col-md-3 image_preview">
                                        {{ Form::label('driving_license_no', __('messages.employees.image')) }}</strong>
                                        <img id="imagePreview"
                                            src="{{ $employee->image ? asset('uploads/public/employee_images/' . $employee->image) : '' }}"
                                            alt="Image Preview"
                                            style="display: {{ $employee->image ? 'block' : 'none' }}; max-width: 40%; height: auto; border-radius: 5px;" />
                                    </div>

                                </div>



                            </div>


                            <div class="modal-body employeeForm">
                                <div
                                    style="background:#6777ef;width:100%;border-radius:2px; color:white;height:50px;padding:10px 0px 10px 0px;margin-bottom:10px;">
                                    <h4 class="text-center">{{ __('messages.employees.identificatin') }}</h4>
                                </div>
                                <div class="row">
                                    <!-- Iqama No and Iqama Expiry Date -->
                                    <div class="col-sm-12 col-md-4">
                                        <div class="form-group">
                                            <strong>{{ Form::label('iqama_no', __('messages.employees.iqama_no')) }}</strong>
                                            <p style="color: #555; min-height: 25px;">{{ $employee->iqama_no }}</p>
                                        </div>
                                        <div class="form-group">
                                            <strong>{{ Form::label('iqama_no_expiry_date', __('messages.employees.iqama_no_expiry_date')) }}</strong>
                                            <p style="color: #555; min-height: 25px;">
                                                {{ $employee->iqama_no_expiry_date ? \Carbon\Carbon::parse($employee->iqama_no_expiry_date)->format('d-m-Y') : '' }}
                                            </p>
                                        </div>
                                    </div>

                                    <!-- Passport No and Passport Expiry Date -->
                                    <div class="col-sm-12 col-md-4">
                                        <div class="form-group">
                                            <strong>{{ Form::label('passport', __('messages.employees.passport')) }}</strong>
                                            <p style="color: #555; min-height: 25px;">{{ $employee->passport ?? '' }}</p>
                                        </div>
                                        <div class="form-group">
                                            <strong>{{ Form::label('passport_expiry_date', __('messages.employees.passport_expiry_date')) }}</strong>
                                            <p style="color: #555; min-height: 25px;">
                                                {{ $employee->passport_expiry_date ? \Carbon\Carbon::parse($employee->passport_expiry_date)->format('d-m-Y') : '' }}
                                            </p>
                                        </div>
                                    </div>

                                    <!-- TUV No and TUV Expiry Date -->
                                    {{-- <div class="col-sm-12 col-md-3">
                                        <div class="form-group">
                                            <strong>{{ Form::label('tuv_no', __('messages.employees.tuv_no')) }}</strong>
                                            <p style="color: #555; min-height: 25px;">{{ $employee->tuv_no ?? '' }}</p>
                                        </div>
                                        <div class="form-group">
                                            <strong>{{ Form::label('tuv_no_expiry_date', __('messages.employees.tuv_no_expiry_date')) }}</strong>
                                            <p style="color: #555; min-height: 25px;">
                                                {{ $employee->tuv_no_expiry_date ? \Carbon\Carbon::parse($employee->tuv_no_expiry_date)->format('d-m-Y') : '' }}
                                            </p>
                                        </div>
                                    </div> --}}

                                    <!-- Driving License No and Driving License Expiry Date -->
                                    <div class="col-sm-12 col-md-4">
                                        <div class="form-group">
                                            <strong>{{ Form::label('driving_license_no', __('messages.employees.driving_license_no')) }}</strong>
                                            <p style="color: #555; min-height: 25px;">{{ $employee->driving_license_no }}
                                            </p>
                                        </div>
                                        <div class="form-group">
                                            <strong>{{ Form::label('driving_license_expiry_date', __('messages.employees.driving_license_expiry_date')) }}</strong>
                                            <p style="color: #555; min-height: 25px;">
                                                {{ $employee->driving_license_expiry_date ? \Carbon\Carbon::parse($employee->driving_license_expiry_date)->format('d-m-Y') : '' }}
                                            </p>
                                        </div>
                                    </div>
                                </div>


                            </div>





                            {{-- <div class="modal-body employeeForm">
                                <div
                                    style="background:#6777ef;width:100%;border-radius:2px; color:white;height:50px;padding:10px 0px 10px 0px;margin-bottom:10px;">
                                    <h4 class="text-center">{{ __('messages.employees.type') }}</h4>
                                </div>
                                <div class="row">
                                    <div class="form-group col-sm-12 col-md-3">
                                        <strong>{{ Form::label('type', __('messages.employees.type')) }}</strong>
                                        <p style="color: #555;">{{ $employee->type }}</p>
                                    </div>
                                    <div class="form-group col-sm-12 col-md-3">
                                        <strong>
                                            {{ Form::label('duty_type', __('messages.employees.duty_type')) }}</strong>
                                        <p style="color: #555;">{{ $employee->duty_type }}</p>
                                    </div>

                                </div>
                            </div> --}}

                            {{-- Banking starts  here --}}

                            {{-- <div class="modal-body employeeForm">
                                <div
                                    style="background:#6777ef;width:100%;border-radius:2px; color:white;height:50px;padding:10px 0px 10px 0px;margin-bottom:10px;">
                                    <h4 class="text-center">{{ __('messages.employees.salary') }}</h4>
                                </div>
                                <div class="row">

                                </div>
                            </div> --}}

                            {{-- Banking starts  here --}}

                            <div class="modal-body employeeForm">
                                <div
                                    style="background:#6777ef;width:100%;border-radius:2px; color:white;height:50px;padding:10px 0px 10px 0px;margin-bottom:10px;">
                                    <h4 class="text-center">{{ __('messages.employees.bank') }}</h4>
                                </div>
                                <div class="row">
                                    <div class="form-group col-sm-12 col-md-6">
                                        <strong>{{ Form::label('bank_name', __('messages.employees.bank_name')) }}</strong>
                                        <p style="color: #555;">{{ $employee->bank_name }}</p>
                                    </div>
                                    <div class="form-group col-sm-12 col-md-6">
                                        <strong>{{ Form::label('branch_name', __('messages.employees.branch_name')) }}</strong>
                                        <p style="color: #555;">{{ $employee->branch_name }}</p>
                                    </div>
                                    <div class="form-group col-sm-12 col-md-6">
                                        <strong>
                                            {{ Form::label('bank_account_no', __('messages.employees.bank_account_no')) }}</strong>
                                        <p style="color: #555;">{{ $employee->bank_account_no }}</p>
                                    </div>
                                    <div class="form-group col-sm-12 col-md-6">
                                        <strong>
                                            {{ Form::label('iban_num', __('messages.employees.iban_num')) }}</strong>
                                        <p style="color: #555;">{{ $employee->iban_num }}</p>
                                    </div>
                                </div>
                            </div>
                            {{--  --}}


                            {{-- <div class="modal-body employeeForm">
                                <div
                                    style="background:#6777ef;width:100%;border-radius:2px; color:white;height:50px;padding:10px 0px 10px 0px;margin-bottom:10px;">
                                    <h4 class="text-center">{{ __('messages.employees.others') }}</h4>
                                </div>
                                <div class="row">
                                    <div class="form-group col-sm-12 col-md-3">
                                        <strong>{{ Form::label('title', __('messages.employees.company_name')) }}</strong>
                                        <p style="color: #555;">{{ $employee->company_name }}</p>
                                    </div>




                                    <div class="form-group col-md-3 col-sm-12">
                                        <strong>{{ Form::label('street', __('messages.employees.street')) }}</strong>
                                        <p style="color: #555;">{{ $employee->street }}</p>
                                    </div>

                                    <div class="form-group col-md-3 col-sm-12">
                                        <strong> {{ Form::label('zip', __('messages.employees.zip')) }}</strong>
                                        <p style="color: #555;">{{ $employee->zip }}</p>
                                    </div>

                                </div>

                            </div> --}}



                            <div class="modal-body employeeForm">
                                <div
                                    style="background:#6777ef;width:100%;border-radius:2px; color:white;height:50px;padding:10px 0px 10px 0px;margin-bottom:10px;">
                                    <h4 class="text-center">{{ __('messages.employees.manage_documents') }}</h4>
                                </div>
                                <div class="row " style="padding:0px 16px 0px 16px;">
                                    <table class="table table-bordered table-responsive">
                                        <thead>
                                            <tr>
                                                <th style="width: 35%;">{{ __('messages.employees.documents') }}</th>
                                                <th style="width: 35%;">{{ __('messages.employees.files') }}</th>
                                                <th style="width: 35%;">{{ __('messages.employees.expiry_date') }}</th>
                                                <th style="width: 10%;">{{ __('messages.common.action') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($employee->documents as $document)
                                                <tr>
                                                    <td>{{ $document->name }}</td>
                                                    <td>{{ $document->file }}</td>
                                                    <td>{{ $document->expiry_date ? \Carbon\Carbon::parse($document->expiry_date)->format('d/m/Y') : 'N/A' }}
                                                    </td>
                                                    <td>
                                                        <a href="{{ asset('uploads/public/employee_docs/' . basename($document->file)) }}"
                                                            class="btn btn-primary" target="_blank" title="View PDF">
                                                            <i class="fas fa-eye"></i>

                                                        </a>

                                                    </td>
                                                </tr>
                                            @endforeach

                                        </tbody>
                                    </table>

                                </div>
                            </div>




                        </div>
                    </div>

                </div>
            </div>
        </div>
    @endsection
    @section('page_scripts')
        <script src="{{ asset('assets/js/jquery.dataTables.min.js') }}"></script>
        <script src="{{ mix('assets/js/custom/custom-datatable.js') }}"></script>
        <script src="{{ mix('assets/js/bs4-summernote/summernote-bs4.js') }}"></script>
        <script src="{{ mix('assets/js/select2.min.js') }}"></script>
        <script src="{{ asset('assets/js/int-tel/js/intlTelInput.min.js') }}"></script>
        <script src="{{ asset('assets/js/int-tel/js/utils.min.js') }}"></script>
    @endsection
    @section('scripts')
        <script src="{{ mix('assets/js/custom/input-price-format.js') }}"></script>
    @endsection
