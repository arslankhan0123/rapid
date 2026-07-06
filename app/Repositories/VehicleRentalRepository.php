<?php

namespace App\Repositories;

use App\Models\VehicleRental;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use App\Http\Requests\VehicleRentalRequest;
use App\Http\Requests\UpdateVehicleRentalRequest;
use Illuminate\Support\Arr;
use App\Models\Account;

/**
 * Class VehicleRentalRepository
 */
class VehicleRentalRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'rental_number',
        'plate_number',
        'name',
        'type',
        'amount',
        'installment_amount',
        'installment_no',
        'agreement_date',
        'expiry_date',
        'notification_date',
        'description',
        'notification_days',
        'branch_id',
        'account_id',
        'paid_amount',
        'type',
        'agreement_type'
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
        return VehicleRental::class;
    }

    public function saveRental($input)
    {

        // dd(Arr::only($input, $this->getFieldsSearchable()));
        
        if($input['agreement_type'] == 'One-time'){
           $input['installment_amount'] = $input['amount'];
           $input['installment_no'] = 1;
        }

        return VehicleRental::create(
            Arr::only($input, $this->getFieldsSearchable())
        );
    }


    public function updateRental($input, $id)
    {
        if($input['agreement_type'] == 'One-time'){
           $input['installment_amount'] = $input['amount'];
           $input['installment_no'] = 1;
           $input['type'] = null;
        }
        
        $rental = VehicleRental::findOrFail($id);
        $rental->update(
            Arr::only($input, $this->getFieldsSearchable())
        );
        return $rental;
    }
    public function getTypes()
    {
        return VehicleRental::getVehicleTypes();
    }
    public function getAccounts()
    {
        return Account::orderBy('id', 'desc')->get();
    }
    public function updatePayment(VehicleRental $rental, array $data)
    {
        $rental->update([
            'branch_id' => $data['branch_id'],
            'account_id' => $data['account_id'],
            'paid_amount' => $data['paid_amount'],
        ]);

        return $rental;
    }
}
