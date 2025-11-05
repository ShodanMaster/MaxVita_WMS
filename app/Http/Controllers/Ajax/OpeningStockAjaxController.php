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

            $data = $openingStock->openingStockSubs->map(function($o){

                        return [
                            'item_id' => $o->item->id,
                            'item_name' => $o->item->item_code . '/' . $o->item->name
                        ];
                    });

            return response()->json($data);
        }
    }
}
