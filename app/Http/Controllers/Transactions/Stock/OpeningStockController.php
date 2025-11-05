<?php

namespace App\Http\Controllers\Transactions\Stock;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class OpeningStockController extends Controller
{
    public function index(){
        return view('transactions.stock.index');
    }
}
