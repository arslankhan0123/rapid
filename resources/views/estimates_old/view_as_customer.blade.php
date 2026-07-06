<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport"
        content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ __('messages.estimate.estimate_prefix') . $estimate->estimate_number }}</title>
    <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="{{ asset('assets/css/@fortawesome/fontawesome-free/css/all.css') }}">
    <link rel="stylesheet" href="{{ mix('assets/css/sales/view-as-customer.css') }}">
    <link rel="stylesheet" href="{{ mix('assets/css/estimates/estimates.css') }}">
</head>

<body>
    <div class="container">
        <div class="col-12">
            <div class="row">
                <div class="logo mt-4">
                    <a href="{{ route('estimates.index') }}">
                        <img src="{{ $settings['logo'] }}" class="img-fluid" width="120px">
                    </a>
                </div>
            </div>
        </div>
        <div class="buttons d-flex mt-3 justify-content-between">
            <div class="status">
                {{-- <a href="#"
                    class="btn btn-outline-secondary mx-1 status-{{ \App\Models\Estimate::STATUS[$estimate->status] }}">
                    {{ \App\Models\Estimate::STATUS[$estimate->status] }}
                </a> --}}
            </div>
            <div class="download-btn">
                <a href="{{ route('estimates.index') }}" class="btn btn-light btn-sm text-uppercase mx-1 border">
                    <i class="fa fa-undo"></i> {{ __('messages.common.back') }}</a>
                <a href="{{ route('estimate.pdf', ['estimate' => $estimate->id]) }}"
                    class="btn btn-light btn-sm text-uppercase mx-1 border">
                    <i class="far fa-file-pdf"></i> {{ __('messages.common.download') }}
                </a>
            </div>
        </div>
        <div class="card my-4 shadow ">
            <div class="card-body">
                <div class="col-12">
                    <div class="row">
                        <div class="col-lg-6 col-md-6 col-sm-12">
                            <h4>{{ __('messages.estimate.estimate_prefix') . $estimate->estimate_number }}</h4>
                            <p class="invoice-company-name m-0 text-muted font-weight-bold">
                                {{ $estimate->customer->company_name }}</p>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-12 estimateDetail">
                            @foreach ($estimate->estimateAddresses as $address)
                                <div class="w-75 float-right invoice-addresses text-right text-muted mb-2">
                                    <p class="font-weight-bold m-0">
                                        {{ $address->type == 1 ? __('messages.estimate.bill_to') : __('messages.estimate.ship_to') }}
                                        :</p>
                                    <p class="m-0">{{ $address->street }}</p>
                                    <p class="m-0">{{ $address->city }}, {{ $address->state }}</p>
                                    <p class="m-0">{{ $address->country }}</p>
                                    <p class="m-0">{{ $address->zip_code }}</p>
                                </div>
                            @endforeach
                            <div class="invoice-date d-table float-right">
                                <div class="d-table-row">
                                    <div class="d-table-cell text-right font-weight-bold text-muted pr-1">
                                        {{ __('messages.estimate.estimate_date') . ':' }}</div>
                                    <div class="d-table-cell">
                                        {{ !empty($estimate->estimate_date) ? Carbon\Carbon::parse($estimate->estimate_date)->translatedFormat('jS M, Y') : __('messages.common.n/a') }}
                                    </div>
                                </div>
                                <div class="d-table-row">
                                    <div class="d-table-cell text-right font-weight-bold text-muted pr-1">
                                        {{ __('messages.estimate.expiry_date') . ':' }}</div>
                                    <div class="d-table-cell">
                                        {{ !empty($estimate->estimate_expiry_date) ? Carbon\Carbon::parse($estimate->estimate_expiry_date)->translatedFormat('jS M, Y') : __('messages.common.n/a') }}
                                    </div>
                                </div>
                                @if (isset($estimate->sales_agent_id))
                                    <div class="d-table-row">
                                        <div class="d-table-cell text-right font-weight-bold text-muted pr-1">
                                            {{ __('messages.invoice.sale_agent') . ':' }}</div>
                                        <div class="d-table-cell">
                                            {{ !empty($estimate->sales_agent_id) ? $estimate->user->full_name : __('messages.common.n/a') }}
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="row">
                        <div class="col-12 mt-5">
                            <table
                                class="table table-responsive-sm table-responsive-md table-responsive-lg table-bordered">
                                <thead>
                                    <tr>
                                        <th>{{ __('messages.employees.id') }}</th>
                                        <th>{{ __('messages.employees.name') }}</th>
                                        <th>{{ __('messages.designations.name') }}</th>
                                        <th>{{ __('messages.estimate.qty') }}</th>
                                        <th class="text-right ">{{ __('messages.products.rate') }}
                                        </th>
                                        <th class="text-right ">{{ __('messages.estimate.taxes') }}(<i
                                                class="fas fa-percentage"></i>)
                                        </th>
                                        <th class="text-right ">{{ __('messages.estimate.total') }}
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($employeeQuotations as $quotation)
                                        <tr>
                                            <td>
                                                {{ $quotation->employee->iqama_no ?? '' }}
                                            </td>
                                            <td style="min-width: 150px;">
                                                <div>
                                                    {{ $quotation->employee->name ?? '' }}
                                                </div>
                                            </td>
                                            <td>
                                                <div>
                                                    {{ $quotation->employee->designation->name ?? '' }}
                                                </div>
                                            </td>
                                            <td>{{ $quotation['hours'] }}</td>
                                            <!-- Assuming 'hours' represent the quantity -->
                                            <td class="text-right">{{ $quotation['rate'] }}</td>
                                            <td class="text-right">{{ $quotation['taxes'] }}</td>
                                            <td class="text-right">
                                                {{ number_format($quotation['hours'] * $quotation['rate'] + $quotation['taxes'], 2) }}
                                            </td>
                                            <!-- Total calculation -->
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <table class="table w-25 float-right text-right">
                                <tbody>
                                    <tr>
                                        <td>
                                            {{ __('messages.estimate.sub_total') . ':' }}
                                        </td>
                                        <td class="amountData">

                                            {{ !empty($estimate->sub_total) ? number_format($estimate->sub_total, 2) : __('messages.common.n/a') }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            {{ __('messages.estimate.discount') . ':' }}
                                        </td>
                                        <td class="itemRate">
                                            {{ formatNumber($estimate->discount) }}{{ isset($estimate->discount_symbol) && $estimate->discount_symbol == 1 ? '%' : '' }}
                                        </td>
                                    </tr>
                                    @foreach ($estimate->salesTaxes as $commonTax)
                                        <tr>
                                            <td>{{ __('messages.products.tax') }} {{ $commonTax->tax }}%</td>
                                            <td>

                                                {{ number_format($commonTax->amount, 2) }}
                                            </td>
                                        </tr>
                                    @endforeach
                                    <tr>
                                        <td>{{ __('messages.estimate.adjustment') . ':' }}</td>
                                        <td class="itemRate">

                                            {{ number_format($estimate->adjustment) }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>{{ __('messages.estimate.total') . ':' }}</td>
                                        <td class="amountData">

                                            {{ number_format($estimate->total_amount, 2) }}
                                        </td>
                                    </tr>
                                    <tr class="text-danger">
                                        <td>{{ __('messages.estimate.amount_due') . ':' }}</td>
                                        <td class="itemRate">

                                            {{ number_format($estimate->total_amount - $totalPaid, 2) }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="col-12">
                    <div class="row">
                        <div class="col-12">
                            <p class="font-weight-bold">{{ __('messages.estimate.terms_conditions') . ':' }}</p>
                            {!! !empty($estimate->term_conditions)
                                ? html_entity_decode($estimate->term_conditions)
                                : __('messages.common.n/a') !!}
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script src="{{ asset('assets/js/popper.min.js') }}"></script>
    <script src="{{ asset('assets/js/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/js/bootstrap.min.js') }}"></script>
</body>

</html>
