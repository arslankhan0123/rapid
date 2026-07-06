<?php

namespace App\Queries;


use App\Models\AssetCategory;
use App\Models\Department;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class TagDataTable
 */
class DepartmentNewDataTable
{
    /**
     * @param  array  $input
     * @return Department
     */
    public function get($input = [])
    {
        /** @var Department $query */
        $query = Department::orderBy('created_at', 'desc')->get();
        return $query;
    }
}
