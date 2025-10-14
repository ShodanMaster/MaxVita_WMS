<?php

namespace App\Http\Controllers\Transactions\Production;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\ProductionPlan;
use Illuminate\Http\Request;

class ProductionPlanController extends Controller
{
    public function index(){
        $productionNumber = ProductionPlan::nextNumber();
        $rmItems = Item::where('item_type', 'RM')->get(['id', 'item_code', 'name']);
        $fgItems = Item::where('item_type', 'FG')->get(['id', 'item_code', 'name']);

        return view('transactions.production.productionplan', compact('productionNumber', 'rmItems', 'fgItems'));
    }

    public function store(Request $request){
        dd($request->all());
    }
}
