<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseSubCategory extends Model
{
    use HasFactory;

    protected $table = 'purchase_sub_categories';
    public static $rules = [
        'name' => 'required',
        'purchase_group_id' =>  'nullable|exists:purchase_groups,id',
        'purchase_category_id' =>  'required|exists:purchase_categories,id',
    ];
    protected $fillable = [
        'name',
        'purchase_group_id',
        'purchase_category_id',
        'description'
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
}
