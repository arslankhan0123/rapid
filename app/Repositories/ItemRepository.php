<?php

namespace App\Repositories;

use App\Models\Item;
use App\Models\ItemGroup;
use App\Models\TaxRate;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ItemRepository
 *
 * @version April 7, 2020, 4:28 am UTC
 */
class ItemRepository extends BaseRepository
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
        return Item::class;
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
        $data['itemGroups'] = ItemGroup::orderBy('name', 'asc')->pluck('name', 'id')->toArray();

        return $data;
    }

    /**
     * @param  int  $id
     * @return Builder|Builder[]|Collection|Model|null
     */
    public function getItem($id)
    {
        return Item::with(['firstTax', 'secondTax'])->find($id);
    }
}
