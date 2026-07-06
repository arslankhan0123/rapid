<?php

namespace App\Http\Controllers;

use App\Http\Requests\CertificateRequest;
use App\Models\Certificate;
use App\Queries\CertificateDataTable;
use App\Repositories\CertificateRepository;

use Illuminate\Database\QueryException;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Models\AssetCategory;
use App\Http\Requests\UpdateCertificateRequest;
use Laracasts\Flash\Flash;
use Throwable;
use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Support\Facades\DB;
use App\Models\Setting;
use App\Models\DocumentNextNumber;


class CertificateController extends AppBaseController
{
    /**
     * @var CertificateRepository
     */
    private $certificateRepository;
    public function __construct(CertificateRepository $certificateRepo)
    {
        $this->certificateRepository = $certificateRepo;
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
            return DataTables::of((new CertificateDataTable())->get($request->all()))->make(true);
        }

        return view('certificate.index');
    }

    public function create()
    {
        $data = $this->certificateRepository->getSyncList();
        $nextNumber = DocumentNextNumber::getNextNumber('certificate');
        $nextNumber = "CN SML-" . $nextNumber;
        $certificateTypes = $this->certificateRepository->getTypes();

        return view('certificate.create', compact(['data', 'nextNumber', 'certificateTypes']));
    }

    public function store(CertificateRequest $request)
    {
        $input = $request->all();
        try {
            $assetCategory = $this->certificateRepository->create($input);
            activity()->causedBy(getLoggedInUser())
                ->performedOn($assetCategory)
                ->useLog('Certificate created.')
                ->log($assetCategory->title . ' Certificate.');
            Flash::success(__('messages.certificate.saved'));
            DocumentNextNumber::updateNumber('certificate');
            return $this->sendResponse($assetCategory, __('messages.certificate.saved'));
        } catch (Throwable $e) {
            throw $e;
        }
    }
    public function destroy(Certificate $category)
    {
        try {
            $category->delete();
            activity()->performedOn($category)->causedBy(getLoggedInUser())
                ->useLog('Certificate deleted.')->log($category->title . ' Certificate deleted.');
            return $this->sendSuccess('Certificate deleted successfully.');
        } catch (QueryException $e) {
            return $this->sendError('Failed to delete! Already in use.');
        }
    }

    public function edit(Certificate $category)
    {
        $data = $this->certificateRepository->getSyncList();
        $certificateTypes = $this->certificateRepository->getTypes();
        return view('certificate.edit', compact(['data', 'category', 'certificateTypes']));
    }
    public function update(Certificate $category, UpdateCertificateRequest $updateCertificateRequest)
    {
        $input = $updateCertificateRequest->all();
        $assetCategory = $this->certificateRepository->update($input, $updateCertificateRequest->id);
        activity()->performedOn($assetCategory)->causedBy(getLoggedInUser())
            ->useLog('Certificate Updated')->log($assetCategory->title . 'Certificate updated.');
        Flash::success(__('messages.certificate.updated'));
        return $this->sendSuccess(__('messages.certificate.updated'));
    }
    public function view($id)
    {

        $data = $this->certificateRepository->getData($id);
        return view('certificate.view', compact(['data']));
    }
    public function pdf(Certificate $category)
    {

        $comLogoPath = Setting::getLogoLocalPath();
        $imagePath = $comLogoPath;

        $imageData = $imagePath && file_exists($imagePath) ? file_get_contents($imagePath) : '';

        $base64 = base64_encode($imageData);
        $company_logo = 'data:image/png;base64,' . $base64;



        $category->load('type');
        // dd($category->description);
        $settings = Setting::pluck('value', 'key')->toArray();

        if (strcasecmp($category->type?->name, 'appreciation') === 0) {
            $pdf = PDF::loadView('certificate.format.appreciation', compact('category','company_logo'));
        } else if (strcasecmp($category->type?->name, 'experience') === 0) {
            $pdf = PDF::loadView('certificate.format.experience', compact('category','company_logo'));
        } else if (strcasecmp($category->type?->name, 'award') === 0) {
            $pdf = PDF::loadView('certificate.format.award', compact('category','company_logo'));
        } else if (strcasecmp($category->type?->name, 'training') === 0) {
            $pdf = PDF::loadView('certificate.format.training', compact('category','company_logo'));
        } else if (strcasecmp($category->type?->name, 'achievement') === 0) {
            $pdf = PDF::loadView('certificate.format.achievement', compact('category','company_logo'));
        } else if (strcasecmp($category->type?->name, 'retirement') === 0) {
            $pdf = PDF::loadView('certificate.format.retirement', compact('category','company_logo'));
        } else if (strcasecmp($category->type?->name, 'excellance') === 0) {
            $pdf = PDF::loadView('certificate.format.excellance', compact('category','company_logo'));
        } else {
            // Redirect back if the type is not 'award'
            return redirect()->back()->with('error', 'Invalid certificate type!');
        }
        $type = $category->type?->name ?? '';
        $pdf->setOptions(["isPhpEnabled" => true, 'isHtml5ParserEnabled' => true]);
        $pdf->setPaper('A4', 'portrait');
        return $pdf->stream("Certificate-" . $type . "-" . str_replace(' ', '_', $category->employee) . '.pdf');
    }
    public function export()
    {
        // Get sample certificate data
        $certificateData = $this->certificateRepository->getCertificateData();
        // Prepare CSV data
        $csvData = [];
        $csvData[] = ['SL', 'Date', 'Type', 'Employee', 'Lab Manager', 'General Manager', 'Description', 'Created At'];

        foreach ($certificateData as $index => $certificate) {
            $csvData[] = [
                $index + 1, // Serial number
                \Carbon\Carbon::parse($certificate->date)->format('d-m-Y'),
                $certificate->type?->name ?? '',
                $certificate->employee,
                $certificate->lab_manager,
                $certificate->general_manager,
                strip_tags($certificate->description ?? ''),
                \Carbon\Carbon::parse($certificate->created_at)->format('d-m-Y') // Created At formatted
            ];
        }

        // Set the headers for the response
        $filename = 'certificate_export_' . now()->format('Y-m-d_H-i') . '.csv';
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
