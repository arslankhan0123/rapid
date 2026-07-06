<?php

namespace App\Repositories;


use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;
use App\Models\CashTransfer;
use App\Models\Account;
use Illuminate\Support\Facades\DB;

/**
 * Class CustomerRepository
 *
 * @version April 3, 2020, 6:37 am UTC
 */
class CashTransferRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'transfer_id',
        'from_account',
        'to_account',
        'transfer_amount',
        'branch_id'
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
        return CashTransfer::class;
    }

    public function create($input)
    {
        return DB::transaction(function () use ($input) {
            // Create the cash transfer record
            $cashTransfer = CashTransfer::create(Arr::only($input, $this->getFieldsSearchable()));

            // Update the opening balance for 'from_account'
            $fromAccount = Account::find($input['from_account']);
            if ($fromAccount) {
                $fromAccount->current_balance -= $input['transfer_amount'];
                $fromAccount->save();
            }

            // Update the opening balance for 'to_account'
            $toAccount = Account::find($input['to_account']);
            if ($toAccount) {
                $toAccount->current_balance += $input['transfer_amount'];
                $toAccount->save();
            }

            return $cashTransfer;
        });
    }

    public function updateCash($input, $id)
    {
        return DB::transaction(function () use ($id, $input) {
            // Fetch the existing cash transfer record
            $cashTransfer = CashTransfer::findOrFail($id);

            // Revert balances for the original accounts
            if ($cashTransfer->from_account) {
                $fromAccount = Account::find($cashTransfer->from_account);
                if ($fromAccount) {
                    $fromAccount->current_balance += $cashTransfer->transfer_amount;
                    $fromAccount->save();
                }
            }

            if ($cashTransfer->to_account) {
                $toAccount = Account::find($cashTransfer->to_account);
                if ($toAccount) {
                    $toAccount->current_balance -= $cashTransfer->transfer_amount;
                    $toAccount->save();
                }
            }

            // Update the cash transfer record
            $cashTransfer->update(Arr::only($input, $this->getFieldsSearchable()));

            // Adjust balances for the new accounts
            $newFromAccount = Account::find($input['from_account']);
            if ($newFromAccount) {
                $newFromAccount->current_balance -= $input['transfer_amount'];
                $newFromAccount->save();
            }

            $newToAccount = Account::find($input['to_account']);
            if ($newToAccount) {
                $newToAccount->current_balance += $input['transfer_amount'];
                $newToAccount->save();
            }

            return $cashTransfer;
        });
    }


    public function getAccounts()
    {
        return Account::get();
    }
}
