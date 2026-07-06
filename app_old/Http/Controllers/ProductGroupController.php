<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateProductGroupRequest;
use App\Http\Requests\UpdateProductGroupRequest;
use App\Models\Product;
use App\Models\ProductGroup;
use App\Queries\ProductGroupDataTable;
use App\Repositories\ProductGroupRepository;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class ProductGroupController extends AppBaseController
{
    /**
     * @var ProductGroupRepository
     */
    private $productGroupRepository;

    public function __construct(ProductGroupRepository $productGroupRepo)
    {
        $this->productGroupRepository = $productGroupRepo;
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
            return DataTables::of((new ProductGroupDataTable())->get())->make(true);
        }

        return view('product_groups.index');
    }

    /**
     * @param  CreateProductGroupRequest  $request
     * @return mixed
     */
    public function store(CreateProductGroupRequest $request)
    {
        $input = $request->all();

        $productGroup = $this->productGroupRepository->create($input);

        activity()->performedOn($productGroup)->causedBy(getLoggedInUser())
            ->useLog('New Product Group created.')->log($productGroup->name.' Product Group created.');

        return $this->sendResponse($productGroup, __('messages.product_group.product_group_saved_successfully'));
    }

    /**
     * @param  ProductGroup  $productGroup
     * @return mixed
     */
    public function edit(ProductGroup $productGroup)
    {
        return $this->sendResponse($productGroup, 'Product Group retrieved successfully.');
    }

    /**
     * @param  ProductGroup  $productGroup
     * @param  UpdateProductGroupRequest  $request
     * @return mixed
     */
    public function update(ProductGroup $productGroup, UpdateProductGroupRequest $request)
    {
        $input = $request->all();

        $productGroup = $this->productGroupRepository->update($input, $productGroup->id);

        activity()->performedOn($productGroup)->causedBy(getLoggedInUser())
            ->useLog('Product Group updated.')->log($productGroup->name.' Product Group updated.');

        return $this->sendSuccess(__('messages.product_group.product_group_updated_successfully'));
    }

    /**
     * @param  ProductGroup  $productGroup
     * @return mixed
     */
    public function destroy(ProductGroup $productGroup)
    {
        $productGroupId = Product::where('item_group_id', '=', $productGroup->id)->exists();

        if ($productGroupId) {
            return $this->sendError(__('messages.product_group.product_group_used_somewhere_else'));
        }

        activity()->performedOn($productGroup)->causedBy(getLoggedInUser())
            ->useLog('Product Group deleted.')->log($productGroup->name.' Product Group deleted.');

        $productGroup->delete();

        return $this->sendSuccess('Product Group deleted successfully.');
    }
}
