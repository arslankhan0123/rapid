<?php

namespace App\Queries;

use App\Models\JobPosition;

class JobPositionDataTable
{
    /**
     * @param  array  $input
     * @return JobPosition
     */
    public function get($input = [])
    {
        /** @var JobPosition $query */
        $query = JobPosition::with(['category', 'department', 'creator', 'updater'])->orderBy('created_at', 'desc')->get();
        return $query;
    }
}
