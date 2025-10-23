<?php

namespace App\Http\Controllers\Transactions\Production;

use App\Helpers\BarcodeGenerator;
use App\Http\Controllers\Controller;
use App\Models\Barcode;
use App\Models\Item;
use App\Models\Location;
use App\Models\ProductionPlan;
use App\Models\Uom;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use RealRashid\SweetAlert\Facades\Alert;

class ProductionBarcodeGenerationController extends Controller
{
    public function index(){
        $planNumbers = ProductionPlan::where('status', 2)->get(['id', 'plan_number']);
        $batch = 'F' . date('ymd');
        $uoms = Uom::wherein('name', ['Bag', 'Box'])->get(['id', 'name']);
        return view('transactions.production.productionbarcodegeneration', compact('planNumbers', 'batch', 'uoms'));
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
            $uomId = $request->uom;
            $itemName = Item::find($productionPlan->item_id)->name;
            DB::beginTransaction();

            $balanceQuantity = $productionPlan->total_quantity - $productionPlan->picked_quantity;

            $baseNumberOfBacodes = $request->number_of_barcodes > $balanceQuantity ? $balanceQuantity : $request->number_of_barcodes ;
            $numberOfBacodes = $baseNumberOfBacodes;
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
                                'uom_id' => $uomId,
                                'spq_quantity' => $netWeight,
                                'grn_spq_quantity' => $netWeight,
                                'status' => '-1',
                                'user_id' => $user->id,
                                'qc_approval_status' => '0',
                            ];


                            $contents[] = [
                                'transaction_number' => $productionPlan->plan_number,
                                'barcode' => $barcodeNumber,
                                'item_name' => $itemName,
                                'spq_quantity' => $barcodeData[count($barcodeData) - 1]['spq_quantity']
                            ];
            }
            // dd($contents);
            if (!empty($barcodeData)) {
                Barcode::insert($barcodeData);

                $productionPlan->update([
                    'picked_quantity' => $productionPlan->picked_quantity + $baseNumberOfBacodes,
                ]);

                if($productionPlan->picked_quantity >= $productionPlan->total_quantity){
                    $productionPlan->update([
                        'status' => 3,
                    ]);
                }
            }

            DB::commit();
            Alert::toast('FG Barcode(s) has been Generated for ' . $productionPlan->plan_number, 'success')->autoClose(3000);

            if ($request->has('prn')) {
                Session::put('contents', $contents);

                return redirect()->back();
            } else {
                return redirect()->back();
            }
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
