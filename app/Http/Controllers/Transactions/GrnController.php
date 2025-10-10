<?php

namespace App\Http\Controllers\Transactions;

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
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use RealRashid\SweetAlert\Facades\Alert;

class GrnController extends Controller
{
    public function index(){
        $grnNumber = Grn::grnNumber();
        $batchNumber = 'B' . date("ymd");
        $vendors = Vendor::get(['id', 'name']);
        $locations = Location::get(['id', 'name']);
        $categories = Category::get(['id', 'name']);
        return view('transactions.grn.index', compact('grnNumber', 'batchNumber', 'vendors', 'locations', 'categories'));
    }

    public function store(Request $request){
        dd($request->all());
        try{

            DB::beginTransaction();

            $userid = Auth::user()->id;
            $batchNumber = 'B' . date("ymd");
            $purchaseNumber = null;

            if($request->purchase_number){
                $purchaseOrder = PurchaseOrder::select('purchase_number')->find($request->purchase_number);
                $purchaseNumber = $purchaseOrder->purchase_number;
            }

            $branchId = Location::find($request->location_id)->branch->id;
            // dd($branchId);
            $grn = Grn::create([
                'grn_number' => Grn::grnNumber(),
                'purchase_number' => $purchaseNumber,
                'invoice_number' => $request->invoice_number,
                'invoice_date' => $request->invoice_date,
                'remarks' => $request->remarks,
                'location_id' => $request->location_id,
                'vendor_id' => $request->vendor_id,
                'branch_id' => $branchId,
            ]);

            if($request->is_purchase == 1){
                $grnPurchaseOrder = GrnPurchaseOrder::create([
                    'grn_id' => $grn->id,
                    'user_id' => $userid
                ]);

            }

            foreach($request->items as $item){

                if($item["purchase_id"] != ""){

                    $purchaseOrderExists = PurchaseOrder::where('id', '=', $item["purchase_id"])->where('status', 0)->first();

                    if($purchaseOrderExists){
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
                        'date_of_manufacture' => $item["dom"],
                        'best_before_date' => $item["bbf"],
                        'total_quantity' => $item["total_quantity"],
                        'number_of_barcodes' => $item["number_of_barcodes"],
                    ]);

                    if($purchaseOrderExists){
                        $grnPurchaseOrderSub = GrnPurchaseOrderSub::create([
                            'grn_purchase_order_id' => $grnPurchaseOrder->id,
                            'purchase_number' => $purchaseOrderExists->purchase_number,
                            'item_id' => $item["item_id"]
                        ]);
                    }

                    $numberOfBarcodes = $item["number_of_barcodes"];
                    $totalPrice = (int)$item["spq"] * $item["price"];
                    // dd($item["price"]);
                    while($numberOfBarcodes--){

                        $barcode = Barcode::nextNumber($request->location_id);

                        Barcode::create([
                            'serial_number' => $barcode,
                            'grn_id' => $grn->id,
                            'branch_id' => $branchId,
                            'location_id' => $request->location_id,
                            'item_id' => $item["item_id"],
                            'date_of_manufacture' => $item["dom"],
                            'best_before_date' => $item["bbf"],
                            'batch_number' => $batchNumber,
                            'price' => $item["price"],
                            'total_price' => $totalPrice,
                            'spq_quantity' => $item["spq"],
                            'net_weight' => $item["total_quantity"],
                            'grn_spq_quantity' => $item["spq"],
                            'grn_net_weight' => $item["total_quantity"],
                            'status' => '-1',
                            'user_id' => $userid,
                            'qc_approval_status' => '0',
                        ]);

                        if($purchaseOrderExists){
                            PurchaseOrderSub::where('purchase_order_id', $purchaseOrderExists->id)
                                ->where('item_id', $item["item_id"])
                                ->where('status', '0')
                                ->whereColumn('quantity', 'picked_quantity')
                                ->update([
                                    'status' => '1'
                                ]);

                            $dataCheck = PurchaseOrderSub::where('purchase_order_id', $purchaseOrderExists->id)->where('status', '0')->count();
                            if ($dataCheck == 0) {
                                PurchaseOrder::where('id', $purchaseOrderExists->id)
                                    ->where('status', "0")
                                    ->update([
                                        'status' => '1'
                                    ]);
                            }

                        }

                    }
                }
            }

            // dd($purchaseOrderExists->purchase_number);
            if ($request->is_purchase != null) {
                Grn::where('id', $grn->id)
                    ->update([
                        'purchase_number' => $purchaseOrderExists->purchase_number,
                    ]);
            }

            // if ($request->prn) {
            //     $printable_content = $this->generateDPL($content);
            //     Alert::toast('GRN saved with Number ' . $grnno, 'success')->autoClose(3000);
            // } else {
                // $printable_content = $grn_id;

                DB::commit();
                Alert::toast('GRN saved with Number ' . $grn->grn_number, 'success')->autoClose(3000);
                return redirect()->back();
            // }

        } catch(Exception $e){
            DB::rollBack();
            dd($e);
        }
    }

    public function grnExcelUpload(Request $request){
        // dd($request->all());
        $request->validate([
            'excel_file' => 'required|file|mimes:xlsx,xls'
        ]);

        $current = date('Y-m-d_H-i-s');

        try{
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

                return redirect()->route('purchase-order.index')->withErrors($errors);
            }

            if(!empty($data["purchase_id"])){
                $purchaseOrder = PurchaseOrder::select('purchase_number')->find($data["purchase_id"]);
                $purchaseNumber = $purchaseOrder->purchase_number;
            }

            $branchId = Location::find($data["location_id"])->branch->id;
            // dd($data["remarks"]);
            $grn = Grn::create([
                'grn_number' => Grn::grnNumber(),
                'purchase_number' => $purchaseNumber,
                'invoice_number' => $data["invoice_number"],
                'invoice_date' => $data["invoice_date"],
                'vendor_id' => $data["vendor_id"],
                'location_id' => $data["location_id"],
                'grn_type' => $data["grn_type"],
                'remarks' => $data["remarks"],
                'branch_id' => $branchId,
            ]);

            if($purchaseNumber){
                $grnPurchaseOrder = GrnPurchaseOrder::create([
                    'grn_id' => $grn->id,
                    'user_id' => $userid
                ]);
            }

            foreach($data["items"] as $item){

                $purchaseOrderExists = PurchaseOrder::where('id', '=', $data["purchase_id"])->where('status', 0)->first();

                if($purchaseOrderExists){
                    $purchaseOrderSub = PurchaseOrderSub::where('purchase_order_id', $purchaseOrderExists->id)
                    ->where('item_id', $item["item_id"])
                    ->where('status', '0')
                    ->first();

                    $purchaseOrderSub->update([
                        'picked_quantity' => $purchaseOrderSub->picked_quantity + $item["total_quantity"]
                    ]);
                }

                $itemData = Item::find($item["item_id"]);

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

                if($purchaseOrderExists){
                    $grnPurchaseOrderSub = GrnPurchaseOrderSub::create([
                        'grn_purchase_order_id' => $grnPurchaseOrder->id,
                        'purchase_number' => $purchaseOrderExists->purchase_number,
                        'item_id' => $item["item_id"]
                    ]);
                }

                $numberOfBarcodes = $item["number_of_barcodes"];
                $totalPrice = (int)$item["spq_quantity"] * $item["price"];

                while($numberOfBarcodes--){

                    $barcode = Barcode::nextNumber($data["location_id"]);

                    Barcode::create([
                        'serial_number' => $barcode,
                        'grn_id' => $grn->id,
                        'branch_id' => $branchId,
                        'location_id' => $data["location_id"],
                        'item_id' => $item["item_id"],
                        'date_of_manufacture' => $item["date_of_manufacture"],
                        'best_before_date' => $item["best_before_date"],
                        'batch_number' => $batchNumber,
                        'price' => $item["price"],
                        'total_price' => $totalPrice,
                        'spq_quantity' => $item["spq_quantity"],
                        'net_weight' => $item["total_quantity"],
                        'grn_spq_quantity' => $item["spq_quantity"],
                        'grn_net_weight' => $item["total_quantity"],
                        'status' => '-1',
                        'user_id' => $userid,
                        'qc_approval_status' => '0',
                    ]);

                    if($purchaseOrderExists){
                        PurchaseOrderSub::where('purchase_order_id', $purchaseOrderExists->id)
                            ->where('item_id', $item["item_id"])
                            ->where('status', '0')
                            ->whereColumn('quantity', 'picked_quantity')
                            ->update([
                                'status' => '1'
                            ]);

                        $dataCheck = PurchaseOrderSub::where('purchase_order_id', $purchaseOrderExists->id)->where('status', '0')->count();
                        if ($dataCheck == 0) {
                            PurchaseOrder::where('id', $purchaseOrderExists->id)
                                ->where('status', "0")
                                ->update([
                                    'status' => '1'
                                ]);
                        }

                    }

                }

            }

            $fileName = $current . '_' . $request->file('excel_file')->getClientOriginalName();

            $request->file('excel_file')->storeAs('excel_uploads/transaction_uploads/grn_uploads', $fileName, 'public');
            DB::commit();
            Alert::toast('Grn saved with Number ' . $grn->grn_number, 'success')->autoClose(3000);
            return redirect()->back();
        } catch (Exception $e) {
            DB::rollBack();
            dd($e);
            Log::error('Grn Excel Upload Error: ' . $e->getMessage());
            Alert::toast('An error occurred while grn excel upload.', 'error')->autoClose(3000);
            return redirect()->route('grn.index');
        }
    }

    private function getExcelData($file){
        $sheet = Excel::toArray([], $file);
        $rows = $sheet[0] ?? [];
        $data = [
            'error' => '',
            'grn_type' => '',
            'location_id' => '',
            'vendor_id' => '',
            'invoice_number' => '',
            'invoice_date' => '',
            'purchase_id' => '',
            'remarks' => '',
            'items' => [],
        ];

        $grn = [
            'grn_type' => $rows[0][1] ?? null,
            'location' => $rows[1][1] ?? null,
            'vendor_name' => $rows[0][3] ?? null,
            'vendor_code' => $rows[1][3] ?? null,
            'invoice_number' => $rows[0][5] ?? null,
            'invoice_date' => $rows[1][5] ?? null,
            'purchase_number' => $rows[0][7] ?? null,
            'remarks' => $rows[1][7] ?? null,
        ];

        foreach (['grn_type', 'location', 'vendor_name', 'vendor_code'] as $key) {
            if (empty($grn[$key])) {

                $label = ucwords(str_replace('_', ' ', $key));
                $data['error'] .= $label . " is required|";
            }
        }


        if ($data['error'] !== '') return $data;

        $data['grn_type'] = $grn['grn_type'];
        $data['invoice_number'] = $grn['invoice_number'];
        $data['invoice_date'] = $grn['invoice_date'];
        $data['remarks'] = $grn['remarks'];

        $vendor = Vendor::where('name', $grn['vendor_name'])->where('vendor_code', $grn['vendor_code'])->first();
        if(!$vendor){
            $data['error'] .= "Vendor does not exist|";
        } else {
            $data['vendor_id'] = $vendor->id;
        }

        $location = Location::where('name', $grn['location'])->first();
        if(!$location){
            $data['error'] .= "Location does not exist|";
        } else {
            $data['location_id'] = $location->id;
        }

        $purchaseNumber = PurchaseOrder::where('purchase_number', $grn['purchase_number'])->first();
        if($purchaseNumber){
            $data['purchase_id'] = $purchaseNumber->id;
        }
        // dd($data);
        if ($data['error'] !== '') return $data;

        for($i = 4; $i < count($rows); $i++){
            $row = $rows[$i];

            if (!array_filter($row)) {
                continue;
            }

            $itemArray = [
                'item_name' => $row[0] ?? '',
                'item_code' => $row[1] ?? '',
                'price' => $row[2] ?? '',
                'date_of_manufacture' => is_numeric($row[3]) ? Date::excelToDateTimeObject($row[3])->format('Y-m-d') : $row[3],
                'best_before_date' => is_numeric($row[4]) ? Date::excelToDateTimeObject($row[4])->format('Y-m-d') : $row[4],
                'total_quantity' => $row[5] ?? '',
                'spq_quantity' => null,
                'number_of_barcodes' => null,
            ];

            foreach(['item_name', 'item_code', 'price', 'date_of_manufacture', 'best_before_date', 'total_quantity'] as $key){
                if (empty($itemArray[$key])) {
                    $label = ucwords(str_replace('_', ' ', $key));
                    $data['error'] .= $label . " is required|";
                }
            }

            $item = Item::where('name', $itemArray['item_name'])
                ->where('item_code', $itemArray['item_code'])
                ->first();

            if(!$item){
                $data['error'] .= 'Row ' . ($i + 1) . " : Item does not exist|";
                return $data;
            }

            $spqQuantity = null;
            $totalQuantity = null;
            if (isset($itemArray['total_quantity']) && is_numeric($itemArray['total_quantity'])) {
                $totalQuantity = (int)$itemArray['total_quantity'];
                $spqQuantity = (int)$item->spq_quantity;

                if ($spqQuantity > 0 && $totalQuantity % $spqQuantity !== 0) {
                    $data['error'] .= 'Row ' . ($i + 1) . " : Total Quantity must be divisible by SPQ (" . $spqQuantity . ").|";
                    return $data;
                }
                $itemArray['spq_quantity'] = $spqQuantity;
                $itemArray['number_of_barcodes'] = $totalQuantity * $spqQuantity;
            }

            if (!empty($data['purchase_id'])) {
                $existsInPurchase = PurchaseOrderSub::where('purchase_order_id', $data['purchase_id'])
                    ->where('item_id', $item->id)
                    ->exists();

                if (!$existsInPurchase) {
                    $data['error'] .= 'Row ' . ($i + 1) . " : Item not found in the selected purchase order|";
                    return $data;
                }
            }

            if($item->item_type != $grn['grn_type']){
                $data['error'] .= 'Row ' . ($i + 1) . " : Item Type is Not ". $grn['grn_type'] ."|";
            }


            if ($data['error'] !== '') return $data;

            $data['items'][] = [
                    'item_id' => $item->id,
                    'price' => $itemArray['price'],
                    'date_of_manufacture' => $itemArray['date_of_manufacture'],
                    'best_before_date' => $itemArray['best_before_date'],
                    'total_quantity' => $itemArray['total_quantity'],
                    'spq_quantity' => $itemArray['spq_quantity'],
                    'number_of_barcodes' => $itemArray['number_of_barcodes'],
                ];
        }

        return $data;
    }
}
