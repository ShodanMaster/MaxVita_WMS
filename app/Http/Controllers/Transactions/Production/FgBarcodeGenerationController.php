<?php

namespace App\Http\Controllers\Transactions\Production;

use App\Http\Controllers\Controller;
use App\Models\ProductionPlan;
use Exception;
use Illuminate\Http\Request;

class FgBarcodeGenerationController extends Controller
{
    public function index(){
        $planNumbers = ProductionPlan::where('status', 2)->get(['plan_number']);
        $batch = 'F' . date('ymd');
        return view('transactions.production.fgbarcodegeneration', compact('planNumbers', 'batch'));
    }

    public function store(Request $request){
        try{


        } catch(Exception $e){
            dd($e);
        }
    }
}
