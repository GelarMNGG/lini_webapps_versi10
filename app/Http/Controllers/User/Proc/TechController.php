<?php

namespace App\Http\Controllers\User\Proc;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Auth;
use DB;

class TechController extends Controller
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
    public function index(Request $request)
    {
        $userId = Auth::user()->id;
        $userType = Auth::user()->user_type;
        $userCompany = Auth::user()->company_id;
        $userDepartment = Auth::user()->department_id;
        $userLevel = Auth::user()->user_level;

        $skin = $request->skin;

        if ($userCompany == 1 && $userDepartment == 9 && $userLevel == 22) {
            //supporting data
            $data['techSkillsDatas'] = DB::table('techs_skills')->get();
            $data['projectDatas'] = DB::table('projects')->get();
            $data['ratingCountAlls'] = DB::table('proc_tech_rating_result')
                ->select(DB::raw('(one + two + three + four + five) as count'))
                ->selectRaw('((one*1) + (two*2) + (three*3) + (four*4) + (five*5)) as totalCount')
                ->selectRaw('tech_id as tech_id')
                ->get();

            $catActive = 1;
            $data['testTrainingCatCount'] = DB::table('proc_test_assessment_questions_categories')->where('status',$catActive)->count();

            //skin implementation
            if ($skin == 1) {
                $data['skin'] = 0;
                $data['skinBack'] = 1;
                
                $data['techsDatas'] = DB::table('techs as t')
                //->leftJoin('proc_test_psychology_results', 'techs.id', '=', 'proc_test_psychology_results.tech_id')
                ->select([
                    't.*',
                    //psychology test
                    DB::raw('(SELECT COUNT(*) FROM proc_test_psychology_results WHERE proc_test_psychology_results.tech_id = t.id) as test_psychology_count'),
                    DB::raw('(SELECT status FROM proc_test_psychology_results WHERE proc_test_psychology_results.tech_id = t.id) as test_psychology_status'),
                    DB::raw('(SELECT result FROM proc_test_psychology_results WHERE proc_test_psychology_results.tech_id = t.id) as test_psychology_result'),
                    DB::raw('(SELECT created_at FROM proc_test_psychology_results WHERE proc_test_psychology_results.tech_id = t.id) as test_psychology_date'),
                    //competency test
                    DB::raw('(SELECT COUNT(*) FROM proc_test_competency_results WHERE proc_test_competency_results.tech_id = t.id) as test_competency_count'),
                    DB::raw('(SELECT result FROM proc_test_competency_results WHERE proc_test_competency_results.tech_id = t.id) as test_competency_result'),
                    DB::raw('(SELECT created_at FROM proc_test_competency_results WHERE proc_test_competency_results.tech_id = t.id) as test_competency_date'),
                    //assessment test
                    DB::raw('(SELECT COUNT(*) FROM proc_test_assessment_results WHERE proc_test_assessment_results.tech_id = t.id) as test_assessment_count'),
                    //DB::raw('(SELECT result FROM proc_test_assessment_results WHERE proc_test_assessment_results.tech_id = t.id) as test_assessment_result'),
                    //DB::raw('(SELECT created_at FROM proc_test_assessment_results WHERE proc_test_assessment_results.tech_id = t.id) as test_assessment_date'),
                ])
                ->where('deleted_at', null)
                ->orderBy('test_psychology_count','DESC')
                ->orderBy('test_assessment_count','DESC')
                //->orderBy('test_assessment_result','DESC')
                ->get();

                return view('user.proc.tech.index-table', $data);
            }else{
                $data['skin'] = 1;
                $data['skinBack'] = 0;
                
                $data['techsDatas'] = DB::table('techs as t')
                //->leftJoin('proc_test_psychology_results', 'techs.id', '=', 'proc_test_psychology_results.tech_id')
                ->select([
                    't.*',
                    //psychology test
                    DB::raw('(SELECT COUNT(*) FROM proc_test_psychology_results WHERE proc_test_psychology_results.tech_id = t.id) as test_psychology_count'),
                    DB::raw('(SELECT status FROM proc_test_psychology_results WHERE proc_test_psychology_results.tech_id = t.id) as test_psychology_status'),
                    DB::raw('(SELECT result FROM proc_test_psychology_results WHERE proc_test_psychology_results.tech_id = t.id) as test_psychology_result'),
                    DB::raw('(SELECT created_at FROM proc_test_psychology_results WHERE proc_test_psychology_results.tech_id = t.id) as test_psychology_date'),
                    //competency test
                    DB::raw('(SELECT COUNT(*) FROM proc_test_competency_results WHERE proc_test_competency_results.tech_id = t.id) as test_competency_count'),
                    DB::raw('(SELECT result FROM proc_test_competency_results WHERE proc_test_competency_results.tech_id = t.id) as test_competency_result'),
                    DB::raw('(SELECT created_at FROM proc_test_competency_results WHERE proc_test_competency_results.tech_id = t.id) as test_competency_date'),
                    //assessment test
                    DB::raw('(SELECT COUNT(*) FROM proc_test_assessment_results WHERE proc_test_assessment_results.tech_id = t.id) as test_assessment_count'),
                    //DB::raw('(SELECT result FROM proc_test_assessment_results WHERE proc_test_assessment_results.tech_id = t.id) as test_assessment_result'),
                    //DB::raw('(SELECT created_at FROM proc_test_assessment_results WHERE proc_test_assessment_results.tech_id = t.id) as test_assessment_date'),
                ])
                ->where('deleted_at', null)
                ->orderBy('test_psychology_count','DESC')
                ->orderBy('test_assessment_count','DESC')
                //->orderBy('test_assessment_result','DESC')
                ->paginate(10);

                return view('user.proc.tech.index', $data);
            }
        }

        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Daftar Teknisi.');
    
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
        $userCompany = Auth::user()->company_id;
        $userDepartment = Auth::user()->department_id;
        $userLevel = Auth::user()->user_level;

        if ($userCompany == 1 && $userDepartment == 9 && $userLevel == 22) {
            $data['skills'] = DB::table('techs_skills')->get();

            return view('user.proc.tech.create', $data);
        }

        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Daftar Teknisi.');
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
        $userCompany = Auth::user()->company_id;
        $userDepartment = Auth::user()->department_id;
        $userLevel = Auth::user()->user_level;

        if ($userCompany == 1 && $userDepartment == 9 && $userLevel == 22) {
            $request->validate([
                'firstname' => 'required',
                'mobile' => 'required|min:9',
                'address' => 'required|min:10',
                'image' => 'mimes:jpeg,jpg,png,pdf|max:2048',
                'email' => 'required|email|string|max:255|unique:admins,email',
                'password' => 'required|confirmed|min:6',
            ]);

            //file handler
            $fileName = null;
            $destinationPath = public_path().'/admintheme/images/users/';
            
            // Retrieving An Uploaded File
            $file = $request->file('image');
            if (!empty($file)) {
                $extension = $file->getClientOriginalExtension();
                $fileName = time().'_'.$file->getClientOriginalName();
        
                // Moving An Uploaded File
                $request->file('image')->move($destinationPath, $fileName);
            }

            //custom setting to support file upload
            $data = $request->except(['_token','_method','password_confirmation','submit']);

            $data['password'] = Hash::make($request['password']);
            $data['active'] = 1;
            $data['is_verified'] = 1;
            $data['email_verified_at'] = Carbon::now()->format('Y-m-d H:i:s');

            #$data = $request->all();
            if (!empty($fileName)) {
                $data['image'] = $fileName;
            }

            DB::table('techs')->insert($data);

            return redirect()->route('user-tech.index')->with('alert-success','Data berhasil disimpan.');
        }

        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Daftar Teknisi.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $userId = Auth::user()->id;
        $userType = Auth::user()->user_type;
        $userCompany = Auth::user()->company_id;
        $userDepartment = Auth::user()->department_id;
        $userLevel = Auth::user()->user_level;

        $firstCheck = DB::table('techs')->where('id', $id)->count();

        if ($firstCheck < 1) {
            return redirect()->back()->with('alert-danger','Halaman yang akan Anda tuju tidak tersedia.');
        }

        if ($userCompany == 1 && $userDepartment == 9 && $userLevel == 22) {
            
            $data['techsData'] = DB::table('techs as t')
            //->leftJoin('proc_test_psychology_results', 'techs.id', '=', 'proc_test_psychology_results.tech_id')
            ->select([
                't.*',
                //task
                DB::raw('(SELECT name FROM projects_task WHERE projects_task.tech_id = t.id AND projects_task.name IS NULL) as task_name'),
                //psychology test
                DB::raw('(SELECT COUNT(*) FROM proc_test_psychology_results WHERE proc_test_psychology_results.tech_id = t.id) as test_psychology_count'),
                DB::raw('(SELECT status FROM proc_test_psychology_results WHERE proc_test_psychology_results.tech_id = t.id) as test_psychology_status'),
                DB::raw('(SELECT result FROM proc_test_psychology_results WHERE proc_test_psychology_results.tech_id = t.id) as test_psychology_result'),
                DB::raw('(SELECT created_at FROM proc_test_psychology_results WHERE proc_test_psychology_results.tech_id = t.id) as test_psychology_date'),
                //competency test
                DB::raw('(SELECT COUNT(*) FROM proc_test_competency_results WHERE proc_test_competency_results.tech_id = t.id) as test_competency_count'),
                DB::raw('(SELECT result FROM proc_test_competency_results WHERE proc_test_competency_results.tech_id = t.id) as test_competency_result'),
                DB::raw('(SELECT created_at FROM proc_test_competency_results WHERE proc_test_competency_results.tech_id = t.id) as test_competency_date'),
                //assessment test
                DB::raw('(SELECT COUNT(*) FROM proc_test_assessment_results WHERE proc_test_assessment_results.tech_id = t.id) as test_assessment_count'),
            ])
            ->where('id', $id)
            ->first();

            //$data['techSkillsDatas'] = DB::table('techs_skills')->get();
            //$data['projectDatas'] = DB::table('projects')->get();

            ###techpersonal datas
                $techId = $data['techsData']->id;
                $data['tech'] = DB::table('techs')->where('id',$techId)->first();
                
                //test result
                    $data['testPsychologyResults'] = DB::table('proc_test_psychology_results as ptpr')
                    ->select([
                        'ptpr.*',
                        DB::raw('(SELECT name FROM proc_test_results_status WHERE proc_test_results_status.id = ptpr.status AND ptpr.status IS NOT NULL) as status_name'),
                    ])
                    ->where('tech_id',$techId)->get();

                    $data['admins'] = DB::table('admins')->get();
                    $data['users'] = DB::table('users')->get();
                    $data['psychologyAnalisysDatas'] = DB::table('proc_test_psychology_analisys')->get();
                    $data['testResultStatus'] = DB::table('proc_test_results_status')->get();

                    $data['testCompetencyResult'] = DB::table('proc_test_competency_results')->where('tech_id',$techId)->get();

                    $data['testAssessmentResult'] = DB::table('proc_test_assessment_results as ptar')
                    ->select([
                        'ptar.*',
                        DB::raw('(SELECT name FROM proc_test_assessment_questions_categories WHERE proc_test_assessment_questions_categories.id = ptar.question_cat) as cat_name')
                    ])
                    ->where('tech_id',$techId)->get();
                //test result end

                //tech personal data
                $data['techPersonalData'] = DB::table('proc_tech_personal_data as ptpd')
                ->select([
                    'ptpd.*',
                    DB::raw('(SELECT name FROM proc_gender WHERE proc_gender.id = ptpd.gender) as gender_name'),
                    DB::raw('(SELECT name FROM religions WHERE religions.id = ptpd.religion) as religion_name'),
                    DB::raw('(SELECT name FROM proc_marital_status WHERE proc_marital_status.id = ptpd.marital_status) as marital_status_name'),
                    DB::raw('(SELECT name FROM list_of_cities WHERE list_of_cities.id = ptpd.city) as city_name'),
                    DB::raw('(SELECT name FROM list_of_provinces WHERE list_of_provinces.id = ptpd.province) as province_name'),
                ])
                ->where('tech_id',$techId)->first();

                //famimily data input
                $data['techFamilyInfo'] = DB::table('proc_family_info')->where('tech_id',$techId)->first();

                //education data input
                $data['educationDatas'] = DB::table('proc_tech_education_info as ptei')
                ->select([
                    'ptei.*',
                    DB::raw('(SELECT name FROM proc_tech_education_info_level WHERE proc_tech_education_info_level.id = ptei.level) as level_name'),
                    DB::raw('(SELECT name FROM list_of_provinces WHERE list_of_provinces.id = ptei.province) as province_name'),
                    DB::raw('(SELECT name FROM list_of_cities WHERE list_of_cities.id = ptei.city) as city_name'),
                ])
                ->where('tech_id',$techId)->get();

                //upload document
                $data['documentDatas'] = DB::table('proc_tech_documents as ptd')
                ->select([
                    'ptd.*',
                    DB::raw('(SELECT name FROM proc_tech_documents_type WHERE proc_tech_documents_type.id = ptd.doc_type) as doc_name')
                ])
                ->where('tech_id',$techId)->get();
            ###techpersonal datas end

            ###tech rating
                $data['ratingCountAlls'] = DB::table('proc_tech_rating_result')
                    ->select(DB::raw('(one + two + three + four + five) as count'))
                    ->selectRaw('((one*1) + (two*2) + (three*3) + (four*4) + (five*5)) as totalCount')
                    ->selectRaw('tech_id as tech_id')
                    ->get();

                $data['ratingGiverCount'] = DB::table('proc_tech_rating')->where('tech_id',$techId)->where('giver_id',$userId)->where('giver_type',$userType)->where('giver_department',$userDepartment)->count();

                $data['ratingDatas'] = DB::table('proc_tech_rating')->where('tech_id',$techId)->get();
            ###tech rating end

            ###supporting data
                $data['adminDatas'] = DB::table('admins')->get();
                $data['userDatas'] = DB::table('users')->get();
                $data['departmentDatas'] = DB::table('department')->get();
                $data['techSkillsDatas'] = DB::table('techs_skills')->get();
            ###supporting data end

            return view('user.proc.tech.show', $data);
        }

        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Daftar Teknisi.');
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
        $userCompany = Auth::user()->company_id;
        $userDepartment = Auth::user()->department_id;
        $userLevel = Auth::user()->user_level;

        $firstCheck = DB::table('techs')->where('id',$id)->first();

        if (!isset($firstCheck)) {
            return redirect()->back()->with('alert-danger','Halaman yang Anda tuju tidak tersedia.');
        }

        if ($userCompany == 1 && $userDepartment == 9 && $userLevel == 22) {
            $data['techData'] = $firstCheck;
            $data['skills'] = DB::table('techs_skills')->get();

            return view('user.proc.tech.edit', $data);
        }
        
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Daftar Teknisi.');
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
        $userCompany = Auth::user()->company_id;
        $userDepartment = Auth::user()->department_id;
        $userLevel = Auth::user()->user_level;

        if ($userCompany == 1 && $userDepartment == 9 && $userLevel == 22) {
            $request->validate([
                'firstname' => 'required',
                'mobile' => 'required|min:9',
                'address' => 'required|min:10',
                'image' => 'mimes:jpeg,jpg,png,pdf|max:2048',
                'email' => 'required|email|string|max:255|unique:admins,email',
                'password' => 'required|min:6',
            ]);

            //file handler
            $fileName = null;
            $destinationPath = public_path().'/admintheme/images/users/';
            
            // Retrieving An Uploaded File
            $file = $request->file('image');
            if (!empty($file)) {
                $extension = $file->getClientOriginalExtension();
                $fileName = time().'_'.$file->getClientOriginalName();
        
                // Moving An Uploaded File
                $request->file('image')->move($destinationPath, $fileName);

                //delete previous image
                $dataImage = User::select('image as image')->where('id', $id)->first();
                $oldImage = $dataImage->image;

                if($oldImage !== 'default.png'){
                    $image_path = $destinationPath.$oldImage;
                    if(File::exists($image_path)) {
                        File::delete($image_path);
                    }
                }
            }

            //custom setting to support file upload
            $data = $request->except(['_token','_method','password_confirmation','submit']);

            $data['password'] = Hash::make($request['password']);
            $data['active'] = 1;
            $data['is_verified'] = 1;
            $data['email_verified_at'] = Carbon::now()->format('Y-m-d H:i:s');

            #$data = $request->all();
            if (!empty($fileName)) {
                $data['image'] = $fileName;
            }

            DB::table('techs')->where('id',$id)->update($data);

            return redirect()->route('user-tech.index')->with('alert-success','Data berhasil diubah.');
        }

        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Daftar Teknisi.');
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
        $userCompany = Auth::user()->company_id;
        $userDepartment = Auth::user()->department_id;
        $userLevel = Auth::user()->user_level;

        $firstCheck = DB::table('techs')->where('id',$id)->first();

        if (!isset($firstCheck)) {
            return redirect()->back()->with('alert-danger','Halaman yang Anda tuju tidak tersedia.');
        }

        if ($userCompany == 1 && $userDepartment == 9 && $userLevel == 22) {
            $data['deleted_at'] = Carbon::now()->format('Y-m-d H:i:s');
    
            //delete from database
            DB::table('techs')->where('id',$id)->update($data);

            return redirect()->route('user-tech.index')->with('alert-success','Data berhasil dihapus.');
        }

        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Daftar Teknisi.');
    }
}
