<?php

namespace App\Http\Controllers\Transactions;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\PurchaseOrder;
use App\Models\Vendor;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use RealRashid\SweetAlert\Facades\Alert;

class PurchaseOrderController extends Controller
{
    public function index(){
        $purchaseNumber = PurchaseOrder::nextNumber();
        $items = Item::where('item_type', 'RM')->get(['id', 'item_code', 'name']);
        $vendors = Vendor::get(['id', 'name']);
        return view('transactions.purchaseorder.index', compact('purchaseNumber', 'items', 'vendors'));
    }

    public function store(Request $request){
        try{
            dd($request->all());
        } catch (Exception $e) {
            // dd($e);
            Log::error('Purchase Store Error: ' . $e->getMessage());

            Alert::toast('An error occurred while adding the purchase.', 'error')->autoClose(3000);
            return redirect()->route('purchase-order.index');
        }
    }
}
