<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport"
        content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ __('messages.credit_note.credit_note_pdf') }}</title>
    <link href="{{ asset('css/invoices/invoice-pdf.css') }}" rel="stylesheet" type="text/css" />
</head>

<body>
    <table class="main-table">
        <tr>
            <td class="app-logo">
                <img src="{{ $settings['logo'] }}" width="100px">
            </td>
            <td class="text-right invoice-number">
                <h2 class="text-uppercase">{{ __('messages.credit_note.credit_note') }}</h2>
                <p>{{ __('messages.credit_note.credit_note_prefix') }}{{ $creditNote->credit_note_number }}</p>
            </td>
        </tr>
        <tr>
            <td class="invoice-customer-detail">
                <p class="font-weight-bold m-0">{{ html_entity_decode($creditNote->customer->company_name) }}</p>
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
            </td>
            <td style="float:right;">
                <table width="100%">
                    <tr>
                        <td>
                            @foreach ($creditNote->creditNoteAddresses as $address)
                                <div class="invoice-addresses text-right mb-2">
                                    <p class="font-weight-bold m-0">
                                        {{ $address->type == 1 ? __('messages.invoice.bill_to') : __('messages.invoice.ship_to') }}
                                        :</p>
                                    <p class="m-0">{{ html_entity_decode($address->street) }}</p>
                                    <p class="m-0">{{ $address->city }}, {{ $address->state }}</p>
                                    <p class="m-0">{{ $address->country }}</p>
                                    <p class="m-0">{{ $address->zip_code }}</p>
                                </div>
                            @endforeach
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <table width="100%">
                                <tr>
                                    <td class="font-weight-bold">
                                        {{ __('messages.credit_note.credit_note_date') . ': ' }}
                                    </td>
                                    <td>{{ !empty($creditNote->credit_note_date)
                                        ? Carbon\Carbon::parse($creditNote->credit_note_date)->translatedFormat('jS M, Y')
                                        : __('messages.common.n/a') }}
                                    </td>
                                </tr>
                                @if (!empty($creditNote->reference))
                                    <tr>
                                        <td class=" font-weight-bold">{{ __('messages.credit_note.reference') . ': ' }}
                                        </td>
                                        <td>{{ !empty($creditNote->reference) ? html_entity_decode($creditNote->reference) : __('messages.common.n/a') }}
                                        </td>
                                    </tr>
                                @endif
                            </table>
                        </td>
            </td>
        </tr>
    </table>
    </td>
    </tr>
    <tr>
        <td colspan="2">
            <table width="100%" class="table table-bordered invoice-sales-items mt-2">
                <thead class="thead-light">
                    <tr>
                        <th scope="col" class="item-index">#</th>
                        <th scope="col" class="item-col">{{ __('messages.invoice.item') }}</th>
                        <th scope="col" class="text-right item-qty">{{ __('messages.invoice.qty') }}</th>
                        <th scope="col" class="text-right rate">{{ __('messages.products.rate') }}( <span
                                class="pdf-css">&#{{ getCurrencyIcon($creditNote->currency) }} </span>)
                        </th>
                        <th scope="col" class="item-taxes">{{ __('messages.invoice.taxes') }}</th>
                        <th scope="col" class="text-right total-amount">{{ __('messages.invoice.amount') }}( <span
                                class="pdf-css">&#{{ getCurrencyIcon($creditNote->currency) }} </span>)
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($creditNote->salesItems as $index => $item)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>
                                <p class="m-0">{{ html_entity_decode($item->item) }}</p>
                                <p class="text-muted m-0 table-data">
                                    <small>{{ html_entity_decode($item->description) }}</small>
                                </p>
                            </td>
                            <td class="text-right">{{ $item->quantity }}</td>
                            <td class="text-right">
                                <span class="pdf-css">&#{{ getCurrencyIcon($creditNote->currency) }}</span>
                                {{ number_format($item->rate, 2) }}
                            </td>
                            <td>
                                @forelse($item->taxes as $tax)
                                    <p><span
                                            class="badge badge-secondary font-weight-normal">{{ $tax->tax_rate }}%</span>
                                    </p>
                                @empty
                                    <p>{{ __('messages.common.n/a') }}</p>
                                @endforelse
                            </td>
                            <td class="text-right">
                                <span class="pdf-css">&#{{ getCurrencyIcon($creditNote->currency) }}</span>
                                {{ number_format($item->total, 2) }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </td>
    </tr>
    <tr>
        <td></td>
        <td class="float-right">
            <table class="table text-right invoice-footer-table mt-2">
                <tbody>
                    <tr>
                        <td>
                            {{ __('messages.invoice.sub_total') . ':' }}
                        </td>
                        <td>
                            <span class="pdf-css">&#{{ getCurrencyIcon($creditNote->currency) }}</span>
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
                                <span class="pdf-css">&#{{ getCurrencyIcon($creditNote->currency) }}</span>
                                {{ number_format($commonTax->amount, 2) }}
                            </td>
                        </tr>
                    @endforeach
                    <tr>
                        <td>{{ __('messages.invoice.adjustment') . ':' }}</td>
                        <td>
                            <span class="pdf-css">&#{{ getCurrencyIcon($creditNote->currency) }}</span>
                            {{ number_format($creditNote->adjustment) }}
                        </td>
                    </tr>
                    <tr>
                        <td>{{ __('messages.invoice.total') . ':' }}</td>
                        <td>
                            <span class="pdf-css">&#{{ getCurrencyIcon($creditNote->currency) }}</span>
                            {{ number_format($creditNote->total_amount, 2) }}
                        </td>
                    </tr>
                    <tr class="text-color">
                        <td>{{ __('messages.invoice.amount_due') . ':' }}</td>
                        <td>
                            <span class="pdf-css">&#{{ getCurrencyIcon($creditNote->currency) }}</span>
                            {{ number_format($creditNote->total_amount, 2) }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </td>
    </tr>
    <tr>
        <td colspan="2">
            <h4 class="mt-2">{{ __('messages.credit_note.client_note') . ':' }}</h4>
            {!! !empty($creditNote->client_note) ? html_entity_decode($creditNote->client_note) : __('messages.common.n/a') !!}
        </td>
    </tr>
    <tr>
        <td colspan="2">
            <h4 class="mt-2">{{ __('messages.credit_note.terms_and_conditions') . ':' }}</h4>
            {!! !empty($creditNote->term_conditions)
                ? html_entity_decode($creditNote->term_conditions)
                : __('messages.common.n/a') !!}
        </td>
    </tr>
    <tr>
        <td colspan="2">
            <p class="mt-2">{{ __('messages.credit_note.authorized_signature') . ' _________________' }}</p>
        </td>
    </tr>
    </table>
</body>

</html>
