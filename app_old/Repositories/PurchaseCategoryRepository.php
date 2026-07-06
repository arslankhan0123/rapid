<?php

namespace App\Repositories;

use App\Models\PurchaseCategory;
use App\Models\ProductUnit;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;
use App\Models\PurchaseGroup;


/**
 * Class CustomerRepository
 *
 * @version April 3, 2020, 6:37 am UTC
 */
class PurchaseCategoryRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'name',
        'purchase_group_id',
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
        return PurchaseCategory::class;
    }
    public function getGroups(){
        return PurchaseGroup::pluck('name','id');
    }

    public function create($input)
    {
        return PurchaseCategory::create(Arr::only($input, ['name', 'purchase_group_id','description']));
    }
}
