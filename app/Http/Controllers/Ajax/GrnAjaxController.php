<?php

namespace App\Http\Controllers\Ajax;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderSub;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;

class GrnAjaxController extends Controller
{
    public function getPurchaseNumber(Request $request){
        if($request->ajax()){
            $purchaseNumbers = PurchaseOrder::where('vendor_id', $request->vendor_id)
                                            ->whereNot('status', 1)->get(['id', 'purchase_number']);

            return response()->json($purchaseNumbers);
        }
    }

    public function itemPurchaseQuantity(Request $request){
        if($request->ajax()){
            $purchaseItem = PurchaseOrderSub::where('purchase_order_id', $request->purchase_id)
                                ->where('item_id', $request->item_id)
                                ->first(['quantity', 'picked_quantity']);

            return response()->json(round($purchaseItem->quantity - $purchaseItem->picked_quantity));

        }
    }

    public function getGrnItems(Request $request){
        if ($request->ajax()) {
            $query = Item::query();

            if ($request->purchase_number) {
                $purchaseOrder = PurchaseOrder::with(['purchaseOrderSubs' => function ($query) {
                    $query->where('status', 0);
                }, 'purchaseOrderSubs.item'])
                ->find($request->purchase_number);

                if ($purchaseOrder) {
                    $itemIds = $purchaseOrder->purchaseOrderSubs->pluck('item_id');
                    $query->whereIn('id', $itemIds);
                }
            }

            if ($request->location_id) {
                $query->whereHas('locations', function ($q) use ($request) {
                    $q->where('locations.id', $request->location_id);
                });
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
