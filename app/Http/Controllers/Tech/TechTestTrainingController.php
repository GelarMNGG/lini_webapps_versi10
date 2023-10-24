<?php

namespace App\Http\Controllers\Tech;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use DB;

class TechTestTrainingController extends Controller
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
        $userId = Auth::user()->id;

        //datas
        $data['testResultsDatas'] = DB::table('proc_test_results as ptr')
        ->select([
            'ptr.*',
            DB::raw('(SELECT name FROM proc_test_results_status WHERE proc_test_results_status.id = ptr.status) status_name')
        ])
        ->where('tech_id',$userId)->get();

        ###test datas
            //psychology test
            $data['testPsychologyDatas'] = DB::table('proc_test_psychology_questions')->inRandomOrder()->get();
            $data['testPsychologyChoicesDatas'] = DB::table('proc_test_psychology_answers')->inRandomOrder()->get();
            $data['testPsychologyResult'] = DB::table('proc_test_psychology_results')->where('tech_id',$userId)->first();
            
            //competency test
            $data['testCompetencyDatas'] = DB::table('proc_test_competency')->inRandomOrder()->get();
            $data['testCompetencyResult'] = DB::table('proc_test_competency_results')->where('tech_id',$userId)->first();

            //training test
            $catActive = 1;
            $data['testAssessmentDatas'] = DB::table('proc_test_assessment_questions')->inRandomOrder()->get();
            $data['testAssessmentChoicesDatas'] = DB::table('proc_test_assessment_answers')->inRandomOrder()->get();
            $data['testAssessmentResult'] = DB::table('proc_test_assessment_results')->where('tech_id',$userId)->count();
            $data['testTrainingCatCount'] = DB::table('proc_test_assessment_questions_categories')->where('status',$catActive)->count();
        ###test datas end
        
        //video datas
        $corporateType = 1;
        $trainingType = 2;
        $videoStatus = 0; //viewed
        $data['videoCorpCultureDatas'] = DB::table('proc_videos')->where('video_type',$corporateType)->get();
        $data['videoCorpCultureCount'] = DB::table('proc_videos')->where('video_type',$corporateType)->count();
        $data['videoCorpCultureViews'] = DB::table('proc_videos_by_tech')->where('tech_id',$userId)->where('video_type',$corporateType)->count();

        $data['videoTrainingDatas'] = DB::table('proc_videos')->where('video_type',$trainingType)->get();
        $data['videoTrainingCount'] = DB::table('proc_videos')->where('video_type',$trainingType)->count();
        $data['videoTrainingViews'] = DB::table('proc_videos_by_tech')->where('tech_id',$userId)->where('video_type',$trainingType)->count();

        $data['videoTrainingCategories'] = DB::table('proc_test_assessment_questions_categories as ptaqc')
        ->select([
            'ptaqc.*',
            DB::raw('(SELECT video FROM proc_videos WHERE proc_videos.video_cat = ptaqc.id) as video'),
            DB::raw('(SELECT thumbnail FROM proc_videos WHERE proc_videos.video_cat = ptaqc.id) as thumbnail'),
            //test count
            DB::raw('(SELECT COUNT(*) FROM proc_test_assessment_results WHERE proc_test_assessment_results.question_cat = ptaqc.id AND proc_test_assessment_results.tech_id = '.$userId.') as test_count'),
            //test count all
            DB::raw('(SELECT COUNT(*) FROM proc_test_assessment_results WHERE proc_test_assessment_results.tech_id = '.$userId.') as test_catcount'),
            DB::raw('(SELECT result FROM proc_test_assessment_results WHERE proc_test_assessment_results.tech_id = '.$userId.' AND proc_test_assessment_results.question_cat = ptaqc.id) as test_result'),
            //video view count
            DB::raw('(SELECT COUNT(*) FROM proc_videos_by_tech WHERE proc_videos_by_tech.tech_id = '.$userId.' AND proc_videos_by_tech.video_type = '.$trainingType.' AND proc_videos_by_tech.video_cat = ptaqc.id) as video_viewcount'),
        ])
        ->where('status',$catActive)
        ->orderBy('sort_order','ASC')
        ->get();

        //supporting data
        $data['testTypesDatas'] = DB::table('proc_test_types')->get();

        return view('tech.test-training.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Test & Training.');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Test & Training.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Test & Training.');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Test & Training.');
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
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Test & Training.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Test & Training.');
    }
}
