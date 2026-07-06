<?php

namespace App\Repositories;

use App\Models\Customer;
use App\Models\Employee;
use App\Models\SampleCategory;
use App\Models\SampleReceiving;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/**
 * Class CustomerRepository
 *
 * @version April 3, 2020, 6:37 am UTC
 */
class SampleReceivingRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'date',
        'time',
        'section',
        'client_name',
        'client_reference',
        'type_of_sample',
        'required_tests',
        'number_of_sample',
        'delivered_by',
        'received_by',
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
        return SampleReceiving::class;
    }

    public function create($input)
    {
        return SampleReceiving::create(Arr::only($input, $this->getFieldsSearchable()));
    }
    /**
     * @return mixed
     */
    public function getSyncList()
    {
        $data['clients'] = Customer::orderBy('company_name', 'asc')->pluck('company_name', 'id')->toArray();
        $employees = Employee::with('designation')
            ->select('id', DB::raw("CONCAT(name, ' - ', COALESCE((SELECT name FROM designations WHERE id = employees.designation_id), 'N/A')) AS name"))
            ->get();
        $data['employees'] = $employees->pluck('name', 'id')->toArray();
        $data['section'] = SampleCategory::orderBy('updated_at', 'desc')->pluck('name', 'id')->toArray();
        return $data;
    }
    /**
     * @return mixed
     */
    public function getData($id)
    {
        $sampleReceivingData = SampleReceiving::with('branch')->find($id)->toArray();
        $deliveredBy = Employee::with('designation')
            ->where('id', $sampleReceivingData['delivered_by'])
            ->select('id', DB::raw("CONCAT(name, ' - ', COALESCE((SELECT name FROM designations WHERE id = employees.designation_id), 'N/A')) AS name"))
            ->get();
        $data['deliveredBy'] = $deliveredBy->pluck('name')->toArray();
        $receivedBy = Employee::with('designation')
            ->where('id', $sampleReceivingData['received_by'])
            ->select('id', DB::raw("CONCAT(name, ' - ', COALESCE((SELECT name FROM designations WHERE id = employees.designation_id), 'N/A')) AS name"))
            ->get();
        $data['receivedBy'] = $receivedBy->pluck('name')->toArray();
        $data['section'] = SampleCategory::orderBy('updated_at', 'desc')
            ->where('id', $sampleReceivingData['section'])
            ->pluck('name')->toArray();
        $data['sampleReceiving'] = $sampleReceivingData;
        return $data;
    }
    public function getSampleReceivingData()
    {
        $sampleReceivingData = SampleReceiving::query()->with('branch')
            ->leftJoin('sample_categories as sc', 'sample_receiving.section', '=', 'sc.id')
            ->leftJoin('employees as ed', 'ed.id', '=', 'sample_receiving.delivered_by')
            ->leftJoin('employees as er', 'er.id', '=', 'sample_receiving.received_by')
            ->select([
                'sc.name as sample_categories_name',
                'ed.name as delivered_by_name',
                'er.name as received_by_name',
                'sample_receiving.date',
                'sample_receiving.time',
                'sample_receiving.client_name',
                'sample_receiving.client_reference',
                'sample_receiving.type_of_sample',
                'sample_receiving.required_tests',
                'sample_receiving.number_of_sample',
                'sample_receiving.created_at',
                'sample_receiving.branch_id'
            ])
            ->get();
        return $sampleReceivingData;
    }
}
