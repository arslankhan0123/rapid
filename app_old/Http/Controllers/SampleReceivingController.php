<?php

namespace App\Http\Controllers;

use App\Http\Requests\SampleReceivingRequest;
use App\Models\SampleReceiving;
use App\Queries\SampleReceivingDataTable;
use App\Repositories\SampleReceivingRepository;

use Illuminate\Database\QueryException;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Models\AssetCategory;
use App\Http\Requests\UpdateSampleReceivingRequest;
use Laracasts\Flash\Flash;
use Throwable;
use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Support\Facades\DB;
use App\Models\Setting;


class SampleReceivingController extends AppBaseController
{
    /**
     * @var SampleReceivingRepository
     */
    private $sampleReceivingRepository;
    public function __construct(SampleReceivingRepository $sampleReceivingRepo)
    {
        $this->sampleReceivingRepository = $sampleReceivingRepo;
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
            return DataTables::of((new SampleReceivingDataTable())->get($request->all()))->make(true);
        }
        $usersBranches = $this->getUsersBranches();
        return view('sample_receiving.index', compact('usersBranches'));
    }

    public function create()
    {
        $nextID = SampleReceiving::getNextID();
        $data = $this->sampleReceivingRepository->getSyncList();
        $usersBranches = $this->getUsersBranches();
        return view('sample_receiving.create', compact(['nextID', 'data', 'usersBranches']));
    }
    public function  pdf(SampleReceiving $category)
    {
        $category->load(['category', 'branch', 'deliveredBy', 'receivedBy']);

        // dd($category->toArray());

        $settings = Setting::pluck('value', 'key')->toArray();

        $data = [
            'settings' => $settings,
            'category' => $category
        ];

        $pdf = PDF::loadView('sample_receiving.pdf', $data);
        $pdf->setOptions(["isPhpEnabled" => true, 'isHtml5ParserEnabled' => true]);
        $pdf->setPaper('A4', 'portrait');

        return $pdf->download("Sample_Receiving_" . $category->client_name . '.pdf');
    }

    public function store(SampleReceivingRequest $request)
    {
        $input = $request->all();

        try {
            $assetCategory = $this->sampleReceivingRepository->create($input);
            activity()->causedBy(getLoggedInUser())
                ->performedOn($assetCategory)
                ->useLog('Sample Category created.')
                ->log($assetCategory->title . ' Sample Category.');
            Flash::success(__('messages.service_categories.saved'));
            return $this->sendResponse($assetCategory, __('messages.sample_receiving.saved'));
        } catch (Throwable $e) {
            throw $e;
        }
    }
    public function destroy(SampleReceiving $category)
    {
        try {
            $category->delete();
            activity()->performedOn($category)->causedBy(getLoggedInUser())
                ->useLog('Sample Category deleted.')->log($category->title . ' Sample Category deleted.');
            return $this->sendSuccess('Sample Category deleted successfully.');
        } catch (QueryException $e) {
            return $this->sendError('Failed to delete! Already in use.');
        }
    }

    public function edit(SampleReceiving $category)
    {
        $data = $this->sampleReceivingRepository->getSyncList();
        $usersBranches = $this->getUsersBranches();
        return view('sample_receiving.edit', compact(['data', 'category', 'usersBranches']));
    }
    public function update(SampleReceiving $category, UpdateSampleReceivingRequest $updateSampleReceivingRequest)
    {
        $input = $updateSampleReceivingRequest->all();
        $assetCategory = $this->sampleReceivingRepository->update($input, $updateSampleReceivingRequest->id);
        activity()->performedOn($assetCategory)->causedBy(getLoggedInUser())
            ->useLog('Sample Receiving Updated')->log($assetCategory->title . 'Sample Receiving updated.');
        Flash::success(__('messages.sample_receiving.updated'));
        return $this->sendSuccess(__('messages.sample_receiving.updated'));
    }
    public function view($id)
    {
        $data = $this->sampleReceivingRepository->getData($id);

        // dd($data);
        return view('sample_receiving.view', compact(['data']));
    }
    public function export()
    {
        // Get sample receiving data
        $sampleReceiving = $this->sampleReceivingRepository->getSampleReceivingData();


        // Prepare CSV data
        $csvData = [];
        $csvData[] = [
            'SL',
            'branch',
            'Date',
            'Time',
            'Section',
            'Client Name',
            'Client Reference',
            'Type Of Sample',
            'Required Tests',
            'Number Of Sample',
            'Delivered By',
            'Received By',
            'Created At'
        ];

        foreach ($sampleReceiving as $index => $sampleReceive) {
            $csvData[] = [
                $index + 1, // Serial number
                $sampleReceive->branch?->name ?? '',
                \Carbon\Carbon::parse($sampleReceive->date)->format('d-m-Y'),
                \Carbon\Carbon::parse($sampleReceive->time)->format('h:i:s A'),
                $sampleReceive->sample_categories_name,
                $sampleReceive->client_name,
                $sampleReceive->client_reference,
                $sampleReceive->type_of_sample,
                $sampleReceive->required_tests,
                $sampleReceive->number_of_sample,
                $sampleReceive->number_of_sample,
                $sampleReceive->delivered_by_name,
                $sampleReceive->received_by_name,
                \Carbon\Carbon::parse($sampleReceive->created_at)->format('d-m-Y') // Created At formatted
            ];
        }

        // Set the headers for the response
        $filename = 'sample_receiving_export_' . now()->format('Y-m-d_H-i') . '.csv';
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
