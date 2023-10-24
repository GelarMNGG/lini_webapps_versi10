<?php

namespace App\Http\Controllers\Admin\Proc;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Auth;
use DB;

class ProcPsychologyTestResultController extends Controller
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
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Tes Psikologi.');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Tes Psikologi.');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Tes Psikologi.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Tes Psikologi.');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Tes Psikologi.');
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
                'status' => 'required',
                'note' => 'required',
            ]);

            $data = $request->except(['_token','_method','submit']);

            $data['assessor_id'] = $userId;
            $data['assessor_type'] = $userType;
            $data['assessor_department'] = $userDepartment;
            $data['assessment_date'] = Carbon::now()->format('Y-m-d H:i:s');

            DB::table('proc_test_psychology_results')->where('id',$id)->update($data);

            return redirect()->back()->with('alert-success','Data berhasil diubah.');
        }
        
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Tes Psikologi.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Tes Psikologi.');
    }
}
