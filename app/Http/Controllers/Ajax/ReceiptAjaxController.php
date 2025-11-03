<?php

namespace App\Http\Controllers\Ajax;

use App\Http\Controllers\Controller;
use App\Models\Barcode;
use App\Models\Bin;
use App\Models\Dispatch;
use App\Models\DispatchSub;
use App\Models\ReceiptScan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReceiptAjaxController extends Controller
{
    public function receiptScan(Request $request){
        if($request->ajax()){

            $user = Auth::user();
            $barcode = Barcode::where('serial_number', $request->barcode)->where('transaction_type', 2)->first();
            $receiptScan = ReceiptScan::where('barcode', $request->barcode)->exists();

            if($receiptScan){
                return response()->json([
                    'status' => 409,
                    'message' => 'Already Scanned!'
                ]);
            }

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
                        'message' => 'Already In Stock!'
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

            $dispatchSub = DispatchSub::where('dispatch_id', $request->dispatch_id)
                                ->where('item_id', $barcode->item_id)
                                ->first();

            if (!$dispatchSub) {
                return response()->json([
                    'status' => 404,
                    'message' => 'Dispatch data not found.'
                ]);
            }
            // dd($barcode->transaction_id, $dispatchSub->dispatch_id);
            if($barcode->item_id != $dispatchSub->item_id){
                return response()->json([
                    'status' => 404,
                    'message' => 'Item does not belongs to this Dispatch.'
                ]);
            }

            // dd($dispatchSub->total_quantity, $dispatchSub->scanned_quantity);
            if($dispatchSub->total_quantity < $dispatchSub->scanned_quantity){
                return response()->json([
                    'status' => 422,
                    'message' => 'Receipt Quantity Exceeded!'
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

            $receiptScan = ReceiptScan::create([
                'barcode' => $barcode->serial_number,
                'item_id' => $barcode->item_id,
                'dispatch_id' => $dispatchSub->dispatch_id,
                'bin_id' => $bin->id,
                'scanned_quantity' => $barcode->net_weight,
                'uom_id' => $barcode->uom_id,
                'batch_number' => $barcode->batch_number,
                'net_weight' => $barcode->net_weight,
                'user_id' => $user->id,
            ]);

            $barcode->update([
                'branch_id' => $user->branch_id,
                'location_id' => $user->location_id,
                'bin_id' => $bin->id,
                'user_id' => $user->id,
                'status' => '1',
            ]);

            // dd($dispatchSub->received_quantity);
            $dispatchSub->update([
                'received_quantity' => $dispatchSub->received_quantity + 1,
            ]);

            $dispatchSubUpdated = DispatchSub::where('dispatch_id', $request->dispatch_id)
                                ->where('item_id', $barcode->item_id)
                                ->where('status', 1)
                                ->first();
            // dd($dispatchSubUpdated);
            if($dispatchSubUpdated->received_quantity == $dispatchSubUpdated->total_quantity){
                $dispatchSubUpdated->update(['status' => 2]);
            }

            $dispatchSubStatus = DispatchSub::where('dispatch_id', $dispatchSub->dispatch_id)->where('status', 2)->exists();
            // dd($dispatchSubStatus);
            $dispatch = Dispatch::find($dispatchSub->dispatch_id);

            $scanComplete = false;
            if($dispatchSubStatus){
                $dispatch->update([
                    'status' => 3
                ]);
                $scanComplete = true;
            }

            DB::commit();

            return response()->json([
                'status' => 200,
                'scan_complete' => $scanComplete,
                'message' => 'Receipt Scan Successful',
            ]);
        }
    }

    public function fetchReceiptScanDetails(Request $request){
        if($request->ajax()){
            $dispatchSubs = DispatchSub::with('item')->where('dispatch_id', $request->dispatch_id)->get();

            $data = $dispatchSubs->map(function($sub){
                return [
                    'item' => $sub->item->item_code . "/" . $sub->item->name,
                    'balance_quantity' => $sub->dispatched_quantity - $sub->received_quantity,
                    'scanned_quantity' => round($sub->received_quantity),
                ];
            });

            return response()->json( $data);
        }
    }
}
