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
            $barcodes = Barcode::where('item_id', $request->item_id)
                    ->where('status', '1');

            $itemCount = 0;
            if($request->type == 'dispatch'){
                $itemCount = $barcodes->count();
            }elseif($request->type == 'production'){
                $itemCount = $barcodes->sum('net_weight');
            }

            if ($request->type === 'dispatch') {
                $itemCount = $barcodes->count();
            } elseif ($request->type === 'production') {
                $itemCount = $barcodes->sum('net_weight');
            } else {
                return response()->json([
                    'in_stock' => false,
                    'count' => 0,
                    'message' => 'Invalid type parameter.'
                ], 400);
            }

            // Determine stock status
            $inStock = $itemCount > 0;

            return response()->json([
                'in_stock' => $inStock,
                'count' => $itemCount
            ]);
        }
    }
}
