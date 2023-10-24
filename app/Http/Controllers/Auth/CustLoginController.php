<?php

namespace App\Http\Controllers\Auth;

use App\Customer;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use DB;

class CustLoginController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest:cust', ['except' => ['logout']]);
    }

    public function showLoginForm()
    {
        return view('auth.cust-login');
    }

    public function login(Request $request)
    {
        $validation = $this->validate($request,[
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);
        
        $active = 1; $verified = 1;
        if(Auth::guard('cust')->attempt(['email' => $request->email, 'password' => $request->password, 'is_verified' => $verified, 'active' => $active], $request->remember)) 
        {
            $data['dataUsers']  = Customer::select()->get();
            $data['notifCount'] = DB::table('notifications')->count();
            return redirect()->intended(route('cust.dashboard', $data));
        }

        return redirect()->back()->withErrors($validation)->withInput($request->only('email','remember'));
    }

    public function logout()
    {
        Auth::guard('cust')->logout();
        return redirect('/');
    }
}
