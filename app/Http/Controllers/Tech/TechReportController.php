<?php

namespace App\Http\Controllers\Tech;

use App\Tech;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Auth;
use DB;

class TechReportController extends Controller
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
    public function index(Request $request)
    {
        $userId = Auth::user()->id;
        $userType = Auth::user()->user_type;
        $userLevel = Auth::user()->user_level;
        $userDepartment = Auth::user()->department_id;
        $projectId  = $request->project_id;
        $taskId  = $request->task_id;
        $catStatus = 1; //active category
        $subcatStatus = 1; //active subcategory

        //first check
        $firstCheck = DB::table('projects_task')->where('id',$taskId)->where('tech_id',$userId)->where('deleted_at',null)->count();

        if ($firstCheck > 0) {

            //get data
            $data['infoTaskProject'] = DB::table('projects_task')
            ->select([
                'projects_task.*',
                DB::raw('(SELECT id FROM projects WHERE projects.id = projects_task.project_id) as project_id'),
                DB::raw('(SELECT name FROM projects WHERE projects.id = projects_task.project_id) as project_name')
            ])
            ->where('id',$taskId)
            ->where('tech_id',$userId)
            ->where('deleted_at',null)
            ->first();

            //task data
            $data['projectTaskDatas'] = DB::table('projects_task as pt')
            ->select([
                'pt.*',
                DB::raw('(SELECT id FROM project_report_template_selected WHERE project_report_template_selected.task_id = pt.id) as template_id')
            ])
            ->where('id',$taskId)
            ->where('tech_id',$userId)
            ->where('deleted_at',null)->get();

            //template data
            $data['projectTemplateDatas'] = DB::table('project_report_template_selected as prtss')
            ->select([
                'prtss.*',
                DB::raw('(SELECT name FROM projects_task WHERE projects_task.id = prtss.task_id) as task_name')
            ])
            ->where('project_id',$projectId)->where('task_id',$taskId)->where('deleted_at',null)->paginate(10);

            if (count($data['projectTemplateDatas']) < 1) {
                return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses data Project Template.');
            }

            //additional info
            #$data['draftCount'] = DB::table('project_report_template_selected')->where('publisher_id',$userId)->where('task_id',$taskId)->where('deleted_at',null)->count();
            $data['dataUsers'] = DB::table('users')->get();

            return view('tech.report.index', $data);
        }

        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses data Project Template.');
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
        $userDepartment = 1; //project department

        $projectId = $request->project_id;
        $taskId = $request->task_id;
        $templateId = $request->template_id; //category
        
        $subcatRequestData = $request->subcat_id;
        if ($subcatRequestData !== null) {
            $subcatId = $request->subcat_id;
            $subcatName = 'subcat_id';
        }else{
            $subcatId = $request->subcatcustom_id;
            $subcatName = 'subcatcustom_id';
        }

        $data['subcatId'] = $subcatId;
        $data['subcatName'] = $subcatName;
                
        $catStatus = 1; //active category
        $subcatStatus = 1; //active subcategory
        $commentStatus = 0; //accessible for technician level user

        //check priviledge
        $priviledgeCheck = DB::table('projects_task')->where('project_id',$projectId)->where('id',$taskId)->where('tech_id',$userId)->where('deleted_at',null)->count();
        
        if ($priviledgeCheck > 0) {
            ### template data ###
                $data['projectTemplate'] = DB::table('project_report_template_selected as prtss')
                ->select([
                    'prtss.*',
                    DB::raw('(SELECT name FROM projects WHERE projects.id = prtss.project_id) as project_name'),
                    DB::raw('(SELECT procat_id FROM projects WHERE projects.id = prtss.project_id) as procat_id'),
                    DB::raw('(SELECT name FROM projects_task WHERE projects_task.id = prtss.task_id) as task_name'),
                    DB::raw('(SELECT status FROM projects WHERE projects.id = prtss.project_id) as project_status'),
                    DB::raw('(SELECT name FROM project_report_category WHERE project_report_category.id = prtss.template_id) as name'),
                    //tempate type
                        DB::raw('(SELECT type FROM project_report_category WHERE project_report_category.id = prtss.template_id) as type'),
                ])
                ->where('template_id',$templateId)
                ->where('project_id',$projectId)
                ->where('task_id',$taskId)
                ->where('deleted_at',null)->first();
            ### template data end ###
            //third check
                if ($subcatRequestData !== null) {
                    $dataSubcat = unserialize($data['projectTemplate']->subcat_id);
                }else{
                    $dataSubcat = unserialize($data['projectTemplate']->subcatcustom_id);
                }
                if (!in_array($subcatId,$dataSubcat)) {
                    return redirect()->back()->with('alert-danger','Subcategory yang Anda tuju tidak tersedia pada template ini.');
                }
            //project category used for image folder placement
                $data['dataProjectCategory'] = DB::table('projects_category')->where('id',$data['projectTemplate']->procat_id)->first();
            //supporting data
                $data['projectTask'] = DB::table('projects_task as pt')
                ->select([
                    'pt.*',
                    DB::raw('(SELECT name FROM projects WHERE projects.id = pt.project_id) as project_name'),
                ])
                ->where('id',$taskId)->where('tech_id',$userId)->where('deleted_at',null)->first();
            //check template type and redirect
            $image = 1;
            if ($data['projectTemplate']->type == $image) {
                ### project pictures ###
                    if ($subcatRequestData !== null) {
                        $data['dataProjectPictures'] = DB::table('project_report_images')
                        ->where('project_id',$projectId)
                        ->where('task_id',$taskId)
                        ->where('template_id',$templateId)
                        ->where('subcat_id',$subcatId)
                        ->where('publisher_id',$userId)
                        ->get();
                    }else{
                        $data['dataProjectPictures'] = DB::table('project_report_images')
                        ->where('project_id',$projectId)
                        ->where('task_id',$taskId)
                        ->where('template_id',$templateId)
                        ->where('subcatcustom_id',$subcatId)
                        ->where('publisher_id',$userId)
                        ->get();
                    }

                    $data['dataProjectPicturesStatus'] = DB::table('project_report_images as pri')
                    ->select([
                        'pri.*',
                        DB::raw('COUNT(pri.approved_at) as countApproved'),
                        DB::raw('COUNT(pri.approved_by_pm_at) as countPMApproved'),
                        //comments
                        DB::raw('(SELECT COUNT(*) FROM project_report_images_comments WHERE project_report_images_comments.pri_id = pri.id) as commentsCount')
                    ])
                    ->where('template_id',$templateId)
                    ->where('project_id',$projectId)
                    ->where('task_id',$taskId)
                    ->where($subcatName,$subcatId)
                    ->first();
                ### project pictures end ###
                ### submit count ###
                    if ($subcatRequestData !== null) {
                        $data['submittedCount'] = DB::table('project_report_images')
                        ->where('project_id',$projectId)
                        ->where('task_id',$taskId)
                        ->where('subcat_id',$subcatId)
                        ->where('submitted_at','!=',null)->count();
                    }else{
                        $data['submittedCount'] = DB::table('project_report_images')
                        ->where('project_id',$projectId)
                        ->where('task_id',$taskId)
                        ->where('subcatcustom_id',$subcatId)
                        ->where('submitted_at','!=',null)->count();
                    }
                ### submit count end ###
                ### subcat data ###
                    $data['subcatsPictureByCatCount'] = DB::table('project_report_images as pri')
                    ->select([
                        'pri.*', 
                        DB::raw('COUNT(pri.cat_id) as total')
                    ])
                    ->where('task_id',$taskId)
                    ->groupBy('cat_id')
                    ->get();

                    if ($subcatRequestData !== null) {
                        $data['dataSubcategory'] = DB::table('project_report_subcategory as prs')
                        ->select([
                            'prs.*',
                            DB::raw('(SELECT COUNT(*) FROM project_report_images WHERE project_report_images.task_id = '.$taskId.' AND project_report_images.subcat_id = prs.id) as subcatcount')
                        ])
                        //->where('id',$subcatId)
                        ->where('status',$subcatStatus)
                        ->where('deleted_at',null)->get();
                    }else{
                        $data['dataSubcategory'] = DB::table('project_report_subcategory_customized as prsc')
                        ->select([
                            'prsc.*',
                            DB::raw('(SELECT COUNT(*) FROM project_report_images WHERE project_report_images.task_id = '.$taskId.' AND project_report_images.subcatcustom_id = prsc.id) as subcatcount')
                        ])
                        //->where('id',$subcatId)
                        ->where('deleted_at',null)->get();
                    }
                ### subcat data end ###
                ### approver count ###
                    //submit count
                    if ($subcatRequestData !== null) {
                        $data['approverData'] = DB::table('project_report_images')
                        ->where('project_id',$projectId)
                        ->where('task_id',$taskId)
                        ->where('subcat_id',$subcatId)
                        ->orderBy('selected_image','DESC')
                        ->first();
                    }else{
                        $data['approverData'] = DB::table('project_report_images')
                        ->where('project_id',$projectId)
                        ->where('task_id',$taskId)
                        ->where('subcatcustom_id',$subcatId)
                        ->orderBy('selected_image','DESC')
                        ->first();
                    }
                ### approver count ###
                ### comments ###
                    if ($subcatRequestData !== null) {
                        $data['dataComments'] = DB::table('project_report_images_comments')->where('status',$commentStatus)
                        ->where('pri_id',$data['dataProjectPicturesStatus']->id)
                        ->where('project_id',$projectId)
                        ->where('task_id',$taskId)
                        ->where('subcat_id',$subcatId)
                        ->orderBy('date','DESC')
                        ->get();
                    }else{
                        $data['dataComments'] = DB::table('project_report_images_comments')->where('status',$commentStatus)
                        ->where('pri_id',$data['dataProjectPicturesStatus']->id)
                        ->where('project_id',$projectId)
                        ->where('task_id',$taskId)
                        ->where('subcatcustom_id',$subcatId)
                        ->orderBy('date','DESC')
                        ->get();
                    }

                    $data['techs'] = DB::table('techs')->get();
                    $data['users'] = DB::table('users')
                    ->select([
                        'users.*',
                        DB::raw('(SELECT name FROM users_level WHERE users_level.id = users.user_level) as title')
                    ])
                    ->get();
                ### comments end ###
                return view('tech.report.create-image',$data);
            }else{
                ### project text ###
                    if ($subcatRequestData !== null) {
                        $data['dataProjectText'] = DB::table('project_report_text')
                        ->where('project_id',$projectId)
                        ->where('task_id',$taskId)
                        ->where('template_id',$templateId)
                        ->where('subcat_id',$subcatId)
                        ->where('publisher_id',$userId)
                        ->first();
                    }else{
                        $data['dataProjectText'] = DB::table('project_report_text')
                        ->where('project_id',$projectId)
                        ->where('task_id',$taskId)
                        ->where('template_id',$templateId)
                        ->where('subcatcustom_id',$subcatId)
                        ->where('publisher_id',$userId)
                        ->first();
                    }
                    $data['dataProjectTextStatus'] = DB::table('project_report_text as prt')
                    ->select([
                        'prt.*',
                        DB::raw('COUNT(prt.approved_at) as countApproved'),
                        DB::raw('COUNT(prt.approved_by_pm_at) as countPMApproved'),
                        //comments
                            DB::raw('(SELECT COUNT(*) FROM project_report_text_comments WHERE project_report_text_comments.prt_id = prt.id) as commentsCount')
                    ])
                    ->where('template_id',$templateId)
                    ->where('project_id',$projectId)
                    ->where('task_id',$taskId)
                    ->where($subcatName,$subcatId)
                    ->first();
                ### project text end ###
                ### subcat data ###
                    $data['subcatsTextByCatCount'] = DB::table('project_report_text as prt')
                    ->select([
                        'prt.*', 
                        //DB::raw('COUNT(prt.cat_id) as total')
                    ])
                    ->where('task_id',$taskId)
                    //->groupBy('cat_id')
                    ->first();

                    if ($subcatRequestData !== null) {
                        $data['dataSubcategory'] = DB::table('project_report_subcategory as prs')
                        ->select([
                            'prs.*',
                            DB::raw('(SELECT COUNT(*) FROM project_report_text WHERE project_report_text.task_id = '.$taskId.' AND project_report_text.subcat_id = prs.id) as subcatcount')
                        ])
                        //->where('id',$subcatId)
                        ->where('status',$subcatStatus)
                        ->where('deleted_at',null)->get();
                    }else{
                        $data['dataSubcategory'] = DB::table('project_report_subcategory_customized as prsc')
                        ->select([
                            'prsc.*',
                            DB::raw('(SELECT COUNT(*) FROM project_report_text WHERE project_report_text.task_id = '.$taskId.' AND project_report_text.subcatcustom_id = prsc.id) as subcatcount')
                        ])
                        //->where('id',$subcatId)
                        ->where('deleted_at',null)->get();
                    }
                ### subcat data end ###
                ### approver count ###
                    //submit count
                    if ($subcatRequestData !== null) {
                        $data['approverData'] = DB::table('project_report_text')
                        ->where('project_id',$projectId)
                        ->where('task_id',$taskId)
                        ->where('subcat_id',$subcatId)
                        ->first();
                    }else{
                        $data['approverData'] = DB::table('project_report_text')
                        ->where('project_id',$projectId)
                        ->where('task_id',$taskId)
                        ->where('subcatcustom_id',$subcatId)
                        ->first();
                    }
                ### approver count ###
                ### comments ###
                    if ($subcatRequestData !== null) {
                        $data['dataComments'] = DB::table('project_report_text_comments')->where('status',$commentStatus)
                        ->where('prt_id',$data['dataProjectTextStatus']->id)
                        ->where('project_id',$projectId)
                        ->where('task_id',$taskId)
                        ->where('subcat_id',$subcatId)
                        ->orderBy('date','DESC')
                        ->get();
                    }else{
                        $data['dataComments'] = DB::table('project_report_text_comments')->where('status',$commentStatus)
                        ->where('prt_id',$data['dataProjectTextStatus']->id)
                        ->where('project_id',$projectId)
                        ->where('task_id',$taskId)
                        ->where('subcatcustom_id',$subcatId)
                        ->orderBy('date','DESC')
                        ->get();
                    }

                    $data['techs'] = DB::table('techs')->get();
                    $data['users'] = DB::table('users')
                    ->select([
                        'users.*',
                        DB::raw('(SELECT name FROM users_level WHERE users_level.id = users.user_level) as title')
                    ])
                    ->get();
                ### comments end ###

                //dd($data['subcatsTextByCatCount']);

                return view('tech.report.create-text',$data);
            }
        }

        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Report.');
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
        $userDepartment = 1; //project department

        $projectId = $request->project_id;
        $taskId = $request->task_id;
        $templateId = $request->template_id; //category

        $subcatRequestData = $request->subcat_id;
        if ($subcatRequestData !== null) {
            $subcatId = $request->subcat_id;
        }else{
            $subcatId = $request->subcatcustom_id;
        }

        $catStatus = 1; //active category
        $subcatStatus = 1; //active subcategory
        $reportType = 1; //image

        $folderName = $request->folder_name;
        if ($folderName == null) {
            $folderName = 'others';
        }
        //check priviledge
            $firstCheck = DB::table('projects_task')->where('project_id',$projectId)->where('tech_id',$userId)->count();
        //secondcheck
            $secondcheck = DB::table('project_report_category')->select('type')->where('id',$templateId)->first();
            $currentReportType = $secondcheck->type;
        if ($firstCheck > 0) {
            if ($currentReportType == $reportType) {
                //image validation
                    $request->validate([
                        'image' => 'mimes:jpeg,jpg,png|max:9216',
                    ]);
                //file handler
                    $fileName = null;
                    $destinationPath = public_path().'/img/projects/'.$folderName.'/';
                // Retrieving An Uploaded File
                    $file = $request->file('image');
                    if (!empty($file)) {
                        $extension = $file->getClientOriginalExtension();
                        //custom filename
                            $projectData = DB::table('projects')->select('name')->where('id',$projectId)->first();
                            $taskData = DB::table('projects_task')->select('name')->where('id',$taskId)->first();
                            $projectName = Str::slug($projectData->name,'-');
                            $taskName = Str::slug($taskData->name,'-');
                            $fileName = $projectName.'_'.$taskName.'_'.time().'_'.$file->getClientOriginalName();
                        // Moving An Uploaded File
                            $request->file('image')->move($destinationPath, $fileName);
                    }
                //custom setting to support file upload
                    $data = $request->except(['_token','submit','folder_name']);
                    $data['publisher_id'] = $userId;
                    $data['publisher_type'] = $userType;
                    $data['created_at'] = Carbon::now()->format('Y-m-d H:i:s');
                //reseting admin approval
                    $reset['approved_at'] = null;
                    if ($subcatRequestData != null) {
                        DB::table('project_report_images')
                        ->where('project_id',$projectId)
                        ->where('task_id',$taskId)
                        ->where('template_id',$templateId)
                        ->where('subcat_id',$subcatId)
                        ->where('publisher_id',$userId)
                        ->update($reset);
                    }else{
                        DB::table('project_report_images')
                        ->where('project_id',$projectId)
                        ->where('task_id',$taskId)
                        ->where('template_id',$templateId)
                        ->where('subcatcustom_id',$subcatId)
                        ->where('publisher_id',$userId)
                        ->update($reset);
                    }
        
                    if (!empty($fileName)) {
                        $data['image'] = $fileName;
                    }
                //insert to database
                    DB::table('project_report_images')->insert($data);
            }else{
                //custom setting to support file upload
                    $data = $request->except(['_token','submit']);
                    $data['publisher_id'] = $userId;
                    $data['publisher_type'] = $userType;
                    $data['created_at'] = Carbon::now()->format('Y-m-d H:i:s');
                //insert to database
                    DB::table('project_report_text')->insert($data);
            }
            //redirect page
            if ($subcatRequestData != null) {
                return redirect()->route('report-tech.create', 'project_id='.$projectId.'&task_id='.$taskId.'&template_id='.$templateId.'&subcat_id='.$subcatId)->with('alert-success','Data berhasil disimpan.');
            }else{
                return redirect()->route('report-tech.create', 'project_id='.$projectId.'&task_id='.$taskId.'&template_id='.$templateId.'&subcatcustom_id='.$subcatId)->with('alert-success','Data berhasil disimpan.');
            }
        }

        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Report.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        $userId = Auth::user()->id;
        $userType = Auth::user()->user_type;
        $userDepartment = Auth::user()->department_id;

        $projectId = $request->project_id;
        $taskId = $request->task_id;
        $catStatus = 1; //active category
        $subcatStatus = 1; //active subcategory

        //first check
            $firstCheck = DB::table('projects_task')->where('project_id',$projectId)->where('id',$taskId)->where('tech_id',$userId)->where('deleted_at',null)->count();
        if ($firstCheck > 0) {
            ### second check ###
                $dataCheck = DB::table('project_report_category')->where('id',$id)->where('deleted_at',null)->count();
                if ($dataCheck < 1) {
                    return redirect()->back()->with('alert-danger','Halaman yang Anda tuju tidak tersedia.');
                }
                $dataCheck2 = DB::table('project_report_template_selected')->where('template_id',$id)->where('project_id',$projectId)->where('task_id',$taskId)->where('deleted_at',null)->count();
                if ($dataCheck2 < 1) {
                    return redirect()->back()->with('alert-danger','Halaman yang Anda tuju tidak tersedia.');
                }
            ### second check end ###
            ### template data ###
                $data['projectTemplate'] = DB::table('project_report_template_selected as prtss')
                ->select([
                    'prtss.*',
                    DB::raw('(SELECT name FROM projects WHERE projects.id = prtss.project_id) as project_name'),
                    DB::raw('(SELECT name FROM projects_task WHERE projects_task.id = prtss.task_id) as task_name'),
                    DB::raw('(SELECT status FROM projects WHERE projects.id = prtss.project_id) as project_status'),
                    DB::raw('(SELECT name FROM project_report_category WHERE project_report_category.id = prtss.template_id) as name'),
                    //tempate type
                        DB::raw('(SELECT type FROM project_report_category WHERE project_report_category.id = prtss.template_id) as type'),
                ])
                ->where('template_id',$id)
                ->where('project_id',$projectId)
                ->where('task_id',$taskId)
                ->where('deleted_at',null)->first();
            ### template data end ###
            ### sub category ###
                //data selected subcat
                    $data['subcats'] = unserialize($data['projectTemplate']->subcat_id);
                    $data['subcatsCustom'] = unserialize($data['projectTemplate']->subcatcustom_id);
                ###image type
                    //data subcategory
                        $data['dataSubcategory'] = DB::table('project_report_subcategory as prs')
                        ->leftjoin('project_report_images','project_report_images.subcat_id','prs.id')
                        ->select([
                            'prs.*',
                            DB::raw('(SELECT COUNT(*) FROM project_report_images as pri WHERE pri.subcat_id = prs.id AND pri.project_id = '.$projectId.' AND pri.task_id = '.$taskId.') as imageCount'),

                            DB::raw('(SELECT COUNT(project_report_images.submitted_at) FROM project_report_images WHERE project_report_images.subcat_id = prs.id AND project_report_images.project_id='.$projectId.' AND project_report_images.task_id = '.$taskId.' AND project_report_images.template_id = '.$id.' AND project_report_images.submitted_at IS NOT NULL) as submittedCount'),

                            DB::raw('(SELECT COUNT(project_report_images.approved_at) FROM project_report_images WHERE project_report_images.subcat_id = prs.id AND project_report_images.project_id='.$projectId.' AND project_report_images.task_id = '.$taskId.' AND project_report_images.template_id = '.$id.' AND project_report_images.approved_at IS NOT NULL) as approvedCount'),

                            DB::raw('(SELECT COUNT(project_report_images.approved_by_pm_at) FROM project_report_images WHERE project_report_images.subcat_id = prs.id AND project_report_images.project_id='.$projectId.' AND project_report_images.task_id = '.$taskId.' AND project_report_images.template_id = '.$id.' AND project_report_images.approved_by_pm_at IS NOT NULL) as approvedPMCount'),

                        ])
                        ->where('prs.status',$subcatStatus)
                        ->where('prs.cat_id',$id)
                        ->where('prs.deleted_at',null)
                        ->groupBy('prs.id')
                        ->get();
                    //customized subcategories
                        $data['dataSubcategoryCustomized'] = DB::table('project_report_subcategory_customized as prsc')
                        ->leftjoin('project_report_images','project_report_images.subcatcustom_id','prsc.id')
                        ->select([
                            'prsc.*',
                            DB::raw('(SELECT COUNT(*) FROM project_report_images as pri WHERE pri.subcatcustom_id = prsc.id AND pri.project_id = '.$projectId.' AND pri.task_id = '.$taskId.') as imageCount'),

                            DB::raw('(SELECT COUNT(project_report_images.submitted_at) FROM project_report_images WHERE project_report_images.subcatcustom_id = prsc.id AND project_report_images.project_id='.$projectId.' AND project_report_images.task_id = '.$taskId.' AND project_report_images.template_id = '.$id.' AND project_report_images.submitted_at IS NOT NULL) as submittedCount'),

                            DB::raw('(SELECT COUNT(project_report_images.approved_at) FROM project_report_images WHERE project_report_images.subcatcustom_id = prsc.id AND project_report_images.project_id='.$projectId.' AND project_report_images.task_id = '.$taskId.' AND project_report_images.template_id = '.$id.' AND project_report_images.approved_at IS NOT NULL) as approvedCount'),

                            DB::raw('(SELECT COUNT(project_report_images.approved_by_pm_at) FROM project_report_images WHERE project_report_images.subcatcustom_id = prsc.id AND project_report_images.project_id='.$projectId.' AND project_report_images.task_id = '.$taskId.' AND project_report_images.template_id = '.$id.' AND project_report_images.approved_by_pm_at IS NOT NULL) as approvedPMCount'),
                        ])
                        ->where('prsc.cat_id',$id)
                        ->where('prsc.deleted_at',null)
                        ->groupBy('prsc.id')
                        ->get();
                ###image type end
                ###text type
                    //data subcategory
                        $data['dataSubcategoryText'] = DB::table('project_report_subcategory as prs')
                        ->leftjoin('project_report_text','project_report_text.subcat_id','prs.id')
                        ->select([
                            'prs.*',
                            DB::raw('(SELECT COUNT(*) FROM project_report_text as pri WHERE pri.subcat_id = prs.id AND pri.project_id = '.$projectId.' AND pri.task_id = '.$taskId.') as textCount'),

                            DB::raw('(SELECT COUNT(project_report_text.submitted_at) FROM project_report_text WHERE project_report_text.subcat_id = prs.id AND project_report_text.project_id='.$projectId.' AND project_report_text.task_id = '.$taskId.' AND project_report_text.template_id = '.$id.' AND project_report_text.submitted_at IS NOT NULL) as submittedCount'),

                            DB::raw('(SELECT COUNT(project_report_text.approved_at) FROM project_report_text WHERE project_report_text.subcat_id = prs.id AND project_report_text.project_id='.$projectId.' AND project_report_text.task_id = '.$taskId.' AND project_report_text.template_id = '.$id.' AND project_report_text.approved_at IS NOT NULL) as approvedCount'),

                            DB::raw('(SELECT COUNT(project_report_text.approved_by_pm_at) FROM project_report_text WHERE project_report_text.subcat_id = prs.id AND project_report_text.project_id='.$projectId.' AND project_report_text.task_id = '.$taskId.' AND project_report_text.template_id = '.$id.' AND project_report_text.approved_by_pm_at IS NOT NULL) as approvedPMCount'),

                        ])
                        ->where('prs.status',$subcatStatus)
                        ->where('prs.cat_id',$id)
                        ->where('prs.deleted_at',null)
                        ->groupBy('prs.id')
                        ->get();
                    //customized subcategories
                        $data['dataSubcategoryCustomizedText'] = DB::table('project_report_subcategory_customized as prsc')
                        ->leftjoin('project_report_text','project_report_text.subcatcustom_id','prsc.id')
                        ->select([
                            'prsc.*',
                            DB::raw('(SELECT COUNT(*) FROM project_report_text as pri WHERE pri.subcatcustom_id = prsc.id AND pri.project_id = '.$projectId.' AND pri.task_id = '.$taskId.') as textCount'),

                            DB::raw('(SELECT COUNT(project_report_text.submitted_at) FROM project_report_text WHERE project_report_text.subcatcustom_id = prsc.id AND project_report_text.project_id='.$projectId.' AND project_report_text.task_id = '.$taskId.' AND project_report_text.template_id = '.$id.' AND project_report_text.submitted_at IS NOT NULL) as submittedCount'),

                            DB::raw('(SELECT COUNT(project_report_text.approved_at) FROM project_report_text WHERE project_report_text.subcatcustom_id = prsc.id AND project_report_text.project_id='.$projectId.' AND project_report_text.task_id = '.$taskId.' AND project_report_text.template_id = '.$id.' AND project_report_text.approved_at IS NOT NULL) as approvedCount'),

                            DB::raw('(SELECT COUNT(project_report_text.approved_by_pm_at) FROM project_report_text WHERE project_report_text.subcatcustom_id = prsc.id AND project_report_text.project_id='.$projectId.' AND project_report_text.task_id = '.$taskId.' AND project_report_text.template_id = '.$id.' AND project_report_text.approved_by_pm_at IS NOT NULL) as approvedPMCount'),
                        ])
                        ->where('prsc.cat_id',$id)
                        ->where('prsc.deleted_at',null)
                        ->groupBy('prsc.id')
                        ->get();
                ###text type end
                ###file type
                    //data subcategory
                        $data['dataSubcategoryFiles'] = DB::table('project_report_subcategory as prs')
                        ->leftjoin('project_report_file','project_report_file.subcat_id','prs.id')
                        ->select([
                            'prs.*',
                            DB::raw('(SELECT id FROM project_report_file as pri WHERE pri.subcat_id = prs.id AND pri.project_id = '.$projectId.' AND pri.task_id = '.$taskId.') as prf_id'),

                            DB::raw('(SELECT COUNT(*) FROM project_report_file as pri WHERE pri.subcat_id = prs.id AND pri.project_id = '.$projectId.' AND pri.task_id = '.$taskId.' AND pri.filled IS NOT NULL) as fileCount'),

                            DB::raw('(SELECT name FROM project_report_file as pri WHERE pri.subcat_id = prs.id AND pri.project_id = '.$projectId.' AND pri.task_id = '.$taskId.') as file_name'),
                            
                            DB::raw('(SELECT filled FROM project_report_file as pri WHERE pri.subcat_id = prs.id AND pri.project_id = '.$projectId.' AND pri.task_id = '.$taskId.') as uploaded_file_name'),

                            DB::raw('(SELECT COUNT(project_report_file.submitted_at) FROM project_report_file WHERE project_report_file.subcat_id = prs.id AND project_report_file.project_id='.$projectId.' AND project_report_file.task_id = '.$taskId.' AND project_report_file.submitted_at IS NOT NULL) as submittedCount'),

                            DB::raw('(SELECT COUNT(project_report_file.approved_at) FROM project_report_file WHERE project_report_file.subcat_id = prs.id AND project_report_file.project_id='.$projectId.' AND project_report_file.task_id = '.$taskId.' AND project_report_file.approved_at IS NOT NULL) as approvedCount'),

                            DB::raw('(SELECT COUNT(project_report_file.approved_by_pm_at) FROM project_report_file WHERE project_report_file.subcat_id = prs.id AND project_report_file.project_id='.$projectId.' AND project_report_file.task_id = '.$taskId.' AND project_report_file.approved_by_pm_at IS NOT NULL) as approvedPMCount'),

                        ])
                        ->where('prs.status',$subcatStatus)
                        ->where('prs.cat_id',$id)
                        ->where('prs.deleted_at',null)
                        ->groupBy('prs.id')
                        ->get();
                    //customized subcategories
                        $data['dataSubcategoryCustomizedFile'] = DB::table('project_report_subcategory_customized as prsc')
                        ->leftjoin('project_report_file','project_report_file.subcatcustom_id','prsc.id')
                        ->select([
                            'prsc.*',
                            DB::raw('(SELECT id FROM project_report_file as pri WHERE pri.subcatcustom_id = prsc.id AND pri.project_id = '.$projectId.' AND pri.task_id = '.$taskId.') as prf_id'),

                            DB::raw('(SELECT COUNT(*) FROM project_report_file as pri WHERE pri.subcatcustom_id = prsc.id AND pri.project_id = '.$projectId.' AND pri.task_id = '.$taskId.' AND pri.filled IS NOT NULL) as fileCount'),

                            DB::raw('(SELECT name FROM project_report_file as pri WHERE pri.subcatcustom_id = prsc.id AND pri.project_id = '.$projectId.' AND pri.task_id = '.$taskId.') as file_name'),

                            DB::raw('(SELECT filled FROM project_report_file as pri WHERE pri.subcatcustom_id = prsc.id AND pri.project_id = '.$projectId.' AND pri.task_id = '.$taskId.') as uploaded_file_name'),

                            DB::raw('(SELECT COUNT(project_report_file.submitted_at) FROM project_report_file WHERE project_report_file.subcatcustom_id = prsc.id AND project_report_file.project_id='.$projectId.' AND project_report_file.task_id = '.$taskId.' AND project_report_file.submitted_at IS NOT NULL) as submittedCount'),

                            DB::raw('(SELECT COUNT(project_report_file.approved_at) FROM project_report_file WHERE project_report_file.subcatcustom_id = prsc.id AND project_report_file.project_id='.$projectId.' AND project_report_file.task_id = '.$taskId.' AND project_report_file.approved_at IS NOT NULL) as approvedCount'),

                            DB::raw('(SELECT COUNT(project_report_file.approved_by_pm_at) FROM project_report_file WHERE project_report_file.subcatcustom_id = prsc.id AND project_report_file.project_id='.$projectId.' AND project_report_file.task_id = '.$taskId.' AND project_report_file.approved_by_pm_at IS NOT NULL) as approvedPMCount'),
                        ])
                        ->where('prsc.cat_id',$id)
                        ->where('prsc.deleted_at',null)
                        ->groupBy('prsc.id')
                        ->get();
                ###file type end
            ### sub category end ###
            return view('tech.report.show', $data);
        }

        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Report.');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Report.');
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
        $userDepartment = 1; //project department

        $projectId = $request->project_id;
        $taskId = $request->task_id;
        $templateId = $request->template_id; //category
        $reportType = 1; //image

        $subcatRequestData = $request->subcat_id;
        if ($subcatRequestData !== null) {
            $subcatId = $request->subcat_id;
        }else{
            $subcatId = $request->subcatcustom_id;
        }

        $catStatus = 1; //active category
        $subcatStatus = 1; //active subcategory
        $folderName = $request->folder_name;
        if ($folderName == null) {
            $folderName = 'others';
        }
        //check priviledge
            $firstCheck = DB::table('projects_task')->where('project_id',$projectId)->where('tech_id',$userId)->count();
        //secondcheck
            $secondcheck = DB::table('project_report_category')->select('type')->where('id',$templateId)->first();
            $currentReportType = $secondcheck->type;

        if ($firstCheck > 0) {
            if ($currentReportType == $reportType) {
                //update status
                if (isset($request->status)) {
                    //data status
                    #$data = $request->except(['_token','submit','_method','folder_name']);
                    $data = $request->only(['status']);
                    $data['status'] = $request->status;
                    $data['submitted_at'] = Carbon::now()->format('Y-m-d H:i:s');
    
                    if ($subcatRequestData !== null) {
                        DB::table('project_report_images')
                        ->where('project_id',$projectId)
                        ->where('task_id',$taskId)
                        ->where('template_id',$templateId)
                        ->where('subcat_id',$subcatId)
                        ->update($data);
                    }else{
                        DB::table('project_report_images')
                        ->where('project_id',$projectId)
                        ->where('task_id',$taskId)
                        ->where('template_id',$templateId)
                        ->where('subcatcustom_id',$subcatId)
                        ->update($data);
                    }
                    if ($subcatRequestData !== null) {
                        return redirect()->route('report-tech.create', 'project_id='.$projectId.'&task_id='.$taskId.'&template_id='.$templateId.'&subcat_id='.$subcatId)->with('alert-success','Data berhasil disimpan.');
                    }else{
                        return redirect()->route('report-tech.create', 'project_id='.$projectId.'&task_id='.$taskId.'&template_id='.$templateId.'&subcatcustom_id='.$subcatId)->with('alert-success','Data berhasil disimpan.');
                    }
                }
                //image post handle
                    $request->validate([
                        'image' => 'mimes:jpeg,jpg,png,pdf|max:9216',
                    ]);
                //file handler
                    $fileName = null;
                    $destinationPath = public_path().'/img/projects/'.$folderName.'/';
                // Retrieving An Uploaded File
                    $file = $request->file('image');
                    if (!empty($file)) {
                        $extension = $file->getClientOriginalExtension();
                        
                        //custom filename
                        $projectData = DB::table('projects')->select('name')->where('id',$projectId)->first();
                        $taskData = DB::table('projects_task')->select('name')->where('id',$taskId)->first();
                        $projectName = Str::slug($projectData->name,'-');
                        $taskName = Str::slug($taskData->name,'-');
                        $fileName = $projectName.'_'.$taskName.'_'.time().'_'.$file->getClientOriginalName();
                
                        // Moving An Uploaded File
                        $request->file('image')->move($destinationPath, $fileName);
        
                        //delete previous image
                        $dataImage = DB::table('project_report_images')->select('image as image')->where('id', $id)->first();
                        $oldImage = $dataImage->image;
        
                        if($oldImage !== 'default.png'){
                            $image_path = $destinationPath.$oldImage;
                            if(File::exists($image_path)) {
                                File::delete($image_path);
                            }
                        }
                    }
                //custom setting to support file upload
                    $data = $request->except(['_token','submit','_method','folder_name']);
                    $data['publisher_id'] = $userId;
                    $data['publisher_type'] = $userType;
                    $data['created_at'] = Carbon::now()->format('Y-m-d H:i:s');
                    if (!empty($fileName)) {
                        $data['image'] = $fileName;
                    }
                //insert to database
                    DB::table('project_report_images')->where('id',$id)->update($data);
            }else{
                //update status
                if (isset($request->status)) {
                    //data status
                    #$data = $request->except(['_token','submit','_method','folder_name']);
                    $data = $request->only(['status']);
                    $data['status'] = $request->status;
                    $data['submitted_at'] = Carbon::now()->format('Y-m-d H:i:s');
    
                    if ($subcatRequestData !== null) {
                        DB::table('project_report_text')
                        ->where('project_id',$projectId)
                        ->where('task_id',$taskId)
                        ->where('template_id',$templateId)
                        ->where('subcat_id',$subcatId)
                        ->update($data);
                    }else{
                        DB::table('project_report_text')
                        ->where('project_id',$projectId)
                        ->where('task_id',$taskId)
                        ->where('template_id',$templateId)
                        ->where('subcatcustom_id',$subcatId)
                        ->update($data);
                    }
                    if ($subcatRequestData !== null) {
                        return redirect()->route('report-tech.create', 'project_id='.$projectId.'&task_id='.$taskId.'&template_id='.$templateId.'&subcat_id='.$subcatId)->with('alert-success','Data berhasil disimpan.');
                    }else{
                        return redirect()->route('report-tech.create', 'project_id='.$projectId.'&task_id='.$taskId.'&template_id='.$templateId.'&subcatcustom_id='.$subcatId)->with('alert-success','Data berhasil disimpan.');
                    }
                }
                //custom setting to support file upload
                    $data = $request->except(['_token','submit','_method','folder_name']);
                    $data['publisher_id'] = $userId;
                    $data['publisher_type'] = $userType;
                    $data['created_at'] = Carbon::now()->format('Y-m-d H:i:s');
                //insert to database
                    DB::table('project_report_text')->where('id',$id)->update($data);
            }
            //redirect page
            if ($subcatRequestData !== null) {
                return redirect()->route('report-tech.create', 'project_id='.$projectId.'&task_id='.$taskId.'&template_id='.$templateId.'&subcat_id='.$subcatId)->with('alert-success','Data berhasil disimpan.');
            }else{
                return redirect()->route('report-tech.create', 'project_id='.$projectId.'&task_id='.$taskId.'&template_id='.$templateId.'&subcatcustom_id='.$subcatId)->with('alert-success','Data berhasil disimpan.');
            }
        }

        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Report.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Report.');
    }

    //customize
    public function report()
    {
        $userId = Auth::user()->id;
        $userType = Auth::user()->user_type;

        $data['userProfile'] = Tech::find($userId);
        $data['dataReports'] = DB::table('projects_report')->get();
        
        return view('tech.report.report', $data);
    }
}
