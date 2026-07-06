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
class SupplierStatementRepository extends BaseRepository
{

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


    public function getSuppliers()
    {
        return Supplier::pluck('company_name', 'id');

    }
    function getGroupData($id)
    {
        return SupplierToGroup::where('supplier_id', $id)->get();
    }


}
