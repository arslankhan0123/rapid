<?php

namespace App\Http\Controllers;

use App\Queries\DocumentDataTable;
use Illuminate\Http\Request;
use App\Repositories\DocumentRepository;
use Yajra\DataTables\DataTables;
use App\Http\Requests\DocumentRequest;
use App\Models\Document;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Storage;
use Laracasts\Flash\Flash;

class DocumentController extends AppBaseController
{
    private $documentRepository;

    public function __construct(DocumentRepository $documentRepo)
    {
        $this->documentRepository = $documentRepo;
    }

    // public function index(Request $request)
    // {
    //     if ($request->ajax()) {
    //         return DataTables::of((new DocumentDataTable())->get($request->only(['user_id'])))->make(true);
    //     }

    //     $users = $this->documentRepository->getUsers();
    //     return view('documents.index', compact('users'));
    // }
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $dataTable = DataTables::of((new DocumentDataTable())->get($request->only(['user_id'])))
                ->filter(function ($query) use ($request) {
                    if ($search = $request->get('search')['value']) {
                        $query->where(function ($q) use ($search) {
                            $q->where('document', 'like', "%{$search}%")
                                ->orWhere('description', 'like', "%{$search}%")
                                ->orWhereHas('user', function ($userQuery) use ($search) {
                                    $userQuery->where('first_name', 'like', "%{$search}%")
                                        ->orWhere('last_name', 'like', "%{$search}%");
                                });
                        });
                    }
                })
                ->make(true);

            return $dataTable;
        }

        $users = $this->documentRepository->getUsers();
        return view('documents.index', compact('users'));
    }


    public function create()
    {
        $users = [];
        if (auth()->id() === 1) {
            $users = $this->documentRepository->getUsers();
        }

        return view('documents.create', compact('users'));
    }

    public function store(DocumentRequest $request)
    {
        $input = $request->all();

        try {
            $document = $this->documentRepository->create($input);

            activity()->causedBy(auth()->user())
                ->performedOn($document)
                ->useLog('Document created.')
                ->log($document->document . ' Document uploaded');

            Flash::success(__('messages.documents.saved'));
            return redirect(route('documents.index'));
        } catch (Exception $e) {
            Flash::error(__('messages.documents.error_saving'));
            return redirect()->back()->withInput();
        }
    }

    public function show(Document $document)
    {
        // Check if user has permission to view this document
        if (auth()->id() !== 1 && auth()->id() !== $document->user_id) {
            abort(403, 'Unauthorized action.');
        }

        return view('documents.show', compact('document'));
    }

    public function destroy(Document $document)
    {
        try {
            // Check if user has permission to delete this document
            if (auth()->id() !== 1 && auth()->id() !== $document->user_id) {
                return $this->sendError('Unauthorized action.');
            }

            $this->documentRepository->delete($document->id);

            activity()->causedBy(auth()->user())
                ->performedOn($document)
                ->useLog('Document deleted.')
                ->log($document->document . ' Document deleted');

            return $this->sendSuccess(__('messages.documents.delete'));
        } catch (QueryException $e) {
            return $this->sendError('Failed to delete document.');
        }
    }

    public function download(Document $document)
    {
        if (auth()->id() !== 1 && auth()->id() !== $document->user_id) {
            abort(403, 'Unauthorized action.');
        }

        $filePath = public_path('uploads/documents/' . $document->document);

        if (!file_exists($filePath)) {
            abort(404, 'File not found.');
        }

        return response()->download($filePath, $document->document);
    }
}