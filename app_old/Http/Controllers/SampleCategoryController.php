<?php

namespace App\Http\Controllers;

use App\Http\Requests\SampleCategoryRequest;
use App\Models\SampleCategory;
use App\Queries\SampleCategoryDataTable;
use App\Repositories\SampleCategoryRepository;

use Illuminate\Database\QueryException;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Models\AssetCategory;
use App\Http\Requests\UpdateSampleCategoryRequest;
use Laracasts\Flash\Flash;
use Throwable;

class SampleCategoryController extends AppBaseController
{
    /**
     * @var SampleCategoryRepository
     */
    private $sampleCategoryRepository;
    public function __construct(SampleCategoryRepository $sampleCategoryRepo)
    {
        $this->sampleCategoryRepository = $sampleCategoryRepo;
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
            return DataTables::of((new SampleCategoryDataTable())->get($request->all()))->make(true);
        }

        return view('sample_categories.index');
    }

    public function create()
    {
        return view('sample_categories.create');
    }

    public function store(SampleCategoryRequest $request)
    {
        $input = $request->all();
        try {
            $assetCategory = $this->sampleCategoryRepository->create($input);
            activity()->causedBy(getLoggedInUser())
                ->performedOn($assetCategory)
                ->useLog('Sample Category created.')
                ->log($assetCategory->title . ' Sample Category.');
            Flash::success(__('messages.sample_categories.saved'));
            return $this->sendResponse($assetCategory, __('messages.sample_categories.saved'));
        } catch (Throwable $e) {
            throw $e;
        }
    }
    public function destroy(SampleCategory $category)
    {
        $data = $category->toArray();
        if (SampleCategory::checkExist($data['id'])) {
            return $this->sendError('Already In Use');
        }
        try {
            $category->delete();
            activity()->performedOn($category)->causedBy(getLoggedInUser())
                ->useLog('Sample Category deleted.')->log($category->title . ' Sample Category deleted.');
            return $this->sendSuccess('Sample Category deleted successfully.');
        } catch (QueryException $e) {
            return $this->sendError('Failed to delete! Already in use.');
        }
    }

    public function edit(SampleCategory $category)
    {
        return view('sample_categories.edit', ['category' => $category]);
    }
    public function update(SampleCategory $category, UpdateSampleCategoryRequest $updateSampleCategoryRequest)
    {
        $input = $updateSampleCategoryRequest->all();
        $assetCategory = $this->sampleCategoryRepository->update($input, $updateSampleCategoryRequest->id);
        activity()->performedOn($assetCategory)->causedBy(getLoggedInUser())
            ->useLog('Sample Category Updated')->log($assetCategory->title . 'Sample Category updated.');
        Flash::success(__('messages.sample_categories.updated'));
        return $this->sendSuccess(__('messages.sample_categories.updated'));
    }
    public function view(SampleCategory $category)
    {
        return view('sample_categories.view', ['category' => $category]);
    }
    public function export()
    {
        // Get sample category
        $sampleCategories = $this->sampleCategoryRepository->getSampleCategory();

        // Prepare CSV data
        $csvData = [];
        $csvData[] = ['SL', 'Name', 'Description', 'Created At'];

        foreach ($sampleCategories as $index => $sampleCategory) {
            $csvData[] = [
                $index + 1, // Serial number
                $sampleCategory->name,
                $sampleCategory->description ?? 'N/A',
                \Carbon\Carbon::parse($sampleCategory->created_at)->format('d-m-Y') // Created At formatted
            ];
        }

        // Set the headers for the response
        $filename = 'sample_categories_export_' . now()->format('Y-m-d_H-i') . '.csv';
        $handle = fopen('php://output', 'w');

        // Send the headers to the browser
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: no-cache');
        header('Expires: 0');

        // Write each row of the CSV to the output
        foreach ($csvData as $row) {
            fputcsv($handle, $row);
        }

        fclose($handle);
        exit; // Terminate the script
    }
}
