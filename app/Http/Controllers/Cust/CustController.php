<?php

namespace App\Http\Controllers\Cust;

use App\Customer;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Auth;
use DB;

class CustController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:cust');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $userId = Auth::user()->company_id;

        $data['dataProjects'] = DB::table('projects as proj')
        ->select([
            'proj.*',
            DB::raw('(SELECT id FROM projects_report WHERE projects_report.project_id = proj.id) as pwo_id'),
            DB::raw('(SELECT firstname FROM users WHERE users.id = proj.pm_id) as pm_firstname'),
            DB::raw('(SELECT lastname FROM users WHERE users.id = proj.pm_id) as pm_lastname'),
        ])
        ->where('customer_id',$userId)->limit(5)->get();
        
        $data['projectStatus'] = DB::table('projects_status')->get();

        return view('cust.index',$data);
    }

    public function editPassword()
    {
        $user = Customer::find(Auth::user()->id);
        $userType = $user->user_type;

        return view('cust.change-password');
    }

    public function changePassword(Request $request)
    {
        $user = Customer::find(Auth::user()->id);
        $userType = $user->user_type;

        if(Hash::check($request['oldPassword'], $user->password))
        {
            $user->password = Hash::make($request['password']);
            $user->update();

            return redirect()->back()->with('success','Pasword Anda telah berhasil diperbarui.');
        }else{
            return redirect()->route('cust.edit.password')->with('error','Pasword lama Anda tidak sesuai.');
        }
    }
}
