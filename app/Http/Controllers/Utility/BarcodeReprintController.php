<?php

namespace App\Http\Controllers\Utility;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;

class BarcodeReprintController extends Controller
{
    public function index(){

        return view('utility.barcodereprint');
    }

    public function store(Request $request){
        try{
            dd($request->all());
        } catch(Exception $e){

        }
    }
}
