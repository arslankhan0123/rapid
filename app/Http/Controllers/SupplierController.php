<?php

namespace App\Http\Controllers;

use App\Queries\SupplierDataTable;
use File;
use Illuminate\Http\Request;
use App\Repositories\SupplierRepository;
use Yajra\DataTables\DataTables;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Exception;
use App\Http\Requests\SupplierRequest;
use App\Http\Requests\UpdateSupplierRequest;
use Illuminate\Database\QueryException;
use App\Models\Supplier;
use Illuminate\Support\Facades\DB;
use Laracasts\Flash\Flash;
use Throwable;

class SupplierController extends AppBaseController
{
    /**
     * @var SupplierRepository
     */
    private $supplierRepository;
    public function __construct(SupplierRepository $supplierRepo)
    {
        $this->supplierRepository = $supplierRepo;
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
            return DataTables::of((new SupplierDataTable())->get($request->only(['group'])))->make(true);
        }
        return view('suppliers.index');
    }

    public function create()
    {
        $data = $this->supplierRepository->getSyncList();

        return view('suppliers.create', compact(['data']));
    }

    public function store(SupplierRequest $request)
    {


        $input = $request->all();
        try {
            $designation = $this->supplierRepository->create($input);
            activity()->causedBy(getLoggedInUser())
                ->performedOn($designation)
                ->useLog('Supplier created.')
                ->log(' Supplier.');
            Flash::success(__('messages.suppliers.saved'));
            return $this->sendResponse($designation, __('messages.suppliers.saved'));
        } catch (Throwable $e) {
            throw $e;
        }
    }
    public function destroy(Supplier $supplier)
    {

        // Begin a transaction
        DB::beginTransaction();
        try {


            $documents = DB::table('supplier_docs')
                ->where('supplier_id', $supplier->id)
                ->get();

            // Delete related documents and files from public storage
            foreach ($documents as $document) {
                // Construct the full file path
                $filePath = public_path("uploads/public/supplier_docs/" . $document->file);
                if (File::exists($filePath)) {
                    File::delete($filePath);
                }
                // Delete document record from the database
                DB::table('supplier_docs')
                    ->where('id', $document->id)
                    ->delete();
            }


            $supplier->supplierGroups()->delete();

            $deleted = $supplier->delete();




            // Commit the transaction
            DB::commit();
            activity()->performedOn($supplier)->causedBy(getLoggedInUser())
                ->useLog('Sub Department deleted.')->log('Supplier deleted.');
            return $this->sendSuccess(__('messages.suppliers.delete'));
        } catch (QueryException $e) {
            // Rollback the transaction on error
            DB::rollBack();
            return $this->sendError('Failed To delete!! Already in use.');
        }
    }

    public function edit(Supplier $supplier)
    {
        $data = $this->supplierRepository->getSyncList();
        $supplierGroups = $this->supplierRepository->getGroupData($supplier->id);
        return view('suppliers.edit', compact(['data', 'supplier', 'supplierGroups']));
    }
    public function view(Supplier $supplier)
    {

        $data = $this->supplierRepository->getSyncList();
        $supplierGroups = $this->supplierRepository->getGroupData($supplier->id);
        return view('suppliers.view', compact(['data', 'supplier', 'supplierGroups']));
    }
    public function update(Supplier $supplier, UpdateSupplierRequest $updateSupplierRequest)
    {
        $input = $updateSupplierRequest->all();
        $subDepartment = $this->supplierRepository->update_supplier($supplier->id, $input);
        activity()->performedOn($subDepartment)->causedBy(getLoggedInUser())
            ->useLog('Suppler Updated')->log('Supplier updated.');
        Flash::success(__('messages.suppliers.saved'));
        return $this->sendSuccess(__('messages.suppliers.saved'));
    }

    function file_delete($id)
    {

        $employee = $this->supplierRepository->delete_file($id);
        activity()->performedOn($employee)->causedBy(getLoggedInUser())
            ->useLog('File Deleted')->log(' Branch File deleted.');
        return $this->sendSuccess(__('messages.employees.delete_file'));
    }

}
