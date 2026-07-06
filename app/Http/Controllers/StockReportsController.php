<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateProductRequest;
use App\Http\Requests\PurhcaseItemRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Requests\UpdateProductUnitRequest;
use App\Models\Product;
use App\Models\ProductUnit;
use App\Queries\StockReportsDataTable;
use App\Queries\ProductDataTable;
use App\Queries\ProductUnitDataTable;
use App\Repositories\PurchaseItemRepository;
use App\Repositories\ProductRepository;
use App\Repositories\ProductUnitRepository;
use Illuminate\Database\QueryException;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Models\AssetCategory;
use App\Http\Requests\UpdatePurchaseItemRequest;
use Laracasts\Flash\Flash;
use Throwable;
use App\Models\PurchaseCategory;
use App\Models\PurchaseSubCategory;
use App\Models\PurchaseItem;
use App\Models\DocumentNextNumber;
use App\Models\Setting;
use App\Repositories\PosRepository;

class StockReportsController extends AppBaseController
{
    /**
     * @var PurchaseItemRepository

     */
    private $purchaseItemRepository;
    public function __construct(PurchaseItemRepository $purchaseItemRepo)
    {
        $this->purchaseItemRepository = $purchaseItemRepo;

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
            return DataTables::of((new StockReportsDataTable())->get($request->all()))->make(true);
        }

        $branches = $this->getUsersBranches();
        $groups = $this->purchaseItemRepository->getGroups();
        $categories = $this->purchaseItemRepository->getCategories();
        $subcategories = $this->purchaseItemRepository->getSubCategories();
        $allItems = $this->purchaseItemRepository->getAllItems();

        return view('stock-reports.index', compact('groups', 'categories', 'subcategories', 'branches', 'allItems'));
    }
}
