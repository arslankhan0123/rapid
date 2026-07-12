<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobSkill extends Model
{
    use HasFactory;

    protected $table = 'job_skills';

    const LEVEL_BEGINNER = 'Beginner';
    const LEVEL_INTERMEDIATE = 'Intermediate';
    const LEVEL_ADVANCED = 'Advanced';
    const LEVEL_EXPERT = 'Expert';

    public static $levels = [
        self::LEVEL_BEGINNER => 'Beginner',
        self::LEVEL_INTERMEDIATE => 'Intermediate',
        self::LEVEL_ADVANCED => 'Advanced',
        self::LEVEL_EXPERT => 'Expert',
    ];

    public $fillable = [
        'skill_code',
        'name',
        'category_id',
        'level',
        'description',
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
        'skill_code' => 'string',
        'name' => 'string',
        'category_id' => 'integer',
        'level' => 'string',
        'description' => 'string',
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
        'skill_code' => 'nullable|max:191|unique:job_skills,skill_code',
        'category_id' => 'nullable|integer|exists:job_categories,id',
        'level' => 'nullable|string|in:Beginner,Intermediate,Advanced,Expert',
        'is_active' => 'nullable|boolean',
    ];

    public function category()
    {
        return $this->belongsTo(JobCategory::class, 'category_id');
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
            if (empty($model->skill_code)) {
                $lastId = static::max('id') ?? 0;
                $model->skill_code = 'JS-' . str_pad($lastId + 1, 4, '0', STR_PAD_LEFT);
            }
        });

        static::updating(function ($model) {
            $model->updated_by = auth()->id() ?? 1;
        });
    }
}
