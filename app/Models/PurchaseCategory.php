<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseCategory extends Model
{
    use HasFactory;

    protected $table = 'purchase_categories';
    public static $rules = [
        'name' => 'required|unique:purchase_categories,name',
        'purchase_group_id' =>  'required|exists:purchase_groups,id',
    ];
    protected $fillable = [
        'name',
        'purchase_group_id',
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
}
