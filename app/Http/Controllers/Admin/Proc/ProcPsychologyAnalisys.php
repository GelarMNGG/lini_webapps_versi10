<?php

namespace App\Http\Controllers\Admin\Proc;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use DB;

class ProcPsychologyAnalisys extends Controller
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
        $userRole = Auth::user()->role;
        $userCompany = Auth::user()->company_id;
        $userDepartment = Auth::user()->department_id;

        if($userCompany == 1 && $userDepartment == 9 || $userRole == 1){
            $data['dataCategory'] = DB::table('proc_test_psychology_analisys')->paginate(10);
    
            return view('admin.proc.proc-test-psychology-analisys.index',$data);
        }
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Kategori Analisis.');
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
        $userRole = Auth::user()->role;
        $userCompany = Auth::user()->company_id;
        $userDepartment = Auth::user()->department_id;

        if($userCompany == 1 && $userDepartment == 9 || $userRole == 1){
            return view('admin.proc.proc-test-psychology-analisys.create');
        }
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Kategori Analisis.');
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
        $userRole = Auth::user()->role;
        $userCompany = Auth::user()->company_id;
        $userDepartment = Auth::user()->department_id;

        if($userCompany == 1 && $userDepartment == 9 || $userRole == 1){
            $request->validate([
                'name' => 'required|unique:proc_test_psychology_analisys,name,'.$request->name,
            ]);
            $data = $request->except(['_token','submit']);
            DB::table('proc_test_psychology_analisys')->insert($data);

            return redirect()->route('admin-test-psychology-analisys.index')->with('alert-success','Data berhasil disimpan');
        }
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Kategori Analisis.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Kategori Analisis.');
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
        $userRole = Auth::user()->role;
        $userCompany = Auth::user()->company_id;
        $userDepartment = Auth::user()->department_id;

        if($userCompany == 1 && $userDepartment == 9 || $userRole == 1){
            $data['dataCategory'] = DB::table('proc_test_psychology_analisys')->where('id',$id)->first();
            return view('admin.proc.proc-test-psychology-analisys.edit',$data);
        }
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Kategori Analisis.');
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
        $userRole = Auth::user()->role;
        $userCompany = Auth::user()->company_id;
        $userDepartment = Auth::user()->department_id;

        if($userCompany == 1 && $userDepartment == 9 || $userRole == 1){
            $request->validate([
                'name' => 'required',
                ]);
            $data = $request->except(['_token','_method','submit']);
            DB::table('proc_test_psychology_analisys')->where('id',$id)->update($data);
        
            return redirect()->route('admin-test-psychology-analisys.index')->with('alert-success','Data berhasil disimpan');
        }
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Kategori Analisis.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $userType = Auth::user()->user_type;
        $userRole = Auth::user()->role;
        $userCompany = Auth::user()->company_id;
        $userDepartment = Auth::user()->department_id;

        
        $dataCheck = DB::table('proc_test_psychology_analisys')->where('id',$id)->count();
        
        if($userCompany == 1 && $userDepartment == 9 || $userRole == 1){
            if ($dataCheck > 0) {
                //delete from database
                DB::table('proc_test_psychology_analisys')->delete($id);
        
                return redirect()->route('admin-test-psychology-analisys.index')->with('alert-success', 'Data berhasil dihapus.');
            }
        }
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Kategori Analisis.');
    }
}
