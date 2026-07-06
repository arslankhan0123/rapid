<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BranchDoc extends Model
{
    use HasFactory;

    /**
     * @var string
     */
    protected $table = 'branch_docs';

    /**
     * @var array
     */
    protected $fillable = [
        'name',
        'branch_id',
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
        'branch_id' => 'integer',
        'file' => 'string',
        'expiry_date' => 'date',
    ];

    /**
     * Relationship with Employee
     */
    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }
}
