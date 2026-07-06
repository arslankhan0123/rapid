<?php

namespace App\Repositories;

use App\Models\Contact;
use App\Models\Department;
use App\Models\Note;
use App\Models\Notification;
use App\Models\PredefinedReply;
use App\Models\Reminder;
use App\Models\Service;
use App\Models\Tag;
use App\Models\Ticket;
use App\Models\TicketPriority;
use App\Models\TicketStatus;
use App\Models\User;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Arr;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

/**
 * Class TicketRepository
 *
 * @version April 8, 2020, 6:13 am UTC
 */
class TicketRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'subject',
        'contact_id',
        'name',
        'email',
        'department_id',
        'cc',
        'assign_to',
        'priority_id',
        'service_id',
        'predefined_reply_id',
        'attachments',
    ];

    /**
     * Return searchable fields
     *
     * @return array
     */
    public function getFieldsSearchable(): array
    {
        return $this->fieldSearchable;
    }

    /**
     * Configure the Model
     **/
    public function model(): string
    {
        return Ticket::class;
    }

    /**
     * @param  null  $customerId
     * @return mixed
     */
    public function getTicketStatusCounts($customerId = null)
    {
        if (isset($customerId)) {
            $data = TicketStatus::with([
                'tickets.contact' => function (BelongsTo $query) use ($customerId) {
                    $query->where('customer_id', '=', $customerId);
                },
            ])->withCount('tickets')->get();
        } else {
            $data = TicketStatus::withCount('tickets')->get();
        }

        return $data;
    }

    /**
     * @return array
     */
    public function getSyncList(): array
    {
        $data = [];
        $data['contacts'] = Contact::with('user')->get()->where('user.is_enable', '=', true)->pluck('user.full_name',
            'id');
        $data['departments'] = Department::orderBy('name', 'asc')->pluck('name', 'id')->toArray();
        $data['assignTo'] = User::orderBy('first_name')->whereIsEnable(true)->user()->get()->pluck('full_name', 'id');
        $data['priority'] = TicketPriority::orderBy('name', 'asc')->whereStatus(true)->pluck('name', 'id');
        $data['ticketStatus'] = TicketStatus::orderBy('name', 'asc')->pluck('name', 'id')->toArray();
        $data['services'] = Service::orderBy('name', 'asc')->pluck('name', 'id')->toArray();
        $data['tags'] = Tag::orderBy('name', 'asc')->pluck('name', 'id')->toArray();
        $data['predefinedReplies'] = PredefinedReply::orderBy('reply_name', 'asc')->pluck('reply_name',
            'id')->toArray();

        return $data;
    }

    /**
     * @param  int  $id
     * @param  string  $class
     * @return array
     */
    public function getReminderData($id, $class): array
    {
        $data = [];
        $data['reminderTo'] = User::whereIsEnable(true)->user()->get()->pluck('full_name', 'id')->toArray();
        $data['ownerId'] = $id;

        foreach (Reminder::REMINDER_MODULES as $key => $value) {
            if ($value == $class) {
                $data['moduleId'] = $key;
                break;
            }
        }

        return $data;
    }

    /**
     * @param  array  $input
     * @return Ticket
     */
    public function create($input): Ticket
    {
        try {
            $ticketInput = Arr::except($input, ['tags', 'attachments']);
            $tagsInput = Arr::only($input, ['tags']);
            $attachmentsInput = Arr::only($input, ['attachments']);

            /** @var Ticket $ticket */
            $ticket = Ticket::create($ticketInput);
            $users = User::whereId($ticket->assign_to)->get();
            $contacts = Contact::whereId($ticket->contact_id)->get();

            if (! empty($input['assign_to'])) {
                foreach ($users as $user) {
                    Notification::create([
                        'title' => 'New Ticket Created',
                        'description' => 'You are assigned to '.$ticket->subject,
                        'type' => Ticket::class,
                        'user_id' => $user->id,
                    ]);
                }
            }

            if (! empty($input['contact_id'])) {
                foreach ($contacts as $contact) {
                    Notification::create([
                        'title' => 'New Ticket Created',
                        'description' => 'You are assigned to '.$ticket->subject,
                        'type' => Ticket::class,
                        'user_id' => $contact->user_id,
                    ]);
                }
            }

            activity()->performedOn($ticket)->causedBy(getLoggedInUser())
                ->useLog('New Ticket created.')->log($ticket->subject.' Ticket created.');

            if (isset($input['tags']) && ! empty($tagsInput)) {
                $ticket->tags()->sync($input['tags']);
            }

            if (! empty($attachmentsInput)) {
                foreach ($attachmentsInput['attachments'] as $attachment) {
                    $ticket->addMedia($attachment)->toMediaCollection(Ticket::TICKET_ATTACHMENT_PATH,
                        config('app.media_disc'));
                }
            }

            return $ticket;
        } catch (Exception $e) {
            throw new UnprocessableEntityHttpException($e->getMessage());
        }
    }

    /**
     * @param  array  $input
     * @param  Ticket  $ticket
     * @return void
     */
    public function update($input, $ticket)
    {
        try {
            $ticketInput = Arr::except($input, ['tags', 'attachments']);
            $tagsInput = Arr::only($input, ['tags']);
            $attachmentsInput = Arr::only($input, ['attachments']);

            $oldUserIds = Ticket::whereId($ticket->id)->get()->pluck('assign_to')->toArray();
            $oldContactUserIds = Ticket::whereId($ticket->id)->get()->pluck('contact_id')->toArray();
            $oldContact = Contact::whereId($ticket->contact_id)->get()->pluck('user_id')->toArray();

            $useraId = implode(' ', $oldContact);

            $userId = implode(' ', $oldUserIds);
            $contactUserId = implode(' ', $oldContactUserIds);

            $newUserIds = $input['assign_to'];
            $newUserContactIds = $input['contact_id'];

            $newContactUserIds = Contact::whereId($input['contact_id'])->get()->pluck('user_id')->toArray();

            $users = User::whereId($newUserIds)->get();
            $userContacts = User::whereId($newContactUserIds)->get();
            $oldContactUser = User::whereId($useraId)->get();

            $ticket->update($ticketInput);

            // Contact Notification
            if (! empty($oldContactUserIds) && $newUserContactIds !== $contactUserId) {
                foreach ($oldContactUser as $removedUser) {
                    Notification::create([
                        'title' => 'Removed From Ticket',
                        'description' => 'You removed from '.$ticket->subject,
                        'type' => Ticket::class,
                        'user_id' => $removedUser->id,
                    ]);
                }
            }
            if ($userContacts->count() > 0 && $newUserContactIds !== $contactUserId) {
                foreach ($userContacts as $user) {
                    Notification::create([
                        'title' => 'New Ticket Assigned',
                        'description' => 'You are assigned to '.$ticket->subject,
                        'type' => Ticket::class,
                        'user_id' => $user->id,
                    ]);
                    foreach ($oldContactUser as $oldUser) {
                        Notification::create([
                            'title' => 'New User Assigned to Ticket',
                            'description' => $user->first_name.' '.$user->last_name.' assigned to '.$ticket->subject,
                            'type' => Ticket::class,
                            'user_id' => $oldUser->id,
                        ]);
                    }
                }
            }

            // User Notification
            if (! empty($oldUserIds) && $newUserIds !== $userId) {
                foreach ($oldUserIds as $removedUser) {
                    Notification::create([
                        'title' => 'Removed From Ticket',
                        'description' => 'You removed from '.$ticket->subject,
                        'type' => Ticket::class,
                        'user_id' => $removedUser,
                    ]);
                }
            }
            if ($users->count() > 0 && $newUserIds !== $userId) {
                foreach ($users as $user) {
                    Notification::create([
                        'title' => 'New Ticket Assigned',
                        'description' => 'You are assigned to '.$ticket->subject,
                        'type' => Ticket::class,
                        'user_id' => $user->id,
                    ]);
                    foreach ($oldUserIds as $oldUser) {
                        Notification::create([
                            'title' => 'New User Assigned to Ticket',
                            'description' => $user->first_name.' '.$user->last_name.' assigned to '.$ticket->subject,
                            'type' => Ticket::class,
                            'user_id' => $oldUser,
                        ]);
                    }
                }
            }

            activity()->performedOn($ticket)->causedBy(getLoggedInUser())
                ->useLog('Ticket updated.')->log($ticket->subject.' Ticket updated.');

            if (isset($input['tags']) && ! empty($tagsInput)) {
                $ticket->tags()->sync($input['tags']);
            }

            if (! empty($attachmentsInput)) {
                foreach ($attachmentsInput['attachments'] as $attachment) {
                    $ticket->addMedia($attachment)->toMediaCollection(Ticket::TICKET_ATTACHMENT_PATH,
                        config('app.media_disc'));
                }
            }
        } catch (Exception $e) {
            throw new UnprocessableEntityHttpException($e->getMessage());
        }
    }

    /**
     * @param $ticket
     * @return Builder[]|Collection
     */
    public function getNotesData($ticket)
    {
        return Note::with('user.media')->where('owner_id', '=', $ticket->id)
            ->where('owner_type', '=', Ticket::class)->orderByDesc('created_at')->get();
    }
}
