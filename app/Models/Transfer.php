<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transfer extends Model
{
    use HasFactory;
    protected $table = 'transfer';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'employee_id',
        'from',
        'to',
        'transfer_date',
        'description',
        'branch_id'
    ];

    /**
     * Relationships
     */

    // Relationship with Employee
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    // Relationship with Branch (from)
    public function fromBranch()
    {
        return $this->belongsTo(Branch::class, 'from');
    }

    // Relationship with Branch (to)
    public function toBranch()
    {
        return $this->belongsTo(Branch::class, 'to');
    }

    /**
     * Validation Rules
     *
     * @return array
     */
    public static function rules()
    {
        return [
            'employee_id' => 'required|exists:employees,id',
            'from'        => 'nullable|exists:branches,id',
            'to'          => 'required|exists:branches,id|different:from',
            'transfer_date' => 'required|date',
            'description' => 'nullable|string|max:255',
        ];
    }
    protected $appends = ['formatted_transfer_date'];

    /**
     * Accessor for the formatted transfer_date
     */
    public function getFormattedTransferDateAttribute()
    {
        return \Carbon\Carbon::parse($this->attributes['transfer_date'])->format('d M Y');
    }
    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }
}
