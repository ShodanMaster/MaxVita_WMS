<?php

namespace App\Http\Controllers\Ajax;

use App\Http\Controllers\Controller;
use App\Models\OpeningStock;
use Illuminate\Http\Request;

class OpeningStockAjaxController extends Controller
{
    public function getItems(Request $request){
        if($request->ajax()){

            $openingStock = OpeningStock::findOrFail($request->id);

            $data = $openingStock->openingStockSubs->where('status', 0)->map(function($sub){

                        return [
                            'item_id' => $sub->item->id,
                            'item_name' => $sub->item->item_code . '/' . $sub->item->name
                        ];
                    })->values();;

            return response()->json($data);
        }
    }
}
