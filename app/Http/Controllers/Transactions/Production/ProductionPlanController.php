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
use Maatwebsite\Excel\Facades\Excel;
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
                    'created_at' => now()
                ];
            }

            if (!empty($insertData)) {
                ProductionPlanSub::insert($insertData);
            }

            DB::commit();
            Alert::toast('Production lan saved with Number ' . $productionNumebr, icon: 'success')->autoClose(3000);
            return redirect()->back();
        } catch (Exception $e) {
            DB::rollBack();
            dd($e);
            Log::error('Production Plan Error: ' . $e->getMessage());

            Alert::toast('An error occurred while adding the production plan.', 'error')->autoClose(3000);
            return redirect()->route('production-plan.index');
        }
    }

    public function productionPlanExcelUpload(Request $request){
        $request->validate([
            'excel_file' => 'required|file|mimes:xlsx,xls'
        ]);
        $current = date('Y-m-d_H-i-s');

        try{
            $user = Auth::user();
            DB::beginTransaction();

            $data = $this->getExcelData($request->excel_file);
            // dd($data);
            if ($data['error'] !== '') {

                $errors = array_filter(
                    explode('|', rtrim($data['error'], '|')),
                    fn($e) => trim($e) !== ''
                );

                return redirect()->route('purchase-order.index')->withErrors($errors);
            }

            $planNumber = ProductionPlan::nextNumber();
            $PrductionPlan = ProductionPlan::create([
                'plan_number' => $planNumber,
                'plan_date' => today(),
                'total_quantity' => $data['total_quantity'],
                'item_id' => $data["fg_item_id"],
                'user_id' => $user->id,
                'branch_id' => $user->branch_id,
            ]);
            // dd($data['rm_items']);
            foreach($data['rm_items'] as $item){
                ProductionPlanSub::create(attributes: [
                    'production_plan_id' => $PrductionPlan->id,
                    'item_id' => $item['item_id'],
                    'total_quantity' => $item['quantity'],
                ]);
            }

            $fileName = $current . '_' . $request->file('excel_file')->getClientOriginalName();

            $request->file('excel_file')->storeAs('excel_uploads/transaction_uploads/production_plan_uploads', $fileName, 'public');
            DB::commit();
            Alert::toast('Production Plan saved with Number ' . $planNumber, 'success')->autoClose(3000);
            return redirect()->back();
        } catch (Exception $e) {
            DB::rollBack();
            dd($e);
            Log::error('Production Plan Excel Error: ' . $e->getMessage());

            Alert::toast('An error occurred while adding the production plan excel upload.', 'error')->autoClose(3000);
            return redirect()->route('production-plan.index');
        }
    }

    private function getExcelData($file){
        $sheet = Excel::toArray([], $file);
        $rows = $sheet[0] ?? [];
        $data = [
            'error' => '',
            'fg_item_id' => '',
            'total_quantity' => '',
            'items' => [],
        ];
        // dd($rows);

        $fgItemArray = [
            'item_name' => $rows[0][1],
            'total_quantity' => $rows[1][1],
        ];
        // dd($fgItemArray);

        foreach (['item_name', 'total_quantity'] as $key) {
            if (empty($fgItemArray[$key])) {
                $label = ucwords(str_replace('_', ' ', $key));
                $data['error'] .= $label . " is required|";
            }
        }
        // dd($data);
        if ($data['error'] !== '') return $data;

        $fgItem = Item::where('name', $fgItemArray['item_name'])->first();
        if(!$fgItem){
            $data['error'] .= "Item does not exist|";
        } else if($fgItem->item_type != 'FG'){
            $data['error'] .= "Item Type not FG|";
        }
        else {
            $data['fg_item_id'] = $fgItem->id;
            $data['total_quantity'] = $fgItemArray['total_quantity'];
        }

        if ($data['error'] !== '') return $data;

        for($i = 4; $i < count($rows); $i++){
            $row = $rows[$i];

            if (!array_filter($row)) {
                continue;
            }

            $rmItemArray = [
                'item_name' => $row[0] ?? '',
                'quantity' => $row[1] ?? '',
            ];

            foreach(['item_name', 'quantity'] as $key){
                if (empty($rmItemArray[$key])) {
                    $label = ucwords(str_replace('_', ' ', $key));
                    $data['error'] .= 'Row ' . ($i + 1) . " : $label required|";
                }
            }

            if ($data['error'] !== '') return $data;

            $rmItem = Item::where('name', $rmItemArray['item_name'])
                ->first();

            if(!$rmItem){
                $data['error'] .= 'Row ' . ($i + 1) . " : Item does not exist|";
            } else if($rmItem->item_type != 'RM'){
                $data['error'] .= "Item Type not RM|";
            }

            if ($data['error'] !== '') return $data;

            $data['rm_items'][] = ['item_id' => $rmItem->id, 'quantity' => $row[1]];
        }

        return $data;
    }
}
