<?php

namespace App\Http\Controllers\Transactions\Production;

use App\Http\Controllers\Controller;
use App\Models\ProductionPlan;
use Illuminate\Http\Request;

class ProductionScanController extends Controller
{
    public function index(){
        $planNumbers = ProductionPlan::whereNot('status',2)->get(['plan_number']);
        return view('transactions.production.productionscan', compact('planNumbers'));
    }

    public function store(Request $request){
       return redirect()->route('production-scan.show', $request->plan_number);
    }

    public function show($planNumber){
        $productionPlan = ProductionPlan::with('productionPlanSubs.item')->where('plan_number',$planNumber)->first();
        return view('transactions.production.scan', compact('planNumber', 'productionPlan'));
    }
}
