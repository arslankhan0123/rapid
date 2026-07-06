@extends('layouts.app')
@section('title')
    {{ __('messages.vehicle-rentals.edit') }}
@endsection
@section('page_css')
    <link href="{{ asset('assets/css/jquery.dataTables.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="{{ asset('assets/css/bs4-summernote/summernote-bs4.css') }}">
@endsection
@section('content')
    <section class="section">
        <div class="section-header item-align-right">
            <h1>{{ __('messages.vehicle-rentals.edit') }}</h1>
            <div class="section-header-breadcrumb float-right">
                <div class="card-header-action mr-3 select2-mobile-margin">
                </div>
            </div>
            <div class="float-right">
                <a href="{{ route('vehicle-rentals.index') }}" class="btn btn-primary form-btn">
                    {{ __('messages.common.list') }}
                </a>
            </div>
        </div>
        <div class="section-body">
            <div class="card">
                <div class="card-body">


                    {{ Form::open(['id' => 'editFormNew']) }}
                    {{ Form::hidden('id', $rental->id, ['id' => 'rental_id']) }}


                    <div class="row">
                        <div class="form-group col-md-6">
                            {{ Form::label('rental_number', 'Rental Seq') }}<span class="required">*</span>
                            {{ Form::text('rental_number', $rental->rental_number, ['class' => 'form-control', 'required', 'id' => 'rentalNumber', 'readonly']) }}
                        </div>

                        <div class="form-group col-md-6">
                            {{ Form::label('vehicle_plate_number', 'Rental Number') }}<span class="required">*</span>
                            {{ Form::text('plate_number', $rental->plate_number, ['class' => 'form-control', 'required', 'id' => 'vehiclePlateNumber']) }}
                        </div>

                        <div class="form-group col-md-6">
                            {{ Form::label('vehicle_name', 'Rental Name') }}<span class="required">*</span>
                            {{ Form::text('name', $rental->name, ['class' => 'form-control', 'required', 'id' => 'vehicleName']) }}
                        </div>
                        
                        <div class="form-group col-md-6">
                            {{ Form::label('amount', 'Agreement Amount') }}<span class="required">*</span>
                            {{ Form::number('amount', $rental->amount, ['class' => 'form-control', 'required', 'id' => 'amount']) }}
                        </div>
                        
                        <div class="form-group col-md-6">
                            {{ Form::label('agreement_date', 'Agreement Type') }}<span class="required">*</span>
                            {{ Form::select(
                                'agreement_type',
                                ['One-time' => 'One-time', 'Installment' => 'Installment'],
                                $rental->agreement_type,
                                [
                                    'class' => 'form-control',
                                    'id' => 'agreement_type',
                                    'required',
                                    'placeholder' => __('Select Agreement Type'),
                                ],
                            ) }}
                        </div>

                        <div class="form-group col-md-6">
                            {{ Form::label('vehicle_type', 'Installment Plan') }}<span class="required">*</span>
                            {{ Form::select('type', $types ?? [], $rental->type, ['class' => 'form-control', 'required', 'id' => 'vehicleType', 'placeholder' => 'Select Payment Schedule']) }}
                        </div>
                        
                        <div class="form-group col-md-6">
                            {{ Form::label('agreement_date', 'Agreement Date') }}<span class="required">*</span>
                            {{ Form::date('agreement_date', $rental->agreement_date, ['class' => 'form-control', 'required', 'id' => 'agreementDate']) }}
                        </div>

                        <div class="form-group col-md-6">
                            {{ Form::label('expiry_date', 'Expiry Date') }}<span class="required">*</span>
                            {{ Form::date('expiry_date', $rental->expiry_date, ['class' => 'form-control', 'required', 'id' => 'expiryDate']) }}
                        </div>
                        
                        <div class="form-group col-md-6">
                            {{ Form::label('installment_no', 'Installment No.') }}<span class="required">*</span>
                            {{ Form::number('installment_no', $rental->installment_no, ['class' => 'form-control', 'readonly', 'id' => 'installment_no']) }}
                        </div> 
                        
                        <div class="form-group col-md-6">
                            {{ Form::label('installment_amount', 'Installment Amount') }}<span class="required">*</span>
                            {{ Form::number('installment_amount', $rental->installment_amount, ['class' => 'form-control', 'readonly', 'id' => 'installment_amount']) }}
                        </div>  
                        
                        <div class="form-group col-md-6 ">
                            {{ Form::label('notification_date', 'Notification Days') }}
                            {{ Form::number('notification_days', (int) $rental->notification_days ?? 0, ['class' => 'form-control']) }}
                        </div>
                        <div class="form-group col-md-6 d-none">
                            {{ Form::label('notification_date', 'Notification Date') }}
                            {{ Form::number('notification_date', $rental->notification_date, ['class' => 'form-control']) }}
                        </div>

                        <div class="form-group col-md-12">
                            {{ Form::label('vehicle_description', 'Description') }}
                            {{ Form::textarea('description', $rental->description, ['class' => 'form-control summernote-simple', 'id' => 'vehicleDescription']) }}
                        </div>
                    </div>


                    <div class="text-right mr-1">
                        {{ Form::button(__('messages.common.submit'), ['type' => 'submit', 'class' => 'btn btn-primary', 'id' => 'btnSave', 'data-loading-text' => "<span class='spinner-border spinner-border-sm'></span> Processing..."]) }}

                    </div>

                    {{ Form::close() }}

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
    <script type="text/javascript">
        $(document).on('submit', '#editFormNew', function(e) {
            e.preventDefault();
            processingBtn('#editFormNew', '#btnSave', 'loading');
            let rentalid = $('#rental_id').val();
            var formData = new FormData(this);

            $.ajax({
                type: 'post',
                url: route('vehicle-rentals.update', rentalid),
                data: formData,
                processData: false,
                contentType: false,
                success: function(result) {
                    if (result.success) {
                        displaySuccessMessage(result.message);
                        const url = route('vehicle-rentals.index');
                        window.location.href = url;
                    }
                },
                error: function(result) {
                    displayErrorMessage(result.responseJSON.message);
                },
                complete: function() {
                    processingBtn('#editFormNew', '#btnSave');
                },
            });
        });
    </script>
    
    <script>
        $(document).ready(function () {
    
            function toggleInstallmentFields() {
                let agreementType = $('#agreement_type').val();
                if (agreementType === 'One-time') {
                    $('#vehicleType, #installment_no, #installment_amount')
                        .closest('.form-group').hide();
                } else {
                    $('#vehicleType, #installment_no, #installment_amount')
                        .closest('.form-group').show();
                }
            }
    
            function calculateInstallments() {
                const amount = parseFloat($('#amount').val());
                const plan = $('#vehicleType').val();
                const startDate = new Date($('#agreementDate').val());
                const endDate = new Date($('#expiryDate').val());
    
                if (!amount || !plan || isNaN(startDate.getTime()) || isNaN(endDate.getTime())) {
                    $('#installment_no').val('');
                    $('#installment_amount').val('');
                    return;
                }
    
                let diff = 0;
    
                switch (plan) {
                    case 'Daily':
                        diff = Math.ceil((endDate - startDate) / (1000 * 60 * 60 * 24)) + 1;
                        break;
                    case 'Weekly':
                        diff = Math.ceil((endDate - startDate) / (1000 * 60 * 60 * 24 * 7));
                        break;
                    case 'Monthly':
                        diff = (endDate.getFullYear() - startDate.getFullYear()) * 12 +
                               (endDate.getMonth() - startDate.getMonth());
                        diff += (endDate.getDate() >= startDate.getDate()) ? 1 : 0;
                        break;
                    case 'Yearly':
                        diff = endDate.getFullYear() - startDate.getFullYear();
                        diff += (endDate.getMonth() > startDate.getMonth() ||
                                (endDate.getMonth() === startDate.getMonth() && endDate.getDate() >= startDate.getDate())) ? 1 : 0;
                        break;
                    case 'Twicly': // 2 times per month
                        let months = (endDate.getFullYear() - startDate.getFullYear()) * 12 +
                                     (endDate.getMonth() - startDate.getMonth());
                        diff = months * 2;
                        break;
                    case 'Quarterly':
                        diff = Math.ceil(((endDate - startDate) / (1000 * 60 * 60 * 24)) / 91); // approx 3 months
                        break;
                    case 'Half Year':
                        diff = Math.ceil(((endDate - startDate) / (1000 * 60 * 60 * 24)) / 182); // approx 6 months
                        break;
                    default:
                        diff = 0;
                }
    
                if (diff > 0) {
                    const perInstallment = (amount / diff).toFixed(2);
                    $('#installment_no').val(diff);
                    $('#installment_amount').val(perInstallment);
                } else {
                    $('#installment_no').val('');
                    $('#installment_amount').val('');
                }
            }
    
            // Initial setup
            toggleInstallmentFields();
    
            // Events
            $('#agreement_type').change(function () {
                toggleInstallmentFields();
            });
    
            $('#vehicleType, #agreementDate, #expiryDate, #amount').on('change keyup', function () {
                calculateInstallments();
            });
        });
    </script>
    
@endsection
