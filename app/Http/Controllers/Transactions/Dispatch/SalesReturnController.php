<?php

namespace App\Http\Controllers\Transactions\Dispatch;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;

class SalesReturnController extends Controller
{
    public function index(){
        $customers = Customer::get(['id', 'name']);
        return view('transactions.dispatch.salesreturn', compact('customers'));
    }


}
