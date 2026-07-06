<?php

namespace App\Repositories;

use App\Models\PurchaseCategory;
use App\Models\ProductUnit;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;
use App\Models\PurchaseGroup;
use App\Models\PurchaseSubCategory;


/**
 * Class CustomerRepository
 *
 * @version April 3, 2020, 6:37 am UTC
 */
class PurchaseSubCategoryRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'name',
        'purchase_group_id',
        'purchase_category_id',
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
        return PurchaseSubCategory::class;
    }
    public function getGroups(){
        return PurchaseGroup::pluck('name','id');
    }
    public function getCategories()
    {
        return PurchaseCategory::orderBy('id','desc')->get();
    }

    public function create($input)
    {
        return PurchaseSubCategory::create(Arr::only($input, ['name', 'purchase_group_id','description','purchase_category_id']));
    }
}
