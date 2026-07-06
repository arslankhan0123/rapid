<?php

namespace App\Repositories;

use App\Models\AssetCategory;
use App\Models\ProductUnit;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;
use App\Models\ExpenseSubCategory;
use App\Models\Department;
use App\Models\ExpenseCategory;

/**
 * Class CustomerRepository
 *
 * @version April 3, 2020, 6:37 am UTC
 */
class ExpenseSubCategoryRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'expense_category_id',
        'name',
        'description',
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
        return ExpenseSubCategory::class;
    }

    public function create($input)
    {
        return ExpenseSubCategory::create(Arr::only($input, ['name', 'description', 'expense_category_id']));
    }
    public function getExpenseCategory()
    {
        return  ExpenseCategory::pluck('name', 'id'); // Retrieves departments as key-value pairs
    }
}
