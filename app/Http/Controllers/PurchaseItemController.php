<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateProductRequest;
use App\Http\Requests\PurhcaseItemRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Requests\UpdateProductUnitRequest;
use App\Models\Product;
use App\Models\ProductUnit;
use App\Queries\ItemListingDataTable;
use App\Queries\PurchaseItemDataTable;
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

use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\IOFactory;


class PurchaseItemController extends AppBaseController
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


        // echo "<pre>";

        // $filePath = public_path('uploads/item_data.xlsx');

        // $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($filePath);
        // $sheet = $spreadsheet->getActiveSheet();
        // $rows = $sheet->toArray(null, true, true, true); // Read as array

        // // Skip header
        // $dataRows = array_slice($rows, 1);

        // $code = 1; // Starting code

        // foreach ($dataRows as $row) {
        //     $barcode = $row['B'] ?? null;
        //     $name = $row['E'] ?? null;


        //     $rawPrice = str_replace(',', '', $row['I'] ?? '');
        //     $price = is_numeric($rawPrice) ? floatval($rawPrice) : null;
        //     // Skip if all required fields are empty
        //     if (empty($barcode) && empty($name) && empty($price)) {
        //         continue;
        //     }


        //     PurchaseItem::create([
        //         'barcode' => $barcode,
        //         'name' => $name,
        //         'full_name' => $name,
        //         'price' => $price,
        //         'cost_price' => $price ? round($price * 0.51, 2) : null,
        //         'purchase_group_id' => 20,
        //         'purchase_category_id' => 31,
        //         'purchase_sub_category_id' => null, // set if needed
        //         'unit_id' => 1, // set if needed
        //         'code' => $code,
        //         'description' => null, // or default
        //     ]);


        //     $code++; // Increment code for next item
        // }




        if ($request->ajax()) {
            return DataTables::of((new PurchaseItemDataTable())->get($request->all()))->make(true);
        }
        return view('purchase_items.index');
    }

    public function itemListing(Request $request)
    {

        if ($request->ajax()) {
            return DataTables::of((new ItemListingDataTable())->get($request->all()))->make(true);
        }
        return view('purchase_items.itemListing');
    }

    public function create()
    {
        $groups = $this->purchaseItemRepository->getGroups();
        $categories = $this->purchaseItemRepository->getCategories();
        $subcategories = $this->purchaseItemRepository->getSubCategories();
        $units = $this->purchaseItemRepository->getUnits();
        $nextNumber = DocumentNextNumber::getNextNumber('item_code');
        $brands = $this->purchaseItemRepository->getBrands();
        $sizes = $this->purchaseItemRepository->getSizes();
        $colors = $this->purchaseItemRepository->getColors();
        $settings = Setting::all()->pluck('value', 'key')->toArray();
        $itemCodeStatus = $settings['item_code_setting'];
        return view('purchase_items.create', compact(['groups', 'categories', 'subcategories', 'units', 'nextNumber', 'brands', 'sizes', 'colors', 'itemCodeStatus']));
    }




    public function store(PurhcaseItemRequest $request)
    {
        $input = $request->all();



        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '_' . $image->getClientOriginalName(); // Unique file name
            $image->move(public_path('img/items/'), $imageName); // Move file to public/img/items/
            $input['image'] = 'img/items/' . $imageName; // Store relative path
        }
        DocumentNextNumber::updateNumber('item_code');
        try {
            $assetCategory = $this->purchaseItemRepository->create($input);
            return response()->json(['success' => true, 'message' => 'Item created successfully']);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => 'Something went wrong'], 500);
        }
    }


    public function destroy(PurchaseItem $item)
    {
        try {
            // Delete the image file if it exists
            if (!empty($item->image) && file_exists(public_path($item->image))) {
                unlink(public_path($item->image));
            }

            $item->delete();

            activity()->performedOn($item)->causedBy(getLoggedInUser())
                ->useLog('Item Deleted')->log($item->name . ' item deleted.');

            return $this->sendSuccess('Item deleted successfully.');
        } catch (QueryException $e) {
            return $this->sendError('Failed to delete!! Already in use.');
        }
    }

    public function edit(PurchaseItem $item)
    {
        $groups = $this->purchaseItemRepository->getGroups();
        $categories = $this->purchaseItemRepository->getCategories();
        $subcategories = $this->purchaseItemRepository->getSubCategories();
        $units = $this->purchaseItemRepository->getUnits();
        $brands = $this->purchaseItemRepository->getBrands();
        $sizes = $this->purchaseItemRepository->getSizes();
        $colors = $this->purchaseItemRepository->getColors();
        $settings = Setting::all()->pluck('value', 'key')->toArray();
        $itemCodeStatus = $settings['item_code_setting'];
        return view('purchase_items.edit', ['item' => $item, 'groups' => $groups, 'categories' => $categories, 'units' => $units, 'subcategories' => $subcategories, 'brands' => $brands, 'sizes' => $sizes, 'colors' => $colors, 'itemCodeStatus' => $itemCodeStatus]);
    }


    public function update(PurchaseItem $item, UpdatePurchaseItemRequest $request)
    {
        $input = $request->all();

        if ($request->hasFile('image')) {
            // Delete the old image if it exists
            if (!empty($item->image) && file_exists(public_path($item->image))) {
                unlink(public_path($item->image));
            }

            // Upload new image to public/img/items/
            $image = $request->file('image');
            $imageName = time() . '_' . $image->getClientOriginalName();
            $image->move(public_path('img/items/'), $imageName);
            $input['image'] = 'img/items/' . $imageName;
        }

        try {
            $updatedItem = $this->purchaseItemRepository->updateItem($input, $item->id);

            activity()
                ->performedOn($updatedItem)
                ->causedBy(getLoggedInUser())
                ->useLog('Purchase Category Updated')
                ->log($updatedItem->name . ' Purchase Category updated.');

            return $this->sendSuccess(__('messages.purchase-items.saved'));
        } catch (Exception $e) {
            return $this->sendError(__('messages.purchase-items.error'), 500);
        }
    }

    public function view(PurchaseItem $item)
    {
        $item->load(['group', 'category', 'subCategory', 'brand', 'color', 'size', 'unit']);
        return view('purchase_items.view', ['category' => $item]);
    }
}
