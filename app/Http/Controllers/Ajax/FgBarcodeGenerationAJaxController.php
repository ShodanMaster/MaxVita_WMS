<?php

namespace App\Http\Controllers\Ajax;

use App\Helpers\BarcodeGenerator;
use App\Http\Controllers\Controller;
use App\Models\Barcode;
use App\Models\Location;
use App\Models\ProductionPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FgBarcodeGenerationAJaxController extends Controller
{
    public function getPlanDetails(Request $request){
        if($request->ajax()){
            $productionPlan = ProductionPlan::where('plan_number', $request->plan_number)->first();

            $data = [
                'fg_item' => $productionPlan->item->item_code.'/'. $productionPlan->item->name,
                'total_quantity' => $productionPlan->total_quantity
            ];

            return response()->json($data);
        }
    }

    // public function fgBarcodeGenerate(Request $request){
    //     if($request->ajax()){
    //         dd($request->all());

    //         $productionPlan = ProductionPlan::where('plan_number', $request->plan_number)->first();
    //         $user = Auth::user();
    //         $batchNumber = 'F' . date('ymd');
    //         $batchNumber = 'F';
    //         $location = Location::find($user->location_id);
    //         $branchId = $location->branch_id;

    //         $barcodeNumber = BarcodeGenerator::nextNumber($location->prefix);
    //         $barcode = Barcode::create([
    //                         'serial_number' => $barcodeNumber,
    //                         'transaction_id' => $productionPlan->id,
    //                         'transaction_type' => '2',
    //                         'branch_id' => $branchId,
    //                         'location_id' => $location->id,
    //                         'item_id' => $productionPlan->item_id,
    //                         'date_of_manufacture' => $request->date_of_manufacture,
    //                         'best_before_date' => $request->best_before_date,
    //                         'batch_number' => $batchNumber,
    //                         'price' => $productionPlan->item->price,
    //                         'total_price' => $totalPrice,
    //                         'net_weight' => $netWeight,
    //                         'grn_net_weight' => $item["total_quantity"],
    //                         'status' => '-1',
    //                         'user_id' => $userid,
    //                         'qc_approval_status' => '0',
    //                     ]);
    //     }
    //     }
    // }
}
