<?php

namespace App\Repositories;

use App\Models\Comment;
use App\Models\Customer;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\ExpenseDoc;
use App\Models\Note;
use App\Models\PaymentMode;
use App\Models\Reminder;
use App\Models\TaxRate;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use App\Models\Currency;
use App\Models\Employee;
use App\Models\ExpenseSubCategory;
use App\Models\Account;
use App\Models\Supplier;

/**
 * Class ExpenseRepository
 *
 * @version April 20, 2020, 5:16 am UTC
 */
class ExpenseRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'name',
        'note',
        'expense_number',
        'pur_inv_number',
        'supp_vat_number',
        'employee_id',
        'employee_name',
        'expense_for',
        'sub_category_id',
        'isTaxable',
        'supplier_id'
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
        return Expense::class;
    }

    /**
     * @return array
     */
    public function getSyncList()
    {
        $data = [];
        $data['expenseCategories'] = ExpenseCategory::orderBy('name', 'asc')->pluck('name', 'id')->toArray();
        $data['customers'] = Employee::pluck('name', 'id');
        $data['taxRates'] = TaxRate::orderBy('tax_rate', 'asc')->pluck('tax_rate', 'id')->toArray();
        $data['paymentModes'] = PaymentMode::orderBy('id', 'desc')->whereActive(true)->pluck('name', 'id')->toArray();
        $data['currencies'] = Customer::CURRENCIES;

        return $data;
    }
    public function getCurrencies()
    {
        return Currency::pluck('name', 'id');
    }

    /**
     * @param  array  $input
     * @return bool
     */
    public function create($input)
    {

        try {
            $input['amount'] = formatNumber($input['amount']);
            $input['tax_applied'] = isset($input['tax_applied']) ? 1 : 0;
            $input['billable'] = isset($input['billable']) ? 1 : 0;
            $input['tax_1_id'] = !empty($input['tax_1_id']) ? $input['tax_1_id'] : null;
            $input['tax_2_id'] = !empty($input['tax_2_id']) ? $input['tax_2_id'] : null;
            $input['tax_rate'] = !empty($input['tax_rate']) ? $input['tax_rate'] : null;

            /** @var Expense $expense */
            $expense = Expense::create($input);


            $this->uploadFiles($input, $expense->id);


            //updateing accounts

            $totalAmount = formatNumber($input['amount']);
            $branchId = $input['branch_id'];
            $account = Account::where('id', $input['payment_mode_id'])
                ->where('branch_id', $branchId)
                ->first();
            if ($account) {
                $account->opening_balance -= $totalAmount;
                $account->save();
            }
            ///


            activity()->performedOn($expense)->causedBy(getLoggedInUser())
                ->useLog('New Expense created.')->log($expense->name . ' Expense created.');

            if (isset($input['receipt_attachment'])) {
                $expense->addMedia($input['receipt_attachment'])->toMediaCollection(
                    Expense::EXPENSE_RECEIPT,
                    config('app.media_disc')
                );
            }

            return true;
        } catch (\Exception $e) {
            throw new UnprocessableEntityHttpException($e->getMessage());
        }
    }

    /**
     * @param  array  $input
     * @param  Expense  $expense
     * @return bool
     */
    public function update($input, $expense)
    {

        try {

            $oldAmount = $expense->amount;
            $newAmount = formatNumber($input['amount']);
            $branchId = $input['branch_id'];


            $input['amount'] = formatNumber($input['amount']);
            $input['tax_applied'] = isset($input['tax_applied']) ? 1 : 0;
            $input['billable'] = isset($input['billable']) ? 1 : 0;
            $input['tax_1_id'] = !empty($input['tax_1_id']) ? $input['tax_1_id'] : null;
            $input['tax_2_id'] = !empty($input['tax_2_id']) ? $input['tax_2_id'] : null;
            $input['tax_rate'] = !empty($input['tax_rate']) ? $input['tax_rate'] : null;
            $expense->update($input);

            $this->uploadFiles($input, $expense->id);



            $account = Account::where('id', $input['payment_mode_id'])
                ->where('branch_id', $branchId)
                ->first();

            if ($account) {
                $account->opening_balance = ($account->opening_balance + $oldAmount) - $newAmount;
                $account->save();
            }


            activity()->performedOn($expense)->causedBy(getLoggedInUser())
                ->useLog('Expense updated.')->log($expense->name . ' Expense updated.');

            if (isset($input['receipt_attachment'])) {
                $expense->clearMediaCollection(Expense::EXPENSE_RECEIPT);
                $expense->addMedia($input['receipt_attachment'])->toMediaCollection(
                    Expense::EXPENSE_RECEIPT,
                    config('app.media_disc')
                );
            }

            return true;
        } catch (\Exception $e) {
            throw new UnprocessableEntityHttpException($e->getMessage());
        }
    }

    /**
     * @param  int  $id
     * @param  string  $class
     * @return array
     */
    public function getReminderData($id, $class)
    {
        $data = [];
        $data['reminderTo'] = User::whereIsEnable(true)->user()->get()->pluck('full_name', 'id')->toArray();
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
     * @param $expense
     * @return Builder[]|Collection
     */
    public function getCommentData($expense)
    {
        return Comment::with('user.media')->where('owner_id', '=', $expense->id)
            ->where('owner_type', '=', Expense::class)->orderByDesc('created_at')->get();
    }

    /**
     * @param $expense
     * @return Builder[]|Collection
     */
    public function getNotesData($expense)
    {
        return Note::with('user.media')->where('owner_id', '=', $expense->id)
            ->where('owner_type', '=', Expense::class)->orderByDesc('created_at')->get();
    }
    public function getEmployees()
    {
        return Employee::whereHas('branch', function ($branchQuery) {
            $branchQuery->whereIn('id', function ($subQuery) {
                $subQuery->select('branch_id')
                    ->from('users_branches')
                    ->where('user_id', auth()->id());
            });
        })->get();
    }

    public function getSubCategories()
    {
        return ExpenseSubCategory::get();
    }
    public function getAccounts()
    {
        return Account::get();
    }

    public function getSuppliers()
    {
        return Supplier::select('company_name', 'vat_number', 'id')->get();
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
        $file->storeAs('public/expense_docs', $filename);

        // Store only the filename in the database
        DB::table('expense_docs')->insert([
            'name' => $name,
            'expense_id' => $employeeId,
            'file' => $filename, // Save only the filename
            'expiry_date' => $expiryDate,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }


    public function delete_file($id)
    {
        // Find the document by ID using Eloquent
        $document = ExpenseDoc::find($id);

        // Check if the document exists
        if (!$document) {
            return false; // Document not found
        }

        // Get the file path
        $filePath = public_path('uploads/public/expense_docs/' . basename($document->file));

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
        } catch (\Exception $e) {
            DB::rollBack();
            return false; // Deletion failed
        }
    }
}
