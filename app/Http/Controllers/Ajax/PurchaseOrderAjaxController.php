<?php

namespace App\Http\Controllers\Ajax;

use App\Http\Controllers\Controller;
use App\Models\PurchaseOrder;
use Illuminate\Http\Request;

class PurchaseOrderAjaxController extends Controller
{
    public function getPurchaseOrder(Request $request){
        // dd($request->all());
        if ($request->ajax()) {
            $purchaseOrder = PurchaseOrder::with('purchaseOrderSubs.item')->find($request->purchase_id);

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
