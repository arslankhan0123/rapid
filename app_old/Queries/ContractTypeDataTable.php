<?php

namespace App\Queries;

use App\Models\ContractType;

/**
 * Class TagDataTable
 */
class ContractTypeDataTable
{
    /**
     * @return ContractType
     */
    public function get()
    {
        /** @var ContractType $query */
        $query = ContractType::query()->select('contract_types.*')->withCount('contracts')->latest();

        return $query;
    }
}
