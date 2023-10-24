<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Auth;
use DB;

class UserProjectTemplateController extends Controller
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
        $userLevel = Auth::user()->user_level;
        $userDepartment = Auth::user()->department_id;

        $projectId  = $request->project_id;
        $taskId  = $request->task_id;
        //$templateId  = $request->template_id;

        $catStatus = 1; //active category
        $subcatStatus = 1; //active subcategory

        if ($userLevel == 3 || $userLevel == 4 && $userDepartment == 1) {
            //taskcheck
                $taskcheck = DB::table('projects_task')->where('project_id',$projectId)->where('id',$taskId)->where('pm_id',$userId)->where('deleted_at',null)->count();
                $taskcheckQCD = DB::table('projects_task')->where('project_id',$projectId)->where('id',$taskId)->where('qcd_id',$userId)->where('deleted_at',null)->count();
                //redeirect if requirements not met
                if ($taskcheck < 1 && $taskcheckQCD < 1) {
                    return redirect()->back()->with('alert-danger','Halaman yang Anda tuju tidak tersedia.');
                }
            //get all needed data
                if ($userLevel == 3) {
                    //project data
                        $data['project'] = DB::table('projects as proj')
                        ->select([
                            'proj.*',
                            DB::raw('(SELECT name FROM projects_category WHERE projects_category.id = proj.procat_id) as procat_name')
                        ])
                        ->where('id',$projectId)
                        ->where('pm_id',$userId)
                        ->first();
                    //project check
                        $data['infoTaskProject'] = DB::table('projects_task')
                        ->select([
                            'projects_task.*',
                            DB::raw('(SELECT id FROM projects WHERE projects.id = projects_task.project_id) as project_id'),
                            DB::raw('(SELECT name FROM projects WHERE projects.id = projects_task.project_id) as project_name')
                        ])
                        ->where('id',$taskId)
                        ->where('pm_id',$userId)
                        ->first();
                    //task data
                        $data['projectTaskDatas'] = DB::table('projects_task as pt')
                        ->select([
                            'pt.*',
                            DB::raw('(SELECT id FROM project_report_template_selected WHERE project_report_template_selected.task_id = pt.id) as template_id')
                        ])
                        ->where('project_id',$projectId)
                        ->where('pm_id',$userId)
                        ->where('deleted_at',null)->get();
                }else{
                    //project data
                        $data['project'] = DB::table('projects as proj')
                        ->select([
                            'proj.*',
                            DB::raw('(SELECT name FROM projects_category WHERE projects_category.id = proj.procat_id) as procat_name')
                        ])
                        ->where('id',$projectId)
                        ->first();
                    //project check
                        $data['infoTaskProject'] = DB::table('projects_task')
                        ->select([
                            'projects_task.*',
                            DB::raw('(SELECT id FROM projects WHERE projects.id = projects_task.project_id) as project_id'),
                            DB::raw('(SELECT name FROM projects WHERE projects.id = projects_task.project_id) as project_name')
                        ])
                        ->where('id',$taskId)
                        ->where('qcd_id',$userId)
                        ->first();
                    //task data
                        $data['projectTaskDatas'] = DB::table('projects_task as pt')
                        ->select([
                            'pt.*',
                            DB::raw('(SELECT id FROM project_report_template_selected WHERE project_report_template_selected.task_id = pt.id) as template_id')
                        ])
                        ->where('project_id',$projectId)
                        ->where('qcd_id',$userId)
                        ->where('deleted_at',null)->get();
                }
            //template data
                $data['projectTemplateDatas'] = DB::table('project_report_template_selected as prts')
                ->select([
                    'prts.*',
                    DB::raw('(SELECT name FROM projects_task WHERE projects_task.id = prts.task_id) as task_name'),
                    DB::raw('(SELECT type FROM project_report_category WHERE project_report_category.id = prts.template_id) as type'),
                    //type name
                        DB::raw('(SELECT name FROM project_report_all_format_type WHERE project_report_all_format_type.id = type) as type_name'),
                    //image count
                        DB::raw('(SELECT COUNT(project_report_images.image) FROM project_report_images WHERE project_report_images.task_id = prts.task_id AND project_report_images.template_id = prts.template_id AND project_report_images.image IS NOT NULL) as imageCount'),
                    //approved image count
                    DB::raw('(SELECT COUNT(project_report_images.approved_at) FROM project_report_images WHERE project_report_images.task_id = prts.task_id AND project_report_images.template_id = prts.template_id AND project_report_images.approved_at IS NOT NULL) as approvedImageCount'),
                ])
                ->where('project_id',$projectId)
                ->where('task_id',$taskId)
                //->where('template_id',$templateId)
                ->where('deleted_at',null)->get();
            //template count
                $data['projectTemplateCount'] = DB::table('project_report_category')->where('deleted_at',null)->count();
                $data['allProjectTemplates'] = DB::table('project_report_category')
                ->select([
                    'project_report_category.*',
                    DB::raw('(SELECT COUNT(*) FROM project_report_subcategory WHERE project_report_subcategory.cat_id = project_report_category.id) as subcatCount'),
                    //type name
                        DB::raw('(SELECT name FROM project_report_all_format_type WHERE project_report_all_format_type.id = project_report_category.type) as type_name'),
                ])
                ->where('deleted_at',null)
                ->paginate(10);

            //additional info
                $data['draftCount'] = DB::table('project_report_template')->where('publisher_id',$userId)->where('task_id',$taskId)->where('deleted_at',null)->count();
                $data['dataUsers'] = DB::table('users')->get();

            //return
            return view('user.project-template.index', $data);
        }

        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses data Project Template.');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses data Project Template.');
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
        $userLevel = Auth::user()->user_level;
        $userDepartment = Auth::user()->department_id;
        $projectStatus = $request->status;
        $projectId = $request->project_id;
        $taskId = $request->task_id;

        if ($userDepartment == 1 && $userLevel == 3 || $userLevel == 4) {

            $data = $request->except(['_token','submit','status']);
            $data['template_id'] = $request->template_id;
            $data['publisher_id'] = $userId;
            $data['publisher_type'] = $userType;
            #$data['cat_id'] = serialize($request['cat_id']);
            $data['subcat_id'] = serialize($request['subcat_id']);
            $data['created_at'] = Carbon::now()->format('Y-m-d H:i:s');

            DB::table('project_report_template_selected')->insert($data);

            if ($projectStatus != null) {
                return redirect()->route('user-projects.index','status='.$projectStatus)->with('alert-success','Data berhasil disimpan');
            }
            
            return redirect()->route('user-projects-template.index', 'project_id='.$projectId.'&task_id='.$taskId)->with('alert-success','Data berhasil disimpan');
        }

        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses data Project Template.');
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
        $userLevel = Auth::user()->user_level;
        $userDepartment = Auth::user()->department_id;

        $projectId = $request->project_id;
        $taskId = $request->task_id;
        $catStatus = 1; //active category
        $subcatStatus = 1; //active subcategory
        
        //pm view report
        $rs = $request->rs;

        if ($userLevel == 3 || $userLevel == 4 && $userDepartment == 1) {

            ### view image report ###
            if ($rs != null) {
                //project_id data
                    $data['projectId'] = $projectId;
                //firstcheck
                    $firstcheck = DB::table('projects_task')->where('project_id',$projectId)->where('id',$taskId)->where('pm_id',$userId)->where('deleted_at',null)->count();
                    $firstcheckQCD = DB::table('projects_task')->where('project_id',$projectId)->where('id',$taskId)->where('qcd_id',$userId)->where('deleted_at',null)->count();

                    if ($firstcheck < 1 && $firstcheckQCD < 1) {
                        return redirect()->back()->with('alert-danger','Halaman yang Anda tuju tidak tersedia.');
                    }
                //second check
                    $dataCheck = DB::table('project_report_category')->where('id',$id)->where('deleted_at',null)->count();
                    if ($dataCheck < 1) {
                        return redirect()->back()->with('alert-danger','Halaman yang Anda tuju tidak tersedia.');
                    }

                    $data['projectTemplate'] = DB::table('project_report_template_selected as prts')
                    ->join('projects_task','projects_task.id','prts.task_id')
                    ->select([
                        'prts.*',
                        DB::raw('(SELECT count(*) FROM projects_task WHERE projects_task.id = prts.id) as taskCount'),
                        DB::raw('(SELECT name FROM project_report_category WHERE project_report_category.id = prts.template_id) as name'),
                        DB::raw('(SELECT name FROM projects WHERE projects.id = prts.project_id) as project_name'),
                        DB::raw('(SELECT name FROM projects_task WHERE projects_task.id = prts.task_id) as task_name'),
                    ])
                    ->where('prts.template_id',$id)
                    ->where('prts.project_id',$projectId)
                    ->where('prts.task_id',$taskId)
                    ->where('prts.deleted_at',null)->first();
                //third check
                    $thirdCheck = $data['projectTemplate'];

                    if ($thirdCheck == null) {
                        return redirect()->back()->with('alert-danger','Halaman yang Anda tuju tidak tersedia.');
                    }
                //data
                    $data['subcats'] = unserialize($data['projectTemplate']->subcat_id);
                //data
                    $data['dataCategory'] = DB::table('project_report_category')->where('id',$id)->where('deleted_at',null)->first();
                    $data['dataSubcategory'] = DB::table('project_report_subcategory')->where('status',$subcatStatus)->where('cat_id',$id)->where('deleted_at',null)->get();
                #$data['dataSubcategory'] = DB::table('project_report_subcategory')->where('status',$subcatStatus)->where('deleted_at',null)->get();
                
                //add category feature
                $data['projectReportCategorys'] = DB::table('project_report_category')->where('publisher_id',$userId)->where('publisher_type', $userType)->where('deleted_at', null)->orderBy('id','DESC')->get();

                //task data
                if (isset($taskId)) {
                    $data['infoTaskProject'] = DB::table('projects_task')
                    ->select([
                        'projects_task.*',
                        DB::raw('(SELECT id FROM projects WHERE projects.id = projects_task.project_id) as project_id'),
                        DB::raw('(SELECT name FROM projects WHERE projects.id = projects_task.project_id) as project_name')
                    ])
                    ->where('id',$taskId)->first();
                }else{
                    $data['infoTaskProject'] = DB::table('projects_task')
                    ->select([
                        'projects_task.*',
                        DB::raw('(SELECT id FROM projects WHERE projects.id = projects_task.project_id) as project_id'),
                        DB::raw('(SELECT name FROM projects WHERE projects.id = projects_task.project_id) as project_name')
                    ])
                    ->where('project_id',$projectId)
                    ->where('deleted_at',null)
                    ->get();
                }

                $data['taskCount'] = $request->task_count;
                $data['templateCount'] = DB::table('project_report_template_selected')->where('template_id',$id)->where('task_id',$taskId)->count();

                return view('user.project.report.create', $data);
            }
            ### view image report end ###

            #### view template ###
                //project_id data
                    $data['projectId'] = $projectId;
                    $data['templateId'] = $id;
                //firstcheck
                    $firstcheck = DB::table('projects_task')->where('project_id',$projectId)->where('pm_id',$userId)->where('deleted_at',null)->count();
                    $firstcheckQCD = DB::table('projects_task')->where('project_id',$projectId)->where('qcd_id',$userId)->where('deleted_at',null)->count();
                    if ($firstcheck < 1 && $firstcheckQCD < 1) {
                        return redirect()->back()->with('alert-danger','Halaman yang Anda tuju tidak tersedia.');
                    }

                //second check
                    $dataCheck = DB::table('project_report_category')->where('id',$id)->where('deleted_at',null)->count();
                    if ($dataCheck < 1) {
                        return redirect()->back()->with('alert-danger','Halaman yang Anda tuju tidak tersedia.');
                    }
                    
                    $dataCheck2 = DB::table('project_report_template_selected')->where('project_id',$projectId)->where('task_id',$taskId)->where('template_id',$id)->where('deleted_at',null)->count();
                    
                    if (isset($projectId) && $dataCheck2 > 0) {
                        $data['projectTemplate'] = DB::table('project_report_template_selected as prts')
                        ->select([
                            'prts.*',
                            DB::raw('(SELECT name FROM project_report_category WHERE project_report_category.id = prts.template_id) as name'),
                            DB::raw('(SELECT name FROM projects WHERE projects.id = prts.project_id) as project_name'),
                            DB::raw('(SELECT name FROM projects_task WHERE projects_task.id = prts.task_id) as task_name'),
                            DB::raw('(SELECT status FROM projects WHERE projects.id = prts.project_id) as project_status')
                        ])
                        ->where('template_id',$id)
                        ->where('project_id',$projectId)
                        ->where('task_id',$taskId)
                        ->where('deleted_at',null)->first();
            
                        //data
                        $data['subcats'] = unserialize($data['projectTemplate']->subcat_id);
                    }else{
                        $data['projectTemplate'] = DB::table('project_report_category')->where('id',$id)->where('deleted_at',null)->first();
                    }

                //data
                $data['dataCategory'] = DB::table('project_report_category')->where('id',$id)->where('deleted_at',null)->first();
                $data['dataSubcategory'] = DB::table('project_report_subcategory')->where('status',$subcatStatus)->where('cat_id',$id)->where('deleted_at',null)->get();
                #$data['dataSubcategory'] = DB::table('project_report_subcategory')->where('status',$subcatStatus)->where('deleted_at',null)->get();
                
                //add category feature
                $data['projectReportCategorys'] = DB::table('project_report_category')->where('publisher_id',$userId)->where('publisher_type', $userType)->where('deleted_at', null)->orderBy('id','DESC')->get();

                //task data
                if (isset($taskId)) {
                    $data['infoTaskProject'] = DB::table('projects_task')
                    ->select([
                        'projects_task.*',
                        DB::raw('(SELECT id FROM projects WHERE projects.id = projects_task.project_id) as project_id'),
                        DB::raw('(SELECT name FROM projects WHERE projects.id = projects_task.project_id) as project_name')
                    ])
                    ->where('id',$taskId)->first();
                }else{
                    $data['infoTaskProject'] = DB::table('projects_task')
                    ->select([
                        'projects_task.*',
                        DB::raw('(SELECT id FROM projects WHERE projects.id = projects_task.project_id) as project_id'),
                        DB::raw('(SELECT name FROM projects WHERE projects.id = projects_task.project_id) as project_name')
                    ])
                    ->where('project_id',$projectId)
                    ->where('deleted_at',null)
                    ->get();
                }

                $data['taskCount'] = $request->task_count;
                $data['templateCount'] = DB::table('project_report_template_selected')->where('template_id',$id)->where('task_id',$taskId)->count();

                //if there is no projectId nor taskId
                if ($projectId == null && $taskId == null) {
                    return view('user.project-template.show-view', $data);
                }

                return view('user.project-template.show', $data);
            #### view template end ###
        }

        // QC Document old version
            /* 
            if ($userLevel == 4 && $userDepartment == 1) {
                ### view image report ###
                    //project_id data
                    $data['projectId'] = $projectId;
                    //firstcheck
                    $firstcheck = DB::table('projects_task')->where('project_id',$projectId)->where('id',$taskId)->where('qcd_id',$userId)->where('deleted_at',null)->count();
                    if ($firstcheck < 1) {
                        return redirect()->back()->with('alert-danger','Halaman yang Anda tuju tidak tersedia.');
                    }
                    
                    //second check
                    $dataCheck = DB::table('project_report_category')->where('id',$id)->where('deleted_at',null)->count();
                    if ($dataCheck < 1) {
                        return redirect()->back()->with('alert-danger','Halaman yang Anda tuju tidak tersedia.');
                    }

                    $data['projectTemplate'] = DB::table('project_report_template_selected as prts')
                    ->join('projects_task','projects_task.id','prts.task_id')
                    ->select([
                        'prts.*',
                        DB::raw('(SELECT count(*) FROM projects_task WHERE projects_task.id = prts.id) as taskCount'),
                        DB::raw('(SELECT name FROM project_report_category WHERE project_report_category.id = prts.template_id) as name'),
                        DB::raw('(SELECT name FROM projects WHERE projects.id = prts.project_id) as project_name'),
                        DB::raw('(SELECT name FROM projects_task WHERE projects_task.id = prts.task_id) as task_name'),
                        DB::raw('(SELECT status FROM projects WHERE projects.id = prts.project_id) as project_status')
                    ])
                    ->where('prts.template_id',$id)
                    ->where('prts.project_id',$projectId)
                    ->where('prts.task_id',$taskId)
                    ->where('prts.deleted_at',null)->first();

                    //third check
                    $thirdCheck = $data['projectTemplate'];
                    if ($thirdCheck == null) {
                        return redirect()->back()->with('alert-danger','Halaman yang Anda tuju tidak tersedia.');
                    }

                    //data
                    $data['subcats'] = unserialize($data['projectTemplate']->subcat_id);

                    //data
                    $data['dataCategory'] = DB::table('project_report_category')->where('id',$id)->where('deleted_at',null)->first();
                    $data['dataSubcategory'] = DB::table('project_report_subcategory')->where('status',$subcatStatus)->where('cat_id',$id)->where('deleted_at',null)->get();
                    #$data['dataSubcategory'] = DB::table('project_report_subcategory')->where('status',$subcatStatus)->where('deleted_at',null)->get();
                    
                    //add category feature
                    $data['projectReportCategorys'] = DB::table('project_report_category')->where('publisher_id',$userId)->where('publisher_type', $userType)->where('deleted_at', null)->orderBy('id','DESC')->get();

                    //task data
                    if (isset($taskId)) {
                        $data['infoTaskProject'] = DB::table('projects_task')
                        ->select([
                            'projects_task.*',
                            DB::raw('(SELECT id FROM projects WHERE projects.id = projects_task.project_id) as project_id'),
                            DB::raw('(SELECT name FROM projects WHERE projects.id = projects_task.project_id) as project_name')
                        ])
                        ->where('id',$taskId)->first();
                    }else{
                        $data['infoTaskProject'] = DB::table('projects_task')
                        ->select([
                            'projects_task.*',
                            DB::raw('(SELECT id FROM projects WHERE projects.id = projects_task.project_id) as project_id'),
                            DB::raw('(SELECT name FROM projects WHERE projects.id = projects_task.project_id) as project_name')
                        ])
                        ->where('project_id',$projectId)
                        ->where('deleted_at',null)
                        ->get();
                    }

                    $data['taskCount'] = $request->task_count;
                    $data['templateCount'] = DB::table('project_report_template_selected')->where('template_id',$id)->where('task_id',$taskId)->count();

                    return view('user.project.report.create', $data);
                ### view image report end ###
            }
            */
        //return
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses data Project Template.');
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
        $userLevel = Auth::user()->user_level;
        $userDepartment = Auth::user()->department_id;
        $catStatus = 1; //active category
        $subcatStatus = 1; //active subcategory

        if ($userDepartment == 1 && $userLevel == 3 || $userLevel == 4) {
            //get data
                $data['projectTemplate'] = DB::table('project_report_template_selected as prts')
                ->select([
                    'prts.*',
                    DB::raw('(SELECT id FROM projects WHERE projects.id = prts.project_id) as project_id'),
                    DB::raw('(SELECT name FROM projects WHERE projects.id = prts.project_id) as project_name'),
                    DB::raw('(SELECT name FROM projects_task WHERE projects_task.id = prts.task_id) as task_name'),
                    DB::raw('(SELECT status FROM projects WHERE projects.id = prts.project_id) as project_status'),
                    //type
                        DB::raw('(SELECT type FROM project_report_category WHERE project_report_category.id = prts.template_id) as type'),
                    //file datas
                        DB::raw('(SELECT name FROM project_report_file WHERE project_report_file.prts_id = prts.id) as file_name'),
                        DB::raw('(SELECT COUNT(*) FROM project_report_file WHERE project_report_file.prts_id = prts.id) as file_count'),
                ])
                ->where('id',$id)->where('deleted_at',null)->first();
            //file template data
                $data['projectTemplateFile'] = DB::table('project_report_file')->where('prts_id',$id)->first();
            //data
                if (isset($data['projectTemplate']->subcat_id)) {
                    $data['subcats'] = unserialize($data['projectTemplate']->subcat_id);
                    $templateId = $data['projectTemplate']->template_id;
                    $data['dataSubcategory'] = DB::table('project_report_subcategory')->where('status',$subcatStatus)->where('cat_id',$templateId)->where('deleted_at',null)->get();
                    $data['subcatsCustomized'] = DB::table('project_report_subcategory_customized')->where('cat_id',$templateId)->where('deleted_at',null)->get();
                }else{
                    $data['dataSubcategory'] = DB::table('project_report_subcategory')->where('status',$subcatStatus)->where('deleted_at',null)->get();
                    $data['subcatsCustomized'] = DB::table('project_report_subcategory_customized')->where('deleted_at',null)->get();
                }
                if (isset($data['projectTemplate']->subcatcustom_id)) {
                    $data['subcatscustom'] = unserialize($data['projectTemplate']->subcatcustom_id);
                }
            //add category feature
                $data['projectReportCategorys'] = DB::table('project_report_category')->where('publisher_id',$userId)->where('publisher_type', $userType)->where('deleted_at', null)->orderBy('id','DESC')->limit(5)->get();
            //return view
            return view('user.project-template.edit', $data);
        }

        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses data Project Template.');
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
        $userLevel = Auth::user()->user_level;
        $userDepartment = Auth::user()->department_id;
        #$projectStatus = $request->project_status;

        $projectId = $request->project_id;
        $taskId = $request->task_id;

        if ($userDepartment == 1 && $userLevel == 3 || $userLevel == 4) {

            $data = $request->except(['_token','_method','submit','project_status','subcatname']);
            #$data['template_id'] = $request->template_id;
            #$data['publisher_id'] = $userId;
            #$data['publisher_type'] = $userType;
            #$data['cat_id'] = serialize($request['cat_id']);
            $data['subcat_id'] = serialize($request['subcat_id']);
            $data['subcatcustom_id'] = serialize($request['subcatcustom_id']);
            #$data['created_at'] = Carbon::now()->format('Y-m-d H:i:s');

            #dd($data);

            DB::table('project_report_template_selected')->where('id', $id)->update($data);

            return redirect()->route('user-projects-template.index', 'project_id='.$projectId.'&task_id='.$taskId)->with('alert-success','Data berhasil diubah');
        }

        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses data Project Template.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        $userId = Auth::user()->id;
        $userLevel = Auth::user()->user_level;
        $userDepartment = Auth::user()->department_id;
        $projectId = $request->project_id;
        $taskId = $request->task_id;
        #$hapus_task_template = $request->hapus_task_template;

        if ($userDepartment == 1 && $userLevel == 3 || $userLevel == 4) {

            //remove from task
            /*if ($hapus_task_template != null) {
                $data['project_id'] = null;
                $data['task_id'] = null;
    
                DB::table('project_report_template_selected')->where('id', $id)->update($data);

                return redirect()->route('user-projects.show',$projectId)->with('alert-success','Data berhasil diubah');
            }
            */

            //version 1
                //$data['deleted_at'] = Carbon::now()->format('Y-m-d H:i:s');
                //DB::table('project_report_template_selected')->where('id', $id)->update($data);
            //version 2
                DB::table('project_report_template_selected')->delete($id);

            return redirect()->back()->with('alert-success','Data berhasil dihapus.');
        }

        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses data Project Template.');
    }
}
