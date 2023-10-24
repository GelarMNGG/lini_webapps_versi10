<?php

namespace App\Http\Controllers\Auth;

use App\Admin;
use Illuminate\Http\Request;
use App\Http\Controllers\MailController;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use Carbon\Carbon;

class AdminRegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    #protected $redirectTo = '/home';
    protected $redirectTo = '';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->redirectTo = route('admin.dashboard');
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Admin
     */
    public function register(Request $request)
    {
        $user = new Admin();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->verification_code = sha1(time());
        $user->save();

        if ($user != null) {
            MailController::sendSignupEmail($user->name, $user->email, $user->verification_code, 'adminverify');
            return redirect()->back()->with(session()->flash('alert-success','Akun Anda telah dibuat. Silahkan cek email untuk memverifikasi dan mengaktifkan email Anda.'));
        }

        return redirect()->back()->with(session()->flash('alert-danger','Terjadi kesalahan silahkan coba daftar kembali.')); 
    }

    public function verifyUser()
    {
        $verification_code = \Illuminate\Support\Facades\Request::get('code');
        $user = Admin::where(['verification_code' => $verification_code])->first();
        if ($user != null) {
            $user->is_verified = 1;
            $user->active = 1;
            $user->email_verified_at = Carbon::now()->format('Y-m-d H:i:s');
            $user->save();

            return redirect()->route('admin.login')->with(session()->flash('alert-success','Akun Anda telah aktif. Silahkan login menggunakan akun Anda.'));
        }

        return redirect()->route('admin.login')->with(session()->flash('alert-danger','Terjadi kesalahan silahkan coba daftar kembali.')); 
    }

    public function showRegisterForm()
    {
        return view('auth.admin-register');
    }
}
