<?php

namespace App\Http\Controllers\Api\Tech;

use App\Http\Controllers\Api\Controller;
use Illuminate\Http\Request;
use DB;

class ProjectsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:tech-api', ['except' => ['login']]);
    }
    
    public function latestProject()
    {
        $user = $this->authUser();

        if (!isset($user->id)) {
            return response()->json(['error' => 'Maaf, data tidak tersedia atau Anda tidak diijinkan mengakses halaman ini.']);
        }
        
        $data['tech'] = DB::table('techs')->select(['id','firstname','lastname','image'])->where('id',$user->id)->first();

        $data['project'] = DB::table('projects_task as pt')
        ->select([
            'id',
            'project_id',
            'name',
            'date_start',
            //project status name
                DB::raw('(SELECT name FROM projects_task_status WHERE projects_task_status.id = pt.status) as status_name'),
        ])
        ->where('tech_id',$user->id)
        ->orderBy('date_start','DESC')
        ->limit(5)
        ->get();

        return response()->json($data);
    }
    
    public function all(Request $request)
    {
        $user = $this->authUser();


        $techId = $user->id;
        $projectId = $request->project_id;

        if (!isset($user->id)) {
            return response()->json(['error' => 'Maaf, data tidak tersedia atau Anda tidak diijinkan mengakses halaman ini.']);
        }
        $data['tech'] = DB::table('techs')->select(['id','firstname','lastname'])->where('id',$user->id)->first();

        $data['project'] = DB::table('projects_task as pt')
        ->select([
            'id',
            'project_id',
            DB::raw('(SELECT name FROM projects WHERE projects.id = pt.project_id) as project_name'),
            'name',
            //'tech_id'
            //project name
            DB::raw('(SELECT firstname FROM users WHERE users.id = pt.pm_id) as pm_firstname'),
            DB::raw('(SELECT lastname FROM users WHERE users.id = pt.pm_id) as pm_lastname'),
            DB::raw('(SELECT firstname FROM users WHERE users.id = pt.pc_id) as pc_firstname'),
            DB::raw('(SELECT lastname FROM users WHERE users.id = pt.pc_id) as pc_lastname'),
            DB::raw('(SELECT firstname FROM users WHERE users.id = pt.qct_id) as qct_firstname'),
            DB::raw('(SELECT lastname FROM users WHERE users.id = pt.qct_id) as qct_lastname'),
            DB::raw('(SELECT firstname FROM users WHERE users.id = pt.qcd_id) as qcd_firstname'),
            DB::raw('(SELECT lastname FROM users WHERE users.id = pt.qcd_id) as qcd_lastname'),
            DB::raw('(SELECT firstname FROM users WHERE users.id = pt.qce_id) as qce_firstname'),
            DB::raw('(SELECT lastname FROM users WHERE users.id = pt.qce_id) as qce_lastname'),
        ])
        ->where('tech_id',$user->id)
        ->get();

        return response()->json($data);
    }

    public function show($id)
    {
        $user = $this->authUser();

        if (!isset($user->id)) {
            return response()->json(['error' => 'Maaf, data tidak tersedia atau Anda tidak diijinkan mengakses halaman ini.']);
        }
        
        $data['project'] = DB::table('projects_task as pt')
        ->select([
            'id',
            'project_id',
            DB::raw('(SELECT name FROM projects WHERE projects.id = pt.project_id) as project_name'),
            'name',
            'number',
            'date_start',
            'date_end',
            'budget',
            //'tech_id'
            //project name
            DB::raw('(SELECT firstname FROM users WHERE users.id = pt.pc_id) as pc_firstname'),
            DB::raw('(SELECT lastname FROM users WHERE users.id = pt.pc_id) as pc_lastname'),
            DB::raw('(SELECT firstname FROM users WHERE users.id = pt.qct_id) as qct_firstname'),
            DB::raw('(SELECT lastname FROM users WHERE users.id = pt.qct_id) as qct_lastname'),
            DB::raw('(SELECT firstname FROM users WHERE users.id = pt.qcd_id) as qcd_firstname'),
            DB::raw('(SELECT lastname FROM users WHERE users.id = pt.qcd_id) as qcd_lastname'),
        ])
        ->where('tech_id',$user->id)
        ->where('id',$id)
        ->first();

        if (!isset($data)) {
            return response()->json(['error' => 'Maaf, data tidak tersedia atau Anda tidak diijinkan mengakses halaman ini.']);
        }

        return response()->json($data);
    }
}
