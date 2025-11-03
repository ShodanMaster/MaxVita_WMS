<?php

namespace App\Http\Controllers\Ajax;

use App\Http\Controllers\Controller;
use App\Models\Barcode;
use App\Models\Bin;
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

    public function binExists(Request $request){
        if($request->ajax()){
            $bin = Bin::where('bin_code', $request->bin)->first();

            if($bin){
                return response()->json([
                    'status' => 200,
                    'message' => 'Bin Exists'
                ]);
            }

            return response()->json([
                'status' => 404,
                'message' => 'Bin Does Not Exists!'
            ]);
        }
    }

    public function itemInStock(Request $request){
        if($request->ajax()){
            $itemCount = Barcode::where('item_id', $request->item_id)
                    ->where('status', '1')
                    ->sum('grn_net_weight');


            $data = [];
            if($itemCount > 0){
                $data = [
                    'in_stock' => true,
                    'count' => $itemCount
                ];
            }else{
                $data = [
                    'in_stock' => false,
                    'count' => $itemCount
                ];
            }

            return response()->json($data);
        }
    }
}
