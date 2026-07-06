<?php

namespace App\Repositories;

use App\Models\Product;
use App\Models\ProductGroup;
use App\Models\TaxRate;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use App\Models\ServiceCategory;

/**
 * Class ProductRepository
 *
 * @version October 12, 2021, 10:50 am UTC
 */
class ProductRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'title',
        'description',
        'tax_1_id',
        'tax_2_id',
        'item_group_id',
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
        return Product::class;
    }

    /**
     * @return mixed
     */
    public function getSyncListForItem()
    {
        $taxes = [];

        $taxRates = TaxRate::get();

        foreach ($taxRates as $tax) {
            $taxes[$tax->id] = $tax->tax_rate.'%';
        }

        $data['taxes'] = $taxes;
        $data['itemGroups'] = ProductGroup::orderBy('name', 'asc')->pluck('name', 'id')->toArray();

        return $data;
    }

    /**
     * @param  int  $id
     * @return Builder|Builder[]|Collection|Model|null
     */
    public function getProduct($id)
    {
        return Product::with(['firstTax', 'secondTax'])->find($id);
    }
    public function getCategories(){
         return ServiceCategory::pluck('name','id');
    }
}
