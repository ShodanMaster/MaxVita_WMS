<?php

namespace App\Http\Controllers\Transactions\Grn;

use App\Http\Controllers\Controller;
use App\Models\Grn;
use Illuminate\Http\Request;

class StorageScanController extends Controller
{
    public function index(){
        $grnNumbers = Grn::where('status',0)->get(['id', 'grn_number']);
        return view('transactions.storagescan.index', compact('grnNumbers'));
    }

    public function store(Request $request){
       return redirect()->route('storage-scan.show', $request->grn_number);
    }

    public function show($id){
        $grn = Grn::with('grnSubs.item')->where('id',$id)->first();
        return view('transactions.storagescan.scan', compact('id', 'grn'));
    }
}
