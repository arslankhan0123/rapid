<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateProductRequest;
use App\Http\Requests\PurhcaseSubCategoryRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Requests\UpdateProductUnitRequest;
use App\Models\Product;
use App\Models\ProductUnit;
use App\Queries\PurchaseSubCategoryDataTable;
use App\Queries\ProductDataTable;
use App\Queries\ProductUnitDataTable;
use App\Repositories\PurchaseSubCategoryRepository;
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
use App\Models\PurchaseSubCategory;

class PurchaseSubCategoryController extends AppBaseController
{
    /**
     * @var PurchaseSubCategoryRepository
     */
    private $purchaseSubCategoryRepository;
    public function __construct(PurchaseSubCategoryRepository $purchaseSubCategoryRepo)
    {
        $this->purchaseSubCategoryRepository = $purchaseSubCategoryRepo;
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
            return DataTables::of((new PurchaseSubCategoryDataTable())->get($request->all()))->make(true);
        }
        return view('purchase_sub_categories.index');
    }

    public function create()
    {
        $groups = $this->purchaseSubCategoryRepository->getGroups();
        $categories=$this->purchaseSubCategoryRepository->getCategories();
        // dd($groups->toArray(),$categories->toArray());
        return view('purchase_sub_categories.create', compact(['groups', 'categories']));
    }

    public function store(PurhcaseSubCategoryRequest $request)
    {

        $input = $request->all();

        try {
            $assetCategory = $this->purchaseSubCategoryRepository->create($input);
            activity()->causedBy(getLoggedInUser())
                ->performedOn($assetCategory)
                ->useLog('Purchase Sub Category created.')
                ->log($assetCategory->name . ' Purchase Sub Category.');
            Flash::success(__('messages.purchase-sub-categories.saved'));
            return $this->sendResponse($assetCategory, __('messages.purchase-sub-categories.saved'));
        } catch (Throwable $e) {
            throw $e;
        }
    }
    public function destroy(PurchaseSubCategory $subcategory)
    {
        try {
            $subcategory->delete();
            activity()->performedOn($subcategory)->causedBy(getLoggedInUser())
                ->useLog('Purchase Category deleted.')->log($subcategory->name . 'Purchase Category  deleted.');
            return $this->sendSuccess('Purchase Category deleted successfully.');
        } catch (QueryException $e) {
            return $this->sendError('Failed To delete!! Already in use.');
        }
    }

    public function edit(PurchaseSubCategory $subcategory)
    {
        $groups = $this->purchaseSubCategoryRepository->getGroups();
        $categories = $this->purchaseSubCategoryRepository->getCategories();
        // dd($subcategory->toArray());
        return view('purchase_sub_categories.edit', ['subcategory' => $subcategory, 'groups'=>$groups, 'categories'=> $categories]);
    }
    public function update(PurchaseSubCategory $subcategory, UpdatePurchaseCategoryRequest $updatePurchaseCategoryRequest)
    {

        $input = $updatePurchaseCategoryRequest->all();
        $assetCategory = $this->purchaseSubCategoryRepository->update($input, $updatePurchaseCategoryRequest->id);
        activity()->performedOn($assetCategory)->causedBy(getLoggedInUser())
            ->useLog('Purchase Category Updated')->log($assetCategory->name . 'Purchase Category updated.');
        Flash::success(__('messages.purchase-categories.saved'));
        return $this->sendSuccess(__('messages.purchase-categories.saved'));
    }
    public function view(PurchaseSubCategory $subcategory)
    {
        $subcategory->load(['group','category']);
        return view('purchase_sub_categories.view', ['category' => $subcategory]);
    }
}
