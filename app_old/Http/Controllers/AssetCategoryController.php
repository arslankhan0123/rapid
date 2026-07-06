<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateProductRequest;
use App\Http\Requests\AssetCategoryRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Requests\UpdateProductUnitRequest;
use App\Models\Product;
use App\Models\ProductUnit;
use App\Queries\AssetCategoryDataTable;
use App\Queries\ProductDataTable;
use App\Queries\ProductUnitDataTable;
use App\Repositories\AssetCategoryRepository;
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
use App\Http\Requests\UpdateAssetCategoryRequest;
use Laracasts\Flash\Flash;
use Throwable;

class AssetCategoryController extends AppBaseController
{
    /**
     * @var AssetCategoryRepository
     */
    private $assetCategoryRepository;
    public function __construct(AssetCategoryRepository $assetCategoryRepo)
    {
        $this->assetCategoryRepository = $assetCategoryRepo;
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
            return DataTables::of((new AssetCategoryDataTable())->get($request->only(['group'])))->make(true);
        }
        return view('asset_categories.index');
    }

    public function create()
    {
        return view('asset_categories.create');
    }

    public function store(AssetCategoryRequest $request)
    {

        $input = $request->all();
        try {
            $assetCategory = $this->assetCategoryRepository->create($input);
            activity()->causedBy(getLoggedInUser())
                ->performedOn($assetCategory)
                ->useLog('Asset Category created.')
                ->log($assetCategory->title . ' Asset Category.');
            Flash::success(__('messages.assets.category_saved'));
            return $this->sendResponse($assetCategory, __('messages.assets.category_saved'));
        } catch (Throwable $e) {
            throw $e;
        }
    }
    public function destroy(AssetCategory $category)
    {
        try {
            $category->delete();
            activity()->performedOn($category)->causedBy(getLoggedInUser())
                ->useLog('Asset Category deleted.')->log($category->title . 'Asset Category  deleted.');
            return $this->sendSuccess('Asset Category deleted successfully.');
        } catch (QueryException $e) {
            return $this->sendError('Failed To delete!! Already in use.');
        }
    }

    public function edit(AssetCategory $category)
    {

        return view('asset_categories.edit', ['category' => $category]);
    }
    public function update(AssetCategory $category, UpdateAssetCategoryRequest $updateAssetCategoryRequest)
    {

        $input = $updateAssetCategoryRequest->all();
        $assetCategory = $this->assetCategoryRepository->update($input, $updateAssetCategoryRequest->id);
        activity()->performedOn($assetCategory)->causedBy(getLoggedInUser())
            ->useLog('Asset Category Updated')->log($assetCategory->title . 'Asset Category updated.');
        Flash::success(__('messages.assets.category_saved'));
        return $this->sendSuccess(__('messages.assets.update_category'));
    }
    public function view(AssetCategory $category)
    {
        return view('asset_categories.view', ['category' => $category]);
    }
}
