<?php

namespace App\Http\Controllers\Transactions;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Grn;
use App\Models\Item;
use App\Models\Location;
use App\Models\Vendor;
use Illuminate\Http\Request;

class GrnController extends Controller
{
    public function index(){
        $grnNumber = Grn::grnNumber();
        $batchNumber = 'B' . date("ymd");
        $vendors = Vendor::get(['id', 'name']);
        $locations = Location::get(['id', 'name']);
        $categories = Category::get(['id', 'name']);
        $items = Item::get(['id', 'item_code', 'name']);
        return view('transactions.grn.index', compact('grnNumber', 'batchNumber', 'vendors', 'locations', 'categories', 'items'));
    }

    public function store(Request $request){
        dd($request->all());
    }
}
