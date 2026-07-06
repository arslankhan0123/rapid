<?php

namespace App\Repositories;

use App\Models\AssetCategory;
use App\Models\ProductUnit;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;
use App\Models\Designation;
use App\Models\Department;
use App\Models\SubDepartment;

/**
 * Class CustomerRepository
 *
 * @version April 3, 2020, 6:37 am UTC
 */
class DesignationRepository extends BaseRepository
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
        return Designation::class;
    }

    public function create($input)
    {
        return Designation::create(Arr::only($input, ['name', 'description','department_id','sub_department_id']));
    }
    public function getDepartment()
    {
        return Department::pluck('name', 'id');
    }
    public function getSubDepartment($department_id = null)
    {
        return SubDepartment::select(['name', 'id', 'department_id'])->get();
    }
}
