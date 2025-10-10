<?php

namespace App\Http\Controllers\Transations;

use App\Http\Controllers\Controller;
use App\Models\Grn;
use Illuminate\Http\Request;

class StorageScanController extends Controller
{
    public function index(){
        $grnNumbers = Grn::whereNot('status',2)->get(['id', 'grn_number']);
        return view('transactions.storagescan.index', compact('grnNumbers'));
    }
}
