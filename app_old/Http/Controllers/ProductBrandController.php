<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductBrandRequest;
use App\Http\Requests\UpdateProductBrandRequest;
use App\Http\Requests\UpdateProductUnitRequest;
use App\Models\ProductBrand;
use App\Models\ProductUnit;
use App\Queries\ProductBrandDataTable;
use App\Repositories\ProductBrandRepository;
use App\Repositories\ProductRepository;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class ProductBrandController extends AppBaseController
{
    private $productRepository;
    private $productBrandRepository;

    public function __construct(ProductRepository $productRepo, ProductBrandRepository $productBrandRepository)
    {
        $this->productRepository = $productRepo;
        $this->productBrandRepository = $productBrandRepository;
    }
    public function index(Request $request)
    {
        if ($request->ajax()) {
            return DataTables::of((new ProductBrandDataTable())->get($request->only(['group'])))->make(true);
        }
        $data = $this->productRepository->getSyncListForItem();

        return view('product_brand.index', compact('data'));
    }

    public function create(){
        return view('product_brand.create');
    }

    public function store(ProductBrandRequest $request)
    {
        $input = $request->all();
        try {
            $productBrand = $this->productBrandRepository->create($input);
            activity()->causedBy(getLoggedInUser())
                ->performedOn($productBrand)
                ->useLog('Brand created.')
                ->log($productBrand->title . ' Brand created.');
            return $this->sendResponse($productBrand, __('messages.products.unit_saved'));
        } catch (Throwable $e) {
            throw $e;
        }
    }

    public function edit(ProductBrand $brand)
    {
        return view('product_brand.edit',compact(['brand']));
    }
    public function update(ProductBrand $brand, UpdateProductBrandRequest $updateProductBrandRequest)
    {
        $input = $updateProductBrandRequest->all();
        $productBrand = $this->productBrandRepository->update($input, $updateProductBrandRequest->id);
        activity()->performedOn($productBrand)->causedBy(getLoggedInUser())
            ->useLog('Product brand  updated.')->log($productBrand->title . ' Product Brand updated.');
        return $this->sendSuccess(__('messages.products.brand_updated'));
    }

    public function destroy(ProductBrand $brand)
    {
        $brand->delete();
        activity()->performedOn($brand)->causedBy(getLoggedInUser())
            ->useLog('Product Brand deleted.')->log($brand->title . ' Product Brand deleted.');
        return $this->sendSuccess('Product Brand deleted successfully.');
    }
    public function view(ProductBrand $brand)
    {
        return view('product_brand.view', compact(['brand']));
    }
}
