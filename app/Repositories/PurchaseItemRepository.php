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
use App\Models\PurchaseItem;
use App\Models\ProductBrand;
use App\Models\ProductColor;
use App\Models\Size;
use App\Models\PaymentMode;

/**
 * Class CustomerRepository
 *
 * @version April 3, 2020, 6:37 am UTC
 */
class PurchaseItemRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'name',
        'purchase_group_id',
        'purchase_category_id',
        'purchase_sub_category_id',
        'price',
        'description',
        'unit_id',
        'code',
        'barcode',
        'brand_id',
        'color_id',
        'size_id',
        'stock',
        'full_name',
        'image',
        'stock',
        'cost_price',
        'qty_in',
        'qty_out',
        'qty_current',
        'specifications',
        'processor',
        'ram',
        'storage',
        'casing',
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
        return PurchaseItem::class;
    }
    public function getGroups()
    {
        return PurchaseGroup::pluck('name', 'id');
    }
    public function getCategories()
    {
        return PurchaseCategory::orderBy('id', 'desc')->get();
    }
    public function getSubCategories()
    {
        return PurchaseSubCategory::orderBy('id', 'desc')->get();
    }
    public function getUnits()
    {
        return ProductUnit::orderBy('id', 'desc')->pluck('title', 'id');
    }

    public function create($input)
    {

        $data = Arr::only($input, array_merge($this->getFieldsSearchable(), ['image']));
        $data['qty_in'] = $data['stock'] ?? 0;
        $data['qty_current'] = $data['stock'] ?? 0;
        return PurchaseItem::create($data);
    }

    // In your model (e.g., Order.php)
    public function getTaxRateAttribute($value)
    {
        return $value ?: 15;
    }

    public function updateItem($input, $id)
    {
        $data = Arr::only($input, array_merge($this->getFieldsSearchable(), ['image']));

        if (isset($data['stock'])) {
            $data['qty_in'] = $data['stock'];
            $data['qty_current'] = $data['stock'];
        }

        $purchaseItem = PurchaseItem::findOrFail($id);
        $purchaseItem->update($data);
        return $purchaseItem;
    }


    public function getBrands()
    {

        return ProductBrand::pluck('title', 'id');
    }
    public function getSizes()
    {
        return Size::pluck('title', 'id');
    }
    public function getColors()
    {
        return ProductColor::pluck('title', 'id');
    }
    public function getPaymentModes()
    {
        return PaymentMode::pluck('name', 'id');
    }

    public function updatePurchaseItemInventory($item_id, $qty, $status = true)
    {
        $purchaseItem = PurchaseItem::findOrFail($item_id);

        if ($status) {
            // Adding inventory
            $purchaseItem->qty_in += $qty;
            $purchaseItem->qty_current += $qty;
        } else {
            // Subtracting from current stock only
            $purchaseItem->qty_current -= $qty;
            $purchaseItem->qty_out += $qty;
        }

        $purchaseItem->save();

        return $purchaseItem;
    }
    public function getAllItems()
    {
        return PurchaseItem::all();
    }



}
