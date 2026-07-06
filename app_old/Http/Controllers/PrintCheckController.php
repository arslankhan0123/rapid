<?php

namespace App\Http\Controllers;

use App\Queries\CheckDataTable;
use App\Models\Asset;
use App\Repositories\CheckRepository;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Http\Requests\CheckRequest;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\UpdateCheckRequest;
use Laracasts\Flash\Flash;
use Throwable;
use App\Models\Check;

class PrintCheckController extends AppBaseController
{
    /**
     * @var CheckRepository
     *
     */
    private $checkRepository;
    public function __construct(CheckRepository $checkRepo)
    {
        $this->checkRepository = $checkRepo;
    }
    public function index(Request $request)
    {
        if ($request->ajax()) {
            return DataTables::of((new CheckDataTable())->get($request->all()))->make(true);
        }


        $usersBranches = $this->getUsersBranches();
        return view('print-checks.index', compact(['usersBranches']));
    }


    public function create()
    {

        $usersBranches = $this->getUsersBranches();
        $banks = $this->checkRepository->getBanks();
        return view('print-checks.create', compact(['usersBranches', 'banks']));
    }

    public function store(CheckRequest $request)
    {

        try {
            $assetStatus = $this->checkRepository->saveCheck($request->all());
            activity()->causedBy(getLoggedInUser())
                ->performedOn($assetStatus)
                ->useLog('Check created.')
                ->log($assetStatus->check_number . ' Check Saves');
            Flash::success(__('messages.print-checks.saved'));
            return  $this->sendResponse($assetStatus, __('messages.print-checks.saved'));
        } catch (Throwable $e) {
            throw $e;
        }
    }
    public function destroy(Check $check)
    {

        $check->delete();
        activity()->performedOn($check)->causedBy(getLoggedInUser())
            ->useLog('Check deleted.')->log($check->check_number . 'Check  deleted.');
        return $this->sendSuccess('Check deleted successfully.');
    }

    public function edit(Check $check)
    {
        $banks = $this->checkRepository->getBanks();
        $usersBranches = $this->getUsersBranches();
        return view('print-checks.edit', compact(['usersBranches', 'check', 'banks']));
    }

    public function update(Check $check, UpdateCheckRequest $updateCheckRequest)
    {
        $updateStatus = $this->checkRepository->updateCheck($updateCheckRequest->all(),  $check);
        activity()->causedBy(getLoggedInUser())
            ->performedOn($check)
            ->useLog('Check Updated.')
            ->log($check->check_number . ' Check Udpated');
        Flash::success(__('messages.print-checks.saved'));
        return  $this->sendResponse($check, __('messages.print-checks.saved'));
    }
    public function view(Check $check)
    {

        $check->load(['branch', 'bank']);
        return view('print-checks.view', compact(['check']));
    }
    public function printPDF(Check $check)
    {
        $check->load('branch');
        $words = $this->amountToWords($check->amount ?? 0);
        $html = view('print-checks.pdf', compact('check', 'words'))->render();
        $mpdf = new \Mpdf\Mpdf([
            'format' => [205.2, 82.3], // Width x Height in mm
            'orientation' => 'P', // Portrait
        ]);


        // $mpdf->SetWatermarkImage(public_path('img/check.jpg'), .5, 'C'); // 0.1 is the opacity (0 is fully transparent, 1 is fully opaque)
        // $mpdf->showWatermarkImage = true;

        $mpdf->WriteHTML($html);
        return $mpdf->Output('check_' . $check->check_number . '.pdf', 'I'); // 'I' for inline view, 'D' for download
    }
}
