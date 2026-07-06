<?php

namespace App\Repositories;


use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;
use App\Models\JournalVoucher;
use Illuminate\Support\Facades\DB;
use App\Models\Account;

/**
 * Class CustomerRepository
 *
 * @version April 3, 2020, 6:37 am UTC
 */
class JournalVoucherRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'account_id',
        'amount',
        'description',
        'branch_id',
        'from_account'
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
        return JournalVoucher::class;
    }

    public function create($input)
    {
        // Start a database transaction to ensure consistency
        DB::beginTransaction();

        try {
            // Create the Journal Voucher
            $journalVoucher = JournalVoucher::create(Arr::only($input, $this->getFieldsSearchable()));

            // Fetch the related account
            $account = Account::find($input['account_id']);

            if ($account) {
                // Deduct the amount from the opening balance
                $newBalance = $account->opening_balance - $input['amount'];

                // Ensure balance does not go negative (optional logic)
                if ($newBalance < 0) {
                    throw new Exception('Insufficient opening balance in the account.');
                }

                // Update the account's opening balance
                $account->update(['opening_balance' => $newBalance]);
            } else {
                throw new Exception('Account not found.');
            }

            // Commit the transaction
            DB::commit();

            return $journalVoucher;
        } catch (Exception $e) {
            // Rollback the transaction in case of any error
            DB::rollBack();

            // Rethrow the exception for error handling
            throw $e;
        }
    }

    public function updateVoucher($input, $id)
    {
        // Start a database transaction
        DB::beginTransaction();

        try {
            // Fetch the existing journal voucher
            $journalVoucher = JournalVoucher::findOrFail($id);

            // Fetch the old account and reverse the previous deduction
            $oldAccount = Account::find($journalVoucher->account_id);

            if ($oldAccount) {
                $oldAccount->update([
                    'opening_balance' => $oldAccount->opening_balance + $journalVoucher->amount,
                ]);
            }

            // Check if the account_id has changed
            if ($journalVoucher->account_id != $input['account_id']) {
                // Handle the new account
                $newAccount = Account::find($input['account_id']);
                if ($newAccount) {
                    $newBalance = $newAccount->opening_balance - $input['amount'];

                    // Ensure the balance doesn't go negative
                    if ($newBalance < 0) {
                        throw new Exception('Insufficient opening balance in the new account.');
                    }

                    $newAccount->update(['opening_balance' => $newBalance]);
                } else {
                    throw new Exception('New account not found.');
                }
            } else {
                // Deduct the updated amount from the same account
                if ($oldAccount) {
                    $newBalance = $oldAccount->opening_balance - $input['amount'];

                    if ($newBalance < 0) {
                        throw new Exception('Insufficient opening balance in the account.');
                    }

                    $oldAccount->update(['opening_balance' => $newBalance]);
                }
            }

            // Update the journal voucher
            $journalVoucher->update(Arr::only($input, $this->getFieldsSearchable()));

            // Commit the transaction
            DB::commit();

            return $journalVoucher;
        } catch (Exception $e) {
            // Rollback the transaction in case of any error
            DB::rollBack();

            // Rethrow the exception for error handling
            throw $e;
        }
    }


    public function getAccounts()
    {
        return Account::get();
    }
}
