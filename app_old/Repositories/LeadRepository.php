<?php

namespace App\Repositories;

use App\Models\Country;
use App\Models\Lead;
use App\Models\LeadSource;
use App\Models\LeadStatus;
use App\Models\Note;
use App\Models\Notification;
use App\Models\Reminder;
use App\Models\Tag;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use App\Models\City;
use App\Models\State;
use App\Models\Area;
use App\Models\ItemGroup;
use App\Models\Item;
use Illuminate\Support\Arr;

/**
 * Class LeadRepository
 *
 * @version April 20, 2020, 12:43 pm UTC
 */
class LeadRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'name',
        'position',
        'email',
        'website',
        'phone',
        'company',
        'description',
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
        return Lead::class;
    }

    /**
     * @return array
     */
    public function getData()
    {
        $data['status'] = LeadStatus::orderBy('name', 'asc')->pluck('name', 'id')->toArray();
        $data['sources'] = LeadSource::orderBy('name', 'asc')->pluck('name', 'id')->toArray();
        $data['assigned'] = User::orderBy('first_name', 'asc')->whereIsEnable(true)->user()->get()->pluck(
            'full_name',
            'id'
        );
        $data['languages'] = Lead::LANGUAGES;
        $data['tags'] = Tag::orderBy('name', 'asc');

        $data['product_groups']  = ItemGroup::orderBy('name', 'asc')->pluck('name', 'id')->toArray();
        $data['products']  = Item::orderBy('title', 'asc')->get();
        return $data;
    }

    /**
     * @param  int  $id
     * @param  string  $class
     * @return array
     */
    public function getReminderData($id, $class)
    {
        $data = [];
        $data['reminderTo'] = User::whereIsEnable(true)->user()->get()->pluck('full_name', 'id');
        $data['ownerId'] = $id;

        foreach (Reminder::REMINDER_MODULES as $key => $value) {
            if ($value == $class) {
                $data['moduleId'] = $key;
                break;
            }
        }

        return $data;
    }

    public function create_lead($input)
    {
        return Lead::create(
            Arr::only(
                $input,
                [
                    'name',
                    'product_group_id',
                    'product_id',
                    'budget',
                    'priority',
                    'start_date',
                    'assignee',
                    'contact',
                    'position',
                    'source_id',
                    'employees',
                    'branches',
                    'business',
                    'automation',
                    'status_id',
                    'default_language',
                    'mobile',
                    'whatsapp',
                    'phone',
                    'fax',
                    'email',
                    'website',
                    'country_id',
                    'state_id',
                    'city_id',
                    'area_id',
                    'facebook',
                    'instagram',
                    'linkedin',
                    'location',
                    'description',
                    'inserted_by'
                ],
            )
        );
    }
    public function store($input)
    {
        $input['public'] = isset($input['public']) ? 1 : 0;

        if (isset($input['contacted_today'])) {
            $input['contacted_today'] = 1;
            $input['date_contacted'] = Carbon::now()->toDateTimeString();
        }

        //        $input['phone'] = preparePhoneNumber($input, 'phone');
        $input['phone'] = removeSpaceFromPhoneNumber($input['phone']);
        $input['estimate_budget'] = removeCommaFromNumbers($input['estimate_budget']);

        $lead = Lead::create($input);

        if (! empty($input['assign_to'])) {
            Notification::create([
                'title' => 'New Lead Created',
                'description' => 'You are assigned to ' . $lead->name,
                'type' => Lead::class,
                'user_id' => $input['assign_to'],
            ]);
        }

        activity()->performedOn($lead)->causedBy(getLoggedInUser())
            ->useLog('New Lead created.')->log($lead->name . ' Lead created.');

        if (isset($input['tags']) && ! empty($input['tags'])) {
            $lead->tags()->sync($input['tags']);
        }

        return true;
    }

    /**
     * @param  array  $input
     * @param  Lead  $lead
     * @return bool
     *
     * @throws Exception
     */
    public function update($input, $lead)
    {
        $input['public'] = isset($input['public']) ? 1 : 0;

        if (isset($input['contacted_today']) && empty($lead->date_contacted)) {
            $input['contacted_today'] = 1;
            $input['date_contacted'] = Carbon::now()->toDateTimeString();
        }

        $input['phone'] = removeSpaceFromPhoneNumber($input['phone']);
        $input['estimate_budget'] = removeCommaFromNumbers($input['estimate_budget']);
        $oldUserIds = Lead::whereId($lead->id)->get()->pluck('assign_to')->toArray();
        $userId = implode(' ', $oldUserIds);
        $newUserIds = $input['assign_to'];
        $users = User::whereId($newUserIds)->get();
        $lead->update($input);

        if (! empty($oldUserIds) && $newUserIds !== $userId) {
            foreach ($oldUserIds as $removedUser) {
                Notification::create([
                    'title' => 'Removed From Lead',
                    'description' => 'You removed from ' . $lead->name,
                    'type' => Lead::class,
                    'user_id' => $removedUser,
                ]);
            }
        }
        if ($users->count() > 0 && $newUserIds !== $userId) {
            foreach ($users as $user) {
                Notification::create([
                    'title' => 'New Lead Assigned',
                    'description' => 'You are assigned to ' . $lead->name,
                    'type' => Lead::class,
                    'user_id' => $user->id,
                ]);
                foreach ($oldUserIds as $oldUser) {
                    Notification::create([
                        'title' => 'New User Assigned to Lead',
                        'description' => $user->first_name . ' ' . $user->last_name . ' assigned to ' . $lead->name,
                        'type' => Lead::class,
                        'user_id' => $oldUser,
                    ]);
                }
            }
        }

        activity()->performedOn($lead)->causedBy(getLoggedInUser())
            ->useLog('Lead updated.')->log($lead->name . ' Lead updated.');

        if (isset($input['tags']) && ! empty($input['tags'])) {
            $lead->tags()->sync($input['tags']);
        }

        return true;
    }

    public function update_lead(Lead $lead, $input)
    {
        // Update the lead with the provided input
        $lead->update(
            Arr::only(
                $input,
                [
                    'name',
                    'product_group_id',
                    'product_id',
                    'budget',
                    'priority',
                    'start_date',
                    'assignee',
                    'contact',
                    'position',
                    'source_id',
                    'employees',
                    'branches',
                    'business',
                    'automation',
                    'status_id',
                    'default_language',
                    'mobile',
                    'whatsapp',
                    'phone',
                    'fax',
                    'email',
                    'website',
                    'country_id',
                    'state_id',
                    'city_id',
                    'area_id',
                    'facebook',
                    'instagram',
                    'linkedin',
                    'location',
                    'description',
                    'inserted_by'
                ]
            )
        );

        return $lead;
    }

    /**
     * @param $lead
     * @return Builder[]|Collection
     */
    public function getNoteData($lead)
    {
        return Note::with('user.media')->where('owner_id', '=', $lead->id)
            ->where('owner_type', '=', Lead::class)->orderByDesc('created_at')->get();
    }

    /**
     * @return mixed
     */
    public function getLeadStatusCounts()
    {
        $data = LeadStatus::withCount('leads')->get();

        return $data;
    }
    public function getCountries()
    {
        return  Country::orderBy('name', 'asc')->pluck('name', 'id')->toArray();
    }
    public function getCities()
    {
        return City::get()->groupBy('state_id');
    }
    public function getStates()
    {
        return State::get()->groupBy('country_id');
    }
    public function getAreas()
    {
        return Area::get()->groupBy('city_id');
    }
}
