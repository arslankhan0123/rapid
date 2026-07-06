<?php

namespace App\Repositories;

use App\Models\BranchDoc;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\Supplier;
use App\Models\CustomerGroup;
use App\Models\Country;
use App\Models\SupplierGroup;
use App\Models\SupplierToGroup;
use App\Models\Currency;
use App\Models\Setting;
use App\Models\Branch;
use App\Models\Bank;


/**
 * Class CustomerRepository
 *
 * @version April 3, 2020, 6:37 am UTC
 */
class BranchRepository extends BaseRepository
{

    protected $fieldSearchable = [
        'company_name',
        'name',
        'website',
        'vat_number',
        'currency_id',
        'city',
        'state',
        'country_id',
        'zip_code',
        'phone',
        'address',
        'address_ar',
        'print_format',
        'invoice_format'
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
        return Branch::class;
    }

    public function create($input)
    {

        DB::beginTransaction();
        try {
            $branch = Branch::create(Arr::only($input, [
                'company_name',
                'name',
                'website',
                'vat_number',
                'currency_id',
                'city',
                'state',
                'country_id',
                'zip_code',
                'phone',
                'address',
                'address_ar',
                'bank_id',
                'invoice_format'

            ]));
            $this->uploadFiles($input, $branch->id);
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }

        return $branch;
    }
    public function update_branch($supplierId, $input)
    {
        $branch = Branch::findOrFail($supplierId);
        DB::beginTransaction();
        try {
            $branch->update(Arr::only($input, [
                'company_name',
                'name',
                'website',
                'vat_number',
                'currency_id',
                'city',
                'state',
                'country_id',
                'zip_code',
                'phone',
                'address',
                'address_ar',
                'bank_id',
                'print_format',
                'invoice_format'
            ]));
            $this->uploadFiles($input, $branch->id);
            DB::commit();
            return $branch;
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
        $file->storeAs('public/branch_docs', $filename);

        // Store only the filename in the database
        DB::table('branch_docs')->insert([
            'name' => $name,
            'branch_id' => $employeeId,
            'file' => $filename, // Save only the filename
            'expiry_date' => $expiryDate,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
    public function getCountries()
    {
        return Country::pluck('name', 'id');
    }
    public function getCurrencies()
    {
        return Currency::pluck('name', 'id');
    }
    public function getCompanyName()
    {
        return Setting::where('key', 'company')->first();
    }
    public function getBanks()
    {
        return Bank::pluck('name', 'id');
    }


    public function delete_file($id)
    {
        // Find the document by ID using Eloquent
        $document = BranchDoc::find($id);

        // Check if the document exists
        if (!$document) {
            return false; // Document not found
        }

        // Get the file path
        $filePath = public_path('uploads/public/branch_docs/' . basename($document->file));

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
