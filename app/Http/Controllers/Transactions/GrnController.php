<?php

namespace App\Http\Controllers\Transactions;

use App\Http\Controllers\Controller;
use App\Models\Grn;
use Illuminate\Http\Request;

class GrnController extends Controller
{
    public function index(){
        $grnNumber = Grn::grnNumber();
        
        return view('transactions.grn.index', compact('grnNumber'));
    }
}
