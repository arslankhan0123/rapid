<?php

namespace App\Repositories;

use App\Models\AssetCategory;
use App\Models\ProductUnit;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;
use App\Models\Employee;
use App\Models\Department;
use App\Models\Designation;
use App\Models\Setting;
use App\Models\SubDepartment;
use App\Models\Country;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Database\QueryException;
use App\Models\EmployeesDoc;
use App\Models\Shift;
use Carbon\Carbon;

/**
 * Class CustomerRepository
 *
 * @version April 3, 2020, 6:37 am UTC
 */
class EmployeeRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'name',
        'designation_id',
        'status',
        'iqama_no_expiry_date',
        'tuv_no',
        'tuv_no_expiry_date',
        'passport_expiry_date',
        'branch_id',
        'absent_allowance_deduction'
    ];

    /**
     * Return searchable fields
     *
     * @return array
     */
    public function getFieldsSearchable()
    {
        return $this->fieldSearchable;
    }

    /**
     * Configure the Model
     **/
    public function model()
    {
        return Employee::class;
    }
    public function getCompanyName()
    {
        return Setting::selectRaw("value as name")->where('key', 'company_name')->first();
    }

    public function create($input)
    {
        if (isset($input['iqama_no_expiry_date'])) {
            $input['iqama_no_expiry_date'] = Carbon::createFromFormat('d-m-Y', $input['iqama_no_expiry_date'])->format('Y-m-d');
        }

        if (isset($input['passport_expiry_date'])) {
            $input['passport_expiry_date'] = Carbon::createFromFormat('d-m-Y', $input['passport_expiry_date'])->format('Y-m-d');
        }

        if (isset($input['driving_license_expiry_date'])) {
            $input['driving_license_expiry_date'] = Carbon::createFromFormat('d-m-Y', $input['driving_license_expiry_date'])->format('Y-m-d');
        }
        // Extract the basic employee data
        $data = Arr::only($input, [
            'name',
            'department_id',
            'sub_department_id',
            'designation_id',
            'dob',
            'join_date',
            'email',
            'phone',
            'gender',
            'marital_status',
            'blood_group',
            'religion',
            'code',
            'iqama_no',
            'passport',
            'driving_license_no',
            'driving_license_expiry_date',
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
            'shift_id',
            'employment_type',
            'iqama_no_expiry_date',
            'tuv_no',
            'tuv_no_expiry_date',
            'passport_expiry_date',
            'branch_id',
            'absent_allowance_deduction',
            'insurance_apply',
            'linkedin_url',
            'facebook_url',
            'router_number',
        ]);

        DB::beginTransaction();
        try {
            // Check if an image file is present
            if (isset($input['image'])) {
                $image = $input['image'];
                $filename = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension(); // Generate a unique filename
                $image->storeAs('public/employee_images', $filename); // Store the image

                // Add the image filename to the data array
                $data['image'] = $filename;
            }

            // Create the employee record
            $employee = Employee::create($data);

            // Handle other file uploads and store them in the employees_docs table
            $this->uploadFiles($input, $employee->id);

            DB::commit();
            return $employee;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }



    private function uploadFiles($input, $employeeId)
    {
        // Check if the input contains files
        if (isset($input['file']) && is_array($input['file'])) {
            foreach ($input['file'] as $index => $file) {
                // Check if a file has been uploaded
                if (is_uploaded_file($file->getPathname())) {
                    $name = $input['doc_name'][$index] ?? ''; // Default to 'unknown' if no name is provided
                    $expiryDate = $input['expiry_date'][$index] ?? null; // Default to null if no expiry date is provided
                    $this->uploadAndStoreFile($file, $name, $employeeId, $expiryDate);
                }
            }
        }
    }
    private function uploadAndStoreFile($file, $name, $employeeId, $expiryDate)
    {
        // Generate a unique filename
        $filename = time() . '_' . $file->getClientOriginalName();

        // Store the file in the 'public/employee_docs' directory
        $file->storeAs('public/employee_docs', $filename);

        // Store only the filename in the database
        DB::table('employees_docs')->insert([
            'name' => $name,
            'employee_id' => $employeeId,
            'file' => $filename, // Save only the filename
            'expiry_date' => $expiryDate,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
    function delete_employee($employee)
    {
        try {
            // Start a database transaction to ensure atomicity
            DB::beginTransaction();

            // Fetch all related documents
            $documents = DB::table('employees_docs')
                ->where('employee_id', $employee->id)
                ->get();

            // Delete related documents and files from public storage
            foreach ($documents as $document) {
                // Construct the full file path
                $filePath = public_path("uploads/public/employee_docs/" . $document->file);
                // Delete file from public folder if it exists
                if (File::exists($filePath)) {
                    File::delete($filePath);
                }
                // Delete document record from the database
                DB::table('employees_docs')
                    ->where('id', $document->id)
                    ->delete();
            }

            // Delete the employee's image if it exists
            if ($employee->image) {
                $imagePath = public_path("uploads/public/employee_images/" . $employee->image);
                if (File::exists($imagePath)) {
                    File::delete($imagePath);
                }
            }

            // Delete the employee record
            $employee->delete();

            // Commit the transaction
            DB::commit();
            return true;
        } catch (QueryException $e) {
            // Rollback the transaction in case of an error
            DB::rollBack();
            // Handle specific exception details if necessary
            return "Already in use!!";
        } catch (Exception $e) {
            // Rollback the transaction in case of a general error
            DB::rollBack();
            // Handle any other exceptions
            return "Failed to delete";
        }
    }

    public function getCountries()
    {
        return Country::orderBy('name', 'asc')->pluck('name', 'id')->toArray();
    }
    public function getDepartments()
    {
        return Department::pluck('name', 'id')->toArray();
    }
    public function getSubDepartment($department_id = null)
    {
        return SubDepartment::select(['name', 'id', 'department_id'])->get();
    }
    public function getDesignation()
    {
        return Designation::select(['name', 'id', 'sub_department_id', 'department_id'])->get();
    }
    public function getEmployee($id)
    {
        return  Employee::with(['department', 'designation'])->where('id', $id)->first();
    }
    public function getShifts()
    {
        return  Shift::pluck('name', 'id');
    }

    public function getEmployeesByStatus($status, $branch = null)
    {
        $query = Employee::with(['department', 'subDepartment', 'designation', 'branch'])
            ->orderBy('updated_at', 'desc');

        // Filter by status if it's not 'all'
        if ($status !== 'all') {
            $query->where('status', $status);
        }

        // Filter by branch if provided
        if (!is_null(value: $branch)) {
            $query->whereHas('branch', function ($query) use ($branch) {
                $query->where('id', $branch); // Assuming 'id' is the column for the branch
            });
        }

        return $query->get();
    }






    ///update operation below

    public function update_employee($employee, $input)
    {

        
        if (isset($input['iqama_no_expiry_date'])) {
            $input['iqama_no_expiry_date'] = Carbon::createFromFormat('d-m-Y', $input['iqama_no_expiry_date'])->format('Y-m-d');
        }

        if (isset($input['passport_expiry_date'])) {
            $input['passport_expiry_date'] = Carbon::createFromFormat('d-m-Y', $input['passport_expiry_date'])->format('Y-m-d');
        }

        if (isset($input['driving_license_expiry_date'])) {
            $input['driving_license_expiry_date'] = Carbon::createFromFormat('d-m-Y', $input['driving_license_expiry_date'])->format('Y-m-d');
        }
        
        if(!isset($input['insurance_apply'])) $input['insurance_apply'] = 0;
        // Extract the basic employee data
        $data = Arr::only($input, [
            'name',
            'department_id',
            'sub_department_id',
            'designation_id',
            'dob',
            'join_date',
            'email',
            'phone',
            'gender',
            'marital_status',
            'blood_group',
            'religion',
            'code',
            'iqama_no',
            'passport',
            'driving_license_no',
            'driving_license_expiry_date',
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
            'shift_id',
            'employment_type',
            'iqama_no_expiry_date',
            'tuv_no',
            'tuv_no_expiry_date',
            'passport_expiry_date',
            'branch_id',
            'absent_allowance_deduction',
            'insurance_apply',
            'linkedin_url',
            'facebook_url',
            'router_number',
        ]);

        DB::beginTransaction();
        try {
            // Check if a new image has been uploaded
            if (isset($input['image'])) {
                // Delete the old image if it exists
                if ($employee->image) {
                    $oldImagePath = public_path('uploads/public/employee_images/' . $employee->image);
                    if (File::exists($oldImagePath)) {
                        File::delete($oldImagePath);
                    }
                }

                // Save the new image
                $image = $input['image'];
                $filename = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                $image->storeAs('public/employee_images', $filename);

                // Add the new image filename to the data array
                $data['image'] = $filename;
            }

            // Update the employee record with the new data
            $employee->update($data);

            // Handle other file uploads and store them in the employees_docs table
            $this->uploadFiles($input, $employee->id);

            DB::commit();
            return $employee;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }


    public function delete_file($id)
    {
        // Find the document by ID using Eloquent
        $document = EmployeesDoc::find($id);

        // Check if the document exists
        if (!$document) {
            return false; // Document not found
        }

        // Get the file path
        $filePath = public_path('uploads/public/employee_docs/' . basename($document->file));

        DB::beginTransaction();
        try {
            // Delete the file from the folder if it exists
            if (file_exists($filePath)) {
                unlink($filePath);
            }

            // Delete the document record from the database
            $document->delete();

            DB::commit();
            return $document; // Return the deleted model instance
        } catch (Exception $e) {
            DB::rollBack();
            return false; // Deletion failed
        }
    }
}
