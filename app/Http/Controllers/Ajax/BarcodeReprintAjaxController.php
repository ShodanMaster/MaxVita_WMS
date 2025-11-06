<?php

namespace App\Http\Controllers\Ajax;

use App\Http\Controllers\Controller;
use App\Models\Grn;
use App\Models\ProductionPlan;
use Illuminate\Http\Request;

class BarcodeReprintAjaxController extends Controller
{
    public function getReprintNumbers(Request $request){
        if($request->ajax()){
            // dd($request->all());

            $documentNumbers = null;

            if($request->transaction_type === "grn"){
                $grns =  Grn::get(['id', 'grn_number']);

                $documentNumbers = $grns->map(function($g){
                    return [
                        'id' => $g->id,
                        'document_number' => $g->grn_number,
                    ];
                });
            }
            elseif($request->transaction_type === "production"){
                $productions =  ProductionPlan::where('status', 3)->get(['id', 'plan_number']);

                $documentNumbers = $productions->map(function($p){
                    return [
                        'id' => $p->id,
                        'document_number' => $p->plan_number,
                    ];
                });
            }

            return response()->json($documentNumbers);
        }
    }
}
