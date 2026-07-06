<?php

namespace App\Repositories;

use App\Mail\ReminderMail;
use App\Models\Reminder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

/**
 * Class ReminderRepository
 *
 * @version April 15, 2020, 9:24 am UTC
 */
class ReminderRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'notified_date',
        'reminder_to',
        'description',
        'is_notified',
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
        return Reminder::class;
    }

    /**
     * @param  array  $input
     * @return bool
     */
    public function store($input)
    {
        $input['owner_type'] = Reminder::REMINDER_MODULES[$input['module_id']];
        $input['added_by'] = getLoggedInUserId();
        $input['is_notified'] = 1;
        $reminder = Reminder::create($input);

        activity()->performedOn($reminder)->causedBy(getLoggedInUser())
            ->useLog('New Reminder created.')->log(html_entity_decode($reminder->description).' Reminder created.');

        Mail::to($reminder->user->email)->send(new ReminderMail($reminder));

        return true;
    }

    /**
     * @param  array  $input
     * @param  Reminder  $reminder
     * @return bool
     */
    public function update($input, $reminder)
    {
        $reminder->update($input);

        activity()->performedOn($reminder)->causedBy(getLoggedInUser())
            ->useLog('Reminder updated.')->log(htmlspecialchars_decode($reminder->description).' Reminder updated.');

        return true;
    }

    public function sendReminderEmail()
    {
        /** @var Reminder $reminders */
        $reminders = Reminder::with('user')->where('status', Reminder::PENDING)
            ->where('is_notified', true)
            ->where('notified_date', '<=', Carbon::now()->toDateTimeString())
            ->get();

        foreach ($reminders as $reminder) {
            try {
                if (! empty($reminder->user->email)) {
                    Mail::to($reminder->user->email)->send(new ReminderMail($reminder));

                    $updateStatus = Reminder::whereId($reminder->id)->update(['status' => Reminder::COMPLETED, 'is_notified' => 1]);
                }
            } catch (\Exception $e) {
                throw new UnprocessableEntityHttpException($e->getMessage());
            }
        }
    }
}
