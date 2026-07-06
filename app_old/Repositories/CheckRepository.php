<?php

namespace App\Repositories;

use App\Models\Check;
use App\Models\Branch;
use Illuminate\Support\Arr;
use App\Http\Requests\CheckRequest;
use App\Http\Requests\UpdateCheckRequest;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use App\Models\Bank;

/**
 * Class ProductRepository
 *
 * @version October 12, 2021, 10:50 am UTC
 */
class CheckRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'check_number',
        'issue_name',
        'amount',
        'branch_id',
        'date',
        'bank_id',
        'issue_place',
    ];


    public function getFieldsSearchable()
    {
        return $this->fieldSearchable;
    }

    public function model()
    {
        return Check::class;
    }


    public function getBranches()
    {
        return Branch::pluck('name', 'id')->toArray();
    }


    public function saveCheck($input)
    {
        $check = Check::create(Arr::only($input, $this->getFieldsSearchable()));
        return $check;
    }


    public function updateCheck($input, $check)
    {
        $check->update(Arr::only($input, $this->getFieldsSearchable()));
        return $check;
    }
    public function getBanks()
    {
        return Bank::pluck('name', 'id');
    }
}
