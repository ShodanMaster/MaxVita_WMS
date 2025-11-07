<?php

namespace App\Http\Controllers\Ajax;

use App\Http\Controllers\Controller;
use App\Models\ProductionPlan;
use Illuminate\Http\Request;

class ProductionBarcodeGenerationAJaxController extends Controller
{
    public function getPlanDetails(Request $request){
        if($request->ajax()){
            $productionPlan = ProductionPlan::with('productionPlanSubs.item')->find($request->plan_number);

            $data = [
                'fg_item' => $productionPlan->item->item_code.'/'. $productionPlan->item->name,
                'total_quantity' => $productionPlan->total_quantity,
                'balance_quantity' => $productionPlan->total_quantity - $productionPlan->picked_quantity,
            ];

            return response()->json($data);
        }
    }

}
