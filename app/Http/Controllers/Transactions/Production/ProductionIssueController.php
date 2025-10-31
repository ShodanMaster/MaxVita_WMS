<?php

namespace App\Http\Controllers\Transactions\Production;

use App\Http\Controllers\Controller;
use App\Models\ProductionPlan;
use Illuminate\Http\Request;

class ProductionIssueController extends Controller
{
    public function index(){
        $planNumbers = ProductionPlan::where('status',0)->get(['id', 'plan_number']);
        return view('transactions.production.productionissue', compact('planNumbers'));
    }

    public function store(Request $request){
       return redirect()->route('production-issue.show', $request->plan_number);
    }

    public function show($id){
        $productionPlan = ProductionPlan::with('productionPlanSubs.item')->find($id);
        return view('transactions.production.productionissuescan', compact('id', 'productionPlan'));
    }
}
