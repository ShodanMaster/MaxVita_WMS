<?php

namespace App\Http\Controllers\Ajax;

use App\Http\Controllers\Controller;
use App\Models\Grn;
use Illuminate\Http\Request;

class StorageScanAjaxController extends Controller
{
    public function getGrnDetails(Request $request){
        // dd($request->all());
        if($request->ajax()){
            $grn = Grn::where('grn_number', $request->grn_number)->first();

            $data = [

                    'grn_number' => $grn->grn_number,
                    'vendor_name' => $grn->vendor->name,
                    'invoice_number' => $grn->invoice_number,
                    'invoice_date' => $grn->invoice_date,
                    'remark' => $grn->remark,
                ];

            return response()->json($data);
        }
    }

    public function storageScan(Request $request){
        if($request->ajax()){
            dd($request->all());
        }
    }
}
