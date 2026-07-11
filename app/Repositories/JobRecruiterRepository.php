<?php

namespace App\Repositories;

use App\Models\JobRecruiter;

class JobRecruiterRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'recruiter_name',
    ];

    /**
     * Return searchable fields
     *
     * @return array
     */
    public function getFieldsSearchable()
    {
        return $this->fieldSearchable;
    }

    /**
     * Configure the Model
     **/
    public function model()
    {
        return JobRecruiter::class;
    }
}
