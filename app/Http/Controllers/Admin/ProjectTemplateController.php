<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use DB;

class ProjectTemplateController extends Controller
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
        $userId = Auth::user()->id;
        $userRole = Auth::user()->role;
        $userType = Auth::user()->user_type;
        $userLevel = Auth::user()->user_level;
        $userDepartment = Auth::user()->department_id;
        $projectId  = $request->project_id;
        $taskId  = $request->task_id;
        $catStatus = 1; //active category
        $subcatStatus = 1; //active subcategory

        if($userDepartment == 1 || $userRole == 1){

            //get data
            $data['project'] = DB::table('projects as proj')
            ->select([
                'proj.*',
                DB::raw('(SELECT name FROM projects_category WHERE projects_category.id = proj.procat_id) as procat_name')
            ])
            ->where('id',$projectId)->first();

            $data['infoTaskProject'] = DB::table('projects_task')
            ->select([
                'projects_task.*',
                DB::raw('(SELECT id FROM projects WHERE projects.id = projects_task.project_id) as project_id'),
                DB::raw('(SELECT name FROM projects WHERE projects.id = projects_task.project_id) as project_name')
            ])
            ->where('id',$taskId)->first();

            //task data
            $data['projectTaskDatas'] = DB::table('projects_task as pt')
            ->select([
                'pt.*',
                DB::raw('(SELECT id FROM project_report_template_selected WHERE project_report_template_selected.task_id = pt.id) as template_id')
            ])
            ->where('project_id',$projectId)->where('deleted_at',null)->get();

            //template data
            $data['projectTemplateDatas'] = DB::table('project_report_template_selected as prts')
            ->select([
                'prts.*', 
                DB::raw('(SELECT name FROM projects_task WHERE projects_task.id = prts.task_id) as task_name'),

                DB::raw('(SELECT COUNT(project_report_images.image) FROM project_report_images WHERE project_report_images.task_id = prts.task_id AND project_report_images.template_id = prts.template_id AND project_report_images.image IS NOT NULL) as imageCount'),

                DB::raw('(SELECT COUNT(project_report_images.approved_at) FROM project_report_images WHERE project_report_images.task_id = prts.task_id AND project_report_images.template_id = prts.template_id AND project_report_images.approved_at IS NOT NULL) as approvedImageCount'),
            ])
            ->where('project_id',$projectId)->where('task_id',$taskId)->where('deleted_at',null)->get();

            $data['projectTemplateCount'] = DB::table('project_report_category')->where('deleted_at',null)->count();
            $data['allProjectTemplates'] = DB::table('project_report_category')
            ->select([
                'project_report_category.*',
                DB::raw('(SELECT COUNT(*) FROM project_report_subcategory WHERE project_report_subcategory.cat_id = project_report_category.id) as subcatCount')
            ])
            ->where('deleted_at',null)
            ->paginate(10);

            //additional info
            $data['draftCount'] = DB::table('project_report_template')->where('task_id',$taskId)->where('deleted_at',null)->count();
            $data['dataUsers'] = DB::table('users')->get();

            return view('admin.project.template.index', $data);
        }

        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Project Template.');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Project Template.');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Project Template.');
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
        $userDepartment = Auth::user()->department_id;

        $projectId = $request->project_id;
        $taskId = $request->task_id;
        $catStatus = 1; //active category
        $subcatStatus = 1; //active subcategory

        if ($userDepartment == 1) {

            //project_id data
            $data['projectId'] = $projectId;
            $data['taskId'] = $taskId;

            //second check
            $dataCheck = DB::table('project_report_category')->where('id',$id)->where('deleted_at',null)->count();
            if ($dataCheck < 1) {
                return redirect()->back()->with('alert-danger','Halaman yang Anda tuju tidak tersedia.');
            }
            
            $dataCheck2 = DB::table('project_report_template_selected')->where('template_id',$id)->where('deleted_at',null)->count();
            
            if (isset($projectId) && $dataCheck2 > 0) {
                $data['projectTemplate'] = DB::table('project_report_template_selected as prts')
                ->select([
                    'prts.*',
                    DB::raw('(SELECT name FROM project_report_category WHERE project_report_category.id = prts.template_id) as name'),
                    DB::raw('(SELECT name FROM projects WHERE projects.id = prts.project_id) as project_name'),
                    DB::raw('(SELECT name FROM projects_task WHERE projects_task.id = prts.task_id) as task_name'),
                    DB::raw('(SELECT status FROM projects WHERE projects.id = prts.project_id) as project_status')
                ])
                ->where('template_id',$id)->where('deleted_at',null)->first();
    
                //data
                $data['subcats'] = unserialize($data['projectTemplate']->subcat_id);
            }else{
                $data['projectTemplate'] = DB::table('project_report_category')->where('id',$id)->where('deleted_at',null)->first();
            }

            //data
            $data['dataCategory'] = DB::table('project_report_category')->where('id',$id)->where('deleted_at',null)->first();
            $data['dataSubcategory'] = DB::table('project_report_subcategory')->where('status',$subcatStatus)->where('cat_id',$id)->where('deleted_at',null)->get();

            //task data
            /*
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
            */

            #$data['taskCount'] = $request->task_count;
            #$data['templateCount'] = DB::table('project_report_template_selected')->where('template_id',$id)->where('task_id',$taskId)->count();

            //if there is no projectId nor taskId
            if ($projectId == null && $taskId == null) {
                return view('admin.project.template.show-view', $data);
            }

            return view('admin.project.template.show', $data);
        }

        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Project Template.');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Project Template.');
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
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Project Template.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Project Template.');
    }
}
