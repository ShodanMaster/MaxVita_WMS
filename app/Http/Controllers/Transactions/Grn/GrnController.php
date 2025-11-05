<?php

namespace App\Http\Controllers\Transactions\Grn;

use App\Helpers\BarcodeGenerator;
use App\Http\Controllers\Controller;
use App\Models\Barcode;
use App\Models\Category;
use App\Models\Grn;
use App\Models\GrnPurchaseOrder;
use App\Models\GrnPurchaseOrderSub;
use App\Models\GrnSub;
use App\Models\Item;
use App\Models\Location;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderSub;
use App\Models\Vendor;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use RealRashid\SweetAlert\Facades\Alert;

class GrnController extends Controller
{
    public function index()
    {
        $grnNumber = Grn::grnNumber();
        $batchNumber = 'B' . date("ymd");
        $vendors = Vendor::get(['id', 'name']);
        $locations = Location::get(['id', 'name']);
        $categories = Category::get(['id', 'name']);
        return view('transactions.grn.index', compact('grnNumber', 'batchNumber', 'vendors', 'locations', 'categories'));
    }

    public function store(Request $request)
    {

        try {

            DB::beginTransaction();

            $userid = Auth::user()->id;
            $batchNumber = 'B' . date("ymd");
            $purchaseNumber = null;

            if ($request->purchase_number) {
                $purchaseOrder = PurchaseOrder::select('purchase_number')->find($request->purchase_number);
                $purchaseNumber = $purchaseOrder->purchase_number;
            }

            $location = Location::find($request->location_id);
            $branchId = $location->branch_id;
            $prefix = $location->prefix;
            $grnNumber = Grn::grnNumber();

            $grn = Grn::create([
                'grn_number' => $grnNumber,
                'purchase_number' => $purchaseNumber,
                'invoice_number' => $request->invoice_number,
                'invoice_date' => $request->invoice_date,
                'remarks' => $request->remarks,
                'location_id' => $request->location_id,
                'vendor_id' => $request->vendor_id,
                'branch_id' => $branchId,
            ]);

            if ($request->is_purchase == 1) {
                $grnPurchaseOrder = GrnPurchaseOrder::create([
                    'grn_id' => $grn->id,
                    'user_id' => $userid
                ]);
            }

            $grnSubData = [];
            $grnPurchaseOrderSubData = [];
            $barcodeData = [];
            $purchaseOrderSubUpdates = [];

            foreach ($request->items as $item) {

                $purchaseOrderExists = PurchaseOrder::where('id', '=', $item["purchase_id"])->where('status', 0)->first();

                if ($purchaseOrderExists) {
                    $purchaseOrderSub = PurchaseOrderSub::where('purchase_order_id', $purchaseOrderExists->id)
                        ->where('item_id', $item["item_id"])
                        ->where('status', '0')
                        ->first();

                    if ($purchaseOrderSub) {
                        $purchaseOrderSubUpdates[] = [
                            'id' => $purchaseOrderSub->id,
                            'picked_quantity' => $purchaseOrderSub->picked_quantity + $item["total_quantity"]
                        ];
                    }
                }

                $grnSubData[] = [
                    'grn_id' => $grn->id,
                    'item_id' => $item["item_id"],
                    'batch_number' => $batchNumber,
                    'date_of_manufacture' => $item["dom"],
                    'best_before_date' => $item["bbf"],
                    'total_quantity' => $item["total_quantity"],
                    'number_of_barcodes' => $item["number_of_barcodes"],
                ];

                if ($purchaseOrderExists) {
                    $grnPurchaseOrderSubData[] = [
                        'grn_purchase_order_id' => $grnPurchaseOrder->id,
                        'purchase_number' => $purchaseOrderExists->purchase_number,
                        'item_id' => $item["item_id"]
                    ];
                }

                $item = Item::find($item["item_id"]);

                $totalQuantity = $item["total_quantity"];
                $spq = $item["spq"];
                $fullBarcodes = $totalQuantity / $spq;
                $remainder = fmod((float)$totalQuantity, (float)$spq);
                $numberOfBarcodes = $item["number_of_barcodes"];
                $quantityPerBarcode = $item["item_type"] == "FG" ? $spq : ($totalQuantity / $numberOfBarcodes);
                $itemPrice = $item->price;
                $baseQuantity = $item["item_type"] === "RM" ? $totalQuantity / $numberOfBarcodes : $spq;
                $itemUom = $item->uom_id;
                for ($i = 0; $i < $numberOfBarcodes; $i++) {

                    $barcode = BarcodeGenerator::nextNumber($prefix);

                    if ($item["item_type"] === "FG") {
                        $quantityPerBarcode = ($i == $numberOfBarcodes - 1 && $remainder > 0) ? $remainder : $spq;
                    } else {
                        $quantityPerBarcode = ($i == $numberOfBarcodes - 1)
                            ? ($totalQuantity - ($baseQuantity * ($numberOfBarcodes - 1)))
                            : $baseQuantity;
                    }

                    $barcodeData[] = [
                        'serial_number' => $barcode,
                        'transaction_id' => $grn->id,
                        'transaction_type' => '1',
                        'branch_id' => $branchId,
                        'location_id' => $request->location_id,
                        'item_id' => $item["item_id"],
                        'date_of_manufacture' => $item["dom"],
                        'best_before_date' => $item["bbf"],
                        'batch_number' => $batchNumber,
                        'price' => $itemPrice,
                        // 'total_price' => $totalPrice,
                        'net_weight' => $quantityPerBarcode,
                        'grn_net_weight' => $quantityPerBarcode,
                        'spq_quantity' => $quantityPerBarcode,
                        'grn_spq_quantity' => $quantityPerBarcode,
                        'uom_id' => $itemUom,
                        'status' => '-1',
                        'user_id' => $userid,
                        'qc_approval_status' => '0',
                    ];

                    $itemName = $item->name;

                    $contents[] = [
                        'transaction_number' => $grn->grn_number,
                        'barcode' => $barcode,
                        'item_name' => $itemName,
                        'spq_quantity' => $quantityPerBarcode
                    ];
                }
            }
            // dd($barcodeData);
            // dd($grnSubData, $grnPurchaseOrderSubData, $barcodeData);
            if (!empty($grnSubData)) {
                GrnSub::insert($grnSubData);
            }

            // Bulk insert GrnPurchaseOrderSub records
            if (!empty($grnPurchaseOrderSubData)) {
                GrnPurchaseOrderSub::insert($grnPurchaseOrderSubData);
            }

            // Bulk insert Barcode records
            if (!empty($barcodeData)) {
                Barcode::insert($barcodeData);
            }
            // dd($purchaseOrderSubUpdates);
            if (!empty($purchaseOrderSubUpdates)) {
                foreach ($purchaseOrderSubUpdates as $updateData) {

                    PurchaseOrderSub::where('id', $updateData['id'])->update([
                        'picked_quantity' => $updateData['picked_quantity']
                    ]);

                    // Re-fetch the record to verify if it's now fully picked
                    $updatedSub = PurchaseOrderSub::find($updateData['id']);

                    if ($updatedSub->picked_quantity >= $updatedSub->quantity) {
                        $updatedSub->update(['status' => 1]);
                    }
                }
            }

            if ($purchaseOrderExists) {
                $dataCheck = PurchaseOrderSub::where('purchase_order_id', $purchaseOrderExists->id)
                    ->where('status', '0')
                    ->count();

                if ($dataCheck == 0) {
                    // If there are no PurchaseOrderSub with status 0, update the PurchaseOrder status to 1
                    PurchaseOrder::where('id', $purchaseOrderExists->id)
                        ->where('status', "0")
                        ->update(['status' => '1']);
                }
            }
            // dd($purchaseOrderExists->purchase_number);
            if ($request->is_purchase != null) {
                Grn::where('id', $grn->id)
                    ->update([
                        'purchase_number' => $purchaseOrderExists->purchase_number,
                    ]);
            }

            // if ($request->has('prn')) {
            //     $grnPrintBarcode = $grn->id;
            //     $printableContent = $this->generateDPL($content);
            // } else {
            // }

            DB::commit();

            Alert::toast('GRN saved with Number ' . $grn->grn_number, 'success')->autoClose(3000);
            if ($request->has('prn')) {
                Session::put('contents', $contents);
                return redirect()->back();
            } else {
                return redirect()->back();
                // return back()->with('print_barcode', $print_barcode);
            }
        } catch (Exception $e) {
            DB::rollBack();
            dd($e);
        }
    }

