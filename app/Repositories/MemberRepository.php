<?php

namespace App\Repositories;

use App\Exceptions\ApiOperationFailedException;
use App\Models\Role;
use App\Models\User;
use DB;
use Exception;
use Illuminate\Support\Facades\Hash;
use Throwable;
use App\Models\Employee;
use App\Models\Branch;
use App\Models\UsersBranch;

/**
 * Class CustomerGroupRepository
 */
class MemberRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'image',
        'language_id',
        'otp_enabled'
    ];

    /**
     * @return array
     */
    public function getLanguageList()
    {
        $data = [];
        $data['languages'] = User::LANGUAGES;

        return $data;
    }

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
        return User::class;
    }

    /**
     * @param  User  $member
     * @return User
     */
    public function prepareCustomerData($member)
    {
        $member->default_language = $member->default_language != null ? setLanguage($member->default_language) : null;

        return $member;
    }

    /**
     * @param  array  $input
     * @return mixed
     *
     * @throws ApiOperationFailedException
     * @throws Exception
     * @throws Throwable
     */
    public function store($input)
    {
        try {
            DB::beginTransaction();
            $input['password'] = Hash::make($input['password']);
            //            $input['phone'] = preparePhoneNumber($input, 'phone');
            $input['phone'] = removeSpaceFromPhoneNumber($input['phone']);
            $member = User::create($input);

            activity()->performedOn($member)->causedBy(getLoggedInUser())
                ->useLog('New Member created.')->log($member->full_name . ' Member created.');

            if (isset($input['send_welcome_email']) && ! empty($input['send_welcome_email'])) {
                $member->sendEmailVerificationNotification();
            }

            if ((isset($input['image']))) {
                $member->addMedia($input['image'])
                    ->toMediaCollection(User::COLLECTION_PROFILE_PICTURES, config('app.media_disc'));
            }

            $roles = Role::whereName('staff_member')->first()->id;
            $member->roles()->sync($roles);

            if (isset($input['permissions']) && $input['permissions']) {
                $member->permissions()->sync($input['permissions']);
            }

            $this->updateUsersBranches($member->id, $input['branches'] ?? []);

            DB::commit();

            return $member;
        } catch (Exception $e) {
            DB::rollBack();
            throw new ApiOperationFailedException($e->getMessage());
        }
    }

    /**
     * @param  int  $userId
     * @param  array  $input
     * @return bool
     *
     * @throws Throwable
     */
    public function updateMember($userId, $input)
    {
        //        $input['phone'] = preparePhoneNumber($input, 'phone');
        $input['phone'] = removeSpaceFromPhoneNumber($input['phone']);

        /** @var User $member */
        $member = User::find($userId);

        $this->update($input, $userId);

        activity()->performedOn($member)->causedBy(getLoggedInUser())
            ->useLog('Member updated.')->log($member->full_name . ' Member updated.');

        $roles = Role::whereName('staff_member')->first()->id;
        $member->roles()->sync($roles);

        if (isset($input['permissions']) && $input['permissions']) {
            $member->permissions()->sync($input['permissions']);
        }

        if ((isset($input['image']))) {
            $member->clearMediaCollection(User::COLLECTION_PROFILE_PICTURES);
            $member->addMedia($input['image'])
                ->toMediaCollection(User::COLLECTION_PROFILE_PICTURES, config('app.media_disc'));
        }

        $this->updateUsersBranches($userId, $input['branches'] ?? []);


        return true;
    }
    public function updateUsersBranches($userId, $branches)
    {
        UsersBranch::where('user_id', $userId)->delete();
        foreach ($branches as $branchId) {
            UsersBranch::create([
                'user_id' => $userId,
                'branch_id' => $branchId,
            ]);
        }
    }

    /**
     * @return mixed
     */
    public function memberCount()
    {
        return User::selectRaw('count(case when is_enable = 1 then 1 end) as active_members')
            ->selectRaw('count(case when is_enable = 0 then 1 end) as deactive_members')
            ->selectRaw('count(*) as total_members')
            ->where('owner_id', '=', null)->where('owner_type', '=', null)->first();
    }
    public function getEmployees()
    {
        return Employee::where('status', 1)->get();
    }

    public function getAllBranches()
    {
        return Branch::pluck('name', 'id');
    }
}
