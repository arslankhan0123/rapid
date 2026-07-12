<?php

namespace App\Queries;

use App\Models\JobCategory;

class JobCategoryDataTable
{
    /**
     * @param  array  $input
     * @return JobCategory
     */
    public function get($input = [])
    {
        /** @var JobCategory $query */
        $query = JobCategory::with(['parent', 'creator', 'updater'])->orderBy('created_at', 'desc')->get();
        return $query;
    }
}
