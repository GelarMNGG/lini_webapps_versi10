<?php

namespace App\Http\Controllers\User\Proc;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use DB;

class ProcAssesmentAnswers extends Controller
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
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Tambah Jawaban.');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $userId = Auth::user()->id;
        $userType = Auth::user()->user_type;
        $userDepartment = Auth::user()->department_id;
        $question_id = $request->qid;

        #first check
        $firstCheck = DB::table('proc_test_assessment_questions')->where('id',$question_id)->count();

        if ($firstCheck < 1) {
            return redirect()->back()->with('alert-danger','Halaham yang akan Anda tuju tidak tersedia');
        }

        if ($userDepartment == 9) {
            $data['dataQuestion'] = DB::table('proc_test_assessment_questions')->where('id',$question_id)->first();
            $data['questionId'] = $question_id;
            return view('user.proc.proc-assesment-answer.create',$data);
        }
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Tambah Jawaban.');
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
                    'answer_a' => 'required',
                    'answer_b' => 'required',
                    'answer_c' => 'required',
                    'answer_d' => 'required',
                    ]);
               $data = $request->except(['_token','submit']);
               DB::table('proc_test_assessment_answers')->insert($data);

            return redirect()->route('user-proc-assesment-question.index')->with('alert-success','Data berhasil disimpan');
        }
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Tambah Jawaban.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Tambah Jawaban.');
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
            $data['dataAnswer'] = DB::table('proc_test_assessment_answers as ptaa')
            ->select([
                'ptaa.*',
                DB::raw('(SELECT question FROM proc_test_assessment_questions WHERE proc_test_assessment_questions.id = ptaa.question_id) as question')
            ])
            ->where('id',$id)->first();

            return view('user.proc.proc-assesment-answer.edit',$data);
        }
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Tambah Jawaban.');
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
                'answer_a' => 'required',
                'answer_b' => 'required',
                'answer_c' => 'required',
                'answer_d' => 'required',
                ]);
            
            $data = $request->except(['_token','_method','submit']);
            DB::table('proc_test_assessment_answers')->where('id',$id)->update($data);
        
            return redirect()->route('user-proc-assesment-question.index')->with('alert-success','Data berhasil disimpan');
        }
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Tambah Jawaban.');
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
               DB::table('proc_test_assessment_answers')->delete($id);
               return redirect()->route('user-proc-assesment-question.index')->with('alert-success','Data berhasil dihapus.');
        }
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Tambah Jawaban.');
    }
}
