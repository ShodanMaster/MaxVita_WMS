<?php

namespace App\Http\Controllers\Transactions;

use App\Http\Controllers\Controller;
use App\Models\Grn;
use App\Models\Item;
use App\Models\Location;
use App\Models\Vendor;
use Illuminate\Http\Request;

class GrnController extends Controller
{
    public function index(){
        $grnNumber = Grn::grnNumber();
        $vendors = Vendor::get(['id', 'name']);
        $locations = Location::get(['id', 'name']);
        $items = Item::get(['id', 'item_code', 'name']);
        return view('transactions.grn.index', compact('grnNumber', 'items'));
    }
}
