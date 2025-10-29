<?php

namespace App\Http\Controllers\Ajax;

use App\Http\Controllers\Controller;
use App\Models\Dispatch;
use Illuminate\Http\Request;

class DispatchAjaxController extends Controller
{
    public function getDispatchDetails(Request $request){
        if ($request->ajax()) {
            $dispatch = Dispatch::with('dispatchSubs.item')->find($request->id);

            if (!$dispatch) {
                return response()->json(['error' => 'Dispatch not found'], 404);
            }

            $items = $dispatch->dispatchSubs->map(function ($d) {
                return [
                    'item_code'   => $d->item->item_code,
                    'item_name'   => $d->item->name,
                    'uom'         => $d->uom->name,
                    'quantity'    => $d->total_quantity,
                ];
            });

            return response()->json($items);
        }
    }

    public function fetchDispatchDetails(Request $request){
        if ($request->ajax()) {
            $dispatch = Dispatch::with('dispatchSubs.item')->find($request->dispatch_id);

            if (!$dispatch) {
                return response()->json(['error' => 'Dispatch not found'], 404);
            }

            $items = $dispatch->dispatchSubs->map(function ($d) {
                return [
                    'item'   => $d->item->item_code . '/' . $d->item->name,
                    'uom'         => $d->uom->name,
                    'balance_quantity'    => $d->total_quantity - $d->dispatch_quantity,
                    'scanned_quantity'    => round($d->dispatch_quantity),
                ];
            });

            return response()->json($items);
        }
    }

    public function dispatchScan(Request $request){

    }
}
