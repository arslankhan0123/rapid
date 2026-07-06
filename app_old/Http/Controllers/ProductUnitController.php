<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateProductRequest;
use App\Http\Requests\ProductUnitRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Requests\UpdateProductUnitRequest;
use App\Models\Product;
use App\Models\ProductUnit;
use App\Queries\ProductDataTable;
use App\Queries\ProductUnitDataTable;
use App\Repositories\ProductRepository; 
use App\Repositories\ProductUnitRepository;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class ProductUnitController extends AppBaseController
{

    private $productRepository;
    private $productUnitRepository;

    public function __construct(ProductRepository $productRepo, ProductUnitRepository $productUnitRepo)
    {
        $this->productRepository = $productRepo;
        $this->productUnitRepository = $productUnitRepo;
    }
    public function index(Request $request)
    {
        if ($request->ajax()) {
            return DataTables::of((new ProductUnitDataTable())->get($request->only(['group'])))->make(true);
        }
        $data = $this->productRepository->getSyncListForItem();
        return view('product_unit.index', compact('data'));
    }
    public function create(){
        return view('product_unit.create');
    }
    public function store(ProductUnitRequest $request)
    {

        $input = $request->all();
        try {
            $productUnit = $this->productUnitRepository->create($input);
            activity()->causedBy(getLoggedInUser())
                ->performedOn($productUnit)
                ->useLog('Unit created.')
                ->log($productUnit->title . ' Unit created.');
            return $this->sendResponse($productUnit, __('messages.products.unit_saved'));
        } catch (Throwable $e) {
            throw $e;
        }
    }
    public function destroy(ProductUnit $unit)
    {
        $unit->delete();
        activity()->performedOn($unit)->causedBy(getLoggedInUser())
            ->useLog('Product Unit deleted.')->log($unit->title . ' Product Unit deleted.');
        return $this->sendSuccess('Product Unit deleted successfully.');
    }

    public function edit(ProductUnit $unit)
    {
        return view('product_unit.edit',compact(['unit']));
    }
    public function update(ProductUnit $unit, UpdateProductUnitRequest $updateProductUnitRequest)
    {
        $input = $updateProductUnitRequest->all();
        $productUnit = $this->productUnitRepository->update($input, $updateProductUnitRequest->id);
        activity()->performedOn($productUnit)->causedBy(getLoggedInUser())
            ->useLog('Product unit  updated.')->log($productUnit->title . ' Product Unit updated.');
        return $this->sendSuccess(__('messages.products.unit_updated'));
    }
    public function view(ProductUnit $unit)
    {
        return view('product_unit.view', compact(['unit']));
    }
}