    public function grnExcelUpload(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'excel_file' => 'required|file|mimes:xlsx,xls'
        ]);

        $current = date('Y-m-d_H-i-s');

        try {
            DB::beginTransaction();

            $userid = Auth::user()->id;
            $batchNumber = 'B' . date("ymd");
            $purchaseNumber = null;

            $data = $this->getExcelData($request->excel_file);
            // dd($data);
            if ($data['error'] !== '') {

                $errors = array_filter(
                    explode('|', rtrim($data['error'], '|')),
                    fn($e) => trim($e) !== ''
                );
                dd($errors);
                return redirect()->route('grn.index')->withErrors($errors);
            }

            if (!empty($data["purchase_id"])) {
                $purchaseOrder = PurchaseOrder::select('purchase_number')->find($data["purchase_id"]);
                $purchaseNumber = $purchaseOrder->purchase_number;
            }

            $location = Location::find($data["location_id"]);
            $branchId = $location->branch_id;
            $prefix = $location->prefix;

            $grn = Grn::create([
                'grn_number' => Grn::grnNumber(),
                'purchase_number' => $purchaseNumber,
                'invoice_number' => $data["invoice_number"],
                'invoice_date' => $data["invoice_date"],
                'vendor_id' => $data["vendor_id"],
                'location_id' => $data["location_id"],
                'remarks' => $data["remarks"],
                'branch_id' => $branchId,
            ]);

            if ($purchaseNumber) {
                $grnPurchaseOrder = GrnPurchaseOrder::create([
                    'grn_id' => $grn->id,
                    'user_id' => $userid
                ]);
            }

            foreach ($data["items"] as $item) {

                $purchaseOrderExists = PurchaseOrder::where('id', '=', $data["purchase_id"])->where('status', 0)->first();

                if ($purchaseOrderExists) {
                    $purchaseOrderSub = PurchaseOrderSub::where('purchase_order_id', $purchaseOrderExists->id)
                        ->where('item_id', $item["item_id"])
                        ->where('status', '0')
                        ->first();

                    $purchaseOrderSub->update([
                        'picked_quantity' => $purchaseOrderSub->picked_quantity + $item["total_quantity"]
                    ]);
                }

                $grnSub = GrnSub::create([
                    'grn_id' => $grn->id,
                    'item_id' => $item["item_id"],
                    'batch_number' => $batchNumber,
                    'quantity' => $item["total_quantity"],
                    'date_of_manufacture' => $item["date_of_manufacture"],
                    'best_before_date' => $item["best_before_date"],
                    'total_quantity' => $item["total_quantity"],
                    'number_of_barcodes' => $item["number_of_barcodes"],
                ]);

                if ($purchaseOrderExists) {
                    $grnPurchaseOrderSub = GrnPurchaseOrderSub::create([
                        'grn_purchase_order_id' => $grnPurchaseOrder->id,
                        'purchase_number' => $purchaseOrderExists->purchase_number,
                        'item_id' => $item["item_id"]
                    ]);
                }

                $numberOfBarcodes = $item["number_of_barcodes"];
                $totalQuantity = $item["total_quantity"];
                $spq = $item["spq_quantity"];
                $fullBarcodes = floor($totalQuantity / $spq);
                $remainder = $totalQuantity % $spq;
                $itemPrice = Item::find($item["item_id"])->price;
                $totalPrice = (int)$item["spq_quantity"] * $itemPrice;
                $quantityPerBarcode = $item["item_type"] == "FG" ? $spq : ($totalQuantity / $numberOfBarcodes);
                $baseQuantity = $item["item_type"] === "RM" ? $totalQuantity / $numberOfBarcodes : $spq;

                for ($i = 0; $i < $numberOfBarcodes; $i++) {

                    $barcode = BarcodeGenerator::nextNumber($prefix);

                    if ($item["item_type"] === "FG") {
                        $quantityPerBarcode = ($i == $numberOfBarcodes - 1 && $remainder > 0) ? $remainder : $spq;
                    } else {
                        $quantityPerBarcode = ($i == $numberOfBarcodes - 1)
                            ? ($totalQuantity - ($baseQuantity * ($numberOfBarcodes - 1)))
                            : $baseQuantity;
                    }

                    Barcode::create([
                        'serial_number' => $barcode,
                        'transaction_id' => $grn->id,
                        'transaction_type' => '1',
                        'branch_id' => $branchId,
                        'location_id' => $data["location_id"],
                        'item_id' => $item["item_id"],
                        'date_of_manufacture' => $item["date_of_manufacture"],
                        'best_before_date' => $item["best_before_date"],
                        'batch_number' => $batchNumber,
                        'price' => $itemPrice,
                        'net_weight' => $quantityPerBarcode,
                        'grn_net_weight' => $quantityPerBarcode,
                        'spq_quantity' => $quantityPerBarcode,
                        'grn_spq_quantity' => $quantityPerBarcode,
                        'status' => '-1',
                        'user_id' => $userid,
                        'qc_approval_status' => '0',
                    ]);

                    if ($purchaseOrderExists) {

                        PurchaseOrderSub::where('purchase_order_id', $purchaseOrderExists->id)
                            ->where('item_id', $item["item_id"])
                            ->where('status', '0')
                            ->whereColumn('quantity', 'picked_quantity')
                            ->update(['status' => '1']);

                        $dataCheck = PurchaseOrderSub::where('purchase_order_id', $purchaseOrderExists->id)
                            ->where('status', '0')
                            ->count();

                        if ($dataCheck == 0) {
                            PurchaseOrder::where('id', $purchaseOrderExists->id)
                                ->where('status', "0")
                                ->update(['status' => '1']);
                        }
                    }
                }
            }

            $fileName = $current . '_' . $request->file('excel_file')->getClientOriginalName();

            $request->file('excel_file')->storeAs('excel_uploads/transaction_uploads/grn_uploads', $fileName, 'public');
            DB::commit();
            Alert::toast('Grn saved with Number ' . $grn->grn_number, 'success')->autoClose(3000);

            $grnPrintableContent = $grn->id;
            return back()->with('print_barcode', $grnPrintableContent);
            // return redirect()->back();
        } catch (Exception $e) {
            DB::rollBack();
            dd($e);
            Log::error('Grn Excel Upload Error: ' . $e->getMessage());
            Alert::toast('An error occurred while grn excel upload.', 'error')->autoClose(3000);
            return redirect()->route('grn.index');
        }
    }

    private function getExcelData($file)
    {
        $sheet = Excel::toArray([], $file);
        $rows = $sheet[0] ?? [];
        // dd($rows);
        $data = [
            'error' => '',
            'location_id' => '',
            'vendor_id' => '',
            'invoice_number' => '',
            'invoice_date' => '',
            'purchase_id' => '',
            'remarks' => '',
            'items' => [],
        ];

        $grn = [
            'vendor_name' => $rows[0][1] ?? null,
            'invoice_number' => $rows[0][3] ?? null,
            'purchase_number' => $rows[0][5] ?? null,
            'location' => $rows[0][7] ?? null,
            'vendor_code' => $rows[1][1] ?? null,
            'invoice_date' => $rows[1][3] ?? null,
            'remarks' => $rows[1][5] ?? null,
        ];

        foreach (['location', 'vendor_name', 'vendor_code'] as $key) {
            if (empty($grn[$key])) {
                $label = ucwords(str_replace('_', ' ', $key));
                $data['error'] .= $label . " is required|";
            }
        }

        if ($data['error'] !== '') return $data;

        $data['invoice_number'] = $grn['invoice_number'];
        $data['invoice_date'] = $grn['invoice_date'];
        $data['remarks'] = $grn['remarks'];

        $vendor = Vendor::where('name', $grn['vendor_name'])->where('vendor_code', $grn['vendor_code'])->first();
        if (!$vendor) {
            $data['error'] .= "Vendor does not exist|";
        } else {
            $data['vendor_id'] = $vendor->id;
        }

        $location = Location::where('name', $grn['location'])->first();
        if (!$location) {
            $data['error'] .= "Location does not exist|";
        } else {
            $data['location_id'] = $location->id;
        }

        $purchaseOrder = PurchaseOrder::where('purchase_number', $grn['purchase_number'])->first();
        if ($purchaseOrder) {
            if ($purchaseOrder->vendor_id != $data["vendor_id"]) {
                $data["error"] .= "This Purchase Number order does not belongs to Vendor.|";
            } else {
                $data['purchase_id'] = $purchaseOrder->id;
            }
        }
        // dd($data);
        if ($data['error'] !== '') return $data;

        for ($i = 4; $i < count($rows); $i++) {
            $row = $rows[$i];

            if (!array_filter($row)) {
                continue;
            }

            $itemArray = [
                'item_name' => $row[0] ?? '',
                'date_of_manufacture' => is_numeric($row[1]) ? Date::excelToDateTimeObject($row[1])->format('Y-m-d') : $row[1],
                'best_before_date' => is_numeric($row[2]) ? Date::excelToDateTimeObject($row[2])->format('Y-m-d') : $row[2],
                'total_quantity' => $row[3] ?? '',
                'spq_quantity' => null,
                'number_of_barcodes' => $row[4] ?? null,
                'item_type' => null,
            ];

            foreach (['item_name', 'date_of_manufacture', 'best_before_date', 'total_quantity'] as $key) {
                if (empty($itemArray[$key])) {
                    $label = ucwords(str_replace('_', ' ', $key));
                    $data['error'] .= $label . " is required|";
                    return $data;
                }
            }

            $item = Item::where('name', $itemArray['item_name'])
                ->first();

            if (!$item) {
                $data['error'] .= 'Row ' . ($i + 1) . " : Item does not exist|";
                return $data;
            }

            $belongs = DB::table('item_location')
                ->where('item_id', $item->id)
                ->where('location_id', $location->id)
                ->exists();

            if (!$belongs) {
                $data['error'] .= 'Row ' . ($i + 1) . " : Item does not exist in this location|";
                return $data;
            }

            if (!empty($data['purchase_id'])) {
                $existsInPurchase = PurchaseOrderSub::where('purchase_order_id', $data['purchase_id'])
                    ->where('item_id', $item->id)
                    ->exists();

                if (!$existsInPurchase) {
                    $data['error'] .= 'Row ' . ($i + 1) . " : Item not found in the selected purchase order|";
                    return $data;
                }

                $purchaseOrderSub = PurchaseOrderSub::where('purchase_order_id', $data['purchase_id'])
                    ->where('item_id', $item->id)
                    ->first();
                // dd($purchaseOrderSub->quantity, $itemArray['total_quantity']);
                if ($purchaseOrderSub && ($purchaseOrderSub->quantity - $purchaseOrderSub->picked_quantity) < $itemArray['total_quantity']) {
                    $data['error'] .= 'Row ' . ($i + 1) . " : Not enough quantity in the purchase order|";
                    return $data;
                }
            }

            $spqQuantity = null;
            $totalQuantity = null;
            // dd($itemArray['total_quantity']);
            if (isset($itemArray['total_quantity']) && is_numeric($itemArray['total_quantity'])) {
                $totalQuantity = (int)$itemArray['total_quantity'];
                $spqQuantity = (int)$item->spq_quantity;

                if ($totalQuantity < $spqQuantity) {
                    $data['error'] .= 'Row ' . ($i + 1) . " : Total Quantity can not be less than spq(" . $spqQuantity . ").|";
                    return $data;
                }

                $itemArray['spq_quantity'] = $spqQuantity;
            }


            if ($data['error'] !== '') return $data;

            $data['items'][] = [
                'item_id' => $item->id,
                'date_of_manufacture' => $itemArray['date_of_manufacture'],
                'best_before_date' => $itemArray['best_before_date'],
                'total_quantity' => $itemArray['total_quantity'],
                'spq_quantity' => $itemArray['spq_quantity'],
                'number_of_barcodes' => $itemArray['number_of_barcodes'],
                'item_type' => $item->item_type,
            ];
        }

        return $data;
    }

    public function edit() {}

    public function printBarcode()
    {
        $contents = Session::pull('contents');
        // dd($contents);
        if (!$contents) {
            abort(404, 'No barcode data found');
        }
        // dd($grn);
        return view('transactions.grn.printbarcode', compact('contents'));
    }

    protected function generateDPL($contents = [])
    {

        $dpl_data = $this->readDPL();

        $printable_data1 = '';

        foreach ($contents as $content) {
            $patterns = array();
            $replacements = array();
            foreach ($content as $k => $v) {
                $patterns[] = "[$k]";
                $replacements[] = (string)$v;
            }
            $assigned = str_replace($patterns, $replacements, $dpl_data);

            // remove unwanted tags
            $assigned = preg_replace("/(\[\w+\])/", '', $assigned);
            $printable_data1 .= $assigned;
        }
        // dd($printable_data1);
        return $printable_data1;
    }

    protected function readDPL()
    {

        // $setting = Setting::select('label', 'type')->where('type', 1)->first();

        // if ($setting && $setting->type == 1) {
        //     if ($setting->label) {
        //         $file = public_path() . '/prn_files/' . $setting->label;
        //         $contents = fopen($file, 'r');
        //         $contents1 = fread($contents, filesize($file));
        //     } else {
        //         $file = public_path() . '/_barcode_label_29x19.txt';
        //         $contents = fopen($file, 'r');
        //         $contents1 = fread($contents, filesize($file));
        //     }
        // }
        // return $contents1;
        return 123;
    }
}
