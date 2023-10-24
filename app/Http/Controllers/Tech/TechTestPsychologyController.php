<?php

namespace App\Http\Controllers\Tech;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Auth;
use DB;

class TechTestPsychologyController extends Controller
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
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Psychology Test.');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Psychology Test.');
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

        $firstCheck = DB::table('proc_test_psychology_results')->where('tech_id',$userId)->count();

        if ($firstCheck < 1) {
            //grabbing data 
            $data = $request->except(['_token','submit']);
    
            $questionIds = array_keys($data);
            $answerIds = array_values($data);
    
            //getting personality type
            $dataExIns = DB::table('proc_test_psychology_questions')->select('id')->where('question_cat',1)->get()->pluck('id')->toArray();
            $dataSeIns = DB::table('proc_test_psychology_questions')->select('id')->where('question_cat',2)->get()->pluck('id')->toArray();
            $dataThFes = DB::table('proc_test_psychology_questions')->select('id')->where('question_cat',3)->get()->pluck('id')->toArray();
            $dataJuPes = DB::table('proc_test_psychology_questions')->select('id')->where('question_cat',4)->get()->pluck('id')->toArray();

            //extrovert & introvert
            $dataExtroverts = array_fill_keys($dataExIns,'a');
            $dataIntroverts = array_fill_keys($dataExIns,'b');
            $dataExInsCount = count($dataExIns);
            //sensing & intuiting
            $dataSensings = array_fill_keys($dataSeIns,'a');
            $dataIntuitings = array_fill_keys($dataSeIns,'b');
            $dataSeInsCount = count($dataSeIns);
            //thingking & feeling
            $dataThinkings = array_fill_keys($dataThFes,'a');
            $dataFeelings = array_fill_keys($dataThFes,'b');
            $dataThFesCount = count($dataThFes);
            //judging & perceiving
            $dataJudgings = array_fill_keys($dataJuPes,'a');
            $dataPerceivings = array_fill_keys($dataJuPes,'b');
            $dataJuPesCount = count($dataJuPes);

            ###comparing & counting the data
            //extrovert & introvert
            $countEx1 = count(array_intersect_assoc($dataExtroverts, $data));
            $countIn1 = count(array_intersect_assoc($dataIntroverts, $data));
            $countEx = count(array_intersect_assoc($dataExtroverts, $data))/$dataExInsCount * 100;
            $countIn = count(array_intersect_assoc($dataIntroverts, $data))/$dataExInsCount * 100;
            if ($countEx > $countIn) {
                $exInResult = 'e';
            }else{
                $exInResult = 'i';
            }

            $countSen1 = count(array_intersect_assoc($dataSensings, $data));
            $countInt1 = count(array_intersect_assoc($dataIntuitings, $data));
            $countSen = count(array_intersect_assoc($dataSensings, $data))/$dataSeInsCount * 100;
            $countInt = count(array_intersect_assoc($dataIntuitings, $data))/$dataSeInsCount * 100;
            if ($countSen > $countInt) {
                $senIntResult = 's';
            }else{
                $senIntResult = 'n';
            }
            
            $countTh1 = count(array_intersect_assoc($dataThinkings, $data));
            $countFe1 = count(array_intersect_assoc($dataFeelings, $data));
            $countTh = count(array_intersect_assoc($dataThinkings, $data))/$dataThFesCount * 100;
            $countFe = count(array_intersect_assoc($dataFeelings, $data))/$dataThFesCount * 100;
            if ($countTh > $countFe) {
                $thFeResult = 't';
            }else{
                $thFeResult = 'f';
            }
            
            $countJu1 = count(array_intersect_assoc($dataJudgings, $data));
            $countPe1 = count(array_intersect_assoc($dataPerceivings, $data));
            $countJu = count(array_intersect_assoc($dataJudgings, $data))/$dataJuPesCount * 100;
            $countPe = count(array_intersect_assoc($dataPerceivings, $data))/$dataJuPesCount * 100;
            if ($countJu > $countPe) {
                $juPeResult = 'j';
            }else{
                $juPeResult = 'p';
            }
            
            ###dummy start
            //$personalityType = array('istj','isfj','istp','isfp','infj','intj','INTP','ESTP','ESFP','ENFP','ENTP','ESTJ','ESFJ','ENFJ','ENTJ');
            
            //$result = array_rand($personalityType,1);
            ###dummy end

            $dataInput['e'] = $countEx1;
            $dataInput['i'] = $countIn1;
            $dataInput['s'] = $countSen1;
            $dataInput['n'] = $countInt1;
            $dataInput['t'] = $countTh1;
            $dataInput['f'] = $countFe1;
            $dataInput['j'] = $countJu1;
            $dataInput['p'] = $countPe1;

            $dataInput['result'] = $exInResult.$senIntResult.$thFeResult.$juPeResult;

            //filtering the data
            $dataInput['tech_id'] = $userId;
            $dataInput['created_at'] = Carbon::now()->format('Y-m-d H:i:s');
            $dataInput['question_id'] = serialize($questionIds);
            $dataInput['answer_id'] = serialize($answerIds);
            //$dataInput['result'] = $totalNilai;
    
            DB::table('proc_test_psychology_results')->insert($dataInput);

            return redirect()->route('tech-test-training.index')->with('alert-success','Selamat Anda telah berhasil menyelesaikan tes psikologi silahkan melanjutkan.');
        }

        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Psychology Test.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Psychology Test.');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Psychology Test.');
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
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Psychology Test.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Psychology Test.');
    }
}
