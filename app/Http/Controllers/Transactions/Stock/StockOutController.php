<?php

namespace App\Http\Controllers\Transactions\Stock;

use App\Http\Controllers\Controller;
use App\Models\Reason;
use Illuminate\Http\Request;

class StockOutController extends Controller
{
    public function index(){
        $reasons = Reason::get(['id', 'reason']);
        return view('transactions.stock.stockout', compact('reasons'));
    }

    public function store(Request $request){
        if($request->ajax()){

        }
    }
}
