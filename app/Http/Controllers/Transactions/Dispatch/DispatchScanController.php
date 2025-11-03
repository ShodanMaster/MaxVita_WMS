<?php

namespace App\Http\Controllers\Transactions\Dispatch;

use App\Http\Controllers\Controller;
use App\Models\Dispatch;
use Illuminate\Http\Request;

class DispatchScanController extends Controller
{
    public function index(){
        $dispatchNumbers = Dispatch::where('status', 0)->get(['id', 'dispatch_number']);
        return view('transactions.dispatch.dispatchscanindex', compact('dispatchNumbers'));
    }

    public function store(Request $request){
        return redirect()->route('dispatch-scan.show', $request->dispatch_number);
    }

    public function show($id){
        $dispatch = Dispatch::with('dispatchSubs.item')->find($id, ['dispatch_number']);
        return view('transactions.dispatch.dispatchscan', compact('id', 'dispatch'));
    }
}
