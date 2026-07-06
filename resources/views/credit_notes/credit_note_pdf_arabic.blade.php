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
            <table border="1" cellspacing="0" cellpadding="5"
                style="width: 100%; border-collapse: collapse;font-size:10px;padding-right:3px;line-height:9px;">
                <tr>
                    <td style="width: 2cm;">Invoice No. <br>رقم الفاتورة </td>
                    <td style="width: 1.22cm;">{{ $creditNote->invoice?->invoice_number }}</td>
                    <td style="width: 2.35cm;">Invoice Date<br>تاريخ الفاتورة</td>
                    <td style="width: 2cm;">
                        {{ \Carbon\Carbon::parse($creditNote->invoice?->invoice_date)->format('d-m-Y') }}
                    </td>
                    <td style="width: 2.48cm;">Invoice Month <br>فاتورة الشهر</td>
                    <td style="width: 1.5cm;">
                        {{ \Carbon\Carbon::parse($creditNote->invoice?->invoice_date)->format('M, y') }}</td>
                    <td style="width: 2.40cm;">Cust. Vat No. <br>رقم الضريبة للعميل</td>
                    <td
                        style="width: 2cm; word-wrap: break-word; word-break: break-word; white-space: normal; overflow-wrap: break-word; overflow: hidden; padding: 2px;">
                        {{ $creditNote->customer->vat_number ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td style="width: 2cm;">Cust. No. <br>رقم العميل </td>
                    <td style="width: 1.22cm;">{{ $creditNote->customer->code ?? 'N/A' }}</td>
                    <td style="width: 2cm;">Cust. Name <br>اسم العميل</td>
                    <!--<td colspan="3">{{ $creditNote->customer->company_name ?? 'N/A' }}</td>-->
                     <td colspan="3">{!! $creditNote->customer->company_name ?? 'N/A' !!}</td>

                    <td style="width: 2.40cm; ">Payment Modes <br>شروط الدفع </td>
                    <td style="width:2cm;">
                        <p>
                            @if ($creditNote->invoice->paymentModes->count() > 1)
                                {{-- Display all payment modes in a comma-separated list for multiple items --}}
                                {{ $creditNote->invoice->paymentModes->pluck('name')->implode(', ') }}
                            @elseif ($creditNote->invoice->paymentModes->count() === 1)
                                {{-- Display the single payment mode --}}
                                {{ $creditNote->invoice->paymentModes->first()->name }}
                            @else
                                {{-- Display "N/A" when no payment modes exist --}}
                                {{ __('messages.common.n/a') }}
                            @endif
                        </p>

                    </td>
                </tr>
                <tr>
                    <td>Project Code <br> رقم المشروع</td>
                    <td>{{ $creditNote->invoice?->project?->project_code ?? 'N/A' }}</td>
                    <td>Project Name<br> اسم المشروع</td>
                    <!--<td>{{ $creditNote->invoice?->project->project_name ?? 'N/A' }}</td>-->
                    <td>{!! $creditNote->invoice?->project->project_name ?? 'N/A' !!}</td>
                    <td>Project Location<br>موقع المشروع</td>
                    <td>{{ $creditNote->invoice?->project?->project_location ?? 'N/A' }}</td>
                    <td style="">Email<br>بريد إلكتروني</td>
                    <td style="width:2cm; word-wrap: break-word; overflow-wrap: break-word; white-space: normal;">
                        {{ $creditNote->invoice->customer->email ?? 'N/A' }}</td>

                </tr>
                <tr>
                    <td>Return No <br>رقم المرتجع</td>
                    <td>{{ $creditNote->credit_note_number ?? 'N/A' }}</td>
                    <td>Return Date <br> تاريخ المرتجع</td>
                    <td>{{ \Carbon\Carbon::parse($creditNote->credit_note_date)->format('d-m-Y') }}</td>
                    <td>Vendor Code <br> رمز المورد</td>
                    <td>{{ $creditNote->vendor_code ?? 'N/A' }}</td>
                    <td>Address<br>العنوان</td>
                    <td>
                        {{-- @if ($creditNote->creditNoteAddresses && $creditNote->creditNoteAddresses->isNotEmpty())
                            @foreach ($creditNote->creditNoteAddresses as $address)
                                <div style="width: 2cm; white-space: nowrap; overflow-wrap: normal;">
                                    {{ implode(', ', array_filter([$address->street, $address->city, $address->state, $address->country, $address->zip_code])) }}
                                </div>
                            @endforeach
                        @else
                            <div
                                style="width:2cm; word-wrap: break-word; overflow-wrap: break-word; white-space: normal;">
                                {{ $creditNote->customer['address'] ?? 'N/A' }}
                            </div>

                        @endif --}}
                        <div style="word-wrap: break-word; overflow-wrap: break-word; white-space: normal;">
                            @if (isset($creditNote->customer->customerAddress))
                                {{ $creditNote->customer?->customerAddress?->addressCountry?->name ?? ' ' }},
                                {{ $creditNote->customer?->customerAddress?->city ?? ' ' }},
                                {{ $creditNote->customer?->customerAddress?->customerState?->name ?? ' ' }},
                                {{ $creditNote->customer?->customerAddress?->zip ?? ' ' }}
                                {{ $creditNote->customer?->address ?? ' ' }}
                            @else
                                N/A
                            @endif
                        </div>

                    </td>
                </tr>
                <tr>
                    <td>Remarks<br>ملاحظة</td>
                    <td colspan="3">{{ $creditNote->admin_text ?? 'N/A' }}</td>
                    <td>Branch<br>فرع</td>
                    <td colspan="3">{{ $creditNote->branch?->name ?? '' }}</td>
                </tr>


            </table>

        </div>

        <div class="content_items">
            <table class="sales_table_data" style="font-size: 12px;line-height:11px;">
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

                    @foreach ($creditNote->salesItems as $index => $item)
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
                                {{ html_entity_decode($item->service->title ?? '') }}</td>

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
                                {{ (int)($item->tax) }}%
                            </td>
                            <td style=" text-align:right; padding-right:1%">
                                {{ number_format($vatAmount, 2) }} <!-- VAT Amount -->
                            </td>
                            <td style=" text-align:right; padding-right:1%">
                                {{ number_format($netPrice, 2) }}
                                <!-- Including VAT -->
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="content_calculation" style="margin-top:10px;">
            <table cellspacing="0" cellpadding="5" style="width: 100%; border-collapse: collapse;font-size:13px;">
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
                                                            {{ $creditNote->branch?->bank?->name ?? '' }}</td>
                                                    </tr>
                                                    <tr>
                                                        <!-- Row 2 -->
                                                        <td
                                                            style="width: 30%; padding: 4px; border-bottom: 1px solid {{ $bColor }};border-right:1px solid {{ $bColor }};">
                                                            A/C No.</td>
                                                        <td
                                                            style="width: 70%; padding: 4px; border-bottom: 1px solid {{ $bColor }};">
                                                            {{ $creditNote->branch?->bank?->account_number ?? '' }}
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <!-- Row 2 -->
                                                        <td
                                                            style="width: 30%; padding: 4px; border-bottom: 1px solid {{ $bColor }};border-right:1px solid {{ $bColor }};">
                                                            IBAN</td>
                                                        <td
                                                            style="width: 70%; padding: 4px; border-bottom: 1px solid {{ $bColor }};">
                                                            {{ $creditNote->branch?->bank?->iban_number ?? '' }}
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <!-- Row 2 -->
                                                        <td
                                                            style="width: 30%; padding: 4px; border-right:1px solid {{ $bColor }};">
                                                            Address</td>
                                                        <td style="width: 70%; padding: 4px;">
                                                            {{ $creditNote->branch?->bank?->address ?? '' }}
                                                        </td>
                                                    </tr>

                                                </table>
                                            </td>

                                            <!-- Middle column: 1px wide (no border in the middle here) -->
                                            <td style="width: 1px;"></td>

                                            <!-- Right side: 40% width -->
                                            <td style="width: 30%; ">
                                                @php
                                                    use Salla\ZATCA\GenerateQrCode;
                                                    use Salla\ZATCA\Tags\Seller;
                                                    use Salla\ZATCA\Tags\TaxNumber;
                                                    use Salla\ZATCA\Tags\InvoiceDate;
                                                    use Salla\ZATCA\Tags\InvoiceTotalAmount;
                                                    use Salla\ZATCA\Tags\InvoiceTaxAmount;

                                                    $qr_seller_trn = $trn_number;
                                                    $qr_tax_amount = $totalVat;

                                                    $qr_invoice_amount = $creditNote->total_amount ?? 0;

                                                    $qr_code = null;
                                                    $qr_seller_name = $com_name;

                                                    $qr_invoice_date = !empty($creditNote->credit_note_date)
                                                        ? date('Y-m-d', strtotime($creditNote->credit_note_date))
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
                                                @if ($qr_code && $creditNote->payment_status != 0)
                                                    <img style="width: 3cm;height:3cm;" src="{{ $qr_code }}"
                                                        alt="QR Code" />
                                                @endif
                                            </td>
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
                    <td style="width:50%; vertical-align: top;text-align:right;">
                        <table style=" border-collapse: collapse; font-size: 12px;">
                            <tr>
                                <td class="font-weight-bold bgColor"
                                    style="border: 1px solid {{ $bColor }}; padding: 6px; text-align: right;">
                                    <strong>Subtotal</strong>
                                </td>
                                <td style="border: 1px solid {{ $bColor }}; padding: 7px; text-align: right;">
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
                                    {{ number_format($creditNote->discount ?? 0, 2) }}
                                </td>
                                <td class="font-weight-bold bgColor"
                                    style="border: 1px solid {{ $bColor }}; padding: 6px; text-align: right;">
                                    <strong> <span>المجموع الخضم </span></strong>
                                </td>
                            </tr>

                            <tr>
                                <td class="font-weight-bold bgColor"
                                    style="border: 1px solid {{ $bColor }}; padding: 7px; text-align: right;">
                                    <strong>Total Taxable</strong>
                                </td>
                                <td style="border: 1px solid {{ $bColor }}; padding: 7px; text-align: right;">
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
                                <td style="border: 1px solid {{ $bColor }}; padding: 7px; text-align: right;">
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
                                        {{ number_format($creditNote->adjustment, 2) }}
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
                                <td style="border: 1px solid {{ $bColor }}; padding: 7px; text-align: right;">
                                    {{ number_format($creditNote->total_amount ?? 0, 2) }}
                                </td>
                                <td class="font-weight-bold bgColor"
                                    style="border: 1px solid {{ $bColor }}; padding: 6px; text-align: right;">
                                    <strong> <span>المجموع الضافى</span></strong>
                                </td>
                            </tr>
                        </table>

                    </td>
                </tr>
            </table>

        </div>

        <div style="font-family: Arial, sans-serif; clear: both;font-size:10pt;">
            @if ($creditNote->invoice?->project?->terms && $creditNote->invoice?->project?->terms->isNotEmpty())
                <div class="mt-3">
                    <table style="width: 100%; border-collapse: collapse; margin-top: 10px;">
                        <thead>
                            <tr>
                                <th
                                    style="padding: 8px; background-color: {{ $bgColor }}; font-family: DejaVu Sans, Arial, sans-serif; text-align: center;">
                                    <table style="width: 100%; border: none; border-collapse: collapse;">
                                        <tr>
                                            <td style="text-align: left; width: 50%;"><strong>Terms and
                                                    Conditions</strong></td>
                                            <td style="text-align: right; direction: rtl; width: 50%;"><strong> الشروط
                                                    و الأحكام :</strong></td>
                                        </tr>
                                    </table>
                                </th>

                            </tr>
                        </thead>
                        <tbody>
                            <!-- Loop through the terms and display them as bullet points -->
                            @foreach ($creditNote->invoice?->project?->terms as $estimateTerm)
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

        <table style="width: 100%; text-align: center;margin-top:30px;">
            <!--<tr>-->
            <!--    <td style="width: 33.33%;">Signature __________________</td>-->
            <!--    <td style="width: 33.33%;">Date __________________</td>-->
            <!--    <td style="width: 33.33%;">Stamp __________________</td>-->
            <!--</tr>-->
            <tr>
                <td colspan="3" style="padding: 10px;"><br>
                    <strong>THANK YOU FOR YOUR BUSINESS!! شكرا لتعاملكم معنا</strong>
                </td>
            </tr>
        </table>
    </main>
</body>
