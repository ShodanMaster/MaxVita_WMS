<?php

namespace App\Http\Controllers\Transactions\Production;

use App\Http\Controllers\Controller;
use App\Models\ProductionPlan;
use Illuminate\Http\Request;

class FgBarcodeGenerationController extends Controller
{
    public function index(){
        $planNumbers = ProductionPlan::where('status', 2)->get(['id', 'plan_number']);
        return view('transactions.production.fgbarcodegeneration', compact('planNumbers'));
    }
}
