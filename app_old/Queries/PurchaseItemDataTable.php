<?php

namespace App\Queries;


use App\Models\AssetCategory;
use Illuminate\Database\Eloquent\Builder;
use App\Models\PurchaseItem;

/**
 * Class TagDataTable
 */
class PurchaseItemDataTable
{

    public function get($input = [])
    {
        /** @var PurchaseItem $query */
        $query = PurchaseItem::query()
            ->select(['id', 'code', 'barcode', 'description','purchase_category_id','name','price','qty_in','qty_out','qty_current','stock','cost_price']) // Select specific columns

            ->with([ 'category']);

        $query = $query->get();
        return $query;
    }
}
