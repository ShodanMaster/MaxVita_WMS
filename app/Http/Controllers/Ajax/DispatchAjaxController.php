<?php

namespace App\Http\Controllers\Ajax;

use App\Http\Controllers\Controller;
use App\Models\Barcode;
use App\Models\Dispatch;
use App\Models\DispatchScan;
use App\Models\DispatchSub;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DispatchAjaxController extends Controller
{

    public function getDispatchDetails(Request $request){

        if ($request->ajax()) {

            $dispatch = Dispatch::with([
                'branch:id,name',
                'location:id,name',
                'dispatchTo:id,name',
                'dispatchSubs.item:id,item_code,name',
                'dispatchSubs.uom:id,name'
            ])->find($request->id);

            if (!$dispatch) {
                return response()->json(['error' => 'Dispatch not found'], 404);
            }

            $data = [
                'dispatch_number' => $dispatch->dispatch_number,
                'dispatch_date' => $dispatch->dispatch_date,
                'from_branch' => $dispatch->branch->name,
                'from_location' => $dispatch->location->name,
                'dispatch_to' => $dispatch->dispatchTo->name,
                'dispatch_type' => $dispatch->dispatch_type,
                'items' => $dispatch->dispatchSubs->map(function($sub){
                    return [
                        'item' => $sub->item->item_code . '/' . $sub->item->name,
                        'uom' => $sub->uom->name,
                        'total_quantity' => $sub->total_quantity,
                    ];
                }),
            ];

            return response()->json($data);
        }
    }


    public function getDispatchItems(Request $request){
        if ($request->ajax()) {
            $dispatch = Dispatch::with('dispatchSubs.item')->find($request->id);

            if (!$dispatch) {
                return response()->json(['error' => 'Dispatch not found'], 404);
            }

            $items = $dispatch->dispatchSubs->map(function ($d) {
                return [
                    'item_code'   => $d->item->item_code,
                    'item_name'   => $d->item->name,
                    'uom'         => $d->uom->name,
                    'quantity'    => $d->total_quantity,
                ];
            });

            return response()->json($items);
        }
    }

    public function fetchDispatchDetails(Request $request){
        if ($request->ajax()) {
            $dispatch = Dispatch::with('dispatchSubs.item')->find($request->dispatch_id);

            if (!$dispatch) {
                return response()->json(['error' => 'Dispatch not found'], 404);
            }

            $items = $dispatch->dispatchSubs->map(function ($d) {
                return [
                    'item'   => $d->item->item_code . '/' . $d->item->name,
                    'uom'         => $d->uom->name,
                    'balance_quantity'    => $d->total_quantity - $d->dispatched_quantity,
                    'dispatched_quantity'    => round($d->dispatched_quantity),
                ];
            });

            return response()->json($items);
        }
    }

    public function dispatchScan(Request $request){
        if($request->ajax()){

            $user = Auth::user();
            $barcode = Barcode::where('serial_number', $request->barcode)
                        ->where('location_id', $user->location_id)
                        ->first();

            if(!$barcode){
                return response()->json([
                    'status' => 404,
                    'message' => 'Barcode not found.'
                ]);
            }

            switch ($barcode->status) {
                case -1:
                    return response()->json([
                        'status' => 409,
                        'message' => 'Not in Stock!'
                    ]);
                case 2:
                    return response()->json([
                        'status' => 409,
                        'message' => 'Already Dispatched!'
                    ]);
                case 2:
                    return response()->json([
                        'status' => 409,
                        'message' => 'Already Transfered!'
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

            if($barcode->uom->id != $dispatchSub->uom->id){
                return response()->json([
                    'status' => 409,
                    'message' => 'Uom Mismatch!'
                ]);
            }

            DB::beginTransaction();

            $dispatchScan = DispatchScan::create([
                'dispatch_id' => $dispatchSub->dispatch_id,
                'barcode' => $barcode->serial_number,
                'item_id' => $barcode->item_id,
                'bin_id' => $barcode->bin->id,
                'uom_id' => $barcode->uom->id,
                'dispatched_quantity' => 1,
                'user_id' => $user->id,
            ]);

            $status = '';

            if($dispatchSub->dispatch->dispatch_type === 'sales'){
                $status = '2';
            }else{
                $status = '4';
            }

            $barcode->update([
                'status' => $status,
            ]);

            $dispatchSub->update([
                'dispatched_quantity' => $dispatchSub->dispatched_quantity + 1,
            ]);

            $dispatchSubUpdated = dispatchSub::where('dispatch_id', $request->dispatch_id)
                                ->where('item_id', $barcode->item_id)
                                ->whereNot('status', 1)
                                ->first();

            if($dispatchSubUpdated->dispatched_quantity == $dispatchSubUpdated->total_quantity){
                $dispatchSubUpdated->update(['status' => 1]);
            }

            $dispatchSubStatus = dispatchSub::where('dispatch_id', $dispatchSub->dispatch_id)->where('status', 0)->exists();

            $dispatch = Dispatch::find($dispatchSub->dispatch_id);

            $scanComplete = false;
            if($dispatchSubStatus){
                $dispatch->update([
                    'status' => 1
                ]);
            }else{
                $dispatch->update([
                    'status' => 2
                ]);
                $scanComplete = true;
            }

            DB::commit();

            return response()->json([
                'status' => 200,
                'scan_complete' => $scanComplete,
                'message' => 'Dispatch Scan Successful',
            ]);
        }
    }
}
