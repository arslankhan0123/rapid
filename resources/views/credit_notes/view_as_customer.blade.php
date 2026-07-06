<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport"
        content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ __('messages.credit_note.credit_note_prefix') . $creditNote->credit_note_number }}</title>
    <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/@fortawesome/fontawesome-free/css/all.css') }}" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="{{ mix('assets/css/sales/view-as-customer.css') }}">
    <link rel="stylesheet" href="{{ mix('assets/css/credit_notes/credit_notes.css') }}">
</head>

<body>
    <div class="container">
        <div class="col-12">
            <div class="row">
                <div class="logo mt-4">
                    <a href="{{ route('credit-notes.index') }}">
                        <img src="{{ $settings['logo'] }}" class="img-fluid" width="120px">
                    </a>
                </div>
            </div>
        </div>
        <div class="buttons d-flex mt-3 justify-content-between">
            <div class="status">
                <a href="#"
                    class="btn btn-outline-secondary mx-1 status-{{ \App\Models\CreditNote::PAYMENT_STATUS[$creditNote->payment_status] }}">
                    {{ \App\Models\CreditNote::PAYMENT_STATUS[$creditNote->payment_status] }}
                </a>
            </div>
            <div class="download-btn">
                <a href="{{ route('credit-notes.index') }}" class="btn btn-light btn-sm text-uppercase mx-1 border">
                    <i class="fa fa-undo"></i> {{ __('messages.common.back') }}</a>
                <a href="{{ route('credit-note.pdf', ['creditNote' => $creditNote->id]) }}"
                    class="btn btn-light btn-sm text-uppercase mx-1 border">
                    <i class="far fa-file-pdf"></i> {{ __('messages.common.download') }}
                </a>
            </div>
        </div>

        <div class="card my-4 shadow ">
            <div class="card-body">
                <div class="col-12">
                    <div class="row justify-content-around">
                        <div class="col-md-4 col-lg-4 col-sm-4">
                            <h4>{{ __('messages.credit_note.credit_note_prefix') . $creditNote->credit_note_number }}
                            </h4>
                            <p class="invoice-company-name m-0 text-muted font-weight-bold">
                                {{ html_entity_decode($creditNote->customer->company_name) }}</p>
                        </div>
                        <div class="col-lg-4 col-md-4 col-sm-4">
                            {{-- //qr code starts here --}}
                            <div class="row">
                                <div class="col-5">
                                    <div class="row">
                                        <div class="col-5">
                                            @php
                                                use Salla\ZATCA\GenerateQrCode;
                                                use Salla\ZATCA\Tags\Seller;
                                                use Salla\ZATCA\Tags\TaxNumber;
                                                use Salla\ZATCA\Tags\InvoiceDate;
                                                use Salla\ZATCA\Tags\InvoiceTotalAmount;
                                                use Salla\ZATCA\Tags\InvoiceTaxAmount;

                                                $qr_seller_trn = 311204277500003;
                                                $qr_tax_amount = $creditNote->salesTaxes->sum('amount');

                                                $qr_invoice_amount = !empty($creditNote->sub_total)
                                                    ? $creditNote->sub_total
                                                    : null;

                                                $qr_code = null;
                                                $qr_seller_name = 'SMiTS ENGiNEERiNG';
                                                $qr_invoice_date = !empty($creditNote->credit_note_date)
                                                    ? date('Y-m-d', strtotime($creditNote->credit_note_date))
                                                    : null;

                                                //&& $qr_tax_amount this wass added to below condition .but what if tax amount is zero which is false ,
                                                if (
                                                    $qr_seller_name &&
                                                    $qr_seller_trn &&
                                                    $qr_invoice_date &&
                                                    $qr_invoice_amount
                                                ) {
                                                    $qr_code = GenerateQrCode::fromArray([
                                                        new Seller($qr_seller_name),
                                                        new TaxNumber($qr_seller_trn),
                                                        new InvoiceDate($qr_invoice_date),
                                                        new InvoiceTotalAmount(round($qr_invoice_amount)),
                                                        new InvoiceTaxAmount($qr_tax_amount),
                                                    ])->render();
                                                }
                                            @endphp
                                            @if ($qr_code)
                                                <img class="mt-1" style="width: 200px;" src="{{ $qr_code }}"
                                                    alt="QR Code" />
                                            @endif
                                        </div>

                                    </div>
                                </div>
                            </div>
                            {{-- q4 code ends here --}}
                        </div>
                        <div class="col-md-4 col-lg-4 col-sm-4 creditNoteDetail">

                            @foreach ($creditNote->creditNoteAddresses as $address)
                                <div class="w-75 float-right invoice-addresses text-right text-muted mb-2">
                                    <p class="font-weight-bold m-0">
                                        {{ $address->type == 1 ? __('messages.invoice.bill_to') : __('messages.invoice.ship_to') }}
                                        :</p>
                                    <p class="m-0">{{ html_entity_decode($address->street) }}</p>
                                    <p class="m-0">{{ $address->city }}, {{ $address->state }}</p>
                                    <p class="m-0">{{ $address->country }}</p>
                                    <p class="m-0">{{ $address->zip_code }}</p>
                                </div>
                            @endforeach
                            <div class="invoice-date d-table float-right">
                                <div class="d-table-row">
                                    <div class="d-table-cell text-right font-weight-bold text-muted pr-1">
                                        {{ __('messages.credit_note.credit_note_date') . ':' }}</div>
                                    <div class="d-table-cell">
                                        {{ !empty($creditNote->credit_note_date) ? Carbon\Carbon::parse($creditNote->credit_note_date)->translatedFormat('jS M, Y') : __('messages.common.n/a') }}
                                    </div>
                                </div>
                                @if (!empty($creditNote->reference))
                                    <div class="d-table-row">
                                        <div class="d-table-cell text-right font-weight-bold text-muted pr-1">
                                            {{ __('messages.credit_note.reference') . ':' }}</div>
                                        <div class="d-table-cell">
                                            {{ !empty($creditNote->reference) ? html_entity_decode($creditNote->reference) : __('messages.common.n/a') }}
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
                                <thead class="thead-light">
                                    <tr>
                                        <th scope="col">#</th>
                                        <th scope="col">{{ __('messages.invoice.item') }}</th>
                                        <th scope="col">{{ __('messages.invoice.qty') }}</th>
                                        <th scope="col" class="text-right itemRate">
                                            {{ __('messages.products.rate') }}</th>
                                        <th scope="col">{{ __('messages.invoice.taxes') }}</th>
                                        <th scope="col" class="text-right itemTotal">
                                            {{ __('messages.invoice.amount') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($creditNote->salesItems as $index => $item)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>
                                                <p class="m-0">{{ html_entity_decode($item->item) }}</p>
                                                <p class="text-muted m-0 table-data">
                                                    <small>{{ html_entity_decode($item->description) }}</small>
                                                </p>
                                            </td>
                                            <td>{{ $item->quantity }}</td>
                                            <td class="text-right">

                                                {{ number_format($item->rate, 2) }}
                                            </td>
                                            <td>
                                                @forelse($item->taxes as $tax)
                                                    <span
                                                        class="badge badge-secondary font-weight-normal">{{ $tax->tax_rate }}%</span>
                                                @empty
                                                    <p>{{ __('messages.common.n/a') }}</p>
                                                @endforelse
                                            </td>
                                            <td class="text-right">

                                                {{ number_format($item->total, 2) }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>

                            <table class="table w-25 float-right text-right">
                                <tbody>
                                    <tr>
                                        <td class="amountData">
                                            {{ __('messages.invoice.sub_total') . ':' }}
                                        </td>
                                        <td>

                                            {{ !empty($creditNote->sub_total) ? number_format($creditNote->sub_total, 2) : __('messages.common.n/a') }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            {{ __('messages.invoice.discount') . ':' }}
                                        </td>
                                        <td>
                                            {{ formatNumber($creditNote->discount) }}{{ isset($creditNote->discount_symbol) && $creditNote->discount_symbol == 1 ? '%' : '' }}
                                        </td>
                                    </tr>
                                    @foreach ($creditNote->salesTaxes as $commonTax)
                                        <tr>
                                            <td>{{ __('messages.products.tax') }} {{ $commonTax->tax }}%</td>
                                            <td>

                                                {{ number_format($commonTax->amount, 2) }}
                                            </td>
                                        </tr>
                                    @endforeach
                                    <tr>
                                        <td>{{ __('messages.invoice.adjustment') . ':' }}</td>
                                        <td class="w-23">

                                            {{ number_format($creditNote->adjustment) }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>{{ __('messages.invoice.total') . ':' }}</td>
                                        <td class="w-23">

                                            {{ number_format($creditNote->total_amount, 2) }}
                                        </td>
                                    </tr>
                                    <tr class="text-danger">
                                        <td>{{ __('messages.invoice.amount_due') . ':' }}</td>
                                        <td class="w-23">

                                            {{ number_format($creditNote->total_amount, 2) }}
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
                            <p class="font-weight-bold">{{ __('messages.credit_note.client_note') . ':' }}</p>
                            {!! !empty($creditNote->client_note) ? html_entity_decode($creditNote->client_note) : __('messages.common.n/a') !!}
                        </div>
                    </div>
                </div>
                <br>
                <div class="col-12">
                    <div class="row">
                        <div class="col-12">
                            <p class="font-weight-bold">{{ __('messages.credit_note.terms_and_conditions') . ':' }}
                            </p>
                            {!! !empty($creditNote->term_conditions)
                                ? html_entity_decode($creditNote->term_conditions)
                                : __('messages.common.n/a') !!}
                        </div>
                    </div>
                </div>
                <hr>
            </div>
        </div>
    </div>

    <script src="{{ asset('assets/js/popper.min.js') }}"></script>
    <script src="{{ asset('assets/js/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/js/bootstrap.min.js') }}"></script>
</body>

</html>
