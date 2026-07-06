<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport"
        content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ __('messages.estimate.estimate_pdf') }}</title>

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>A4 Sized Page</title>
        <style>
            @page {
                size: A4;
                margin: 20mm;
            }

            body {
                font-family: Arial, sans-serif;
                margin: 0;
                padding: 0;
            }

            .header {
                display: flex;
                justify-content: space-between;
                align-items: center;
                width: 100%;
                margin-bottom: 20px;
            }

            .header img {
                width: 20%;
            }

            .header-content {
                text-align: center;
                width: 65%;
            }

            .table-bordered {
                width: 100%;
                border-collapse: collapse;
                margin-top: 5px;
            }

            th,
            td {
                border: 1px solid #dee2e6;

                text-align: left;
            }

            th {
                background-color: #f8f9fa;
                font-weight: bold;
            }

            .text-right {
                text-align: right;
            }

            .amount-summary {
                display: flex;
                justify-content: space-between;
                margin-top: 20px;
            }

            .summary-section {
                width: 30%;
            }

            .thank-you {
                text-align: center;
                margin: 20px 0;
            }

            .address-section {
                margin: 10px 0;
            }

            /* Flex layout for amount summary */
            .summary-container {
                display: flex;
                justify-content: space-between;
            }

            .summary-left {
                width: 45%;
            }

            .summary-center {
                width: 10%;
                text-align: center;
            }

            .summary-right {
                width: 45%;
            }

            .bank-details {
                margin-top: 0px;
                padding-top: 10px;
            }
        </style>
    </head>

<body>
    <table style="width: 100%; table-layout: fixed;">
        <tr>
            <td style="width: 30%;">
                <img src="data:image/png;base64,{{ $com_logo }}" style="width: 60%;padding:10%;">
            </td>
            <td style="width: 100%;">
                <h4 style="font-weight: bold; text-align:center;">Manara for General Contracting Est.</h4>
                <p style="text-align:center;">
                    C.R. 4031251746 &nbsp; | &nbsp; Mobile: +966 562015468 <br>
                    Email: <a href="mailto:info@manaraest.com">info@manaraest.com</a> &nbsp; | &nbsp;
                    Website: <a href="https://www.manaraest.com" target="_blank">www.manaraest.com</a>
                </p>
                <h2 style="font-weight: bold;text-align:center;">{{ __('messages.estimate.estimate') }}</h2>
            </td>
        </tr>
    </table>
    <br>
    <table class="table table-bordered">
        <tbody>
            <tr>
                <td>
                    <strong>{{ __('messages.estimate.customer_name') }}:</strong><br>
                    {{ html_entity_decode($estimate->customer_name ?? '') }}
                </td>
                <td>
                    <strong>{{ __('messages.estimate.reference') }}:</strong><br>
                    {{ html_entity_decode($estimate->reference ?? '') }}
                </td>
                <td>
                    <strong>{{ __('messages.estimate.estimate_number') }}:</strong><br>
                    {{ $estimate->estimate_number }}
                </td>
                <td>
                    <strong>{{ __('messages.estimate.estimate_date') }}:</strong><br>
                    {{ Carbon\Carbon::parse($estimate->estimate_date)->translatedFormat('d-m-Y') }}
                </td>
            </tr>
            <tr>
                <td>
                    <strong>{{ __('messages.company.mobile') }}:</strong><br>
                    {{ !empty($estimate->mobile) ? html_entity_decode($estimate->mobile) : __('messages.common.n/a') }}
                </td>
                <td>
                    <strong>{{ __('messages.company.address') }}:</strong><br>
                    {{ !empty($estimate->address) ? html_entity_decode($estimate->address) : __('messages.common.n/a') }}
                </td>
                <td>
                    <strong>{{ __('messages.estimate.email') }}:</strong><br>
                    {{ !empty($estimate->email) ? html_entity_decode($estimate->email) : __('messages.common.n/a') }}
                </td>
                <td>
                    <strong>{{ __('messages.estimate.expiry_date') }}:</strong><br>
                    {{ isset($estimate->estimate_expiry_date) ? Carbon\Carbon::parse($estimate->estimate_expiry_date)->translatedFormat('d-m-Y') : __('messages.common.n/a') }}
                </td>
            </tr>
        </tbody>
    </table>

    <br>
    <table style="width: 100%;">
        <tr>
            <td colspan="2">

                <div class="table-responsive"> <!-- Responsive wrapper for the table -->
                    <table width="100%" class="table table-bordered invoice-sales-items mt-2">
                        <thead>
                            <tr>
                                <th>{{ __('messages.estimate.sl') }}</th>
                                <th>{{ __('messages.department.departments') }}</th>
                                <th class="text-right itemRate">{{ __('messages.estimate.rate') }}</th>
                                <th class="text-right itemTotal">Remarks</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($employeeQuotations as $quotation)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>
                                        <div style="min-width:150px;">
                                            {{ $quotation->employee->name ?? '' }}
                                        </div>
                                    </td>
                                    <td class="text-right">{{ $quotation['rate'] }}</td>
                                    <td class="text-right">{{ $quotation['remarks'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </td>
        </tr>


    </table>
    <br>
    <table>
        <tr>
            <td colspan="2">
                <h5 class="mt-2">{{ __('messages.estimate.terms_conditions') . ':' }}</h5>
                {!! !empty($estimate->term_conditions)
                    ? html_entity_decode($estimate->term_conditions)
                    : __('messages.common.n/a') !!}
            </td>
        </tr>
    </table>
    <br>
    <table style="width: 100%;">
        <tbody>
            <tr>
                <td style="text-align: center;">
                    <p><strong>THANK YOU FOR YOUR BUSINESS!!</strong></p>
                    <p>
                        Makkah Al Mukarramah . Omaima Bint Al Khattab Street . Tel: 012538109 .<br>
                        P.O.Box 3177 Code 24241 . Kingdom of Saudi Arabia
                    </p>
                </td>
            </tr>
        </tbody>
    </table>
</body>

</html>
