<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class  CustomerDoc extends Model
{
    use HasFactory;

    /**
     * @var string
     */
    protected $table = 'customer_docs';

    /**
     * @var array
     */
    protected $fillable = [
        'name',
        'customer_id',
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
        'customer_id' => 'integer',
        'file' => 'string',
        'expiry_date' => 'date',
    ];

    /**
     * Relationship with Employee
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }
}
