<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductColorRequest;
use App\Http\Requests\UpdateProductColorRequest;
use App\Models\ProductColor;
use App\Queries\ProductColorDataTable;
use App\Repositories\ProductColorRepository;
use App\Repositories\ProductRepository;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class ProductColorController extends AppBaseController
{
    private $productRepository;
    private $productColorRepository;

    public function __construct(ProductRepository $productRepo, ProductColorRepository $productColorRepository)
    {
        $this->productRepository = $productRepo;
        $this->productColorRepository = $productColorRepository;
    }
    public function index(Request $request)
    {
        if ($request->ajax()) {
            return DataTables::of((new ProductColorDataTable())->get($request->only(['group'])))->make(true);
        }
        $data = $this->productRepository->getSyncListForItem();

        return view('product_color.index', compact('data'));
    }

    public function create(){
        return view('product_color.create');
    }

    public function store(ProductColorRequest $request)
    {
        $input = $request->all();
        try {
            $productColor = $this->productColorRepository->create($input);
            activity()->causedBy(getLoggedInUser())
                ->performedOn($productColor)
                ->useLog('Color created.')
                ->log($productColor->title . ' Color created.');
            return $this->sendResponse($productColor, __('messages.products.color_saved'));
        } catch (Throwable $e) {
            throw $e;
        }
    }

    public function edit(ProductColor $color)
    {
        return view('product_color.edit',compact(['color']));
    }
    public function update(ProductColor $color, UpdateProductColorRequest $updateProductColorRequest)
    {
        $input = $updateProductColorRequest->all();
        $productColor = $this->productColorRepository->update($input, $updateProductColorRequest->id);
        activity()->performedOn($productColor)->causedBy(getLoggedInUser())
            ->useLog('Product Color  updated.')->log($productColor->title . ' Product Color updated.');
        return $this->sendSuccess(__('messages.products.color_updated'));
    }

    public function destroy(ProductColor $color)
    {
        $color->delete();
        activity()->performedOn($color)->causedBy(getLoggedInUser())
            ->useLog('Product Color deleted.')->log($color->title . ' Product Color deleted.');
        return $this->sendSuccess('Product Color deleted successfully.');
    }
    public function view(ProductColor $color)
    {
        return view('product_color.view', compact(['color']));
    }
}
