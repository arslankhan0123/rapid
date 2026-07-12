<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobPosition extends Model
{
    use HasFactory;

    protected $table = 'job_positions';

    const TYPE_FULL_TIME = 'Full-time';
    const TYPE_PART_TIME = 'Part-time';
    const TYPE_CONTRACT = 'Contract';
    const TYPE_INTERNSHIP = 'Internship';

    public static $employmentTypes = [
        self::TYPE_FULL_TIME => 'Full-time',
        self::TYPE_PART_TIME => 'Part-time',
        self::TYPE_CONTRACT => 'Contract',
        self::TYPE_INTERNSHIP => 'Internship',
    ];

    const STATUS_OPEN = 'Open';
    const STATUS_CLOSED = 'Closed';
    const STATUS_INACTIVE = 'Inactive';

    public static $statuses = [
        self::STATUS_OPEN => 'Open',
        self::STATUS_CLOSED => 'Closed',
        self::STATUS_INACTIVE => 'Inactive',
    ];

    public $fillable = [
        'position_code',
        'title',
        'category_id',
        'department_id',
        'employment_type',
        'experience_required',
        'min_salary',
        'max_salary',
        'description',
        'status',
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
        'position_code' => 'string',
        'title' => 'string',
        'category_id' => 'integer',
        'department_id' => 'integer',
        'employment_type' => 'string',
        'experience_required' => 'string',
        'min_salary' => 'double',
        'max_salary' => 'double',
        'description' => 'string',
        'status' => 'string',
        'created_by' => 'integer',
        'updated_by' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'title' => 'required|max:191',
        'position_code' => 'nullable|max:191|unique:job_positions,position_code',
        'category_id' => 'nullable|integer|exists:job_categories,id',
        'department_id' => 'nullable|integer|exists:departments,id',
        'employment_type' => 'nullable|string|in:Full-time,Part-time,Contract,Internship',
        'experience_required' => 'nullable|string|max:191',
        'min_salary' => 'nullable|numeric|min:0',
        'max_salary' => 'nullable|numeric|min:0',
        'status' => 'nullable|string|in:Open,Closed,Inactive',
    ];

    public function category()
    {
        return $this->belongsTo(JobCategory::class, 'category_id');
    }

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function skills()
    {
        return $this->belongsToMany(JobSkill::class, 'job_position_skills', 'job_position_id', 'job_skill_id')->withTimestamps();
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
            $model->created_by = auth()->id() ?? 1; // Fallback to 1
            if (empty($model->position_code)) {
                $lastId = static::max('id') ?? 0;
                $model->position_code = 'JP-' . str_pad($lastId + 1, 4, '0', STR_PAD_LEFT);
            }
        });

        static::updating(function ($model) {
            $model->updated_by = auth()->id() ?? 1;
        });
    }
}
