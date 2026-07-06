<?php

namespace App\Repositories;

use App\Models\AssetCategory;
use App\Models\ProductUnit;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;
use App\Models\SubDepartment;
use App\Models\Department;

/**
 * Class CustomerRepository
 *
 * @version April 3, 2020, 6:37 am UTC
 */
class SubDepartmentRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'name',
        'description',
        'department_id'
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
        return SubDepartment::class;
    }

    public function create($input)
    {
        return SubDepartment::create(Arr::only($input, ['name', 'description', 'department_id']));
    }
    public function getDepartments()
    {
        return  Department::pluck('name', 'id'); // Retrieves departments as key-value pairs
    }
}
