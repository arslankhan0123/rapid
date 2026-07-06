<?php

namespace App\Http\Controllers;

use App\Queries\TermDataTable;
use Illuminate\Http\Request; 
use App\Repositories\TermRepository;
use Yajra\DataTables\DataTables;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Exception;
use App\Http\Requests\TermsRequest;
use App\Http\Requests\UpdateTermRequest;
use Laracasts\Flash\Flash;
use Throwable;
use App\Models\Term;
use Illuminate\Database\QueryException;

class TermController extends AppBaseController
{
    /**
     * @var TermRepository
     */
    private $termRepository;
    public function __construct(TermRepository $termRepo)
    {
        $this->termRepository = $termRepo;
    }
    /**
     * @param  Request  $request
     * @return Application|Factory|View
     *
     * @throws Exception
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            return DataTables::of((new TermDataTable())->get($request->only(['group'])))->make(true);
        }
        return view('terms.index');
    }

    public function create()
    {
        return view('terms.create');
    }

    public function store(TermsRequest $request)
    {

        $input = $request->all();
        try {
            $terms = $this->termRepository->create($input);
            activity()->causedBy(getLoggedInUser())
                ->performedOn($terms)
                ->useLog('Terms & Conditions created.')
                ->log($terms->terms . ' Terms & Condition Created.');
            Flash::success(__('messages.terms.saved'));
            return $this->sendResponse($terms, __('messages.terms.saved'));
        } catch (Throwable $e) {
            throw $e;
        }
    }
    public function destroy(Term $term)
    {

        try {
            $term->delete();
            activity()->performedOn($term)->causedBy(getLoggedInUser())
                ->useLog('Terms deleted.')->log($term->terms . '  Terms & Conditions deleted.');
            return $this->sendSuccess(__('messages.terms.delete'));
        } catch (QueryException $e) {
            return $this->sendError('Failed To delete!! Already in use.');
        }
    }

    public function edit(Term $term)
    {
        return view('terms.edit', compact(['term']));
    }
    public function update(Term $term, UpdateTermRequest $updateTermRequest)
    {

        $input = $updateTermRequest->all();
        $updateRetirement = $this->termRepository->update($input, $updateTermRequest->id);
        activity()->performedOn(model: $updateRetirement)->causedBy(getLoggedInUser())
            ->useLog('Terms & Conditions')->log($updateRetirement->name . ' Terms & Conditions updated.');
        Flash::success(__('messages.terms.saved'));
        return $this->sendSuccess(__('messages.terms.saved'));
    }

    public function view(Term $term)
    {
        return view('terms.view', compact(['term']));
    }
}
