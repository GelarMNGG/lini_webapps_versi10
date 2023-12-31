<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Auth;
use DB;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */

    #protected $redirectTo = '';
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->redirectTo = route('user.index');
        $this->middleware('guest', ['except' => ['logout','userLogout']]);
    }

    public function credentials(Request $request)
    {
        return array_merge($request->only($this->username(), 'password', ['is_verified' => 1]));
    }
    
    public function userLogout(Request $request)
    {
        Auth::guard('web')->logout();
        return redirect('/');
    }
}
