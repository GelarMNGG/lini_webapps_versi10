<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Auth;
use DB;

class UserProjectReportController extends Controller
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
    public function index()
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Report Project.');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $userId = Auth::user()->id;
        $userLevel = Auth::user()->user_level;
        $userDepartment = Auth::user()->department_id;
        
        $projectId = $request->project_id;
        $taskId = $request->task_id;
        //check priviledge & getting the data
            $priviledgeCheck = DB::table('projects_task')->where('project_id',$projectId)->where('id',$taskId)->where('qcd_id',$userId)->where('deleted_at',null)->count();
        //first check
            if ($priviledgeCheck < 1) {
                return redirect()->back()->with('alert-danger','Halaman yang Anda tuju tidak tersedia.');
            }
        //second check
            if ($userLevel == 3 || $userLevel == 4 && $userDepartment == 1) {
                //project work order
                    $data['projectTask'] = DB::table('projects_task as pt')
                    ->select([
                        'pt.*',
                        DB::raw('(SELECT id FROM projects WHERE projects.id = pt.project_id) as project_id'),
                        DB::raw('(SELECT name FROM projects WHERE projects.id = pt.project_id) as project_name'),
                    ])
                    ->where('id',$taskId)->first();
                //return view
                return view('user.project.report.create',$data);
            }
        //return view
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Create Report Project.');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Report Project.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request,$id)
    {
        $userId = Auth::user()->id;
        $userLevel = Auth::user()->user_level;
        $userDepartment = Auth::user()->department_id;

        $projectId = $request->project_id;
        $taskId = $request->task_id;

        $catStatus = 1; //active category
        $subcatStatus = 1; //active subcategory
        $commentStatus = 1; //accessable for PM PC AD level user
        $externalCommentStatus = 0; //accesable for both external and internal team
        //check priviledge & getting the data
            $priviledgeCheck = DB::table('projects_task as taskTableCheck')
            ->select([
                'taskTableCheck.*',
                DB::raw('(SELECT COUNT(project_report_images.submitted_at) FROM project_report_images WHERE project_report_images.task_id = taskTableCheck.id AND project_report_images.template_id = '.$id.' AND project_report_images.submitted_at IS NOT NULL) as submittedCount'),
            ])
            ->where('id',$taskId)
            ->where('project_id',$projectId)
            ->where('qcd_id',$userId)
            ->first();
        //first check
            /*
            if (!isset($priviledgeCheck) || $priviledgeCheck->submittedCount < 1) {

                return redirect()->back()->with('alert-danger','Halaman yang Anda tuju tidak tersedia.');
            }
            */
        //user level 4 is Document admin, user level 3 is Project Manager
        if ($userLevel == 4 || $userLevel == 3 && $userDepartment == 1) {
            //check priviledge & getting the data
                $data['projectTask'] = DB::table('projects_task')->where('id',$taskId)->first();
            //data template
                $data['projectTemplate'] = DB::table('project_report_template as prt')
                ->select([
                    'prt.*',
                    DB::raw('(SELECT id FROM projects WHERE projects.id = prt.project_id) as project_id'),
                    DB::raw('(SELECT procat_id FROM projects WHERE projects.id = prt.project_id) as procat_id'),
                    DB::raw('(SELECT name FROM projects WHERE projects.id = prt.project_id) as project_name')
                ])
                ->where('task_id',$taskId)->first();
            //////////////// project report start ///////////////
                $data['dataProjectReportCount'] = DB::table('projects_report')->where('task_id',$taskId)->count();
                //site information
                    $data['dataSites'] = DB::table('site_information')->where('task_id',$taskId)->get();
                //data technicians - installation personnel list
                    $data['dataTechnicians'] = DB::table('projects_task as pt')
                    ->select([
                        'pt.*',
                        DB::raw('(SELECT title FROM techs WHERE techs.id = pt.tech_id) as tech_title'),
                        DB::raw('(SELECT firstname FROM techs WHERE techs.id = pt.tech_id) as tech_firstname'),
                        DB::raw('(SELECT lastname FROM techs WHERE techs.id = pt.tech_id) as tech_lastname'),
                        DB::raw('(SELECT mobile FROM techs WHERE techs.id = pt.tech_id) as tech_mobile'),
                        DB::raw('(SELECT company FROM techs WHERE techs.id = pt.tech_id) as tech_company'),
                    ])
                    ->where('id',$taskId)
                    ->get();
            //////////////// project report end ///////////////
            //////////////// project image start ///////////////
                //project
                    $data['project'] = DB::table('projects as proj')
                    ->select([
                        'proj.*',
                        //partner & customer company
                            DB::raw('(SELECT name FROM clients WHERE clients.id = proj.customer_id) as customer_name'),
                            DB::raw('(SELECT logo FROM clients WHERE clients.id = proj.customer_id) as customer_logo'),
                            DB::raw('(SELECT name FROM clients WHERE clients.id = proj.partner_id) as partner_name'),
                            DB::raw('(SELECT logo FROM clients WHERE clients.id = proj.partner_id) as partner_logo'),
                        //partner & customer contact person
                            DB::raw('(SELECT firstname FROM customers WHERE customers.id = proj.customer_id) as customer_pic_firstname'),
                            DB::raw('(SELECT lastname FROM customers WHERE customers.id = proj.customer_id) as customer_pic_lastname'),
                            DB::raw('(SELECT firstname FROM customers WHERE customers.id = proj.partner_id) as partner_pic_firstname'),
                            DB::raw('(SELECT lastname FROM customers WHERE customers.id = proj.partner_id) as partner_pic_lastname'),
                        //pwo
                            DB::raw('(SELECT id FROM projects_task WHERE projects_task.project_id = proj.id) as task_id'),
                            DB::raw('(SELECT number FROM projects_task WHERE projects_task.project_id = proj.id) as pwo_number'),
                    ])
                    ->where('id',$data['projectTemplate']->project_id)->where('tech_id',$data['projectTask']->id)->first();
                //project category used for image folder placement
                    $data['dataProjectCategory'] = DB::table('projects_category')->where('id',$data['projectTemplate']->procat_id)->first();
                //project pictures
                    $data['dataProjectPictures'] = DB::table('project_report_images')->where('task_id',$taskId)->get();
                    $data['dataProjectPicturesStatus'] = DB::table('project_report_images as pri')
                    ->select([
                        'pri.*',
                        DB::raw('COUNT(pri.approved_at) as countApproved'),
                        DB::raw('COUNT(pri.approved_by_pm_at) as countPMApproved'),
                        //comments
                            DB::raw('(SELECT COUNT(*) FROM project_report_images_comments WHERE project_report_images_comments.pri_id = pri.id) as commentsCount')
                    ])
                    ->where('task_id',$taskId)
                    ->first();
                //data
                    $data['cats'] = unserialize($data['projectTemplate']->cat_id);
                    $data['subcats'] = unserialize($data['projectTemplate']->subcat_id);
                    $data['dataCategory'] = DB::table('project_report_category')->where('status',$catStatus)->where('deleted_at',null)->get();
                    $data['dataSubcategory'] = DB::table('project_report_subcategory')->where('status',$subcatStatus)->where('deleted_at',null)->get();
                //supporting data
                    $data['subcatsPictureByCatCount'] = DB::table('project_report_images')
                        ->select('cat_id', DB::raw('count(*) as total'))
                        ->groupBy('cat_id')
                        ->get();
            //////////////// Project image end ///////////////
            //////////////// Approver start ///////////////
                //data admin doc
                    $data['dataApprover'] = DB::table('project_report_images as pri')
                    ->select([
                        'pri.*',
                        //admin doc
                            DB::raw('(SELECT user_level FROM users WHERE users.id = pri.approver_id) as user_level'),
                            DB::raw('(SELECT name FROM users_level WHERE users_level.id = user_level) as title'),
                            DB::raw('(SELECT firstname FROM users WHERE users.id = pri.approver_id) as firstname'),
                            DB::raw('(SELECT lastname FROM users WHERE users.id = pri.approver_id) as lastname'),
                    ])
                    ->where('task_id',$taskId)
                    ->where('approved_at','!=', null)
                    ->first();
                //data pm
                    $data['dataProjectManager'] = DB::table('project_report_images as pri')
                    ->select([
                        'pri.*',
                        //project manager
                            DB::raw('(SELECT user_level FROM users WHERE users.id = '.$priviledgeCheck->pm_id.') as user_level'),
                            DB::raw('(SELECT name FROM users_level WHERE users_level.id = user_level) as title'),
                            DB::raw('(SELECT firstname FROM users WHERE users.id = '.$priviledgeCheck->pm_id.') as firstname'),
                            DB::raw('(SELECT lastname FROM users WHERE users.id = '.$priviledgeCheck->pm_id.') as lastname'),
                    ])
                    ->where('task_id',$taskId)
                    ->where('approved_by_pm_at','!=', null)
                    ->first();
            //////////////// Approver end ///////////////
            //////////////// Report comments start ///////////////
                //internal comunications
                    $data['dataComments'] = DB::table('project_report_images_comments')->where('status',$commentStatus)->where('task_id',$taskId)->orderBy('date', 'DESC')->get();
                //external communications
                    $data['dataExternalComments'] = DB::table('projects_report_comments')->where('status',$externalCommentStatus)->where('task_id',$taskId)->orderBy('date', 'DESC')->get();
                    $data['dataProjectReportCommentsCount'] = DB::table('projects_report_comments')->where('task_id',$taskId)->count();
                //commentators
                    $data['users'] = DB::table('users')
                    ->select([
                        'users.*',
                        DB::raw('(SELECT name FROM users_level WHERE users_level.id = users.user_level) as title')
                    ])
                    ->get();
                    $data['customers'] = DB::table('customers')->get();
            //////////////// Report comments end ///////////////
            return view('user.project.report.report-images-selected',$data);
        }

        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Report Project.');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Report Project.');
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
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Report Project.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Report Project.');
    }
}
