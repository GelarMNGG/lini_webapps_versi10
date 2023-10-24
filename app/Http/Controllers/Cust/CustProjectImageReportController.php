<?php

namespace App\Http\Controllers\Cust;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use DB;

class CustProjectImageReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:cust');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Project Report.');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Project Report.');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Project Report.');
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
        $userLevel = Auth::user()->user_level;

        $userDepartment = 1;

        $projectId = $request->pid;
        $taskId = $id;

        $sharedStatus = 1; //shared to customer
        $catStatus = 1; //active category
        $subcatStatus = 1; //active subcategory
        $commentStatus = 1; //accessable for PM PC AD level user
        $externalCommentStatus = 0; //accesable for both external and internal team
        
        //check priviledge & getting the data
        $infoProjectTask = DB::table('projects_task as taskTableCheck')
        ->select([
            'taskTableCheck.*',
            #DB::raw('(SELECT COUNT(project_report_images.submitted_at) FROM project_report_images WHERE project_report_images.task_id = taskTableCheck.id AND project_report_images.template_id = '.$id.' AND project_report_images.submitted_at IS NOT NULL) as submittedCount'),
            //shared count
            #DB::raw('(SELECT COUNT(project_report_images.submitted_at) FROM project_report_images WHERE project_report_images.task_id = taskTableCheck.id AND project_report_images.template_id = '.$id.' AND project_report_images.submitted_at IS NOT NULL) as submittedCount'),
        ])
        ->where('id',$taskId)
        ->where('project_id',$projectId)
        ->first();

        $data['infoProjectTask'] = $infoProjectTask;

        //first check
        if (!isset($infoProjectTask)) {

            return redirect()->back()->with('alert-danger','Halaman yang Anda tuju belum tersedia.');
        }

        //user level 4 is Document admin, user level 3 is Project Manager
        if ($userDepartment == 1) {
            
            ### template data ###
                $data['projectTemplate'] = DB::table('project_report_template_selected as prt')
                ->select([
                    'prt.*',
                    DB::raw('(SELECT id FROM projects WHERE projects.id = prt.project_id) as project_id'),
                    DB::raw('(SELECT procat_id FROM projects WHERE projects.id = prt.project_id) as procat_id'),
                    DB::raw('(SELECT name FROM projects WHERE projects.id = prt.project_id) as project_name'),
                    //DB::raw('(SELECT name FROM project_report_category WHERE project_report_category.id = '.$templateId.') as category_name'),
                ])
                ->where('project_id',$projectId)
                ->where('task_id',$taskId)
                //->where('template_id',$templateId)
                ->first();

                if ($data['projectTemplate'] == null) {
                    return redirect()->back()->with('alert-danger','Halaman yang Anda tuju tidak tersedia.');
                }

                $data['subcatIds'] = unserialize($data['projectTemplate']->subcat_id);
                $data['subcatcustomIds'] = unserialize($data['projectTemplate']->subcatcustom_id);

            ### template data end ###

            //////////////// project image start ///////////////

                //project
                $data['project'] = DB::table('projects as proj')
                ->select([
                    'proj.*',
                    //task
                    DB::raw('(SELECT name FROM projects_task WHERE projects_task.id = '.$taskId.') as task_name'),
                    //partner & customer company
                    DB::raw('(SELECT name FROM clients WHERE clients.id = proj.customer_id AND proj.customer_id IS NOT NULL) as customer_name'),
                    DB::raw('(SELECT logo FROM clients WHERE clients.id = proj.customer_id AND proj.customer_id IS NOT NULL) as customer_logo'),
                    DB::raw('(SELECT name FROM clients WHERE clients.id = proj.partner_id AND proj.partner_id IS NOT NULL) as partner_name'),
                    DB::raw('(SELECT logo FROM clients WHERE clients.id = proj.partner_id AND proj.partner_id IS NOT NULL) as partner_logo'),
                    //partner & customer contact person
                    DB::raw('(SELECT firstname FROM customers WHERE customers.id = proj.customer_id AND proj.customer_id IS NOT NULL) as customer_pic_firstname'),
                    DB::raw('(SELECT lastname FROM customers WHERE customers.id = proj.customer_id AND proj.customer_id IS NOT NULL) as customer_pic_lastname'),
                    DB::raw('(SELECT firstname FROM customers WHERE customers.id = proj.partner_id AND proj.partner_id IS NOT NULL) as partner_pic_firstname'),
                    DB::raw('(SELECT lastname FROM customers WHERE customers.id = proj.partner_id AND proj.partner_id IS NOT NULL) as partner_pic_lastname'),
                ])
                ->where('id',$projectId)
                ->where('pm_id',$infoProjectTask->pm_id)
                ->where('deleted_at',null)
                ->first();

                //check privilege
                $projectCheck = $data['project'];
                if ($projectCheck == null) {
                    return redirect()->back()->with('alert-danger','Halaman yang Anda tuju tidak tersedia.');
                }

                //project category used for image folder placement
                $data['dataProjectCategory'] = DB::table('projects_category')->where('id',$data['projectTemplate']->procat_id)->first();

                //project pictures - subcat_id
                $data['dataProjectPictures'] = DB::table('project_report_images as pri')
                ->select([
                    'pri.*',
                    DB::raw('(SELECT name FROM project_report_subcategory WHERE project_report_subcategory.id = pri.subcat_id) as subcat_name')
                ])
                ->where('project_id',$projectId)
                ->where('task_id',$taskId)
                ->where('shared',$sharedStatus)
                //->where('template_id',$templateId)
                ->where('subcat_id','!=',null)
                ->where('selected_image',1)
                ->get();

                //project pictures - subcatcustom_id
                $data['dataProjectPicturesCustom'] = DB::table('project_report_images as pri')
                ->select([
                    'pri.*',
                    DB::raw('(SELECT name FROM project_report_subcategory_customized WHERE project_report_subcategory_customized.id = pri.subcatcustom_id) as subcat_name')
                ])
                ->where('project_id',$projectId)
                ->where('task_id',$taskId)
                ->where('shared',$sharedStatus)
                //->where('template_id',$templateId)
                ->where('subcatcustom_id','!=',null)
                ->where('selected_image',1)
                ->get();

                $dataSubCount = count($data['dataProjectPictures']);

                if ($dataSubCount > 0) {
                    $data['subcatName'] = 'subcat_id';
                }else{
                    $data['subcatName'] = 'subcatcustom_id';
                }

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
                    DB::raw('(SELECT user_level FROM users WHERE users.id = '.$infoProjectTask->pm_id.') as user_level'),
                    DB::raw('(SELECT name FROM users_level WHERE users_level.id = user_level) as title'),
                    DB::raw('(SELECT firstname FROM users WHERE users.id = '.$infoProjectTask->pm_id.') as firstname'),
                    DB::raw('(SELECT lastname FROM users WHERE users.id = '.$infoProjectTask->pm_id.') as lastname'),
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
            
            return view('cust.project.report.report-images-selected',$data);

        }

        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Project Report.');//
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
        //id = id.project_id
            $dataId = explode('.',$id);
            $id = $dataId[0];
            $projectId = $dataId[1];

            $userDepartment = 1;
            $taskId = $id;

            $catStatus = 1; //active category
            $subcatStatus = 1; //active subcategory
            $commentStatus = 1; //accessible for Customer PM PC AD level user
        //check priviledge & getting the data
            $infoProjectTask = DB::table('projects_task')->where('id',$taskId)->first();

            if (!isset($infoProjectTask)) {
                return redirect()->back()->with('alert-danger','Halaman yang Anda tuju belum tersedia.');
            }
        //first check
            $firstCheck = DB::table('projects as proj')
            ->select([
                'proj.*',
                //partner & customer company
                    DB::raw('(SELECT name FROM clients WHERE clients.id = proj.customer_id AND proj.customer_id IS NOT NULL) as customer_name'),
                    DB::raw('(SELECT logo FROM clients WHERE clients.id = proj.customer_id AND proj.customer_id IS NOT NULL) as customer_logo'),
                    DB::raw('(SELECT name FROM clients WHERE clients.id = proj.partner_id AND proj.partner_id IS NOT NULL) as partner_name'),
                    DB::raw('(SELECT logo FROM clients WHERE clients.id = proj.partner_id AND proj.partner_id IS NOT NULL) as partner_logo'),
                //partner & customer contact person
                    DB::raw('(SELECT firstname FROM customers WHERE customers.id = proj.customer_id AND proj.customer_id IS NOT NULL) as customer_pic_firstname'),
                    DB::raw('(SELECT lastname FROM customers WHERE customers.id = proj.customer_id AND proj.customer_id IS NOT NULL) as customer_pic_lastname'),
                    DB::raw('(SELECT firstname FROM customers WHERE customers.id = proj.partner_id AND proj.partner_id IS NOT NULL) as partner_pic_firstname'),
                    DB::raw('(SELECT lastname FROM customers WHERE customers.id = proj.partner_id AND proj.partner_id IS NOT NULL) as partner_pic_lastname'),
            ])
            ->where('id',$projectId)->where('customer_id',$userId)->first();

            if (!isset($firstCheck)) {
                return redirect()->back()->with('alert-danger','Halaman yang Anda tuju belum tersedia.');
            }
        //check priviledge & getting the data
            $privilegeCheck = DB::table('projects_task as taskTableCheck')
            ->select([
                'taskTableCheck.*',
                DB::raw('(SELECT COUNT(project_report_images.submitted_at) FROM project_report_images WHERE project_report_images.task_id = taskTableCheck.id AND project_report_images.submitted_at IS NOT NULL) as submittedCount'),
                DB::raw('(SELECT name FROM projects WHERE projects.id = taskTableCheck.project_id) as project_name'),
                DB::raw('(SELECT procat_id FROM projects WHERE projects.id = taskTableCheck.project_id) as procat_id'),
                DB::raw('(SELECT pm_id FROM projects WHERE projects.id = taskTableCheck.project_id) as pmId'),
            ])
            ->where('id',$taskId)->first();

            $data['project'] = $firstCheck;
            $data['infoProjectTask'] = $privilegeCheck;
        //privilege check
            if (!isset($privilegeCheck) || $privilegeCheck->submittedCount < 1) {

                if (!isset($privilegeCheck->projectStatus)) {
                    $projectStatus = 1;
                }else{
                    $projectStatus = $privilegeCheck->projectStatus;
                }
                return redirect()->back()->with('alert-danger','Halaman yang Anda tuju tidak tersedia.');
            }
        //project department clients check
            if ($userDepartment == 1) {
                //data template
                    $data['projectTemplateDatas'] = DB::table('project_report_template_selected as prt')
                    ->select([
                        'prt.*',
                        DB::raw('(SELECT id FROM projects WHERE projects.id = prt.project_id) as project_id'),
                        DB::raw('(SELECT procat_id FROM projects WHERE projects.id = prt.project_id) as procat_id'),
                        DB::raw('(SELECT name FROM projects WHERE projects.id = prt.project_id) as project_name')
                    ])
                    ->where('project_id',$projectId)
                    ->where('task_id',$taskId)->get();
                //project category used for image folder placement
                    $data['dataProjectCategory'] = DB::table('projects_category')->where('id',$privilegeCheck->procat_id)->first();
                //project pictures
                    $data['dataProjectPictures'] = DB::table('project_report_images')->where('task_id',$id)->get();
                    $data['dataProjectPicturesStatus'] = DB::table('project_report_images as pri')
                    ->select([
                        'pri.*',
                        DB::raw('COUNT(pri.approved_at) as countApproved'),
                        DB::raw('COUNT(pri.approved_by_pm_at) as countPMApproved'),
                        //comments
                            DB::raw('(SELECT COUNT(*) FROM project_report_images_comments WHERE project_report_images_comments.pri_id = pri.id) as commentsCount')
                    ])
                    ->where('project_id',$projectId)
                    ->where('task_id',$id)
                    ->first();
                    if (!isset($data['dataProjectPicturesStatus']->id)) {
                        return redirect()->back()->with('alert-danger','Gambar belum tersedia. Cobalah beberapa saat lagi.');
                    }
                //data technicians
                    $data['dataTechnician'] = DB::table('project_report_images as pri')
                    ->select([
                        'pri.*',
                        //technicians
                            DB::raw('(SELECT title FROM techs WHERE techs.id = pri.publisher_id) as title'),
                            DB::raw('(SELECT user_type FROM techs WHERE techs.id = pri.publisher_id) as user_type'),
                            DB::raw('(SELECT firstname FROM techs WHERE techs.id = pri.publisher_id) as firstname'),
                            DB::raw('(SELECT lastname FROM techs WHERE techs.id = pri.publisher_id) as lastname'),
                    ])
                    ->where('project_id',$projectId)
                    ->where('task_id',$id)
                    ->where('publisher_id','!=', null)
                    ->first();
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
                    ->where('project_id',$projectId)
                    ->where('task_id',$id)
                    ->where('approved_at','!=', null)
                    ->first();
                //data pm
                    $data['dataProjectManager'] = DB::table('project_report_images as pri')
                    ->select([
                        'pri.*',
                        //project manager
                            DB::raw('(SELECT user_level FROM users WHERE users.id = '.$privilegeCheck->pmId.') as user_level'),
                            DB::raw('(SELECT name FROM users_level WHERE users_level.id = user_level) as title'),
                            DB::raw('(SELECT firstname FROM users WHERE users.id = '.$privilegeCheck->pmId.') as firstname'),
                            DB::raw('(SELECT lastname FROM users WHERE users.id = '.$privilegeCheck->pmId.') as lastname'),
                    ])
                    ->where('project_id',$projectId)
                    ->where('task_id',$id)
                    ->where('approved_by_pm_at','!=', null)
                    ->first();
                //supporting data
                    $data['subcatsUploadCount'] = DB::table('project_report_images')->where('task_id',$id)->count();
                    $data['subcatsPictureByCatCount'] = DB::table('project_report_images')
                        ->select('cat_id', DB::raw('count(*) as total'))
                        ->groupBy('cat_id')
                        ->get();
                //data
                    #$data['cats'] = unserialize($data['projectTemplate']->template_id);
                    #$data['subcats'] = unserialize($data['projectTemplate']->subcat_id);
                    $data['dataCategory'] = DB::table('project_report_category')->where('status',$catStatus)->where('deleted_at',null)->get();
                    $data['dataSubcategory'] = DB::table('project_report_subcategory')->where('status',$subcatStatus)->where('deleted_at',null)->get();
                //project
                    /*$data['project'] = DB::table('projects as proj')
                    ->select([
                        'proj.*',
                        DB::raw('(SELECT id FROM projects_task WHERE projects_task.project_id = proj.id) as task_id'),
                        DB::raw('(SELECT number FROM projects_task WHERE projects_task.project_id = proj.id) as pwo_number'),
                    ])
                    ->where('id',$data['projectTemplate']->project_id)->where('tech_id',$data['infoProjectTask']->tech_id)->first();
                    */
                //comments
                    $data['dataComments'] = DB::table('project_report_images_comments')->where('status',$commentStatus)->where('pri_id',$data['dataProjectPicturesStatus']->id)->orderBy('date', 'DESC')->get();
                    $data['techs'] = DB::table('techs')->get();
                    $data['users'] = DB::table('users')
                    ->select([
                        'users.*',
                        DB::raw('(SELECT name FROM users_level WHERE users_level.id = users.user_level) as title')
                    ])
                    ->get();
                return view('cust.project.report.report-images',$data);
            }
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Project Report.');
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
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Project Report.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Project Report.');
    }
}
