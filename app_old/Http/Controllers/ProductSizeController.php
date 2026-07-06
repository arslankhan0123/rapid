<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductBrandRequest;
use App\Http\Requests\ProductSizeRequest;
use App\Http\Requests\UpdateProductBrandRequest;
use App\Http\Requests\UpdateProductSizeRequest;
use App\Models\ProductBrand;
use App\Models\Size;
use App\Queries\ProductBrandDataTable;
use App\Queries\ProductSizeDataTable;
use App\Repositories\ProductBrandRepository;
use App\Repositories\ProductRepository;
use App\Repositories\ProductSizeRepository;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class ProductSizeController extends AppBaseController
{
    private $productRepository;
    private $productSizeRepository;

    public function __construct(ProductRepository $productRepo, ProductSizeRepository $productSizeRepository)
    {
        $this->productRepository = $productRepo;
        $this->productSizeRepository = $productSizeRepository;
    }
    public function index(Request $request)
    {
        if ($request->ajax()) {
            return DataTables::of((new ProductSizeDataTable())->get($request->only(['group'])))->make(true);
        }
        $data = $this->productRepository->getSyncListForItem();

        return view('product_size.index', compact('data'));
    }

    public function create(){
        return view('product_size.create');
    }

    public function store(ProductSizeRequest $request)
    {
        $input = $request->all();
        try {
            $productSize = $this->productSizeRepository->create($input);
            activity()->causedBy(getLoggedInUser())
                ->performedOn($productSize)
                ->useLog('Size created.')
                ->log($productSize->title . ' Size created.');
            return $this->sendResponse($productSize, __('messages.products.size_saved'));
        } catch (Throwable $e) {
            throw $e;
        }
    }

    public function edit(Size $size)
    {
        return view('product_size.edit',compact(['size']));
    }
    public function update(Size $size, UpdateProductSizeRequest $updateProductSizeRequest)
    {
        $input = $updateProductSizeRequest->all();
        $productSize = $this->productSizeRepository->update($input, $updateProductSizeRequest->id);
        activity()->performedOn($productSize)->causedBy(getLoggedInUser())
            ->useLog('Product Size  updated.')->log($productSize->title . ' Product Size updated.');
        return $this->sendSuccess(__('messages.products.size_updated'));
    }

    public function destroy(Size $size)
    {
        $size->delete();
        activity()->performedOn($size)->causedBy(getLoggedInUser())
            ->useLog('Product Size deleted.')->log($size->title . ' Product Size deleted.');
        return $this->sendSuccess('Product Size deleted successfully.');
    }
    public function view(Size $size)
    {
        return view('product_size.view', compact(['size']));
    }
}
