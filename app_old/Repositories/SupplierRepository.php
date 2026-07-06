<?php

namespace App\Repositories;

use App\Models\SupplierDoc;
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

/**
 * Class CustomerRepository
 *
 * @version April 3, 2020, 6:37 am UTC
 */
class SupplierRepository extends BaseRepository
{
    /**
     * @var array
     */

    protected $language = [
        'en' => 'English',
        'es' => 'Spanish',
        'fr' => 'French',
        'de' => 'German',
        'ru' => 'Russian',
        'pt' => 'Portuguese',
        'ar' => 'Arabic',
        'zh' => 'Chinese',
        'tr' => 'Turkish',
    ];

    protected $currency = [
        '0' => 'INR',
        '1' => 'AUD',
        '2' => 'USD',
        '3' => 'EUR',
        '4' => 'JPY',
        '5' => 'GBP',
        '6' => 'CAD',
    ];
    protected $fieldSearchable = [
        'company_name',
        'vat_number',
        'phone',
        'website',
        'street',
        'city',
        'zip'
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
        return Supplier::class;
    }

    public function create($input)
    {

        $supplier = Supplier::create(Arr::only($input, ['company_name', 'vat_number', 'website', 'phone', 'currency', 'country', 'default_language', 'street', 'city', 'zip', 'state','opening_balance']));
        $supplierId = $supplier->id;

        // Initialize array for batch insert
        $dataToInsert = [];
        if (isset($input['groups']) && !empty($input['groups'])) {
            foreach ($input['groups'] as $groupId) {
                $dataToInsert[] = [
                    'supplier_id' => $supplierId,
                    'group_id' => $groupId,
                ];
            }
        }
        if (!empty($dataToInsert)) {
            SupplierToGroup::insert($dataToInsert);
        }
        $this->uploadFiles($input, $supplier->id);
        return $supplier;
    }
    public function update_supplier($supplierId, $input)
    {

        // Find the supplier by ID
        $supplier = Supplier::findOrFail($supplierId);

        // Update supplier data
        $supplier->update(Arr::only($input, ['company_name', 'vat_number', 'website', 'phone', 'currency', 'country', 'default_language', 'street', 'city', 'zip', 'state','opening_balance']));

        // Initialize array for batch insert
        $dataToInsert = [];
        if (isset($input['groups']) && !empty($input['groups'])) {
            foreach ($input['groups'] as $groupId) {
                $dataToInsert[] = [
                    'supplier_id' => $supplierId,
                    'group_id' => $groupId,
                ];
            }
        }

        // First, delete existing groups for the supplier
        SupplierToGroup::where('supplier_id', $supplierId)->delete();

        // Then, insert the new groups
        if (!empty($dataToInsert)) {
            SupplierToGroup::insert($dataToInsert);
        }
        $this->uploadFiles($input, $supplier->id);
        return $supplier;
    }

    public function getSyncList()
    {
        $data = [];
        $data['supplierGroups'] = SupplierGroup::orderBy('name', 'asc')->pluck('name', 'id')->toArray();
        $data['currencies'] = Currency::pluck('name', 'id');
        $data['languages'] = $this->language;
        $data['countries'] = Country::orderBy('name', 'asc')->pluck('name', 'id')->toArray();
        return $data;
    }
    function getGroupData($id)
    {
        return SupplierToGroup::where('supplier_id', $id)->get();
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
        $file->storeAs('public/supplier_docs', $filename);

        // Store only the filename in the database
        DB::table('supplier_docs')->insert([
            'name' => $name,
            'supplier_id' => $employeeId,
            'file' => $filename, // Save only the filename
            'expiry_date' => $expiryDate,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function delete_file($id)
    {
        $document = SupplierDoc::find($id);
        if (!$document) {
            return false; // Document not found
        }
        $filePath = public_path('uploads/public/supplier_docs/' . basename($document->file));
        DB::beginTransaction();
        try {
            if (file_exists($filePath)) {
                unlink($filePath);
            }
            $document->delete();
            DB::commit();
            return $document; // Return the deleted model instance
        } catch (Exception $e) {
            DB::rollBack();
            return false; // Deletion failed
        }
    }
}
