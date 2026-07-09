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
            ->select(['id', 'code', 'barcode', 'description','purchase_category_id', 'purchase_group_id', 'name', 'full_name', 'price','qty_in','qty_out','qty_current','stock','cost_price', 'created_at']) // Select specific columns

            ->with([ 'category', 'group']);

        $query = $query->get();
        return $query;
    }
}
