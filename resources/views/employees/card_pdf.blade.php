<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>ID Card</title>
    <style>
        @page {
            size: 5.59cm 8.38cm;
            margin: 0;
        }

        html,
        body {
            margin: 0;
            padding: 0;
            width: 5.59cm;
            height: 8.38cm;
            font-family: Arial, sans-serif;
        }

        body {
            overflow: hidden;
            background-image: url('{{ $layout }}');
            background-size: cover;
            background-repeat: no-repeat;
            background-position: center;
        }



        .company-name {
            font-size: 5.21pt;
            font-weight: bold;
            text-align: left;
        }

        .employee-name {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 4px;
        }

        .designation {
            font-size: 11px;
            color: #555;
            margin-bottom: 8px;
        }
    </style>
</head>

<body>


    <div style="position: absolute; top: 20px; left: 50%; transform: translateX(-50%); width: 91%;">

        <table style="text-align: center; height: 35px;">
            <tr>
                <td style="display: inline-flex; align-items: center; justify-content: center;">
                    <img src="{{ $companyImage }}" alt="Company Logo"
                        style="width: 25px; height: 25px; border-radius: 5%; vertical-align: middle; margin-right: 1px;background:white;border-radius:2px;">
                    <span
                        style="font-size: 10pt; font-weight: bold; color: white; vertical-align: middle; line-height: 0;">
                        {!! $compnayName !!}
                    </span>

                </td>
            </tr>
        </table>
    </div>
    <div style="position: absolute; top: 70px; left: 50%; transform: translateX(-50%);">

        <div style="text-align: center; margin-bottom: 10px;height: 82px;">
            <img src="{{ $employeeImage }}" alt="Employee Image" style="width: 82px; height: 82px; border-radius: 5%; ">
        </div>

    </div>
    <div style="position: absolute; top: 160px; left: 50%; transform: translateX(-50%);">
        <div style="text-align: center; ">
            <div
                style="font-size: 16px; font-weight: bold; margin-bottom: 4px;text-transform: uppercase;color:#26ace1;">
                {{ $employee->name }}
            </div>
            <div style="font-size: 12px; color: #5f6060;text-transform: uppercase;font-weight:bold;">
                {{ $employee->designation?->name ?? '' }}
            </div>
        </div>


        <table
            style="width: 50%;  margin: 10px auto 0 auto; font-size: 10px; border-collapse: collapse;color: #5f6060;">
            <tr>
                <td style="padding: 3px; font-weight: bold;">DOB</td>
                <td>:</td>
                <td style="padding: 3px;">
                    {{ $employee->join_date ? \Carbon\Carbon::parse($employee->join_date)->format('d/m/Y') : '-' }}
                </td>
            </tr>
            <tr>
                <td style="padding: 3px; font-weight: bold;">Mail</td>
                <td>:</td>
                <td
                    style="padding: 3px; word-wrap: break-word; word-break: break-all; white-space: normal;width:100px;max-width:100px;">
                    {{ $employee->email ?? '-' }}
                </td>
            </tr>
            <tr>
                <td style="padding: 3px; font-weight: bold;">Phone</td>
                <td>:</td>
                <td style="padding: 3px;">{{ $employee->phone ?? '-' }}</td>
            </tr>
        </table>

    </div>

</body>

</html>
