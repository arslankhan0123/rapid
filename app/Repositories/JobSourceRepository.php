<?php

namespace App\Repositories;

use Illuminate\Support\Arr;
use App\Models\JobSource;

class JobSourceRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
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
        return JobSource::class;
    }

    public function create($input)
    {
        return JobSource::create(Arr::only($input, ['name','description']));
    }
}
