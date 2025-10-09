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
        // dd($request->all());
        try{

            DB::beginTransaction();

            $userid = Auth::user()->id;
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
                            '' => $purchaseOrderSub->picked_quantity + $item["total_quantity"]
                        ]);
                    }else{
                        $batch = $item["batch_no"];
                    }

                    // $item = Item::findOrFail($item["item_id"]);

                    $grnSub = GrnSub::create([
                        'grn_id' => $grn->id,
                        'item_id' => $item["item_id"],
                        'batch_number' => $item["batch_no"],
                        'quantity' => $item["total_quantity"],
                        'date_of_manufacture' => $item["dom"],
                        'best_before_date' => $item["bbf"],
                        'total_quantity' => $item["total_quantity"],
                        'number_of_barcodes' => $item["number_of_barcodes"],
                    ]);

                    // dd($purchaseOrderExists->purchase_number);
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
                            'batch_number' => $request->location_id,
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
}
