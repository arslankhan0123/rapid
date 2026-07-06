<?php

namespace App\Http\Controllers;

use App\Queries\AssetDataTable;
use App\Models\Asset;
use App\Repositories\AssetRepository;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Http\Requests\AssetRequest;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\UpdateAssetRequest;
use Laracasts\Flash\Flash;
use Throwable;

class AssetController extends AppBaseController
{
    /**
     * @var AssetRepository
     *
     */
    private $assetRepository;
    public function __construct(AssetRepository $assetRepo)
    {
        $this->assetRepository = $assetRepo;
    }
    public function index(Request $request)
    {
        if ($request->ajax()) {
            return DataTables::of((new AssetDataTable())->get($request->all()))->make(true);
        }
        $categories = $this->assetRepository->getCategories();
        $employees = $this->assetRepository->getEmployees();
        $companySetting = $this->assetRepository->getCompanyName();
        $usersBranches = $this->getUsersBranches();

        return view('assets.index', compact(['categories', 'employees', 'companySetting', 'usersBranches']));
    }


    public function create()
    {

        $categories = $this->assetRepository->getCategories();
        $employees = $this->assetRepository->getEmployees();
        $companySetting = $this->assetRepository->getCompanyName();
        $usersBranches = $this->getUsersBranches();
        $suppliers = $this->assetRepository->getSuppliers();

        return view('assets.create', compact(['categories', 'employees', 'companySetting', 'usersBranches', 'suppliers']));
    }

    public function store(AssetRequest $request)
    {

        try {
            $assetStatus = $this->assetRepository->saveAsset($request);
            activity()->causedBy(getLoggedInUser())
                ->performedOn($assetStatus)
                ->useLog('Asset  created.')
                ->log($assetStatus->title . ' Asset.');
            Flash::success(__('messages.assets.asset_saved'));
            return $this->sendResponse($assetStatus, __('messages.assets.asset_saved'));
        } catch (Throwable $e) {
            throw $e;
        }
    }
    public function destroy(Asset $asset)
    {

        // Define the path to the image
        $imagePath = public_path('uploads/public/images/' . $asset->image);

        // Delete the image file if it exists
        if ($asset->image && file_exists($imagePath)) {
            unlink($imagePath);
        }
        $asset->delete();
        activity()->performedOn($asset)->causedBy(getLoggedInUser())
            ->useLog('Asset  deleted.')->log($asset->name . 'Asset  deleted.');
        return $this->sendSuccess('Asset deleted successfully.');
    }

    public function edit(Asset $asset)
    {
        $categories = $this->assetRepository->getCategories();
        $employees = $this->assetRepository->getEmployees();
        $companySetting = $this->assetRepository->getCompanyName();
        $usersBranches = $this->getUsersBranches();
        $suppliers = $this->assetRepository->getSuppliers();
        return view('assets.edit', compact(['categories', 'employees', 'companySetting', 'asset', 'usersBranches', 'suppliers']));
    }

    public function update(Asset $asset, UpdateAssetRequest $updateAssetRequest)
    {

        $updateStatus = $this->assetRepository->updateAsset($updateAssetRequest, $updateAssetRequest->id);
        activity()->causedBy(getLoggedInUser())
            ->performedOn($asset)
            ->useLog('Asset  created.')
            ->log($asset->title . ' Asset.');
        Flash::success(__('messages.assets.asset_saved'));
        return $this->sendResponse($asset, __('messages.assets.asset_saved'));
    }
    public function view(Asset $asset)
    {

        $asset->load(['employee', 'category', 'branch']);
        return view('assets.view', compact(['asset']));
    }
}
