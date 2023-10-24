<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use DB;

class UserProjectController extends Controller
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
        $userLevel = Auth::user()->user_level;
        $userDepartment = Auth::user()->department_id;
        $projectStatus = $request->status;

        //supporting data
        $data['dataProjectStatus'] = DB::table('projects_status')->get();
        $data['taskNumber'] = DB::table('projects_task')->latest('id')->first();
        
        $data['users'] = DB::table('users')->where('department_id',$userDepartment)->get();
        $data['techs'] = DB::table('techs')->get();
        
        //user level 3 = project manager user
        //user level 4 = QC document user
        //user level 6 = QC expense user
        
        if ($userDepartment == 1) {

            if ($userLevel == 22) {
                //project data for co admin
                $data['projects'] = DB::table('projects')
                ->leftjoin('projects_task','projects_task.project_id','projects.id')
                ->select([
                    'projects.*',
                    DB::raw('(SELECT name FROM projects_status WHERE projects_status.id = projects.status AND projects.status IS NOT NULL) as project_status_name'),
                    DB::raw('(SELECT COUNT(*) FROM projects_task WHERE projects_task.project_id = projects.id AND projects_task.pm_id = projects.pm_id AND projects_task.deleted_at IS NULL) as taskCount'),
                ])
                ->where('projects.deleted_at',null)
                #->where('projects_task.deleted_at',null)
                ->groupBy('projects.id')
                ->orderBy('projects.status','ASC')
                ->orderBy('projects.created_at','DESC')
                ->paginate(10);
    
                //project count
                $data['projectsCount'] = DB::table('projects')->where('deleted_at',null)->count();
    
                //task Data
                //$data['taskDatas'] = DB::table('projects_task')->where('pm_id',$userId)->where('deleted_at',null)->get();
                
                return view('user.project.index-co-admin', $data);
            }elseif ($userLevel == 3) {
                //project data for pm
                $data['projects'] = DB::table('projects')
                ->leftjoin('projects_task','projects_task.project_id','projects.id')
                ->select([
                    'projects.*',
                    DB::raw('(SELECT name FROM projects_status WHERE projects_status.id = projects.status AND projects.status IS NOT NULL) as project_status_name'),
                    DB::raw('(SELECT COUNT(*) FROM projects_task WHERE projects_task.project_id = projects.id AND projects_task.pm_id = projects.pm_id AND projects_task.deleted_at IS NULL) as taskCount'),
                ])
                ->where('projects.pm_id',$userId)
                ->where('projects.deleted_at',null)
                #->where('projects_task.deleted_at',null)
                ->groupBy('projects.id')
                ->orderBy('projects.status','ASC')
                ->orderBy('projects.created_at','DESC')
                ->paginate(10);
    
                //project count
                $data['projectsCount'] = DB::table('projects')->where('pm_id',$userId)->where('deleted_at',null)->count();
    
                //task Data
                //$data['taskDatas'] = DB::table('projects_task')->where('pm_id',$userId)->where('deleted_at',null)->get();
                
                return view('user.project.index-pm', $data);
            }else{
                if ($userLevel == 2) {
                    $user_field = 'pc_id';
                }elseif($userLevel == 4){
                    $user_field = 'qcd_id';
                }elseif($userLevel == 5){
                    $user_field = 'qct_id';
                }elseif($userLevel == 6){
                    $user_field = 'qce_id';
                }else{
                    return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Dashboard Project.');
                }

                $data['projects'] = DB::table('projects_task')
                ->select([
                    'projects_task.*',
                    DB::raw('(SELECT id FROM projects WHERE projects.id = projects_task.project_id) as project_id'),
                    DB::raw('(SELECT name FROM projects WHERE projects.id = projects_task.project_id) as project_name'),
                    DB::raw('(SELECT status FROM projects WHERE projects.id = projects_task.project_id AND projects.status IS NOT NULL) as project_status'),
                ])
                ->where($user_field,$userId)
                ->where('deleted_at',null)
                ->orderBy('projects_task.status','ASC')
                ->orderBy('projects_task.created_at','DESC')
                ->paginate(10);
    
                //project count
                $data['projectsCount'] = DB::table('projects_task')->where($user_field,$userId)->where('deleted_at',null)->count();
    
                return view('user.project.index', $data);
            }
        }
        
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Dashboard Project.');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $userId = Auth::user()->id;
        $userLevel = Auth::user()->user_level;
        $userDepartment = Auth::user()->department_id;
        $pmLevel = 3; //project manager

        if ($userLevel == 22 && $userDepartment == 1) {
            $data['projectNumber'] = DB::table('projects')->latest('id')->first();
            $data['dataUsers'] = DB::table('users')->where('user_level',$pmLevel)->get();
            $data['dataCategories'] = DB::table('projects_category')->get();
            $data['dataCustomers'] = DB::table('customers')->get();
    
            return view('user.project.create', $data);
        }

        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Dashboard Project.');
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
        $userLevel = Auth::user()->user_level;
        $userType = Auth::user()->user_type;
        $userDepartment = Auth::user()->department_id;

        if ($userLevel == 22 && $userDepartment == 1) {
            $request->validate([
                'name' => 'required|unique:projects,name,'.$request->name,
            ]);
    
            //custom setting to support file upload
            $data = $request->except(['_token','_method','submit']);
    
            DB::table('projects')->insert($data);

            //insert log & send notifications
                $dataProject = DB::table('projects')->orderBy('id','DESC')->first();
                ###publisher data
                $dataNotif['publisher_id'] = $userId;
                $dataNotif['publisher_type'] = $userType;
                $dataNotif['publisher_department'] = $userDepartment;

                $publisherName = Auth::user()->name;
                $publisherFirstname = Auth::user()->firstname;
                $publisherLastname = Auth::user()->lastname;
                if ($publisherFirstname !== null) {
                    $publisherName = ucfirst($publisherFirstname).' '.ucfirst($publisherLastname);
                }else{
                    $publisherName = ucfirst($publisherName);
                }
                ###receiver data
                $logName = $request->name;
                $dataReceiverDepartment = DB::table('admins')->where('department_id',$userDepartment)->first();
                $dataNotif['receiver_id'] = $dataReceiverDepartment->id;
                $dataNotif['receiver_type'] = 'admin';
                $dataNotif['receiver_department'] = $userDepartment;
                $dataNotif['level'] = 1;

                ###check pm
                $dataPMId = $request->pm_id;
                if (isset($dataPMId)) {
                    ###notification data for PM
                    $dataNotifPM['publisher_id'] = $userId;
                    $dataNotifPM['publisher_type'] = $userType;
                    $dataNotifPM['publisher_department'] = $userDepartment;

                    $dataNotifPM['receiver_id'] = $dataPMId;
                    $dataNotifPM['receiver_type'] = $userType;
                    $dataNotifPM['receiver_department'] = $userDepartment;
                    $dataNotifPM['level'] = 1;

                    ###notif message
                    $dataNotifPM['desc'] = "Membuat proyek <a href='".route('user-projects.show',$dataProject->id)."'><strong>".ucfirst($logName)."</strong></a> dan menunjuk Anda sebagai <strong>Project Manager</strong>.";
                    ###insert data to notifications table
                    DB::table('notifications')->insert($dataNotifPM);

                    ###logging
                    $dataPMs = DB::table('users')->where('id',$dataPMId)->first();
                    $dataLog['project_id'] = $dataProject->id;
                    $dataLog['name'] = "Penunjukan <strong>".ucfirst($dataPMs->firstname)." ".ucfirst($dataPMs->lastname)."</strong> sebagai Project Manager.";
                    $dataLog['publisher_id'] = $userId;
                    $dataLog['publisher_type'] = $userType;
                    DB::table('projects_log')->insert($dataLog);
                }

                ###logging
                $dataLog['project_id'] = $dataProject->id;
                $dataLog['name'] = "Pembuatan proyek <strong>".ucfirst($logName)."</strong>";
                $dataLog['publisher_id'] = $userId;
                $dataLog['publisher_type'] = $userType;
                DB::table('projects_log')->insert($dataLog);

                ###notif message
                $dataNotif['desc'] = "Membuat proyek <a href='".route('admin-projects.show',$dataProject->id)."'><strong>".ucfirst($logName)."</strong></a>";
                ###insert data to notifications table
                DB::table('notifications')->insert($dataNotif);
            //insert log & send notifications end

            return redirect()->route('user-projects.index')->with('alert-success','Data berhasil disimpan.');
        }

        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Dashboard Project.');
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
        $userLevel = Auth::user()->user_level;
        $userDepartment = Auth::user()->department_id;

        //template
        $pcLevel = 2; //PC
        $pmLevel = 3; //PM
        $qcdLevel = 4; //QC Document
        $qctLevel = 5; //QC Tools
        $qceLevel = 6; //QC Expenses

        //pr status
        $prStatus = 3; //3 approved

        // project department check
        if ($userDepartment == 1) {

            ###project task status
            $data['projectTaskStatus'] = DB::table('projects_task_status')->get();
            $data['taskNumber'] = DB::table('projects_task')->latest('id')->first();

            ### check priviledge - Co Admin
            if($userLevel == 22){
    
                $data['userDepartment'] = $userDepartment;
                
                $data['project'] = DB::table('projects as proj')
                ->select([
                    'proj.*',
                    DB::raw('(SELECT name FROM projects_category WHERE projects_category.id = proj.procat_id) as procat_name')
                ])
                ->where('id',$id)->first();
    
                $data['dataProjectStatus'] = DB::table('projects_status')->get();
                $data['dataTaskStatus'] = DB::table('projects_task_status')->get();
                
                $data['dataUsers'] = DB::table('users')->get();
                $data['dataTechs'] = DB::table('techs')->get();
    
                //task data
                $data['projectTaskDatas'] = DB::table('projects_task as pt')->where('project_id',$id)->where('deleted_at',null)->paginate(10);
    
                //task count manually  - work on both local and online
                $data['taskCount'] = DB::table('projects_task')->where('project_id',$id)->where('deleted_at',null)->count();
                $data['taskStatus0'] = DB::table('projects_task')->where('project_id',$id)->where('status',0)->where('deleted_at',null)->count();
                $data['taskStatus1'] = DB::table('projects_task')->where('project_id',$id)->where('status',1)->where('deleted_at',null)->count();
                $data['taskStatus2'] = DB::table('projects_task')->where('project_id',$id)->where('status',2)->where('deleted_at',null)->count();
                $data['taskStatus3'] = DB::table('projects_task')->where('project_id',$id)->where('status',3)->where('deleted_at',null)->count();
                $data['taskStatus4'] = DB::table('projects_task')->where('project_id',$id)->where('status',4)->where('deleted_at',null)->count();
    
                //template data
                $data['projectTemplateDatas'] = DB::table('project_report_template as prt')
                ->select([
                    'prt.*',
                    DB::raw('(SELECT name FROM projects_task WHERE projects_task.id = prt.task_id) as task_name')
                ])
                ->where('project_id',$id)->where('deleted_at',null)->get();
                $data['projectTemplateCount'] = DB::table('project_report_template')->where('project_id',$id)->where('deleted_at',null)->count();
                $data['allProjectTemplates'] = DB::table('project_report_template')->where('deleted_at',null)->get();
    
                return view('user.project.show-co-admin', $data);
            }

            ### check priviledge - PM
            if($userLevel == 3){
    
                //second check
                $secondCheck = DB::table('projects')->where('id',$id)->where('pm_id',$userId)->where('deleted_at',null)->count();
                if ($secondCheck < 1) {
                    return redirect()->back()->with('alert-danger','Halaman yang Anda tuju tidak tersedia.');
                }
    
                $data['userDepartment'] = $userDepartment;
                
                $data['project'] = DB::table('projects as proj')
                ->select([
                    'proj.*',
                    DB::raw('(SELECT name FROM projects_category WHERE projects_category.id = proj.procat_id) as procat_name')
                ])
                ->where('id',$id)->first();
    
                $data['dataProjectStatus'] = DB::table('projects_status')->get();
                $data['dataTaskStatus'] = DB::table('projects_task_status')->get();
                
                $data['dataUsers'] = DB::table('users')->get();
                $data['dataTechs'] = DB::table('techs')->get();
    
                //task data
                $data['projectTaskDatas'] = DB::table('projects_task as pt')->where('project_id',$id)->where('deleted_at',null)->paginate(10);
    
                //task count manually  - work on both local and online
                $data['taskCount'] = DB::table('projects_task')->where('project_id',$id)->where('deleted_at',null)->count();
                $data['taskStatus0'] = DB::table('projects_task')->where('project_id',$id)->where('status',0)->where('deleted_at',null)->count();
                $data['taskStatus1'] = DB::table('projects_task')->where('project_id',$id)->where('status',1)->where('deleted_at',null)->count();
                $data['taskStatus2'] = DB::table('projects_task')->where('project_id',$id)->where('status',2)->where('deleted_at',null)->count();
                $data['taskStatus3'] = DB::table('projects_task')->where('project_id',$id)->where('status',3)->where('deleted_at',null)->count();
                $data['taskStatus4'] = DB::table('projects_task')->where('project_id',$id)->where('status',4)->where('deleted_at',null)->count();
    
                //template data
                $data['projectTemplateDatas'] = DB::table('project_report_template as prt')
                ->select([
                    'prt.*',
                    DB::raw('(SELECT name FROM projects_task WHERE projects_task.id = prt.task_id) as task_name')
                ])
                ->where('project_id',$id)->where('deleted_at',null)->get();
                $data['projectTemplateCount'] = DB::table('project_report_template')->where('project_id',$id)->where('deleted_at',null)->count();
                $data['allProjectTemplates'] = DB::table('project_report_template')->where('deleted_at',null)->get();
    
                return view('user.project.show-pm', $data);
            }
    
            ### QC Document/Expenses ###
            if ($userLevel == 2 || $userLevel == 4 || $userLevel == 5 || $userLevel == 6) {
    
                ### default data
                $data['projectTask'] = null;

                ### check user level
                if ($userLevel == 2) {
                    $user_field = 'pc_id';
                }elseif($userLevel == 4){
                    $user_field = 'qcd_id';
                }elseif($userLevel == 5){
                    $user_field = 'qct_id';
                }elseif($userLevel == 6){
                    $user_field = 'qce_id';
                }else{
                    return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Dashboard Project.');
                }
                
                ### getting the data
                $data['projectTask'] = DB::table('projects_task')
                ->select([
                    'projects_task.*',
                    DB::raw('(SELECT name FROM projects WHERE projects.id = projects_task.project_id) as project_name'),
                    //task value
                    DB::raw('(SELECT budget FROM project_purchase_requisition WHERE project_purchase_requisition.task_id = projects_task.id AND  project_purchase_requisition.status = '.$prStatus.' AND project_purchase_requisition.budget IS NOT NULL) as task_budget'),
                ])
                ->where('id',$id)
                ->where($user_field,$userId)
                ->where('deleted_at',null)->first();
    
                //taskcheck
                if (!isset($data['projectTask'])) {
                    return redirect()->back()->with('alert-danger','Halaman yang Anda tuju tidak tersedia.');
                }
    
                $projectId = $data['projectTask']->project_id;
                $taskId = $data['projectTask']->id;
    
                ### user & tech datas ###
                    $data['dataPMs'] = DB::table('users')->where('department_id', $userDepartment)->where('user_level',$pmLevel)->get();
                    $data['dataPCs'] = DB::table('users')->where('department_id', $userDepartment)->where('user_level',$pcLevel)->get();
                    $data['dataQCDs'] = DB::table('users')->where('department_id', $userDepartment)->where('user_level',$qcdLevel)->get();
                    $data['dataQCEs'] = DB::table('users')->where('department_id', $userDepartment)->where('user_level',$qceLevel)->get();
                    $data['dataQCTs'] = DB::table('users')->where('department_id', $userDepartment)->where('user_level',$qctLevel)->get();
    
                    //data tech
                    $data['dataTechs'] = DB::table('techs')->get();
                ### user & tech datas end ###
                
                ### template data ###
                    $data['projectTemplateDatas'] = DB::table('project_report_template as prt')
                    ->select([
                        'prt.*',
                        DB::raw('(SELECT name FROM projects_task WHERE projects_task.id = prt.task_id) as task_name')
                    ])
                    ->where('project_id',$id)->where('deleted_at',null)->get();
                    $data['projectTemplateCount'] = DB::table('project_report_template')->where('project_id',$id)->where('deleted_at',null)->count();
                    $data['allProjectTemplates'] = DB::table('project_report_template')->where('deleted_at',null)->get();
                ### template data end ###
    
                return view('user.project.show-user', $data);
    
            }
        }

        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Dashboard Project.');
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
        $userLevel = Auth::user()->user_level;
        $userDepartment = Auth::user()->department_id;
        $pcLevel = 2;
        $pmLevel = 3;
        $adLevel = 4;
        
        //check priviledge
        if($userLevel == 3 && $userDepartment == 1){
            $data['project'] = DB::table('projects')
            ->select([
                'projects.*',
                DB::raw('(SELECT firstname FROM users WHERE users.id = projects.pc_id) as pc_firstname')
            ])
            ->where('id',$id)->first();
            $data['projectStatus'] = DB::table('projects_status')->get();
            $data['dataProjectCoordinators'] = DB::table('users')->where('department_id',$userDepartment)->where('user_level',$pcLevel)->get();
            $data['dataAdminDocuments'] = DB::table('users')->where('department_id',$userDepartment)->where('user_level',$adLevel)->get();
            $data['techs'] = DB::table('techs')->get();

            return view('user.project.edit', $data);
        }
        //co admin project

        if ($userLevel == 22 && $userDepartment == 1) {
            $data['userDepartment'] = $userDepartment;
            $data['project'] = DB::table('projects')->where('id',$id)->first();
            $data['projectStatus'] = DB::table('projects_status')->get();
            $data['dataUsers'] = DB::table('users')->where('user_level',$pmLevel)->get();
            $data['dataTechs'] = DB::table('techs')->get();
            $data['dataCustomers'] = DB::table('customers')->get();
            $data['dataCategories'] = DB::table('projects_category')->get();
    
            return view('user.project.edit-co-admin', $data);
        }
        
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Dashboard Project.');
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
        $userLevel = Auth::user()->user_level;
        $userType = Auth::user()->user_type;
        $userDepartment = Auth::user()->department_id;

        $projectId = $request->project_id;
        $projectStatus = $request->status;
        $updateProjectStatus = $request->update_status_project;
        $pmId = $request->pm_id;

        //check priviledge pm
        if($userLevel == 3 && $userDepartment == 1){

            //update project status
            if ($updateProjectStatus != null) {
                $data['status'] = $updateProjectStatus;
                
                DB::table('projects')->where('id', $projectId)->update($data);

                return redirect()->route('user-projects.show', $projectId)->with('alert-success','Data berhasil disimpan.');
            }

            //update project data
            $request->validate([
                'name' => 'required',
                'pc_id' => 'required',
            ]);

            //custom setting to support file upload
            $data = $request->except(['_token','_method','submit']);

            DB::table('projects')->where('id',$id)->update($data);

            return redirect()->route('user-projects.index','status='.$projectStatus)->with('alert-success','Data berhasil diubah.');
        }

        //co admin
        if ($userLevel == 22 && $userDepartment == 1) {
            $request->validate([
                'name' => 'required|unique:projects_task,name,'.$id,
            ]);
    
            //custom setting to support file upload
            $data = $request->except(['_token','_method','submit']);

            //check pm
            $checkCount = DB::table('projects_task')->where('project_id',$id)->where('pm_id',null)->where('deleted_at',null)->count();

            if ($pmId != null && $checkCount > 0) {
                $dataPM['pm_id'] = $request->pm_id;
                DB::table('projects_task')->where('project_id',$id)->update($dataPM);
            }
    
            //insert log & send notifications
                $oldProjectData = DB::table('projects')->where('id',$id)->first();
                ###publisher data
                $dataNotif['publisher_id'] = $userId;
                $dataNotif['publisher_type'] = $userType;
                $dataNotif['publisher_department'] = $userDepartment;

                $publisherName = Auth::user()->name;
                $publisherFirstname = Auth::user()->firstname;
                $publisherLastname = Auth::user()->lastname;
                if ($publisherFirstname !== null) {
                    $publisherName = ucfirst($publisherFirstname).' '.ucfirst($publisherLastname);
                }else{
                    $publisherName = ucfirst($publisherName);
                }
                ###receiver data
                $logName = $request->name;
                $dataReceiverDepartment = DB::table('admins')->where('department_id',$userDepartment)->first();
                $dataNotif['receiver_id'] = $dataReceiverDepartment->id;
                $dataNotif['receiver_type'] = 'admin';
                $dataNotif['receiver_department'] = $userDepartment;
                $dataNotif['level'] = 1;

                ###check pm ammendment
                $oldPMData = $oldProjectData->pm_id;
                $newPMData = $request->pm_id;
                $dataPMId = $newPMData;
                if ($oldPMData != $newPMData) {
                    ###notification data for PM
                    $dataNotifPM['publisher_id'] = $userId;
                    $dataNotifPM['publisher_type'] = $userType;
                    $dataNotifPM['publisher_department'] = $userDepartment;

                    $dataNotifPM['receiver_id'] = $dataPMId;
                    $dataNotifPM['receiver_type'] = $userType;
                    $dataNotifPM['receiver_department'] = $userDepartment;
                    $dataNotifPM['level'] = 1;

                    ###notif message
                    $dataNotifPM['desc'] = "Membuat proyek <a href='".route('user-projects.show',$oldProjectData->id)."'><strong>".ucfirst($logName)."</strong></a> dan menunjuk Anda sebagai <strong>Project Manager</strong>.";
                    ###insert data to notifications table for pm
                    DB::table('notifications')->insert($dataNotifPM);

                    ###logging
                    $dataProject = DB::table('projects')->orderBy('id','DESC')->first();
                    $dataPMs = DB::table('users')->where('id',$dataPMId)->first();
                    $dataLog['project_id'] = $dataProject->id;
                    $dataLog['name'] = "Penunjukan <strong>".ucfirst($dataPMs->firstname)." ".ucfirst($dataPMs->lastname)."</strong> sebagai Project Manager yang baru.";
                    $dataLog['publisher_id'] = $userId;
                    $dataLog['publisher_type'] = $userType;
                    DB::table('projects_log')->insert($dataLog);
                }

                ###check project name ammendment
                $oldNameData = $oldProjectData->name;
                $newNameData = $request->name;
                if ($oldNameData != $newNameData || $oldPMData != $newPMData) {
                    ###logging
                    $dataLog['project_id'] = $oldProjectData->id;
                    $dataLog['name'] = "Pembuatan proyek <strong>".ucfirst($logName)."</strong>";
                    $dataLog['publisher_id'] = $userId;
                    $dataLog['publisher_type'] = $userType;
                    DB::table('projects_log')->insert($dataLog);
    
                    ###notif message
                    if ($oldNameData != $newNameData && $oldPMData != $newPMData) {
                        $dataNotif['desc'] = "Mengubah proyek <a href='".route('admin-projects.show',$oldProjectData->id)."'><strong>".ucfirst($oldNameData)."</strong></a> menjadi <a href='".route('admin-projects.show',$oldProjectData->id)."'><strong>".ucfirst($newNameData)."</strong></a> dan menunjuk ".ucfirst($dataPMs->firstname).' '.ucfirst($dataPMs->lastname)." sebagai Project Manager yang baru.";
                    }elseif($oldPMData != $newPMData) {
                        $dataNotif['desc'] = "Menunjuk ".ucfirst($dataPMs->firstname)." ".ucfirst($dataPMs->lastname)." sebagai Project Manager yang baru untuk proyek <a href='".route('admin-projects.show',$oldProjectData->id)."'><strong>".ucfirst($oldNameData)."</strong></a>";
                    }else{
                        $dataNotif['desc'] = "Mengubah proyek <a href='".route('admin-projects.show',$oldProjectData->id)."'><strong>".ucfirst($oldNameData)."</strong></a> menjadi <a href='".route('admin-projects.show',$oldProjectData->id)."'><strong>".ucfirst($newNameData)."</strong></a>.";
                    }
                    ###insert data to notifications table
                    DB::table('notifications')->insert($dataNotif);
                }
            //insert log & send notifications end
            
            //input project data
            DB::table('projects')->where('id',$id)->update($data);

            return redirect()->route('user-projects.index','status='.$projectStatus)->with('alert-success','Data berhasil diubah.');
        }
            
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Dashboard Project.');
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
        $userLevel = Auth::user()->user_level;
        $userDepartment = Auth::user()->department_id;

        if ($userLevel == 22 && $userDepartment ==1) {
            DB::table('projects')->delete($id);

            return redirect()->back()->with('alert-success','Data berhasil dihapus.');
        }

        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Dashboard Project.');
    }

    //customization
    public function progress($id)
    {
        $userId = Auth::user()->id;
        $userLevel = Auth::user()->user_level;
        $userDepartment = Auth::user()->department_id;
        
        //getting all data by user
        if ($userLevel == 3 || $userLevel == 22 && $userDepartment == 1) {
            
            //second check
            if ($userLevel != 22) {
                $projectCount = DB::table('projects')->where('id',$id)->where('pm_id',$userId)->where('deleted_at',null)->count();
    
                if ($projectCount < 1) {
                    return redirect()->back()->with('alert-danger','Halaman yang Anda tuju tidak tersedia.');
                }
            }

            $data['project'] = DB::table('projects')
            ->leftJoin('projects_task','projects_task.project_id','projects.id')
            ->select([
                'projects.*',
                DB::raw('(SELECT COUNT(*) FROM projects_task WHERE projects_task.status=0 AND projects_task.project_id = projects.id) as newCount'),
                DB::raw('(SELECT COUNT(*) FROM projects_task WHERE projects_task.status=1 AND projects_task.project_id = projects.id) as inpreparationCount'),
                DB::raw('(SELECT COUNT(*) FROM projects_task WHERE projects_task.status=2 AND projects_task.project_id = projects.id) as onprogressCount'),
                DB::raw('(SELECT COUNT(*) FROM projects_task WHERE projects_task.status=3 AND projects_task.project_id = projects.id) as reportingCount'),
                DB::raw('(SELECT COUNT(*) FROM projects_task WHERE projects_task.status=4 AND projects_task.project_id = projects.id) as finishedCount'),
            ])
            ->where('projects_task.deleted_at',null)
            ->where('projects.id',$id)->first();

            //data
            $data['dataLogs'] = DB::table('projects_log')->orderBy('date','DESC')->where('project_id',$id)->get();

            return view('user.project.progress', $data);
        }
        
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Progres Project.');
    }

    public function dashboard()
    {
        $userId = Auth::user()->id;
        $userLevel = Auth::user()->user_level;
        $userDepartment = Auth::user()->department_id;

        ### PM datas ###
            if ($userLevel == 3 && $userDepartment == 1) {

                //getting all data
                $data['projects'] = DB::table('projects')
                ->join('projects_task','projects_task.project_id','projects.id')
                ->select([
                    'projects.*',
                    DB::raw('(SELECT COUNT(*) FROM projects_task WHERE projects_task.project_id = projects.id) as taskCount'),
                    DB::raw('(SELECT COUNT(*) FROM projects_task WHERE projects_task.status = 0 AND projects_task.project_id = projects.id) as newCount'),
                    DB::raw('(SELECT COUNT(*) FROM projects_task WHERE projects_task.status = 1 AND projects_task.project_id = projects.id) as onpreparationCount'),
                    DB::raw('(SELECT COUNT(*) FROM projects_task WHERE projects_task.status = 2 AND projects_task.project_id = projects.id) as onprogressCount'),
                    DB::raw('(SELECT COUNT(*) FROM projects_task WHERE projects_task.status = 3 AND projects_task.project_id = projects.id) as reportingCount'),
                    DB::raw('(SELECT COUNT(*) FROM projects_task WHERE projects_task.status = 4 AND projects_task.project_id = projects.id) as finishedCount'),
                ])
                ->groupBy('projects_task.project_id')
                ->where('projects.pm_id',$userId)
                ->where('projects.deleted_at',null)
                ->where('projects_task.deleted_at',null)
                ->limit(20)->get();

                return view('user.project.dashboard', $data);
            }
        ### PM datas end ###

        ### QC Documents datas ###
            if ($userLevel == 4 && $userDepartment == 1) {
                //getting all data
                $data['projects'] = DB::table('projects')
                ->join('projects_task','projects_task.project_id','projects.id')
                ->select([
                    'projects.*',
                    DB::raw('(SELECT COUNT(*) FROM projects_task WHERE projects_task.project_id = projects.id) as taskCount'),
                    DB::raw('(SELECT COUNT(*) FROM projects_task WHERE projects_task.status = 0 AND projects_task.project_id = projects.id) as newCount'),
                    DB::raw('(SELECT COUNT(*) FROM projects_task WHERE projects_task.status = 1 AND projects_task.project_id = projects.id) as onpreparationCount'),
                    DB::raw('(SELECT COUNT(*) FROM projects_task WHERE projects_task.status = 2 AND projects_task.project_id = projects.id) as onprogressCount'),
                    DB::raw('(SELECT COUNT(*) FROM projects_task WHERE projects_task.status = 3 AND projects_task.project_id = projects.id) as reportingCount'),
                    DB::raw('(SELECT COUNT(*) FROM projects_task WHERE projects_task.status = 4 AND projects_task.project_id = projects.id) as finishedCount'),
                ])
                ->groupBy('projects_task.project_id')
                ->where('projects.qcd_id',$userId)
                ->where('projects.deleted_at',null)
                ->where('projects_task.deleted_at',null)
                ->limit(20)->get();

                return view('user.project.dashboard', $data);
            }
        ### QC Documents datas end ###

        ### QC Expenses datas start ###
            if ($userLevel == 6 && $userDepartment == 1) {
                //getting all data
                $data['projects'] = DB::table('projects')
                ->join('projects_task','projects_task.project_id','projects.id')
                ->select([
                    'projects.*',
                    DB::raw('(SELECT COUNT(*) FROM projects_task WHERE projects_task.project_id = projects.id) as taskCount'),
                    DB::raw('(SELECT COUNT(*) FROM projects_task WHERE projects_task.status = 0 AND projects_task.project_id = projects.id) as newCount'),
                    DB::raw('(SELECT COUNT(*) FROM projects_task WHERE projects_task.status = 1 AND projects_task.project_id = projects.id) as onpreparationCount'),
                    DB::raw('(SELECT COUNT(*) FROM projects_task WHERE projects_task.status = 2 AND projects_task.project_id = projects.id) as onprogressCount'),
                    DB::raw('(SELECT COUNT(*) FROM projects_task WHERE projects_task.status = 3 AND projects_task.project_id = projects.id) as reportingCount'),
                    DB::raw('(SELECT COUNT(*) FROM projects_task WHERE projects_task.status = 4 AND projects_task.project_id = projects.id) as finishedCount'),
                ])
                ->groupBy('projects_task.project_id')
                ->where('projects.qce_id',$userId)
                ->where('projects.deleted_at',null)
                ->where('projects_task.deleted_at',null)
                ->limit(20)->get();

                return view('user.project.dashboard', $data);
            }
        ### QC Expenses datas end ###
        
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Dashboard Project.');
    }
}
