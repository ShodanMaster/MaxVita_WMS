<?php

namespace App\Http\Controllers\Transactions\Dispatch;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Item;
use App\Models\Uom;
use Illuminate\Http\Request;

class SalesReturnController extends Controller
{
    public function index(){
        $customers = Customer::get(['id', 'name']);
        $items = Item::where('item_type', 'FG')->get(['id', 'item_code', 'name']);
        $uoms = Uom::wherein('name', ['Bag', 'Box'])->get(['id', 'name']);
        return view('transactions.dispatch.salesreturn', compact('customers', 'items', 'uoms'));
    }

    public function store(Request $request){
        dd($request->all());
    }
}
