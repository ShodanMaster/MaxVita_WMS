<?php

namespace App\Http\Controllers\Transactions\Stock;

use App\Http\Controllers\Controller;
use App\Models\Barcode;
use App\Models\Reason;
use App\Models\StockOut;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StockOutController extends Controller
{
    public function index(){
        $reasons = Reason::get(['id', 'reason']);
        return view('transactions.stock.stockout', compact('reasons'));
    }

    public function store(Request $request){
        if($request->ajax()){

            $userId = Auth::id();
            $reason = Reason::find($request->reason_id);
            // dd($reason);
            if(!$reason){
                return response()->json([
                    'status' => 404,
                    'message' => 'Reason Not Found'
                ]);
            }

            $barcode = Barcode::where('serial_number', $request->barcode)->first();

            if(!$barcode){
                return response()->json([
                    'status' => 404,
                    'message' => 'Barcode not found.'
                ]);
            }

            switch ($barcode->status) {
                case -1:
                    return response()->json([
                        'status' => 409,
                        'message' => 'Barcode Not in Stock!'
                    ]);
                case 2:
                    return response()->json([
                        'status' => 409,
                        'message' => 'Barcode Already Dispatched!'
                    ]);
                case 2:
                    return response()->json([
                        'status' => 409,
                        'message' => 'Barcode Already Transfered!'
                    ]);
                case 8:
                    return response()->json([
                        'status' => 404,
                        'message' => 'Barcode Does not Exist!'
                    ]);
            }

            $barcode->update([
                'status' => '8'
            ]);

            $stockOut = StockOut::create([
                'barcode' => $barcode->serial_number,
                'reason_id' => $reason->id,
                'user_id' => $userId
            ]);

            return response()->json([
                'status' => 200,
                'message' => 'Stock Out Successful'
            ]);

        }
    }
}
