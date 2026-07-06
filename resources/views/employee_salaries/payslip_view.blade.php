@php

    // $format = $settings['print_format'] ?? 1;

    $baseImagePath = public_path('img/company');
    // Company logo
    $imagePath = $baseImagePath . '/company_logo.png';
    $imageData = file_get_contents($imagePath);
    $base64 = base64_encode($imageData);
    $company_logo = 'data:image/png;base64,' . $base64; // Ensure correct format
@endphp


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payslip</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        .container {
            width: 100%;
            padding: 15mm;
            font-size: 12px;
            max-width: 180mm;
            /* Adjust width to ensure content fits */
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 5px;
        }

        .header .company-info {
            font-size: 12px;
        }

        .header img {
            width: 50px;
            height: auto;
        }

        .title {
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            margin: 8px 0 15px;
            border-top: 1px solid #666;
            padding-top: 5px;
        }

        .summary {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            font-size: 11px;
        }

        .summary .employee-info,
        .summary .net-pay {
            width: 48%;
        }

        .summary .net-pay {
            text-align: center;
        }

        .summary .net-pay p {
            margin: 3px 0;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
        }

        .table th,
        .table td {
            padding: 6px;
            border: 1px solid #ddd;
            text-align: left;
            font-size: 12px;
        }

        .amount {
            text-align: right !important;

        }

        .table th {
            background-color: #f5f5f5;
            font-weight: bold;

        }

        .table th:first-child,
        .table td:first-child {
            /* text-align: left; */
        }

        .net-pay-table {
            margin-top: 0px;
            font-size: 11px;
        }

        .footer {
            margin-top: 15px;
            text-align: center;
            font-size: 11px;
        }

        .footer p {
            margin-bottom: 3px;
        }

        .text-left {
            text-align: start;
        }

        .text-right {
            text-align: end;
        }
    </style>
</head>

