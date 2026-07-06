<?php

namespace App\Queries;


use App\Models\AssetCategory;
use App\Models\Designation;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class TagDataTable
 */
class DesignationDataTable
{
    /**
     * @param  array  $input
     * @return Designation
     */
    public function get($input = [])
    {
        /** @var Designation $query */
        $query = Designation::with(['department', 'subDepartment'])->orderBy('created_at', 'desc')->get();
        return $query;
    }
}
