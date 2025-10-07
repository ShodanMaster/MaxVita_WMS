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
}
