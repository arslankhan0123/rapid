@php

    $contentTitle = 'Tax';
    $footer = 'Footer Content';

    $format = $settings['print_format'] ?? 1;

    if ($settings['print_format'] == 1) {
        //smit

        $com_name = 'شركة سهم المنار للتجارة والمقاولات';
        $trn_number = 311204277500003;
    } else {
        //ran

        $com_name = 'مختبر راية  النجاح لمواد البناء';
        $trn_number = 310429743800003;
    }

    $com_name = $settings['company'];
    $trn_number = $settings['vat_number'];

    $bgColor = '#fff7f2';
    $bColor = '#e2e2e2';

@endphp

<body>

    <head>

        <head>
            <style>
                .content_info {
                    padding-left: 5px;
                    padding-right: 5px;
                    padding-bottom: 10px;
                }

                .content_info table {
                    border: 1px solid {{ $bColor }};
                    /* Set border color */
                    border-collapse: collapse;


                }

                .content_info table th,
                .content_info table td {
                    border: 1px solid {{ $bColor }};
                    /* Set border color for cells */
                    padding: 5px;
                    /* Adjust padding for cells */
                    border: 1px solid {{ $bColor }};
                }

                .content_info table td:nth-child(odd) {
                    background: {{ $bgColor }};
                    /* Background color for odd rows' first cell */
                }

                .content_items {
                    font-size: 8pt;
                    width: 100%;
                    padding-left: 5px;
                    padding-right: 5px;

                    /* Ensures the container takes full width */
                }

                .sales_table_data {
                    width: 100%;
                    /* Makes the table take full width */
                    border-collapse: collapse;

                    border: 1px solid {{ $bColor }};
                    /* Ensures borders collapse for cleaner layout */
                }

                .sales_table_data th {
                    background: {{ $bgColor }};
                    /* Set the background color for table headers */
                    padding: 8px;
                    /* Add padding to table header cells */
                    text-align: center;
                    border: 1px solid {{ $bColor }};
                }

                .sales_table_data td {
                    padding: 8px;
                    border: 1px solid {{ $bColor }};
                    /* Add padding to table data cells */
                }

                .bColor {
                    border: .05cm solid #e2e2e2 !important;
                }

                .bgColor {
                    background: #fff7f2 !important;
                }

                .page-break {
                    page-break-after: always;
                    /* Ensure content starts on a new page */
                }
            </style>
        </head>
        <main>
            <div class="content_info">
                <table cellspacing="0" style="width: 100%; border-collapse: collapse;font-size:11px;line-height:9px;">

                    <!-- ROW 1 -->
                    <tr>
                        <td style="width: 2.3cm;">Quote No <br> رقم عرض السعر</td>
                        <td style="width: 1.22cm;">{{ $estimate->estimate_number }}</td>

                        <td style="width: 2cm;">Quote Date<br>تاريخ عرض السعر</td>
                        <td style="width: 2cm;">{{ \Carbon\Carbon::parse($estimate->estimate_date)->format('d-m-Y') }}
                        </td>

                        <td style="width: 2.6cm;">Quote Validity<br>صلاحية عرض السعر</td>
                        <td style="width: 3cm;">{{ \Carbon\Carbon::parse($estimate->estimate_date)->format('d-m-Y') }}
                        </td>

                        <td style="width: 2.8cm;">Cust. Reference<br>مرجع العملاء</td>
                        <td style="width:1.4cm;">{{ $estimate->reference }}</td>
                    </tr>

                    <!-- ROW 2 -->
                    <tr>
                        <td style="width: 2cm;">Cust. Name <br> اسم العميل</td>
                        <td colspan="3">{!! $estimate->customer->company_name ?? 'N/A' !!}</td>

                        <td style="width: 2cm;">Email<br>بريد إلكتروني</td>
                        <td style="width: 1.9cm;">{{ $estimate->email ?? 'N/A' }}</td>

                        <td>Address<br>العنوان</td>
                        <td style="width:2cm;">
                            @if ($estimate->estimateAddresses && $estimate->estimateAddresses->isNotEmpty())
                                @foreach ($estimate->estimateAddresses as $address)
                                    <div style="word-wrap: break-word;">
                                        {{ implode(', ', array_filter([$address->street, $address->city, $address->state, $address->country, $address->zip_code])) }}
                                    </div>
                                @endforeach
                            @else
                                <div style="word-wrap: break-word;">{{ $estimate->customer['address'] ?? 'N/A' }}</div>
                            @endif
                        </td>
                    </tr>

                    <!-- ROW 3 -->
                    <tr>
                        <td style="width:2cm;">Subject<br>موضوع</td>
                        <td colspan="3">{{ $estimate->admin_note ?? '' }}</td>

                        <td>Quality No<br>رقم الجودة</td>
                        <td style="width:1.9cm;">{{ $estimate->quality_no ?? '' }}</td>

                        <td>Branch<br>فرع</td>
                        <td>{{ $estimate->branch?->name ?? '' }}</td>
                    </tr>

                    <!-- NOTES ROW -->
                    <tr>
                        <td colspan="8" style="background: white;">{{ $estimate->client_note ?? '' }}</td>
                    </tr>

                </table>
            </div>




            <div class="content_items">
                <table class="sales_table_data" style="font-size: 12px;line-height:12px;">
                    <thead>
                        <tr style="padding: 0px;">
                            <th style="width: 5%;">S.<br>الرقم</th>
                            <th style="width: 9%;">Item<br>رقم الصنف</th>
                            <th class="text-center" style="width: 25%;">Description<br>الوصف</th>
                            <th style="width: 6%;">Qty<br>الكيمة</th>
                            <th class="text-center pr-3" style="width: 10%;">Rate<br>السعر</th>
                            <th style="text-align:center;width: 7%;" class="p-0 pr-1">Disc.<br>خصم</th>
                            <th class="p-0 text-center pr-2 w-20" style="width: 15%;">Amount<br>الإجمالي </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($estimate->salesItems as $index => $item)
                            @php
                                // $netAmount = $item->quantity * $item->rate - $item->discount;
                                $netAmount = $item->quantity * $item->rate;
                            @endphp
                            <tr>
                                <td style=" text-align:center; width: .70cm;">
                                    {{ $index + 1 }}</td>
                                <td style=" padding: 2px; text-align:left; padding-left:5px; width:1.10cm;">
                                    {{ $item->item }}</td>
                                <td
                                    style=" padding: 2px; text-align:left; padding-left:1%;width:6.49cm; word-wrap: break-word; word-break: break-all;">

                                    <?php if (strlen($item->headline ?? '')) {
                                        echo "<b style='color: darkorange;'>" . $item->headline . '</b>' . '<br>';
                                    } ?>
                                    <?php if (strlen($item->service->title ?? '')) {
                                        echo "<b style='color: black;'>" . $item->service->title . '</b>' . '<br>';
                                    } ?>
                                    <?php if (strlen($item->subheadline ?? '')) {
                                        echo $item->subheadline;
                                    } ?>

                                </td>
                                <td style=" text-align:center;width:.96cm;">
                                    {{ $item['quantity'] }}</td>
                                <td style="text-align:right; width:1.50cm;">
                                    {{ number_format($item['rate'], 2) }}</td>
                                <td style=" text-align:right;width:1.30cm;">
                                    {{ number_format($item['discount'], 2) }}</td>
                                <td style=" text-align:right; padding-right:.5%">
                                    {{ number_format($netAmount, 2) }}
                                    <!-- Net Amount -->
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{-- <div class="content_items">
                <table class="sales_table_data" style="font-size: 12px;line-height:12px;">
                    <thead>
                        <tr style="padding: 0px;">
                            <th style="width: 5%;">S.<br>الرقم</th>
                            <th style="width: 9%;">Item<br>رقم الصنف</th>
                            <th class="text-center" style="width: 18%;">Description<br>الوصف</th>
                            <th style="width: 6%;">Qty<br>الكيمة</th>
                            <th class="text-center pr-3" style="width: 10%;">Rate<br>السعر</th>
                            <th style="text-align:center;width: 7%;" class="p-0 pr-1">Disc.<br>خصم</th>
                            <th style="text-align: center; width: 10%;" class="p-0 pr-1">Taxable<br>للضريبة</th>
                            <th style="text-align: center;width: 6%;" class="p-0 pr-1">Vat %<br>ضريبة</th>
                            <th style="text-align: center;width: 6%;" class="pr-1">Vat <br>قيمة </th>
                            <th class="p-0 text-center pr-2 w-20" style="width: 11%;">Amount<br>الإجمالي </th>
                        </tr>
                    </thead>
                    <tbody>

                        @foreach ($estimate->salesItems as $index => $item)
                            @php
                                $befoPrice = $item->quantity * $item->rate - $item->discount; // After discount
                                $vatAmount = $befoPrice * ($item->tax / 100); // Apply tax on the net price
                                $netPrice = $befoPrice + $vatAmount;

                            @endphp
                            <tr>
                                <td style=" text-align:center; width: .70cm;">
                                    {{ $index + 1 }}</td>
                                <td style=" padding: 2px; text-align:left; padding-left:5px; width:1.10cm;">
                                    {{ $item->item }}</td>
                                <td
                                    style=" padding: 2px; text-align:left; padding-left:1%;width:6.49cm; word-wrap: break-word; word-break: break-all;">

                                    <?php if (strlen($item->headline ?? '')) {
                                        echo "<b style='color: darkorange;'>" . $item->headline . '</b>' . '<br>';
                                    } ?>
                                    <?php if (strlen($item->service->title ?? '')) {
                                        echo "<b style='color: black;'>" . $item->service->title . '</b>' . '<br>';
                                    } ?>
                                    <?php if (strlen($item->subheadline ?? '')) {
                                        echo $item->subheadline;
                                    } ?>

                                </td>


                                <td style=" text-align:center;width:.96cm;">
                                    {{ $item['quantity'] }}</td>
                                <td style="text-align:right; width:1.50cm;">
                                    {{ number_format($item['rate'], 2) }}</td>
                                <td style=" text-align:right;width:1.30cm;">
                                    {{ number_format($item['discount'], 2) }}</td>
                                <td style=" text-align:right;width:1.30cm;">
                                    {{ number_format($item['quantity'] * $item['rate'] - $item['discount'], 2) }}</td>
                                <!-- Excluding VAT Amount -->
                                <td style=" text-align:right; width:1.50cm;">
                                    {{ (int) $item->tax }}%
                                </td>
                                <td style=" text-align:right; padding-right:.5%">
                                    {{ number_format($vatAmount, 2) }} <!-- VAT Amount -->
                                </td>
                                <td style=" text-align:right; padding-right:.5%">
                                    {{ number_format($netPrice, 2) }}
                                    <!-- Including VAT -->
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div> --}}
            <div class="content_calculation" style="margin-top:10px;">
                <table cellspacing="0" cellpadding="5" style="width: 100%; border-collapse: collapse;font-size:12px;">
                    <tr>
                        <!-- First Table -->
                        <td style="width: 50%; vertical-align: top;text-align:left;">
                            <table cellspacing="0" cellpadding="5" style=" border-collapse: collapse;">

                                <tr>
                                    <td
                                        style="width: 9.5cm; background: {{ $bgColor }}; border: 1px solid {{ $bColor }};">
                                        <table style="width: 100%; border-collapse: collapse;">
                                            <tr>
                                                <td style="text-align: left; font-size: 10pt;">
                                                    <strong>Amount In Words</strong>
                                                </td>
                                                <td style="text-align: right; font-size: 10pt;">
                                                    <strong> مبلغ بالكتابة</strong>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>


                                    <td>
                                </tr>
                                <tr>
                                    <td style=" border: 1px solid {{ $bColor }};width: 9.5cm;">
                                        {{ ucfirst($words) }} Only
                                    </td>
                                </tr>
                                <tr>
                                    <td style="border: 1px solid {{ $bColor }};text-align:right;width: 9.5cm;">
                                        {{ ucfirst($wordsAr) }}
                                    </td>
                                </tr>
                                <tr>
                                    <td
                                        style="width: 9.5cm; background: {{ $bgColor }}; border: 1px solid {{ $bColor }};">
                                        <table style="width: 100%; border: none;">
                                            <tr>
                                                <td style="width: 50%; text-align: left; padding: 0px;">
                                                    <strong>Bank Details</strong>
                                                </td>
                                                <td style="width: 50%; text-align: right; padding: 0px;">
                                                    <strong> تفاصيل البنك</strong>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>

                                    <td>
                                </tr>
                                <tr>
                                    <td
                                        style="width: 9.5cm;   border: 1px solid {{ $bColor }};height:2cm;padding:0px;">

                                        <table
                                            style="width: 100%; border-collapse: collapse; font-family: Arial, sans-serif;">
                                            <tr>
                                                <!-- Left side: 60% width -->
                                                <td style="width: 70%; border-right: 1px solid {{ $bColor }};">
                                                    <table style="width: 100%; border-collapse: collapse;">
                                                        <tr>
                                                            <!-- Row 1 -->
                                                            <td
                                                                style="width: 30%; padding: 4px; border-bottom: 1px solid {{ $bColor }};border-right:1px solid {{ $bColor }};">
                                                                Bank Name</td>
                                                            <td
                                                                style="width: 70%; padding: 4px; border-bottom: 1px solid {{ $bColor }};">
                                                                {{ $estimate->branch?->bank?->name ?? '' }}</td>
                                                        </tr>
                                                        <tr>
                                                            <!-- Row 2 -->
                                                            <td
                                                                style="width: 30%; padding: 4px; border-bottom: 1px solid {{ $bColor }};border-right:1px solid {{ $bColor }};">
                                                                A/C No.</td>
                                                            <td
                                                                style="width: 70%; padding: 4px; border-bottom: 1px solid {{ $bColor }};">
                                                                {{ $estimate->branch?->bank?->account_number ?? '' }}
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <!-- Row 2 -->
                                                            <td
                                                                style="width: 30%; padding: 4px; border-bottom: 1px solid {{ $bColor }};border-right:1px solid {{ $bColor }};">
                                                                IBAN</td>
                                                            <td
                                                                style="width: 70%; padding: 4px; border-bottom: 1px solid {{ $bColor }};">
                                                                {{ $estimate->branch?->bank?->iban_number ?? '' }}
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <!-- Row 2 -->
                                                            <td
                                                                style="width: 30%; padding: 4px; border-right:1px solid {{ $bColor }};">
                                                                Address</td>
                                                            <td style="width: 70%; padding: 4px;">
                                                                {{ $estimate->branch?->bank?->address ?? '' }}
                                                            </td>
                                                        </tr>

                                                    </table>
                                                </td>

                                                <!-- Middle column: 1px wide (no border in the middle here) -->
                                                <td style="width: 1px;"></td>

                                                <!-- Right side: 40% width -->
                                                {{-- <td style="width: 30%; ">
                                                    @php
                                                        use Salla\ZATCA\GenerateQrCode;
                                                        use Salla\ZATCA\Tags\Seller;
                                                        use Salla\ZATCA\Tags\TaxNumber;
                                                        use Salla\ZATCA\Tags\InvoiceDate;
                                                        use Salla\ZATCA\Tags\InvoiceTotalAmount;
                                                        use Salla\ZATCA\Tags\InvoiceTaxAmount;

                                                        $qr_seller_trn = $trn_number;
                                                        $qr_tax_amount = $totalVat;

                                                        $qr_invoice_amount = $estimate->total_amount ?? 0;

                                                        $qr_code = null;
                                                        $qr_seller_name = $com_name;

                                                        $qr_invoice_date = !empty($estimate->estimate_date)
                                                            ? date('Y-m-d', strtotime($estimate->estimate_date))
                                                            : null;

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
                                                        <img style="width: 3.6cm;height:2.2cm;"
                                                            src="{{ $qr_code }}" alt="QR Code" />
                                                    @endif
                                                </td> --}}
                                            </tr>
                                        </table>



                                        <div style="width: 30%;float:right;">

                                        </div>
                                    </td>

                                    <td>

                                </tr>

                            </table>

                        </td>

                        <!-- Second Table -->
                        {{-- <td style="width:50%; vertical-align: top;text-align:right;">
                            <table style=" border-collapse: collapse; font-size: 12px;">
                                <tr>
                                    <td class="font-weight-bold bgColor"
                                        style="border: 1px solid {{ $bColor }}; padding: 6px; text-align: right;">
                                        <strong>Subtotal</strong>
                                    </td>
                                    <td
                                        style="border: 1px solid {{ $bColor }}; padding: 7px; text-align: right;">
                                        {{ number_format($subtotal, 2) }}
                                    </td>
                                    <td class="font-weight-bold bgColor"
                                        style="border: 1px solid {{ $bColor }}; padding: 6px; text-align: right;">
                                        <strong> <span>المجموع الفرعي</span></strong>
                                    </td>
                                </tr>

                                <tr>
                                    <th class="font-weight-bold bgColor"
                                        style="border: 1px solid {{ $bColor }}; padding: 7px; text-align: right;">
                                        Total Discount
                                    </th>
                                    <td class="text-right p-1"
                                        style="border: 1px solid {{ $bColor }}; padding: 7px; text-align: right;">
                                        {{ number_format($estimate->discount ?? 0, 2) }}
                                    </td>
                                    <td class="font-weight-bold bgColor"
                                        style="border: 1px solid {{ $bColor }}; padding: 6px; text-align: right;">
                                        <strong> <span>المجموع الخصم </span></strong>
                                    </td>
                                </tr>

                                <!-- Add VAT Row -->
                                <tr>
                                    <td class="font-weight-bold bgColor"
                                        style="border: 1px solid {{ $bColor }}; padding: 7px; text-align: right;">
                                        <strong>VAT ({{ $vatRate }}%)</strong>
                                    </td>
                                    <td
                                        style="border: 1px solid {{ $bColor }}; padding: 7px; text-align: right;">
                                        {{ number_format($vatAmount, 2) }}
                                    </td>
                                    <td class="font-weight-bold bgColor"
                                        style="border: 1px solid {{ $bColor }}; padding: 6px; text-align: right;">
                                        <strong> <span>ضريبة القيمة المضافة ({{ $vatRate }}%)</span></strong>
                                    </td>
                                </tr>

                                @if ($isRound)
                                    <tr>
                                        <td class="font-weight-bold bgColor"
                                            style="border: 1px solid {{ $bColor }}; padding: 7px; text-align: right;">
                                            <strong>Round Off</strong>
                                        </td>
                                        <td
                                            style="border: 1px solid {{ $bColor }}; padding: 7px; text-align: right;">
                                            {{ number_format($estimate->adjustment, 2) }}
                                        </td>
                                        <td class="font-weight-bold bgColor"
                                            style="border: 1px solid {{ $bColor }}; padding: 6px; text-align: right;">
                                            <strong> <span>تقريب</span></strong>
                                        </td>
                                    </tr>
                                @endif

                                <tr>
                                    <td class="font-weight-bold bgColor"
                                        style="border: 1px solid {{ $bColor }}; padding: 7px; text-align: right;">
                                        <strong>Net Amount</strong>
                                    </td>
                                    <td
                                        style="border: 1px solid {{ $bColor }}; padding: 7px; text-align: right;">
                                        {{ number_format($netAmountAfterVat, 2) }}
                                    </td>
                                    <td class="font-weight-bold bgColor"
                                        style="border: 1px solid {{ $bColor }}; padding: 6px; text-align: right;">
                                        <strong> <span>المجموع الضافي</span></strong>
                                    </td>
                                </tr>
                            </table>
                        </td> --}}
                        <!-- Second Table -->
                        {{-- <td style="width:50%; vertical-align: top;text-align:right;">
                            <table style=" border-collapse: collapse; font-size: 12px;">
                                <tr>
                                    <td class="font-weight-bold bgColor"
                                        style="border: 1px solid {{ $bColor }}; padding: 6px; text-align: right;">
                                        <strong>Subtotal</strong>
                                    </td>
                                    <td
                                        style="border: 1px solid {{ $bColor }}; padding: 7px; text-align: right;">
                                        {{ number_format($subtotal, 2) }}
                                    </td>
                                    <td class="font-weight-bold bgColor"
                                        style="border: 1px solid {{ $bColor }}; padding: 6px; text-align: right;">
                                        <strong> <span>المجموع الفرعي</span></strong>
                                    </td>
                                </tr>

                                <tr>
                                    <th class="font-weight-bold bgColor"
                                        style="border: 1px solid {{ $bColor }}; padding: 7px; text-align: right;">
                                        Total Discount
                                    </th>
                                    <td class="text-right p-1"
                                        style="border: 1px solid {{ $bColor }}; padding: 7px; text-align: right;">
                                        {{ number_format($estimate->discount ?? 0, 2) }}
                                    </td>
                                    <td class="font-weight-bold bgColor"
                                        style="border: 1px solid {{ $bColor }}; padding: 6px; text-align: right;">
                                        <strong> <span>المجموع الخضم </span></strong>
                                    </td>
                                </tr>

                                @if ($isRound)
                                    <tr>
                                        <td class="font-weight-bold bgColor"
                                            style="border: 1px solid {{ $bColor }}; padding: 7px; text-align: right;">
                                            <strong>Round Off</strong>
                                        </td>
                                        <td
                                            style="border: 1px solid {{ $bColor }}; padding: 7px; text-align: right;">
                                            {{ number_format($estimate->adjustment, 2) }}
                                        </td>
                                        <td class="font-weight-bold bgColor"
                                            style="border: 1px solid {{ $bColor }}; padding: 6px; text-align: right;">
                                            <strong> <span>تقريب</span></strong>
                                        </td>
                                    </tr>
                                @endif

                                <tr>
                                    <td class="font-weight-bold bgColor"
                                        style="border: 1px solid {{ $bColor }}; padding: 7px; text-align: right;">
                                        <strong>Net Amount</strong>
                                    </td>
                                    <td
                                        style="border: 1px solid {{ $bColor }}; padding: 7px; text-align: right;">
                                        {{ number_format($estimate->total_amount ?? 0, 2) }}
                                    </td>
                                    <td class="font-weight-bold bgColor"
                                        style="border: 1px solid {{ $bColor }}; padding: 6px; text-align: right;">
                                        <strong> <span>المجموع الضافى</span></strong>
                                    </td>
                                </tr>
                            </table>
                        </td> --}}

                        <!-- Second Table -->
                        <td style="width:50%; vertical-align: top;text-align:right;">
                            <table style=" border-collapse: collapse; font-size: 12px;">
                                <tr>
                                    <td class="font-weight-bold bgColor"
                                        style="border: 1px solid {{ $bColor }}; padding: 6px; text-align: right;">
                                        <strong>Subtotal</strong>
                                    </td>
                                    <td
                                        style="border: 1px solid {{ $bColor }}; padding: 7px; text-align: right;">
                                        {{ number_format($subtotal, 2) }}
                                    </td>
                                    <td class="font-weight-bold bgColor"
                                        style="border: 1px solid {{ $bColor }}; padding: 6px; text-align: right;">
                                        <strong> <span>المجموع الفرعي</span></strong>
                                    </td>
                                </tr>

                                <tr>
                                    <th class="font-weight-bold bgColor"
                                        style="border: 1px solid {{ $bColor }}; padding: 7px; text-align: right;">
                                        Total Discount
                                    </th>
                                    <td class="text-right p-1"
                                        style="border: 1px solid {{ $bColor }}; padding: 7px; text-align: right;">
                                        {{ number_format($estimate->discount ?? 0, 2) }}
                                    </td>
                                    <td class="font-weight-bold bgColor"
                                        style="border: 1px solid {{ $bColor }}; padding: 6px; text-align: right;">
                                        <strong> <span>المجموع الخصم </span></strong>
                                    </td>
                                </tr>

                                <tr>
                                    <td class="font-weight-bold bgColor"
                                        style="border: 1px solid {{ $bColor }}; padding: 7px; text-align: right;">
                                        <strong>Total Taxable</strong>
                                    </td>
                                    <td
                                        style="border: 1px solid {{ $bColor }}; padding: 7px; text-align: right;">
                                        {{ number_format($totalTaxable, 2) }}
                                    </td>
                                    <td class="font-weight-bold bgColor"
                                        style="border: 1px solid {{ $bColor }}; padding: 3px; text-align: right;">
                                        <strong> <span>المجموع الخاضع للضريبة</span></strong>
                                    </td>
                                </tr>

                                <tr>
                                    <td class="font-weight-bold bgColor"
                                        style="border: 1px solid {{ $bColor }}; padding: 7px; text-align: right;">
                                        <strong>Total Vat</strong>
                                    </td>
                                    <td
                                        style="border: 1px solid {{ $bColor }}; padding: 7px; text-align: right;">
                                        {{ number_format($totalVat, 2) }}
                                    </td>
                                    <td class="font-weight-bold bgColor"
                                        style="border: 1px solid {{ $bColor }}; padding: 6px; text-align: right;">
                                        <strong> <span>المجموع الضريبة</span></strong>
                                    </td>
                                </tr>

                                @if ($isRound)
                                    <tr>
                                        <td class="font-weight-bold bgColor"
                                            style="border: 1px solid {{ $bColor }}; padding: 7px; text-align: right;">
                                            <strong>Round Off</strong>
                                        </td>
                                        <td
                                            style="border: 1px solid {{ $bColor }}; padding: 7px; text-align: right;">
                                            {{ number_format($estimate->adjustment, 2) }}
                                        </td>
                                        <td class="font-weight-bold bgColor"
                                            style="border: 1px solid {{ $bColor }}; padding: 6px; text-align: right;">
                                            <strong> <span>تقريب</span></strong>
                                        </td>
                                    </tr>
                                @endif

                                <tr>
                                    <td class="font-weight-bold bgColor"
                                        style="border: 1px solid {{ $bColor }}; padding: 7px; text-align: right;">
                                        <strong>Net Amount</strong>
                                    </td>
                                    <td
                                        style="border: 1px solid {{ $bColor }}; padding: 7px; text-align: right;">
                                        {{ number_format($totalTaxable + $totalVat, 2) }}
                                    </td>
                                    <td class="font-weight-bold bgColor"
                                        style="border: 1px solid {{ $bColor }}; padding: 6px; text-align: right;">
                                        <strong> <span>المجموع الضافي</span></strong>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>

            </div>

            <div style="font-family: Arial, sans-serif; clear: both;font-size:10pt;">
                @if ($estimate->terms && $estimate->terms->isNotEmpty())
                    <div class="mt-3">
                        <table style="width: 100%; border-collapse: collapse; margin-top: 10px;">
                            <thead>
                                <tr>
                                    <th
                                        style="padding: 6px; background-color: {{ $bgColor }}; font-family: DejaVu Sans, Arial, sans-serif; text-align: center;">
                                        <table style="width: 100%; border: none; border-collapse: collapse;">
                                            <tr>
                                                <td style="text-align: left; width: 50%;"><strong>Terms and
                                                        Conditions</strong></td>
                                                <td style="text-align: right; direction: rtl; width: 50%;"><strong>
                                                        الشروط
                                                        و الأحكام :</strong></td>
                                            </tr>
                                        </table>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Loop through the terms and display them as bullet points -->
                                @foreach ($estimate->terms as $estimateTerm)
                                    <tr>
                                        <td>
                                            <ul style="margin: 0; padding-left: 15px;">
                                                <li style="font-size: 12px;padding:0px;line-height:10px;">
                                                    {{ $estimateTerm['description'] }}</li>
                                            </ul>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            <!-- Move this section right after your main content and before any footer -->
            <table style="width: 100%; text-align: center; margin-top: 18px;"> <!-- Reduced from 30px to 10px -->
                <!--<tr>-->
                <!--    <td style="width: 33.33%;">Signature __________________</td>-->
                <!--    <td style="width: 33.33%;">Date __________________</td>-->
                <!--    <td style="width: 33.33%;">Stamp __________________</td>-->
                <!--</tr>-->
                <tr>
                    <td colspan="3" style="padding: 5px;"> <!-- Reduced padding from 10px -->
                        <strong>THANK YOU FOR YOUR BUSINESS!! شكرا لتعاملكم معنا</strong>
                    </td>
                </tr>
            </table>
        </main>
</body>
