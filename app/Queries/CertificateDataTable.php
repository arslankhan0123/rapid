<?php

namespace App\Queries;

use App\Models\Certificate;
use App\Models\SampleReceiving;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

/**
 * Class SampleCategoryDataTable
 */
class CertificateDataTable
{
    /**
     * @param  array  $input
     * @return Builder
     */
    public function get($input = []): Builder
    {
        $query = Certificate::query()
            ->with('type')
            ->select([
                'id',
                'certificate_number',
                "type_id",
                DB::raw("DATE_FORMAT(date, '%d-%b-%Y') as formatted_date"),
                'employee',
                'lab_manager',
                'general_manager',
                'description'
            ]);

        // Apply filter for group if present
        if (!empty($input['group'])) {
            $query->where('group', $input['group']);
        }

        // List of allowed columns for sorting
        $allowedColumns = [
            'id',
            'certificate_number',
            'date',
            'employee',
            'lab_manager',
            'general_manager',
            'description',
            "type_id",
        ];

        // Handle dynamic sorting
        if (!empty($input['order']) && isset($input['columns'])) {
            $columnIndex = $input['order'][0]['column'];
            $sortDirection = $input['order'][0]['dir'] ?? 'desc';

            // Get column name from the columns array and validate it
            $columnName = $input['columns'][$columnIndex]['data'] ?? 'updated_at';
            $columnName = in_array($columnName, $allowedColumns) ? $columnName : 'updated_at';

            // Apply sorting to query
            $query->orderBy($columnName, $sortDirection);
        } else {
            // Default sorting on initial load
            $query->orderBy('updated_at', 'desc');
        }


        return $query;
    }
}
