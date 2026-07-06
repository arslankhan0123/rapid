<?php

namespace App\Repositories;

use App\Mail\ProjectMail;
use App\Models\Contact;
use App\Models\Customer;
use App\Models\Notification;
use App\Models\Project;
use App\Models\ProjectMember;
use App\Models\Tag;
use App\Models\User;
use Arr;
use Auth;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Mail;
use App\Models\Employee;
use App\Models\Country;
use App\Models\Service;
use App\Models\ProjectService;
use App\Models\Item;
use App\Models\ServiceCategory;
use App\Models\Term;
use App\Models\ProjectTerm;

/**
 * Class ProjectRepository
 *
 * @version April 16, 2020, 5:45 am UTC
 */
class ProjectRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'name',
        'progress',
        'billing_type',
        'status',
        'estimate_hours',
        'start_date',
        'deadline',
        'description',
        'send',
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
        return Project::class;
    }

    /**
     * @param  null  $customerId
     * @return mixed
     */
    public function getProjectsStatusCount($customerId = null)
    {
        if (! empty($customerId)) {
            return Project::selectRaw('count(case when status = 0 then 1 end) as not_started')
                ->selectRaw('count(case when status = 1 then 1 end) as in_progress')
                ->selectRaw('count(case when status = 2 then 1 end) as on_hold')
                ->selectRaw('count(case when status = 3 then 1 end) as cancelled')
                ->selectRaw('count(case when status = 4 then 1 end) as finished')
                ->selectRaw('count(*) as total_projects')
                ->where('customer_id', '=', $customerId)->first();
        }

        return Project::selectRaw('count(case when status = 0 then 1 end) as not_started')
            ->selectRaw('count(case when status = 1 then 1 end) as in_progress')
            ->selectRaw('count(case when status = 2 then 1 end) as on_hold')
            ->selectRaw('count(case when status = 3 then 1 end) as cancelled')
            ->selectRaw('count(case when status = 4 then 1 end) as finished')
            ->selectRaw('count(*) as total_projects')
            ->first();
    }

    /**
     * @return mixed
     */
    public function getSyncList()
    {
        $data['customers'] = Customer::orderBy('updated_at', 'desc')->pluck('company_name', 'id')->toArray();
        $data['members'] = Employee::with('designation')->orderBy('name', 'asc')->get();
        $data['billingTypes'] = Project::BILLING_TYPES;
        $data['status'] = Project::STATUS;
        $data['tags'] = Tag::orderBy('name', 'asc')->pluck('name', 'id')->toArray();
        $data['countries'] = Country::orderBy('name', 'asc')->pluck('name', 'id')->toArray();
        $data['languages'] = Customer::LANGUAGES;

        return $data;
    }
    public function getServices()
    {
        return Item::get();
    }
    public function getServiceCategories()
    {
        return ServiceCategory::pluck('name', 'id');
    }

    /**
     * @param  array  $input
     */
    public function saveProject($input)
    {


        $projectInputs = Arr::except($input, ['members']);
        //  $userIds = $input['members'];

        $project = $this->create($projectInputs);

        activity()->performedOn($project)->causedBy(getLoggedInUser())
            ->useLog('New Project created.')->log($project->project_name . ' Project created.');


        $this->storeProjectService($input, $project);
        $this->storeTerms($project->id, $input);
        // if (! empty($input['members'])) {

        //     // Create an array to hold the member IDs and their respective rates
        //     $memberRates = [];

        //     // Loop through members and their corresponding hourly rates
        //     foreach ($input['members'] as $index => $memberId) {
        //         // Push the member ID and rate into the new array
        //         $memberRates[] = [
        //             'id' => $memberId,
        //             'hourly_rate' => $input['hourly_rate'][$index],
        //         ];
        //     }


        //      $this->storeProjectMembers($memberRates, $project);
        // }

        // if (isset($input['contacts']) && ! empty($input['contacts'])) {
        //     $project->projectContacts()->sync($input['contacts']);
        // }

        // if (isset($input['tags']) && ! empty($input['tags'])) {
        //     $project->tags()->sync($input['tags']);
        // }
    }

    /**
     * @param  int  $id
     * @return mixed
     */
    public function getProjectData($id)
    {
        $project = Project::with(['tags'])->select('projects.*')->find($id);

        return $project;
    }

    /**
     * @param  int  $id
     * @param $input
     * @return Builder|Builder[]|Collection|Model
     */
    public function updateProject($id, $input)
    {
        $projectInputs = Arr::except($input, ['members', 'tags']);
        $projectInputs['calculate_progress_through_tasks'] = isset($input['calculate_progress_through_tasks']) ? 1 : 0;
        $projectInputs['progress'] = isset($input['progress']) ? $input['progress'] : 0;

        $projectInputs['send_email'] = isset($input['send_email']) ? 1 : 0;

        $project = Project::findOrFail($id);

        $oldUserIds = $project->members->pluck('user_id')->toArray();
        $oldContactIds = $project->projectContacts->pluck('user_id')->toArray();

        // $newUserIds = $input['members'];
        // $contactIds = $input['contacts'];
        // $newContactIds = Contact::whereIn('id', $contactIds)->get()->pluck('user_id')->toArray();

        // $removedUserIds = array_diff($oldUserIds, $newUserIds);
        // $removedContactIds = array_diff($oldContactIds, $newContactIds);

        // $userIds = array_diff($newUserIds, $oldUserIds);
        // $contactIds = array_diff($newContactIds, $oldContactIds);

        // $users = Employee::whereIn('id', $userIds)->get();
        // $contacts = User::whereIn('id', $contactIds)->get();

        $project = $this->update($projectInputs, $id);

        if (! empty($removedContactIds)) {
            foreach ($removedContactIds as $removedUser) {
                Notification::create([
                    'title' => 'Removed From Project',
                    'description' => 'You removed from ' . $project->project_name,
                    'type' => Project::class,
                    'user_id' => $removedUser,
                ]);
            }
        }

        // if ($contacts->count() > 0) {
        //     foreach ($contacts as $user) {
        //         Notification::create([
        //             'title' => 'New Project Assigned',
        //             'description' => 'You are assigned to '.$project->project_name,
        //             'type' => Project::class,
        //             'user_id' => $user->id,
        //         ]);
        //         foreach ($oldContactIds as $oldUser) {
        //             Notification::create([
        //                 'title' => 'New User Assigned to Project',
        //                 'description' => $user->first_name.' '.$user->last_name.' assigned to '.$project->project_name,
        //                 'type' => Project::class,
        //                 'user_id' => $oldUser,
        //             ]);
        //         }
        //     }
        // }

        if (! empty($removedUserIds)) {
            foreach ($removedUserIds as $removedUser) {
                Notification::create([
                    'title' => 'Removed From Project',
                    'description' => 'You removed from ' . $project->project_name,
                    'type' => Project::class,
                    'user_id' => $removedUser,
                ]);
            }
        }
        // if ($users->count() > 0) {
        //     foreach ($users as $user) {
        //         Notification::create([
        //             'title' => 'New Project Assigned',
        //             'description' => 'You are assigned to ' . $project->project_name,
        //             'type' => Project::class,
        //             'user_id' => $user->id,
        //         ]);
        //         foreach ($oldUserIds as $oldUser) {
        //             Notification::create([
        //                 'title' => 'New User Assigned to Project',
        //                 'description' => $user->name . ' ' . $user->name . ' assigned to ' . $project->project_name,
        //                 'type' => Project::class,
        //                 'user_id' => $oldUser,
        //             ]);
        //         }
        //     }
        // }

        activity()->performedOn($project)->causedBy(getLoggedInUser())
            ->useLog('Project updated.')->log($project->project_name . ' Project updated.');

        $this->storeProjectService($input, $project);

        $this->storeTerms($project->id, $input);
        // if (! empty($input['members'])) {

        //     // Create an array to hold the member IDs and their respective rates
        //     $memberRates = [];

        //     // Loop through members and their corresponding hourly rates
        //     foreach ($input['members'] as $index => $memberId) {
        //         // Push the member ID and rate into the new array
        //         $memberRates[] = [
        //             'id' => $memberId,
        //             'hourly_rate' => $input['hourly_rate'][$index],
        //         ];
        //     }

        //     $this->storeProjectMembers($memberRates, $project);
        // }

        // if (isset($input['contacts']) && ! empty($input['contacts'])) {
        //     $project->projectContacts()->sync($input['contacts']);
        // }

        // if (isset($input['tags']) && ! empty($input['tags'])) {
        //     $project->tags()->sync($input['tags']);
        // }

        return $project;
    }

    public function storeProjectService($input, $project)
    {
        // dd($input);
        // Delete existing project services before adding new ones
        $project->services()->delete();

        // Loop through the array of ref_no, service_id, and unit_price
        foreach ($input['ref_no'] as $index => $ref_no) {
            $data['project_id'] = $project->id; // The project ID
            $data['ref_no'] = $ref_no; // Reference number from input array
            $data['category_id'] = $input['category_id'][$index];
            $data['service_id'] = $input['service_id'][$index]; // Corresponding service_id from input array
            $data['unit_price'] = $input['unit_price'][$index]; // Corresponding unit price from input array

            // Create a new ProjectService record if service_id is provided
            if ($data['service_id']) {
                $projectService = ProjectService::create($data);
            }
        }
    }



    public function storeProjectMembers($input, $project)
    {


        $project->members()->delete();

        foreach ($input as $record) {

            $data['owner_id'] = $project->getId();
            $data['owner_type'] = $project->getOwnerType();
            $data['user_id'] = $record['id'];
            $data['hourly_rate'] = $record['hourly_rate'];

            if ($record['id']) {
                $projectMember = ProjectMember::create($data);
            }


            if (isset($input['send_email']) && $input['send_email']) {
                Mail::to($projectMember->user->email)->send(new ProjectMail($project, $projectMember));
            }
        }

        // return true;
    }

    /**
     * @param $input
     * @param $project
     * @return bool
     */
    public function updateProjectMembers($input, $project)
    {
        $project->members()->delete();
        $existUserIds = $project->members->pluck('user_id')->toArray();

        foreach ($input['members'] as $record) {
            $data['owner_id'] = $project->getId();
            $data['owner_type'] = $project->getOwnerType();
            $data['user_id'] = $record;
            $projectMember = ProjectMember::create($data);

            if ($project->send_email && ! in_array($record, $existUserIds)) {
                Mail::to($projectMember->user->email)->send(new ProjectMail($project, $projectMember));
            }
        }

        return true;
    }

    /**
     * @param  int  $id
     * @return mixed
     */
    public function getProjectDetails($id)
    {
        $project = Project::with('tags', 'projectContacts.user', 'members.user', 'members.user.designation', 'customer', 'terms', 'branch')->find($id);

        return $project;
    }

    /**
     * @param $projectId
     * @return mixed
     */
    public function getProjectDetailClient($projectId)
    {
        $customerId = Auth::user()->contact->customer_id;

        $project = Project::with(
            'tags',
            'projectContacts.user',
            'members.user',
            'customer'
        )->whereCustomerId($customerId)->findOrFail($projectId);

        return $project;
    }

    public function getCustomers()
    {
        return Customer::orderBy('id', 'desc')->get();
    }
    public function getTerms()
    {
        return Term::pluck('terms', 'id');
    }

    public function storeTerms($id, $input)
    {


        // Retrieve terms and descriptions from the input
        $terms = $input['terms'] ?? [];
        $descriptions = $input['termDescription'] ?? [];

        // Filter out any null or empty terms and ensure they are properly indexed
        $terms = array_filter($terms, function ($term) {
            return !is_null($term) && $term !== '';
        });

        ProjectTerm::where('project_id', $id)->delete();
        // If the terms are still empty after filtering, return early
        if (empty($terms)) {

            return;
        }



        // Prepare data for insertion
        $estimateTerms = [];
        $count = count($terms);  // Use the filtered count of terms

        // Loop through the terms and descriptions
        for ($i = 0; $i < $count; $i++) {
            $term = $terms[$i] ?? null;
            $description = $descriptions[$i] ?? null;

            // Skip if both term and description are empty or null
            if (empty($term) && empty($description)) {
                continue;
            }

            $estimateTerms[] = [
                'project_id' => $id,
                'terms_id' => $term, // Get terms_id from the terms array
                'description' => $description, // Get description, if available
                'created_at' => now(), // Set the created_at timestamp
                'updated_at' => now(), // Set the updated_at timestamp
            ];
        }

        // Insert new terms if there are valid entries
        if (!empty($estimateTerms)) {
            ProjectTerm::insert($estimateTerms);
        }
    }
}
