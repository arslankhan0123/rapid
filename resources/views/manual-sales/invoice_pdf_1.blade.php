<?php

// company logo
$imagePath = public_path('img/company/company_logo.png');
$imageData = file_get_contents($imagePath);
$base64 = base64_encode($imageData);
$base64Image = 'data:image/jpg;base64,' . $base64;

// // company_name image
// $imageCompanyPath = public_path('img/company/company_name.png');
// $imageCompanyData = file_get_contents($imageCompanyPath);
// $companybase64 = base64_encode($imageCompanyData);
// $company_name = 'data:image/png;base64,' . $companybase64;

// company_name image
$imagefooterPath = public_path('img/company/footer.jpg');
$imagefooterPath = file_get_contents($imagefooterPath);
$imagefooterPath = base64_encode($imagefooterPath);
$footerImage = 'data:image/jpg;base64,' . $imagefooterPath;

// company_name image
$header = public_path('img/company/header.jpg');
$imageheader = file_get_contents($header);
$headerImage = base64_encode($imageheader);
$headerImage = 'data:image/jpg;base64,' . $headerImage;

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tax Invoice</title>
    <style>
        @page {
            size: A4;
            margin: 0;
        }

        body {

            font-family: Arial, sans-serif;
            /* Or use a specific font family */
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
            color: #333;
            font-size: 12px;
        }

        .invoice_body {

            position: relative;
            /* Make it relative to position the overlay */
            overflow: hidden;
            /* Ensure content does not overflow */
        }

        .invoice_body::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: url({{ $base64Image }});
            background-size: 700px;
            background-position: center;
            background-repeat: no-repeat;
            margin: 50px;
            opacity: 0.3;
            z-index: 1;
            margin-right: 0px;
            margin-left: 0px;


        }

        .invoice-container {
            position: relative;
            /* Keep the container content above the background */
            z-index: 2;
            /* Ensure it is on top of the background image */
            width: 210mm;
            /* Set your desired width */
            height: 297mm;
            /* Set your desired height */
            margin: auto;
            box-sizing: border-box;
            background: rgba(255, 255, 255, 0.87);
            /* Optional: give the container a white background with slight opacity */
        }



        .header {
            position: fixed;

            /* Adjust based on the height of your image */
            left: 0;
            right: 0;
            height: 50px;
            /* Set height for the header */
            text-align: center;
        }

        .footer {
            position: fixed;
            bottom: 36px;
            /* Adjust based on the height of your image */
            left: 0;
            right: 0;
            height: 50px;
            /* Set height for the footer */
            text-align: center;
        }



        .company-info,
        .invoice-info {
            width: 100%;
            margin-bottom: 20px;
        }

        .company-info td,
        .invoice-info td {
            padding: 5px;
        }

        .company-info td {
            vertical-align: top;
        }

        .company-info tr:nth-child(1) td {
            font-size: 18px;
            font-weight: bold;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        .table-bordered,
        .table-bordered th,
        .table-bordered td {
            border: 1px solid #ddd;
        }

        .table-bordered th,
        .table-bordered td {
            padding: 8px;
            text-align: left;
        }

        .table-bordered th {
            background-color: #f9f9f9;
        }

        .total-section {
            margin-top: 20px;
            font-size: 16px;
        }

        .total-section td {
            padding: 10px;
        }

        .total-section .total-title {
            font-weight: bold;
        }

        .amount-in-words {
            margin-top: 20px;
            font-size: 14px;
            font-style: italic;
        }



        .bank-details {
            margin-top: 20px;
            font-size: 12px;
        }




        .invoice-title {

            width: 100%;
            margin: auto;
            text-align: center;
        }

        .invoice-title .txtp {

            color: black;

            height: 35px;
            text-align: center;
            margin: auto;
            font-size: 16px;
            font-weight: bold;

        }



        .clearfix::after {
            content: "";
            /* Create a pseudo-element */
            clear: both;
            /* Clear floats */
            display: table;
            /* Create a block formatting context */
        }

        .header_title {
            text-align: center;
            font-size: 17px;
            font-weight: bold;
            color: #336966;

        }
    </style>
</head>

<body>
    <div class="invoice_body">
        <div class="invoice-container">

            <div class="header" style="width: 210mm;padding:0px;margin:0px;">
                <img src="{{ $headerImage }}" style="width: 210mm;padding:0px;">
            </div>
            <div class="pdfContents" style="min-height:210mm;margin-top:55mm;padding-left:1mm;padding-right:1mm">


            </div>

            <!-- Table -->



            <footer>
                <div class="footer" style="width: 210mm;padding:0px;margin:0px;">
                    <img src="{{ $footerImage }}" style="width: 210mm;padding:0px;">
                </div>
            </footer>
        </div>
    </div>

</body>

</html>
