<?php

namespace App\Http\Controllers\Utility;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use RealRashid\SweetAlert\Facades\Alert;

class PasswordChangeController extends Controller
{
    public function index(){
        return view('utility.passwordchange');
    }

    public function store(Request $request){
        // dd($request->all());
        $request->validate([
            'currentPassword' => 'required|string|min:8',
            'password' => 'required|string|min:8|confirmed',
        ]);

        try{
            $user = auth()->user();

            if(!Hash::check($request->currentPassword, $user->password)){
                Alert::toast('Current Password is Wrong', 'error')->autoClose(3000);
                return redirect()->back();
            }

            $user->update([
                'password' => Hash::make($request->password),
            ]);

            Alert::toast('Password Changed Successfully.', 'success')->autoClose(3000);
            return redirect()->back();
        } catch (Exception $e) {
            dd($e);
            Log::error('Password Change Error: ' . $e->getMessage());
            Alert::toast('An error occurred while changing user password.', 'error')->autoClose(3000);
            return redirect()->back();
        }
    }
}
