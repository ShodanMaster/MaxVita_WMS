<?php

namespace App\Http\Controllers\Transactions\Receipt;

use App\Http\Controllers\Controller;
use App\Models\Dispatch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReceiptScanController extends Controller
{
    public function index(){
        $user = Auth::user();
        $dispatchNumbers = Dispatch::where('status', 2)
                        ->where('dispatch_to_id', $user->location_id)
                        ->where('dispatch_type', 'transfer')
                        ->get(['id', 'dispatch_number']);
        return view('transactions.receipt.index', compact('dispatchNumbers'));
    }

    public function store(Request $request){
        return redirect()->route('receipt-scan.show', $request->dispatch_number);
    }

    public function show($id){
        $dispatch = Dispatch::with('dispatchSubs.item')->where('id', $id)->first(['dispatch_number']);
        return view('transactions.receipt.scan', compact('id', 'dispatch'));
    }

}
