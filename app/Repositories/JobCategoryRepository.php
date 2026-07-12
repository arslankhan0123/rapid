<?php

namespace App\Repositories;

use Illuminate\Support\Arr;
use App\Models\JobCategory;

class JobCategoryRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'category_code',
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
        return JobCategory::class;
    }

    public function create($input)
    {
        return JobCategory::create(Arr::only($input, [
            'category_code',
            'name',
            'parent_id',
            'description',
            'display_order',
            'is_active'
        ]));
    }
}
