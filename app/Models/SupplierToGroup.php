<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupplierToGroup extends Model
{
    use HasFactory;

    protected $table = 'supplier_to_group';

    /**
     * @var array
     */
    protected $fillable = [
        'supplier_id',
        'group_id',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'supplier_id' => 'integer',
        'group_id' => 'integer',
    ];

    /**
     * Get the supplier that belongs to this group.
     */
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    /**
     * Get the group that belongs to this supplier.
     */
    public function group()
    {
        return $this->belongsTo(SupplierGroup::class);
    }
}
