<?php

namespace App\Http\Controllers;

use App\Http\Requests\ServiceCategoryRequest;
use App\Queries\ServiceCategoryDataTable;
use App\Repositories\ServiceCategoryRepository;

use Illuminate\Database\QueryException;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Models\AssetCategory;
use App\Http\Requests\UpdateServiceCategoryRequest;
use Laracasts\Flash\Flash;
use Throwable;
use App\Models\ServiceCategory;

class ServiceCategoryController extends AppBaseController
{
    /**
     * @var ServiceCategoryRepository
     */
    private $serviceCategoryRepository;
    public function __construct(ServiceCategoryRepository $serviceCategoryRepo)
    {
        $this->serviceCategoryRepository = $serviceCategoryRepo;
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
            return DataTables::of((new ServiceCategoryDataTable())->get($request->all()))->make(true);
        }

        return view('service_categories.index');
    }

    public function create()
    {
        return view('service_categories.create');
    }

    public function store(ServiceCategoryRequest $request)
    {

        $input = $request->all();
        try {
            $assetCategory = $this->serviceCategoryRepository->create($input);
            activity()->causedBy(getLoggedInUser())
                ->performedOn($assetCategory)
                ->useLog('Service Category created.')
                ->log($assetCategory->title . ' Service Category.');
            Flash::success(__('messages.service_categories.saved'));
            return $this->sendResponse($assetCategory, __('messages.service_categories.saved'));
        } catch (Throwable $e) {
            throw $e;
        }
    }
    public function destroy(ServiceCategory $category)
    {
        $category->load('services');
        if ($category->services->isNotEmpty()) {
            return $this->sendError('Already In Use');
        }
        try {
            $category->delete();
            activity()->performedOn($category)->causedBy(getLoggedInUser())
                ->useLog('Service Category deleted.')->log($category->title . ' Asset Category deleted.');
            return $this->sendSuccess('Service Category deleted successfully.');
        } catch (QueryException $e) {
            return $this->sendError('Failed to delete! Already in use.');
        }
    }

    public function edit(ServiceCategory $category)
    {

        return view('service_categories.edit', ['category' => $category]);
    }
    public function update(ServiceCategory $category, UpdateServiceCategoryRequest $updateServiceCategoryRequest)
    {

        $input = $updateServiceCategoryRequest->all();
        $assetCategory = $this->serviceCategoryRepository->update($input, $updateServiceCategoryRequest->id);
        activity()->performedOn($assetCategory)->causedBy(getLoggedInUser())
            ->useLog('Service Category Updated')->log($assetCategory->title . 'Service Category updated.');
        Flash::success(__('messages.service_categories.saved'));
        return $this->sendSuccess(__('messages.service_categories.saved'));
    }
    public function view(ServiceCategory $category)
    {
        return view('service_categories.view', ['category' => $category]);
    }
}
