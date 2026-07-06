<?php

namespace App\Http\Controllers;

use App\Queries\ExpenseSubCategoryDataTable;
use Illuminate\Http\Request;
use App\Repositories\ExpenseSubCategoryRepository;
use Yajra\DataTables\DataTables;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Exception;
use App\Http\Requests\ExpenseSubCategoryRequest;
use App\Http\Requests\UpdateSubExpenseCategoryRequest;
use Illuminate\Database\QueryException;
use Laracasts\Flash\Flash;
use Throwable;
use App\Models\ExpenseSubCategory;

class ExpenseSubCategoryController extends AppBaseController
{
    /**
     * @var ExpenseSubCategoryRepository
     */
    private $expenseSubCategoryRepository;
    public function __construct(ExpenseSubCategoryRepository $expenseSubCategoryRepo)
    {
        $this->expenseSubCategoryRepository = $expenseSubCategoryRepo;
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
            return DataTables::of((new ExpenseSubCategoryDataTable())->get($request->all()))->make(true);

        }
        $expenseCategories = $this->expenseSubCategoryRepository->getExpenseCategory();// Retrieves departments as key-value pairs

        return view('expense_sub_categories.index',compact('expenseCategories'));
    }

    public function create()
    {
        $expenseCategories = $this->expenseSubCategoryRepository->getExpenseCategory();// Retrieves departments as key-value pairs
        return view('expense_sub_categories.create', compact('expenseCategories'));
    }

    public function store(ExpenseSubCategoryRequest $request)
    {

        $input = $request->all();
        try {
            $designation = $this->expenseSubCategoryRepository->create($input);
            activity()->causedBy(getLoggedInUser())
                ->performedOn($designation)
                ->useLog('Expense Sub Category created.')
                ->log($designation->name . ' Expense Sub Category.');
            Flash::success(__('messages.expense_sub_categories.saved'));
            return $this->sendResponse($designation, __('messages.expense_sub_categories.saved'));
        } catch (Throwable $e) {
            throw $e;
        }
    }
    public function destroy(ExpenseSubCategory $category)
    {

        try {
            $category->delete();
            activity()->performedOn($category)->causedBy(getLoggedInUser())
                ->useLog('Expense Sub Category deleted.')->log($category->name . 'Expense Sub Category deleted.');
            return $this->sendSuccess(__('messages.department.sub_departments'));
        } catch (QueryException $e) {
            return $this->sendError('Failed To delete!! Already in use.');
        }
    }
    public function view(ExpenseSubCategory $category)
    {
        $category->load('expenseCategory');
        return view('expense_sub_categories.view', compact(['category']));
    }

    public function edit(ExpenseSubCategory $category)
    {
        $expenseCategories = $this->expenseSubCategoryRepository->getExpenseCategory();
        return view('expense_sub_categories.edit', compact(['category', 'expenseCategories']));
    }
    public function update(ExpenseSubCategory $category, UpdateSubExpenseCategoryRequest $updateSubExpenseCategoryRequest)
    {
        $input = $updateSubExpenseCategoryRequest->all();
        $subDepartment = $this->expenseSubCategoryRepository->update($input, $updateSubExpenseCategoryRequest->id);
        activity()->performedOn($subDepartment)->causedBy(getLoggedInUser())
            ->useLog('Expense Category Updated')->log($subDepartment->name . ' Expense Category updated.');
        Flash::success(__('messages.expense_sub_categories.saved'));
        return $this->sendSuccess(__('messages.expense_sub_categories.saved'));
    }
}
