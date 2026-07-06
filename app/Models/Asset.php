<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Asset extends Model
{
    use HasFactory;

    public static $rules = [
        'name' => [
            'required',
            'regex:/^[\p{L}\p{M}\s]+$/u',
        ],
        'purchase_date' => 'required',
        'manufacturer' => 'required',
        'warranty_end_date' => 'required|date|after_or_equal:purchase_date',
        'warranty_end_date' => 'required',
        'serial_number' => 'nullable|regex:/^[0-9]+$/',
        'qty' => 'required|integer|min:1',
        'price' => 'required|numeric|min:0.01',
        'total' => 'nullable|numeric|min:0.01'
    ];

    protected $guarded = [];
    /**
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'name' => 'string',
        'purchase_date' => 'datetime:d-m-Y',
        'manufacturer' => 'string',
        'warranty_end_date' => 'datetime:d-m-Y',
        'created_at' => 'datetime', // Add created_at cast
        'updated_at' => 'datetime', // Add updated_at cast if needed
    ];
    public function category()
    {
        return $this->belongsTo(AssetCategory::class, 'asset_category_id');
    }
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }
    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }
}
