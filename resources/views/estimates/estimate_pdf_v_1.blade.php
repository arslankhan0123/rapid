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

    $font_quotation =
        'data:image/png;base64,' . base64_encode(file_get_contents(public_path('print/font_images/quotation.png')));

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

@endphp
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Quotation PDF</title>
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
            padding-top: 178px;
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
            height: 110px;
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
            background: red;

            /* Ensure the footer stays above content */
        }

        .bColor {
            border: .05cm solid #e2e2e2 !important;
        }

        .bgColor {
            background: #fff7f2 !important;
        }

        /* Container with inline-block elements to display them in one row */
        .quotation-header {
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
        .quotation {
            vertical-align: middle;
            margin-top: 10px;
            float: left;
            display: inline-block;
            width: 8cm;
            height: 1.13cm;
            text-align: center;
            font-size: 20pt;

            border: 1px solid {{ $bColor }};
            background: {{ $bgColor }};

        }

        .customer_info {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .customer_info th,
        .customer_info td {
            height: 1.16cm;
            text-align: left;
            border: 1px solid {{ $bColor }};


        }
    </style>


</head>

<body>
    <!-- Header -->
    <header>
        <img src="{{ $headerImage }}" style="width: 100%;height:110px; padding: 0px;">
        <div class="quotation-header">
            <div class="vat">
                Vat No. : 310429743800003
            </div>
            <div class="quotation">
                Quotation <img src="{{ $font_quotation }}" style="width: 45%; margin-top: 15px;margin-left:5px;">
            </div>
        </div>
    </header>

    <!-- Footer -->
    <footer>
        <img src="{{ $footerImage }}" style="width: 100%; padding: 0px;">
    </footer>

    <!-- Watermark -->
    <div class="watermark">

    </div>

    <!-- Table Content -->
    <main>

        <table class="customer_info">
            <tr>
                <th class="bgColor">Quote No.<br></th>
                <td >12250</td>
                <th  class="bgColor">Quote Date<br></th>
                <td>25-11-2024</td>
                <th class="bgColor">Quote Validity<br></th>
                <td>30-12-2024</td>
            </tr>
            <tr>
                <th class="bgColor">Cust. Reference<br></th>
                <td>Mohammed</td>
                <th class="bgColor">Cust. Name<br></th>
                <td>Advanced Construction Technology Services</td>
                <th class="bgColor">Email<br></th>
                <td>info@smitsengineering.com</td>
            </tr>
            <tr>
                <th class="bgColor">Address<br></th>
                <td colspan="5">Jeddah, Khaled Bin, Saudi Arabia</td>
            </tr>
            <tr>
                <th class="bgColor">Subject<br></th>
                <td colspan="5">Quotation for Manpower Supply Services</td>
            </tr>
        </table>

        <div style="margin-top: 20px;">
            <p>Dear Sir,</p>
            <p>Reference to your inquiry, please find below our rates for providing manpower supply services. We hope
                that you find our proposal satisfactory and meet your requirements. Our rates for the above requirement
                are as follows:</p>
        </div>

    </main>
    <script type="text/php">
    if (isset($pdf)) {
        $text = " Page {PAGE_NUM} of {PAGE_COUNT}";
        $font = $fontMetrics->getFont("Arial", "normal");
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
