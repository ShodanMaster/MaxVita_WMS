<?php

namespace App\Http\Controllers\Transactions\Production;

use App\Helpers\BarcodeGenerator;
use App\Http\Controllers\Controller;
use App\Models\Barcode;
use App\Models\Location;
use App\Models\ProductionPlan;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use RealRashid\SweetAlert\Facades\Alert;

class ProductionBarcodeGenerationController extends Controller
{
    public function index(){
        $planNumbers = ProductionPlan::where('status', 2)->get(['id', 'plan_number']);
        $batch = 'F' . date('ymd');
        return view('transactions.production.productionbarcodegeneration', compact('planNumbers', 'batch'));
    }

    public function store(Request $request){
        try{
            // dd($request->all());

            $productionPlan = ProductionPlan::find($request->plan_number);
            $user = Auth::user();
            $batchNumber = 'F' . date('ymd');
            $location = Location::find($user->location_id);
            $branchId = $location->branch_id;
            // dd($productionPlan->total_quantity);
            $netWeight = $productionPlan->item->spq_quantity;
            DB::beginTransaction();
            $numberOfBacodes = $productionPlan->total_quantity;
            while($numberOfBacodes--){

                $barcodeNumber = BarcodeGenerator::nextNumber($location->prefix);
                $barcodeData[] = [
                                'serial_number' => $barcodeNumber,
                                'transaction_id' => $productionPlan->id,
                                'transaction_type' => '2',
                                'branch_id' => $branchId,
                                'location_id' => $location->id,
                                'item_id' => $productionPlan->item_id,
                                'date_of_manufacture' => $request->date_of_manufacture,
                                'best_before_date' => $request->best_before_date,
                                'batch_number' => $batchNumber,
                                'price' => $productionPlan->item->price,
                                // 'total_price' => $totalPrice,
                                'net_weight' => $netWeight,
                                'grn_net_weight' => $netWeight,
                                'spq_quantity' => $netWeight,
                                'grn_spq_quantity' => $netWeight,
                                'status' => '-1',
                                'user_id' => $user->id,
                                'qc_approval_status' => '0',
                            ];
            }

            if (!empty($barcodeData)) {
                Barcode::insert($barcodeData);
                $productionPlan->update([
                    'status' => 3,
                ]);
            }

            DB::commit();
            Alert::toast('FG Barcode(s) has been Generated for ' . $productionPlan->plan_number, 'success')->autoClose(3000);
            return redirect()->back();
        } catch(Exception $e){
            DB::rollBack();
            dd($e);
            Log::error('FG Barcode Generate Error: ' . $e->getMessage());
            Alert::toast('An error occurred while generating FG Barcode.', 'error')->autoClose(3000);
            return redirect()->route('production-barcode-generation.index');
        }
    }
    // public function producitonBarcodeGenerate(Request $request){
    //     if($request->ajax()){

    //     }
    //     }
    // }
}
