<?php

namespace App\Queries;

use App\Models\JobSource;

class JobSourceDataTable
{
    /**
     * @param  array  $input
     * @return JobSource
     */
    public function get($input = [])
    {
        /** @var JobSource $query */
        $query = JobSource::orderBy('created_at', 'desc')->get();
        return $query;
    }
}
