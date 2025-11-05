<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class DashboardController extends Controller
{
    public function index(){
        return view('dashboard');
    }


    public function printBarcode()
    {
        $contents = Session::pull('contents');
        // dd($contents);
        if (!$contents) {
            abort(404, 'No barcode data found');
        }
        // dd($grn);
        return view('transactions.grn.printbarcode', compact('contents'));
    }
}
