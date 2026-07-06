<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateLeadRequest;
use App\Http\Requests\UpdateLeadRequest;
use App\Models\Contact;
use App\Models\Country;
use App\Models\CustomerGroup;
use App\Models\Lead;
use App\Models\LeadSource;
use App\Models\LeadStatus;
use App\Models\Proposal;
use App\Models\Task;
use App\Repositories\LeadRepository;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Exception;
use Flash;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Routing\Redirector;
use Illuminate\View\View;

class LeadController extends AppBaseController
{
    /** @var LeadRepository */
    private $leadRepository;

    public function __construct(LeadRepository $leadRepo)
    {
        $this->leadRepository = $leadRepo;
    }

    /**
     * Display a listing of the Lead.
     *
     * @return Factory|View
     */
    public function index()
    {
        $statusArr = LeadStatus::orderBy('name', 'asc')->pluck('name', 'id')->toArray();
        $leadSourceArr = LeadSource::orderBy('name', 'asc')->pluck('name', 'id')->toArray();

        return view('leads.index', compact('statusArr', 'leadSourceArr'));
    }

    /**
     * Show the form for creating a new Lead.
     *
     * @param  null  $customerId
     * @return Factory|View
     */
    public function create($customerId = null)
    {
        $data = $this->leadRepository->getData();
        $countries=$this->leadRepository->getCountries();
        $states=$this->leadRepository->getStates();
        $cities=$this->leadRepository->getCities();
        $areas=$this->leadRepository->getAreas();

        return view('leads.create', compact(['data', 'customerId', 'countries','states','cities','areas']));
    }

    /**
     * Store a newly created Lead in storage.
     *
     * @param  CreateLeadRequest  $request
     * @return RedirectResponse|Redirector
     */
    public function store(CreateLeadRequest $request)
    {

        $input = $request->all();
        $lead = $this->leadRepository->create_lead($input);
        Flash::success(__('messages.lead.lead_saved_successfully'));
        activity()->causedBy(getLoggedInUser())
            ->performedOn($lead)
            ->useLog('Lead created.')
            ->log($lead->name . ' Lead Created');
        return $this->sendResponse($lead, __('messages.lead.lead_saved_successfully'));
    }

    /**
     * Display the specified Lead.
     *
     * @param  Lead  $lead
     * @return Factory|View
     */

     public function view(Lead $lead){

        $lead->load(['leadStatus','leadSource', 'productGroup','product','assignedTo', 'leadCountry', 'state', 'city', 'area']);
        return view('leads.view', compact('lead'));
     }
    public function show(Lead $lead)
    {
        $groupName = (request('group') == null) ? 'lead_details' : (request('group'));

        $data = $this->leadRepository->getReminderData($lead->id, Lead::class);
        $status = Task::STATUS;
        $priorities = Task::PRIORITY;
        $notes = $this->leadRepository->getNoteData($lead);
        $data['languages'] = Lead::LANGUAGES;
        $data['countries'] = Country::orderBy('name', 'asc')->pluck('name', 'id')->toArray();
        $data['customerGroups'] = CustomerGroup::all()->pluck('name', 'id')->toArray();
        $statusArr = Proposal::STATUS;

        return view(
            "leads.views.$groupName",
            compact('lead', 'data', 'status', 'priorities', 'notes', 'groupName', 'statusArr')
        );
    }

    /**
     * Show the form for editing the specified Lead.
     *
     * @param  Lead  $lead
     * @return Factory|View
     */
    public function edit(Lead $lead)
    {
        $data = $this->leadRepository->getData();
        $countries = $this->leadRepository->getCountries();
        $states = $this->leadRepository->getStates();
        $cities = $this->leadRepository->getCities();
        $areas = $this->leadRepository->getAreas();
        return view('leads.edit', compact('lead', 'data','countries','states','cities','areas'));
    }

    /**
     * Update the specified Lead in storage.
     *
     * @param  Lead  $lead
     * @param  UpdateLeadRequest  $request
     * @return RedirectResponse|Redirector
     *
     * @throws Exception
     */
    public function update(Lead $lead, UpdateLeadRequest $request)
    {
        $input = $request->all();
        $updated_lead = $this->leadRepository->update_lead($lead, $input);
        Flash::success(__('messages.lead.lead_saved_successfully'));
        activity()->causedBy(getLoggedInUser())
            ->performedOn($lead)
            ->useLog('Lead created.')
            ->log($updated_lead->name . ' Lead Created');
        return $this->sendResponse($lead, __('messages.lead.lead_saved_successfully'));
    }

    /**
     * Remove the specified Lead from storage.
     *
     * @param  Lead  $lead
     * @return JsonResource
     *
     * @throws Exception
     */
    public function destroy(Lead $lead)
    {
        $proposalLeadId = Proposal::where('owner_id', '=', $lead->id)->where(
            'owner_type',
            '=',
            Lead::class
        )->exists();

        if ($proposalLeadId) {
            return $this->sendError('Lead can\'t be deleted.');
        }

        activity()->performedOn($lead)->causedBy(getLoggedInUser())
            ->useLog('Lead deleted.')->log($lead->name . ' Lead deleted.');

        $this->leadRepository->delete($lead->id);

        return $this->sendSuccess('Lead deleted successfully.');
    }

    /**
     * @param  Lead  $lead
     * @param  int  $status
     * @return JsonResponse
     */
    public function changeStatus(Lead $lead, $status)
    {
        $lead->update(['status_id' => $status]);

        return $this->sendSuccess(__('messages.lead.lead_status_updated_successfully'));
    }

    /**
     * @return Factory|View
     */
    public function kanbanList()
    {
        /** @var Lead[] $leads */
        $leads = Lead::with(['assignedTo.media', 'leadSource'])->get()->groupBy('status_id');

        $leadStatus = LeadStatus::orderBy('order', 'asc')->get();

        return view('leads.kanban.index', compact('leads', 'leadStatus'));
    }

    /**
     * @param  Request  $request
     * @return mixed
     */
    public function contactAsPerCustomer(Request $request)
    {
        /** @var Contact $contact */
        $contact = Contact::with('user')->whereCustomerId($request->get('customer_id'))->get();
        $contacts = $contact->where('user.is_enable', '=', true)->pluck('user.full_name', 'id')->toArray();

        return $this->sendResponse($contacts, 'member retrieved data success.');
    }

    /**
     * @param  Lead  $lead
     * @return mixed
     */
    public function getNotesCount(Lead $lead)
    {
        return $this->sendResponse($lead->notes()->count(), 'Notes count retrieved successfully.');
    }

    /**
     * @return Application|Factory|View
     */
    public function leadConvertChart()
    {
        $carbonPeriod = CarbonPeriod::dates(now()->startOfMonth(), now()->endOfMonth());
        $allLeads = Lead::where('lead_convert_customer', 1)->whereMonth(
            'lead_convert_date',
            Carbon::now()->month
        )->get();
        $leads = [];
        $currentMonthDates = [];

        foreach ($carbonPeriod as $date) {
            $currentMonthDates[] = $date->translatedFormat('jS M, Y');
            $leads[] = $allLeads->where('lead_convert_date', $date->format('Y-m-d'))->count();
        }

        return view('leads.lead_convert_customer_chart', compact('leads', 'currentMonthDates'));
    }
}
