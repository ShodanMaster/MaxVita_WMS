<?php

namespace App\Http\Controllers\Ajax;

use App\Http\Controllers\Controller;
use App\Models\Barcode;
use App\Models\Bin;
use App\Models\Grn;
use App\Models\GrnSub;
use App\Models\StorageScan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StorageScanAjaxController extends Controller
{
    public function getGrnDetails(Request $request){
        // dd($request->all());
        if($request->ajax()){
            $grn = Grn::find($request->id);

            $data = [

                    'grn_number' => $grn->grn_number,
                    'vendor_name' => $grn->vendor->name,
                    'invoice_number' => $grn->invoice_number,
                    'invoice_date' => $grn->invoice_date,
                    'remark' => $grn->remark,
                ];

            return response()->json($data);
        }
    }

    public function storageScan(Request $request){
        if($request->ajax()){

            $user = Auth::user();
            $barcode = Barcode::where('serial_number', $request->barcode)->where('transaction_type', 1)->first();

            if(!$barcode){
                return response()->json([
                    'status' => 404,
                    'message' => 'Barcode not found.'
                ]);
            }

            switch ($barcode->status) {
                case 1:
                    return response()->json([
                        'status' => 409,
                        'message' => 'Already Scanned!'
                    ]);
                case 2:
                    return response()->json([
                        'status' => 409,
                        'message' => 'Already Dispatched!'
                    ]);
                case 8:
                    return response()->json([
                        'status' => 404,
                        'message' => 'Does not Exist!'
                    ]);
            }

            $grnSub = GrnSub::where('grn_id', $request->grn_id)
                                ->where('batch_number', $barcode->batch_number)
                                ->where('item_id', $barcode->item_id)
                                ->first();

            if (!$grnSub) {
                return response()->json([
                    'status' => 404,
                    'message' => 'GRN data not found.'
                ]);
            }
            // dd($barcode->transaction_id, $grnSub->grn_id);
            if($barcode->transaction_id != $grnSub->grn_id){
                return response()->json([
                    'status' => 404,
                    'message' => 'Item does not belongs to this GRN.'
                ]);
            }

            // dd($grnSub->total_quantity, $grnSub->scanned_quantity);
            if($grnSub->total_quantity < $grnSub->scanned_quantity){
                return response()->json([
                    'status' => 422,
                    'message' => 'Storage Quantity Exceeded!'
                ]);
            }

            $bin = Bin::where('bin_code', $request->bin)->first();

            if(!$bin){
                return response()->json([
                    'status' => 404,
                    'message' => 'Bin not found.'
                ]);
            }

            DB::beginTransaction();

            $storageScan = StorageScan::create([
                'barcode' => $barcode->serial_number,
                'item_id' => $barcode->item_id,
                'grn_id' => $grnSub->grn_id,
                'bin_id' => $bin->id,
                'scanned_quantity' => $barcode->net_weight,
                'batch_number' => $barcode->batch_number,
                'net_weight' => $barcode->net_weight,
                'user_id' => $user->id,
            ]);

            $barcode->update([
                'branch_id' => $user->branch_id,
                'location_id' => $user->location_id,
                'bin_id' => $bin->id,
                'status' => '1',
            ]);

            // dd($grnSub->scanned_quantity, $barcode->net_weight);
            $grnSub->update([
                'scanned_quantity' => $grnSub->scanned_quantity + $barcode->net_weight,
            ]);

            $grnSubUpdated = GrnSub::where('grn_id', $request->grn_id)
                                ->where('batch_number', $barcode->batch_number)
                                ->where('item_id', $barcode->item_id)
                                ->whereNot('grn_status', 1)
                                ->first();

            if($grnSubUpdated->scanned_quantity == $grnSubUpdated->total_quantity){
                $grnSubUpdated->update(['grn_status' => 1]);
            }

            $grnSubStatus = GrnSub::where('grn_id', $grnSub->grn_id)->where('grn_status', 0)->exists();

            $grn = Grn::find($grnSub->grn_id);

            $scanComplete = false;
            if(!$grnSubStatus){
                $grn->update([
                    'status' => 1
                ]);
                $scanComplete = true;
            }

            DB::commit();

            return response()->json([
                'status' => 200,
                'scan_complete' => $scanComplete,
                'message' => 'Storage Scan Successful',
            ]);
        }
    }

    public function fetchStorageScanDetails(Request $request){
        if($request->ajax()){
            $grnSubs = GrnSub::with('item')->where('grn_id', $request->grn_id)->get();

            $data = $grnSubs->map(function($sub){
                return [
                    'item' => $sub->item->item_code . "/" . $sub->item->name,
                    'balance_quantity' => $sub->total_quantity - $sub->scanned_quantity,
                    'scanned_quantity' => $sub->scanned_quantity,
                ];
            });

            return response()->json( $data);
        }
    }
}
