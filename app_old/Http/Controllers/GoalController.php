<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateGoalRequest;
use App\Http\Requests\UpdateGoalRequest;
use App\Models\Goal;
use App\Repositories\GoalRepository;
use Exception;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Routing\Redirector;
use Illuminate\View\View;
use Laracasts\Flash\Flash;

class GoalController extends AppBaseController
{
    /** @var GoalRepository */
    private $goalRepository;

    public function __construct(GoalRepository $goalRepo)
    {
        $this->goalRepository = $goalRepo;
    }

    /**
     * Display a listing of the Goal.
     *
     * @return Factory|View
     */
    public function index()
    {
        $goalTypes = Goal::GOAL_TYPE;

        return view('goals.index', compact('goalTypes'));
    }

    /**
     * Show the form for creating a new Goal.
     *
     * @return Factory|View
     */
    public function create()
    {
        $members = $this->goalRepository->getStaffMember();
        $goalTypes = Goal::GOAL_TYPE;

        return view('goals.create', compact('members', 'goalTypes'));
    }

    /**
     * Store a newly created Goal in storage.
     *
     * @param  CreateGoalRequest  $request
     * @return RedirectResponse|Redirector
     */
    public function store(CreateGoalRequest $request)
    {
        $input = $request->all();
        $this->goalRepository->store($input);

        Flash::success(__('messages.goal.goal_saved_successfully'));

        return redirect(route('goals.index'));
    }

    /**
     * Display the specified Goal.
     *
     * @param  Goal  $goal
     * @return Factory|View
     */
    public function show(Goal $goal)
    {
        $goal = $this->goalRepository->getGoalDetails($goal->id);

        return view('goals.show', compact('goal'));
    }

    /**
     * Show the form for editing the specified Goal.
     *
     * @param  Goal  $goal
     * @return Factory|View
     */
    public function edit(Goal $goal)
    {
        $data = [];
        $data['goalMembers'] = $goal->goalmembers()->pluck('user_id')->toArray();
        $members = $this->goalRepository->getStaffMember();
        $goalTypes = Goal::GOAL_TYPE;

        return view('goals.edit', $data, compact('goal', 'members', 'goalTypes'));
    }

    /**
     * Update the specified Goal in storage.
     *
     * @param  UpdateGoalRequest  $request
     * @param  Goal  $goal
     * @return RedirectResponse|Redirector
     */
    public function update(UpdateGoalRequest $request, Goal $goal)
    {
        $input = $request->all();
        $this->goalRepository->updateGoal($goal->id, $input);

        Flash::success(__('messages.goal.goal_saved_successfully'));

        return redirect(route('goals.index'));
    }

    /**
     * Remove the specified Goal from storage.
     *
     * @param  Goal  $goal
     * @return Response
     *
     * @throws Exception
     */
    public function destroy(Goal $goal)
    {
        activity()->performedOn($goal)->causedBy(getLoggedInUser())
            ->useLog('Goal deleted.')->log($goal->subject.' Goal deleted.');

        $goal->delete();

        return $this->sendSuccess('Goal deleted successfully.');
    }
}
