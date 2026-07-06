<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseReturnDoc extends Model
{
    use HasFactory;

    /**
     * @var string
     */
    protected $table = 'purchase_return_docs';

    /**
     * @var array
     */
    protected $fillable = [
        'name',
        'purchase_return_id',
        'file',
        'expiry_date',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'name' => 'string',
        'purchase_return_id' => 'integer',
        'file' => 'string',
        'expiry_date' => 'date',
    ];

    /**
     * Relationship with Employee
     */
    public function invoice()
    {
        return $this->belongsTo(PurchaseReturn::class, 'purchase_return_id');
    }
}
