<?php

namespace App\Http\Controllers\Utility;

use App\Http\Controllers\Controller;
use App\Models\Barcode;
use App\Models\Grn;
use App\Models\ProductionPlan;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

use function Illuminate\Log\log;

class BarcodeReprintController extends Controller
{
    public function index(){
        return view('utility.barcodereprint');
    }

    public function store(Request $request){

        $request->validate([
            'transaction_type' => 'required|in:grn,production',
            'document_number' => 'required|string|max:255',
            'barcode_from' => 'nullable|exists:barcodes,serial_number',
            'barcode_to' => 'nullable|exists:barcodes,serial_number|gte:barcode_from',
        ]);

        try{

            $transactionType = $request->transaction_type === 'grn' ? 1 : 2;

            $query = Barcode::where('transaction_type', $transactionType)->where('transaction_id', $request->document_number);

            if($request->barcode_from){
                $query->where('serial_number', '>=', $request->barcode_from);
            }

            if($request->barcode_to){
                $query->where('serial_number', '<=', $request->barcode_to);
            }

            $data = $query->get();

            $transactionNumber = null;

            if($request->transaction_type === 'grn'){
                $transactionNumber = Grn::find($request->document_number)->grn_number;
            }else{
                $transactionNumber = ProductionPlan::find($request->document_number)->plan_number;
            }

            $contents = $data->map(function ($d) use ($transactionNumber) {
                return [
                    'transaction_number' => $transactionNumber,
                    'barcode' => $d->serial_number,
                    'item_name' => $d->item->name ?? null,
                    'spq_quantity' => $d->grn_spq_quantity,
                ];
            })->toArray();

            if(empty($contents)){
                return redirect()->back()->with('error', 'No Data Found');
            }

            Session::put('contents', $contents);

            return redirect()->back()->with('success', 'Reprint Successful');

        } catch(Exception $e){
            return redirect()->back()->with('error', 'Something Went Wrong!');
        }
    }
}
