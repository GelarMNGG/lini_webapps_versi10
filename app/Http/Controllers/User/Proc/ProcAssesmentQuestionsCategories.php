<?php

namespace App\Http\Controllers\User\Proc;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use DB;

class ProcAssesmentQuestionsCategories extends Controller
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
            $data['dataCategory'] = DB::table('proc_test_assessment_questions_categories')->paginate(10);
    
            return view('user.proc.proc-assesment-question-category.index',$data);
        }
        
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Kategori.');
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
        
            return view('user.proc.proc-assesment-question-category.create');
        }
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Kategori.');
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
                'name' => 'required|unique:proc_test_assessment_questions_categories,name,'.$request->name,
            ]);
            $data = $request->except(['_token','submit']);
            DB::table('proc_test_assessment_questions_categories')->insert($data);

            return redirect()->route('user-assesment-question-cat.index')->with('alert-success','Data berhasil disimpan');
        }
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Kategori.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Kategori.');
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
            $data['dataCategory'] = DB::table('proc_test_assessment_questions_categories')->where('id',$id)->first();
            return view('user.proc.proc-assesment-question-category.edit',$data);
        }
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Kategori.');
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
                'name' => 'required',
                ]);
            $data = $request->except(['_token','_method','submit']);
            DB::table('proc_test_assessment_questions_categories')->where('id',$id)->update($data);
        
            return redirect()->route('user-assesment-question-cat.index')->with('alert-success','Data berhasil disimpan');
        }
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Kategori.');
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

        $dataCheck = DB::table('proc_test_assessment_questions_categories')->where('id',$id)->count();

        if ($userDepartment == 9) {
            if ($dataCheck > 0) {
                //delete from database
                DB::table('proc_test_assessment_questions_categories')->delete($id);
        
                return redirect()->route('user-assesment-question-cat.index')->with('alert-success', 'Data berhasil dihapus.');
            }
        }
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Kategori.');
    }
}
