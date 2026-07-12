<?php

namespace App\Queries;

use App\Models\JobSkill;

class JobSkillDataTable
{
    /**
     * @param  array  $input
     * @return JobSkill
     */
    public function get($input = [])
    {
        /** @var JobSkill $query */
        $query = JobSkill::with(['category', 'creator', 'updater'])->orderBy('created_at', 'desc')->get();
        return $query;
    }
}
