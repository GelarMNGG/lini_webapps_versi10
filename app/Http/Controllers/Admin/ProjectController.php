<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Auth;
use DB;

class ProjectController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $userRole = Auth::user()->role;
        $userDepartment = Auth::user()->department_id;

        if($userDepartment == 1 || $userRole == 1){
            
            //project data
            $data['projects'] = DB::table('projects')
            ->leftjoin('projects_task','projects_task.project_id','projects.id')
            ->select([
                'projects.*',
                DB::raw('(SELECT COUNT(*) FROM projects_task WHERE projects_task.project_id = projects.id AND projects_task.pm_id = projects.pm_id AND projects_task.deleted_at IS NULL) as taskCount'),
            ])
            ->where('projects.deleted_at',null)
            #->where('projects_task.deleted_at',null)
            ->groupBy('projects.id')
            ->paginate(10);

            //project count
            $data['projectsCount'] = DB::table('projects')->where('deleted_at',null)->count();

            //task Data
            $data['taskDatas'] = DB::table('projects_task')->where('deleted_at',null)->get();

            $data['users'] = DB::table('users')->where('department_id',$userDepartment)->get();

            #dd($data);
    
            return view('admin.project.index', $data);
        }

        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Project.');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $userId = Auth::user()->id;
        $userRole = Auth::user()->role;
        $userDepartment = Auth::user()->department_id;
        $userLevel = 3; //project manager

        if($userDepartment == 1 || $userRole == 1){
            $data['projectNumber'] = DB::table('projects')->latest('id')->first();
            $data['dataUsers'] = DB::table('users')->where('user_level',$userLevel)->get();
            $data['dataCategories'] = DB::table('projects_category')->get();
            $data['dataCustomers'] = DB::table('customers')->get();
    
            return view('admin.project.create', $data);
        }

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
        $userId = Auth::user()->id;
        $userRole = Auth::user()->role;
        $userDepartment = Auth::user()->department_id;

        if($userDepartment == 1 || $userRole == 1){
            $request->validate([
                'name' => 'required|unique:projects,name,'.$request->name,
            ]);
    
            //custom setting to support file upload
            $data = $request->except(['_token','_method','submit']);
    
            DB::table('projects')->insert($data);
    
            return redirect()->route('admin-projects.index')->with('alert-success','Data berhasil disimpan.');
        }
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
        $userRole = Auth::user()->role;
        $userDepartment = Auth::user()->department_id;

        //first check
        $dataProjectCount = DB::table('projects')->where('id',$id)->count();

        if ($dataProjectCount > 0 && $userDepartment == 1 || $userRole == 1) {
            $data['userDepartment'] = $userDepartment;

            $data['project'] = DB::table('projects as proj')
            ->select([
                'proj.*',
                DB::raw('(SELECT name FROM projects_category WHERE projects_category.id = proj.procat_id) as procat_name')
            ])
            ->where('id',$id)->first();

            $data['projectStatus'] = DB::table('projects_status')->get();
            $data['dataTaskStatus'] = DB::table('projects_task_status')->get();
            $data['dataUsers'] = DB::table('users')->get();
            $data['dataTechs'] = DB::table('techs')->get();
            $data['dataCustomers'] = DB::table('customers')->get();

            //task data
            $data['projectTaskDatas'] = DB::table('projects_task as pt')
            ->select([
                'pt.*',
                DB::raw('(SELECT id FROM project_report_template_selected WHERE project_report_template_selected.task_id = pt.id AND project_report_template_selected.task_id IS NULL AND project_report_template_selected.deleted_at IS NULL) as template_id')
            ])
            ->where('project_id',$id)->where('deleted_at',null)->get();

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
    
            return view('admin.project.show', $data);
        }
        
        return redirect()->back()->with('alert-danger','Halaman yang akan Anda tuju tidak tersedia.');
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
        $userRole = Auth::user()->role;
        $userDepartment = Auth::user()->department_id;
        $userLevel = 3; //project manager

        if ($userDepartment == 1 || $userDepartment == 9 || $userRole == 1) {
            $data['userDepartment'] = $userDepartment;
            $data['project'] = DB::table('projects')->where('id',$id)->first();
            $data['projectStatus'] = DB::table('projects_status')->get();
            $data['dataUsers'] = DB::table('users')->where('user_level',$userLevel)->get();
            $data['dataTechs'] = DB::table('techs')->get();
            $data['dataCustomers'] = DB::table('customers')->get();
            $data['dataCategories'] = DB::table('projects_category')->get();
    
            return view('admin.project.edit', $data);
        }

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
        $userId = Auth::user()->id;
        $userRole = Auth::user()->role;
        $userDepartment = Auth::user()->department_id;
        $projectStatus = $request->status;
        $pmId = $request->pm_id;

        if($userDepartment == 1 || $userRole == 1){
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
    
            DB::table('projects')->where('id',$id)->update($data);
    
            return redirect()->route('admin-projects.index','status='.$projectStatus)->with('alert-success','Data berhasil diubah.');
        }

        if ($userDepartment == 9) {
            $request->validate([
                'name' => 'required|unique:projects_task,name,'.$id,
                'tech_id' => 'required',
            ]);
    
            //custom setting to support file upload
            $data = $request->except(['_token','_method','submit']);
    
            DB::table('projects')->where('id',$id)->update($data);
    
            return redirect()->route('admin-pr.index')->with('alert-success','Teknisi berhasil ditambahkan.');
        }

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
        $userRole = Auth::user()->role;
        $userDepartment = Auth::user()->department_id;

        if($userDepartment == 1 || $userRole == 1){
            $data['deleted_at'] = Carbon::now()->format('Y-m-d H:i:s');
    
            DB::table('projects')->where('id', $id)->update($data);
    
            return redirect()->route('admin-projects.index')->with('alert-success','Data berhasil dihapus.');
        }

        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Project.');
    }

    //customization
    public function progress($id)
    {
        $data['project'] = DB::table('projects')
            ->join('projects_task','projects_task.project_id','projects.id')
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

        return view('admin.project.progress', $data);
    }

    public function dashboard()
    {
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
        ->where('projects.deleted_at',null)
        ->where('projects_task.deleted_at',null)
        ->limit(20)->get();

        return view('admin.project.dashboard', $data);
    }
}
