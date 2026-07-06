<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Models\User;
use Throwable;

class ActivityLogController extends AppBaseController
{
    /**
     * @param  Request  $request
     * @return Application|Factory|JsonResponse|View
     *
     * @throws Throwable
     */
    public function index(Request $request)
    {
        $activityLogs = ActivityLog::with('createdBy')->orderBy('created_at', 'DESC')->paginate(10);

        if ($request->ajax()) {
            $startDate = $request->get('startDate');
            $endDate = $request->get('endDate');
            $userId = $request->input('user_id');

            if (! empty($startDate) && ! empty($endDate)) {
                
                $query = ActivityLog::whereDate('created_at', '>=', $startDate)
                    ->whereDate('created_at', '<=', $endDate);
                    
                if ($userId > 0) {
                    $query->where('causer_type', 'App\Models\User');
                    $query->where('causer_id', $userId);
                }
                    
                $activityLogs = $query->orderByDesc('id')->get();
                
                

                $html = view('activity_logs.activity_log_lists', compact('activityLogs'))->render();

                return response()->json(['html' => $html]);
            }
        }

        if ($request->ajax()) {
            try {
                return $this->sendResponse($activityLogs, 'Activity log data retrieved successfully.');
            } catch (\Exception $e) {
                return $this->sendError($e, '404');
            }
        }
        
        $user_list = User::get(['id', 'first_name', 'last_name'])
        ->mapWithKeys(function ($user) {
            return [$user->id => $user->first_name . ' ' . $user->last_name];
        })->toArray();
        
         

        return view('activity_logs.index', compact(['activityLogs', 'user_list']));
    }
}
