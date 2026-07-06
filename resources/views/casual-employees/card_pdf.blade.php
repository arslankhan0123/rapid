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

    <table style="width: 100%; margin-bottom: 10px; margin-left: 12px;margin-top:10px;">
        <tr>
            <td style="width: 40px;">
                <img src="{{ $companyImage }}" alt="Company Logo"
                    style="width: 30px; height: 30px; border-radius: 50%; border: 2px solid #ffffff;">
            </td>
            <td style="vertical-align: middle;">
                <div style="font-size: 7pt; font-weight: bold;">{!! $companyName !!}</div>
            </td>
        </tr>
    </table>




    <div style="text-align: center; margin-bottom: 10px;">
        <img src="{{ $employeeImage }}" alt="Employee Image" style="width: 90px; height: 90px; border-radius: 50%; ">
    </div>


    <div style="text-align: center; margin-top: 8px;">
        <div style="font-size: 15px; font-weight: bold; margin-bottom: 4px;text-transform: uppercase;">
            {{ $employee->name }}
        </div>
        <div style="font-size: 13px; color: #000000;text-transform: uppercase;">
            {{ $employee->designation?->name ?? '' }}
        </div>
    </div>


    <table style="width: 50%;  margin: 10px auto 0 auto; font-size: 10px; border-collapse: collapse;">
        <tr>
            <td style="padding: 4px; font-weight: bold;">ID</td>
            <td>:</td>
            <td style="padding: 4px;">{{ $employee->iqama_no ?? '-' }}</td>
        </tr>
        <tr>
            <td style="padding: 4px; font-weight: bold;">E-mail</td>
            <td>:</td>
            <td
                style="padding: 4px; word-wrap: break-word; word-break: break-all; white-space: normal;width:100px;max-width:100px;">
                {{ $employee->email ?? '-' }}
            </td>
        </tr>
        <tr>
            <td style="padding: 4px; font-weight: bold;">Phone</td>
            <td>:</td>
            <td style="padding: 4px;">{{ $employee->phone ?? '-' }}</td>
        </tr>
    </table>


</body>

</html>
