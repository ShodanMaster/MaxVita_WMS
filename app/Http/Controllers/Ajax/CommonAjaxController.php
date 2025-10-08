<?php

namespace App\Http\Controllers\Ajax;

use App\Http\Controllers\Controller;
use App\Models\Item;
use Illuminate\Http\Request;

class CommonAjaxController extends Controller
{
    public function getSpqQuantity(Request $request){
        if($request->ajax()){

            $spqQuantity = Item::where('id', $request->item_id)->first(['spq_quantity']);
            return response()->json( $spqQuantity);

        }
    }

    public function getItemUom(Request $request){
        if($request->ajax()){

            $item = Item::with('uom:id,name')
                        ->find($request->item_id);

            if ($item) {
            return response()->json([
                'spq_quantity' => $item->spq_quantity,
                'uom_name' => $item->uom ? $item->uom->name : null  
            ]);
        }
        }
    }
}
