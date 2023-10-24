<?php

namespace App\Http\Controllers\Api\Tech;

use App\Http\Controllers\Api\Controller;
use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Auth;
use DB;

class ProjectsToolsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:tech-api', ['except' => ['login']]);
    }

    public function all(Request $request)
    {
        $user = $this->authUser();

        if (!isset($user->id)) {
            return response()->json(['error' => 'Maaf, data tidak tersedia atau Anda tidak diijinkan mengakses halaman ini.']);
        }

        if (!isset($request->project_id)) {
            return response()->json(['error' => 'Maaf, project id tidak tersedia.']);
        } 

        if (!isset($request->task_id)) {
            return response()->json(['error' => 'Maaf, task id tidak tersedia.']);
        } 

        $projectId = $request->project_id;
        $taskId = $request->task_id;
        $techId = $user->id;
        $userDepartment = 1; //project department

        //check priviledge
        $dataCountCheck = DB::table('projects_task')->where('id',$taskId)->where('tech_id',$techId)->where('deleted_at',null)->count();

        $data['dataFinishedCount'] = DB::table('project_tools')->where('task_id',$taskId)->where('publisher_id',$techId)->where('status',3)->where('report_submitted',null)->where('deleted_at',null)->count();
        $data['dataReportCount'] = DB::table('project_tools_report')->where('task_id',$taskId)->where('publisher_id',$techId)->count();
        
        if ($dataCountCheck > 0) {
            //getting data
            $data['dataTools'] = DB::table('project_tools as pt')
            ->select([
                'pt.*',
                DB::raw('(SELECT name FROM project_tools_report_status WHERE project_tools_report_status.id = pt.status) as status_name')
            ])
            ->where('task_id',$taskId)->get();

            $data['projectTask'] = DB::table('projects_task as pt')
            ->select([
                'pt.*',
                DB::raw('(SELECT name FROM projects WHERE projects.id = pt.project_id) as project_name')
            ])
            ->where('id',$taskId)->where('tech_id',$techId)->where('deleted_at',null)->first();
        }
        return response()->json($data);
    }
    
}