<body>
    <div class="container">

        <table
            style="width:100%; height:140px; border:1px solid rgb(199, 199, 199); border-collapse: collapse;border-bottom:none;">
            <tr style="border-bottom:1px solid rgb(199, 199, 199);">
                <td style="height: 60px;">
                    <div class="company-info" style="padding:10px;">
                        <h2>SMIT Engineering</h2>
                        <p>Dubai</p>
                    </div>
                </td>
                <td style="padding:10px;">
                    <div style="width:170px;margin-left:80px;">
                            <div style="height:90px; background-image: url('{{ $company_logo }}'); background-size: 58%;  background-position: center; background-repeat: no-repeat;">

                            </div>
                    </div>
                    {{-- <img src="{{ $company_logo }}" style="width: 100px;height:90px; padding: 0px;float:right;"> --}}
                </td>

            </tr>
            <tr>
                <td colspan="2"
                    style="border-bottom:1px solid rgb(199, 199, 199);text-align:center; height:40px;font-weight:bolder;font-size:15px;">
                    <h>Payslip for the month of November 2024</p>
                </td>
            </tr>
            <tr style="">
                <td style="border-right: 1px solid rgb(199, 199, 199); width:60%; height:140px;padding:10px;">
                    <div class="employee-info">
                        <p style="font-weight: bold; padding-bottom:10px;">EMPLOYEE PAY SUMMARY</p>
                        <table style="width: 80%; border-collapse: collapse;">
                            <tr>
                                <td style="font-weight: bold;">Employee Name</td>
                                <td style="text-align: center; padding: 5px;">:</td>
                                <td style="padding: 5px;">Hasib Ahmed</td>
                            </tr>
                            <tr>
                                <td style="font-weight: bold; ">Designation</td>
                                <td style="text-align: center; padding: 5px;">:</td>
                                <td style="padding: 5px;">DevOps Eng. </td>
                            </tr>
                            <tr>
                                <td style="font-weight: bold; ">Date of Joining</td>
                                <td style="text-align: center; padding: 5px;">:</td>
                                <td style="padding: 5px;">30/06/2020</td>
                            </tr>
                            <tr>
                                <td style="font-weight: bold; ">Pay Period</td>
                                <td style="text-align: center; padding: 5px;">:</td>
                                <td style="padding: 5px;">November 2024</td>
                            </tr>
                            <tr>
                                <td style="font-weight: bold; ">Pay Date</td>
                                <td style="text-align: center; padding: 5px;">:</td>
                                <td style="padding: 5px;">31/01/2024</td>
                            </tr>
                        </table>
                    </div>

                </td>
                <td style="width: 40%;height:140px;">
                    <div class="net-pay" style="text-align: center;">
                        <h4>Employee Net Pay</h4>
                        <p style="font-size: 30px; color: black;font-weight:bolder;">65,500.00 SAR</p>
                        <p>Paid Days: 31 | LOP Days: 0</p>
                    </div>
                </td>
            </tr>
            <tr style="margin:0px;padding:0px;">
                <td style="border-right: 1px solid rgb(199, 199, 199); width:60%;height:140px; ">

                    <table class="table">

                        <tr>
                            <th>EARNINGS</th>
                            <th class="amount">AMOUNT</th>
                        </tr>
                        <tr>
                            <td>BASIC</td>
                            <td class="amount">45000 SAR</td>
                        </tr>
                        <tr>
                            <td>House Rent Allowance</td>
                            <td class="amount">15000 SAR</td>
                        </tr>
                        <tr>
                            <td>Fixed Allowance</td>
                            <td class="amount">5500 SAR</td>
                        </tr>
                        <tr>
                            <td><strong>Gross Earnings</strong></td>
                            <td class="amount"><strong>65,500.00 SAR</strong></td>
                        </tr>
                    </table>
                </td>
                <td style=" width: 40%;">
                    <div style="height:140px;padding-top:3.2%;">
                        <table class="table" style="width: 100%; border-collapse: collapse; margin-top: 0;">
                            <tbody>
                                <tr>
                                    <th style="text-align: left;">DEDUCTIONS</th>
                                    <th class="amount" style="text-align: right;">AMOUNT</th>
                                </tr>
                                <tr>
                                    <td> Professional Tax</td>
                                    <td class="amount" style="text-align: right; padding: 5px;">200 SAR</td>
                                </tr>
                                <tr>
                                    <td style="">/</td>
                                    <td class="amount" style="text-align: right; padding: 5px;"></td>
                                </tr>
                                <!-- Removed unnecessary height -->
                                <tr>
                                    <td style="">/</td>
                                    <td class="amount" style="text-align: right; padding: 5px;"></td>
                                </tr>
                                <tr>
                                    <td style=""><strong>Total Deductions</strong></td>
                                    <td class="amount" style="text-align: right; padding: 5px;">
                                        <strong>200 SAR</strong>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </td>
            </tr>
        </table>

        </td>
        </tr>

        </table>


        <table class="table net-pay-table">
            <tr>
                <th style="width: 565px;">NET PAY</th>
                <th class="amount">AMOUNT</th>
            </tr>
            <tr>
                <td style="text-align: left;">Gross Earnings</td>
                <td class="amount">65,500.00 SAR</td>
            </tr>
            <tr>
                <td style="text-align: left;">Total Deductions</td>
                <td class="amount">- 200.00 SAR</td>
            </tr>
            <tr>
                <td style="text-align: right;padding-right:10px;"><strong>Total Net Payable</strong></td>
                <td class="amount"><strong>65,300.00 SAR</strong></td>
            </tr>
            <tr>
                <td colspan="2" style="text-align: center;padding:20px;">
                    <p>Total Net Payable 65,300.00 (Sixty-Five Thousand Three Hundred Saudi Riyals Only)</p>
                    <p>**Total Net Payable = Gross Earnings - Total Deductions</p>
                </td>
            </tr>

            <tr>
                <td colspan="2">
                    <table style="width: 90%;margin:auto;margin-top:0px;border:none !important;">
                        <tr>
                            <td style="text-align: center;border:none !important;">Employer Signature</td>
                            <td style="width: 40%;height:100px;border:none !important;"></td>
                            <td style="text-align: center;border:none !important;">Employee Signature</td>
                        </tr>
                        <tr>
                            <td style="border:none !important;">
                                <hr>
                            </td>
                            <td style="width: 40%;border:none !important;"></td>
                            <td style="border:none !important;">
                                <hr>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>


    </div>
</body>

</html>
