<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateProductRequest;
use App\Http\Requests\PurhcaseItemRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Requests\UpdateProductUnitRequest;
use App\Models\Product;
use App\Models\ProductUnit;
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


class PrintLabelController extends AppBaseController
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
            return DataTables::of((new PurchaseItemDataTable())->get($request->all()))->make(true);
        }
        return view('print-labels.index');
    }

    public function getAllItemsNew(Request $request)
    {
        $query = $request->input('term'); // Get search term from AJAX request

        $items = PurchaseItem::where('name', 'LIKE', "%{$query}%")
            ->orWhere('barcode', 'LIKE', "%{$query}%")
            ->orWhere('code', 'LIKE', "%{$query}%")
            ->get();

        // return response()->json($items);

        return response()
            ->json($items)
            ->header('Cache-Control', 'public, max-age=600') // cache for 10 minutes
            ->header('Expires', now()->addMinutes(10)->toRfc7231String());
    }

    public function getAllItems(Request $request)
    {
        $query = $request->input('term'); // Get search term from AJAX request

        $items = PurchaseItem::where('name', 'LIKE', "%{$query}%")
            ->orWhere('code', 'LIKE', "%{$query}%")
            ->orWhere('barcode', 'LIKE', "%{$query}%")

            ->get();

        return response()->json($items);
    }

    public function previewLabel(Request $request)
    {

        $htmlContent = '<div class="container"><div class="row">';
        $path = public_path('img/sar.png');
        $type = pathinfo($path, PATHINFO_EXTENSION);
        $data = file_get_contents($path);
        $currencyIcon = 'data:image/' . $type . ';base64,' . base64_encode($data);


        $settings = Setting::all()->pluck('value', 'key')->toArray();
        $sellerName = $settings['company'] ?? '';
        // Retrieve all quantities from the request
        $quantities = $request->input('quantity', []);

        // Get the purchase items by their IDs
        $items = PurchaseItem::with(['category', 'brand'])->whereIn('id', array_keys($quantities))->get();

        // Prepare the PDF generator
        $mpdf = new \Mpdf\Mpdf([
            'mode' => 'utf-8',
            'autoLangToFont' => true,
            'autoScriptToLang' => true,
            'directionality' => 'rtl', // Force RTL for Arabic content
            'orientation' => 'P',
            'format' => [38, 28], // Custom size: 55mm x 25mm
            'margin_left' => 0,
            'margin_right' => 0,
            'margin_top' => 0,
            'margin_bottom' => 0,
            'margin_header' => 0,
            'margin_footer' => 0,

        ]);


        // Loop over each item and generate a label
        foreach ($items as $item) {
            $quantity = $quantities[$item->id] ?? 0;

            // Generate the PDF label for the item based on its quantity
            for ($i = 0; $i < $quantity; $i++) {
                // Generate Barcode for the item
                $generator = new \Picqer\Barcode\BarcodeGeneratorPNG();
                $barcode = base64_encode($generator->getBarcode($item->barcode, $generator::TYPE_CODE_128));

                // Data for the label
                $data = [
                    'item' => $item,
                    'quantity' => $quantity,
                    'barcode' => $barcode,
                    'barcode_label' => $item->barcode ?? '',
                    'category' => $item->category?->name ?? '',
                    'brand' => $item->brand?->title ?? '',
                    'currency_icon' => $currencyIcon,
                    'seller_name' => $sellerName,
                ];

                // Assuming you have a Blade view for the label
                $html = view('print-labels.printLabel', $data)->render(); // Replace with your actual view path

                $htmlContent .= '<div class="col-md-4 col-lg-4 col-sm-6" >' . view('print-labels.printLabelForView', $data)->render() . '</div>';


                // Write the HTML to the PDF
                $mpdf->WriteHTML($html);
            }

        }
        $htmlContent .= '</div></div>'; // Add page break after each label

        if ($request->print) {

            $filename = "print_label_" . date('Ymd_His') . '.pdf';

            $path = public_path('print/pos_invoices/' . $filename);
            $mpdf->Output($path, \Mpdf\Output\Destination::FILE);
            $printStatus = $this->printPdfLabel($path);
            return $this->sendResponse($printStatus, 'Print Status');
        } else {
            $pdfContent = $mpdf->Output('', 'S');

            return response()->json([
                'statusCode' => 200,
                'message' => 'PDF generated successfully',
                'pdfData' => base64_encode($pdfContent), // Base64 encode the PDF content
                'htmlContent' => $htmlContent,
            ], 200);
        }
    }
}
