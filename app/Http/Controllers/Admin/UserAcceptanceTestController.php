<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use DB;

class UserAcceptanceTestController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $userId = Auth::user()->id;
        $userType = Auth::user()->user_type;
        $userCompany = Auth::user()->company_id;
        $userDepartment = Auth::user()->department_id;

        if ($userCompany == 1 && $userDepartment == 5) {
            $data['dataUat'] = DB::table('user_acceptance_test')->paginate(10);
    
            return view('admin.user-acceptance-test.index',$data);
        }
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman User acceptance Test.');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $userId = Auth::user()->id;
        $userType = Auth::user()->user_type;
        $userCompany = Auth::user()->company_id;
        $userDepartment = Auth::user()->department_id;

        if ($userCompany == 1 && $userDepartment == 5) {
            return view('admin.user-acceptance-test.create');
        }
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman User acceptance Test.');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $userId = Auth::user()->id;
        $userType = Auth::user()->user_type;
        $userCompany = Auth::user()->company_id;
        $userDepartment = Auth::user()->department_id;

        if ($userCompany == 1 && $userDepartment == 5) {
            $request->validate([
                'title' => 'required',
                'steps' => 'required',
                ]);
            $data = $request->except(['_token','submit']);
            DB::table('user_acceptance_test')->insert($data);

            return redirect()->route('admin-user-acceptance-test.index')->with('alert-success','Data berhasil disimpan');
        }
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman User acceptance Test.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman User acceptance Test.');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $userId = Auth::user()->id;
        $userType = Auth::user()->user_type;
        $userCompany = Auth::user()->company_id;
        $userDepartment = Auth::user()->department_id;

        if ($userCompany == 1 && $userDepartment == 5) {
            $data['dataUat'] = DB::table('user_acceptance_test')->where('id',$id)->first();
            return view('admin.user-acceptance-test.edit',$data);
        }
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman User acceptance Test.');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $userId = Auth::user()->id;
        $userType = Auth::user()->user_type;
        $userCompany = Auth::user()->company_id;
        $userDepartment = Auth::user()->department_id;

        if ($userCompany == 1 && $userDepartment == 5) {
            $request->validate([
                'title' => 'required',
                ]);
            
            $data = $request->except(['_token','_method','submit']);
            DB::table('user_acceptance_test')->where('id',$id)->update($data);
        
            return redirect()->route('admin-user-acceptance-test.index')->with('alert-success','Data berhasil disimpan');
        }
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman User acceptance Test.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $userId = Auth::user()->id;
        $userType = Auth::user()->user_type;
        $userCompany = Auth::user()->company_id;
        $userDepartment = Auth::user()->department_id;

        if ($userCompany == 1 && $userDepartment == 5) {
               
               //delete from database
               DB::table('user_acceptance_test')->delete($id);
               return redirect()->route('admin-user-acceptance-test.index')->with('alert-success','Data berhasil dihapus.');
        }
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman User acceptance Test.');
    }
}
