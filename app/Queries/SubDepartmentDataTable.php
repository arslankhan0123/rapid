<?php

namespace App\Queries;
use App\Models\SubDepartment;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class TagDataTable
 */
class SubDepartmentDataTable
{
    /**
     * @param  array  $input
     * @return SubDepartment
     */
    public function get($input = [])
    {
        /** @var SubDepartment $query */
        $query = SubDepartment::with('department')->get();
        return $query;
    }
}
