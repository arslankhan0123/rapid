<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SampleReceiving extends Model
{
    use HasFactory;
    protected $table = 'sample_receiving';

    public static $rules = [
        'date' => 'required|date',
        'time' => 'required|date_format:H:i',
        'section' => 'required|integer|exists:sample_categories,id',
        'client_name' => 'required|string|max:255',
        'client_reference' => 'required|string|max:255',
        'type_of_sample' => 'required|string|max:255',
        'required_tests' => 'required|string|max:255',
        'number_of_sample' => 'required|string|max:255',
        'delivered_by' => 'required|integer|exists:employees,id',
        'received_by' => 'required|integer|exists:employees,id'
    ];
    protected $fillable = [
        'date',
        'time',
        'section',
        'client_name',
        'client_reference',
        'type_of_sample',
        'required_tests',
        'number_of_sample',
        'delivered_by',
        'received_by',
        'branch_id'
    ];
    /**
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'date' => 'date',
        'time' => 'datetime:H:i:s',
        'section' => 'integer',
        'client_name' => 'string',
        'client_reference' => 'string',
        'type_of_sample' => 'string',
        'required_tests' => 'string',
        'number_of_sample' => 'string',
        'delivered_by' => 'integer',
        'received_by' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public static function getNextID()
    {
        return self::max('id') + 1;
    }

    public function category()
    {
        return $this->belongsTo(SampleCategory::class, 'section');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }
    public function deliveredBy()
    {
        return $this->belongsTo(Employee::class, 'delivered_by');
    }
    public function receivedBy()
    {
        return $this->belongsTo(Employee::class, 'received_by');
    }


}
