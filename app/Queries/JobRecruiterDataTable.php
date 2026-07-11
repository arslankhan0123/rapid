<?php

namespace App\Queries;

use App\Models\JobRecruiter;

/**
 * Class JobRecruiterDataTable
 */
class JobRecruiterDataTable
{
    /**
     * @param  array  $input
     * @return JobRecruiter
     */
    public function get($input = [])
    {
        /** @var JobRecruiter $query */
        $query = JobRecruiter::query()->select('job_recruiters.*');

        return $query;
    }
}
