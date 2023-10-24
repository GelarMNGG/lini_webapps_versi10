<?php

namespace App\Http\Controllers\Admin\Proc;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use DB;

class ProcAssesmentQuestions extends Controller
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
            $data['dataQuestions'] = DB::table('proc_test_assessment_questions as ptaq')
            ->select([
                'ptaq.*',
                DB::raw('(SELECT id FROM proc_test_assessment_answers WHERE proc_test_assessment_answers.question_id = ptaq.id) as answer_id'),
                DB::raw('(SELECT answer_a FROM proc_test_assessment_answers WHERE proc_test_assessment_answers.question_id = ptaq.id) as answer_a'),
                DB::raw('(SELECT answer_b FROM proc_test_assessment_answers WHERE proc_test_assessment_answers.question_id = ptaq.id) as answer_b'),
                DB::raw('(SELECT answer_c FROM proc_test_assessment_answers WHERE proc_test_assessment_answers.question_id = ptaq.id) as answer_c'),
                DB::raw('(SELECT answer_d FROM proc_test_assessment_answers WHERE proc_test_assessment_answers.question_id = ptaq.id) as answer_d'),
                DB::raw('(SELECT name FROM proc_test_assessment_questions_categories WHERE proc_test_assessment_questions_categories.id = ptaq.cat_id) as category_name')
            ])
            ->paginate(10);
    
            return view('admin.proc.proc-assesment-question.index',$data);
        }

        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Assesment.');
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
            $data['questionCats'] = DB::table('proc_test_assessment_questions_categories')->get();
            return view('admin.proc.proc-assesment-question.create',$data);
        }
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Assesment.');
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
                    'question' => 'required',
                    ]);
               $data = $request->except(['_token','submit']);
               DB::table('proc_test_assessment_questions')->insert($data);

            return redirect()->route('admin-proc-assesment-question.index')->with('alert-success','Data berhasil disimpan');
        }
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Assesment.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Assesment.');
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
            $data['questionCats'] = DB::table('proc_test_assessment_questions_categories')->get();
            $data['dataQuestion'] = DB::table('proc_test_assessment_questions')->where('id',$id)->first();
            $data['dataAnswer'] = DB::table('proc_test_assessment_answers')->where('question_id',$id)->first();

            return view('admin.proc.proc-assesment-question.edit',$data);
        }
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Assesment.');
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
                'question' => 'required',
                'cat_id' => 'required'
                ]);
            
            $data = $request->except(['_token','_method','submit']);
            DB::table('proc_test_assessment_questions')->where('id',$id)->update($data);
        
            return redirect()->route('admin-proc-assesment-question.index')->with('alert-success','Data berhasil disimpan');
        }
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Assesment.');
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
        $userRole = Auth::user()->role;
        $userCompany = Auth::user()->company_id;
        $userDepartment = Auth::user()->department_id;

        if($userCompany == 1 && $userDepartment == 9 || $userRole == 1){
               //delete from database
               DB::table('proc_test_assessment_questions')->delete($id);
               return redirect()->route('admin-proc-assesment-question.index')->with('alert-success','Data berhasil dihapus.');
        }   

        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Assesment.');
    }
}
