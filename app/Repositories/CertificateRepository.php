<?php

namespace App\Repositories;

use App\Models\Certificate;
use App\Models\Customer;
use App\Models\Employee;
use App\Models\SampleCategory;
use App\Models\SampleReceiving;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\CertificateType;


/**
 * Class CustomerRepository
 *
 * @version April 3, 2020, 6:37 am UTC
 */
class CertificateRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'date',
        'employee',
        'lab_manager',
        'general_manager',
        'description',
        'certificate_number',
        'type_id'
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
        return Certificate::class;
    }

    public function create($input)
    {
        return Certificate::create(Arr::only($input, [
            'date',
            'employee',
            'lab_manager',
            'general_manager',
            'description',
            'certificate_number',
            'type_id'
        ]));
    }
    /**
     * @return mixed
     */
    public function getSyncList()
    {
        $data['clients'] = Employee::orderBy('id', 'desc')->pluck('name', 'id')->toArray();
        return $data;
    }
    /**
     * @return mixed
     */
    public function getData($id)
    {
        $certificate = Certificate::with('type')->find($id);

        if (!$certificate) {
            return ['error' => 'Certificate not found'];
        }

        return [
            'data' => $certificate->toArray()
        ];
    }

    public function getCertificateData()
    {
        return Certificate::query()->with('type')->get();
    }
    public function getTypes()
    {
        return CertificateType::pluck('name', 'id');
    }
}
