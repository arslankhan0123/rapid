<?php

namespace App\Repositories;

use App\Models\Address;
use App\Models\Contact;
use App\Models\Country;
use App\Models\Customer;
use App\Models\CustomerDoc;
use App\Models\CustomerGroup;
use App\Models\CustomerToCustomerGroup;
use App\Models\Lead;
use App\Models\Note;
use App\Models\Permission;
use App\Models\Reminder;
use App\Models\Role;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\State;
use App\Models\Currency;
use App\Models\PaymentMode;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use App\Models\CustomerPayment;

/**
 * Class CustomerRepository
 *
 * @version April 3, 2020, 6:37 am UTC
 */
class CustomerRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'company_name',
        'vat_number',
        'phone',
        'website',
        'currency',
        'mobile',
        'email',
        'inactive',
        'location_url',
        'customer_logo',
        'fax',
        'address',
        'opening_balance',
        'customer_arabic_name'
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
        return Customer::class;
    }

    /**
     * @return array
     */
    public function getSyncList()
    {
        $data = [];
        $data['customerGroups'] = CustomerGroup::orderBy('name', 'asc')->pluck('name', 'id')->toArray();
        $data['currencies'] = Customer::CURRENCIES;
        $data['languages'] = Customer::LANGUAGES;
        $data['countries'] = Country::orderBy('name', 'asc')->pluck('name', 'id')->toArray();
        $data['paymentModes'] = PaymentMode::orderBy('name', 'asc')->whereActive(true)->pluck('name', 'id')->toArray();

        return $data;
    }
    public function getStates()
    {
        return State::get();
    }
    public function getCurrencies()
    {
        return Currency::pluck('name', 'id');
    }

    /**
     * @param  array  $input
     * @return bool|Model
     */
    public function create($input)
    {
        $addressInputArray = Arr::only($input, ['street', 'city', 'state', 'zip', 'country']);
        // $addressArray = Address::prepareInputForAddress($addressInputArray);
        $addressArray = $addressInputArray;
        $input['phone'] = preparePhoneNumber($input, 'phone');
        $input['phone'] = removeSpaceFromPhoneNumber($input['phone']);

        if (isset($input['customer_logo'])) {
            $image = $input['customer_logo'];
            $filename = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension(); // Generate a unique filename
            $image->storeAs('customer/', $filename); // Store the image

            // Add the image filename to the data array
            $input['customer_logo'] = $filename;
        }

        $customer = Customer::create(Arr::only(array_merge($input, $addressArray), (new Customer())->getFillable()));


        $this->uploadFiles($input, $customer->id);


        activity()->performedOn($customer)->causedBy(getLoggedInUser())
            ->useLog('New Customer created.')->log($customer->company_name . ' Customer created.');

        if (isset($input['groups']) && !empty(array_filter($input['groups']))) {
            foreach ($input['groups'] as $group) {
                CustomerToCustomerGroup::create([
                    'customer_id' => $customer->id,
                    'customer_group_id' => $group,
                ]);
            }
        }

        $isBillingAddressEmpty = Address::containsOnlyNull($addressArray);
        //  $isShippingAddressEmpty = Address::containsOnlyNull($addressArray[2]);

        if (!$isBillingAddressEmpty) {
            self::createAddress($customer->id, $addressArray, Address::ADDRESS_TYPES[1]);
        }

        // if (! $isShippingAddressEmpty) {
        //     self::createAddress($customer->id, $addressArray[2], Address::ADDRESS_TYPES[2]);
        // }

        // Check if there are payment modes to store
        if (isset($input['payment_modes']) && is_array($input['payment_modes'])) {
            $this->storePaymentModes($customer->id, $input['payment_modes']);
        }
        return true;
    }

    public function storePaymentModes($customerId, array $paymentModes)
    {
        // Detach existing payment modes if necessary
        CustomerPayment::where('customer_id', $customerId)->delete();

        // Loop through payment modes and store them
        foreach ($paymentModes as $paymentMode) {
            CustomerPayment::create([
                'customer_id' => $customerId,
                'payment_id' => $paymentMode, // Assuming payment mode ID is stored here
            ]);
        }
    }

    /**
     * @param  Customer  $customer
     * @param  bool  $isEdit
     * @return array
     */
    public function prepareAddress($customer, $isEdit = false)
    {
        $query = Address::with('addressCountry')->whereOwnerType(Customer::class)->whereOwnerId($customer->id)->get();
        $billingAddress = $query->where('type', '=', Address::ADDRESS_TYPES[1])->first();

        if ($billingAddress != null && !$isEdit) {
            $billingAddress->country = $billingAddress->country != null ? $billingAddress->addressCountry->name ?? '' : null;
        }

        $shippingAddress = $query->where('type', '=', Address::ADDRESS_TYPES[2])->first();

        if ($shippingAddress != null && !$isEdit) {
            $shippingAddress->country = $shippingAddress->country != null ? $shippingAddress->addressCountry->name : null;
        }

        return [$billingAddress, $shippingAddress];
    }

    /**
     * @param  Customer  $customer
     * @return Customer
     */
    public function prepareCustomerData($customer)
    {

        $customer->currency = $customer->currency != null ? Customer::CURRENCIES[$customer->currency] : null;
        $customer->default_language = $customer->default_language != null ? Customer::LANGUAGES[$customer->default_language] : null;
        $customer->country = $customer->country != null ? $customer->customerCountry->name ?? '' : null;

        return $customer;
    }

    /**
     * @param  array  $input
     * @param  Customer  $customer
     * @return bool
     *
     * @throws Exception
     */
    public function update($input, $customer)
    {

        $addressInputArray = Arr::only($input, ['street', 'city', 'state', 'zip', 'country']);
        $addressArray = $addressInputArray;
        // $addressArray = Address::prepareInputForAddress($addressInputArray);
        //        $input['phone'] = preparePhoneNumber($input, 'phone');
        $input['phone'] = removeSpaceFromPhoneNumber($input['phone']);

        if (isset($input['customer_logo']) && !empty($input['customer_logo'])) {
            // Check if the current customer_logo is not null and the file exists in the folder
            // dd(file_exists(public_path('uploads/customer/' . $customer->customer_logo)));
            if ($customer->customer_logo && file_exists(public_path('uploads/customer/' . $customer->customer_logo))) {
                // Delete the old image
                unlink(public_path('uploads/customer/' . $customer->customer_logo));
            }

            // Process the new image upload
            $image = $input['customer_logo'];
            $filename = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension(); // Generate a unique filename
            $image->storeAs('customer/', $filename); // Store the image

            // Add the image filename to the data array
            $input['customer_logo'] = $filename;
        } else {
            // If customer_logo is not being updated, keep the existing logo
            $input['customer_logo'] = $customer->customer_logo;
        }

        $customer->update(array_merge($input, $addressArray));



        $this->uploadFiles($input, $customer->id);

        activity()->performedOn($customer)->causedBy(getLoggedInUser())
            ->useLog('Customer updated.')->log($customer->company_name . ' Customer updated.');

        if (!empty($input['groups'])) {
            $customer->customerGroups()->sync($input['groups']);
        }

        $isBillingAddressEmpty = Address::containsOnlyNull($addressArray);
        // $isShippingAddressEmpty = Address::containsOnlyNull($addressArray[2]);

        if (!empty($customer->billingAddress())) {
            $billingAddress = $isBillingAddressEmpty ?
                $customer->billingAddress()->delete() : $customer->billingAddress()->update($addressArray);
        } else {
            if (!$isBillingAddressEmpty) {
                self::createAddress($customer->id, $addressArray, Address::ADDRESS_TYPES[1]);
            }
        }

        // if (! empty($customer->shippingAddress())) {
        //     $shippingAddress = $isShippingAddressEmpty ?
        //         $customer->shippingAddress()->delete() : $customer->shippingAddress()->update($addressArray[2]);
        // } else {
        //     if (! $isShippingAddressEmpty) {
        //         self::createAddress($customer->id, $addressArray[2], Address::ADDRESS_TYPES[2]);
        //     }
        // }
        if (isset($input['payment_modes']) && is_array($input['payment_modes'])) {
            $this->storePaymentModes($customer->id, $input['payment_modes']);
        }
        return true;
    }

    /**
     * @param  int  $customerId
     * @param  array  $address
     * @param $addressType
     * @return bool
     */
    public function createAddress($customerId, $address, $addressType)
    {
        $ownerId = $customerId;
        $ownerType = Customer::class;
        Address::create(array_merge(
            $address,
            ['owner_id' => $ownerId, 'owner_type' => $ownerType, 'type' => $addressType]
        ));

        return true;
    }

    /**
     * @return int
     */
    public function customerCount()
    {
        return Customer::selectRaw('count(*) as total_customers')->first();
    }

    /**
     * @param  int  $id
     * @param  string  $class
     * @return array
     */
    public function getReminderData($id, $class)
    {
        $data = [];
        $data['reminderTo'] = Contact::whereCustomerId($id)->with('user')->get()->where(
            'user.is_enable',
            '=',
            true
        )->pluck('user.full_name', 'user.id')->toArray();
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
     * @param $customer
     * @return mixed
     */
    public function getNoteData($customer)
    {
        return Note::with('user.media')->where('owner_id', '=', $customer->id)
            ->where('owner_type', '=', Customer::class)->orderByDesc('created_at')->get();
    }

    /**
     * @param $searchData
     * @return mixed
     */
    public function searchCustomerData($searchData)
    {
        return Customer::where('company_name', 'LIKE', '%' . $searchData . '%')->get();
    }

    /**
     * @param $input
     */
    public function addCustomerAddress($input)
    {
        $addressInputArray = Arr::only($input, ['street', 'city', 'state', 'zip', 'country']);
        $addressArray = Address::prepareInputForAddress($addressInputArray);
        $isBillingAddressEmpty = Address::containsOnlyNull($addressArray[1]);
        $isShippingAddressEmpty = Address::containsOnlyNull($addressArray[2]);
        $ownerId = $input['customer_id'];

        if (!$isBillingAddressEmpty) {
            $this->createAddress($ownerId, $addressArray[1], Address::ADDRESS_TYPES[1]);
        }

        if (!$isShippingAddressEmpty) {
            $this->createAddress($ownerId, $addressArray[2], Address::ADDRESS_TYPES[2]);
        }

        return true;
    }

    /**
     * @param $input
     * @return bool
     */
    public function leadConvertToCustomer($input)
    {
        $leadConvertCustomerUpdate = Lead::whereId($input['lead_id'])->update([
            'lead_convert_customer' => true,
            'lead_convert_date' => Carbon::now()->format('Y-m-d'),
        ]);
        $customer = Customer::create($input);
        $input['password'] = Hash::make($input['password']);

        if (isset($input['groups']) && !empty(array_filter($input['groups']))) {
            foreach ($input['groups'] as $group) {
                CustomerToCustomerGroup::create([
                    'customer_id' => $customer->id,
                    'customer_group_id' => $group,
                ]);
            }
        }

        /* @var  User $user */
        $user = User::create([
            'first_name' => $input['company_name'],
            'email' => $input['email'],
            'password' => $input['password'],
        ]);

        $roles = Role::whereName('client')->first()->id;
        $user->roles()->sync($roles);

        /** @var Contact $contact */
        $contact = Contact::create([
            'customer_id' => $customer->id,
            'user_id' => $user->id,
        ]);

        Contact::whereCustomerId($customer->id)->update(['primary_contact' => true]);

        $permissions = Permission::whereType('Contacts')->get();
        $user->permissions()->sync($permissions);

        $user->update(['owner_id' => $contact->id, 'owner_type' => Contact::class]);
        $contact->update(['user_id' => $user->id]);

        return true;
    }



    private function uploadFiles($input, $employeeId)
    {
        // Check if the input contains files
        if (isset($input['file']) && is_array($input['file'])) {
            foreach ($input['file'] as $index => $file) {
                // Check if a file has been uploaded
                if (is_uploaded_file($file->getPathname())) {
                    $name = $input['doc_name'][$index] ?? ''; // Default to 'unknown' if no name is provided
                    $expiryDate = $input['expiry_date'][$index] ?? null; // Default to null if no expiry date is provided
                    $this->uploadAndStoreFile($file, $name, $employeeId, $expiryDate);
                }
            }
        }
    }
    private function uploadAndStoreFile($file, $name, $employeeId, $expiryDate)
    {
        // Generate a unique filename
        $filename = time() . '_' . $file->getClientOriginalName();

        // Store the file in the 'public/employee_docs' directory
        $file->storeAs('public/customer_docs', $filename);

        // Store only the filename in the database
        DB::table('customer_docs')->insert([
            'name' => $name,
            'customer_id' => $employeeId,
            'file' => $filename, // Save only the filename
            'expiry_date' => $expiryDate,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }




    public function delete_file($id)
    {
        // Find the document by ID using Eloquent
        $document = CustomerDoc::find($id);

        // Check if the document exists
        if (!$document) {
            return false; // Document not found
        }

        // Get the file path
        $filePath = public_path('uploads/public/customer_docs/' . basename($document->file));

        DB::beginTransaction();
        try {
            // Delete the file from the folder if it exists
            if (file_exists($filePath)) {
                unlink($filePath);
            }

            // Delete the document record from the database
            $document->delete();

            DB::commit();
            return $document; // Return the deleted model instance
        } catch (Exception $e) {
            DB::rollBack();
            return false; // Deletion failed
        }
    }

    public function getCustomersList()
    {
        return Customer::pluck('company_name', 'id')->toArray();
    }
}
