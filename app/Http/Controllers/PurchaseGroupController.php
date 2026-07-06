<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateProductRequest;
use App\Http\Requests\PurhcaseGroupRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Requests\UpdateProductUnitRequest;
use App\Models\Product;
use App\Models\ProductUnit;
use App\Queries\PurchaseGroupDataTable;
use App\Queries\ProductDataTable;
use App\Queries\ProductUnitDataTable;
use App\Repositories\PurchaseGroupRepository;
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
use App\Http\Requests\UpdatePurchaseGroupRequest;
use Laracasts\Flash\Flash;
use Throwable;
use App\Models\PurchaseGroup;

class PurchaseGroupController extends AppBaseController
{
    /**
     * @var PurchaseGroupRepository
     */
    private $purchaseGroupRepository;
    public function __construct(PurchaseGroupRepository $purchaseGroupRepo)
    {
        $this->purchaseGroupRepository = $purchaseGroupRepo;
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
            return DataTables::of((new PurchaseGroupDataTable())->get($request->all()))->make(true);
        }
        return view('purchase_groups.index');
    }

    public function create()
    {
        return view('purchase_groups.create');
    }

    public function store(PurhcaseGroupRequest $request)
    {

        $input = $request->all();

        try {
            $assetCategory = $this->purchaseGroupRepository->create($input);
            activity()->causedBy(getLoggedInUser())
                ->performedOn($assetCategory)
                ->useLog('Purchase Group created.')
                ->log($assetCategory->name . ' Purchase Group.');
            Flash::success(__('messages.purchase-groups.saved'));
            return $this->sendResponse($assetCategory, __('messages.purchase-groups.saved'));
        } catch (Throwable $e) {
            throw $e;
        }
    }
    // public function destroy(PurchaseGroup $group)
    // {
    //     try {
    //         $group->delete();
    //         activity()->performedOn($group)->causedBy(getLoggedInUser())
    //             ->useLog('Purchase Group deleted.')->log($group->name . 'Purchase Group  deleted.');
    //         return $this->sendSuccess('Purchase Category deleted successfully.');
    //     } catch (QueryException $e) {
    //         return $this->sendError('Failed To delete!! Already in use.');
    //     }
    // }

    public function destroy(PurchaseGroup $group)
    {
        try {
            // Check if group is used in categories or subcategories
            if ($group->categories()->exists() || $group->subcategories()->exists()) {
                return $this->sendError('Cannot delete! This group is in use by a category or subcategory.');
            }

            // Safe to delete
            $group->delete();

            // Log the deletion activity
            activity()
                ->performedOn($group)
                ->causedBy(getLoggedInUser())
                ->useLog('Purchase Group deleted.')
                ->log($group->name . ' Purchase Group deleted.');

            return $this->sendSuccess('Purchase Group deleted successfully.');
        } catch (QueryException $e) {
            return $this->sendError('Failed to delete due to a database error.');
        }
    }


    public function edit(PurchaseGroup $group)
    {

        return view('purchase_groups.edit', ['category' => $group]);
    }
    public function update(PurchaseGroup $group, UpdatePurchaseGroupRequest $updatePurchaseGroupRequest)
    {

        $input = $updatePurchaseGroupRequest->all();
        $assetCategory = $this->purchaseGroupRepository->update($input, $updatePurchaseGroupRequest->id);
        activity()->performedOn($assetCategory)->causedBy(getLoggedInUser())
            ->useLog('Purchase Group Updated')->log($assetCategory->name . 'Purchase Group updated.');
        Flash::success(__('messages.purchase-categories.saved'));
        return $this->sendSuccess(__('messages.purchase-categories.saved'));
    }
    public function view(PurchaseGroup $group)
    {
        return view('purchase_groups.view', ['category' => $group]);
    }
}
