<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseItem extends Model
{
    use HasFactory;

    protected $table = 'purchase_items';
    public static $rules = [
        'full_name' => 'required',
        'purchase_group_id' =>  'nullable|exists:purchase_groups,id',
        'purchase_category_id' =>  'nullable|exists:purchase_categories,id',
        'purchase_sub_category_id' =>  'nullable|exists:purchase_sub_categories,id',
        'unit_id' =>  'nullable|exists:product_units,id',
        'price' => 'nullable|numeric',
        'code' => "required|unique:purchase_items,code",
        'specifications' => 'nullable|string',
        'processor' => 'nullable|string|max:255',
        'ram' => 'nullable|string|max:255',
        'storage' => 'nullable|string|max:255',
        'casing' => 'nullable|string|max:255',
    ];
    protected $fillable = [
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
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'name' => 'string'
    ];



    public function group()
    {
        return $this->belongsTo(PurchaseGroup::class, 'purchase_group_id');
    }
    public function category()
    {
        return $this->belongsTo(PurchaseCategory::class, 'purchase_category_id');
    }
    public function subCategory()
    {
        return $this->belongsTo(PurchaseSubCategory::class, 'purchase_sub_category_id');
    }
    public function unit()
    {
        return $this->belongsTo(ProductUnit::class, 'unit_id');
    }
    public function brand()
    {
        return $this->belongsTo(ProductBrand::class, "brand_id");
    }
    public function size()
    {
        return $this->belongsTo(Size::class, "size_id");
    }
    public function color()
    {
        return $this->belongsTo(ProductColor::class, "color_id");
    }
    public function orderItems()
    {
        return $this->hasMany(PosOrderItem::class, 'item_id');
    }
    public function refundItems()
    {
        return $this->hasMany(PosRefundItem::class, 'item_id');
    }
}
