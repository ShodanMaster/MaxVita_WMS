<?php

namespace App\Http\Controllers\Ajax;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\PurchaseOrder;
use Illuminate\Http\Request;

class GrnAjaxController extends Controller
{
    public function getPurchaseNumber(Request $request){
        if($request->ajax()){
            $purchaseNumbers = PurchaseOrder::where('vendor_id', $request->vendor_id)
                                            ->whereNot('status', 1)->get(['purchase_number']);

            return response()->json($purchaseNumbers);
        }
    }

    public function getGrnItems(Request $request){
        if ($request->ajax()) {
            $query = Item::query();

            if ($request->purchase_number) {
                $purchaseOrder = PurchaseOrder::where('purchase_number', $request->purchase_number)
                                        ->with('purchaseOrderSubs.item')
                                        ->first();

                if ($purchaseOrder) {
                    $itemIds = $purchaseOrder->purchaseOrderSubs->pluck('item_id');
                    $query->whereIn('id', $itemIds);
                } else {
                    return response()->json([]);
                }
            }

            if ($request->category_id) {
                $query->where('category_id', $request->category_id);
            }

            if ($request->grn_type) {
                $query->where('item_type', $request->grn_type);
            }

            $items = $query->get(['id', 'item_code', 'name']);

            return response()->json($items);
        }

        return response()->json([]);
    }

}
