<?php

namespace App\Queries;

use App\Models\Attendance;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class TagDataTable
 */
class AttendaceDataTable
{

    public function get($input = [])
    {
        $query = Attendance::with(['iqmaEmployee','customer','project'])->orderBy('updated_at', 'desc');

        if (!empty($input['month'])) {
            $yearMonth = explode('-', $input['month']);
            $year = $yearMonth[0];
            $month = $yearMonth[1];
            $query->whereYear('date', $year)->whereMonth('date', $month);;
        }
        if (!empty($input['customer_id'])) {
            $query->where('customer_id', $input['customer_id']);
        }
        if (!empty($input['project_id'])) {
            $query->where('project_id', $input['project_id']);
        }
        // Check if there's a search query and apply it
        if (!empty($input['search']['value'])) {

            $searchValue = $input['search']['value'];
            $query->whereHas('employee', function ($query) use ($searchValue) {
                $query->where('iqama_no', 'like', "%{$searchValue}%");
            });
        }


        return $query->get(); // Ensure that the structure matches DataTable's expectations
    }
}
