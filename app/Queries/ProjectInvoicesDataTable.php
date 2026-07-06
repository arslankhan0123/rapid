<?php

namespace App\Queries;


use App\Models\MonthlyAttendanceInvoice;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

/**
 * Class TagDataTable
 */
class ProjectInvoicesDataTable
{

    public function get($input = [])
    {
        $query = MonthlyAttendanceInvoice::with(['customer', 'project', 'postedBy', 'updatedBy']);

        // Filter based on input
        if (!empty($input['month'])) {
            $query->whereMonth('posted_at', Carbon::parse($input['month'])->month)
                ->whereYear('posted_at', Carbon::parse($input['month'])->year);
        }

        if (!empty($input['customer_id'])) {
            $query->where('customer_id', $input['customer_id']);
        }

        if (!empty($input['project_id'])) {
            $query->where('project_id', $input['project_id']);
        }


        return $query->orderBy('created_at', 'desc')->get();
    }
}
