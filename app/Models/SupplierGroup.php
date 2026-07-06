<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\SupplierToGroup;


class SupplierGroup extends Model
{
    use HasFactory;

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'name' => 'required|unique:supplier_groups,name',
    ];

    /**
     * @var string
     */
    protected $table = 'supplier_groups';

    /**
     * @var array
     */
    protected $fillable = [
        'name',
        'description'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'name' => 'string',
    ];

    public function suppliers()
    {
        return $this->hasMany(SupplierToGroup::class,'group_id' ,'id');
    }
}
