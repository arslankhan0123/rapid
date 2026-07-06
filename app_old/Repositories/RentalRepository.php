<?php

namespace App\Repositories;

use App\Models\AssetCategory;
use App\Models\ProductUnit;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;
use App\Models\Supplier;
use App\Models\Rental;
use App\Models\TaxRate;

/**
 * Class CustomerRepository
 *
 * @version April 3, 2020, 6:37 am UTC
 */
class RentalRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'name',
        'supplier_id',
        'start_date',
        'end_date',
        'type',
        'tax_id',
        'tax_amount',
        'total_rent_amount'
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
        return Rental::class;
    }

    public function create($input)
    {
        return Rental::create(Arr::only($input, ['supplier_id', 'start_date', 'end_date', 'type', 'description', 'amount','tax_id','tax_amount', 'total_rent_amount']));
    }
    public function getSuppliers()
    {
        return  Supplier::pluck('company_name', 'id'); // Retrieves departments as key-value pairs
    }
    public function getTaxRates()
    {
        return TaxRate::all();
    }
}
