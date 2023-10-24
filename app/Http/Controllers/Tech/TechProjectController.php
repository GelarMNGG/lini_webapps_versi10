<?php

namespace App\Http\Controllers\Tech;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use DB;

class TechProjectController extends Controller
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
        $userType = Auth::user()->user_type;
        $userDepartment = 1; //project department

        $data['dataTask'] = DB::table('projects_task')
        ->select([
            'projects_task.*',
            DB::raw('(SELECT name FROM projects WHERE projects.id = projects_task.project_id) as project_name'),

        ])
        ->where('tech_id',$userId)->where('deleted_at',null)->paginate(10);

        $data['users'] = DB::table('users')->where('department_id',$userDepartment)->get();
        
        //get task
        $data['taskCount'] = DB::table('projects_task')->where('tech_id',$userId)->where('deleted_at',null)->count();

        return view('tech.project.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Project.');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Project.');
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
        $userDepartment = 1; //Project

        $pcLevel = 2; //PC
        $qcdLevel = 4; //QC Document
        $qctLevel = 5; //QC Tools

        $prStatus = 3; //approved pr

        //check priviledge
        $firstCheck = DB::table('projects_task')->where('id',$id)->where('tech_id',$userId)->where('deleted_at',null)->count();

        if($firstCheck > 0){
            
            $data['projectTask'] = DB::table('projects_task')
            ->select([
                'projects_task.*',
                DB::raw('(SELECT name FROM projects WHERE projects.id = projects_task.project_id) as project_name'),
                //task value
                DB::raw('(SELECT budget FROM project_purchase_requisition WHERE project_purchase_requisition.task_id = projects_task.id AND  project_purchase_requisition.status = '.$prStatus.' AND project_purchase_requisition.budget IS NOT NULL) as task_budget'),

            ])
            ->where('id',$id)
            ->where('tech_id',$userId)
            ->where('deleted_at',null)->first();

            //taskcheck
            if (!isset($data['projectTask'])) {
                return redirect()->back()->with('alert-danger','Halaman yang Anda tuju tidak tersedia.');
            }

            $projectId = $data['projectTask']->project_id;
            $taskId = $data['projectTask']->id;

            //data user
            $data['dataPCs'] = DB::table('users')->where('department_id', $userDepartment)->where('user_level',$pcLevel)->get();
            $data['dataQCDs'] = DB::table('users')->where('department_id', $userDepartment)->where('user_level',$qcdLevel)->get();
            $data['dataQCTs'] = DB::table('users')->where('department_id', $userDepartment)->where('user_level',$qctLevel)->get();
    
            //template data
            $data['projectTemplateDatas'] = DB::table('project_report_template as prt')
            ->select([
                'prt.*',
                DB::raw('(SELECT name FROM projects_task WHERE projects_task.id = prt.task_id) as task_name')
            ])
            ->where('project_id',$id)->where('deleted_at',null)->get();
            $data['projectTemplateCount'] = DB::table('project_report_template')->where('project_id',$id)->where('deleted_at',null)->count();
            $data['allProjectTemplates'] = DB::table('project_report_template')->where('deleted_at',null)->get();

            return view('tech.project.show', $data);
        }

        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Project.');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Project.');
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
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Project.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Project.');
    }

    //customization
    public function progress($id)
    {
        $userId = Auth::user()->id;
        $userType = Auth::user()->user_type;

        $firstCheck = DB::table('projects_task')->where('id',$id)->where('tech_id',$userId)->count();

        if ($firstCheck > 0) {
            $data['projectTask'] = DB::table('projects_task as pt')
            ->select([
                'pt.*',
                DB::raw('(SELECT status FROM projects WHERE projects.id = pt.project_id) as project_status'),
                DB::raw('(SELECT name FROM projects WHERE projects.id = pt.project_id) as project_name'),
                //progress count
                DB::raw('(SELECT COUNT(task_id) FROM project_tools_report WHERE project_tools_report.task_id = pt.id AND project_tools_report.status = 3 AND project_tools_report.task_id IS NOT NULL) as tools_report_count'),
                DB::raw('(SELECT COUNT(task_id) FROM project_expenses_report WHERE project_expenses_report.task_id = pt.id AND project_expenses_report.status = 4 AND project_expenses_report.task_id IS NOT NULL) as expenses_report_count'),
                DB::raw('(SELECT COUNT(task_id) FROM project_report_images WHERE project_report_images.task_id = pt.id AND project_report_images.status = 4 AND project_report_images.task_id IS NOT NULL) as images_report_count'),
            ])
            ->where('id',$id)->first();
    
            //data tool
            $data['dataTool'] = DB::table('project_tools_report')->where('task_id',$id)->orderBy('status','DESC')->first();
            $data['dataExpense'] = DB::table('project_expenses_report')->where('task_id',$id)->orderBy('status','DESC')->first();
            $data['dataReportImage'] = DB::table('project_report_images')->where('task_id',$id)->orderBy('status','DESC')->first();
    
            return view('tech.project.progress', $data);
        }

        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Log Project.');
    }

    public function dashboard()
    {
        $userId = Auth::user()->id;

        //getting all data by user
        $data['projects'] = DB::table('projects as proj')
        /*->select([
            'proj.*',
            DB::raw('(SELECT COUNT(*) WHERE proj.status = 1) as newCount'),
            DB::raw('(SELECT COUNT(*) WHERE proj.status = 2) as onprogressCount'),
            DB::raw('(SELECT COUNT(*) WHERE proj.status = 3) as reportingCount'),
            DB::raw('(SELECT COUNT(*) WHERE proj.status = 4) as finishedCount'),
        ])
        */
        ->where('tech_id',$userId)->limit(20)->get();

        //count the data by status
        $data['newCount'] = DB::table('projects')->where('tech_id',$userId)->where('status',1)->count();
        $data['onprogressCount'] = DB::table('projects')->where('tech_id',$userId)->where('status',2)->count();
        $data['reportingCount'] = DB::table('projects')->where('tech_id',$userId)->where('status',3)->count();
        $data['finishedCount'] = DB::table('projects')->where('tech_id',$userId)->where('status',4)->count();

        return view('tech.project.dashboard', $data);
    }
}
