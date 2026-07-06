<?php

namespace App\Repositories;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use App\Models\Award;
use App\Models\Employee;

/**
 * Class CustomerRepository
 *
 * @version April 3, 2020, 6:37 am UTC
 */
class AwardRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'name',
        'desciption',
        'award_by',
        'gift',
        'date',
        'employee_id'
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
        return Award::class;
    }

    public function create($input)
    {
        return Award::create(Arr::only($input, [
            'name',
            'description',
            'gift',
            'date',
            'employee_id',
            'award_by',
        ]));
    }
    public function getEmplyee()
    {
        return  Employee::pluck('name', 'id'); // Retrieves departments as key-value pairs
    }
}
