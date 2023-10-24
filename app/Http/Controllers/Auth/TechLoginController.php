<?php

namespace App\Http\Controllers\Auth;

use App\Tech;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use DB;

class TechLoginController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest:tech', ['except' => ['logout']]);
    }

    public function showLoginForm()
    {
        return view('auth.tech-login');
    }

    public function login(Request $request)
    {
        $validation = $this->validate($request,[
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);
        
        $active = 1; $verified = 1;
        if(Auth::guard('tech')->attempt(['email' => $request->email, 'password' => $request->password, 'is_verified' => $verified, 'active' => $active], $request->remember)) 
        {
            $data['dataUsers']  = Tech::select()->get();
            $data['notifCount'] = DB::table('notifications')->count();
            return redirect()->intended(route('tech.dashboard', $data));
        }

        return redirect()->back()->withErrors($validation)->withInput($request->only('email','remember'));
    }

    public function logout(Request $request)
    {
        $active = $request->active;
        $techId = $request->tech_id;
        if (isset($active)) {
            $data['active'] = $active;
            DB::table('techs')->where('id',$techId)->update($data);
        }

        Auth::guard('tech')->logout();
        return redirect('/');
    }
}
