<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\EmployeesDoc;
use App\Models\Country;

class Employee extends Model
{
    use HasFactory;
    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'name' => 'required',
        'code' => 'required|unique:employees,code',
        'file.*' => 'nullable|file|mimes:pdf|max:2048', // Note max size is 2MB, not 2
        'expiry_date.*' => 'nullable|date',
        'image.*' => 'nullable|image|mimes:jpg,jpeg,png|max:2048', // Allow only JPG, JPEG, PNG images
        'iqama_no' => 'required|unique:employees,iqama_no',
        'basic_salary'        => 'required|numeric|min:0.01',
        'transport_allowance' => 'nullable|numeric|min:0.01',
        'gross_salary'        => 'nullable|numeric|min:0.01',
    ];

    /**
     * @var string
     */
    protected $table = 'employees';

    /**
     * @var array
     */
    protected $fillable = [
        'name',
        'code',
        'currency_id',
        'department_id',
        'sub_department_id',
        'designation_id',
        'dob',
        'join_date',
        'email',
        'phone',
        'linkedin_url',
        'facebook_url',
        'router_number',
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
        'insurance_apply',

    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'name' => 'string',
        'department_id' => 'integer',
        'sub_department_id' => 'integer',
        'designation_id' => 'integer',
        'dob' => 'date',
        'join_date' => 'date:Y-m-d',
        'email' => 'string',
        'phone' => 'string',
        'gender' => 'string',
        'marital_status' => 'string',
        'blood_group' => 'string',
        'religion' => 'string',
        'national_id' => 'string',
        'iqama_no' => 'string',
        'passport' => 'string',
        'driving_license_no' => 'string',
        'type' => 'string',
        'duty_type' => 'string',
        'hourly_rate' => 'float',
        'bank_name' => 'string',
        'branch_name' => 'string',
        'bank_account_no' => 'string',
        'iban_num' => 'string',
        'basic_salary' => 'float',
        'transport_allowance' => 'float',
        'gross_salary' => 'float',
        'status' => 'boolean',
        'company_name' => 'string',
    ];

    function department()
    {
        return $this->BelongsTo(Department::class, 'department_id');
    }
    function subDepartment()
    {
        return $this->BelongsTo(SubDepartment::class, 'sub_department_id');
    }
    function designation()
    {
        return $this->BelongsTo(Designation::class, 'designation_id');
    }
    function countryEmployee()
    {
        return $this->BelongsTo(Country::class, 'country');
    }

    public function documents()
    {
        return $this->hasMany(EmployeesDoc::class);
    }
    public function shifts()
    {
        return $this->belongsTo(Shift::class, 'shift_id');
    }
    public function projectMember()
    {
        return $this->hasMany(ProjectMember::class, 'user_id', 'id',);
    }
    public function attendance()
    {
        return $this->hasMany(NewAttendance::class, 'employee_id');
    }

    public function employeeRates()
    {
        return $this->hasMany(EmployeeRate::class, 'employee_id', 'id');
    }

    public function employeeRate($customer_id, $project_id, $month)
    {
        return $this->employeeRates()
            ->where('customer_id', $customer_id)
            ->where('project_id', $project_id)
            ->where('month', $month)
            ->first();
    }
    public function allowances()
    {
        return $this->hasMany(Allowance::class, 'employee_id');
    }
    public function deductions()
    {
        return $this->hasMany(Deduction::class, 'employee_id');
    }

    public function advances()
    {
        return $this->hasMany(SalaryAdvance::class, 'employee_id');
    }
    public function loans()
    {
        return $this->hasMany(Loan::class, 'employee_id');
    }
    public function insurances()
    {
        return $this->hasMany(Insurance::class, 'employee_id');
    }
    public function bonuses()
    {
        return $this->hasMany(Bonus::class, 'employee_id');
    }
    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function transfers()
    {
        return $this->hasMany(Transfer::class, 'employee_id');
    }

    public function getLatestDateAttribute()
    {
        $latestTransfer = $this->transfers()->latest('id')->first();

        // Return the join_date if no transfer is found, otherwise the latest transfer's `to` date
        return $latestTransfer ? $latestTransfer->created_at : $this->join_date;
    }

    public function leaveApplications()
    {
        return $this->hasMany(LeaveApplication::class, 'employee_id');
    }

    public function currency()
    {
        return $this->belongsTo(\App\Models\Currency::class, 'currency_id');
    }
}