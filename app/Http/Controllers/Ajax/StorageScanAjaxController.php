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
            $grn = Grn::where('grn_number', $request->grn_number)->first();

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
            $barcode = Barcode::where('serial_number', $request->barcode)->first();

            if(!$barcode){
                return response()->json([
                    'status' => 404,
                    'message' => 'Barcode not found.'
                ]);
            }
            elseif($barcode->status == 1){
                return response()->json([
                    'status' => 500,
                    'message' => 'Already Scanned!'
                ]);
            }
            elseif($barcode->status == 2){
                return response()->json([
                    'status' => 500,
                    'message' => 'Already Despatched!'
                ]);
            }
            elseif($barcode->status == 8){
                return response()->json([
                    'status' => 500,
                    'message' => 'Does not Exist!'
                ]);
            }

            $grnSub = GrnSub::where('grn_id', $barcode->grn_id)
                                ->where('batch_number', $barcode->batch_number)
                                ->where('item_id', $barcode->item_id)
                                ->first();

            if (!$grnSub) {
                return response()->json([
                    'status' => 404,
                    'message' => 'GRN data not found.'
                ]);
            }

            if($barcode->grn_id != $grnSub->grn_id){
                return response()->json([
                    'status' => 404,
                    'message' => 'Barcode does not belong to this GRN.'
                ]);
            }

            // dd($grnSub->total_quantity, $grnSub->scanned_quantity);
            if($grnSub->total_quantity < $grnSub->scanned_quantity){
                return response()->json([
                    'status' => 500,
                    'message' => 'Storage Quantity Exceeded!'
                ]);
            }

            $bin = Bin::where('name', $request->bin)->first();

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

            if($grnSub->scanned_quantity == $grnSub->total_quantity){
                GrnSub::where('grn_id', $barcode->grn_id)->update(['grn_status' => 1]);
            }

            $grnSubStatus = GrnSub::where('grn_id', $grnSub->grn_id)->where('status', 0)->exists();

            $grn = Grn::find($grnSub->grn_id);

            if($grnSubStatus){
                $grn->update([
                    'status' => 2
                ]);
            }else{
                $grn->update([
                    'status' => 1
                ]);
            }

            DB::commit();

            return response()->json([
                'status' => 200,
                'message' => 'Storage Scan Successful',
            ]);
        }
    }
}
