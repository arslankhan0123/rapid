<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Carbon\Carbon;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class NotificationController extends AppBaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|JsonResponse|View
     */
    public function index()
    {
        $notifications = Notification::whereUserId(Auth::id())->where('read_at',
            null)->orderByDesc('created_at')->get();

        return $this->sendResponse($notifications, 'Notification retrieved successfully');
    }

    /**
     * @param  Notification  $notification
     * @return JsonResponse
     */
    public function readNotification(Notification $notification)
    {
        $notification->read_at = Carbon::now();
        $notification->save();

        return $this->sendSuccess(__('messages.notification.notification_read_successfully'));
    }

    /**
     * @return JsonResponse
     */
    public function readAllNotification()
    {
        Notification::whereReadAt(null)->where('user_id',
            getLoggedInUserId())->update(['read_at' => Carbon::now()]);

        return $this->sendSuccess(__('messages.notification.all_notification_read_successfully'));
    }
}
