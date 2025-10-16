<?php

namespace App\Http\Controllers\Ajax;

use App\Http\Controllers\Controller;
use App\Models\Barcode;
use App\Models\Bin;
use App\Models\ProductionPlan;
use App\Models\ProductionPlanSub;
use App\Models\ProductionIssue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProductionIssueAjaxController extends Controller
{
    public function getProductionDetails(Request $request){
        if($request->ajax()){
            $productionPlan = ProductionPlan::where('plan_number', $request->plan_number)->first();

            $data = [
                'plan_number' => $productionPlan->plan_number,
                'item' => $productionPlan->item->item_code.'/'. $productionPlan->item->name,
                'total_quantity' => $productionPlan->total_quantity
            ];

            return response()->json([$data]);
        }
    }

    public function productionIssueScan(Request $request){
        if($request->ajax()){
            // dd($request->all());
            $user = Auth::user();
            $productionPlan = ProductionPlan::where('plan_number', $request->plan_number)->first();
            $barcode = Barcode::where('serial_number', $request->barcode)->first();
            // dd($barcode);
            if(!$barcode){
                return response()->json([
                    'status' => 404,
                    'message' => 'Barcode not found.'
                ]);
            }

            if ($barcode->status == 5) {
                return response()->json([
                    'status' => 409,
                    'message' => 'Already Scanned!'
                ]);
            } elseif ($barcode->status == 2) {
                return response()->json([
                    'status' => 409,
                    'message' => 'Already Despatched!'
                ]);
            } elseif ($barcode->status == 8) {
                return response()->json([
                    'status' => 404,
                    'message' => 'Does not Exist!'
                ]);
            } elseif ($barcode->status != 1) {
                return response()->json([
                    'status' => 409,
                    'message' => 'Not In Stock!'
                ]);
            }

            $productionPlanSub = ProductionPlanSub::where('production_plan_id', $productionPlan->id)
                                ->where('item_id', $barcode->item_id)
                                ->first();

            if (!$productionPlanSub) {
                return response()->json([
                    'status' => 404,
                    'message' => 'Production plan data not found!'
                ]);
            }

            if (!$productionPlan->productionPlanSubs->contains('item_id', $barcode->item_id)) {
                return response()->json([
                    'status' => 409,
                    'message' => 'Item Mis Match!'
                ]);
            }

            $bin = Bin::where('name', $request->bin)->first();

            if(!$bin){
                return response()->json([
                    'status' => 404,
                    'message' => 'Bin not found!'
                ]);
            }

            DB::beginTransaction();

            $productionIssue = ProductionIssue::create([
                'barcode' => $barcode->serial_number,
                'item_id' => $barcode->item_id,
                'production_plan_id' => $productionPlan->id,
                'bin_id' => $bin->id,
                'scanned_quantity' => $barcode->net_weight,
                'net_weight' => $barcode->net_weight,
                'user_id' => $user->id,
            ]);

            $barcode->update([
                'branch_id' => $user->branch_id,
                'location_id' => $user->location_id,
                'bin_id' => $bin->id,
                'status' => '5',
            ]);

            // dd($productionPlanSub->scanned_quantity, $barcode->net_weight);
            $productionPlanSub->update([
                'picked_quantity' => $productionPlanSub->picked_quantity + $barcode->net_weight,
            ]);

            $productionPlanSubUpdated = ProductionPlanSub::where('item_id', $barcode->item_id)
                                                        ->whereNot('status', 1)
                                                        ->first();

            if($productionPlanSubUpdated->total_quantity == $productionPlanSubUpdated->picked_quantity){
                $productionPlanSubUpdated->update(['status' => 1]);
            }

            $productionPlanSubStatus = ProductionPlanSub::where('production_plan_id', $productionPlanSub->production_plan_id)->where('status', 0)->exists();

            $productionPlan = ProductionPlan::find($productionPlanSub->production_plan_id);

            $scanComplete = false;
            if($productionPlanSubStatus){
                $productionPlan->update([
                    'status' => 1
                ]);
            }else{
                $productionPlan->update([
                    'status' => 2
                ]);
                $scanComplete = true;

            }

            DB::commit();

            return response()->json([
                'status' => 200,
                'scan_complete' => $scanComplete,
                'message' => 'Production Scan Successful!',
            ]);
        }
    }
}
