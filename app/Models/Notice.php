<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notice extends Model
{
    use HasFactory;

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'name' => 'required',
        'notice_date' => 'required|date',
    ];

    /**
     * @var string
     */
    protected $table = 'notices';

    /**
     * @var array
     */
    protected $fillable = [
        'name',
        'notice_type',
        'notice_date',
        'show',
        'description',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'name' => 'string',
        'notice_type' => 'string',
        'notice_date' => 'date',
        'show' => 'boolean',
        'description' => 'string',
    ];

    public function getTruncatedDescriptionAttribute()
    {
        $length = 300;
        $value = $this->attributes['description'] ?? ''; // Handle null cases
        $value = htmlspecialchars_decode($value);

        if (strlen($value) > $length) {
            $value = substr($value, 0, $length);
            $value = rtrim($value, " \t\n\r\0\x0B");
            $value = preg_replace('/\s+?(\S+)?$/', '', $value);
            $value .= '...';
        }

        return $value;
    }
}
