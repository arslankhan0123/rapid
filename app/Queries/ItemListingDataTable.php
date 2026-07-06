<?php

namespace App\Queries;


use App\Models\AssetCategory;
use Illuminate\Database\Eloquent\Builder;
use App\Models\PurchaseItem;

/**
 * Class TagDataTable
 */
class ItemListingDataTable
{

    public function get($input = [])
    {
        /** @var PurchaseItem $query */
        $query = PurchaseItem::query()
            ->select([
                'id',
                'code',
                'barcode',
                'description',
                'purchase_category_id',
                'name',
                'price',
                'cost_price',
                'qty_in',
                'qty_out',
                'qty_current',
                'stock',
                'unit_id'
            ])
            ->with([
                'category:id,name',
                'unit:id,title'
            ]);

        return $query->get();
    }

}
