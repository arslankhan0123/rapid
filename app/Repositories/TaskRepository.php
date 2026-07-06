<?php

namespace App\Repositories;

use App\Models\Comment;
use App\Models\Contract;
use App\Models\Customer;
use App\Models\Estimate;
use App\Models\Invoice;
use App\Models\Lead;
use App\Models\Notification;
use App\Models\Project;
use App\Models\Proposal;
use App\Models\Tag;
use App\Models\Task;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class TaskRepository
 *
 * @version April 13, 2020, 10:21 am UTC
 */
class TaskRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'subject',
        'status',
        'start_date',
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
        return Task::class;
    }

    /**
     * @return mixed
     */
    public function getStatusCount()
    {
        return Task::selectRaw('count(case when status = 1 then 1 end) as not_started')
            ->selectRaw('count(case when status = 2 then 1 end) as in_progress')
            ->selectRaw('count(case when status = 3 then 1 end) as testing')
            ->selectRaw('count(case when status = 4 then 1 end) as awaiting_feedback')
            ->selectRaw('count(case when status = 5 then 1 end) as completed')
            ->first();
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        $data['status'] = Task::STATUS;
        $data['priority'] = Task::PRIORITY;
        $data['relatedTo'] = Task::RELATED_TO_array;
        $data['tags'] = Tag::orderBy('name', 'asc')->pluck('name', 'id');
        $data['users'] = User::orderBy('first_name')->whereNull(['owner_id', 'owner_type'])->where('is_enable',
            1)->get()->pluck('full_name',
            'id');

        return $data;
    }

    /**
     * @param  array  $input
     * @return bool
     */
    public function store($input)
    {
        // TODO: add comma to rate and add attachment files
        $input['public'] = isset($input['public']) ? 1 : 0;
        $input['billable'] = isset($input['billable']) ? 1 : 0;

        if (isset($input['related_to']) && isset($input['owner_id'])) {
            $input['owner_type'] = Task::RELATED_TO[$input['related_to']];
        }

        $task = Task::create($input);
        $users = User::all()->where('id', '=', $task->member_id);

        if (! empty($input['subject'])) {
            foreach ($users as $user) {
                Notification::create([
                    'title' => 'New Task Created',
                    'description' => 'You are assigned to '.$task->subject,
                    'type' => Task::class,
                    'user_id' => $user->id,
                ]);
            }
        }

        activity()->performedOn($task)->causedBy(getLoggedInUser())
            ->useLog('New Task created.')->log($task->subject.' Task created.');

        if (isset($input['tags']) && ! empty($input['tags'])) {
            $task->tags()->sync($input['tags']);
        }

        return true;
    }

    /**
     * @param  array  $input
     * @param  Task  $task
     * @return bool|Builder|Builder[]|Collection|Model
     */
    public function update($input, $task)
    {
        $input['public'] = isset($input['public']) ? 1 : 0;
        $input['billable'] = isset($input['billable']) ? 1 : 0;

        if (isset($input['related_to']) && isset($input['owner_id'])) {
            $input['owner_type'] = Task::RELATED_TO[$input['related_to']];
        }

        $oldUserIds = Task::whereId($task->id)->get()->pluck('member_id')->toArray();
        $userId = implode(' ', $oldUserIds);
        $newUserIds = $input['member_id'];
        $users = User::whereId($newUserIds)->get();
        $task->update($input);

        if (! empty($oldUserIds) && $newUserIds !== $userId) {
            foreach ($oldUserIds as $removedUser) {
                Notification::create([
                    'title' => 'Removed From Task',
                    'description' => 'You removed from '.$task->subject,
                    'type' => Task::class,
                    'user_id' => $removedUser,
                ]);
            }
        }
        if ($users->count() > 0 && $newUserIds !== $userId) {
            foreach ($users as $user) {
                Notification::create([
                    'title' => 'New Task Assigned',
                    'description' => 'You are assigned to '.$task->subject,
                    'type' => Task::class,
                    'user_id' => $user->id,
                ]);
                foreach ($oldUserIds as $oldUser) {
                    Notification::create([
                        'title' => 'New User Assigned to Task',
                        'description' => $user->first_name.' '.$user->last_name.' assigned to '.$task->subject,
                        'type' => Task::class,
                        'user_id' => $oldUser,
                    ]);
                }
            }
        }

        activity()->performedOn($task)->causedBy(getLoggedInUser())
            ->useLog('Task updated.')->log($task->subject.' Task updated.');

        if (isset($input['tags']) && ! empty($input['tags'])) {
            $task->tags()->sync($input['tags']);
        }

        return true;
    }

    /**
     * @param  int  $id
     * @return Collection|null
     */
    public function getOwner($id)
    {
        $ownerType = $id != null ? Task::RELATED_TO[$id] : null;

        switch ($ownerType) {
            case Customer::class:
                return Customer::pluck('company_name', 'id');

            case Invoice::class:
                $invoices = Invoice::get();
                $data['invoices'] = [];

                foreach ($invoices as $invoice) {
                    $data['invoices'][$invoice->id] = $invoice->title.' - '.$invoice->invoice_number;
                }

                return $data['invoices'];

            case Ticket::class:
                return Ticket::pluck('subject', 'id');

            case Project::class:
                return Project::pluck('project_name', 'id');

            case Proposal::class:
                $proposals = Proposal::get();
                $data['proposals'] = [];

                foreach ($proposals as $proposal) {
                    $data['proposals'][$proposal->id] = $proposal->title.' - '.$proposal->proposal_number;
                }

                return $data['proposals'];

            case Estimate::class:
                $estimates = Estimate::get();
                $data['estimates'] = [];

                foreach ($estimates as $estimate) {
                    $data['estimates'][$estimate->id] = $estimate->title.' - '.$estimate->estimate_number;
                }

                return $data['estimates'];

            case Lead::class:
                return Lead::pluck('name', 'id');

            case Contract::class:
                return Contract::pluck('subject', 'id');

            default:
                return null;
        }
    }

    /**
     * @param $task
     * @return mixed
     */
    public function getCommentData($task)
    {
        return Comment::with('user.media')->where('owner_id', '=', $task->id)
            ->where('owner_type', '=', Task::class)->orderByDesc('created_at')->get();
    }

    /**
     * @param $task
     * @return Task
     */
    public function getTaskDetailClient($task)
    {
        $customerId = $task->getRelatedTo(Project::class, 'id');

        /** @var Task $task */
        $task = Task::whereOwnerId($customerId)->whereOwnerType(Project::class)->findOrFail($task->id);

        return $task;
    }
}
