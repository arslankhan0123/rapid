<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateCustomerRequest;
use App\Http\Requests\UpdateCustomerRequest;
use App\Models\Bank;
use App\Models\Branch;
use App\Models\Country;
use App\Models\Customer;
use App\Models\Task;
use App\Models\User;
use App\Repositories\CustomerRepository;
use Exception;
use File;
use Flash;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class CustomerController extends AppBaseController
{
    /** @var CustomerRepository */
    private $customerRepository;

    public function __construct(CustomerRepository $customerRepo)
    {
        $this->customerRepository = $customerRepo;
    }

    /**
     * Display a listing of the Customer.
     *
     * @return Factory|View
     */
    public function index()
    {
        return view('customers.index');
    }

    /**
     * Show the form for creating a new Customer.
     *
     * @return Factory|View
     */
    public function create()
    {

        $data = $this->customerRepository->getSyncList();

        $states = $this->customerRepository->getStates();
        $currencies = $this->customerRepository->getCurrencies();
        $banks = Bank::orderBy('name')->pluck('name', 'id')->toArray();

        return view('customers.create', compact('data', 'states', 'currencies', 'banks'));
    }

    /**
     * Store a newly created Customer in storage.
     *
     * @param  CreateCustomerRequest  $request
     * @return RedirectResponse|Redirector
     */
    public function store(CreateCustomerRequest $request)
    {

        $input = $request->all();

        $this->customerRepository->create($input);
        Flash::success(__('messages.customer.customer_saved_successfully'));
        return redirect(route('customers.index'));
    }
    /**
     * Display the specified Customer.
     *
     * @param  Customer  $customer
     * @return View
     *
     * @throws Exception
     */
    public function show(Customer $customer)
    {
        $customer->load(['customerPayment', 'customerPayment.payment', 'bank']);
        $groupName = request('group') ?? 'profile';
        $data['groupName'] = $groupName;
        $customer = $this->customerRepository->prepareCustomerData($customer);
        $customers = Customer::pluck('company_name', 'id')->toArray();
        $data['customer'] = $customer;
        $data['customers'] = $customers;
        $data['banks'] = Bank::orderBy('name')->pluck('name', 'id')->toArray();

        if ($groupName == 'profile') {
            [$data['billingAddress'], $data['shippingAddress']] = $this->customerRepository->prepareAddress($customer);
            $data['customerGroups'] = $customer->customerGroups()->pluck('name');
            $data['country'] = Country::orderBy('name', 'asc')->pluck('name', 'id')->toArray();
            $data['bankName'] = $customer->bank->name ?? null;
        } elseif (in_array($groupName, ['reminders', 'notes'])) {
            $records = $this->customerRepository->getReminderData($customer->id, Customer::class);
            $data['data'] = $records;
            if ($groupName == 'notes') {
                $notes = $this->customerRepository->getNoteData($customer);
                $data['notes'] = $notes;
            }
        } elseif ($groupName == 'tasks') {
            $data['status'] = Task::STATUS;
            $data['priorities'] = Task::PRIORITY;
        }

        $states = $this->customerRepository->getStates();
        return view("customers.views.$groupName", compact('states'))->with($data);
    }


    /**
     * Show the form for editing the specified Customer.
     *
     * @param  Customer  $customer
     * @return Factory|View
     */
    public function edit(Customer $customer)
    {
        $customer->load(['customerPayment']);
        // dd($customer->toArray());

        $data = $this->customerRepository->getSyncList();
        [$data['billingAddress'], $data['shippingAddress']] = $this->customerRepository->prepareAddress(
            $customer,
            true
        );
        $states = $this->customerRepository->getStates();
        $currencies = $this->customerRepository->getCurrencies();
        $banks = Bank::orderBy('name')->pluck('name', 'id')->toArray();
        return view('customers.edit', compact('customer', 'data', 'states', 'currencies', 'banks'));
    }

    /**
     * Update the specified Customer in storage.
     *
     * @param  Customer  $customer
     * @param  UpdateCustomerRequest  $request
     * @return RedirectResponse|Redirector
     *
     * @throws Exception
     */
    public function update(Customer $customer, UpdateCustomerRequest $request)
    {


        if (isset($request['inactive'])) {
            $request['inactive'] = 1;
        } else {
            $request['inactive'] = 0;
        }


        $this->customerRepository->update($request->all(), $customer);

        Flash::success(__('messages.customer.customer_updated_successfully'));

        return redirect(route('customers.index'));
    }

    /**
     * Remove the specified Customer from storage.
     *
     * @param  Customer  $customer
     * @return JsonResponse
     *
     * @throws Exception
     */
    public function destroy(Customer $customer)
    {
        try {
            DB::beginTransaction();
            $customer->address()->delete();


            $documents = DB::table('customer_docs')
                ->where('customer_id', $customer->id)
                ->get();

            // Delete related documents and files from public storage
            foreach ($documents as $document) {
                // Construct the full file path
                $filePath = public_path("uploads/public/customer_docs/" . $document->file);
                // Delete file from public folder if it exists
                if (File::exists($filePath)) {
                    File::delete($filePath);
                }
                // Delete document record from the database
                DB::table('customer_docs')
                    ->where('id', $document->id)
                    ->delete();
            }



            activity()->performedOn($customer)->causedBy(getLoggedInUser())
                ->useLog('Customer deleted.')->log($customer->company_name . ' Customer deleted.');

            if ($customer->contact()->exists()) {
                $customer->contact()->first()->user()->delete();
                $customer->contact()->delete();
            }

            $customer->invoice()->delete();
            $customer->creditNote()->delete();
            $customer->estimate()->delete();
            $customer->project()->delete();
            $customer->contract()->delete();
            $customer->proposal()->delete();

            $this->customerRepository->delete($customer->id);

            DB::commit();

            return $this->sendSuccess('Customer deleted successfully.');
        } catch (Exception $exception) {
            DB::rollBack();
            throw new UnprocessableEntityHttpException($exception->getMessage());
        }
    }

    /**
     * @param  Customer  $customer
     * @return mixed
     */
    public function getNotesCount(Customer $customer)
    {
        return $this->sendResponse($customer->notes()->count(), 'Notes count retrieved successfully.');
    }

    /**
     * @param  Request  $request
     * @return mixed
     */
    public function searchCustomer(Request $request)
    {
        $input = $request->all();
        $searchCustomer = $this->customerRepository->searchCustomerData($input['searchData']);

        return $this->sendResponse($searchCustomer, 'Customer search data successfully.');
    }

    /**
     * @param  Request  $request
     * @return mixed
     */
    public function addCustomerAddress(Request $request)
    {
        $input = $request->all();
        $this->customerRepository->addCustomerAddress($input);

        return $this->sendSuccess('success');
    }

    /**
     * @param  Request  $request
     * @return mixed
     */
    public function leadConvertToCustomer(Request $request)
    {
        $input = $request->all();
        $emailExists = User::whereEmail($input['email'])->exists();
        if ($emailExists) {
            return $this->sendError('Email id already exists');
        }

        $customer = $this->customerRepository->leadConvertToCustomer($input);
        return $this->sendSuccess('Lead convert to customer');
    }

    function file_delete($id)
    {

        $employee = $this->customerRepository->delete_file($id);
        activity()->performedOn($employee)->causedBy(getLoggedInUser())
            ->useLog('File Deleted')->log(' Customer File deleted.');
        return $this->sendSuccess(__('messages.employees.delete_file'));
    }
}
