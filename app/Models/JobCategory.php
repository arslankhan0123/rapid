<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobCategory extends Model
{
    use HasFactory;

    protected $table = 'job_categories';

    public $fillable = [
        'category_code',
        'name',
        'parent_id',
        'description',
        'display_order',
        'is_active',
        'created_by',
        'updated_by'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'category_code' => 'string',
        'name' => 'string',
        'parent_id' => 'integer',
        'description' => 'string',
        'display_order' => 'integer',
        'is_active' => 'boolean',
        'created_by' => 'integer',
        'updated_by' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'name' => 'required|max:191',
        'category_code' => 'nullable|max:191|unique:job_categories,category_code',
        'parent_id' => 'nullable|integer|exists:job_categories,id',
        'display_order' => 'nullable|integer',
        'is_active' => 'nullable|boolean',
    ];

    public function parent()
    {
        return $this->belongsTo(JobCategory::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(JobCategory::class, 'parent_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->created_by = auth()->id() ?? 1; // Fallback to 1 if not logged in
            if (empty($model->category_code)) {
                $lastId = static::max('id') ?? 0;
                $model->category_code = 'JC-' . str_pad($lastId + 1, 4, '0', STR_PAD_LEFT);
            }
        });

        static::updating(function ($model) {
            $model->updated_by = auth()->id() ?? 1;
        });
    }
}
