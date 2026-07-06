<?php

namespace App\Repositories;

use App\Models\Asset;
use App\Models\AssetCategory;
use App\Models\Employee;
use App\Models\Product;
use App\Models\ProductGroup;
use App\Models\Supplier;
use App\Models\TaxRate;
use App\Models\Setting;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use App\Http\Requests\AssetRequest;
use App\Http\Requests\UpdateAssetRequest;

/**
 * Class ProductRepository
 *
 * @version October 12, 2021, 10:50 am UTC
 */
class AssetRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'name',
        'company_name',
        'company_asset_code',
        'purchase_date',
        'manufacturer',
        'serial_number',
        'warranty_end_date',
        'invoice_number',
        'asset_note',
        'branch_id',
        'supplier_id',
        'qty',
        'price',
        'total'
    ];

    /**
     * Return searchable fields
     *
     * @return array
     */
    public function getFieldsSearchable()
    {
        return $this->fieldSearchable;
    }

    /**
     * Configure the Model
     **/
    public function model()
    {
        return Asset::class;
    }


    /**
     * @param  int  $id
     * @return Builder|Builder[]|Collection|Model|null
     */
    public function getCategories()
    {
        return AssetCategory::pluck('title', 'id')->toArray();
    }
    public function getEmployees()
    {
        return Employee::pluck('name', 'id')->toArray();
    }
    public function getSuppliers()
    {
        return Supplier::pluck('company_name', 'id');
    }
    public function getCompanyName()
    {
        return Setting::where('key', 'company_name')->first();
    }

    public function saveAsset(AssetRequest $request)
    {

        // Handle file upload
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '_' . $image->getClientOriginalName(); // Rename image file with timestamp prefix
            $image->storeAs('public/images', $imageName); // Store image in storage/app/public/images directory
        } else {
            $imageName = null;
        }

        // Create new Asset instance
        $asset = new Asset();
        $asset->name = $request->input('name');
        $asset->purchase_date = $request->input('purchase_date');
        $asset->warranty_end_date = $request->input('warranty_end_date');
        $asset->manufacturer = $request->input('manufacturer');
        $asset->invoice_number = $request->input('invoice_number');
        $asset->branch_id = $request->input('branch_id') ?? null;

        $asset->qty = $request->input('qty', 1);
        $asset->price = $request->input('price', 0.00);
        $asset->total = $request->input('total', $asset->qty * $asset->price);
        // Conditional assignment of attributes
        if ($request->has('company_asset_code')) {
            $asset->company_asset_code = $request->input('company_asset_code');
        }
        if ($request->has('asset_category_id')) {
            $asset->asset_category_id = $request->input('asset_category_id');
        }
        if ($request->has('is_working')) {
            $asset->is_working = $request->input('is_working');
        }
        if ($request->has('company_name')) {
            $asset->company_name = $request->input('company_name');
        }
        if ($request->has('employee_id')) {
            $asset->employee_id = $request->input('employee_id');
        }
        if ($request->has('serial_number')) {
            $asset->serial_number = $request->input('serial_number');
        }
        if ($request->has('asset_note')) {
            $asset->asset_note = $request->input('asset_note');
        }
        if ($request->has('supplier_id')) {
            $asset->supplier_id = $request->input('supplier_id');
        }
        $asset->image = $imageName; // Save image file name
        // Save the asset
        $asset->save();
        // Redirect back with success message
        return $asset;
    }

    public function updateAsset(UpdateAssetRequest $request, $id)
    {


        //  dd($request->all());
        // $request=$request->all();
        // Retrieve the existing Asset instance
        $asset = Asset::findOrFail($id);
        // Handle file upload
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '_' . $image->getClientOriginalName(); // Rename image file with timestamp prefix
            $image->storeAs('public/images', $imageName); // Store image in storage/app/public/images directory
            $asset->image = $imageName; // Save image file name
        }
        // Update the asset attributes
        $asset->name = $request->input('name');
        $asset->purchase_date = $request->input('purchase_date');
        $asset->warranty_end_date = $request->input('warranty_end_date');
        $asset->manufacturer = $request->input('manufacturer');
        $asset->invoice_number = $request->input('invoice_number');

        $asset->qty = $request->input('qty', 1);
        $asset->price = $request->input('price', 0.00);
        $asset->total = $request->input('total', $asset->qty * $asset->price);

        // Conditional assignment of attributes
        if ($request->has('company_asset_code')) {
            $asset->company_asset_code = $request->input('company_asset_code');
        }
        if ($request->has('asset_category_id')) {
            $asset->asset_category_id = $request->input('asset_category_id');
        }
        if ($request->has('is_working')) {
            $asset->is_working = $request->input('is_working');
        }
        if ($request->has('company_name')) {
            $asset->company_name = $request->input('company_name');
        }
        if ($request->has('employee_id')) {
            $asset->employee_id = $request->input('employee_id');
        }
        if ($request->has('serial_number')) {
            $asset->serial_number = $request->input('serial_number');
        }
        if ($request->has('asset_note')) {
            $asset->asset_note = $request->input('asset_note');
        }
        if ($request->has('supplier_id')) {
            $asset->supplier_id = $request->input('supplier_id');
        }
        return $asset->update();
    }
}
