<?php

namespace App\Http\Controllers\Tech;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Auth;
use DB;

class TechTestCompetencyController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:tech');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Competency Test.');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Competency Test.');
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

        $firstCheck = DB::table('proc_test_competency_results')->where('tech_id',$userId)->count();

        if ($firstCheck < 1) {
            //grabbing data
            $data = $request->except(['_token','submit','question_count']);
    
            $questionIds = array_keys($data);
            $answerIds = array_values($data);
    
            //getting test total value
            $dataAnswers = DB::table('proc_test_competency')->get();
            $totalNilai = 0;
            foreach ($dataAnswers as $dataAnswer) {
                foreach ($data as $questionNo => $Value) {
                    if($dataAnswer->id == $questionNo && $dataAnswer->answer == $Value){
                        $totalNilai++;
                    }
                }
            }
    
            //filtering the data
            $dataInput['tech_id'] = $userId;
            $dataInput['created_at'] = Carbon::now()->format('Y-m-d H:i:s');
            $dataInput['question_id'] = serialize($questionIds);
            $dataInput['answer'] = serialize($answerIds);
            $totalQuestion = $request->question_count;
            $dataInput['result'] = ($totalNilai/$totalQuestion) * 100;
    
            DB::table('proc_test_competency_results')->insert($dataInput);

            return redirect()->route('tech-test-training.index')->with('alert-success','Selamat Anda telah berhasil menyelesaikan tes kompetensi.');
        }

        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Competency Test.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Competency Test.');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Competency Test.');
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
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Competency Test.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Competency Test.');
    }
}
