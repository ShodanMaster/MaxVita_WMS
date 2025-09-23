<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function index(){
        return view('auth.login');
    }

    public function store(Request $request){
        try{
            // dd($request->all());
            $request->validate([
                'username' => 'required|string',
                'password' => 'required|string',
            ]);

            $credentials = [
                'username' => $request->username,
                'password' => $request->password,
            ];

            if(Auth::attempt($credentials, $request->rememberMe)){
                return redirect()->route('dashboard')->with('success', 'Logged In Succesfully');
            }

            return redirect()->back()->with('warning', 'Wrong Credentials');

        }catch(\Exception $e){
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function logout(Request $request){
        Auth::logout();

        return redirect('/login')->with('success', 'Logged out Succesfully');
    }
}
