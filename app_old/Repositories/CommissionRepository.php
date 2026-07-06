<?php

namespace App\Repositories;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use App\Models\Commission;
use App\Models\Employee;

/**
 * Class CustomerRepository
 *
 * @version April 3, 2020, 6:37 am UTC
 */
class CommissionRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'desciption',
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
        return Commission::class;
    }

    public function create($input)
    {
        return Commission::create(Arr::only($input, [
            'employee_id',
            'commission',
            'description',
        ]));
    }
    public function getEmplyee()
    {
        return  Employee::pluck('name', 'id'); // Retrieves departments as key-value pairs
    }
}
