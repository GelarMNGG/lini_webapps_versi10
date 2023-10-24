<?php

namespace App\Http\Controllers\Tech;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Auth;
use DB;

class TechProjectToolController extends Controller
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
        $userDepartment = 1; //project department
        #$projectId = $request->project_id;
        $taskId = $request->task_id;

        //check priviledge
        $dataCountCheck = DB::table('projects_task')->where('id',$taskId)->where('tech_id',$userId)->where('deleted_at',null)->count();

        $data['dataFinishedCount'] = DB::table('project_tools')->where('task_id',$taskId)->where('publisher_id',$userId)->where('status',3)->where('report_submitted',null)->where('deleted_at',null)->count();
        $data['dataReportCount'] = DB::table('project_tools_report')->where('task_id',$taskId)->where('publisher_id',$userId)->count();
        
        if ($dataCountCheck > 0) {
            //getting data
            $data['dataTools'] = DB::table('project_tools as pt')
            ->select([
                'pt.*',
                DB::raw('(SELECT name FROM project_tools_report_status WHERE project_tools_report_status.id = pt.status) as status_name')
            ])
            ->where('task_id',$taskId)
            ->where('publisher_id',$userId)->paginate(10);

            $data['projectTask'] = DB::table('projects_task as pt')
            ->select([
                'pt.*',
                DB::raw('(SELECT name FROM projects WHERE projects.id = pt.project_id) as project_name')
            ])
            ->where('id',$taskId)->where('tech_id',$userId)->where('deleted_at',null)->first();

            return view('tech.tools.index', $data);
        }

        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Tools.');
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

        #$projectId = $request->project_id;
        $taskId = $request->task_id;
        $toolStatus = 1; //active tool

        //check priviledge
        $dataCheck = DB::table('projects_task')->where('id',$taskId)->where('tech_id',$userId)->where('deleted_at',null)->count();

        if ($dataCheck > 0) {

            //getting data
            $data['projectTask'] = DB::table('projects_task as pt')
            ->select([
                'pt.*',
                DB::raw('(SELECT name FROM projects WHERE projects.id = pt.project_id) as project_name')
            ])
            ->where('id',$taskId)->where('tech_id',$userId)->where('deleted_at',null)->first();

            if (!isset($data['projectTask'])) {
                return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Tools.');
            }

            $data['toolCodes'] = DB::table('project_tools_code')->where('status',$toolStatus)->get();
            
            return view('tech.tools.create',$data);
        }

        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Tools.');
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
        $projectStatus = $request->status;
        #$projectId = $request->project_id;
        $taskId = $request->task_id;

        $request->validate([
            'name' => 'required',
        ]);

        //getting data
        $data = $request->except(['_token','submit','status']);
        $data['publisher_id'] = $userId;
        $data['publisher_type'] = $userType;

        //check priviledge
        $dataCountCheck = DB::table('projects_task')->where('id',$taskId)->where('tech_id',$userId)->where('deleted_at',null)->count();

        if ($dataCountCheck > 0) {
            //insert data
            DB::table('project_tools')->insert($data);

            return redirect()->route('project-tool-tech.index','task_id='.$taskId)->with('alert-success', 'Data berhasil disimpan');
        }

        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Tools.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Tools.');
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
        $toolStatus = 1;

        //check priviledge
        $dataCountCheck = DB::table('project_tools')->where('status',$toolStatus)->where('publisher_id',$userId)->count();

        if ($dataCountCheck > 0) {

            $data['dataTool'] = DB::table('project_tools')->where('id',$id)->first();
            $taskId = $data['dataTool']->task_id;

            //getting data
            $data['projectTask'] = DB::table('projects_task as pt')
            ->select([
                'pt.*',
                DB::raw('(SELECT name FROM projects WHERE projects.id = pt.project_id) as project_name')
            ])
            ->where('id',$taskId)->where('tech_id',$userId)->first();

            return view('tech.tools.edit', $data);
        }

        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Tools.');
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
        #$projectId = $request->project_id;
        $taskId = $request->task_id;
        $toolStatus = 1;

        //check priviledge
        $dataCountCheck = DB::table('project_tools')->where('id',$id)->where('status',$toolStatus)->where('publisher_id',$userId)->count();

        if ($dataCountCheck > 0) {
            //update status
            if (isset($request->status)) {
                $data = $request->except(['_token','_method','submit']);

                $data['status'] = $request->status;
                $data['request_submitted'] = Carbon::now()->format('Y-m-d H:i:s');

                DB::table('project_tools')->where('id',$id)->update($data);

                return redirect()->route('project-tool-tech.index','task_id='.$taskId)->with('alert-success', 'Data berhasil disimpan');
            }

            $request->validate([
                'name' => 'required',
                'code' => 'required',
            ]);
    
            //getting data
            $data = $request->except(['_token','_method','submit']);
            $data['publisher_id'] = $userId;
            $data['publisher_type'] = $userType;

            //update data
            DB::table('project_tools')->where('id',$id)->update($data);

            return redirect()->route('project-tool-tech.index','task_id='.$taskId)->with('alert-success', 'Data berhasil disimpan');
        }

        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Tools.');
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
        $toolStatus = 1;

        //check priviledge
        $dataCountCheck = DB::table('project_tools')->where('status',$toolStatus)->where('publisher_id',$userId)->count();

        if ($dataCountCheck > 0) {
            DB::table('project_tools')->delete($id);
        }

        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Tools.');
    }

    //customize
    public function report(Request $request)
    {
        $userId = Auth::user()->id;
        $userType = Auth::user()->user_type;
        $projectId = $request->project_id;
        $taskId = $request->task_id;
        $approverDepartment = 4; //general affair
        $toolStatus = 3;

        //check priviledge
        $dataCountCheck = DB::table('project_tools')->where('status',$toolStatus)->where('publisher_id',$userId)->count();
        
        if ($dataCountCheck > 0) {
            //getting the data
            $data['projectTask'] = DB::table('projects_task as pt')
            ->select([
                'pt.*',
                DB::raw('(SELECT name FROM projects WHERE projects.id = pt.project_id) as project_name')
            ])
            ->where('id',$taskId)->where('tech_id',$userId)->where('deleted_at',null)->first();
            
            $data['dataReportTools'] = DB::table('project_tools')
            ->where('project_id',$projectId)
            ->where('task_id',$taskId)
            ->where('status',$toolStatus)->where('publisher_id',$userId)->get();

            $data['userProfile'] = DB::table('techs')->where('id',$userId)->first();
            $data['approverProfile'] = DB::table('admins')->where('department_id',$approverDepartment)->first();
            $data['dataReportCount'] = DB::table('project_tools_report')->where('task_id',$taskId)->where('publisher_id',$userId)->count();

            return view('tech.tools.report', $data);
        }

        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Tools.');
    }

}
