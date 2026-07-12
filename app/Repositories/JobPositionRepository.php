<?php

namespace App\Repositories;

use Illuminate\Support\Arr;
use App\Models\JobPosition;
use Illuminate\Support\Facades\DB;
use Exception;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class JobPositionRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'position_code',
        'title',
        'description'
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
        return JobPosition::class;
    }

    public function create($input)
    {
        try {
            DB::beginTransaction();

            $jobPosition = JobPosition::create(Arr::only($input, [
                'position_code',
                'title',
                'category_id',
                'department_id',
                'employment_type',
                'experience_required',
                'min_salary',
                'max_salary',
                'description',
                'status'
            ]));

            if (isset($input['skills'])) {
                $jobPosition->skills()->sync($input['skills']);
            }

            DB::commit();

            return $jobPosition;
        } catch (Exception $e) {
            DB::rollBack();
            throw new UnprocessableEntityHttpException($e->getMessage());
        }
    }

    public function update($input, $id)
    {
        try {
            DB::beginTransaction();

            $jobPosition = $this->model->findOrFail($id);
            $jobPosition->update(Arr::only($input, [
                'position_code',
                'title',
                'category_id',
                'department_id',
                'employment_type',
                'experience_required',
                'min_salary',
                'max_salary',
                'description',
                'status'
            ]));

            if (isset($input['skills'])) {
                $jobPosition->skills()->sync($input['skills']);
            } else {
                $jobPosition->skills()->sync([]);
            }

            DB::commit();

            return $jobPosition;
        } catch (Exception $e) {
            DB::rollBack();
            throw new UnprocessableEntityHttpException($e->getMessage());
        }
    }
}
