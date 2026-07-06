<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Certificate extends Model
{
    use HasFactory;
    protected $table = 'certificate';

    public static $rules = [
        'date' => 'required|date',
        'employee' => 'required|string|max:255',
        'lab_manager' => 'required|string|max:255',
        'general_manager' => 'required|string|max:255',
        'description' => 'nullable|string',
    ];
    protected $fillable = [
        'date',
        'employee',
        'lab_manager',
        'general_manager',
        'description',
        'certificate_number',
        'type_id'
    ];
    /**
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'date' => 'date',
        'employee' => 'string',
        'lab_manager' => 'string',
        'general_manager' => 'string',
    ];

    // public function getDateAttribute($value)
    // {
    //     return \Carbon\Carbon::parse($value)->format('jS M Y');
    // }
    public function certificate()
    {
        return $this->belongsTo(SampleCategory::class, 'section');
    }

    public function type()
    {
        return $this->belongsTo(CertificateType::class, 'type_id');
    }
}
