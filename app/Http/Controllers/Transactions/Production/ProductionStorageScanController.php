<?php

namespace App\Http\Controllers\Transactions\Production;

use App\Http\Controllers\Controller;
use App\Models\ProductionPlan;
use Illuminate\Http\Request;

class ProductionStorageScanController extends Controller
{
    public function index(){
        $planNumbers = ProductionPlan::where('status', 3)->get(['id', 'plan_number']);
        return view('transactions.production.productionscanindex', compact('planNumbers'));
    }

    public function store(Request $request){
        dd($request);
    }
}
