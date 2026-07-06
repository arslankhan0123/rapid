@php

    $contentTitle = 'Tax';
    $footer = 'Footer Content';

    $format = $settings['print_format'] ?? 1;

    $footerheight = '52px';
    if ($format == 2) {
        $footerheight = '88px';
    }
    // Define the base path for different formats
    $baseImagePath = public_path('print/format_' . $format);

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

    if ($settings['print_format'] == 1) {
        //smit

        $com_name = 'شركة سهم المنار للتجارة والمقاولات';
        $trn_number = 311204277500003;
    } else {
        //ran

        $com_name = 'مختبر راية  النجاح لمواد البناء';
        $trn_number = 310429743800003;
    }
    $bgColor = '#fff7f2';
    $bColor = '#e2e2e2';
    //dd(file_exists(storage_path('fonts/Amiri-Regular.ttf')));
@endphp
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Invoice</title>
    <style>
        @page {
            margin-top: 0px;
            /* Adjust based on header height */
            margin-bottom: 0px;
            /* Adjust based on footer height */
            margin-left: 0px;
            margin-right: 00px;
        }

        body {
            padding-left: .10cm;
            padding-right: .10cm;
            padding-top: 172px;
            /* Same as header height */
            padding-bottom: {{ $footerheight }};
            /* Same as footer height */

        }

        /* Set up header and footer */
        header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;

            /* Adjust the height of the header image */
            text-align: center;
            z-index: 10;
            /* Ensure the header stays above content */
        }

        footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            height: {{ $footerheight }};
            /* Adjust the height of the footer image */
            text-align: center;
            z-index: 10;


            /* Ensure the footer stays above content */
        }



        .bColor {
            border: .05cm solid #e2e2e2 !important;
        }

        .bgColor {
            background: #fff7f2 !important;
        }

        .watermark {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;

            background-size: 85%;
            /* Cover to ensure full width */
            background-position: center;
            background-repeat: no-repeat;
            opacity: 0.12;
            /* 20% visibility */
            z-index: -1;
            /* Prevent watermark from interfering with content */
        }

        .page-break {
            page-break-after: always;
            /* Ensure content starts on a new page */
        }

        .content-header {
            width: 100%;
            height: 1.80cm;
            margin: 0;

        }

        /* VAT section */
        .vat {
            display: inline-block;
            padding: 5px;
            font-size: 13pt;
            padding-left: .26cm;
            text-align: start;
            padding-top: 20px;
            width: 30%;
            float: left;
            text-align: left;
            /* Adjust width as needed */
        }

        /* Quotation section with blue background */
        .content_header_title {
            vertical-align: middle;
            margin-top: 10px;
            float: left;
            display: inline-block;
            width: 8cm;
            height: 1.13cm;
            text-align: center;
            font-size: 20pt;
            line-height: 40px;
            border: 1px solid {{ $bColor }};
            background: {{ $bgColor }};

        }

        .content_info {

            font-size: 10pt;
        }

        .content_info table {
            border: 1px solid {{ $bColor }};
            /* Set border color */
            border-collapse: collapse;
            width: 100%;

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
            margin-top: .30cm;
            clear: both;


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
    </style>
</head>

<body>
    <!-- Header -->
    <header>


    </header>

    <footer>
        <img src="{{ $footerImage }}" style="width: 100%; padding: 0px;">

    </footer>


    <!-- Table Content -->
    <main>
        <img src="{{ $headerImage }}" style="width: 100%;height:110px; padding: 0px;">
        <div class="content-header">
            <div class="vat">
                Vat No. : 310429743800003
            </div>
            <div class="content_header_title">
                Vat Invoice
            </div>
            <div class="arabic-text">
                فاتورة ضريبة القيمة المضافة
            </div>

        </div>
        <table style="border-collapse: collapse; width: 100%;">
            <tr style="background-color: #ffffff; font-weight: bold;">
                <th style="background-color: #ffffff; border: 2px solid black;padding: 10px;text-align: center; width: 65%; font-size: 20px;"
                    colspan="2">TAX INVOICE <span style="text-align: right; margin-left: 25px;">لفاتورة
                        الضريبية</span></th>
                <th style="background-color: #ffffff; border: 2px solid black;padding: 10px;text-align: left; width: 35%;"
                    colspan="2">Vat # 311204277500003</th>
            </tr>
            <tr>
                <th
                    style="background-color: #ffffff; border: 2px solid black;padding: 10px;text-align: left;width: 20%;">
                    Customer Reg No. <br> قم تسجيل العميل</th>
                <th style="background-color: #ffffff; border: 2px solid black;padding: 10px;text-align: left;"></th>
                <th
                    style="background-color: #ffffff; border: 2px solid black;padding: 10px;text-align: left;width: 15%;">
                    Invoice No <br> قم تسجيل العميل</th>
                <th style="background-color: #ffffff; border: 2px solid black;padding: 10px;text-align: left;"></th>
            </tr>
            <tr>
                <th style="background-color: #ffffff; border: 2px solid black;padding: 10px;text-align: left;">Customer
                    Name <br> قم تسجيل العميل </th>
                <th style="background-color: #ffffff; border: 2px solid black;padding: 10px;text-align: left;"></th>
                <th style="background-color: #ffffff; border: 2px solid black;padding: 10px;text-align: left;">Date of
                    Invoice <br> قم تسجيل العميل </th>
                <th style="background-color: #ffffff; border: 2px solid black;padding: 10px;text-align: left;"></th>
            </tr>
            <tr>
                <th style="background-color: #ffffff; border: 2px solid black;padding: 10px;text-align: left;">VAT Reg
                    No <br> قم تسجيل العميل </th>
                <th style="background-color: #ffffff; border: 2px solid black;padding: 10px;text-align: left;"></th>
                <th style="background-color: #ffffff; border: 2px solid black;padding: 10px;text-align: left;">Invoice
                    Period <br> قم تسجيل العميل </th>
                <th style="background-color: #ffffff; border: 2px solid black;padding: 10px;text-align: left;"></th>
            </tr>
            <tr>
                <th style="background-color: #ffffff; border: 2px solid black;padding: 10px;text-align: left;">Customer
                    Address <br> قم تسجيل العميل </th>
                <th style="background-color: #ffffff; border: 2px solid black;padding: 10px;text-align: left;"></th>
                <th style="background-color: #ffffff; border: 2px solid black;padding: 10px;text-align: left;">PO No/Ref
                    No <br> قم تسجيل العميل </th>
                <th style="background-color: #ffffff; border: 2px solid black;padding: 10px;text-align: left;"></th>
            </tr>
            <tr>
                <th style="background-color: #ffffff; border: 2px solid black;padding: 10px;text-align: left;">Project
                    Name <br> قم تسجيل العميل </th>
                <th style="background-color: #ffffff; border: 2px solid black;padding: 10px;text-align: left;"></th>
                <th style="background-color: #ffffff; border: 2px solid black;padding: 10px;text-align: left;">Due Date
                    <br> قم تسجيل العميل </th>
                <th style="background-color: #ffffff; border: 2px solid black;padding: 10px;text-align: left;"></th>
            </tr>
            <tr>
                <th style="background-color: #ffffff; border: 2px solid black;padding: 10px;text-align: left;">
                    Description <br> قم تسجيل العميل </th>
                <th style="background-color: #ffffff; border: 2px solid black;padding: 10px;text-align: left;"></th>
                <th style="background-color: #ffffff; border: 2px solid black;padding: 10px;text-align: left;">
                    Contact/Email <br> قم تسجيل العميل </th>
                <th style="background-color: #ffffff; border: 2px solid black;padding: 10px;text-align: left;"></th>
            </tr>
        </table>
    </main>
    <script type="text/php">
    if (isset($pdf)) {
        $text = " Page {PAGE_NUM} of {PAGE_COUNT}";
        $font = $fontMetrics->getFont("times", "normal");
        $size = 13;
        $color = array(0, 0, 0);
        $word_space = 0.0;  // default
        $char_space = 0.0;  // default
        $angle = 0.0;  // default
        $pdf->page_text(520, 100, $text, $font, $size, $color, $word_space, $char_space, $angle);
    }
</script>
</body>

</html>
