<?php

namespace App\Repositories;

use Illuminate\Support\Arr;
use App\Models\JobSkill;

class JobSkillRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'skill_code',
        'name',
        'description'
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
        return JobSkill::class;
    }

    public function create($input)
    {
        return JobSkill::create(Arr::only($input, [
            'skill_code',
            'name',
            'category_id',
            'level',
            'description',
            'is_active'
        ]));
    }
}
