<?php

namespace App\Models;

use App\Models\Shift;
use App\Models\Branch;
use App\Models\Country;
use App\Models\Department;
use App\Models\Designation;
use App\Models\EmployeesDoc;
use App\Models\SubDepartment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CasualEmployee extends Model
{
    use HasFactory;

    /**
     * Table name.
     *
     * @var string
     */
    protected $table = 'casual_employees';

    /**
     * Mass-assignable columns.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'name',
        'code',
        'dob',
        'join_date',
        'email',
        'phone',
        'gender',
        'marital_status',
        'blood_group',
        'religion',
        'iqama_no',
        'passport',
        'driving_license_no',
        'type',
        'duty_type',
        'hourly_rate',
        'bank_name',
        'branch_name',
        'bank_account_no',
        'iban_num',
        'basic_salary',
        'transport_allowance',
        'gross_salary',
        'status',
        'company_name',
        'country',
        'street',
        'city',
        'state',
        'zip',
        'image',
        'shift_id',
        'employment_type',
        'iqama_no_expiry_date',
        'tuv_no',
        'tuv_no_expiry_date',
        'passport_expiry_date',
        'driving_license_expiry_date',
        'branch_id',
        'absent_allowance_deduction',
    ];

    /**
     * Attribute casting.
     *
     * @var array<string,string>
     */
    protected $casts = [
        'dob'                         => 'date',
        'join_date'                   => 'date',
        'hourly_rate'                 => 'float',
        'basic_salary'                => 'float',
        'transport_allowance'         => 'float',
        'gross_salary'                => 'float',
        'status'                      => 'boolean',
        'iqama_no_expiry_date'        => 'date',
        'tuv_no_expiry_date'          => 'date',
        'passport_expiry_date'        => 'date',
        'driving_license_expiry_date' => 'date',
    ];

    /* ------------ Relationships ------------ */

    // public function department(): BelongsTo
    // {
    //     return $this->belongsTo(Department::class, 'department_id');
    // }

    // public function subDepartment(): BelongsTo
    // {
    //     return $this->belongsTo(SubDepartment::class, 'sub_department_id');
    // }

    // public function designation(): BelongsTo
    // {
    //     return $this->belongsTo(Designation::class, 'designation_id');
    // }

    public function countryEmployee(): BelongsTo
    {
        return $this->belongsTo(Country::class, 'country');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(EmployeesDoc::class, 'employee_id');
    }

    public function shifts(): BelongsTo
    {
        return $this->belongsTo(Shift::class, 'shift_id');
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    // Add any other hasMany or belongsTo relationships you need here.
}
