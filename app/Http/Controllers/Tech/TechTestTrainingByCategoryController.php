<?php

namespace App\Http\Controllers\Tech;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Auth;
use DB;

class TechTestTrainingByCategoryController extends Controller
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
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Test by Category.');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $userId = Auth::user()->id;
        
        $catId = $request->cid;
        $vidType = 2; //video training
        $vidStatus = 1; //video training

        //check video
        $firstCheck = DB::table('proc_videos_by_tech as pvbt')->where('tech_id',$userId)->where('video_type',$vidType)->where('video_cat',$catId)->first();

        if (!isset($firstCheck)) {
            $dataVideo['tech_id'] = $userId;
            $dataVideo['video_type'] = $vidType;
            $dataVideo['video_cat'] = $catId;
            $dataVideo['status'] = $vidStatus;
            $dataVideo['created_at'] = Carbon::now()->format('Y-m-d H:i:s');
            DB::table('proc_videos_by_tech')->insert($dataVideo);
        }

        //prev test
        $catActive = 1;
        $testResultCheck = DB::table('proc_test_assessment_results')->select('question_cat')->where('tech_id',$userId)->orderBy('id','DESC')->first();
        $newTest = DB::table('proc_test_assessment_questions_categories')->select('id','sort_order','name')->where('id',$catId)->where('status',$catActive)->first();

        if ($testResultCheck != NULL) {
            $prevCat = $testResultCheck->question_cat;
            $prevTest = DB::table('proc_test_assessment_questions_categories')->select('sort_order')->where('id',$prevCat)->where('status',$catActive)->first();
            $prevOrder = $prevTest->sort_order;
            //current test
            $newOrder = $newTest->sort_order;
            if ($newOrder != $prevOrder + 1) {
                return redirect()->back()->with('alert-danger','Halaman yang akan Anda tuju tidak tersedia.');
            }
        }

        $data['categoryDatas'] = $newTest;

        //training test
        $data['testAssessmentDatas'] = DB::table('proc_test_assessment_questions')->where('cat_id',$catId)->inRandomOrder()->limit(3)->get();

        $data['testAssessmentChoicesDatas'] = DB::table('proc_test_assessment_answers')->inRandomOrder()->get();
        $data['testAssessmentResult'] = DB::table('proc_test_assessment_results')->where('question_cat',$catId)->where('tech_id',$userId)->count();

        return view('tech.test-training.training-test', $data);
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

        $catId = $request->question_cat;

        $firstCheck = DB::table('proc_test_assessment_results')->where('tech_id',$userId)->where('question_cat',$catId)->count();

        if ($firstCheck < 1) {
            //grabbing data
            $data = $request->except(['_token','submit','question_count']);
    
            $questionIds = array_keys($data);
            $answerIds = array_values($data);
    
            //getting test total value
            $dataAnswers = DB::table('proc_test_assessment_questions')->get();
            $totalNilai = 0;
            foreach ($dataAnswers as $dataAnswer) {
                foreach ($data as $questionNo => $Value) {
                    if($dataAnswer->id == $questionNo && $dataAnswer->right_answer == $Value){
                        $totalNilai++;
                    }
                }
            }
    
            //filtering the data
            $dataInput['tech_id'] = $userId;
            $dataInput['created_at'] = Carbon::now()->format('Y-m-d H:i:s');
            $dataInput['question_cat'] = $catId;
            $dataInput['question_id'] = serialize($questionIds);
            $dataInput['answer'] = serialize($answerIds);
            $totalQuestion = $request->question_count;
            $dataInput['result'] = ($totalNilai/$totalQuestion) * 100;
    
            DB::table('proc_test_assessment_results')->insert($dataInput);

            $catActive = 1;
            $trainingCatData = DB::table('proc_test_assessment_questions_categories')->select('id')->where('status',$catActive)->orderBy('sort_order','DESC')->first();
            $lastTest = $trainingCatData->id;

            if ($catId == $lastTest) {
                return redirect()->route('tech-test-training.index')->with('alert-success','Selamat Anda telah berhasil menyelesaikan tes asesmen, selanjutnya tim Lini akan menghubungi Anda.');
            }else{
                return redirect()->route('tech-test-training.index')->with('alert-success','Data tes berhasil disimpan, silahkan melanjutkan ke tes berikutnya.');
            }
        }

        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Test by Category.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Test by Category.');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Test by Category.');
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
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Test by Category.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Test by Category.');
    }
}
