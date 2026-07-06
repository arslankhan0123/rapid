<!DOCTYPE html>
<html>

<head>
    <title>Leave Application</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        h1 {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }
    </style>
</head>

<body>
    <h1>leave Application</h1>

    <table>
        <tr>
            <th>{{ __('messages.branches.name') }}</th>
            <td>{{ $leaveApplication->branch?->name ?? __('messages.common.n/a') }}</td>
        </tr>
        <tr>
            <th>{{ __('messages.employees.name') }}</th>
            <td>{{ $leaveApplication->employee?->name ?? __('messages.common.n/a') }}</td>
        </tr>
        <tr>
            <th>{{ __('messages.employees.id') }}</th>
            <td>{{ $leaveApplication->employee?->iqama_no ?? __('messages.common.n/a') }}</td>
        </tr>

        <tr>
            <th>{{ __('messages.designations.name') }}</th>
            <td>{{ $leaveApplication->employee?->designation?->name ?? __('messages.common.n/a') }}</td>
        </tr>
        <tr>
            <th>{{ __('messages.designations.name') }}</th>
            <td>{{ $leaveApplication->employee?->designation?->name ?? __('messages.common.n/a') }}</td>
        </tr>
        <tr>
            <th>{{ __('messages.leave-applications.from_date') }}</th>
            <td>{{ \Carbon\Carbon::parse($leaveApplication->from_date)->format('d-m-Y') ?? '' }}</td>
        </tr>
        <tr>
            <th>{{ __('messages.leave-applications.end_date') }}</th>
            <td>{{ \Carbon\Carbon::parse($leaveApplication->end_date)->format('d-m-Y') ?? '' }}</td>
        </tr>

        <tr>
            <th>{{ __('messages.leave-applications.total_days') }}</th>
            <td>{{ $leaveApplication->total_days ?? '' }}</td>
        </tr>

        <tr>
            <th>{{ __('messages.leave-applications.leave_type') }}</th>
            <td>{{ $leaveApplication->leave?->name ?? __('messages.common.n/a') }}</td>
        </tr>
         <tr>
            <th>{{ __('messages.leave-applications.paid_leave_days') }}</th>
            <td>{{ $leaveApplication->paid_leave_days ?? '' }}</td>
        </tr>
         <tr>
            <th>{{ __('messages.leave-applications.paid_leave_amount') }}</th>
            <td>{{ $leaveApplication->paid_leave_amount ?? '' }}</td>
        </tr>
         <tr>
            <th>{{ __('messages.leave-applications.ticket_amount') }}</th>
            <td>{{ $leaveApplication->ticket_amount ?? '' }}</td>
        </tr>
         <tr>
            <th>{{ __('messages.leave-applications.claim_amount') }}</th>
            <td>{{ $leaveApplication->claim_amount ?? '' }}</td>
        </tr>
        <tr>
            <th>{{ __('messages.common.created_on') }}</th>
            <td>
                {{ $leaveApplication->created_at->translatedFormat('jS M, Y') }}
            </td>
        </tr>
        <tr>
            <th>{{ __('messages.common.last_updated') }}</th>
            <td>
                {{ $leaveApplication->updated_at->translatedFormat('jS M, Y') }}
            </td>
        </tr>
        <tr>
            <th>{{ __('messages.leave-applications.description') }}</th>
            <td>{!! isset($leaveApplication->description)
                ? html_entity_decode($leaveApplication->description)
                : __('messages.common.n/a') !!}</td>
        </tr>
    </table>


</body>

</html>
