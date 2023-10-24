<?php

namespace App\Http\Controllers\Auth;

use App\Admin;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use DB;

class AdminLoginController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest:admin', ['except' => ['logout']]);
    }

    public function showLoginForm()
    {
        return view('auth.admin-login');
    }

    public function login(Request $request)
    {
        $validation = $this->validate($request,[
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);
        
        $active = 1; $verified = 1;
        if(Auth::guard('admin')->attempt(['email' => $request->email, 'password' => $request->password, 'is_verified' => $verified, 'active' => $active], $request->remember)) 
        {
            $data['dataUsers']  = Admin::select()->get();
            $data['notifCount'] = DB::table('notifications')->count();
            return redirect()->intended(route('admin.dashboard', $data));
        }

        return redirect()->back()->withErrors($validation)->withInput($request->only('email','remember'));
    }

    public function logout()
    {
        Auth::guard('admin')->logout();
        return redirect('/');
    }
}
