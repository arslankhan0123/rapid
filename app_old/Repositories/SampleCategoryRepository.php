<?php

namespace App\Repositories;
use App\Models\SampleCategory;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;

/**
 * Class CustomerRepository
 *
 * @version April 3, 2020, 6:37 am UTC
 */
class SampleCategoryRepository extends BaseRepository
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
        return SampleCategory::class;
    }

    public function create($input)
    {
        return SampleCategory::create(Arr::only($input, ['name','description']));
    }
    public function getSampleCategory()
    {
        return SampleCategory::get();
    }
}
