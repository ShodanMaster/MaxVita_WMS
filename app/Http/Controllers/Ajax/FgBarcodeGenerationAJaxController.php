<?php

namespace App\Http\Controllers\Ajax;

use App\Http\Controllers\Controller;
use App\Models\ProductionPlan;
use Illuminate\Http\Request;

class FgBarcodeGenerationAJaxController extends Controller
{
    public function getPlanDetails(Request $request){
        if($request->ajax()){
            $productionPlan = ProductionPlan::where('plan_number', $request->plan_number)->first();

            $data = [

            ];
        }
    }
}
