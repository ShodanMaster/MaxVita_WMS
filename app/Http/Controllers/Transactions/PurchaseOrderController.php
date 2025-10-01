<?php

namespace App\Http\Controllers\Transactions;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderSub;
use App\Models\Vendor;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
            // dd($request->all());
            DB::beginTransaction();
            $purchaseNumber = PurchaseOrder::nextNumber();
            $purchaseOrder = PurchaseOrder::create([
                'branch_id' => auth()->user()->branch_id,
                'purchase_number' => $purchaseNumber,
                'purchase_date' => $request->purchase_date,
                'vendor_id' => $request->vendor
            ]);
            // dd($purchaseOrder->id);
            foreach($request->items as $item){
                PurchaseOrderSub::create([
                    'purchase_order_id' => $purchaseOrder->id,
                    'item_id' => $item['item_id'],
                    'quantity' => $item['total_quantity'],
                ]);
            }
            DB::commit();
            Alert::toast('Purchase Order saved with Number ' . $purchaseNumber, 'success')->autoClose(3000);
            return redirect()->back();
        } catch (Exception $e) {
            DB::rollBack();
            dd($e);
            Log::error('Purchase Store Error: ' . $e->getMessage());

            Alert::toast('An error occurred while adding the purchase.', 'error')->autoClose(3000);
            return redirect()->route('purchase-order.index');
        }
    }
}
