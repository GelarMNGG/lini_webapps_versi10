<?php

namespace App\Http\Controllers\User\Project;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Auth;
use DB;

class UserQcProjectReportController extends Controller
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
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman report project.');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman report project.');
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
        $userDepartment = Auth::user()->department_id; //project department

        $projectId = $request->project_id;
        $taskId = $request->task_id;
        $projectDepartment = 1;
        $qcDocument = 4;

        //firstcheck
        if ($userDepartment == $projectDepartment && $userLevel == $qcDocument) {
            $data = $request->except(['_token']);

            $combinedData = explode('.',$request->f_code);
            $data['f_code'] = $combinedData[0];
            $data['type'] = $combinedData[1];
            $data['created_at'] = Carbon::now()->format('Y-m-d H:i:s');

            DB::table('project_report_all_format')->insert($data);

            return redirect()->back()->with('alert-success','Data berhasil disimpan.');
        }

        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman report project.');
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
        $taskId = $id;
        
        $projectDepartment = 1; //project department
        $catStatus = 1; //active category
        $pm = 3; //pm
        $qcDocument = 4; //qc document
        $subcatStatus = 1; //active subcategory
        $commentStatus = 0; //accessible for technician level user
        //firstcheck
        if ($userDepartment != $projectDepartment) {
            return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Project Report');
        }
        //check priviledge
            if ($userLevel == $qcDocument) {
                $privilegeCheck = DB::table('projects_task as pt')
                ->select([
                    'pt.*',
                    DB::raw('(SELECT name FROM projects WHERE projects.id = pt.project_id) as project_name')
                ])
                ->where('project_id',$projectId)->where('id',$taskId)->where('qcd_id',$userId)->where('deleted_at',null)->first();
            }else{
                $pmCheck = DB::table('projects')->where('id',$projectId)->where('pm_id',$userId)->where('deleted_at',null)->count();
                //privilege
                    if ($pmCheck > 0) {
                        $privilegeCheck = DB::table('projects_task as pt')
                        ->select([
                            'pt.*',
                            DB::raw('(SELECT name FROM projects WHERE projects.id = pt.project_id) as project_name'),
                        ])
                        ->where('project_id',$projectId)->where('id',$taskId)->where('deleted_at',null)->first();
                    }else{
                        $privilegeCheck = NULL;
                    }
            }
        
        if (isset($privilegeCheck)) {
            //project data
                $data['dataProject'] = DB::table('projects as proj')
                ->select([
                    'proj.*',
                    //company name
                        DB::raw('(SELECT name FROM clients WHERE clients.id = proj.customer_id) as customer_name'),
                        DB::raw('(SELECT logo FROM clients WHERE clients.id = proj.customer_id) as customer_logo'),
                    //pic name
                        DB::raw('(SELECT firstname FROM customers WHERE customers.company_id = proj.customer_id) as customer_pic_firstname'),
                        DB::raw('(SELECT lastname FROM customers WHERE customers.company_id = proj.customer_id) as customer_pic_lastname'),
                    //partner name
                        DB::raw('(SELECT name FROM clients WHERE clients.id = proj.partner_id) as partner_name'),
                        DB::raw('(SELECT logo FROM clients WHERE clients.id = proj.partner_id) as partner_logo'),
                    //pic name
                        DB::raw('(SELECT firstname FROM customers WHERE customers.company_id = proj.partner_id) as partner_pic_firstname'),
                        DB::raw('(SELECT lastname FROM customers WHERE customers.company_id = proj.partner_id) as partner_pic_lastname'),
                    //pic name
                        DB::raw('(SELECT firstname FROM users WHERE users.id = proj.pm_id) as pm_firstname'),
                        DB::raw('(SELECT lastname FROM users WHERE users.id = proj.pm_id) as pm_lastname'),
                    //project category folder
                        DB::raw('(SELECT folder FROM projects_category WHERE projects_category.id = proj.procat_id) as folder'),
                ])
                ->where('id',$projectId)->first();
            //supporting data
                $data['projectTask'] = $privilegeCheck;
            ### content ###
                //report all check
                    $reportAllCheck = DB::table('project_report_all')->where('project_id',$projectId)->where('task_id',$taskId)->count();
                    if ($reportAllCheck < 1) {
                        //setting up the data
                            $dataReportAll['project_id'] = $projectId;
                            $dataReportAll['task_id'] = $taskId;
                            $dataReportAll['publisher_id'] = $userId;
                            $dataReportAll['publisher_type'] = $userType;
                            $dataReportAll['created_at'] = Carbon::now()->format('Y-m-d H:i:s');
                        //insert to the database
                            DB::table('project_report_all')->insert($dataReportAll);
                    }
                //project report all data
                    $data['projectReportAllData'] = DB::table('project_report_all as pra')
                    ->select([
                        'pra.*',
                        //comments
                            DB::raw('(SELECT COUNT(*) FROM project_report_all_comments WHERE project_report_all_comments.pra_id = pra.id) as commentsCount'),
                        //count approval
                            DB::raw('COUNT(pra.submitted_at) as countSubmitted'),
                            DB::raw('COUNT(pra.approved_by_pm_at) as countPMApproved'),
                    ])
                    ->where('project_id',$projectId)
                    ->where('task_id',$taskId)
                    ->first();
                //project report all id
                    $praId = $data['projectReportAllData']->id;
                //format report datas
                    $data['projectReportFormatDatas'] = DB::table('project_report_all_format as praf')
                    ->select([
                        'praf.*',
                        DB::raw('(SELECT image FROM project_report_all_format_code WHERE project_report_all_format_code.id = praf.f_code) as image'),
                        DB::raw('(SELECT image_count FROM project_report_all_format_code WHERE project_report_all_format_code.id = praf.f_code) as image_count'),
                        //category name
                            DB::raw('(SELECT name FROM project_report_category WHERE project_report_category.id = praf.template_id) as name'),
                    ])
                    ->where('pra_id',$praId)
                    ->orderBy('sort_order','DESC')
                    ->get();
                //format code report datas
                    $data['projectReportFormatCodeDatas'] = DB::table('project_report_all_format_code')->get();
                //selected template datas
                    $data['projectReportTemplateSelectedDatas'] = DB::table('project_report_template_selected as prts')
                    ->select([
                        'prts.*',
                        DB::raw('(SELECT type FROM project_report_category WHERE project_report_category.id = prts.template_id) as type'),
                    ])
                    ->where('project_id',$projectId)->where('task_id',$taskId)->get();
                //template content
                    //image
                        $data['projectReportTemplateContentDatas'] = DB::table('project_report_images as pri')
                        ->select([
                            'pri.*',
                            DB::raw('(SELECT name FROM project_report_category WHERE project_report_category.id = pri.template_id) as name'),
                            DB::raw('(SELECT name FROM project_report_subcategory WHERE project_report_subcategory.id = pri.subcat_id) as subcat_name'),
                            DB::raw('(SELECT name FROM project_report_subcategory_customized WHERE project_report_subcategory_customized.id = pri.subcat_id) as subcatcust_name'),
                        ])
                        ->where('project_id',$projectId)->where('task_id',$taskId)->where('selected_image',1)->get();
                    //text
                        $data['projectReportTemplateContentDatasText'] = DB::table('project_report_text as prt')
                        ->select([
                            'prt.*',
                            DB::raw('(SELECT name FROM project_report_category WHERE project_report_category.id = prt.template_id) as name'),
                            DB::raw('(SELECT name FROM project_report_subcategory WHERE project_report_subcategory.id = prt.subcat_id) as subcat_name'),
                            DB::raw('(SELECT name FROM project_report_subcategory_customized WHERE project_report_subcategory_customized.id = prt.subcat_id) as subcatcust_name'),
                        ])
                        ->where('project_id',$projectId)->where('task_id',$taskId)->get();
            ### content end ###
            ### comments ###
                //data comments
                    if (isset($praId)) {
                        $data['dataComments'] = DB::table('project_report_all_comments')->where('status',$commentStatus)
                        ->where('pra_id',$praId)
                        ->where('project_id',$projectId)
                        ->where('task_id',$taskId)
                        ->orderBy('date','DESC')
                        ->get();
                    }
                //data user
                    $data['users'] = DB::table('users')
                    ->select([
                        'users.*',
                        DB::raw('(SELECT name FROM users_level WHERE users_level.id = users.user_level) as title')
                    ])
                    ->get();
            ### comments end ###
            return view('user.project.project-report-qc.show',$data);
        }
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman report project.');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman report project.');
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
        $userDepartment = Auth::user()->department_id; //project department

        $projectDepartment = 1;
        $pm = 3;
        $qcDocument = 4;
        $projectId = $request->project_id;
        $taskId = $request->task_id;
        
        //firstcheck
            if ($userLevel == $qcDocument) {
                $firstcheck = DB::table('projects_task')->where('project_id',$projectId)->where('id',$taskId)->where('qcd_id',$userId)->where('deleted_at',null)->count();
            }else{
                $firstcheck = DB::table('projects')->where('id',$projectId)->where('pm_id',$userId)->where('deleted_at',null)->count();
            }

            if ($firstcheck < 1) {
                return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengubah format report project.');
            }
        //standard
        if ($userDepartment == $projectDepartment && $userLevel == $qcDocument || $userLevel == $pm) {
            //if edit template data
            if (isset($request->template_id)) {
                $data = $request->only(['template_id']);
            }elseif(isset($request->status)){
                $data = $request->only(['status','project_id','task_id']);

                $projectId = $data['project_id'];
                $taskId = $data['task_id'];
                $status = $data['status'];
                if ($status == 1) {
                    $data['submitted_at'] = Carbon::now()->format('Y-m-d H:i:s');
                }elseif($status == 2){
                    $data['approver_id'] = $userId;
                    $data['approver_type'] = $userType;
                    $data['approved_by_pm_at'] = Carbon::now()->format('Y-m-d H:i:s');
                }elseif($status == 0){
                    $data['submitted_at'] = NULL;
                    $data['approved_by_pm_at'] = NULL;
                }
                $data['updated_at'] = Carbon::now()->format('Y-m-d H:i:s');

                DB::table('project_report_all')->where('project_id',$projectId)->where('task_id',$taskId)->update($data);
                return redirect()->back()->with('alert-success','Status laporan berhasil diubah.');
            }else{
                $data = $request->except(['_token','_method']);
                $combinedData = explode('.',$request->f_code);
                $data['f_code'] = $combinedData[0];
                $data['type'] = $combinedData[1];
            }

            $data['updated_at'] = Carbon::now()->format('Y-m-d H:i:s');

            DB::table('project_report_all_format')->where('id',$id)->update($data);

            return redirect()->back()->with('alert-success','Data berhasil diubah.');
        }

        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman report project.');
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
        $userType = Auth::user()->user_type;
        $userLevel = Auth::user()->user_level;
        $userDepartment = Auth::user()->department_id; //project department

        $projectDepartment = 1;
        $qcDocument = 4;

        //firstcheck
        if ($userDepartment == $projectDepartment && $userLevel == $qcDocument) {

            DB::table('project_report_all_format')->delete($id);

            return redirect()->back()->with('alert-success','Data berhasil dihapus.');
        }

        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman report project.');
    }
}
