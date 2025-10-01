<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\PurchaseOrder;
use Illuminate\Http\Request;

class AjaxController extends Controller
{
    public function getSpqQuantity(Request $request){
        if($request->ajax()){
            $spqQuantity = Item::where('id', $request->item_id)->first(['spq_quantity']);

            return response()->json( $spqQuantity);
        }
    }

    public function getPurchaseOrder(Request $request){
        // dd($request->all());
        if ($request->ajax()) {
            $purchaseOrder = PurchaseOrder::with('purchaseOrderSubs.item')->where('purchase_number', $request->purchase_number)->first();

            if (!$purchaseOrder) {
                return response()->json(['error' => 'Purchase order not found'], 404);
            }

            $items = $purchaseOrder->purchaseOrderSubs->map(function ($p) {
                return [
                    'item_code'   => $p->item->item_code,
                    'item_name'   => $p->item->name,
                    'spq_quantity' => $p->item->spq_quantity,
                    'quantity'    => $p->quantity,
                ];
            });

            return response()->json($items);
        }

    }
}
