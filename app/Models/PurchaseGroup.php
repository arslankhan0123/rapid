<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseGroup extends Model
{
    use HasFactory;

    protected $table = 'purchase_groups';
    public static $rules = [
        'name' => 'required|unique:purchase_groups,name'
    ];
    protected $fillable = [
        'name',
        'description'
    ];
    /**
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'name' => 'string'
    ];
}
