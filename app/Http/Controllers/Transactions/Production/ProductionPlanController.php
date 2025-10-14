<?php

namespace App\Http\Controllers\Transactions\Production;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\ProductionPlan;
use App\Models\ProductionPlanSub;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use RealRashid\SweetAlert\Facades\Alert;

class ProductionPlanController extends Controller
{
    public function index(){
        $productionNumber = ProductionPlan::nextNumber();
        $rmItems = Item::where('item_type', 'RM')->get(['id', 'item_code', 'name']);
        $fgItems = Item::where('item_type', 'FG')->get(['id', 'item_code', 'name']);

        return view('transactions.production.productionplan', compact('productionNumber', 'rmItems', 'fgItems'));
    }

    public function store(Request $request){
        // dd($request->all());
        try{
            $user = Auth::user();
            DB::beginTransaction();
            $productionNumebr = ProductionPlan::nextNumber();

            $production = ProductionPlan::create([
                'plan_number' => $productionNumebr,
                'plan_date' => $request->plan_date,
                'total_quantity' => $request->quantity,
                'item_id' => $request->fgItem,
                'user_id' => $user->id,
                'branch_id' => $user->branch_id,
            ]);

            $insertData = [

            ];

            foreach($request->rmItems as $rmItem){
                $insertData[] = [
                    'production_plan_id' => $production->id,
                    'item_id' => $rmItem['item_id'],
                    'total_quantity' => $rmItem['total_quantity'],
                ];
            }

            if (!empty($insertData)) {
                ProductionPlanSub::insert($insertData);
            }

            DB::commit();
            Alert::toast('Production lan saved with Number ' . $productionNumebr, 'success')->autoClose(3000);
            return redirect()->back();
        } catch (Exception $e) {
            DB::rollBack();
            dd($e);
            Log::error('Production Plan Error: ' . $e->getMessage());

            Alert::toast('An error occurred while adding the production plan.', 'error')->autoClose(3000);
            return redirect()->route('production-plan.index');
        }
    }
}
