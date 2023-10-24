<?php

namespace App\Http\Controllers\Tech;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Auth;
use DB;

class TechProjectReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:tech');
    }


    //tool report start
    public function viewToolReport(Request $request)
    {
        $userId = Auth::user()->id;
        $userType = Auth::user()->user_type;
        $projectId = $request->project_id;
        $taskId = $request->task_id;
        $approverDepartment = 4; //general affair
        $toolStatus = 3;

        //check priviledge
        $dataCountCheck = DB::table('projects_task')->where('id',$taskId)->where('tech_id',$userId)->where('deleted_at',null)->count();
        
        if ($dataCountCheck > 0) {
            //getting the data
            $data['projectToolsReport'] = DB::table('project_tools_report as ptr')
            ->select([
                'ptr.*',
                DB::raw('(SELECT name FROM projects WHERE projects.id = ptr.project_id) as project_name'),
                DB::raw('(SELECT name FROM projects_task WHERE projects_task.id = ptr.task_id) as task_name'),
                DB::raw('(SELECT number FROM projects_task WHERE projects_task.id = ptr.task_id) as task_number'),
            ])
            ->where('project_id',$projectId)->where('task_id',$taskId)->where('publisher_id',$userId)->first();

            //supporting data
            $data['tool_id'] = unserialize($data['projectToolsReport']->tool_id);
            $data['dataReportTools'] = DB::table('project_tools')->where('status',$toolStatus)
            ->where('project_id',$projectId)
            ->where('task_id',$taskId)
            ->where('publisher_id',$userId)->get();

            $data['userProfile'] = DB::table('techs')->where('id',$userId)->first();
            $data['approverProfile'] = DB::table('admins')->where('department_id',$approverDepartment)->first();

            return view('tech.report.tools-report', $data);
        }

        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Tools.');
    }

    public function saveToolReport(Request $request)
    {
        $userId = Auth::user()->id;
        $userType = Auth::user()->user_type;
        $projectId = $request->project_id;
        $taskId = $request->task_id;

        //check priviledge
        #$dataCountCheck = DB::table('project_tools')->where('status',$toolStatus)->where('publisher_id',$userId)->count();
        $dataCountCheck = DB::table('projects_task')->where('id',$taskId)->where('tech_id',$userId)->where('deleted_at',null)->count();

        if ($dataCountCheck > 0) {
            //getting the data
            $data = $request->except(['_token','_method','submit']);
            $data['publisher_id'] = $userId;
            $data['publisher_type'] = $userType;
            $data['tool_id'] = serialize($request->tool_id);
            $data['created_at'] = Carbon::now()->format('Y-m-d H:i:s');

            //insert data to database
            DB::table('project_tools_report')->insert($data);

            return redirect()->route('project-tool-tech.index','task_id='.$taskId)->with('alert-success', 'Data berhasil disimpan');
        }

        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Tools Report.');
    }
    //tool report end

    //expense report start
    public function viewExpenseReport(Request $request)
    {
        $userId = Auth::user()->id;
        $userType = Auth::user()->user_type;
        $projectId = $request->project_id;
        $taskId = $request->task_id;
        $approverDepartment = 3; //general affair
        $expenseStatus = 4; //approved by pm

        //check priviledge
        $dataCountCheck = DB::table('projects_task')->where('id',$taskId)->where('tech_id',$userId)->where('deleted_at',null)->count();
        
        if ($dataCountCheck > 0) {

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
                ->where('project_id',$projectId)->where('id',$taskId)->where('tech_id',$userId)->first();
            ### expense report data end ###

            //getting the data
            $data['projectExpensesReport'] = DB::table('project_expenses_report as ptr')
            ->select([
                'ptr.*',
                DB::raw('(SELECT name FROM projects WHERE projects.id = ptr.project_id) as project_name'),
                DB::raw('(SELECT name FROM projects_task WHERE projects_task.id = ptr.task_id) as task_name'),
                DB::raw('(SELECT number FROM projects_task WHERE projects_task.id = ptr.task_id) as number'),
            ])
            ->where('project_id',$projectId)->where('task_id',$taskId)->where('publisher_id',$userId)->first();

            //supporting data
            $data['expense_id'] = unserialize($data['projectExpensesReport']->expense_id);
            $data['dataReportExpenses'] = DB::table('project_expenses')->where('status',$expenseStatus)->where('project_id',$projectId)->where('task_id',$taskId)->where('publisher_id',$userId)->get();

            $data['dataReportExpensesCount'] = DB::table('project_expenses')
            ->select(DB::raw('SUM(amount) as total_amount'))
            ->where('project_id',$projectId)
            ->where('task_id',$taskId)
            ->where('status',$expenseStatus)
            ->where('publisher_id',$userId)
            ->where('publisher_type',$userType)
            ->where('deleted_at',null)
            ->first();

            $data['userProfile'] = DB::table('techs')->where('id',$userId)->first();
            $data['approverProfile'] = DB::table('admins')->where('department_id',$approverDepartment)->first();

            return view('tech.report.expenses-report', $data);
        }

        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Expenses Report.');
    }

    public function saveExpenseReport(Request $request)
    {
        $userId = Auth::user()->id;
        $userType = Auth::user()->user_type;

        $projectId = $request->project_id;
        $taskId = $request->task_id;

        //check priviledge
        $dataCountCheck = DB::table('projects_task')->where('id',$taskId)->where('tech_id',$userId)->where('deleted_at',null)->count();

        if ($dataCountCheck > 0) {

            if ($request->id != null) {

                if ($request->status != null) {
                    $id = $request->id;
                    $data['status'] = $request->status;
                    $data = $request->except(['_token','_method','submit']);
                    $data['submitted_at'] = Carbon::now()->format('Y-m-d H:i:s');
    
                    //insert data to database
                    DB::table('project_expenses_report')->where('id',$id)->update($data);
    
                    return redirect()->back()->with('alert-success', 'Laporan pengeluaran berhasil dikirimkan.');
                }else{
                    $id = $request->id;
                    $data = $request->except(['_token','_method','submit']);
                    $data['canceled_at'] = Carbon::now()->format('Y-m-d H:i:s');
    
                    //insert data to database
                    DB::table('project_expenses_report')->where('id',$id)->update($data);
    
                    return redirect()->back()->with('alert-success', 'Permohonan pembatalan laporan telah berhasil dikirimkan.');
                }
            }

            //getting the data
            $data = $request->except(['_token','_method','submit']);
            $data['status'] = 2; //submitted
            $data['publisher_id'] = $userId;
            $data['publisher_type'] = $userType;
            $data['expense_id'] = serialize($request->expense_id);
            $data['submitted_at'] = Carbon::now()->format('Y-m-d H:i:s');
            $data['created_at'] = Carbon::now()->format('Y-m-d H:i:s');

            //check project report status
            $reportCheck = DB::table('project_expenses_report')->where('task_id',$taskId)->where('project_id',$projectId)->count();
            //insert data to database
            if ($reportCheck > 0) {
                $data['canceled_at'] = null;
                DB::table('project_expenses_report')->where('task_id',$taskId)->where('project_id',$projectId)->update($data);
            }else{
                DB::table('project_expenses_report')->insert($data);
            }

            return redirect()->route('expenses-tech.index','project_id='.$projectId.'&task_id='.$taskId)->with('alert-success', 'Data berhasil disimpan');
        }

        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Expenses Report.');
    }
    //expense report end
}
