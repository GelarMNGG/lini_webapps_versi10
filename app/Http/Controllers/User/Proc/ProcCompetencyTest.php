<?php

namespace App\Http\Controllers\User\Proc;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use DB;

class ProcCompetencyTest extends Controller
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

        if ($userDepartment == 9) {
            $data['dataQuestions'] = DB::table('proc_test_competency')->paginate(10);
    
            return view('user.proc.proc-test-competency.index',$data);
        }
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Tes Kompetensi.');
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

        if ($userDepartment == 9) {
            $data['dataQuestions'] = DB::table('proc_test_competency')->get();
            return view('user.proc.proc-test-competency.create',$data);
        }
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Tes Kompetensi.');
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

        if ($userDepartment == 9) {
            $request->validate([
                'question' => 'required',
                'answer' => 'required',
                ]);
            $data = $request->except(['_token','submit']);
            DB::table('proc_test_competency')->insert($data);

            return redirect()->route('user-proc-test-competency.index')->with('alert-success','Data berhasil disimpan');
        }                     
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Tes Kompetensi.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Tes Kompetensi.');
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

        if ($userDepartment == 9) {
            $data['dataQuestion'] = DB::table('proc_test_competency')->where('id',$id)->first();
            return view('user.proc.proc-test-competency.edit',$data);
        }
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Tes Kompetensi.');
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

        if ($userDepartment == 9) {
            $request->validate([
                'question' => 'required',
                'answer' => 'required',
                ]);
            
            $data = $request->except(['_token','_method','submit']);
            DB::table('proc_test_competency')->where('id',$id)->update($data);
        
            return redirect()->route('user-proc-test-competency.index')->with('alert-success','Data berhasil disimpan');
        }
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Tes Kompetensi.');
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
        $userDepartment = Auth::user()->department_id;

        if ($userDepartment == 9) {
               
               //delete from database
               DB::table('proc_test_competency')->delete($id);
               return redirect()->route('user-proc-test-competency.index')->with('alert-success','Data berhasil dihapus.');
        }
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Tes Kompetensi.');
    }
}
