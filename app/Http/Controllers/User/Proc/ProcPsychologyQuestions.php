<?php

namespace App\Http\Controllers\User\Proc;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use DB;

class ProcPsychologyQuestions extends Controller
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
            $data['dataQuestions'] = DB::table('proc_test_psychology_questions as ptpq')
            ->select([
                'ptpq.*',
                DB::raw('(SELECT answer_a FROM proc_test_psychology_answers WHERE proc_test_psychology_answers.question_id = ptpq.id) as answer_a'),
                DB::raw('(SELECT answer_b FROM proc_test_psychology_answers WHERE proc_test_psychology_answers.question_id = ptpq.id) as answer_b'),
                DB::raw('(SELECT name FROM proc_test_psychology_questions_categories WHERE proc_test_psychology_questions_categories.id = ptpq.question_cat) as category_name'),
            ])->paginate(10);

            #dd($data['dataQuestionsCategories']);

            return view('user.proc.proc-test-psychology.index',$data);
        }
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Tes Psikologi.');
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
            $data['dataQuestions'] = DB::table('proc_test_psychology_questions')->get();
            $data['questionCats'] = DB::table('proc_test_psychology_questions_categories')->get();
            return view('user.proc.proc-test-psychology.create',$data);
        }
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
        $userId = Auth::user()->id;
        $userType = Auth::user()->user_type;
        $userDepartment = Auth::user()->department_id;

        if ($userDepartment == 9) {
                $request->validate([
                    'question' => 'required',
                    ]);
               $data = $request->except(['_token','submit']);
               DB::table('proc_test_psychology_questions')->insert($data);

            return redirect()->route('user-proc-test-psychology.index')->with('alert-success','Data berhasil disimpan');
        }
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
        $userId = Auth::user()->id;
        $userType = Auth::user()->user_type;
        $userDepartment = Auth::user()->department_id;

        if ($userDepartment == 9) {
            $data['questionCats'] = DB::table('proc_test_psychology_questions_categories')->get();
            $data['dataQuestion'] = DB::table('proc_test_psychology_questions')->where('id',$id)->first();
            return view('user.proc.proc-test-psychology.edit',$data);
        }
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
        $userDepartment = Auth::user()->department_id;

        if ($userDepartment == 9) {
            $request->validate([
                'question' => 'required',
                'question_cat' => 'required'
                ]);
            
            $data = $request->except(['_token','_method','submit']);
            DB::table('proc_test_psychology_questions')->where('id',$id)->update($data);
        
            return redirect()->route('user-proc-test-psychology.index')->with('alert-success','Data berhasil disimpan');
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
        $userId = Auth::user()->id;
        $userType = Auth::user()->user_type;
        $userDepartment = Auth::user()->department_id;

        if ($userDepartment == 9) {
               
               //delete from database
               DB::table('proc_test_psychology_questions')->delete($id);
               return redirect()->route('user-proc-test-psychology.index')->with('alert-success','Data berhasil dihapus.');
        }
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Tes Psikologi.');
    }
}
