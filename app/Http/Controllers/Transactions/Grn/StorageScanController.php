<?php

namespace App\Http\Controllers\Transactions\Grn;

use App\Http\Controllers\Controller;
use App\Models\Grn;
use Illuminate\Http\Request;

class StorageScanController extends Controller
{
    public function index(){
        $grnNumbers = Grn::whereNot('status',2)->get(['grn_number']);
        return view('transactions.storagescan.index', compact('grnNumbers'));
    }

    public function store(Request $request){
       return redirect()->route('storage-scan.show', $request->grn_number);
    }

    public function show($grnNumber){
        $grn = Grn::with('grnSubs.item')->where('grn_number',$grnNumber)->first();
        return view('transactions.storagescan.scan', compact('grnNumber', 'grn'));
    }
}
