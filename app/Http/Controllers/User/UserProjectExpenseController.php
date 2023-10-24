<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Auth;
use DB;

class UserProjectExpenseController extends Controller
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

        $projectId = $request->project_id;
        $taskId = $request->task_id;

        $expenseStatus = 4; //approved by admin
        $expenseSubmittedStatus = 2; //submitted
        $projectStatus = 1;
        $projectStatusPM = 2;

        if ($userLevel == 6 || $userLevel == 3 && $userDepartment == 1) {

            ### dataCountCheck ###
                if ($userLevel == 3) {
                    $dataCountCheck = DB::table('projects_task')->where('project_id',$projectId)->where('id',$taskId)->where('pm_id',$userId)->where('deleted_at',null)->count();
                }else{
                    $dataCountCheck = DB::table('projects_task')->where('project_id',$projectId)->where('id',$taskId)->where('qce_id',$userId)->where('deleted_at',null)->count();
                }

                if ($dataCountCheck < 1) {
                    return redirect()->back()->with('alert-danger','Halaman yang Anda tuju tidak tersedia.');
                }
            ### dataCountCheck end ###

            ### getting data ###
                if ($userLevel == 3) {
                    $data['dataExpenses'] = DB::table('project_expenses as pe')
                    ->select([
                        'pe.*',
                        DB::raw('(SELECT name FROM project_expenses_status WHERE project_expenses_status.id = pe.status) as status_name'),
                        DB::raw('(SELECT COUNT(expense_id) FROM project_expenses_files WHERE project_expenses_files.expense_id = pe.id) as expenses_files_count'),
                    ])
                    ->where('status','>',$projectStatusPM)
                    ->where('project_id',$projectId)
                    ->where('task_id',$taskId)
                    ->get();
                }else{
                    $data['dataExpenses'] = DB::table('project_expenses as pe')
                    ->select([
                        'pe.*',
                        DB::raw('(SELECT name FROM project_expenses_status WHERE project_expenses_status.id = pe.status) as status_name'),
                        DB::raw('(SELECT COUNT(expense_id) FROM project_expenses_files WHERE project_expenses_files.expense_id = pe.id) as expenses_files_count'),
                    ])
                    ->where('status','>',$projectStatus)
                    ->where('project_id',$projectId)
                    ->where('task_id',$taskId)
                    ->get();
                }

                //data task
                if ($userLevel == 3) {
                    $data['projectTask'] = DB::table('projects_task as pt')
                    ->select([
                        'pt.*',
                        DB::raw('(SELECT name FROM projects WHERE projects.id = pt.project_id) as project_name')
                    ])
                    ->where('id',$taskId)
                    ->where('pm_id',$userId)
                    ->where('deleted_at',null)->first();
                }else{
                    $data['projectTask'] = DB::table('projects_task as pt')
                    ->select([
                        'pt.*',
                        DB::raw('(SELECT name FROM projects WHERE projects.id = pt.project_id) as project_name')
                    ])
                    ->where('id',$taskId)
                    ->where('qce_id',$userId)
                    ->where('deleted_at',null)->first();
                }
            ### getting data end ###

            ### additional data ###
                $data['dataExpensesStatus'] = DB::table('project_expenses_status')->get();
                $dataExpensePublisher = $data['projectTask']->tech_id;
                $data['dataExpenseCount'] = DB::table('project_expenses')->where('task_id',$taskId)->where('publisher_id',$dataExpensePublisher)->where('status','>',$expenseSubmittedStatus)->count();

                $data['dataReportCount'] = DB::table('project_expenses_report')->where('task_id',$taskId)->where('publisher_id',$dataExpensePublisher)->count();
            ### additional data ###

            $techId = $data['projectTask']->tech_id;
            $data['dataExpensesImages'] = DB::table('project_expenses_files')->where('task_id',$taskId)->where('tech_id',$techId)->get();

            return view('user.project.expense.index', $data);
        }

        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Project Expenses.');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Project Expenses.');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Project Expenses.');
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
        #$taskId = $request->task_id;

        $approverDepartment = 3; //finance
        $expenseStatus = 4; //approved by admin

        if ($userLevel == 6 || $userLevel == 3 && $userDepartment == 1) {

            ### dataCountCheck ###
                if ($userLevel == 3) {

                    ### expense report data ###
                        $data['projectTaskInfo'] = DB::table('projects_task as pt')
                        ->select([
                            'pt.*',
                            DB::raw('(SELECT name FROM projects WHERE projects.id = pt.project_id) as project_name'),
                            //technician
                            DB::raw('(SELECT firstname FROM techs WHERE techs.id = pt.tech_id AND techs.id IS NOT NULL) as tech_firstname'),
                            DB::raw('(SELECT lastname FROM techs WHERE techs.id = pt.tech_id AND techs.id IS NOT NULL) as tech_lastname'),
                            //QC doc
                            DB::raw('(SELECT firstname FROM users WHERE users.id = pt.qcd_id AND users.id IS NOT NULL) as qcd_firstname'),
                            DB::raw('(SELECT lastname FROM users WHERE users.id = pt.qcd_id AND users.id IS NOT NULL) as qcd_lastname'),
                            //QC expense
                            DB::raw('(SELECT firstname FROM users WHERE users.id = pt.qce_id AND users.id IS NOT NULL) as qce_firstname'),
                            DB::raw('(SELECT lastname FROM users WHERE users.id = pt.qce_id AND users.id IS NOT NULL) as qce_lastname'),
                            //pm
                            DB::raw('(SELECT firstname FROM users WHERE users.id = pt.pm_id AND users.id IS NOT NULL) as pm_firstname'),
                            DB::raw('(SELECT lastname FROM users WHERE users.id = pt.pm_id AND users.id IS NOT NULL) as pm_lastname'),
                        ])
                        ->where('project_id',$projectId)->where('id',$taskId)->where('pm_id',$userId)->where('deleted_at',null)->first();
                    ### expense report data end ###
                }else{

                    ### expense report data ###
                        $data['projectTaskInfo'] = DB::table('projects_task as pt')
                        ->select([
                            'pt.*',
                            DB::raw('(SELECT name FROM projects WHERE projects.id = pt.project_id) as project_name'),
                            //technician
                            DB::raw('(SELECT firstname FROM techs WHERE techs.id = pt.tech_id AND techs.id IS NOT NULL) as tech_firstname'),
                            DB::raw('(SELECT lastname FROM techs WHERE techs.id = pt.tech_id AND techs.id IS NOT NULL) as tech_lastname'),
                            //QC doc
                            DB::raw('(SELECT firstname FROM users WHERE users.id = pt.qcd_id AND users.id IS NOT NULL) as qcd_firstname'),
                            DB::raw('(SELECT lastname FROM users WHERE users.id = pt.qcd_id AND users.id IS NOT NULL) as qcd_lastname'),
                            //QC expense
                            DB::raw('(SELECT firstname FROM users WHERE users.id = pt.qce_id AND users.id IS NOT NULL) as qce_firstname'),
                            DB::raw('(SELECT lastname FROM users WHERE users.id = pt.qce_id AND users.id IS NOT NULL) as qce_lastname'),
                            //pm
                            DB::raw('(SELECT firstname FROM users WHERE users.id = pt.pm_id AND users.id IS NOT NULL) as pm_firstname'),
                            DB::raw('(SELECT lastname FROM users WHERE users.id = pt.pm_id AND users.id IS NOT NULL) as pm_lastname'),
                        ])
                        ->where('project_id',$projectId)->where('id',$taskId)->where('qce_id',$userId)->where('deleted_at',null)->first();
                    ### expense report data end ###
                }

                $dataCountCheck = $data['projectTaskInfo'];

                if ($dataCountCheck == null) {
                    return redirect()->back()->with('alert-danger','Halaman yang Anda tuju tidak tersedia.');
                }
            ### dataCountCheck end ###
        
            ### check priviledge ###
            $dataCountCheck = DB::table('project_expenses')->where('status',$expenseStatus)->where('publisher_id',$dataCountCheck->tech_id)->count();
            
            if ($dataCountCheck > 0) {

                //getting the data
                $data['projectExpensesReport'] = DB::table('project_expenses_report as ptr')
                ->select([
                    'ptr.*',
                    DB::raw('(SELECT name FROM projects WHERE projects.id = ptr.project_id) as project_name'),
                    DB::raw('(SELECT name FROM projects_task WHERE projects_task.id = ptr.task_id) as task_name'),
                    DB::raw('(SELECT number FROM projects_task WHERE projects_task.id = ptr.task_id) as number'),
                ])
                ->where('project_id',$projectId)->where('task_id',$taskId)->where('publisher_id',$data['projectTaskInfo']->tech_id)->first();

                //supporting data
                $data['expense_id'] = unserialize($data['projectExpensesReport']->expense_id);
                $data['dataReportExpenses'] = DB::table('project_expenses')->where('status',$expenseStatus)->where('project_id',$projectId)->where('task_id',$taskId)->where('publisher_id',$data['projectTaskInfo']->tech_id)->get();

                $data['dataReportExpensesCount'] = DB::table('project_expenses')
                ->select(DB::raw('SUM(amount) as total_amount'))
                ->where('project_id',$projectId)
                ->where('task_id',$taskId)
                ->where('status',$expenseStatus)
                ->where('deleted_at',null)
                ->first();

                $data['userProfile'] = DB::table('techs')->where('id',$data['projectTaskInfo']->tech_id)->first();
                $data['approverProfile'] = DB::table('admins')->where('department_id',$approverDepartment)->first();
                $data['dataReportCount'] = DB::table('project_expenses_report')->where('task_id',$taskId)->where('publisher_id',$data['projectTaskInfo']->tech_id)->count();

                return view('user.project.expense.report', $data);
            }
        }

        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Project Expenses.');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Project Expenses.');
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
        $userDepartment = Auth::user()->department_id;

        $projectId = $request->project_id;
        $taskId = $request->task_id;

        $expenseStatus = 4; //approved by admin
        $expenseSubmittedStatus = 2; //submitted

        ### dataCountCheck ###
            if ($userLevel == 3) {
                $dataCountCheck = DB::table('projects_task')->where('project_id',$projectId)->where('id',$taskId)->where('pm_id',$userId)->where('deleted_at',null)->count();
            }else{
                $dataCountCheck = DB::table('projects_task')->where('project_id',$projectId)->where('id',$taskId)->where('qce_id',$userId)->where('deleted_at',null)->count();
            }

            if ($dataCountCheck < 1) {
                return redirect()->back()->with('alert-danger','Halaman yang Anda tuju tidak tersedia.');
            }
        ### dataCountCheck end ###

        ### 6 QC expenses
        if ($userLevel == 6 || $userLevel == 3 && $userDepartment == 1) {

            //update status
            $updateStatus = $request->status;
            if (isset($updateStatus)) {

                if ($updateStatus == 1) {

                    ### reject note ###
                    $request->validate([
                        'reject_note' => 'required|min:5',
                    ]);
                    ### reject note end ###

                    $data = $request->except(['_token','_method','submit']);
                    $data['status'] = $request->status;
                    $data['submitted_at'] = null;
                    $data['approved_at'] = null;
                    $data['rejected_at'] = Carbon::now()->format('Y-m-d H:i:s');

                    DB::table('project_expenses')->where('id',$id)->update($data);
    
                    return redirect()->back()->with('alert-success', 'Data pengeluaran telah ditolak.');
                }elseif($updateStatus == 3){
                    $data = $request->except(['_token','_method','submit']);
                    $data['status'] = $request->status;
                    $data['approved_at'] = Carbon::now()->format('Y-m-d H:i:s');

                    DB::table('project_expenses')->where('id',$id)->update($data);
    
                    return redirect()->back()->with('alert-success', 'Data pengeluaran telah disetujui.');
                }elseif($updateStatus == 4){
                    $data = $request->except(['_token','_method','submit']);
                    $data['status'] = $request->status;
                    $data['approved_by_pm_at'] = Carbon::now()->format('Y-m-d H:i:s');

                    DB::table('project_expenses')->where('id',$id)->update($data);
    
                    return redirect()->back()->with('alert-success', 'Data pengeluaran telah disetujui.');
                }
            }
        }

        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Project Expenses.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Project Expenses.');
    }
}
