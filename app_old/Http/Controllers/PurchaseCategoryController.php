<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateProductRequest;
use App\Http\Requests\PurhcaseCategoryRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Requests\UpdateProductUnitRequest;
use App\Models\Product;
use App\Models\ProductUnit;
use App\Queries\PurchaseCategoryDataTable;
use App\Queries\ProductDataTable;
use App\Queries\ProductUnitDataTable;
use App\Repositories\PurchaseCategoryRepository;
use App\Repositories\ProductRepository;
use App\Repositories\ProductUnitRepository;
use Illuminate\Database\QueryException;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Models\AssetCategory;
use App\Http\Requests\UpdatePurchaseCategoryRequest;
use Laracasts\Flash\Flash;
use Throwable;
use App\Models\PurchaseCategory;

class PurchaseCategoryController extends AppBaseController
{
    /**
     * @var PurchaseCategoryRepository
     */
    private $purchaseCategoryRepository;
    public function __construct(PurchaseCategoryRepository $purchaseCategoryRepo)
    {
        $this->purchaseCategoryRepository = $purchaseCategoryRepo;
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
            return DataTables::of((new PurchaseCategoryDataTable())->get($request->all()))->make(true);
        }
        return view('purchase_categories.index');
    }

    public function create()
    {
        $groups = $this->purchaseCategoryRepository->getGroups();
        return view('purchase_categories.create', compact(['groups']));
    }

    public function store(PurhcaseCategoryRequest $request)
    {

        $input = $request->all();

        try {
            $assetCategory = $this->purchaseCategoryRepository->create($input);
            activity()->causedBy(getLoggedInUser())
                ->performedOn($assetCategory)
                ->useLog('Purchase Category created.')
                ->log($assetCategory->name . ' Purchase Category.');
            Flash::success(__('messages.purchase-categories.saved'));
            return $this->sendResponse($assetCategory, __('messages.purchase-categories.saved'));
        } catch (Throwable $e) {
            throw $e;
        }
    }
    public function destroy(PurchaseCategory $category)
    {
        try {
            $category->delete();
            activity()->performedOn($category)->causedBy(getLoggedInUser())
                ->useLog('Purchase Category deleted.')->log($category->name . 'Purchase Category  deleted.');
            return $this->sendSuccess('Purchase Category deleted successfully.');
        } catch (QueryException $e) {
            return $this->sendError('Failed To delete!! Already in use.');
        }
    }

    public function edit(PurchaseCategory $category)
    {
        $groups = $this->purchaseCategoryRepository->getGroups();
        return view('purchase_categories.edit', ['category' => $category, 'groups'=>$groups]);
    }
    public function update(PurchaseCategory $category, UpdatePurchaseCategoryRequest $updatePurchaseCategoryRequest)
    {

        $input = $updatePurchaseCategoryRequest->all();
        $assetCategory = $this->purchaseCategoryRepository->update($input, $updatePurchaseCategoryRequest->id);
        activity()->performedOn($assetCategory)->causedBy(getLoggedInUser())
            ->useLog('Purchase Category Updated')->log($assetCategory->name . 'Purchase Category updated.');
        Flash::success(__('messages.purchase-categories.saved'));
        return $this->sendSuccess(__('messages.purchase-categories.saved'));
    }
    public function view(PurchaseCategory $category)
    {
        $category->load('group');
        return view('purchase_categories.view', ['category' => $category]);
    }
}
