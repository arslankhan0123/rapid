@php

    $contentTitle = 'Tax';
    $footer = 'Footer Content';

    $format = $settings['print_format'] ?? 1;

    $baseImagePath = public_path('img/company');
    // Company logo
    $imagePath = $baseImagePath . '/company_logo_color.png';
    $imageData = file_get_contents($imagePath);
    $base64 = base64_encode($imageData);
    $company_logo = 'data:image/png;base64,' . $base64; // Ensure correct format
    $bgColor = '#fff7f2';
    $bColor = '#e2e2e2';

@endphp
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Sample Receiving</title>
    <style>
        @page {
            margin-top: 0px;
            /* Adjust based on header height */
            margin-bottom: 0px;
            /* Adjust based on footer height */
            margin-left: 0px;
            margin-right: 00px;
        }

        .content_header {
            display: table;
            width: 100%;
            background-color: {{ $bgColor }};
            /* Set the background color */
            border: 1px solid {{ $bColor }};
            /* Optional border */
            padding: 10px;
            margin-bottom: 20px;
        }

        .content_header .left,
        .content_header .middle,
        .content_header .right {
            display: table-cell;
            vertical-align: middle;
            text-align: center;
        }

        .left {
            display: block;
            width: 2.5cm;
            height: 2.62cm;
        }

        .left div {
            width: 2.5cm;
            height: 2.62cm;
            background-image: url('{{ $company_logo }}');
            background-size: 100%;
            /* Ensures the image covers the entire container */
            background-position: center;
            /* Centers the image */
            background-repeat: no-repeat;
            /* Prevents tiling */
        }

        .content_header .left {
            text-align: left;
            width: 20%;
        }

        .content_header .middle {
            text-align: center;
            width: 60%;
        }

        .content_header .right {
            text-align: right;
            width: 20%;
        }

        .content_header img {
            max-width: 100%;
            max-height: 50px;
        }

        .content_header h2 {
            margin: 0;
            font-size: 18px;
        }

        .content_header .net_pay {
            font-weight: bold;
            font-size: 16px;
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
    </style>
</head>

<body>



    <!-- Table Content -->
    <main>

        <div class="content_header">
            <div class="left">
                <div>
                </div>
            </div>
            <div class="middle">
                <h1 style="font-size: 20pt;">Certificate</h1>
                <p>{{ $settings['company'] ?? '' }}</p>
                <p>Makkah, New Shubayka<br>Saudi Arabia</p>
            </div>
            <div class="right">
                <h1 style="font-size: 20pt;"></h1>
            </div>
        </div>
        <div class="content_info">
            <div class="content_info">
                <table border="1" cellspacing="0" cellpadding="5"
                    style="width: 98%; border-collapse: collapse; font-family: Arial, sans-serif; font-size: 14px;margin:1%;">
                    <tr>
                        <td style="width: 20%; font-weight: bold;">Certificate number</td>
                        <td style="width: 30%;">{{ $certificate->certificate_number ?? '' }}</td>
                        <td style="width: 20%; font-weight: bold;">Employee</td>
                        <td style="width: 30%;">{{ $certificate->employee ?? '' }}</td>
                    </tr>
                    <tr>
                        <td style="font-weight: bold;">Lab Manager</td>
                        <td>{{ $certificate->lab_manager ?? '' }}</td>
                        <td style="font-weight: bold;">General Manager</td>
                        <td>{{ $certificate->general_manager ?? '' }}</td>
                    </tr>
                    <tr>
                        <td style="font-weight: bold;">Date</td>
                        <td>{{ $certificate->date ? \Carbon\Carbon::parse($certificate->date)->format('d-m-Y') : '' }}
                        </td>
                        <td style="font-weight: bold;">Created At</td>
                        <td>{{ $certificate->created_at ? \Carbon\Carbon::parse($certificate->created_at)->format('d-m-Y') : '' }}
                        </td>
                    </tr>
                    <tr>

                        <td style="font-weight: bold;">Description</td>
                        <td colspan="3">{!! $certificate->description ?? '' !!}</td>
                    </tr>
                </table>



            </div>
        </div>
    </main>
    </script>
</body>

</html>
