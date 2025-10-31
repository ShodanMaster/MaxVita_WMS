<?php

namespace App\Http\Controllers\Ajax;

use App\Http\Controllers\Controller;
use App\Models\Barcode;
use App\Models\Bin;
use App\Models\ProductionPlan;
use App\Models\ProductionScan;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProductionStorageScanAjaxController extends Controller
{

    public function fetchfGStorageScanDetails(Request $request){
        if($request->ajax()){

            $productionPlan = ProductionPlan::with('item')->find($request->id);

            $data = [
                'fg_item' => $productionPlan->item->item_code . '/' . $productionPlan->item->name,
                'total_quantity' => $productionPlan->total_quantity,
                'scanned_quantity' => $productionPlan->scanned_quantity,
            ];

            return response()->json($data);
        }
    }

    public function productionStorageScan(Request $request){
        try{
            if($request->ajax()){
                // dd($request->all());
                $user = Auth::user();
                $barcode = Barcode::where('serial_number', $request->barcode)->where('transaction_type', 2)->first();
                $weight = $request->weight;
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

                $productionPlan = ProductionPlan::where('id', $request->production_plan_id)
                                    ->where('item_id', $barcode->item_id)
                                    ->first();
                // dd($productionPlan);
                if (!$productionPlan) {
                    return response()->json([
                        'status' => 404,
                        'message' => 'Production Plan data not found.'
                    ]);
                }
                // dd($barcode->transaction_id, $productionPlan->grn_id);
                if($barcode->transaction_id != $productionPlan->id){
                    return response()->json([
                        'status' => 404,
                        'message' => 'Item does not belongs to this Production Plan.'
                    ]);
                }

                if($productionPlan->total_quantity < $productionPlan->scanned_quantity){
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

                $barcode->update([
                    'branch_id' => $user->branch_id,
                    'location_id' => $user->location_id,
                    'net_weight' => $weight,
                    'grn_net_weight' => $weight,
                    'bin_id' => $bin->id,
                    'status' => '1',
                ]);

                $productionScan = ProductionScan::create([
                    'barcode' => $barcode->serial_number,
                    'item_id' => $barcode->item_id,
                    'production_plan_id' => $productionPlan->id,
                    'bin_id' => $bin->id,
                    'scanned_quantity' => 1,
                    'net_weight' => $weight,
                    'spq_quantity' => $barcode->spq_quantity,
                    'user_id' => $user->id,
                ]);

                $productionPlan->update([
                    'scanned_quantity' => $productionPlan->scanned_quantity + 1,
                ]);

                $productionPlanUpdated = ProductionPlan::where('id', $request->production_plan_id)
                                    ->where('item_id', $barcode->item_id)
                                    ->whereNot('status', 1)
                                    ->first();

                if($productionPlanUpdated->scanned_quantity == $productionPlanUpdated->total_quantity){
                    $productionPlanUpdated->update(['status' => 1]);
                }

                $productionPlanStatus = ProductionPlan::where('id', $productionPlan->id)->where('status', 3)->exists();

                $productionPlan = ProductionPlan::find($productionPlan->id);

                $scanComplete = false;
                if(!$productionPlanStatus){
                    $productionPlan->update([
                        'status' => 4
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
        } catch(Exception $e){
            dd($e);
        }
    }
}
