@php
    $contentTitle = 'Tax';
    $footer = 'Footer Content';

    $format = $settings['print_format'] ?? 1;

    // Define the base path for different formats
    $baseImagePath = public_path('print/format_' . $format);

    // Company logo
    $imagePath = $baseImagePath . '/company_logo.png';
    $imageData = file_get_contents($imagePath);
    $base64 = base64_encode($imageData);
    $backgroundImage = 'data:image/png;base64,' . $base64; // Ensure correct format

    // Company header image
    $headerPath = $baseImagePath . '/header.jpg';
    $imageheader = file_get_contents($headerPath);
    $headerImage = base64_encode($imageheader);
    $headerImage = 'data:image/jpg;base64,' . $headerImage;

    // Company footer image
    $footerPath = $baseImagePath . '/footer.jpg';
    $imagefooter = file_get_contents($footerPath);
    $imagefooterPath = base64_encode($imagefooter);
    $footerImage = 'data:image/jpg;base64,' . $imagefooterPath;

    $trn_number = '1000230103000003';
@endphp

<!DOCTYPE html>
<html>

<head>
    <style>
        @page {
            size: A4;
            margin: 0;

        }

        body {
            font-family: 'Roboto', sans-serif;
            margin: 0;
            padding: 0;
            position: relative;
        }

        .background {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: url('{{ $backgroundImage }}');
            background-size: 90%;
            /* Cover to ensure full width */
            background-position: center;
            background-repeat: no-repeat;
            opacity: 0.15;
            /* 20% visibility */
            z-index: -1;
        }

        .header {
            position: absolute;
            top: 0px;
            left: 0;
            right: 0;
            text-align: center;
            z-index: 1;
            margin: 0;
            padding: 0;

        }

        .footer {
            position: fixed;
            bottom: 0px;
            left: 0;
            right: 0;
            text-align: center;
            z-index: 1;
            margin: 0;
            padding: 0;
            background: orange;
            /* Optional: space from bottom */
        }

        .content {
            margin: 0;
            /* Set body margin to 0 */
            padding: 0;
            /* Set body padding to 0 */
            margin: 0;
            /* Remove left and right margins */
            position: relative;
            z-index: 1;
            /* Bring content above background */
            padding-top: 130px;
            /* Space for header */
            padding-bottom: 60px;
            /* Space for footer */
            box-sizing: border-box;
            /* Include padding in height calculation */
            width: 100%;
            /* Set width to 100% */
        }

        h3 {
            margin: 10px 0;
        }

        .page-break {
            page-break-after: always;
            /* Ensure content starts on a new page */
        }

        .content-title {
            width: 150px;
            border: 2px solid #3b6b67;
            color: #f7996e;
            font-weight: bolder;
            text-align: center;
            margin: auto;
            margin-top: 45px;
            height: 36px;
            line-height: 6px;
            font-size: 20px;

        }

        .sales_info {
            width: 85%;
            border-collapse: collapse;
            font-size: 13px;

        }

        .sales_info tr {
            padding: 10px;
            /* Adjust the padding as needed */
        }



        .sales_info th,
        .sales_info td {
            text-align: left;
            padding: 3px;
        }

        .sales_table_data {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            font-size: 14px;
            /* Set font size */
        }

        .sales_table_data th,
        .sales_table_data td {
            border: 1px solid #848484;
            /* Border for table cells */
            padding: 0;
            /* Remove padding */
            margin: 0;
            /* Remove margin */

        }

        .sales_table_data th {
            /* Light background for header */
        }

        .sales_table_data tr:nth-child(even) {
            /* Zebra striping for even rows */
        }
    </style>
</head>

