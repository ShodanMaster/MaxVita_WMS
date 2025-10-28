<?php

namespace App\Http\Controllers\Transactions\Dispatch;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Dispatch;
use App\Models\DispatchSub;
use App\Models\Item;
use App\Models\Location;
use App\Models\Uom;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use RealRashid\SweetAlert\Facades\Alert;

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
        // dd($request->all());
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

            if($request->location_id === $request->to_location_id){
                return redirect()->back()->withErrors(['to_location_id' => 'To Location must be different from From Location'])->withInput();
            }

            $location = Location::find($request->location_id, ['id', 'branch_id']);

            $dispatchTo = match($request->dispatch_type){
                'sales' => Customer::find($request->customer_id, ['id', 'name']),
                'transfer' => Location::find($request->to_location_id, ['id', 'name', 'branch_id']),
            };

            $dispatch = new Dispatch([
                'dispatch_number' => $request->dispatch_number ,
                'dispatch_date' => $request->dispatch_date ,
                'from_branch_id' => $location->branch_id ,
                'from_location_id' => $location->id,
                'dispatch_type' => $request->dispatch_type ,
                'user_id' => Auth::id(),
            ]);

            $dispatch->dispatchTo()->associate($dispatchTo);
            $dispatch->save();

            $items = json_decode($request->items_json, true);

            $dispatchSubs = [];
            foreach($items as $item){
                $dispatchSubs[] = [
                    'dispatch_id' => $dispatch->id,
                    'item_id' => $item['item_id'],
                    'uom_id' => $item['uom_id'],
                    'total_quantity' => $item['quantity'],
                ];
            }

            if(!empty($dispatchSubs)){
                DispatchSub::insert($dispatchSubs);
            }

            DB::commit();
            Alert::toast('Dispatch saved with Number ' . $dispatch->dispatch_number, 'success')->autoClose(3000);
            return redirect()->back();
        }catch(Exception $e){
            DB::rollBack();
            dd(vars: $e);
        }
    }
}
