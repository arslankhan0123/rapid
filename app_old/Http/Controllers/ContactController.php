<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateContactRequest;
use App\Http\Requests\UpdateContactRequest;
use App\Models\Contact;
use App\Models\Customer;
use App\Models\Permission;
use App\Models\User;
use App\Repositories\ContactRepository;
use Exception;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Laracasts\Flash\Flash;
use Throwable;

class ContactController extends AppBaseController
{
    /** @var ContactRepository */
    private $contactRepository;

    public function __construct(ContactRepository $contactsRepo)
    {
        $this->contactRepository = $contactsRepo;
    }

    /**
     * Display a listing of the Contacts.
     *
     * @return Factory|View
     */
    public function index(): Factory|View
    {
        abort(404);

        return view('contacts.index');
    }

    /**
     * Show the form for creating a new Contacts.
     *
     * @param  null  $customerId
     * @return Factory|View
     */
    public function create($customerId = null): Factory|View
    {
        $customers = Customer::orderBy('company_name', 'asc')->pluck('company_name', 'id')->toArray();
        $permissions = Permission::whereType(Contact::TYPE)->pluck('name', 'id')->toArray();

        return view('contacts.create', compact('customers', 'permissions', 'customerId'));
    }

    /**
     * Store a newly created Contacts in storage.
     *
     * @param  CreateContactRequest  $request
     * @return RedirectResponse|Redirector
     *
     * @throws Throwable
     */
    public function store(CreateContactRequest $request): Redirector|RedirectResponse
    {
        $input = $request->all();

        $this->contactRepository->store($input);

        Flash::success(__('messages.contact.contact_saved_successfully'));

        return redirect($request->get('url'));
    }

    /**
     * Display the specified Contacts.
     *
     * @param  Contact  $contact
     * @return Factory|View
     */
    public function show(Contact $contact): Factory|View
    {
        $permissions = Permission::whereType(Contact::TYPE)->pluck('name', 'id')->toArray();
        $contactPermissions = $contact->user->permissions->pluck('id')->toArray();

        return view('contacts.show',
            compact('contact', 'permissions', 'contactPermissions'));
    }

    /**
     * Show the form for editing the specified Contacts.
     *
     * @param  Contact  $contact
     * @return Factory|View
     */
    public function edit(Contact $contact): Factory|View
    {
        $customers = Customer::all()->pluck('company_name', 'id')->toArray();
        $permissions = Permission::whereType(Contact::TYPE)->pluck('name', 'id')->toArray();
        $contactPermissions = $contact->user->permissions->pluck('id')->toArray();

        return view('contacts.edit', compact('contact', 'contactPermissions', 'customers', 'permissions'));
    }

    /**
     * Update the specified Contacts in storage.
     *
     * @param  Contact  $contact
     * @param  UpdateContactRequest  $request
     * @return RedirectResponse|Redirector
     *
     * @throws Throwable
     */
    public function update(Contact $contact, UpdateContactRequest $request): Redirector|RedirectResponse
    {
        $this->contactRepository->update($request->all(), $contact);

        Flash::success(__('messages.contact.contact_updated_successfully'));

        return redirect($request->get('url'));
    }

    /**
     * Remove the specified Contacts from storage.
     *
     * @param  Contact  $contact
     * @return JsonResponse
     *
     * @throws Exception
     */
    public function destroy(Contact $contact): JsonResponse
    {
        if ($contact->email == getLoggedInUser()->email) {
            return $this->sendError('Login contact can\'t deleted.');
        }

        $contact->user()->delete();
        $contact->projectContacts()->detach();

        $contact->delete();

        return $this->sendSuccess('Contact deleted successfully');
    }

    /**
     * @param  int  $id
     * @return JsonResponse
     */
    public function activeDeActiveContact($id): JsonResponse
    {
        $contact = User::find($id);
        $contact->update(['is_enable' => ! $contact->is_enable]);

        return $this->sendSuccess(__('messages.contact.contact_status_updated_successfully'));
    }

    /**
     * @param $userId
     * @return RedirectResponse
     */
    public function impersonate($userId): RedirectResponse
    {
        if ((getLoggedInUser()->hasRole('client')) || session('impersonated_by') == 1) {
            return redirect()->back();
        }

        $user = User::find($userId);
        getLoggedInUser()->impersonate($user);

        return redirect()->route('clients.dashboard');
    }

    /**
     * @return RedirectResponse
     */
    public function impersonateLeave(): RedirectResponse
    {
        getLoggedInUser()->leaveImpersonation();

        return redirect()->route('dashboard');
    }
}