<body>
    <div class="background"></div>

    <div class="header">
        <img src="{{ $headerImage }}" style="width: 100%;height:150px; padding: 0px;">
    </div>

    <div class="content">
        <div class="content-title">
            <p>Credit Note</p>
        </div>
        <div style="width:100%;text-align:center; line-height: 5px;font-size:18px;font-weight:400;">
        </div>
        <div style="width: 100%;margin-top:30px;padding-left:10px;padding-right:10px;">
            <!-- Remove fixed height for responsive content -->
            <table class="sales_info">
                <tr >
                    <th>Invoice No.</th>
                    <th>Invoice Date</th>
                    <th>Credit Note No.</th>
                    <th colspan="2">Credit Note Date </th>
                </tr>
                <tr>
                    <td>{{ $creditNote->invoice_id }}</td>
                    <td>{{ \Carbon\Carbon::parse($creditNote->invoice_date)->format('d-m-Y') }}</td>
                    <td>{{ $creditNote->credit_note_number }}</td>
                    <td>{{ \Carbon\Carbon::parse($creditNote->credit_note_date)->format('d-m-Y') }}</td>
                </tr>
                <tr><br></tr>
                <tr>
                    <th>Invoice Month </th>
                    <th>Customer No.</th>
                    <th colspan="2">Customer Name</th>
                </tr>
                <tr>
                    <td>{{ \Carbon\Carbon::parse($creditNote->invoice->invoice_date)->format('M y') }}</td>
                    <td>{{ $creditNote->customer->code ?? 'N/A' }}</td>
                    <td colspan="2">{{ $creditNote->customer->company_name ?? 'N/A' }}</td>


                </tr>
                <tr><br></tr>
                <tr>
                    <th>Email</th>
                    <th>Address</th>
                    <th >Payment Mode</th>
                    <th colspan="2">VAT Number</th>
                </tr>
                <tr>
                    <td>{{ $creditNote->customer->email ?? 'N/A' }}</td>
                    <td>{{ $creditNote->project->project_location ?? 'N/A' }}</td>
                    <td>
                        @forelse($creditNote->invoice->paymentModes as $paymentMode)
                            {{ html_entity_decode($paymentMode->name) }}

                        @empty
                            {{ __('messages.common.n/a') }}
                        @endforelse
                    </td>
                    <td>{{ $creditNote->customer->vat_number ?? 'N/A' }}</td>
                </tr>
                <tr><br></tr>
                <tr>

                    <th>Project Code</th>
                    <th>Project Name</th>
                    <th>Project Location </th>
                     <th> P.0 Number </th>
                    <th>Address</th>
                </tr>
                <tr>
                    <td>{{ $creditNote->invoice->project->project_code ?? 'N/A' }}</td>
                    <td>{{ $creditNote->invoice->project->project_name ?? 'N/A' }}</td>
                    <td>{{ $creditNote->invoice->project->project_location ?? 'N/A' }}</td>
                    <td>{{ $creditNote->invoice->project->po_number ?? 'N/A' }}</td>
                    <td>
                        {{-- {{ dd($creditNote->toArray()) }} --}}
                        @if ($creditNote->creditNoteAddresses && $creditNote->creditNoteAddresses->isNotEmpty())
                            @foreach ($creditNote->creditNoteAddresses as $address)
                                <div style="margin-top: 5px;">
                                    {{ html_entity_decode($address->street) .
                                        ', ' .
                                        $address->city .
                                        ', ' .
                                        $address->state .
                                        ', ' .
                                        $address->country .
                                        ', ' .
                                        $address->zip_code }}
                                </div>
                            @endforeach
                        @else
                            <div style="margin-top: 5px;">
                                {{ $creditNote->customer['address'] ?? ('' . ', ' . $creditNote->customerCountry['name'] ?? '') }}
                            </div>
                        @endif
                    </td>
                </tr>
                <tr><br></tr>
                <tr>


                    <th>Remarks</th>
                </tr>
                <tr>
                    <td>{!! !empty($creditNote->admin_text) ? html_entity_decode($creditNote->admin_text) : __('messages.common.n/a') !!}</td>
                </tr>
            </table>
            <table class="sales_table_data"
                style="width: 96.7%; border-collapse: collapse; margin-top: 20px; font-size: 12px;">
                <thead>
                    <tr>
                        <th style="border: 1px solid #848484;">SL</th>
                        <th style="border: 1px solid #848484; text-align:center; padding:6px;">Item<br>Code
                        </th>
                        {{-- <th style="border: 1px solid #848484; text-align:center; padding:6px;">Category<br>Name
                        </th> --}}
                        <th style="border: 1px solid #848484;  text-align:center; padding:6px;">Service<br> Name
                        </th>
                        <th style="border: 1px solid #848484;  text-align:center;">Unit<br>Qty</th>
                        <th style="border: 1px solid #848484;  text-align:center; padding:6px;width:10%;">Unit<br>Rate
                        </th>
                        <th style="border: 1px solid #848484; text-align:center; padding:6px;">Excluding <br>VAT</th>
                        <th style="border: 1px solid #848484; width: 8%; text-align:center; padding:6px;">VAT<br>%
                        </th>
                        <th style="border: 1px solid #848484; width: 11%; text-align:center; padding:6px;">
                            VAT<br>Amount
                        </th>
                        <th style="border: 1px solid #848484; width: 12%; text-align:center; padding:6px;">
                            Including<br>VAT</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $vat_amount = 0;
                        // dd($creditNote->salesItems->toArray());
                    @endphp
                    @foreach ($creditNote->salesItems as $index => $item)
                        @php
                            $vatAmount =
                                $item->quantity * $item->rate * (1 - $item->discount / 100) * ($item->tax / 100);
                            $includingVat = $item->quantity * $item->rate + $vatAmount;
                        @endphp
                        <tr>
                            <td style="border: 1px solid #848484; text-align:center; width: 25px;">
                                {{ $index + 1 }}</td>
                            <td
                                style="border: 1px solid #848484; padding: 2px; text-align:left; padding-left:2%; width:70px;">
                                {{ $item->item }}</td>
                            {{-- <td
                                style="border: 1px solid #848484; padding: 4px; text-align:left; padding-left:2%;width: 110px; word-wrap: break-word; word-break: break-all;">
                                {{ $item->category->name ?? '' }}</td> --}}
                            <td
                                style="border: 1px solid #848484; padding: 2px; text-align:left; padding-left:1%;width: 340px; word-wrap: break-word; word-break: break-all;">
                                {{ html_entity_decode($item->service->title ?? '') }}</td>

                            <td style="border: 1px solid #848484; text-align:center;width:40px;">
                                {{ $item['quantity'] }}</td>
                            <td style="border: 1px solid #848484; text-align:right; padding-right:8%">
                                {{ number_format($item['rate'], 2) }}</td>
                            <td style="border: 1px solid #848484; text-align:right; padding-right:5%;width:60px;">
                                {{ number_format($item['quantity'] * $item['rate'], 2) }}</td>
                            <!-- Excluding VAT Amount -->
                            <td style="border: 1px solid #848484; text-align:right; padding-right:5%">
                                {{ number_format($item->tax, 2) }}
                            </td>
                            <td style="border: 1px solid #848484; text-align:right; padding-right:5%">

                                {{ number_format($vatAmount, 2) }} <!-- VAT Amount -->
                            </td>
                            <td style="border: 1px solid #848484; text-align:right; padding-right:5%">
                                {{ number_format($includingVat, 2) }}
                                <!-- Including VAT -->
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>



            <table
                style=" width: 97.5%;
            border-collapse: collapse;
            margin-top: 10px;
            font-size: 14px;">
                <tr>
                    <!-- Left Section: Two Divs -->
                    <td style="width: 45%; vertical-align: top; padding-right: 20px;">
                        <!-- Top Div -->
                        <div style=" padding: 0px; margin-bottom: 20px;">
                            <strong> Amount In words</strong><br>
                            {{ ucfirst($words) }} Only
                        </div>

                        @if ($creditNote->terms && $creditNote->terms->isNotEmpty())

                            <div style="font-family: Arial, sans-serif;">
                                <strong style="font-size: 18px;">Terms & Conditions</strong><br>
                                <div class="mt-3">
                                    <table style="width: 100%; border-collapse: collapse; margin-top: 10px;">
                                        {{-- <thead>
                                            <tr>
                                                <th style="border: 1px solid #dee2e6; padding: 8px; text-align: left;">
                                                    SL
                                                </th>
                                                <th style="border: 1px solid #dee2e6; padding: 8px; text-align: left;">
                                                    Description
                                                </th>
                                            </tr>
                                        </thead> --}}
                                        <tbody>
                                            <!-- Loop through the terms and display them -->
                                            @foreach ($creditNote->terms as $index => $estimateTerm)
                                                <tr>
                                                    <td style="border: 1px solid #dee2e6; padding: 8px;">
                                                        {{ $index + 1 }}</td>
                                                    <td style="border: 1px solid #dee2e6; padding: 8px;">
                                                        {{ $estimateTerm['description'] }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endif

                    </td>

                    <!-- Right Section: Calculation Table -->
                    <td style="width: 25%; vertical-align: top;">
                        <table style="width: 100%; border-collapse: collapse; font-size: 13px;">
                            <tr>
                                <td class="font-weight-bold"
                                    style="border: 1px solid #848484; padding: 5px; text-align: right;">
                                    <strong>Total Before Discount</strong>
                                </td>
                                <td style="border: 1px solid #848484; padding: 5px; text-align: right;">
                                    {{ number_format($totalIncludingVAT, 2) }} SAR
                                </td>
                            </tr>


                            <tr>
                                <th class="font-weight-bold"
                                    style="border: 1px solid #848484; padding: 5px; text-align: right;">Discount
                                    {{ isset($creditNote->discount_type) ? ($creditNote->discount_type == 0 ? '%' : '$') : ' ' }}
                                </th>
                                <td class="text-right p-1"
                                    style="border: 1px solid #848484; padding: 5px; text-align: right;">
                                    {{ number_format($creditNote->discount ?? 0, 2) }}</td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold"
                                    style="border: 1px solid #848484; padding: 5px; text-align: right;">
                                    <strong>Total After Discount</strong>
                                </td>
                                <td style="border: 1px solid #848484; padding: 5px; text-align: right;">
                                    {{ number_format($afterDiscount, 2) }} SAR
                                </td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold"
                                    style="border: 1px solid #848484; padding: 5px; text-align: right;">
                                    <strong>Total VAT</strong>
                                </td>
                                <td style="border: 1px solid #848484; padding: 5px; text-align: right;">
                                    {{ number_format($totalVATAmount, 2) }} SAR
                                </td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold"
                                    style="border: 1px solid #848484; padding: 5px; text-align: right;">
                                    <strong>Net Total </strong>
                                </td>
                                <td style="border: 1px solid #848484; padding: 5px; text-align: right;">
                                    {{ number_format($newTotal, 2) }} SAR
                                </td>
                            </tr>
                        </table>

                    </td>

                </tr>
            </table>

        </div>

    </div>

    <div class="footer">
        <img src="{{ $footerImage }}" style="width: 100%; padding: 0px;">
    </div>
</body>

</html>
