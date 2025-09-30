<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;

class AjaxController extends Controller
{
    public function getSpqQuantity(Request $request){
        if($request->ajax()){
            $spqQuantity = Item::where('id', $request->item_id)->first(['spq_quantity']);

            return response()->json( $spqQuantity);
        }
    }
}
