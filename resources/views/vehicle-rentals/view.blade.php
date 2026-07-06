@extends('layouts.app')
@section('title')
    {{ __('messages.vehicle-rentals.view') }}
@endsection
@section('page_css')
    <link href="{{ asset('assets/css/jquery.dataTables.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="{{ asset('assets/css/bs4-summernote/summernote-bs4.css') }}">
@endsection
@section('content')
    <section class="section">
        <div class="section-header item-align-right">
            <h1>{{ __('messages.vehicle-rentals.view') }}</h1>
            <div class="section-header-breadcrumb float-right">
                <div class="card-header-action mr-3 select2-mobile-margin">
                </div>
            </div>
            <div class="float-right">
                <a href="{{ route('vehicle-rentals.index') }}"
                    class="btn btn-primary form-btn">{{ __('messages.common.list') }}</i>
                </a>
            </div>
        </div>
        <div class="section-body">
            <div class="card">
                <div class="card-body">


                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <strong>Rental Seq</strong>
                                <p style="color: #555;">{{ $rental->rental_number ?? '' }}</p>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <strong> Rental Number</strong>
                                <p style="color: #555;">{{ $rental->plate_number ?? '' }}</p>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <strong>Rental Name</strong>
                                <p style="color: #555;">{{ $rental->name ?? '' }}</p>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <strong>Agreement Amount</strong>
                                <p style="color: #555;">{{ $rental->amount ?? '' }}</p>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <strong>Agreement Type</strong>
                                <p style="color: #555;">
                                <p style="color: #555;">{{ $rental->agreement_type ?? '' }}</p>
                                </p>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <strong>Installment Plan </strong>
                                <p style="color: #555;">{{ ucfirst($rental->type ?? '') }}</p>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <strong>Installment No </strong>
                                <p style="color: #555;">{{ $rental->installment_no ?? '' }}</p>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <strong>Installment Amount </strong>
                                <p style="color: #555;">{{ $rental->installment_amount ?? '' }}</p>
                            </div>
                        </div>




                        <div class="col-md-3">
                            <div class="form-group">
                                <strong>Agreement Date</strong>
                                <p style="color: #555;">
                                    {{ $rental->agreement_date ? \Carbon\Carbon::parse($rental->agreement_date)->format('d-m-Y') : '' }}
                                </p>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <strong>Expiry Date</strong>
                                <p style="color: #555;">
                                    {{ $rental->expiry_date ? \Carbon\Carbon::parse($rental->expiry_date)->format('d-m-Y') : '' }}
                                </p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <strong> Notification Days</strong>
                                <p style="color: #555;">{{ $rental->notification_days ?? '' }}</p>
                            </div>
                        </div>
                        <div class="col-md-3 d-none">
                            <div class="form-group">
                                <strong>Notification Date</strong>
                                <p style="color: #555;">
                                    {{ $rental->notification_date ? \Carbon\Carbon::parse($rental->notification_date)->format('d-m-Y') : '' }}
                                </p>
                            </div>
                        </div>

                        <div class="col-md-3 ">
                            <div class="form-group">
                                <strong>Paid Amount</strong>
                                <p style="color: #555;">
                                    {{ $rental->paid_amount }}
                                </p>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group">
                                <strong> Description</strong>
                                <p style="color: #555;">{!! $rental->description ?? '' !!}</p>
                            </div>
                        </div>
                    </div>

                    <div class="row">

                        {{-- <table class="table table-responsive-sm table-responsive-md table-responsive-lg table-striped table-bordered" id="productsTable" >
                            <thead>
                                <tr>
                                    <th scope="col" class="text-center">Install Ment No</th>
                                    <th scope="col" class="text-center">Installment Amount</th>
                                    <th scope="col" class="text-center">Paid Amount</th>
                                    <th scope="col" class="text-center">Paid Date</th>
                                    <th scope="col" class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($details_rental as $key => $single_rent)
                                <tr>
                                    <td class="text-center">{{ $key+1 }}</td>
                                    <td class="text-right">{{ $single_rent->installment_amount }}</td>
                                    <td class="text-right">{{ $single_rent->paid_amount }}</td>
                                    <td class="text-center">{{ $single_rent->paid_date }}</td>
                                    <td class="text-center">
                                        @if (auth()->user()->can('pay_vehicle_rental'))
                                            @php
                                                $row = [
                                                    'id'         => $single_rent->id,
                                                    'rental_id'  => $single_rent->vehicle_rental_id,
                                                    'branch_id'  => $rental->branch_id,
                                                    'account_id' => $rental->account_id,
                                                    'paid_amount'=> $single_rent->paid_amount,
                                                ];
                                            @endphp
                                            @if ($single_rent->paid_amount < $single_rent->installment_amount)
                                            <a title="Pay" href="#" class="btn btn-success action-btn has-icon pay-btn" data-id="2" style="float:right;margin:2px;width:50px;" onclick='openPayModal(@json($row))'> Pay </a>
                                            @endif
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table> --}}
                        <table class="table table-bordered" id="productsTable">
                            <thead>
                                <tr>
                                    <th class="text-center">Installment No</th>
                                    <th class="text-center">Installment Amount</th>
                                    <th class="text-center">Paid Amount</th>
                                    <th class="text-center">Installment Date</th>
                                    <th class="text-center">Paid Date</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($details_rental as $key => $single_rent)
                                    <tr>
                                        <td class="text-center">{{ $key + 1 }}</td>
                                        <td class="text-right">{{ $single_rent->installment_amount }}</td>
                                        <td class="text-right">{{ $single_rent->paid_amount }}</td>
                                        <td class="text-center">
                                            {{ $single_rent->installment_date ? \Carbon\Carbon::parse($single_rent->installment_date)->format('d M Y') : '-' }}
                                        </td>
                                        <td class="text-center">
                                            {{ $single_rent->paid_date ? \Carbon\Carbon::parse($single_rent->paid_date)->format('d M Y') : '-' }}
                                        </td>
                                        <td class="text-center">
                                            @if (auth()->user()->can('pay_vehicle_rental'))
                                                @php
                                                    $row = [
                                                        'id' => $single_rent->id,
                                                        'rental_id' => $single_rent->vehicle_rental_id,
                                                        'branch_id' => $rental->branch_id,
                                                        'account_id' => $rental->account_id,
                                                        'paid_amount' => $single_rent->paid_amount,
                                                    ];
                                                @endphp
                                                @if ($single_rent->paid_amount < $single_rent->installment_amount)
                                                    <a href="#" class="btn btn-success btn-sm"
                                                        onclick='openPayModal(@json($row))'>
                                                        Pay
                                                    </a>
                                                @endif
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>


                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
