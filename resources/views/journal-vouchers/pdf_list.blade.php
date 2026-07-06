@php
    use Carbon\Carbon;

    $baseImagePath = public_path('img/company');
    // Company logo
    $imagePath = $baseImagePath . '/company_logo_color.png';
    $imageData = file_get_contents($imagePath);
    $base64 = base64_encode($imageData);
    $company_logo = 'data:image/png;base64,' . $base64; // Ensure correct format
    $bgColor = '#fff7f2';
    $bColor = '#e2e2e2';
    $totalWd = 0;
    $totalCTC = 0;
    $totalDeductions = 0;

@endphp

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice Details</title>
    <style>
        @page {
            size: A4 portrait;
            /* Set the page size to A4 and change the orientation to landscape */
            margin: 0;
            padding: 0;
        }

        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        .content_header {
            display: table;
            width: 100%;
            background-color: {{ $bgColor }};
            /* Set the background color */
            border: 2px solid {{ $bColor }};
            /* Optional border */
            padding: 10px 5px 5px 5px;

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
            width: 30%;
            height: 2.62cm;

        }

        .right {
            padding-right: 10px;
        }

        .left div {
            width: 3cm;
            height: 2.62cm;
            margin-left: 10px;
            background-image: url('{{ $company_logo }}');
            background-size: 92%;

            background-position: center;
            background-repeat: no-repeat;

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

        .bColor {
            border: .05cm solid #e2e2e2 !important;
        }

        .bgColor {
            background: #fff7f2 !important;
        }

        .salary_sheet_table {
            border-collapse: collapse;
            width: 99%;
            margin: auto;
            margin-top: 15px;
            font-family: Arial, sans-serif;
            font-size: 8.5pt;
        }

        .salary_sheet_table th,
        .salary_sheet_table td {
            border: 1px solid #e2e2e2;
            padding: 4px;
            text-align: center;
        }

        .salary_sheet_table th {
            background-color: {{ $bgColor }};
        }

        .salary_sheet_table .header-row {
            background-color: {{ $bgColor }};
        }

        .salary_sheet_table .earnings-column {
            background-color: {{ $bgColor }}2;
        }

        .salary_sheet_table .deductions-column {
            background-color: {{ $bgColor }};
        }

        .salary_sheet_table .total-row {
            font-weight: bold;
        }

        .salary_sheet_table .amount-in-words {
            font-style: italic;
        }

        .text-left {
            text-align: left !important;
        }

        .text-right {
            text-align: right !important;
        }

        .textH {
            color: #ff7038 !important;
        }
    </style>
</head>

<body>
    <div class="contents">
        <div class="content_header">
            <div class="left">
                <div>
                </div>
            </div>
            <div class="middle">
                <h1 style="font-size: 15pt;line-height:0px;color:#f7996e;">{{ $settings['company'] ?? '' }}</h1>
                <p style="line-height:18px;"><span style="font-size: 18pt;font-weight:bold;color:#3b6b67;">Journal
                        Voucher Report</span></p>
                <p></p>
            </div>
            <div class="right">
                <h1 style="font-size: 20pt;"></h1>
            </div>
        </div>
        <div class="content_body">
            <table style="font-size: 12px; width: 99%; border-collapse: collapse; margin-top: 10px; margin: .5%;"
                class="employee_info">
                <thead>
                    <tr>
                        <th
                            style="background-color: {{ $bgColor }}; border: 1px solid rgb(187, 187, 187); text-align: left; padding: 2px;">
                            Branch Name</th>
                        <th
                            style="background-color: {{ $bgColor }}; border: 1px solid rgb(187, 187, 187); text-align: left; padding: 2px;">
                            From Account</th>
                        <th
                            style="background-color: {{ $bgColor }}; border: 1px solid rgb(187, 187, 187); text-align: left; padding: 2px;">
                            To Account</th>
                        <th
                            style="background-color: {{ $bgColor }}; border: 1px solid rgb(187, 187, 187); text-align: left; padding: 2px;">
                            Description</th>
                        <th
                            style="background-color: {{ $bgColor }}; border: 1px solid rgb(187, 187, 187); text-align: right; padding: 2px;">
                            Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($exportData as $row)
                        <tr>
                            <td style="border: 1px solid rgb(187, 187, 187); padding: 2px;">{{ $row['Branch Name'] }}
                            </td>
                            <td style="border: 1px solid rgb(187, 187, 187); padding: 2px;">{{ $row['From Account'] }}</td>
                            <td style="border: 1px solid rgb(187, 187, 187); padding: 2px;">{{ $row['To Account'] }}</td>
                            <td style="border: 1px solid rgb(187, 187, 187); padding: 2px;">{!! $row['Description'] !!}</td>
                            <td style="text-align:right; border: 1px solid rgb(187, 187, 187); padding: 2px;">
                                {{ $row['Amount'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>





        </div>
    </div>

</body>

</html>
