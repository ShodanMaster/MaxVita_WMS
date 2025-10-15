<?php

namespace App\Http\Controllers\Ajax;

use App\Http\Controllers\Controller;
use App\Models\ProductionPlan;
use Illuminate\Http\Request;

class ProductionScanAjaxController extends Controller
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
}
