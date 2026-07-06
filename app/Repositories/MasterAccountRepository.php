<?php

namespace App\Repositories;

use App\Models\MasterAccount;
use Illuminate\Support\Arr;

class MasterAccountRepository extends BaseRepository
{
    protected $fieldSearchable = [
        'name',
        'account_number',
        'account_level',
        'account_type',
        'report_type',
        'amount_type',
        'description',
        'status'
    ];

    public function getFieldsSearchable()
    {
        return $this->fieldSearchable;
    }

    public function model()
    {
        return MasterAccount::class;
    }

    public function create($input)
    {
        return MasterAccount::create(
            Arr::only($input, ['name', 'account_number', 'account_level', 'account_type', 'report_type', 'amount_type', 'description', 'status'])
        );
    }

    public function update($input, $id)
    {
        $masterAccount = MasterAccount::find($id);

        if (!$masterAccount) {
            return false;
        }

        $masterAccount->update(
            Arr::only($input, ['name', 'account_number', 'account_level', 'account_type', 'report_type', 'amount_type', 'description', 'status'])
        );

        return $masterAccount;
    }

    public function getMasterAccounts()
    {
        return MasterAccount::orderBy('account_type')
            ->orderBy('account_level')
            ->orderBy('name')
            ->get();
    }
}
