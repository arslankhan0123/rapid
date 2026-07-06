<?php

namespace App\Http\Controllers;

use App\Queries\SupplierGroupDataTable;
use Illuminate\Http\Request;
use App\Repositories\SupplierGroupRepository;
use Yajra\DataTables\DataTables;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Exception;
use App\Http\Requests\SupplierGroupRequest;
use App\Models\SupplierGroup;
use App\Http\Requests\UpdateSupplierGroupRequest;
use Illuminate\Database\QueryException;
use Laracasts\Flash\Flash;
class SupplierGroupController extends AppBaseController
{
    /**
     * @var SupplierGroupRepository
     */
    private $supplierGroupRepository;
    public function __construct(SupplierGroupRepository $supplierGroupRepo)
    {
        $this->supplierGroupRepository = $supplierGroupRepo;
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
            return DataTables::of((new SupplierGroupDataTable())->get($request->only(['group'])))->make(true);
        }
        return view('supplier_groups.index');
    }


    public function create()
    {
        return view('supplier_groups.create');
    }

    public function store(SupplierGroupRequest $request)
    {

        $input = $request->all();
        try {
            $designation = $this->supplierGroupRepository->create($input);
            activity()->causedBy(getLoggedInUser())
                ->performedOn($designation)
                ->useLog('Supplier Group created.')
                ->log($designation->name . ' Suppler Group.');
            Flash::success(__('messages.supplier_groups.saved'));

            return $this->sendResponse($designation, __('messages.supplier_groups.saved'));
        } catch (Throwable $e) {
            throw $e;
        }
    }
    public function destroy(SupplierGroup $supplierGroup)
    {
        try {
            $supplierGroup->delete();
            activity()->performedOn($supplierGroup)->causedBy(getLoggedInUser())
                ->useLog('Supplier Group deleted.')->log($supplierGroup->name . 'Supplier Group deleted.');
            return $this->sendSuccess(__('messages.supplier_groups.delete'));
        } catch (QueryException $e) {
            return $this->sendError('Failed To delete!! Already in use.');
        }
    }

    public function edit(SupplierGroup $supplierGroup)
    {
        return view('supplier_groups.edit', compact(['supplierGroup']));
    }
    public function update(SupplierGroup $supplierGroup, UpdateSupplierGroupRequest $updateSupplierGroupRequest)
    {
        $input = $updateSupplierGroupRequest->all();
        $designation = $this->supplierGroupRepository->update($input, $updateSupplierGroupRequest->id);
        activity()->performedOn($designation)->causedBy(getLoggedInUser())
            ->useLog('Supplier Group Updated')->log($designation->title . 'Supplier Group updated.');
        Flash::success(__('messages.supplier_groups.saved'));
        return $this->sendSuccess(__('messages.supplier_groups.saved'));
    }

    public function view(SupplierGroup $supplierGroup)
    {
        return view('supplier_groups.view', compact(['supplierGroup']));
    }
}
