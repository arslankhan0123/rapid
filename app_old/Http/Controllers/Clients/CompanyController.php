<?php

namespace App\Http\Controllers\Clients;

use App\Http\Controllers\AppBaseController;
use App\Http\Requests\UpdateCustomerRequest;
use App\Models\Country;
use App\Models\Customer;
use App\Repositories\Clients\CustomerRepository;
use Exception;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Auth;
use Laracasts\Flash\Flash;

/**
 * Class CompanyController
 */
class CompanyController extends AppBaseController
{
    /**
     * @var CustomerRepository
     */
    private $customerRepository;

    public function __construct(CustomerRepository $customerRepository)
    {
        $this->customerRepository = $customerRepository;
    }

    /**
     * @return Factory|View
     */
    public function companyDetails()
    {
        $clientCustomerId = Auth::user()->contact->customer_id;
        $customer = Customer::find($clientCustomerId);
        $data['currencies'] = Customer::CURRENCIES;
        $data['default_language'] = Customer::LANGUAGES;
        $data['countries'] = Country::orderBy('name', 'asc')->pluck('name', 'id')->toArray();

        return view('clients.company.company_details', compact('customer', 'data'));
    }

    /**
     * @param  Customer  $customer
     * @param  UpdateCustomerRequest  $request
     * @return RedirectResponse|Redirector
     *
     * @throws Exception
     */
    public function update(Customer $customer, UpdateCustomerRequest $request)
    {
        $customer = $this->customerRepository->update($request->all(), $customer);

        Flash::success(__('messages.customer.customer_updated_successfully'));

        return redirect(route('clients.dashboard'));
    }
}
