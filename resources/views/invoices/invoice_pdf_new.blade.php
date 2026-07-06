<?php

// company logo
$imagePath = public_path('img/company/company_logo.png');
$imageData = file_get_contents($imagePath);
$base64 = base64_encode($imageData);
$base64Image = 'data:image/jpg;base64,' . $base64;

// company_name image
$imageCompanyPath = public_path('img/company/company_name.png');
$imageCompanyData = file_get_contents($imageCompanyPath);
$companybase64 = base64_encode($imageCompanyData);
$company_name = 'data:image/png;base64,' . $companybase64;

// company_name image
$imagebrandPath = public_path('img/company/company_brand_logo.png');
$imagebrandData = file_get_contents($imagebrandPath);
$brandimagebase64 = base64_encode($imagebrandData);
$brand_image = 'data:image/png;base64,' . $brandimagebase64;

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
            margin-right:0px;
            margin-left:0px;

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
            padding-top: 2mm;
            padding-left: 2mm;
            padding-right: 2mm;
            margin: auto;
            box-sizing: border-box;
            background: rgba(255, 255, 255, 0.5);
            /* Optional: give the container a white background with slight opacity */
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

        .footer {
            text-align: center;
            margin-top: 40px;
            font-size: 12px;
            color: #777;

        }

        .headerDesign {
            display: flex;
            justify-content: center;
            align-items: center;
            width: 100%;
            height: 13px;

            /* Adjust as necessary */
        }

        .left,
        .right {

            /* Adjust width as necessary */
            height: 100%;
            background-color: #f4a078;
            /* Left and right background color */
            position: relative;
        }

        .left {
            clip-path: polygon(0 0, 100% 0, 99% 100%, 0% 100%);
            /* Less complex shape for left */
            /* Shape for left */
            width: 81%;
        }

        .right {
            width: 19%;
            clip-path: polygon(5% 0, 100% 0, 100% 100%, 0% 100%);
            /* Shape for right */
            background-color: #336966;
            /* Background color for right */
        }

        .footerDesign {
            display: flex;
            justify-content: center;
            align-items: center;
            width: 100%;
            height: 13px;

            /* Adjust as necessary */
        }

        .footerleft,
        .footerright {
            /* Adjust width as necessary */
            height: 100%;
            background-color: #f4a078;
            /* Left and right background color */
            position: relative;
        }

        .footerleft {
            clip-path: polygon(0 0, 100% 0, 99% 100%, 0% 100%);
            width: 80%;
        }

        .footerright {
            width: 20%;
            clip-path: polygon(5% 0, 100% 0, 100% 100%, 0% 100%);
            /* Shape for right */
            background-color: #336966;
            /* Background color for right */
        }


        .header img {
            width: 100px;
            height: auto;
        }

        .invoice-title {

            width: 100%;
            margin: auto;
            text-align: center;
        }

        .invoice-title .txtp {

            color: #f4a078;
            line-height: 35px;
            border: 2px solid #336966;
            width: 150px;
            height: 35px;
            text-align: center;
            margin: auto;
            font-size: 22px;
            font-weight: bolder;

        }

        .header {
            text-align: center;
            margin-left: 35px;
            margin-right: 35px;

        }

        /* header css */
        .header .logo {

            width: 10%;
            float: left;

        }

        .header .logo img {
            height: 80px;
        }

        .header .info {
            height: 60px;
            width: 64%;
            float: left;
            margin-left: 35px;
            /* Prevent image repetition */
        }

        .header .info {
            background-size: cover;
            background-position: center center;
            background-repeat: no-repeat;
        }

        .header .info img {
            padding-left: 5px;
            padding-right: 25px;
            width: 95%;
            height: 60px;
            padding-top: 10px;
        }

        .header .right_logo {
            margin-top: 18px;
            float: right;

        }

        .header .right_logo img {
            height: 42px;
            width: 140px;
            padding-top: 3px;
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
            <!-- Header -->
            <div class="headerDesign">
                <div class="left"></div>
                <div class="middle"></div>
                <div class="right"></div>
            </div>
            <div class="header_title">
                <p>Saham Al Manar Trading Contracting and Inspection Testing Service: CR 4031261951</p>
            </div>
            <div class="header clearfix">
                <div class="logo">
                    <img src="{{ $base64Image }}" alt="Logo">
                </div>
                <div class="info">
                    <img src="{{ $company_name }}" alt="Logo">
                </div>
                <div class="right_logo">
                    <img src="{{ $brand_image }}" alt="">
                </div>
            </div>

            <!-- Invoice Info -->
            <div class="invoice-title">
                <div class="txtp">
                    Tax Invoice
                </div>

            </div>



            <div class="pdfContents" style="height:227mm;margin-top:10px;">
                <table class="company-info">
                    <tr>
                        <td>Invoice No.</td>
                        <td>150</td>
                        <td>Invoice Date</td>
                        <td>25-09-2024</td>
                    </tr>
                    <tr>
                        <td>Customer No.</td>
                        <td>102211</td>
                        <td>Invoice Month</td>
                        <td>Oct</td>
                    </tr>
                    <tr>
                        <td>Customer Name</td>
                        <td>Bin Sayeed Abu Zahara</td>
                        <td>VAT Number</td>
                        <td>1000240103000003</td>
                    </tr>
                    <tr>
                        <td>Address</td>
                        <td>Jarwal Makkah</td>
                        <td>Payment Mode</td>
                        <td>Credit</td>
                    </tr>
                    <tr>
                        <td>Email</td>
                        <td>info@binsayeed.com</td>
                        <td>Project Code</td>
                        <td>10420</td>
                    </tr>
                    <tr>
                        <td>Project Location</td>
                        <td>Misfalah</td>
                        <td>Project Name</td>
                        <td>Makkah Project</td>
                    </tr>
                </table>
                <table class="table-bordered">
                    <thead>
                        <tr>
                            <th>SL</th>
                            <th>Department</th>
                            <th>Total Unit</th>
                            <th>Working Hours</th>
                            <th>Overtime Hours</th>
                            <th>Total Hours</th>
                            <th>Rate Hourly</th>
                            <th>Excluding VAT</th>
                            <th>VAT Rate</th>
                            <th>VAT Amount</th>
                            <th>Including VAT</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>1</td>
                            <td>Gypsum Board</td>
                            <td>5</td>
                            <td>100.00</td>
                            <td>20.00</td>
                            <td>120.00</td>
                            <td>5.00</td>
                            <td>475.00</td>
                            <td>15.00%</td>
                            <td>71.25</td>
                            <td>546.25</td>
                        </tr>
                        <tr>
                            <td>2</td>
                            <td>Painter</td>
                            <td>5</td>
                            <td>100.00</td>
                            <td>20.00</td>
                            <td>120.00</td>
                            <td>5.00</td>
                            <td>475.00</td>
                            <td>15.00%</td>
                            <td>71.25</td>
                            <td>546.25</td>
                        </tr>
                        <tr>
                            <td>3</td>
                            <td>Mason</td>
                            <td>5</td>
                            <td>100.00</td>
                            <td>20.00</td>
                            <td>120.00</td>
                            <td>5.00</td>
                            <td>475.00</td>
                            <td>15.00%</td>
                            <td>71.25</td>
                            <td>546.25</td>
                        </tr>
                        <!-- Add more rows as needed -->
                    </tbody>
                </table>

                <!-- Totals Section -->
                <table class="total-section">
                    <tr>
                        <td class="total-title">Total Before Discount</td>
                        <td>2,500.00 SAR</td>
                    </tr>
                    <tr>
                        <td class="total-title">Total Discount</td>
                        <td>125.00 SAR</td>
                    </tr>
                    <tr>
                        <td class="total-title">Total Before VAT</td>
                        <td>2,375.00 SAR</td>
                    </tr>
                    <tr>
                        <td class="total-title">Total VAT Amount</td>
                        <td>356.25 SAR</td>
                    </tr>
                    <tr>
                        <td class="total-title">Total Net Amount</td>
                        <td>2,731.00 SAR</td>
                    </tr>
                </table>
            </div>

            <!-- Table -->


            <!-- Amount in Words -->
            {{-- <p class="amount-in-words">Amount in words: Two hundred eighty-four Saudi Riyals and fifty Halalas only</p>

            <!-- Bank Details -->
            <div class="bank-details">
                <strong>Bank Details:</strong><br>
                Bank Al Rajhi<br>
                Al Manara Manpower Supply Est.<br>
                A/C No: 0420000412<br>
                IBAN: SAR742216623522145551
            </div> --}}

            <!-- Footer -->
            <div class="footer">
                <div class="footerDesign">
                    <div class="footerleft"></div>
                    <div class="footerright"></div>
                </div>
            </div>
        </div>
    </div>

</body>

</html>
