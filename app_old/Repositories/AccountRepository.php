<?php

namespace App\Repositories;


use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;
use App\Models\Account;
use App\Models\Employee;
use App\Models\Branch;

/**
 * Class CustomerRepository
 *
 * @version April 3, 2020, 6:37 am UTC
 */
class AccountRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'account_number',
        'account_name',
        'opening_balance',
        'branch_id',
        'received_by',
        'current_balance',
        'date',
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
        return Account::class;
    }

    public function create($input)
    {

        return Account::create(Arr::only($input, $this->getFieldsSearchable()));
    }
    public function getEmployees()
    {
        return Employee::where('status', 1)
            ->whereHas('branch', function ($branchQuery) {
                $branchQuery->whereIn('id', function ($subQuery) {
                    $subQuery->select('branch_id')
                        ->from('users_branches')
                        ->where('user_id', auth()->id());
                });
            })->pluck('name', 'id');
    }
    public function getAccountNumbers()
    {
        return Account::pluck('account_number', 'branch_id');
    }
    public function getAccounts()
    {
        return Account::orderBy('id', 'desc')->get();
    }
    public function transferCash($input)
    {
        $fromAccount = Account::find($input['from_account']);
        $toAccount = Account::find($input['to_account']);

        if (!$fromAccount || !$toAccount) {
            return response()->json(['status' => false, 'message' => 'Invalid account IDs provided.']);
        }


        if ($fromAccount->opening_balance < $input['transfer_amount']) {
            return response()->json(['status' => false, 'message' => 'Insufficient balance in the From Account.']);
        }

        // Deduct from the from_account
        $fromAccount->opening_balance -= $input['transfer_amount'];
        $fromAccount->save();

        // Add to the to_account
        $toAccount->opening_balance += $input['transfer_amount'];
        $toAccount->save();

        return response()->json(['status' => true, 'message' => 'Cash transferred successfully']);
    }
    public function PayCash($input)
    {
        $account = Account::find($input['account_id']);

        if (!$account) {
            return response()->json(['status' => false, 'message' => 'Invalid account ID provided.']);
        }

        // Add the amount to the account's opening balance
        if (isset($input['date']) && !empty($input['date'])) {
            $account->date = $input['date'];
        }
        $account->opening_balance += $input['amount'];
        $account->save();

        return response()->json(['status' => true, 'message' => 'Amount added to the account successfully']);
    }


    public function updateCash($input)
    {
        $account = Account::find($input['account_id']);

        if (!$account) {
            return response()->json(['status' => false, 'message' => 'Invalid account ID provided.']);
        }

        // Add the amount to the account's opening balance
        if (isset($input['date']) && !empty($input['date'])) {
            $account->date = $input['date'];
        }
        $account->opening_balance = $input['amount'];
        $account->save();

        return response()->json(['status' => true, 'message' => 'Current Opening Balance Updated successfully']);
    }



    public function getAccountsByBranch()
    {
        return Branch::with('accounts')->orderBy('name','asc')->get();
    }
}
