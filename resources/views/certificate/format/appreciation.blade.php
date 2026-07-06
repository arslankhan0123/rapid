<?php
// Load image path using public_path()
$imagePath = public_path('print/certificate_layout/appreciation.png');

// Get image contents and encode to Base64
$imageData = base64_encode(file_get_contents($imagePath));
$imageBase64 = 'data:image/jpeg;base64,' . $imageData;
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Appreciation Certificate</title>
    <style>
        @page {
            size: 23.29cm 16.61cm;
            margin: 0;
        }

        @font-face {
            font-family: 'EdwardianScriptITC';
            src: url('{{ public_path('fonts/edwardianscriptitc.ttf') }}') format('truetype');
            font-style: normal;
            font-weight: normal;
        }

        body {
            margin: 0;
            padding: 0;
            font-family: 'Arial', sans-serif;
            position: relative;
        }

        .certificate-container {
            width: 23.29cm;
            height: 16.61cm;
            position: relative;
            background-image: url('<?php echo $imageBase64; ?>');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }

        .date {
            width: 110px;
            font-size: 8pt;
            margin-left: 80.3%;
            padding-top: 31px;

        }

        .cn_number {
            width: 110px;
            font-size: 8pt;
            margin-left: 80.3%;
            padding-top: 10.5px;

        }

        .employee_name {
            font-family: 'EdwardianScriptITC', cursive;
            font-style: italic;
            color: #f38f31;
            text-align: right;
            margin-top: 185px;
            margin-right: 10.5%;
        }

        .description {
            text-align: right;
            width: 65%;
            float: right;
            margin-right: 10.5%;
            color: black;
        }

        table {
            width: 52%;
            margin: auto;
            padding-top: 160px;
            margin-left: 38%;


        }

        td {

            padding: 0px;
            text-align: center;
            font-size: 9pt;
        }

        .left-column {
            text-align: center;
            width: 250px;
        }

        .right-column {
            text-align: center;
            width: 250px;
        }

        .company_logo {
            position: absolute;
            top: 0px;
            left: 360px;
            z-index: 99999;
        }

        .company_logo div {
            width: 5cm;
            height: 3.1cm;
            background-image: url('{{ $company_logo }}');
            background-size: contain;
            /* Better scaling */
            background-position: center;
            background-repeat: no-repeat;
        }
    </style>
</head>

<body>

    <div class="company_logo">
        <div>
        </div>
    </div>

    <div class="certificate-container">
        <div class="date">
            {{ \Carbon\Carbon::parse($category->date ?? '01-01-2025')->format('jS M Y') }}
        </div>
        <div class="cn_number">
            {{ $category->certificate_number ?? '' }}
        </div>
        <div class="employee_name">
            <h1>{{ $category->employee ?? '' }}</h1>
        </div>

        <div class="description">
            {{ strip_tags($category->description) ?? '' }}
        </div>
        <table>
            <tbody>
                <tr>
                    <td class="left-column">{{ $category->general_manager ?? '' }} </td>
                    <td class="right-column">{{ $category->lab_manager ?? '' }}</td>
                </tr>
            </tbody>
        </table>
    </div>
</body>

</html>
