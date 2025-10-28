<?php

namespace App\Http\Controllers\Transactions\Dispatch;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Item;
use App\Models\Location;
use App\Models\Uom;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DispatchController extends Controller
{
    public function index(){
        $locations = Location::get(['id', 'name']);
        $customers = Customer::get(['id', 'name']);
        $fgItems = Item::where('item_type', 'FG')->get(['id', 'item_code', 'name']);
        $uoms = Uom::wherein('name', ['Bag', 'Box'])->get(['id', 'name']);
        return view('transactions.dispatch.index', compact('locations', 'customers', 'fgItems', 'uoms'));
    }

    public function store(Request $request){
        $request->validate([
            'dispatch_type' => 'required|in:sales, transfer',
            'dispatch_date' => 'required|date',
            'dispatch_number' => 'required|string|unique:dispatches,dispatch_number',
            'location_id' => 'exists:locations,id',
            'customer_id' => 'exists:customers,id',
            'items_json' => 'required|string',
        ]);

        try{
            DB::beginTransaction();
            $items = json_decode($request->items_json, true);

            DB::commit();
        }catch(Exception $e){
            DB::rollBack();
            dd(vars: $e);
        }
    }
}
