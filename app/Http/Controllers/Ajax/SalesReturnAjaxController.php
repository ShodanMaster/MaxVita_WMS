<?php

namespace App\Http\Controllers\Ajax;

use App\Http\Controllers\Controller;
use App\Models\Barcode;
use App\Models\Bin;
use App\Models\Customer;
use App\Models\DispatchScan;
use App\Models\SalesReturn;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SalesReturnAjaxController extends Controller
{
    public function withBarcodeData(Request $request){
        if($request->ajax()){
            // dd($request->all());

            $barcode = Barcode::where('serial_number', $request->barcode)->where('status', '2')->first();

            if(!$barcode){
                return response()->json([
                    'status' => 404,
                    'message' => 'Barcode not found.'
                ]);
            }

            switch ($barcode->status) {
                case 1:
                    return response()->json([
                        'status' => 409,
                        'message' => 'Already Scanned!'
                    ]);
                case 4:
                    return response()->json([
                        'status' => 409,
                        'message' => 'Already Transfered!'
                    ]);
                case 8:
                    return response()->json([
                        'status' => 404,
                        'message' => 'Does not Exist!'
                    ]);
            }

            $bin = Bin::where('bin_code', $request->bin)->first();

            if(!$bin){
                return response()->json([
                    'status' => 404,
                    'message' => 'Bin not found.'
                ]);
            }

            $customer = Customer::find($request->customer_id);

            if(!$customer){
                return response()->json([
                    'status' => 404,
                    'message' => 'Customer not found.'
                ]);
            }

            $dispatchScan = DispatchScan::where('barcode', $barcode->serial_number)->first();

            if($customer->id != $dispatchScan->dispatch->dispatchTo->id){
                return response()->json([
                    'status' => 404,
                    'message' => 'This item does not belong to this customer.'
                ]);
            }

            $data = [
                'customer_name' => $customer->name,
                'dispatch_number' => $dispatchScan->dispatch->dispatch_number,
                'item_name' => $barcode->item->name,

                'input_data' => [
                    'barcode' => $barcode->serial_barcode,
                    'customer_id' => $customer->id,
                    'dispatch_id' => $dispatchScan->dispatch_id,
                    'item_id' => $barcode->item_id,
                    'bin_id' => $bin->id,
                    'spq' => $request->spq,
                ],
            ];

            return response()->json([
                'status' => 200,
                'message' => 'data fetched',
                'data' => $data,
            ]);

        }
    }

    public function itemReturn(Request $request){
        if($request->ajax()){
            // dd($request->spq);
            $barcode = Barcode::where('serial_number', $request->barcode)->where('status', '2')->first();

            if(!$barcode){
                return response()->json([
                    'status' => 404,
                    'message' => 'Barcode not found.'
                ]);
            }

            $user =Auth::user();

            DB::beginTransaction();

            $salesReturn = SalesReturn::Create([
                'return_number' => SalesReturn::withNumber(),
                'barcode' => $request->barcode,
                'spq' => $request->spq,
                'customer_id' => $request->customer_id,
                'dispatch_id' => $request->dispatch_id,
                'item_id' => $request->item_id,
                'bin_id' => $request->bin_id,
                'user_id' => $user->id,
            ]);

            $barcode->update([
                'spq' => $request->spq,
                'branch_id' => $user->branch_id,
                'location_id' => $user->location_id,
                'bin_id' => $request->bin_id,
                'status' => '1',
            ]);

            DB::commit();

            return response()->json([
                'status' => 200,
                'message' => 'Return Scan Successful',
            ]);
        }
    }

    public function fetchBins(Request $request){
        if($request->ajax()){
            $bins = Bin::where('location_id', $request->location_id)->get(['id', 'bin_code', 'name']);

            $data = $bins->map(function($bin){
                return [
                    'id' => $bin->id,
                    'name' => $bin->bin_code . '/' . $bin->name
                ];
            });
            return response()->json($data);
        }
    }
}
