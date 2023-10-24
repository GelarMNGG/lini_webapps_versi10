<?php

namespace App\Http\Controllers\User\Proc;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use DB;

class ProcPsychologyAnalisys extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth' => 'verified']);
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
        $userDepartment = Auth::user()->department_id;
        $userCompany = Auth::user()->company_id;
        $userLevel = Auth::user()->user_level;

        if ($userCompany == 1 && $userDepartment == 9 && $userLevel == 22) {
            $data['dataCategory'] = DB::table('proc_test_psychology_analisys')->paginate(10);
    
            return view('user.proc.proc-test-psychology-analisys.index',$data);
        }
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
        $userDepartment = Auth::user()->department_id;
        $userCompany = Auth::user()->company_id;
        $userLevel = Auth::user()->user_level;

        if ($userCompany == 1 && $userDepartment == 9 && $userLevel == 22) {
        
            return view('user.proc.proc-test-psychology-analisys.create');
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
        $userDepartment = Auth::user()->department_id;
        $userCompany = Auth::user()->company_id;
        $userLevel = Auth::user()->user_level;


        if ($userCompany == 1 && $userDepartment == 9 && $userLevel == 22) {
            $request->validate([
                'name' => 'required|unique:proc_test_psychology_analisys,name,'.$request->name,
            ]);
            $data = $request->except(['_token','submit']);
            DB::table('proc_test_psychology_analisys')->insert($data);

            return redirect()->route('user-test-psychology-analisys.index')->with('alert-success','Data berhasil disimpan');
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
        $userDepartment = Auth::user()->department_id;
        $userCompany = Auth::user()->company_id;
        $userLevel = Auth::user()->user_level;

        if ($userCompany == 1 && $userDepartment == 9 && $userLevel == 22) {
            $data['dataCategory'] = DB::table('proc_test_psychology_analisys')->where('id',$id)->first();
            return view('user.proc.proc-test-psychology-analisys.edit',$data);
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
        $userDepartment = Auth::user()->department_id;
        $userCompany = Auth::user()->company_id;
        $userLevel = Auth::user()->user_level;

        if ($userCompany == 1 && $userDepartment == 9 && $userLevel == 22) {
            $request->validate([
                'name' => 'required',
                ]);
            $data = $request->except(['_token','_method','submit']);
            DB::table('proc_test_psychology_analisys')->where('id',$id)->update($data);
        
            return redirect()->route('user-test-psychology-analisys.index')->with('alert-success','Data berhasil disimpan');
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
        $userDepartment = Auth::user()->department_id;
        $userCompany = Auth::user()->company_id;
        $userLevel = Auth::user()->user_level;

        $dataCheck = DB::table('proc_test_psychology_analisys')->where('id',$id)->count();

        if ($userCompany == 1 && $userDepartment == 9 && $userLevel == 22) {
            if ($dataCheck > 0) {
                //delete from database
                DB::table('proc_test_psychology_analisys')->delete($id);
        
                return redirect()->route('user-test-psychology-analisys.index')->with('alert-success', 'Data berhasil dihapus.');
            }
        }
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Kategori Analisis.');
    }
}