@include('vehicle-rentals.pay_modal')
@section('page_scripts')
    <script src="{{ asset('assets/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ mix('assets/js/custom/custom-datatable.js') }}"></script>
    <script src="{{ mix('assets/js/bs4-summernote/summernote-bs4.js') }}"></script>
    <script src="{{ mix('assets/js/select2.min.js') }}"></script>

    <script>
        function openPayModal(row) {
            $('#payModal').modal('show');

            // Ensure row is properly parsed if passed as a JSON string
            if (typeof row === "string") {
                row = JSON.parse(row);
            }

            $('#payModal input[name="id"]').val(row.id);
            $('#payModal input[name="rental_id"]').val(row.rental_id);
            $('#payModal select[name="branch_id"]').val(row.branch_id).trigger('change');
            $('#payModal select[name="account_id"]').val(row.account_id).trigger('change');
            $('#payModal input[name="paid_amount"]').val(row.paid_amount);
        }
    </script>

    <script>
        $(document).ready(function() {
            // Store account data in a JavaScript object
            var accounts = @json($accounts);

            $('#branch').change(function() {
                var branchId = $(this).val(); // Get selected branch ID
                var $accountSelect = $('#selectAcount');

                // Clear previous options
                $accountSelect.empty();
                $accountSelect.append('<option value="">Select Cash Account</option>');

                // Filter accounts based on the selected branch
                $.each(accounts, function(index, account) {
                    if (account.branch_id == branchId) {
                        $accountSelect.append('<option value="' + account.id + '">' + account
                            .account_name + '</option>');
                    }
                });
            });


            $('#payForm').submit(function(e) {
                e.preventDefault(); // Prevent default form submission
                startLoader();
                var formData = $(this).serialize(); // Serialize form data
                var rentalId = $('input[name="rental_id"]').val(); // Get rental_id

                $.ajax({
                    url: route('vehicle-rentals.update-payment', rentalId), // Dynamic route
                    type: 'POST',
                    data: formData,
                    success: function(response) {
                        $('#payModal').modal('hide');
                        displaySuccessMessage(response.message);
                        location.reload();
                    },
                    error: function(xhr) {
                        alert('Error: ' + xhr.responseJSON.message);
                    }
                });
            });
        });
    </script>
@endsection
